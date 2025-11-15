<?php

/**
*
*	API
*
* */

define('SUNAG', true);
define('ROOT', str_replace('\\', '/', dirname(__FILE__, 2)) . '/');

include('../includes/config.php');

include('../includes/global.php');
include('../includes/methods.php');
include('../includes/database.php');
include('../includes/custom/database.php');
include('../includes/token.php');
include('../includes/session.php');

include('../includes/logtxt.php');

include('../includes/uploader.php');

//include('../includes/user.php');

include('../includes/thirdparties/PHPMailer/src/PHPMailer.php');
include('../includes/thirdparties/PHPMailer/src/SMTP.php');
include('../includes/thirdparties/PHPMailer/src/Exception.php');
include('../includes/email.php');


need_https();

$db = new CustomDataBase(DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
$token = new Token();
$session = new Session('session', 3600 * 24 * 365);

$list_categories= array(
	//1 => array( 'id' => '1', 'name' => 'Campanhas'),
	1 => array( 'id' => '1', 'parent' => 'socials','name' => 'Socials', 'property' => 'posts_banners_socials', 'name_list' => 'Socials', 'url_list' => 'posts.php', 'tags' => 'post_tags', 'url' => 'post_edit.php'),
	2 => array( 'id' => '2', 'parent' => 'comunicado','name' => 'Comunicados', 'property' => 'posts_banners_comunicados', 'name_list' => 'Comunicados', 'url_list' => 'communicateds.php'),
	3 => array( 'id' => '3', 'parent' => 'post','name' => 'Notícias', 'property' => 'posts_banners_noticias', 'name_list' => 'Notícias', 'url_list' => 'news.php'),
	//4 => array( 'id' => '4', 'parent' => 'post','name' => 'Stories', 'property' => 'posts_banners_stories', 'name_list' => 'Postagens', 'url_list' => 'posts.php'),
	5 => array( 'id' => '5', 'parent' => 'whats_gallery', 'name' => 'Galeria Whatsapp', 'property' => 'whatsapp_galeries_banners', 'name_list' => 'Galerias Whatsapp', 'url_list' => 'whatsapp_galeries.php', 'tags' => 'whatsapp_gallery_tags', 'url' => 'whatsapp_gallery_edit.php'),
	6 => array( 'id' => '6', 'parent' => 'campaign','name' => 'Campanha', 'property' => 'campaigns_banners', 'name_list' => 'Campanhas', 'url_list' => 'campaigns.php', 'tags' => 'campaign_tags', 'url' => 'campaign_edit.php'),
	7 => array( 'id' => '7', 'parent' => 'template','name' => 'Background', 'property' => 'template_backgrounds', 'name_list' => 'Backgrounds', 'url_list' => 'templates.php', 'tags' => 'highlighted_backgrounds_tags', 'url' => 'template_edit.php', 'name2' => 'Item', 'url2' => 'template_item_edit.php'),
	8 => array( 'id' => '8', 'parent' => 'template','name' => 'Estilo', 'property' => 'template_styles', 'name_list' => 'Estilos', 'url_list' => 'templates.php?type=2&', 'tags' => 'template_styles_tags', 'url' => 'template_edit.php', 'name2' => 'Item', 'url2' => 'template_item_edit.php'),
	9 => array( 'id' => '9', 'parent' => 'template','name' => 'Background', 'property' => 'template_backgrounds', 'name_list' => 'Backgrounds', 'url_list' => 'templates.php', 'tags' => 'highlighted_backgrounds_tags', 'url' => 'template_edit.php', 'name2' => 'Item', 'url2' => 'template_item_edit.php'),
   10 => array( 'id' => '10', 'parent' => 'template','name' => 'Estilo', 'property' => 'template_styles', 'name_list' => 'Estilos', 'url_list' => 'templates.php?type=2&', 'tags' => 'highlighted_styles_tags', 'url' => 'template_edit.php', 'name2' => 'Item', 'url2' => 'template_item_edit.php'),
   11 => array( 'id' => '11', 'parent' => 'template','name' => 'Template', 'property' => 'templates', 'name_list' => 'Templates', 'url_list' => 'templates.php', 'tags' => 'highlighted_templates_tags', 'url' => 'template_edit.php', 'name2' => 'Item', 'url2' => 'template_item_edit.php'),
   15 => array( 'id' => '15', 'parent' => 'quick_view','name' => 'Visão Rápida', 'property' => 'quick_view_materials', 'name_list' => 'Visões Rápidas', 'url_list' => 'quick_views.php', 'tags' => 'quick_view_tags', 'url' => 'quick_view_edit.php', 'name2' => 'Materiais', 'url2' => 'quick_view_materials.php')

);

//Campanhas	

$list_content_types= array(
	1 => array( 'id' => '1', 'name' => 'Link'),
	2 => array( 'id' => '2', 'name' => 'Upload')
	);

//Categorias em Noticias - (Segmento, Renault )
$list_label_news= array(
	1 => array( 'id' => '3', 'name' => 'Segmento'),
	2 => array( 'id' => '4', 'name' => 'Renault')
	);


/*	
$list_group_tags= array(
	1 => array( 'id' => '1', 'name' => 'Postagem', 'name_list' => 'Postagens', 'url_list' => 'posts.php', 'url' => 'post_edit.php', 'tabela' => 'posts', 'tags' => 'post_tags'),
	2 => array( 'id' => '2', 'name' => 'Galeria Whatsapp', 'name_list' => 'Galerias Whatsapp', 'url_list' => 'whatsapp_galeries.php', 'url' => 'whatsapp_gallery_edit.php', 'tabela' => 'whatsapp_galeries', 'tags' => 'whatsapp_gallery_tags'),
	3 => array( 'id' => '3', 'name' => 'Campanha', 'name_list' => 'Campanhas', 'url_list' => 'campaigns.php', 'url' => 'campaign_edit.php', 'tabela' => 'campaigns', 'tags' => 'campaign_tags'),
	);*/
/*
$list_group= array(
	1 => array( 'id' => '1', 'name' => 'Postagem', 'name_list' => 'Postagens', 'url_list' => 'posts.php', 'url' => 'post_edit.php', 'tabela' => 'posts'),
	2 => array( 'id' => '2', 'name' => 'Galeria Whats', 'name_list' => 'Galerias Whats', 'url_list' => 'whatsapp_galeries.php', 'url' => 'whatsapp_gallery_edit.php', 'tabela' => 'whatsapp_galeries'),
	3 => array( 'id' => '3', 'name' => 'Campanha', 'name_list' => 'Campanhas', 'url_list' => 'campaigns.php', 'url' => 'campaign_edit.php', 'tabela' => 'campaigns'),
	);	
*/
// APP

include('useradmin.php');
include('webadmin.php');

$useradm = new UserAdmin();

$webadm = new WebAdmin();
$webadm->logged = $useradm->logged();
$webadm->footer = $useradm->logged();

$uploader = new Uploader();

function need_login() {
	
	global $useradm,$db;
	
	if ($useradm->level() == 2 and !empty($useradm->bir()) ){
		
		if(!empty($useradm->manager_accesses())){

			$manager_accesses = array_filter( explode(',', $useradm->manager_accesses()) );
			
			$base = basename($_SERVER['SCRIPT_NAME']);
			
			if(!in_array($base, $manager_accesses)) {
				
				redirect($manager_accesses[0]);
			
			}
		}
		
	}
	
	if (!$useradm->logged()) {
		
		$redirect = basename($_SERVER['REQUEST_URI']);
		
		$redirect = str_replace('&', '%26', $redirect);
		redirect("signin.php?redirect=$redirect");

	}
	
}


?>