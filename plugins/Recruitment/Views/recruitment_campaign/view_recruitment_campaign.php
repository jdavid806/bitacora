<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('recruitment_campaign_detail'); ?></h4>

					<?php 

					if($campaigns->cp_status == 1 ){
						echo  '<span class="badge bg-info large mt-4">'.app_lang('planning').'</span>';
					}elseif($campaigns->cp_status == 2 ){
						echo  '<span class="badge bg-info large mt-4">'.app_lang('overdue').'</span>';
					}elseif($campaigns->cp_status == 3 ){
						echo  '<span class="badge bg-success large mt-4">'.app_lang('in_progress').'</span>';
					}elseif($campaigns->cp_status == 4){
						echo  '<span class="badge bg-primary large mt-4">'.app_lang('finish').'</span>';
					}elseif($campaigns->cp_status == 5 ){
						echo  '<span class="badge bg-danger large mt-4">'.app_lang('cancel').'</span>';
					}

					?>

					<?php

					$manager = explode(',', $campaigns->cp_manager); 
					$curent_user = get_staff_user_id1();
					?>


					<div class="title-button-group">
						<?php
						if((in_array($curent_user, $manager) || $curent_user == $campaigns->cp_add_from || is_admin()) ){
							?>
							<div class="form-group mt-4">
								<select name="change_status" id="change_status" class="select2 validate-hidden " onchange="change_status_campaign(this,<?php echo html_entity_decode($campaigns->cp_id); ?>); return false;" data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('change_status_to'); ?>">

									<option value="1" <?php if ($campaigns->cp_status == 1) {echo 'selected';}?>><?php echo app_lang('planning'); ?></option>
									<option value="3" <?php if ($campaigns->cp_status == 3) {echo 'selected';}?>><?php echo app_lang('in_progress'); ?></option>
									<option value="4" <?php if ($campaigns->cp_status == 4) {echo 'selected';}?>><?php echo app_lang('finish'); ?></option>
									<option value="5" <?php if ($campaigns->cp_status == 5) {echo 'selected';}?>><?php echo app_lang('cancel'); ?></option>

								</select>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div class="row col-md-12">
						<h4 class="general-infor-color"><?php echo app_lang('general_infor') ?></h4>
						<hr class="general-infor-hr" />
					</div>
					<div class="col-md-6 ">
						<table class="table border table-striped margin-top-0">
							<tbody>
								<tr class="project-overview">
									<td class="bold" width="30%"><?php echo app_lang('campaign_code'); ?></td>
									<td><?php echo html_entity_decode($campaigns->campaign_code); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold" width="30%"><?php echo app_lang('campaign_name'); ?></td>
									<td><?php echo html_entity_decode($campaigns->campaign_name); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('department'); ?></td>
									<td><?php echo get_rec_dpm_name($campaigns->cp_department); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('form_of_work'); ?></td>
									<td>
										<?php 
										if(strlen($campaigns->cp_form_work) > 0){
											echo app_lang($campaigns->cp_form_work);
										}else{
											echo html_entity_decode($campaigns->cp_form_work);
										}
										?>
									</td>
								</tr>

								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('starting_salary'); ?></td>
									<td><?php echo to_currency($campaigns->cp_salary_from, get_base_currency()) . ' - ' . to_currency($campaigns->cp_salary_to, get_base_currency()); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('recruitment_channel_form'); ?></td>
									<?php 
									$r_form_name='';
									if(!is_array($rec_channel_form) ==1){
										if($rec_channel_form){
											$r_form_name = $rec_channel_form->r_form_name;
										}
									}
									?>
									<td><?php echo html_entity_decode($r_form_name); ?></td>
								</tr>

								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('reason_recruitment'); ?></td>
									<td><?php echo html_entity_decode($campaigns->cp_reason_recruitment); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('company_name'); ?></td>
									<td><?php echo html_entity_decode(get_rec_company_name($campaigns->company_id)); ?></td>
								</tr>

								<?php 
								$rec_channel = get_rec_channel_form_key($campaigns->rec_channel_form_id);
								?>

								<?php if(isset($rec_channel) && $rec_channel !='') {?>
									<tr class="project-overview">
										<td class="bold"><?php echo 'Form url'; ?></td>
										<td> 
											<span class="label label-default">
												<a href="<?php echo site_url('forms/wtl/'.$campaigns->cp_id.'/'.$rec_channel); ?>" target="_blank"><?php echo site_url('recruitment/forms'); ?></a>
											</span>
										</tr>
									<?php } ?>

								</tbody>
							</table>

						</div>

						<div class="col-md-6 padding-left-right-0">
							<table class="table table-striped margin-top-0">
								<tbody>
									<tr class="project-overview">
										<td class="bold" width="40%"><?php echo app_lang('position'); ?></td>
										<td><?php echo get_rec_position_name($campaigns->cp_position); ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('amount_recruiment'); ?></td>
										<td><?php echo html_entity_decode($campaigns->cp_amount_recruiment); ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('workplace'); ?></td>
										<td><?php echo html_entity_decode($campaigns->cp_workplace); ?></td>
									</tr>

									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('recruiment_duration'); ?></td>
										<td><?php echo format_to_date($campaigns->cp_from_date, false) . ' - ' . format_to_date($campaigns->cp_to_date, false) ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('status'); ?></td>
										<td class="padding-5">
											<?php if ($campaigns->cp_status == 1) {
												$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-planning-style"> ' . app_lang('planning') . ' </span>';
											} elseif ($campaigns->cp_status == 3) {
												$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-progress-style"> ' . app_lang('in_progress') . ' </span>';
											} elseif ($campaigns->cp_status == 4) {
												$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-finish-style""> ' . app_lang('finish') . ' </span>';
											} elseif ($campaigns->cp_status == 5) {
												$_data = ' <span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-cancel-style""> ' . app_lang('cancel') . ' </span>';
											}

											if (($campaigns->cp_status == 1 || $campaigns->cp_status == 2) && date('Y-m-d') > $campaigns->cp_to_date) {
												$_data = '<span class="label label inline-block project-status-' . $campaigns->cp_status . ' campaign-overdue-style"> ' . app_lang('overdue') . ' </span>';
											}
											echo html_entity_decode($_data);
											?>
										</td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('add_from'); ?></td>
										<td><a href="<?php echo get_uri('team_members/view/'.$campaigns->cp_add_from.'/general'); ?>"><?php echo get_staff_image($campaigns->cp_add_from, true); ?></a></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('follower'); ?></td>
										<td>
											<?php $follows = explode(',', $campaigns->cp_follower);
											foreach ($follows as $f) {
												if(is_numeric($f)){
													echo get_staff_image($f, false);
													?>
													
												<?php } }?>
											</td>
										</tr>

										<tr class="project-overview">
											<td class="bold"><?php echo app_lang('manager'); ?></td>
											<td>
												<?php
												foreach ($manager as $f) {
													if(is_numeric($f)){
														echo get_staff_image($f, false);

														?>
														
													<?php } }?>
												</td>
											</tr>

										</tbody>
									</table>
								</div>
								<div class="col-md-12 padding-left-10">
									<p class="bold text-muted"><?php echo app_lang('job_description') . ': ' . $campaigns->cp_job_description; ?></p>

								</div>
								<div class="row col-md-12">
									<h4 class="candidate_request-color"><?php echo app_lang('candidate_request') ?></h4>
									<hr class="candidate_request-hr"/>
								</div>
								<div class="col-md-6">
									<table class="table border table-striped margin-top-0">
										<tbody>
											<tr class="project-overview">
												<td class="bold" width="30%"><?php echo app_lang('gender'); ?></td>
												<td><?php echo app_lang($campaigns->cp_gender); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('height'); ?></td>
												<td><?php echo html_entity_decode($campaigns->cp_height); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('literacy'); ?></td>
												<td><?php echo app_lang($campaigns->cp_literacy); ?></td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="col-md-6 padding-left-right-0">
									<table class="table table-striped margin-top-0">
										<tbody>
											<tr class="project-overview">
												<td class="bold" width="30%"><?php echo app_lang('ages'); ?></td>
												<td><?php echo html_entity_decode($campaigns->cp_ages_from . ' - ' . $campaigns->cp_ages_to); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('weight'); ?></td>
												<td><?php echo html_entity_decode($campaigns->cp_weight); ?></td>
											</tr>
											<tr class="project-overview">
												<td class="bold"><?php echo app_lang('experience'); ?></td>
												<td><?php echo app_lang($campaigns->cp_experience); ?></td>
											</tr>
										</tbody>
									</table>
								</div>


								<div class="col-md-12">
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

						<div class="card">
							<div class="container-fluid">
								<div class="">
									<div class="btn-bottom-toolbar text-right mb20 mt20">
										<a href="<?php echo get_uri('recruitment/recruitment_campaign'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
									</div>
								</div>
								<div class="btn-bottom-pusher"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php require 'plugins/Recruitment/assets/js/recruitment_campaigns/recruitment_campaign_detail_js.php';?>

		</body>
		</html>

