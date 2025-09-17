
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if(is_admin() || hr_has_permission('hr_profile_can_create_dependent_persons')) { ?>


						<?php echo modal_anchor(get_uri("hr_profile/dependent_person_modal"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('hr_new_dependent_person'), array("class" => "btn btn-info text-white ", "title" => app_lang('hr_new_dependent_person'), "data-post-manage" => true)); ?>

						<a href="<?php echo admin_url('hr_profile/import_xlsx_dependent_person'); ?>" class="d-none btn mright5 btn-default pull-left display-block">
							<?php echo app_lang('hr_job_p_import_excel'); ?>
						</a>

					<?php } ?>

				</div>
			</div>
			<div class="row ml2 mr5 mt15">

				<div  class="col-md-3 leads-filter-column pull-right">
					<?php 
					$array_status=[];
					$array_status['1'] = app_lang('hr_agree_label');
					$array_status['2'] = app_lang('hr_rejected_label');
					$array_status['0'] = app_lang('hr_pending_label');
					?>
					<select name="status[]" id="status" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('hr_status_label'); ?>">
						<?php foreach($array_status as $key => $status) { ?>
							<option value="<?php echo html_entity_decode($key); ?>"><?php echo html_entity_decode($status); ?></option>
						<?php } ?>
					</select>
				</div>

				<div  class="col-md-3 leads-filter-column pull-right">
					<select name="staff[]" id="staff" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('hr_hr_staff_name'); ?>">
						<?php foreach($staff as $s) { ?>
							<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['first_name'].' '. $s['last_name']); ?></option>
						<?php } ?>
					</select>
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
							<?php if(hr_has_permission('hr_profile_can_delete_dependent_persons') || is_admin()){ ?>
								<div class="checkbox checkbox-danger">
									<input type="checkbox" name="mass_delete" id="mass_delete">
									<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
								</div>
							<?php } ?>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

							<?php if(hr_has_permission('hr_profile_can_delete_dependent_persons') || is_admin()){ ?>
								<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>

			<?php if (hr_has_permission('hr_profile_can_delete_dependent_persons')) { ?>
				<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_dependent_person" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
			<?php } ?>


			<div class="table-responsive">
				<?php render_datatable1(array(
					'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_dependent_person" class="form-check-input"><label></label></div>',
					app_lang('id'),
					app_lang('hr_dependent_name'),
					app_lang('hr_hr_staff_name'),
					app_lang('hr_dependent_bir'),
					app_lang('hr_dependent_iden'),
					app_lang('hr_start_month'),
					app_lang('hr_reason_label'),
					app_lang('hr_status_label'),
					app_lang('options'),
					app_lang('hr_status_comment'),
					"<i data-feather='menu' class='icon-16'></i>",

				),'table_dependent_person',
				array('customizable-table'),
				array(
					'id'=>'table-table_dependent_person',
					'data-last-order-identifier'=>'table_dependent_person',
				)); ?>

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

<div class="modal fade" id="approvaldependent" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(get_uri('hr_profile/approval_status'), array('class' => 'approval_status', 'autocomplete' => 'off', 'class' => 'general-form')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="approval-title"><?php echo app_lang('hr_agree_label'); ?></span>
					<span class="reject-title"><?php echo app_lang('hr_rejected_label'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

			</div>
			<div class="modal-body card-body">
				<div id="dependent_status">

				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form"> 
							<div class="row">
								<div class="col-md-6 start_month_hide">
									<?php 
									echo render_date_input1('start_month','hr_start_month'); ?>
								</div>
								<div class="col-md-6 end_month_hide">
									<?php 
									echo render_date_input1('end_month','hr_end_month'); ?>
								</div>
							</div>    
							<div class="row">
								<div class="col-md-12">
									<?php 
									echo render_input1('reason','hr_reason_label'); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

				<button type="button" class="btn btn-info text-white" onclick="update_status(this); return false;" ><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
<div id="modal_wrapper"></div>


<?php require 'plugins/Hr_profile/assets/js/dependent_person/manage_js.php';?>

</body>
</html>
