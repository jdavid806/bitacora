<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<?php if (re_has_permission("recruitment_can_create") || is_admin()) { ?>

							<a href="<?php echo get_uri('recruitment/candidates'); ?>" class="btn btn-info pull-left display-block text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('new_candidate'); ?></a>
							<a href="#" onclick="send_mail_candidate(); return false;" class="btn btn-success pull-left display-block mleft5 text-white" ><span data-feather="send" class="icon-16"></span> <?php echo ' ' . app_lang('send_mail'); ?></a>

							<a href="<?php echo get_uri('recruitment/switch_kanban/'.$switch_kanban); ?>" class="btn btn-default mleft10 pull-left hidden-xs d-none">
								<?php if($switch_kanban == 1){ echo app_lang('rec_switch_to_list_view');}else{echo app_lang('rec_switch_to_kanban');}; ?>
							</a>

						<?php } ?>

						<?php if (re_has_permission("recruitment_can_create") || re_has_permission("recruitment_can_edit") || is_admin()) { ?>
							<a href="#" onclick="print_candidate_bulk_actions(); return false;" class="btn btn-primary pull-left display-block mleft5 text-white" ><span data-feather="file-plus" class="icon-16"></span> <?php echo app_lang('print_candidate'); ?></a>
						<?php } ?>

					</div>
				</div>
				<div class="row ml2 mr5 mt15">

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="company_filter" id="company_filter" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('company_name'); ?>">
								<option value="" >- <?php echo app_lang('company_name'); ?></option>

								<?php foreach($company_list as $cp_key =>  $company_value){ ?>
									<option value="<?php echo html_entity_decode($company_value['id']); ?>" > <?php echo html_entity_decode($company_value['company_name']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div>

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="skill_filter[]" id="skill_filter" class="select2 validate-hidden" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('skill_name'); ?>" data-actions-box="true">
								<?php foreach($skills as $dpkey =>  $skill){ ?>
									<option value="<?php echo html_entity_decode($skill['id']); ?>"> <?php echo html_entity_decode($skill['skill_name']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div> 

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="job_title_filter[]" id="job_title_filter" class="select2 validate-hidden" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('job_position'); ?>">
								<?php foreach($job_titles as $job_key =>  $job_value){ ?>
									<option value="<?php echo html_entity_decode($job_value['position_id']); ?>"> <?php echo html_entity_decode($job_value['position_name']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div>

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="experience_filter[]" id="experience_filter" class="select2 validate-hidden"  multiple="true" data-width="100%" placeholder="<?php echo app_lang('experience'); ?>" data-actions-box="true">
								<?php foreach(rec_year_experience() as $key =>  $year_experience){ ?>
									<option value="<?php echo html_entity_decode($year_experience['value']); ?>"> <?php echo html_entity_decode($year_experience['label']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div>

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="age_group_filter" id="age_group_filter" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('age_group'); ?>">
								<option value="" ><?php echo app_lang('age_group'); ?></option>
								<option value="11/16" ><?php echo app_lang('11 - 16'); ?></option>
								<option value="17/20" ><?php echo app_lang('17 - 20'); ?></option>
								<option value="21/24" ><?php echo app_lang('21 - 24'); ?></option>
								<option value="25/34" ><?php echo app_lang('25 - 34'); ?></option>
								<option value="35/44" ><?php echo app_lang('35 - 44'); ?></option>
								<option value="45/54" ><?php echo app_lang('45 - 54'); ?></option>
								<option value="55/64" ><?php echo app_lang('55 - 64'); ?></option>
								<option value="65" ><?php echo app_lang('65+'); ?></option>
							</select>
						</div>
					</div> 


					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="change_status[]" id="change_status" class="select2 validate-hidden" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('change_status_to'); ?>">
								<option value="1" ><?php echo app_lang('application'); ?></option>
								<option value="2" ><?php echo app_lang('potential'); ?></option>
								<option value="3" ><?php echo app_lang('interview'); ?></option>
								<option value="4" ><?php echo app_lang('won_interview'); ?></option>
								<option value="5" ><?php echo app_lang('send_offer'); ?></option>
								<option value="6" ><?php echo app_lang('elect'); ?></option>
								<option value="7" ><?php echo app_lang('non_elect'); ?></option>
								<option value="8" ><?php echo app_lang('unanswer'); ?></option>
								<option value="9" ><?php echo app_lang('transferred'); ?></option>
								<option value="10" ><?php echo app_lang('freedom'); ?></option>
							</select>
						</div>
					</div>  
					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="rec_campaign[]" id="rec_campaign" class="select2 validate-hidden" data-live-search="true" multiple="true"  data-width="100%" placeholder="<?php echo app_lang('recruitment_campaign'); ?>">

								<?php foreach ($rec_campaigns as $s) {?>
									<option value="<?php echo html_entity_decode($s['cp_id']); ?>" ><?php echo html_entity_decode($s['campaign_code'] . ' - ' . $s['campaign_name']); ?></option>
								<?php }?>
							</select>
						</div>
					</div>
					<div class="col-md-3"> <?php echo render_date_input1('birthday_filter', '', '', ['placeholder' => app_lang('birthday')]); ?></div>

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="gender_filter[]" id="gender_filter" class="select2 validate-hidden" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('gender'); ?>">
								<option value="male" ><?php echo app_lang('male'); ?></option>
								<option value="female" ><?php echo app_lang('female'); ?></option>
							</select>
						</div>
					</div>  

					<div class="col-lg-3 ">
						<div class="form-group">
							<select name="marital_status_filter[]" id="marital_status_filter" class="select2 validate-hidden" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('marital_status'); ?>">
								<option value="single" ><?php echo app_lang('single'); ?></option>
								<option value="married" ><?php echo app_lang('married'); ?></option>
							</select>
						</div>
					</div>  
				</div>


				<div class="table-responsive">

					<?php
					$table_data = array(
						app_lang('id'),
						app_lang('candidate_code'),
						app_lang('candidate_name'),
						app_lang('tranfer_personnel'),
						app_lang('skill_name'),
						app_lang('status'),
						app_lang('email'),
						app_lang('phonenumber'),
						app_lang('birthday'),
						app_lang('gender'),
						app_lang('marital_status'),
						app_lang('campaign'),
						"<i data-feather='menu' class='icon-16'></i>",

					);

					render_datatable1($table_data,'table_rec_candidate',
				); ?>

			</div>
		</div>
	</div>
</div>
</div>


<div class="modal fade" id="mail_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(site_url('recruitment/send_mail_list_candidate'), array('id' => 'mail_candidate-form', "class" => "general-form")); ?>
		<div class="modal-content width-100">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="add-title"><?php echo app_lang('send_mail'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<label for="candidate"><?php echo app_lang('send_to'); ?></label>
						<select name="candidate[]" id="candidate" class="select2 validate-hidden" multiple="true"  data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('send_to'); ?>" required>

							<?php foreach ($candidates as $s) {?>
								<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['candidate_code'] . ' ' . $s['candidate_name'].' '.$s['last_name']); ?></option>
							<?php }?>
						</select>
						<br><br>
					</div>
					<div class="col-md-12">

					</div>

					<div class="col-md-12">
						<?php echo render_input1('subject', 'subject', '', '', [], [], '', '', true); ?>
					</div>

					<div class="col-md-12">
						<?php echo render_textarea1('content', 'content', '', array(), array(), '', '', true) ?>
					</div>
					<div id="type_care">

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
				<button id="sm_btn" type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>


<?php echo form_open_multipart(site_url('recruitment/download_candidate_profile'), array('id'=>'item_print_candidate')); ?>      
<div class="modal fade" id="table_commodity_list_print_candidate" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="add-title"><?php echo app_lang('print_candidate'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<?php if(re_has_permission("recruitment_can_create") || is_admin()){ ?>

					<div class="row">
						<div class=" col-md-12">
							<div class="form-group">
								<select name="item_select_print_candidate[]" id="item_select_print_candidate" class="select2 validate-hidden" data-live-search="true" data-actions-box="true" data-width="100%" placeholder="<?php echo app_lang('select_candidate'); ?>">

									<?php foreach($candidates as $candidate) { ?>
										<option value="<?php echo html_entity_decode($candidate['id']); ?>"><?php echo html_entity_decode($candidate['candidate_code'].'-'.$candidate['candidate_name'].' '.$candidate['last_name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

				<?php } ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
				<?php if(re_has_permission("recruitment_can_create") || is_admin()){ ?>
					<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('confirm'); ?></button>

				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate_profile_manage_js.php';?>

</body>
</html>