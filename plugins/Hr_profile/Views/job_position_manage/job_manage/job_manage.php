<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if(is_admin() || hr_has_permission('hr_profile_can_create_job_description')) { ?>
							<a href="#" onclick="new_job_p(); return false;" class="btn mright5 btn-info pull-left text-white">
								<span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_new_job_p'); ?>
							</a>
						<?php } ?>
						<a href="<?php echo site_url('hr_profile/job_positions'); ?>" class=" btn mright5 btn-default pull-left display-block">
							<span data-feather="arrow-left" class="icon-16"></span> <?php echo app_lang('hr__back'); ?>
						</a>

					</div>
				</div>

				<div class="row ml2 mr5 mt15">
					<div class="col-md-3 pull-right">
						<div class="form-group ">
							<select name="job_position_id[]" class="select2 validate-hidden" id="job_position_id" data-width="100%" data-live-search="true" multiple="true" data-actions-box="true" placeholder="<?php echo app_lang('hr_hr_job_position'); ?>">
								<?php foreach ($get_job_position as $p) {?>
									<option value="<?php echo html_entity_decode($p['position_id']); ?>"><?php echo html_entity_decode($p['position_name']); ?></option>
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
								<?php if(hr_has_permission('hr_profile_can_delete_job_description') || is_admin()){ ?>
									<div class="row">
										<div class="col-md-12">
											<div class="checkbox checkbox-danger">
												<input type="checkbox" name="mass_delete" id="mass_delete" class="form-check-input">
												<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
											</div>
										</div>
									</div>
								<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<?php if(hr_has_permission('hr_profile_can_delete_job_description') || is_admin()){ ?>
									<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_job_description')) { ?>
					<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_job" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>


				<div class="table-responsive">

					<?php render_datatable1(array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_job" class="form-check-input"><label></label></div>',
						app_lang('hr_job_id'),
						app_lang('hr_job_p'),
						app_lang('hr_hr_description'),
						app_lang('departments'),
						"<i data-feather='menu' class='icon-16'></i>",

					),'table_job',
					array('customizable-table'),
					array(
						'id'=>'table-table_job',
						'data-last-order-identifier'=>'table_job',
					)); ?>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="job_p" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('hr_edit_job_p'); ?></span>
					<span class="add-title"><?php echo app_lang('hr_new_job_p'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>
			<?php echo form_open_multipart(get_uri('hr_profile/job_p'),array('class'=>'job_p','autocomplete'=>'off', 'class' => 'general-form')); ?>
			<div class="modal-body card-body">
				<div id="additional_job"></div>
				<div role="tabpanel" class="tab-pane active" id="general_infor">
					<div class="row">
						<div class="col-md-12">
							<div id="additional"></div>   
							<div class="form">     
								<?php 
								echo render_input1('job_name','hr_job_p', '', '', [], [], '', '', true); ?>
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
<?php require 'plugins/Hr_profile/assets/js/job_position/job/job_js.php';?>
</body>
</html>
