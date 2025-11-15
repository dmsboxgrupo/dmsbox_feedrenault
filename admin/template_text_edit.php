<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$type = get('type');
$template_id = (int)get('template_id');
$item_id = (int)get('item_id');


//$type_id=1;
//if($type == 'stl') $type_id=2;

//echo "type= $type"; die();


//$manager_user_logado = $useradm->get_property('id');
//$manager = ($useradm->is_master(LEVEL_MASTER))? "" : "and (user=$manager_user_logado)";

if ($item_id > 0) {
	
	$template_item_content = $db->content( $item_id, 'template_items' );
	
} else {
	
	redirect("templates.php");
	
}
$manager_user_logado = $useradm->get_property('id');
$is_new = $content_id == 0;

//$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 $manager ");
/*
function get_content_url( $query, $id = 0, $q_fname = '', $type = 'image' ) {
	
	$query_fname = "";
	
	$root = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/content/';
	
	if( !empty($q_fname) ){
		
		$query_fname = "&filename=$q_fname";
		
	}
	
	return $root . "/?q={$query}&id={$id}{$query_fname}";
	
}*/

function get_content($insert=true) {
		
	global $is_new, $type, $item_id,$manager_user_logado;
	
	$content = array(
		'name'=> get_post('name'),
		'template_text'=> get_post('template_text'),
		'template_color'=> get_post('template_color')
	);
	
	if (!$insert) {

		$content['id'] = 0;	

	}
	
	if($is_new){
		
		$content['type'] = $type;
		$content['template_item'] = $item_id;
		$content['user'] = $manager_user_logado;
	}
	
	return $content;

}

function update_template_item($new_text=null) {
	
	global $db, $template_item_content;
	
	$template_items = array();
	
	$arr = array_filter(explode(",", $template_item_content['template_item_texts']));
	
	foreach($arr as $template_text) {
		
		$content = $db->content( $template_text, 'template_item_texts' );		
		
		if ( $content['status'] ) {
		
			array_push( $template_items, $content['id'] );
			
		}
		
	}
	
	if ($new_text) {
		
		array_push( $template_items, $new_text );
		
	}
	
	$db->update('template_items', $template_item_content['id'], array(
		'template_item_texts' => implode(",", $template_items)
	));
}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'template_item_texts' );

	if (has('post')) {	
		
		$db->update('template_item_texts', $content_id, get_content());
		
		update_template_item();
		
		redirect("template_texts.php?type={$type}&template_id={$template_id}&item_id={$item_id}", array(
				'message' => 'Registro alterado com sucesso.',
				'type' => 'success'
			));
		
	} elseif (has('toggle_active')) {

		$db->update('template_item_texts', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		update_template_item();
		
		redirect("template_texts.php?type={$type}&template_id={$template_id}&item_id={$item_id}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {

	
	$conteudo = get_content(true);
	
	//print_r($conteudo); die();
	
	$content_id = $db->insert('template_item_texts', $conteudo);
	
	//echo "content_id= $content_id"; die();
	update_template_item( $content_id );
			
	redirect("template_texts.php?type={$type}&template_id={$template_id}&item_id={$item_id}", array(
		'message' => 'Registro adicionado com sucesso.',
		'type' => 'success'
	));
	
} else {

	$content = $db->content( get_content(false), 'template_item_texts' );

}

// View
//print_r($content); die();
$webadm->set_page( array( 'name' => $is_new ? 'Novo Texto' : 'Editar Texto' ) );

/*--- Back ----*/

$webadm->add_parent( array( 'name' => 'Template - Backgrounds', 'url' => 'templates.php' ) );

//$webadm->add_parent( array( 'name' => 'Itens', 'url' => "template_background_items.php?id={$template_id}" ) );

//$webadm->add_parent( array( 'name' => 'Item', 'url' => "template_background_item_edit.php?template_background_id={$template_id}&id=9" ) );

if($type=='stl'){
	
	$webadm->add_parent( array( 'name' => 'Itens', 'url' => "template_styles_items.php?id={$template_id}" ) );
	$webadm->add_parent( array( 'name' => 'Item', 'url' => "template_style_item_edit.php?template_style_id={$template_id}&id={$item_id}" ) );
	
} else {
	
	$webadm->add_parent( array( 'name' => 'Itens', 'url' => "template_background_items.php?id={$template_id}" ) );
	$webadm->add_parent( array( 'name' => 'Item', 'url' => "template_background_item_edit.php?template_background_id={$template_id}&id={$item_id}" ) );
	
}

$webadm->add_parent( array( 'name' => 'Textos', 'url' => "templates.php?type={$type}&template_id={$template_id}&item_id={$item_id}" ) );



/*-------*/
/*
$webadm->add_parent(array( 'name' => 'Carro', 'url' => "car_edit.php?id={$car_id}"));

$webadm->add_parent(array( 'name' => 'Versões', 'url' => "car_versions.php?car_id={$car_id}"));
$webadm->add_parent(array( 'name' => 'Versão', 'url' => "car_version_edit.php?car_id={$car_id}&id={$version_id}"));

$webadm->add_parent(array( 'name' => 'Cores', 'url' => "template_texts.php?car_id={$car_id}&version_id={$version_id}"));*/

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

<form id="target" action="<?php echo "?item_id={$item_id}&type={$type}&template_id={$template_id}&id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">

						<div class="col-md-12">
							<input name="name" type="text" class="form-control" placeholder="Texto" readonly />
						</div>
						
					</div>
				</div>

				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>

			</div>
		</div>
		<div class="card-body">
			<div class="row">
				
				<div class="col-md-12">
				
					<div class="form-group m-b-10">
						<label>Título</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content['name']); ?>" required />
					</div>
				
					<div class="form-group m-b-20">
						<label>Texto</label>
						<textarea name="template_text" type="text" rows="5" class="form-control" placeholder="Digite o Texto"><?php echo html($content['template_text']); ?></textarea>
						
					</div>

					<div class="form-group m-b-10">
						<label>Cor do Texto</label>
						<input  name="template_color"  type="color" value="<?php echo html($content['template_color']); ?>">
					</div>

				</div>
			</div>
		</div>
	</div>
</form>

<script src="js/template_text_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>