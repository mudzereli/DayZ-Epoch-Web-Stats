<?php
	
	include "../config.php";
	
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);

    $myquery = "
	select case when HUMANITY >= 5000  then 'Hero'
			    when HUMANITY <= -5000 then 'Bandit'
				else                        'Survivor'
		   end as LIFESTYLE,
		   count(*) as COUNT
	from Character_DATA
	where ALIVE = 1 /* and date(LASTLOGIN) >= now() - INTERVAL 15 DAY */
	group by LIFESTYLE
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