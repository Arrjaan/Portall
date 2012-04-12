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

if ( $_SERVER['REQUEST_METHOD'] == "GET" ) 
	$tweets = $connection->get($_REQUEST['call']);
if ( $_SERVER['REQUEST_METHOD'] == "POST" ) {
	$args = $_POST;
	unset($args['call']);
	$tweets = $connection->post($_REQUEST['call'], $args);
}
	

/* If method is set change API call made. Test is called by default. */
if ( $_REQUEST['call'] == "statuses/home_timeline" ) {	
	echo "<h2>Twitter</h2>";
	echo '<table class="table">
			<thead>
				<tr>
					<th>Tweets</th>
				</tr>
			</thead><tbody>';
			
	foreach ( $tweets as $tweet ) {
		echo '<tr><td><img src="'. $tweet->user->profile_image_url . '" /></td><td>'. $tweet->user->name .':<br />'. $tweet->text .'</td></tr>';
	}
	
	echo"</tbody>
				</table>";
}

if ( !empty($_REQUEST['call'] ) ) {
	echo $connection->http_code;
}

if ( !isset($_REQUEST['call']) ) header("Location: /");

/* Some example calls */
//$connection->get('users/show', array('screen_name' => 'abraham'));
//$connection->post('statuses/destroy', array('id' => 5437877770));
//$connection->post('friendships/create', array('id' => 9436992));
//$connection->post('friendships/destroy', array('id' => 9436992));

/* Include HTML to display on the page */
//include('html.inc');
