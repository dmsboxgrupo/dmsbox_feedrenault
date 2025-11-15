<?php

// API

include('includes/common.php');

$category = (int)get('category');
$content_id = (int)get('content_id');

$par_content = ($content_id>0)?"content_id={$content_id}&":"";

$par_content_type = ($category == 7) ? "&type=1&" : (($category == 8) ? "&type=2&" : "") ;

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
/*
$metadata_id = $db->get_metadata_id( 'tag_groups' );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("users.php");
	
}*/

function get_content($insert=true) {
	
	global $uploader;
	
	$content = array(
		'name' => get_post('name')
	);
	
	return $content;
	
}
/*
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
	
}*/



// Atuliza Tag
if ($id > 0) {
	//recupera dados usuario
	$content = $db->content( $db->select_id( 'tag_subgroups', $id ), 'tag_subgroups' );	
	
	if (has('post')) {
		$campos = get_content("");
		$db->update('tag_subgroups', $id, $campos);
		
		redirect("tag_subgroups.php?{$par_content}category={$category}", array(
			'message' => 'Grupo Tag alterada com sucesso.',
			'type' => 'success'
		));

	} elseif (has('toggle_active')) {
		//alterana status
		$db->update('tag_subgroups', $id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("tag_subgroups.php?{$par_content}category={$category}", array(
			'message' => 'Grupo Tag alterada com sucesso.',
			'type' => 'success'
		));
		
	}
} elseif (has('post')) {
	// Inclui Tag
	$content = get_content(false);
	//validando email
	$sqlTeste= "select 'S' from tag_subgroups where name='".$content['name']."' ";
	//$existe = query($sqlTeste);
	$existe = $db->query($sqlTeste);
	
	if($error==""){
		if($existe && $existe[0]['S']=='S'){
			
			$error = "Nome ".$content['nome']." ja cadastrado.";
		}
		else{
			$campos=get_content();
			$campos['status']=1;
			//$campos['category']= $category;
			//$campos['metadata']= $metadata_id;
//print_r($campos);		
			$retorno = $db->insert('tag_subgroups', $campos);
//echo"retorno= $retorno"; die();
			//update_tags( $retorno );

			$_SESSION['message'] = 'Registro cadastrado com sucesso.';	    
			$_SESSION['type'] = 'success';
			
			
			//echo "tags.php?{$par_content}category={$category}"; die();
			redirect("tag_subgroups.php?{$par_content}category={$category}");
		}
	}
	
} else {
	//$content = get_content(false);
	$content = $db->content( get_content(false), 'tag_subgroups' );
}

// Inicia HTML


if($is_new){
	
	$webadm->set_page( array( 'name' => 'Novo Subgrupo Tag' ) );
	
}else{
	
	$webadm->set_page( array( 'name' => 'Editar Subgrupo Tag' ) );
	
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
//print_r($content);die();
$webadm->add_parent( array( 'name' => 'Tags', 'url' => "tags.php?{$par_content}category={$category}" ) );
$webadm->add_parent( array( 'name' => 'Subgrupos Tags', 'url' =>"tag_subgroups.php?{$par_content}category={$category}"));

//echo "teste";die();
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
<script src="js/tag_subgroup_edit.js"></script>
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