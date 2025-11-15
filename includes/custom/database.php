<?php

class CustomDataBase extends DataBase {

	public function __construct($host, $databank, $username, $password) {
		
		parent::__construct($host, $databank, $username, $password);
		
    }
	
	public function set_metadata($property, $value = "") {
		
		$result = $this->get_metadata( $property );
		
		if ( $result ) {
			
			$this->update( 'metadata', $result['id'], array( 'value' => $value ) );
			
		} else {
			
			return $this->insert( 'metadata', array( 'property' => $property, 'value' => $value ) );
			
		}
		
	}
	
	public function get_metadata($property) {
		
		$result = current( $this->query("SELECT * FROM metadata WHERE property = :property;", array( 'property' => $property )) );
		
		return $result;
		
	}
		
	public function get_metadata_id($property) {
		
		$meta = $this->get_metadata( $property );
		
		return $meta ? (int)$meta['id'] : 0;
		
	}	
	
	public function get_metadata_value($property) {
		
		$meta = $this->get_metadata( $property );
		
		return $meta ? $meta['value'] : null;
		
	}
	
	public function content($content_or_id, $type) {
		
		global $uploader;
		
		if (!$content_or_id) return $content_or_id;
		
		$content = is_array($content_or_id) ? $content_or_id : $this->select_id($type, $content_or_id);
		$content['id'] = isset($content['id']) ? $content['id'] : 0;
		
		if ($content) {
			
			switch($type) {
				
				case 'users':
				
					if(array_key_exists('background_image', $content)){
						
						$background_image = $uploader->get( $content['background_image'] );
						$content['background_image_url'] = $background_image ? $background_image['url'] : '';
						$content['background_image_extension'] = $background_image ? $background_image['extension'] : '';
						$content['background_image_name'] = $background_image ? $background_image['name'] : '';
						
					}
					/*
					if(array_key_exists('image', $content)){
						
						$image = $uploader->get( $content['image'] );
						$content['image_url'] = $image ? $image['url'] : '';
						
					}*/
					
					$content['edit_url'] = "user_edit.php?id={$content['id']}";
					
					break;
					
				case 'user_cards':
				
					if(array_key_exists('image', $content)){
						
						$image = $uploader->get( $content['image'] );
						$content['image_url'] = $image ? $image['url'] : '';
						$content['image_extension'] = $image ? $image['extension'] : '';
						$content['image_name'] = $image ? $image['name'] : '';
						
					}
				
					if(array_key_exists('background_image', $content)){
						
						$background_image = $uploader->get( $content['background_image'] );
						$content['background_image_url'] = $background_image ? $background_image['url'] : '';
						$content['background_image_extension'] = $background_image ? $background_image['extension'] : '';
						$content['background_image_name'] = $background_image ? $background_image['name'] : '';
						
					}
					
					$content['edit_url'] = "user_edit.php?id={$content['id']}";
					
					break;

				case 'managers':
					
					$content['edit_url'] = "manager_edit.php?id={$content['id']}";
					
					break;
					
				case 'versions':
					
					$content['edit_url'] = "version_edit.php?id={$content['id']}";
					
					break;
				
				case 'vehicle_version':
				
					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					$content['image_extension'] = $image ? $image['extension'] : '';
					
					$content['vehicle_name'] = "";
					if( $content['vehicle'] > 0 ) $content['vehicle_name'] = current(array_filter(array_column($this->query("SELECT name FROM vehicles WHERE id = {$content['vehicle']}"), 'name')));
					
					$content['version_name'] = "";
					if( $content['version'] > 0 ) $content['version_name'] = current(array_filter(array_column($this->query("SELECT name FROM versions WHERE id = {$content['version']}"), 'name')));
					
					$content['edit_url'] = "vehicle_version_edit.php?id={$content['id']}";
					
					break;
				
				case 'tutorials':
					/*$file = $uploader->get( $content['file'] );
					$content['file_url'] = $file ? $file['url'] : '';
					$content['file_extension'] = $file ? $file['extension'] : '';
					$content['file_name'] = $file ? $file['name'] : '';*/
					$file = $uploader->get( $content['file'] );

					$content['file_url'] = $file ? $file['url'] : '';
					$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
					$content['date_color'] = @$content['status'] ? 'green' : 'red';
					$content['edit_url'] = "tutorial_edit.php?id={$content['id']}";
					
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					break;
					
				case 'faq_groups':
					
					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					
					$content['edit_url'] = "faq_group_topics.php?id={$content['id']}";
					
					break;
					
				case 'faq_group_topics':
					
					$content['edit_url'] = "faq_group_topic_items.php?faq_group_id={$content['faq_group']}&id={$content['id']}";
					
					break;
					
				case 'faq_group_topic_items':
					
					//$image = $uploader->get( $content['image'] );

					//$content['image_url'] = $image ? $image['url'] : '';
					
					//$thumbnail = $uploader->get( $content['thumbnail'] );
					
					//$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					//$content['edit_url'] = "template_items.php?type={$content['type']}&id={$content['id']}";
					
			
					$image = (array_key_exists('image', $content))? $uploader->get( $content['image'] ) : "";

					$content['image_url'] = $image ? $image['url'] : '';
					
					$content['faq_group'] = (array_key_exists('faq_group', $content))? $content['faq_group'] : 0;
					
					$content['faq_group_topic'] = (array_key_exists('faq_group_topic', $content))? $content['faq_group_topic'] : 0;
					
					$content['edit_url'] = "faq_group_topic_item_edit.php?faq_group_id={$content['faq_group']}&faq_group_topic_id={$content['faq_group_topic']}&id={$content['id']}";
						
					break;
					
				case 'templates':
					
					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					//$content['edit_url'] = "template_items.php?type={$content['type']}&id={$content['id']}";
					$content['edit_url'] = "template_edit.php?type={$content['type']}&id={$content['id']}";
					
					break;
				
				case 'template_items':
					
					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					$content['edit_url'] = "template_item_edit.php?template_id={$content['template']}&id={$content['id']}";
					
					break;
				
				case 'template_background_items':
					
					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					$content['edit_url'] = "template_background_item_edit.php?template_background_id={$content['template_background']}&id={$content['id']}";
					
					break;
					
				case 'template_style_items':

					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					$content['edit_url'] = "template_style_item_edit.php?template_style_id={$content['template_style']}&id={$content['id']}";
					
					break;
					
				case 'template_texts':

					$content['edit_url'] = "template_text_edit.php?id={$content['id']}";
					
					break;
					
				case 'categories':
					
					$content['edit_url'] = "category_edit.php?id={$content['id']}";
					
					break;
				
				case 'user_categories':
					
					$content['edit_url'] = "user_category_edit.php?id={$content['id']}";
					
					break;
					
				
				case 'quick_view_materials':
				
					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					$content['image_extension'] = $image ? $image['extension'] : '';
					
					if(array_key_exists('image_highlight', $content)){
						
						$image_highlight = $uploader->get( $content['image_highlight'] );
					
						$content['image_highlight_url'] = $image_highlight ? $image_highlight['url'] : '';
						
					}
					
					$file = $uploader->get( $content['file'] );
					$content['file_url'] = $file ? $file['url'] : '';
					$content['file_extension'] = $file ? $file['extension'] : '';
					$content['file_name'] = $file ? $file['name'] : '';
				
					$content['date_color'] = @$content['status'] ? 'green' : 'red';
					$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
				
					$content['edit_url'] = "quick_view_material_edit.php?quick_view_id={$content['quick_view']}&id={$content['id']}";
				
					break;
				case 'posts':
				case 'whatsapp_galeries':
				case 'campaigns':
				case 'communicateds':
				case 'renault_universe':
				case 'renault_care_services':
				case 'renault_academy':
				case 'news':

					$image = $uploader->get( $content['image'] );

					$content['image_url'] = $image ? $image['url'] : '';
					$content['image_extension'] = $image ? $image['extension'] : '';
					
					if(array_key_exists('image_highlight', $content)){
						
						$image_highlight = $uploader->get( $content['image_highlight'] );
					
						$content['image_highlight_url'] = $image_highlight ? $image_highlight['url'] : '';
						
					}
				
					$content['date_color'] = @$content['status'] ? 'green' : 'red';
					$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
					
					$file = $uploader->get( $content['file'] );
					$content['file_url'] = $file ? $file['url'] : '';
					$content['file_extension'] = $file ? $file['extension'] : '';
					$content['file_name'] = $file ? $file['name'] : '';
					
					if(array_key_exists('tell_client', $content)){
						
						$tell_client = $uploader->get( $content['tell_client'] );
						$content['tell_client_url'] = $tell_client ? $tell_client['url'] : '';
						$content['tell_client_extension'] = $tell_client ? $tell_client['extension'] : '';
						$content['tell_client_name'] = $tell_client ? $tell_client['name'] : '';
						
					}
					
					if ($type == 'posts') {
						
						if(array_key_exists('expiration_date', $content)) $content['expiration_date_sc'] = substr($content['expiration_date'], 0, 10);
						
						$content['edit_url'] = "post_edit.php?id={$content['id']}";
						
					} elseif ($type == 'whatsapp_galeries') {
						
						$content['edit_url'] = "whatsapp_gallery_edit.php?id={$content['id']}";
						
					} elseif ($type == 'campaigns') {
						
						$content['edit_url'] = "campaign_edit.php?id={$content['id']}";
						
					}elseif ($type == 'communicateds') {
						
						$content['edit_url'] = "communicated_edit.php?id={$content['id']}";

					}elseif ($type == 'renault_universe') {
						
						$content['edit_url'] = "renault_universe_edit.php?id={$content['id']}";
						
					}elseif ($type == 'renault_care_services') {
						
						$content['edit_url'] = "renault_care_service_edit.php?id={$content['id']}";
						
					}elseif ($type == 'renault_academy') {
						
						$content['edit_url'] = "renault_academy_edit.php?id={$content['id']}";
						
					}elseif ($type == 'news') {
						
						$content['edit_url'] = "new_edit.php?id={$content['id']}";
						
					}
					
					break;

				case 'tags':
				
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					if(!empty($content['category']))
						$content['edit_url'] = "tag_edit.php?category={$content['category']}&id={$content['id']}";
					
					break;
				
				case 'tag_groups':
				
					$thumbnail = $uploader->get( $content['thumbnail'] );
					
					$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					if(!empty($content['category']))
						$content['edit_url'] = "tag_group_edit.php?category={$content['category']}&id={$content['id']}";
					
					break;
					
				case 'tag_subgroups':
				
					//$thumbnail = $uploader->get( $content['thumbnail'] );
					
					//$content['thumbnail_url'] = $thumbnail ? $thumbnail['url'] : '';
					
					//if(!empty($content['category']))
						$content['edit_url'] = "tag_subgroup_edit.php?id={$content['id']}";
					
					break;
				case 'renault_universe_post_topics':
				case 'post_topics':
				
					if ($type == 'renault_universe_post_topics') {
						
						$content['edit_url'] = "renault_universe_post_topic_edit.php?post_id={$content['post']}&id={$content['id']}";
						
					}else {
				
						$content['edit_url'] = "post_topic_edit.php?post_id={$content['post']}&id={$content['id']}";
					
					}
					
					break;
			
				case 'vehicles':
				
					$image = $uploader->get( $content['image'] );
					
					$content['image_url'] = $image ? $image['url'] : '';
				
					$content['edit_url'] = "vehicle_edit.php?id={$content['id']}";
					
					$content['extension'] = $image ? $image['extension'] : '';
						
					break;
					
				case 'quick_views':
				
					//$image = $uploader->get( $content['image'] );
					
					//$content['image_url'] = $image ? $image['url'] : '';
					
					$image = $uploader->get( $content['image'] );
					
					$content['image_url'] = $image ? $image['url'] : '';
				
					$content['date_color'] = @$content['status'] ? 'green' : 'red';
					$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
					$content['edit_url'] = "quick_view_edit.php?id={$content['id']}";
					
					//$content['extension'] = $image ? $image['extension'] : '';
						
					break;
					
				case 'quick_view_stamps':
				
					//$image = $uploader->get( $content['image'] );
					
					//$content['image_url'] = $image ? $image['url'] : '';
				
					//$content['date_color'] = @$content['status'] ? 'green' : 'red';
					//$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
					//$content['edit_url'] = "quick_view_stamp_edit.php?id={$content['id']}";
					
					$image = $uploader->get( $content['image'] );
					
					$content['image_url'] = $image ? $image['url'] : '';
					
					$content['edit_url'] = "quick_view_stamp_edit.php?quick_view_id={$content['quick_view']}&id={$content['id']}";
					
					//$content['extension'] = $image ? $image['extension'] : '';
						
					break;
				case 'banners':
					
					$image = $uploader->get( $content['image'] );
					
					$content['image_url'] = $image ? $image['url'] : '';
				
					$content['edit_url'] = "banner_edit.php?id={$content['id']}";
					
					$content['extension'] = $image ? $image['extension'] : '';
					
					break;
				
				case 'surveys':
				
					$content['date_color'] = @$content['status'] ? 'green' : 'red';
					$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
					$content['edit_url'] = "survey_answers.php?id={$content['id']}";
					
					break;
					
				case 'survey_answers':
				
					$content['date_color'] = @$content['status'] ? 'green' : 'red';
					$content['date_str'] = date_format( date_create( @$content['date'] ), 'd/m/Y H:i:s' );
					$content['edit_url'] = "survey_answer_edit.php?survey_id={$content['survey']}&id={$content['id']}";
					
					break;

			}
			
		}
		
		return $content;
		
	}

}

?>