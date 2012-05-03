<?php
	session_start(); 
	
	require 'facebook.php';
	require 'config.SECURE.php';
	require_once('../../config.SECURE.inc.php');

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
		
		echo "<h2>Facebook</h2>";
		echo '<table class="table">
				<thead>
					<tr>
						<th>Posts</th>
					</tr>
				</thead><tbody>';
				
		foreach ( $wall as $post ) {
			if ( empty($post['message']) ) continue;
			if ( isset($post['to']['data'][0]['name']) ) echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td><td class="msgRow"><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a> <i class="icon-chevron-right"></i> <a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['to']['data'][0]['id'].'\',\'span3\');">'. $post['to']['data'][0]['name'] .'</a>:<br />'. $post['message'].'<br />';
			else echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td><td class="msgRow"><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a>:<br />'. $post['message'].'<br />';
			
			if ( !empty($post['picture']) ) echo '<a href="'.$post['link'].'" target="_BLANK"><img src="'.$post['picture'].'" /></a><br />';
			
			echo '<span class="twToolBox">
				<a onclick="fbLike(\''.$post['id'].'\');"><img src="/lib/layout/img/like.png" /></a> ';
			echo '<a onclick="document.getElementById(\'make_comment'.$post['id'].'\').style.display = \'inline\';"><img src="/lib/layout/img/comment.png" /></a>';
			echo '<span id="make_comment'.$post['id'].'" class="make_comment input-append"><br /><form onsubmit="return false;" class="form-inline"><input class="input input-medium" /><input type="button" value="Comment" onclick="fbComment(\''.$post['id'].'\',this.form);" class="btn"></form></span>';
			echo '<span class="pull-right">'.date("d-m H:i:s O",strtotime($post['created_time'])).'</span>';
			echo '</span>';
			
			echo '</td></tr>';
		}
		
		echo"</tbody>
					</table>";
		if ( $_SESSION['debug'] ) print_r($wall);
	}
	/* User information */
	if ( preg_match("/[0-9]{10,20}/",$_REQUEST['call']) ) {	
		$data = json_decode(file_get_contents("https://graph.facebook.com/".$wall['id']."?access_token=".$facebook->getAccessToken()),true);
		
		echo "<h2>". $wall['name'] ." <a href=\"#\" onclick=\"reset();\" class=\"close\">&times;</a></h2>";
		echo '<table class="table"><tr><td><a onclick="loadIMG(\''.$data['name'].'\',\'http://graph.facebook.com/'.$wall['id'].'/picture?type=large\');" data-toggle="modal" href="#imgModal"><img src="http://graph.facebook.com/'.$wall['id'].'/picture?type=normal" /></a></td><td>';
		
		echo  '<strong>'.$data['name'].'</strong><br />';
		if ( !empty($data['birthday']) ) echo 'Birthday: '.$data['birthday'].'<br />';
		if ( !empty($data['hometown']['name']) ) echo 'Hometown: '.$data['hometown']['name'].'<br />';
		if ( !empty($data['education']['school']['name']) ) echo 'School: '.$data['education']['school']['name'].'<br />';
		if ( !empty($data['relationship_status']) ) echo 'Relationship status: '.$data['relationship_status'].'<br />';
		
		echo '</td></tr></table>';
		
		echo '<table class="table">
				<thead>
					<tr>
						<th>Posts</th>
					</tr>
				</thead><tbody>';
				
		$posts = $facebook->api('/'.$wall['id'].'/feed','GET');
		$posts = $posts["data"];
		
		foreach ( $posts as $post ) {
			if ( empty($post['message']) ) continue;
			echo '<tr><td><img src="http://graph.facebook.com/'.$post['from']['id'].'/picture" /></td><td><a style="color: #999;" onclick="ajax(\'/lib/facebook/index.php?call=/'.$post['from']['id'].'\',\'span3\');">'. $post['from']['name'] .'</a>:<br />'. $post['message'].'<br />';
			
			if ( !empty($post['picture']) ) echo '<a href="'.$post['link'].'" target="_BLANK"><img src="/thumb/'.base64_encode($post['picture']).'" /></a><br />';
			
			echo '</td></tr>';
		}
		
		echo"</tbody>
					</table>";	
		if ( $_SESSION['debug'] ) print_r($data);
	}
?>