<?php
if(empty($_POST['data'])) {
	exit;
}

$filename = "data.ical";

header("Cache-Control: ");
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="'.$filename.'"');

echo $_POST['data'];

?>