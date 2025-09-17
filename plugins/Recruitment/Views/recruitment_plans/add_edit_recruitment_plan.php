<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-8 col-lg-8 container-fluid">
			<?php echo form_open_multipart(get_uri("recruitment/proposal"), array("id" => "proposal", "class" => "general-form", "role" => "form")); ?>
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>
				<div class="modal-body clearfix">
					<ul class="nav nav-tabs pb15" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="general_infor-tab" data-bs-toggle="tab" data-bs-target="#general_infor" type="button" role="tab" aria-controls="general_infor" aria-selected="true"><?php echo app_lang('general_infor'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="properties-tab" data-bs-toggle="tab" data-bs-target="#properties" type="button" role="tab" aria-controls="properties" aria-selected="false"><?php echo app_lang('candidate_request'); ?></button>
						</li>

					</ul>

					<?php 
						$id = '';
						$proposal_name = '';
						$position = '';
						$department = '';
						$amount_recruiment = '';
						$form_work = '';
						$workplace = '';
						$salary_from = '';
						$salary_to = '';
						$from_date = '';
						$to_date = '';
						$reason_recruitment = '';
						$job_description = '';
						$approver = '';
						$ages_from = '';
						$ages_to = '';
						$gender = '';
						$height = '';
						$weight = '';
						$literacy = '';
						$experience = '';

						if(isset($recruitment_plan)){
							$id = $recruitment_plan->id;
							$proposal_name = $recruitment_plan->proposal_name;
							$position = $recruitment_plan->position;
							$department = $recruitment_plan->department;
							$amount_recruiment = $recruitment_plan->amount_recruiment;
							$form_work = $recruitment_plan->form_work;
							$workplace = $recruitment_plan->workplace;
							$salary_from = $recruitment_plan->salary_from;
							$salary_to = $recruitment_plan->salary_to;
							$from_date = format_to_date($recruitment_plan->from_date);
							$to_date = format_to_date($recruitment_plan->to_date);
							$reason_recruitment = $recruitment_plan->reason_recruitment;
							$job_description = $recruitment_plan->job_description;
							$approver = $recruitment_plan->approver;
							$ages_from = $recruitment_plan->ages_from;
							$ages_to = $recruitment_plan->ages_to;
							$gender = $recruitment_plan->gender;
							$height = $recruitment_plan->height;
							$weight = $recruitment_plan->weight;
							$literacy = $recruitment_plan->literacy;
							$experience = $recruitment_plan->experience;
						}
					 ?>
					<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
					
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="general_infor" role="tabpanel" aria-labelledby="general_infor-tab">
							<div class="row">
								
								<div class="col-md-12"> 
									<?php echo render_input1('proposal_name','proposal_name', $proposal_name, '', [], [], '', '', true); ?>
								</div>
							</div>
							<div class="row">

								<div class="col-md-6">
									<label for="position"><small class="req text-danger">* </small><?php echo app_lang('position'); ?></label>
									<select name="position" id="position" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('position'); ?>" required>
										<option value="">- <?php echo app_lang('position'); ?> -</option>

										<?php foreach($positions as $s) { ?>
											<option value="<?php echo html_entity_decode($s['position_id']); ?>" <?php if($s['position_id'] == $position){ echo "selected";} ?>><?php echo html_entity_decode($s['position_name']); ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-6">
									<label for="department"><?php echo app_lang('department'); ?></label>
									<select name="department" id="department" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('department'); ?>">
										<option value="">- <?php echo app_lang('department'); ?> -</option>

										<?php foreach($departments as $s) { ?>
											<option value="<?php echo html_entity_decode($s['id']); ?>" <?php if($s['id'] == $department){ echo "selected";} ?>><?php echo html_entity_decode($s['title']); ?></option>
										<?php } ?>
									</select>
									<br><br>
								</div>
							</div>

							<div class="row">

								<div class="col-md-6">
									<?php echo render_input1('amount_recruiment','amount_recruiment', $amount_recruiment,'number'); ?>
								</div>
								<div class="col-md-6">
									<label for="form_of_work"><?php echo app_lang('form_of_work'); ?></label>
									<select name="form_work" id="form_of_work" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('form_of_work'); ?>">
										<option value="">- <?php echo app_lang('form_of_work'); ?> -</option>

										<option value="intership" <?php if('intership' == $form_work){ echo "selected";} ?>><?php echo app_lang('intership'); ?></option>
										<option value="full_time" <?php if('full_time' == $form_work){ echo "selected";} ?>><?php echo app_lang('full_time'); ?></option>
										<option value="part_time" <?php if('part_time' == $form_work){ echo "selected";} ?>><?php echo app_lang('part_time'); ?></option>
										<option value="collaborators" <?php if('collaborators' == $form_work){ echo "selected";} ?>><?php echo app_lang('collaborators'); ?></option>
									</select>
								</div>
							</div>
							<div class="row">

								<div class="col-md-12"> 
									<?php echo render_input1('workplace','workplace', $workplace); ?>
								</div>
							</div>
							<div class="row">

								<div class="col-md-6"> <?php 
								$attr = array();
								$attr = ['data-type' => 'currency'];
								?>
								<div class="form-group">
									<label><?php echo app_lang('starting_salary_from'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control text-right" name="salary_from" value="<?php echo html_entity_decode($salary_from); ?>" data-type="currency">
										<button type="button" class="input-group-text clickable no-border" id=""><?php echo get_setting("currency_symbol"); ?></button>

									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label><?php echo app_lang('starting_salary_from'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control text-right" name="salary_to" value="<?php echo html_entity_decode($salary_to); ?>" data-type="currency">

										<button type="button" class="input-group-text clickable no-border" id=""><?php echo get_setting("currency_symbol"); ?></button>
									</div>
								</div>

							</div>
						</div>
						<div class="row">

							<div class="col-md-6"> <?php echo render_date_input1('from_date','from_date', $from_date); ?></div>
							<div class="col-md-6"> <?php echo render_date_input1('to_date','to_date', $to_date, [], [] ,'', '', true); ?></div>
						</div>
						<div class="row">
							<div class="col-md-12"> <?php echo render_textarea1('reason_recruitment','reason_recruitment', $reason_recruitment) ?></div>
						</div>
						<div class="row">
							<div class="col-md-12"> <?php echo render_textarea1('job_description','job_description', $job_description,array(),array(),'') ?></div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<label for="approver"><small class="req text-danger">* </small><?php echo app_lang('approver'); ?></label>
								<select name="approver" id="approver" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('approver'); ?>" required>
									<option value="">- <?php echo app_lang('approver') ?></option>
									<?php foreach($staffs as $s) { ?>
										<option value="<?php echo html_entity_decode($s['id']); ?>" <?php if($approver == $s['id']){ echo "selected";} ?>><?php echo html_entity_decode($s['first_name'].' '.$s['last_name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

					</div>
					<div class="tab-pane fade " id="properties" role="tabpanel" aria-labelledby="properties-tab">
						<div class="row">

							<div class="col-md-6"> <?php echo render_input1('ages_from','ages_from', $ages_from,'number'); ?></div>
							<div class="col-md-6"> <?php echo render_input1('ages_to','ages_to', $ages_to,'number'); ?></div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<label for="gender"><?php echo app_lang('gender'); ?></label>
								<select name="gender" id="gender" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('gender'); ?>">
									<option value=""></option>
									<option value="male" <?php if($gender == 'male'){ echo "selected";} ?>><?php echo app_lang('male'); ?></option>
									<option value="female" <?php if($gender == 'female'){ echo "selected";} ?>><?php echo app_lang('female'); ?></option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="profit"><?php echo app_lang('height') ?></label>
								<div class="input-group">
									<button type="button" class="input-group-text clickable no-border" id=""><?php echo '>='; ?></button>
									<input type="number" id="height" name="height" class="form-control text-aligh-right" value="<?php echo html_entity_decode($height); ?>" min="0" max="3" step="0.1">

								</div>
							</div>
							<div class="col-md-4">
								<label for="profit"><?php echo app_lang('weight') ?></label>
								<div class="input-group">
									<button type="button" class="input-group-text clickable no-border" id=""><?php echo '>='; ?></button>
									
									<input type="weight" id="weight" name="weight" class="form-control text-aligh-right" value="<?php echo html_entity_decode($weight); ?>">

								</div>
								<br>
							</div>
						</div>

						<div class="row">

							<div class="col-md-6">
								<label for="literacy"><?php echo app_lang('literacy'); ?></label>
								<select name="literacy" id="literacy" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('not_required'); ?>">
									<option value="">- <?php echo app_lang('literacy'); ?> -</option>

									<option value="primary_level" <?php if($literacy == 'primary_level'){ echo "selected";} ?>><?php echo app_lang('primary_level'); ?></option>
									<option value="intermediate_level" <?php if($literacy == 'intermediate_level'){ echo "selected";} ?>><?php echo app_lang('intermediate_level'); ?></option>
									<option value="college_level" <?php if($literacy == 'primacollege_levelry_level'){ echo "selected";} ?>><?php echo app_lang('college_level'); ?></option>
									<option value="masters" <?php if($literacy == 'masters'){ echo "selected";} ?>><?php echo app_lang('masters'); ?></option>
									<option value="doctor" <?php if($literacy == 'doctor'){ echo "selected";} ?>><?php echo app_lang('doctor'); ?></option>
									<option value="bachelor" <?php if($literacy == 'bachelor'){ echo "selected";} ?>><?php echo app_lang('bachelor'); ?></option>
									<option value="engineer" <?php if($literacy == 'engineer'){ echo "selected";} ?>><?php echo app_lang('engineer'); ?></option>
									<option value="university" <?php if($literacy == 'university'){ echo "selected";} ?>><?php echo app_lang('university'); ?></option>
									<option value="intermediate_vocational" <?php if($literacy == 'primary_level'){ echo "selected";} ?>><?php echo app_lang('intermediate_vocational'); ?></option>
									<option value="college_vocational" <?php if($literacy == 'college_vocational'){ echo "selected";} ?>><?php echo app_lang('college_vocational'); ?></option>
									<option value="in-service" <?php if($literacy == 'in-service'){ echo "selected";} ?>><?php echo app_lang('in-service'); ?></option>
									<option value="high_school" <?php if($literacy == 'high_school'){ echo "selected";} ?>><?php echo app_lang('high_school'); ?></option>
									<option value="intermediate_level_pro" <?php if($literacy == 'intermediate_level_pro'){ echo "selected";} ?>><?php echo app_lang('intermediate_level_pro'); ?></option>

								</select>
							</div>
							<div class="col-md-6">
								<label for="experience"><?php echo app_lang('experience'); ?></label>
								<select name="experience" id="experience" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('experience'); ?>">
									<option value="">- <?php echo app_lang('experience'); ?> -</option>

									<option value="no_experience_yet" <?php if($experience == 'no_experience_yet'){ echo "selected";} ?>><?php echo app_lang('no_experience_yet'); ?></option>
									<option value="less_than_1_year" <?php if($experience == 'less_than_1_year'){ echo "selected";} ?>><?php echo app_lang('less_than_1_year'); ?></option>
									<option value="1_year" <?php if($experience == '1_year'){ echo "selected";} ?>><?php echo app_lang('1_year'); ?></option>
									<option value="2_years" <?php if($experience == '2_years'){ echo "selected";} ?>><?php echo app_lang('2_years'); ?></option>
									<option value="3_years" <?php if($experience == '3_years'){ echo "selected";} ?>><?php echo app_lang('3_years'); ?></option>
									<option value="4_years" <?php if($experience == '4_years'){ echo "selected";} ?>><?php echo app_lang('4_years'); ?></option>
									<option value="5_years" <?php if($experience == '5_years'){ echo "selected";} ?>><?php echo app_lang('5_years'); ?></option>
									<option value="over_5_years" <?php if($experience == 'over_5_years'){ echo "selected";} ?>><?php echo app_lang('over_5_years'); ?></option>
								</select>
								<br><br>
							</div>
						</div>

						<div class="row">

							<div class="col-md-12">
								<?php echo render_input1('file','file_proposal','','file') ?>

							</div>
						</div>

						<div class="row">
							<div id="contract_attachments" class="mtop30 ">
								<?php if(isset($proposal_attachment)){ ?>

									<?php
									$data = '<div class="row" id="attachment_file">';
									foreach($proposal_attachment as $attachment) {
										$href_url = base_url('plugins/Recruitment/Uploads/proposal/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
										if(!empty($attachment['external'])){
											$href_url = $attachment['external_link'];
										}
										$data .= '<div class="display-block contract-attachment-wrapper">';
										$data .= '<div class="row">';
										$data .= '<div class="col-md-1 mr-5">';
										$data .= modal_anchor(get_uri("recruitment/plan_file/".$attachment['id']."/".$attachment['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $attachment['file_name'], "data-post-id" => $attachment['id']));

										$data .= '</a>';
										$data .= '</div>';
										$data .= '<div class=col-md-9>';
										$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
										$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
										$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
										$data .= '</div>';
										$data .= '<div class="col-md-2 text-right">';
										if(is_admin() || re_has_permission("recruitment_can_delete")){
											$data .= '<a href="#" class="text-danger" onclick="delete_proposal_attachment(this,'.$attachment['id'].'); return false;"><span data-feather="x-circle" class="icon-16" ></span></a>';
										}
										$data .= '</div>';
										$data .= '</div>';

										$data .= '<div class="clearfix"></div><hr/>';
										$data .= '</div>';
									}
									$data .= '</div>';
									echo html_entity_decode($data);
									?>
								<?php } ?>

							</div>

							<div id="contract_file_data"></div>
						</div>
					</div>

				</div>


			</div>
		</div>

		<div class="card">
			<div class="container-fluid">
				<div class="">
					<div class="btn-bottom-toolbar text-right mb20 mt20">
						<a href="<?php echo get_uri('recruitment/recruitment_proposal'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>

						<?php if(re_has_permission("recruitment_can_create") || re_has_permission("recruitment_can_edit")){ ?>
							<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
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

<?php require 'plugins/Recruitment/assets/js/recruitment_plans/add_edit_recruitment_plan_js.php';?>
</body>
</html>
