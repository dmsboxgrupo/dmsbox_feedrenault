<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
//$categories = $list_categories;

$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 and `category` = 2");
$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");
/*
function up_stamps ( ) {
	
	$stamp_files = get_upload('stamps');
	
	global $db, $content_id, $uploader;

	$stamp_up_ids = array();
	
	foreach($stamp_files['name'] as $key => $file_name) {
		
		$arquivo = array();

		$arquivo['name'] = $stamp_files['name'][$key];
		$arquivo['type'] = $stamp_files['type'][$key];
		$arquivo['tmp_name'] = $stamp_files['tmp_name'][$key];
		$arquivo['error'] = $stamp_files['error'][$key];
		$arquivo['size'] = $stamp_files['size'][$key];
		
		
		
		list($width, $height, $type, $attr) = getimagesize( $arquivo['tmp_name'] );
		
		$file_id = $uploader->upload( $arquivo );
		
		$stamp_up_ids[] = $file_id;

	}

	$quick_view = $db->select_id( 'quick_views', $content_id );
	
	$images_ids = $quick_view['stamps'];
	$images_ids = ($images_ids)? $images_ids . "," : $images_ids;
	$images_ids .= implode(",", $stamp_up_ids);
	
	return $images_ids;
	
}*/


function get_content($insertOrUpdate=true) {
		
	global $uploader;
	
	$content = array(
		'name' => get_post('name'),
		'main_sentence' => get_post('main_sentence'),
		'message' => get_post('message'),
		'strengths' => get_post('strengths'),
		//'attributes' => get_post('attributes'),
		//'stamps' => get_post('stamps'),
		'legal_text' => get_post('legal_text'),
		'vehicle' => get_post('vehicles'),
	);

	if(get_post('guarda_arq2')== 0){
		
		$content['image'] = 0;
		
	}
	
	if(get_post('guarda_arq3')== 0){
		
		$content['image_highlight'] = 0;
		
	}
	
	if (!$insertOrUpdate) {

		$content['id'] = 0;
		$content['stamps'] = 0;
		$content['image'] = 0;
		$content['image_highlight'] = 0;
		/*$content['file'] = 0;
		$content['tags'] = get_post('tags');
		$content['vehicles'] = get_post('vehicles');
		$content['tell_client'] = 0;*/
		
	}else{
		
		/*$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		$content['vehicles'] = empty(get_post('vehicles'))?"":implode(",", get_post('vehicles'));*/
	}
	
	if (has_upload('stamps')) {
		

		//$stamps_ids = up_stamps();

	}
	
	if (has_upload('image')) {
		
		$image_id = $uploader->upload( get_upload('image') );

		$content['image'] = $image_id;
	}
	
	if (has_upload('image_highlight')) {
		
		$file_highlight_id = $uploader->upload( get_upload('image_highlight') );

		$content['image_highlight'] = $file_highlight_id;
	}

	/*
	if (has_upload('file')) {
		
		$file_id = $uploader->upload( get_upload('file') );

		$content['file'] = $file_id;
	}else{
		
		if(get_post('guarda_arq')=='') $content['file'] = 0;
		
	}
	
	if (has_upload('tell_client')) {
		
		$tell_client_id = $uploader->upload( get_upload('tell_client') );

		$content['tell_client'] = $tell_client_id;
	}*/
	
	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'quick_views' );
	/*
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}*/

	if (has('post')) {	
		//$teste = get_content();

		$db->update('quick_views', $content_id, get_content());
		
		$save = get_post('save');
		
		if($save){	 
		
			redirect('quick_views.php', array(
				'message' => 'Ficha de Modelo alterada com sucesso.',
				'type' => 'success'
			));
			
		}else{
			
			redirect("quick_view_stamps.php?id={$content_id}", array(
				'message' => 'Comunicado adicionado com sucesso.',
				'type' => 'success'
			));
			
		}

	} elseif (has('toggle_active')) {

		$db->update('quick_views', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('quick_views.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('quick_views', get_content());
	
	$save = get_post('save');
	
	if($save){	 
	
		redirect('quick_views.php', array(
			'message' => 'Ficha de Modelo adicionada com sucesso.',
			'type' => 'success'
		));
		
	}else{
		
		redirect("quick_view_stamps.php?id={$content_id}", array(
				'message' => 'Comunicado adicionado com sucesso.',
				'type' => 'success'
			));
		
	}
	
	
} else {
	
	$content = $db->content( get_content(false), 'quick_views' );
	
}

// View
/*
$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'materials_btn', 'name' => 'materials_btn', 'value' => '1'), 
												'name' => 'Gerenciar Materiais', 'icon' => 'mdi mdi-format-list-bulleted') );
*/												
if (!$is_new && $useradm->is_level(LEVEL_MASTER)) $webadm->add_button( array( 'name' => 'Gerenciar Materiais', 'icon' => 'mdi mdi-buffer m-r-5', 'url' => "quick_view_materials.php?content_id=$content_id" ) );
												
$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'stamps_btn', 'name' => 'stamps_btn', 'value' => '1'), 
												'name' => 'Gerenciar Selos', 'icon' => 'mdi mdi-format-list-bulleted') );


