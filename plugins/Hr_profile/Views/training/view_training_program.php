<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-md-12" id="training-add-edit-wrapper">

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($training_program->training_name); ?></h4>
				</div>
				<div class="modal-body card-body clearfix">

					<div class="row">
						<div class="col-md-12 panel-padding">
							<table class="table border table-striped table-margintop">
								<tbody>

									<tr class="project-overview">
										<td class="bold" width="20%"><?php echo app_lang('hr_training_type'); ?></td>
										<td><?php echo html_entity_decode(get_type_of_training_by_id($training_program->training_type)) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('hr_training_item'); ?></td>
										<td><?php echo get_training_library_name($training_program->position_training_id) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('hr_mint_point'); ?></td>
										<td><?php echo html_entity_decode($training_program->mint_point) ; ?></td>
									</tr>

									<?php if($training_program->additional_training == 'additional_training'){ ?>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('hr_additional_training'); ?></td>
											<td><?php echo hr_get_list_staff_name($training_program->staff_id) ; ?></td>
										</tr>


										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('hr_training_time'); ?></td>
											<td><?php echo app_lang('hr_time_to_start').': '.($training_program->time_to_start).' -  '.app_lang('hr_time_to_end').': '. ($training_program->time_to_end) ; ?></td>
										</tr>

									<?php }else{ ?>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('hr__position_apply'); ?></td>
											<td><?php echo hr_get_list_job_position_name($training_program->job_position_id) ; ?></td>
										</tr>
									<?php } ?>


								</tbody>
							</table>
						</div>
						<div class="col-md-12">
							<h4 class="h4-color"><?php echo app_lang('hr_hr_description'); ?></h4>
							<hr class="hr-color">
							<p><?php echo html_entity_decode($training_program->description) ; ?></p>

						</div>

						<table class="table dt-table" >
							<thead>
								<th class="sorting_disabled hide"><?php echo app_lang('ID'); ?></th>
								<th class="sorting_disabled"><?php echo app_lang('name'); ?></th>
								<th class="sorting_disabled"><?php echo app_lang('hr_training_result'); ?></th>
								<th class="sorting_disabled"><?php echo app_lang('date'); ?></th>
								<th class="sorting_disabled"><?php echo app_lang('hr_status_label'); ?></th>
							</thead>
							<tbody>
								<?php $index=1; ?>

								<?php if(count($training_results) > 0){ ?>
									<?php foreach ($training_results as $key => $value) { ?>

										<tr>
											<td class="hide"><b><?php echo html_entity_decode($index); ?></b></td>
											<td><b><?php echo get_staff_full_name1($value['staff_id']); ?></b></td>

											<td>
												<?php

												echo app_lang('total_point').' / '.app_lang('hr_mint_point') .': '.html_entity_decode(isset($value['training_program_point']) ? $value['training_program_point'] : '') .'/'.html_entity_decode($training_program->mint_point) ;
												?>
											</td>
											<td></td>

											<td>
												<?php 
												if((float)$value['training_program_point'] >= (float)$training_program->mint_point){
													echo ' <span class="badge bg-success large mt-0 "> '.app_lang('hr_complete').' </span>';
												}else{
													echo ' <span class="badge bg-warning large mt-0"> '.app_lang('hr_not_yet_complete').' </span>';
												}
												?>
											</td>
										</tr>

										<?php $index++; ?>
										<?php if(isset($value['staff_training_result'])){ ?>
											<?php  foreach ($value['staff_training_result'] as $r_key => $r_value) { ?>
												<tr>
													<td class="hide"><b><?php echo html_entity_decode($index); ?></b></td>

													<td>
														<a href="<?php echo get_uri('hr_profile/view_staff_training_result/'.$r_value['staff_id'].'/'.$r_value['resultsetid'].'/'.$r_value['training_id'].'/'.$r_value['hash']); ?>"><?php echo '&nbsp;&nbsp;&nbsp;+'. html_entity_decode($r_value['training_name']); ?></a>


													</td>
													<td>
														<?php echo app_lang('hr_point').': <strong>'. html_entity_decode($r_value['total_point']).'/'. html_entity_decode($r_value['total_question_point']).'</strong>'; ?>

													</td>
													<td><?php echo html_entity_decode(format_to_datetime($r_value['date'], false)) ?></td>
													<td></td>

												</tr>
												<?php $index++; ?>


											<?php }}} ?>

										<?php } ?>

									</tbody>
								</table>

							</div>

						</div>

						<div class="modal-footer">

							<a href="<?php echo get_uri('hr_profile/training_programs'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
						</div>

					</div>

				</div>

			</div>

		</div>
	</div>


</body>
</html>
