<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');
$quick_view_id = (int)get('quick_view_id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
$categories = $list_categories;

$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 and `category` = 15");
//$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");

//formats
//$json_image_types = $db->get_metadata_value( "image_types" );

//$image_types = current(json_decode($json_image_types, true));


if ($quick_view_id > 0) {
	
	$quick_view_content = $db->content( $quick_view_id, 'quick_views' );

} else {
	//communicateds.php
	//redirect("quick_view_stamps.php?id={$quick_view_id}");
	redirect("quick_views.php");
	
}

function get_content($insertOrUpdate=true) {
		
	global $uploader, $quick_view_id;
	
	$content = array(
		'category' => 15,
		//'image_type' => get_post('image_type'),
		//'category'=> get_post('categories'),
		'text' => get_post('text'),
		'cta' => get_post('cta'),
		'text_highlight' => get_post('text_highlight'),
		//'name' => get_post('name'),
		//'link' => get_post('link'),
		//'open_tags'=> get_post('open_tags'),
		//'version' => get_post('versions'),
		//'color'=> get_post('color'),
		'is_storie' => get_post('is_storie'),
		'quick_view' => $quick_view_id
	);
/*
	if(get_post('guarda_arq')== 0){
		
		$content['image'] = 0;
		
	}*/
	
	if(get_post('guarda_arq2')== 0){
		
		$content['image'] = 0;
		
	}
	
	if(get_post('guarda_arq3')== 0){
		
		$content['image_highlight'] = 0;
		
	}

	if (!$insertOrUpdate) {

		$content['id'] = 0;
		$content['image'] = 0;
		$content['image_highlight'] = 0;
		$content['file'] = 0;
		$content['tags'] = get_post('tags');
		//$content['vehicles'] = get_post('vehicles');
		
	}else{
		
		$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		//$content['vehicles'] = empty(get_post('vehicles'))?"":implode(",", get_post('vehicles'));
	}

	if (has_upload('image')) {
		//print_r($content); die();
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}
	
	if (has_upload('image_highlight')) {
		
		$file_highlight_id = $uploader->upload( get_upload('image_highlight') );

		$content['image_highlight'] = $file_highlight_id;
	}
	
	if (has_upload('file')) {
		
		$file_id = $uploader->upload( get_upload('file') );

		$content['file'] = $file_id;
	}else{
		
		if(get_post('guarda_arq')=='') $content['file'] = 0;
		
	}

	return $content;

}

function update_materials($new_material=null) {
	
	global $db, $quick_view_content;
	
	$materials = array();
	
	$arr =explode(",", $quick_view_content['materials']);
	foreach($arr as $material) {
		
		$content = $db->content( $material, 'quick_view_materials' );		
		
		if ( $content['status'] ) {
		
			array_push( $materials, $content['id'] );
			
		}
		
	}
	if ($new_material) {
		
		array_push( $materials, $new_material );
		
	}
	
	$db->update('quick_views', $quick_view_content['id'], array(
		'materials' => implode(",", $materials)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'quick_view_materials' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {	
	//print_r($content); die();
		$db->update('quick_view_materials', $content_id, get_content());
		
		$save = get_post('save');
//print_r($save); die();		
		if($save){	 
			redirect("quick_view_materials.php?content_id=$quick_view_id", array(
				'message' => 'Material alterado com sucesso.',
				'type' => 'success'
			));
		}else{
			redirect("tags.php?content_id=$content_id&category=5", array(
				'message' => 'Material adicionado com sucesso.',
				'type' => 'success'
			));
		}
		
		

	} elseif (has('toggle_active')) {

		$db->update('quick_view_materials', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("quick_view_materials.php?content_id=$quick_view_id", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('quick_view_materials', get_content());
	
	update_materials( $content_id );
	
	$save = get_post('save');
		
	if($save){
		redirect("quick_view_materials.php?content_id=$quick_view_id", array(
			'message' => 'Publicação adicionado com sucesso.',
			'type' => 'success'
		));
	}else{
		redirect("tags.php?content_id=$content_id&category=5", array(
			'message' => 'Material adicionado com sucesso.',
			'type' => 'success'
		));
	}
	
	
} else {
	
	$content = $db->content( get_content(false), 'quick_view_materials' );
	
}

//print_r($tags); die();
//print_r($content); die();
// View


//$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'tags_btn', 'name' => 'tags_btn', 'value' => '1'), 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple' ) );

$webadm->set_page( array( 'name' => $is_new ? 'Novo Material' : 'Editar Material' ) );
	
//$webadm->add_button( array( 'attribs' => array('id' => 'versions_btn', 'name' => 'versions_btn', 'value' => '2'), 'name' => 'Gerenciar Versões', 'icon' => 'mdi mdi-presentation', 'value' => '2' ) );

//$webadm->add_parent( array( 'name' => 'Galerias WhatsApp', 'url' => 'quick_view_materials.php' ) );

$webadm->add_parent( array( 'name' => 'Fichas de Modelos', 'url' => 'quick_views.php' ) );
$webadm->add_parent( array( 'name' => 'Ficha de Modelo', 'url' => "quick_view_edit.php?id=$quick_view_id"));
$webadm->add_parent( array( 'name' => 'Materiais', 'url' => "quick_view_materials.php?content_id=$quick_view_id" ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'switcher', 'tagsinput');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 625px);
}

#editor-container2{
	width: 100%;
	height: calc(100vh - 625px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: 210px;
}

.bootstrap-tagsinput {
	width: 100%;
    min-height: 38px;
    line-height: 30px;
}
</style>

<form id="target" action="<?php echo "?quick_view_id={$quick_view_id}&id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<!--<div class="col-md-11">
					<input name="name" type="text" class="form-control" placeholder="Nome da Galeria WhatsApp" value="?php echo html($content['name']); ?>" required />
				</div>-->
				<div class="col-md-11">
					<div class="row">
						<div class="col-md-10">
							<input name="name" readonly type="text" class="form-control" placeholder="Materiais" />
						</div>
						
						<div class="col-md-1" style="margin-left: -15px;">
							<div class="switch" style="margin-top: 5px;">
								<label style="display: inline-flex;">
									<input  name="is_storie" type="checkbox" value='1'
										<?php echo $content['is_storie']== '1' ? ' checked' : ''; ?>
									>
									<span class="lever switch-col-light-blue" style="margin-top: 8px;"></span>
									<div style="font-size: 18px;margin-top: 4px;margin-left: 10px;">DESTACAR</div>
								</label>                                
							</div>
						</div>
					</div>
				</div>
				
				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>
				
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-3">
				
					<div class="form-group m-b-10">
						<label>Thumbnail</label>
						<input name="image" type="file" id="image" data-max-file-size="80M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
					</div>
					
					<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
					
					<div class="form-group m-b-0">
						<label>Mensagem</label>
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none;" id="text" name="text"></textarea>
					</div>
					
				</div>
				
				<div class="col-md-3">
				
					<div class="form-group m-b-10">
						<label>Thumbnail Destaque</label>
						<input name="image_highlight" type="file" id="image_highlight" data-max-file-size="80M" 
						class="dropify" data-default-file="<?php echo url( $content['image_highlight_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
					</div>
					
					<input id="guarda_arq3"  name="guarda_arq3" value="<?php echo html($content['image_highlight']);  ?>"style="display: none;"/>
					
					<div class="form-group m-b-0">
						<label>Mensagem Destaque</label>
						<div id="editor-container2"><?php echo $content['text_highlight']; ?></div>
						<textarea style="display: none;" id="text_highlight" name="text_highlight"></textarea>
					</div>
					
				</div>
				
				<div class="col-md-6">
				
					<div class="form-group m-b-10">
						<label>cta</label>
						<input name="cta" type="text" class="form-control" placeholder="Digite o cta" value="<?php echo html($content['cta']); ?>" />
					</div>
				
					<div class="form-group m-b-10">
						<label>Categorias Tags</label>
						<select id="tags" class="form-control col-md-12 select2" name="tags[]" multiple="multiple">
							<?php 
							
								$arr_aux = explode(",", $content['tags']);
								
								foreach($tags as $tag) {
									
									$sel = (in_array($tag['id'], $arr_aux))? "selected":"";
									
								//$sel = ( $content['tags'] == $tag['id'] )? "selected":"";
								//$sel = (strpos($content['tags'], $tag['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($tag['id']); ?>" $sel ><?php echo html($tag['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					<!--
					<div class="form-group m-b-10">
						<label>Tipo de Imagem</label>
						<select id="image_type" class="form-control col-md-12 select2" name="image_type" >
							
							<option value="0" >...</option>
							
							<php foreach($image_types as $image_type) {
								
								$sel = (strpos($content['image_type'], "{$image_type['id']}") !== false)? "selected":"";
							?>
								<option <php echo $sel ?> value="<php echo html($image_type['id']); ?>" $sel ><php echo html($image_type['name']); ?></option>
							<php } ?>
						</select>
					</div>
					-->
				</div>
			</div>
		</div>
	</div>
</form>
<script src="js/whatsapp_gallery_edit.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>