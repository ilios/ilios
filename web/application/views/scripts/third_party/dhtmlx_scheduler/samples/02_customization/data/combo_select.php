<?php
	require_once('../../../codebase/connector/combo_connector.php');
	require_once("../../common/config.php");

	$combo = new ComboConnector($res, $dbtype);

	$combo->event->attach("beforeFilter", "by_id");
	function by_id($filter) {
		if (isset($_GET['id']))
			$filter->add("item_id", $_GET['id'], '=');
	}	

	$combo->dynamic_loading(3);
	$combo->render_table("Countries","item_id","item_nm");

?>
