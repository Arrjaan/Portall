<?php

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.SECURE.php');
require_once('functions.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

/* Post request. */
if ( $_SERVER['REQUEST_METHOD'] == "GET" && isset($_REQUEST['call']) ) 
	$tweets = $connection->get($_REQUEST['call'], array("include_entities" => "true"));
if ( $_SERVER['REQUEST_METHOD'] == "POST" & isset($_REQUEST['call']) ) {	
	$tweets = $connection->post($_REQUEST['call'], $_POST);
}

/* Return status code when tweeting, retweeting or favoriting. */
if ( $_REQUEST['call'] == "statuses/update" ) echo $connection->http_code;
if ( preg_match("/statuses\/retweet\/[0-9]./",$_REQUEST['call']) ) echo $connection->http_code;
if ( preg_match("/favorites\/create/\/[0-9]./",$_REQUEST['call']) ) echo $connection->http_code;
if ( $_REQUEST['call'] == "account/verify_credentials" ) {	
	echo "<h2>". $tweets->name ."</h2>";
	$u_tweets = $connection->get("statuses/user_timeline", array("screen_name" => $tweets->screen_name, "count" => "10"));
	$connection->get("users/profile_image/".$tweets->screen_name, array("size" => "bigger"));
	echo '<table><tbody><tr><td><img src="'. $connection->http_info['redirect_url'] .'" /></td><td>';
	
	echo '</td></tr></table>';

	makeTable($tweets);
}
if ( $_REQUEST['call'] == "users/lookup" ) {	
	echo "<h2>". $tweets[0]->name ."</h2>";
	$u_tweets = $connection->get("statuses/user_timeline", array("screen_name" => $tweets[0]->screen_name, "count" => "10"));
	
	$connection->get("users/profile_image/".$tweets[0]->screen_name, array("size" => "bigger"));
	$bigger = $connection->http_info['redirect_url'];
	$connection->get("users/profile_image/".$tweets[0]->screen_name, array("size" => "original"));
	$original = $connection->http_info['redirect_url'];
	
	echo '<table class="table"><tr><td><a onclick="loadIMG(\'' .$tweets[0]->name. '\',\''.$original.'\');" data-toggle="modal" href="#imgModal"><img src="'. $bigger .'" /></a></td><td>';
	
	echo '</td></tr></table>';
				
	makeTable($u_tweets);
}

/* Loading Tweets. */
if ( $_REQUEST['call'] == "statuses/home_timeline" || $_REQUEST['call'] == "statuses/mentions" ) {	
	echo "<h2>Twitter</h2>";
	makeTable($tweets);
}

if ( !isset($_REQUEST['call']) ) header("Location: /");

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

?>
