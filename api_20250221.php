<?php

// API

header( 'Access-Control-Allow-Headers: *' );

include('includes/common.php');
//include('includes/user.php');
//include('includes/email.php');       

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

// Inicia API Externa (JSON)

function get_content_format( $url ) {
	
	$extension = strtolower( pathinfo($url, PATHINFO_EXTENSION) );
	
	if ( in_array($extension, array('mp4')) ) {
	
		return 'video';
		
	} elseif ( in_array($extension, array('jpg', 'png', 'jpeg', 'gif')) ) {
		
		return 'image';
		
	} else if ( in_array($extension, array('pdf')) ) {
	
		return 'pdf';
		
	}

	return '';
	
}

function get_category_id( $name ) {
	
	if ( $name == 'redes' ) return 1;
	if ( $name == 'comunicados' ) return 2;
	if ( $name == 'noticias' ) return 3;
	if ( $name == 'galeria' ) return 5;
	if ( $name == 'campanha' ) return 6;
	if ( $name == 'enquete' ) return 7;
	if ( $name == 'universo' ) return 12;
	if ( $name == 'careservices' ) return 13;
	if ( $name == 'academy' ) return 14;
	
	return 0;
	
}

function get_content_url( $query, $id = 0, $type = 'image' ) {
	
	return url( "content.php?q={$query}&id={$id}&type={$type}" );
	
}

function contains_tags( &$filters, &$tags ) {
	
	$contains = false;
	
	foreach($filters as &$filter) {

		if( in_array($filter, $tags) ) {
			
			$contains = true;
			
		}
		
	}
	
	return $contains;

}

function register_last_login( $user_id ){
	
	global $db;
	
	$last_login = date('Y-m-d H:i:s');
	
	$db->update('users', $user_id, array(
								'last_login' => $last_login
							));

}

function parse_link( $link, &$target ) {
	
	preg_match('/whatsapp_gallery_edit\.php\?id=(\d+)/', $link, $matches_gallery);
	preg_match('/campaign_edit\.php\?id=(\d+)/', $link, $matches_campaign);
	
	if ($matches_gallery) {
		
		$target['link_gallery'] = (int)$matches_gallery[1];
		$target['link'] = '#id=' . $target['link_gallery'];
		
	}
	
	if ($matches_campaign) {
		
		$target['link_campaign'] = (int)$matches_campaign[1];
		$target['link'] = '#id=' . $target['link_campaign'];
		
	}
	
}

function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
      return mb_convert_encoding($content, 'UTF-8',
          mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
}

function get_api_arr( $query, $id ) {

	//$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/renault/api.php';
	//$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/newfeed/api.php';
	$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/api.php';
	
	$arr = array();
	
	if($query == 'surveys'){
		$query = 'feed&filter=enquete&version=1';
		//$id = '7';
	}
	
	$arr = file_get_contents($root . "/?q={$query}&id={$id}");

	$arr = json_decode($arr, 1);
	
	return $arr;

}

set_time_limit ( 60 * 5 );

content_type('text/plain');

$q = get_request('q');
$secret_key = get_request('secret_key');

$error = '';
$user = null;

$json = array();

if ($secret_key) {
	
	$user = current( $db->query( "SELECT * FROM users WHERE secret_key = :secret_key", array(
		'secret_key' => $secret_key
	) ) );
	
	$json['logged'] = (bool)$user;
	
	if (!$user) {
		
		$error = 'Login expirado.';
		
	}
	
}

// Obtem dados

