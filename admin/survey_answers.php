<?php

// API

include('includes/common.php');
$_SESSION['type']=$_SESSION['type'];

// Codigo da pagina

need_login();

// Controller
$survey_id = (int)get('id');
$is_new = $survey_id == 0;

//campo de erro
$error = "";

//$manager_user_logado = $useradm->get_property('id');

//////////****** content_root ******//////////

function get_content_root($insert=true) {
	
	//global $is_new;
	
	$content =  array(
		'title' => get_post('title'),
		'text' => get_post('text'),
		'is_pin' => (int)get_post('is_pin'),
		'closing_date' => get_post('closing_date')
	);
	
	return 	$content;
	
}

if ($survey_id > 0) {

	$content_root = $db->content( $survey_id , 'surveys' );
	//print_r($content_root); die();
	if (has('post')) {
		
		$conteudo = get_content_root();

		if ( $conteudo['is_pin'] > 0 ){
		/*
			$db->query( "update `surveys` set is_pin=0
					WHERE id<>$survey_id" ) ;*/
			
			$desfix_arr = $db->query("SELECT * FROM surveys WHERE is_pin = 1");
			//print_r($desfix_arr);die();
			foreach($desfix_arr as $desfix) {
				
				$db->update('surveys', $desfix['id'], array('is_pin' => 0));
				
			}
			
			
					
		}

		$db->update('surveys', $survey_id, get_content_root());
		
		$save = get_post('save');
		
		if($save){
			
			redirect("survey_answers.php?id=$survey_id", array(
				'message' => 'Enquete alterada com sucesso.',
				'type' => 'success'
			));
		
		}
		

	} elseif (has('toggle_active')) {

		$db->update('surveys', $survey_id, array('status' => $content_root['status'] ? 0 : 1));
			
		redirect('surveys.php', array(
			'message' => 'Status alterado com sucesso.',
			'type' => 'success'
		));
		
	}
	
} elseif (has('post')) {
	$content_root = $db->content( get_content_root(false), 'surveys' );

	if(empty($content_root['text'])){
		
			$error = "Preencha o campo 'Texto da Enquete'";
			//print_r($error); die();
	} else {

		$survey_id = $db->insert('surveys', get_content_root(true));

		$save = get_post('save');
		
		if($save){
			redirect("survey_answers.php?id=$survey_id", array(
				'message' => 'Galeria adicionado com sucesso.',
				'type' => 'success'
			));
		}
	}
	
} else {
	
	$content_root = $db->content( get_content_root(false), 'surveys' );
	
}

//////////****** content_root  End******//////////

if ($survey_id > 0) {
	
	$content = $db->content( $db->select_id('surveys', $survey_id), 'surveys' );

	if (has('update')) {
		
		$json = array();
		
		$db->update('surveys', $survey_id, $json);
		
		echo json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
		
		$_SESSION['message'] = 'Registro alterado com sucesso.';	    
		$_SESSION['type'] = 'success';
		
		exit;
		
	}

}
//print_r($content); die();

$webadm->set_page( array( 'name' => 'Enquete' ) );

if(!$is_new) $webadm->add_button( array( 'name' => 'Nova Resposta', 'icon' => 'mdi mdi-playlist-plus', 'url' => "survey_answer_edit.php?survey_id=$survey_id" ) );

$webadm->add_parent( array( 'name' => 'Enquetes', 'url' => 'surveys.php' ) );

//$webadm->add_plugins('datatables', 'sweetalert');
$webadm->add_plugins('datatables', 'sweetalert', 'quilljs');

$webadm->start_panel();

?>
<!-- ============================================================== -->
<!-- Start Page Content -->
<!-- ============================================================== -->
<style>

#survey_text {
	width: 100%;
	background:#fff;
	height: calc(10vh); 
	
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

