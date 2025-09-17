<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">

					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo '#' . $candidate->candidate_code . ' - ' . $candidate->candidate_name . ' ' . $candidate->last_name; ?> <?php echo get_status_candidate($candidate->status); ?></h4>

					<?php if(re_has_permission("recruitment_can_create") || re_has_permission("recruitment_can_edit")){ ?>
						<div class="title-button-group">

							<a href="#" class="btn btn-warning pull-left display-block text-white" onclick="open_rating_dialog(); return false;"><i class="fa fa-star"></i><?php echo ' ' . app_lang('rate_candidate'); ?></a>
							<a href="#" onclick="send_mail_candidate(); return false;" class="btn btn-info pull-left display-block text-white" ><i class="fa fa-envelope"></i><?php echo app_lang('send_mail'); ?></a>

							<a href="Javascript:void(0);" id="toggle_popup_approval" class="btn btn-success pull-left display-block text-white"><i class="fa fa-user-md"></i><?php echo ' ' . app_lang('rec_care') . ' '; ?><i class="fa fa-caret-down"></i></a>
							<ul id="popup_approval" class="dropdown-menu dropdown-menu-right">
								<div class="col-md-12">
									<a href="#" onclick="interview(); return false;" class="btn btn-info pull-right display-block mright5 interview-background text-white"><i class="fa fa-microphone"></i><?php echo ' ' . app_lang('interview'); ?></a>
									<a href="#" onclick="test(); return false;" class="btn btn-info pull-right display-block mright5 test-background text-white"><i class="fa fa-forward"></i><?php echo ' ' . app_lang('test'); ?></a>
									<a href="#" onclick="call(); return false;" class="btn btn-info pull-right display-block mright5 call-background text-white"><i class="fa fa-phone"></i><?php echo ' ' . app_lang('call'); ?></a>
									<a href="#" onclick="sendmail(); return false;" class="btn btn-info pull-right display-block mright5 send_mail-background text-white"><i class="fa fa-envelope"></i><?php echo ' ' . app_lang('send_mail'); ?></a>

								</div>
							</ul>

							<?php if(re_has_permission("recruitment_can_edit")){ ?>

								<a href="<?php echo html_entity_decode(get_uri('recruitment/candidates/' . $candidate->id )); ?>" class="btn btn-default pull-right mleft5 mbot15" ><i class="fa fa-edit"></i><?php echo ' ' . app_lang('edit'); ?></a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>

				<div class="row modal-body clearfix">
					<div class="col-md-3">
						<select name="change_status" id="change_status" onchange="change_status_candidate(this,<?php echo html_entity_decode($candidate->id); ?>); return false;" class="select2 validate-hidden" data-width="100%" placeholder="<?php echo app_lang('change_status_to'); ?>">
							<option value=""></option>
							<option value="1" class="<?php if ($candidate->status == 1) {echo 'hide';}?>"><?php echo app_lang('application'); ?></option>
							<option value="2" class="<?php if ($candidate->status == 2) {echo 'hide';}?>"><?php echo app_lang('potential'); ?></option>
							<option value="3" class="<?php if ($candidate->status == 3) {echo 'hide';}?>"><?php echo app_lang('interview'); ?></option>
							<option value="4" class="<?php if ($candidate->status == 4) {echo 'hide';}?>"><?php echo app_lang('won_interview'); ?></option>
							<option value="5" class="<?php if ($candidate->status == 5) {echo 'hide';}?>"><?php echo app_lang('send_offer'); ?></option>
							<option value="6" class="<?php if ($candidate->status == 6) {echo 'hide';}?>"><?php echo app_lang('elect'); ?></option>
							<option value="7" class="<?php if ($candidate->status == 7) {echo 'hide';}?>"><?php echo app_lang('non_elect'); ?></option>
							<option value="8" class="<?php if ($candidate->status == 8) {echo 'hide';}?>"><?php echo app_lang('unanswer'); ?></option>
							<option value="9" class="<?php if ($candidate->status == 9) {echo 'hide';}?>"><?php echo app_lang('transferred'); ?></option>
							<option value="10" class="<?php if ($candidate->status == 10) {echo 'hide';}?>"><?php echo app_lang('freedom'); ?></option>
						</select>
					</div>
				</div>

				<div class="col-md-12 modal-body clearfix">
					<ul class="nav nav-tabs pb15" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php if($tab == 'detail'){echo "active"; } ?>" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab" aria-controls="detail" aria-selected="true"><?php echo app_lang('detail'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php if($tab == 'applied_job'){echo "active"; } ?>" id="applied_job-tab" data-bs-toggle="tab" data-bs-target="#applied_job" type="button" role="tab" aria-controls="applied_job" aria-selected="false"><?php echo app_lang('re_applied_jobs'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php if($tab == 'history_recruitment'){echo "active"; } ?>" id="history_recruitment-tab" data-bs-toggle="tab" data-bs-target="#history_recruitment" type="button" role="tab" aria-controls="history_recruitment" aria-selected="false"><?php echo app_lang('history_recruitment'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php if($tab == 'capacity_profile'){echo "active"; } ?>" id="capacity_profile-tab" data-bs-toggle="tab" data-bs-target="#capacity_profile" type="button" role="tab" aria-controls="capacity_profile" aria-selected="false"><?php echo app_lang('capacity_profile'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link <?php if($tab == 'attachment'){echo "active"; } ?>" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachment" type="button" role="tab" aria-controls="attachment" aria-selected="false"><?php echo app_lang('attachment'); ?></button>
						</li>

					</ul>


					<div class="tab-content">
						<div role="tabpanel" class="tab-pane <?php if($tab == 'detail'){echo "active"; } ?>" id="detail" aria-labelledby="detail-tab">

							<p class="bold margin-top-15 general-infor-color"><?php echo app_lang('general_infor'); ?></p>
							<hr class="margin-top-10 general-infor-hr"/>

							<div class="row">
								<div class="col-md-2 padding-left-right-0">
									<div class="container">
										<div class="picture-container pull-left">
											<div class="picture pull-left">
												<?php 
												$thumbnail = get_file_uri('plugins/Recruitment/Uploads/none_avatar.jpg');
												if (isset($candidate->avar)){
													$thumbnail = get_file_uri('plugins/Recruitment/Uploads/candidate/avartar/'.$candidate->id . '/' . $candidate->avar->file_name);
												}
												?>

												<img class="width-height-160" src="<?php echo html_entity_decode($thumbnail); ?>" class="picture-src" id="wizardPicturePreview" title="">

											</div>
										</div>
									</div>
								</div>
								<div class="col-md-5 padding-left-right-0">
									<table class="table border table-striped margin-top-0">
										<tbody>
											<tr class="project-overview">
												<td class="bold" width="30%"><?php echo app_lang('full_name'); ?></td>
												<td><?php echo html_entity_decode($candidate->candidate_name . ' ' . $candidate->last_name); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('gender'); ?></td>
												<td><?php echo app_lang($candidate->gender); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('nation'); ?></td>
												<td><?php echo html_entity_decode($candidate->nation); ?></td>
											</tr>

											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('marital_status'); ?></td>
												<td><?php echo app_lang($candidate->marital_status); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('height'); ?></td>
												<td><?php echo html_entity_decode($candidate->height); ?></td>
											</tr>

										</tbody>
									</table>
								</div>
								<div class="col-md-5 padding-left-right-0">
									<table class="table border table-striped margin-top-0">
										<tbody>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('candidate_code'); ?></td>
												<td><?php echo html_entity_decode($candidate->candidate_code); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold" width="30%"><?php echo app_lang('birthday'); ?></td>
												<td><?php echo format_to_date($candidate->birthday, false); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('nationality'); ?></td>
												<td><?php echo html_entity_decode($candidate->nationality); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('religion'); ?></td>
												<td><?php echo html_entity_decode($candidate->religion); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('weight'); ?></td>
												<td><?php echo html_entity_decode($candidate->weight); ?></td>
											</tr>

										</tbody>
									</table>
								</div>
							</div>


							<p class="bold other_infor-style"><?php echo app_lang('other_infor'); ?></p>
							<hr class="other_infor-hr" />

							<div class="row">
								<div class="col-md-6 ">
									<table class="table border table-striped margin-top-0">
										<tbody>
											<tr class="project-overview">
												<td class="bold" width="30%"><?php echo app_lang('campaign'); ?></td>
												<td><?php
												if(is_numeric($candidate->rec_campaign) && $candidate->rec_campaign != 0){
													$cp = get_rec_campaign_hp($candidate->rec_campaign);
													$datas = '';
													if (isset($cp)) {
														$datas = '<a href="' . get_uri('recruitment/recruitment_campaign/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
													}
													echo html_entity_decode($datas);
												}
												?>
											</td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('submission_date'); ?></td>
											<td><?php echo format_to_date($candidate->date_add, false); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('identification'); ?></td>
											<td><?php echo html_entity_decode($candidate->identification); ?></td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('birthplace'); ?></td>
											<td><?php echo html_entity_decode($candidate->birthplace); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('resident'); ?></td>
											<td><?php echo html_entity_decode($candidate->resident); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('phonenumber'); ?></td>
											<td><?php echo html_entity_decode($candidate->phonenumber); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('alternate_contact_number'); ?></td>
											<td><?php echo html_entity_decode($candidate->alternate_contact_number); ?></td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('skype'); ?></td>
											<td><a href="<?php echo html_entity_decode($candidate->skype); ?>"><?php echo html_entity_decode($candidate->skype); ?></a></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('skill_name'); ?></td>
											<td><?php echo html_entity_decode($skill_name); ?></td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('introduce_yourself'); ?></td>
											<td><?php echo html_entity_decode($candidate->introduce_yourself); ?></td>
										</tr>
									</tbody>
								</table>
							</div>

							<div class="col-md-6 padding-left-right-0">
								<table class="table border table-striped margin-top-0">
									<tbody>
										<tr class="project-overview">
											<td class="bold" width="30%"><?php echo app_lang('status'); ?></td>
											<td><?php echo get_status_candidate($candidate->status); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('desired_salary'); ?></td>
											<td><?php echo to_currency($candidate->desired_salary, get_base_currency()); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('days_for_identity'); ?></td>
											<td><?php echo format_to_date($candidate->days_for_identity, false); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('home_town'); ?></td>
											<td><?php echo html_entity_decode($candidate->home_town); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('current_accommodation'); ?></td>
											<td><?php echo html_entity_decode($candidate->current_accommodation); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('email'); ?></td>
											<td><a href="mailto:<?php echo html_entity_decode($candidate->email); ?>"><?php echo html_entity_decode($candidate->email); ?></a></td>

										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('facebook'); ?></td>
											<td><a href="<?php echo html_entity_decode($candidate->facebook); ?>"><?php echo html_entity_decode($candidate->facebook); ?></a></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('linkedin'); ?></td>
											<td><a href="<?php echo html_entity_decode($candidate->linkedin); ?>"><?php echo html_entity_decode($candidate->linkedin); ?></a></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('experience'); ?></td>
											<td><?php echo html_entity_decode(app_lang($candidate->year_experience)); ?></td>
										</tr>
										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('interests'); ?></td>
											<td><?php echo html_entity_decode($candidate->interests); ?></td>
										</tr>


									</tbody>
								</table>
							</div>
						</div>

						<div class="row ">
							<p class="bold other_infor-style"><?php echo app_lang('work_experience'); ?></p>
							<hr class="other_infor-hr" />

							<?php if (count($candidate->work_experience) > 0) {?>
								<div class="col-md-12">
									<table class="table dt-table margin-top-0">
										<thead>
											<th><?php echo app_lang('from_date'); ?></th>
											<th><?php echo app_lang('to_date'); ?></th>
											<th><?php echo app_lang('company'); ?></th>
											<th><?php echo app_lang('position'); ?></th>
											<th><?php echo app_lang('contact_person'); ?></th>
											<th><?php echo app_lang('salary'); ?></th>
											<th><?php echo app_lang('reason_quitwork'); ?></th>
											<th><?php echo app_lang('job_description'); ?></th>
										</thead>
										<tbody>

											<?php foreach ($candidate->work_experience as $we) {?>
												<tr class="project-overview">
													<td><?php echo format_to_date($we['from_date'], false); ?></td>
													<td><?php echo format_to_date($we['to_date'], false); ?></td>
													<td><?php echo html_entity_decode($we['company']); ?></td>
													<td><?php echo html_entity_decode($we['position']); ?></td>
													<td><?php echo html_entity_decode($we['contact_person']); ?></td>
													<td><?php echo html_entity_decode(to_currency((float)$we['salary'], get_base_currency())); ?></td>
													<td><?php echo html_entity_decode($we['reason_quitwork']); ?></td>
													<td><?php echo html_entity_decode($we['job_description']); ?></td>
												</tr>
											<?php }?>
										</tbody>
									</table>
								</div>
							<?php } else {?>
								<p><?php echo app_lang('no_result'); ?></p>
							<?php }?>



							<p class="bold other_infor-style"><?php echo app_lang('literacy'); ?></p>
							<hr class="other_infor-hr" />
							<?php if (count($candidate->literacy) > 0) {?>
								<div class="col-md-12">

									<table class="table dt-table margin-top-0">
										<thead>
											<th><?php echo app_lang('from_date'); ?></th>
											<th><?php echo app_lang('to_date'); ?></th>
											<th><?php echo app_lang('diploma'); ?></th>
											<th><?php echo app_lang('training_places'); ?></th>
											<th><?php echo app_lang('specialized'); ?></th>
											<th><?php echo app_lang('training_form'); ?></th>

										</thead>
										<tbody>
											<?php foreach ($candidate->literacy as $we) {?>
												<tr class="project-overview">
													<td><?php echo format_to_date($we['literacy_from_date'], false); ?></td>
													<td><?php echo format_to_date($we['literacy_to_date'], false); ?></td>
													<td><?php echo html_entity_decode($we['diploma']); ?></td>
													<td><?php echo html_entity_decode($we['training_places']); ?></td>
													<td><?php echo html_entity_decode($we['specialized']); ?></td>
													<td><?php echo html_entity_decode($we['training_form']); ?></td>

												</tr>
											<?php }?>
										</tbody>
									</table>
								</div>
							<?php } else {?>
								<p><?php echo app_lang('no_result'); ?></p>
							<?php }?>
							<p class="bold other_infor-style"><?php echo app_lang('family_infor'); ?></p>
							<hr class="other_infor-hr" />
							<?php if (count($candidate->family_infor) > 0) {?>
								<div class="col-md-12">

									<table class="table dt-table margin-top-0">
										<thead>
											<th><?php echo app_lang('relationship'); ?></th>
											<th><?php echo app_lang('name'); ?></th>
											<th><?php echo app_lang('birthday'); ?></th>
											<th><?php echo app_lang('job'); ?></th>
											<th><?php echo app_lang('address'); ?></th>
											<th><?php echo app_lang('phone'); ?></th>

										</thead>
										<tbody>
											<?php foreach ($candidate->family_infor as $we) {?>
												<tr class="project-overview">
													<td><?php echo html_entity_decode($we['relationship']); ?></td>
													<td><?php echo html_entity_decode($we['name']); ?></td>
													<td><?php echo format_to_date($we['fi_birthday'], false); ?></td>
													<td><?php echo html_entity_decode($we['job']); ?></td>
													<td><?php echo html_entity_decode($we['address']); ?></td>
													<td><?php echo html_entity_decode($we['phone']); ?></td>

												</tr>
											<?php }?>
										</tbody>
									</table>
								</div>
							<?php } else {?>
								<p><?php echo app_lang('no_result'); ?></p>
							<?php }?>
						</div>

					</div>

					<div role="tabpanel" class="tab-pane <?php if($tab == 'applied_job'){echo "active"; } ?>" id="applied_job" aria-labelledby="applied_job-tab">
						<?php if (count($candidate->applied_jobs) > 0) {
							?>
							<table class="table dt-table margin-top-0 table-applied_job">
								<thead>
									<th><?php echo app_lang('campaign'); ?></th>
									<th><?php echo app_lang('date_applied'); ?></th>
									<th><?php echo app_lang('status'); ?></th>
									<th></th>
								</thead>
								<tbody>
									<?php foreach ($candidate->applied_jobs as $applied_job) {?>
										<tr class="project-overview">
											<td><?php
											$cp = get_rec_campaign_hp($applied_job['campaign_id']);
											$datas = '';
											if (isset($cp)) {
												$datas = '<a href="' . get_uri('recruitment/recruitment_campaign/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
											}
											echo html_entity_decode($datas);
										?></td>
										<td><?php echo format_to_date($applied_job['date_created'], false); ?></td>
										<td>
											<?php 
											echo re_render_status_html($applied_job['id'], 'applied_job', $applied_job['status']);
											?>
										</td>
										<td>
											<?php if($applied_job['activate'] == '0'){ ?>
												<?php echo app_lang('rec_candidate_has_abandoned_apply_for_this_position'); ?>
											<?php }else{ ?>
											<?php } ?>
										</td>

									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php }else{ ?>
						<p class="text-center"><?php echo app_lang('no_result'); ?></p>
					<?php } ?>
				</div>

				<div role="tabpanel" class="tab-pane <?php if($tab == 'history_recruitment'){echo "active"; } ?>" id="history_recruitment" aria-labelledby="history_recruitment-tab">
					<p class="bold other_infor-style"><?php echo app_lang('campaign_has_joined'); ?></p>
					<hr class="other_infor-hr" />
					<?php if ($candidate->rec_campaign > 0) {
						?>
						<table class="table dt-table margin-top-0">
							<thead>
								<th><?php echo app_lang('campaign'); ?></th>
								<th><?php echo app_lang('status'); ?></th>
								<th><?php echo app_lang('submission_date'); ?></th>
								<th><?php echo app_lang('desired_salary'); ?></th>
							</thead>
							<tbody>

								<tr class="project-overview">
									<td><?php
									$cp = get_rec_campaign_hp($candidate->rec_campaign);
									$datas = '';
									if (isset($cp)) {
										$datas = '<a href="' . get_uri('recruitment/recruitment_campaign/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
									}
									echo html_entity_decode($datas);
								?></td>
								<td><?php echo get_status_candidate($candidate->status); ?></td>
								<td><?php echo format_to_date($candidate->date_add, false); ?></td>
								<td><?php echo to_currency($candidate->desired_salary, get_base_currency()); ?></td>
							</tr>
						</tbody>
					</table>
				<?php } else {?>
					<p><?php echo app_lang('no_result'); ?></p>
				<?php }?>

				<p class="bold other_infor-style"><?php echo app_lang('interview_schedule'); ?></p>
				<hr class="other_infor-hr" />
				<?php if (count($list_interview) > 0) {
					?>
					<table class="table dt-table margin-top-0">
						<thead>
							<th><?php echo app_lang('add_from'); ?></th>
							<th><?php echo app_lang('interview_schedules_name'); ?></th>
							<th><?php echo app_lang('rec_time'); ?></th>
							<th><?php echo app_lang('interview_day'); ?></th>
							<th><?php echo app_lang('recruitment_campaign'); ?></th>
							<th><?php echo app_lang('interviewer'); ?></th>
							<th><?php echo app_lang('date_add'); ?></th>
							<th><?php echo app_lang('status'); ?></th>
						</thead>
						<tbody>
							<?php foreach ($list_interview as $li) {
								?>
								<tr>
									<td>
										<?php
										$_data = get_staff_image($li['added_from'], true);
										echo html_entity_decode($_data);
										?>
									</td>
									<td><?php echo html_entity_decode($li['is_name']) ?></td>
									<td><?php echo html_entity_decode($li['from_time'] . ' - ' . $li['to_time']); ?></td>
									<td><?php echo format_to_date($li['interview_day'], false); ?></td>
									<td><?php
									$cp = get_rec_campaign_hp($li['campaign']);
									if ($li['campaign'] != '' && $li['campaign'] != 0) {
										if (isset($cp)) {
											$_data = $cp->campaign_code . ' - ' . $cp->campaign_name;
										} else {
											$_data = '';
										}
									} else {
										$_data = '';

									}

									echo html_entity_decode($_data);
									?>

								</td>
								<td>
									<?php
									$inv = explode(',', $li['interviewer']);
									$ata = '';
									foreach ($inv as $iv) {
										$ata .= get_staff_image($iv, false);
									}
									echo html_entity_decode($ata);
									?>
								</td>
								<td><?php echo format_to_date($li['added_date'], false); ?></td>
								<td>
									<?php 
									echo re_render_status_html($li['in_id'], 'interview', $li['status']);
									?>
								</td>
							</tr>
						<?php }?>
					</tbody>
				</table>
			<?php }else{ ?>
				<p><?php echo app_lang('no_result'); ?></p>
			<?php } ?>

			<p class="bold other_infor-style"><?php echo app_lang('care_history'); ?></p>
			<hr class="other_infor-hr" />
			<?php if (count($candidate->care) > 0) {
				?>
				<table class="table dt-table margin-top-0" >
					<thead>
						<th><?php echo app_lang('type'); ?></th>
						<th><?php echo app_lang('caregiver'); ?></th>
						<th><?php echo app_lang('rec_time'); ?></th>
						<th><?php echo app_lang('result'); ?></th>
						<th><?php echo app_lang('description'); ?></th>
					</thead>
					<tbody>

						<?php foreach ($candidate->care as $care) {
							?>
							<tr class="project-overview">
								<td><?php echo app_lang($care['type']); ?></td>
								<td>
									<?php
									echo get_staff_image($care['add_from']);
									?>
								</td>
								<td><?php echo format_to_date($care['care_time'], false); ?></td>
								<td><?php echo html_entity_decode($care['care_result']); ?></td>
								<td><?php echo html_entity_decode($care['description']); ?></td>
							</tr>
						<?php }?>
					</tbody>
				</table>
			<?php } else {?>
				<p><?php echo app_lang('no_result'); ?></p>
			<?php }?>

		</div>

		<div role="tabpanel" class="tab-pane <?php if($tab == 'capacity_profile'){echo "active"; } ?>" id="capacity_profile" aria-labelledby="capacity_profile-tab">

			<div class="row col-md-12">
				<p class="bold other_infor-style"><?php echo app_lang('candidate_evaluation'); ?></p>
				<hr class="other_infor-hr" />
			</div>

			<div class="col-md-6">
				<?php if (count($cd_evaluation) > 0) {
					?>
					<table class="table border table-striped margin-top-0">
						<tbody>
							<tr class="project-overview">
								<td class="bold" width="30%"><?php echo app_lang('assessor'); ?></td>
								<td><?php
								echo get_staff_image($assessor, true);
								
							?></td>
						</tr>
						<tr class="project-overview">
							<td class="bold"><?php echo app_lang('evaluation_date'); ?></td>
							<td><?php echo format_to_date($evaluation_date, false); ?></td>
						</tr>
						<tr class="project-overview">
							<td class="bold"><?php echo app_lang('avg_score'); ?></td>
							<td><?php echo html_entity_decode($avg_score); ?></td>
						</tr>
						<tr class="project-overview">
							<td class="bold"><?php echo app_lang('feedback'); ?></td>
							<td><?php echo html_entity_decode($feedback); ?></td>
						</tr>

					</tbody>
				</table>
			<?php } else {?>
				<p class="" ><?php echo app_lang('none_evaluation_for_cd'); ?></p>
			<?php }?>
		</div>
		<div class="col-md-6">

		</div>
		<div class="row col-md-12">
			<p class="bold other_infor-style"><?php echo app_lang('result'); ?></p>
			<hr class="other_infor-hr" />
			<?php if (count($data_group) > 0) { ?>
				<table class="table dt-table margin-top-0">
					<thead>
						<th><?php echo app_lang('criteria_name'); ?></th>
						<th><?php echo app_lang('proportion'); ?></th>
						<th><?php echo app_lang('rec_score'); ?></th>
						<th><?php echo app_lang('result'); ?></th>

					</thead>
					<tbody>
						<?php 
						$count_gr = 1;
						foreach ($data_group as $key => $gr) {
							?>
							<tr>
								<td class="bold text-danger"><?php echo html_entity_decode($count_gr . '. ' . get_criteria_name($key)); ?></td>
								<td class="bold text-danger"><?php echo html_entity_decode($gr['toltal_percent'] . '%'); ?></td>
								<td class="bold text-danger"></td>
								<td class="bold text-danger"><?php echo html_entity_decode($gr['result']); ?></td>
							</tr>
							<?php $count_cr = 1;foreach ($cd_evaluation as $cd) {
								if ($cd['group_criteria'] == $key) {
									?>
									<tr>
										<td><?php echo html_entity_decode($count_gr . '.' . $count_cr . '. ' . get_criteria_name($cd['criteria'])); ?></td>
										<td><?php echo html_entity_decode($cd['percent'] . '%'); ?></td>
										<td>
											<?php echo app_lang($cd['rate_score'].'_point'); ?>
										</td>
										<td><?php echo html_entity_decode(($cd['rate_score'] * $cd['percent']) / 100); ?></td>
									</tr>
									<?php $count_cr++;}}?>
									<?php $count_gr++;}?>
								</tbody>
							</table>
						<?php }else{ ?>
							<p><?php echo app_lang('no_result'); ?></p>

						<?php } ?>

					</div>

				</div>

				<div role="tabpanel" class="tab-pane <?php if($tab == 'attachment'){echo "active"; } ?>" id="attachment" aria-labelledby="attachment-tab">
					<div class="col-md-12" id="candidate_pv_file">
						<div class="row">
							<div id="contract_attachments" class="mtop30 ">
								<?php if(isset($candidate->file)){ ?>

									<?php
									$data = '<div class="row" id="attachment_file">';
									foreach($candidate->file as $attachment) {
										$href_url = base_url('plugins/Recruitment/Uploads/candidate/files/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
										if(!empty($attachment['external'])){
											$href_url = $attachment['external_link'];
										}
										$data .= '<div class="display-block contract-attachment-wrapper" data-attachment-id="' . $attachment['id'] . '">';
										$data .= '<div class="row">';
										$data .= '<div class="col-md-1 mr-5">';
										$data .= modal_anchor(get_uri("recruitment/candidate_file/".$attachment['id']."/".$attachment['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $attachment['file_name'], "data-post-id" => $attachment['id']));

										$data .= '</a>';
										$data .= '</div>';
										$data .= '<div class=col-md-9>';
										$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
										$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
										$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
										$data .= '</div>';
										$data .= '<div class="col-md-2 text-right">';
										if(is_admin() || re_has_permission("recruitment_can_delete")){
											$data .= '<a href="#" class="text-danger" onclick="delete_candidate_attachment('.$attachment['id'].'); return false;"><span data-feather="x-circle" class="icon-16" ></span></a>';
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
	</div>
</div>
</div>
</div>
<div class="modal fade" id="care_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(site_url('recruitment/care_candidate'), array('id' => 'care_candidate-form',"class" => "general-form")); ?>
		<div class="modal-content width-100">
			<div class="modal-header">
				<h4 class="modal-title">
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6">
						<?php $attr = [];
						$attr = ['disabled' => "true"];
						echo render_input1('candidate', 'candidate', $candidate->candidate_code . ' - ' . $candidate->candidate_name . ' ' . $candidate->last_name, 'text', $attr);

						echo form_hidden('candidate', $candidate->id);
						?>
					</div>
					<div class="col-md-6">
						<?php echo render_date_input1('care_time', 'care_time', '', [], [], '', '', true) ?>
					</div>
					<div class="col-md-12" id="care_rs">

					</div>
					<div class="col-md-12">
						<?php echo render_textarea1('description', 'description') ?>
					</div>
					<div id="type_care">

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
				<button id="sm_btn" type="submit" onclick="submit_care_candidate(); return false;" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
			</div>
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="mail_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(site_url('recruitment/send_mail_candidate'), array('id' => 'mail_candidate-form',"class" => "general-form")); ?>
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
						<?php $attr = [];
						$attr = ['disabled' => "true"];
						echo render_input1('candidate', 'candidate', $candidate->candidate_code . ' - ' . $candidate->candidate_name . ' ' . $candidate->last_name, 'text', $attr);

						echo form_hidden('candidate', $candidate->id);
						?>
					</div>
					<div class="col-md-12">
						<?php echo render_input1('email', 'email', $candidate->email, '', [], [], '', '', true); ?>
					</div>

					<div class="col-md-12">
						<?php echo render_input1('subject', 'subject', '', '', [] , [], '', '', true); ?>
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
		</div><!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="candidate_rating" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content ">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="add-title"><?php echo app_lang('rate_candidate'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<?php echo form_open(site_url('recruitment/rating_candidate'), array('id' => 'rating-modal',"class" => "general-form")); ?>
			<?php echo form_hidden('candidate', $candidate->id); ?>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<?php if ($evaluation != '') {
							$count_gr = 1;
							foreach ($evaluation['groups'] as $gr) {

								?>

								<h5 class="bold"><?php echo html_entity_decode($count_gr . '. ' . $gr['criteria_title']); ?></h5>
								<hr class="criteria_title-hr" />

								<?php
								$count_cr = 1;
								$rating_data = [];
								$rating_data[] = [
									'name' => 1,
									'label' => app_lang('1_point'),
								];
								$rating_data[] = [
									'name' => 2,
									'label' => app_lang('2_point'),
								];
								$rating_data[] = [
									'name' => 3,
									'label' => app_lang('3_point'),
								];
								$rating_data[] = [
									'name' => 4,
									'label' => app_lang('4_point'),
								];
								$rating_data[] = [
									'name' => 5,
									'label' => app_lang('5_point'),
								];
								

								foreach ($evaluation['criteria'] as $cr) {
									if ($cr['group_cr'] == $gr['id']) {

										?>

										<div class="star-rating">


											&nbsp;&nbsp;&nbsp;<?php echo html_entity_decode($count_gr . '.' . $count_cr . '. ' . $cr['criteria_title'] . ' (' . $cr['percent'] . '%)'); ?>

											<?php echo render_select1('rating['.$cr['evaluation_criteria'].']', $rating_data, ['name', 'label'],'', '', [], [], '', 'rating-value', false ); ?>

											<input type="hidden" name="percent[<?php echo html_entity_decode($cr['evaluation_criteria']); ?>]" value="<?php echo html_entity_decode($cr['percent']); ?>">
											<input type="hidden" name="group[<?php echo html_entity_decode($cr['evaluation_criteria']); ?>]" value="<?php echo html_entity_decode($gr['id']); ?>">

										</div> 

										<?php $count_cr++;}}
										$count_gr++;}?>
										<?php echo render_textarea1('feedback', 'feedback'); ?>
									<?php } else {echo '<p class="bold text-danger">' . app_lang('none_evaluetion_form') . '</p>';}?>


								</div>

							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

							<button group="submit" onclick="submit_rating_candidate(); return false;" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>

			<?php require 'plugins/Recruitment/assets/js/candidate_profiles/candidate_detail_js.php';?>

		</body>
		</html>