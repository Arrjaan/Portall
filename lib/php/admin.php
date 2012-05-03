<?php
require_once('lib/twitter/twitteroauth/twitteroauth.php');

$access_token = $_SESSION['access_token'];
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

function idsToScreenName($ids) {
	global $connection;
	$users = $connection->post("users/lookup", array("user_id" => $ids));
	$names = array();
	
	foreach ( $users as $key => $user ) {
		$names[$key] = $user->screen_name;
	}
	
	return $names;
}


if ( $_SESSION['userid'] == '1' ) {
	$q = $db->query("select * from `users`");
	
	$type = "home";
	$content = '<p>Administration Panel</p>
        <p>
			<table class="table">
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Twitter</th>
						<th>Facebook</th>
						<th>Registered at</th>
					</tr>
				</thead>
				<tbody>';
	
	$ids = '';
	
	while ( $data = $q->fetch_assoc() ) {
		if ( $data['twitter'] !== "0" ) $ids .= $data['twitter'].',';
	}
	
	$ids = substr($ids, 0, -1);
	$ids = idsToScreenName($ids);	
	
	$q = $db->query("select * from `users`");
	
	while ( $data = $q->fetch_assoc() ) {
		if ( $data['twitter'] == "0" ) $twitter = '<i class="icon-remove"></i>';
		else {
			$twitter = '<a href="http://twitter.com/'.$ids[count($ids) - 1].'"><i class="icon-ok"></i></a>';
			array_pop($ids);
		}
		if ( $data['facebook'] == "0" ) $facebook = '<i class="icon-remove"></i>';
		else $facebook = '<a href="http://facebook.com/'.$data['facebook'].'"><i class="icon-ok"></i></a>';
		if ( $data['email'] == "" ) $email = ' - ';
		else $email = '<a href="mailto:'.$data['email'].'">'.$data['email'].'</a>';
		
		$content .= '				<tr>
						<td>'.$data['fullname'].'</td>
						<td>'.$email.'</td>
						<td>'.$twitter.'</td>
						<td>'.$facebook.'</td>
						<td>'.date("d-m-Y H:i:s O",$data['time']).'</td>
					</tr>';
	}
	$content .= '			</tbody>
			</table>
		</p>';
}
else header("Location: /home");

?>