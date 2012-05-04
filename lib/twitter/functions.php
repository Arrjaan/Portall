<?php

function makeTable($tweets, $public = false) {
	if ( isset($tweets->error) ) {
		if ( $tweets->error == "Not authorized" ) echo "<em>The tweets from this user are protected and can not be showed.</em>";
		else echo "<em>Error: ". $tweets->error .".</em>";
		return false;
	}
	
	echo '<table class="table" id="twitter">
			<thead>
				<tr>';
				if ( $_REQUEST['call'] == "statuses/mentions" ) echo '<th>Mentions</th>';
				if ( $_REQUEST['call'] == "statuses/home_timeline" ) echo '<th>Tweets</th>';
				echo'</tr>
			</thead><tbody>';
			
	foreach ( $tweets as $tweet ) {	
		echo '<tr><td>';

		if ( $public ) {
			$twText = linkify_tweet($tweet);
			echo '<img src="'. $tweet->user->profile_image_url . '" />';
			echo '</td><td class="msgRow"><a class="none" style="color: #999;" href="http://twitter.com/'. $tweet->user->screen_name .'/status/'. $tweet->id .'">'. $tweet->user->name .'</a>:<br />'. $twText .'<br />';
			$tweetid = $tweet->id;
		}
		elseif ( isset($tweet->retweeted_status) ) {
			$twText = linkify_tweet($tweet);
			echo '<img src="'. $tweet->retweeted_status->user->profile_image_url . '" />';
			echo '</td><td class="msgRow"><a class="none" style="color: #999;" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name='. $tweet->retweeted_status->user->screen_name .'\',\'span3\');">'. $tweet->retweeted_status->user->name .'</a> 
			(retweeted by <a class="none" style="color: #999;" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name='. $tweet->user->screen_name .'\',\'span3\');">'. $tweet->user->name .'</a>)<br />'. $twText .'<br />';
			$tweetid = $tweet->retweeted_status->id;
		}
		else {
			$twText = linkify_tweet($tweet);
			echo '<img src="'. $tweet->user->profile_image_url . '" />';
			echo '</td><td class="msgRow"><a class="none" style="color: #999;" onclick="post(\'/lib/twitter/index.php?call=users/lookup\',\'screen_name='. $tweet->user->screen_name .'\',\'span3\');">'. $tweet->user->name .'</a>:<br />'. $twText .'<br />';
			$tweetid = $tweet->id;
		}
		
		if ( isset($tweet->entities->media[0]->sizes->thumb->w) ) 
			echo '<a onclick="loadIMG(\'Image\',\''. $tweet->entities->media[0]->media_url .'\');" data-toggle="modal" href="#imgModal"><img src="'. $tweet->entities->media[0]->media_url . ':thumb" /></a><br />';
		if ( isset($tweet->retweeted_status->entities->media[0]->sizes->thumb->w) ) 
			echo '<a onclick="loadIMG(\'Image\',\''. $tweet->retweeted_status->entities->media[0]->media_url .'\');" data-toggle="modal" href="#imgModal"><img src="'. $tweet->retweeted_status->entities->media[0]->media_url . ':thumb" /></a><br />';
		elseif ( preg_match("/twitpic\.com/",$tweet->entities->urls[0]->display_url) ) {
			$code = explode("/",$tweet->entities->urls[0]->display_url);
			echo '<a onclick="loadIMG(\'Twitpic\',\'http://twitpic.com/show/thumb/'.$code[1].'\');" data-toggle="modal" href="#imgModal"><img src="http://twitpic.com/show/mini/'.$code[1].'" /></a><br />';
		}
		elseif ( preg_match("/yfrog\.com/",$tweet->entities->urls[0]->display_url) ) {
			$code = explode("/",$tweet->entities->urls[0]->display_url);
			echo '<a onclick="loadIMG(\'yfrog\',\'http://yfrog.com/'.$code[1].':medium\');" data-toggle="modal" href="#imgModal"><img src="http://yfrog.com/'.$code[1].':small" /></a><br />';
		}
		elseif ( preg_match("/youtube\.com\/watch\?v/",$tweet->entities->urls[0]->expanded_url) ) {
			$url = explode("=",$tweet->entities->urls[0]->expanded_url);
			if ( preg_match("/&/", $url[1]) ) {
				$url = explode("&",$url[1]);
				$url = $url[0];
			}
			else $url = $url[1];
			echo '<a onclick="loadYT(\''. $url .'\');" data-toggle="modal" href="#imgModal"><img src="http://img.youtube.com/vi/'.$url.'/2.jpg" /></a><br />';
		}
		elseif ( preg_match("/youtu\.be\//",$tweet->entities->urls[0]->expanded_url) ) {
			$url = explode("/",$tweet->entities->urls[0]->expanded_url);
			if ( preg_match("/&/", $url[1]) ) {
				$url = explode("&",$url[1]);
				$url = $url[0];
			}
			else $url = $url[1];
			echo '<a onclick="loadYT(\''. $url .'\');" data-toggle="modal" href="#imgModal"><img src="http://img.youtube.com/vi/'.$url.'/2.jpg" /></a><br />';
		}
		
		if ( $public ) echo '<span style="color: #999;" class="pull-right">'.date("d-m H:i:s O",strtotime($tweet->created_at)).'</span>';
		else {
			echo '<span class="twToolBox">
				<a href="#top" onclick="reply(\''. $tweet->user->screen_name .'\',\''. $tweet->id .'\');"><img class="hoverClass" src="/lib/layout/img/icons/reply.png" alt="&raquo; Reply" /></a> ';
			if ( $tweet->retweeted == 1 || $tweet->retweeted_status->retweeted == 1 )
				echo '<a onclick="post(\'/lib/twitter/index.php?call=statuses/destroy/'. $tweet->id .',\'\', \'postM\');"><img class="hoverClass" src="/lib/layout/img/icons/retweet_on.png" alt="Retweeted!" /></a> ';
			else 
				echo '<a onclick="post(\'/lib/twitter/index.php?call=statuses/retweet/'. $tweetid .'\',\'\', \'postM\');"><img class="hoverClass" src="/lib/layout/img/icons/retweet.png" alt="&raquo; Retweet" /></a> ';
			if ( $tweet->favorited == 1 || $tweet->retweeted_status->favorited == 1 )
				echo '<a onclick="post(\'/lib/twitter/index.php?call=favorites/create/'. $tweetid .'\',\'\', \'postM\');"><img class="hoverClass" src="/lib/layout/img/icons/favorite_on.png" alt="Favorited!" /></a> ';
			else 
				echo '<a onclick="post(\'/lib/twitter/index.php?call=favorites/destroy/'. $tweetid .'\',\'\', \'postM\');"><img class="hoverClass" src="/lib/layout/img/icons/favorite.png" alt="&raquo; Favorite" /></a> ';
			echo '<span class="pull-right">'.date("d-m H:i:s O",strtotime($tweet->created_at)).'</span>';
			echo '</span>';
		}
		
		echo '</td></tr>';
	}
	
	echo "</tbody>
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
        '<a onclick="loadIFrame(\''. str_replace("https","http",$twdata->entities->urls[0]->url) .'\');" data-toggle="modal" href="#imgModal">'. $twdata->entities->urls[0]->display_url .'</a>',
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