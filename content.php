<?php

// API

include('includes/common.php');

// Inicia API Externa (JSON)

$q = get_request('q');
$id = (int)get_request('id');
$card = get_request('card');
$filename = get_request('filename');
// Obtem conteudo

$width = 0;
$height = 0;

if ( $q == 'banner' ) {

	$width = 500;
	$height = 250;

} elseif ( $q == 'highlight-thumbnail' ) {

	$width = 200;
	$height = 400;

} elseif ( $q == 'vehicle' ) {
	
	$width = 480;
	$height = 320;

} elseif ( $q == 'intro' ) {

	$intro_banner_meta = $db->get_metadata( "intro_banner" );
	$id = (int)$intro_banner_meta['value'];
	
	$width = 1000;
	$height = 1500;

} elseif ( $q == 'painel-thumbnail' ) {
	
	$width = 80;
	$height = 80;

}

if ( $id > 0 ) {
	
	$upload_file = $uploader->get( $id );
	
	$upload_url = $upload_file['url'];
	$upload_ext = $upload_file['extension'];
	$upload_type = $upload_file['type'];
	$upload_name = $upload_file['name'];
	
	$content_url = $upload_url;
	
	if ( substr( $upload_type, 0, 5 ) == 'video' ) {
		
		include('includes/ffmpeg.php');
		
		$ffmpeg = new ffmpeg();
		$info = $ffmpeg->info( $upload_url );
		
		$video_cache_url = "uploads_cache/{$id}.jpg";
		$video_catch_path = dirname(__FILE__) . "/{$video_cache_url}";

		if ( !file_exists( $video_cache_url ) ) {

			$ffmpeg->snapshot( $upload_url, $video_catch_path, 0, $info['width'], $info['height'] );

			//$ffmpeg->snapshot( $upload_url, $video_catch_path, number_format( $info['duration'] / 2, 3 ), $info['width'], $info['height'] );
			
		}

		if( empty( $filename) ) $content_url = $video_cache_url;

		if ( $width > 0 && $height > 0 ) {

			$cache_url = "uploads_cache/{$id}_{$width}x{$height}.jpg";
			
			if ( !file_exists( $cache_url ) ) {
				
				include_once('includes/imagelib.php');
				
				$imageLib = new ImageLib();
				$imageLib->resize( $video_cache_url, $cache_url, $width, $height );

			}

			if( empty( $filename) ) $content_url = $cache_url;
			
		}
		
	} elseif ( $width > 0 && $height > 0 ) {
		
		$cache_url = "uploads_cache/{$id}_{$width}x{$height}.{$upload_ext}";
		
		if ( !file_exists( $cache_url ) ) {
			
			include_once('includes/imagelib.php');
			
			$imageLib = new ImageLib();
			$imageLib->resize( $upload_url, $cache_url, $width, $height );

		}
		
		$content_url = $cache_url;
		
	}
	
	if( !empty( $filename) ) {
	//print_r($content_url); print_r($filename); die();  uploads/6053.pdf  6053.pdf
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/octet-stream"); 
	
		/*if( $upload_ext == 'pdf' )header("Content-Type: application/pdf"); 
		else if( $upload_ext == 'png' )header("Content-Type: image/png");
		else if( $upload_ext == 'mp4' )header("Content-Type: video/mp4"); 
		else if( $upload_ext == 'jpg' )header('Content-Type: image/jpeg'); 
		*/
		header("Content-Disposition: attachment; filename=\"". basename($filename) ."\"");
		
		ob_clean();
		
		readfile("$content_url");
	
	} else {

		header( 'Location: ' . HOST . "/{$content_url}" );
		
	}
	
}else{
	
	if( $card != '') {
		
		//$content_url = "uploads/6053.pdf";
		//$filename = "6053.pdf";
		
		//$card =  "downloads/pdf/" . $card . "/Cartao Digital.pdf";
		$card = 'download/pdf/'. $card .'/Cartao Digital.pdf';
		//$card =  "uploads/5662.jpg";
		//print_r($card); die();
		
		//download/pdf/806c6d56c3ca63b84d835d54547c7146/Cartao Digital.pdf"
//echo "$card"	; die();	
		header("Content-Description: File Transfer"); 
		header("Content-Type: application/octet-stream"); 
	
		header("Content-Disposition: attachment; filename=Cartao_Digital.pdf");
		//header("Content-Disposition: attachment; filename=\"". basename($filename) ."\"");
		
		ob_clean();
		//feedrenault_webapp\download\pdf
		//readfile("../feedrenault_webapp/$card");
		
		//$card .=  "/Cartao Digital.pdf";
		//uploads/5662.jpg
		readfile("$card");
		//readfile("$content_url");
		
		
		
	} 

}

?>