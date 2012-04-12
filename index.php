<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Social Media Reinvented.">
    <meta name="author" content="Arrjaan">

    <!-- Le styles -->
    <link href="lib/layout/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="lib/layout/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="lib/layout/ico/favicon.ico">
	
	<!-- Le scripts -->
	<script src="lib/js/jquery.js"></script>
	<script src="lib/js/ajax.js"></script>
  </head>

  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Portal</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#tw">Twitter</a></li>
              <li><a href="#fb">Facebook</a></li>
              <li><a href="#nw">Add New Service</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container" id="content">

      <!-- Example row of columns -->
      <div class="row">
        <div class="span4" id="row1">
          <h2>Twitter</h2>
			<a onclick="ajax('lib/twitter/redirect.php');"><img src="lib/twitter/images/lighter.png" alt="Sign in with Twitter" /></a>
			<a onclick="ajax('lib/twitter/index.php?call=statuses/home_timeline');"><img src="lib/twitter/images/darker.png" alt="Sign in with Twitter" /></a>
        </div>
      </div>

      <hr>

      <footer>
        <p>&copy; Company 2012</p>
      </footer>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="lib/layout/js/jquery.js"></script>
    <script src="lib/layout/js/bootstrap-transition.js"></script>
    <script src="lib/layout/js/bootstrap-alert.js"></script>
    <script src="lib/layout/js/bootstrap-modal.js"></script>
    <script src="lib/layout/js/bootstrap-dropdown.js"></script>
    <script src="lib/layout/js/bootstrap-scrollspy.js"></script>
    <script src="lib/layout/js/bootstrap-tab.js"></script>
    <script src="lib/layout/js/bootstrap-tooltip.js"></script>
    <script src="lib/layout/js/bootstrap-popover.js"></script>
    <script src="lib/layout/js/bootstrap-button.js"></script>
    <script src="lib/layout/js/bootstrap-collapse.js"></script>
    <script src="lib/layout/js/bootstrap-carousel.js"></script>
    <script src="lib/layout/js/bootstrap-typeahead.js"></script>
  </body>
</html>
