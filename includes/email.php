<?php
/*
include('../includes/thirdparties/PHPMailer/src/PHPMailer.php');
include('../includes/thirdparties/PHPMailer/src/SMTP.php');
include('../includes/thirdparties/PHPMailer/src/Exception.php');
*/
ini_set('default_socket_timeout', 1500);
ini_set('max_execution_time', 1500);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function envia_email($email,$tit,$txt,$prm='') {

	$mail = new PHPMailer(true);

	try {
		
		//$mail->SMTPDebug = SMTP::DEBUG_SERVER;
		//$mail->SMTPDebug = 4;
		$mail->isSMTP();
		$mail->Host = 'email-smtp.sa-east-1.amazonaws.com';
		
		$mail->SMTPAuth = true;
		$mail->Username = 'AKIASFLZYTCM2VIF3GXI';
		$mail->Password = 'BHFpae1BbUhoV/IG7+idFkoFLG9gswzoaVjIB3YpFoWf';
		
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		
		$mail->setFrom('noreply@feedrenault.com.br');
		$mail->addAddress($email);
		
		$mail->isHTML(true);
		$mail->Subject = $tit;
		$mail->Body = $txt;
		
		$mail->CharSet = 'UTF-8';
		
		$arr_parm = explode("|", $prm);
		
		foreach ($arr_parm as $p) { 
			$pigual=strpos($p,'=');
			if ($pigual>0) {
				$p1=explode('=',$p);
				$pr=strtoupper($p1[0]);
				${$pr}=$p1[1];
			} else ${$p}=true;	
		}
		
		//nome do arquivo + _ref
		/*if(isset($IMG)){ 
			$ref = substr($IMG, 1+strrpos($IMG,"/"),-4)."_ref";
			$mail->AddEmbeddedImage($IMG, $ref);
		}*/
		
		if(isset($IMG)){ 
			$arr_img = explode(";", $IMG); //print_r($arr_img);
			foreach ($arr_img as $i) { 
				$ref = substr($i, 1+strrpos($i,"/"),-4)."_ref";
				$mail->AddEmbeddedImage($i, $ref);
			}
		}
		
		$mail->send();
		
		/*
		echo "teste - Ok";
		die();*/

	}catch(Exception $e){
		//txtLog('_envia_email_',"Erro ao enviar {$mail->ErrorInfo}"); 
		echo "Erro ao enviar mensagem: {$mail->ErrorInfo}";	
		Die();
		/*
		echo "teste - Erro ao enviar {$mail->ErrorInfo}";
		die();*/
	}
	
}
?>