<div class="card rounded-0">
	<div class="page-title clearfix">
		<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_hr_company_training'); ?></h4>
		<div class="title-button-group">
		</div>
	</div>

	<div class="table-responsive">
		<table class="table dt-table" >
			<thead>
				<th class="sorting_disabled hide"><?php echo app_lang('ID'); ?></th>
				<th class="sorting_disabled"><?php echo app_lang('name'); ?></th>
				<th class="sorting_disabled"><?php echo app_lang('hr_training_result'); ?></th>
				<th class="sorting_disabled"><?php echo app_lang('hr_status_label'); ?></th>
				<th class="sorting_disabled"><i data-feather='menu' class='icon-16'></i></th>
			</thead>
			<tbody>
				<?php $index = 1;?>
				<?php if (isset($training_data)) {
					?>
					<?php foreach ($training_data as $key => $value) {?>

						<tr>
							<td class="hide"><b><?php echo html_entity_decode($index); ?></b></td>
							<td><b><?php echo html_entity_decode(isset($value['list_training_allocation']['training_name']) ? $value['list_training_allocation']['training_name'] : ''); ?></b></td>

							<td>
								<?php
								echo get_type_of_training_by_id(isset($value['list_training_allocation']['training_type']) ? $value['list_training_allocation']['training_type'] : '');

								echo ': ' . html_entity_decode(isset($value['training_program_point']) ? $value['training_program_point'] : '') . '/' . html_entity_decode(isset($value['training_allocation_min_point']) ? $value['training_allocation_min_point'] : '');
								?>
							</td>
							<td>
								<?php
								if (isset($value['complete']) && $value['complete'] == 0) {

									echo ' <span class="badge bg-success large mt-0 "> ' . app_lang('hr_complete') . ' </span>';
								} else {
									echo ' <span class="badge bg-primary large mt-0"> ' . app_lang('hr_not_yet_complete') . ' </span>';
								}
								?>
							</td>
							<td></td>
						</tr>
						<?php $index++;?>

						<?php if (isset($value['staff_training_result'])) {
							?>
							<?php foreach ($value['staff_training_result'] as $r_key => $r_value) {?>
								<?php 
								$view = '';
								?>
								<tr>
									<td class="hide"><b><?php echo html_entity_decode($index); ?></b></td>
									<td>

										<?php if (isset($value['list_training_allocation']['time_to_start']) || isset($value['list_training_allocation']['time_to_end'])) {?>

											<?php
											$current_date = date('Y-m-d');

											if ($value['list_training_allocation']['time_to_start'] != null && $value['list_training_allocation']['time_to_end'] != null) {
												if (strtotime(date('Y-m-d')) >= strtotime($value['list_training_allocation']['time_to_start']) && strtotime(date('Y-m-d')) <= strtotime($value['list_training_allocation']['time_to_end'])) {

													$show_training = true;

												} else {
													$show_training = false;
												}
											} elseif ($value['list_training_allocation']['time_to_start'] != null) {
												if (strtotime(date('Y-m-d')) >= strtotime($value['list_training_allocation']['time_to_start'])) {

													$show_training = true;

												} else {
													$show_training = false;
												}

											} elseif ($value['list_training_allocation']['time_to_end'] != null) {
												if (strtotime(date('Y-m-d')) <= strtotime($value['list_training_allocation']['time_to_end'])) {

													$show_training = true;

												} else {
													$show_training = false;
												}
											} else {
												$show_training = true;
											}

											?>

											<?php if ($show_training == true) {?>
												<a href="<?php echo get_uri('hr_profile/training_detail/'.$r_value['training_id'] . '/' .  hr_get_training_hash($r_value['training_id'])); ?>" target="_blank"><?php echo '&nbsp;&nbsp;&nbsp;+' . html_entity_decode($r_value['training_name']); ?></a>

												<?php 
												$view = '
												
												<a href="'. get_uri('hr_profile/training_detail/'.$r_value['training_id'] . '/' .  hr_get_training_hash($r_value['training_id'])).'" target="_blank"><i data-feather="eye" class="icon-16"></i></a>

												';

												?>
											<?php } else {?>
												<a href="#" class="text-danger" title="<?php echo app_lang('training_over_due'); ?>"><?php echo '&nbsp;&nbsp;&nbsp;+' . html_entity_decode($r_value['training_name']); ?></a>

											<?php }?>

										<?php } else {?>
											<a href="<?php echo get_uri('hr_profile/training_detail/'.$r_value['training_id'] . '/' .  hr_get_training_hash($r_value['training_id'])); ?>"  target="_blank"><?php echo '&nbsp;&nbsp;&nbsp;+' . html_entity_decode($r_value['training_name']); ?></a>

											<?php 
											$view = '
											<a href="'. get_uri('hr_profile/training_detail/'.$r_value['training_id'] . '/' .  hr_get_training_hash($r_value['training_id'])).'" target="_blank"><i data-feather="eye" class="icon-16"></i></a>
											
											';

											?>
										<?php }?>
									</td>
									<td>
										<?php echo app_lang('hr_point') . ': ' . html_entity_decode($r_value['total_point']) . '/' . html_entity_decode($r_value['total_question_point']); ?>

									</td>
									<td></td>
									<td>
										<?php echo html_entity_decode($view); ?>
									</td>

								</tr>
								<?php $index++;?>

							<?php }}}?>

						<?php }?>

					</tbody>
				</table>

			</div>
		</div>

		<div class="card rounded-0">
			<div class="page-title clearfix">
				<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_hr_more_training'); ?></h4>
				<div class="title-button-group">
					<div class="_buttons">
						<button class="btn btn-info text-white" type="button" onclick="create_trainings();"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_more_training_sessions'); ?></button>

					</div>
				</div>
			</div>

			<div class="table-responsive">
				<?php
				$table_data = array(
					app_lang('hr_training_programs_name'),
					app_lang('hr_hr_training_places'),
					app_lang('hr_time_to_start'),
					app_lang('hr_time_to_end'),
					app_lang('hr_training_result'),
					app_lang('hr_degree'),
					app_lang('hr_notes'),
					"<i data-feather='menu' class='icon-16'></i>",

				);
				render_datatable1($table_data, 'table_education', array(), array('data-page-length' => '10'));
				?>

			</div>

		</div>

		<div class="modal fade" id="education_sidebar" tabindex="-1" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">
							<span class="edit-title-training"><?php echo app_lang('hr_update_training_sessions'); ?></span>
							<span class="add-title-training"><?php echo app_lang('hr_more_training_sessions'); ?></span>
						</h4>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<?php echo form_open_multipart(get_uri("hr_profile/save_update_education"), array("id" => "save_update_education", "class" => "save_update_education general-form", "role" => "form", "autocomplete" => "false")); ?>

					<div class="modal-body card-body">
						<input type="hidden" name="id" value="">
						<input type="hidden" name="staff_id" value="<?php echo html_entity_decode($staffid); ?>">
						<div class="row">
							<div class="col-md-12">

								<?php echo render_input1('training_programs_name', 'hr_training_programs_name', '', 'text', [], [], '', '', true); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php echo render_input1('training_places', 'hr_hr_training_places', '', 'text', [], [], '', '', true); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 pl-0">
								<?php echo render_date_input1('training_time_from', 'hr_time_to_start', '', [], [], '', '', true); ?>
							</div>
							<div class="col-md-6 pr-0">
								<?php echo render_date_input1('training_time_to', 'hr_time_to_end', '', [], [], '', '', true); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php echo render_textarea1('training_result', 'hr_training_result', '', array(), array(), '', 'tinymce'); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php echo render_input1('degree', 'hr_degree', '', 'text'); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php
								echo render_textarea1('notes', 'hr_notes', '');
								?>
							</div>
						</div>
						<div class="clearfix"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
						<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('save'); ?></button>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>


		<?php require 'plugins/Hr_profile/assets/js/hr_record/includes/training_js.php';?>