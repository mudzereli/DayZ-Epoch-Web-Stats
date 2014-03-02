<?php
	include "config.php";
	
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);

    $myquery = "select * from t_aggregate_stats";
	
    $query = mysql_query($myquery);

    if ( ! $query ) {
        echo mysql_error();
        die;
    }

	while ($row = mysql_fetch_assoc($query)) {
		$aggregate_stats[$row["METRIC"]] = $row["VALUE"];
	}

	arsort($aggregate_stats);
	
    mysql_close($server);
?>