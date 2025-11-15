<?php

// API

include('includes/common.php');

$category = (int)get('category');
$content_id = (int)get('content_id');

//$template_id = (int)get('template_id');

//$template = "";
//$template_id = 0;
/*
if($template_id>0) {
	
	if($category == 8)	{
		
		$template = "style";
		$type = 2;
		//$template_id =  $template_style_id;
		
	}elseif($category == 7){
		
		$template = "background";
		$type = 1;
		//$template_id =  $template_background_id;
		
	}
	
	
}*/


$where_cat = ( $category >0 )? " and category = $category" : "";
//print_r($where_cat); die();
$tag_groups = $db->query("SELECT id, name FROM tag_groups WHERE status = 1 $where_cat");
$tag_subgroups = $db->query("SELECT id, name FROM tag_subgroups WHERE status = 1 ");

$par_content = ($content_id>0)?"content_id={$content_id}&":"";

//$par_content .= (!empty($template))?"template_id={$template_id}&":"";

$par_content_type = ($category == 7) ? "&type=1&" : (($category == 8) ? "&type=2&" : "") ;

// Codigo da pagina
need_login();

//id pagina
$id = (int)get('id');
//campo de erro
$error ="";

//Bloqueio de campos
$desabilita ="";

$meta_property = $list_categories[$category]['tags'];
$metadata_id = $db->get_metadata_id( $meta_property );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("users.php");
	
}

function get_content($insert=true) {
	/*return array(
		'name' => get_post('name'),
		'group' => get_post('group'),
		'hashtag' => get_post('hashtag')
	);*/
	
	global $uploader;
	
	$content = array(
		'name' => get_post('name'),
		//'group' => get_post('group'),
		'tag_group' => get_post('tag_group'),
		'tag_subgroup' => get_post('tag_subgroup'),
		'hashtag' => get_post('hashtag')
	);
	/*
	if(get_post('guarda_arq2')== 0){
		
		$content['thumbnail'] = 0;
		
	}*/
	/*
	if (!$insert) {

		$content['id'] = 0;
		$content['thumbnail'] = 0;
		
	}
	*/
	
	/*if (has_upload('thumbnail')) {
		
		$thumbnail_id = $uploader->upload( get_upload('thumbnail') );

		$content['thumbnail'] = $thumbnail_id;
	}*/
	
	return $content;
	
}

function update_tags($new_topic=null) {
	
	global $db, $root_content;
	
	$tags = array();
	
	$arr =explode(",", $root_content['value']);
	foreach($arr as $tag) {
		
		$content = $db->content( $tag, 'tags' );
		
		if ( $content['status'] ) {
		
			array_push( $tags, $content['id'] );
			
		}
		
	}
	if ($new_topic) {
		
		array_push( $tags, $new_topic );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $tags)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}



// Atuliza Tag
if ($id > 0) {
	//recupera dados usuario
	$content = $db->content( $db->select_id( 'tags', $id ), 'tags' );	
	
	if (has('post')) {
		$campos = get_content("");
		$db->update('tags', $id, $campos);
		
		/*$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect('tags.php');
*/	
		redirect("tags.php?{$par_content}category={$category}", array(
			'message' => 'Tag alterada com sucesso.',
			'type' => 'success'
		));
		
		/*
		redirect("tags.php?content_id={$content_id}&category={$category}", array(
			'message' => 'Tag alterada com sucesso.',
			'type' => 'success'
		));*/

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('tags', $id, array('status' => $content['status'] ? 0 : 1));
		
		/*$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		redirect('tags.php');
		*/
		redirect("tags.php?{$par_content}category={$category}", array(
			'message' => 'Tag alterada com sucesso.',
			'type' => 'success'
		));
		
	}
} elseif (has('post')) {
	// Inclui Tag
	$content = get_content(false);
	//validando email
	$sqlTeste= "select 'S' from tags where name='".$content['name']."' ";
	//$existe = query($sqlTeste);
	$existe = $db->query($sqlTeste);
	
	if($error==""){
		if($existe && $existe[0]['S']=='S'){
			
			$error = "Nome ".$content['nome']." ja cadastrado.";
		}
		else{
			$campos=get_content();
			$campos['status']=1;
			$campos['category']= $category;
			$campos['metadata']= $metadata_id;
			
			$retorno = $db->insert('tags', $campos);

			update_tags( $retorno );

			$_SESSION['message'] = 'Registro cadastrado com sucesso.';	    
			$_SESSION['type'] = 'success';
			
			
			//echo "tags.php?{$par_content}category={$category}"; die();
			redirect("tags.php?{$par_content}category={$category}");
		}
	}
	
} else {
	$content = get_content(false);
}

// Inicia HTML

$webadm->set_page( array( 'name' => 'Nova Tag' ) );
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
//$webadm->add_parent( array( 'name' => 'Tags', 'url' => "tags.php?content_id={$content_id}&group={$group}" ) );

//$webadm->add_button( array( 'name' => 'Importar Planilha', 'icon' => 'mdi mdi-account-multiple', 'url' => 'user_import.php' ) );

$webadm->add_plugins('dropify', 'select2');
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
							<div class="form-group" style="display: none">
								<label>Hashtag</label>
								<input name="hashtag" value="<?php echo html($content['hashtag']); ?>" type="text" class="form-control" placeholder="Insira a hashtag">
							</div>
							<!--
							<div class="form-group">
								<label>Grupo</label>
								<input name="group" value="?php echo html($content['group']); ?>" type="text" class="form-control" placeholder="Insira o grupo">
							</div>
							-->
							
							<!--<div class="form-group m-b-10">
								<label>Thumbnail</label>
								<input name="thumbnail" type="file" id="thumbnail" data-max-file-size="100M" 
								class="dropify" data-default-file="<php echo url( $content['thumbnail_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp svg" />
							</div>
							
							<input id="guarda_arq2"  name="guarda_arq2" value="?php echo html($content['thumbnail']);  ?>"style="display: none;"/>
							-->
							
							<div class="form-group m-b-10">
								<label>Grupo</label>
								<select id="tag_group" class="form-control col-md-12 select2" name="tag_group" >
									
									<option value="0" >...</option>
									
									<?php foreach($tag_groups as $tag_group) {
										
										$sel = (strpos($content['tag_group'], "{$tag_group['id']}") !== false)? "selected":"";
									?>
										<option <?php echo $sel ?> value="<?php echo html($tag_group['id']); ?>" $sel ><?php echo html($tag_group['name']); ?></option>
									<?php } ?>
								</select>
							</div>
							
							<div class="form-group m-b-10">
								<label>Sub Grupo</label>
								<select id="tag_subgroup" class="form-control col-md-12 select2" name="tag_subgroup" >
									
									<option value="0" >...</option>
									
									<?php foreach($tag_subgroups as $tag_subgroup) {
										
										$sel = (strpos($content['tag_subgroup'], "{$tag_subgroup['id']}") !== false)? "selected":"";
									?>
										<option <?php echo $sel ?> value="<?php echo html($tag_subgroup['id']); ?>" $sel ><?php echo html($tag_subgroup['name']); ?></option>
									<?php } ?>
								</select>
							</div>
					
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
<script src="js/tag_edit.js"></script>
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