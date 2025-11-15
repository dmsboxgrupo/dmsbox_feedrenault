<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];

// Codigo da pagina

need_login();

// Controller
$faq_group_id = (int)get('faq_group_id');
$faq_group_topic_id = (int)get('id');
/*
echo "faq_group_id= ".$faq_group_id;
echo " -/ faq_group_topic_id= ". $faq_group_topic_id;

die();*/

$is_new = $faq_group_topic_id == 0;

//campo de erro
$error = "";

//$manager_user_logado = $useradm->get_property('id');

//////////****** content_root ******//////////

//campo de erro
$error = "";

if ($faq_group_id > 0) {
	
	$faq_group_content = $db->content( $faq_group_id, 'faq_groups' );
	
} else {
	
	redirect("faq_groups.php?id={$faq_group_id}");
	
}

function get_content($insert=true) {
	
	global $is_new, $faq_group_id;
	
	$content =  array(
		'title' => get_post('title')
		//'faq_group' => get_post('faq_group')
		//'faq_group_topic_items' => get_post('title')
	);
	
	if($is_new){

		$content['faq_group'] = $faq_group_id;	

	}
	
	return 	$content;
	
}

function update_faq_group_topic($new_faq_group_topic=null) {
	
	global $db, $faq_group_content;
	
	$faq_groups = array();
	
	$arr =explode(",", $faq_group_content['faq_group_topics']);
	foreach($arr as $faq_group_topic) {
		
		$content = $db->content( $faq_group_topic, 'faq_group_topics' );		
		
		if ( $content['status'] ) {
		
			array_push( $faq_groups, $content['id'] );
			
		}
		
	}
	if ($new_faq_group_topic) {
		
		array_push( $faq_groups, $new_faq_group_topic );
		
	}
	
	$db->update('faq_groups', $faq_group_content['id'], array(
		'faq_group_topics' => implode(",", $faq_groups)
	));
	
	$_SESSION['message'] = 'Registro alterado com sucesso.';
	$_SESSION['type'] = 'success';
	
}

