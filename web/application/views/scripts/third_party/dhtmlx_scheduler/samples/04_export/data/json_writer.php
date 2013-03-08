<?php
file_put_contents("./data.json",$_POST["data"]);
header("Location:./dummy.html");
?>