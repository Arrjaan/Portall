<?php

if ( !$_SESSION['userid'] ) {
	$type = "home";
	$content = '<p>Social networking reinvented.</p>
        <p>
			<a href="/login/twitter"><img src="/lib/layout/img/twitter.png" alt="Sign in with Twitter" /></a><br />
			<a href="/login/facebook"><img src="/lib/layout/img/facebook.png" alt="Sign in with Facebook" /></a>
		</p>';
}
else header("Location: /home");

?>