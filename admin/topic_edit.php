<?php

// API

include('includes/common.php');

// Codigo da pagina
/*
$website->need_https();
$website->need_login();

$content_id = (int)get('id');
$technology_id = (int)get('technology');

if ($technology_id > 0) {
	
	$tec_content = $db->content( $db->select_id('technologies', $technology_id), 'technologies' );

} else {
	
	$website->redirect('technologies.php');
	
}

function get_content($insert=true) {
	
	global $technology_id;
	
	$content = array(
		'technology' => $technology_id,
		'name' => $insert ? (get_post('name') ?: 'Sem nome') : '',
		'text' => get_post('text')
	);
	
	return $content;

}

function update_topics($new_topic=null) {
	
	global $db, $tec_content;
	
	$topics = array();
	
	foreach($tec_content['topics'] as $topic) {
		
		$content = $db->content( $topic, 'technology_topics' );
		
		if ( $content['status'] ) {
		
			array_push( $topics, $content['id'] );
			
		}
		
	}
	
	if ($new_topic) {
		
		array_push( $topics, $new_topic );
		
	}
	
	$db->update('technologies', $tec_content['id'], array(
		'topics' => implode( $topics, ',' )
	));
	
}

if ($content_id > 0) {
	
	$content = $db->content( $db->select_id( 'technology_topics', $content_id ), 'technology_topics' );

	if (has('post')) {

		$db->update('technology_topics', $content_id, get_content());

		$website->redirect("technology_topics.php?id={$technology_id}");

	} elseif (has('remove')) {

		$db->update('technology_topics', $content_id, array(
			'status' => 0
		));
		
		update_topics();

		$website->redirect("technology_topics.php?id={$technology_id}");

	}
	
} elseif (has('post')) {

	$content_id = $db->insert('technology_topics', get_content());
	
	update_topics( $content_id );

	$website->redirect("technology_topics.php?id={$technology_id}");

} else {
	
	$content = get_content(false);
	
}


// Inicia HTML
$website->page_info = array('name' => 'Editor de tópico', 'description' => $content['name'] ?: 'Novo tópico');
$website->add_parent('Tecnogias', 'technologies.php');
$website->add_parent($tec_content['name'], "technology_edit.php?id={$technology_id}");
$website->add_parent('Tópicos', "technology_topics.php?id={$technology_id}");
$website->add_plugins('quilljs');
$website->start_panel();
*/

$webadm->set_page( array( 'name' => 'Nova Categoria' ) );
$webadm->add_parent( array( 'name' => 'Categorias', 'url' => 'categories.php' ) );

//$webadm->add_button( array( 'name' => 'Importar Planilha', 'icon' => 'mdi mdi-account-multiple', 'url' => 'user_import.php' ) );

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

<form action="<?php echo "?technology={$technology_id}&id={$content_id}&post"; ?>" method="post">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<input name="name" type="text" class="form-control" placeholder="Nome do tópico" value="<?php echo html( 'nome' ); ?>">
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<div class="flex p-t-0">
					<div class="col-md-12">
						<div class="form-group">
							<div id="editor-container"><?php echo 'text'; ?></div>
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