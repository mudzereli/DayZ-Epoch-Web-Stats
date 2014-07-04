<?php

require "PDOError.php";
require "../config.php";
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get("/players/:order/:dir/:count/:page/:filter",'getPlayers');
$app->get("/count/players/:order/:dir/:count/:page/:filter",'getCountPlayers');
$app->get("/leaders",'getLeaders');
$app->get("/summary",'getSummary');
$app->get("/chartdata/humanity",'getHumanityChartData');
$app->get("/chartdata/avgkills",'getKillChartData');
$app->get("/chartdata/objects",'getObjectChartData');
$app->get("/chartdata/objectsbyclass",'getObjectClassChartData');
$app->get("/chartdata/vehiclesbyclass",'getVehicleClassChartData');
$app->get("/chartdata/structuresbyclass",'getStructureClassChartData');
$app->get("/logins/:limit",'getLogins');
$app->get("/chartdata/objectsbyupdateddate",'getObjectsByUpdatedDateChartData');
$app->get("/chartdata/objectsbycreateddate",'getObjectsByCreatedDateChartData');

$app->run();

function getPlayerUID($tbl) {
    global $HIDE_PLAYERUID;
    if($HIDE_PLAYERUID) { 
        return "'XXXXXXXXX' as PlayerUID"; 
    } else { 
        return $tbl . ".PlayerUID";
    } 
}

function getPlayers($order,$dir,$count,$page,$filter) {
    global $PLAYER_DATA, $CHARACTER_DATA;
    $PLAYERUID = getPlayerUID("a");
    $offset = intval($count) * (intval($page) - 1);
    $sql = "
        select a.*, b.Humanity, b.Generation, b.Alive, b.Model
        from (
            select $PLAYERUID, a.PlayerName, sum(b.KillsZ) as ZombieKills, sum(b.HeadshotsZ) as ZombieHeadshots, sum(b.KillsB) as BanditKills, sum(b.KillsH) as HumanKills, sum(b.DistanceFoot) as Distance, avg(Duration) as AvgLife, max(CharacterId) as LastCharacterId
            from $PLAYER_DATA a 
            inner join $CHARACTER_DATA b on a.PlayerUID = b.PlayerUID
            group by a.PlayerUID
        ) a
        inner join $CHARACTER_DATA b on a.LastCharacterId = b.CharacterId
    ";
    if($filter !== "no-filter") {
        $sql = $sql . " where lcase(a.PlayerName) like lcase('%$filter%') or a.PlayerUID like '%$filter%'";
    }
    $sql = $sql . " order by $order $dir limit $offset, $count";
    echo runQuery($sql);
}

function getCountPlayers($order,$dir,$count,$page,$filter) {
    global $PLAYER_DATA, $CHARACTER_DATA;
    $offset = intval($count) * (intval($page) - 1);
    $PLAYERUID = getPlayerUID("a");
    $sql = "
        select a.*, b.Humanity, b.Generation, b.Alive, b.Model
        from (
            select $PLAYERUID, a.PlayerName, sum(b.KillsZ) as ZombieKills, sum(b.HeadshotsZ) as ZombieHeadshots, sum(b.KillsB) as BanditKills, sum(b.KillsH) as HumanKills, sum(b.DistanceFoot) as Distance, avg(Duration) as AvgLife, max(CharacterId) as LastCharacterId
            from $PLAYER_DATA a 
            inner join $CHARACTER_DATA b on a.PlayerUID = b.PlayerUID
            group by a.PlayerUID
        ) a
        inner join $CHARACTER_DATA b on a.LastCharacterId = b.CharacterId
    ";
    if($filter !== "no-filter") {
        $sql = $sql . " where lcase(a.PlayerName) like lcase('%$filter%') or a.PlayerUID like '%$filter%'";
    }
    $sql = "select count(*) as RECORD_COUNT from ($sql) a";
    echo runQuery($sql);
}

