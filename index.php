<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" id="ng-app" ng-app="DayZEpoch" ng-controller="AppController">
    <head>
        <?php include "config.php" ?>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>DayZ Epoch Stats For <?php echo $SERVER_NAME; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- css includes -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/main.css">

        <!-- respond goes here for compatibility -->
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
      <!--[if lt IE 9]>
          <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
      <![endif]-->
      <!-- navbar -->
      <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <span class="navbar-brand"><?php echo $SERVER_NAME; ?> Server Stats</span>
            </div>
            <ul class="nav navbar-nav">
              <li ng-class="{active: isActive('/home')}"><a href="#/home">Home</a></li>
              <li ng-class="{active: isActive('/players')}"><a href="#/players">Players</a></li>
              <li ng-class="{active: isActive('/objects')}"><a href="#/objects">Objects</a></li>
            </ul>
        </div>
      </nav>
      
      <!-- main page content loads here-->
      <div id="content" class="container" ng-view></div>
      
      <!-- footer -->
      <footer class="footer">&copy; 2014 -- <?php echo $SERVER_NAME; ?> -- powered by <a href="https://github.com/mudzereli/DayZ-Epoch-Web-Stats">DayZ Epoch Server Stats</a></footer>

      <!-- javascript includes -->
      <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.16/angular.min.js"></script>
      <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.16/angular-route.js"></script>
      <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.10.0/ui-bootstrap-tpls.min.js"></script>
      <script src="js/vendor/ng-google-chart.js"></script>
      <script src="app/app.js"></script>
      <script src="js/main.js"></script>
    </body>
</html>
