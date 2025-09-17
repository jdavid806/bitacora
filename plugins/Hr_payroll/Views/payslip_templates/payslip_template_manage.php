<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<?php echo form_hidden('internal_id',$internal_id); ?>

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if (hrp_has_permission('hr_payroll_can_create_hrp_payslip_template') || is_admin()) { ?>
							<a href="#" onclick="new_payslip_template(); return false;"class="btn btn-info pull-left text-white">
								<span data-feather="plus-circle" class="icon-16" ></span> <?php echo app_lang('_new'); ?>
							</a>
						<?php } ?>
					</div>
				</div>
				
				<div class="table-responsive">

					<?php render_datatable1(array(
						app_lang('id'),
						app_lang('templates_name'),
						app_lang('staff_id_created'),
						app_lang('date_created'),
						"<i data-feather='menu' class='icon-16'></i>",

					),'payslip_template_table'); ?>

				</div>

			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="payslip_template_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog popup-with modal-lg">
		<?php echo form_open(get_uri("hr_payroll/payslip_template"), array("id" => "add_payslip_template", "class" => "general-form", "role" => "form")); ?>

		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('edit_payslip_template'); ?></span>
					<span class="add-title"><?php echo app_lang('new_payslip_template'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>

			<div class="modal-body">
				<div id="additional_payslip_template"></div>
				<div id="additional_payslip_column"></div>

				<div class="row">
					<div class="col-md-12">
						<label class="payslip-template-lable"><?php echo app_lang('except_staff_note'); ?></label>
					</div>
					<div class="col-md-12">
						<?php echo render_input1('templates_name','templates_name','','text', [], [], '', '', true); ?>
					</div>            
				</div>
				<div class="row">
					<div class="col-md-6 hide"> 

						<div class="form-group">
							<label for="payslip_id_copy" class="control-label"><?php echo app_lang('payslip_id_copy_lable'); ?></label>
							<select name="payslip_id_copy" id="payslip_id_copy" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">

							</select>
						</div>
					</div>
					<div class="col-md-12">
						<div class="form-group">
							<label for="payslip_columns" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('payslip_columns_lable'); ?></label>
							<select name="payslip_columns[]" id="payslip_columns" class="select2 validate-hidden"  data-live-search="true" data-width="100%" multiple="true" data-actions-box="true" data-live-search="true" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required>

							</select>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="department_id" class="control-label"><?php echo app_lang('staff_departments'); ?></label>
							<select name="department_id[]" class="form-control select2 validate-hidden" multiple="true" id="department_id" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_all_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($departments as $department_key => $department) { ?>
									<option value="<?php echo html_entity_decode($department['id']); ?>" ><?php  echo html_entity_decode($department['title']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="role_employees" class="control-label"><?php echo app_lang('role'); ?></label>
							<select name="role_employees[]" class="form-control select2 validate-hidden" multiple="true" id="role_employees" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_all_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($roles as $key => $role) { ?>
									<option value="<?php echo html_entity_decode($role['id']); ?>" ><?php  echo html_entity_decode($role['title']); ?></option>
								<?php } ?>
							</select>

						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="staff_employees" class="control-label"><?php echo app_lang('staff'); ?></label>
							<select name="staff_employees[]" class="form-control select2 validate-hidden" multiple="true" id="staff_employees" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_all_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($staffs as $key => $staff) { ?>

									<option value="<?php echo html_entity_decode($staff['id']); ?>" ><?php  echo html_entity_decode($staff['first_name'].' '.$staff['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							<label for="except_staff" class="control-label"><?php echo app_lang('except_staff'); ?></label>
							<select name="except_staff[]" class="form-control select2 validate-hidden" multiple="true" id="except_staff" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($staffs as $key => $staff) { ?>

									<option value="<?php echo html_entity_decode($staff['id']); ?>" ><?php  echo html_entity_decode($staff['first_name'].' '.$staff['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hrp_close'); ?></button>
				<button type="button" class="btn btn-info payslip_template_checked text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div> 



<?php require 'plugins/Hr_payroll/assets/js/payslip_templates/payslip_template_manage_js.php';?>


</body>
</html>