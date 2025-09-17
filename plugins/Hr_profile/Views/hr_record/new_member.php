<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-8 col-lg-8 container-fluid">
			<?php echo form_open_multipart(get_uri("hr_profile/add_edit_member"), array("id" => "add_edit_member", "class" => "general-form", "role" => "form", "autocomplete" => "false")); ?>
			<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">

			<div class="card">
				<div class="card-header ">

					<ul class="nav nav-tabs pb15 justify-content-left border-bottom-0" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="tab_staff_profile-tab" data-bs-toggle="tab" data-bs-target="#tab_staff_profile" type="button" role="tab" aria-controls="tab_staff_profile" aria-selected="true"><?php echo app_lang('general_info'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="tab_staff_contact-tab" data-bs-toggle="tab" data-bs-target="#tab_staff_contact" type="button" role="tab" aria-controls="tab_staff_contact" aria-selected="false"><?php echo app_lang('hr_staff_profile_related_info'); ?></button>
						</li>
						
					</ul>
				</div>

				<div class="card-body">
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="tab_staff_profile" role="tabpanel" aria-labelledby="tab_staff_profile-tab">
							<?php
							$thumbnail = get_file_uri('plugins/Hr_profile/Uploads/none_avatar.jpg');

							if(isset($member)){
								$link_cover_image = get_avatar($member->image);
								$image_exist = file_exists($link_cover_image); 
							}else{
								$image_exist = false;
							}
							?>
							<div class="col-md-12">  
								<div class="picture-container float-start">
									<div class="picture pull-left">
										<img src="<?php if(isset($image_exist) && isset($link_cover_image)){ echo  html_entity_decode($link_cover_image); }else{  echo html_entity_decode($thumbnail); } ?>" class="picture-src" id="wizardPicturePreview" title="">
										<input type="file" name="profile_image" class="form-control" id="profile_image" accept=".png, .jpg, .jpeg">
									</div>
								</div>
							</div>


							<div class="clearfix"></div>
							<br>
							<div class="clearfix"></div>

							<div class="row">
								<?php $first_name = (isset($member) ? $member->first_name : ''); ?>
								<?php $last_name = (isset($member) ? $member->last_name : ''); ?>
								<?php $attrs = (isset($member) ? array() : array('autofocus'=>true)); ?>

								<div class="col-md-12">
									<?php  $hr_codes = (isset($member) ? $member->staff_identifi : $staff_code); ?>
									<?php echo render_input1('staff_identifi','hr_staff_code',$hr_codes,'text',$attrs, [], '', '', true); ?>
								</div> 

							</div>

							<div class="row">
								<div class="col-md-6">
									<?php echo render_input1('first_name','hr_firstname',$first_name,'text',$attrs, [],'', '', true); ?>
								</div>
								<div class="col-md-6">
									<?php echo render_input1('last_name','hr_lastname',$last_name,'text',$attrs, [], '', '', true); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="gender" class="control-label"><?php echo app_lang('hr_sex'); ?></label>
										<select name="gender" class="select2 validate-hidden" id="gender" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
											<option value=""></option>                  
											<option value="male" <?php if(isset($member) && $member->gender == 'male'){echo 'selected';} ?>><?php echo app_lang('male'); ?></option>
											<option value="female" <?php if(isset($member) && $member->gender == 'female'){echo 'selected';} ?>><?php echo app_lang('female'); ?></option>
											<option value="other" <?php if(isset($member) && $member->gender == 'other'){echo 'selected';} ?>><?php echo app_lang('other'); ?></option>
											
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<?php 
									$dob = '';
									if(isset($member) && $member->dob != '0000-00-00'){
										$dob = $member->dob;
									}
									echo render_date_input1('dob','date_of_birth', $dob); ?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<?php $value = isset($member) ? $member->email : ''; ?>
									<div class="form-group">
										<label for="email" class=" col-md-3"><small class="req text-danger">* </small><?php echo app_lang('email'); ?></label>
										<div class="row">
											<div class=" col-md-12">
												<?php
												echo form_input(array(
													"id" => "email",
													"name" => "email",
													"class" => "form-control",
													"autocomplete" => "off",
													"data-rule-email" => true,
													"data-msg-email" => app_lang("enter_valid_email"),
													"data-rule-required" => true,
													"data-msg-required" => app_lang("field_required"),
													"value" => $value,
													"autocomplete" => "off",
												));
												?>
											</div>
										</div>
									</div>
								</div>

								<div class="col-md-6">
									<?php $phone = (isset($member) ? $member->phone : ''); ?>
									<?php echo render_input1('phone','phone',$phone); ?>
								</div>


							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="workplace" class="control-label"><?php echo app_lang('hr_hr_workplace'); ?></label>
										<select name="workplace" class="select2 validate-hidden" id="workplace" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
											<option value=""></option>                  
											<?php foreach($workplace as $w){ ?>

												<option value="<?php echo html_entity_decode($w['id']); ?>" <?php if(isset($member) && $member->workplace == $w['id']){echo 'selected';} ?>><?php echo html_entity_decode($w['name']); ?></option>

											<?php } ?>
										</select>
									</div>
								</div>

								<div class="col-md-6">
									<?php 
									$status_work = isset($member) ? $member->status_work : 'working';
									$status_work_data = [];
									$status_work_data[] = [
										'name' => 'working',
										'value' => app_lang('hr_working'),
									];
									$status_work_data[] = [
										'name' => 'maternity_leave',
										'value' => app_lang('hr_maternity_leave'),
									];
									$status_work_data[] = [
										'name' => 'inactivity',
										'value' => app_lang('hr_inactivity'),
									];
									
									 ?>
									<?php echo render_select1('status_work', $status_work_data, array('name', 'value'), 'hr_status_work', $status_work, ['data-width' => '100%', 'class' => ''], array(), '', '', false, true); ?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<?php 
									$job_position = isset($member) ? $member->job_position : '';
									
									 ?>
									<?php echo render_select1('job_position', $positions, array('position_id', 'position_name'), 'hr_hr_job_position', $job_position, ['data-width' => '100%', 'class' => ''], array(), '', '', false, true); ?>
								</div>


								<div class="col-md-6">
									<?php if(has_permission('hrm_hr_records','', 'edit') || has_permission('hrm_hr_records','', 'create')){ ?>
										<?php $value = (isset($member) ? $member->team_manage : ''); ?>
										<?php echo render_select1('team_manage',$list_staff,array('id',array('first_name', 'last_name')),'hr_team_manage',$value); ?>
									<?php } ?>
								</div>
							</div>  

							<?php if(is_admin() || has_permission('hrm_hr_records','', 'edit')){ ?>

								<?php
								$role_id = isset($member) ? $member->role_id : 0;
								if(isset($member) && $member->is_admin){
									$role_id = 'admin';
								}
								?>

								<div class="row">
									<div class="col-md-6">
										<?php if(isset($member) && $member->is_admin && !is_admin()){ ?>
											<?php echo render_select1('role',$roles,array('id','title'),'role',$role_id, ['disabled' => 'disabled'], [], '', '', false); ?>
										<?php }else{ ?>
											<?php echo render_select1('role',$roles,array('id','title'),'role',$role_id, [], [], '', '', false); ?>
										<?php } ?>
									</div>
									<div class="col-md-6">
										<?php 
										$ssn = (isset($member) ? $member->ssn : '');
										echo render_input1('ssn','ssn',$ssn,'text'); ?> 
									</div>
								</div>
							<?php } ?>

							<div class="row">
								<div class="col-md-6">
									<?php $literacy = (isset($member) ? $member->literacy : ''); ?> 
									<div class="form-group">
										<label for="literacy" class="control-label"><?php echo app_lang('hr_hr_literacy'); ?></label>
										<select name="literacy" id="literacy" class="select2 validate-hidden" data-width="100%" data-none-selected-text="<?php echo app_lang('hr_not_required'); ?>">
											<option value="">-</option>
											<option value="primary_level" <?php if($literacy == 'primary_level'){ echo 'selected'; } ?> ><?php echo app_lang('hr_primary_level'); ?></option>
											<option value="intermediate_level" <?php if($literacy == 'intermediate_level'){ echo 'selected'; } ?> ><?php echo app_lang('hr_intermediate_level'); ?></option>
											<option value="college_level" <?php if($literacy == 'college_level'){ echo 'selected'; } ?> ><?php echo app_lang('hr_college_level'); ?></option>
											<option value="masters" <?php if($literacy == 'masters'){ echo 'selected'; } ?> ><?php echo app_lang('hr_masters'); ?></option>
											<option value="doctor" <?php if($literacy == 'doctor'){ echo 'selected'; } ?> ><?php echo app_lang('hr_Doctor'); ?></option>
											<option value="bachelor" <?php if($literacy == 'bachelor'){ echo 'selected'; } ?> ><?php echo app_lang('hr_bachelor'); ?></option>
											<option value="engineer" <?php if($literacy == 'engineer'){ echo 'selected'; } ?> ><?php echo app_lang('hr_Engineer'); ?></option>
											<option value="university" <?php if($literacy == 'university'){ echo 'selected'; } ?> ><?php echo app_lang('hr_university'); ?></option>
											<option value="intermediate_vocational" <?php if($literacy == 'intermediate_vocational'){ echo 'selected'; } ?> ><?php echo app_lang('hr_intermediate_vocational'); ?></option>
											<option value="college_vocational" <?php if($literacy == 'college_vocational'){ echo 'selected'; } ?> ><?php echo app_lang('hr_college_vocational'); ?></option>
											<option value="in-service" <?php if($literacy == 'in-service'){ echo 'selected'; } ?> ><?php echo app_lang('hr_in-service'); ?></option>
											<option value="high_school" <?php if($literacy == 'high_school'){ echo 'selected'; } ?> ><?php echo app_lang('hr_high_school'); ?></option>
											<option value="intermediate_level_pro" <?php if($literacy == 'intermediate_level_pro'){ echo 'selected'; } ?> ><?php echo app_lang('hr_intermediate_level_pro'); ?></option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="hourly_rate"><?php echo app_lang('staff_hourly_rate'); ?></label>
										<div class="input-group">
											<input type="number" name="hourly_rate" step="any" value="<?php if(isset($member)){echo html_entity_decode($member->hourly_rate);} else {echo 0;} ?>" id="hourly_rate" class="form-control">
										</div>
									</div>
								</div>
							</div>

							<?php if(is_admin() || has_permission('hrm_hr_records','', 'edit')){ ?>
								<div class="form-group">
									<div class="row">
										<div class="col-md-12">
											<?php
											$orther_infor = (isset($member) ? $member->orther_infor : '');
											echo render_textarea1('orther_infor','hr_orther_infor',$orther_infor); ?>
										</div>
									</div>

									<br>
									<?php if(count($departments) > 0){ ?>
										<label for="departments"><?php echo app_lang('team'); ?></label>
									<?php } ?>

									<?php foreach($departments as $department){ ?>
										<div class="checkbox checkbox-primary">
											<?php
											$checked = '';
											if(isset($member)){
												foreach ($staff_departments as $staff_department) {
													if($staff_department['id'] == $department['id']){
														$checked = ' checked';
													}
												}
											}
											?>
											<input type="checkbox" id="dep_<?php echo html_entity_decode($department['id']); ?>" name="departments[]" class='form-check-input' value="<?php echo html_entity_decode($department['id']); ?>"<?php echo html_entity_decode($checked); ?>>
											<label for="dep_<?php echo html_entity_decode($department['id']); ?>"><?php echo html_entity_decode($department['title']); ?></label>
										</div>
									<?php } ?>
								</div>
							<?php } ?>

							

							<div class="form-group">
								<label for="password" class="col-md-3"><small class="req text-danger">* </small><?php echo app_lang('password'); ?></label>
								<div class="row">
									<div class=" col-md-11">
										<div class="input-group">
											<?php
											if(isset($member)){
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
							<div class="row">
								<div class="col-md-12">
									<hr class="hr-10" />
									<div class="checkbox checkbox-primary">
										<input type="checkbox" name="email_login_details" id="email_login_details" class='form-check-input'>
										<label for="email_login_details"><?php echo app_lang('email_login_details'); ?></label>
									</div>
								</div>
							</div>

							</div>
							<div class="tab-pane fade show" id="tab_staff_contact" role="tabpanel" aria-labelledby="tab_staff_contact-tab">
								<div class="row">
									<div class="col-md-6">
										<?php 
										$home_town = (isset($member) ? $member->home_town : '');
										echo render_input1('home_town','hr_hr_home_town',$home_town,'text'); ?> 
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label for="marital_status" class="control-label"><?php echo app_lang('hr_hr_marital_status'); ?></label>
											<select name="marital_status" class="select2 validate-hidden" id="marital_status" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
												<option value="">-</option>                  
												<option value="single" <?php if(isset($member) && $member->marital_status == 'single'){echo 'selected';} ?>><?php echo app_lang('hr_single'); ?></option>
												<option value="married" <?php if(isset($member) && $member->marital_status == 'married'){echo 'selected';} ?>><?php echo app_lang('hr_married'); ?></option>
											</select>
										</div>
									</div>


								</div>

								<div class="row">
									<div class="col-md-6">
										<?php 
										$address = (isset($member) ? $member->address : '');
										echo render_input1('address','hr_current_address',$address,'text'); ?>
									</div>
									<div class="col-md-6">
										<?php
										$nation = (isset($member) ? $member->nation : '');
										echo render_input1('nation','hr_hr_nation',$nation,'text'); ?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
										$birthplace = (isset($member) ? $member->birthplace : '');
										echo render_input1('birthplace','hr_hr_birthplace',$birthplace,'text'); ?> 
									</div>
									<div class="col-md-6">
										<?php 
										$religion = (isset($member) ? $member->religion : '');
										echo render_input1('religion','hr_hr_religion',$religion,'text'); ?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php 
										$identification = (isset($member) ? $member->identification : '');
										echo render_input1('identification','hr_citizen_identification',$identification,'text'); ?>
									</div>
									<div class="col-md-6">
										<?php
										$days_for_identity = '';
										if(isset($member) && $member->days_for_identity != '0000-00-00'){
											$days_for_identity = $member->days_for_identity;
										}
										echo render_date_input1('days_for_identity','hr_license_date', format_to_date($days_for_identity)); ?>
									</div>
								</div> 

								<div class="row">
									<div class="col-md-6">
										<?php
										$place_of_issue = (isset($member) ? $member->place_of_issue : '');
										echo render_input1('place_of_issue','hr_hr_place_of_issue',$place_of_issue, 'text'); ?>
									</div>
									<div class="col-md-6">
										<?php 
										$resident = (isset($member) ? $member->resident : '');
										echo render_input1('resident','hr_hr_resident',$resident,'text'); ?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
										$account_number = (isset($member) ? $member->account_number : '');
										echo render_input1('account_number','hr_bank_account_number',$account_number, 'text'); ?>
									</div>
									<div class="col-md-6">
										<?php
										$name_account = (isset($member) ? $member->name_account : '');
										echo render_input1('name_account','hr_bank_account_name',$name_account, 'text'); ?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<?php
										$issue_bank = (isset($member) ? $member->issue_bank : '');
										echo render_input1('issue_bank','hr_bank_name',$issue_bank, 'text'); ?>
									</div>
									<div class="col-md-6">
										<?php
										$Personal_tax_code = (isset($member) ? $member->Personal_tax_code : '');
										echo render_input1('Personal_tax_code','hr_Personal_tax_code',$Personal_tax_code, 'text'); ?>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="facebook" class="control-label"><i class="fa fa-facebook"></i> Facebook</label>
											<input type="text" class="form-control" placeholder="https://www.facebook.com/" name="facebook" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->facebook);} ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="linkedin" class="control-label"><i class="fa fa-linkedin"></i> Linkedin</label>
											<input type="text" class="form-control" placeholder="https://www.linkedin.com" name="linkedin" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->linkedin);} ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="twitter" class="control-label"><i class="fa fa-twitter"></i> Twitter</label>
											<input type="text" class="form-control" placeholder="https://twitter.com/" name="twitter" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->twitter);} ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="whatsapp" class="control-label"><i class="fa fa-whatsapp"></i> WhatsApp</label>
											<input type="text" class="form-control" name="whatsapp" placeholder="https://wa.me/+001XXXXXXX" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->whatsapp);} ?>">
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="skype" class="control-label"><i class="fa fa-skype"></i> Skype</label>
											<input type="text" class="form-control" name="skype" value="<?php if(isset($member)){echo html_entity_decode($member->skype);} ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="digg" class="control-label"><i class="fa fa-digg"></i> Digg</label>
											<input type="text" class="form-control" name="digg" placeholder="http://digg.com/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->digg);} ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="youtube" class="control-label"><i class="fa fa-youtube"></i> youtube</label>
											<input type="text" class="form-control" name="youtube" placeholder="https://www.youtube.com/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->youtube);} ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="pinterest" class="control-label"><i class="fa fa-pinterest"></i> pinterest</label>
											<input type="text" class="form-control" name="pinterest" placeholder="https://www.pinterest.com/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->pinterest);} ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="instagram" class="control-label"><i class="fa fa-instagram"></i> instagram</label>
											<input type="text" class="form-control" name="instagram" placeholder="https://instagram.com/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->instagram);} ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="github" class="control-label"><i class="fa fa-github"></i> github</label>
											<input type="text" class="form-control" name="github" placeholder="https://github.com/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->github);} ?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="tumblr" class="control-label"><i class="fa fa-tumblr"></i> tumblr</label>
											<input type="text" class="form-control" name="tumblr" placeholder="https://www.tumblr.com/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->tumblr);} ?>">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="vine" class="control-label"><i class="fa fa-vine"></i> vine</label>
											<input type="text" class="form-control" name="vine" placeholder="https://vine.co/" value="<?php if(isset($social_link)){echo html_entity_decode($social_link->vine);} ?>">
										</div>
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
								<a href="<?php echo get_uri('hr_profile/staff_infor'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>

								<?php if (is_admin() || hr_has_permission('hr_profile_can_edit_hr_records') || hr_has_permission('hr_profile_can_create_hr_records')) { ?>
									<button type="button" class="btn btn-primary text-white add_edit_member_submit"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
								<?php } ?>

							</div>
						</div>
						<div class="btn-bottom-pusher"></div>
					</div>
				</div>

				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
	<div id="modal_wrapper"></div>
	<div id="change_serial_modal_wrapper"></div>

	<?php require 'plugins/Hr_profile/assets/js/hr_record/add_update_staff_js.php';?>

</body>
</html>

