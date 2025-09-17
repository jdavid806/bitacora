<?php echo form_open(get_uri("recruitment/job_position/".$id), array("id" => "add_job_position-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$position_name = '';
						$position_description = '';
						$job_skill = '';
						$industry_id = '';

						if(isset($job_position_data)){
							$position_name = $job_position_data->position_name;
							$position_description = $job_position_data->position_description;
							$job_skill = explode(",", $job_position_data->job_skill);
							$industry_id = $job_position_data->industry_id;
						}
						?>

						<div class="col-md-12">
							<?php echo render_input1('position_name', 'job_position', $position_name, '', [], [], '', '', true); ?>

							<?php echo render_select1('job_skill[]', $skills, array('id', 'skill_name'), 'skill_name', $job_skill, ['multiple' => true], [], '', '', false); ?>

							<?php echo render_select1('industry_id', $industry_list, array('id', 'industry_name'), 'industry', $industry_id); ?>

							<?php echo render_textarea1('position_description', 'description', $position_description) ?>
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
<?php require 'plugins/Recruitment/assets/js/settings/modal_forms/job_position_modal_js.php';?>
