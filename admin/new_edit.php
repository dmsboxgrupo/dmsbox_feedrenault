<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
//$categories = $list_categories;
/*$categories = array_filter($list_categories,
	function($k) {    
		return($k['parent']=='post');
	}
);*/

function get_content($insert=true) {
		
	global $uploader;
	
	$content = array(		
		//'category'=> get_post('categories'),
		'category'=> 3,
		'text' => get_post('text'),
		'text_highlight' => get_post('text_highlight'),
		'link' => get_post('link'),
		'name' => get_post('name'),
		'cta' => get_post('cta'),
		'is_storie' => get_post('is_storie'),
		'label'=> get_post('label'),
		'open_tags'=> get_post('open_tags')
	);
	
	if(get_post('guarda_arq')== 0){
		
		$content['image'] = 0;
		
	}
	
	if(get_post('guarda_arq2')== 0){
		
		$content['image_highlight'] = 0;
		
	}
	
	if(get_post('guarda_arq3')== 0){
		
		$content['tell_client'] = 0;
		
	}
	
	if (!$insert) {
		
		$content['id'] = 0;
		$content['image'] = 0;
		$content['image_highlight'] = 0;
		$content['tell_client'] = 0;
	}

	if (has_upload('image')) {
		
		$image_id = $uploader->upload( get_upload('image') );

		$content['image'] = $image_id;
	}
	
	if (has_upload('image_highlight')) {
		
		$file_highlight_id = $uploader->upload( get_upload('image_highlight') );

		$content['image_highlight'] = $file_highlight_id;
	}
	
	if (has_upload('tell_client')) {
		
		$tell_client_id = $uploader->upload( get_upload('tell_client') );

		$content['tell_client'] = $tell_client_id;
	}

	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'posts' );
	
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}
	
	if( $content['image_highlight'] > 0 ) {
		
		$arquivo2 = $db->content( $content['image_highlight'], 'uploads' );
		
	}
	
	if( $content['tell_client'] > 0 ) {
		
		$arquivo3 = $db->content( $content['tell_client'], 'uploads' );
		
	}

	if (has('post')) {	
	
		$db->update('posts', $content_id, get_content());
		
		$save = get_post('save');
		if($save){	 
			redirect('news.php', array(
				'message' => 'Post alterado com sucesso.',
				'type' => 'success'
			));
		}else{
			redirect("post_topics.php?id={$content_id}", array(
				'message' => 'Publicação adicionado com sucesso.',
				'type' => 'success'
		));
		
	}

	} elseif (has('toggle_active')) {

		$db->update('posts', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('news.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('posts', get_content(true));
	
	$save = get_post('save');
	if($save){	 
		redirect('news.php', array(
			'message' => 'Publicação adicionado com sucesso.',
			'type' => 'success'
		));
	}else{
		redirect("post_topics.php?id={$content_id}", array(
			'message' => 'Publicação adicionado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} else {
	
	$content = $db->content( get_content(false), 'posts' );
	
}

// View
//if ( $content_id > 0 && $list_categories[$content['category']]['name'] == 'Comunicados' )
	
//$webadm->add_button( array( 'attribs' => array( 'id' => 'topics_btn'), 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted', 'url' => "post_topics.php?id=$content_id" ) );
//$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'topics_btn', 'name' => 'topics_btn', 'value' => '1'), 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted') );


$webadm->set_page( array( 'name' => $is_new ? 'Nova Notícia' : 'Editar Notícia' ) );
$webadm->add_parent( array( 'name' => 'Notícias', 'url' => 'news.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'tagsinput');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	min-height: 305px;
	height: calc(50vh - 850px);
}

#editor-container2 {
	width: 100%;
	min-height: 305px;
	height: calc(50vh - 850px);
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	/*height: 210px;*/
	min-height: 223px;
	height: calc(50vh - 850px);
}

.bootstrap-tagsinput {
	width: 100%;
    min-height: 38px;
    line-height: 30px;
}

#tclient .dropify-wrapper{
	width: 100%;
	min-height: 210px;
	height: calc(30vh - 650px);
}
</style>
<form id="target" action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">
						
						<div class="col-md-10">
							<input name="name" type="text" class="form-control" placeholder="Notícias" readonly />
						</div>
						
						<div class="col-md-1" style="margin-left: -15px;">
							<div class="switch" style="margin-top: 5px;">
								<label style="display: inline-flex;">
									<input  name="is_storie" type="checkbox" value='1'
										<?php echo $content['is_storie']== '1' ? ' checked' : ''; ?>
									>
									<span class="lever switch-col-light-blue" style="margin-top: 8px;"></span>
									<div style="font-size: 18px;margin-top: 4px;margin-left: 10px;">DESTACAR</div>
								</label>                                
							</div>
						</div>
					</div>
				</div>
				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
				
					<div class="row form-group m-b-10">
						<div class="col-6">
							<label>Thumbnail</label>
							<input data-show-remove="true" name="image" type="file" id="image" data-max-file-size="100M" 
							class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
						</div>
						<input id="guarda_arq"  name="guarda_arq" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
						
						<div class="col-6">
							<label>Thumbnail Destaque</label>
							<input data-show-remove="true" name="image_highlight" type="file" id="image_highlight" data-max-file-size="100M" 
							class="dropify" data-default-file="<?php echo url( $content['image_highlight_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" />
						</div>
						<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['image_highlight']);  ?>"style="display: none;"/>
						<!--
						<div class="col-6">
							<label>Thumbnail Destaque</label>
							<input data-show-remove="true" name="image" type="file" id="image" data-max-file-size="100M" 
							class="dropify" data-default-file="?php echo "../".$content['image_url']; ?>" data-allowed-file-extensions="jpg jpeg png gif webp" />
						</div>					
						<input id="guarda_arq"  name="guarda_arq" value="?php echo html($content['image']);  ?>"style="display: none;"/>
						
						
						
						<div class="form-group m-b-0">
						<label>Mensagem</label>
						<div id="editor-container"><php echo $content['text']; ?></div>
						<textarea style="display: none" id="text" name="text"></textarea>
					</div>
						-->
						
					</div>

					<div class="row">
					
						<div class="col-6">
							<label>Mensagem</label>
							<div id="editor-container"><?php echo $content['text']; ?></div>
							<textarea style="display: none" id="text" name="text"></textarea>
						</div>
						
						<div class="col-6">
							<label>Mensagem Destaque</label>
							<div id="editor-container2"><?php echo $content['text_highlight']; ?></div>
							<textarea style="display: none" id="text_highlight" name="text_highlight"></textarea>
						</div>
					
					</div>
				</div>
				<div class="col-md-6">
				
					<div class="form-group m-b-10">
						<label>cta</label>
						<input name="cta" type="text" class="form-control" placeholder="Digite o cta" value="<?php echo html($content['cta']); ?>" />
					</div>
				
					<div class="form-group m-b-10">
						<label>Título</label>
						<input name="name" type="text" class="form-control" placeholder="Digite o titulo" value="<?php echo html($content['name']); ?>" />
					</div>
					
					<div class="form-group m-b-10">
						<label>Link</label>
						<input data-bv-uri-allowlocal  name="link" type="url" class="form-control" placeholder="Digite o Link" value="<?php echo html($content['link']); ?>"
						style="max-height:10px;"/>
					</div>
					
					<div class="form-group m-b-10">
						<label>Tags Livres</label>
						<div class="form-group m-b-0">
							<input data-role="tagsinput" name="open_tags" type="text"
							placeholder="Digite as Tags Livres" value="<?php echo html($content['open_tags']); ?>" />
						</div>
					</div>
				
					<div class="form-group m-b-10">
							<label>Categoria</label>
						<div class="form-group m-b-0">
							<select id="label" class="select2" name="label">
								<?php foreach($list_label_news as $list_label) { ?>
									<option <?php 
									
										echo selected( $list_label['name'] == $content['label'] )
									
									?> value="<?php 

										echo html($list_label['name']); 
										
									?>"><?php 
									
										echo html($list_label['name']); 
									
									?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					
					<div id="tclient" class="form-group m-b-10">
						<label>Conte para o seu cliente</label>
						<input data-show-remove="true" name="tell_client" type="file" id="tell_client" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['tell_client_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif pdf" />
					</div>
					<input id="guarda_arq3"  name="guarda_arq3" value="<?php echo html($content['tell_client']);  ?>"style="display: none;"/>
					
					
				</div>
			</div>
		</div>
	</div>
</form>
<script src="js/post_edit.js"></script>

<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>