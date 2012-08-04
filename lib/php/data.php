<?php

switch ( $page[2] ) {
	case "session":
		echo $_SESSION[$page[3]];
	break;
	case "limit":
		echo $_SESSION['limit'].'|'.date("H:i:s",$_SESSION['limit_reset']).'|'.limitStatus();
	break;
}

die();
?>