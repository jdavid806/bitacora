    <?php echo form_hidden('site_url', base_url()); ?>
    <?php echo form_hidden('admin_url', get_uri()); ?>

<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="row">
			<div class="col-md-12" id="training-add-edit-wrapper">
				<div class="row">
					<div class="col-md-12">
						<div class="panel_s">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-12 margin-left-12">
										<h4 class="modal-title pl-3">
											<span class="edit-title"><?php echo app_lang('add_recuitment_channel'); ?></span>

										</h4>
									</div>
								</div>
								<div class="modal-body">

									<ul class="nav nav-tabs pb15" id="myTab" role="tablist">
										<li class="nav-item" role="presentation">
											<button class="nav-link active" id="form_infomation-tab" data-bs-toggle="tab" data-bs-target="#form_infomation" type="button" role="tab" aria-controls="form_infomation" aria-selected="true"><?php echo app_lang('form_infomation'); ?></button>
										</li>
										<li class="nav-item" role="presentation">
											<button class="nav-link " id="form_builder-tab" data-bs-toggle="tab" data-bs-target="#form_builder" type="button" role="tab" aria-controls="form_builder" aria-selected="false"><?php echo app_lang('form_builder'); ?></button>
										</li>
									</ul>

									<?php echo form_open_multipart(site_url('recruitment/add_edit_recruitment_channel'), array('class' => 'recruitment-channel-add-edit general-form', 'autocomplete' => 'off')); ?>

									<div class="tab-content">
										<?php if (isset($recruitment_channel_id)) {?>
											<?php echo form_hidden('recruitment_channel_id', $recruitment_channel_id); ?>
										<?php }?>
										<!-- form_infomation start -->
										<div role="tabpanel" class="tab-pane active" id="form_infomation" aria-labelledby="form_infomation-tab">
											<div class="row mt-5">
												<div class="col-md-6">

													<?php $r_form_name = (isset($form->r_form_name) ? $form->r_form_name : '');?>

													<?php echo render_input1('r_form_name', 'form_name', $r_form_name, '', [], [], '', '', true); ?>

													<!-- form type -->
													<?php $related = [
														0 => ['id' => '1', 'name' => app_lang('form')],
													];?>
													<?php $form_type_value = (isset($form->form_type) ? $form->form_type : '');?>
													<?php echo render_select1('form_type', $related, array('id', 'name'), 'form_type', $form_type_value, [], [], '', '', false, true); ?>



													<div class="form-group select-placeholder">
														<label for="language" class="control-label"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo app_lang('form_lang_validation_help'); ?>"><small class="req text-danger">* </small></i> <?php echo app_lang('re_form_lang_validation'); ?></label>
														<select name="language" id="language" class="select2 validate-hidden" placeholder="<?php echo app_lang('re_form_lang_validation'); ?>" required>
															<?php foreach ($languages as $availableLanguage) {
																?>
																<option value="<?php echo html_entity_decode($availableLanguage); ?>"<?php if ((isset($form->language) && $form->language == $availableLanguage) || (!isset($form) && 'English' == $availableLanguage)) {
																	echo ' selected';
																}?>><?php echo html_entity_decode(ucfirst($availableLanguage)); ?></option>
															<?php }?>
														</select>
													</div>

													<?php $value = (isset($form->submit_btn_name) ? $form->submit_btn_name : 'Submit');?>
													<?php echo render_input1('submit_btn_name', 're_form_btn_submit_text', $value, '', [], [], '', '', true); ?>

													<?php $value = (isset($form->success_submit_msg) ? $form->success_submit_msg : '');?>
													<?php echo render_textarea1('success_submit_msg', 're_form_success_submit_msg', $value); ?>

												</div>
												<div class="col-md-6">
													<?php $lead_status = (isset($form->lead_status) ? $form->lead_status : '');?>
													<?php

													$status = ['1' => ['id' => '1', 'name' => app_lang('application')],
													'2' => ['id' => '2', 'name' => app_lang('potential')],
													'3' => ['id' => '3', 'name' => app_lang('interview')],
													'4' => ['id' => '4', 'name' => app_lang('won_interview')],
													'5' => ['id' => '5', 'name' => app_lang('send_offer')],
													'6' => ['id' => '6', 'name' => app_lang('elect')],
													'7' => ['id' => '7', 'name' => app_lang('non_elect')],
													'8' => ['id' => '8', 'name' => app_lang('unanswer')],
													'9' => ['id' => '9', 'name' => app_lang('transferred')],
													'11' => ['id' => '10', 'name' => app_lang('preliminary_selection')],
												];

												echo render_select1('lead_status', $status, array('id', 'name'), 'status', $lead_status,[], [], '', '', false, true );

												$selected = '';
												foreach ($members as $staff) {
													if (isset($form->responsible) && $form->responsible == $staff['id']) {
														$selected = $staff['id'];
													} elseif(!isset($form->responsible)){
														$selected = get_staff_user_id1();

													}
												}
												?>
												<?php echo render_select1('responsible', $members, array('id', array('first_name', 'last_name')), 're_notify_assigned_user', $selected, [], [], '','',false, true); ?>

												<hr />
												<label for="" class="control-label"><?php echo app_lang('notification_settings'); ?></label>
												<div class="clearfix"></div>
												<div class="checkbox checkbox-primary">
													<input type="checkbox" class="form-check-input" name="notify_lead_imported" id="notify_lead_imported" <?php
													if (isset($form->notify_lead_imported) && $form->notify_lead_imported == 1 || !isset($form->notify_lead_imported)) {
														echo 'checked';
													}?>>
													<label for="notify_lead_imported"><?php echo app_lang('notify_when_new_candidates'); ?></label>
												</div>
												<div class="select-notification-settings<?php if (isset($form) && $form->notify_lead_imported == '0') {
													echo ' hide';
												}?>">
												<hr />
												<div class="radio radio-primary radio-inline">
													<input type="radio" name="notify_type" value="specific_staff" id="specific_staff" <?php if (isset($form) && $form->notify_type == 'specific_staff' || !isset($form)) {
														echo 'checked';
													} ?> class="form-check-input">
													<label for="specific_staff"><?php echo app_lang('re_specific_staff_members'); ?></label>
												</div>
												<div class="radio radio-primary radio-inline">
													<input type="radio" name="notify_type" id="roles" value="roles" <?php if (isset($form) && $form->notify_type == 'roles') {
														echo 'checked';
													} ?> class="form-check-input">
													<label for="roles"><?php echo app_lang('re_staff_with_roles'); ?></label>
												</div>
												<div class="radio radio-primary radio-inline">
													<input type="radio" name="notify_type" id="assigned" value="assigned" <?php if (isset($form) && $form->notify_type == 'assigned') {
														echo 'checked';
													} ?> class="form-check-input">
													<label for="assigned"><?php echo app_lang('re_notify_assigned_user'); ?></label>
												</div>
												<div class="clearfix mtop15"></div>


												<div id="specific_staff_notify" class="<?php if (isset($form) && $form->notify_type != 'specific_staff') {
													echo 'hide';
												} ?>">
												<?php
												$selected = array();
												if (isset($form) && $form->notify_type == 'specific_staff') {
													$selected = explode(",", $form->notify_ids_staff);
												}
												?>
												<?php echo render_select1('notify_ids_staff[]', $members, array('id', array('first_name', 'last_name')), 're_leads_email_integration_notify_staff', $selected, array('multiple'=>true), [], '', '', false); ?>
											</div>


												<div id="role_notify" class="<?php if (isset($form) && $form->notify_type != 'roles' || !isset($form)) {
													echo 'hide';} ?>">
													<?php
													$selected = array();
													if (isset($form) && $form->notify_type == 'roles') {
														$selected = explode(",", $form->notify_ids_roles);
													}
													?>
													<?php echo render_select1('notify_ids_roles[]', $roles, array('id', array('title')), 're_leads_email_integration_notify_roles', $selected, array('multiple'=>true), [], '', '', false); ?>
												</div>

											</div>
										</div>
									</div>

								</div>
								<!-- form_infomation end -->

								<!-- form_builder start -->
								<div role="tabpanel" class="tab-pane" id="form_builder" aria-labelledby="form_builder-tab">
									<div id="form-build-wrap"></div>
									<div id='my_formBuilder'></div>
								</div>
								<!-- form_builder end -->
								<input type="hidden" name="form_data">


								<?php if (re_has_permission("recruitment_can_create")) {?>
									<div class="row">
										<div class="col-md-12">
											<div class="modal-footer">
												<a href="<?php echo get_uri('recruitment/recruitment_channel'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
												<button id="sm_btn2" type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
											</div>
										</div>
									</div>
								<?php }?>

							</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div><!-- /.modal-content -->

			</div>
		</div>
	</div>
</div>
</div>

</div>
<?php require 'plugins/Recruitment/assets/plugins/form-builder/_form_js_formatter_js.php';?>
</body>
</html>
