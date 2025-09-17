<li>

	<span data-feather="key" class="icon-14 ml-20"></span>
	<h5><?php echo app_lang("can_access_hr_payrolls"); ?></h5>


	<!-- hr_payroll_employee -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_employee"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_employee", "1", $hr_payroll_can_view_own_hrp_employee ? true : false, "id='hr_payroll_can_view_own_hrp_employee' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_employee"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_employee", "1", $hr_payroll_can_view_global_hrp_employee ? true : false, "id='hr_payroll_can_view_global_hrp_employee' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_employee"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_employee", "1", $hr_payroll_can_create_hrp_employee ? true : false, "id='hr_payroll_can_create_hrp_employee' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_employee"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_employee", "1", $hr_payroll_can_edit_hrp_employee ? true : false, "id='hr_payroll_can_edit_hrp_employee' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_employee"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_employee", "1", $hr_payroll_can_delete_hrp_employee ? true : false, "id='hr_payroll_can_delete_hrp_employee' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_employee"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_attendance -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_attendance"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_attendance", "1", $hr_payroll_can_view_own_hrp_attendance ? true : false, "id='hr_payroll_can_view_own_hrp_attendance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_attendance"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_attendance", "1", $hr_payroll_can_view_global_hrp_attendance ? true : false, "id='hr_payroll_can_view_global_hrp_attendance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_attendance"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_attendance", "1", $hr_payroll_can_create_hrp_attendance ? true : false, "id='hr_payroll_can_create_hrp_attendance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_attendance"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_attendance", "1", $hr_payroll_can_edit_hrp_attendance ? true : false, "id='hr_payroll_can_edit_hrp_attendance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_attendance"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_attendance", "1", $hr_payroll_can_delete_hrp_attendance ? true : false, "id='hr_payroll_can_delete_hrp_attendance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_attendance"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_commission -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_commission"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_commission", "1", $hr_payroll_can_view_own_hrp_commission ? true : false, "id='hr_payroll_can_view_own_hrp_commission' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_commission"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_commission", "1", $hr_payroll_can_view_global_hrp_commission ? true : false, "id='hr_payroll_can_view_global_hrp_commission' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_commission"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_commission", "1", $hr_payroll_can_create_hrp_commission ? true : false, "id='hr_payroll_can_create_hrp_commission' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_commission"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_commission", "1", $hr_payroll_can_edit_hrp_commission ? true : false, "id='hr_payroll_can_edit_hrp_commission' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_commission"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_commission", "1", $hr_payroll_can_delete_hrp_commission ? true : false, "id='hr_payroll_can_delete_hrp_commission' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_commission"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_deduction -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_deduction"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_deduction", "1", $hr_payroll_can_view_own_hrp_deduction ? true : false, "id='hr_payroll_can_view_own_hrp_deduction' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_deduction"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_deduction", "1", $hr_payroll_can_view_global_hrp_deduction ? true : false, "id='hr_payroll_can_view_global_hrp_deduction' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_deduction"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_deduction", "1", $hr_payroll_can_create_hrp_deduction ? true : false, "id='hr_payroll_can_create_hrp_deduction' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_deduction"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_deduction", "1", $hr_payroll_can_edit_hrp_deduction ? true : false, "id='hr_payroll_can_edit_hrp_deduction' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_deduction"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_deduction", "1", $hr_payroll_can_delete_hrp_deduction ? true : false, "id='hr_payroll_can_delete_hrp_deduction' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_deduction"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_bonus_kpi -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_bonus_kpi"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_bonus_kpi", "1", $hr_payroll_can_view_own_hrp_bonus_kpi ? true : false, "id='hr_payroll_can_view_own_hrp_bonus_kpi' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_bonus_kpi"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_bonus_kpi", "1", $hr_payroll_can_view_global_hrp_bonus_kpi ? true : false, "id='hr_payroll_can_view_global_hrp_bonus_kpi' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_bonus_kpi"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_bonus_kpi", "1", $hr_payroll_can_create_hrp_bonus_kpi ? true : false, "id='hr_payroll_can_create_hrp_bonus_kpi' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_bonus_kpi"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_bonus_kpi", "1", $hr_payroll_can_edit_hrp_bonus_kpi ? true : false, "id='hr_payroll_can_edit_hrp_bonus_kpi' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_bonus_kpi"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_bonus_kpi", "1", $hr_payroll_can_delete_hrp_bonus_kpi ? true : false, "id='hr_payroll_can_delete_hrp_bonus_kpi' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_bonus_kpi"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>


	<!-- hr_payroll_insurrance -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_insurrance"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_insurrance", "1", $hr_payroll_can_view_own_hrp_insurrance ? true : false, "id='hr_payroll_can_view_own_hrp_insurrance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_insurrance"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_insurrance", "1", $hr_payroll_can_view_global_hrp_insurrance ? true : false, "id='hr_payroll_can_view_global_hrp_insurrance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_insurrance"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_insurrance", "1", $hr_payroll_can_create_hrp_insurrance ? true : false, "id='hr_payroll_can_create_hrp_insurrance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_insurrance"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_insurrance", "1", $hr_payroll_can_edit_hrp_insurrance ? true : false, "id='hr_payroll_can_edit_hrp_insurrance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_insurrance"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_insurrance", "1", $hr_payroll_can_delete_hrp_insurrance ? true : false, "id='hr_payroll_can_delete_hrp_insurrance' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_insurrance"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_payslips -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_payslip"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_payslip", "1", $hr_payroll_can_view_own_hrp_payslip ? true : false, "id='hr_payroll_can_view_own_hrp_payslip' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_payslip"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_payslip", "1", $hr_payroll_can_view_global_hrp_payslip ? true : false, "id='hr_payroll_can_view_global_hrp_payslip' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_payslip"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_payslip", "1", $hr_payroll_can_create_hrp_payslip ? true : false, "id='hr_payroll_can_create_hrp_payslip' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_payslip"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_payslip", "1", $hr_payroll_can_edit_hrp_payslip ? true : false, "id='hr_payroll_can_edit_hrp_payslip' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_payslip"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_payslip", "1", $hr_payroll_can_delete_hrp_payslip ? true : false, "id='hr_payroll_can_delete_hrp_payslip' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_payslip"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_payslip_template -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_payslip_template"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_payslip_template", "1", $hr_payroll_can_view_own_hrp_payslip_template ? true : false, "id='hr_payroll_can_view_own_hrp_payslip_template' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_payslip_template"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_payslip_template", "1", $hr_payroll_can_view_global_hrp_payslip_template ? true : false, "id='hr_payroll_can_view_global_hrp_payslip_template' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_payslip_template"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_payslip_template", "1", $hr_payroll_can_create_hrp_payslip_template ? true : false, "id='hr_payroll_can_create_hrp_payslip_template' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_payslip_template"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_payslip_template", "1", $hr_payroll_can_edit_hrp_payslip_template ? true : false, "id='hr_payroll_can_edit_hrp_payslip_template' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_payslip_template"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_payslip_template", "1", $hr_payroll_can_delete_hrp_payslip_template ? true : false, "id='hr_payroll_can_delete_hrp_payslip_template' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_payslip_template"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_income_tax -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_income_tax"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_own_hrp_income_tax", "1", $hr_payroll_can_view_own_hrp_income_tax ? true : false, "id='hr_payroll_can_view_own_hrp_income_tax' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_own_hrp_income_tax"><?php echo app_lang("hrp_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_income_tax", "1", $hr_payroll_can_view_global_hrp_income_tax ? true : false, "id='hr_payroll_can_view_global_hrp_income_tax' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_income_tax"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- hr_payroll_report -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_report"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_report", "1", $hr_payroll_can_view_global_hrp_report ? true : false, "id='hr_payroll_can_view_global_hrp_report' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_report"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
		</div>
	</div>

	<!--hr_payroll_setting -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_payroll_setting"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_view_global_hrp_setting", "1", $hr_payroll_can_view_global_hrp_setting ? true : false, "id='hr_payroll_can_view_global_hrp_setting' class='form-check-input'");
				?>
				<label for="hr_payroll_can_view_global_hrp_setting"><?php echo app_lang("hrp_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_create_hrp_setting", "1", $hr_payroll_can_create_hrp_setting ? true : false, "id='hr_payroll_can_create_hrp_setting' class='form-check-input'");
				?>
				<label for="hr_payroll_can_create_hrp_setting"><?php echo app_lang("hrp_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_edit_hrp_setting", "1", $hr_payroll_can_edit_hrp_setting ? true : false, "id='hr_payroll_can_edit_hrp_setting' class='form-check-input'");
				?>
				<label for="hr_payroll_can_edit_hrp_setting"><?php echo app_lang("hrp_permission_label_edit"); ?></label>
			</div>
			<div>
				<?php
				echo form_checkbox("hr_payroll_can_delete_hrp_setting", "1", $hr_payroll_can_delete_hrp_setting ? true : false, "id='hr_payroll_can_delete_hrp_setting' class='form-check-input'");
				?>
				<label for="hr_payroll_can_delete_hrp_setting"><?php echo app_lang("hrp_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

</li>