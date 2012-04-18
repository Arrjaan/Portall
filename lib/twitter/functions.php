<?php

function makeTable($tweets) {
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
		elseif ( preg_match("/twitpic\.com/",$tweet->entities->urls[0]->display_url) ) {
			$code = explode("/",$tweet->entities->urls[0]->display_url);
			echo '<img src="http://twitpic.com/show/mini/'.$code[1].'" />';
		}
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

?>