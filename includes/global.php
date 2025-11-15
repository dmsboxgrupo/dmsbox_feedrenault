<?php

/**
 * GLOBAL SETTINGS
 *
 * @author: Jean Carlo Deconto ( 2019 )
 *
 * (C) SUNAG - www.sunag.com.br / contact@sunag.com.br
**/

set_time_limit(30);

error_reporting( E_ALL );

ini_set("log_errors", 1);
ini_set("error_log", "./error.log");

ini_set('default_charset', 'utf-8');

//date_default_timezone_set('America/Sao_Paulo'); // com horario de verão
date_default_timezone_set('America/Fortaleza'); // sem horario de verão

// Avoid escapeshellarg() issues with UTF-8 filenames
setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

setlocale(LC_NUMERIC, 'en_US');

header('X-Robots-Tag: noindex');
header('Content-Language: pt-br');

?>