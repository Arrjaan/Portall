<?php session_start(); if ( isset($_GET['debug']) ) $_SESSION['debug'] = true; if ( isset($_GET['halt']) ) $_SESSION['debug'] = false; if ( !isset($_SESSION['debug']) ) $_SESSION['debug'] = false; ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Portall</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Social Media Reinvented.">
    <meta name="author" content="Arrjaan">

    <!-- Le styles -->
    <link href="/lib/layout/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="/lib/layout/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="/lib/layout/css/sprite.css" rel="stylesheet">

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="/lib/layout/ico/favicon.ico">
	
	<!-- Le scripts -->
	<script src="/lib/js/jquery.js"></script>
	<script src="/lib/js/ajax.js"></script>
  </head>

  <body>
	<script>
		function uFacebook() {
			<?php 
			if ( $page[1] == "home" ) echo "ajax('/lib/facebook/index.php?call=/me/home', 'span2');";
			if ( $page[1] == "notify" ) echo "ajax('/lib/facebook/index.php?call=/me/home', 'span2');";
			?>
			setTimeout("uFacebook()",60000);
		}
		function uTwitter() {
			<?php 
			if ( $page[1] == "home" ) echo "ajax('/lib/twitter/index.php?call=statuses/home_timeline', 'span1');";
			if ( $page[1] == "notify" ) echo "ajax('/lib/twitter/index.php?call=statuses/mentions', 'span1');";
			?>
			setTimeout("uTwitter()",45000);
		}
		$(document).ready(function(){
			var postStatus = new Array(); 
			setStatus("Twitter","true");
			setStatus("Facebook","false");	
			setFBID("<?php echo $_SESSION['facebook']; ?>");
			document.getElementById('span3').innerHTML = '<h2>User Info</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />';
			<?php // FB + TW
			if ( isset($_SESSION['facebook']) && !empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) ) {
			?>
			uTwitter();
			uFacebook();
			ajax('/lib/facebook/index.php?call=/<?php echo $_SESSION['facebook']; ?>', 'span3');
			document.getElementById('span1').innerHTML = '<h2>Twitter</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />';
			document.getElementById('span2').innerHTML = '<h2>Facebook</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />';
			<?php
			} // TW
			if ( !isset($_SESSION['facebook']) && !empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) ) {
			?>	
			uTwitter();
			ajax('/lib/twitter/index.php?call=account/verify_credentials','span3');
			document.getElementById('span1').innerHTML = '<h2>Twitter</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />';
			<?php
			} // FB
			if ( $_SESSION['facebook'] && isset($_SESSION['facebook']) && empty($_SESSION['access_token']) && empty($_SESSION['access_token']['oauth_token']) && empty($_SESSION['access_token']['oauth_token_secret']) ) {
			?>
			uFacebook();
			ajax('/lib/facebook/index.php?call=/<?php echo $_SESSION['facebook']; ?>', 'span3');
			document.getElementById('span2').innerHTML = '<h2>Facebook</h2><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." />';
			<?php
			}
			?>
		});
	</script>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
			<span class="icon-bar"></span>
          </a>
          <a class="brand" href="/">Portall</a>
          <div class="nav-collapse">
            <ul class="nav">
				<li><a href="/home">Home</a></li>
				<li><a href="/notify">Notifications</a></li>
            </ul>
			<ul class="nav pull-right">
				<li class="dropdown" id="menu1">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#menu1">Debug <b class="caret"></b></a>
					<ul class="dropdown-menu">
						<li><a href="?debug">Start Debug</a></li>
						<li><a href="?halt">Stop Debug</a></li>
						<li class="divider"></li>
						<li><a href="?clear">Clear all sessions</a></li>
					</ul>
				</li>
				<li><a href="/lib/twitter/clearsessions.php?logout">Logout</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="container" id="content">
		<audio id="sound">
			<source src="/lib/sound/song.ogg" type="audio/ogg" />
			<source src="/lib/sound/song.mp3" type="audio/mpeg" />
			<center>Your browser does not support <a class="initialism" title="The newest method to display a webpage." href="http://en.wikipedia.org/wiki/HTML5">HTML5</a>. Why not <a href="http://browsehappy.com">&raquo; upgrade your browser</a>?</center><br /><br />
		</audio>
	<?php if ( $_SESSION['debug'] ) { echo "SESSIONS:<br />"; print_r($_SESSION); echo "<br /><br />COOKIES:<br />"; print_r($_COOKIE); echo "<br /><br />"; ?>
	<a onclick="document.getElementById('sound').play();">&raquo; Test sound</a><br />
	<a onclick="document.getElementById('source').innerHTML = quene.length + ': ' + quene.toSource() + '<br /><br />';">&raquo; View AJAX status</a><br />
	<span id="source"></span>
	<?php } ?>
	<?php if ( $type == "home" ) { ?>
      <div class="hero-unit">
        <h1>Portall</h1>
        <?php echo $content; ?>
      </div>
	<?php } else { ?>
	<form onsubmit="status();return false;" style="text-align: center;" >
		<div id="postM" class="input-append control-group">
			<div class="controls">
				<label id="postPrefs">Twitter <a onclick="setStatus('Twitter', 'false');"><i class="icon-ok"></i></a> | Facebook <a onclick="setStatus('Facebook', 'true');"><i class="icon-remove"></i></a></label>
				<input size="150" onkeyup="count(this.value)" type="text" id="nTweet" class="input-xlarge search-query"/><button type="button" onclick="status();" class="btn">Tweet!</button>
				<span id="postMHelp" class="help-inline">140</span>
			</div>
		</div>	
	</form>
      <!-- Columns -->
      <div class="row">
		<a name="tw"></a>
        <div class="span4" id="span1">
          <h2>Twitter</h2>
			<?php
				if ( !empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) ) {
					if ( $page[1] == "home" ) echo '<botton class="btn btn-primary" onclick="ajax(\'/lib/twitter/index.php?call=statuses/home_timeline\', \'span1\')">Laad Tweets</botton>';
					if ( $page[1] == "notify" ) echo '<botton class="btn btn-primary" onclick="ajax(\'/lib/twitter/index.php?call=statuses/mentions\', \'span1\')">Laad Tweets</botton>';
				}
				else 
					echo '<a href="/lib/twitter/clearsessions.php"><img src="/lib/layout/img/twitter.png" alt="Sign in with Twitter" /></a>';
			?>
		</div>
		<a name="fb"></a>
        <div class="span4" id="span2">
          <h2>Facebook</h2>
			<?php
				if ( $_SESSION['facebook'] && isset($_SESSION['facebook']) ) {
					if ( $page[1] == "home" ) echo '<botton class="btn btn-primary" onclick="ajax(\'/lib/facebook/index.php?call=/me/home\', \'span2\')">Laad Posts</botton>';
					if ( $page[1] == "notify" ) echo '<botton class="btn btn-primary" onclick="ajax(\'/lib/facebook/index.php?call=/me/home\', \'span2\')">Laad Posts</botton>';
				}
				else 
					echo '<a href="/lib/facebook/index.php?action=auth"><img src="/lib/layout/img/facebook.png" alt="Sign in with Facebook" /></a>';		
			?>
		</div>
		<div class="span4" id="span3">
		</div>
      </div>
		<?php } ?>
      <hr>

      <footer>
        <p>&copy; <a href="http://arrjaan.github.com">Arrjaan</a> 2012</p>
      </footer>

    </div> <!-- /container -->
	
	<div class="modal fade" id="imgModal">
		<div class="modal-header">
			<a class="close" data-dismiss="modal"><i class="icon-remove"></i></a>
			<h3 id="imgModalTitle"></h3>
		</div>
		<div class="modal-body" style="text-align: center;" id="imgModalBody">
			<p><img src="/lib/layout/img/ajax-loader.gif" alt="Loading..." /></p>
		</div>
		<div id="imgModalFooter" class="modal-footer">
		</div>
    </div>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--
	
	-->
	<script src="/lib/layout/js/jquery.js"></script>
    <script src="/lib/layout/js/bootstrap-scrollspy.js"></script>
    <script src="/lib/layout/js/bootstrap-tab.js"></script>
    <script src="/lib/layout/js/bootstrap-tooltip.js"></script>
    <script src="/lib/layout/js/bootstrap-popover.js"></script>
    <script src="/lib/layout/js/bootstrap-button.js"></script>
    <script src="/lib/layout/js/bootstrap-carousel.js"></script>
    <script src="/lib/layout/js/bootstrap-typeahead.js"></script>
	<script src="/lib/layout/js/bootstrap-collapse.js"></script>
	<script src="/lib/layout/js/bootstrap-transition.js"></script>
    <script src="/lib/layout/js/bootstrap-modal.js"></script>
	<script src="/lib/layout/js/bootstrap-alert.js"></script>
	<script src="/lib/layout/js/bootstrap-dropdown.js"></script>
  </body>
</html>