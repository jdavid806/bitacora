<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_bonus_kpi'); ?></h4>
					<div class="title-button-group">
						
					</div>
				</div>
				<div class="row ml2 mr5 filter_by">

					<div class="col-md-2">
						<?php echo render_input1('month_timesheets','month',date('Y-m'), 'month'); ?>   
					</div>

					<div class="col-md-3 leads-filter-column pull-left">
						<?php echo render_select1('department_timesheets',$departments,array('id', 'title'),'hrp_department',''); ?>
					</div>

					<div class="col-md-3 leads-filter-column pull-left">
						<div class="form-group">
							<label for="staff_timesheets" class="control-label"><?php echo app_lang('hrp_staff'); ?></label>
							<select name="staff_timesheets[]" class="form-control select2 validate-hidden" multiple="true" id="staff_timesheets" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-live-search="true"> 
								<?php foreach ($staffs as $key => $staff) { ?>

									<option value="<?php echo html_entity_decode($staff['id']); ?>" ><?php  echo html_entity_decode($staff['first_name'].' '.$staff['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

				</div>

				<?php echo form_open(get_uri("hr_payroll/add_bonus_kpi"), array("id" => "add_bonus_kpi", "class" => "general-form", "role" => "form")); ?>


				<div class="table-responsive pt15 pl15 pr15">

					<div class="col-md-12">
						<small><?php echo app_lang('handsontable_scroll_horizontally') ?></small>
					</div>
					<div id="total_insurance_histtory" class="col-md-12">
						<div id="example" class="hot handsontable htColumnHeaders" >
						</div>
						<?php echo form_hidden('bonus_kpi_value'); ?>
						<?php echo form_hidden('month', date('m-Y')); ?>
						<?php echo form_hidden('allowance_commodity_fill_month'); ?>
						<?php echo form_hidden('latch'); ?>

					</div>

				</div>

				<div class="modal-footer">
					<?php if(hrp_has_permission('hr_payroll_can_create_hrp_bonus_kpi') || hrp_has_permission('hr_payroll_can_edit_hrp_bonus_kpi')){ ?>
						<button type="button" class="btn btn-info pull-right save_bonus_kpi mleft5 text-white" onclick="save_bonus_kpi(this); return false;"><span data-feather="check-circle" class="icon-16" ></span> <?php echo html_entity_decode($button_name); ?></button>
					<?php } ?>

				</div>

				<?php echo form_close(); ?>

			</div>
		</div>
	</div>
</div>


<?php require 'plugins/Hr_payroll/assets/js/bonus/bonus_js.php';?>


</body>
</html>
