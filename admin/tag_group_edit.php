<?php

// API

include('includes/common.php');

$category = (int)get('category');
$content_id = (int)get('content_id');

$par_content = ($content_id>0)?"content_id={$content_id}&":"";

$par_content_type = ($category == 7) ? "&type=1&" : (($category == 8) ? "&type=2&" : "") ;
/*
$subgroups = $db->query("SELECT tags.id, tags.name FROM tags 
					join metadata on property='{$tag_names}' and FIND_IN_SET(tags.id, metadata.value) 
					WHERE tags.status = 1 and tags.category = {$category_tags}");
*/					

$tag_subgroups = $db->query("SELECT tag_subgroups.id, tag_subgroups.name FROM tag_subgroups 
					WHERE tag_subgroups.status = 1");

// Codigo da pagina
need_login();

//id pagina
$id = (int)get('id');

$is_new = $id == 0;

//campo de erro
$error ="";

//Bloqueio de campos
$desabilita ="";

//$meta_property = $list_categories[$category]['tags'];
//$metadata_id = $db->get_metadata_id( 'tag_groups' );
$meta_property = "groups_".$list_categories[$category]['tags'];
$metadata_id = $db->get_metadata_id( $meta_property );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("users.php");
	
}

function get_content($insert=true) {
	
	global $uploader;
	
	$content = array(
		'name' => get_post('name'),
	);
	
	if(get_post('guarda_arq2')== 0){
		
		$content['thumbnail'] = 0;
		
	}
	//echo "aki $insert";
	
	if (!$insert) {

		//$content['id'] = 0;
		$content['thumbnail'] = 0;
		//$content['thumbnail_url'] = '';
		$content['tag_subgroups'] = get_post('tag_subgroups');
	}else{
		//echo "teste";die();	
		//$content['tag_subgroups'] = empty(get_post('tag_subgroups'))?"":implode(",", get_post('tag_subgroups'));
		//template_texts_values
		$content['tag_subgroups'] = get_post('template_texts_values');
		
	}//die();
	//print_r($content);
	//
	//die();
	if (has_upload('thumbnail')) {
//echo "aki"; 	
		$thumbnail_id = $uploader->upload( get_upload('thumbnail') );
//echo " - aki $thumbnail_id"; die();
		$content['thumbnail'] = $thumbnail_id;
	}
	
	return $content;
	
}

