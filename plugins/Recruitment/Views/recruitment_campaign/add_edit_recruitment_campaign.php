<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-8 col-lg-8 container-fluid">
			<?php echo form_open_multipart(get_uri("recruitment/campaign"), array("id" => "campaign", "class" => "general-form", "role" => "form")); ?>
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
					$cp_id = '';
					$cp_position = '';
					$campaign_code = '';
					$campaign_name = '';
					$department = '';
					$cp_amount_recruiment = '';
					$cp_form_work = '';
					$cp_workplace = '';
					$cp_salary_from = '';
					$cp_salary_to = '';
					$cp_from_date = '';
					$cp_to_date = '';
					$cp_reason_recruitment = '';
					$cp_job_description = '';
					$cp_ages_from = '';
					$cp_ages_to = '';
					$cp_gender = '';
					$cp_height = '';
					$cp_weight = '';
					$cp_literacy = '';
					$cp_experience = '';
					$cp_proposal = '';
					$rec_channel_form_id = '';
					$company_id = '';
					$arr_cp_manager = [];
					$arr_cp_follower = [];
					$display_salary = "checked";
					$cp_department = '';

					if(isset($recruitment_campaign)){
						$cp_id = $recruitment_campaign->cp_id;
						$cp_position = $recruitment_campaign->cp_position;
						$campaign_code = $recruitment_campaign->campaign_code;
						$campaign_name = $recruitment_campaign->campaign_name;
						$cp_department = $recruitment_campaign->cp_department;
						$cp_amount_recruiment = $recruitment_campaign->cp_amount_recruiment;
						$cp_form_work = $recruitment_campaign->cp_form_work;
						$cp_workplace = $recruitment_campaign->cp_workplace;
						$cp_salary_from = $recruitment_campaign->cp_salary_from;
						$cp_salary_to = $recruitment_campaign->cp_salary_to;
						$cp_from_date = format_to_date($recruitment_campaign->cp_from_date, false);
						$cp_to_date = format_to_date($recruitment_campaign->cp_to_date, false);
						$cp_reason_recruitment = $recruitment_campaign->cp_reason_recruitment;
						$cp_job_description = $recruitment_campaign->cp_job_description;
						$cp_ages_from = $recruitment_campaign->cp_ages_from;
						$cp_ages_to = $recruitment_campaign->cp_ages_to;
						$cp_gender = $recruitment_campaign->cp_gender;
						$cp_height = $recruitment_campaign->cp_height;
						$cp_weight = $recruitment_campaign->cp_weight;
						$cp_literacy = $recruitment_campaign->cp_literacy;
						$cp_experience = $recruitment_campaign->cp_experience;
						$cp_proposal = $recruitment_campaign->cp_proposal;
						$rec_channel_form_id = $recruitment_campaign->rec_channel_form_id;
						$company_id = $recruitment_campaign->company_id;
						$arr_cp_manager = explode(",", $recruitment_campaign->cp_manager);
						$arr_cp_follower = explode(",", $recruitment_campaign->cp_follower);
						if($recruitment_campaign->display_salary == 1){
							$display_salary = "checked";
						}else{
							$display_salary = "";
						}

					}
					?>
					<input type="hidden" name="cp_id" value="<?php echo html_entity_decode($cp_id); ?>">
					
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="general_infor" role="tabpanel" aria-labelledby="general_infor-tab">
							<div class="row">
								
								<div class="col-md-6"> 
									<?php echo render_input1('campaign_code','campaign_code', $campaign_code, '', [], [], '', '', true); ?>
								</div>
								<div class="col-md-6">
									<?php echo render_input1('campaign_name', 'campaign_name', $campaign_name, '', [], [], '', '', true); ?>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 <?php  if(get_setting('recruitment_create_campaign_with_plan') == 0 ){ echo 'hide';} ;?>">
									<div class="form-group">

										<label for="cp_proposal"><?php echo app_lang('recruitment_proposal'); ?></label>
										<select name="cp_proposal[]" id="proposal" class="select2 validate-hidden" multiple="true" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('recruitment_proposal'); ?>">

											<?php foreach ($rec_proposal as $s) {?>
												<option value="<?php echo html_entity_decode($s['id']); ?>" <?php if($s['id'] == $cp_proposal){ echo "selected";} ?>><?php echo html_entity_decode($s['proposal_name']); ?></option>
											<?php }?>
										</select>
									</div>
								</div>

								<div class="col-md-12">
									<div class="form-group">
										<label for="rec_channel_form_id"><?php echo app_lang('recruitment_channel_form'); ?></label>
										<select name="rec_channel_form_id" id="rec_channel_form_id" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('recruitment_channel_form'); ?>">
											<?php foreach ($rec_channel_form as $rec_c_f) {?>
												<option value="<?php echo html_entity_decode($rec_c_f['id']); ?>" <?php if($rec_c_f['id'] == $rec_channel_form_id){ echo "selected";} ?>><?php echo html_entity_decode($rec_c_f['r_form_name']); ?></option>
											<?php }?>
										</select>
									</div>
								</div>
							</div>

							<div class="row">

								<div class="col-md-6">
									<label for="cp_position"><small class="req text-danger">* </small><?php echo app_lang('position'); ?></label>
									<select name="cp_position" id="cp_position" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('ticket_settings_none_assigned'); ?>" required>
										<option value="">- <?php echo app_lang('position'); ?> -</option>

										<?php foreach($positions as $s) { ?>
											<option value="<?php echo html_entity_decode($s['position_id']); ?>" <?php if($s['position_id'] == $cp_position){ echo "selected";} ?>><?php echo html_entity_decode($s['position_name']); ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-md-6">
									<label for="company_id"><?php echo app_lang('company'); ?></label>
									<select name="company_id" id="company_id" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('ticket_settings_none_assigned'); ?>">
										<option value="">- <?php echo app_lang('company'); ?> -</option>

										<?php foreach($company_list as $company) { ?>
											<option value="<?php echo html_entity_decode($company['id']); ?>" <?php if($company['id'] == $company_id){ echo "selected";} ?>><?php echo html_entity_decode($company['company_name']); ?></option>
										<?php } ?>
									</select>
									<br><br>
								</div>
							</div>

							<div class="row">

								<div class="col-md-6">
									<?php echo render_input1('cp_amount_recruiment','amount_recruiment', $cp_amount_recruiment,'number'); ?>
								</div>
								<div class="col-md-6">
									<label for="cp_form_work"><?php echo app_lang('form_of_work'); ?></label>
									<select name="cp_form_work" id="form_of_work" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('ticket_settings_none_assigned'); ?>">
										<option value="">- <?php echo app_lang('form_of_work'); ?> -</option>

										<option value="intership" <?php if('intership' == $cp_form_work){ echo "selected";} ?>><?php echo app_lang('intership'); ?></option>
										<option value="full_time" <?php if('full_time' == $cp_form_work){ echo "selected";} ?>><?php echo app_lang('full_time'); ?></option>
										<option value="part_time" <?php if('part_time' == $cp_form_work){ echo "selected";} ?>><?php echo app_lang('part_time'); ?></option>
										<option value="collaborators" <?php if('collaborators' == $cp_form_work){ echo "selected";} ?>><?php echo app_lang('collaborators'); ?></option>
									</select>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<label for="cp_department"><?php echo app_lang('department'); ?></label>
									<select name="cp_department" id="cp_department" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('ticket_settings_none_assigned'); ?>">
										<option value="">- <?php echo app_lang('department'); ?> -</option>

										<?php foreach($departments as $department) { ?>
											<option value="<?php echo html_entity_decode($department['id']); ?>" <?php if($department['id'] == $cp_department){ echo "selected";} ?>><?php echo html_entity_decode($department['title']); ?></option>
										<?php } ?>
									</select>
									<br><br>
								</div>
								<div class="col-md-6"> 
									<?php echo render_input1('cp_workplace','workplace', $cp_workplace); ?>
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
										<input type="text" class="form-control text-right" name="cp_salary_from" value="<?php echo html_entity_decode($cp_salary_from); ?>" data-type="currency">
										<button type="button" class="input-group-text clickable no-border" id=""><?php echo get_setting("currency_symbol"); ?></button>

									</div>
								</div>

							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label><?php echo app_lang('starting_salary_to'); ?></label>
									<div class="input-group">
										<input type="text" class="form-control text-right" name="cp_salary_to" value="<?php echo html_entity_decode($cp_salary_to); ?>" data-type="currency">

										<button type="button" class="input-group-text clickable no-border" id=""><?php echo get_setting("currency_symbol"); ?></button>
									</div>
								</div>

							</div>
						</div>
						<div class="row">

							<div class="col-md-6"> <?php echo render_date_input1('cp_from_date','from_date', $cp_from_date); ?></div>
							<div class="col-md-6"> <?php echo render_date_input1('cp_to_date','to_date', $cp_to_date, [], [] ,'', '', true); ?></div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="checkbox checkbox-primary">
										<input  type="checkbox" id="display_salary" name="display_salary" value="display_salary" <?php echo html_entity_decode($display_salary); ?>>
										<label for="display_salary"><?php echo app_lang('rec_display_salary'); ?><small ><?php echo app_lang('rec_display_salary_tooltip') ?> </small>
										</label>
									</div>
								</div>
							</div>  
						</div>
						<div class="row">
							<div class="col-md-12"> <?php echo render_textarea1('cp_reason_recruitment','reason_recruitment', $cp_reason_recruitment) ?></div>
						</div>
						<div class="row">
							<div class="col-md-12"> <?php echo render_textarea1('cp_job_description','job_description', $cp_job_description,array(),array(),'') ?></div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label for="cp_manager"><small class="req text-danger">* </small><?php echo app_lang('manager'); ?></label>
								<select name="cp_manager[]" id="cp_manager" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('manager'); ?>" multiple="true">
									<?php foreach($staffs as $s) { ?>
										<option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(in_array($s['id'], $arr_cp_manager)){ echo "selected";} ?>><?php echo html_entity_decode($s['first_name'].' '.$s['last_name']); ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-md-6">
								<label for="cp_follower"><small class="req text-danger">* </small><?php echo app_lang('follower'); ?></label>
								<select name="cp_follower[]" id="cp_follower" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('follower'); ?>" multiple="true">
									<?php foreach($staffs as $s) { ?>
										<option value="<?php echo html_entity_decode($s['id']); ?>" <?php if(in_array($s['id'], $arr_cp_follower)){ echo "selected";} ?>><?php echo html_entity_decode($s['first_name'].' '.$s['last_name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

					</div>
					<div class="tab-pane fade " id="properties" role="tabpanel" aria-labelledby="properties-tab">
						<div class="row">

							<div class="col-md-6"> <?php echo render_input1('cp_ages_from','ages_from', $cp_ages_from,'number'); ?></div>
							<div class="col-md-6"> <?php echo render_input1('cp_ages_to','ages_to', $cp_ages_to,'number'); ?></div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<label for="cp_gender"><?php echo app_lang('gender'); ?></label>
								<select name="cp_gender" id="cp_gender" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('gender'); ?>">
									<option value=""></option>
									<option value="male" <?php if($cp_gender == 'male'){ echo "selected";} ?>><?php echo app_lang('male'); ?></option>
									<option value="female" <?php if($cp_gender == 'female'){ echo "selected";} ?>><?php echo app_lang('female'); ?></option>
								</select>
							</div>
							<div class="col-md-4">
								<label for="profit"><?php echo app_lang('height') ?></label>
								<div class="input-group">
									<button type="button" class="input-group-text clickable no-border" id=""><?php echo '>='; ?></button>
									<input type="number" id="cp_height" name="cp_height" class="form-control text-aligh-right" value="<?php echo html_entity_decode($cp_height); ?>" min="0" max="3" step="0.1">

								</div>
							</div>
							<div class="col-md-4">
								<label for="profit"><?php echo app_lang('weight') ?></label>
								<div class="input-group">
									<button type="button" class="input-group-text clickable no-border" id=""><?php echo '>='; ?></button>
									
									<input type="cp_weight" id="cp_weight" name="cp_weight" class="form-control text-aligh-right" value="<?php echo html_entity_decode($cp_weight); ?>">

								</div>
								<br>
							</div>
						</div>

						<div class="row">

							<div class="col-md-6">
								<label for="cp_literacy"><?php echo app_lang('literacy'); ?></label>
								<select name="cp_literacy" id="cp_literacy" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('not_required'); ?>">
									<option value="">- <?php echo app_lang('literacy'); ?> -</option>

									<option value="primary_level" <?php if($cp_literacy == 'primary_level'){ echo "selected";} ?>><?php echo app_lang('primary_level'); ?></option>
									<option value="intermediate_level" <?php if($cp_literacy == 'intermediate_level'){ echo "selected";} ?>><?php echo app_lang('intermediate_level'); ?></option>
									<option value="college_level" <?php if($cp_literacy == 'primacollege_levelry_level'){ echo "selected";} ?>><?php echo app_lang('college_level'); ?></option>
									<option value="masters" <?php if($cp_literacy == 'masters'){ echo "selected";} ?>><?php echo app_lang('masters'); ?></option>
									<option value="doctor" <?php if($cp_literacy == 'doctor'){ echo "selected";} ?>><?php echo app_lang('doctor'); ?></option>
									<option value="bachelor" <?php if($cp_literacy == 'bachelor'){ echo "selected";} ?>><?php echo app_lang('bachelor'); ?></option>
									<option value="engineer" <?php if($cp_literacy == 'engineer'){ echo "selected";} ?>><?php echo app_lang('engineer'); ?></option>
									<option value="university" <?php if($cp_literacy == 'university'){ echo "selected";} ?>><?php echo app_lang('university'); ?></option>
									<option value="intermediate_vocational" <?php if($cp_literacy == 'primary_level'){ echo "selected";} ?>><?php echo app_lang('intermediate_vocational'); ?></option>
									<option value="college_vocational" <?php if($cp_literacy == 'college_vocational'){ echo "selected";} ?>><?php echo app_lang('college_vocational'); ?></option>
									<option value="in-service" <?php if($cp_literacy == 'in-service'){ echo "selected";} ?>><?php echo app_lang('in-service'); ?></option>
									<option value="high_school" <?php if($cp_literacy == 'high_school'){ echo "selected";} ?>><?php echo app_lang('high_school'); ?></option>
									<option value="intermediate_level_pro" <?php if($cp_literacy == 'intermediate_level_pro'){ echo "selected";} ?>><?php echo app_lang('intermediate_level_pro'); ?></option>

								</select>
							</div>
							<div class="col-md-6">
								<label for="cp_experience"><?php echo app_lang('experience'); ?></label>
								<select name="cp_experience" id="cp_experience" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('ticket_settings_none_assigned'); ?>">
									<option value="">- <?php echo app_lang('experience'); ?> -</option>

									<option value="no_experience_yet" <?php if($cp_experience == 'no_experience_yet'){ echo "selected";} ?>><?php echo app_lang('no_experience_yet'); ?></option>
									<option value="less_than_1_year" <?php if($cp_experience == 'less_than_1_year'){ echo "selected";} ?>><?php echo app_lang('less_than_1_year'); ?></option>
									<option value="1_year" <?php if($cp_experience == '1_year'){ echo "selected";} ?>><?php echo app_lang('1_year'); ?></option>
									<option value="2_years" <?php if($cp_experience == '2_years'){ echo "selected";} ?>><?php echo app_lang('2_years'); ?></option>
									<option value="3_years" <?php if($cp_experience == '3_years'){ echo "selected";} ?>><?php echo app_lang('3_years'); ?></option>
									<option value="4_years" <?php if($cp_experience == '4_years'){ echo "selected";} ?>><?php echo app_lang('4_years'); ?></option>
									<option value="5_years" <?php if($cp_experience == '5_years'){ echo "selected";} ?>><?php echo app_lang('5_years'); ?></option>
									<option value="over_5_years" <?php if($cp_experience == 'over_5_years'){ echo "selected";} ?>><?php echo app_lang('over_5_years'); ?></option>
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
								<?php if(isset($campaign_attachment)){ ?>

									<?php
									$data = '<div class="row" id="attachment_file">';
									foreach($campaign_attachment as $attachment) {
										$href_url = base_url('plugins/Recruitment/Uploads/campaign/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
										if(!empty($attachment['external'])){
											$href_url = $attachment['external_link'];
										}
										$data .= '<div class="display-block contract-attachment-wrapper">';
										$data .= '<div class="row">';
										$data .= '<div class="col-md-1 mr-5">';
										$data .= modal_anchor(get_uri("recruitment/campaign_file/".$attachment['id']."/".$attachment['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $attachment['file_name'], "data-post-id" => $attachment['id']));

										$data .= '</a>';
										$data .= '</div>';
										$data .= '<div class=col-md-9>';
										$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
										$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
										$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
										$data .= '</div>';
										$data .= '<div class="col-md-2 text-right">';
										if(is_admin() || re_has_permission("recruitment_can_delete")){
											$data .= '<a href="#" class="text-danger" onclick="delete_campaign_attachment(this,'.$attachment['id'].'); return false;"><span data-feather="x-circle" class="icon-16" ></span></a>';
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
						<a href="<?php echo get_uri('recruitment/recruitment_campaign'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>

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

<?php require 'plugins/Recruitment/assets/js/recruitment_campaigns/add_edit_recruitment_campaign_js.php';?>
</body>
</html>