$webadm->set_page( array( 'name' => $is_new ? 'Nova Ficha de Modelo' : 'Editar Ficha de Modelo' ) );

$webadm->add_parent( array( 'name' => 'Fichas de Modelos', 'url' => 'quick_views.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'switcher');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(50vh - 545px);
	min-height: 265px;
}

#editor-container2 {
	width: 100%;
	height: calc(50vh - 545px);
	min-height: 265px;
}

#editor-container3 {
	width: 100%;
	height: calc(50vh - 545px);
	min-height: 265px;
}

#editor-container4 {
	width: 100%;
	height: calc(50vh - 545px);
	min-height: 265px;
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(100vh - 470px);
}

#tclient .dropify-wrapper{
	width: 100%;
	/*height: 210px;*/
	min-height: 210px;
	height: calc(100vh - 725px);
}
.dropzone{
	width: 100%;
	height: calc(100vh - 440px);
}
</style>
<form id="target" action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">
						
						<div class="col-md-10">
							<input readonly type="text" class="form-control" placeholder="Ficha de Modelo" />
						</div>
						<!--
						<div class="col-md-1" style="margin-left: -15px;">
							<div class="switch" style="margin-top: 5px;">
								<label style="display: inline-flex;">
									<input  name="is_storie" type="checkbox" value='1'
										<php echo $content['is_storie']== '1' ? ' checked' : ''; ?>
									>
									<span class="lever switch-col-light-blue" style="margin-top: 8px;"></span>
									<div style="font-size: 18px;margin-top: 4px;margin-left: 10px;">DESTACAR</div>
								</label>                                
							</div>
						</div>-->
					</div>
				</div>
				
				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>
				
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					
					<div class="row">
					
						<div class="col-md-6">
							<div class="form-group m-b-10">
								<label>Apresentação de Produto</label>
								<div id="editor-container"><?php echo $content['message']; ?></div>
								<textarea style="display: none;" id="message" name="message"></textarea>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group m-b-10">
								<label>Texto Jurídico</label>
								<div id="editor-container3"><?php echo $content['legal_text']; ?></div>
								<textarea style="display: none;" id="legal_text" name="legal_text"></textarea>
							</div>				
						</div>	
						
					</div>
					
					<div class="row">
					
						<div class="col-md-12">
							<div class="form-group m-b-0">
								<label>Mapa de Features</label>
								<div id="editor-container2"><?php echo $content['strengths']; ?></div>
								<textarea style="display: none;" id="strengths" name="strengths"></textarea>
							</div>	
						</div>
						
					</div>
					
				</div>
				
				<div class="col-md-6">
				
					<div class="form-group m-b-10">
						<label>Nome</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o nome" value="<?php echo html($content['name']); ?>" />
					</div>
					
					<div class="form-group m-b-10">
						<label>Veículo</label>
						<select id="vehicles" class="form-control col-md-12 select2" name="vehicles" >
							
							<option value="0" >...</option>
							
							<?php 
							
								$arr_aux = explode(",", $content['vehicle']);
								foreach($vehicles as $vehicle) {
									$sel = (in_array($vehicle['id'], $arr_aux))? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($vehicle['id']); ?>" $sel ><?php echo html($vehicle['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					
					<div class="form-group m-b-10">
						<label>Frase Principal</label>
						<input name="main_sentence" type="text" class="form-control" placeholder="Digite a frase principal" value="<?php echo html($content['main_sentence']); ?>" />
					</div>
					
					<div class="row">
					
						<div class="col-md-6">
							<div class="form-group m-b-10">
								<label>Banner</label>
								<input name="image" type="file" id="image" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp mp4 avi" />
							</div>
							<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
						</div>
						
						
						<div class="col-md-6">
							<div class="form-group m-b-10">
								<label>Thumbnail</label>
								<input name="image_highlight" type="file" id="image_highlight" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['image_highlight_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp  mp4 avi" />
							</div>
							<input id="guarda_arq3"  name="guarda_arq3" value="<?php echo html($content['image_highlight']);  ?>"style="display: none;"/>
						</div>
					
					</div>
					
				</div>
				
			</div>
		</div>
	</div>
</form>
<script src="js/quick_view_edit.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>