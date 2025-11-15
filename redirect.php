<?php

// API

include('includes/common.php');

content_type('text/plan');

$q = get('q');
$text = has('text');

if ($q == 'android') {
	
	$redirect = "https://play.google.com/store/apps/details?id=com.dmsbox.feedrenault";
	
} elseif ($q == 'ios') {
	
	$json = json_decode( file_get_contents('ulink_apple/file.json'), true );
	$links = $json['Sheet1'];
	
	if ( empty( $db->get_metadata('ios_link_offset') ) ) {
		
		$db->set_metadata('ios_link_offset', 1);
		
	}
	
	$start = 7;
	$offset = (int)$db->get_metadata_value('ios_link_offset');
	$index = $start + $offset;
	
	$link = $links[ $index ]['Link'];
	
	$db->set_metadata('ios_link_offset', $offset + 1);
	
	$redirect = $link;
	
} else {
	
	$redirect = "https://www.feedrenault.com.br/";

}

if ($text) echo $redirect;
else header("Location: {$redirect}");

?>
