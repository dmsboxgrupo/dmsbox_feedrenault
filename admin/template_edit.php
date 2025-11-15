<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

//parametros
$type = (int)get('type');
$id = (int)get('id');

$is_new = $id == 0;

if($type==2){
	
	$template_name ='styles';
	$template_label ='Estilo';
	$category_tags = 8;
	$tag_names ='template_styles_tags';
	
}else{
	$type =1;
	$template_name ='templates';
	$template_label ='Template';
	$category_tags = 11;
	$tag_names ='highlighted_templates_tags';
	
}

$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");

$json_str = $db->get_metadata_value( "template_types" );

$template_types = current(json_decode($json_str, true));

$json_str_c = $db->get_metadata_value( "channels" );

$channels = current(json_decode($json_str_c, true));

//formats
$json_formats = $db->get_metadata_value( "formats" );

$formats = current(json_decode($json_formats, true));

$manager_user_logado = $useradm->get_property('id');

$tags = $db->query("SELECT tags.id, tags.name FROM tags 
					join metadata on property='{$tag_names}' and FIND_IN_SET(tags.id, metadata.value) 
					WHERE tags.status = 1 and tags.category = {$category_tags}");
					/*
					echo "SELECT tags.id, tags.name FROM tags 
					join metadata on property='{$tag_names}' and FIND_IN_SET(tags.id, metadata.value) 
					WHERE tags.status = 1 and tags.category = {$category_tags}"; die();*/
					
/*
FROM `cars` 
	join car_versions on FIND_IN_SET(car_versions.id, car_versions) 
*/

//$category_id = (int)get('category');
//$category = $list_categories[$category_id];

//variaveis do grupo
//$namelist = $category['name_list'];
//$url_list = $category['url_list'];

//$readonly = "readonly";

$tabela = 	'metadata';
$metadata_id = $db->get_metadata_id( $template_name );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );
	
	//content tags templates
	$metadata_tag_id = $db->get_metadata_id( 'highlighted_templates_tags' );
	//$root_content_tags = $db->content( $metadata_tag_id, 'metadata' );

} else {

	redirect("templates.php");
	
}



function get_content($insert=true) {
	
	global $metadata_id, $uploader, $type, $manager_user_logado, $is_new;
	
	$content = array(		
		'name' => get_post('name'),
		'format' => get_post('format'),
		'field1' => get_post('field1'),
		'field1_color' => get_post('field1_color'),
		'field2' => get_post('field2'),
		'field2_color' => get_post('field2_color'),
		'field3' => get_post('field3'),
		'field3_color' => get_post('field3_color'),
		'field4' => get_post('field4'),
		'field4_color' => get_post('field4_color'),
		'field5' => get_post('field5'),
		'field1_style' => get_post('field1_style'),
		'field2_style' => get_post('field2_style'),
		'field3_style' => get_post('field3_style'),
		'field4_style' => get_post('field4_style'),
		'search_tags'=> get_post('search_tags'),
		'template_type' => get_post('template_type'),
		'channel' => get_post('channel'),
		'show_logo' => get_post('show_logo')
	);
	
	if($is_new){ 
		$content['metadata'] = $metadata_id;
		$content['type'] = $type;
		$content['user'] = $manager_user_logado;
	}
	
	if(get_post('guarda_arq')== 0){
		
		$content['image'] = 0;
		
	}
	
	if(get_post('guarda_arq_thumb')== 0){
		
		$content['thumbnail'] = 0;
		
	}
	
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;
		$content['thumbnail'] = 0;
		$content['tags'] = get_post('tags');
		//$content['template_item_texts'] = get_post('template_item_texts');
		$content['vehicles'] = get_post('vehicles');
		
	}else{
		
		$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		$content['vehicles'] = empty(get_post('vehicles'))?"":implode(",", get_post('vehicles'));
		//print_r($content['tags']); die();
		//$content['template_item_texts'] = empty(get_post('template_item_texts'))?"":implode(",", get_post('template_item_texts'));
		//if(!empty(get_post('template_item_texts_values'))) $content['template_item_texts'] =get_post('template_item_texts_values');
		
	}
	
	if (has_upload('image')) {
		
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}
	
	if (has_upload('thumbnail')) {
		
		$file_id = $uploader->upload( get_upload('thumbnail') );

		$content['thumbnail'] = $file_id;
	}
	
	
	return $content;

}