function getLeaders() {
    global $PLAYER_DATA, $CHARACTER_DATA;
    echo runQuery("
        (select PlayerName, 'Zombie Kills' as Metric, sum(KillsZ) as Value 
        from $CHARACTER_DATA a inner join $PLAYER_DATA b on a.PlayerUID = b.PlayerUID 
        group by a.PlayerUID 
        order by sum(KillsZ) desc limit 1)
            union all
        (select PlayerName, 'Hero' as Metric, Humanity as Value 
        from $CHARACTER_DATA a inner join $PLAYER_DATA b on a.PlayerUID = b.PlayerUID 
        group by a.PlayerUID order by Humanity desc limit 1)
            union all
        (select PlayerName, 'Bandit' as Metric, Humanity as Value 
        from $CHARACTER_DATA a inner join $PLAYER_DATA b on a.PlayerUID = b.PlayerUID 
        group by a.PlayerUID order by Humanity asc limit 1)
            union all
        (select PlayerName, 'Deaths' as Metric, count(*) as Value 
        from $CHARACTER_DATA a inner join $PLAYER_DATA b on a.PlayerUID = b.PlayerUID 
        where Alive = 0
        group by a.PlayerUID order by count(*) desc limit 1)
    ");
}

function getHumanityChartData() {
    global $PLAYER_DATA, $CHARACTER_DATA;
    echo runQuery("
        select case when Humanity > 5000 then 'Hero' when Humanity < -500 then 'Bandit' else 'Survivor' end as LIFESTYLE, count(*) as TOTAL
        from $CHARACTER_DATA a 
        inner join (select max(CharacterId) as MaxCharacterId, PlayerUID from $CHARACTER_DATA group by PlayerUID) b on a.CharacterId = b.MaxCharacterId 
        inner join $PLAYER_DATA c on a.PlayerUID = c.PlayerUID
        group by case when Humanity > 5000 then 'Hero' when Humanity < -500 then 'Bandit' else 'Neutral' end
    ");
}

function getKillChartData() {
    global $PLAYER_DATA, $CHARACTER_DATA;
    echo runQuery("
        select 'Human Kills' as KILLTYPE, avg(KillsH) as TOTAL from $CHARACTER_DATA
        union all
        select 'Bandit Kills' as KILLTYPE, avg(KillsB) as TOTAL from $CHARACTER_DATA
        union all
        select 'Zombie Kills' as KILLTYPE, avg(KillsZ) as TOTAL from $CHARACTER_DATA
        union all
        select 'Zombie Headshots' as KILLTYPE, avg(HeadshotsZ) as TOTAL from $CHARACTER_DATA
    ");
}

function getObjectChartData() {
    global $OBJECT_DATA;
    echo runQuery("
        select case when Fuel > 0 or Hitpoints not in ('[]') or ClassName = 'MMT_Civ' then 'Vehicle' when Inventory not in ('[]','[[[],[]],[[],[]],[[],[]]]') then 'Storage'  else 'Structure' end as OBJECT_TYPE, count(*) as TOTAL
        from $OBJECT_DATA a 
        group by case when Fuel > 0 or Hitpoints not in ('[]') or ClassName = 'MMT_Civ' then 'Vehicle' when Inventory not in ('[]','[[[],[]],[[],[]],[[],[]]]') then 'Storage'  else 'Structure' end
    ");
}

function getLogins($limit) {
    global $CHARACTER_DATA, $PLAYER_DATA;
    echo runQuery("select a.PlayerUID, a.PlayerName, b.LastLogin from $PLAYER_DATA a inner join $CHARACTER_DATA b on a.PlayerUID = b.PlayerUID order by b.LastLogin desc limit $limit");
}

function getObjectsByCreatedDateChartData() {
    global $OBJECT_DATA;
    echo runQuery("
        select a.DATESTAMP, a.TOTAL as TOTAL_CREATED, a.TOTAL_VEHICLES as TOTAL_VEHICLES_CREATED, a.TOTAL-a.TOTAL_VEHICLES as TOTAL_STRUCTURES_CREATED
        from
            (select DATE_FORMAT(a.Datestamp,'%b %d') as DATESTAMP, count(*) as TOTAL, sum(case when Fuel > 0 or Hitpoints not in ('[]') or ClassName = 'MMT_Civ' then 1 else 0 end) as TOTAL_VEHICLES
            from $OBJECT_DATA a
            where a.Datestamp > (NOW() - INTERVAL 30 DAY)
            group by YEAR(a.Datestamp), MONTH(a.Datestamp), DAY(a.Datestamp)) a  
    ");
}

function getObjectsByUpdatedDateChartData() {
    global $OBJECT_DATA;
    echo runQuery("
        select a.DATESTAMP, a.TOTAL as TOTAL_UPDATED, a.TOTAL_VEHICLES as TOTAL_VEHICLES_UPDATED, a.TOTAL-a.TOTAL_VEHICLES as TOTAL_STRUCTURES_UPDATED
        from
            (select DATE_FORMAT(a.LastUpdated,'%b %d') as DATESTAMP, count(*) as TOTAL, sum(case when Fuel > 0 or Hitpoints not in ('[]') or ClassName = 'MMT_Civ' then 1 else 0 end) as TOTAL_VEHICLES
            from $OBJECT_DATA a 
            where a.LastUpdated > (NOW() - INTERVAL 30 DAY)
            group by YEAR(a.LastUpdated), MONTH(a.LastUpdated), DAY(a.LastUpdated)) a
    ");
}

function getObjectClassChartData() {
    global $OBJECT_DATA;
    echo runQuery("
        select ClassName, count(*) as TOTAL
        from $OBJECT_DATA a 
        group by ClassName
    ");
}

function getVehicleClassChartData() {
    global $OBJECT_DATA;
    echo runQuery("
        select ClassName, count(*) as TOTAL
        from $OBJECT_DATA a 
        where Fuel > 0 or Hitpoints not in ('[]') or ClassName = 'MMT_Civ'
        group by ClassName
    ");
}

function getStructureClassChartData() {
    global $OBJECT_DATA;
    echo runQuery("
        select ClassName, count(*) as TOTAL
        from $OBJECT_DATA a 
        where not (Fuel > 0 or Hitpoints not in ('[]') or ClassName = 'MMT_Civ')
        group by ClassName
    ");
}

function getSummary() {
    global $PLAYER_DATA, $OBJECT_DATA, $CHARACTER_DATA, $PLAYER_LOGIN;
    echo runQuery("
        select 'Total Logins'            as METRIC,   count(*)          as VALUE from $PLAYER_LOGIN
        union all  
        select 'Total Unique Players'    as METRIC,   count(*)          as VALUE from $PLAYER_DATA
        union all  
        select 'Characters Created'      as METRIC,   count(*)          as VALUE from $CHARACTER_DATA
        union all  
        select 'Total Objects'           as METRIC,   count(*)          as VALUE from $OBJECT_DATA
        union all  
        select 'Total Vehicles'          as METRIC,   count(*)          as VALUE from $OBJECT_DATA where Fuel > 0 or Hitpoints not in ('[]') or Classname = 'MMT_Civ'
        union all  
        select 'Total Structures'        as METRIC,   count(*)          as VALUE from $OBJECT_DATA where not (Fuel > 0 or Hitpoints not in ('[]') or Classname = 'MMT_Civ')
        union all  
        select 'Destroyed Vehicles'      as METRIC,   count(*)          as VALUE from $OBJECT_DATA where (Fuel > 0 or Hitpoints not in ('[]') or Classname = 'MMT_Civ') and Damage = 1
        union all  
        select 'Zombie Headshots'        as METRIC,   sum(HeadshotsZ)   as VALUE from $CHARACTER_DATA
        union all  
        select 'Zombies Killed'          as METRIC,   sum(KillsZ)       as VALUE from $CHARACTER_DATA
        union all  
        select 'Humans Killed'           as METRIC,   sum(KillsH)       as VALUE from $CHARACTER_DATA
        union all  
        select 'Bandits Killed'          as METRIC,   sum(KillsB)       as VALUE from $CHARACTER_DATA
        union all  
        select 'Dead Characters'         as METRIC,   count(*)          as VALUE from $CHARACTER_DATA where Alive = 0
        union all
        select 'Distance Traveled'       as METRIC,   sum(DistanceFoot) as VALUE from $CHARACTER_DATA
        union all
        select 'Average Lifespan (mins)' as METRIC,   avg(Duration)     as VALUE from $CHARACTER_DATA
        union all
        select 'Average Humanity'        as METRIC,   avg(Humanity)     as VALUE from $CHARACTER_DATA
        union all
        select 'Time Played (mins)'      as METRIC,   sum(Duration)     as VALUE from $CHARACTER_DATA
    ");
}

function runQuery($sql) {
    try {
        $dbh = getConnection();
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dbh = null;
        return json_encode($results);
    } catch (PDOException $e) {
        return json_encode(new PDOError($e->getMessage()));
    }
}

function getConnection() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT;
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    ); 
    $dbh = new PDO(sprintf('mysql:host=%s;dbname=%s;port=%s',$DB_HOST,$DB_NAME,$DB_PORT), $DB_USER, $DB_PASS, $options);
    return $dbh;
}

?>