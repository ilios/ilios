<?php
if(empty($_POST['data'])) {
	echo "why";
	exit;
}

$filename = "data.xml";

header("Cache-Control: ");
header("Content-type: text/plain");
header('Content-Disposition: attachment; filename="'.$filename.'"');

echo $_POST['data'];

?>