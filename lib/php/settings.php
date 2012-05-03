<?php

if ( !$_SESSION['userid'] ) {
	header('Location: /');
	die();
}
$q = $db->query("select * from `users` where `id` = '".$_SESSION['userid']."'");
$user = $q->fetch_assoc();

if ( !empty($_POST['email']) ) {
	$_SESSION['email_code']	= substr(sha1($_POST['email'].time()), 0, 10);
	$_SESSION['email'] = $_POST['email'];
	
	$message = "<p>Hello!</p>\n<p>Welcome at Portal! Your verification code is:</p>\n\n<p>".$_SESSION['email_code']."</p>\n\n<p>Kind Regards,</p>\n\n<p>Portall</p>";
	$message = wordwrap($message, 70);
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'To: '.$user["fullname"].' <'.$_POST["email"].'>' . "\r\n";
	$headers .= 'From: Portall <noreply@portall.eu5.org>' . "\r\n";
	
	if ( !mail($_POST['email'], "Portall Account Verification", $message, $headers) ) die('Mail error!');
	header('Location: /settings/verify');
	die();
}

switch ($page[2]) {
	case "home":
		$page_title = "Welcome!";
		$type = "home";
		$content = ':)<br />';
	break;
	case "verify":
		$page_title = "Welcome!";
		$type = "home";
		$content = '<p>Please enter the verification code that you have received via email.</p>
			<form method="post" class="form-inline">
				<input name="email_confirm">
				<button type="submit" class="btn btn-primary">Verify</button>
			</form>';
	break;
	case "timezone":
		$type = "home";
		$content = '<p>It\'s time to set your timezone!</p>
			<form method="post" class="form-inline">
				<select onchange="setRegion(this.value)">
					<option value="0">Africa</option>
					<option value="1">America</option>
					<option value="2">Antartica</option>
					<option value="3">Asia</option>
					<option value="4">Atlantic</option>
					<option value="5">Europe</option>
					<option value="6">Indian</option>
					<option value="7">Pacific</option>
				</select>
				<br /><br />
				<select placeholder="Select a region first." name="tz" id="tz">
					<option>Select a region first.</option>
				</select>
				<br /><br />
				<button type="submit" class="btn btn-primary">Submit</button> 
			<a href="/settings" class="btn">Back</a>
			</form><script>setRegion(0)</script>';
	break;
	case "getTimezone":
		static $regions = array(
			'Africa' => DateTimeZone::AFRICA,
			'America' => DateTimeZone::AMERICA,
			'Antarctica' => DateTimeZone::ANTARCTICA,
			'Asia' => DateTimeZone::ASIA,
			'Atlantic' => DateTimeZone::ATLANTIC,
			'Europe' => DateTimeZone::EUROPE,
			'Indian' => DateTimeZone::INDIAN,
			'Pacific' => DateTimeZone::PACIFIC
		);

		foreach ($regions as $name => $mask) {
			$tzlist[] = DateTimeZone::listIdentifiers($mask);
		}

		die(json_encode($tzlist[$page[3]]));
	break;
	default:
		if ( empty($user['email']) ) {
			$page_title = "Welcome!";
			$type = "home";
			$content = '<p>Please enter your email address to continue.</p>
				<form method="post" class="form-inline">
					<input name="email" pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$" type="email" placeholder="email@example.com">
					<button type="submit" class="btn btn-primary">Submit</button>
				</form>';
		}
		else header("Location: /settings/home");
	break;
}

if ( !empty($_POST['email_confirm']) ) {
	if ( $_SESSION['email_code'] == $_POST['email_confirm'] ) {
		$db->query("update `users` set `email` = '".$_SESSION['email']."' where `id` = '".$_SESSION['userid']."'");
		header('Location: /settings/home');
	}
	else {
		$page_title = "Welcome!";
		$type = "home";
		$content = '
			<p>Please enter the verification code that you have received via email.</p>
			<div class="alert alert-error alert-block"> 
				<!-- <a class="close" data-dismiss="alert" href="#">&times;</a>-->
				Whoops! The code that you have entered is wrong. :(
			</div>
			<form method="post" class="form-inline">
				<input name="email_confirm">
				<button type="submit" class="btn btn-primary">Verify</button>
			</form>';
	}
}

if ( !empty($_POST['tz']) ) {
	$q = $db->query("select * from `prefs` where `id` = '".$_SESSION['userid']."'");
	
	if ( $q->num_rows > 0 ) $db->query("update `prefs` set `timezone` = '".$_POST['tz']."' where `id` = '".$_SESSION['userid']."'");
	else $db->query("insert into `prefs` (`id`,`timezone`) values ('".$_SESSION['userid']."','".$_POST['tz']."')");
	
	$type = "home";
	$content = '<p>It\'s time to set your timezone!</p>
		<form method="post" class="form-inline">
			<div class="alert alert-success alert-block"> 
				<!-- <a class="close" data-dismiss="alert" href="#">&times;</a>-->
				Timezone altered. :)
			</div>
			<select onchange="setRegion(this.value)">
				<option value="0">Africa</option>
				<option value="1">America</option>
				<option value="2">Antartica</option>
				<option value="3">Asia</option>
				<option value="4">Atlantic</option>
				<option value="5">Europe</option>
				<option value="6">Indian</option>
				<option value="7">Pacific</option>
			</select>
			<br /><br />
			<select placeholder="Select a region first." name="tz" id="tz">
				<option>Select a region first.</option>
			</select>
			<br /><br />
			<button type="submit" class="btn btn-primary">Submit</button> 
			<a href="/settings" class="btn">Back</a>
		</form><script>setRegion(0)</script>';
}
?>

