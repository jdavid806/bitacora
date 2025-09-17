<li>

	<span data-feather="key" class="icon-14 ml-20"></span>
	<h5><?php echo app_lang("can_access_recruitments"); ?></h5>


	<!-- HR Organization -->
	<div>
		<div class="ml15">
			
			<div>
				<?php
				echo form_checkbox("recruitment_can_view_global", "1", $recruitment_can_view_global ? true : false, "id='recruitment_can_view_global' class='form-check-input'");
				?>
				<label for="recruitment_can_view_global"><?php echo app_lang("recruitment_view_global"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("recruitment_can_create", "1", $recruitment_can_create ? true : false, "id='recruitment_can_create' class='form-check-input'");
				?>
				<label for="recruitment_can_create"><?php echo app_lang("recruitment_permission_label_create"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("recruitment_can_edit", "1", $recruitment_can_edit ? true : false, "id='recruitment_can_edit' class='form-check-input'");
				?>
				<label for="recruitment_can_edit"><?php echo app_lang("recruitment_permission_label_edit"); ?></label>
			</div>
			
			<div>
				<?php
				echo form_checkbox("recruitment_can_delete", "1", $recruitment_can_delete ? true : false, "id='recruitment_can_delete' class='form-check-input'");
				?>
				<label for="recruitment_can_delete"><?php echo app_lang("recruitment_permission_label_delete"); ?></label>
			</div>
			
		</div>
	</div>
</li>