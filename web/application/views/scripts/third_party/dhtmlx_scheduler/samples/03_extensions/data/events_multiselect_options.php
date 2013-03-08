<?php
	include ('../../../codebase/connector/scheduler_connector.php');
	include ('../../../codebase/connector/crosslink_connector.php');
	require_once ('../../common/config.php');

	$cross = new CrossOptionsConnector($res, $dbtype);
	$cross->dynamic_loading(true);
	$cross->options->render_table("user","user_id","user_id(value),username(label)");
	$cross->link->render_table("event_user","event_id", "user_id,event_id");
	
	$fruitCross = new CrossOptionsConnector($res, $dbtype);
	$fruitCross->dynamic_loading(true);
	$fruitCross->options->render_table("fruit","fruit_id","fruit_id(value),fruit_name(label)");
	$fruitCross->link->render_table("event_fruit","event_id","fruit_id,event_id");
	
	$scheduler = new SchedulerConnector($res, $dbtype);
	
	$scheduler->set_options("user_id", $cross->options);
	$scheduler->set_options("fruit_id", $fruitCross->options);
	
	$scheduler->render_table("events_ms","event_id","start_date,end_date,event_name,details");
?>