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
	$_SESSION['facebook'] = $user;
	
	if ( $user && !isset($_GET['call']) ) {
		$db = new Mysqli($db['server'],$db['user'],$db['pass'],$db['db'],$db['port']);
		$q = $db->query("select * from `users` where `facebook` = '".$user."'");
	
		if ( $q->num_rows > 0 ) {
			$data = $q->fetch_assoc();
			$_SESSION['session'] = substr(sha1(time().$twuser->id),0,10);
			$db->query("update `users` set `session` = '".$_SESSION['session']."', `fb_token` = '".$facebook->getAccessToken()."' where `id` = '".$data['id']."'");
			$_SESSION['userid'] = $data['id'];
		}
		else {
			$fbid = $_SESSION['facebook']; 
			$user_profile = $facebook->api('/me','GET');
			$fullname = $user_profile['name'];
			$_SESSION['session'] = substr(sha1(time().$twuser->id),0,10);
			$q = $db->query("insert into `users` values ('0', '".$fullname."', '', '0', '', '', '".$fbid."', '".$facebook->getAccessToken()."', '".time()."','".$_SESSION['session']."')");
			$_SESSION['userid'] = $db->insert_id;
		}	
		setcookie("portall_session",$_SESSION['session'],time()+60*60*24,'/','portall.eu5.org');
		header('Location: /'); 
	}
	
	if ( !$user ) {
		$params = array(
			'scope' => 'read_stream, friends_likes, read_mailbox'
		);
		$loginUrl = $facebook->getLoginUrl($params);	
		header("Location: ".$loginUrl);
	}
	
	if ( isset($_GET['code']) ) header("Location: /home");
	
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
			echo '<tr><td>'. $post['from']['name'] .':<br />'. $post['message'] .'</td></tr>';
		}
		
		echo"</tbody>
					</table>";
	}
?>