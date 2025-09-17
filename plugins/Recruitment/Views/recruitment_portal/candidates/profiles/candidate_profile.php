
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="row">

				<div class="col-md-8">
					<div class="card">

						<?php echo form_open_multipart('recruitment_portal/profile',array('autocomplete'=>'off')); ?>
						<?php echo form_hidden('profile',true); ?>

						<div class="page-title clearfix">
							<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('clients_profile_heading'); ?></h4>
						</div>


						<div class="modal-body clearfix">
							<div class="row">
								<div class="col-md-12">
									<h4 class="text-danger"><?php echo app_lang('file_campaign'); ?></h4>
									<?php echo render_input1('file', '', '', 'file') ?>

									<div class="row">
										<div id="contract_attachments" class="col-md-12">
											<?php if(isset($csv)){ ?>

												<?php
												$data = '<div class="row" id="attachment_file">';
												foreach($csv as $attachment) {

													$href_url = site_url('plugins/Recruitment/uploads/candidate/file/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
													if(!empty($attachment['external'])){
														$href_url = $attachment['external_link'];
													}
													$data .= '<div class="display-block contract-attachment-wrapper">';
													$data .= '<div class="row">';
													$data .= '<div class="col-md-1 mr-5 hide">';
													$data .= '<a name="preview-btn" onclick="preview_candidate_btn(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'.app_lang("preview_file").'">';
													$data .= '<i class="fa fa-eye"></i>'; 
													$data .= '</a>';
													$data .= '</div>';
													$data .= '<div class=col-md-9>';
													$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
													$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
													$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
													$data .= '</div>';
													$data .= '<div class="col-md-2 text-right">';
													$data .= '<a href="'.site_url('recruitment_portal/remove_candidate_cv/'.$attachment['id']) .'" class="text-danger" ><span data-feather="x" class="icon-16"></span> </a>';
													$data .= '</div>';
													$data .= '<div class="clearfix"></div><hr/>';
													$data .= '</div>';
												}
												$data .= '</div>';
												echo html_entity_decode($data);
												?>
											<?php } ?>
											<!-- check if edit contract => display attachment file end-->

										</div>

										<div id="contract_file_data"></div>
									</div>

									<h4 class="text-danger"><?php echo app_lang('re_contact_information'); ?></h4>
									<div class="form-group">
										<?php if($candidate->avar == NULL){ ?>
											<div class="form-group profile-image-upload-group">
												<label for="profile_image" class="profile-image"><?php echo app_lang('client_profile_image'); ?></label>
												<input type="file" name="cd_avar" class="form-control" id="cd_avar">
											</div>
										<?php } ?>
										<?php if($candidate->avar != NULL){ ?>
											<div class="form-group profile-image-group">
												<div class="row">
													<div class="col-md-9">
														<?php echo candidate_profile_image(get_candidate_id(),[
															'client-profile-image-thumb',
														], 'small', ['data-toggle' => 'tooltip', 'data-title' => get_candidate_name(get_candidate_id()), 'data-placement' => 'bottom' ]); ?>

													</div>
													<div class="col-md-3 text-right">
														<a class="text-danger" href="<?php echo site_url('recruitment_portal/remove_profile_image/'.$candidate->avar->id); ?>"><span data-feather="x" class="icon-16"></span></a>
													</div>
												</div>
											</div>
										<?php } ?>

									</div>
									<div class="form-group profile-firstname-group">
										<small class="req text-danger">* </small><label for="candidate_name"><?php echo app_lang('clients_firstname'); ?></label>
										<input type="text" class="form-control" name="candidate_name" id="candidate_name" value="<?php echo set_value('candidate_name',$candidate->candidate_name); ?>" data-rule-required="1" data-msg-required="<?php echo app_lang('field_required'); ?>" required>
									</div>
									<div class="form-group profile-lastname-group">
										<small class="req text-danger">* </small><label for="lastname"><?php echo app_lang('clients_lastname'); ?></label>
										<input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo set_value('last_name',$candidate->last_name); ?>" required>
									</div>

									<div class="form-group profile-email-group">
										<label for="email"><?php echo app_lang('clients_email'); ?></label>
										<input type="email" name="email" class="form-control" id="email" value="<?php echo html_entity_decode($candidate->email); ?>" disabled>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group profile-phone-group">
												<label for="phonenumber"><?php echo app_lang('clients_phone'); ?></label>
												<input type="text" class="form-control" name="phonenumber" id="phonenumber" value="<?php echo html_entity_decode($candidate->phonenumber); ?>">
											</div>
										</div>
										<div class="col-md-6">
											<?php $alternate_contact_number = (isset($candidate) ? $candidate->alternate_contact_number : '');
											echo render_input1('alternate_contact_number', 'alternate_contact_number', $alternate_contact_number);?>
										</div>
									</div>


									<div class="row">
										<div class="col-md-6">
											<?php $birthday = (isset($candidate) ? format_to_date($candidate->birthday, false) : '');
											echo render_date_input1('birthday', 'birthday', $birthday)?>
										</div>

										<div class="col-md-6">
											<div class="form-group">
												<label for="gender"><?php echo app_lang('gender'); ?></label>
												<select name="gender" id="gender" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('gender'); ?>">
													<option value=""></option>
													<option value="male" <?php if (isset($candidate) && $candidate->gender == 'male') {echo 'selected';}?>><?php echo app_lang('male'); ?></option>
													<option value="female" <?php if (isset($candidate) && $candidate->gender == 'female') {echo 'selected';}?>><?php echo app_lang('female'); ?></option>
												</select>
												<br><br>
											</div>
										</div>
										<div class="col-md-12">
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

									<ul class="nav nav-tabs pb15" id="myTab" role="tablist">
										<li class="nav-item" role="presentation">
											<button class="nav-link active" id="re_summary-tab" data-bs-toggle="tab" data-bs-target="#re_summary" type="button" role="tab" aria-controls="re_summary" aria-selected="true"><?php echo app_lang('re_summary'); ?></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="re_work_experience-tab" data-bs-toggle="tab" data-bs-target="#re_work_experience" type="button" role="tab" aria-controls="re_work_experience" aria-selected="false"><?php echo app_lang('re_work_experience'); ?></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="re_skills-tab" data-bs-toggle="tab" data-bs-target="#re_skills" type="button" role="tab" aria-controls="re_skills" aria-selected="false"><?php echo app_lang('re_skills'); ?></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="employment_literacy-tab" data-bs-toggle="tab" data-bs-target="#employment_literacy" type="button" role="tab" aria-controls="employment_literacy" aria-selected="false"><?php echo app_lang('employment_literacy'); ?></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link" id="re_other_information-tab" data-bs-toggle="tab" data-bs-target="#re_other_information" type="button" role="tab" aria-controls="re_other_information" aria-selected="false"><?php echo app_lang('re_other_information'); ?></button>
										</li>
										

									</ul>




									<div class="tab-content" id="myTabContent">
										<div class="tab-pane fade show active" id="re_summary" role="tabpanel" aria-labelledby="re_summary-tab">
											<div class="row">
												<div class="card-body">
													<?php $introduce_yourself = (isset($candidate) ? $candidate->introduce_yourself : '');
													$rows=[];
													$rows['rows'] = 6;
													echo render_textarea1('introduce_yourself', 'introduce_yourself', $introduce_yourself, $rows)?>
												</div>
											</div>
										</div>

										<div class="tab-pane fade" id="re_work_experience" role="tabpanel" aria-labelledby="re_work_experience-tab">

											<div class="row">
												<div class="card-body ">
													<div class="work_experience">
														<?php if (isset($candidate) && count($candidate->work_experience) > 0) {
															foreach ($candidate->work_experience as $key => $val) {
																?>
																<div class="row mbot20" id="work_experience-item">
																	<div class="col-md-3">
																		<?php $from_date = format_to_date($val['from_date']);
																		echo render_date_input1('from_date[' . $key . ']', 'from_date', $from_date);?>
																	</div>

																	<div class="col-md-3">
																		<?php $to_date = format_to_date($val['to_date']);
																		echo render_date_input1('to_date[' . $key . ']', 'to_date', $to_date);?>
																	</div>

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
																		<div class="col-md-12 line-height-content">
																			<span class="input-group-btn pull-bot">
																				<button name="add" class="btn new_work_experience btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																			</span>
																		</div> 
																	<?php } else {?>
																		<div class="col-md-12 line-height-content">
																			<span class="input-group-btn pull-bot">
																				<button name="add" class="btn remove_work_experience btn-danger border-radius-4" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
																			</span>
																		</div>
																	<?php }?>
																</div>

															<?php }} else {?>
																<div class="row mbot20" id="work_experience-item">
																	<div class="col-md-3">
																		<?php echo render_date_input1('from_date[0]', 'from_date', ''); ?>
																	</div>

																	<div class="col-md-3">
																		<?php echo render_date_input1('to_date[0]', 'to_date', ''); ?>
																	</div>

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

																		<p class=""><?php echo app_lang('job_description'); ?></p>
																		<?php echo render_textarea1('job_description[0]','',''); ?>


																	</div>

																	<div class="col-md-12 line-height-content">
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
											
											<div class="tab-pane fade" id="re_skills" role="tabpanel" aria-labelledby="re_skills-tab">

												<div class="row">
													<div class="card-body">

														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<select name="skill[]" id="skill" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('re_skills'); ?>">
																		<?php if(isset($candidate->skill)){ $skill_id = explode(',', $candidate->skill);} ; ?>

																		<?php foreach($skills as $dpkey =>  $skill){ ?>
																			<option value="<?php echo html_entity_decode($skill['id']); ?>"  <?php if(isset($skill_id) && in_array($skill['id'], $skill_id) == true ){echo 'selected';} ?>> <?php echo html_entity_decode($skill['skill_name']); ?></option>                  
																		<?php }?>
																	</select>
																</div>
															</div>

															<div class="col-md-6">
																<div class="form-group">
																	<select name="year_experience" id="year_experience" data-live-search="true" class="select2 validate-hidden" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">

																		<?php foreach(rec_year_experience() as $key =>  $year_experience){ ?>
																			<option value="<?php echo html_entity_decode($year_experience['value']); ?>"  <?php if(isset($candidate) && $candidate->year_experience == $year_experience['value'] ){echo 'selected';} ?>> <?php echo html_entity_decode($year_experience['label']); ?></option>                  
																		<?php }?>
																	</select>
																</div>
															</div>
														</div>

													</div>
												</div>
											</div>

											<div class="tab-pane fade" id="employment_literacy" role="tabpanel" aria-labelledby="employment_literacy-tab">

												<div class="row">
													<div class="card-body">
														<div class="list_literacy">
															<?php if (isset($candidate) && count($candidate->literacy) > 0) {
																foreach ($candidate->literacy as $key => $val) {
																	?>
																	<div class="row mbot20" id="literacy-item">
																		<div class="col-md-6">
																			<?php $literacy_from_date = format_to_date($val['literacy_from_date']);
																			echo render_date_input1('literacy_from_date[' . $key . ']', 'from_date', $literacy_from_date);?>
																		</div>

																		<div class="col-md-6">
																			<?php $literacy_to_date = format_to_date($val['literacy_to_date']);
																			echo render_date_input1('literacy_to_date[' . $key . ']', 'to_date', $literacy_to_date);?>
																		</div>

																		<div class="col-md-6">
																			<?php $diploma = $val['diploma'];
																			echo render_input1('diploma[' . $key . ']', 'diploma', $diploma)?>
																		</div>

																		<div class="col-md-6">
																			<?php $training_places = $val['training_places'];
																			echo render_input1('training_places[' . $key . ']', 'training_places', $training_places)?>
																		</div>

																		<div class="col-md-6">
																			<?php $specialized = $val['specialized'];
																			echo render_input1('specialized[' . $key . ']', 'specialized', $specialized)?>
																		</div>
																		<div class="col-md-6">
																			<?php $training_form = $val['training_form'];
																			echo render_input1('training_form[' . $key . ']', 'training_form', $training_form)?>
																		</div>
																		<?php if ($key == 0) {?>
																			<div class="col-md-12 line-height-content">
																				<span class="input-group-btn pull-bot">
																					<button name="add" class="btn new_literacy btn-success border-radius-4" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span> </button>
																				</span>
																			</div>
																		<?php } else {?>
																			<div class="col-md-12 line-height-content">
																				<span class="input-group-btn pull-bot">
																					<button name="add" class="btn remove_literacy btn-danger border-radius-4" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
																				</span>
																			</div>
																		<?php }?>


																	</div>
																<?php }} else {?>
																	<div class="row mbot20" id="literacy-item">
																		<div class="col-md-6">
																			<?php echo render_date_input1('literacy_from_date[0]', 'from_date', ''); ?>
																		</div>

																		<div class="col-md-6">
																			<?php echo render_date_input1('literacy_to_date[0]', 'to_date', ''); ?>
																		</div>

																		<div class="col-md-6">
																			<?php echo render_input1('diploma[0]', 'diploma') ?>
																		</div>

																		<div class="col-md-6">
																			<?php echo render_input1('training_places[0]', 'training_places') ?>
																		</div>

																		<div class="col-md-6">
																			<?php echo render_input1('specialized[0]', 'specialized') ?>
																		</div>
																		<div class="col-md-6">
																			<?php echo render_input1('training_form[0]', 'training_form') ?>
																		</div>

																		<div class="col-md-12 line-height-content">
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

												<div class="tab-pane fade" id="re_other_information" role="tabpanel" aria-labelledby="re_other_information-tab">
													
													<div class="row">
														<div class="card-body">
															<div class="row">
																<div class="col-md-12">
																	<?php 
																	$rows1=[];
																	$rows1['rows'] = 1;
																	?>
																	<?php $birthplace = (isset($candidate) ? $candidate->birthplace : '');
																	echo render_textarea1('birthplace', 'birthplace', $birthplace, $rows1)?>
																</div>

																<div class="col-md-12">
																	<?php $home_town = (isset($candidate) ? $candidate->home_town : '');
																	echo render_textarea1('home_town', 'home_town', $home_town, $rows1)?>
																</div>

																<div class="col-md-4">
																	<?php $identification = (isset($candidate) ? $candidate->identification : '');
																	echo render_input1('identification', 'identification', $identification);?>
																</div>
																<div class="col-md-4">
																	<?php $days_for_identity = (isset($candidate) ? format_to_date($candidate->days_for_identity) : '');
																	echo render_date_input1('days_for_identity', 'days_for_identity', $days_for_identity);?>
																</div>
																<div class="col-md-4">
																	<?php $place_of_issue = (isset($candidate) ? $candidate->place_of_issue : '');
																	echo render_input1('place_of_issue', 'place_of_issue', $place_of_issue);?>
																</div>

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
																	if(isset($candidate) && is_numeric($candidate->nationality)){
																		$nationality = get_country_name($candidate->nationality);
																	}

																	echo render_input1('nationality', 'nationality', $nationality);?>
																</div>
																<div class="col-md-4">
																	<?php $nation = (isset($candidate) ? $candidate->nation : '');
																	echo render_input1('nation', 'nation', $nation);?>
																</div>
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

																<?php 

																$rows1=[];
																$rows1['rows'] = 1;
																?>

																<div class="col-md-12">
																	<?php $interests = (isset($candidate) ? $candidate->interests : '');
																	echo render_textarea1('interests', 'interests', $interests, $rows1)?>
																</div>

																<div class="col-md-12">
																	<?php $skype = (isset($candidate) ? $candidate->skype : '');
																	echo render_input1('skype', 'skype', $skype);?>
																</div>
																<div class="col-md-12">
																	<?php $facebook = (isset($candidate) ? $candidate->facebook : '');
																	echo render_input1('facebook', 'facebook', $facebook);?>
																</div>

																<div class="col-md-12">
																	<?php $linkedin = (isset($candidate) ? $candidate->linkedin : '');
																	echo render_input1('linkedin', 'linkedin', $linkedin);?>
																</div>

																<div class="col-md-12">
																	<?php $resident = (isset($candidate) ? $candidate->resident : '');
																	echo render_textarea1('resident', 'resident', $resident, $rows1)?>
																</div>
																<div class="col-md-12">
																	<?php $current_accommodation = (isset($candidate) ? $candidate->current_accommodation : '');
																	echo render_textarea1('current_accommodation', 'current_accommodation', $current_accommodation, $rows1)?>

																</div>
															</div>
														</div>
													</div>
												</div>

											</div>


										</div>
										<div class="row p15 contact-profile-save-section">
											<div class="col-md-12 text-right mtop20">
												<div class="form-group">
													<button type="submit" class="btn btn-info contact-profile-save text-white"><?php echo app_lang('clients_edit_profile_update_btn'); ?></button>
												</div>
											</div>
										</div>
									</div>

								</div>
								<?php echo form_close(); ?>
							</div>
						</div>
						<div class="col-md-4 contact-profile-change-password-section">
							<div class="card">

								<div class="page-title clearfix">
									<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('clients_edit_profile_change_password_heading'); ?></h4>
								</div>


								<div class="modal-body clearfix">
									<?php echo form_open('recruitment_portal/profile'); ?>
									<?php echo form_hidden('change_password',true); ?>
									<div class="form-group">
										<small class="req text-danger">* </small><label for="oldpassword"><?php echo app_lang('clients_edit_profile_old_password'); ?></label>
										<input type="password" class="form-control" name="oldpassword" id="oldpassword" required>
									</div>
									<div class="form-group">
										<small class="req text-danger">* </small><label for="newpassword"><?php echo app_lang('clients_edit_profile_new_password'); ?></label>
										<input type="password" class="form-control" name="newpassword" id="newpassword" required>
									</div>
									<div class="form-group">
										<small class="req text-danger">* </small><label for="newpasswordr"><?php echo app_lang('clients_edit_profile_new_password_repeat'); ?></label>
										<input type="password" class="form-control" name="newpasswordr" id="newpasswordr" required>
									</div>
									<div class="form-group">
										<button type="submit" class="btn btn-info btn-block text-white"><?php echo app_lang('clients_edit_profile_change_password_btn'); ?></button>
									</div>
									<?php echo form_close(); ?>
								</div>

							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<?php require 'plugins/Recruitment/assets/js/recruitment_portals/candidates/profiles/candidate_profile_js.php';?>
