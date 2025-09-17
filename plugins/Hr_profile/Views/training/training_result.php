<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "training_results";
			echo view("Hr_profile\Views/training/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('training_results'); ?></h4>
					<div class="title-button-group">

					</div>
				</div>

				<div class="row ml2 mr5 mt15">
					<div  class="col-md-4 leads-filter-column">
						<div class="form-group">
							<label><?php echo app_lang('staff_name'); ?></label>
							<select name="staff[]" id="staff" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">
								<?php foreach($list_staff as $s) { ?>
									<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['first_name'].' '. $s['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div  class="col-md-4 leads-filter-column">
						<div class="form-group">

							<label><?php echo app_lang('hr_training_library'); ?></label>
							<select name="training_library[]" id="training_library" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">
								<?php foreach($training_libraries as $training_library) { ?>
									<option value="<?php echo html_entity_decode($training_library['training_id']); ?>"><?php echo html_entity_decode($training_library['subject']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div  class="col-md-4 leads-filter-column">
						<div class="form-group">

							<label><?php echo app_lang('hr_training_program'); ?></label>
							<select name="training_program[]" id="training_program" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">
								<?php foreach($training_programs as $training_program) { ?>
									<option value="<?php echo html_entity_decode($training_program['training_process_id']); ?>"><?php echo html_entity_decode($training_program['training_name']); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>


				<div class="table-responsive">

					<?php 
					$table_data = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_training_result" class="form-check-input"><label></label></div>',

						app_lang('id'),
						app_lang('hr_hr_staff_name'),
						app_lang('hr_training_library'),
						app_lang('hr_training_type'),
						app_lang('hr_datecreator'),
						"<i data-feather='menu' class='icon-16'></i>",
					);

					render_datatable1($table_data,'table_training_result',
						array('customizable-table'),
						array(
							'id'=>'table-table_training_result',
							'data-last-order-identifier'=>'table_training_result',
						)); 

						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php require 'plugins/Hr_profile/assets/js/training/training_result_js.php';?>
</body>
</html>
