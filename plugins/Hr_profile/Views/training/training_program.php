<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "training_programs";
			echo view("Hr_profile\Views/training/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr__training_program'); ?></h4>
					<div class="title-button-group">

						<?php  if(is_admin() || hr_has_permission('hr_profile_can_create_hr_training')) { ?>
							<a href="#"  onclick="new_training_process(); return false;" class="btn btn-info text-white" >
								<span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_hr_add'); ?>
							</a>

						<?php } ?>

					</div>
				</div>

				<div class="modal fade bulk_actions" id="table_training_program_bulk_actions" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo app_lang('hr_bulk_actions'); ?></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

							</div>
							<div class="modal-body card-body">
								<?php if(hr_has_permission('hr_profile_can_delete_hr_training') || is_admin()){ ?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" class="form-check-input" name="mass_delete" id="mass_delete">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<?php if(hr_has_permission('hr_profile_can_delete_hr_training') || is_admin()){ ?>
									<a href="#" class="btn btn-info text-white" onclick="training_program_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_hr_training')) { ?>
					<a href="#"  onclick="training_program_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_training_program" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>



				<div class="table-responsive">

					<?php 
					$table_data = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_training_program" class="form-check-input"><label></label></div>',

						app_lang('id'),
						app_lang('name'),
						app_lang('hr_training_type'),
						app_lang('hr_hr_description'),
						app_lang('hr_mint_point'),
						app_lang('hr_datecreator'),
						"<i data-feather='menu' class='icon-16'></i>",

					);

					render_datatable1($table_data,'table_training_program',
						array('customizable-table'),
						array(
							'id'=>'table-table_training_program',
							'data-last-order-identifier'=>'table_training_program',
						)); 

						?>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="job_position_training" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg new_job_positions_dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						<span class="add-title-training"><?php echo app_lang('hr_edit_training_process'); ?></span>
						<span class="edit-title-training"><?php echo app_lang('hr_new_training_process'); ?></span>
					</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

				</div>
				<?php echo form_open_multipart(get_uri("hr_profile/job_position_training_add_edit"), array("id" => "job_position_training_add_edit", "class" => "job_position_training_add_edit general-form", "role" => "form")); ?>

				<div class="modal-body card-body">
					<div id="additional_form_training"></div>
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="interview_infor">

							<div class="row">
								<div class="col-md-6">
									<?php echo render_input1('training_name', 'hr_training_name', '', '', [], [], '', '', true); ?>
								</div>
								<div class="col-md-6">
									<label for="training_type" class="control-label"><span class="text-danger">* </span><?php echo app_lang('hr_training_type'); ?></label>
									<select onchange="training_type_change(this)" name="training_type" class="select2 validate-hidden" id="training_type" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
										<option value="">-</option> 

										<?php foreach ($type_of_trainings as $key => $value) { ?>
											<option value="<?php echo html_entity_decode($value['id']) ?>" <?php if(isset($position_training) && $position_training->training_type ==  $value['id'] ){echo 'selected';} ?> ><?php echo html_entity_decode($value['name'])  ?></option>

										<?php } ?>
										
									</select>

								</div>
							</div>

							<div class="row ">
								<div class="col-md-6">
									<label for="position_training_id" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo app_lang('hr_training_item'); ?></label>

									<select name="position_training_id[]" class="select2 validate-hidden mb-5" id="position_training_id[]" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-sl-id="e_criteria[0]" required="1" > 
									</select>
								</div>
								<div class="col-md-6">
									<?php $mint_point_f="1";
									$min_p =[];
									$min_p['min']='0';
									?>
									<?php echo render_input1('mint_point','hr_mint_point',$mint_point_f,'number', $min_p, [], '', '', true); ?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<div class="checkbox checkbox-primary">
											<input  type="checkbox" id="additional_training" name="additional_training"  value="additional_training" >
											<label for="additional_training"><?php echo app_lang('hr_additional_training'); ?></label>
										</div>
									</div>
								</div>
							</div>

							<div class="row additional_training_hide hide">
								<div class="col-md-12">
									<div class="form-group">
										<label for="staff_id" class="control-label"><?php echo app_lang('hr_hr_staff_name'); ?></label>
										<select name="staff_id[]" data-live-search="true" class="select2 validate-hidden staff_additional" id="staff_id" data-width="100%" multiple="true"data-actions-box="true" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" > 
											<?php foreach ($staffs as $staff){ ?>
												<option value="<?php echo html_entity_decode($staff['id']) ?>"><?php echo html_entity_decode($staff['first_name'].' '.$staff['last_name']); ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								
								<div class="col-md-6">
									<?php
									echo render_date_input1('time_to_start','hr_time_to_start'); ?>
								</div>
								<div class="col-md-6">
									<?php
									echo render_date_input1('time_to_end','hr_time_to_end'); ?>
								</div>
							</div>

							<div class="row mb-4 onboading_hide">
								<div class="col-md-6">

									<label for="department_id" class="control-label get_id_row" value ="0" ><?php echo app_lang('hr_department'); ?></label>
									<select onchange="department_change(this)" name="department_id[]" class="select2 validate-hidden" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">
										<?php foreach($hr_profile_get_department_name as $dp){ ?> 
											<option value="<?php echo html_entity_decode($dp['id']); ?>"><?php echo html_entity_decode($dp['title']); ?></option>
										<?php } ?>

									</select>

								</div>

								<div class="col-md-6">

									<label for="job_position_id" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo app_lang('hr__position_apply'); ?></label>

									<select name="job_position_id[]" class="select2 validate-hidden" id="job_position_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
										<?php foreach($get_job_position as $p){ ?> 
											<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($member) && $member->job_position == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
										<?php } ?>
									</select>
									<div class="clearfix"></div>
									<br>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">

									<p class="bold"><?php echo app_lang('hr_hr_description'); ?></p>
									<?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
									<?php echo render_textarea1('description','',$contents,array(),array(),'','tinymce'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">

					<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>
					<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
				</div>
				<?php echo form_close(); ?>                 
			</div>
		</div>
	</div>


<?php require 'plugins/Hr_profile/assets/js/training/training_program_js.php';?>
</body>
</html>
