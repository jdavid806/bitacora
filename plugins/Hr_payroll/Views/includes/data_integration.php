<div id="page-content" class="page-wrapper clearfix">
	<div class="row">

		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "data_integration";
			echo view("Hr_payroll\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('data_integration'); ?></h4>
				</div>

				<div class="card-body">

					<?php echo form_open_multipart(get_uri('hr_payroll/data_integration'), array('id'=>'data_integration')); ?>

					<div class="row">
						<div class="col-md-12">

							<div class="form-group">
								<div class="checkbox checkbox-primary">
									<input class="form-check-input"  type="checkbox" id="integrated_hrprofile" name="integrated_hrprofile" <?php if(get_setting('integrated_hrprofile') == 1 ){ echo 'checked';} ?> value="integrated_hrprofile" <?php if($hr_profile_active == false){echo ' disabled';} ?>>
									<label for="integrated_hrprofile"><?php echo app_lang('integrated_hrprofile'); ?>

									<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo html_entity_decode($hr_profile_title); ?>"></i></a>
								</label>
							</div>
						</div>
					</div>
				</div>

				<?php if(1==2){ ?>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="checkbox checkbox-primary">
									<input class="form-check-input" type="checkbox" id="integrated_timesheets" name="integrated_timesheets" <?php if(get_setting('integrated_timesheets') == 1 ){ echo 'checked';} ?> value="integrated_timesheets" <?php if($timesheets_active == false){echo ' disabled';} ?>>
									<label for="integrated_timesheets"><?php echo app_lang('integrated_timesheets'); ?>

									<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo html_entity_decode($timesheets_title); ?>"></i></a>
								</label>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php 
			$attendance_types = hrp_attendance_type();
			$actual_workday   = explode(',', get_setting('integration_actual_workday'));
			$paid_leave       = explode(',', get_setting('integration_paid_leave'));
			$unpaid_leave     = explode(',', get_setting('integration_unpaid_leave'));
			?>

			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="standard_working_time" class="control-label clearfix"><small class="req text-danger">* </small><?php echo app_lang('standard_working_time_of_month'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('tooltip_standard_working_time'); ?>"></i></a></label>
						<input type="number" min="0" max="1000" id="standard_working_time" name="standard_working_time" class="form-control" value="<?php echo get_setting('standard_working_time'); ?>" required>
					</div>
				</div>
			</div>

			<?php if(1==2){ ?>
				<div class="col-md-12 option-show <?php if(get_setting('integrated_timesheets') == 1){ echo '';}else{ echo 'hide';}  ?>">

					<div class="row">
						<div class="col-md-4">
							<div class="form-group select-placeholder ">
								<label for="integration_actual_workday" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('integration_actual_workday'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('tooltip_actual_workday'); ?>"></i></a></label>
								<select name="integration_actual_workday[]" id="integration_actual_workday" multiple="true" class="form-control select2 validate-hidden" data-actions-box="true" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required>
									<?php foreach ($actual_workday_type as $key => $value) { ?>

										<?php 
										$selected ='';
										if(in_array($key, $actual_workday)){
											$selected .= ' selected';
										}
										?>
										<option value="<?php echo html_entity_decode($key); ?>" <?php echo  html_entity_decode($selected)?>><?php  echo html_entity_decode($value); ?></option>

									<?php } ?>
								</select>
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="integration_paid_leave" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('integration_paid_leave'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('tooltip_paid_leave'); ?>"></i></a></label>
								<select name="integration_paid_leave[]" class="form-control select2 validate-hidden" multiple="true" id="integration_paid_leave" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
									<?php foreach ($paid_leave_type as $key => $value) { ?>

										<?php 
										$selected ='';
										if(in_array($key, $paid_leave)){
											$selected .= ' selected';
										}
										?>
										<option value="<?php echo html_entity_decode($key); ?>" <?php echo html_entity_decode($selected); ?>><?php  echo html_entity_decode($value); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="integration_unpaid_leave" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('integration_unpaid_leave'); ?><a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('tooltip_unpaid_leave'); ?>"></i></a></label>
								<select name="integration_unpaid_leave[]" class="form-control select2 validate-hidden" multiple="true" id="integration_unpaid_leave" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
									<?php foreach ($unpaid_leave_type as $key => $value) { ?>

										<?php 
										$selected ='';
										if(in_array($key, $unpaid_leave)){
											$selected .= ' selected';
										}
										?>
										<option value="<?php echo html_entity_decode($key); ?>" <?php echo  html_entity_decode($selected)?>><?php echo html_entity_decode($value); ?></option>

									<?php } ?>
								</select>
							</div>
						</div>
					</div>
				</div>


				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<div class="checkbox checkbox-primary">
								<input class="form-check-input" type="checkbox" id="integrated_commissions" name="integrated_commissions" <?php if(get_setting('integrated_commissions') == 1 ){ echo 'checked';} ?> value="integrated_commissions" <?php if($commissions_active == false){echo ' disabled';} ?>>
								<label for="integrated_commissions"><?php echo app_lang('integrated_commissions'); ?>

								<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo html_entity_decode($commissions_title); ?>"></i></a>
							</label>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		<div class="row">
			<div class="col-md-12">
				<div class="modal-footer">
					<?php if(is_admin()){ ?>
						<button type="submit" class="btn btn-info text-white"><?php echo app_lang('submit'); ?><span data-feather="check-circle" class="icon-16" ></span> </button>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>

	</div>
</div>
</div>
</div>
</div>

<div class="clearfix"></div>

<?php require 'plugins/Hr_payroll/assets/js/settings/data_integration_js.php';?>
</body>
</html>
