<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];

// Codigo da pagina

need_login();

// Controller
$faq_group_id = (int)get('id');
$is_new = $faq_group_id == 0;

$metadata_id = $db->get_metadata_id( 'faq_groups' );

if ($metadata_id > 0) {
	
	$root_content = $db->content( $metadata_id, 'metadata' );

} else {

	redirect("vehicles.php");
	
}


//campo de erro
$error = "";

//$manager_user_logado = $useradm->get_property('id');

//////////****** content_root ******//////////

function get_content_root($insert=true) {
	
	global $uploader;
	
	$content =  array(
		'title' => get_post('title'),
		'subtitle' => get_post('subtitle')
	);
	
	if(get_post('guarda_arq') == 0){
		
		$content['image'] = 0;
		
	}
	
	if (!$insert) {

		$content['image'] = 0;
		
	}
	
	if (has_upload('image')) {
		
		$image_id = $uploader->upload( get_upload('image') );

		$content['image'] = $image_id;
	}
	
	return 	$content;
	
}

function update_faq_group($new_faq_group=null) {
	
	global $db, $root_content;
	
	$faq_groups = array();
	
	$arr =explode(",", $root_content['value']);
	
	foreach($arr as $faq_group) {
		
		$content = $db->content( $faq_group, 'faq_groups' );
		
		if ( $content['status'] ) {
		
			array_push( $faq_groups, $content['id'] );
			
		}
		
	}
	if ($new_faq_group) {
		
		array_push( $faq_groups, $new_faq_group );
		
	}
	
	$db->update('metadata', $root_content['id'], array(
		'value' => implode(",", $faq_groups)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($faq_group_id > 0) {

	$content_root = $db->content( $faq_group_id , 'faq_groups' );
	//print_r($content_root); die();
	if (has('post')) {
		
		//$teste = get_content_root();

		$db->update('faq_groups', $faq_group_id, get_content_root());
		
		$save = get_post('save');
		
		if($save){
			
			redirect("faq_group_topics.php?id=$faq_group_id", array(
				'message' => 'Enquete alterada com sucesso.',
				'type' => 'success'
			));
		
		}
		

	} elseif (has('toggle_active')) {

		$db->update('faq_groups', $faq_group_id, array('status' => $content_root['status'] ? 0 : 1));
			
		redirect('faq_groups.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {
	
	//$content_root = $db->content( get_content_root(false), 'faq_groups' );
	//$content_root = $db->get_content(true);
	$content_root = get_content_root(true);
/*
	if(empty($content_root['text'])){
		
			$error = "Preencha o campo 'Texto da Enquete'";
			//print_r($error); die();
	} else {*/
//print_r($content_root); die();
		$faq_group_id = $db->insert('faq_groups', $content_root);
		
		update_faq_group( $faq_group_id );

		$save = get_post('save');
		
		if($save){
			redirect("faq_group_topics.php?id=$faq_group_id", array(
				'message' => 'Galeria adicionado com sucesso.',
				'type' => 'success'
			));
		}
	//}
	
} else {
	
	$content_root = $db->content( get_content_root(false), 'faq_groups' );
	
}

//////////****** content_root  End******//////////

if ($faq_group_id > 0) {
	
	$content = $db->content( $db->select_id('faq_groups', $faq_group_id), 'faq_groups' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('faq_group_topics')) {
			
			$faq_group_topics = get_post('faq_group_topics');
			
			$json['faq_group_topics'] = $faq_group_topics;
			
		}
		
		$db->update('faq_groups', $faq_group_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}
//print_r($content); die();

$webadm->set_page( array( 'name' => 'Tópicos' ) );

if(!$is_new) $webadm->add_button( array( 'name' => 'Novo Tópico', 'icon' => 'mdi mdi-playlist-plus', 'url' => "faq_group_topic_items.php?faq_group_id=$faq_group_id" ) );

$webadm->add_parent( array( 'name' => 'Gurpos FAQ', 'url' => 'faq_groups.php' ) );

//$webadm->add_plugins('datatables', 'sweetalert');
$webadm->add_plugins('datatables', 'sweetalert', 'quilljs', 'dropify');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
/*
#survey_text {
	width: 100%;
	background:#fff;
	height: calc(10vh); 
	
}*/

.dropify-wrapper{
	width: 100%;
	height: 120px;
}

.dataTables_scrollBody {
  /*max-height: 100px !important;*/
   height: calc(35vh) !important;
}
</style>

<?php if ( $_SESSION['type']=='success' ) { $_SESSION['type']='';?>
	<div id="success-alert" class="alert alert-success">
		<?php echo $_SESSION['message']; $_SESSION['message']=''; ?>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span></button>
	</div>
<?php } ?>

<form id="target" action="<?php echo "?id={$faq_group_id}&post"; ?>" method="post" enctype="multipart/form-data">

	<?php if ( $error ) { ?>
		<div class="alert alert-danger">
	<?php echo html( $error ); ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
		</div>
	<?php } ?>
	
	<div class="card no-margin">
		
		<div class="card-header">

			<div class="row">

				<div class="col-md-11">
				
					<div class="row">
						
						<div class="col-md-10 ">
						
							<div class=" m-b-10">
								<label>Título</label>
								<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content_root['title']); ?>" required />
							</div>	
							<div class=" m-b-10">
								<label>Subtítulo</label>
								<input name="subtitle" type="text" class="form-control" placeholder="Digite o Subtítulo" value="<?php echo html($content_root['subtitle']); ?>"  />
							</div>		
						
						</div>
						
						
					
						<div class="col-md-2 ">
							<label>Imagem</label>
							<input name="image" type="file" id="image" data-max-file-size="100M" 
							class="dropify" data-default-file="<?php echo url( $content_root['image_url'] ) ?>" data-allowed-file-extensions="jpg jpeg png gif webp svg" />
						</div>
						<input id="guarda_arq"  name="guarda_arq" value="<?php echo html($content_root['image']);  ?>"style="display: none;"/>

					</div>
					
					
					
					
				</div>
				
				
				<button name="save" id="save" type="submit" class="btn btn-info" value=1 style="align-self: center;"><i class="fa fa-check m-r-10"></i>Salvar</button>
					
			</div>

		</div>
		
<?php if(!$is_new){ ?>
		<div class="card-body">		
			<div class="table-responsive">
				<table id="contents" class="display pageResize nowrap table table-striped table-bordered">
					<thead>
						<tr>
							<th>Seq.</th>
							<th><i class="mdi mdi-drag-vertical"></i></th>
							<th>Tópicos</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$aux =	explode(",", $content['faq_group_topics']);
						if(!empty ($aux[0])){
						for($i = 0; $i < count($aux); $i++) {
							extract( $db->content( $aux[$i], 'faq_group_topics'), EXTR_PREFIX_ALL, 'i' );
								if (!empty ($i_status)) {
						?>
						<tr faq-group-id="<?php echo $i_faq_group; ?>" faq-group-topic-id="<?php echo $i_id; ?>" ">
						
							<td><?php echo $i; ?></td>
							<td class="align-middle dt-nowrap">
								<i class="mdi mdi-drag-vertical"></i>
							</td>
							
							<td class="dt-nowrap text-nowrap align-middle">
								<a href="<?php echo $i_edit_url; ?>">
									<h4><?php echo htmltotext($i_title, 50); ?></h4>
								</a>
							</td>
							
							<td class="text-center align-middle">
								
								<div class="btn-group btn-group-justified">
									<?php if ($i_status) { ?>
									<a class="btn btn-danger text-white btn-sm toggle-faq-group-topic" data-toggle="tooltip" title="Remover Resposta">
										<i class="mdi mdi-close"></i>
									</a>
									<?php } else { ?>
									<a class="btn btn-info text-white btn-sm toggle-faq-group-topic" data-toggle="tooltip" title="Ativar Resposta">
										<i class="fas fa-eye"></i>
									</a>
									<?php } ?>
								</div>
							</td>
							
							
						</tr>
						<?php }} } ?>
					</tbody>
				</table>
			</div>						
		</div>
		<?php } ?>
	</div>
</form>

<script src="js/faq_group_topics.js"></script>

<script>
$(function() {
	var contents = $('#contents').DataTable({		
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },
		rowReorder: true,
		paging: false,
		searching: false,
		scrollY: 'calc(100vh - (440px - 52px))',
		columnDefs: [
			{ targets: 0, visible: false },
			{ orderable: true, className: 'reorder', targets: 1 },
			{ orderable: false, targets: '_all' },
			{ width: "1px", targets: [0,1,3] }
		],
		order: [[ 0, "asc" ]]
	});
	
	contents.on( 'row-reorder', function ( e, diff, edit ) {

	var seq = [];

	$('#contents tbody tr').each(function() {
		
		seq.push( parseInt( $(this).attr('faq-group-topic-id') ) );
		
	});
	
	//console.log( seq );
	
	$.post( "faq_group_topics.php?id=<?php echo $faq_group_id ?>&update", { faq_group_topics: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-faq-group-topic').click(function(){
		
		var faqGroupId = $(this).closest('tr').attr( "faq-group-id" );
		var contentId = $(this).closest('tr').attr( "faq-group-topic-id" );
		//var categoryId = $(this).closest('tr').attr( "category-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover essa Resposta?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/faq_group_topic_items.php'); ?>?faq_group_id=" + faqGroupId + 
					"&toggle_active" + "&id=" + contentId;
				
			});
		} else
			window.location = "<?php echo url('admin/faq_group_topic_items.php'); ?>?faq_group_id=" + faqGroupId + 
			"&toggle_active" + "&id=" + contentId;
	});
	
	$(document).ready(function() {
	  $("#success-alert").fadeTo(5000, 500).slideUp(500, function() {
		  $("#success-alert").slideUp(500);
		});	  
	});
});

</script>

<!-- ============================================================== -->
<!-- End post Content -->
<!-- ============================================================== -->
<?php 

// Finaliza HTML

$webadm->end_panel();

?>