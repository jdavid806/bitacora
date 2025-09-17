<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<?php if (re_has_permission("recruitment_can_create") || is_admin()) {?>
							<a href="#" onclick="new_interview_schedule(); return false;" class="btn btn-info pull-left display-block text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('new_interview_schedule'); ?></a>
						<?php }?>

						<?php if (re_has_permission("recruitment_can_view_global") || is_admin()) {?>
							<a href="<?php echo get_uri('recruitment/calendar_interview_schedule'); ?>" class="btn btn-default pull-left display-block mleft5 d-none"><?php echo app_lang('calendar_view'); ?></a>
						<?php }?>

					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div class="col-md-3"> <?php echo render_date_input1('cp_from_date_filter', '', $from_date_filter
					, ['placeholder' => app_lang('from_date')]); ?></div>
					<div class="col-md-3"> <?php echo render_date_input1('cp_to_date_filter', '', '', ['placeholder' => app_lang('to_date')]); ?></div>
					<?php if(is_admin()){ ?>
						<div class="col-md-3">
							<div class="form-group">
								<select name="cp_manager_filter[]" id="cp_manager_filter" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-live-search="true" multiple="true" data-width="100%" placeholder="<?php echo app_lang('interviewer'); ?>">

									<?php foreach ($staffs as $s) {?>
										<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['first_name'] . ' ' . $s['last_name']); ?></option>
									<?php }?>
								</select>
							</div>
						</div>
					<?php } ?>

				</div>

				<div class="table-responsive">

					<?php
					$table_data = array(
						app_lang('interview_schedules_name'),
						app_lang('rec_time'),
						app_lang('interview_day'),
						app_lang('recruitment_campaign'),
						app_lang('candidate'),
						app_lang('interviewer'),
						app_lang('date_add'),
						app_lang('add_from'),
						app_lang('send_notify'),
						"<i data-feather='menu' class='icon-16'></i>",

					);
					render_datatable1($table_data,'table_interview',
				); ?>

			</div>
		</div>
	</div>
</div>
</div>


<div class="modal fade" id="interview_schedules_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<?php echo form_open_multipart(site_url('recruitment/interview_schedules'), array('id' => 'interview_schedule-form',"class" => "general-form")); ?>
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="add-title"><?php echo app_lang('new_interview_schedule'); ?></span>
					<span class="edit-title"><?php echo app_lang('edit_interview_schedule'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div id="additional_interview"></div>
					<div class="col-md-12">
						<h5 class="bold"><?php echo app_lang('general_infor') ?></h5>
						<hr class="margin-top-10"/>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="campaign"><span class="text-danger">* </span><?php echo app_lang('recruitment_campaign'); ?></label>
							<select onchange="campaign_change(); return false;" name="campaign" id="campaign" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('recruitment_campaign'); ?>" required>
								<option value="">- <?php echo app_lang('recruitment_campaign'); ?></option>
								<?php foreach ($rec_campaigns as $s) {?>
									<option value="<?php echo html_entity_decode($s['cp_id']); ?>" <?php if (isset($candidate) && $s['cp_id'] == $candidate->rec_campaign) {echo 'selected';}?>><?php echo html_entity_decode($s['campaign_code'] . ' - ' . $s['campaign_name']); ?></option>
								<?php }?>
							</select>
						</div>

					</div>
					<div class="col-md-4">
						<?php echo render_input1('is_name', 'interview_schedules_name', '', '', [], [], '', '', true) ?>

					</div>
					<div class="col-md-4">

						<div class="form-group">
							<label for="position"><?php echo app_lang('position'); ?></label>
							<select name="position" id="position" class="select2 validate-hidden" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('position'); ?>">
								<option value="">- <?php echo app_lang('position'); ?></option>
								<?php foreach ($positions as $p) {?>
									<option value="<?php echo html_entity_decode($p['position_id']); ?>"><?php echo html_entity_decode($p['position_name']); ?></option>
								<?php }?>

							</select>
						</div>

					</div>

					<div class="col-md-4">
						<?php echo render_date_input1('interview_day', 'interview_day', '', [] ,[], '', '', true); ?>
					</div>
					<div class="col-md-4">
						<?php echo render_input1('from_time', 'from_time', '', 'time', [], [], '', '', true); ?>

					</div>

					<div class="col-md-4">
						<?php echo render_input1('to_time', 'to_time', '', 'time', [], [], '', '', true); ?>

					</div>
					<div class="col-md-12">
						
						<?php echo render_input1('interview_location', 'interview_location', '', '', [], [], '', '', true); ?>
					</div>

					<div class="col-md-12 form-group">
						<label for="interviewer"><span class="text-danger">* </span><?php echo app_lang('interviewer'); ?></label>
						<select name="interviewer[]" id="interviewer" class="select2 validate-hidden" multiple="true" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('interviewer'); ?>" required>

							<?php foreach ($staffs as $s) {?>
								<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['first_name'] . ' ' . $s['last_name']); ?></option>
							<?php }?>
						</select>
						<br><br>
					</div>

					<div class="col-md-12">
						<h5 class="bold"><?php echo app_lang('list_of_candidates_participating'); ?></h5>
						<hr class="margin-top-10"/>
					</div>

					<div class="col-md-12">
						<div id="example"></div>
					</div>

					<div class="col-md-4"> <label for="candidate[0]"><span class="text-danger">* </span><?php echo app_lang('candidate'); ?></label> </div>
					<div class="col-md-3"> <label for="email"><?php echo app_lang('email').'/'.app_lang('phonenumber'); ?></label> </div>
					<div class="col-md-4"> <label for="phonenumber"><?php echo app_lang('from_time').'/'.app_lang('to_time'); ?></label> </div>

					<div class="list_candidates">

						<div class="row col-md-12" id="candidates-item">
							<div class="col-md-4 form-group">
								<select name="candidate[0]" onchange="candidate_infor_change(this); return false;" id="candidate[0]" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('ticket_settings_none_assigned'); ?>" required>
									<option value=""></option>
									<?php foreach ($candidates as $s) {?>
										<?php echo var_dump($candidates); ?>
										<option value="<?php echo html_entity_decode($s['id']); ?>"><?php echo html_entity_decode($s['candidate_code'] . ' ' . $s['candidate_name'] . ' ' . $s['last_name']); ?></option>
									<?php }?>
								</select>
							</div>

							<div class="col-md-4">

								<input type="text" disabled="true" name="email[0]" id="email[0]" class="form-control" />
							</div>

							<div class="col-md-3">
								<input type="text" disabled="true" name="phonenumber[0]" id="phonenumber[0]" class="form-control" />
							</div>
							<div class="col-md-4">
								<?php echo render_input1('cd_from_time', 'from_time', '', 'time'); ?>
							</div>

							<div class="col-md-4">
								<?php echo render_input1('cd_to_time', 'to_time', '', 'time'); ?>
							</div>

							<div class="col-md-1 lightheight-34-nowrap">
								<span class="input-group-btn pull-bot">
									<button name="add" class="btn new_candidates btn-success border-radius-4" data-ticket="true" type="button"><i class="fa fa-plus"></i></button>
								</span>
							</div>

						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">

				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
				<button id="sm_btn" type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>

			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php require 'plugins/Recruitment/assets/js/interview_schedules/interview_schedule_manage_js.php';?>

</body>
</html>
