<?php

function closeBtn() {
	return " <span class=\"close\"><a href=\"#\" onclick=\"ajax('/lib/twitter/index.php?call=account/verify_credentials','span3');\"><img src=\"/lib/layout/img/tw.png\"></a> <a href=\"#\" onclick=\"ajax('/lib/facebook/index.php?call=/".$_SESSION['facebook']."', 'span3');\"><img src=\"/lib/layout/img/fb.png\"></a></span></h2>";
}

function restoreLogin($data) {
	if ( empty($_SESSION['limit']) ) $_SESSION['limit'] = '?';
	$_SESSION['access_token'] = 
		array( 
			"oauth_token" => $data['tw_token'],
			"oauth_token_secret" => $data['tw_secret']
		);
	if ( empty($_SESSION['facebook']) && !empty($data['facebook']) ) {
		$_SESSION['fb_436057069743792_access_token'] = $data['fb_token'];
		$_SESSION['fb_436057069743792_user_id'] = $data['facebook'];
		$_SESSION['facebook'] = $data['facebook'];
	}
	$_SESSION['userid'] = $data['id'];
	$_SESSION['session'] = $_COOKIE['portall_session'];
	setcookie("portall_session",$_SESSION['session'],time()+60*60*24,'/','portall.eu5.org');
}

function limitStatus() {
	if ( $_SESSION['limit'] > 299 ) return "badge-success";
	if ( $_SESSION['limit'] > 149 ) return "badge-info";
	if ( $_SESSION['limit'] > 50 ) return "badge-warning";
	if ( $_SESSION['limit'] < 51 ) return "badge-error";
}

function timetostr($time) {
    $time_difference = time() - $time ;

    $seconds    = $time_difference ;
    // Seconds
    if($seconds <= 60) {
        return "$seconds seconds ago";
    }

    $minutes    = round($time_difference / 60 );
    //Minutes
    if($minutes <= 60) {
        if($minutes == 1) {
            return "one minute ago";
        } else {
            return "$minutes minutes ago";
        }
    }
   
    $hours      = round($time_difference / 3600 );
    //Hours
    if($hours <= 24) {
        if($hours == 1) {
            return "one hour ago";
        } else {
            return "$hours hours ago";
        }
    }
   
    $days      = round($time_difference / 86400 );
    //Days
    if($days <= 7) {
        if($days == 1) {
            return "one day ago";
        } else {
            return "$days days ago";
        }
    }
   
    $weeks      = round($time_difference / 604800 );
    //Weeks
    if($weeks <= 4) {
        if($weeks == 1) {
            return "one week ago";
        }else{
            return "$weeks weeks ago";
        }
    }
   
    $months    = round($time_difference / 2419200 );
    //Months
    if($months <= 12) {
        if($months==1) {
            return "one month ago";
        } else {
            return "$months months ago";
        }
    }
   
    $years      = round($time_difference / 29030400 );
    //Years
    if($years==1) {
        return "one year ago";
    } else {
        return "$years years ago";
    }
}

?>
