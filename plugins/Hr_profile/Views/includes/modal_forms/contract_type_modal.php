<?php echo form_open(get_uri("hr_profile/contract_type/".$id), array("id" => "add_contract_type-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 
						$name_contracttype = '';
						$description = '';
						if(isset($contract_type_data)){
							$name_contracttype = $contract_type_data->name_contracttype;
							$description = $contract_type_data->description;
						}
						?>
						<div class="col-md-12">
							<?php 
							echo render_input1('name_contracttype','name', $name_contracttype, '', [], [], '', '', true); ?>
						</div>

						<div class="col-md-12">
							<p class="bold"><?php echo app_lang('hr_hr_description'); ?></p>
							<?php echo render_textarea1('description','', $description,array(),array(),'','tinymce'); ?>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
		<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('save'); ?></button>

	</div>
</div>
<?php echo form_close(); ?>