<!DOCTYPE html>
<html class="no-js">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/bootswatch/3.1.1/yeti/bootstrap.min.css" rel="stylesheet">
        <!-- Place favicon.ico and apple-touch-icon(s) in the root directory -->
        <script src="http://ajax.aspnetcdn.com/ajax/modernizr/modernizr-2.7.1.js"></script>
  		<script src="http://d3js.org/d3.v3.min.js"></script>
  		<script src="http://dimplejs.org/dist/dimple.v1.1.5.min.js"></script>
        <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="js/stats.js"></script>
        <style>.tooltip{opacity: 0.9;}</style> <!-- this fixes a conflict with dimple and bootstrap -->
    </head>
    <body>
        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        <?php include "widgets/aggregate_stats.php" ?>
        <nav class="navbar navbar-default" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">DayZ Epoch Web Stats Example</a>
                </div>
                
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
                	<h4 class="text-right text-muted" href="#">Average Lifespan: <?php echo $aggregate_stats["Avg Mins Lived"]; ?> mins. <span class="text-danger">Ready To Die?</span></h4>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <div class="container">
        	<div class="jumbotron text-center">
            	<h1>Over <?php echo $aggregate_stats["Unique Players"]; ?> unique players!</h1>
            	<h3><?php echo $aggregate_stats["Active Players"]; ?> active in last 2 weeks</h3>
            </div>
        	<div class="row">
            	<div class="col-md-4">
                	<div class="panel panel-info">
                    	<div class="panel-heading">Aggregate Stats</div>
                        <div class="panel-body">
                            <table class="table">
                            	<thead hidden>
                                	<th>Statistic</th>
                                    <th class="text-right">Amount</th>
                                </thead>
                                <tbody>
                                    <?php foreach($aggregate_stats as $key => $value) { ?>
                            			<tr>
                                        	<td><?php echo $key; ?></td>
                                        	<td class="text-right"><?php echo $value; ?></td>
                                        </tr>				
									<?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                	<div class="panel panel-danger">
                    	<div class="panel-heading">Last Week</div>
                        <div class="panel-body text-center"><div id="div-daily-stats-sm"></div></div>
                    </div>
                	<div class="panel panel-warning">
                    	<div class="panel-heading">Deployables <span class="badge pull-right"><?php echo $aggregate_stats["Deployables"]; ?></span></div>
                        <div class="panel-body text-center"><div id="div-server-deployables"></div></div>
                    </div>
                	<div class="panel panel-warning">
                    	<div class="panel-heading">Vehicles <span class="badge pull-right"><?php echo $aggregate_stats["Vehicles"]; ?></span></div>
                        <div class="panel-body text-center"><div id="div-server-vehicles"></div></div>
                    </div>
                	<div class="panel panel-warning">
                    	<div class="panel-heading">Lockables <span class="badge pull-right"><?php echo $aggregate_stats["Lockables"]; ?></span></div>
                        <div class="panel-body text-center"><div id="div-server-lockables"></div></div>
                    </div>
                </div>
                <div class="col-md-8">
                	<div class="panel panel-danger">
                    	<div class="panel-heading">Server Lifeline (Last Month)</div>
                        <div class="panel-body text-center"><div id="div-daily-stats"></div></div>
                    </div>
                	<div class="panel panel-warning">
                    	<div class="panel-heading">Server Object Summary <span class="badge pull-right"><?php echo $aggregate_stats["Objects"]; ?></span></div>
                        <div class="panel-body text-center"><div id="div-server-objects"></div></div>
                    </div>
                	<div class="panel panel-success">
                    	<div class="panel-heading">Living Survivor Summary <span class="badge pull-right"><?php echo $aggregate_stats["Live Characters"]; ?></span></div>
                        <div class="panel-body text-center"><div id="div-server-survivors"></div></div>
                    </div>
                	<div class="panel panel-danger">
                    	<div class="panel-heading">Death Log <span class="badge pull-right"><?php echo ($aggregate_stats["Dead Characters"] + $aggregate_stats["Zombie Kills"] + $aggregate_stats["Bandits PKed"] + $aggregate_stats["Survivors PKed"]); ?></span></div>
                        <div class="panel-body text-center"><div id="div-server-deaths"></div></div>
                    </div>
                </div>
            </div>
        </div>
        <script>
			$(document).ready(function() {
				daily_stats("#div-daily-stats",30,true,500);
				daily_stats("#div-daily-stats-sm",7,false,500);
				object_stats("#div-server-objects","ALL",true,false,500);
				object_stats("#div-server-deployables","DEPLOYABLE",false,false,500);
				object_stats("#div-server-vehicles","VEHICLE",false,false,500);
				object_stats("#div-server-lockables","LOCKABLE",false,false,500);
				survivor_stats("#div-server-survivors",true,false,500);
				survivor_stats("#div-server-survivors",false,false,500);
				death_stats("#div-server-deaths",true,false,500);
				death_stats("#div-server-deaths",false,false,500);
			});
		</script>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
    </body>
</html>
