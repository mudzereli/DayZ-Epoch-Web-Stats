<?php
	
	include "../config.php";
	
    $server = mysql_connect($host, $username, $password);
    $connection = mysql_select_db($database, $server);
	
	if (strcmp($_GET["cat"],"ALL") == 0) {
		$myquery = "select * from v_object_class";
	} else {
		$myquery = "select * from v_object_class where CATEGORY = '" . $_GET["cat"] . "'";
	}
	
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