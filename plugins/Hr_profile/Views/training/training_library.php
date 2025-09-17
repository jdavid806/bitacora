<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "training_libraries";
			echo view("Hr_profile\Views/training/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr__training_library'); ?></h4>
					<div class="title-button-group">


						<?php if(hr_has_permission('hr_profile_can_create_hr_training') || hr_has_permission('hr_profile_can_view_global_hr_training')){ ?>
							<?php if(hr_has_permission('hr_profile_can_create_hr_training')){ ?>
								<a href="<?php echo admin_url('hr_profile/position_training'); ?>" class="btn btn-info text-white" >
									<span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_hr_add'); ?>
								</a>
							<?php } ?>
						<?php } ?>
					</div>
				</div>

				<div class="modal fade bulk_actions" id="table_training_table_bulk_actions" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo app_lang('hr_bulk_actions'); ?></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

							</div>
							<div class="modal-body card-body">
								<?php if(hr_has_permission('hr_profile_can_delete_hr_training') || is_admin()){ ?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" name="mass_delete" id="mass_delete">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>
								<?php if(hr_has_permission('hr_profile_can_delete_hr_training') || is_admin()){ ?>
									<a href="#" class="btn btn-info text-white" onclick="training_library_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_hr_training')) { ?>
					<a href="#"  onclick="training_library_bulk_actions(); return false;" data-toggle="modal" data-table=".table-training_table" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>


				<div class="table-responsive">

					<?php $training_table = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="training_table" class="form-check-input"><label></label></div>',
						app_lang('id'),
						app_lang('hr_survey_dt_name'),
						app_lang('hr_training_type'),
						app_lang('hr_survey_dt_total_questions'),
						app_lang('hr_survey_dt_total_participants'),
						app_lang('hr_survey_dt_date_created'),
						"<i data-feather='menu' class='icon-16'></i>",


					); 

					render_datatable1($training_table,'training_table',
						array('customizable-table'),
						array(
							'id'=>'table-training_table',
							'data-last-order-identifier'=>'training_table',
						)); 

						?>

					</div>
				</div>
			</div>
		</div>
	</div>
	<?php require 'plugins/Hr_profile/assets/js/training/training_library_js.php';?>
</body>
</html>
