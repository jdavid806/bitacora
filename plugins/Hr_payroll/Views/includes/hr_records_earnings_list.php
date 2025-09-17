<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "hr_records_earnings_list";
			echo view("Hr_payroll\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('earnings_list_hr_records'); ?></h4>
				</div>

				<?php echo form_open_multipart(get_uri("hr_payroll/setting_earnings_list_hr_records"), array("id" => "add_earnings_list_hr_records", "class" => "general-form", "role" => "form")); ?>

				<div class="table-responsive pt15 pl15 pr15">
					<div id="inventory_min">
					</div>   
					<div class="form"> 
						<div id="earnings_list_hr_records_hs" class="col-md-12 add_handsontable handsontable htColumnHeaders">

						</div>
						<?php echo form_hidden('earnings_list_hr_records_hs'); ?>
					</div>
				</div>
				<?php if(hrp_has_permission('hr_payroll_can_create_hrp_setting') || hrp_has_permission('hr_payroll_can_edit_hrp_setting')){ ?>
					<div class="modal-footer">

						<button type="button" class="btn btn-primary add_earnings_list_hr_records" title="<?php echo app_lang('synchronized_hr_salary_allowance_tye_title'); ?>"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('hrp_synchronized'); ?></button>
					</div>
				<?php } ?>

				<?php echo form_close(); ?>

			</div>
		</div>
	</div>
</div>
<?php require 'plugins/Hr_payroll/assets/js/settings/hr_records_earnings_list_js.php';?>

</body>
</html>
