drop view if exists v_object_class;
create view v_object_class as 
select count(*) as COUNT,
       a.CLASSNAME,
       (case when (sum(a.FUEL) > 0 or sum(a.DAMAGE) > 0) then 'VEHICLE' 
             when (a.CLASSNAME like "%Locked%")          then 'LOCKABLE' 
             else                                             'DEPLOYABLE' 
        end) as CATEGORY 
from Object_DATA a 
group by a.CLASSNAME 
order by COUNT desc;

drop view if exists v_player_data;
create view v_player_data as 
select a.PLAYERUID,
       a.PLAYERNAME,
       max(b.DATESTAMP) as PLAYERLASTLOGIN,
       min(b.DATESTAMP) as PLAYERFIRSTLOGIN
from       Player_DATA  a
inner join Player_LOGIN b on a.PLAYERUID = b.PLAYERUID
group by a.PLAYERUID
order by PLAYERLASTLOGIN desc;

drop procedure if exists p_build_daily_stats;
delimiter //
create procedure p_build_daily_stats()
comment 'builds daily stat table that can be queried for data'
begin
    drop table if exists t_daily_stats;
    create table t_daily_stats as
    select "New Players"        as METRIC, count(*) as COUNT, date(PLAYERFIRSTLOGIN) as DATE from v_player_data group by date(PLAYERFIRSTLOGIN)
    union all
    select "Inactive Players"   as METRIC, count(*) as COUNT, INTERVAL 15 DAY + date(PLAYERLASTLOGIN) as DATE from v_player_data group by date(PLAYERLASTLOGIN)
    union all
    select "Characters Killed"  as METRIC, count(*) as COUNT, date(LASTLOGIN) as DATE from Character_DATA where ALIVE = 0 group by date(LASTLOGIN)
    union all                                                                 
    select "Characters Created" as METRIC, count(*) as COUNT, date(DATESTAMP) as DATE from Character_DATA group by date(DATESTAMP)
    union all                                                                 
    select "Structures Built"   as METRIC, count(*) as COUNT, date(DATESTAMP) as DATE from Object_DATA a inner join v_object_class b on a.CLASSNAME = b.CLASSNAME where b.CATEGORY in ("LOCKABLE","DEPLOYABLE") group by date(DATESTAMP)
    union all                                                                 
    select "Vehicles Spawned"   as METRIC, count(*) as COUNT, date(DATESTAMP) as DATE from Object_DATA a inner join v_object_class b on a.CLASSNAME = b.CLASSNAME where b.CATEGORY = "VEHICLE" group by date(DATESTAMP)
    ;
end//
delimiter ;

drop procedure if exists p_build_aggregate_stats;
delimiter //
create procedure p_build_aggregate_stats()
comment 'builds flat table of statistics'
begin
    drop table if exists t_aggregate_stats;
    create table t_aggregate_stats as
    select "Unique Players" as METRIC, count(*) as COUNT from v_player_data
    union all
    select "Active Players" as METRIC, count(*) as COUNT from v_player_data where date(PLAYERLASTLOGIN) >= (now() - INTERVAL 15 DAY)
    union all
    select "Player Logins" as METRIC, count(*) as COUNT from Player_LOGIN where ACTION = 0
    union all
    select "Characters Created" as METRIC, count(*) as COUNT from Character_DATA
    union all
    select "Dead Characters" as METRIC, count(*) as COUNT from Character_DATA where ALIVE = 0
    union all
    select "Live Characters" as METRIC, count(*) as COUNT from Character_DATA where ALIVE = 1
    union all
    select "Zombie Kills" as METRIC, sum(KILLSZ) as COUNT from Character_DATA
    union all
    select "Zombie Headshots" as METRIC, sum(HEADSHOTSZ) as COUNT from Character_DATA
    union all
    select "Survivors PKed" as METRIC, sum(KILLSH) as COUNT from Character_DATA
    union all
    select "Distance Foot" as METRIC, sum(DISTANCEFOOT) as COUNT from Character_DATA
    union all
    select "Bandits PKed" as METRIC, sum(KILLSB) as COUNT from Character_DATA
    union all
    select "Avg Humanity" as METRIC, round(avg(HUMANITY)) as COUNT from Character_DATA where ALIVE = 1
    union all
    select "Avg Generation" as METRIC, round(avg(GENERATION)) as COUNT from Character_DATA where ALIVE = 1
    union all
    select "Avg Mins Lived" as METRIC, round(avg(DURATION)) as COUNT from Character_DATA where DURATION > 0
    union all
    select "Heroes" as METRIC, count(*) as COUNT from Character_DATA where ALIVE = 1 and HUMANITY >= 5000
    union all
    select "Bandits" as METRIC, count(*) as COUNT from Character_DATA where ALIVE = 1 and HUMANITY <= -5000
    union all
    select "Structures" as METRIC, count(*) as COUNT from Object_DATA a inner join v_object_class b on a.CLASSNAME = b.CLASSNAME where b.CATEGORY in ("LOCKABLE","DEPLOYABLE")
    union all                                                                 
    select "Vehicles"   as METRIC, count(*) as COUNT from Object_DATA a inner join v_object_class b on a.CLASSNAME = b.CLASSNAME where b.CATEGORY = "VEHICLE"
    ;
end//
delimiter ;

drop event if exists e_build_stats;
delimiter //
CREATE EVENT e_build_stats
	ON SCHEDULE EVERY 5 MINUTE STARTS CURRENT_TIMESTAMP
	ON COMPLETION PRESERVE
	COMMENT 'builds stat tables every 5 minutes'
	DO BEGIN
    call p_build_daily_stats();
    call p_build_aggregate_stats();
END//
delimiter ;