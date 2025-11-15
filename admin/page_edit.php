<?php

// API

include('includes/common.php');

need_login();
txtLog('_page_Edit',"Inicio"); 
$content_id = (int)get('id');

$sqlTeste= "select id, name from categories where status=1 ";
$categories = $db->query($sqlTeste);

function get_content($insert=true, $is_new=false) {
		
	global $uploader;
	
	$content = array(		
		'category'=> get_post('categories'),
		'name' => get_post('name'),
		'status' => get_post('status'),
		//'image' => get_post('image'),
		'text' => get_post('text'),
		//'link' => get_post('link'),
		'last_modification' => date('Y-m-d\TH:i:sO')
	);
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;		
	}
	/*
	if (has_upload('image')) {
		$content['image'] = $uploader->upload( get_upload('image') );
	}*/
	
	if (has_upload('image')) {
		
		$teste1 =$uploader->upload( get_upload('image') );
		txtLog('_page_Edit',"Inicio  teste1=".$teste1); 
		$content['image'] = $teste1;
	}
	//> image ( $uploader->upload( get_file('image') ) )
	
	
	txtLog('_page_Edit',"Inicio 1"); 
	return $content;

}
/*
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
*/
if ($content_id > 0) {

	//$content = $db->content( $db->select_id( 'pages', $content_id ), 'page' );	
	$content = $db->content(  $content_id , 'pages' );	
	if($content['image']>0)
		$arquivo = $db->content( $content['image'], 'uploads' );

	if (has('post')) {
		$db->update('pages', $content_id, get_content());
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';		
		
		redirect("pages.php");

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('pages', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect('pages.php');
	}
	
} elseif (has('post')) {	
	//get_content(true);
	
	$content_id = $db->insert('pages',  get_content(true, true));
	
	//redirect('pages.php');
	 redirect("page_edit.php?id=$content_id");
	
} else {
	
	$content = get_content(false);
	
}

// Inicia HTML

if ($content_id > 0) {
	//$webadm->add_button('Gerenciar tópicos', "page_topics.php?id={$content_id}", 'mdi mdi-file-document', array( 'style' => 'background-color: #17a2b8; border: 1px solid #17a2b8;' ));
	$webadm->add_button( array( 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => "page_topics.php?id=$content_id" ) );
}

$webadm->set_page( array( 'name' => 'Nova Página' ) );
$webadm->add_parent( array( 'name' => 'Páginas', 'url' => 'pages.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2');
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

.dropify-wrapper{
	width: 100%;
	height: calc(100vh - 375px);
}

</style>

<form action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-4">
					<input name="name" type="text" class="form-control" placeholder="Nome da página" value="<?php echo html($content['name']);  ?>"
					required style="max-height:10px;"/>					
				</div>
				<select id="categories" class="form-control col-md-4 select2" name="categories">
					<?php foreach($categories as $category) {
						$sel = $content['category']==$category['id']? "selected":"";
						//if($content['category']==$category['id'])$sel = "selected";
					?>
						<option <?php echo $sel ?> value="<?php echo html($category['id']); ?>" $sel ><?php echo html($category['name']); ?></option>
					<?php } ?>
				</select>									
				<div class="col-md-4" align="right">
					<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<div class="flex p-t-0">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<div id="editor-container"><?php echo $content['text']; ?></div>
								<textarea style="display: none" id="text" name="text"></textarea>
							</div>
						</div>
						<div class="col-md-6" >
						<!--<label>Imagem</label>
								data-height="271"  data-max-file-size="3M" data-max-width="800" data-max-height="600"
								-->
							<div class="form-group" >
								<input name="image" type="file"  id="image" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php if ($content['image']>0) echo url( "uploads/". $content['image'].".".$arquivo['extension'] ); ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
							</div>							
						</div>
					</div>
					<!--<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								    <input data-bv-uri-allowlocal  name="link" type="url" class="form-control" placeholder="Link" value="?php echo html($content['link']);  ?>"
								style="max-height:10px;"/>
							</div>
						</div>		
					</div>-->
				</div>
			</div>
		</div>
	</div>
</form>
<script>

$(function() {
	/*$(".select2").select2({
		ajax: {
		//url: '/example/api',
		processResults: function (data) {
			  // Transforms the top-level key of the response object from 'items' to 'results'
			  return {
				results: data.items
			  };
			}
		  }
	});*/
	var select2 = $(".select2").select2({tags: true});
	//var select2 = $("combo").select2();
	select2.data('select2').$selection.css('height', '38px');
	select2.data('select2').$selection.css('border', '1px solid #ced4da');
	select2.data('select2').$selection.css('color', '#99abb4');
	//select2.data('select2').$selection.css('padding-bot', '6px');
	/*
	$("#categories").on("change", function (){
		var idcategory = $("#categories").val();
		//alert(idcategory);
		//$("#idcategory").val();
	});*/
	
	
	$('#text').val( $('#editor-container').html() );

	/*var toolbarOptions = [
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
	];*/
	
	var toolbarOptions = [
		['bold', 'italic', 'underline'],
		['link'],
		
		//[{ 'header': 1 }, { 'header': 2 }],
		[{ 'list': 'ordered'}, { 'list': 'bullet' }]
		
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

$(document).ready(function() {
	$('.dropify').dropify({
		tpl: {
			clearButton: ''
		}
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