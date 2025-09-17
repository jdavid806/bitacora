<li>

	<span data-feather="key" class="icon-14 ml-20"></span>
	<h5><?php echo app_lang("can_access_hr_profiles"); ?></h5>

	<!-- HR Dashboard -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_dashboard"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_hr_dashboard", "1", $hr_profile_can_view_global_hr_dashboard ? true : false, "id='hr_profile_can_view_global_hr_dashboard' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_hr_dashboard"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
		</div>
	</div>

	<!-- HR Organization -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_organizational_chart"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_organizational_chart", "1", $hr_profile_can_view_own_organizational_chart ? true : false, "id='hr_profile_can_view_own_organizational_chart' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_organizational_chart"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_organizational_chart", "1", $hr_profile_can_view_global_organizational_chart ? true : false, "id='hr_profile_can_view_global_organizational_chart' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_organizational_chart"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_organizational_chart", "1", $hr_profile_can_create_organizational_chart ? true : false, "id='hr_profile_can_create_organizational_chart' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_organizational_chart"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_organizational_chart", "1", $hr_profile_can_edit_organizational_chart ? true : false, "id='hr_profile_can_edit_organizational_chart' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_organizational_chart"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_organizational_chart", "1", $hr_profile_can_delete_organizational_chart ? true : false, "id='hr_profile_can_delete_organizational_chart' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_organizational_chart"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR Receive staff -->
	<div>
		<label for=""><strong><?php echo app_lang("hr_receiving_staff_lable"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_onboarding", "1", $hr_profile_can_view_own_onboarding ? true : false, "id='hr_profile_can_view_own_onboarding' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_onboarding"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_onboarding", "1", $hr_profile_can_view_global_onboarding ? true : false, "id='hr_profile_can_view_global_onboarding' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_onboarding"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_onboarding", "1", $hr_profile_can_create_onboarding ? true : false, "id='hr_profile_can_create_onboarding' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_onboarding"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_onboarding", "1", $hr_profile_can_edit_onboarding ? true : false, "id='hr_profile_can_edit_onboarding' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_onboarding"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_onboarding", "1", $hr_profile_can_delete_onboarding ? true : false, "id='hr_profile_can_delete_onboarding' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_onboarding"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR records -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_records"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_hr_records", "1", $hr_profile_can_view_own_hr_records ? true : false, "id='hr_profile_can_view_own_hr_records' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_hr_records"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_hr_records", "1", $hr_profile_can_view_global_hr_records ? true : false, "id='hr_profile_can_view_global_hr_records' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_hr_records"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_hr_records", "1", $hr_profile_can_create_hr_records ? true : false, "id='hr_profile_can_create_hr_records' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_hr_records"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_hr_records", "1", $hr_profile_can_edit_hr_records ? true : false, "id='hr_profile_can_edit_hr_records' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_hr_records"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_hr_records", "1", $hr_profile_can_delete_hr_records ? true : false, "id='hr_profile_can_delete_hr_records' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_hr_records"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR Job Description -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_job_escription"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_job_description", "1", $hr_profile_can_view_own_job_description ? true : false, "id='hr_profile_can_view_own_job_description' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_job_description"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_job_description", "1", $hr_profile_can_view_global_job_description ? true : false, "id='hr_profile_can_view_global_job_description' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_job_description"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_job_description", "1", $hr_profile_can_create_job_description ? true : false, "id='hr_profile_can_create_job_description' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_job_description"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_job_description", "1", $hr_profile_can_edit_job_description ? true : false, "id='hr_profile_can_edit_job_description' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_job_description"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_job_description", "1", $hr_profile_can_delete_job_description ? true : false, "id='hr_profile_can_delete_job_description' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_job_description"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR Training -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_training"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_hr_training", "1", $hr_profile_can_view_own_hr_training ? true : false, "id='hr_profile_can_view_own_hr_training' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_hr_training"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_hr_training", "1", $hr_profile_can_view_global_hr_training ? true : false, "id='hr_profile_can_view_global_hr_training' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_hr_training"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_hr_training", "1", $hr_profile_can_create_hr_training ? true : false, "id='hr_profile_can_create_hr_training' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_hr_training"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_hr_training", "1", $hr_profile_can_edit_hr_training ? true : false, "id='hr_profile_can_edit_hr_training' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_hr_training"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_hr_training", "1", $hr_profile_can_delete_hr_training ? true : false, "id='hr_profile_can_delete_hr_training' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_hr_training"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>


	<!-- HR Contract -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_contract"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_hr_contract", "1", $hr_profile_can_view_own_hr_contract ? true : false, "id='hr_profile_can_view_own_hr_contract' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_hr_contract"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_hr_contract", "1", $hr_profile_can_view_global_hr_contract ? true : false, "id='hr_profile_can_view_global_hr_contract' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_hr_contract"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_hr_contract", "1", $hr_profile_can_create_hr_contract ? true : false, "id='hr_profile_can_create_hr_contract' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_hr_contract"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_hr_contract", "1", $hr_profile_can_edit_hr_contract ? true : false, "id='hr_profile_can_edit_hr_contract' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_hr_contract"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_hr_contract", "1", $hr_profile_can_delete_hr_contract ? true : false, "id='hr_profile_can_delete_hr_contract' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_hr_contract"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR Dependent persons -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_dependent_persons"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_dependent_persons", "1", $hr_profile_can_view_own_dependent_persons ? true : false, "id='hr_profile_can_view_own_dependent_persons' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_dependent_persons"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_dependent_persons", "1", $hr_profile_can_view_global_dependent_persons ? true : false, "id='hr_profile_can_view_global_dependent_persons' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_dependent_persons"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_dependent_persons", "1", $hr_profile_can_create_dependent_persons ? true : false, "id='hr_profile_can_create_dependent_persons' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_dependent_persons"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_dependent_persons", "1", $hr_profile_can_edit_dependent_persons ? true : false, "id='hr_profile_can_edit_dependent_persons' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_dependent_persons"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_dependent_persons", "1", $hr_profile_can_delete_dependent_persons ? true : false, "id='hr_profile_can_delete_dependent_persons' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_dependent_persons"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR layoff checklists -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_resignation_procedures"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_own_layoff_checklists", "1", $hr_profile_can_view_own_layoff_checklists ? true : false, "id='hr_profile_can_view_own_layoff_checklists' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_own_layoff_checklists"><?php echo app_lang("hr_view_own"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_layoff_checklists", "1", $hr_profile_can_view_global_layoff_checklists ? true : false, "id='hr_profile_can_view_global_layoff_checklists' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_layoff_checklists"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_layoff_checklists", "1", $hr_profile_can_create_layoff_checklists ? true : false, "id='hr_profile_can_create_layoff_checklists' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_layoff_checklists"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_layoff_checklists", "1", $hr_profile_can_edit_layoff_checklists ? true : false, "id='hr_profile_can_edit_layoff_checklists' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_layoff_checklists"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_layoff_checklists", "1", $hr_profile_can_delete_layoff_checklists ? true : false, "id='hr_profile_can_delete_layoff_checklists' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_layoff_checklists"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- Reports -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_report"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_report", "1", $hr_profile_can_view_global_report ? true : false, "id='hr_profile_can_view_global_report' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_report"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
		</div>
	</div>

	<!-- HR Settings -->
	<div>
		<label for=""><strong><?php echo app_lang("HR_setting"); ?></strong></label>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_view_global_setting", "1", $hr_profile_can_view_global_setting ? true : false, "id='hr_profile_can_view_global_setting' class='form-check-input'");
				?>
				<label for="hr_profile_can_view_global_setting"><?php echo app_lang("hr_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_create_setting", "1", $hr_profile_can_create_setting ? true : false, "id='hr_profile_can_create_setting' class='form-check-input'");
				?>
				<label for="hr_profile_can_create_setting"><?php echo app_lang("hr_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_edit_setting", "1", $hr_profile_can_edit_setting ? true : false, "id='hr_profile_can_edit_setting' class='form-check-input'");
				?>
				<label for="hr_profile_can_edit_setting"><?php echo app_lang("hr_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("hr_profile_can_delete_setting", "1", $hr_profile_can_delete_setting ? true : false, "id='hr_profile_can_delete_setting' class='form-check-input'");
				?>
				<label for="hr_profile_can_delete_setting"><?php echo app_lang("hr_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>

</li>