<?php
	
	include "../config.php";
	
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);

    $myquery = "
	select 'Zombie' as DEATH_TYPE, sum(KillsZ) as COUNT from Character_DATA
	union all
	select 'Hero' as DEATH_TYPE, sum(KillsH) as COUNT from Character_DATA
	union all
	select 'Bandit' as DEATH_TYPE, sum(KillsB) as COUNT from Character_DATA
	union all
	select 'Survivor' as DEATH_TYPE, count(*) as COUNT from Character_DATA where ALIVE = 0
	";
	
    $query = mysql_query($myquery);

    if ( ! $query ) {
        echo mysql_error();
        die;
    }

    $data = array();

    for ($x = 0; $x < mysql_num_rows($query); $x++) {
        $data[] = mysql_fetch_assoc($query);
    }

    echo json_encode($data);     

    mysql_close($server);
?>