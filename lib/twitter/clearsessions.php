<?php
/**
 * @file
 * Clears PHP sessions and redirects to the connect page.
 */
 
/* Load and clear sessions */
session_start();

unset($_SESSION['access_token']['oauth_token']);
unset($_SESSION['access_token']['oauth_token_secret']);
unset($_SESSION['access_token']);
unset($_SESSION['userid']);
unset($_SESSION['session']);
unset($_COOKIE['portall_session']);
setcookie("portall_session", "", time()-3600,'/','portall.eu5.org');

 
/* Redirect to page with the connect to Twitter option. */
if ( !isset($_GET['logout']) ) header('Location: ./connect.php');
else  header('Location: /');
