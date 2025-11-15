<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');
$survey_id = (int)get('survey_id');

$manager_user_logado = $useradm->get_property('id');
//$manager = ($useradm->is_master(LEVEL_MASTER))? "" : "and (user=$manager_user_logado)";

//campo de erro
$error = "";

if ($survey_id > 0) {
	
	$survey_content = $db->content( $survey_id, 'surveys' );
	
} else {
	
	redirect("surveys_answers.php?id={$survey_id}");
	
}

$is_new = $content_id == 0;

//$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 $manager ");

function get_content($insert=true) {
		
	global $is_new, $survey_id;
	
	$content = array(
		'text' => get_post('text')
	);
	
	if($is_new){
		
		$content['survey'] = $survey_id;
	}
	
	if (!$insert) {
		
		$content['id'] = 0;
		
	}else{
		
		//$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		
	}

	/*if (has_upload('image')) {
		
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}*/
	
	return $content;

}
//function update_gallery_image($new_gallery=null) {
function update_survey_answer($new_survey_answer=null) {
	
	global $db, $survey_content;
	
	$surveys = array();
	
	$arr =explode(",", $survey_content['survey_answers']);
	foreach($arr as $survey_answer) {
		
		$content = $db->content( $survey_answer, 'survey_answers' );		
		
		if ( $content['status'] ) {
		
			array_push( $surveys, $content['id'] );
			
		}
		
	}
	if ($new_survey_answer) {
		
		array_push( $surveys, $new_survey_answer );
		
	}
	
	$db->update('surveys', $survey_content['id'], array(
		'survey_answers' => implode(",", $surveys)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'survey_answers' );
	
	if (has('post')) {
		
		$conteudo = get_content();
		
		if(!$conteudo['text']) {
			
			$error = "Resposta deve ser preenchida.";
			
		}else{
			
			$db->update('survey_answers', $content_id, $conteudo);

			$save = get_post('save');
			
			if($save){

				redirect("survey_answers.php?id={$survey_id}", array(
					'message' => 'Resposta alterada com sucesso.',
					'type' => 'success'
				));
				
			}
			
		}

	} elseif (has('toggle_active')) {

		$db->update('survey_answers', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("survey_answers.php?id={$survey_id}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
		
		
	}
	
} elseif (has('post')) {	

	
	$content = get_content(true);
	
	if(!$content['text']) {
		
		$error = "Resposta deve ser preenchida.";

	} else {
		
		$content_id = $db->insert('survey_answers', $content);
		
		update_survey_answer( $content_id );
		
		$save = get_post('save');
			
		if($save){
			
			redirect("survey_answers.php?id={$survey_id}", array(
				'message' => 'Publicação adicionado com sucesso.',
				'type' => 'success'
			));
			
		}
	
	}
	

} else {
	
	$content = $db->content( get_content(false), 'survey_answers' );

}

// View

$webadm->set_page( array( 'name' => $is_new ? 'Nova Resposta' : 'Editar Resposta' ) );

$webadm->add_parent( array( 'name' => 'Enquetes', 'url' => 'surveys.php' ) );

$webadm->add_parent(array( 'name' => 'Enquete', 'url' => "survey_answers.php?id={$survey_id}"));

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'tagsinput', 'switcher');

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
.ql-snow.ql-toolbar button, .ql-snow .ql-toolbar button{
	width: 30px;
	
}

</style>

<form id="target" action="<?php echo "?survey_id={$survey_id}&id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">

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
							<input name="name" type="text" class="form-control" placeholder="Resposta" readonly />
						</div>
						
					</div>
				</div>

				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>

			</div>
		</div>
		<div class="card-body">
		
			<div class="row">
				<div class="col-md-12">		

					<div class="form-group m-b-0">
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none" id="text" name="text" ></textarea>
					</div>
					
				</div>

			</div>
			
		</div>
	</div>
</form>
<script src="js/survey_answer_edit.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>