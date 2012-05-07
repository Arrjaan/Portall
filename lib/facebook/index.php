<?php
	session_start(); 
	
	require 'facebook.php';
	require 'config.SECURE.php';
	require_once('../../config.SECURE.inc.php');
	require_once('../../functions.php');
	require 'function.php';

	$facebook = new Facebook(array(
	  'appId'  => CONSUMER_KEY,
	  'secret' => CONSUMER_SECRET,
	));
	
	//die(print_r($_POST,true));

	// Get User ID
	$user = $facebook->getUser();
	if ( empty($_SESSION['facebook']) ) $_SESSION['facebook'] = "init";
	if ( $_SESSION['facebook'] == "init" ) $_SESSION['facebook'] = $user;
	
	if ( $user && !isset($_GET['call']) ) {
		$user_profile = $facebook->api('/me','GET');
		
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
			'scope' => 'publish_stream, user_status, friends_status, user_activities, friends_activities, read_stream, user_likes, friends_likes, read_mailbox, read_stream, manage_notifications, user_hometown, friends_hometown, user_education_history, friends_education_history, user_birthday, friends_birthday,user_relationships, friends_relationships, user_relationship_details, friends_relationship_details'
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
		if ( !empty($wall['id']) ) echo '200';
		else print_r($wall);
		die();
	}
	/* Wall */
	if ( $_REQUEST['call'] == "/me/home" ) {	
		$wall = $facebook->api('/me/home','GET');
        $wall = $wall['data'];
		
		makeFBTable($wall);
	}
	/* Load Comments */
	if ( preg_match("/[0-9_]{10,40}\/comments/",$_REQUEST['call']) ) {	
		$wall = $wall['data'];
		
		makeFBTable($wall, "Comments <a href=\"#\" onclick=\"reset();\" class=\"close\">&times;</a>", "Replies"); 
		
		$post_id = str_replace("comments","",str_replace("/","",$_REQUEST['call']));
		echo '<span class="input-append"><form onsubmit="return false;" class="form-inline"><input class="input" /><input type="button" value="Comment" onclick="fbComment(\''.$post_id.'\',this.form);" class="btn"></form></span>';			
		
		if ( $_SESSION['debug'] ) print_r($wall);
	}
	/* User information */
	elseif ( preg_match("/[0-9]{10,20}/",$_REQUEST['call']) ) {	
		$data = json_decode(file_get_contents("https://graph.facebook.com/".$wall['id']."?access_token=".$facebook->getAccessToken()),true);
		
		echo "<h2>". $wall['name'] ." <a href=\"#\" onclick=\"reset();\" class=\"close\">&times;</a></h2>";
		echo '<table class="table"><tr><td><a onclick="loadIMG(\''.$data['name'].'\',\'http://graph.facebook.com/'.$wall['id'].'/picture?type=large\');" data-toggle="modal" href="#imgModal"><img src="http://graph.facebook.com/'.$wall['id'].'/picture?type=normal" /></a></td><td>';
		
		echo  '<strong>'.$data['name'].'</strong><br />';
		if ( !empty($data['birthday']) ) echo 'Birthday: '.$data['birthday'].'<br />';
		if ( !empty($data['hometown']['name']) ) echo 'Hometown: '.$data['hometown']['name'].'<br />';
		if ( !empty($data['education']['school']['name']) ) echo 'School: '.$data['education']['school']['name'].'<br />';
		if ( !empty($data['relationship_status']) ) echo 'Relationship status: '.$data['relationship_status'].'<br />';
		
		echo '</td></tr></table>';
				
		$posts = $facebook->api('/'.$wall['id'].'/feed','GET');
		$posts = $posts["data"];
		
		makeFBTable($posts); 
	}
?>