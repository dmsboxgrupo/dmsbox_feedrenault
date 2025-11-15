<?php

// API

include('includes/common.php');

need_login();

// Controller
$content_id = (int)get('id');

$is_new = $content_id == 0;

//$categories = $db->query("SELECT id, name FROM categories WHERE status = 1");
//$categories = $list_categories;

$tags = $db->query("SELECT id, name FROM tags WHERE status = 1 and `category` = 2");
$vehicles = $db->query("SELECT id, name FROM vehicles WHERE status = 1");




function get_content($insertOrUpdate=true) {
		
	global $uploader;
	
	$content = array(
		'category' => 2,
		//'category'=> get_post('categories'),
		'text' => get_post('text'),
		'cta' => get_post('cta'),
		'text_highlight' => get_post('text_highlight'),
		'name' => get_post('name'),
		'link' => get_post('link'),
		'label' => get_post('label'),
		'search_news' => get_post('search_news'),
		'is_storie' => get_post('is_storie'),
		'content_type'=> get_post('content_type'),
		'search_galeries' => get_post('search_galeries')
	);


	if(get_post('guarda_arq3')== 0){
		
		$content['tell_client'] = 0;
		
	}
	
	if(get_post('guarda_arq4')== 0){
		
		$content['image_highlight'] = 0;
		
	}
	
	if(get_post('guarda_arq2')== 0){
		
		$content['image'] = 0;
		
	}
	/*
	if(get_post('guarda_arq')== 0){
		
		$content['file'] = 0;
		
	}*/
	
	if (!$insertOrUpdate) {

		$content['id'] = 0;
		$content['image'] = 0;
		$content['file'] = 0;
		$content['tags'] = get_post('tags');
		$content['vehicles'] = get_post('vehicles');
		$content['tell_client'] = 0;
		$content['image_highlight'] = 0;
		
	}else{
		
		$content['tags'] = empty(get_post('tags'))?"":implode(",", get_post('tags'));
		$content['vehicles'] = empty(get_post('vehicles'))?"":implode(",", get_post('vehicles'));
	}

	if (has_upload('image')) {
		
		$image_id = $uploader->upload( get_upload('image') );

		$content['image'] = $image_id;
	}
	
	if (has_upload('image_highlight')) {
		
		$file_highlight_id = $uploader->upload( get_upload('image_highlight') );

		$content['image_highlight'] = $file_highlight_id;
	}
	
	if (has_upload('file')) {
		
		$file_id = $uploader->upload( get_upload('file') );

		$content['file'] = $file_id;
		$content['link'] = '';
		
	}else{
		if(get_post('guarda_arq')=='' || $content['content_type']==1) {
			$content['file'] =0;
			if($content['content_type']==2) $content['content_type']=0;
		}
	}
	
	
	if (has_upload('tell_client')) {
		
		$tell_client_id = $uploader->upload( get_upload('tell_client') );

		$content['tell_client'] = $tell_client_id;
	}
	
	return $content;

}

if ($content_id > 0) {

	$content = $db->content( $content_id , 'posts' );
	/*
	if( $content['image'] > 0 ) {
		
		$arquivo = $db->content( $content['image'], 'uploads' );
		
	}*/
	
	if($content['content_type']==1){
		
		$hide_upload = 'display: none;';
		$hide_link = '';
		
	} else{
		
		$hide_upload = '';
		$hide_link = 'display: none;';
		
	}

	if (has('post')) {	
	
		$db->update('posts', $content_id, get_content());
		
		$save = get_post('save');
		
		if($save){	 
			redirect('communicateds.php', array(
				'message' => 'Comunicado alterado com sucesso.',
				'type' => 'success'
			));
		}else{
			redirect("post_topics.php?id={$content_id}", array(
				'message' => 'Comunicado adicionado com sucesso.',
				'type' => 'success'
			));
		}

	} elseif (has('toggle_active')) {

		$db->update('posts', $content_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect('communicateds.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {	

	$content_id = $db->insert('posts', get_content());
	
	$save = get_post('save');
	
	if($save){	 
		redirect('communicateds.php', array(
			'message' => 'Comunicado adicionado com sucesso.',
			'type' => 'success'
		));
	}else{
		redirect("post_topics.php?id={$content_id}", array(
			'message' => 'Comunicado adicionado com sucesso.',
			'type' => 'success'
		));
		
	}
	
	
} else {
	
	$content = $db->content( get_content(false), 'posts' );
	
}

if($is_new){
	
	$hide_upload = 'display: none;';
	
	$hide_link = '';
}

// View

$webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'topics_btn', 'name' => 'topics_btn', 'value' => '1'), 'name' => 'Gerenciar tópicos', 'icon' => 'mdi mdi-format-list-bulleted') );

$webadm->set_page( array( 'name' => $is_new ? 'Novo Comunicado' : 'Editar Comunicado' ) );

$webadm->add_parent( array( 'name' => 'Communicados', 'url' => 'communicateds.php' ) );

$webadm->add_plugins('quilljs', 'dropify', 'select2', 'switcher');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 550px);
	min-height: 374px;
}

#editor-container2 {
	width: 100%;
	height: calc(100vh - 550px);
	min-height: 374px;
}

.ql-bubble .ql-tooltip {
  z-index: 1;
}

.dropify-wrapper{
	width: 100%;
	height: 210px;
}

