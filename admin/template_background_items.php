<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];

// Codigo da pagina

need_login();

// Controller
$template_background_id = (int)get('id');
$is_new = $template_background_id == 0;

//campo de erro
$error = "";
$manager_user_logado = $useradm->get_property('id');

//////////****** content_root ******//////////

function get_content_root($insert=true) {
	
	global $is_new, $manager_user_logado;
	
	$content =  array(
		'name' => get_post('name')
	);
	
	if($is_new){

		$content['user'] = $manager_user_logado;	

	}
	
	return 	$content;
	
}

if ($template_background_id > 0) {

	$content_root = $db->content( $template_background_id , 'template_backgrounds' );
	
	if (has('post')) {

		$db->update('template_backgrounds', $template_background_id, get_content_root());
		
		$save = get_post('save');
		
		if($save){
			
			redirect("template_background_items.php?id=$template_background_id", array(
				'message' => 'Background alterado com sucesso.',
				'type' => 'success'
			));
		
		}else{
			
			redirect("template_background_item_edit.php?template_background_id=$template_background_id", array(
				'message' => 'Background adicionado com sucesso.',
				'type' => 'success'
			));
		}
		

	} elseif (has('toggle_active')) {

		$db->update('template_backgrounds', $template_background_id, array('status' => $content_root['status'] ? 0 : 1));
			
		redirect('template_backgrounds.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {
	$content_root = $db->content( get_content_root(false), 'template_backgrounds' );

	if(empty($content_root['name'])){
		
			$error = "Preencha o campo 'Nome do Background'";
			
	} else {

		$template_background_id = $db->insert('template_backgrounds', get_content_root(true));

		$save = get_post('save');
		
		if($save){
			redirect("template_background_items.php?id=$template_background_id", array(
				'message' => 'Background adicionado com sucesso.',
				'type' => 'success'
			));
		}else{
			
			redirect("template_background_item_edit.php?template_background_id=$template_background_id", array(
				'message' => 'Campanha adicionada com sucesso.',
				'type' => 'success'
			));
		}
	}
	
} else {
	
	$content_root = $db->content( get_content_root(false), 'template_backgrounds' );
	
}

//////////****** content_root  End******//////////

if ($template_background_id > 0) {
	
	$content = $db->content( $db->select_id('template_backgrounds', $template_background_id), 'template_backgrounds' );
//print_r($content); die();
	if (has('update')) {
		
		$json = array();
		
		if (has_post('template_background_items')) {
			
			$template_background_items = get_post('template_background_items');
			
			$json['template_background_items'] = $template_background_items;
			
		}
		
		$db->update('template_backgrounds', $template_background_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}
//print_r($content); die();

$webadm->set_page( array( 'name' => ( !$is_new )? 'Itens' : 'Novo Background' ) );

$webadm->add_button( array( 'name' => 'Gerenciar tags', 'icon' => 'mdi mdi-tag-multiple', 'url' => "tags.php?content_id=$template_background_id&category=7" ) );

//if(!$is_new) $webadm->add_button( array( 'name' => 'Novo Item', 'icon' => 'mdi mdi-apps', 'url' => "template_background_item_edit.php?template_background_id=$template_background_id" ) );
if(!$is_new) $webadm->add_button( array( 'attribs' => array('type' => 'submit', 'id' => 'btns', 'name' => 'btns', 'value' => '1'), 'name' => 'Novo Item', 'icon' => 'mdi mdi-note-multiple' ) );

$webadm->add_parent( array( 'name' => 'Template - Backgrounds', 'url' => 'templates.php?bck' ) );

$webadm->add_plugins('datatables', 'sweetalert', 'select2');
$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>
#editor-container {
	width: 100%;
	height: calc(100vh - 700px);
}

.card-body{
	 min-height: 510px;
}

</style>

<?php if ( $_SESSION['type']=='success' ) { $_SESSION['type']='';?>
	<div id="success-alert" class="alert alert-success">
	<?php echo $_SESSION['message']; $_SESSION['message']=''; ?>
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span></button>
	</div>
<?php } ?>

<form id="target" action="<?php echo "?id={$template_background_id}&post"; ?>" method="post" enctype="multipart/form-data">	
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
						
						<div class="col-md-12">
							<label>Nome</label>
							<input required id="name_template_background" name="name" type="text" class="form-control" placeholder="Nome do Background" value="<?php echo html( $content_root['name'] ); ?>">
						</div>
						
					</div>
					
				</div>
				
				<button name="save" id="save" type="submit" class="btn btn-info" value=1><i class="fa fa-check m-r-10"></i>Salvar</button>
				
			</div>
			
		</div>

		<div class="card-body">
		
			<?php if(!$is_new){ ?>
				<div class="table-responsive">
					<table id="contents" class="display pageResize nowrap table table-striped table-bordered">
						<thead>
							<tr>
								<th>Seq.</th>
								<th><i class="mdi mdi-drag-vertical"></i></th>
								<th>Nome</th>
								<th>Ação</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$aux =	explode(",", $content['template_background_items']);
							if(!empty ($aux[0])){
							for($i = 0; $i < count($aux); $i++) {
								extract( $db->content( $aux[$i], 'template_background_items'), EXTR_PREFIX_ALL, 'i' );
									if (!empty ($i_status)) {
							?>
							<tr template_background-id="<?php echo $i_template_background; ?>" template_background-item-id="<?php echo $i_id; ?>" content-name="<?php echo html( $i_name ); ?>">
								<td><?php echo $i; ?></td>
								<td class="align-middle dt-nowrap">
									<i class="mdi mdi-drag-vertical"></i>
								</td>
								
								<td class="dt-nowrap text-nowrap align-middle">
									<a href="<?php echo $i_edit_url; ?>">
										<h4><?php echo html( $i_name ); ?></h4>
									</a>
								</td>
								
								<td class="text-center align-middle">
									
									<div class="btn-group btn-group-justified">
										<?php if ($i_status) { ?>
										<a class="btn btn-danger text-white btn-sm toggle-template_background_item" data-toggle="tooltip" title="Remover Item">
											<i class="mdi mdi-close"></i>
										</a>
										<?php } else { ?>
										<a class="btn btn-info text-white btn-sm toggle-template_background_item" data-toggle="tooltip" title="Ativar Item">
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
				<?php } ?>
			
			
		</div>
	</div>
</form>


<script>
$(function() {
	
	$("#btns").click(function(){
		/*var teste = $("#name_template_background").val();
		if($("#name_template_background").val() == ""){
			alert("Preencha Nome da Background'");
		}*/
		$("#target").submit();
	});
	
	var contents = $('#contents').DataTable({		
		language: { url: "<?php echo url( 'admin/plugins/datatables-language/Portuguese-Brasil.json' ) ?>" },
		rowReorder: true,
		paging: false,
		searching: false,
		scrollY: 'calc(100vh - (540px - 52px))',
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
		
		seq.push( parseInt( $(this).attr('template_background-item-id') ) );
		
	});

	$.post( "template_background_items.php?id=<?php echo $template_background_id ?>&update", { template_background_items: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-template_background_item').click(function(){
		
		var template_background_itemId = $(this).closest('tr').attr( "template_background-item-id" );
		var template_background_Id = $(this).closest('tr').attr( "template_background-id" );
		
		if($(this).hasClass( "btn-danger" )){
			swal({   
				title: "Deseja mesmo remover esse Item?",
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#DD6B55",   
				confirmButtonText: "Sim, remover!",   
				closeOnConfirm: false,
				cancelButtonText: "Cancelar"
			}, function(){
				
				window.location = "<?php echo url('admin/template_background_item_edit.php'); ?>?id=" + template_background_itemId + "&template_background_id=" + template_background_Id +
					"&toggle_active";
				
			});
		} else
			window.location = "<?php echo url('admin/template_background_item_edit.php'); ?>?id=" + template_background_itemId + "&template_background_id=" + template_background_Id +
			"&toggle_active";
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