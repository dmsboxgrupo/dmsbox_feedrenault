<?php

/**
*
*	API
*
* */

define('SUNAG', true);
define('ROOT', str_replace('\\', '/', dirname(__FILE__, 2)) . '/');

include('includes/config.php');

include('includes/global.php');
include('includes/methods.php');
include('includes/database.php');
include('includes/custom/database.php');
include('includes/token.php');
include('includes/session.php');
//require_once "includes/LogTxt.php";
//include('includes/logtxt.php');
//include('../includes/email.php');



include('includes/thirdparties/PHPMailer/src/PHPMailer.php');
include('includes/thirdparties/PHPMailer/src/SMTP.php');
include('includes/thirdparties/PHPMailer/src/Exception.php');

include('includes/email.php');

need_https();

$db = new CustomDataBase(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
$token = new Token();
$session = new Session('session', 3600 * 24 * 365);

// APP

include('admin/includes/useradmin.php');
include('webadmin.php');

//$useradm = new UserAdmin();
//$useradm->level("false");

//$teste = $useradm->is_level(LEVEL_MASTER);
//echo "opa= $teste";
//die();
$webadm = new WebAdmin();
//$webadm->logged = $useradm->logged();
//$webadm->footer = $useradm->logged();
/*
function need_login() {
	
	global $useradm;
	
	if (!$useradm->logged()) {
		
		redirect('signin.php');

	}
	
}*/


?>