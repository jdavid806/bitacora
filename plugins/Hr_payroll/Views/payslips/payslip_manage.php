<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<?php echo form_hidden('internal_id',$internal_id); ?>

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if (hrp_has_permission('hr_payroll_can_create_hrp_payslip') || is_admin()) { ?>
							<a href="#" onclick="new_payslip(); return false;"class="btn btn-info pull-left mright10 display-block text-white">
								<span data-feather="plus-circle" class="icon-16" ></span> <?php echo app_lang('_new'); ?>
							</a>
						<?php } ?>
					</div>
				</div>
				
				<div class="table-responsive">

					<?php render_datatable1(array(
						app_lang('id'),
						app_lang('payslip_name'),
						app_lang('payslip_template'),
						app_lang('payslip_month'),
						app_lang('hrp_time_range'),
						app_lang('staff_id_created'),
						app_lang('date_created'),
						app_lang('status'),
						app_lang('options'),
						"<i data-feather='menu' class='icon-16'></i>",

					),'payslip_table'); ?>

				</div>

			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="payslip_template_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog popup-with modal-lg">
		<?php echo form_open(get_uri("hr_payroll/payslip"), array("id" => "add_payslip", "class" => "general-form", "role" => "form")); ?>

		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('edit_payslip'); ?></span>
					<span class="add-title"><?php echo app_lang('new_payslip'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>

			<div class="modal-body">
				<div id="additional_payslip_template"></div>
				<div id="additional_payslip_column"></div>

				<div class="row">
					<div class="col-md-3">
						<?php echo render_input1('payslip_month','month',date('Y-m'), 'month', [], [], '', '', true); ?>
					</div>
					<div class="col-md-9">
						<?php echo render_input1('payslip_range', 'hrp_time_range', '', '', [], ["data-bs-original-title" => app_lang("hrp_tooltip_hrp_time_range"), "data-bs-toggle" => "tooltip"]); ?>   
					</div>
					
					<div class="col-md-12">
						<?php echo render_input1('payslip_name','payslip_name','','text', [], [], '', '', true); ?>
					</div>            
				</div>

				<div class="row">
					<div class="col-md-6"> 

						<div class="form-group">
							<label for="payslip_template_id" class="control-label"><?php echo app_lang('payslip_template_id_lable'); ?></label>
							<select name="payslip_template_id" id="payslip_template_id" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required>

							</select>
						</div>
					</div>
					<div class="col-md-6"> 

						<div class="form-group">
							<label for="pdf_template_id" class="control-label"><?php echo _l('pdf_payslip_template_label'); ?></label>
							<select name="pdf_template_id" id="pdf_template_id" class="select2"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

							</select>
						</div>
					</div>
				</div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hrp_close'); ?></button>
				<button type="button" class="btn btn-info payslip_checked text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>

		<div id="box-loading"></div>

	</div>


	<div class="modal fade" id="edit_payslip_template_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog popup-with modal-lg">
		<?php echo form_open(get_uri("hr_payroll/edit_payslip"), array("id" => "edit_payslip", "class" => "general-form", "role" => "form")); ?>

		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('edit_payslip'); ?></span>
					<span class="add-title"><?php echo app_lang('new_payslip'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>

			<div class="modal-body">
				<div id="additional_edit_payslip_id"></div>

				<div class="row">
					<div class="col-md-6">
						<?php echo render_input1('payslip_name','payslip_name','','text', [], [], '', '', true); ?>
					</div>  
					<div class="col-md-6"> 

						<div class="form-group">
							<label for="pdf_template_id" class="control-label"><?php echo _l('pdf_payslip_template_label'); ?></label>
							<select name="pdf_template_id" id="pdf_template_id" class="select2"  data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">

							</select>
						</div>
					</div>          
				</div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hrp_close'); ?></button>
				<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
				</div>
			</div>
			<?php echo form_close(); ?>
		</div>

		<div id="box-loading"></div>

	</div>

</div> 

<?php require 'plugins/Hr_payroll/assets/js/payslips/payslip_manage_js.php';?>

</body>
</html>