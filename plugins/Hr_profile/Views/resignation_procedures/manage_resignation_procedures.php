
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if(is_admin() || hr_has_permission('hr_profile_can_create_layoff_checklists')){ ?>
						
							<?php echo modal_anchor(get_uri("hr_profile/new_resignation_procedures_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('hr_new_resignation_procedures'), array("class" => "btn btn-info text-white ", "title" => app_lang('hr_new_resignation_procedures'))); ?>
							
						<?php } ?>
					</div>
				</div>
				
				<div class="modal fade bulk_actions" id="table_resignation_procedures_bulk_actions" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo app_lang('hr_bulk_actions'); ?></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body card-body">
								<?php if(hr_has_permission('hr_profile_can_delete_layoff_checklists') || is_admin()){ ?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" name="mass_delete" id="mass_delete" class="form-check-input">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<?php if(hr_has_permission('hr_profile_can_delete_layoff_checklists') || is_admin()){ ?>
									<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_layoff_checklists')) { ?>
					<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_resignation_procedures" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>

				<div class="table-responsive">
					<?php
					$table_data = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_resignation_procedures" class="form-check-input"><label></label></div>',
						app_lang('staff_id'),
						app_lang('hr_hr_staff_name'),
						app_lang('departments'),
						app_lang('hr_hr_job_position'),
						app_lang('email'),
						app_lang('hr_day_off'),
						app_lang('hr_progress_label'),
						app_lang('hr_status_label'),
						"<i data-feather='menu' class='icon-16'></i>",

					);
					render_datatable1($table_data,'table_resignation_procedures',
						array('customizable-table'),
						array(
							'id'=>'table-table_resignation_procedures',
							'data-last-order-identifier'=>'table_resignation_procedures',
						));

						?>

					</div>
				</div>
			</div>
		</div>
	</div>


	<div id="detail_checklist_staff" class="modal fade">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content ">
				<?php echo form_open_multipart(get_uri('hr_profile/update_status_option_name'), array('id' => 'update_status_option_name', 'autocomplete' => 'off', 'class' => 'general-form')); ?>

				<div class="modal-header pd-x-20">
					<h4 class="modal-title">
						<span class="approval-title"><?php echo app_lang('hr_resignation_procedures'); ?></span>
					</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

				</div>
				<div class="modal-body card-body pd-20">
					<div class="content-modal-details">

					</div>
				</div><!-- modal-body -->
				<?php echo form_hidden('finish', 0); ?>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>
					<?php if(is_admin() || hr_has_permission('hr_profile_can_edit_layoff_checklists')){ ?>
						<button type="submit" class="btn btn-success text-white" id="finish_btn"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_hr_finish'); ?></button>
						<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
					<?php } ?>

				</div>
				<?php echo form_close(); ?>
			</div>
		</div><!-- modal-dialog -->
	</div>	


	<?php require 'plugins/Hr_profile/assets/js/resignation_procedures/resignation_procedures_manage_js.php';?>

</body>
</html>
