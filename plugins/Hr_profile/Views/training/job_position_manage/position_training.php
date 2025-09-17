<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-md-5" id="training-add-edit-wrapper">
			<?php echo form_open_multipart(get_uri("hr_profile/position_training"), array("id" => "training_form", "class" => "general-form", "role" => "form")); ?>
			<?php 
			$id = '';
			if(isset($position_training)){
				$id = $position_training->training_id;
			}
			?>
			<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>
				<div class="modal-body card-body clearfix">

					<label for="training_type" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('hr_training_type'); ?></label>
					<select name="training_type" class="select2 validate-hidden" id="training_type" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
						<?php foreach ($type_of_trainings as $key => $value) { ?>
							<option value="<?php echo html_entity_decode($value['id']) ?>" <?php if(isset($position_training) && $position_training->training_type == $value['id'] ){echo 'selected';}; ?> ><?php echo html_entity_decode($value['name']) ?></option>
						<?php } ?>
					</select>

					<div class="clearfix"></div>
					<br>
					<div class="clearfix"></div>
					<?php $value = (isset($position_training) ? $position_training->subject : ''); ?>
					<?php $attrs = (isset($position_training) ? array() : array('autofocus'=>true)); ?>
					<?php echo render_input1('subject','name',$value,'text',$attrs, [], '', '', true); ?>


					<p class="bold"><?php echo app_lang('hr_hr_description'); ?></p>

					<?php $value = (isset($position_training) ? $position_training->viewdescription : ''); ?>
					<?php echo render_textarea1('viewdescription','',$value,array(),array(),'','tinymce-view-description'); ?>                     
				</div>
			</div>

			<div class="card">
				<div class="container-fluid">
					<div class="">
						<div class="btn-bottom-toolbar text-right mb20 mt20">
							<a href="<?php echo get_uri('hr_profile/training_libraries'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
							<button type="submit" class="btn btn-info pull-right text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
						</div>
					</div>
					<div class="btn-bottom-pusher"></div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>

		<div class="col-md-7 " id="training_questions_wrapper">

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>
				<?php if(isset($position_training)){ ?>
					<ul class="nav nav-tabs tabs-in-body-no-margin" role="tablist">
						<li role="presentation" class="active">
							<a href="#survey_questions_tab" aria-controls="survey_questions_tab" role="tab" data-toggle="tab">
								<?php echo app_lang('hr_training_question_string'); ?>
							</a>
						</li>
						<li class="toggle_view">
							<a href="#" onclick="training_toggle_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo app_lang('toggle_full_view'); ?>">
								<i class="fa fa-expand"></i></a>
							</li>
						</ul>
						<div class="tab-content">
							<div role="tabpanel" class="tab-pane active" id="survey_questions_tab">
								<div class="row mt-3 title-button-group">
									<div class="title-button-group">
										<a href="<?php echo get_uri('hr_profile/training_detail/'.$position_training->training_id . '/' . $position_training->hash); ?>" target="_blank" class="btn btn-success pull-right mleft10 btn-with-tooltip" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo app_lang('hr_survey_list_view_tooltip'); ?>"> <span data-feather="eye" class="icon-16" ></span></a>
										<?php if(hr_has_permission('hr_profile_can_edit_hr_training') || hr_has_permission('hr_profile_can_create_hr_training')){ ?>

											<span class="dropdown inline-block mt10">
												<button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
													<i data-feather="align-justify" class="icon-16"></i> <?php echo app_lang('hr_survey_insert_field'); ?>
												</button>
												<ul class="dropdown-menu" role="menu">
													<li role="presentation"><a href="#" onclick="add_training_question('checkbox',<?php echo html_entity_decode($position_training->training_id); ?>);return false;" class="dropdown-item"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_survey_field_checkbox')  ?></a></li>
												</ul>
											</span>
										<?php } ?>

											</div>
										</div>
									<div class="clearfix"></div>
									<hr />
									<?php
									$question_area = '<ul class="list-unstyled survey_question_callback" id="survey_questions">';
									if(count($position_training->questions) > 0){

										foreach($position_training->questions as $question){
											$question_area .= '<li>';
											$question_area .= '<div class="form-group question">';
											$question_area .= '<div class="row pl-4">';
											$question_area .= '<div class="checkbox checkbox-primary required col-md-2">';
											if($question['required'] == 1){
												$_required = ' checked';
											} else {
												$_required = '';
											}
											$question_area .= '<input type="checkbox"  class="form-check-input" id="req_'.$question['questionid'].'" onchange="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-question_required="'.$question['questionid'].'" name="required[]" '.$_required.'>';
											$question_area .= '<label for="req_'.$question['questionid'].'">'.app_lang('hr_survey_question_required').'</label>';
											$question_area .= '</div>';
														 //start input
											$question_area .= '<div class="col-md-4">';
											$question_area .= '<input type="number" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-question-point="'.$question['questionid'].'" class="form-control questionid" value="'.$question['point'].'" title="'.app_lang('hr_score').'..." placeholder="'.app_lang('hr_score').'..."> ';
											$question_area .= '</div>';

											$question_area .= '</div>';

											$question_area .= '<input type="hidden" value="" name="order[]">';

											$question_area .='<label for="'.$question['questionid'].'" class="control-label display-block">'.app_lang('hr_question_string').'</label>
											<a href="#" onclick="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].'); return false;" class="title-button-group update-question-button"><span data-feather="refresh-cw" class="icon-16" ></span></a>
											<a href="#" onclick="remove_question_from_database(this,'.$question['questionid'].'); return false;" class="title-button-group text-danger me-3"><span data-feather="x-circle" class="icon-16" ></span></a>
											';
											$question_area .= '<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-questionid="'.$question['questionid'].'" class="form-control questionid" value="'.$question['question'].'">';
											if($question['boxtype'] == 'textarea'){
												$question_area .= '<textarea class="form-control mtop20" disabled="disabled" rows="6">'.app_lang('hr_survey_question_only_for_preview').'</textarea>';
											} else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
												$question_area .= '<div class="row">';
												$x = 0;
												foreach($question['box_descriptions'] as $box_description){

													if($box_description['correct'] == 0){
														$correct_checked = ' checked';
													} else {
														$correct_checked = '';
													}

													$box_description_icon_class = 'minus';
													$box_description_function = 'remove_box_description_from_database(this,'.$box_description['questionboxdescriptionid'].'); return false;';
													if($x == 0){
														$box_description_icon_class = 'plus-circle';
														$box_description_function = 'add_box_description_to_database(this,'.$question['questionid'].','.$question['boxid'].'); return false;';
													}
													$question_area .= '<div class="box_area mt-2">';

													$question_area .= '<div class="col-md-12">';
													$question_area .= '<a href="#" class="add_remove_action survey_add_more_box" onclick="'.$box_description_function.'"><span data-feather="'.$box_description_icon_class.'" class="icon-16" ></span> </a>';
													$question_area .= '<div class="'.$question['boxtype'].' '.$question['boxtype'].'-primary">';
													$question_area .= '<input class="form-check-input" type="'.$question['boxtype'].'" onchange="update_answer_question(this,\''.$question['boxtype'].'\','.$question['questionid'].','.$box_description['questionboxdescriptionid'].');"  data-checked-descriptionid="'.$box_description['questionboxdescriptionid'].'" class="data_checked_descriptionid" '.$correct_checked.' />';
													$question_area .= '
													<label>
													<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-box-descriptionid="'.$box_description['questionboxdescriptionid'].'" value="'.$box_description['description'].'" class="form-control  survey_input_box_description">
													</label>';
													$question_area .= '</div>';
													$question_area .= '</div>';
													$question_area .= '</div>';
													$x++;
												}

												$question_area .= '</div>';
											} else {
												$question_area .= '<input type="text" class="form-control mtop20" disabled="disabled" value="'.app_lang('hr_survey_question_only_for_preview').'">';
											}
											$question_area .= '</div>';
											$question_area .= '</li>';
										}
									}
									$question_area .= '</ul>';
									echo html_entity_decode($question_area);
									?>
								</div>

							<?php } else { ?>
								<p class="no-margin"><?php echo app_lang('hr_survey_create_first'); ?></p>
							<?php } ?>
						</div>
					</div>
				</div>


			</div>

		</div>
	</div>
	<div id="modal_wrapper"></div>

	<?php require 'plugins/Hr_profile/assets/js/training/position_training_js.php';?>


</body>
</html>
