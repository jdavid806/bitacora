
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<?php if (is_admin() || hr_has_permission('hr_profile_can_create_job_description')) {?>

							<?php echo modal_anchor(get_uri("hr_profile/new_job_position_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('hr_new_job_position'), array("class" => "btn btn-info text-white ", "title" => app_lang('hr_new_job_position'))); ?>

						<?php }?>
						<?php if (is_admin() || hr_has_permission('hr_profile_can_create_job_description')) {?>
							<a href="<?php echo get_uri('hr_profile/import_job_position'); ?>" class="btn btn-success pull-left display-block  mr-4 button-margin-r-b" title="<?php echo app_lang('hr_job_p_import_excel') ?> "><span data-feather="upload" class="icon-16"></span>
								<?php echo app_lang('hr_job_p_import_excel'); ?>
							</a>
						<?php }?>

						<?php if (is_admin() || hr_has_permission('hr_profile_can_create_job_description')) {?>
							<span class="dropdown inline-block mt10">
								<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
									<i data-feather="tool" class="icon-16"></i> <?php echo app_lang('hr_position_groups'); ?>
								</button>
								<ul class="dropdown-menu" role="menu">

									<?php if (is_admin() || hr_has_permission('hr_profile_can_create_job_description')) {?>
										<li role="presentation"><a href="#" class="dropdown-item" onclick="new_job_p(); return false;" >
											<span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_new_position_groups');?>
										</a> </li>
									<?php }?>


									<?php if (is_admin() || hr_has_permission('hr_profile_can_create_job_description')) {?>
										<li role="presentation"><a href="<?php echo site_url('hr_profile/job_position_manage') ?>" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> <?php echo app_lang('hr_manage_position_groups')  ?></a></li>

									<?php } ?>
								</ul>
							</span>
						<?php } ?>


					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div class="col-md-3 pull-right">
						<div class="form-group ">
							<select name="job_p_id[]" class="select2 validate-hidden" id="job_p_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" placeholder="<?php echo app_lang('hr_job_p_id'); ?>">
								<?php foreach ($job_p_id as $p) {?>
									<option value="<?php echo html_entity_decode($p['job_id']); ?>"><?php echo html_entity_decode($p['job_name']); ?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="col-md-3 pull-right">
						<div class="form-group">
							<select name="department_id[]" class="select2 validate-hidden" id="department_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true"  placeholder="<?php echo app_lang('departments') ?>">

								<?php foreach ($hr_profile_get_department_name as $dp) {?>
									<option value="<?php echo html_entity_decode($dp['id']); ?>"><?php echo html_entity_decode($dp['title']); ?></option>
								<?php }?>

							</select>
						</div>
					</div>
				</div>

				<div class="modal fade bulk_actions" id="table_contract_bulk_actions" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo app_lang('hr_bulk_actions'); ?></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body card-body">
								<?php if (hr_has_permission('hr_profile_can_delete_job_description') || is_admin()) {?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" name="mass_delete" id="mass_delete"  class="form-check-input">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php }?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<?php if (hr_has_permission('hr_profile_can_delete_job_description') || is_admin()) {?>
									<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php }?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_job_description')) {?>
					<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_job_position" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php }?>

				<div class="table-responsive">

					<?php render_datatable1(array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_job_position" class="form-check-input"><label></label></div>',
						app_lang('position_id'),
						app_lang('hr_position_code'),
						app_lang('hr_position_name'),
						app_lang('hr_job_descriptions'),
						app_lang('department_name'),
						app_lang('hr_job_p_id'),
						"<i data-feather='menu' class='icon-16'></i>",

					), 'table_job_position',
					array('customizable-table'),
					array(
						'id' => 'table-table_job_position',
						'data-last-order-identifier' => 'table_job_position',
					));?>

				</div>
			</div>
		</div>
	</div>
</div>


<!-- New position group -->
<div class="modal fade" id="job_p" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('hr_edit_job_p'); ?></span>
					<span class="add-title"><?php echo app_lang('hr_new_job_p'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>
			<?php echo form_open_multipart(get_uri('hr_profile/job_p'), array('class' => 'job_p', 'autocomplete' => 'off', 'class' => 'general-form')); ?>
			<div class="modal-body card-body">
				<div id="additional_job"></div>
				<div role="tabpanel" class="tab-pane active" id="general_infor">
					<div class="row">
						<div class="col-md-12">
							<div id="additional"></div>
							<div class="form">
								<?php echo render_input1('job_name', 'hr_job_p', '', '', [], [], '', '', true); ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">

							<?php echo render_textarea1('description', 'hr_hr_description', '', array(), array(), '', 'tinymce'); ?>
						</div>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>
				<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>

			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<?php require 'plugins/Hr_profile/assets/js/job_position/position/position_manage_js.php';?>
</body>
</html>
