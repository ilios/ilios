<?php
file_put_contents("./data_r.xml",$_POST["data"]);
header("Location:./data/dummy.html");
?>