if ($faq_group_topic_id > 0) {
//print_r($content); die();	
	$content = $db->content( $faq_group_topic_id , 'faq_group_topics' );

	if (has('post')) {
		
		$conteudo = get_content();
		
		if(!$conteudo['title']) {
			
			$error = "Tópico deve ser preenchida.";
			
		}else{
			
			$db->update('faq_group_topics', $faq_group_topic_id, $conteudo);

			$save = get_post('save');
			
			if($save){

				redirect("faq_group_topics.php?id={$faq_group_id}", array(
					'message' => 'Tópico alterado com sucesso.',
					'type' => 'success'
				));
				
			}
			
		}
		

	} elseif (has('toggle_active')) {

		$db->update('faq_group_topics', $faq_group_topic_id, array('status' => $content['status'] ? 0 : 1));
		
		redirect("faq_group_topics.php?id={$faq_group_id}", array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {
	
	$content = get_content(true);
	
	if(!$content['title']) {
		
		$error = "Tópico deve ser preenchido.";

	} else {
		
		
		$faq_group_topic_id = $db->insert('faq_group_topics', $content);
		
		update_faq_group_topic( $faq_group_topic_id );
		
		$save = get_post('save');
			
		if($save){
			
			redirect("faq_group_topics.php?id={$faq_group_id}", array(
				'message' => 'Tópico adicionado com sucesso.',
				'type' => 'success'
			));
			
		}
	
	}
	
} else {
	
	//$content_root = $db->content( get_content_root(false), 'surveys' );
	$content = $db->content( get_content(false), 'faq_group_topics' );
	
}

//////////****** content_root  End******//////////

if ($faq_group_topic_id > 0) {
	
	$content = $db->content( $db->select_id('faq_group_topics', $faq_group_topic_id), 'faq_group_topics' );

	if (has('update')) {
		
		$json = array();
		
		if (has_post('faq_group_topic_items')) {
			
			$faq_group_topic_items = get_post('faq_group_topic_items');
			
			$json['faq_group_topic_items'] = $faq_group_topic_items;
			
		}
		
		$db->update('faq_group_topics', $faq_group_topic_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}
//print_r($content); die();

$webadm->set_page( array( 'name' => 'Perguntas' ) );

if(!$is_new) $webadm->add_button( array( 'name' => 'Nova Pergunta', 'icon' => 'mdi mdi-playlist-plus', 'url' => "faq_group_topic_item_edit.php?faq_group_id=$faq_group_id&faq_group_topic_id=$faq_group_topic_id" ) );

$webadm->add_parent( array( 'name' => 'Grupos FAQ', 'url' => 'faq_groups.php' ) );

$webadm->add_parent( array( 'name' => 'Grupo FAQ', 'url' => "faq_group_topics.php?id=$faq_group_id" ) );

//$webadm->add_plugins('datatables', 'sweetalert');
$webadm->add_plugins('datatables', 'sweetalert', 'quilljs');

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

<form id="target" action="<?php echo "?faq_group_id={$faq_group_id}&id={$faq_group_topic_id}&post"; ?>" method="post" enctype="multipart/form-data">

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
					
						<div class="col-md-12 m-b-10">
							<label>Título</label>
							<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content['title']); ?>" required />
							
						</div>
						
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
							<th>Perguntas</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$aux =	explode(",", $content['faq_group_topic_items']);
						if(!empty ($aux[0])){
						for($i = 0; $i < count($aux); $i++) {
							extract( $db->content( $aux[$i], 'faq_group_topic_items'), EXTR_PREFIX_ALL, 'i' );
								if (!empty ($i_status)) {
						?>
						<tr faq-group-id="<?php echo $i_faq_group; ?>" faq-group-topic-id =  "<?php echo $faq_group_topic_id; ?>"  faq-group-topic-item-id="<?php echo $i_id; ?>" ">
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
									<a class="btn btn-danger text-white btn-sm toggle-faq-group-topic-item-answer" data-toggle="tooltip" title="Remover Pergunta">
										<i class="mdi mdi-close"></i>
									</a>
									<?php } else { ?>
									<a class="btn btn-info text-white btn-sm toggle-faq-group-topic-item-answer" data-toggle="tooltip" title="Ativar Pergunta">
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

<script src="js/faq_group_topic_items.js"></script>

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
		
		seq.push( parseInt( $(this).attr('faq-group-topic-item-id') ) );
		
	});
	
	//console.log( seq );
	
	$.post( "faq_group_topic_items.php?faq_group_id=<?php echo $faq_group_id ?>&id=<?php echo $faq_group_topic_id ?>&update", { faq_group_topic_items: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-faq-group-topic-item-answer').click(function(){
		
		var faqGroupId = $(this).closest('tr').attr( "faq-group-id" );
		//var contentId = $(this).closest('tr').attr( "faq-group-topic-id" );
		var faqGroupTopicId = $(this).closest('tr').attr( "faq-group-topic-id" );
		var contentTopicId = $(this).closest('tr').attr( "faq-group-topic-item-id" );
		//var categoryId = $(this).closest('tr').attr( "category-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover essa Pergunta?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				//faq_group_topic_item_edit.php?faq_group_id=12&faq_group_topic_id=6&id=6"
				
				window.location = "<?php echo url('admin/faq_group_topic_item_edit.php'); ?>?faq_group_id=" + faqGroupId + "&faq_group_topic_id=" + faqGroupTopicId + 
					"&toggle_active" + "&id=" + contentTopicId;
				
			});
		} else
			window.location = "<?php echo url('admin/faq_group_topic_item_edit.php'); ?>?faq_group_id=" + faqGroupId + "&faq_group_topic_id=" + faqGroupTopicId + 
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