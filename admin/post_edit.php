<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
//$categories = $list_categories;
/*$categories = array_filter($list_categories,
	function($k) {    
		return($k['parent']=='post');
	}
);*/

function get_content($insert=true) {
		
	global $uploader;
	
	$content = array(		
		//'category'=> get_post('categories'),
		'category'=> 1,
		'text' => get_post('text'),
		'cta' => get_post('cta'),
		'link' => get_post('link'),
		'is_storie' => get_post('is_storie'),
		'file' => 0
	);
	
	if(get_post('guarda_arq')== 0){
		
		$content['image'] = 0;
		
	}
	
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;		
	}

	if (has_upload('image')) {
		
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}

	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'posts' );
	/*
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}*/

	if (has('post')) {	
	
		$db->update('posts', $content_id, get_content());
		
		$save = get_post('save');
		if($save){	 
			redirect('posts.php', array(
				'message' => 'Post alterado com sucesso.',
				'type' => 'success'
			));
		}else{
			redirect("post_topics.php?id={$content_id}", array(
				'message' => 'Publicação adicionado com sucesso.',
				'type' => 'success'
		));
		
	}

	} elseif (has('toggle_active')) {

		$db->update('posts', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('posts.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('posts', get_content(true));
	
	$save = get_post('save');
	if($save){	 
		redirect('posts.php', array(
			'message' => 'Publicação adicionado com sucesso.',
			'type' => 'success'
		));
	}else{
		redirect("post_topics.php?id={$content_id}", array(
			'message' => 'Publicação adicionado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} else {
	
	$content = $db->content( get_content(false), 'posts' );
	
}

// View
//if ( $content_id > 0 && $list_categories[$content['category']]['name'] == 'Comunicados' )
	
//$webadm->add_button( array( 'attribs' => array( 'id' => 'topics_btn'), 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => "post_topics.php?id=$content_id" ) );
//$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'topics_btn', 'name' => 'topics_btn', 'value' => '1'), 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted') );


$webadm->set_page( array( 'name' => $is_new ? 'Nova Social' : 'Editar Social' ) );
$webadm->add_parent( array( 'name' => 'Socials', 'url' => 'posts.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 514px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(100vh - 470px);
}
</style>
<form id="target" action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">
						
						<div class="col-md-10">
							<input name="name" type="text" class="form-control" placeholder="Socials" readonly />
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
							<label>Link</label>
							<input data-bv-uri-allowlocal  name="link" type="url" class="form-control" placeholder="Link, Youtube" value="<?php echo html($content['link']); ?>"
						style="max-height:10px;"/>
					</div>						
					<div class="form-group m-b-0">
						<label>Mensagem</label>
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none" id="text" name="text"></textarea>
					</div>
				</div>
				<div class="col-md-6">
				
					<div class="form-group m-b-10">
						<label>cta</label>
						<input name="cta" type="text" class="form-control" placeholder="Digite o cta" value="<?php echo html($content['cta']); ?>" />
					</div>

					<div class="form-group m-b-0">
						<label>Thumbnail</label>
						<input name="image" type="file" id="image" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
					</div>		
					
					<input id="guarda_arq"  name="guarda_arq" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
					
				</div>
			</div>
		</div>
	</div>
</form>
<script src="js/post_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>