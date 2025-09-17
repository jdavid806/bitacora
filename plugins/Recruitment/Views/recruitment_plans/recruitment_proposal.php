<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<?php if (re_has_permission("recruitment_can_create") || is_admin()) { ?>
							<a href="<?php echo get_uri('recruitment/add_recruitment_plan'); ?>"  class="mright5 btn btn-info pull-left text-white">
								<span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('_new_proposal'); ?>
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
						'label' => app_lang('_proposal'),
					];
					$status_filter_data[] = [
						'name' => '2',
						'label' => app_lang('approved'),
					];
					$status_filter_data[] = [
						'name' => '3',
						'label' => app_lang('made_finish'),
					];
					$status_filter_data[] = [
						'name' => '4',
						'label' => app_lang('reject'),
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
				</div>


				<div class="table-responsive">

					<?php
					$table_data = array(
						app_lang('proposal_name'),
						app_lang('position'),
						app_lang('form_of_work'),
						app_lang('department'),       
						app_lang('amount_recruiment'),
						app_lang('status'),
						"<i data-feather='menu' class='icon-16'></i>",
					);

					render_datatable1($table_data,'table_rec_proposal',
				); ?>

			</div>
		</div>
	</div>
</div>
</div>

<?php require 'plugins/Recruitment/assets/js/recruitment_plans/recruitment_plan_manage_js.php';?>

</body>
</html>

