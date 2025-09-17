
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<a href="<?php echo site_url('hr_profile/staff_profile/'.get_staff_user_id1().'/general'); ?>" class="btn mright5 btn-danger text-white"><span data-feather="user" class="icon-16"></span> <?php echo app_lang('hr_my_profile'); ?></a>

						<?php if (is_admin() || hr_has_permission('hr_profile_can_create_hr_records') || hr_has_permission('hr_profile_can_edit_hr_records')) { ?>
							<a href="<?php echo site_url('hr_profile/new_member'); ?>" class="btn mright5 btn-info text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_new_staff'); ?></a>
							<a href="<?php echo site_url('hr_profile/importxlsx'); ?>" class="btn mright5 btn-success  text-white">
								<span data-feather="upload" class="icon-16"></span> <?php echo app_lang('hr_import_xlsx_hr_profile'); ?>
							</a>
						<?php } ?>

						<?php if (is_admin() || hr_has_permission('hr_profile_can_create_hr_records') || hr_has_permission('hr_profile_can_edit_hr_records') ) { ?>
							<a href="#" onclick="staff_export_item(); return false;"  class="mright5 btn btn-warning pull-left   hr_export_staff text-white">
								<span data-feather="external-link" class="icon-16"></span> <?php echo app_lang('hr_export_staff'); ?>
							</a>

							<a href="#" id="dowload_items"  class="btn btn-success pull-left  mr-4 button-margin-r-b hide"><span data-feather="download" class="icon-16"></span> <?php echo app_lang('dowload_staffs'); ?></a>

						<?php } ?>

						<a href="#" onclick="view_staff_chart(); return false;"  class="mright5 btn btn-default ">
							<span data-feather="eye" class="icon-16"></span> <?php echo app_lang('hr_view_staff_chart'); ?>
						</a>

					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div class="col-md-3 pull-right hide">
						<input type="text" id="staff_dep_tree" name="staff_dep_tree" class="selectpicker" placeholder="<?php echo app_lang('hr_team_manage'); ?>" autocomplete="off">
						<input type="hidden" name="staff_tree" id="staff_tree"/>
					</div>

					<div class="col-md-3 pull-right">
						<select name="status_work[]" class="select2 validate-hidden" multiple="true" id="status_work" data-width="100%" placeholder="<?php echo app_lang('hr_status_label'); ?>"> 
							<option value="<?php echo 'working' ?>"><?php echo app_lang('hr_working'); ?></option>
							<option value="<?php echo 'maternity_leave'; ?>"><?php echo app_lang('hr_maternity_leave'); ?></option>
							<option value="<?php echo 'inactivity'; ?>"><?php echo app_lang('hr_inactivity'); ?></option>
						</select>
					</div>
					<div class="col-md-3 pull-right">
						<select name="staff_role[]" class="select2 validate-hidden" multiple="true" id="staff_role" data-width="100%" data-actions-box="true" data-live-search="true" placeholder="<?php echo app_lang('hr_hr_job_position'); ?>"> 
							<?php 
							foreach ($staff_role as $value) { ?>
								<option value="<?php echo html_entity_decode($value['position_id']); ?>"><?php echo html_entity_decode($value['position_name']) ?></option>
							<?php }
							?>              
						</select>
					</div>
					<div class="col-md-3 leads-filter-column pull-right">
						<select name="hr_profile_deparment" class="select2 validate-hidden" id="hr_profile_deparment" data-width="100%"  data-live-search="true" placeholder="<?php echo app_lang('departments'); ?>"> 
							<option value="">- <?php echo app_lang('departments'); ?> -</option>
							<?php 
							foreach ($departments as $value) { ?>
								<option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($value['title']) ?></option>
							<?php }
							?>              
						</select>
					</div>

				</div>

				<div class="modal fade bulk_actions" id="table_staff_bulk_actions" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo app_lang('hr_bulk_actions'); ?></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body card-body">
								<?php if(has_permission('crm_mana_leads','','delete')){ ?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" name="mass_delete" id="mass_delete" class="form-check-input">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php } ?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_hr_records')) { ?>
					<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_staff" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>

				<div class="table-responsive">

					<?php
					$table_data = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_staff" class="form-check-input"><label></label></div>',
						app_lang('name'),
						app_lang('hr_staff_code'),
						app_lang('email'),
						app_lang('departments'),       
						app_lang('hr_sex'),
						app_lang('hr_hr_job_position'),
						app_lang('role'),
						app_lang('hr_active'),
						app_lang('hr_status_work'), 
						"<i data-feather='menu' class='icon-16'></i>",
					);

					render_datatable1($table_data,'table_staff',
						array('customizable-table'),
						array(
							'id'=>'table-table_staff',
							'data-last-order-identifier'=>'table_staff',
						)); ?>

					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="staff_chart_view" tabindex="-1" role="dialog">
		<div class="modal-dialog organizational_chart_dialog  modal-lg app-modal-body mw100p">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						<span class="edit-title"><?php echo app_lang('hr_staff_chart'); ?></span>
					</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

				</div>
				<div class="modal-body card-body">
					<div class="row">
						<div class="col-md-12" id="st_chart">
							<div id="staff_chart"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_wrapper"></div>

	<?php require 'plugins/Hr_profile/assets/js/hr_record/hr_record_js.php';?>

</body>
</html>