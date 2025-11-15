<?php

/**
*
*	API
*
* */

define('SUNAG', true);
define('ROOT', str_replace('\\', '/', dirname(__FILE__, 2)) . '/');

include('config.php');

include('global.php');
include('methods.php');
include('database.php');
include('custom/database.php');
include('token.php');
include('session.php');
include('uploader.php');

include('user.php');

include('thirdparties/PHPMailer/src/PHPMailer.php');
include('thirdparties/PHPMailer/src/SMTP.php');
include('thirdparties/PHPMailer/src/Exception.php');

include('email.php');

need_https();

$db = new CustomDataBase(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
$token = new Token();
$session = new Session('session', 3600 * 24 * 365);

// APP

$uploader = new Uploader();

?>