<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
//$categories = $list_categories;

//$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 and `category` = 2");
//$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");




function get_content($insertOrUpdate=true) {
		
	global $uploader;
	
	$content = array(
		//'category' => 14,
		//'category'=> get_post('categories'),
		//'text' => get_post('text'),
		//'text_highlight' => get_post('text_highlight'),
		'name' => get_post('name')
		//'link' => get_post('link'),
		//'label' => get_post('label'),
		//'search_news' => get_post('search_news'),
		//'is_storie' => get_post('is_storie'),
		//'search_galeries' => get_post('search_galeries')
	);


	/*if(get_post('guarda_arq3')== 0){
		
		$content['tell_client'] = 0;
		
	}
	
	if(get_post('guarda_arq4')== 0){
		
		$content['thumbnail'] = 0;
		
	}
	
	if(get_post('guarda_arq2')== 0){
		
		$content['image'] = 0;
		
	}*/
	
	if(get_post('guarda_arq2')== 0){
		
		$content['file'] = 0;
		
	}
	
	if(get_post('guarda_arq4')== 0){
		
		$content['thumbnail'] = 0;
		
	}
	
	if (!$insertOrUpdate) {

		$content['id'] = 0;
		//$content['image'] = 0;
		$content['file'] = 0;
		//$content['tags'] = get_post('tags');
		//$content['vehicles'] = get_post('vehicles');
		//$content['tell_client'] = 0;
		$content['thumbnail'] = 0;
		
	}

	
	
	if (has_upload('file')) {
		
		$file_id = $uploader->upload( get_upload('file') );

		$content['file'] = $file_id;
		
	}else{
		
		if(get_post('guarda_arq2')=='') $content['file'] = 0;
		
	}
	
	if (has_upload('thumbnail')) {
		
		$thumbnail_id = $uploader->upload( get_upload('thumbnail') );

		$content['thumbnail'] = $thumbnail_id;
	}
	
	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'tutorials' );
	/*
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}*/

	if (has('post')) {	
	
		$db->update('tutorials', $content_id, get_content());
		
		$save = get_post('save');
		
		//if($save){	 
			redirect('tutorials.php', array(
				'message' => 'Tutorial alterado com sucesso.',
				'type' => 'success'
			));
		/*}else{
			redirect("post_topics.php?id={$content_id}", array(
				'message' => 'Tutorial adicionado com sucesso.',
				'type' => 'success'
			));
		}*/

	} elseif (has('toggle_active')) {

		$db->update('tutorials', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('tutorials.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('tutorials', get_content());
	
	$save = get_post('save');
	
	//if($save){	 
		redirect('tutorials.php', array(
			'message' => 'Tutorial adicionado com sucesso.',
			'type' => 'success'
		));
	/*}else{
		redirect("post_topics.php?id={$content_id}", array(
			'message' => 'Tutorial adicionado com sucesso.',
			'type' => 'success'
		));
		
	}*/
	
	
} else {
	
	$content = $db->content( get_content(false), 'tutorials' );
	
}

// View

//$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'topics_btn', 'name' => 'topics_btn', 'value' => '1'), 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted') );

$webadm->set_page( array( 'name' => $is_new ? 'Novo Tutorial' : 'Editar Tutorial' ) );

$webadm->add_parent( array( 'name' => 'Tutoriais', 'url' => 'tutorials.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'switcher');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 620px);
	min-height: 315px;
}

#editor-container2 {
	width: 100%;
	height: calc(100vh - 620px);
	min-height: 315px;
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: 210px;
}

#tclient .dropify-wrapper{
	width: 100%;
	/*height: 210px;*/
	min-height: 210px;
	height: calc(100vh - 725px);
}
</style>
<form id="target" action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
			
				<div class="col-md-11">
					
					<input readonly type="text" class="form-control" placeholder="Tutorial" />
						
				</div>
				
				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>
				
			</div>
		</div>
		
		<div class="card-body">
			
				
					<div class="form-group m-b-10">
						<label>Nome</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o nome" value="<?php echo html($content['name']); ?>" />
					</div>
					
					<div class="row">
						<div class="col-md-6">
						
							<div class="form-group m-b-10">
								<label>Thumbnail</label>
								<input name="thumbnail" type="file" id="thumbnail" data-max-file-size="80M" 
								class="dropify" data-default-file="<?php echo url( $content['thumbnail_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
							</div>
						
							<input id="guarda_arq4"  name="guarda_arq4" value="<?php echo html($content['thumbnail']);  ?>"style="display: none;"/>
						
						</div>
						
						<div class="col-md-6">
						
							<div class="form-group m-b-10">
								<label>Arquivo</label>
								<input name="file" type="file" id="file" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['file_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp mp4 avi" />
							</div>
							<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['file']);  ?>"style="display: none;"/>
						
						</div>
					</div>
					
					
				
		</div>
	</div>
</form>
<script src="js/tutorial_edit.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>