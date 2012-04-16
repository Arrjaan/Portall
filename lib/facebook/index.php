<?php
	session_start(); 
	
	require 'facebook.php';
	require 'config.SECURE.php';
	require_once('../../config.SECURE.inc.php');

	$facebook = new Facebook(array(
	  'appId'  => CONSUMER_KEY,
	  'secret' => CONSUMER_SECRET,
	));

	// Get User ID
	$user = $facebook->getUser();
	if ( empty($_SESSION['facebook']) ) $_SESSION['facebook'] = "init";
	if ( $_SESSION['facebook'] == "init" ) $_SESSION['facebook'] = $user;
	
	if ( $user && !isset($_GET['call']) ) {
		$user_profile = $facebook->api('/me','GET');
		$db = new Mysqli($db['server'],$db['user'],$db['pass'],$db['db'],$db['port']);
		
		if ( !empty($_SESSION['userid']) && !empty($_SESSION['session']) ) {
			$q = $db->query("select * from `users` where `session` = '".$_SESSION['session']."' and `id` = '".$_SESSION['userid']."'");
			$data = $q->fetch_assoc();
			$db->query("update `users` set `facebook` = '".$_SESSION['facebook']."', `fb_token` = '".$facebook->getAccessToken()."' where `id` = '".$data['id']."'");
		}	
		else {
			$q = $db->query("select * from `users` where `facebook` = '".$_SESSION['facebook']."'");
			
			if ( $q->num_rows > 0 ) {
				$data = $q->fetch_assoc();
				$_SESSION['session'] = substr(sha1(time().$twuser->id),0,10);
				$db->query("update `users` set `session` = '".$_SESSION['session']."', `fb_token` = '".$facebook->getAccessToken()."' where `id` = '".$data['id']."'");
				$_SESSION['userid'] = $data['id'];
			}
			else {
				$fullname = $user_profile['name'];
				$_SESSION['session'] = substr(sha1(time().$twuser->id),0,10);
				$q = $db->query("insert into `users` values ('0', '".$fullname."', '', '0', '', '', '".$_SESSION['facebook']."', '".$facebook->getAccessToken()."', '".time()."','".$_SESSION['session']."')");
				$_SESSION['userid'] = $db->insert_id;
			}	
			setcookie("portall_session",$_SESSION['session'],time()+60*60*24,'/','portall.eu5.org');
			header('Location: /'); 
		}
	}
	
	if ( !$user ) {
		$params = array(
			'scope' => 'user_status, friends_status, user_activities, friends_activities, read_stream, user_likes, friends_likes, read_mailbox, read_stream, manage_notifications'
		);
		$loginUrl = $facebook->getLoginUrl($params);	
		header("Location: ".$loginUrl);
	}
	
	if ( isset($_GET['code']) ) header("Location: /home");
	
	if ( $_SERVER['REQUEST_METHOD'] == "GET" && isset($_GET['call']) ) {
		$wall = $facebook->api($_GET['call'],'GET');
	}
	if ( $_SERVER['REQUEST_METHOD'] == "POST" & isset($_POST['call']) ) {
		$args = $_POST;
		unset($args['call']);
		$wall = $facebook->api($_POST['call'],'POST',$args);
	}
	
	if ( $_REQUEST['call'] == "/me/home" ) {	
		$wall = $facebook->api('/me/home','GET');
        $wall = $wall['data'];
		
		echo "<h2>Facebook</h2>";
		echo '<table class="table">
				<thead>
					<tr>
						<th>Posts</th>
					</tr>
				</thead><tbody>';
				
		foreach ( $wall as $post ) {
			if ( empty($post['message']) ) continue;
			echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td><td><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a>:<br />'. $post['message'].'<br />';
			
			if ( !empty($post['picture']) ) echo '<a href="'.$post['link'].'" target="_BLANK"><img src="/thumb/'.base64_encode($post['picture']).'" /></a><br />';
			
			echo '</td></tr>';
		}
		
		echo"</tbody>
					</table>";
		if ( $_SESSION['debug'] ) print_r($wall);
	}
	if ( preg_match("/[0-9]{1,15}",$_REQUEST['call']) ) {	
		echo "<h2>". $wall['id'] ."</h2>";
		echo '<table class="table"><tbody><tr><td><img src="http://graph.facebook.com/'.$wall['id'].'/picture" /></td><td></td></tr></table>';
		
		echo '<table class="table">
				<thead>
					<tr>
						<th>Tweets</th>
					</tr>
				</thead><tbody>';
				
		foreach ( $u_tweets as $tweet ) {
			$twText = linkify_tweet($tweet->text);
		
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
?>