<?php echo form_open(get_uri("hr_profile/job_position"), array("id" => "job_position-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">

		</ul>
		<?php 

		$position_id = '';
		$position_code = $job_position_code_sample;
		$position_name = '';
		$hr_job_p_id = '';
		$department_id = '';
		$job_position_description = '';
		if(isset($job_position_data)){
			$position_id = $job_position_data->position_id;
			$position_code = $job_position_data->position_code;
			$position_name = $job_position_data->position_name;
			$hr_job_p_id = $job_position_data->job_p_id;
			$department_id = $job_position_data->department_id;
			$job_position_description = $job_position_data->job_position_description;
		}

		?>

		<input type="hidden" name="position_id" value="<?php echo html_entity_decode($position_id); ?>" />

		<div class="row">
			<div class="col-md-6">
				<?php echo render_input1('position_code', 'hr_position_code', $position_code, '', [], [], '', '', true); ?>
			</div>
			<div class="col-md-6">
				<?php echo render_input1('position_name', 'hr_position_name', $position_name, '', [], [], '', '', true); ?>
			</div>

		</div>

		<div class="row">
			<div class="col-md-6">
				<?php echo render_select1('job_p_id', $job_p_id, array('job_id', 'job_name'), 'hr_job_p_id', $hr_job_p_id); ?>
			</div>

			<div class="col-md-6">
				<div class="form-group">
					<label for="department_id" class="control-label get_id_row" value ="0" ><?php echo app_lang('departments'); ?></label>

					<select name="department_id[]" class="select2 validate-hidden" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-action-box="true" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>">
						<?php foreach ($hr_profile_get_department_name as $dp) {?>
							<?php 
							$selected = "";
							$arr_department = explode(",", $department_id);
							if(in_array($dp['id'], $arr_department)){
								$selected = "selected='selected'";
							}

							?>
							<option value="<?php echo html_entity_decode($dp['id']); ?>" <?php echo html_entity_decode($selected) ?>><?php echo html_entity_decode($dp['title']); ?></option>
						<?php }?>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<?php echo render_textarea1('job_position_description', 'hr_job_descriptions', $job_position_description, array(), array(), '', 'tinymce'); ?>

			</div>
		</div>

	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
	<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
<?php require 'plugins/Hr_profile/assets/js/job_position/position/position_modal_js.php';?>
