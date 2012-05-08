<?php

switch ( $page[2] ) {
	case "session":
		echo $_SESSION[$page[3]];
	break;
}

die();
?>