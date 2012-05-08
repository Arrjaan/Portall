<?php

function limitStatus() {
	if ( $_SESSION['limit'] > 299 ) return "badge-success";
	if ( $_SESSION['limit'] > 149 ) return "badge-info";
	if ( $_SESSION['limit'] > 99 ) return "badge-warning";
	if ( $_SESSION['limit'] < 51 ) return "badge-important";
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
