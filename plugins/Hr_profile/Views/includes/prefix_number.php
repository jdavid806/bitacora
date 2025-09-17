<?php echo form_open(get_uri("hr_profile/prefix_number"), array("id" => "prefix_number-form", "class" => "general-form", "role" => "form")); ?>

<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "prefix_numbers";
			echo view("Hr_profile\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('prefix_numbers'); ?></h4>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<h5 class="no-margin font-bold h5-color"><?php echo app_lang('hr_position_code') ?></h5>
							<hr class="hr-color">
						</div>
					</div>

					<div class="form-group">
						<label><?php echo app_lang('hr_job_position_prefix'); ?></label>
						<div  class="form-group" app-field-wrapper="job_position_prefix">
							<input type="text" id="job_position_prefix" name="job_position_prefix" class="form-control" value="<?php echo get_setting('job_position_prefix'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label><?php echo app_lang('hr_job_position_number'); ?></label>
						<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('hr_next_number_tooltip'); ?>"></i>
						<div  class="form-group" app-field-wrapper="job_position_number">
							<input type="number" min="0" id="job_position_number" name="job_position_number" class="form-control" value="<?php echo get_setting('job_position_number'); ?>">
						</div>
					</div>
					
					<!-- contract code -->
					<div class="row">
						<div class="col-md-12">
							<h5 class="no-margin font-bold h5-color"><?php echo app_lang('hr_staff_contract_code') ?></h5>
							<hr class="hr-color">
						</div>
					</div>

					<div class="form-group">
						<label><?php echo app_lang('hr_contract_code_prefix'); ?></label>
						<div  class="form-group" app-field-wrapper="contract_code_prefix">
							<input type="text" id="contract_code_prefix" name="contract_code_prefix" class="form-control" value="<?php echo get_setting('contract_code_prefix'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label><?php echo app_lang('hr_contract_code_number'); ?></label>
						<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('hr_next_number_tooltip'); ?>"></i>
						<div  class="form-group" app-field-wrapper="contract_code_number">
							<input type="number" min="0" id="contract_code_number" name="contract_code_number" class="form-control" value="<?php echo get_setting('contract_code_number'); ?>">
						</div>
					</div>

					<!-- staff code -->
					<div class="row">
						<div class="col-md-12">
							<h5 class="no-margin font-bold h5-color"><?php echo app_lang('hr_staff_code') ?></h5>
							<hr class="hr-color">
						</div>
					</div>

					<div class="form-group">
						<label><?php echo app_lang('hr_staff_code_prefix'); ?></label>
						<div class="form-group" app-field-wrapper="staff_code_prefix">
							<input type="text" id="staff_code_prefix" name="staff_code_prefix" class="form-control" value="<?php echo get_setting('staff_code_prefix'); ?>">
						</div>
					</div>

					<div class="form-group">
						<label> <?php echo app_lang('hr_staff_code_number'); ?></label>
						<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('hr_next_number_tooltip'); ?>"></i>

						<div  class="form-group" app-field-wrapper="staff_code_number">
							<input type="number" min="0" id="staff_code_number" name="staff_code_number" class="form-control" value="<?php echo get_setting('staff_code_number'); ?>">
						</div>
					</div>

					<div class="row d-none">
						<div class="col-md-12">
							<h5 class="no-margin font-bold h5-color" ><?php echo app_lang('hr_not_staff_member_setting')?></h5>
							<hr class="hr-color" >
						</div>
					</div>
					<div class="row d-none">
						<div class="col-md-12">
							<div class="form-group">
								<div class="checkbox checkbox-primary">
									<input class="form-check-input" type="checkbox" id="hr_profile_hide_menu" name="hr_profile_hide_menu" <?php if(get_setting('hr_profile_hide_menu') == 1 ){ echo 'checked';} ?> value="1">
									<label for="hr_profile_hide_menu"><?php echo app_lang('hr_not_staff_member_label'); ?>
									<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('hr_not_staff_member_tooltip'); ?>"></i>
									</a>
								</label>
							</div>
						</div>
					</div>
				</div>

				

				<div class="modal-footer">
					<?php if(hr_has_permission('hr_profile_can_create_setting') || hr_has_permission('hr_profile_can_edit_setting') ){ ?>
						<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('save'); ?></button>
					<?php } ?>

				</div>
				<?php echo form_close(); ?>

			</div>
		</div>
	</div>
</div>
</div>

</body>
</html>