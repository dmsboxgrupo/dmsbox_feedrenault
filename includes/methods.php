<?php

/**
*
*	METHODS
*
* */

function has_upload($n) {
	
	return isset($_FILES[$n]) && $_FILES[$n]['size'] > 0;
	
}

function get_upload($n) {
	
	if (isset($_FILES[$n])) {
		
		return $_FILES[$n];
		
	}
	
}

function save_upload($n, $path) {
	
	$file = get_upload($n);
	$file_path = $file['tmp_name'];
	
	return move_uploaded_file($file_path, $path);
	
}

function has_post($n) {
	
	return isset($_POST[$n]);
	
}

function get_post($n) {
	
	if (isset($_POST[$n]) && $_POST[$n] != null) {
		
		return is_array($_POST[$n]) ? $_POST[$n] : stripslashes(trim((string)$_POST[$n]));
		
	}
	
	return "";
}

function has($n) {
	
	return isset($_GET[$n]);
	
}

function has_file ($n){

	return isset($_FILES[$n]);
	
}

function get($n, $v="") {
	
	if (isset($_GET[$n]) && $_GET[$n] != null) {
		
		return stripslashes(trim((string)$_GET[$n]));
		
	}
	
	return $v;
}

function has_request($n) {
	
	return isset($_REQUEST[$n]);
	
}

function get_request($n, $v="") {
	
	if (isset($_REQUEST[$n]) && $_REQUEST[$n] != null) {
		
		return stripslashes(trim((string)$_REQUEST[$n]));
		
	}
	
	return $v;
}

function html($str) {
	
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
	
}

function htmltotext( $html, $length=null ) {
	
	$text = strip_tags( $html );
	
	if ($length && mb_strlen($text) > $length) {
		
		$text = mb_substr ($text, 0, $length) . '...';
		
	}
	
	return $text;
	
}

function url($url='') {

	return strpos($url, '//') === 0 || strpos($url, 'http') === 0 ? $url : HOST . "/$url";

}

function selected($sel) {
	
	return $sel ? 'selected' : '';
	
}

function disabled($sel) {
	
	return $sel ? 'disabled' : '';
	
}

function redirect($url, $params=null) {

	header("Location: {$url}");
	
	exit;
	
}

function need_https() {
	
	if ( strpos(HOST, 'https') !== 0 ) return;
	
	if (! isset($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off' ) {
		
		$redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		
		header("Location: $redirect_url");
		
		exit;
	}
	
}

function get_ids( $list ) {
	
	if (is_string($list)) {
		
		return array_filter( array_map( 'intval', explode(',', $list) ) );
		
	} elseif (is_array($list)) {
	
		$ids = array();
		
		foreach( $list as &$item ) {
			
			array_push( $ids, $item['id'] );
			
		}
		
		return $ids;
		
	} else {
	
		return array();
		
	}
	
}

function refresh($seconds, $url='') {
	
	$header = "refresh:{$seconds}";
	
	if ( $url ) $header .= ";url={$url}";
	
	header( $header );
	
}

function content_type($type) {
	
	header("Content-Type: $type; charset=utf-8");
	
}

function inactivate_user() {
	//inativa usuarios sem acesso nos ultimos 60 dias
	
	global $db;

	$inactivate_user =  $db->query( "update `users` set status=0
					WHERE level=3 and DATEDIFF(CURDATE(), last_login) >=60" ) ;
	
}

function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

?>