function update_template_tags() {
	
	global $db, $metadata_tag_id;

	$root_content_tags = $db->content( $metadata_tag_id, 'metadata' );
				
	$template_tags_new = current($db->query("SELECT GROUP_CONCAT(tags_id order by tags_id) as tags_ids from(
												SELECT tags.id as tags_id
												FROM tags 
												JOIN templates on FIND_IN_SET(tags.id, templates.tags) 
												JOIN metadata on property='highlighted_templates_tags' and FIND_IN_SET(tags.id, metadata.value)=0
												WHERE tags.status = 1 and category=7 
												GROUP by tags.id
											) tab
											"))['tags_ids'];
	
	
	$tags = $root_content_tags['value'];
	
	if($template_tags_new) $tags .= (($tags)?",":"").$template_tags_new;
	
	$db->update('metadata', $root_content_tags['id'], array(
		'value' => $tags
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';		
				

}


function update_templates($new_topic=null) {
	
	global $db, $root_content;
	
	$templates = array();
	
	$arr =explode(",", $root_content['value']);
	foreach($arr as $template) {
		
		$content = $db->content( $template, 'templates' );
		
		if ( $content['status'] ) {
		
			array_push( $templates, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $templates, $new_topic );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $templates)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($id > 0) {
	
	$content = $db->content( $db->select_id( 'templates', $id ), 'templates' );

	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {
			
		$campos = get_content();	
		
		$db->update('templates', $id, $campos);
		
		update_template_tags();
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';
		$_SESSION['type'] = 'success';

		redirect("templates.php?type={$type}");

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('templates', $id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect("templates.php?type={$type}");
	} 
	
} elseif (has('post')) {

	$campos = get_content();
	
	$id = $db->insert('templates', $campos);
		
	update_templates( $id );
	
	update_template_tags();
	
	$_SESSION['message'] = 'Registro incluido com sucesso.';
	$_SESSION['type'] = 'success';

	redirect("templates.php?type={$type}");

} else {

	$content = $db->content( get_content(false), 'templates' );
	
}

//print_r($content); die();

// Inicia HTML

$webadm->set_page( array( 'name' => ( !$is_new )? "{$template_label}" : "{$template_label}" ) );
$webadm->add_parent( array( 'name' => "{$template_label}s", 'url' => "templates.php"));

//if(!$is_new)$webadm->add_button( array( 'name' => 'Textos', 'icon' => 'mdi mdi-format-list-bulleted-type', 'url' => "template_texts.php?type={$type}&template_id={$id}" ) );
$webadm->add_button( array( 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple', 'url' => "tags.php?content_id={$id}&category={$category_tags}" ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2','tagsinput');
$webadm->start_panel();



?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<style>
#field1, #field2, #field3, #field4, #field5{
	width: 100%;
	height: calc(50vh - 240px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(50vh - 220px);
}

#thumb .dropify-wrapper{
	width: 100%;
	height: calc(50vh - 220px);
}


.cor1 {
    background-color: green;

}
.cor2 {
    background-color: orange;

}
.cor3 {
    background-color: pink;
	
}


#color_me{
  width:250px;   
}

#estilo1 { font-size: 130%; line-height: 120%; font-weight: normal; }
#estilo2 { font-size: 130%; line-height: 120%; font-weight: bold; }
#estilo3 { font-size: 180%; line-height: 120%; font-weight: normal; }
#estilo4 { font-size: 180%; line-height: 120%; font-weight: bold; }
#estilo5 { font-size: 230%; line-height: 120%; font-weight: normal; }
#estilo6 { font-size: 230%; line-height: 120%; font-weight: bold; }
#estilo7 { font-size: 280%; line-height: 100%; font-weight: bold; background-color: green;}

.bootstrap-tagsinput {
	width: 100%;
    min-height: 38px;
    line-height: 30px;
}
</style>

<form action="<?php echo "?type={$type}&id={$id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md">
					<div class="col-md-12">
						<input name="name" type="text" class="form-control" placeholder="<?php echo $template_label; ?>" readonly />
					</div>
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				
				<div class="col-md-6">
				
					<div class="form-group m-b-10">
						<label>Título</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content['name']); ?>" required />
					</div>
					
					<div class="form-group m-b-10">
						<label>Formato</label>
						<select id="format" class="form-control col-md-12 select2" name="format" >
							
							<option value="0" >...</option>
							
							<?php foreach($formats as $format) {
								
								$sel = (strpos($content['format'], "{$format['id']}") !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($format['id']); ?>" $sel ><?php echo html($format['name']); ?></option>
							<?php } ?>
						</select>
					</div>
				
					<div class="row">
					
						<div class="col-md-12">
							<div class="form-group m-b-10">
								<label>Canal</label>
								<select id="channel" class="form-control col-md-12 select2" name="channel" >
									
									<option value="0" >...</option>
									
									<?php foreach($channels as $channel) {
										
										$sel = (strpos($content['channel'], "{$channel['id']}") !== false)? "selected":"";
									?>
										<option <?php echo $sel ?> value="<?php echo html($channel['id']); ?>" $sel ><?php echo html($channel['name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
<!--
						<div class="col-md-3">
							<div class="form-group m-b-10" style="text-align: center;">
								<label>Inclui Logo</label>
								
								<div class="switch" style="margin-top: 5px;">
									<label style="display: inline-flex;">
										<input  name="show_logo" type="checkbox" value='1'
											?php echo $content['show_logo']== '1' ? ' checked' : ''; ?>
										>
										<span class="lever switch-col-light-blue" style="margin-top: 8px;"></span>
									</label>                                
								</div>
								
							</div>	
						</div>
						-->
					</div>	
					
					<div class="form-group m-b-10">
						
							<label>Tipo Template</label>
							<select id="template_type" class="form-control col-md-12 select2" name="template_type" >
								
								<option value="0" >...</option>
								
								<?php foreach($template_types as $template_type) {
									
									$sel = (strpos($content['template_type'], "{$template_type['id']}") !== false)? "selected":"";
								?>
									<option <?php echo $sel ?> value="<?php echo html($template_type['id']); ?>" $sel ><?php echo html($template_type['name']); ?></option>
								<?php } ?>
							</select>
						 
					</div>
						
					<div class="form-group m-b-10">
						<label>Veículos</label>
						<select id="vehicles" class="form-control col-md-12 select2" name="vehicles[]" multiple="multiple">
							<?php 
								$arr_aux = explode(",", $content['vehicles']);
								
								foreach($vehicles as $vehicle) {
								
									$sel = (in_array($vehicle['id'], $arr_aux))? "selected":"";

							?>
								<option <?php echo $sel ?> value="<?php echo html($vehicle['id']); ?>" $sel ><?php echo html($vehicle['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div id="img" class="form-group m-b-0">
								<label>Imagem</label>
								<input name="image" type="file" id="image" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
							</div>	
						</div>	

						<div class="col-md-6">
							<div id="thumb" class="form-group m-b-0">
								<label>Thumbnail</label>
								<input name="thumbnail" type="file" id="thumbnail" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['thumbnail_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
							</div>
						</div>
					</div>
					
				</div>
				
				<input id="guarda_arq"  name="guarda_arq" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
				
				<div class="col-md-6">
					
					<div class="form-group m-b-10">
						<label>Tags</label>
						<select id="tags" class="form-control col-md-12 select2" name="tags[]" multiple="multiple">

							<?php foreach($tags as $tag) {
								$sel = (strpos($content['tags'], $tag['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($tag['id']); ?>" $sel ><?php echo html($tag['name']); ?></option>
							<?php } ?>
							
						</select>
					</div>
					<!--
					<div class="form-group m-b-10">
						<label>Tags de Busca</label>
							<input data-role="tagsinput" name="search_tags" type="text" 
							placeholder="Digite as Tags de Busca" value="?php echo html($content['search_tags']); ?>" />
					</div>
					-->
					<?php if($type == 1) { ?>
						<div class="form-group m-b-10">
							
							<!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#home" role="tab"><span class="hidden-sm-up"><i class="ti-home"></i></span> <span class="hidden-xs-down">Texto 1</span></a> </li>
								<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#profile" role="tab"><span class="hidden-sm-up"><i class="ti-user"></i></span> <span class="hidden-xs-down">Texto 2</span></a> </li>
								<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#messages" role="tab"><span class="hidden-sm-up"><i class="ti-email"></i></span> <span class="hidden-xs-down">Texto 3</span></a> </li>
								<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#texto4" role="tab"><span class="hidden-sm-up"><i class="ti-email2"></i></span> <span class="hidden-xs-down">Texto 4</span></a> </li>
								<li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#texto5" role="tab"><span class="hidden-sm-up"><i class="ti-email2"></i></span> <span class="hidden-xs-down">Texto 5</span></a> </li>
							</ul>
							<!-- Tab panes -->
							<div class="tab-content tabcontent-border">
								<div class="tab-pane p-20 active" id="home" role="tabpanel">
									<!--
									<div class="form-group m-b-20">
										<label>Texto</label>
										<textarea name="field1" type="text" rows="5" class="form-control" placeholder="Digite o Texto">?php echo html($content['field1']); ?></textarea>
									</div>-->
									<div class="form-group m-b-0">
										<label>Texto</label>
										<textarea  id="field1" class="form-control" name="field1"><?php echo $content['field1']; ?></textarea>
									</div>
									<!--
									<div class="row">
										<div class="col-md-6 form-group m-b-10 m-t-10">
											<label>Estilo do Texto</label>
											<select id="field1_style" class="form-control col-md-12 select2" name="field1_style" >
													<option style="" value="0" >. . .</option>
													<option style="font-size: 130%; line-height: 120%; font-weight: normal;" ?php if($content['field1_style']==1)echo "selected"; else echo ""; ?> value="1" >Estilo 1</option>
													<option style="font-size: 130%; line-height: 120%; font-weight: bold;" ?php if($content['field1_style']==2)echo "selected"; else echo ""; ?> value="2" >Estilo 2</option>
													<option style="font-size: 180%; line-height: 120%; font-weight: normal;" ?php if($content['field1_style']==3)echo "selected"; else echo ""; ?> value="3" >Estilo 3</option>
													<option style="font-size: 180%; line-height: 120%; font-weight: bold;" ?php if($content['field1_style']==4)echo "selected"; else echo ""; ?> value="4" >Estilo 4</option>
													<option style="font-size: 230%; line-height: 120%; font-weight: normal;" ?php if($content['field1_style']==5)echo "selected"; else echo ""; ?> value="5" >Estilo 5</option>
													<option style="font-size: 230%; line-height: 120%; font-weight: bold;" ?php if($content['field1_style']==6)echo "selected"; else echo ""; ?> value="6" >Estilo 6</option>
													<option style="font-size: 280%; line-height: 100%; font-weight: bold;" ?php if($content['field1_style']==7)echo "selected"; else echo ""; ?> value="7" >Estilo 7</option>
													
											</select>
										</div>
										
										<div class="col-md-6 form-group m-b-10 m-t-10">
											<label>Cor do Texto</label>
											
											<select class="form-control col-md-12 select2"  id="field1_color" name="field1_color" style="background:?php echo $content['field1_color']; ?> ">
												<option style="background: #EFDF00;" ?php if(empty($content['field1_color']) || $content['field1_color']=='#EFDF00')echo "selected"; else echo ""; ?> value="#EFDF00" >Amarelo</option>
												<option style="background: #FFFFFF;" ?php if($content['field1_color']=='#FFFFFF')echo "selected"; else echo "#FFFFFF"; ?> value="#FFFFFF" >Branco</option>
												<option style="background: #D9D9D6;" ?php if($content['field1_color']=='#D9D9D6')echo "selected"; else echo "#D9D9D6"; ?> value="#D9D9D6" >Cinza Claro</option>
												<option style="background: #BBBCBC;" ?php if($content['field1_color']=='#BBBCBC')echo "selected"; else echo "#BBBCBC"; ?> value="#BBBCBC" >Cinza</option>
												<option style="background: #888B8D;" ?php if($content['field1_color']=='#888B8D')echo "selected"; else echo "#888B8D"; ?> value="#888B8D" >Cinza Escuro</option>
												<option style="background: #000000;" ?php if($content['field1_color']=='#000000')echo "selected"; else echo "#000000"; ?> value="#000000" >Preto</option>
												<option style="background: #707070;" ?php if($content['field1_color']=='#707070')echo "selected"; else echo "#707070"; ?> value="#707070" >Chumbo</option>
											</select>
										</div>
										
									</div>
									-->
									
								</div>
								<div class="tab-pane  p-20" id="profile" role="tabpanel">
									
									<div class="form-group m-b-0">
										<label>Texto</label>
										<div id="editor-container2"></div>
										<textarea  id="field2" class="form-control" name="field2"><?php echo $content['field2']; ?></textarea>
									</div>
									<!--<div class="row">
									<div class="col-md-6 form-group m-b-10 m-t-10">
										<label>Estilo do Texto</label>
										<select id="field2_style" class="form-control col-md-12 select2" name="field2_style" >
												<option style="" value="0" >. . .</option>
												<option style="font-size: 130%; line-height: 120%; font-weight: normal;" <?php if($content['field2_style']==1)echo "selected"; else echo ""; ?> value="1" >Estilo 1</option>
												<option style="font-size: 130%; line-height: 120%; font-weight: bold;" <?php if($content['field2_style']==2)echo "selected"; else echo ""; ?> value="2" >Estilo 2</option>
												<option style="font-size: 180%; line-height: 120%; font-weight: normal;" <?php if($content['field2_style']==3)echo "selected"; else echo ""; ?> value="3" >Estilo 3</option>
												<option style="font-size: 180%; line-height: 120%; font-weight: bold;" <?php if($content['field2_style']==4)echo "selected"; else echo ""; ?> value="4" >Estilo 4</option>
												<option style="font-size: 230%; line-height: 120%; font-weight: normal;" <?php if($content['field2_style']==5)echo "selected"; else echo ""; ?> value="5" >Estilo 5</option>
												<option style="font-size: 230%; line-height: 120%; font-weight: bold;" <?php if($content['field2_style']==6)echo "selected"; else echo ""; ?> value="6" >Estilo 6</option>
												<option style="font-size: 280%; line-height: 100%; font-weight: bold;" <?php if($content['field2_style']==7)echo "selected"; else echo ""; ?> value="7" >Estilo 7</option>
												
										</select>
									</div>
									
									<div class="col-md-6 form-group m-b-10 m-t-10">
										<label>Cor do Texto</label>
										
										<select class="form-control col-md-12 select2"  id="field2_color" name="field2_color" style="background:<?php echo $content['field2_color']; ?> ">
											<option style="background: #EFDF00;" <?php if(empty($content['field2_color']) || $content['field2_color']=='#EFDF00')echo "selected"; else echo ""; ?> value="#EFDF00" >Amarelo</option>
											<option style="background: #FFFFFF;" <?php if($content['field2_color']=='#FFFFFF')echo "selected"; else echo "#FFFFFF"; ?> value="#FFFFFF" >Branco</option>
											<option style="background: #D9D9D6;" <?php if($content['field2_color']=='#D9D9D6')echo "selected"; else echo "#D9D9D6"; ?> value="#D9D9D6" >Cinza Claro</option>
											<option style="background: #BBBCBC;" <?php if($content['field2_color']=='#BBBCBC')echo "selected"; else echo "#BBBCBC"; ?> value="#BBBCBC" >Cinza</option>
											<option style="background: #888B8D;" <?php if($content['field2_color']=='#888B8D')echo "selected"; else echo "#888B8D"; ?> value="#888B8D" >Cinza Escuro</option>
											<option style="background: #000000;" <?php if($content['field2_color']=='#000000')echo "selected"; else echo "#000000"; ?> value="#000000" >Preto</option>
											<option style="background: #707070;" <?php if($content['field2_color']=='#707070')echo "selected"; else echo "#707070"; ?> value="#707070" >Chumbo</option>
										</select>
									</div>
									</div>-->
								</div>
								
								<div class="tab-pane p-20" id="messages" role="tabpanel">
								
									<div class="form-group m-b-0">
										<label>Texto</label>
										<div id="editor-container3"></div>
										<textarea  id="field3" class="form-control" name="field3"><?php echo $content['field3']; ?></textarea>
									</div>
									<!--<div class="row">
									<div class="col-md-6 form-group m-b-10 m-t-10">
										<label>Estilo do Texto</label>
										<select id="field3_style" class="form-control col-md-12 select2" name="field3_style" >
												<option style="" value="0" >. . .</option>
												<option style="font-size: 130%; line-height: 120%; font-weight: normal;" ?php if($content['field3_style']==1)echo "selected"; else echo ""; ?> value="1" >Estilo 1</option>
												<option style="font-size: 130%; line-height: 120%; font-weight: bold;" ?php if($content['field3_style']==2)echo "selected"; else echo ""; ?> value="2" >Estilo 2</option>
												<option style="font-size: 180%; line-height: 120%; font-weight: normal;" ?php if($content['field3_style']==3)echo "selected"; else echo ""; ?> value="3" >Estilo 3</option>
												<option style="font-size: 180%; line-height: 120%; font-weight: bold;" ?php if($content['field3_style']==4)echo "selected"; else echo ""; ?> value="4" >Estilo 4</option>
												<option style="font-size: 230%; line-height: 120%; font-weight: normal;" ?php if($content['field3_style']==5)echo "selected"; else echo ""; ?> value="5" >Estilo 5</option>
												<option style="font-size: 230%; line-height: 120%; font-weight: bold;" ?php if($content['field3_style']==6)echo "selected"; else echo ""; ?> value="6" >Estilo 6</option>
												<option style="font-size: 280%; line-height: 100%; font-weight: bold;" ?php if($content['field3_style']==7)echo "selected"; else echo ""; ?> value="7" >Estilo 7</option>
												
										</select>
									</div>
									
									<div class="col-md-6 form-group m-b-10 m-t-10">
										<label>Cor do Texto</label>
										
										<select class="form-control col-md-12 select2"  id="field3_color" name="field3_color" style="background:?php echo $content['field3_color']; ?> ">
											<option style="background: #EFDF00;" ?php if(empty($content['field3_color']) || $content['field3_color']=='#EFDF00')echo "selected"; else echo ""; ?> value="#EFDF00" >Amarelo</option>
											<option style="background: #FFFFFF;" ?php if($content['field3_color']=='#FFFFFF')echo "selected"; else echo "#FFFFFF"; ?> value="#FFFFFF" >Branco</option>
											<option style="background: #D9D9D6;" ?php if($content['field3_color']=='#D9D9D6')echo "selected"; else echo "#D9D9D6"; ?> value="#D9D9D6" >Cinza Claro</option>
											<option style="background: #BBBCBC;" ?php if($content['field3_color']=='#BBBCBC')echo "selected"; else echo "#BBBCBC"; ?> value="#BBBCBC" >Cinza</option>
											<option style="background: #888B8D;" ?php if($content['field3_color']=='#888B8D')echo "selected"; else echo "#888B8D"; ?> value="#888B8D" >Cinza Escuro</option>
											<option style="background: #000000;" ?php if($content['field3_color']=='#000000')echo "selected"; else echo "#000000"; ?> value="#000000" >Preto</option>
											<option style="background: #707070;" ?php if($content['field3_color']=='#707070')echo "selected"; else echo "#707070"; ?> value="#707070" >Chumbo</option>
											
										</select>
									</div>
									</div>-->
									
								</div>
								
								<div class="tab-pane p-20" id="texto4" role="tabpanel">
								
									<div class="form-group m-b-0">
										<label>Texto</label>
										<div id="editor-container4"></div>
										<textarea  id="field4" class="form-control" name="field4"><?php echo $content['field4']; ?></textarea>
									</div>
									<!--<div class="row">
									<div class="col-md-6 form-group m-b-10 m-t-10">
										<label>Estilo do Texto</label>
										<select id="field4_style" class="form-control col-md-12 select2" name="field4_style" >
												<option style="" value="0" >. . .</option>
												<option style="font-size: 130%; line-height: 120%; font-weight: normal;" ?php if($content['field4_style']==1)echo "selected"; else echo ""; ?> value="1" >Estilo 1</option>
												<option style="font-size: 130%; line-height: 120%; font-weight: bold;" ?php if($content['field4_style']==2)echo "selected"; else echo ""; ?> value="2" >Estilo 2</option>
												<option style="font-size: 180%; line-height: 120%; font-weight: normal;" ?php if($content['field4_style']==3)echo "selected"; else echo ""; ?> value="3" >Estilo 3</option>
												<option style="font-size: 180%; line-height: 120%; font-weight: bold;" ?php if($content['field4_style']==4)echo "selected"; else echo ""; ?> value="4" >Estilo 4</option>
												<option style="font-size: 230%; line-height: 120%; font-weight: normal;" ?php if($content['field4_style']==5)echo "selected"; else echo ""; ?> value="5" >Estilo 5</option>
												<option style="font-size: 230%; line-height: 120%; font-weight: bold;" ?php if($content['field4_style']==6)echo "selected"; else echo ""; ?> value="6" >Estilo 6</option>
												<option style="font-size: 280%; line-height: 100%; font-weight: bold;" ?php if($content['field4_style']==7)echo "selected"; else echo ""; ?> value="7" >Estilo 7</option>
												
										</select>
									</div>
									
									<div class="col-md-6 form-group m-b-10 m-t-10">
										<label>Cor do Texto</label>
										
										<select class="form-control col-md-12 select2"  id="field4_color" name="field4_color" style="background:?php echo $content['field4_color']; ?> ">
										
											<option style="background: #EFDF00;" ?php if(empty($content['field4_color']) || $content['field4_color']=='#EFDF00')echo "selected"; else echo ""; ?> value="#EFDF00" >Amarelo</option>
											<option style="background: #FFFFFF;" ?php if($content['field4_color']=='#FFFFFF')echo "selected"; else echo "#FFFFFF"; ?> value="#FFFFFF" >Branco</option>
											<option style="background: #D9D9D6;" ?php if($content['field4_color']=='#D9D9D6')echo "selected"; else echo "#D9D9D6"; ?> value="#D9D9D6" >Cinza Claro</option>
											<option style="background: #BBBCBC;" ?php if($content['field4_color']=='#BBBCBC')echo "selected"; else echo "#BBBCBC"; ?> value="#BBBCBC" >Cinza</option>
											<option style="background: #888B8D;" ?php if($content['field4_color']=='#888B8D')echo "selected"; else echo "#888B8D"; ?> value="#888B8D" >Cinza Escuro</option>
											<option style="background: #000000;" ?php if($content['field4_color']=='#000000')echo "selected"; else echo "#000000"; ?> value="#000000" >Preto</option>
											<option style="background: #707070;" ?php if($content['field4_color']=='#707070')echo "selected"; else echo "#707070"; ?> value="#707070" >Chumbo</option>

										</select>
									</div>
									</div>
									-->
								</div>
								<div class="tab-pane p-20" id="texto5" role="tabpanel">
								
									<div class="form-group m-b-0">
										<label>Texto</label>
										<div id="editor-container5"></div>
										<textarea  id="field5" class="form-control" name="field5"><?php echo $content['field5']; ?></textarea>
									</div>
									
								</div>
							</div>
						</div>	
					<?php } ?>
					
					<input id="guarda_arq_thumb"  name="guarda_arq_thumb" value="<?php echo html($content['thumbnail']);  ?>"style="display: none;"/>
					
				</div>
				
			</div>
		
			
		</div>
	</div>
</form>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.quilljs.com/1.3.7/quill.bubble.css"></link>
<link rel="stylesheet" type="text/css" href="https://cdn.quilljs.com/1.3.7/quill.snow.css"></link>
<script src="https://cdn.jsdelivr.net/gh/T-vK/DynamicQuillTools@master/DynamicQuillTools.js"></script>

<script src="js/template_edit.js"></script>



<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>