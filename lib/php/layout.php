<?php session_start(); ?>
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
			ajax('/lib/facebook/index.php?call=/me/home', 'span2'); 
			setTimeout("uFacebook()",60000);
		}
		function uTwitter() {
			ajax('/lib/twitter/index.php?call=statuses/home_timeline', 'span1');
			setTimeout("uTwitter()",45000);
		}
		$(document).ready(function(){
			<?php 
			if ( $_SESSION['facebook'] && isset($_SESSION['facebook']) ) {
			?>
			uFacebook();
			document.getElementById('span2').innerHTML = 'Loading...';
			<?php
			}
			if ( $_SESSION['facebook'] && isset($_SESSION['facebook']) && !empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) ) {
			?>	
			setTimeout("uTwitter()",5000);
			document.getElementById('span1').innerHTML = 'Loading...';
			<?php
			}
			if ( !isset($_SESSION['facebook']) && !empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) ) {
			?>
			uTwitter();
			document.getElementById('span1').innerHTML = 'Loading...';
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
              <li><a href="#tw">Twitter</a></li>
              <li><a href="#fb">Facebook</a></li>
            </ul>
			<ul class="nav pull-right">
			  <li><a href="/lib/twitter/clearsessions.php?logout">Logout</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container" id="content">
	<?php if ( isset($_GET['debug']) ) { echo "SESSIONS:<br />"; print_r($_SESSION); echo "<br /><br />COOKIES:<br />"; print_r($_COOKIE); echo "<br /><br />"; } ?>
	<?php if ( $type == "home" ) { ?>
	<!-- Main hero unit -->
      <div class="hero-unit">
        <h1>Portall</h1>
        <?php echo $content; ?>
      </div>
	<?php } else { ?>
	<form onsubmit="post('/lib/twitter/index.php?call=statuses/update','status=' + encodeURIComponent(document.getElementById('nTweet').value), 'postM'); return false;" style="text-align: center;" >
		<div id="postM" class="input-append control-group">
			<div class="controls">
				<input onfocus="this.value = this.value;" onkeyup="count(this.value)" type="text" id="nTweet" class="input-large search-query"/>
				<button type="button" onclick="post('/lib/twitter/index.php?call=statuses/update','status=' + encodeURIComponent(document.getElementById('nTweet').value), 'postM');" class="btn">Tweet!</button>
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
				if ( !empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret']) )
					echo '<botton class="btn btn-primary" onclick="ajax(\'/lib/twitter/index.php?call=statuses/home_timeline\', \'span1\')">Laad Tweets</botton>';
				else 
					echo '<a href="/lib/twitter/clearsessions.php"><img src="/lib/layout/img/twitter.png" alt="Sign in with Twitter" /></a>';
			?>
		</div>
		<a name="fb"></a>
        <div class="span4" id="span2">
          <h2>Facebook</h2>
			<?php
				if ( $_SESSION['facebook'] && isset($_SESSION['facebook']) )
					echo '<botton class="btn btn-primary" onclick="ajax(\'/lib/facebook/index.php?call=/me/home\', \'span2\')">Laad Posts</botton>';
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

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="/lib/layout/js/jquery.js"></script>
    <script src="/lib/layout/js/bootstrap-transition.js"></script>
    <script src="/lib/layout/js/bootstrap-alert.js"></script>
    <script src="/lib/layout/js/bootstrap-modal.js"></script>
    <script src="/lib/layout/js/bootstrap-dropdown.js"></script>
    <script src="/lib/layout/js/bootstrap-scrollspy.js"></script>
    <script src="/lib/layout/js/bootstrap-tab.js"></script>
    <script src="/lib/layout/js/bootstrap-tooltip.js"></script>
    <script src="/lib/layout/js/bootstrap-popover.js"></script>
    <script src="/lib/layout/js/bootstrap-button.js"></script>
    <script src="/lib/layout/js/bootstrap-collapse.js"></script>
    <script src="/lib/layout/js/bootstrap-carousel.js"></script>
    <script src="/lib/layout/js/bootstrap-typeahead.js"></script>
  </body>
</html>