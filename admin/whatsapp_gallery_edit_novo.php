<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
$categories = $list_categories;

$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 and `category` = 5");
$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");

$versions = $db->query("SELECT id, name FROM versions WHERE status = 1 ");

function get_content($insertOrUpdate=true) {
		
	global $uploader;
	
	$content = array(
		'category' => 5,
		//'category'=> get_post('categories'),
		'text' => get_post('text'),
		'name' => get_post('name'),
		'link' => get_post('link'),
		'open_tags'=> get_post('open_tags'),
		'version' => get_post('versions'),
		'color'=> get_post('color'),
		'is_storie' => get_post('is_storie')
	);

	if (!$insertOrUpdate) {

		$content['id'] = 0;
		$content['image'] = 0;
		$content['file'] = 0;
		$content['tags'] = get_post('tags');
		$content['vehicles'] = get_post('vehicles');
		
	}else{
		
		$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		$content['vehicles'] = empty(get_post('vehicles'))?"":implode(",", get_post('vehicles'));
	}

	if (has_upload('image')) {
		//print_r($content); die();
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}
	
	if (has_upload('file')) {
		
		$file_id = $uploader->upload( get_upload('file') );

		$content['file'] = $file_id;
	}else{
		
		if(get_post('guarda_arq')=='') $content['file'] = 0;
		
	}

	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'posts' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {	
	//print_r($content); die();
		$db->update('posts', $content_id, get_content());
		
		$save = get_post('save');
//print_r($save); die();		
		if($save){	 
			redirect('whatsapp_galeries.php', array(
				'message' => 'Galeria WhatsApp alterada com sucesso.',
				'type' => 'success'
			));
		}else{
			redirect("tags.php?content_id=$content_id&category=5", array(
				'message' => 'Galeria WhatsApp adicionada com sucesso.',
				'type' => 'success'
			));
		}
		
		

	} elseif (has('toggle_active')) {

		$db->update('posts', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('whatsapp_galeries.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('posts', get_content());
	
	$save = get_post('save');
		
	if($save){
		redirect('whatsapp_galeries.php', array(
			'message' => 'Publicação adicionado com sucesso.',
			'type' => 'success'
		));
	}else{
		redirect("tags.php?content_id=$content_id&category=5", array(
			'message' => 'Galeria WhatsApp adicionada com sucesso.',
			'type' => 'success'
		));
	}
	
	
} else {
	
	$content = $db->content( get_content(false), 'posts' );
	
}

//print_r($tags); die();
//print_r($content); die();
// View


$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'tags_btn', 'name' => 'tags_btn', 'value' => '1'), 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple' ) );

$webadm->set_page( array( 'name' => $is_new ? 'Nova Galeria WhatsApp' : 'Editar Galeria WhatsApp' ) );


if (!$is_new && $useradm->is_level(LEVEL_MASTER)) $webadm->add_button( array( 'name' => 'Gerenciar Versões', 'icon' => 'mdi mdi-buffer m-r-5', 'url' => "versions.php?content_id=$content_id" ) );

if (!$is_new && $useradm->is_level(LEVEL_MASTER)) $webadm->add_button( array( 'name' => 'Gerenciar Veículo Versão', 'icon' => 'mdi mdi-buffer m-r-5', 'url' => "vehicle_version.php?content_id=$content_id" ) );
	
//$webadm->add_button( array( 'attribs' => array('id' => 'versions_btn', 'name' => 'versions_btn', 'value' => '2'), 'name' => 'Gerenciar Versões', 'icon' => 'mdi mdi-presentation', 'value' => '2' ) );

$webadm->add_parent( array( 'name' => 'Galerias WhatsApp', 'url' => 'whatsapp_galeries.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'switcher', 'tagsinput');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 700px);
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
<form id="target" action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<!--<div class="col-md-11">
					<input name="name" type="text" class="form-control" placeholder="Nome da Galeria WhatsApp" value="?php echo html($content['name']); ?>" required />
				</div>-->
				<div class="col-md-11">
					<div class="row">
						<div class="col-md-10">
							<input name="name" readonly type="text" class="form-control" placeholder="Galeria WhatsApp" />
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
				<div class="col-md-6">
					<div class="form-group m-b-10">
						<label>Thumbnail</label>
						<input name="image" type="file" id="image" data-max-file-size="80M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
					</div>
					<div class="form-group m-b-0">
						<label>Mensagem</label>
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none;" id="text" name="text"></textarea>
					</div>
				</div>
				<div class="col-md-6">
				
					<div class="form-group m-b-10">
						<label>Veículos</label>
						<select id="vehicles" class="form-control col-md-12 select2" name="vehicles[]" multiple="multiple">
							<?php 
								$arr_aux = explode(",", $content['vehicles']);
								
								foreach($vehicles as $vehicle) {
								
									$sel = (in_array($vehicle['id'], $arr_aux))? "selected":"";
								//$sel = ( $content['vehicles'] == $vehicle['id'] )? "selected":"";
								//$sel = (strpos($content['vehicles'], $vehicle['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($vehicle['id']); ?>" $sel ><?php echo html($vehicle['name']); ?></option>
							<?php } ?>
						</select>
					</div>
					<!--
					<div class="form-group m-b-10">
						<label>Versão</label>
						<input data-bv-uri-allowlocal name="version" type="text" class="form-control" placeholder="Insira a versão" 
							value="<php echo html($content['version']); ?>"
						style="max-height:10px;"/>
					</div>-->
					
					<div class="form-group m-b-10">
						<label>Versão</label>
						<select id="versions" class="form-control col-md-12 select2" name="versions" >
							
							<option value="0" >...</option>
							
							<?php foreach($versions as $version) {
								$sel = (strpos($content['version'], $version['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($version['id']); ?>" $sel ><?php echo html($version['name']); ?></option>
							<?php } ?>
						</select>
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
					<div class="form-group m-b-10">
						<label>Visualizar PDF</label>
						<!--<input type="file" class="form-control" id="file" name="file" value="?php echo url( $content['file_url'] ) ?>" placeholder="Selecione um arquivo PDF">-->
						<div  id="up_file" >
							<div class="<?php echo $content['file']==0 ? 'fileinput fileinput-new input-group' : 'fileinput input-group fileinput-exists'; ?>"
							data-provides="fileinput">
								<div class="form-control" data-trigger="fileinput">
									<i class="fa fa-file fileinput-exists"></i>
									<span class="fileinput-filename" style="max-width:100%">
										<?php echo html($content['file_name']); ?>
									</span>
								</div>
								<span class="input-group-addon btn btn-secondary btn-file"> 
								<span class="fileinput-new">Selecione</span>
								<span class="fileinput-exists">Alterar</span>
								<input name="file" type="file" id="file_teste" data-max-file-size="100M" 
									data-default-file="<?php echo url( $content['file_url'] ) ?>" data-allowed-file-extensions="pdf" >
								</span>
								<a href="#" class="input-group-addon btn btn-secondary fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
						</div>
					</div>
					<input id="guarda_arq"  name="guarda_arq" 
									value="<?php echo html($content['file_name']);  ?>"style="display: none;"/>			
					
					<div class="form-group m-b-10">
						<label>Tags Livres</label>
						<div class="form-group m-b-0">
							<input data-role="tagsinput" name="open_tags" type="text"
							placeholder="Digite as Tags Livres" value="<?php echo html($content['open_tags']); ?>" />
						</div>
					</div>
					
					<div class="form-group m-b-10">
						<label>Link</label>
						<input data-bv-uri-allowlocal name="link" type="url" class="form-control" placeholder="Insira um link" 
							value="<?php echo html($content['link']); ?>"
						style="max-height:10px;"/>
					</div>
					
					<div class="col-md-4">
						<div class="row">
							<label>Cor Fundo</label>
						</div>
						<div class="row">
							<input  name="color"  type="color" value="<?php echo html($content['color']); ?>">
						</div>
					</div>
					
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