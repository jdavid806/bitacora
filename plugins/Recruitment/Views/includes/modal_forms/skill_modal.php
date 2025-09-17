<?php echo form_open(get_uri("recruitment/skill/".$id), array("id" => "add_skill-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$skill_name = '';

						if(isset($skill_data)){
							$skill_name = $skill_data->skill_name;
						}
						?>

						<div class="col-md-12">
							<?php echo render_input1('skill_name', 'skill_name', $skill_name, '', [], [], '', '', true); ?>
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
