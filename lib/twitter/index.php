<?php
/**
 * @file
 * User has successfully authenticated with Twitter. Access tokens saved to session and DB.
 */

/* Load required lib files. */
session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.SECURE.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

if ( $_SERVER['REQUEST_METHOD'] == "GET" && isset($_REQUEST['call']) ) 
	$tweets = $connection->get($_REQUEST['call'], array("include_entities" => "true"));
if ( $_SERVER['REQUEST_METHOD'] == "POST" & isset($_REQUEST['call']) ) {	
	$tweets = $connection->post($_REQUEST['call'], $_POST);
}

function get_page_title($url){

	if( !($data = file_get_contents($url)) ) return false;

	if( preg_match("#<title>(.+)<\/title>#iU", $data, $t))  {
		return trim($t[1]);
	} else {
		return false;
	}
}

function linkify_tweet($twdata) {
	if ( !empty($twdata->retweeted_status->text) ) $tweet = $twdata->retweeted_status->text;
	else $tweet = $twdata->text;

	$tweet = str_replace($twdata->entities->urls[0]->url,
        '<a onclick="loadIFrame(\''. $twdata->entities->urls[0]->url .'\');" data-toggle="modal" href="#imgModal">'. $twdata->entities->urls[0]->display_url .'</a>',
        $tweet);
	$tweet = preg_replace('/(^|\s)@(\w+)/',
        '\1<a href="#" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name=\2\',\'span3\');">@\2</a>',
        $tweet);
	$tweet = preg_replace('/(^|\s)#(\w+)/',
        '\1<a href="http://search.twitter.com/search?q=%23\2">#\2</a>',
        $tweet);
	$tweet = wordwrap($tweet,40);
	return $tweet;
}

if ( $_REQUEST['call'] == "statuses/update" ) echo $connection->http_code;
if ( $_REQUEST['call'] == "account/verify_credentials" ) {	
	echo "<h2>". $tweets->name ."</h2>";
	$u_tweets = $connection->get("statuses/user_timeline", array("screen_name" => $tweets->screen_name, "count" => "10"));
	$connection->get("users/profile_image/".$tweets->screen_name, array("size" => "bigger"));
	echo '<table><tbody><tr><td><img src="'. $connection->http_info['redirect_url'] .'" /></td><td>';
	
	echo '</td></tr></table>';
	
	echo '<table class="table">
			<thead>
				<tr>
					<th>Tweets</th>
				</tr>
			</thead><tbody>';
			
	foreach ( $u_tweets as $tweet ) {
		$twText = linkify_tweet($tweet);
	
		echo '<tr><td><img src="'. $tweet->user->profile_image_url . '" /></td><td>'. $tweet->user->name .':<br />'. $twText .'<br />';
		
		if ( isset($tweet->entities->media[0]->sizes->thumb->w) ) 
			echo '<img src="'. $tweet->entities->media[0]->media_url . ':thumb" /><br />';
		
		echo '<span class="twToolBox">
			<a onclick="reply(\''. $tweet->user->screen_name .'\',\''. $tweet->id .'\');">&raquo; Reply</a> 
			<a onclick="post(\'/lib/twitter/index.php?call=statuses/retweet/'. $tweet->id .'\',\'\', \'postM\');">&raquo; Retweet</a>
		</span>';
		
		
		echo '</td></tr>';
	}
	
	echo"</tbody>
				</table>";	
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
	
	echo '<table class="table">
			<thead>
				<tr>
					<th>Tweets</th>
				</tr>
			</thead><tbody>';
			
	foreach ( $u_tweets as $tweet ) {
		$twText = linkify_tweet($tweet);
	
		echo '<tr><td><img src="'. $tweet->user->profile_image_url . '" /></td><td>'. $tweet->user->name .':<br />'. $twText .'<br />';
		
		if ( isset($tweet->entities->media[0]->sizes->thumb->w) ) 
			echo '<img src="'. $tweet->entities->media[0]->media_url . ':thumb" /><br />';
		
		echo '<span class="twToolBox">
			<a onclick="reply(\''. $tweet->user->screen_name .'\',\''. $tweet->id .'\');">&raquo; Reply</a> 
			<a onclick="post(\'/lib/twitter/index.php?call=statuses/retweet/'. $tweet->id .'\',\'\', \'postM\');">&raquo; Retweet</a>
		</span>';
		
		
		echo '</td></tr>';
	}
	
	echo"</tbody>
				</table>";	
}

/* Loading Tweets. */
if ( $_REQUEST['call'] == "statuses/home_timeline" || $_REQUEST['call'] == "statuses/mentions" ) {	
	echo "<h2>Twitter</h2>";
	echo '<table class="table">
			<thead>
				<tr>';
				if ( $_REQUEST['call'] == "statuses/mentions" ) echo '<th>Mentions</th>';
				if ( $_REQUEST['call'] == "statuses/home_timeline" ) echo '<th>Tweets</th>';
				echo'</tr>
			</thead><tbody>';
			
	foreach ( $tweets as $tweet ) {	
		echo '<tr><td>';

		if ( isset($tweet->retweeted_status) ) {
			$twText = linkify_tweet($tweet);
			echo '<img src="'. $tweet->retweeted_status->user->profile_image_url . '" />';
			echo '</td><td><a class="none" style="color: #999;" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name='. $tweet->retweeted_status->user->screen_name .'\',\'span3\');">'. $tweet->retweeted_status->user->name .'</a> 
			(retweeted by <a class="none" style="color: #999;" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name='. $tweet->user->screen_name .'\',\'span3\');">'. $tweet->user->name .'</a>)<br />'. $twText .'<br />';
			$tweetid = $tweet->retweeted_status->id;
		}
		else {
			$twText = linkify_tweet($tweet);
			echo '<img src="'. $tweet->user->profile_image_url . '" />';
			echo '</td><td><a class="none" style="color: #999;" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name='. $tweet->user->screen_name .'\',\'span3\');">'. $tweet->user->name .'</a>:<br />'. $twText .'<br />';
			$tweetid = $tweet->id;
		}
		
		if ( isset($tweet->entities->media[0]->sizes->thumb->w) ) 
			echo '<img src="'. $tweet->entities->media[0]->media_url . ':thumb" /><br />';
		elseif ( preg_match("/youtube\.com\/watch\?v/",$tweet->entities->urls[0]->expanded_url) ) {
			$url = explode("=",$tweet->entities->urls[0]->expanded_url);
			$url = $url[1];
			echo '<iframe width="150" height="131" src="http://www.youtube.com/embed/'.$url.'" frameborder="0" allowfullscreen></iframe><br />';
		}
		
		echo '<span class="twToolBox">
			<a onclick="reply(\''. $tweet->user->screen_name .'\',\''. $tweet->id .'\');">&raquo; Reply</a> 
			<a onclick="post(\'/lib/twitter/index.php?call=statuses/retweet/'. $tweetid .'\',\'\', \'postM\');">&raquo; Retweet</a>
		</span>';
		
		
		echo '</td></tr>';
	}
	
	echo"</tbody>
				</table>";
				
	if ( $_SESSION['debug'] ) print_r($tweets);
}

if ( !isset($_REQUEST['call']) ) header("Location: /");

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

/* Include HTML to display on the page */
//include('html.inc');