<form id="target" action="<?php echo "?id={$survey_id}&post"; ?>" method="post" enctype="multipart/form-data">

	<?php if ( $error ) { ?>
		<div class="alert alert-danger">
	<?php echo html( $error ); ?>
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span></button>
		</div>
	<?php } ?>
	
	<div class="card no-margin">
		
		<div class="card-header">

			<div class="row">
			
			<!--
				<div class="form-group m-b-10">
					<label>Título</label>
					<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="php echo html($content['title']); ?>" required />
				</div>
			-->
				<div class="col-md-11">
					<div class="row">
					
					<div class="col-md-8 m-b-10">
					
							<label>Título</label>
							<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="<?php echo html($content_root['title']); ?>" required />
							
						
					</div>
					
					<div class="col-md-2">
					
							<label style="width:100%">Data de Encerramento</label>
							<input type="date" name="closing_date" class="form-control"  value="<?php echo html($content_root['closing_date']); ?>">
					</div>
					
					
					
					<?php if(!$is_new) { ?>
					
						<div class="col-md-2" style="">
						<label style="width:100%">Destacar</label>
							<div class="switch" style="margin-top: 5px;">
								<label style="display: inline-flex;">
									<input  name="is_pin" type="checkbox" value='1'
										<?php echo $content['is_pin']== '1' ? ' checked' : ''; ?>
									>
									<span class="lever switch-col-light-blue" style="margin-top: 8px;"></span>
									<!--<div style="font-size: 18px;margin-top: 4px;margin-left: 10px;">Destacar</div>-->
								</label>                                
							</div>
						</div>
						
					<?php } ?>
					<!--
						<div class="col-md-12 m-b-10">
							<label>Título</label>
							<input name="title" type="text" class="form-control" placeholder="Digite o Título" value="<php echo html($content_root['title']); ?>" required />
							
						</div>
						
						
						
						
						<div class="col-md-12 m-b-10">
							<label style="width:100%">Data de Encerramento</label>
							<input type="date" id="birthday" name="birthday" class="form-control" style="width:160px;">
						</div>
						-->
						
						
						
						
						
						
						<div class="col-md-12">
							<label>Pergunta</label>
							<div id="survey_text"><?php echo $content_root['text']; ?></div>
							<textarea style="display: none" id="text" name="text"></textarea>
							
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
							<th>Respostas</th>
							<th>Ação</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$aux =	explode(",", $content['survey_answers']);
						if(!empty ($aux[0])){
						for($i = 0; $i < count($aux); $i++) {
							extract( $db->content( $aux[$i], 'survey_answers'), EXTR_PREFIX_ALL, 'i' );
								if (!empty ($i_status)) {
						?>
						<tr survey-id="<?php echo $i_survey; ?>" survey-answer-id="<?php echo $i_id; ?>" ">
							<td><?php echo $i; ?></td>
							<td class="align-middle dt-nowrap">
								<i class="mdi mdi-drag-vertical"></i>
							</td>
							
							<td class="dt-nowrap text-nowrap align-middle">
								<a href="<?php echo $i_edit_url; ?>">
									<h4><?php echo htmltotext($i_text, 50); ?></h4>
								</a>
							</td>
							
							<td class="text-center align-middle">
								
								<div class="btn-group btn-group-justified">
									<?php if ($i_status) { ?>
									<a class="btn btn-danger text-white btn-sm toggle-survey-answer" data-toggle="tooltip" title="Remover Resposta">
										<i class="mdi mdi-close"></i>
									</a>
									<?php } else { ?>
									<a class="btn btn-info text-white btn-sm toggle-survey-answer" data-toggle="tooltip" title="Ativar Resposta">
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

<script src="js/survey_answers.js"></script>

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
		
		seq.push( parseInt( $(this).attr('survey-answer-id') ) );
		
	});
	
	//console.log( seq );
	
	$.post( "survey_answers.php?id=<?php echo $survey_id ?>&update", { survey_answers: seq.join(',') })
		.done(function( data ) {

			console.log( data );

		});

	} );
	
	$('.toggle-survey-answer').click(function(){
		
		var surveyId = $(this).closest('tr').attr( "survey-id" );
		var contentId = $(this).closest('tr').attr( "survey-answer-id" );
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
				
				window.location = "<?php echo url('admin/survey_answer_edit.php'); ?>?survey_id=" + surveyId + 
					"&toggle_active" + "&id=" + contentId;
				
			});
		} else
			window.location = "<?php echo url('admin/survey_answer_edit.php'); ?>?survey_id=" + surveyId + 
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