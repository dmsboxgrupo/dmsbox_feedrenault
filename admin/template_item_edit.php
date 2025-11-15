<?php

// API

include('includes/common.php');

need_login();

// Controller
$template_id = (int)get('template_id');
$type = (int)get('type');
$content_id = (int)get('id');

$type_name = 'Background';
$category_tags = 7;

if($type==2){
	
	$type_name = 'Estilo';
	$category_tags = 8;
	
}

$manager_user_logado = $useradm->get_property('id');

$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 and `category` = {$category_tags}");

//$template_texts = $db->query("SELECT id, name FROM template_item_texts WHERE status = 1 and `type` = {$type}");


if ($template_id > 0) {
	
	$template_content = $db->content( $template_id, 'templates' );

} else {
	
	redirect("template_items.php?id={$template_id}");
	
}

$is_new = $content_id == 0;


function get_content($insert=true) {
		
	global $is_new, $template_id, $uploader;
	
	$content = array(
		'name'=> get_post('name')
	);
	
	
	if(get_post('guarda_arq')== 0){
		
		$content['image'] = 0;
		
	}
	
	if(get_post('guarda_arq_thumb')== 0){
		
		$content['thumbnail'] = 0;
		
	}
	
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;
		$content['thumbnail'] = 0;
		$content['tags'] = get_post('tags');
		//$content['template_item_texts'] = get_post('template_item_texts');
		
	}else{
		
		$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		//$content['template_item_texts'] = empty(get_post('template_item_texts'))?"":implode(",", get_post('template_item_texts'));
		//if(!empty(get_post('template_item_texts_values'))) $content['template_item_texts'] =get_post('template_item_texts_values');
		
	}
	
	if (has_upload('image')) {
		
		$file_id = $uploader->upload( get_upload('image') );

		$content['image'] = $file_id;
	}
	
	if (has_upload('thumbnail')) {
		
		$file_id = $uploader->upload( get_upload('thumbnail') );

		$content['thumbnail'] = $file_id;
	}
	
	if($is_new){
		
		$content['template'] = $template_id;
	}

	return $content;

}

