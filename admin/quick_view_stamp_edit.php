<?php

// API

include('includes/common.php');

// Codigo da pagina
need_login();

$content_id = (int)get('id');
$quick_view_id = (int)get('quick_view_id');

$is_new = $content_id == 0;

if ($quick_view_id > 0) {
	
	$quick_view_content = $db->content( $quick_view_id, 'quick_views' );

} else {
	//communicateds.php
	//redirect("quick_view_stamps.php?id={$quick_view_id}");
	redirect("communicateds.php");
	
}

function get_content($insert=true) {
	
	global $quick_view_id, $uploader;
	
	$content = array(
		'quick_view' => $quick_view_id,
		'name' => $insert ? (get_post('name') ?: 'Sem nome') : '',
		'text' => get_post('text')
	);
	
	if(get_post('guarda_arq2')== 0){
		
		$content['image'] = 0;
		
	}
	
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;		
		//$content['image_url'] = '';	
	}

	if (has_upload('image')) {
		
		$file_id = $uploader->upload( get_upload('image') );
		
		$content['image'] = $file_id;
	}
	
	return $content;

}

function update_stamps($new_stamp=null) {
	
	global $db, $quick_view_content;
	
	$stamps = array();
	
	$arr =explode(",", $quick_view_content['stamps']);
	foreach($arr as $stamp) {
		
		$content = $db->content( $stamp, 'quick_view_stamps' );		
		
		if ( $content['status'] ) {
		
			array_push( $stamps, $content['id'] );
			
		}
		
	}
	if ($new_stamp) {
		
		array_push( $stamps, $new_stamp );
		
	}
	
	$db->update('quick_views', $quick_view_content['id'], array(
		'stamps' => implode(",", $stamps)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($content_id > 0) {
	
	$content = $db->content( $db->select_id( 'quick_view_stamps', $content_id ), 'quick_view_stamps' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}

	if (has('post')) {

		$db->update('quick_view_stamps', $content_id, get_content());
		$_SESSION['message'] = 'Registro alterado com sucesso.';
		$_SESSION['type'] = 'success';

		redirect("quick_view_stamps.php?id={$quick_view_id}");

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('quick_view_stamps', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect("quick_view_stamps.php?id={$quick_view_id}");
	} 
	/*elseif (has('remove')) {

		$db->update('quick_view_stamps', $content_id, array(
			'status' => 0
		));
		
		update_stamps();

		redirect("quick_view_stamps.php?id={$quick_view_id}");

	}*/
	
} elseif (has('post')) {
	
	$content_id = $db->insert('quick_view_stamps', get_content());
	
	update_stamps( $content_id );

	$_SESSION['message'] = 'Registro incluido com sucesso.';
	$_SESSION['type'] = 'success';

	redirect("quick_view_stamps.php?id={$quick_view_id}");

} else {
	
	//$content = get_content(false);
	$content = $db->content( get_content(false), 'quick_view_stamps' );
	
}

// Inicia HTML
//$webadm->set_page( array( 'name' => 'Editor de Selo', 'description' => $content['name'] ?: 'Novo Selo') );
$webadm->set_page( array( 'name' => $is_new ? 'Novo Selo' : 'Editar Selo' ) );

$webadm->add_parent( array( 'name' => 'Fichas de Modelos', 'url' => 'quick_views.php' ) );
$webadm->add_parent( array( 'name' => 'Ficha de Modelo', 'url' => "quick_view_edit.php?id=$quick_view_id"));

$webadm->add_parent(array( 'name' => 'Selos', 'url' => "quick_view_stamps.php?id={$quick_view_id}"));
$webadm->add_plugins('quilljs', 'dropify');
$webadm->start_panel();


//print_r($content);die();
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
	height: calc(100vh - 357px);
}

</style>

<form action="<?php echo "?quick_view_id={$quick_view_id}&id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<input readonly type="text" class="form-control" placeholder="Selo" />
				</div>
				<button id="save" type="submit" class="btn btn-info"><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
		
			
				<div class="flex p-t-0">
					<div class="row">
						<div class="col-md-6">
							
							<div class="form-group m-b-10">
								<label>Nome</label>
								<input name="name" type="text" class="form-control" placeholder="Digite o título" value="<?php echo html( $content['name'] ); ?>">
							</div>	
							
							<div class="form-group m-b-0">
								<label>Descrição</label>
								<div id="editor-container"><?php echo $content['text']; ?></div>
								<textarea style="display: none" id="text" name="text"></textarea>
							</div>
						</div>
						
						<div class="col-md-6">
							<!--
							<div class="form-group m-b-0">
								<label>Imagem</label>
								<input name="image" type="file" id="image" data-max-file-size="100M" 
								class="dropify" data-default-file="<php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
							</div>			
							-->
							<div class="form-group m-b-10">
								<label>Thumbnail</label>
								<input name="image" type="file" id="image" data-max-file-size="80M" 
								class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
							</div>
							
							<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
							
						</div>
						
					</div>
				</div>
			
			
		</div>
	</div>
</form>

<script src="js/quick_view_stamp_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>