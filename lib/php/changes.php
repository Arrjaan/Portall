<?php

require_once('lib/twitter/twitteroauth/twitteroauth.php');
require_once('lib/twitter/functions.php');

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

$u_tweets = $connection->get("statuses/user_timeline", array("screen_name" => "PortallSocial", "count" => "20", "include_entities" => 1));

ob_start();
makeTable($u_tweets, true);
$html = ob_get_contents();
ob_end_clean();

$type = "home";
$content = '<p>Changelog</p>
    <p>
		'.$html.'
	</p>';
?>