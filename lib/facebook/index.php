<?php
	session_start(); 
	
	require 'facebook.php';
	require 'config.SECURE.php';

	$facebook = new Facebook(array(
	  'appId'  => CONSUMER_KEY,
	  'secret' => CONSUMER_SECRET,
	));

	// Get User ID
	$user = $facebook->getUser();
	$_SESSION['facebook'] = $user;
	
	if ( $_GET['action'] == "auth" ) {
		if ($user)
			$logUrl = $facebook->getLogoutUrl();
		else {
			$params = array(
			  'scope' => 'read_stream, friends_likes, read_mailbox',
			  'redirect_uri' => 'http://portall.eu5.org/lib/facebook/index.php'
			);
			$logUrl = $facebook->getLoginUrl($params);
		}
		header("Location: ".$logUrl);
	}
	
	if ( !empty($_GET['code']) ) header("Location: /");
	
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