#tclient .dropify-wrapper{
	width: 100%;
	/*height: 210px;*/
	min-height: 210px;
	height: calc(100vh - 725px);
}
</style>
<form id="target" action="<?php echo "?id={$content_id}&post"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">
		<div class="card-header">
			<div class="row">
				<div class="col-md-11">
					<div class="row">
						
						<div class="col-md-10">
							<input readonly type="text" class="form-control" placeholder="Comunicados" />
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
				<div class="col-md-3">
					
					<div class="form-group m-b-10">
						<label>Thumbnail</label>
						<input name="image" type="file" id="image" data-max-file-size="100M" 
						class="dropify" data-default-file="<?php echo url( $content['image_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp" />
					</div>
					<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['image']);  ?>"style="display: none;"/>
					
					
					<div class="form-group m-b-0">
						<label>Mensagem</label>
						<div id="editor-container"><?php echo $content['text']; ?></div>
						<textarea style="display: none;" id="text" name="text"></textarea>
					</div>
					
				</div>
				
				<div class="col-md-3">
				
					<div class="form-group m-b-10">
						<label>Thumbnail Destaque</label>
						<input name="image_highlight" type="file" id="image_highlight" data-max-file-size="80M" 
						class="dropify" data-default-file="<?php echo url( $content['image_highlight_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif webp" />
					</div>
					
					<input id="guarda_arq4"  name="guarda_arq4" value="<?php echo html($content['image_highlight']);  ?>"style="display: none;"/>
					
					<div class="form-group m-b-0">
						<label>Mensagem Destaque</label>
						<div id="editor-container2"><?php echo $content['text_highlight']; ?></div>
						<textarea style="display: none;" id="text_highlight" name="text_highlight"></textarea>
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
						<label>Rótulo</label>
						<input name="label" type="text" class="form-control" placeholder="Digite o rótulo" value="<?php echo html($content['label']); ?>" />
					</div>
					
					<div class="form-group m-b-10">
						<label>Busca Novidades</label>
						<input name="search_news" type="text" class="form-control" placeholder="Digite a Busca Novidades" value="<?php echo html($content['search_news']); ?>" />
					</div>
				
					<div class="form-group m-b-10">
						<label>Busca Galerias Whatsapp</label>
						<input name="search_galeries" type="text" class="form-control" placeholder="Digite a Busca Galerias Whatsapp" value="<?php echo html($content['search_galeries']); ?>" />
					</div>
					<!--
					<div class="form-group m-b-10">
						<label>Visualizar PDF</label>
						<!--<input type="file" class="form-control" id="file" name="file" value="?php echo url( $content['file_url'] ) ?>" placeholder="Selecione um arquivo PDF">->
						<div  id="up_file" >
							<div class="<php echo $content['file']==0 ? 'fileinput fileinput-new input-group' : 'fileinput input-group fileinput-exists'; ?>"
							data-provides="fileinput">
								<div class="form-control" data-trigger="fileinput">
									<i class="fa fa-file fileinput-exists"></i>
									<span id="arq_up" class="fileinput-filename" style="max-width:100%">
										<php echo html($content['file_name']); ?>
									</span>
								</div>
								<span class="input-group-addon btn btn-secondary btn-file"> 
								<span class="fileinput-new">Selecione</span>
								<span class="fileinput-exists">Alterar</span>
								<input name="file" type="file" id="file_teste" data-max-file-size="100M" 
									data-default-file="<php echo url( $content['file_url'] ) ?>" data-allowed-file-extensions="pdf" >
								</span>
								<a href="#" class="input-group-addon btn btn-secondary fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
						</div>
					</div>
					<input id="guarda_arq"  name="guarda_arq" 
									value="<php echo html($content['file_name']);  ?>"style="display: none;"/>
					-->
					
					<div class="form-group m-b-10">		
						<label>Link / Upload do PDF</label>
						<div class="row">
							<div class="col-md-3">
								<select id="content_type" class="select2" name="content_type">
									<?php foreach($list_content_types as $content_type) { ?>
										<option <?php 
										
											echo selected( $content_type['id'] == $content['content_type'] )
										
										?> value="<?php 

											echo html($content_type['id']); 
											
										?>"><?php 
										
											echo html($content_type['name']); 
										
										?></option>
									<?php } ?>
								</select>
							</div>						
							<div class="col-md-9">
								<input data-bv-uri-allowlocal id="link" name="link" type="url" class="form-control" placeholder="Insira o Link" 
									value="<?php echo html($content['link']);  ?>"style="<?php echo $hide_link;  ?> max-height:10px;"/>								
								<div class="col-md-12">
									<div  id="up_file" style="<?php echo $hide_upload;  ?>">
										<div class="<?php echo $content['file']==0 ? 'fileinput fileinput-new input-group' : 'fileinput input-group fileinput-exists'; ?>"
										data-provides="fileinput">
											<div class="form-control" data-trigger="fileinput">
												<i class="fa fa-file fileinput-exists"></i>
												<span id="arq_up" class="fileinput-filename" style="max-width:100%">
													<?php echo html($content['file_name']); ?>
												</span>
											</div>
											<span class="input-group-addon btn btn-secondary btn-file"> 
											<span class="fileinput-new">Selecione</span>
											<span class="fileinput-exists">Alterar</span>
											<input name="file" type="file" id="file_teste" data-max-file-size="100M" 
												data-default-file="<?php echo url( $content['file_url'] ) ?>" data-allowed-file-extensions="mp4 jpg jpeg png gif" >
											</span>
											<a id="remove_file" href="#" class="input-group-addon btn btn-secondary fileinput-exists" data-dismiss="fileinput">Remover</a> </div>
									</div>
								</div>
								<input id="guarda_arq"  name="guarda_arq" 
									value="<?php echo html($content['file_name']);  ?>"style="display: none;"/>			
								
							</div>
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
<script src="js/communicated_edit.js"></script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>