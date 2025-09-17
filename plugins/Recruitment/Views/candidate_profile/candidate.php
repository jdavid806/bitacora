<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">

				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>
				<?php if (isset($candidate)) {

					echo form_hidden('candidateid', $candidate->id);
					echo form_open_multipart(site_url('recruitment/add_update_candidate/' . $candidate->id), array('id' => 'recruitment-candidate-form', "class" => "general-form", "role" => "form"));} else {
						echo form_open_multipart(site_url('recruitment/add_update_candidate'), array('id' => 'recruitment-candidate-form'));}?>
						<div class="modal-body clearfix">

							<div class="row">
								<div class="col-md-7">
									<div class="page-title clearfix">
										<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('general_infor'); ?></h4>
									</div>
									<div class="modal-body clearfix">

										<div class="col-md-12">
											<label for="rec_campaign"><?php echo app_lang('recruitment_campaign'); ?></label>
											<select name="rec_campaign" id="rec_campaign" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('recruitment_campaign'); ?>">
												<option value="">- <?php echo app_lang('recruitment_campaign'); ?></option>
												<?php foreach ($rec_campaigns as $s) {?>
													<option value="<?php echo html_entity_decode($s['cp_id']); ?>" <?php if (isset($candidate) && $s['cp_id'] == $candidate->rec_campaign) {echo 'selected';}?>><?php echo html_entity_decode($s['campaign_code'] . ' - ' . $s['campaign_name']); ?></option>
												<?php }?>
											</select>
											<br><br>
										</div>

										<div class="col-md-12">
											<?php $candidate_code = (isset($candidate) ? $candidate->candidate_code : $candidate_code_default);
											echo render_input1('candidate_code', 'candidate_code', $candidate_code)?>
										</div>

										<div class="row">
											<div class="col-md-6">
												<?php $candidate_name = (isset($candidate) ? $candidate->candidate_name : '');
												echo render_input1('candidate_name', 'first_name', $candidate_name, '', [], [], '', '', true)?>
											</div>

											<div class="col-md-6">
												<?php $last_name = (isset($candidate) ? $candidate->last_name : '');
												echo render_input1('last_name', 'last_name', $last_name, '', [], [], '', '', true)?>
											</div>
										</div>

										<div class="row">
											<div class="col-md-4">
												<?php 
												$birthday = '';
												if(isset($candidate) && $candidate->birthday != '0000-00-00'){
													$birthday = format_to_date($candidate->birthday);
												}
												echo render_date_input1('birthday', 'birthday', $birthday)?>
											</div>

											<div class="col-md-4">
												<label for="gender"><?php echo app_lang('gender'); ?></label>
												<select name="gender" id="gender" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('gender'); ?>">
													<option value=""></option>
													<option value="male" <?php if (isset($candidate) && $candidate->gender == 'male') {echo 'selected';}?>><?php echo app_lang('male'); ?></option>
													<option value="female" <?php if (isset($candidate) && $candidate->gender == 'female') {echo 'selected';}?>><?php echo app_lang('female'); ?></option>
												</select>
												<br><br>
											</div>

											<div class="col-md-4">
												<?php $arrAtt = array();
												$arrAtt['data-type'] = 'currency';
												$desired_salary = (isset($candidate) ? to_decimal_format((float)$candidate->desired_salary) : '');
												?>

												<div class="form-group">
													<label><?php echo app_lang('desired_salary'); ?></label>
													<div class="input-group">
														<input type="text" class="form-control text-right" name="desired_salary" value="<?php echo html_entity_decode($desired_salary); ?>" data-type="currency">
														<button type="button" class="input-group-text clickable no-border" id=""><?php echo get_setting("currency_symbol"); ?></button>

													</div>
												</div>
											</div>
										</div>

										<div class="row">											
											<div class="col-md-6">
												<?php $birthplace = (isset($candidate) ? $candidate->birthplace : '');
												echo render_textarea1('birthplace', 'birthplace', $birthplace)?>
											</div>
											<div class="col-md-6">
												<?php $home_town = (isset($candidate) ? $candidate->home_town : '');
												echo render_textarea1('home_town', 'home_town', $home_town)?>
											</div>
										</div>

										<div class="row">											
											<div class="col-md-4">
												<?php $identification = (isset($candidate) ? $candidate->identification : '');
												echo render_input1('identification', 'identification', $identification);?>
											</div>
											<div class="col-md-4">
												<?php

												$days_for_identity = '';
												if(isset($candidate) && $candidate->days_for_identity != '0000-00-00'){
													$days_for_identity = format_to_date($candidate->days_for_identity);
												}

												echo render_date_input1('days_for_identity', 'days_for_identity', $days_for_identity);?>
											</div>
											<div class="col-md-4">
												<?php $place_of_issue = (isset($candidate) ? $candidate->place_of_issue : '');
												echo render_input1('place_of_issue', 'place_of_issue', $place_of_issue);?>
											</div>
										</div>

										<div class="row">											
											<div class="col-md-4">
												<label for="marital_status" class="control-label"><?php echo app_lang('marital_status'); ?></label>
												<select name="marital_status" class="select2 validate-hidden" id="marital_status" data-width="100%" placeholder="<?php echo app_lang('marital_status'); ?>">
													<option value=""></option>
													<option value="<?php echo 'single'; ?>" <?php if (isset($candidate) && $candidate->marital_status == 'single') {echo 'selected';}?> ><?php echo app_lang('single'); ?></option>
													<option value="<?php echo 'married'; ?>" <?php if (isset($candidate) && $candidate->marital_status == 'married') {echo 'selected';}?>  ><?php echo app_lang('married'); ?></option>
												</select>
											</div>
											<div class="col-md-4">
												<?php $nationality = (isset($candidate) ? $candidate->nationality : '');
												echo render_input1('nationality', 'nationality', $nationality);?>
											</div>
											<div class="col-md-4">
												<?php $nation = (isset($candidate) ? $candidate->nation : '');
												echo render_input1('nation', 'nation', $nation);?>
											</div>
										</div>

										<div class="row">											
											<div class="col-md-4">
												<?php $religion = (isset($candidate) ? $candidate->religion : '');
												echo render_input1('religion', 'religion', $religion);?>
											</div>
											<div class="col-md-4">
												<?php $height = (isset($candidate) ? $candidate->height : '');
												echo render_input1('height', 'height', $height);?>
											</div>
											<div class="col-md-4">
												<?php $weight = (isset($candidate) ? $candidate->weight : '');
												echo render_input1('weight', 'weight', $weight);?>
											</div>
										</div>

										<div class="col-md-12">
											<?php $introduce_yourself = (isset($candidate) ? $candidate->introduce_yourself : '');

											$rows=[];
											$rows['rows'] = 12;
											echo render_textarea1('introduce_yourself', 'introduce_yourself', $introduce_yourself, $rows)?>
										</div>

										<div class="row">											

											<div class="col-md-6">
												<div class="form-group">
													<label for="skill[]" class="control-label"><?php echo app_lang('skill_name'); ?></label> 
													<select name="skill[]" id="skill" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('skill_name'); ?>">
														<?php if(isset($candidate->skill)){ $skill_id = explode(',', $candidate->skill);} ; ?>

														<?php foreach($skills as $dpkey =>  $skill){ ?>
															<option value="<?php echo html_entity_decode($skill['id']); ?>"  <?php if(isset($skill_id) && in_array($skill['id'], $skill_id) == true ){echo 'selected';} ?>> <?php echo html_entity_decode($skill['skill_name']); ?></option>                  
														<?php }?>
													</select>
												</div>
											</div>

											<div class="col-md-6">
												<div class="form-group">
													<label for="year_experience" class="control-label"><?php echo app_lang('experience'); ?></label> 
													<select name="year_experience" id="year_experience" data-live-search="true" class="select2 validate-hidden" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('experience'); ?>">

														<?php foreach(rec_year_experience() as $key =>  $year_experience){ ?>
															<option value="<?php echo html_entity_decode($year_experience['value']); ?>"  <?php if(isset($candidate) && $candidate->year_experience == $year_experience['value'] ){echo 'selected';} ?>> <?php echo html_entity_decode($year_experience['label']); ?></option>                  
														<?php }?>
													</select>
												</div>
											</div>
										</div>

										<div class="col-md-12">
											<?php $interests = (isset($candidate) ? $candidate->interests : '');
											echo render_textarea1('interests', 'interests', $interests)?>
										</div>

									</div>
								</div>


								<div class="col-md-5">
									<div class="panel panel-info">
										<div class="page-title clearfix">
											<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('contact_info'); ?></h4>
										</div>
										<div class="modal-body clearfix">

											<div class="row">
												<div class="col-md-6">
													<?php $phonenumber = (isset($candidate) ? $candidate->phonenumber : '');
													echo render_input1('phonenumber', 'phonenumber', $phonenumber);?>
												</div>

												<div class="col-md-6">
													<?php $alternate_contact_number = (isset($candidate) ? $candidate->alternate_contact_number : '');
													echo render_input1('alternate_contact_number', 'alternate_contact_number', $alternate_contact_number);?>
												</div>
											</div>
											<div class="row">

												<div class="col-md-6">
													<?php $email = (isset($candidate) ? $candidate->email : '');
													echo render_input1('email', 'email', $email, '', [], [], '', '', true)?>
												</div>

												<div class="col-md-6">
													<?php $skype = (isset($candidate) ? $candidate->skype : '');
													echo render_input1('skype', 'skype', $skype);?>
												</div>
											</div>
											<div class="row">

												<div class="col-md-6">
													<?php $facebook = (isset($candidate) ? $candidate->facebook : '');
													echo render_input1('facebook', 'facebook', $facebook);?>
												</div>

												<div class="col-md-6">
													<?php $linkedin = (isset($candidate) ? $candidate->linkedin : '');
													echo render_input1('linkedin', 'linkedin', $linkedin);?>
												</div>
											</div>


											<div class="col-md-12">
												<?php $resident = (isset($candidate) ? $candidate->resident : '');
												echo render_textarea1('resident', 'resident', $resident)?>
											</div>
											<div class="col-md-12">
												<?php $current_accommodation = (isset($candidate) ? $candidate->current_accommodation : '');
												echo render_textarea1('current_accommodation', 'current_accommodation', $current_accommodation)?>

											</div>
											<?php 
											$thumbnail = get_file_uri('plugins/Recruitment/Uploads/none_avatar.jpg');
											if (isset($candidate->avar)){
												$thumbnail = get_file_uri('plugins/Recruitment/Uploads/candidate/avartar/'.$candidate->id . '/' . $candidate->avar->file_name);
											}


											 ?>
											<div class="col-md-12 pull-left">
												<div class="">
													<div class="picture-container pull-left">
														<div class="picture pull-left">
															<img src="<?php if (isset($candidate->avar)) {echo html_entity_decode($thumbnail);} else {echo html_entity_decode($thumbnail);}?>" class="picture-src" id="wizardPicturePreview" title="">
															<input name="cd_avar" type="file" id="wizard-picture" accept=".png, .jpg, .jpeg" class="">
														</div>

														<h5 class=""><?php echo app_lang('choose_picture'); ?></h5>

													</div>
												</div>
											</div>
											<div class="col-md-12">
												<hr>
												<?php echo render_input1('file', 'file_campaign', '', 'file') ?>
											</div>
											<div class="col-md-12">
												<div class="form-group">
													<label for="password" class="col-md-3"><small class="req text-danger">* </small><?php echo app_lang('password'); ?></label>
													<div class="row">
														<div class=" col-md-11">
															<div class="input-group">
																<?php
																if(isset($candidate)){
																	echo form_password(array(
																		"id" => "password",
																		"name" => "password",
																		"class" => "form-control",
																		"placeholder" => app_lang('password'),
																		"autocomplete" => "off",
																		"data-rule-required" => true,
																		"data-msg-required" => app_lang("field_required"),
																		"data-rule-minlength" => 6,
																		"data-msg-minlength" => app_lang("enter_minimum_6_characters"),
																		"style" => "z-index:auto;"
																	));
																}else{

																	echo form_password(array(
																		"id" => "password",
																		"name" => "password",
																		"class" => "form-control",
																		"placeholder" => app_lang('password'),
																		"autocomplete" => "off",
																		"data-rule-required" => true,
																		"data-msg-required" => app_lang("field_required"),
																		"data-rule-minlength" => 6,
																		"data-msg-minlength" => app_lang("enter_minimum_6_characters"),
																		"autocomplete" => "off",
																		"required" => 1,
																		"style" => "z-index:auto;"
																	));
																}
																?>
																<button type="button" class="input-group-text clickable no-border" id="generate_password"><span data-feather="key" class="icon-16"></span> <?php echo app_lang('generate'); ?></button>
															</div>
														</div>
														<div class="col-md-1 p0">
															<a href="#" id="show_hide_password" class="btn btn-default" title="<?php echo app_lang('show_text'); ?>"><span data-feather="eye" class="icon-16"></span></a>
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>
								</div>
							</div>


							<div class="col-md-12">
								<div class="panel panel-success">
									<div class="page-title clearfix">
										<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('work_experience'); ?></h4>
									</div>
									<div class="modal-body clearfix">
										<div class="work_experience">
											<?php if (isset($candidate) && count($candidate->work_experience) > 0) {
												foreach ($candidate->work_experience as $key => $val) {
													?>
													<div class="row col-md-12 mb-4" id="work_experience-item">
														<div class="col-md-3">
															<?php
															 $from_date = '';
															 if( $val['from_date'] != '0000-00-00'){
															 	$from_date = format_to_date($val['from_date']);
															 }

															echo render_date_input1('from_date[' . $key . ']', 'from_date', $from_date, [], [], '', 'init-datepicker-from-date'.$key);?>
														</div>

														<div class="col-md-3">
															<?php
															 $to_date = '';
															 if( $val['to_date'] != '0000-00-00'){
															 	$to_date = format_to_date($val['to_date']);
															 }
															echo render_date_input1('to_date[' . $key . ']', 'to_date', $to_date, [], [], '', 'init-datepicker-to-date'.$key);?>
														</div>

														<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate1_js.php';?>
														

														<div class="col-md-3">
															<?php $company = $val['company'];
															echo render_input1('company[' . $key . ']', 'company', $company)?>
														</div>

														<div class="col-md-3">
															<?php $position = $val['position'];
															echo render_input1('position[' . $key . ']', 'position', $position)?>
														</div>

														<div class="col-md-3">
															<?php $contact_person = $val['contact_person'];
															echo render_input1('contact_person[' . $key . ']', 'contact_person', $contact_person)?>
														</div>
														<div class="col-md-3">
															<?php $salary = $val['salary'];
															echo render_input1('salary[' . $key . ']', 'salary', $salary)?>
														</div>

														<div class="col-md-6">
															<?php $reason_quitwork = $val['reason_quitwork'];
															echo render_input1('reason_quitwork[' . $key . ']', 'reason_quitwork', $reason_quitwork)?>
														</div>

														<div class="col-md-12">
															<?php $job_description = $val['job_description'];
															echo render_textarea1('job_description[' . $key . ']', 'job_description', $job_description)?>
														</div>
 
														<?php if ($key == 0) {?>
															<div class="col-md-12 ">
																<span class="input-group-btn pull-bot">
																	<button name="add" class="btn new_work_experience btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																</span>
															</div>
														<?php } else {?>
															<div class="col-md-12 ">
																<span class="input-group-btn pull-bot">
																	<button name="add" class="btn remove_work_experience btn-danger border-radius-4" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span> </button>
																</span>
															</div>
														<?php }?>
													</div>

												<?php }} else {?>
													<div class="row col-md-12 mb-4" id="work_experience-item">
														<div class="col-md-3">
															<?php echo render_date_input1('from_date[0]', 'from_date', '', [], [], '', 'init-datepicker-from-date0');?>
														</div>

														<div class="col-md-3">
															<?php echo render_date_input1('to_date[0]', 'to_date', '', [], [], '', 'init-datepicker-to-date0');?>
														</div>

														<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate2_js.php';?>


														<div class="col-md-3">
															<?php echo render_input1('company[0]', 'company') ?>
														</div>

														<div class="col-md-3">
															<?php echo render_input1('position[0]', 'position') ?>
														</div>

														<div class="col-md-3">
															<?php echo render_input1('contact_person[0]', 'contact_person') ?>
														</div>
														<div class="col-md-3">
															<?php echo render_input1('salary[0]', 'salary') ?>
														</div>

														<div class="col-md-6">
															<?php echo render_input1('reason_quitwork[0]', 'reason_quitwork') ?>
														</div>

														<div class="col-md-12">

															<p class="bold"><?php echo app_lang('job_description'); ?></p>
															<?php echo render_textarea1('job_description[0]','',''); ?>


														</div>

														<div class="col-md-12 ">
															<span class="input-group-btn pull-bot">
																<button name="add" class="btn new_work_experience btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
															</span>
														</div>

													</div>
												<?php }?>
											</div>
										</div>
									</div>
								</div>



								<div class="col-md-12">
									<div class="panel panel-default">
										<div class="page-title clearfix">
											<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('literacy'); ?></h4>
										</div>
										<div class="modal-body clearfix">
											<div class="list_literacy">
												<?php if (isset($candidate) && count($candidate->literacy) > 0) {
													foreach ($candidate->literacy as $key => $val) {
														?>
														<div class="row col-md-12 mb-4" id="literacy-item">
															<div class="col-md-2">
																<?php 

																$literacy_from_date = '';
																if( $val['literacy_from_date'] != '0000-00-00'){
																	$literacy_from_date = format_to_date($val['literacy_from_date']);
																}

																echo render_date_input1('literacy_from_date[' . $key . ']', 'from_date', $literacy_from_date, [], [], '', 'init-datepicker-literacy-from-date'.$key);?>
															</div>

															<div class="col-md-2">
																<?php
																 
																$literacy_to_date = '';
																if( $val['literacy_to_date'] != '0000-00-00'){
																	$literacy_to_date = format_to_date($val['literacy_to_date']);
																}

																echo render_date_input1('literacy_to_date[' . $key . ']', 'to_date', $literacy_to_date, [], [], '', 'init-datepicker-literacy-to-date'.$key);?>
															</div>
															<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate3_js.php';?>


															<div class="col-md-2">
																<?php $diploma = $val['diploma'];
																echo render_input1('diploma[' . $key . ']', 'diploma', $diploma)?>
															</div>

															<div class="col-md-2">
																<?php $training_places = $val['training_places'];
																echo render_input1('training_places[' . $key . ']', 'training_places', $training_places)?>
															</div>

															<div class="col-md-2">
																<?php $specialized = $val['specialized'];
																echo render_input1('specialized[' . $key . ']', 'specialized', $specialized)?>
															</div>
															<div class="col-md-2">
																<?php $training_form = $val['training_form'];
																echo render_input1('training_form[' . $key . ']', 'training_form', $training_form)?>
															</div>
															<?php if ($key == 0) {?>
																<div class="col-md-12">
																	<span class="input-group-btn pull-bot">
																		<button name="add" class="btn new_literacy btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																	</span>
																</div>
															<?php } else {?>
																<div class="col-md-12">
																	<span class="input-group-btn pull-bot">
																		<button name="add" class="btn remove_literacy btn-danger border-radius-4" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span> </button>
																	</span>
																</div>
															<?php }?>


														</div>
													<?php }} else {?>
														<div class="row col-md-12 mb-4" id="literacy-item">
															<div class="col-md-2">
																<?php echo render_date_input1('literacy_from_date[0]', 'from_date', '', [], [], '', 'init-datepicker-literacy-from-date0');?>
															</div>

															<div class="col-md-2">
																<?php echo render_date_input1('literacy_to_date[0]', 'to_date', '', [], [], '', 'init-datepicker-literacy-to-date0');?>
															</div>

															<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate4_js.php';?>

															<div class="col-md-2">
																<?php echo render_input1('diploma[0]', 'diploma') ?>
															</div>

															<div class="col-md-2">
																<?php echo render_input1('training_places[0]', 'training_places') ?>
															</div>

															<div class="col-md-2">
																<?php echo render_input1('specialized[0]', 'specialized') ?>
															</div>
															<div class="col-md-2">
																<?php echo render_input1('training_form[0]', 'training_form') ?>
															</div>

															<div class="col-md-12">
																<span class="input-group-btn pull-bot">
																	<button name="add" class="btn new_literacy btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																</span>
															</div>

														</div>
													<?php }?>
												</div>
											</div>
										</div>
									</div>


									<div class="col-md-12">
										<div class="panel panel-warning">
											<div class="page-title clearfix">
												<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('family_infor'); ?></h4>
											</div>
											<div class="modal-body clearfix">
												<div class="list_family_infor">
													<?php if (isset($candidate) && count($candidate->family_infor) > 0) {
														foreach ($candidate->family_infor as $key => $val) {
															?>
															<div class="row col-md-12 mb-4" id="family_infor-item">
																<div class="col-md-2">
																	<?php $relationship = $val['relationship'];
																	echo render_input1('relationship[' . $key . ']', 'relationship', $relationship);?>
																</div>

																<div class="col-md-2">
																	<?php $name = $val['name'];
																	echo render_input1('name[' . $key . ']', 'name', $name);?>
																</div>

																<div class="col-md-2">
																	<?php 

																	$fi_birthday = '';
																	if( $val['fi_birthday'] != '0000-00-00'){
																		$fi_birthday = format_to_date($val['fi_birthday']);
																	}

																	echo render_date_input1('fi_birthday[' . $key . ']', 'birthday', $fi_birthday, [], [], '', 'init-datepicker-fi-birthday'.$key);?>
																</div>


																<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate5_js.php';?>


																<div class="col-md-2">
																	<?php $job = $val['job'];
																	echo render_input1('job[' . $key . ']', 'job', $job)?>
																</div>

																<div class="col-md-2">
																	<?php $address = $val['address'];
																	echo render_input1('address[' . $key . ']', 'address', $address)?>
																</div>
																<div class="col-md-2">
																	<?php $phone = $val['phone'];
																	echo render_input1('phone[' . $key . ']', 'phone', $phone)?>
																</div>
																<?php if ($key == 0) {?>
																	<div class="col-md-12">
																		<span class="input-group-btn pull-bot">
																			<button name="add" class="btn new_family_infor btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																		</span>
																	</div>
																<?php } else {?>
																	<div class="col-md-12">
																		<span class="input-group-btn pull-bot">
																			<button name="add" class="btn remove_family_infor btn-danger border-radius-4" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span> </button>
																		</span>
																	</div>
																<?php }?>
															</div>
														<?php }} else {?>
															<div class="row col-md-12 mb-4" id="family_infor-item">
																<div class="col-md-2">
																	<?php echo render_input1('relationship[0]', 'relationship', ''); ?>
																</div>

																<div class="col-md-2">
																	<?php echo render_input1('name[0]', 'name', ''); ?>
																</div>

																<div class="col-md-2">
																	<?php echo render_date_input1('fi_birthday[0]', 'birthday', '', [], [], '', 'init-datepicker-fi-birthday0');?>
																</div>
																
																<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate6_js.php';?>

																<div class="col-md-2">
																	<?php echo render_input1('job[0]', 'job') ?>
																</div>

																<div class="col-md-2">
																	<?php echo render_input1('address[0]', 'address') ?>
																</div>
																<div class="col-md-2">
																	<?php echo render_input1('phone[0]', 'phone') ?>
																</div>

																<div class="col-md-12">
																	<span class="input-group-btn pull-bot">
																		<button name="add" class="btn new_family_infor btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																	</span>
																</div>

															</div>
														<?php }?>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

								<div class="card">
									<div class="container-fluid">
										<div class="">
											<div class="btn-bottom-toolbar text-right mb20 mt20">
												<a href="<?php echo get_uri('recruitment/candidate_profile'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>

												<?php if(re_has_permission("recruitment_can_create") || re_has_permission("recruitment_can_edit")){ ?>
													<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
												<?php } ?>
											</div>
										</div>
										<div class="btn-bottom-pusher"></div>
									</div>
								</div>

							</div>
						</div>
					</div>


					<?php require 'plugins/Recruitment/assets/js/candidate_profiles/add_edit_candidate_profile_js.php';?>

				</body>
				</html>