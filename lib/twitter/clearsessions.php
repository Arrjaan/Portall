<?php
session_start();

if ( !isset($_GET['logout']) ) {
	unset($_SESSION['access_token']['oauth_token']);
	unset($_SESSION['access_token']['oauth_token_secret']);
	unset($_SESSION['access_token']);
	header('Location: ./connect.php');
}
else {
	setcookie("portall_session", "", time()-3600,'/','portall.eu5.org');
	session_destroy();
	header('Location: /');
}
?>