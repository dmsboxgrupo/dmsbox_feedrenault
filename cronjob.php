<?php

// API
	
date_default_timezone_set('America/Sao_Paulo');
	
$date = new DateTime();

$result = $date->format('Y-m-d H:i:s');

file_get_contents('https://feedrenault.com.br/task.php?q=all');

file_put_contents('temp/cronjob.txt', $result);

?>
