<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hrp_salary_deductions'); ?></h4>
					<div class="title-button-group">
						
					</div>
				</div>
				<div class="row ml2 mr5 filter_by">

					<div class="col-md-2">
						<?php echo render_input1('month_deductions','month',date('Y-m'), 'month'); ?>   
					</div>

					<div class="col-md-3 leads-filter-column pull-left">
						<?php echo render_select1('department_deductions',$departments,array('id', 'title'),'hrp_department',''); ?>
					</div>

					<div class="col-md-3 leads-filter-column pull-left">
						<div class="form-group">
							<label for="role_deductions" class="control-label"><?php echo app_lang('role'); ?></label>
							<select name="role_deductions[]" class="form-control select2 validate-hidden" multiple="true" id="role_deductions" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($roles as $key => $role) { ?>
									<option value="<?php echo html_entity_decode($role['id']); ?>" ><?php  echo html_entity_decode($role['title']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="col-md-3 leads-filter-column pull-left">

						<div class="form-group">
							<label for="staff_deductions" class="control-label"><?php echo app_lang('hrp_staff'); ?></label>
							<select name="staff_deductions[]" class="form-control select2 validate-hidden" multiple="true" id="staff_deductions" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($staffs as $key => $staff) { ?>

									<option value="<?php echo html_entity_decode($staff['id']); ?>" ><?php  echo html_entity_decode($staff['first_name'].' '.$staff['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>

					</div>


				</div>

				<?php echo form_open(get_uri("hr_payroll/add_manage_deductions"), array("id" => "add_manage_deductions", "class" => "general-form", "role" => "form")); ?>


				<div class="table-responsive pt15 pl15 pr15">

					<div class="col-md-12">
						<small><?php echo app_lang('handsontable_scroll_horizontally') ?></small>
					</div>
					<div id="total_insurance_histtory" class="col-md-12">
						<div id="hrp_deductions_value" class="hot handsontable htColumnHeaders" >
						</div>
						<?php echo form_hidden('hrp_deductions_value'); ?>
						<?php echo form_hidden('month', date('m-Y')); ?>
						<?php echo form_hidden('deductions_fill_month'); ?>
						<?php echo form_hidden('department_deductions_filter'); ?>
						<?php echo form_hidden('staff_deductions_filter'); ?>
						<?php echo form_hidden('role_deductions_filter'); ?>

						<?php echo form_hidden('hrp_deductions_rel_type'); ?>

					</div>

				</div>

				<div class="modal-footer">
					<?php if(hrp_has_permission('hr_payroll_can_create_hrp_deduction') || hrp_has_permission('hr_payroll_can_edit_hrp_deduction')){ ?>
						<button type="button" class="btn btn-info pull-right save_manage_deductions mleft5 text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo html_entity_decode($button_name); ?></button>


						<a href="<?php echo get_uri('hr_payroll/import_xlsx_deductions'); ?>" class=" hide btn mright5 btn-default pull-right">
							<?php echo app_lang('hrp_import_excel'); ?>
						</a>
					<?php } ?>

				</div>

				<?php echo form_close(); ?>

			</div>
		</div>
	</div>
</div>


<?php require 'plugins/Hr_payroll/assets/js/deductions/deductions_manage_js.php';?>


</body>
</html>