<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			
			<?php 
			echo form_hidden('_attachment_sale_id', $intv_sch->id);
			 ?>
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('interview_schedule_detail'); ?></h4>
				</div>
				<div class="modal-body clearfix row ml2 mr5 mt15">
					<ul class="nav nav-tabs pb15" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="tab_estimate-tab" data-bs-toggle="tab" data-bs-target="#tab_estimate" type="button" role="tab" aria-controls="tab_estimate" aria-selected="true"><?php echo app_lang('general_infor'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link " id="tab_activilog-tab" data-bs-toggle="tab" data-bs-target="#tab_activilog" type="button" role="tab" aria-controls="tab_activilog" aria-selected="false"><?php echo app_lang('rec_interview_comments'); ?></button>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="tab_estimate" aria-labelledby="tab_estimate-tab">
							<div class="col-md-12">
								<table class="table border table-striped margin-top-0">
									<tbody>
										<tr class="project-overview">
											<td class="bold" width="30%"><?php echo app_lang('interview_schedules_name'); ?></td>
											<td><?php echo html_entity_decode($intv_sch->is_name); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold" width="30%"><?php echo app_lang('rec_time'); ?></td>
											<?php
											$from_hours_format = '';
											$to_hours_format = '';

											$from_hours = format_to_datetime($intv_sch->from_hours, false);
											$from_hours = explode(" ", $from_hours);
											foreach ($from_hours as $key => $value) {
												if ($key != 0) {
													$from_hours_format .= $value;
												}
											}

											$to_hours = format_to_datetime($intv_sch->to_hours, false);
											$to_hours = explode(" ", $to_hours);
											foreach ($to_hours as $key => $value) {
												if ($key != 0) {
													$to_hours_format .= $value;
												}
											}

											?>
											<td><?php echo html_entity_decode($from_hours_format . ' - ' . $to_hours_format); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('interview_day'); ?></td>
											<td><?php echo format_to_date($intv_sch->interview_day, false); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('recruitment_campaign'); ?></td>
											<td><?php $cp = get_rec_campaign_hp($intv_sch->campaign);
											if (isset($cp)) {
												$_data = $cp->campaign_code . ' - ' . $cp->campaign_name;
											} else {
												$_data = '';
											}
											echo html_entity_decode($_data);?></td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('date_add'); ?></td>
											<td>
												<?php echo format_to_date($intv_sch->added_date, false); ?>
											</td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('interviewer'); ?></td>
											<td><?php
											$inv = explode(',', $intv_sch->interviewer);
											$ata = '';
											foreach ($inv as $iv) {
												$ata .= get_staff_image($iv, false);
											}
											echo html_entity_decode($ata);
										?></td>
									</tr>
								</tbody>
							</table>
						</div>

						<div class="col-md-12">
							<h4 class="isp-general-infor"><?php echo app_lang('list_of_candidates_participating') ?></h4>
							<hr class="isp-general-infor-hr" />
						</div>
						<div class="row">
							<?php foreach ($intv_sch->list_candidate as $cd) {?>
								<div class="col-md-6">
									<div class="col-md-12">
										<div class="row">
											<div class="thumbnail">
												<div class="caption" onclick="location.href='<?php echo site_url('recruitment/candidate/' . $cd['candidate']) ?>'">

													<h4 id="thumbnail-label"><?php echo candidate_profile_image($cd['candidate'], ['staff-profile-image-small mright5'], 'small') . ' #' . $cd['candidate_code'] . ' - ' . $cd['candidate_name'] . ' ' . $cd['last_name']; ?></h4>

													<p><?php echo app_lang('email') . ': ' . $cd['email']; ?></p>

													<div class="thumbnail-description smaller"><?php echo app_lang('phonenumber') . ': ' . $cd['phonenumber']; ?></div>

												</div>
											</div>
										</div>
									</div>
								</div>
							<?php }?>
						</div>

					</div>
					<div role="tabpanel" class="tab-pane " id="tab_activilog" aria-labelledby="tab_activilog-tab">
						<div class="panel_s no-shadow">
							<div class="activity-feed">
								<?php foreach($activity_log as $log){ ?>
									<div class="feed-item">
										<div class="date">
											<span class="text-has-action" data-toggle="tooltip" data-title="<?php echo format_to_datetime($log['date'], false); ?>"><?php echo format_to_datetime($log['date'], false); ?>
												
											</span>
											<?php if($log['staffid'] == get_staff_user_id1() || is_admin() || re_has_permission("recruitment_can_delete")){ ?>
												<a href="#" class="pull-right text-danger" onclick="delete_wh_activitylog(this,<?php echo html_entity_decode($log['id']); ?>);return false;"><i class="fa fa fa-times"></i></a>
											<?php } ?>
										</div>
										<div class="text">
											<?php if($log['staffid'] != 0){ ?>
												<a href="<?php echo site_url('profile/'.$log["staffid"]); ?>">
													<?php echo get_staff_image($log['staffid'], false);
													?>
												</a>
												<?php
											}
											$additional_data = '';
											if(!empty($log['additional_data'])){
												$additional_data = unserialize($log['additional_data']);
												echo ($log['staffid'] == 0) ? $log['description'].' '.$additional_data : $log['full_name'] .' - '.$log['description'].' '.$additional_data;
											} else {
												echo html_entity_decode($log['full_name']) . ' - ';
												echo html_entity_decode($log['description']);
											}
											?>
										</div>

									</div>
								<?php } ?>
							</div>
							<div class="col-md-12 general-form">
								<?php echo render_textarea1('wh_activity_textarea','','',array('placeholder'=>app_lang('rec_interview_comments')),array(),'mtop15'); ?>
								<div class="text-right">
									<button id="wh_enter_activity" class="btn btn-info text-white"><?php echo app_lang('submit'); ?></button>
								</div>
							</div>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div class="card">
			<div class="container-fluid">
				<div class="">
					<div class="btn-bottom-toolbar text-right mb20 mt20">
						<a href="<?php echo get_uri('recruitment/interview_schedule'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
					</div>
				</div>
				<div class="btn-bottom-pusher"></div>
			</div>
		</div>
	</div>
</div>
</div>

<?php require 'plugins/Recruitment/assets/js/interview_schedules/view_interview_schedule_js.php';?>

</body>
</html>

