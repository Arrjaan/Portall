<?php

if ( $page[2] == "twitter" ) header("Location: /lib/twitter/clearsessions.php");
if ( $page[2] == "facebook" ) header("Location: /lib/facebook/index.php");
if ( $page[2] !== "twitter" && $page[2] !== "facebook" ) header("Location: /");

?>