function update_tags($new_topic=null) {
	
	global $db, $root_content;
	
	$tag_groups = array();
	
	$arr =explode(",", $root_content['value']);
	foreach($arr as $tag_group) {
		
		$content = $db->content( $tag_group, 'tag_groups' );
		
		if ( $content['status'] ) {
		
			array_push( $tag_groups, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $tag_groups, $new_topic );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $tag_groups)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

function update_subgroups() {
	
	global $db, $metadata_tag_id;

	$root_content_tags = $db->content( $metadata_tag_id, 'metadata' );
				
	$template_tags_new = current($db->query("SELECT GROUP_CONCAT(tags_id order by tags_id) as tags_ids from(
												SELECT tags.id as tags_id
												FROM tags 
												JOIN templates on FIND_IN_SET(tags.id, templates.tags) 
												JOIN metadata on property='highlighted_templates_tags' and FIND_IN_SET(tags.id, metadata.value)=0
												WHERE tags.status = 1 and category=7 
												GROUP by tags.id
											) tab
											"))['tags_ids'];
	
	
	$tags = $root_content_tags['value'];
	
	if($template_tags_new) $tags .= (($tags)?",":"").$template_tags_new;
	
	$db->update('metadata', $root_content_tags['id'], array(
		'value' => $tags
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';		
				

}


// Atuliza Tag
if ($id > 0) {
	//recupera dados usuario
	$content = $db->content( $db->select_id( 'tag_groups', $id ), 'tag_groups' );	
	
	if (has('post')) {
		$campos = get_content();
		
		//print_r($campos);die();
//echo "teste";die();	
		
		//$campos['subgroups'] = $root_content_tags['value'];
	
		//if($content['subgroups']) $tags .= (($tags)?",":"").$content['subgroups'];
		
		$db->update('tag_groups', $id, $campos);
//print_r($campos);die();		
		//update_subgroups();
		
		redirect("tag_groups.php?{$par_content}category={$category}", array(
			'message' => 'Grupo Tag alterada com sucesso.',
			'type' => 'success'
		));

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('tag_groups', $id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("tag_groups.php?{$par_content}category={$category}", array(
			'message' => 'Grupo Tag alterada com sucesso.',
			'type' => 'success'
		));
		
	}
	
	if($content['tag_subgroups']){
		
		$arr_ts = explode(",", $content['tag_subgroups']);
		//print_r($arr_ts);die();
		foreach(array_reverse($arr_ts) as $aux){
			
			$index = array_search($aux, array_column($tag_subgroups, 'id'));
			
			if ($index !== false) {
				$item = $tag_subgroups[$index];
				unset($tag_subgroups[$index]);
				array_unshift($tag_subgroups, $item);
			}
		}
		
	}
} elseif (has('post')) {
	// Inclui Tag
	$content = get_content(false);
	//validando email
	$sqlTeste= "select 'S' from tag_groups where name='".$content['name']."' and category=$category and status=1";
	//$existe = query($sqlTeste);
	$existe = $db->query($sqlTeste);
	
	if($error==""){
		if($existe && $existe[0]['S']=='S'){
			
			$error = "Nome ".$content['name']." ja cadastrado.";
		}
		else{

			$content['status']=1;
			$content['category']= $category;
		
			$retorno = $db->insert('tag_groups', $content);

			update_tags( $retorno );

			$_SESSION['message'] = 'Registro cadastrado com sucesso.';	    
			$_SESSION['type'] = 'success';
			
			
			//echo "tags.php?{$par_content}category={$category}"; die();
			redirect("tag_groups.php?{$par_content}category={$category}");
		}
	}
	
} else {
	//$content = get_content(false);
	$content = $db->content( get_content(false), 'tag_groups' );
}

// Inicia HTML

if($is_new){
	
	$webadm->set_page( array( 'name' => 'Novo Grupo Tag' ) );
	
}else{
	
	$webadm->set_page( array( 'name' => 'Editar Grupo Tag' ) );
	
}
$webadm->add_parent( array( 'name' => $list_categories[$category]['name_list'], 'url' => $list_categories[$category]['url_list']));


if(!empty($template)){	
	
	$webadm->add_parent( array( 'name' => $list_categories[$category]['name'], 'url' => $list_categories[$category]['url']."?id={$template_id}&type={$type}"));	
	$webadm->add_parent( array( 'name' => 'Item', 'url' => $list_categories[$category]['url2']."?id=$content_id&template_id={$template_id}&type={$type}"));
	
}else{
	if($content_id>0)		
		$webadm->add_parent( array( 'name' => $list_categories[$category]['name'], 'url' => $list_categories[$category]['url']."?id=$content_id{$par_content_type}"));
	elseif($category == 7 and empty($content_id))
		$webadm->add_parent( array( 'name' => 'Tags de Destaque', 'url' => 'template_tags.php'));

}

$webadm->add_parent( array( 'name' => 'Tags', 'url' => "tags.php?{$par_content}category={$category}" ) );
$webadm->add_parent( array( 'name' => 'Grupos Tags', 'url' =>"tag_groups.php?{$par_content}category={$category}"));

//$webadm->add_plugins('dropify', 'select2');
$webadm->add_plugins('quilljs', 'dropify', 'select2','tagsinput');
$webadm->start_panel();
?>

<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<div class="row">
	<div class="col-12">
		<div class="card no-margin">
			<div class="card-body wizard-content">
				<form action="?id=<?php echo $id; ?>&post<?php echo "&content_id={$content_id}&category={$category}"; ?>" method="post" enctype="multipart/form-data" class="tab-wizard wizard-circle" autocomplete="off">					
					<?php if ( $error ) { ?>
						<div class="alert alert-danger">
					<?php echo html( $error ); ?>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
						</div>
					<?php } ?>
					<div class="row">
						<div class="col-md">
							<div class="form-group">
								<label>Nome</label>
								<input name="name" value="<?php echo html($content['name']); ?>" type="text" class="form-control" placeholder="Insira o nome" required>
							</div>
							
							<div class="form-group">
								<label>Subgrupos</label>
								<select id="tag_subgroups" class="form-control col-md-12 select2" name="tag_subgroups[]" multiple="multiple">

									<?php 
										$arr_aux = explode(",", $content['tag_subgroups']);
										foreach($tag_subgroups as $tag_subgroup) {
											//$selectedTags = explode(",", $content['tag_subgroups']);
											//$sel = in_array($tag_subgroup['id'], $selectedTags) ? "selected" : "";
											$sel = (in_array($tag_subgroup['id'], $arr_aux))? "selected":"";
									?>
										<option <?php echo $sel ?> value="<?php echo html($tag_subgroup['id']); ?>" $sel ><?php echo html($tag_subgroup['name']); ?></option>
									<?php } ?>
									
									
								</select>
							</div>
							<input type="hidden" name="template_texts_values" id="template_texts_values" value="<?php echo $content['tag_subgroups']; ?>" />
							
							
							<div class="form-group m-b-10">
								<label>Thumbnail</label>
								<input name="thumbnail" type="file" id="thumbnail" data-max-file-size="100M" 
								class="dropify" data-default-file="<?php echo url( $content['thumbnail_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp svg" />
							</div>
							
							<input id="guarda_arq2"  name="guarda_arq2" value="<?php echo html($content['thumbnail']);  ?>"style="display: none;"/>
							
						</div>
					</div>					
					<div class="form-actions">
						<button type="submit" class="btn btn-block btn-info text-uppercase"><i class="fa fa-check m-r-5"></i> Salvar</button>
						<!--<input type="submit" value="Salvar" name="submit" class="btn btn-block btn-info text-uppercase"/>-->
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.quilljs.com/1.3.7/quill.bubble.css"></link>
<link rel="stylesheet" type="text/css" href="https://cdn.quilljs.com/1.3.7/quill.snow.css"></link>
<script src="https://cdn.jsdelivr.net/gh/T-vK/DynamicQuillTools@master/DynamicQuillTools.js"></script>

<script src="js/tag_group_edit.js"></script>
<script>
$(function() {
	
	//$(".select2").select2();

	//$('#cpf').mask('000.000.000-00', {reverse: true});

});
</script>
<!-- ============================================================== -->
<!-- End Page Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>