<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

$content_id = (int)get('id');
$post_id = (int)get('post_id');

$is_new = $content_id == 0;

if ($post_id > 0) {
	
	$post_content = $db->content( $post_id, 'posts' );

} else {
	//renault_universe.php
	//redirect("post_topics.php?id={$post_id}");
	redirect("renault_universe.php");
	
}

function get_content($insert=true) {
	
	global $post_id;
	
	$content = array(
		'post' => $post_id,
		'name' => $insert ? (get_post('name') ?: 'Sem nome') : '',
		'text' => get_post('text')
	);
	
	return $content;

}

function update_topics($new_topic=null) {
	
	global $db, $post_content;
	
	$topics = array();
	
	$arr =explode(",", $post_content['topics']);
	foreach($arr as $topic) {
		
		$content = $db->content( $topic, 'post_topics' );		
		
		if ( $content['status'] ) {
		
			array_push( $topics, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $topics, $new_topic );
		
	}
	
	$db->update('posts', $post_content['id'], array(
		'topics' => implode(",", $topics)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {
	
	$content = $db->content( $db->select_id( 'post_topics', $content_id ), 'post_topics' );

	if (has('post')) {

		$db->update('post_topics', $content_id, get_content());
		$_SESSION['message'] = 'Registro alterado com sucesso.';
		$_SESSION['type'] = 'success';

		redirect("renault_universe_post_topics.php?id={$post_id}");

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('post_topics', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect("renault_universe_post_topics.php?id={$post_id}");
	} 
	/*elseif (has('remove')) {

		$db->update('post_topics', $content_id, array(
			'status' => 0
		));
		
		update_topics();

		redirect("post_topics.php?id={$post_id}");

	}*/
	
} elseif (has('post')) {

	$content_id = $db->insert('post_topics', get_content());
		
	update_topics( $content_id );

	$_SESSION['message'] = 'Registro incluido com sucesso.';
	$_SESSION['type'] = 'success';

	redirect("renault_universe_post_topics.php?id={$post_id}");

} else {
	
	$content = get_content(false);
	
}

// Inicia HTML
//$webadm->set_page( array( 'name' => 'Editor de tópico', 'description' => $content['name'] ?: 'Novo tópico') );
$webadm->set_page( array( 'name' => $is_new ? 'Novo Tópico' : 'Editar Tópico' ) );

$webadm->add_parent( array( 'name' => 'Universo Renault', 'url' => 'renault_universe.php' ) );
$webadm->add_parent( array( 'name' => 'Universo Renault', 'url' => "renault_universe_edit.php?id=$post_id"));

$webadm->add_parent(array( 'name' => 'Tópicos', 'url' => "renault_universe_post_topics.php?id={$post_id}"));
$webadm->add_plugins('quilljs');
$webadm->start_panel();



?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<style>

#editor-container {
	width: 100%;
	height: calc(100vh - 426px);	
}
.ql-bubble .ql-tooltip {
  z-index: 1;
}


.ql-snow .ql-color-picker .ql-picker-options { 
  width: 172px;
}

.ql-snow .ql-picker.ql-font{
	width: 150px;
}

.ql-editor{	
	font-family: Poppins;
}
</style>

<form action="<?php echo "?post_id={$post_id}&id={$content_id}&post"; ?>" method="post">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<input name="name" type="text" class="form-control" placeholder="Nome do tópico" value="<?php echo html( $content['name'] ); ?>">
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<div class="flex p-t-0">
					<div class="col-md-12">
						<div class="form-group">
							<div id="editor-container"><?php echo $content['text']; ?></div>
							<textarea style="display: none" id="text" name="text"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<script src="js/post_topic_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>