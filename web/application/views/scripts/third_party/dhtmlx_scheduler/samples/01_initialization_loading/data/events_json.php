<?php
	require_once('../../../codebase/connector/scheduler_connector.php');
	include ('../../common/config.php');

	$scheduler = new JSONSchedulerConnector($res, $dbtype);
	$scheduler->render_table("events","event_id","start_date,end_date,event_name,details");
?>