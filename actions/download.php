<?php
header("Content-type: \"". $_GET['mime'] ."\""); 
header("Content-Description: File Transfer"); 
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"". $_GET['name'] ."\"");

ob_clean();

readfile($_GET['url']);
die();
?>