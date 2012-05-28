<?php

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.SECURE.php');
require_once('../../config.SECURE.inc.php');
require_once('../../functions.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

require_once('functions.php');

if ( $_SESSION['limit'] < 15 && $_SESSION['limit'] > 0 && $_SESSION['limit_reset'] > time() ) {
	$hF = date("h");
	$hT = date("h",$_SESSION['limit_reset']);

	if ( $hF == $hT ) $diff = date("i",$_SESSION['limit_reset']) - date("i");
	else $diff = 60 + date("i",$_SESSION['limit_reset']) - date("i");
	$diff = $diff / 60 * 100;
	$diff = 100 - $diff;

	die('<h2>Error</h2>You have almost exceeded your Twitter API rate-limit!<br><br>
		<div class="progress progress-striped active progress-warning">
			<div class="bar" style="width: '.round($diff).'%;">'.round($diff).'%</div>
		</div>
		You have to wait till '.date("H:i:s",$_SESSION['limit_reset']).' before your timeline will show up again.');
}

if ( !isset($_SESSION['screen_name']) ) {
	$acc = $connection->get("account/verify_credentials");
	$_SESSION['screen_name'] = $acc->screen_name;
}
if ( !isset($_SESSION['since_id']) ) $_SESSION['since_id'] = 1;

/* Post request. */
if ( $_GET['call'] == "search" ) {
	$data = $_GET;
	unset($data['call']);
	$tweets = $connection->get($_REQUEST['call'], $data);
}
elseif ( $_SERVER['REQUEST_METHOD'] == "GET" && isset($_REQUEST['call']) ) 
	$tweets = $connection->get($_REQUEST['call'], array("include_entities" => "true"));
if ( $_SERVER['REQUEST_METHOD'] == "POST" & isset($_REQUEST['call']) ) {	
	$tweets = $connection->post($_REQUEST['call'], $_POST);
}
if ( isset($_REQUEST['call']) ) {
	$x = $connection->headers;
	$x = explode("\n",$x);
	
	foreach ( $x as $header ) {
		$y = explode(": ",$header);
		if ( $y[0] == "X-RateLimit-Remaining") $_SESSION['limit'] = $y[1];
		if ( $y[0] == "X-RateLimit-Reset" ) $_SESSION['limit_reset'] = $y[1];
	}
}
if ( isset($_REQUEST['hdrs']) ) {
	$connection->get("statuses/home_timeline");
	$x = $connection->headers;
	$x = explode("\n",$x);
	print_r($x);
	die();
}

/* Return status code when tweeting, retweeting or favoriting. */
if ( $_REQUEST['call'] == "statuses/update" ) echo $connection->http_code;
if ( $_REQUEST['call'] == "friendships/destroy" || $_REQUEST['call'] == "friendships/create" ) {
	$tweets = $connection->post("users/lookup", array("user_id" => $_POST['user_id']));
	$lookup = $_POST['user_id'];
}
if ( $_REQUEST['call'] == "account/verify_credentials" ) {	
	echo "<h2>". $tweets->name . closeBtn();
	$u_tweets = $connection->get("statuses/user_timeline", array("screen_name" => $tweets->screen_name, "count" => "10"));
	
	$connection->get("users/profile_image/".$tweets->screen_name, array("size" => "bigger"));
	$bigger = $connection->http_info['redirect_url'];
	$connection->get("users/profile_image/".$tweets->screen_name, array("size" => "original"));
	$original = $connection->http_info['redirect_url'];
	
	echo '<table class="table"><tr><td><a onclick="loadIMG(\'' .$tweets->name. '\',\''.$original.'\');" data-toggle="modal" href="#mdl"><img src="'. $bigger .'" /></a></td><td>';
	
	echo "<strong>". $tweets->name ."</strong> @".$tweets->screen_name ."<br />";
	echo "Bio: ".$tweets->description ."<br />";
	echo "Tweets: ".$tweets->tweets_count ."<br />";
	echo "Followers: ".$tweets->followers_count ." | Follows: ". $tweets->friends_count ." | Favorites: ". $tweets->favourites_count ." | Listed: ". $tweets->listed_count ."<br /><br />";
		
	echo '</td></tr></tbody></table>';

	makeTable($u_tweets);
}
if ( $_REQUEST['call'] == "users/lookup" || isset($lookup) ) {	
	echo "<h2>". $tweets[0]->name . closeBtn();
	$u_tweets = $connection->get("statuses/user_timeline", array("screen_name" => $tweets[0]->screen_name, "count" => "10"));
	
	$connection->get("users/profile_image/".$tweets[0]->screen_name, array("size" => "bigger"));
	$bigger = $connection->http_info['redirect_url'];
	$connection->get("users/profile_image/".$tweets[0]->screen_name, array("size" => "original"));
	$original = $connection->http_info['redirect_url'];
	
	echo '<table class="table"><tr><td><a onclick="loadIMG(\'' .$tweets[0]->name. '\',\''.$original.'\');" data-toggle="modal" href="#mdl"><img src="'. $bigger .'" /></a></td><td>';
	
	echo "<strong>". $tweets[0]->name ."</strong> @".$tweets[0]->screen_name ."<br />";
	echo "Bio: <em>".$tweets[0]->description ."</em><br />";
	echo "Tweets: ".$tweets[0]->statuses_count ."<br />";
	echo "Followers: ".$tweets[0]->followers_count ." | Follows: ". $tweets[0]->friends_count ." | Favorites: ". $tweets[0]->favourites_count ." | Listed: ". $tweets[0]->listed_count ."<br />";
	
	if ( $tweets[0]->following ) echo '<button class="btn btn-danger" onclick="post(\'/lib/twitter/index.php?call=friendships/destroy\',\'user_id='. $tweets[0]->id .'\',\'span3\');">Unfollow</button>';
	if ( !$tweets[0]->following ) echo '<button class="btn btn-primary" onclick="post(\'/lib/twitter/index.php?call=friendships/create\',\'user_id='. $tweets[0]->id .'\',\'span3\');">Follow</button>';
	
	echo '</td></tr></table>';
				
	makeTable($u_tweets);
}
/* Loading Searches. */
if ( $_REQUEST['call'] == "search" ) {	
	echo "<h2>Search <em>".urldecode($_REQUEST['q'])."</em>". closeBtn();
	makeTable($tweets->results);
}
/* Loading Tweets. */
if ( $_REQUEST['call'] == "statuses/home_timeline" || $_REQUEST['call'] == "statuses/mentions" ) {	
	echo "<h2>Twitter</h2>";
	makeTable($tweets);
	
	foreach ( $tweets as $tweet ) {
		if ( preg_match("/\@".$_SESSION['screen_name']."/",$tweet->text) && $_SESSION['since_id'] < $tweet->id ) {
			$_SESSION['since_id'] = $tweet->id;
			echo "<span style='display: none;'>PLAYSND</span>";
			break;
		}
	}
}

if ( !isset($_REQUEST['call']) ) header("Location: /");

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

?>
