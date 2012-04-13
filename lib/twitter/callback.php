<?php
/**
 * @file
 * Take the user when they return from Twitter. Get access tokens.
 * Verify credentials and redirect to based on response from Twitter.
 */

/* Start session and load lib */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.SECURE.php');
require_once('../../config.SECURE.inc.php');

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

/* Save the access tokens. Normally these would be saved in a database for future use. */
$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
	/* The user has been verified and the access tokens can be saved for future use */
	$db = new Mysqli($db['server'],$db['user'],$db['pass'],$db['db'],$db['port']);
	$conn = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

	if ($db->connect_errno) {
		printf("Connect failed: %s\n", $db->connect_error);
		exit();
	}
	
	$twuser = $conn->get('account/verify_credentials');
	
	$q = $db->query("select * from `users` where `twitter` = '". $twuser->id ."'");
	if ( $q->num_rows > 0 ) {
		$data = $q->fetch_assoc();
		$_SESSION['session'] = substr(sha1(time().$twuser->id),0,10);
		$db->query("update `users` set `session` = '".$_SESSION['session']."', `tw_token` = '".$access_token['oauth_token']."', `tw_secret` = '".$access_token['oauth_token_secret']."' where `id` = '".$data['id']."'");
		$_SESSION['userid'] = $data['id'];
	}
	else {
		$_SESSION['session'] = substr(sha1(time().$twuser->id),0,10);
		$q = $db->query("insert into `users` values ('0', '". $twuser->name ."', '', '". $twuser->id ."', '0', '', '".time()."','".$_SESSION['session']."')");
		$_SESSION['userid'] = $db->insert_id;
	}	
	setcookie("portall_session",$_SESSION['session'],time()+60*60*24,'/','portall.eu5.org');
	header('Location: /home'); 
} else {
	/* Save HTTP status for error dialog on connnect page.*/
	header('Location: ./clearsessions.php');
}