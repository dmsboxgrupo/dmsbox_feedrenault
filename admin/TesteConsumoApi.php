

<form id="target" action="<?php echo "https://feedrenault.com.br/api.php?q=upload&secret_key=0627c6dc2673e376c357435ec89a60b7031a79e8"; ?>" method="post" enctype="multipart/form-data">
	<div class="card no-margin">		
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">		

					<!--Imagem 1 -->
					<div class="form-group m-b-0">
						
							<div class="dropzone">
								<input name="file" type="file" />
							</div>
							<div class="form-actions m-t-20">
								<button type="submit" class="btn btn-block btn-info text-uppercase"><i class="fa fa-check m-r-5"></i> Subir Imagem</button>
							</div>
						
					</div>
					
				</div>				
			</div>
		</div>
	</div>
</form>