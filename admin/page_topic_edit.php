<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

$content_id = (int)get('id');
$page_id = (int)get('page');

if ($page_id > 0) {
	
	$pag_content = $db->content( $page_id, 'pages' );

} else {
	
	//redirect('pages.php');
	redirect("page_topics.php?id={$page_id}");
	
}

function get_content($insert=true) {
	
	global $page_id;
	
	$content = array(
		'page' => $page_id,
		'name' => $insert ? (get_post('name') ?: 'Sem nome') : '',
		'text' => get_post('text')
	);
	
	return $content;

}

function update_topics($new_topic=null) {
	
	global $db, $pag_content;
	
	$topics = array();
	
	$arr =explode(",", $pag_content['topics']);
	foreach($arr as $topic) {
		
		$content = $db->content( $topic, 'page_topics' );		
		
		if ( $content['status'] ) {
		
			array_push( $topics, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $topics, $new_topic );
		
	}
	
	$db->update('pages', $pag_content['id'], array(
		'topics' => implode(",", $topics)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {
	
	$content = $db->content( $db->select_id( 'page_topics', $content_id ), 'page_topics' );

	if (has('post')) {

		$db->update('page_topics', $content_id, get_content());
		$_SESSION['message'] = 'Registro alterado com sucesso.';
		$_SESSION['type'] = 'success';

		redirect("page_topics.php?id={$page_id}");

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('page_topics', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect("page_topics.php?id={$page_id}");
	} 
	/*elseif (has('remove')) {

		$db->update('page_topics', $content_id, array(
			'status' => 0
		));
		
		update_topics();

		redirect("page_topics.php?id={$page_id}");

	}*/
	
} elseif (has('post')) {

	$content_id = $db->insert('page_topics', get_content());
		
	update_topics( $content_id );

	$_SESSION['message'] = 'Registro incluido com sucesso.';
	$_SESSION['type'] = 'success';

	redirect("page_topics.php?id={$page_id}");

} else {
	
	$content = get_content(false);
	
}


// Inicia HTML
//$website->page_info = array('name' => 'Editor de tópico', 'description' => $content['name'] ?: 'Novo tópico');
$webadm->set_page( array( 'name' => 'Editor de tópico', 'description' => $content['name'] ?: 'Novo tópico') );
$webadm->add_parent( array( 'name' => 'Páginas', 'url' => 'pages.php'));
$webadm->add_parent(array( 'name' => $pag_content['name'], 'url' => "page_edit.php?id={$page_id}"));
$webadm->add_parent(array( 'name' => 'Tópicos', 'url' => "page_topics.php?id={$page_id}"));
$webadm->add_plugins('quilljs');
$webadm->start_panel();



?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 416px);
}
.ql-bubble .ql-tooltip {
  z-index: 1;
}
</style>

<form action="<?php echo "?page={$page_id}&id={$content_id}&post"; ?>" method="post">
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
<script>

$(function() {

	$('#text').val( $('#editor-container').html() );

	var toolbarOptions = [
		['bold', 'italic', 'underline', 'strike'],
		['blockquote', 'code-block'],
		
		
		[{ 'color': [ 'black', 'red' ] }, { 'background': [] }],
		[{ 'align': [] }],
		
		['link', 'image'],

		[{ 'header': 1 }, { 'header': 2 }],
		[{ 'list': 'ordered'}, { 'list': 'bullet' }],
		[{ 'script': 'sub'}, { 'script': 'super' }],
		[{ 'indent': '-1'}, { 'indent': '+1' }],
		[{ 'direction': 'rtl' }],

		[{ 'size': ['small', false, 'large', 'huge'] }],
		[{ 'header': [1, 2, 3, 4, 5, 6, false] }],

		['clean']
	];

	var quill = new Quill('#editor-container', {
		modules: {
			toolbar: toolbarOptions
		},
		placeholder: 'Escreva aqui...',
		theme: 'snow'
	});
	
	quill.on('text-change', function() {
		$('#text').val( quill.container.firstChild.innerHTML );
	});

});

</script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>