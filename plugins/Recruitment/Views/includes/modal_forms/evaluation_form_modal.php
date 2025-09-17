<?php echo form_open(get_uri("recruitment/evaluation_form/".$id), array("id" => "add_evaluation_form-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$form_name = '';
						$job_position = '';
						

						if(isset($evaluation_form_data)){
							$form_name = $evaluation_form_data->form_name;
							$job_position = $evaluation_form_data->position;
						}
						?>

						<div class="col-md-12">
							<?php echo render_input1('form_name', 'form_name', $form_name, '', [], [], '', '', true); ?>
							
							<div class="form-group">
								<label for="job_position"><small class="req text-danger">* </small><?php echo app_lang('job_position'); ?></label>
								<select name="job_position" id="job_position" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('job_position'); ?>">
									<option value="">- <?php echo app_lang('all'); ?></option>

									<?php foreach($positions as $s) { ?>
										<option value="<?php echo html_entity_decode($s['position_id']); ?>" <?php if($s['position_id'] == $job_position){ echo "selected";} ?>><?php echo html_entity_decode($s['position_name']); ?></option>
									<?php } ?>
								</select>
							</div>

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
<?php require 'plugins/Recruitment/assets/js/settings/modal_forms/evaluation_form_modal_js.php';?>
