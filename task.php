<?php

// API
	
include('includes/common.php');
include('includes/socials.php');

$socials = new Socials(
	// Twitter Token
	"AAAAAAAAAAAAAAAAAAAAAHbIMQEAAAAAvxAMxv6k5311%2FwE0i9US1sEOVu0%3DXh84GOHWaB9gXTxRRfG1GLqti4q2yxPtQWotLiNi2TvcnkUolN",
	// YouTube Token
	"AIzaSyDwrV0b5rqeVKNAy5titNuhRkqMCG4Udbk",
	// Facebook Token
	//"EAA5VWSqmETwBOZC5v5pUhfv2rHWl2h2UtyixRcml03QuVlNGY7FCGtmbWJ77Bb4i31yawOnIYNbnM0phqOuuZBlt9PNc3oY8r4tv6NZCcCrr9tMJ2ZA6cHWxDtV6FzCwX9hQJK3OID4ZA0vKp2nKZAqZA902TH6aK9ywpie9hreZCLsjd5X4UBZC5RrbZB",
	"EAA5VWSqmETwBPFIaVccZBxHGuxiDGbwyWrhRePD4Lm7etmhTSDk0b4OatNf3VBkMhZA5qzmagZCRIudraIT2hDCsOiv8xIZBc36TeVtuVqVKEeMdc6O8hXz7ZCTnGCBA9QIToFRDXKLBSVZCZCWZBUZCRcYbTvIHMktr1ZAAwOgZB8UsgIvbqKBHxREYGKKlyVa",

	// duração do cache em segundos. 14400 = 6 horas    
	1,
	// máximo de elementos retornados. Default = 50    
	10
);

set_time_limit ( 60 * 5 );

header("Access-Control-Allow-Origin: *");

content_type('text/plain');

function posts_social( $posts, $social_type ) {

	global $db, $uploader;
	
	foreach($posts as &$post) {
		
		$post_id = $post['uid'];

		$app_post = current( $db->query( "SELECT * FROM posts WHERE social_id ='{$post_id}' AND social_type = {$social_type}", array(
			'social_id' => $post['uid'],
			'social_type' => $social_type
		) ) );
		
		if (!$app_post) {
			
			$text = $post['message'];
			$link = $post['link'];

			$content_id = 0;
			
			if ($social_type == 1 || $social_type == 3 || $social_type == 4) {
			
				if ($post['video']) {
					
					$content_id = $uploader->upload_content( $post['video'] );
					
				} elseif (count($post['images']) > 0) {
					
					$content_id = $uploader->upload_content( $post['images'][0] );
					
				}
				
			} elseif ($social_type == 2) {
				
				/*if (count($post['images']) > 0) {
					
					$content_id = $uploader->upload_content( $post['images'][0] );
					
				}*/
				
				$link = $post['video'];
				
			}
			
			$post_data = array(
				'category' => 1,
				'text' => $text,
				'link' => $link,
				'social_id' => $post_id,
				'social_type' => $social_type,
				'image' => $content_id,
				'date' => $post['time']
			);
			
			$db->insert('posts', $post_data);

		}
		
	}
	
}

// Inicia API Externa (JSON)

$q = get_request('q');

$json = array();

switch($q) {

	case 'twitter':
		
		//posts_social( $socials->getTwitterFeed("RenaultBrasil" ), 1 );
		
		break;
	
	case 'youtube':

		posts_social( $socials->getYoutubeFeed("RenaultBrasil" ), 2 );
		
		break;
	
	case 'instagram':

		posts_social( $socials->getInstagramFeed("17841400101283010" ), 3 );
		
		break;
	
	case 'facebook':

		//posts_social( $socials->getFacebookFeed("107996345919864" ), 4 );
		
		break;
	
	case 'all':
	
		//posts_social( $socials->getTwitterFeed("RenaultBrasil" ), 1 );
		posts_social( $socials->getYoutubeFeed("RenaultBrasil" ), 2 );
		posts_social( $socials->getInstagramFeed("17841400101283010" ), 3 );
		//posts_social( $socials->getFacebookFeed("107996345919864" ), 4 );
		
		//inactivate_user();
		
		break;
	
}

// print

echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
