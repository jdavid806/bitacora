<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if(hr_has_permission('hr_profile_can_create_hr_contract') || is_admin()){ ?>
							<div class="_buttons">
								<a href="<?php echo get_uri('hr_profile/contract'); ?>" class="btn btn-info pull-left text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('new_contract'); ?></a>
								
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div  class="col-md-3 pull-right">
						<input type="text" id="hrm_derpartment_tree" name="hrm_derpartment_tree" class="selectpicker" placeholder="<?php echo app_lang('hr_hr_filter_by_department'); ?>" autocomplete="off">
						<input type="hidden" name="hrm_deparment" id="hrm_deparment"/>
					</div> 


					<div  class="col-md-3 leads-filter-column">
						<div class="form-group">
							<select name="staff[]" id="staff" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('staff_name'); ?>">
								<?php foreach($staff as $s) { ?>
									<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['first_name'].' '. $s['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div> 

					<div  class="col-md-3 leads-filter-column ">
						<?php 
						$input_attr_e = [];
						$input_attr_e['placeholder'] = app_lang('hr_start_month');

						echo render_date_input1('validity_start_date','','',$input_attr_e,[], 'general-form' ); ?>
					</div> 
					<div  class="col-md-3 leads-filter-column ">
						<?php 
						$input_attr = [];
						$input_attr['placeholder'] = app_lang('hr_end_month');

						echo render_date_input1('validity_end_date','','',$input_attr ,[], 'general-form' ); ?>
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
								<?php if(hr_has_permission('hr_profile_can_delete_hr_contract') || is_admin()){ ?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" name="mass_delete" id="mass_delete">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php } ?>
							</div>
							<div class="modal-footer">

								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<?php if(hr_has_permission('hr_profile_can_delete_hr_contract') || is_admin()){ ?>
									<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_hr_contract')) { ?>
					<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_contract" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>

				<div class="table-responsive">

					<?php
					$table_data = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_contract"  class="form-check-input"><label></label></div>',

						app_lang('id'),
						app_lang('hr_contract_code'),
						app_lang('hr_name_contract'),
						app_lang('staff'),
						app_lang('departments'),
						app_lang('hr_start_month'),
						app_lang('hr_end_month'),
						app_lang('hr_status_label'),
						app_lang('hr_sign_day'),  
						"<i data-feather='menu' class='icon-16'></i>",

					);

					render_datatable1($table_data,'table_contract',
						array('customizable-table'),
						array(
							'id'=>'table-table_contract',
							'data-last-order-identifier'=>'table_contract',
						)); ?>

					</div>
				</div>
			</div>
		</div>
	</div>


	<?php require 'plugins/Hr_profile/assets/js/contracts/manage_contract_js.php';?>

</body>
</html>