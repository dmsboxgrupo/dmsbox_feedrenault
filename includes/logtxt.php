<?php
if(!function_exists("txtLog")){
	function txtLog($tipo,$txt) {	
		Global $arqLog,$scr;
		$anomes=date("Ym",time());
		$d=date("Ymd",time());
		$h=date("His",time());
		//$dir=getcwd();
		//$dir=$_SERVER['DOCUMENT_ROOT'];
		if(!isset($_SERVER['APPL_PHYSICAL_PATH'])) $dir = $_SERVER['DOCUMENT_ROOT'];
		else $dir = $_SERVER['APPL_PHYSICAL_PATH'];
		
		//$dir.="/renault/admin/log";	
		$dir.="/admin/log";	
		$ld=date("Ymd",time());
		if(!is_dir($dir)) mkdir($dir, 0777);
		$dir.="/$anomes"; 
		
		//echo "dir= $dir"; die();
		if(!is_dir($dir)) if(!mkdir($dir, 0777)) $dir="C:";		
		
		//$user=$_SESSION['a_username'];		
		$user="UsuarioLog";	
		$arqLog=$dir."/log$tipo$ld.txt";
		//echo "teste $arqLog";die();
		$script=$_SERVER['PHP_SELF'];
		$script=str_replace('dados/','',$script);
		$script=str_replace('/','',$script);
		// Includes
		$included_files = get_included_files();
		$inc='';
		foreach ($included_files as $filename) {
			$filename=str_replace('/home/www/renault/dados/','',$filename);
			$filename=str_replace('/home/www/renault/','',$filename);
			$filename=str_replace('.php','',$filename);
		    //if (strpos(" $filename",'wce')) $inc=$filename;
		}
		$fp=fopen($arqLog, "a+");
		$logTxt="$d $h $user $script $inc $txt\n";
		fwrite($fp, $logTxt);
		fclose($fp);
		
		
	}
}
?>