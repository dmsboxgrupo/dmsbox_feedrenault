<?php

// API

include('includes/common.php');

need_login();

// Controller
$faq_group_id = (int)get('faq_group_id');
$faq_group_topic_id = (int)get('faq_group_topic_id');
$faq_group_topic_item_id = (int)get('id');

//echo "faq_group_id= ".$faq_group_id;
//echo " -/ faq_group_topic_id= ". $faq_group_topic_id;


//$manager_user_logado = $useradm->get_property('id');
//$manager = ($useradm->is_master(LEVEL_MASTER))? "" : "and (user=$manager_user_logado)";

//campo de erro
$error = "";

if ($faq_group_topic_id > 0) {
	
	$faq_group_topics_content = $db->content( $faq_group_topic_id, 'faq_group_topics' );
	
} else {
	
	redirect("faq_group_topics.php?faq_group_id={$faq_group_id}&id={$faq_group_topic_id}");
	
}

$is_new = $faq_group_topic_item_id == 0;

//$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 $manager ");

function get_content($insert=true) {
		
	global $is_new, $faq_group_id, $faq_group_topic_id, $uploader;
	
	$content = array(
		'title' => get_post('title'),
		'text' => get_post('text')
	);
	
	if($is_new){

		$content['faq_group'] = $faq_group_id;
		$content['faq_group_topic'] = $faq_group_topic_id;

	}
	
	if(get_post('guarda_arq')== 0){
		
		$content['image'] = 0;
		
	}
	
	if (!$insert) {

		$content['id'] = 0;
		$content['image'] = 0;
		
	}
	
	if (has_upload('image')) {
		
		$image_id = $uploader->upload( get_upload('image') );

		$content['image'] = $image_id;
	}
	
	return $content;

}
//function update_gallery_image($new_gallery=null) {
function update_faq_group_topic_item($new_faq_group_topic_item=null) {
	
	global $db, $faq_group_topics_content;
	
	$faq_group_topics = array();
	
	$arr =explode(",", $faq_group_topics_content['faq_group_topic_items']);
	foreach($arr as $faq_group_topic_item) {
		
		$content = $db->content( $faq_group_topic_item, 'faq_group_topic_items' );		
		
		if ( $content['status'] ) {
		
			array_push( $faq_group_topics, $content['id'] );
			
		}
		
	}
	if ($new_faq_group_topic_item) {
		
		array_push( $faq_group_topics, $new_faq_group_topic_item );
		
	}
	
	$db->update('faq_group_topics', $faq_group_topics_content['id'], array(
		'faq_group_topic_items' => implode(",", $faq_group_topics)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($faq_group_topic_item_id > 0) {

	$content = $db->content( $faq_group_topic_item_id , 'faq_group_topic_items' );
	
	if (has('post')) {
		
		$conteudo = get_content();
		
		if(!$conteudo['text']) {
			
			$error = "Resposta deve ser preenchida.";
			
		}else{
			
			$db->update('faq_group_topic_items', $faq_group_topic_item_id, $conteudo);

			$save = get_post('save');
			
			if($save){

				redirect("faq_group_topic_items.php?faq_group_id={$faq_group_id}&id={$faq_group_topic_id}", array(
					'message' => 'Resposta alterada com sucesso.',
					'type' => 'success'
				));
				
			}
			
		}

	} elseif (has('toggle_active')) {

		$db->update('faq_group_topic_items', $faq_group_topic_item_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("faq_group_topic_items.php?faq_group_id={$faq_group_id}&id={$faq_group_topic_id}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
		
		
	}
	
} elseif (has('post')) {	

	
	$content = get_content(true);
	//print_r($content); die();
	if(!$content['title']) {
		
		$error = "Pergunta deve ser preenchida.";

	} else {
		
		$faq_group_topic_item_id = $db->insert('faq_group_topic_items', $content);
		
		update_faq_group_topic_item( $faq_group_topic_item_id );
		
		$save = get_post('save');
			
		if($save){
			
			redirect("faq_group_topic_items.php?faq_group_id={$faq_group_id}&id={$faq_group_topic_id}", array(
				'message' => 'Publicação adicionado com sucesso.',
				'type' => 'success'
			));
			
		}
	
	}
	

} else {
	
	$content = $db->content( get_content(false), 'faq_group_topic_items' );

}

//print_r($content); die();

// View

$webadm->set_page( array( 'name' => $is_new ? 'Nova Pergunta' : 'Editar Pergunta' ) );

$webadm->add_parent( array( 'name' => 'Grupos FAQ', 'url' => 'faq_groups.php' ) );

$webadm->add_parent( array( 'name' => 'Grupo FAQ', 'url' => "faq_group_topics.php?id=$faq_group_id" ) );

$webadm->add_parent( array( 'name' => 'Tópico', 'url' => "faq_group_topic_items.php?faq_group_id=$faq_group_id&id=$faq_group_topic_id" ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'tagsinput', 'switcher');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(50vh - 300px);
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
.ql-snow.ql-toolbar button, .ql-snow .ql-toolbar button{
	width: 30px;
	
}

</style>

<form id="target" action="<?php echo "?faq_group_id={$faq_group_id}&faq_group_topic_id={$faq_group_topic_id}&id={$faq_group_topic_item_id}&post"; ?>" method="post" enctype="multipart/form-data">

	<?php if ( $error ) { ?>
		<div class="alert alert-danger">
	<?php echo html( $error ); ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
		</div>
	<?php } ?>

	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">

						<div class="col-md-12">
							<input name="name" type="text" class="form-control" placeholder="Pergunta" readonly />
						</div>
						
					</div>
				</div>

				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>

			</div>
		</div>
		<div class="card-body">
		
			<div class="row">
				<div class="col-md-12">		
	<!--
					<div class="form-group m-b-10">
						<label>Resposta</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o Título" value="<php echo html($content['name']); ?>" required />
						<div id="editor-container"><php echo $content['text']; ?></div>
						<textarea style="display: none" id="text" name="text" ></textarea>
					</div>
					-->

					<div class="form-group m-b-10">
						<label>Pergunta</label>
						<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content['title']); ?>" required />
					</div>
					<!--
					<div class="form-group m-b-0">
						<label>Resposta</label>
						<input name="text" type="textarea" class="form-control" placeholder="Digite o Título" value="<?php echo html($content['title']); ?>" required />
					</div>-->
					<!--
					<div class="form-group m-b-0">
						<label>Thumbnail</label>
						<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="?php echo html($content['title']); ?>" required />
					</div>-->
					
					<div class="form-group m-b-10">
						<label>Resposta</label>
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none;" id="text" name="text"></textarea>
					</div>
					
					<div class="form-group m-b-10">
						<label>Thumbnail</label>
						<input name="image" type="file" id="image" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
					</div>
					<input id="guarda_arq"  name="guarda_arq" value="<?php echo html($content['image']);  ?>"style="display: none;"/>		
					
					
				</div>

			</div>
			
		</div>
	</div>
</form>
<script src="js/faq_group_topic_item_edit.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>