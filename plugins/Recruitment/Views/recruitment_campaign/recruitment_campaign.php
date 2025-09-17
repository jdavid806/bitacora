<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<?php if (re_has_permission("recruitment_can_create") || is_admin()) { ?>
							<a href="<?php echo get_uri('recruitment/add_recruitment_campaign'); ?>"  class="mright5 btn btn-info pull-left text-white">
								<span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('new_campaign'); ?>
							</a>
						<?php } ?>

					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div class="col-md-3 leads-filter-column pull-right">
						<select name="department_filter[]" class="select2 validate-hidden" id="department_filter" data-width="100%"  data-live-search="true" multiple="true" placeholder="<?php echo app_lang('filter_by_department'); ?>"> 
							<?php 
							foreach ($departments as $value) { ?>
								<option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($value['title']) ?></option>
							<?php }
							?>              
						</select>
					</div>

					<div class="col-md-3 leads-filter-column pull-right">
						<select name="position_filter[]" class="select2 validate-hidden" id="position_filter" data-width="100%"  data-live-search="true" multiple="true" placeholder="<?php echo app_lang('filter_by_position'); ?>"> 
							<?php 
							foreach ($positions as $value) { ?>
								<option value="<?php echo html_entity_decode($value['position_id']); ?>"><?php echo html_entity_decode($value['position_name']) ?></option>
							<?php }
							?>              
						</select>
					</div>

					<?php 
					$status_filter_data = [];
					$status_filter_data[] = [
						'name' => '1',
						'label' => app_lang('planning'),
					];
					$status_filter_data[] = [
						'name' => '2',
						'label' => app_lang('overdue'),
					];
					$status_filter_data[] = [
						'name' => '3',
						'label' => app_lang('in_progress'),
					];
					$status_filter_data[] = [
						'name' => '4',
						'label' => app_lang('finish'),
					];
					$status_filter_data[] = [
						'name' => '5',
						'label' => app_lang('cancel'),
					];

					?>


					<div class="col-md-3 leads-filter-column pull-right">
						<select name="status_filter[]" class="select2 validate-hidden" id="status_filter" data-width="100%"  data-live-search="true" multiple="true" placeholder="<?php echo app_lang('filter_by_status'); ?>"> 
							<?php 
							foreach ($status_filter_data as $status) { ?>
								<option value="<?php echo html_entity_decode($status['name']); ?>"><?php echo html_entity_decode($status['label']) ?></option>
							<?php }
							?>              
						</select>
					</div>

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="company_filter[]" id="company_filter" class="select2 validate-hidden" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('company_name'); ?>">
								<?php foreach($company_list as $cp_key =>  $company_value){ ?>
									<option value="<?php echo html_entity_decode($company_value['id']); ?>" > <?php echo html_entity_decode($company_value['company_name']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div>
					<div class="col-md-3"> <?php echo render_date_input1('cp_from_date_filter', 'from_date', ''); ?></div>
					<div class="col-md-3"> <?php echo render_date_input1('cp_to_date_filter', 'to_date', ''); ?></div>

					<div class="col-md-3">
						<div class="form-group">
							<label for="rec_channel_form_id_filter"><?php echo app_lang('recruitment_channel_form'); ?></label>
							<select name="rec_channel_form_id_filter[]" id="rec_channel_form_id_filter" class="select2 validate-hidden"  data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('recruitment_channel_form'); ?>">
								<?php foreach ($rec_channel_form as $rec_c_f) {?>
									<option value="<?php echo html_entity_decode($rec_c_f['id']); ?>"><?php echo html_entity_decode($rec_c_f['r_form_name']); ?></option>
								<?php }?>
							</select>
						</div>
					</div>

					<div class="col-md-3">
						<label for="cp_manager_filter"><?php echo app_lang('manager'); ?></label>
						<select name="cp_manager_filter[]" id="cp_manager_filter" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('manager'); ?>">

							<?php foreach ($staffs as $s) {?>
								<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['first_name'] . ' ' . $s['last_name']); ?></option>
							<?php }?>
						</select>
					</div>

				</div>


				<div class="table-responsive">

					<?php
					$table_data = array(
						app_lang('campaign_name'),
						app_lang('company_name'),
						app_lang('position'),
						app_lang('form_of_work'),
						app_lang('department'),
						app_lang('recruiment_duration'),
						app_lang('amount_recruiment'),
						app_lang('recruitment_channel_form'),
						app_lang('manager'),
						app_lang('status'),
						"<i data-feather='menu' class='icon-16'></i>",

					);

					render_datatable1($table_data,'table_rec_campaign',
				); ?>

			</div>
		</div>
	</div>
</div>
</div>

<?php require 'plugins/Recruitment/assets/js/recruitment_campaigns/recruitment_campaign_manage_js.php';?>

</body>
</html>