function update_template_item($new_template=null) {
	
	global $db, $template_content;
	
	$templates = array();
	
	$arr =array_filter(explode(",", $template_content['template_items']));
	
	foreach($arr as $template_item) {
		
		$content = $db->content( $template_item, 'template_items' );
		
		if ( $content['status'] ) {
		
			array_push( $templates, $content['id'] );
			
		}
		
	}
	if ($new_template) {
		
		array_push( $templates, $new_template );
		
	}
	
	$db->update('templates', $template_content['id'], array(
		'template_items' => implode(",", $templates)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'template_items' );

	$arr = array_filter(explode(",", $content['template_item_texts']));
	
	if($arr ){

		$template_item_texts = array();

		foreach($arr as $item){
			$name = current($db->query("SELECT name FROM template_item_texts WHERE status = 1 and type={$type} and id={$item}"))['name'] ;		
			array_push( $template_item_texts, array("id" => "$item", "name" => "$name") );
		}
		
		$arr_colors = $template_item_texts;
		
		$template_item_texts = array_merge($template_item_texts, $db->query("SELECT id, name FROM template_item_texts WHERE status = 1 and type={$type} and id not in ({$content['template_item_texts']})"));	
	
	} else {
		
		$template_item_texts = $db->query("SELECT id, name FROM template_item_texts WHERE status = 1 and type={$type} ");	
		
	}
//print_r($template_texts); die();

	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if( $content['thumbnail'] > 0 ) {
		
		$arquivo2 = $db->content( $content['thumbnail'], 'uploads' );
		
	}
	

	if (has('post')) {	
		
		$db->update('template_items', $content_id, get_content());

		redirect("template_items.php?id={$template_id}&type={$type}", array(
			'message' => 'Campanha alterado com sucesso.',
			'type' => 'success'
		));
		
		
		$save = get_post('save');
		//echo "save= $save"; die();
		if($save){	 
			redirect('templates.php', array(
				'message' => 'Item alterado com sucesso.',
				'type' => 'success'
			));
		}else{
			/*redirect("tags.php?content_id=$content_id&category={$category_tags}", array(
				'message' => 'Item alterado com sucesso.',
				'type' => 'success'
			));*/
		}
		

	} elseif (has('toggle_active')) {

		$db->update('template_items', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("template_items.php?id={$template_id}&type={$type}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));

	}
	
} elseif (has('post')) {	

	
	$conteudo = get_content(true);
	
	$content_id = $db->insert('template_items', $conteudo);
	
	update_template_item( $content_id );

	redirect("template_items.php?id={$template_id}&type={$type}", array(
		'message' => 'Publicação adicionado com sucesso.',
		'type' => 'success'
	));

} else {
	
	$content = $db->content( get_content(false), 'template_items' );
	
	$template_item_texts = $db->query("SELECT id, name FROM template_item_texts WHERE status = 1 and type={$type} ");	

}

// View

$webadm->set_page( array( 'name' => $is_new ? 'Novo Item' : 'Editar Item' ) );

//$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'tags_btn', 'name' => 'tags_btn', 'value' => '1'), 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple' ) );

//$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'tags_btn', 'name' => 'tags_btn', 'value' => '1'), 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple' ) );

if(!$is_new && $type==1)$webadm->add_button( array( 'name' => 'Textos', 'icon' => 'mdi mdi-format-list-bulleted-type', 'url' => "template_texts.php?type={$type}&template_id={$template_id}&item_id={$content_id}" ) );

$webadm->add_button( array( 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple', 'url' => "tags.php?content_id=$content_id&template_id=$template_id&category={$category_tags}" ) );

$webadm->add_parent( array( 'name' => "Template - {$type_name}", 'url' => 'templates.php' ) );

$webadm->add_parent(array( 'name' => 'Itens', 'url' => "template_items.php?id={$template_id}&type={$type}"));

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'tagsinput', 'switcher');

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
	height: calc(100vh - 390px);
}

#thumb .dropify-wrapper{
	width: 100%;
	height: calc(100vh - 556px);
}

</style>

<form id="target" action="<?php echo "?type={$type}&template_id={$template_id}&id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">

						<div class="col-md-12">
							<input name="name" type="text" class="form-control" placeholder="<?php echo $type_name; ?>" readonly />
						</div>
						
					</div>
				</div>

				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>

			</div>
		</div>
		
		<div class="card-body">
			<div class="row">
				
				<div class="col-md-6">
					<div id="img" class="form-group m-b-0">
						<label>Imagem</label>
						<input name="image" type="file" id="image" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
					</div>							
				</div>
				
				<input id="guarda_arq"  name="guarda_arq" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
				
				<div class="col-md-6">
					
					<div class="form-group m-b-10">
						<label>Título</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content['name']); ?>" required />
					</div>
					
					<div class="form-group m-b-10">
						<label>Tags</label>
						<select id="tags" class="form-control col-md-12 select2" name="tags[]" multiple="multiple">

							<option value="0" >...</option>

							<?php foreach($tags as $tag) {
								$sel = (strpos($content['tags'], $tag['id']) !== false)? "selected":"";
							?>
								<option <?php echo $sel ?> value="<?php echo html($tag['id']); ?>" $sel ><?php echo html($tag['name']); ?></option>
							<?php } ?>
							
						</select>
					</div>
					
					<!--
					<div class="form-group m-b-10">
						<label>Textos</label>
						<select id="template_texts" class="form-control col-md-12 select2" name="template_texts[]" multiple="multiple">
							?php foreach($template_texts as $template_text) {
								$sel = (strpos($content['template_texts'], $template_text['id']) !== false)? "selected":"";
							?>
								<option ?php echo $sel ?> value="?php echo html($template_text['id']); ?>" $sel >?php echo html($template_text['name']); ?></option>
							?php } ?>
						</select>
					</div>

					<input type="hidden" name="template_texts_values" id="template_texts_values" value="?php echo html($content['template_texts']); ?>" />
-->					
					<div id="thumb" class="form-group m-b-0">
						<label>Thumbnail</label>
						<input name="thumbnail" type="file" id="thumbnail" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['thumbnail_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
					</div>							
					<input id="guarda_arq_thumb"  name="guarda_arq_thumb" value="<?php echo html($content['thumbnail']);  ?>"style="display: none;"/>
					
				</div>
				
				
			</div>
		</div>
	</div>
</form>

<script src="js/template_background_item_edit.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>