<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

//parametros
$id = (int)get('id');

$category_id = (int)get('category');
$category = $list_categories[$category_id];

//variaveis do grupo
$namelist = $category['name_list'];
$url_list = $category['url_list'];

$readonly = "readonly";

$tabela = 	'metadata';
$metadata_id = $db->get_metadata_id( $category['property'] );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("banners.php?category={$category}");
	
}

function get_content($insert=true) {
	
	global $metadata_id, $uploader;
	
	$content = array(
		'category'=> get_post('categories'),
		'metadata' => $metadata_id,
		'name' => $insert ? (get_post('name') ?: 'Sem nome') : '',
		'text' => get_post('text'),
		'link' => get_post('link'),
		//'group' => $group		
	);
	
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

function update_banners($new_topic=null) {
	
	global $db, $root_content;
	
	$banners = array();
	
	$arr =explode(",", $root_content['value']);
	foreach($arr as $banner) {
		
		$content = $db->content( $banner, 'banners' );
		
		if ( $content['status'] ) {
		
			array_push( $banners, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $banners, $new_topic );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $banners)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($id > 0) {
	
	$content = $db->content( $db->select_id( 'banners', $id ), 'banners' );

	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {
		
		$db->update('banners', $id, get_content());
		$_SESSION['message'] = 'Registro alterado com sucesso.';
		$_SESSION['type'] = 'success';

		redirect("banners.php?category={$category_id}");

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('banners', $id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect("banners.php?category={$category_id}");
	} 
	
} elseif (has('post')) {

	$id = $db->insert('banners', get_content());
		
	update_banners( $id );

	$_SESSION['message'] = 'Registro incluido com sucesso.';
	$_SESSION['type'] = 'success';

	redirect("banners.php?category={$category_id}");

} else {

	$content = $db->content( get_content(false), 'banners' );
	
}

// Inicia HTML

$webadm->set_page( array( 'name' => 'Editor de Banner', 'description' => $content['name'] ?: 'Novo Banner') );
$webadm->add_parent( array( 'name' => $namelist, 'url' => $url_list));
$webadm->add_parent(array( 'name' => 'Banners', 'url' => "banners.php?category={$category_id}"));

$webadm->add_plugins('quilljs', 'dropify');
$webadm->start_panel();



?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->

<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 482px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: calc(100vh - 437px);
}

</style>

<form action="<?php echo "?category={$category_id}&id={$id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md">
					<select <?php echo "{$readonly}"; ?>  id="categories" class="form-control select2" name="categories">
						<?php //foreach($categories as $category) { 
						if ($category_id>0) {?>
							<option <?php 
							
								echo selected( $category['id'] == $content['category'] ) 
							
							?> value="<?php 

								echo html($category['id']); 
								
							?>"><?php 
							
								echo html($category['name']); 
							
							?></option>
						<?php } ?>
					</select>
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<div class="flex p-t-0">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group m-b-10">
								<label>Título</label>
								<input name="name" type="text" class="form-control" placeholder="Digite o título" value="<?php echo html( $content['name'] ); ?>">
							</div>							
							<div class="form-group m-b-0">
								<label>Mensagem</label>
								<div id="editor-container"><?php echo $content['text']; ?></div>
								<textarea style="display: none" id="text" name="text"></textarea>
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="form-group m-b-10">
									<label>Link</label>
									<input data-bv-uri-allowlocal  name="link" type="url" class="form-control" placeholder="Digite o Link" value="<?php echo html($content['link']); ?>"
								style="max-height:10px;"/>
							</div>
							<div class="form-group m-b-0">
								<label>Imagem</label>
								<input name="image" type="file" id="image" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<script src="js/banner_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>