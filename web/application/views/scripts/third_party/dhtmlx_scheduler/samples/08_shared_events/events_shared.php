<?php
	include ('../../codebase/connector/scheduler_connector.php');
	include ('../common/config.php');
	
	$user_id = intval($_GET['user']);
	
	$scheduler = new schedulerConnector($res, $dbtype);
	
	$scheduler->access->deny("insert");
	$scheduler->access->deny("update");
	$scheduler->access->deny("delete");
	
	$scheduler->render_sql("select * from events_shared where event_type=1 AND userId = ".$user_id,"event_id","start_date,end_date,text,event_type,userId");
?>