switch($q) {

	case 'set_meta':
		
		if ($user) {
			
			$name = get_request('name');
			
			if ($name) {
				
				$metadata = json_decode( $user['metadata'], true );
				
				if (has_request('value')) {
					
					$type = get_request('type');
					$value = get_request('value');
					
					if ($type == 'number') $value = (double)$value;
					elseif ($type == 'boolean') $value = $value == 'true' ? true : (bool)$value;
					
					$metadata[ $name ] = $value;
					
				} else {
					
					unset( $metadata[ $name ] );
					
				}
				
				$db->update('users', $user['id'], array(
					'metadata' => json_encode( $metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE )
				));
				
				$json['metadata'] = $metadata;
				
			}

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		break;

	case 'user_data':
		
		$uuid = get_request('token');

		$token_data = $token->get( $uuid );
		
		if ( $token_data && $token->is_valid( $token_data ) ) {	
	
			$content_id = $token_data["uid"];
	
			$user_data = new User($content_id);
			
			$content = (array)$user_data;
			
			$json['name'] = $content['name'];
			$json['email'] = $content['email'];
			$json['bir'] = $content['bir'];

		} else {
			
			$error = "Token expirado.";
			
		}
	
		break;

	case 'vehicles':
		
		$vehicles_id = get_ids( $db->get_metadata_value( "vehicles" ) );
		
		$json['vehicles'] = [];
		
		foreach($vehicles_id as $vehicle_id) {
			
			$vehicle = $db->content( $vehicle_id, 'vehicles' );
			
			if ($vehicle['status']) {
			
				$vehicle_item = array();
				$vehicle_item['id'] = (int)$vehicle['id'];
				$vehicle_item['name'] = $vehicle['name'];
				$vehicle_item['version'] = $vehicle['version'];
				$vehicle_item['content'] = get_content_url( 'vehicle', $vehicle['image'] );
				
				$json['vehicles'][] = $vehicle_item;
				
			}
			
		}
		
		break;
		
	case 'material_library_tags':
		
		$groups_id = get_ids( $db->get_metadata_value( "tag_groups" ) );
	
		$json['material_library_tags'] = [];
		
		foreach($groups_id as $group_id) {
			
			$group = $db->content( $group_id, 'tag_groups' );
	//print_r($group );die();			
			if ($group['status']) {
			
				$group_item = array();
				$group_item['id'] = (int)$group['id'];
				$group_item['name'] = $group['name'];
				
				$subgroups = array_filter( explode(',', $group['tag_subgroups']) );
				
				foreach($subgroups as &$subgroup) {
			
					$tag_subgroup = $db->content( $subgroup, 'tag_subgroups' );
					
					$group_subitem = array();
					$group_subitem['id'] = (int)$tag_subgroup['id'];
					$group_subitem['name'] = $tag_subgroup['name'];
				
					$group_item['subgroups'][] = $group_subitem;
				
				}
				//$group_item['version'] = $group['version'];
				//$group_item['thumbnail'] = (int)$group['thumbnail'];
				$group_item['thumbnail'] = ($group['thumbnail'] > 0 ) ? get_content_url( 'group', $group['thumbnail'] ) : "";
				
				$json['material_library_tags'][] = $group_item;
				
			}
			
		}
		
		break;

	case 'banners':
		
		$category = get_category_id( get_request('filter') );
		
		$where = '';
		
		if ($category > 0) {
			
			$where .= " AND category = {$category} ";
			
		}
		
		//$banners = $db->query( "SELECT * FROM banners WHERE status = 1 {$where} ORDER BY date DESC" );
		//whatsapp_galeries_banners
		$banners = $db->query( 
		"SELECT 
			banners.id as id, 
			banners.name as name, 
			banners.text as text, 
			banners.link as link, 
			banners.image as image, 
			FIND_IN_SET(banners.id, metadata.value) as ordem 
			FROM `banners` 
			join metadata on property='whatsapp_galeries_banners' and FIND_IN_SET(banners.id, metadata.value) 
			 WHERE status = 1 {$where}
			order by ordem" );
		
		
		$json['banners'] = [];
		
		foreach($banners as &$banner) {
			
			$banner = $db->content( $banner, 'banners' );
			
			$banner_item = array();
			$banner_item['id'] = $banner['id'];
			$banner_item['name'] = $banner['name'];
			$banner_item['text'] = $banner['text'];
			$banner_item['link'] = $banner['link'];
			$banner_item['content'] = url( get_content_url( 'banner', $banner['image'] ) );
			
			parse_link( $banner_item['link'], $banner_item );
			
			$json['banners'][] = $banner_item;
			
		}
		
		break;
		
	case 'get_template_types':
	
		$template_types = [];
		
		$json_str = $db->get_metadata_value( "template_types" );

		$template_type_ids = current(json_decode($json_str, true));

		foreach($template_type_ids as &$template_type_id) {
			
			
			$template_type = array();
				
			$image = $uploader->get( $template_type_id['thumbnail'] );
			
			$template_type['id'] = $template_type_id['id'];
			$template_type['name'] = $template_type_id['name'];
			$template_type['template_type_url'] = url($image ? $image['url'] : '');
			
			
			array_push ($template_types,  $template_type);
			
		}
		
		$json['template_types'] = $template_types;
		
	break;
	
	case 'update_template_selection':
	
		$template_id = get_request('template_id');
		
		if ($user) {
		
			if( $template_id > 0 ){
				
				$template = current($db->query( "SELECT * FROM templates WHERE status = 1 and id={$template_id}" ));		
				
				if($template){
				
					$template_selection = current($db->query( "SELECT * FROM template_selections WHERE template={$template_id} and user={$user['id']}" ));
					
					if(!$template_selection){
						
						$ranking_id = $db->insert('template_selections', array(
							'user' => $user['id'],
							'template' => $template_id,
							'amount' => 1
						));
						
						
					}else{
						
						$template_selection['amount']++;
					
						$db->update('template_selections', $template_selection['id'], array(
											'amount' => $template_selection['amount']
										));
										
					}
					
					
					//print_r($template['selections'] ); die();
					
					
									
					$retorno = get_api_arr("templates&secret_key=$secret_key", $template_id);
					
					$json = $retorno;
					
				}else{
					
					$error = "Template não cadastrado.";
					
				}
				
			}else{
				
				$error = "Template inválido.";
				
			}
		
		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
	
	break;

	case 'templates':
	
		$version = get_request('version');
	
		if( $version > 1 ){
	
			$json['templates'] = array();
		
			$id = get_request('id');
			
			//tipo template
			$template_limit = (int)get_request('limit');
			
			//format template
			$template_format = (int)get_request('format');
		
			//tipo template
			$template_type = get_request('template_type');
			
			//tipo template
			$channel = get_request('channel');
			
			//veiculo
			$vehicle = get_request('vehicle');
			
			//veiculos
			$filter_vehicles = get_ids( get_request('vehicles') );
			
			//ordem
			$order = get_request('order');
			$order_by = ""; 
			
			if( $order == 0 ){
				
				$order_by = " templates.date desc, "; 
				
			}else {
				
				if( $order == 1 ){
					
					$order_by = " templates.date, "; 
					
				}else {
					
					$order_by = " amount desc, "; 
					
				}

			}
			
			// 0 - mais novo para o mais antigo
			// 1 - mais antigo para o mais novo 
			// 2 - maior relevancia
			
			
			
			$template_where = "";
			$tags_where = "";
			
			if( $template_type != "" ) $template_where = " and template_type=$template_type ";
			
			if( $channel != "" ) $template_where = " and channel=$channel ";
			
			if( $template_format != "" ) $template_where = " and format=$template_format ";
			
			if( $vehicle != "" ) $template_where .= " and FIND_IN_SET( $vehicle, vehicles )>0";
			
			if( $id != "" ) $template_where .= " and templates.id = $id ";
			
			//if( $group != "" ) $tags_where = " and tags.group='$group'";
			
			$group = mb_strtolower( get_request('group') );
			
			$group_arr = array();
			
			if($group){
							
				$group_arr = array_column($db->query( "SELECT * 
											FROM tags 
											WHERE status=1 and `group` = '$group'" ), 'id');

				//if($group_arr) $filter_tags = $group_arr;
				//else $filter_tags[] = "vazio";
				
			}
			
			$tag = mb_strtolower( get_request('tag') );
			
			$tag_arr = array();
			
			if($tag){
							
				/*$tag_arr = array_column($db->query( "SELECT * 
											FROM tags 
											WHERE status=1 and `id` = '$tag'" ), 'id');*/
				 
				 $tag_arr = $db->content( $tag, 'tags' );
				 
				//if($group_arr) $filter_tags = $group_arr;
				//else $filter_tags[] = "vazio";
				
			}
			
			
			$tags = array();
			$template_tags = array();	
							
			$templates_db = $db->query("SELECT 
										templates.id as id,
										templates.template_type as template_type,
										templates.channel as channel,
										templates.format as format,
										templates.show_logo as show_logo,
										templates.vehicles as vehicles,
										templates.name as name,
		(select count(*) from template_selections where template=templates.id) as amount,								
										
										templates.selections as selections,
										templates.status as status,
										templates.image as image,
										templates.thumbnail as thumbnail,
										templates.tags as tags,
										templates.field1 as field1,
										templates.field1_color as field1_color,
										templates.field1_style as field1_style,
										
										templates.field2 as field2,
										templates.field2_color as field2_color,
										templates.field2_style as field2_style,
										
										templates.field3 as field3,
										templates.field3_color as field3_color,
										templates.field3_style as field3_style,
										
										templates.field4 as field4,
										templates.field5 as field5,
										templates.field4_color as field4_color,
										templates.field4_style as field4_style,
			templates.type as type,
										FIND_IN_SET(templates.id, metadata.value)  as ordem,
										CASE
											WHEN `type` = 2 THEN 'template_styles'
											ELSE  'templates'
										END as type_name
									FROM `templates`
								join metadata on (property='templates') and FIND_IN_SET(templates.id, metadata.value) 
									WHERE status=1 $template_where 
									order by $order_by ordem");
								
								/*
								echo "SELECT 
										templates.id as id,
										templates.template_type as template_type,
										templates.show_logo as show_logo,
										templates.vehicles as vehicles,
										templates.name as name,
		(select count(*) from template_selections where template=templates.id) as amount,								
										
										templates.selections as selections,
										templates.status as status,
										templates.image as image,
										templates.thumbnail as thumbnail,
										templates.tags as tags,
										templates.field1 as field1,
										templates.field1_color as field1_color,
										templates.field1_style as field1_style,
										
										templates.field2 as field2,
										templates.field2_color as field2_color,
										templates.field2_style as field2_style,
										
										templates.field3 as field3,
										templates.field3_color as field3_color,
										templates.field3_style as field3_style,
										
										templates.field4 as field4,
										templates.field4_color as field4_color,
										templates.field4_style as field4_style,
			templates.type as type,
										FIND_IN_SET(templates.id, metadata.value)  as ordem,
										CASE
											WHEN `type` = 2 THEN 'template_styles'
											ELSE  'templates'
										END as type_name
									FROM `templates`
								join metadata on (property='templates') and FIND_IN_SET(templates.id, metadata.value) 
									WHERE status=1 $template_where 
									order by $order_by ordem"; die();*/
			/*$db_template_tags = $db->query("SELECT tags.id as tag_id, tags.name, property as template_type, FIND_IN_SET(tags.id, metadata.value)  as ordem
												FROM `tags`
												join metadata on property='highlighted_templates_tags' and FIND_IN_SET(tags.id, metadata.value) 
												join templates on FIND_IN_SET(tags.id, templates.tags)  
												WHERE tags.status=1  
												$tags_where 
												group by tags.id, tags.name
												order by ordem");

			foreach($db_template_tags as $db_template_tag) {

				$array_item = array();
				
				$array_item['id'] = (int)$db_template_tag['tag_id'];
				$array_item['name'] = $db_template_tag['name'];
				
				array_push ($template_tags,  $array_item);			
				
			}
			
			$template_tags_idxs = array_column($template_tags, 'template_type');*/
			//print_r($templates); die();
			
			$index_template = 0;
			
			foreach ($templates_db as $index_temp => $template){
				
				//limitando quantidade de itens retornados
				if( $template_limit> 0 and count($json[$template['type_name']]) >= $template_limit) break;
				
				$item = array();
				
				if($template['status']){
				
					${$template['type_name']}['id'] = (int)$template['id'];
					${$template['type_name']}['name'] = $template['name'];
					${$template['type_name']}['format'] = $template['format'];
					${$template['type_name']}['template_type'] = (int)$template['template_type'];
					${$template['type_name']}['channel'] = (int)$template['channel'];
					${$template['type_name']}['show_logo'] = (int)$template['show_logo'];
					//${$template['type_name']}['vehicles'] = $template['vehicles'];
					${$template['type_name']}['vehicles'] = get_ids( $template['vehicles']);
					//${$template['type_name']}['selections'] = $template['selections'];
					
					if($user){
						
						$template_amount = (int)current(array_filter(array_column($db->query( "SELECT count(*) cont FROM template_selections WHERE template= :template ", array(
							'template' => (int)$template['id']
						) ), 'cont')));
						
						${$template['type_name']}['amount'] = $template_amount;
					
					}
					
					$template_image = $db->content( $template, 'templates' );
					
					if($template['image']){
							
							${$template['type_name']}['content'] = get_content_url( 'image', $template_image['image'] );
							${$template['type_name']}['content_type'] = get_content_format( $template_image['image_url'] );
						
					}
					
					if($template['thumbnail']){
						
						${$template['type_name']}['thumb_content'] = get_content_url( 'thumbnail', $template_image['thumbnail'] );
						${$template['type_name']}['thumb_content_type'] = get_content_format( $template_image['thumbnail_url'] );
					
					}
					
					if ( count($filter_vehicles) > 0 ) {
				//print_r($filter_vehicles); print_r( $template['vehicles']);die();
						if( !contains_tags($filter_vehicles, ${$template['type_name']}['vehicles']) ) {
							
							continue;
							
						}
						
					}

					//if ( count($group_arr) > 0 ) {
					if($group) {
						
						$item_tags = get_ids( $template['tags'] );
					
						//print_r($item['tags']);
						if( !contains_tags($group_arr, $item_tags) ) {
							
							continue;
							
						}
						
					}
					
					if($tag) {
						
						$item_tags = get_ids( $template['tags'] );
					
						//print_r($item['tags']);
						if( !contains_tags($tag_arr, $item_tags) ) {
							
							continue;
							
						}
						
					}
					
					if($template['tags']){
						
						$arr_tags = array_filter( explode(',', $template['tags']) );
						
						$arr_tags_ids = array();
						$arr_template_tags_ids = array();
						
						foreach($arr_tags as $arr_tag) {
							
							if(!in_array( $arr_tag, array_column($tags, 'id'))){									
							
								$template_background_item_tag = $db->content( $arr_tag, 'tags' );
								
								$tag_item = array();
								$tag_item['id'] = (int)$template_background_item_tag['id'];
								$tag_item['name'] = $template_background_item_tag['name'];
								$tag_item['group'] = $template_background_item_tag['group'];
								
								$tag_item['posts'][] = $index_template;
								
								array_push ($tags,  $tag_item);
								
							} else {
							
								$indice = array_search($arr_tag, array_column($tags, 'id'));
						
								$tags[ $indice ]['posts'][] = $index_template;
								
							}
							
							array_push ($arr_tags_ids, array_search($arr_tag, array_column($tags, 'id')));

						}
						
						${$template['type_name']}['tags'] = implode(",", $arr_tags_ids);

					}else { 
						
						${$template['type_name']}['tags'] = "";
					}

					if($template['field1'] || $template['field2'] || $template['field3'] || $template['field4']  || $template['field5']){
						
						$arr_fields = array();
						
						//$arr_fields['name']	= "Field 1";
						$arr_fields['text']	= $template['field1'];
						//$arr_fields['color'] = $template['field1_color'];
						//$arr_fields['style'] = $template['field1_style'];
						${$template['type_name']}['fields']['field_0'] = $arr_fields;
						
						$arr_fields = array();
						
						$arr_fields['text']	= $template['field2'];
						//$arr_fields['color'] = $template['field2_color'];
						//$arr_fields['style'] = $template['field2_style'];
						
						${$template['type_name']}['fields']['field_1'] = $arr_fields;
						
						$arr_fields = array();
						
						$arr_fields['text']	= $template['field3'];
						//$arr_fields['color'] = $template['field3_color'];
						//$arr_fields['style'] = $template['field3_style'];
						
						${$template['type_name']}['fields']['field_2'] = $arr_fields;
						
						$arr_fields = array();
						
						$arr_fields['text']	= $template['field4'];
						//$arr_fields['color'] = $template['field4_color'];
						//$arr_fields['style'] = $template['field4_style'];
						
						${$template['type_name']}['fields']['field_3'] = $arr_fields;
						
						$arr_fields = array();
						
						$arr_fields['text']	= $template['field5'];
						//$arr_fields['color'] = $template['field4_color'];
						//$arr_fields['style'] = $template['field4_style'];
						
						${$template['type_name']}['fields']['field_4'] = $arr_fields;
						
					}
					
					
				}
				
				$json[$template['type_name']][] = ${$template['type_name']};
				
				$index_template ++;
				
			}//die();		
//print_r($tags);die();			
			$json['tags'] = $tags;
		
			if($tags){
			
				$arr_template_tags_ids = array();
			
				foreach($template_tags as $template_tag){
					
					//array_push ($arr_template_tags_ids, array_search($template_tag['id'], array_column($tags, 'id')));
					$teste = array_search($template_tag['id'], array_column($tags, 'id'));
					if($teste){
						
						array_push ($arr_template_tags_ids, $teste);
					}
						
				}
					
			}
			
			//$json['template_tags'] = implode(",", $arr_template_tags_ids);
		 
		}/*else{
				
				$tags = array();
			$template_tags = array();
									
			$templates = $db->query("SELECT 
										templates.id as id,
										templates.name as name,
										templates.status as status,
										templates.image as image,
										templates.thumbnail as thumbnail,
										templates.tags as tags,
										templates.field1 as field1,
										templates.field1_color as field1_color,
										templates.field1_style as field1_style,
										
										templates.field2 as field2,
										templates.field2_color as field2_color,
										templates.field2_style as field2_style,
										
										templates.field3 as field3,
										templates.field3_color as field3_color,
										templates.field3_style as field3_style,
			templates.type as type,
										FIND_IN_SET(templates.id, metadata.value)  as ordem,
										CASE
											WHEN `type` = 2 THEN 'template_styles'
											ELSE  'template_backgrounds'
										END as type_name
									FROM `templates`
									join metadata on (property='styles' or property='backgrounds') and FIND_IN_SET(templates.id, metadata.value) 
									WHERE status=1 
									order by ordem");
								
			$db_template_tags = $db->query("SELECT tags.id as tag_id, tags.name, property as template_type, FIND_IN_SET(tags.id, metadata.value)  as ordem
												FROM `tags`
												join metadata on property='highlighted_backgrounds_tags' and FIND_IN_SET(tags.id, metadata.value) 
												join templates on FIND_IN_SET(tags.id, templates.tags)  
												WHERE tags.status=1
												group by tags.id, tags.name
												order by ordem");

			foreach($db_template_tags as $db_template_tag) {

				$array_item = array();
				
				$array_item['id'] = (int)$db_template_tag['tag_id'];
				$array_item['name'] = $db_template_tag['name'];
				
				array_push ($template_tags,  $array_item);			
				
			}
			
			$template_tags_idxs = array_column($template_tags, 'template_type');
			//print_r($templates); die();
			foreach($templates as $template) {
				$item = array();
				
				if($template['status']){
				
					${$template['type_name']}['id'] = (int)$template['id'];
					${$template['type_name']}['name'] = $template['name'];
					
					
					$template_image = $db->content( $template, 'templates' );
					
					if($template['image']){
							
							${$template['type_name']}['content'] = get_content_url( 'image', $template_image['image'] );
							${$template['type_name']}['content_type'] = get_content_format( $template_image['image_url'] );
						
					}
					
					if($template['thumbnail']){
						
						${$template['type_name']}['thumb_content'] = get_content_url( 'thumbnail', $template_image['thumbnail'] );
						${$template['type_name']}['thumb_content_type'] = get_content_format( $template_image['thumbnail_url'] );
					
					}
					
					if($template['tags']){
							
						$arr_tags = array_filter( explode(',', $template['tags']) );
						
						$arr_tags_ids = array();
						$arr_template_tags_ids = array();
						
						foreach($arr_tags as $arr_tag) {
							
							if(!in_array( $arr_tag, array_column($tags, 'id'))){									
								
								$template_background_item_tag = $db->content( $arr_tag, 'tags' );
								
								array_push ($tags,  $template_background_item_tag);
								
							}
							
							array_push ($arr_tags_ids, array_search($arr_tag, array_column($tags, 'id')));

						}
						
						${$template['type_name']}['tags'] = implode(",", $arr_tags_ids);

					}

					if($template['field1'] || $template['field2'] || $template['field3']){
						
						$arr_fields = array();
						
						//$arr_fields['name']	= "Field 1";
						$arr_fields['text']	= $template['field1'];
						$arr_fields['color'] = $template['field1_color'];
						$arr_fields['style'] = $template['field1_style'];
						${$template['type_name']}['fields']['field_0'] = $arr_fields;
						
						$arr_fields = array();
						
						$arr_fields['text']	= $template['field2'];
						$arr_fields['color'] = $template['field2_color'];
						$arr_fields['style'] = $template['field2_style'];
						
						${$template['type_name']}['fields']['field_1'] = $arr_fields;
						
						$arr_fields = array();
						
						$arr_fields['text']	= $template['field3'];
						$arr_fields['color'] = $template['field3_color'];
						$arr_fields['style'] = $template['field3_style'];
						
						${$template['type_name']}['fields']['field_2'] = $arr_fields;
						
					}
					
					
				}
				
				$json[$template['type_name']][] = ${$template['type_name']};
			}
			
			$json['tags'] = $tags;
			
			if($tags){
			
				$arr_template_tags_ids = array();
			
				foreach($template_tags as $template_tag){
					
					//array_push ($arr_template_tags_ids, array_search($template_tag['id'], array_column($tags, 'id')));
					$teste = array_search($template_tag['id'], array_column($tags, 'id'));
					if($teste){
						
						array_push ($arr_template_tags_ids, $teste);
					}
						
				}
					
			}
			
			$json['template_tags'] = implode(",", $arr_template_tags_ids);
			
		}*/
		
	break;

	case 'templates_old':
		
		$tags = array();
		$template_tags = array();
		
		/*$templates = $db->query("SELECT *, FIND_IN_SET(templates.id, metadata.value)  as ordem,
									CASE
										WHEN `type` = 2 THEN 'template_styles'
										ELSE  'template_backgrounds'
									END as type_name
								FROM `templates`
								join metadata on (property='styles' or property='backgrounds') and FIND_IN_SET(templates.id, metadata.value) 
								WHERE status=1
								order by ordem");*/		
								
		$templates = $db->query("SELECT 
									templates.id as id,
									templates.name as name,
									templates.status as status,
									templates.image as image,
									templates.thumbnail as thumbnail,
									templates.tags as tags,
									templates.field1 as field1,
									templates.field1_color as field1_color,
									templates.field1_style as field1_style,
									
									templates.field2 as field2,
									templates.field2_color as field2_color,
									templates.field2_style as field2_style,
									
									templates.field3 as field3,
									templates.field3_color as field3_color,
									templates.field3_style as field3_style,
		templates.type as type,
									FIND_IN_SET(templates.id, metadata.value)  as ordem,
									CASE
										WHEN `type` = 2 THEN 'template_styles'
										ELSE  'template_backgrounds'
									END as type_name
								FROM `templates`
								join metadata on (property='styles' or property='backgrounds') and FIND_IN_SET(templates.id, metadata.value) 
								WHERE status=1 
								order by ordem");
							
		$db_template_tags = $db->query("SELECT tags.id as tag_id, tags.name, property as template_type, FIND_IN_SET(tags.id, metadata.value)  as ordem
											FROM `tags`
											join metadata on property='highlighted_backgrounds_tags' and FIND_IN_SET(tags.id, metadata.value) 
											join templates on FIND_IN_SET(tags.id, templates.tags)  
											WHERE tags.status=1
											group by tags.id, tags.name
											order by ordem");

		foreach($db_template_tags as $db_template_tag) {

			$array_item = array();
			
			$array_item['id'] = (int)$db_template_tag['tag_id'];
			$array_item['name'] = $db_template_tag['name'];
			
			array_push ($template_tags,  $array_item);			
			
		}
		
		$template_tags_idxs = array_column($template_tags, 'template_type');
		//print_r($templates); die();
		foreach($templates as $template) {
			$item = array();
			
			if($template['status']){
			
				${$template['type_name']}['id'] = (int)$template['id'];
				${$template['type_name']}['name'] = $template['name'];
				
				
				$template_image = $db->content( $template, 'templates' );
				
				if($template['image']){
						
						${$template['type_name']}['content'] = get_content_url( 'image', $template_image['image'] );
						${$template['type_name']}['content_type'] = get_content_format( $template_image['image_url'] );
					
				}
				
				if($template['thumbnail']){
					
					${$template['type_name']}['thumb_content'] = get_content_url( 'thumbnail', $template_image['thumbnail'] );
					${$template['type_name']}['thumb_content_type'] = get_content_format( $template_image['thumbnail_url'] );
				
				}
				
				if($template['tags']){
						
					$arr_tags = array_filter( explode(',', $template['tags']) );
					
					$arr_tags_ids = array();
					$arr_template_tags_ids = array();
					
					foreach($arr_tags as $arr_tag) {
						
						if(!in_array( $arr_tag, array_column($tags, 'id'))){									
							
							$template_background_item_tag = $db->content( $arr_tag, 'tags' );
							
							array_push ($tags,  $template_background_item_tag);
							
						}
						
						array_push ($arr_tags_ids, array_search($arr_tag, array_column($tags, 'id')));

					}
					
					${$template['type_name']}['tags'] = implode(",", $arr_tags_ids);

				}

				if($template['field1'] || $template['field2'] || $template['field3']){
					
					$arr_fields = array();
					
					//$arr_fields['name']	= "Field 1";
					$arr_fields['text']	= $template['field1'];
					$arr_fields['color'] = $template['field1_color'];
					$arr_fields['style'] = $template['field1_style'];
					${$template['type_name']}['fields']['field_0'] = $arr_fields;
					
					$arr_fields = array();
					
					$arr_fields['text']	= $template['field2'];
					$arr_fields['color'] = $template['field2_color'];
					$arr_fields['style'] = $template['field2_style'];
					
					${$template['type_name']}['fields']['field_1'] = $arr_fields;
					
					$arr_fields = array();
					
					$arr_fields['text']	= $template['field3'];
					$arr_fields['color'] = $template['field3_color'];
					$arr_fields['style'] = $template['field3_style'];
					
					${$template['type_name']}['fields']['field_2'] = $arr_fields;
					
				}
				
				
			}
			
			$json[$template['type_name']][] = ${$template['type_name']};
		}
		
		$json['tags'] = $tags;
		
		if($tags){
		
			$arr_template_tags_ids = array();
		
			foreach($template_tags as $template_tag){
				
				//array_push ($arr_template_tags_ids, array_search($template_tag['id'], array_column($tags, 'id')));
				$teste = array_search($template_tag['id'], array_column($tags, 'id'));
				if($teste){
					
					array_push ($arr_template_tags_ids, $teste);
				}
					
			}
				
		}
		
		$json['template_tags'] = implode(",", $arr_template_tags_ids);
		
		
	break;

	case 'tags':
		
		$filter = get_request('filter');
		$metadata_name = null;
		
		$group = mb_strtolower( get_request('group') );
		
		$where =" and `group` = ''";
		if($group) $where = " and FIND_IN_SET( `group`, '{$group}' )>0 ";
		
		if ($filter === 'galeria') $metadata_name = 'whatsapp_gallery_tags';
		elseif ($filter === 'campanha') $metadata_name = 'campaign_tags';

		$json['tags'] = [];
		
		if ($metadata_name) {
		
			$tags_id = get_ids( $db->get_metadata_value( $metadata_name ) );
			
			foreach($tags_id as $tag_id) {
				
				//$tag = $db->content( $tag_id, 'tags' );
				
				$tag = current( $db->query( 
					"SELECT *  
						FROM `tags` 
						where status = 1 and `id` = {$tag_id} $where"));
				
				if ( $tag ) {
				
					$tag = $db->content( $tag, 'tags' );
					
					$tag_item = array();
					$tag_item['id'] = (int)$tag['id'];
					$tag_item['name'] = $tag['name'];
					
					$json['tags'][] = $tag_item;
					
				}
				
			}
			
		}
		
	break;
	
	//- Agora será possivel outras reações além do like nos posts as reações serão: Like, Love, Wow, Clap, Sad
	case 'rank_new':
	
		if ($user) {
			
			$post_id = (int)get_request('id');
			$category_id = (int)get_request('category');
			
			//$category_s = "";
			//if($category_id) $category_s = " and category ={$category_id}";
			
			$ranking = current( $db->query( "SELECT * FROM ranking WHERE user = :user AND post = :post AND category = :category ", array(
				'user' => $user['id'],
				'post' => $post_id,
				'category' => $category_id
			) ) );
			
			if (!$ranking) {
				
				$ranking_id = $db->insert('ranking', array(
					'user' => $user['id'],
					'post' => $post_id,
					'category' => $category_id
				));
				
				$ranking = $db->select_id( 'ranking', $ranking_id );
				
			
			
				if (has_request('score')) {
					
					$db->update('ranking', $ranking['id'], array(
						'score' => min( 5, max( 1, (int)get_request('score') ) )
					));
					
					// refresh
					$ranking = $db->select_id( 'ranking', $ranking['id'] );
					
				}
			
				
				$json['score'] = (int)$ranking['score'];
				
			}else{
				
				/*$db->remove('ranking', $ranking['id']);
				
				$json['score'] = 0;*/
			
				try {
				
					$db->query( "DELETE FROM ranking WHERE id = :id ", array(
						'id' => $ranking['id']
					) );
					
				} catch (Exception $exc) {
					
					$json['score'] = 0;
					
				}

			}
			
			$json['id'] = $ranking['id'];

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
	break;
	
	case 'rank':
	
		if ($user) {
			
			$post_id = (int)get_request('id');
			$category_id = (int)get_request('category');
			$score = (int)get_request('score');
			
			//$category_s = "";
			//if($category_id) $category_s = " and category ={$category_id}";
			
			$ranking = current( $db->query( "SELECT * FROM ranking WHERE user = :user AND post = :post AND category = :category ", array(
				'user' => $user['id'],
				'post' => $post_id,
				'category' => $category_id
			) ) );
			
			if (!$ranking) {
				
				if($score){
					
					$ranking_id = $db->insert('ranking', array(
						'user' => $user['id'],
						'post' => $post_id,
						'category' => $category_id,
						'score' => $score
					));
					
					$ranking = $db->select_id( 'ranking', $ranking_id );
					
				
				
					/*if (has_request('score')) {
						
						$db->update('ranking', $ranking['id'], array(
							'score' => min( 5, max( 1, (int)get_request('score') ) )
						));
						
						// refresh
						$ranking = $db->select_id( 'ranking', $ranking['id'] );
						
					}*/
				
					
					$json['score'] = (int)$ranking['score'];
					$json['id'] = $ranking['id'];
					
				}
				
			}else{
		//echo "aki $score SA"; die();	
				/*$db->remove('ranking', $ranking['id']);
				
				$json['score'] = 0;*/
				if( $score > 0 ){
					
			//echo "aki $score SA"; die();			
					$db->update('ranking', $ranking['id'], array(
						'score' => min( 5, max( 1, (int)get_request('score') ) )
					));
					
					$ranking = $db->select_id( 'ranking', $ranking['id'] );
					$json['score'] = (int)$ranking['score'];
					$json['id'] = $ranking['id'];
					
				}else{
					
					try {
					
						$db->query( "DELETE FROM ranking WHERE id = :id ", array(
							'id' => $ranking['id']
						) );
						
					} catch (Exception $exc) {
						
						$json['score'] = 0;
						
					}
					
				}
			}
			
			

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
	break;

	case 'view':
		
		if ($user) {
			
			$category = (int)get_request('category');
			if($category != 7) $category =0;
			
			if (has_request('ids')) {
				
				$post_ids = get_ids( get_request('ids') );
				
				foreach($post_ids as &$post_id) {
					
					$viewed = $db->insert('viewed', array(
						'user' => $user['id'],
						'post' => $post_id,
						'category' => $category
					));
					
				}
				
				$json['ids'] = $post_ids;
				
			} else {
			
				$post_id = (int)get_request('id');
				
				if ($post_id > 0) {
				
					$viewed = $db->insert('viewed', array(
						'user' => $user['id'],
						'post' => $post_id,
						'category' => $category
					));

					$json['id'] = (int)$viewed;
					
				}
				
			}

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
	
	break;
	
	case 'downloaded':
		
		if ($user) {
			
			//$category = (int)get_request('category');
			//if($category != 7) 
			//$category =0;
			
			$post_id = (int)get_request('id');
			
			if ($post_id > 0) {
			
				$downloaded = $db->insert('downloaded', array(
					'user' => $user['id'],
					'post' => $post_id,
					'category' => 0
				));

				$json['id'] = (int)$downloaded;
				
			}
		
		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
	
	break;

	case 'favorite':
	case 'read_later':
	case 'notification':
	
		if ($user) {
			
			$post_id = (int)get_request('id');
			$category = (int)get_request('category');
			
			if ($q == 'favorite') $table = 'favorites';
			elseif ($q == 'read_later') $table = 'read_later';
			elseif ($q == 'notification') $table = 'notifications';
			
			
			
			$table_s = "SELECT * FROM {$table} WHERE user = :user AND post = :post";
			
			$arr_columns =array(
					'user' => $user['id'],
					'post' => $post_id
				);
			
			if( $q == 'read_later' ){
			
				$table_s .= " AND category= :category";
				$arr_columns['category'] = $category;
				
			} 
			
			//print_r($table_s); die();
			
			$contentmeta = current( $db->query( $table_s, $arr_columns ) );
			
			if (!$contentmeta) {
				
				$contentmeta_id = $db->insert( $table, $arr_columns);
				
				$contentmeta = $db->select_id( $table, $contentmeta_id );
				
			}
			
			if (has_request('value')) {
				
				$db->update( $table, $contentmeta['id'], array(
					'value' => (int)get_request('value') == 1
				));
				
				// refresh
				$contentmeta = $db->select_id( $table, $contentmeta['id'] );
				
			}

			$json['id'] = (int)$contentmeta['id'];
			$json['value'] = (bool)$contentmeta['value'];

		}
		
	break;

	case 'global':
		
		$intro_banner_meta = $db->get_metadata( "intro_banner" );
		$intro_banner_image = $uploader->get( $intro_banner_meta['value'] );
		
		$json['login_screen'] = url( $intro_banner_image['url'] );
		
		$login_screen_video_meta = $db->get_metadata( "login_screen_video" );
		$login_screen_video = $uploader->get( $login_screen_video_meta['value'] );
		
		$json['login_screen_video'] = $login_screen_video_meta['value']>0? url( $login_screen_video['url'] ) : '';
		
		$compartilhar_intro_meta = $db->get_metadata( "compartilhar_intro" );
		$compartilhar_intro = $uploader->get( $compartilhar_intro_meta['value'] );
		
		$json['compartilhar_intro'] = $compartilhar_intro_meta['value']>0? url( $compartilhar_intro['url'] ): '';
		
		$compartilhar_imagens_meta = $db->get_metadata( "compartilhar_imagens" );
		$compartilhar_imagens = $uploader->get( $compartilhar_imagens_meta['value'] );
		
		$json['compartilhar_imagens'] = $compartilhar_imagens_meta['value']>0? url( $compartilhar_imagens['url'] ): '';
		
		$compartilhar_catalogos_meta = $db->get_metadata( "compartilhar_catalogos" );
		$compartilhar_catalogos = $uploader->get( $compartilhar_catalogos_meta['value'] );
		
		$json['compartilhar_catalogos'] = $compartilhar_catalogos_meta['value']>0? url( $compartilhar_catalogos['url'] ): '';
		
		$compartilhar_cards_atributos_meta = $db->get_metadata( "compartilhar_cards_atributos" );
		$compartilhar_cards_atributos = $uploader->get( $compartilhar_cards_atributos_meta['value'] );
		
		$json['compartilhar_cards_atributos'] = $compartilhar_cards_atributos_meta['value']>0? url( $compartilhar_cards_atributos['url'] ): '';
		
		$compartilhar_comp_concorrencia_meta = $db->get_metadata( "compartilhar_comp_concorrencia" );
		$compartilhar_comp_concorrencia = $uploader->get( $compartilhar_comp_concorrencia_meta['value'] );
		
		$json['compartilhar_comp_concorrencia'] = $compartilhar_comp_concorrencia_meta['value']>0 ? url( $compartilhar_comp_concorrencia['url'] ): '';
		
		$compartilhar_comp_versoes_meta = $db->get_metadata( "compartilhar_comp_versoes" );
		$compartilhar_comp_versoes = $uploader->get( $compartilhar_comp_versoes_meta['value'] );
		
		$json['compartilhar_comp_versoes'] = $compartilhar_comp_versoes_meta['value']>0? url( $compartilhar_comp_versoes['url'] ): '';
		
		$compartilhar_on_demand_meta = $db->get_metadata( "compartilhar_on_demand" );
		$compartilhar_on_demand = $uploader->get( $compartilhar_on_demand_meta['value'] );
		
		$json['compartilhar_on_demand'] = $compartilhar_on_demand_meta['value']>0? url( $compartilhar_on_demand['url'] ): '';
		
	break;
	
	case 'globals':
		
		
		
		$intro_banner_meta = $db->get_metadata( "intro_banner" );
		$intro_banner_image = $uploader->get( $intro_banner_meta['value'] );
		
		$json['globals']['login_screen'] = url( $intro_banner_image['url'] );
		
		$login_screen_video_meta = $db->get_metadata( "login_screen_video" );
		$login_screen_video = $uploader->get( $login_screen_video_meta['value'] );
		
		$json['globals']['login_screen_video'] = $login_screen_video_meta['value']>0? url( $login_screen_video['url'] ) : '';
		
		$compartilhar_intro_meta = $db->get_metadata( "compartilhar_intro" );
		$compartilhar_intro = $uploader->get( $compartilhar_intro_meta['value'] );
		
		$json['globals']['compartilhar_intro'] = $compartilhar_intro_meta['value']>0? url( $compartilhar_intro['url'] ): '';
		
		$compartilhar_imagens_meta = $db->get_metadata( "compartilhar_imagens" );
		$compartilhar_imagens = $uploader->get( $compartilhar_imagens_meta['value'] );
		
		$json['globals']['compartilhar_imagens'] = $compartilhar_imagens_meta['value']>0? url( $compartilhar_imagens['url'] ): '';
		
		$compartilhar_catalogos_meta = $db->get_metadata( "compartilhar_catalogos" );
		$compartilhar_catalogos = $uploader->get( $compartilhar_catalogos_meta['value'] );
		
		$json['globals']['compartilhar_catalogos'] = $compartilhar_catalogos_meta['value']>0? url( $compartilhar_catalogos['url'] ): '';
		
		$compartilhar_cards_atributos_meta = $db->get_metadata( "compartilhar_cards_atributos" );
		$compartilhar_cards_atributos = $uploader->get( $compartilhar_cards_atributos_meta['value'] );
		
		$json['globals']['compartilhar_cards_atributos'] = $compartilhar_cards_atributos_meta['value']>0? url( $compartilhar_cards_atributos['url'] ): '';
		
		$compartilhar_comp_concorrencia_meta = $db->get_metadata( "compartilhar_comp_concorrencia" );
		$compartilhar_comp_concorrencia = $uploader->get( $compartilhar_comp_concorrencia_meta['value'] );
		
		$json['globals']['compartilhar_comp_concorrencia'] = $compartilhar_comp_concorrencia_meta['value']>0 ? url( $compartilhar_comp_concorrencia['url'] ): '';
		
		$compartilhar_comp_versoes_meta = $db->get_metadata( "compartilhar_comp_versoes" );
		$compartilhar_comp_versoes = $uploader->get( $compartilhar_comp_versoes_meta['value'] );
		
		$json['globals']['compartilhar_comp_versoes'] = $compartilhar_comp_versoes_meta['value']>0? url( $compartilhar_comp_versoes['url'] ): '';
		
		$compartilhar_on_demand_meta = $db->get_metadata( "compartilhar_on_demand" );
		$compartilhar_on_demand = $uploader->get( $compartilhar_on_demand_meta['value'] );
		
		$json['globals']['compartilhar_on_demand'] = $compartilhar_on_demand_meta['value']>0? url( $compartilhar_on_demand['url'] ): '';
		
		
	break;
	
	case 'devices':
		
		$devices = array_column( $db->query( "
			SELECT `user`, `device`, `os`
			FROM `devices` 
			GROUP BY `device`
			ORDER BY `id` DESC"), 'device' );

		$json['devices'] = $devices;

	break;

	case 'login':
		
		$email = get_request('email');
		$password = get_request('password');
		
		$device_key = get_request('device');
		$device_app_build = get_request('build');
		$device_os = get_request('os');
		
		$user_data = current( $db->query( "SELECT * FROM users WHERE email = :email", array(
			'email' => $email
		) ) );
		
		if ($user_data) {
			
			if ($user_data['status'] == 1) {
			
				if($user_data['status_email'] == 1 || $user_data['level'] == 1) {
					
					if($user_data['status_gerente'] == 1 || $user_data['level'] == 1) {
					
						if ($user_data['password'] == $password) {
							
							$secret_key = sha1( 'RENAULT:' . $user_data['id'] . '.' . $email . '.' . $password );
							
							$db->update('users', $user_data['id'], array(
								'secret_key' => $secret_key
							));
							
							register_last_login($user_data['id']);
							
							if ($device_key) {
								
								// register device
								
								$db->insert('devices', array(
									'user' => $user_data['id'],
									'device' => $device_key,
									'app_build' => $device_app_build,
									'os' => $device_os
								));
								
							}
							
							$json['name'] = $user_data['name'];
							$json['short_name'] = explode( ' ', $user_data['name'] )[0];
							$json['email'] = $user_data['email'];
							$json['bir'] = $user_data['bir'];
							$json['level'] = (int)$user_data['level'];
							
							//user_category
							$user_category = current($db->query("SELECT name FROM `user_categories` WHERE id={$user_data['user_category']}"));
							if($user_category)  $json['category_name'] = $user_category['name']; 
							
							$json['metadata'] = $user_data['metadata'] ? json_decode( $user_data['metadata'], true ) : [];
							$json['secret_key'] = $secret_key;
							$json['logged'] = true;
							
							//$json['background_image'] = $user_data['background_image'];
							
							if( $user_data['background_image'] > 0 ){
						$user_data['background_image'] = 9690;			
								$user_data_db = $db->content( $user_data, 'users' );
						
								$json['background_image_id'] = $user_data['background_image'];
								$json['background_image_url'] = url( $user_data_db['background_image_url'] );
								
								$json_str = $db->get_metadata_value( "background_images" );
		
								//$background_image_ids = array_column(current(json_decode($json_str, true)), 'background_image_id');
								$background_image_ids = current(json_decode($json_str, true));
								
								$indice = array_search($user_data['background_image'], array_column($background_image_ids, 'background_image_id'));

								if ($indice !== false) {

									$json['text_color'] = $background_image_ids[$indice]['color'];
									
								} else {
									
									$error = "Imagem Inválida.";
									
								}
								
							}
							
							if(empty($user_data['categories'])) $user_data['categories'] = "noticias,pos-vendas,comunicados,redes-sociais";
								
							$json['categories'] = explode(",", $user_data['categories']);
							$json['image_url'] =  $user_data['image_url'];
							
						} else {
							
							$error = "Senha Inválida.";
							
						}
						
					} else {
					
						if($user_data['status_gerente'] === "0") {
							
							$error = "Acesso Recusado.";
							
						} else {
							
							$error = "Aguardando confirmação gerente.";
							
						}
							
					
					}
				
				} else {
					
					$error = "Email não confirmado.";
					
				}
			
			} else {
				
				$error = "Conta desativada.";
				
			}
			
		} else {
			
			$error = "Email não cadastrado.";
			
		}

	break;

	case 'user':
		
		$id = (int)get_request('id');
		
		$json['user'] = $db->select_id( 'users', $id );
		
	break;
	
	case 'get_background_images':
	
		$background_images = [];
		
		//$background_image_ids = get_ids( $db->get_metadata_value( "background_images2" ) );
		
		$json_str = $db->get_metadata_value( "background_images" );
		
		$background_image_ids = current(json_decode($json_str, true));
		
		//print_r($background_image_ids);die();
		//foreach($background_image_ids as &$background_image_id) {
			//print_r($background_image_ids); die();
		foreach($background_image_ids as &$background_image_id) {
			
			if($background_image_id['background_image_id'] != 9690){
				
				$background_image = array();
					
				$image = $uploader->get( $background_image_id['background_image_id'] );
				
				$background_image['id'] = $background_image_id['background_image_id'];
				$background_image['background_image_url'] = url($image ? $image['url'] : '');
				$background_image['text_color'] = $background_image_id['color'];
				
				array_push ($background_images,  $background_image);
				
			}
			
		}
		
		$json['background_images'] = $background_images;
		
	break;
		
	case 'create_user':

		$app_user = array();
		$app_user['name'] 	= get_request('name');
		$app_user['email'] = get_request('email');
		$app_user['bir'] 	= (int)get_request('bir');
		$app_user['password'] = get_request('password');
		
		$user = new User();
		
		$reponse_message = $user->create_user_app($app_user);
		
		$user_id = (int)$reponse_message;

		if( $user_id > 0) {
			
			$json['user_id'] = $user_id;
			
		} else {
			
			$error = $reponse_message;
			
		}

	break;

	case 'confirm_user':
		
		$uuid = get_request('token');

		$token_data = $token->get( $uuid );
		//print_r($token_data); die();	
		if ( $token_data && $token->is_valid( $token_data ) ) {	
	
			$content_id = $token_data["uid"];
	
			$user_data = new User($content_id);
		
			if( $user_data->id ){
				
				$user_data->confirm(array(
					'name' => get_request('name'),
					'email' => get_request('email'),
					'bir' => $user_data->bir,
					'password' => get_request('password')
				));
				
				//$token->burn($uuid);
				
				$json['success'] = true;
				
			} else {
			
				$error = "Usuário não encontrado.";
				
			}

		} else {
			
			$error = "Token expirado.";
			
		}

	break;
		
	/*
	case 'update_user':
		
		if ( $user )  {
	
			if($user['password'] == get_request('current_password')){ 

				$user_data = new User($user['id']);

				$user_data->update_user_app(array(
					'name' => get_request('name'),
					'password' => get_request('new_password') ?: $user['password']
				));
				
				$json['success'] = true;
				
			} else {
				
				$error = "Senha Inválida.";
				
			}
			
			
		} else {
			
			$error = "Usuário não logado.";
			
		}

	break;
	*/
	
	case 'update_user':
	
		$background_image = get_request('background_image');
		//$background_image_ids = get_ids( $db->get_metadata_value( "background_images" ) );
		
		$json_str = $db->get_metadata_value( "background_images" );
		
		$background_image_ids = array_column(current(json_decode($json_str, true)), 'background_image_id');
		
		//$novo_array = array_column($background_image_ids, 'background_image_id');
		
		//$novo_array = array_column($background_image_ids, 'nome');
		//print_r($background_image_ids); die();
		//$background_image_ids = get_ids( $db->get_metadata_value( "background_images2" ) );
		
		
		
		$user_up = array();
		$error = "";
		
		if ( $user )  {
		
			//nome
			if(get_request('name')){
				
				$user_up['name'] = get_request('name');
				
			}
			
			//categorias
			if(get_request('categories')){
				
				$items = explode(",", get_request('categories'));

				$items = array_map('trim', $items);
				
				$items = array_unique($items);

				if (count($items) < 3 || count($items) > 4) {
				
					$error = "A quantidade de itens deve estar entre 3 e 4.";
				
				} else {
					
					//$categorias = ["noticias","redes","careservices","comunicados","universo","academy"];
					$categorias = ["noticias","redes-sociais","pos-vendas","comunicados","universo-renault","renault-academy"];
					
					foreach ($items as $item) {
						
						if ( !in_array($item, $categorias) and $error == "" ) {
							
							$error =  "Categoria '{$item}' inválida.";
							
						}
					}
					
					if( $error == "" )
						$user_up['categories'] = implode(",", $items);
					
				}

			}
			
			//senha
			if(get_request('new_password')){
		
				if($user['password'] == get_request('current_password')){ 
					
					$user_up['password'] = get_request('new_password') ?: $user['password'];
					
				} else {
					
					$error = "Senha Inválida.";
					
				}

			}
			
			//cor de fundo
			if(get_request('background_image')){
				
				if( in_array( $background_image, $background_image_ids ) ){
					
					$user_up['background_image'] = $background_image;
					
				} else {
					
					$error = "Imagem inválida.";
					
				}
				
			}
			
			//upload imagem
			if(get_request('image_url')){
			/*	
				$req_uri = $_SERVER['REQUEST_URI'];
				$path = substr($req_uri,0,strrpos($req_uri,'/'));
				
				$file_id = $uploader->upload( $image, $path );
				$user_up['image'] = $file_id;*/
				
				$user_up['image_url'] = get_request('image_url');
			//echo 	$user_up['image_url']; die();
			}
			
			if( $error == "" && count($user_up) > 0){
				
				//print_r($user); die();
				
				$user_data = new User($user['id']);
				
				$user_data->update_user_app($user_up);
				
				
				$user_data_db = $db->content( $user['id'], 'users' );
		//print_r($user_data_db); die();
				$json['name'] = $user_data_db['name'];
				$json['background_image_url'] = url( $user_data_db['background_image_url'] );
				$json['image_url'] = $user_data_db['image_url'];
				if(!empty($user_data_db['categories'])) $json['categories'] = explode(",", $user_data_db['categories']);
								
					
				$json['success'] = true;
				
			}

		} else {
			
			$error = "Usuário não logado.";
			
		}
		
	break;
	
	/*case 'update_user_background_image':
		
		if ( $user )  {
	
			//if($user['password'] == get_request('current_password')){ 
			
				$background_image = get_request('background_image');

				$background_image_ids = get_ids( $db->get_metadata_value( "background_images" ) );
				
				if( in_array( $background_image, $background_image_ids ) ){
					
					$user_data = new User($user['id']);

					$user_data->update_user_app(array(
						'background_image' => $background_image
					));
					
					
				} else {
					
					$error = "Imagem inválida.";
					
				}

				
				
				$json['success'] = true;
			
			
		} else {
			
			$error = "Usuário não logado.";
			
		}

	break;*/
		
	case 'disable_user':
		
		if ( $user )  {
	
			$user_data = new User($user['id']);

			$user_data->disable("app");
			
			$json['success'] = true;
			
		} else {
			
			$error = "Usuário não logado.";
			
		}

	break;
		
	case 'request_reactivation':
		
		$uuid = get_request('token');

		$token_data = $token->get( $uuid );
		
		if ( $token_data && $token->is_valid( $token_data ) ) {	
	
			$content_id = $token_data["uid"];
	
			$user_data = new User($content_id);
			
			$user_data->request_reactivation();
			
			$token->burn($uuid);
			
			$json['success'] = true;

		} else {
			
			$error = "Token expirado.";
			
		}

	break;
	
	case 'password_confirm':
		
		$uuid = get_request('token');

		$token_data = $token->get( $uuid );
		
		if ( $token_data && $token->is_valid( $token_data ) ) {	
	
			$content_id = $token_data["uid"];
	
			$user_data = new User($content_id);

			$user_data->update_pass(array(
				'email' => get_request('email'),
				'password' => get_request('password')
			));
			
			$token->burn($uuid);
			
			$json['success'] = true;

		} else {
			
			$error = "Token expirado.";
			
		}

	break;

	case 'password_request':
		
		$email 	= get_request('email');
		
		$user_data = current( $db->query( "SELECT * FROM users WHERE email = :email", array(
			'email' => $email
		) ) );
		
		if ($user_data) {
			
			$id = (int)$user_data['id'];
			
			$user = new User($id);
			
			$json['message'] = $user->password_request();
			
		} else {
			
			$error = "Email não cadastrado.";
			
		}

	break;
		
	case 'invited_request':
		
		$name = get_request('name');
		
		$email = get_request('email');
		
		$manager = get_request('manager');
		
		if($email != ''){
			
			$user = new User();
			
			if($name == '')$email;
		
			if($manager){
				
				$user->enviaConviteGerente($name, $email, "app");
				
			} else {
				
				$user->enviaConvite($name, $email, "app");
				
			}
			
		}
		$json['message'] = "Convite enviado.";
		
		
		//$json['user'] = $db->select_id( 'users', $id );
		
	break;

	case 'notifications':
		
		$notifications = [];
		
		if ($user) {
			
			$update = has_request('update');
			
			$posts = $db->query( "SELECT * FROM posts WHERE status = 1 AND date < :date ORDER BY date DESC limit 9", array(
				'date' => $user['last_view']
			) );
			
			foreach($posts as &$post) {
				
				$post = $db->content( $post, 'posts' );
				
				$viewed = current( $db->query( "SELECT * FROM viewed WHERE user = :user AND post = :post AND category=0 LIMIT 99", array(
					'user' => $user['id'],
					'post' => $post['id']
				) ) );
				
				$notification = [];
				$notification['id'] = (int)$post['id'];
				$notification['message'] = htmltotext( $post['text'], 50 );
				$notification['content'] = url( $post['image_url'] );
				$notification['content_type'] = get_content_format( $post['image_url'] );
				$notification['viewed'] = $viewed ? true : false;
				
				$notifications[] = $notification;
				
			}
			
		}
		
		$json['notifications'] = $notifications;
		
	break;

	case 'feed_vehicle':
	
		$filter = get_request('filter');
		$vehicles = get_request('vehicles');
		$secret_key = get_request('secret_key');
		$time = get_request('time');
		$version = get_request('version');
		
		if($vehicles){
		
			$param = "feed&filter=$filter&vehicles=$vehicles&secret_key=$secret_key&time=$time&version=$version";

			$catalogos_compactos = [];
			$card_comparativo = [];
			$imagens_veiculos = [];
			$cards_atributos = [];
			$comparativos_concorrencia = [];
		
		
			$catalogos_compactos = get_api_arr("$param"."&tags=12", "");
			$card_comparativo = get_api_arr("$param"."&tags=19", "");
			
			
			//q=vehicleversions&secret_key=f2b924fe0d9a2a6273acb63ef46a89590911763a&time=1674660598198
			$imagens_veiculos = get_api_arr("vehicleversions&secret_key=$secret_key&time=$time&vehicles=$vehicles", "");
			//print_r($imagens_veiculos); die();
			
			
			//$imagens_veiculos = get_api_arr("$param"."&group=images&content_type=image&vehicleversions", "");
			//$cards_atributos = get_api_arr("$param"."&content_type=image&tags=11,20,25,26,27,28,29,32,57,66,68", "");
			
			$cards_atributos = get_api_arr("$param"."&group=cards_attributes", "");
			
			//$comparativos_concorrencia = get_api_arr("$param"."&content_type=image&tags=10", "");
			$comparativos_concorrencia = get_api_arr("$param"."&content_type=image&group=competition_comparatives", "");
			
			$json['catalogos_compactos'] = $catalogos_compactos;
			$json['card_comparativo'] = $card_comparativo;
			$json['imagens_veiculos'] = $imagens_veiculos;
			$json['cards_atributos'] = $cards_atributos;
			$json['comparativos_concorrencia'] = $comparativos_concorrencia;
			
		}else{
			
			$error = "Veículo inválido.";
			
		}
	
	break;

	case 'vehicleversions':
	
		$vehicles = get_request('vehicles');
		
		
		$sql_par = "";
		if($vehicles) $sql_par = "and vehicle_version.vehicle = $vehicles";
		//print_r($vehicles); die();
		//grupo de tags
		//$group = mb_strtolower( get_request('group') );
		//group=images
		$group = 'images';
		
		$vehicle_versions = [];

		$ordem_ids = get_ids( $db->get_metadata_value( "vehicle_version" ) );
		
		$tag_images = 
				$db->query( "SELECT * FROM `tags`
							where `group`= 'images' ");
		
		$tags_arr = [];
		
		foreach($ordem_ids as $vv_id) {
			
			$vehicle_versions_db = $db->query( "SELECT  
									vehicle_version.id as id,
									vehicle_version.vehicle as vehicle,
									vehicle_version.version as version,
									vehicle_version.link as link,
									vehicle_version.image as image,
                                    count(*) as quantidade
							FROM `posts` 
							join `vehicle_version` on posts.version=vehicle_version.version AND FIND_IN_SET( vehicle_version.vehicle, posts.vehicles )>0
							where posts.status=1 and vehicle_version.status=1 and vehicle_version.id=$vv_id  $sql_par
                            group by vehicle_version.id, vehicle_version.vehicle, vehicle_version.version,vehicle_version.link, vehicle_version.image");
			
			/*$vehicle_versions_db = 
				$db->query( "SELECT  * 
					from vehicle_version
					where status=1 and id=$vv_id and version in 
					(select p.version from posts p
					 where p.status=1 AND p.version=vehicle_version.version AND FIND_IN_SET( vehicle_version.vehicle, p.vehicles )>0 )");
 */
			//foreach($vehicle_versions_db as $vehicle_version_db) {
				
			foreach($vehicle_versions_db as &$vehicle_version_db) {
			
				$vehicle_version_db = $db->content( $vehicle_version_db, 'vehicle_version' );
				
				$vehicle_version = array(
					'id' => (int)$vehicle_version_db['id'],
					'vehicle' => (int)$vehicle_version_db['vehicle'],
					'vehicle_name' => $vehicle_version_db['vehicle_name'],
					'version' => (int)$vehicle_version_db['version'],
					'version_name' => $vehicle_version_db['version_name'],
					'link' => $vehicle_version_db['link'],
					'thumbnail' => url( $vehicle_version_db['image_url'] ), //$vehicle_version_db['image']
					'quantidade' => $vehicle_version_db['quantidade']
				);
				
				$posts =  $db->query( " SELECT * FROM `posts` 
								WHERE
									posts.status = 1 AND category = 5 AND version<>''
									AND posts.image in (select uploads.id from uploads where uploads.id = posts.image and type like '%image%' )
									AND version={$vehicle_version_db['version']}
									AND FIND_IN_SET( {$vehicle_version_db['vehicle']}, posts.vehicles )>0 ");
				
				foreach($posts as $post) {

					$tags_in_group = array_column($db->query( 
						"SELECT id
							FROM `tags` where status=1 and 
								`group`= 'images'and FIND_IN_SET( `id`, :tagsIds )>0; ", 
								array(
									'tagsIds' => $post['tags']
								) ), 'id');
						

					if( !$tags_in_group  ) {

						continue;

					}else{
					
						//$item['tags'] = $tags_in_group;
						$vehicle_version['tags'] =  $tags_in_group;
						//array_push ($tags_arr,  $tags_in_group);
						
						if(!in_array( $tags_in_group[0], array_column($tags_arr, 'id'))){
					
							$tags_db = $db->content( $tags_in_group[0], 'tags' );
							
							$tag_item = array();
									
							$tag_item['id'] = (int)$tags_db['id'];
							$tag_item['name'] = $tags_db['name'];
							$tag_item['posts'][] = count($vehicle_versions);

							array_push ($tags_arr,  $tag_item);
						
						} else {
							
							$chave = array_search( $tags_in_group[0], array_column($tags_arr, 'id'));
						
							if(!in_array( count($vehicle_versions), $tags_arr[$chave]['posts'])){
								
								array_push ( $tags_arr[$chave]['posts'],  count($vehicle_versions) );
								
							}else{
								
								continue;
								
							}
						
						}

					}
					
				}

				array_push ($vehicle_versions,  $vehicle_version);
			
			}
		}
		//die();
		//echo"---------------------------------------------";
		//print_r($tags_arr); die();
		
		/*
		
		if ( $group ) {
			//SELECT GROUP_CONCAT(id) FROM `tags` where FIND_IN_SET( `group`, 'grupo3' )>0 and FIND_IN_SET( `id`, '19,22,27' )>0 ORDER BY `id` DESC;
			$tags_in_group = array_column($db->query( 
				"SELECT id
					FROM `tags` where status=1 and 
					FIND_IN_SET( `group`, :group )>0 and 
					FIND_IN_SET( `id`, :tagsIds )>0; ", array(
						'group' => $group,
						'tagsIds' => $post['tags']
				) ), 'id');
				

			if( !$tags_in_group  ) {

				continue;

			}else{
			
				$item['tags'] = $tags_in_group;
				
			}
			/*echo "------------------------------";
			echo $item['id']." / ". $post['tags'] ." /- ";
			print_r($item['tags']);	
			print_r($tags_in_group);		
			//$item['tags'] = 
			
			
		}
		
		*/
		$json['feed'] = $vehicle_versions;
		//$json['vehicle_versions'] = $vehicle_versions;
		$json['tags'] = $tags_arr;
		
	
	break;
	
	case 'feed':
		
		//user
		$user_id = isset($user['id'])? $user['id'] : 0;
		
		// feed
		
		$version = get_request('version');
		$feed_limit = (int)get_request('limit');
		$trending = has_request('trending');
		if($trending) $feed_limit=20;
		
		
		//$version = '1';
		
		//&vehicleversions
		//&vehicleversion=iconic
		$vehicleversions = has_request('vehicleversions');
		$vehicleversion = get_request('vehicleversion');
		
		$search = mb_strtolower( get_request('search') );
		$search_tags = mb_strtolower( get_request('tags') );
		
		//grupo de tags
		$group = "";//mb_strtolower( get_request('group') );
		
		$tag_group = mb_strtolower( get_request('group') );
		$tag_subgroup = mb_strtolower( get_request('subgroup') );
		
		$filter_content_type = mb_strtolower( get_request('content_type') );
		
		$search_open_tags = mb_strtolower( get_request('open_tags') );
		
		$filter = get_request('filter');
		$filter_tags = get_ids( get_request('tags') );
		
		$order = mb_strtolower( get_request('order') );
		
		$image_type = (int)get_request('image_type');
		
		//default	
		$order_by = " ORDER BY is_pin DESC, date DESC ";
		
		if( $order=="downloaded" ) $order_by = " ORDER BY is_pin DESC, downloaded DESC, date DESC ";
		else if( $order=="viewed" ) $order_by = " ORDER BY is_pin DESC, viewed_count DESC, date DESC ";
		
		/*
		$group_tags = array_column($db->query( "SELECT * 
										FROM tags 
										WHERE status=1 and `group`<>'' " ), 'id');
		*/
		
		$group_tags = array_column($db->query( "SELECT * 
										FROM tags 
										WHERE status=1 and `tag_group`>0 " ), 'id');
										
		/*if($group){
						
			$group_arr = array_column($db->query( "SELECT * 
										FROM tags 
										WHERE status=1 and `group` = '$group'" ), 'id');

			if($group_arr) $filter_tags = $group_arr;
			else $filter_tags[] = "vazio";
			
		}*/
		
		if($tag_group){
						
			//if($tag_subgroup)			
			$w_subgroup = ($tag_subgroup)? " and tag_subgroup=$tag_subgroup " : "";
			$group_arr = array_column($db->query( "SELECT * 
										FROM tags 
										WHERE status=1 and `tag_group` = $tag_group $w_subgroup" ), 'id');

			if($group_arr) $filter_tags = $group_arr;
			else $filter_tags[] = "vazio";
			
		}
		
		$filter_vehicles = get_ids( get_request('vehicles') );
		$filter_type = '';
		
		$feed_type = get_request('type');
		
		$force_favorites = has_request('favorites');
		$force_read_later = has_request('read_later');
		$force_highlights = has_request('highlights');
		$force_notifications = has_request('notifications');
		
		$folhetos_tag_id = (int)$db->get_metadata_value( "folhetos_tag" );
		
		$is_app = has_request('is_app');
		
		$feed = [];
		$tags = [];
		
		$tags_lib = [];
		
		$post_where = '';
		$survey_where = '';
		
		if (!empty($filter)) {
			
			if ($filter == 'redes') {
				
				$post_where .= 'AND social_type > 0';
				
			} elseif ($filter == 'comunicados') {
				
				$post_where .= 'AND category = 2';
				
				
			} elseif ($filter == 'universo') {
				
				$post_where .= 'AND category = 12';	
			
			} elseif ($filter == 'careservices') {
				
				$post_where .= 'AND category = 13';	
						
			} elseif ($filter == 'academy') {
				
				$post_where .= 'AND category = 14';	
						
			} elseif ($filter == 'noticias') {
				
				$post_where .= 'AND category = 3';
				
			} elseif ($filter == 'galeria') {
				
				$post_where .= 'AND category = 5';
				
			} elseif ($filter == 'folhetos') {
				
				$post_where .= 'AND category = 5';
				
				$filter_tags = get_ids( $folhetos_tag_id );
			
			} elseif ($filter == 'campanha') {
				
				$post_where .= 'AND category = 6';
			
			} elseif ($filter == 'enquete') {
				
				$post_where .= 'AND category = 7';
							
			} elseif ($filter == 'novidades' && !$force_highlights) {
				
				// posts
				
				$post_where .= 'AND category not in ( 5 )';
				
			} elseif ($filter == 'compartilhar' ) {
				
				// posts
				
				//$post_where .= 'AND category not in ( 5 )';
				/*
				$filter_tags = array_column($db->query( 
						"SELECT id
							FROM `tags` where status=1 and 
								`group`<>'' and `group`<>'institutional_contents' "), 'id');*/
				//todo criar tag_group institutional_contents	e fixar aki			
				
				/*$group_name_is = current(array_column( $db->query( "select name from tag_groups
																		where id = (SELECT tag_group FROM `tags` where tags.id=$group_is);"), 'name' ));*/
				$filter_tags = array_column($db->query( 
						"SELECT id
							FROM `tags` where status=1 and 
								`tag_group` > 0 and  tag_group not in (SELECT tag_groups.id FROM `tag_groups` where tag_groups.name='guias institucionais') "), 'id');
								
				//print_r($filter_tags); die();
				
			} elseif ($filter == 'itens_salvos') {
				
				// posts
				
				//$post_where .= 'AND category = 7';
				
			}
			
		}
		
		if( $version < 4 ){
			
			$post_where .= ' AND category not in ( 13 ) ';
			
		}
		
		if( $version < 3 ){
			
			$post_where .= ' AND category not in ( 12 ) ';
			
		}
		
		if($image_type){
			
			$post_where .= " AND image_type = $image_type ";
			
		}


		if ( $vehicleversions ) {
				
			// posts
			$post_where .= " AND version<>''";
			
		}
		
		if ( $vehicleversion ) {
				
			// posts
			$post_where .= " AND version = '$vehicleversion' ";
			
		}
		
		
		
		if( $filter_content_type ){
			
			//$cond_content_type = "";
			//if( $filter_content_type == 'image' ) $cond_content_type = "" 
			
			$post_where .= " AND posts.image in (select uploads.id from uploads where uploads.id = posts.image and type like '%{$filter_content_type}%' ) ";
			
		}

		if ($force_highlights) {
			
			$post_where .= ' AND is_storie = 1 AND posts.date >= DATE(NOW()) - INTERVAL 14 DAY';
			
		}
		
		if ($force_notifications) {
			//print_r($user);die(); 
			$post_where .= " AND posts.date > '{$user['last_view']}' ";
			$post_where .= " AND category != 1 ";
			
		}
		
		if( $version <> 5 ){
			
			$post_where .= ' AND posts.id <> 4550';
		}
		
		$search_id = 0;
		if ($search) {
				
			if (strpos($search, 'id:') === 0) {
				
				$post_id = (int)substr($search, 3);
				$search_id = $post_id;
				
				$post_where .= " AND posts.id = {$post_id} ";
				$survey_where = " AND id = {$post_id} ";
				
			} else {
				
				$search_text = trim( preg_replace("/#(\\w+)/", '', $search) );
				
				preg_match_all("/#(\\w+)/", $search, $hashs);

				if ( count( $hashs ) > 1 ) {
					
					$hashs = $hashs[1];
					
					$hashs_where = [];
					
					foreach($hashs as &$hash) {
						
						if ($hash == 'comunicado') {
							
							$hashs_where[] = 'category = 2';
							
						} elseif ($hash == 'campanha') {
						
							$hashs_where[] = 'category = 6';
						
						} elseif ($hash == 'noticia') {
						
							$hashs_where[] = 'category = 3';
						
						} elseif ($hash == 'twitter') {
							
							$hashs_where[] = 'social_type = 1';
							
						} elseif ($hash == 'youtube') {
							
							$hashs_where[] = 'social_type = 2';
							
						} elseif ($hash == 'instagram') {
							
							$hashs_where[] = 'social_type = 3';
							
						} elseif ($hash == 'facebook') {
							
							$hashs_where[] = 'social_type = 4';
							
						} elseif ($hash == 'card') {
							
							$filter_type = 'image';
							
						} elseif ($hash == 'video') {
							
							$filter_type = 'video';
							
						} elseif ($hash == 'folheto') {
							
							$filter_type = 'pdf';
							
						} elseif ($hash == 'noticiasegmento') {
							
							$hashs_where[] = 'category = 3 AND label = "Segmento"';
							
						} elseif ($hash == 'noticiarenault') {
							
							$hashs_where[] = 'category = 3 AND label = "Renault"';
							
						}

					}
					
					if (count($hashs_where) > 0) {
						
						$post_where .= ' AND ( ' . implode(' OR ', $hashs_where) . ' ) ';
						
					}
					
				}
				
				if ($search_text) {
					
					$post_where .= " AND ( ( name LIKE '%{$search_text}%' ) OR ( text LIKE '%{$search_text}%' ) OR ( open_tags LIKE '%{$search_text}%' ) ) ";
					$survey_where .= " AND ( ( title LIKE '%{$search_text}%' ) OR ( text LIKE '%{$search_text}%' ) ) ";
					
				}

			}
			
		}
		
		if ($search_tags) {
			
			$tags_list = array_filter( explode(',', $search_tags) );
			/*$tags_where = [];
			
			foreach($tags_list as &$tag_id) {
				
				$tags_where[] = " ( tags = '{$tag_id}' ) ";
				
			}
			
			$post_where .= ' AND ( ' . implode( 'OR ', $tags_where ) . ' ) ';*/
			
		}

		if ($search_open_tags) {
			
			$post_where .= " AND ( open_tags LIKE '%{$search_open_tags}%' ) ";
			
		}
		
		$post_id = (int)get_request('id');
		
		if ( $post_id > 0 ) {
			
			/*if($version){
				
				$post_where .= " AND id = {$post_id} ";
				
			}else{
				
				$post_where = " AND id = {$post_id} ";
				
			}
			$survey_where  .= " AND id = {$post_id} ";*/
			
			if($version && ($filter == 'enquete' || $filter == '' || $filter == 'itens_salvos')){
				
				$survey_where  .= " AND id = {$post_id} ";
				
			}
			
			$post_where .= " AND id = {$post_id} ";
			
		}
		
		if (has_request('page')) {
			
			$page = (int)get_request('page');
			$page_limit = (int)get_request('page_limit');
			
			if($page_limit<=0) $page_limit=10;
			
			/*
			$count = 20;
			$start = $page * $count;
		
			$limit = "{$start}, {$count}";
			*/
			$limit = "2000";
		} else {
			
			if($force_notifications){
			
				$limit = "9";
			
			} else {
				
					$limit = "2000";
				
			}
			
		}
		$left_join="";
		if ($user && $force_favorites) {
				
			//$post_where .= " AND id in (SELECT favorites.post FROM favorites where favorites.user={$user['id']}) ";
			$left_join .= "left join favorites on favorites.user={$user['id']} and favorites.post = posts.id";	
			$post_where .= " and favorites.id > 0 ";
		}
		
		$surveys = "";
		$survey_field = "";
		
		if($version && ($filter == 'enquete' || $filter == 'novidades' || $filter == 'itens_salvos' )){
			
			$survey_fix = ($version >1)? "surveys.is_pin as is_pin," : "0 as is_pin,";
			
			$surveys = "union 
							select 
								{$survey_fix}
								'' as color,
								surveys.id as id, surveys.title as name, '' as cta, 0 as image_type, surveys.text as text, '' as link, surveys.date as date, 0 as expiration_date,7 as category, '' as tags,
								'' as vehicles, '' as topics, '' as open_tags, '' as search_news, '' as search_galeries, '' as image, '' as image_highlight, ''  as text_highlight,
								'' as material_link, '' as social_type, '' as label, 0 as file, 0 as tell_client, 0 favorite, 
								
								COALESCE((SELECT score FROM ranking where ranking.user={$user_id} and ranking.post= surveys.id and ranking.category=7), 0) user_score,
								COALESCE((SELECT 1 FROM viewed where viewed.user={$user_id} and viewed.post= surveys.id and viewed.category=7 LIMIT 1), 0 ) viewed,
								COALESCE((SELECT count(*) FROM viewed where viewed.post= surveys.id and viewed.category=7 LIMIT 1), 0 ) viewed_count,
								COALESCE((SELECT count(*) FROM downloaded where downloaded.post= surveys.id and downloaded.category=7 LIMIT 1), 0 ) downloaded, 
								COALESCE((SELECT AVG(ranking.score) as score FROM ranking WHERE ranking.post = surveys.id and ranking.category=7), 0 ) score, 
								COALESCE((SELECT COUNT(ranking.score) as count FROM ranking WHERE ranking.post = surveys.id and ranking.category=7), 0 ) count,
								COALESCE(( SELECT 1 FROM read_later WHERE read_later.user = {$user_id} AND read_later.post = surveys.id and read_later.value=1 and read_later.category=7 ), 0 ) read_later,   
								0 notification, 
								0 version,
								survey_answers  
							from surveys
							where status=1 {$survey_where} ";
							
			$survey_field = ", 0 as survey_answers ";
			
		}
	/*
	echo "
			SELECT 
					0 as is_pin,
					posts.color as color,
					posts.id as id,
					posts.name as name,
					posts.text as text,
					posts.link as link,
					posts.date as date,
					posts.category as category,
					posts.tags as tags,
					posts.vehicles as vehicles,
					posts.topics as topics,
					posts.open_tags as open_tags,
					posts.search_news as search_news,
					posts.search_galeries as search_galeries,
					posts.image as image,
					posts.image_highlight as image_highlight,
					posts.material_link as material_link,
					posts.social_type as social_type,
					posts.label as label,
					posts.file as file,
					posts.tell_client as tell_client,
			COALESCE((SELECT 1 FROM favorites where favorites.user={$user_id} and favorites.post= posts.id), 0) favorite,
			COALESCE((SELECT score FROM ranking where ranking.user={$user_id} and ranking.post= posts.id and ranking.category=0), 0) user_score,
			COALESCE((SELECT 1 FROM viewed where viewed.user={$user_id} and viewed.post= posts.id and viewed.category=0 LIMIT 1), 0 ) viewed,
			COALESCE((SELECT AVG(ranking.score) as score FROM ranking WHERE ranking.post = posts.id and ranking.category=0), 0 ) score,
			COALESCE((SELECT COUNT(ranking.score) as count FROM ranking WHERE ranking.post = posts.id and ranking.category=0), 0 ) count,
			COALESCE((SELECT 1 FROM read_later WHERE read_later.user = {$user_id} AND read_later.post = posts.id and read_later.value=1  and read_later.category=0), 0 ) read_later, 
			COALESCE((SELECT 1 FROM notifications WHERE notifications.user = {$user_id} AND notifications.post = posts.id and posts.category = 6), 0 ) notification,
			posts.version as version
			{$survey_field}   			
			FROM posts 
			{$left_join} 
			WHERE posts.status = 1 {$post_where}  
			{$surveys } 
			ORDER BY is_pin DESC, date DESC 
			LIMIT {$limit}"  ; die();*/
			
		$posts = $db->query( "
			SELECT 
					0 as is_pin,
					posts.color as color,
					posts.id as id,
					posts.name as name,
					posts.cta as cta,
					posts.image_type as image_type,
					posts.text as text,
					posts.link as link,
					posts.date as date,
					posts.expiration_date as expiration_date,
					posts.category as category,
					posts.tags as tags,
					posts.vehicles as vehicles,
					posts.topics as topics,
					posts.open_tags as open_tags,
					posts.search_news as search_news,
					posts.search_galeries as search_galeries,
					posts.image as image,
					posts.image_highlight as image_highlight,
					posts.text_highlight  as text_highlight,
					posts.material_link as material_link,
					posts.social_type as social_type,
					posts.label as label,
					posts.file as file,
					posts.tell_client as tell_client,
			COALESCE((SELECT 1 FROM favorites where favorites.user={$user_id} and favorites.post= posts.id), 0) favorite,
			COALESCE((SELECT score FROM ranking where ranking.user={$user_id} and ranking.post= posts.id and ranking.category=0), 0) user_score,
			COALESCE((SELECT 1 FROM viewed where viewed.user={$user_id} and viewed.post= posts.id and viewed.category=0 LIMIT 1), 0 ) viewed,
			COALESCE((SELECT count(*) FROM viewed where viewed.post= posts.id and viewed.category=0 LIMIT 1), 0 ) viewed_count,
			COALESCE((SELECT count(*) FROM downloaded where downloaded.post= posts.id and downloaded.category=0 LIMIT 1), 0 ) downloaded,
			COALESCE((SELECT AVG(ranking.score) as score FROM ranking WHERE ranking.post = posts.id and ranking.category=0), 0 ) score,
			COALESCE((SELECT COUNT(ranking.score) as count FROM ranking WHERE ranking.post = posts.id and ranking.category=0), 0 ) count,
			COALESCE((SELECT 1 FROM read_later WHERE read_later.user = {$user_id} AND read_later.post = posts.id and read_later.value=1  and read_later.category=0), 0 ) read_later, 
			COALESCE((SELECT 1 FROM notifications WHERE notifications.user = {$user_id} AND notifications.post = posts.id and posts.category = 6), 0 ) notification,
			posts.version as version
			{$survey_field}   			
			FROM posts 
			{$left_join} 
			WHERE posts.status = 1 and (expiration_date = 0 or expiration_date is null or expiration_date > NOW()) {$post_where}  
			{$surveys } 
			{$order_by } 
			LIMIT {$limit}" );
	
		//versoes dos veiculos
		$versions = [];
		
		foreach($posts as &$post) {
			
			//limitando quantidade de itens retornados
			if( $feed_limit> 0 and count($feed) >= $feed_limit) break;
			
			$post = $db->content( $post, 'posts' );
			
			//print_r($post);
			/*
			$score = current( $db->query( "SELECT AVG(score) as score, COUNT(score) as count FROM ranking WHERE post = :post", array(
				'post' => $post['id']
			) ) );*/
			
			$item = [];
			$item['title'] = $post['name'];	
			$item['cta'] = $post['cta']? $post['cta'] : "Conheça todos os detalhes";		
			$item['image_type'] = $post['image_type'];	
			$item['message'] = $post['text'];
			if( strip_tags($post['text_highlight']) && trim($post['text_highlight']) )$item['message_highlight'] = $post['text_highlight'];
		
			$item['id'] = (int)$post['id'];
			$item['feed_id'] = count($feed);
			$item['link'] = $post['link'];
			$item['date'] = str_replace( '-', '/', $post['date'] );
			//$item['expiration_date'] = $post['expiration_date']? str_replace( '-', '/', $post['expiration_date'] ) : "";
			$item['expiration_date'] = ($post['expiration_date'] === '0000-00-00 00:00:00' || empty($post['expiration_date']))? "" : $post['expiration_date'];
		
			$item['category_id'] = (int)$post['category'];
			$item['favorite'] = false;
			//$item['score'] = (float)$score['score'];
			$item['score'] = (float)$post['score'];
			//$item['score_count'] = (int)$score['count'];
			$item['score_count'] = (int)$post['count'];
			$item['tags'] = get_ids( $post['tags'] );
			$item['vehicles'] = get_ids( $post['vehicles'] );
			$item['topics'] = get_ids( $post['topics'] );
			$item['open_tags'] = array_filter( explode(',', $post['open_tags'] ) );
			$item['viewed'] = false;
			$item['embed'] = false;
			//if(isset($post['survey_answers'])) $item['survey_answers'] = get_ids( $post['survey_answers'] );
			$item['filter_tags'] = [];
			$item['version'] = (int)$post['version'];
			
			if( $post['is_pin']> 0 ) $item['is_pin'] = (int)$post['is_pin'];
			if( $post['color'] <> '' ) $item['color'] = $post['color'];
			
			if ($post['search_news']) $item['search_news'] = $post['search_news'];
			if ($post['search_galeries']) $item['search_galeries'] = $post['search_galeries'];

			if (count($item['topics']) > 0) {
				
				$topics = [];
				
				foreach($item['topics'] as &$topic_id) {
					
					$topic = $db->select_id( 'post_topics', $topic_id );
					
					if ($topic['status'] == 1) {
					
						$topics[] = array(
							'title' => $topic['name'],
							'text' => $topic['text']
						);
						
					}
					
				}
				
				$item['topics'] = $topics;
				
			}
			
			// filters
			
			if ( count($filter_tags) > 0 ) {
				
	//print_r($item['tags']);
				if( !contains_tags($filter_tags, $item['tags']) ) {
					
					continue;
					
				}else{
					//echo "baba";
					//print_r($filter_tags); 
				//print_r($item);
				
					$tags_filtradas = array_intersect($filter_tags, $item['tags']);
					//print_r($tags_filtradas);
				//die();
					$item['tags'] = $tags_filtradas;
					$post['tags'] = implode($tags_filtradas);
					
				}
				
			} else {
				if($filter != "itens_salvos" && (!$search_open_tags)){
						
					//echo "tags =". $post['tags'];
					/*$tags_in_group = array_column($db->query( 
							"SELECT id
								FROM `tags` where 
								`group` <> '' and 
								FIND_IN_SET( `id`, :tagsIds )>0; ", array(
									'tagsIds' => $post['tags']
							) ), 'id');*/
					$tags_in_group = array_column($db->query( 
							"SELECT id
								FROM `tags` where 
								`tag_group` > 0 and 
								FIND_IN_SET( `id`, :tagsIds )>0; ", array(
									'tagsIds' => $post['tags']
							) ), 'id');
							//echo "--|";
						//print_r($tags_in_group);echo "|--";
					if( $tags_in_group  ) {

						if( !$search_id  )continue;	
						
					}
					
				}
				
			}
			
			if ( count($filter_vehicles) > 0 ) {
				
				if( !contains_tags($filter_vehicles, $item['vehicles']) ) {
					
					continue;
					
				}
				
			}
			
			// visual file
			
			$content_type = get_content_format( $post['image_url'] );
			
			if ( $content_type == 'video' ) {
			
				$item['filter_tags'][] = 'video';
				
			}
			
			if ($content_type) {
				
				$item['content_type'] = $content_type;
				$item['content'] = url( $post['image_url'] );
				// get_content_url
				//$item['content'] = HOST."/content.php?id=".$post['image'];
				
			} else {
				
				$item['alt_content_type'] = 'image';
				$item['alt_content'] = url( 'images/notif.jpg' );
				
			}
			
			if ($filter_type == 'image' || $filter_type == 'video') {
				
				if ($filter_type != $content_type) {
					
					continue;
					
				}
				
			}

			// thumbnails
			
			if ($content_type == 'image' || $content_type == 'video') {
				
				if ($force_highlights) {
					
					if($post['image_highlight'] > 0){
						
						$item['thumbnail'] = url( get_content_url( 'highlight-thumbnail', $post['image_highlight'] ) );
					
					} else {
						
						$item['thumbnail'] = url( get_content_url( 'highlight-thumbnail', $post['image'] ) );
						
					}
					
					if( $post['text_highlight'] <> '' ){
						
						$item['text'] = $post['text_highlight'];
					
					} else {
						
						$item['text'] = $post['text'];
						
					}
					
					
				} elseif ($content_type == 'video') {
					
					if( $version >= 5 ){
					
						if($post['image_highlight'] > 0){
							
							if( $post['category'] == 6 ){
								
								$item['thumbnail'] = url( get_content_url( 'thumbnail', $post['image_highlight'] ) );
								
							}else{
								
								$item['thumbnail'] = url( get_content_url( 'highlight-thumbnail', $post['image_highlight'] ) );
								
							}
						
						} else {
							
							$item['thumbnail'] = url( get_content_url( 'video', $post['image'] ) );
							
						}
						
					}else{
						
						$item['thumbnail'] = url( get_content_url( 'video', $post['image'] ) );
						
					}
					
				}
				
			}

			// download
			
			if ($post['category'] != 6 && isset($item['content'])) {
				
				//$item['download'] = $item['content']."&filename={$post['image']}.{$post['image_extension']}";
				//$item['download'] = get_content_url( 'upload', $post['image'] );
				$item['download'] = url( get_content_url( '', $post['image'], '' ) . "&filename={$post['image']}.{$post['image_extension']}" );
			
			}
			if ($post['category'] == 6) { 
			
				$item['material_link'] = $post['material_link'];
			
			}

			$archive_type = get_content_format( $post['file_url'] );
			
			if ( $archive_type == 'pdf' ) {
			
				$item['pdf'] = url( $post['file_url'] );
				//$item['download'] = $item['pdf'];
				$item['download'] = url( get_content_url( '', $post['file'], '' ) . "&filename={$post['file']}.{$post['file_extension']}" );	
				$item['pdf_download'] = url( get_content_url( '', $post['file'], '' ) . "&filename={$post['file']}.{$post['file_extension']}" );
				
			}
			
			if ($filter_type == 'pdf' && $archive_type != 'pdf') {
				
				continue;
				
			}
			
			if ($post['material_link']) {
				
				$item['download'] = $post['material_link'];
				
			}
			
			// nao utilizar download em noticias
			if ($force_highlights && $post['category'] == 3) {
				
				unset( $item['download'] );
				
			}
			
			if( $version > 1){
				
				if ( $post['tell_client'] > 0 ) {
					//print_r($item); die();
					$item['tell_client_type'] = get_content_format( $post['tell_client_url'] );
					$item['tell_client'] = url( $post['tell_client_url'] );
					
					$item['tell_client_download'] = url( get_content_url( '', $post['tell_client'], '' ) . "&filename={$post['tell_client']}.{$post['tell_client_extension']}" );
					$item['download'] = url( get_content_url( '', $post['tell_client'], '' ) . "&filename={$post['tell_client']}.{$post['tell_client_extension']}" );
						
				}else{
					
					if ( $post['category'] == 3 ) {
						
						if( $post['file'] > 0 ){
							
							$item['tell_client_type'] = get_content_format( $post['file_url'] );
							$item['tell_client'] = url( $post['file_url'] );
							$item['download'] = url( get_content_url( '', $post['file'], '' ) . "&filename={$post['file']}.{$post['file_extension']}" );
							$item['tell_client_download'] = $item['download'];
							
						}else{
						
							$item['download'] = "";
						
						}
					}
					
				}
				
			} else{
				
				if ( $post['category'] == 3 ) {
				
					if ( $post['file']>0 ){
						
						$item['tell_client_type'] = get_content_format( $post['file_url'] );
						$item['tell_client'] = url( $post['file_url'] );
						$item['download'] = url( get_content_url( '', $post['file'], '' ) . "&filename={$post['file']}.{$post['file_extension']}" );
						$item['tell_client_download'] = $item['download'];
						
					} else {
						
						$item['download'] = "";
						
					}
					
				}
					
			}
			
			
			
			
			
			
			// share
			
			$item['share_message'] = htmltotext( $post['text'] );
			
			if ( mb_strlen($item['share_message']) > 150 ) {
				
				$item['share_message'] = mb_substr($item['share_message'], 0, 150) . '...';
				
			}
			
			if ($post['category'] != 2) {
				
				if ($item['link']) {
					
					$item['share_link'] = $item['link'];

					parse_link( $item['link'], $item );

				} elseif ($content_type) {
					
					if($archive_type == 'pdf'){
						
						$item['share_link'] = $item['pdf'];
						
					}else{
					
						$item['share_link'] = $item['content'];
						
					}
					
				}
				
			}
			
			// get contains
			
			$contains_notification = $post['category'] == 6;
			
			if ( in_array( $folhetos_tag_id, $item['tags'] ) ) {
				
				$contains_read_later = true;
				
			} else {
			
				$contains_read_later = in_array( $post['category'], array( 1, 2, 3, 6, 7, 5, 12, 13 ) );
				
			}
			
			// youtube

			//preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $item['link'], $youtube_matche);
			preg_match("#(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]+)#", $item['link'], $youtube_matche);

			if (!empty($youtube_matche)) {
				
				$item['youtube'] = $youtube_matche[1];
				$item['filter_tags'][] = 'youtube';
				
			}
			
			// sino3d
			
			if ( strpos( $item['link'], 'https://www.sino3d.app/ar/' ) === 0 || strpos( $item['link'], 'https://www.novorenaultkwid.com.br/eletrico/' ) === 0 ||
			      strpos( $item['link'], 'https://localhost/' ) === 0 || strpos( $item['link'], 'https://localhost/' ) === 0 || 
				  strpos( $item['link'], 'https://www.novorenaultkwid.com.br/3d/' ) === 0 || strpos( $item['link'], 'https://novorenaultkwid.com.br/3d/' ) === 0 ||
				  strpos( $item['link'], 'https://sino3d.app/autoshowroom/kardian/' ) === 0 || strpos( $item['link'], 'https://www.sino3d.app/autoshowroom/kardian/' ) === 0 ||
				  strpos( $item['link'], 'https://sino3d.app/360/kardian/' ) === 0 || strpos( $item['link'], 'https://www.sino3d.app/360/kardian/' ) === 0) {
				
				$item['iframe'] = $item['link'];
				$item['filter_tags'][] = 'sino3d';
				
				if ($is_app) {
				
					continue;
					
				}
				
			}
			
			//--

			if ($contains_read_later) {
				
				$item['read_later'] = false;
				
			}
			
			if ($contains_notification) {
				
				$item['notification'] = false;
				
			}

			if ($post['social_type'] == 1) {
				
				$item['type'] = 'twitter';
				$item['filter_tags'][] = 'twitter';
				
			} elseif ($post['social_type'] == 2) {
				
				$item['type'] = 'youtube';
				$item['filter_tags'][] = 'youtube';
				
			} elseif ($post['social_type'] == 3) {
				
				$item['type'] = 'instagram';
				$item['filter_tags'][] = 'instagram';
				
			} elseif ($post['social_type'] == 4) {
				
				$item['type'] = 'facebook';
				$item['filter_tags'][] = 'facebook';
				
			} elseif ($post['category'] == 2) {
				
				$item['type'] = 'comunicados';
				$item['filter_tags'][] = 'comunicado';
				
			} elseif ($post['category'] == 3) {
				
				$item['type'] = 'noticias';
				$item['filter_tags'][] = 'noticia';
				
			} elseif ($post['category'] == 5) {
				
				$item['type'] = 'galeria';
				$item['filter_tags'][] = 'galeria';
				
			} elseif ($post['category'] == 6) {
				
				$item['type'] = 'campanha';
				$item['filter_tags'][] = 'campanha';
				
			} elseif ($post['category'] == 7) {
				
				$item['type'] = 'enquete';
				$item['filter_tags'][] = 'enquete';
				
			} elseif ($post['category'] == 12) {
				
				$item['type'] = 'universo';
				$item['filter_tags'][] = 'universo';
				
			} elseif ($post['category'] == 13) {
				
				$item['type'] = 'careservices';
				$item['filter_tags'][] = 'careservices';
				
			} elseif ($post['category'] == 14) {
				
				$item['type'] = 'academy';
				$item['filter_tags'][] = 'academy';
				
			}

			// customs
			
			if ($post['category'] == 6) {
				
				// campanha
				
				$item['link_campaign'] = $item['id'];
				
			} elseif ($post['category'] == 2 || $post['category'] == 3 || $post['category'] == 12) {
				
				// comunicados
				
				$item['label'] = $post['label'];
				
			}
			
			if ($user) {
				/*
				$user_score = current( $db->query( "SELECT * FROM ranking WHERE user = :user AND post = :post", array(
					'user' => $user['id'],
					'post' => $post['id']
				) ) );*/
				//$item['user_score'] = $user_score ? (int)$user_score['score'] : 0;
				$item['user_score'] =(int)$post['user_score'];
				$item['favorite'] = (bool)$post['favorite'];
				$item['viewed'] = (bool)$post['viewed'];
				$item['downloaded'] = $post['downloaded'];
				$item['viewed_count'] = $post['viewed_count'];
				
				
				if ($contains_read_later) {
					/*
					$read_later = current( $db->query( "SELECT * FROM read_later WHERE user = :user AND post = :post", array(
						'user' => $user['id'],
						'post' => $post['id']
					) ) );
					
					$item['read_later'] = $read_later ? $read_later['value'] == 1 : false;
					*/
					$item['read_later'] =(bool)$post['read_later'];
				}
				
				if ($contains_notification) {
					/*
					$notification = current( $db->query( "SELECT * FROM notifications WHERE user = :user AND post = :post", array(
						'user' => $user['id'],
						'post' => $post['id']
					) ) );
					
					$item['notification'] = $notification ? $notification['value'] == 1 : false;
					*/
					
					$item['notification'] = (bool)$post['notification'];
					
				}
				
				if ($force_notifications && $item['viewed']) {
					
					continue;
					
				}
				
			}
			
			if ($force_read_later && ( !isset($item['read_later']) || !$item['read_later'] )) {
				
				continue;
				
			} elseif ($force_favorites && !$item['favorite']) {
				
				continue;
				
			}
			
			// tags

			foreach($item['tags'] as &$tag_id) {
				
				$tag = $db->select_id( 'tags', $tag_id );
				//$tag = $db->content( $tag_id, 'tags' );
				
				if (!isset($tags_lib[ $tag_id ])) {
					
					$tags_lib[ $tag_id ] = array(
						'id' => (int)$tag['id'],
						'name' => $tag['name'],
						//'thumbnail' => $tag['thumbnail_url'],
						'thumbnail' => url( get_content_url( 'image', $tag['thumbnail'] ) ),
						'posts' => array()
					);
					
				}
				
				$tags_lib[ $tag_id ]['posts'][] = count($feed);
				
			}
			
			// survey_answers
			
			if ($post['category'] == 7) {

				$survey_answers_ids = get_ids( $post['survey_answers'] );
				
				$survey_id = $item['id'];
				
				$survey_total = (int)current(array_filter(array_column($db->query( "SELECT COUNT(*) cont FROM survey_user_answers WHERE survey = :survey ", array(
					'survey' => (int)$survey_id
				) ), 'cont')));
				
				$item['total'] = $survey_total;
				
				$vote =0;
				
				if(isset($user['id'])){
				
					$vote = (int)
						current(array_filter(array_column(
							$db->query( "SELECT max(survey_answer) vote 
										 FROM survey_user_answers 
										 WHERE survey = :survey and user = :user and 
												id = (SELECT max(a.id) vote FROM survey_user_answers a WHERE a.survey = :survey and a.user = :user)", array(
											'survey' => (int)$survey_id,
											'user' => $user['id']
										) ), 'vote')));
					
					$item['vote'] = $vote;
					
				}
				
				$is_closed = (bool)
						current(array_filter(array_column(
							$db->query( "SELECT 1 is_closed FROM `surveys` where id=:id and closing_date<CURDATE();", array(
											'id' => (int)$survey_id
										) ), 'is_closed')));
				$item['is_closed'] = $is_closed;
				
				$survey_answers_lib = array();
				$item['survey_answers']  = array();
				foreach( $survey_answers_ids as &$survey_answer_id) {
					
					$survey_answer = $db->select_id( 'survey_answers', $survey_answer_id );
					
					$percentage = 0;
					$count = 0;
					
					if($survey_total>0){
						
						$count = (int)current(array_filter(array_column($db->query( "SELECT COUNT(*) cont FROM survey_user_answers WHERE survey = :survey and survey_answer = :survey_answer ", array(
							'survey' => (int)$survey_answer['survey'],
							'survey_answer' => (int)$survey_answer['id']
						) ), 'cont')));
						
						$percentage = round((((int)$count*100) / $survey_total), 2);
						
					}
						
					$survey_answers_lib[] = array(
						'id' => (int)$survey_answer['id'],
						'text' => $survey_answer['text'],
						'amount' => (int)$count,
						'percentage' => $percentage
					);
					
				}

				$item['survey_answers'] = $survey_answers_lib;
			
			}

			// links
			
			if ($post['category'] == 1) {
				
				$pattern = '@(http(s)?://)?(([a-zA-Z0-9])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';

				$item['message'] = preg_replace( 
					"/(?<!a href=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", 
					"<a href=\"\\0\" target=\"blank\">\\0</a>", 
					$item['message']
				);
				
			}

			// ver mais
			
			if ($feed_type == 'posts' || $force_highlights) {
				
				$vermais_str = "<p><br></p><p><br></p>";
				$vermais_pos = strpos($item['message'], $vermais_str);
				
				if ( $vermais_pos !== false ) {
					
					if ($force_highlights) {
						
						$item['message'] = substr_replace($item['message'], "<br><div class='vermais-mostrar'>", $vermais_pos, strlen($vermais_str)) . '</div>';
						
					} else {
					
						$item['message'] = substr_replace($item['message'], "<br><div class='vermais'>Ver mais</div><div class='vermais-mostrar'>", $vermais_pos, strlen($vermais_str)) . '</div>';
						
					}

				}
				
			}
			
			//print_r($feed);			
			if( $vehicleversions || $vehicleversion ){
				
				
				$vehicle_versions_db = current(
						$db->query( "SELECT  max(id) as max_id 
							from vehicle_version
							where 
								version = {$post['version']} and FIND_IN_SET( vehicle_version.vehicle, {$post['vehicles']} )>0 and 
								status=1 "));
				
					//if( !in_array( $post['version'], $versions) ){
					
				if ($vehicle_versions_db){

					if(!in_array( $post['version'], array_column($versions, 'version'))){
								 
						//$version_db = $db->content( $post['version'], 'versions' );
						$version_db = $db->content( $vehicle_versions_db['max_id'], 'vehicle_version' );
						
						$version_item = array();
						
						
						$version_item['id'] = (int)$version_db['id'];
						if($version_db['version_name'] == 'E-tech'){
							
							$version_item['title'] = "imagens " . $version_db['vehicle_name'];
						
						} else {
							
							$version_item['title'] = "imagens " . $version_db['vehicle_name'] . " " . $version_db['version_name'];
							
						}
							
						//$version_item['title'] = "imagens " . $version_db['vehicle_name'] . " " . $version_db['version_name'];
						$version_item['vehicle'] = (int)$version_db['vehicle'];
						$version_item['vehicle_name'] = $version_db['vehicle_name'];
						$version_item['version']= (int)$version_db['version'];
						$version_item['version_name'] = $version_db['version_name'];
						$version_item['link'] = $version_db['link'];
						$version_item['image_url'] = url( $version_db['image_url'] ); //$version_db['image']
								
						/*$version_item['id'] = (int)$version_db['id'];
						$version_item['name'] = $version_db['name'];
						$version_item['link'] = $version_db['link'];*/

						$version_item['posts'][] = count($feed);

						array_push ($versions,  $version_item);
//print_r($version_item);
					} else {
						
						if( $vehicleversions ){ 
							
							continue;
							
						}else{
							
							array_push ( $versions[0]['posts'],  count($feed) );
//print_r($version_item);							
						}
						
					}
				
				} else {
					
					continue;
					
				}

			}
			
			if( $filter == "itens_salvos"){
			
				$arr_tags_gps = array_intersect($item['tags'], $group_tags);
				
				if( count($arr_tags_gps)>0 ){

					$group_is = reset($arr_tags_gps);
				
					//$group_name_is = current(array_column( $db->query( " SELECT * FROM tags where id = $group_is"), 'group' ));
					$group_name_is = current(array_column( $db->query( "select name from tag_groups
																		where id = (SELECT tag_group FROM `tags` where tags.id=$group_is);"), 'name' ));

					if( $group_name_is == 'images' )  $group_name_is ='imagens dos veículos';	
					if( $group_name_is == 'compact_catalogs' )  $group_name_is ='catálogos compactos';
					if( $group_name_is == 'cards_attributes' )  $group_name_is ='cards atributos';
					if( $group_name_is == 'competition_comparatives' )  $group_name_is ='comparativos concorrência';
					if( $group_name_is == 'comparative_versions' )  $group_name_is ='comparativo versões';
					if( $group_name_is == 'renault_on_demand' )  $group_name_is ='Renault On Demand';
				
					$vehicle_name_is='';
				
					if($item['vehicles']){
						
						$vehicle_is = $item['vehicles'][0];
						
						$vehicle_name_is = current(array_column( $db->query( " SELECT `name` FROM `vehicles` where id = $vehicle_is"), 'name' ));
					
					}
					
					$version_name_is ='';
					
					if($item['version']){
						
						$version_is = $item['version'];
						
						$version_name_is = current(array_column( $db->query( " SELECT `name` FROM `versions` where id = $version_is"), 'name' ));
						
					}
					if($version_name_is == 'E-tech') $version_name_is="";
				
					$item['title'] = $vehicle_name_is . " " . $version_name_is;
					$item['share_type'] = $group_name_is;
					$item['filter_tags'] = array('compartilhar');
					
					
				}else{
					
					if($post['category'] == 5 ) continue;
					
				}
				
			}
			

			//-

			$feed[] = $item;
			
		}
//die();
		foreach($tags_lib as &$tag) {
				
			$tags[] = $tag;
			
		}

		
		if ($filter == 'galeria') {
			
			$whatsapp_gallery_tags = get_ids( $db->get_metadata_value( "whatsapp_gallery_tags" ) );
			
			usort($tags, function($a, $b)
			{
				global $whatsapp_gallery_tags;
				
				return array_search($a['id'], $whatsapp_gallery_tags) > array_search($b['id'], $whatsapp_gallery_tags);
			});

		}
		
		if ($filter == 'campanha') {
			
			$campaign_tags = get_ids( $db->get_metadata_value( "campaign_tags" ) );
			
			usort($tags, function($a, $b)
			{
				global $campaign_tags;
				
				return array_search($a['id'], $campaign_tags) > array_search($b['id'], $campaign_tags);
			});

		}
		
		//print_r($whatsapp_gallery_tags); die();
		//print_r($compartilahr_ids ); die();
		if ($filter == 'compartilhar') {
			
			//$whatsapp_gallery_tags = get_ids( $db->get_metadata_value( "whatsapp_gallery_tags" ) );
			/*
			usort($tags, function($a, $b)
			{
				global $compartilahr_ids;
				
				return array_search($a['id'], $compartilahr_ids) > array_search($b['id'], $compartilahr_ids);
			});*/

		}
		//print_r($compartilahr_ids ); die();
		
		
		if ( $trending ) {
			
			usort($feed, function($a, $b)
			{
				
				if ($a["score_count"] == $b["score_count"]) {
					return 0;
				}
				
				return ($a["score_count"] < $b["score_count"]) ? 1 : -1;
				
			});

		}
		//print_r($item['tags']); die();
	//echo "fim"; die();	
	
		if(  has_request('page') && count($feed) > 0 ){
			
			$intervalos = array_chunk($feed, $page_limit);
			$json['feed'] = (isset($intervalos[$page]))? $intervalos[$page] : [];
			
		}else{
			
			$json['feed'] = $feed;
			
		}
		//$json['feed'] = $feed;
		$json['tags'] = $tags;
		if($versions) $json['vehicleversions'] = $versions;

		
		break;

		case 'old_feed':
		
		// feed
		
		$search = mb_strtolower( get_request('search') );
		$search_tags = mb_strtolower( get_request('tags') );
		$search_open_tags = mb_strtolower( get_request('open_tags') );
		
		$filter = get_request('filter');
		$filter_tags = get_ids( get_request('tags') );
		$filter_vehicles = get_ids( get_request('vehicles') );
		$filter_type = '';
		
		$feed_type = get_request('type');
		
		$force_favorites = has_request('favorites');
		$force_read_later = has_request('read_later');
		$force_highlights = has_request('highlights');
		$force_notifications = has_request('notifications');
		
		$folhetos_tag_id = (int)$db->get_metadata_value( "folhetos_tag" );
		
		$is_app = has_request('is_app');
		
		$feed = [];
		$tags = [];
		
		$tags_lib = [];
		
		$post_where = '';
		
		if ($filter == 'redes') {
			
			$post_where .= 'AND social_type > 0';
			
		} elseif ($filter == 'comunicados') {
			
			$post_where .= 'AND category = 2';
			
		} elseif ($filter == 'noticias') {
			
			$post_where .= 'AND category = 3';
			
		} elseif ($filter == 'galeria') {
			
			$post_where .= 'AND category = 5';
			
		} elseif ($filter == 'folhetos') {
			
			$post_where .= 'AND category = 5';
			
			$filter_tags = get_ids( $folhetos_tag_id );
		
		} elseif ($filter == 'campanha') {
			
			$post_where .= 'AND category = 6';
			
		} elseif ($filter == 'novidades' && !$force_highlights) {
			
			// posts
			
			$post_where .= 'AND category not in ( 5 )';
			
		}

		if ($force_highlights) {
			
			//$post_where .= ' AND is_storie = 1 AND date >= DATE(NOW()) - INTERVAL 14 DAY';
			$post_where .= ' AND is_storie = 1 AND date >= DATE(NOW()) - INTERVAL 180 DAY';
			
		}
		
		if ($force_notifications) {
			
			$post_where .= " AND date > '{$user['last_view']}' ";
			$post_where .= " AND category != 1 ";
			
		}
		
		if ($search) {
				
			if (strpos($search, 'id:') === 0) {
				
				$post_id = (int)substr($search, 3);
				
				$post_where .= " AND id = {$post_id} ";
				
				if($filter != 'enquete') $post_where .= " AND category < 7 ";
				else  $post_where .= " AND category = 7 ";
				
				
			} else {
				
				$search_text = trim( preg_replace("/#(\\w+)/", '', $search) );
				
				preg_match_all("/#(\\w+)/", $search, $hashs);

				if ( count( $hashs ) > 1 ) {
					
					$hashs = $hashs[1];
					
					$hashs_where = [];
					
					foreach($hashs as &$hash) {
						
						if ($hash == 'comunicado') {
							
							$hashs_where[] = 'category = 2';
							
						} elseif ($hash == 'campanha') {
						
							$hashs_where[] = 'category = 6';
						
						} elseif ($hash == 'noticia') {
						
							$hashs_where[] = 'category = 3';
						
						} elseif ($hash == 'twitter') {
							
							$hashs_where[] = 'social_type = 1';
							
						} elseif ($hash == 'youtube') {
							
							$hashs_where[] = 'social_type = 2';
							
						} elseif ($hash == 'instagram') {
							
							$hashs_where[] = 'social_type = 3';
							
						} elseif ($hash == 'facebook') {
							
							$hashs_where[] = 'social_type = 4';
							
						} elseif ($hash == 'card') {
							
							$filter_type = 'image';
							
						} elseif ($hash == 'video') {
							
							$filter_type = 'video';
							
						} elseif ($hash == 'folheto') {
							
							$filter_type = 'pdf';
							
						} elseif ($hash == 'noticiasegmento') {
							
							$hashs_where[] = 'category = 3 AND label = "Segmento"';
							
						} elseif ($hash == 'noticiarenault') {
							
							$hashs_where[] = 'category = 3 AND label = "Renault"';
							
						}

					}
					
					if (count($hashs_where) > 0) {
						
						$post_where .= ' AND ( ' . implode(' OR ', $hashs_where) . ' ) ';
						
					}
					
				}
				
				if ($search_text) {
					
					$post_where .= " AND ( ( name LIKE '%{$search_text}%' ) OR ( text LIKE '%{$search_text}%' ) OR ( open_tags LIKE '%{$search_text}%' ) ) ";
					
				}

			}
			
		}
		
		if ($search_tags) {
			
			$tags_list = array_filter( explode(',', $search_tags) );
			$tags_where = [];
			
			foreach($tags_list as &$tag_id) {
				
				$tags_where[] = " ( tags = '{$tag_id}' ) ";
				
			}
			
			$post_where .= ' AND ( ' . implode( 'OR ', $tags_where ) . ' ) ';
			
		}
		
		if ($search_open_tags) {
			
			$post_where .= " AND ( open_tags LIKE '%{$search_open_tags}%' ) ";
			
		}
		
		$post_id = (int)get_request('id');
		
		if ( $post_id > 0 ) {
			
			$post_where = " AND id = {$post_id} ";
			
		}
		
		if (has_request('page')) {
			
			$page = (int)get_request('page');
			
			$count = $page_limit;
			$start = $page * $count;
		
			$limit = "{$start}, {$count}";
			
		} else {
			
			$limit = "200";
			
		}
		
		if ($user && $force_favorites) {
				
			$post_where .= " AND id in (SELECT favorites.post FROM favorites where favorites.user={$user['id']}) ";
				
		}
		
		$posts = $db->query( "SELECT * FROM posts WHERE status = 1 {$post_where} ORDER BY date DESC LIMIT {$limit}" );
		
		foreach($posts as &$post) {
			
			$post = $db->content( $post, 'posts' );
			
			$score = current( $db->query( "SELECT AVG(score) as score, COUNT(score) as count FROM ranking WHERE post = :post", array(
				'post' => $post['id']
			) ) );
			
			$item = [];
			$item['title'] = $post['name'];
			$item['message'] = $post['text'];
			$item['id'] = (int)$post['id'];
			$item['feed_id'] = count($feed);
			$item['link'] = $post['link'];
			$item['date'] = str_replace( '-', '/', $post['date'] );
			$item['category_id'] = (int)$post['category'];
			$item['favorite'] = false;
			$item['score'] = (float)$score['score'];
			$item['score_count'] = (int)$score['count'];
			$item['tags'] = get_ids( $post['tags'] );
			$item['vehicles'] = get_ids( $post['vehicles'] );
			$item['topics'] = get_ids( $post['topics'] );
			$item['open_tags'] = array_filter( explode(',', $post['open_tags'] ) );
			$item['viewed'] = false;
			$item['embed'] = false;
			$item['filter_tags'] = [];
			
			if ($post['search_news']) $item['search_news'] = $post['search_news'];
			if ($post['search_galeries']) $item['search_galeries'] = $post['search_galeries'];

			if (count($item['topics']) > 0) {
				
				$topics = [];
				
				foreach($item['topics'] as &$topic_id) {
					
					$topic = $db->select_id( 'post_topics', $topic_id );
					
					if ($topic['status'] == 1) {
					
						$topics[] = array(
							'title' => $topic['name'],
							'text' => $topic['text']
						);
						
					}
					
				}
				
				$item['topics'] = $topics;
				
			}
			
			// filters
			
			if ( count($filter_tags) > 0 ) {
				
				if( !contains_tags($filter_tags, $item['tags']) ) {
					
					continue;
					
				}
				
			}
			
			if ( count($filter_vehicles) > 0 ) {
				
				if( !contains_tags($filter_vehicles, $item['vehicles']) ) {
					
					continue;
					
				}
				
			}
			
			// visual file
			
			$content_type = get_content_format( $post['image_url'] );
			
			if ( $content_type == 'video' ) {
			
				$item['filter_tags'][] = 'video';
				
			}
			
			if ($content_type) {
				
				$item['content_type'] = $content_type;
				$item['content'] = url( $post['image_url'] );
				// get_content_url
				//$item['content'] = HOST."/content.php?id=".$post['image'];
				
			} else {
				
				$item['alt_content_type'] = 'image';
				$item['alt_content'] = url( 'images/notif.jpg' );
				
			}
			
			if ($filter_type == 'image' || $filter_type == 'video') {
				
				if ($filter_type != $content_type) {
					
					continue;
					
				}
				
			}

			// thumbnails
			
			if ($content_type == 'image' || $content_type == 'video') {
				
				if ($force_highlights) {
					
					if($post['image_highlight'] > 0){
						
						$item['thumbnail'] = url( get_content_url( 'highlight-thumbnail', $post['image_highlight'] ) );
					
					} else {
						
						$item['thumbnail'] = url( get_content_url( 'highlight-thumbnail', $post['image'] ) );
						
					}
					
					
				} elseif ($content_type == 'video') {
					
					$item['thumbnail'] = url( get_content_url( 'video', $post['image'] ) );
					
				}
				
			}

			// download
			
			if ($post['category'] != 6 && isset($item['content'])) {
				
				$item['download'] = $item['content'];
				
			}

			$archive_type = get_content_format( $post['file_url'] );
			
			if ( $archive_type == 'pdf' ) {
			
				$item['pdf'] = url( $post['file_url'] );
				$item['download'] = $item['pdf'];
				
			}
			
			if ($filter_type == 'pdf' && $archive_type != 'pdf') {
				
				continue;
				
			}
			
			if ($post['material_link']) {
				
				$item['download'] = $post['material_link'];
				
			}
			
			// nao utilizar download em noticias
			if ($force_highlights && $post['category'] == 3) {
				
				unset( $item['download'] );
				
			}
			
			// share
			
			$item['share_message'] = htmltotext( $post['text'] );
			
			if ( mb_strlen($item['share_message']) > 150 ) {
				
				$item['share_message'] = mb_substr($item['share_message'], 0, 150) . '...';
				
			}
			
			if ($post['category'] != 2) {
				
				if ($item['link']) {
					
					$item['share_link'] = $item['link'];

					parse_link( $item['link'], $item );

				} elseif ($content_type) {
					
					$item['share_link'] = $item['content'];
					
				}
				
			}
			
			// get contains
			
			$contains_notification = $post['category'] == 6;
			
			if ( in_array( $folhetos_tag_id, $item['tags'] ) ) {
				
				$contains_read_later = true;
				
			} else {
			
				$contains_read_later = in_array( $post['category'], array( 2, 3 ) );
				
			}
			
			// youtube

			preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $item['link'], $youtube_matche);

			if (!empty($youtube_matche)) {
				
				$item['youtube'] = $youtube_matche[0];
				$item['filter_tags'][] = 'youtube';
				
			}
			
			// sino3d
			
			if ( strpos( $item['link'], 'https://localhost/' ) === 0 || strpos( $item['link'], 'https://localhost/' ) === 0 || 
			strpos( $item['link'], 'https://www.novorenaultkwid.com.br/3d/' ) === 0 || strpos( $item['link'], 'https://novorenaultkwid.com.br/3d/' ) === 0 ||
			strpos( $item['link'], 'https://sino3d.app/autoshowroom/kardian/' ) === 0 || strpos( $item['link'], 'https://www.sino3d.app/autoshowroom/kardian/' ) === 0 
			) {
				
				$item['iframe'] = $item['link'];
				$item['filter_tags'][] = 'sino3d';
				
				if ($is_app) {
				
					continue;
					
				}
				
			}
			
			//--

			if ($contains_read_later) {
				
				$item['read_later'] = false;
				
			}
			
			if ($contains_notification) {
				
				$item['notification'] = false;
				
			}

			if ($post['social_type'] == 1) {
				
				$item['type'] = 'twitter';
				$item['filter_tags'][] = 'twitter';
				
			} elseif ($post['social_type'] == 2) {
				
				$item['type'] = 'youtube';
				$item['filter_tags'][] = 'youtube';
				
			} elseif ($post['social_type'] == 3) {
				
				$item['type'] = 'instagram';
				$item['filter_tags'][] = 'instagram';
				
			} elseif ($post['social_type'] == 4) {
				
				$item['type'] = 'facebook';
				$item['filter_tags'][] = 'facebook';
				
			} elseif ($post['category'] == 2) {
				
				$item['type'] = 'comunicados';
				$item['filter_tags'][] = 'comunicado';
				
			} elseif ($post['category'] == 3) {
				
				$item['type'] = 'noticias';
				$item['filter_tags'][] = 'noticia';
				
			} elseif ($post['category'] == 5) {
				
				$item['type'] = 'galeria';
				$item['filter_tags'][] = 'galeria';
				
			} elseif ($post['category'] == 6) {
				
				$item['type'] = 'campanha';
				$item['filter_tags'][] = 'campanha';
				
			}

			// customs
			
			if ($post['category'] == 6) {
				
				// campanha
				
				$item['link_campaign'] = $item['id'];
				
			} elseif ($post['category'] == 2 || $post['category'] == 3) {
				
				// comunicados
				
				$item['label'] = $post['label'];
				
			}
			
			if ($user) {
				
				$user_score = current( $db->query( "SELECT * FROM ranking WHERE user = :user AND post = :post", array(
					'user' => $user['id'],
					'post' => $post['id']
				) ) );
				
				$favorite = current( $db->query( "SELECT * FROM favorites WHERE user = :user AND post = :post", array(
					'user' => $user['id'],
					'post' => $post['id']
				) ) );

				$viewed = current( $db->query( "SELECT * FROM viewed WHERE user = :user AND post = :post LIMIT 1", array(
					'user' => $user['id'],
					'post' => $post['id']
				) ) );

				$item['user_score'] = $user_score ? (int)$user_score['score'] : 0;
				$item['favorite'] = $favorite ? $favorite['value'] == 1 : false;
				$item['viewed'] = $viewed ? true : false;
				
				if ($contains_read_later) {

					$read_later = current( $db->query( "SELECT * FROM read_later WHERE user = :user AND post = :post", array(
						'user' => $user['id'],
						'post' => $post['id']
					) ) );
					
					$item['read_later'] = $read_later ? $read_later['value'] == 1 : false;
					
				}
				
				if ($contains_notification) {

					$notification = current( $db->query( "SELECT * FROM notifications WHERE user = :user AND post = :post", array(
						'user' => $user['id'],
						'post' => $post['id']
					) ) );
					
					$item['notification'] = $notification ? $notification['value'] == 1 : false;
					
				}
				
				if ($force_notifications && $viewed) {
					
					continue;
					
				}
				
			}
			
			if ($force_read_later && ( !isset($item['read_later']) || !$item['read_later'] )) {
				
				continue;
				
			} elseif ($force_favorites && !$item['favorite']) {
				
				continue;
				
			}
			
			// tags

			foreach($item['tags'] as &$tag_id) {
				
				$tag = $db->select_id( 'tags', $tag_id );
				
				if (!isset($tags_lib[ $tag_id ])) {
					
					$tags_lib[ $tag_id ] = array(
						'id' => (int)$tag['id'],
						'name' => $tag['name'],
						'posts' => array()
					);
					
				}
				
				$tags_lib[ $tag_id ]['posts'][] = count($feed);
				
			}

			// links
			
			if ($post['category'] == 1) {
				
				$pattern = '@(http(s)?://)?(([a-zA-Z0-9])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';

				$item['message'] = preg_replace( 
					"/(?<!a href=\")((http|ftp)+(s)?:\/\/[^<>\s]+)/i", 
					"<a href=\"\\0\" target=\"blank\">\\0</a>", 
					$item['message']
				);
				
			}

			// ver mais
			
			if ($feed_type == 'posts' || $force_highlights) {
				
				$vermais_str = "<p><br></p><p><br></p>";
				$vermais_pos = strpos($item['message'], $vermais_str);
				
				if ( $vermais_pos !== false ) {
					
					if ($force_highlights) {
						
						$item['message'] = substr_replace($item['message'], "<br><div class='vermais-mostrar'>", $vermais_pos, strlen($vermais_str)) . '</div>';
						
					} else {
					
						$item['message'] = substr_replace($item['message'], "<br><div class='vermais'>Ver mais</div><div class='vermais-mostrar'>", $vermais_pos, strlen($vermais_str)) . '</div>';
						
					}

				}
				
			}

			//-

			$feed[] = $item;
			
		}
		
		foreach($tags_lib as &$tag) {
				
			$tags[] = $tag;
			
		}
		
		if ($filter == 'galeria') {
			
			$whatsapp_gallery_tags = get_ids( $db->get_metadata_value( "whatsapp_gallery_tags" ) );
			
			usort($tags, function($a, $b)
			{
				global $whatsapp_gallery_tags;
				
				return array_search($a['id'], $whatsapp_gallery_tags) > array_search($b['id'], $whatsapp_gallery_tags);
			});

		}
		
		$json['feed'] = $feed;
		$json['tags'] = $tags;
		
	break;

	case 'upload':

		$json['upload'] = array();
		
		if ($user) {
		
			$file = get_upload('file');
			$template = get_request('template');
			
			if($file){

				try {
					
					$req_uri = $_SERVER['REQUEST_URI'];
					$path = substr($req_uri,0,strrpos($req_uri,'/'));
					
					$file_id = $uploader->upload( $file, $path );

					if($file_id){
						
						$arr_uploads = array();
						
						$arr_uploads['file'] = $file_id;
						$arr_uploads['user'] = $user['id'];
						$arr_uploads['name'] = $file['name'];
						if($template) $arr_uploads['template'] = $template;
						
						$template_uploads = $db->insert('template_uploads', $arr_uploads);
						
					}
					
					$json['upload']['content'] = get_content_url( 'upload', $file_id );
					
				} catch (Exception $exc) {
					
					$error = $exc->getMessage();
				}
				
			} else {
				
				$error = "Arquivo não encontrado.";
				
			}

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		
		
	
	break;
	/*
	case 'surveys':
		
		if ($user) {
			
			$item = array();
			
			$survey_id = (int)get_request('id');
			
			$survey = current( $db->query( "SELECT * FROM surveys WHERE id = :id and status=1", array(
				'id' => $survey_id
			) ) );

			$survey_total = (int)current(array_filter(array_column($db->query( "SELECT COUNT(*) cont FROM survey_user_answers WHERE survey = :survey ", array(
				'survey' => (int)$survey_id
			) ), 'cont')));
			
			$voted = (int)current(array_filter(array_column($db->query( "SELECT min(1) voted FROM survey_user_answers WHERE survey = :survey and user = :user ", array(
				'survey' => (int)$survey_id,
				'user' => $user['id']
			) ), 'voted')));
			
			$survey_answers = array();
			
			$arr_survey_answers = array_filter( explode(',', $survey['survey_answers']) );
					
			$arr_survey_answers_ids = array();
			
			foreach($arr_survey_answers as $arr_survey_answer) {
						
				if(!in_array( $arr_survey_answer, array_column($survey_answers, 'id'))){									
					
					$survey_answer_db = $db->content( $arr_survey_answer, 'survey_answers' );
					
					$percentage = 0;
					
					if($survey_total>0){
						
						$count = (int)current(array_filter(array_column($db->query( "SELECT COUNT(*) cont FROM survey_user_answers WHERE survey = :survey and survey_answer = :survey_answer ", array(
							'survey' => (int)$survey_answer_db['survey'],
							'survey_answer' => (int)$survey_answer_db['id']
						) ), 'cont')));
						
						$percentage = round((((int)$count*100) / $survey_total), 2);
						
					}
					$survey_answer = array(
						'id' => (int)$survey_answer_db['id'],
						'text' => $survey_answer_db['text'],
						'amount' => (int)$count,
						'percentage' => $percentage
						
					);
				
					array_push ($survey_answers,  $survey_answer);
					
				}
				
				array_push ($arr_survey_answers_ids, array_search($arr_survey_answer, array_column($survey_answers, 'id')));

			}
			
			//$item['survey_answers'] = implode(",", $arr_survey_answers_ids);	
			$item['id'] = (int)$survey['id'];
			$item['text'] = $survey['text'];
			$item['total'] = $survey_total;
			$item['voted'] = $voted;
			
			$json['surveys'] = $item;
			
			$json['survey_answers'] = $survey_answers;

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		break;
		*/
	case 'set_survey_vote':
		
		//echo "teste";
		//$hotspots = current(get_api_arr('surveys', 7));
		
		if ($user) {
			
			$item = array();
			
			$survey_id = (int)get_request('survey_id');
			$survey_answer_id = (int)get_request('survey_answer_id');
			
			if(!$survey_id){
				
				$error = "Pesquisa inválida.";
			
			}else{
				
				if(!$survey_answer_id){
			
					$error = "Resposta inválida.";
				
				}else{
					
					
					$vote_id = (int)
						current(array_filter(array_column(
							$db->query( "SELECT id  
									FROM survey_user_answers 
									WHERE survey = :survey and user = :user 
									order by id desc
									limit 1", array(
											'survey' => (int)$survey_id,
											'user' => $user['id']
										) ), 'id')));				
									
					if($vote_id){
						
						$db->update('survey_user_answers', $vote_id, array(
								'survey_answer' => $survey_answer_id
							));
							
						$survey_user_answers_db = $vote_id;
						
					}else {
						
						$survey_user_answers_db = $db->insert('survey_user_answers', array(
							'user' => $user['id'],
							'survey' => $survey_id,
							'survey_answer' => $survey_answer_id,
						));
						
					}
					
					if( $survey_user_answers_db > 0 ){

						$retorno = current(get_api_arr('surveys', $survey_id));
						
						$json['survey'] = current($retorno);
					}

				}
				
			}

		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		break;
		
		/*case 'survey_teste':
		
			//echo "teste";
			$json['survey'] =  current(get_api_arr('surveys', 1));
		
		break;*/
		
	case 'faqs':
	
		$json['faq_groups'] = array();	
	
		$faq_id = (int)get_request('faq');
		
		$query = get_request('query');				
		
		$group_where = "";
		$topic_where = array();
		$faq_where =   array();
			
		$sub_sel_where="";
		
		if( $faq_id ){
			
			$faq = $db->content( $faq_id, 'faq_group_topic_items' );
			
			if(count($faq)>0){
				
				$group_where = " and faq_groups.id= {$faq['faq_group']}";
				$topic_where = array($faq['faq_group_topic']);
				$faq_where =   array($faq_id);
				
				$sub_sel_where = " and faq_group_topic_items.id = $faq_id ";
				
			}
			
		}
		
		$search ['query']  = '%' . $query . '%';
		
		$group_where .= " and  faq_groups.id in (SELECT distinct faq_group_topic_items.faq_group FROM `faq_group_topic_items`
					where faq_group_topic_items.status=1 $sub_sel_where and (faq_group_topic_items.title LIKE :query or faq_group_topic_items.text LIKE :query)) ";
		
	
		$faq_groups = $db->query("SELECT 
								faq_groups.id as id,
								faq_groups.title as title,
								faq_groups.subtitle as subtitle,
								faq_groups.image as image,
								faq_groups.faq_group_topics as faq_group_topics,
								FIND_IN_SET(faq_groups.id, metadata.value)  as ordem
								FROM `faq_groups` 
								JOIN metadata on (property='faq_groups') and FIND_IN_SET(faq_groups.id, metadata.value)
								WHERE faq_groups.status=1 $group_where  
								order by ordem;",$search);

		foreach($faq_groups as $faq_group) {
			
			$faq_group_item = array();
			
			$faq_group_item['id'] = (int)$faq_group['id'];
			$faq_group_item['title'] = $faq_group['title'];
			$faq_group_item['subtitle'] = $faq_group['subtitle'];
			$faq_group_item['image'] = get_content_url( 'faq_groups', $faq_group['image'] );
			
			/* topicos*/
			$faq_group_topic_ids = get_ids( $faq_group['faq_group_topics']);
			if($topic_where) $faq_group_topic_ids = $topic_where;
			
			foreach($faq_group_topic_ids as $faq_group_topic_id) {
				
				$faq_group_topic = $db->content( $faq_group_topic_id, 'faq_group_topics' );
				
				if ( $faq_group_topic['status'] ) {
			
					$faq_group_topic_item = array();
					
					$faq_group_topic_item['id'] = (int)$faq_group_topic['id'];
					$faq_group_topic_item['title'] = $faq_group_topic['title'];
					
					/* faq*/
					$faqs_ids = get_ids( $faq_group_topic['faq_group_topic_items']);
					if($faq_where) $faqs_ids = $faq_where;
					
					foreach($faqs_ids as $faqs_id) {
						
						$faq = $db->content( $faqs_id, 'faq_group_topic_items' );
						
						if ( $faq['status'] ) {
								
							if($query)
							if (!(stripos($faq['title'], $query) !== false) && !(stripos($faq['text'], $query) !== false)) continue;
							
							$faq_item = array();
					
							$faq_item['id'] = (int)$faq['id'];
							$faq_item['question'] = $faq['title'];
							$faq_item['answer'] = $faq['text'];
							
							if($faq['image']){
								
								$faq_item['content'] = url( $faq['image_url'] );
								$faq_item['content_type'] = get_content_format( $faq['image_url'] );
								
							}
							
							
							$faq_group_topic_item['faqs'][] = $faq_item;

						}
						
						
					}
					
					$faq_group_item['faq_topics'][] = $faq_group_topic_item;
					
				}
				
			}
			
			$json['faq_groups'][] = $faq_group_item;

		}

		break;
		
	case 'tutorials':
	
		$json['tutorials'] = array();	
	
		$tutorial_id = (int)get_request('id');
		$query = get_request('query');
		
		
		$where = "";
		
		$search ['query']  = '%' . $query . '%';
		
		if( $tutorial_id ) {
			
			$search ['id']  = $tutorial_id;
			$where = "and id = :id";
		}
						
		$tutorials = $db->query( "SELECT * FROM tutorials 
									WHERE status = 1 $where and 
										name LIKE :query 
									order by date desc", $search ) ;						
								
		foreach($tutorials as $tutorial) {
			
			$tutorial = $db->content( $tutorial, 'tutorials' );
			
			$tutorial_item = array();
			
			$tutorial_item['id'] = (int)$tutorial['id'];
			$tutorial_item['name'] = $tutorial['name'];
			$tutorial_item['thumbnail'] = get_content_url( 'tutorials', $tutorial['thumbnail'] );
			$tutorial_item['file'] = url( $tutorial['file_url'] );
			
			
			$json['tutorials'][] = $tutorial_item;
			
		}
		
		break;
		
		
	case 'set_up_image':
		
		$file_id = 0;
	
		if ($user) {
			
			$file = get_upload('file');

			try {
				
				$user_card = array();
				
				$user_card['user'] = $user['id'];
				
				if($file){
					
					$req_uri = $_SERVER['REQUEST_URI'];
					$path = substr($req_uri,0,strrpos($req_uri,'/'));
					
					$file_id = $uploader->upload( $file, $path );
					
					//imagem usuario
					if($file_id) $user_card['image'] = $file_id ;
					
				print_r($file_id ); die();
				}
				
			
			} catch (Exception $exc) {
				
				$error = $exc->getMessage();
			}
			
		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		break;	
		
	case 'set_digital_card':
		
		$file_id = 0;
	
		if ($user) {
			
			//$file = get_upload('file');

			try {
				
				$user_card = array();
				
				$user_card['user'] = $user['id'];
				
				/*if($file){
					
					$req_uri = $_SERVER['REQUEST_URI'];
					$path = substr($req_uri,0,strrpos($req_uri,'/'));
					
					$file_id = $uploader->upload( $file, $path );
					
					//imagem usuario
					if($file_id) $user_card['image'] = $file_id ;
				
				}*/
				
				$user_cards_db = current($db->query( "SELECT * FROM `user_cards`
										where `user`= {$user['id']} limit 1"));
			
				
				if(get_request('image')) $user_card['image'] = get_request('image');
				
				if(get_request('name')) $user_card['name'] = get_request('name');
				if(get_request('job')) $user_card['job'] = get_request('job');
				if(get_request('background_image')) $user_card['background_image'] = get_request('background_image');

				
				
				if(get_request('whatsapp')) $user_card['whatsapp'] = get_request('whatsapp');
				if(get_request('phone')) $user_card['phone'] = get_request('phone');
				if(get_request('email')) $user_card['email'] = get_request('email');
				if(get_request('location')) $user_card['location'] = get_request('location');
				if(get_request('concessionaire')) $user_card['concessionaire'] = get_request('concessionaire');
		
				if( !$user_cards_db ){
					
					//inclusao

					$user_card_db = $db->insert('user_cards', $user_card);
					
				} else {
					
					//alteracao
					
					$user_card_db = $db->update('user_cards', $user_cards_db['id'], $user_card);
					
				}
				
				$retorno = get_api_arr("get_digital_card&secret_key=$secret_key", 0);
				
				$json = $retorno;
			
			} catch (Exception $exc) {
				
				$error = $exc->getMessage();
			}
			
		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		break;
		
	case 'get_digital_card':
	
		if ($user) {
			
			
			$user_cards_db = 
				current($db->query( "SELECT * FROM `user_cards`
							where `user`= {$user['id']} limit 1"));
			
			$user_card = array();
			
			
			$user_card['name'] = ""; 
			$user_card['job'] = "";
			$user_card['background_image'] = "";
			$user_card['image'] = "";
			$user_card['whatsapp'] = "";
			$user_card['phone'] = "";
			$user_card['email'] = "";
			$user_card['location'] = "";
			$user_card['concessionaire'] = "";
			
			if($user_cards_db){
				
				$user_card_content = $db->content( $user_cards_db['id'], 'user_cards' );

				
				
				$user_card['name'] = $user_cards_db['name'];
				$user_card['job'] = $user_cards_db['job'];
				
				$user_card['background_image'] = $user_cards_db['background_image'];
				
				if( $user_card['background_image'] > 0 ){
									
					//$user_data_db = $db->content( $user_card, 'users' );
					
					$user_card['background_image_id'] = $user_card_content['background_image'];
					$user_card['background_image_url'] = url( $user_card_content['background_image_url'] );

				}
						
				$user_card['image'] = $user_cards_db['image'];
				/*
				if( $user_card['image'] > 0 ){
									
					//$user_data_db = $db->content( $user_card, 'users' );
					
					$user_card['image_id'] = $user_card_content['image'];
					$user_card['image_url'] = url( $user_card_content['image_url'] );

				}*/
				
				$user_card['whatsapp'] = $user_cards_db['whatsapp'];
				$user_card['phone'] = $user_cards_db['phone'];
				$user_card['email'] = $user_cards_db['email'];
				//if(!$user_card['email'])$user_card['email'] = $user['email'];
				$user_card['location'] = $user_cards_db['location'];
				$user_card['concessionaire'] = $user_cards_db['concessionaire'];
		
			}else{
				
				$user_card['email'] = $user['email'];
				$user_card['concessionaire'] = 'Nome da Concessionária';
				
			}
			$json['digital_card'] = $user_card;
			
		} else {
			
			$error = "Nenhum usuário logado.";
			
		}
		
		break;
		
	
		
}

if ($error) {
	
	$json['error'] = $error;
	
	
}

// print

echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
