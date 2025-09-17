<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('recruitment_plan_detail'); ?></h4>

					<?php 
					if($proposals->status == 1 ){
						echo '<span class="badge bg-info large mt-4">'.app_lang('_proposal').'</span>';
					}elseif($proposals->status == 2 ){
						echo '<span class="badge bg-success large mt-4">'.app_lang('approved').'</span>';
					}elseif($proposals->status == 3 ){
						echo '<span class="badge bg-primary large mt-4">'.app_lang('made_finish').'</span>';
					}elseif($proposals->status == 4 ){
						echo '<span class="badge bg-danger large mt-4">'.app_lang('reject').'</span>';
					}
					?>

					<div class="title-button-group">
						<?php if((get_staff_user_id1() == $proposals->approver || is_admin()) && $proposals->status == 1){ ?>
							<a href="<?php echo get_uri('recruitment/approve_reject_proposal/'.'reject'.'/'.$proposals->id); ?>" id="reject_btn" data-loading-text="<?php echo app_lang('wait_text'); ?>" class="btn btn-warning pull-right mleft10 display-block text-white">
								<?php echo app_lang('reject'); ?>
							</a>
							<a href="<?php echo get_uri('recruitment/approve_reject_proposal/'.'approved'.'/'.$proposals->id); ?>" id="approved_btn" data-loading-text="<?php echo app_lang('wait_text'); ?>"  class="btn btn-success pull-right display-block text-white">
								<?php echo app_lang('approve'); ?>
							</a>
						<?php } ?>
					</div>
				</div>
				<div class="row ml2 mr5 mt15">
					<div class="row col-md-12">
						<?php if(get_staff_user_id1() == $proposals->approver && $proposals->status == 1){ ?>
							<div id="reject_div">
								<a href="<?php echo get_uri('recruitment/approve_reject_proposal/'.'reject'.'/'.$proposals->id); ?>" id="reject_btn" data-loading-text="<?php echo app_lang('wait_text'); ?>" class="btn btn-warning pull-right mleft10 display-block">
									<?php echo app_lang('reject'); ?>
								</a>
							</div>
							<div id="approved_div">
								<a href="<?php echo get_uri('recruitment/approve_reject_proposal/'.'approved'.'/'.$proposals->id); ?>" id="approved_btn" data-loading-text="<?php echo app_lang('wait_text'); ?>"  class="btn btn-success pull-right display-block">
									<?php echo app_lang('approve'); ?>
								</a>
							</div>
						<?php } ?>
						<h4 class="general-infor-color"><?php echo app_lang('general_infor') ?></h4>
						<hr class="general-infor-hr" />
					</div>
					<div class="col-md-6" class="padding-left-right-0">
						<table class="table border table-striped margin-top-0">
							<tbody>
								<tr class="project-overview">
									<td class="bold" width="30%"><?php echo app_lang('proposal_name'); ?></td>
									<td><?php echo html_entity_decode($proposals->proposal_name); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('department'); ?></td>
									<td><?php echo get_rec_dpm_name($proposals->department); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('form_of_work'); ?></td>
									<td>
										<?php 
										if(strlen($proposals->form_work) > 0){
											echo app_lang($proposals->form_work);
										}else{
											echo html_entity_decode($proposals->form_work);
										}
										?>

									</td>
								</tr>

								<tr class="project-overview">
									<?php 
									$current_id = get_base_currency();
									?>
									<td class="bold"><?php echo app_lang('starting_salary'); ?></td>
									<td><?php echo to_currency($proposals->salary_from, $current_id).' - '.to_currency($proposals->salary_to, $current_id); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('campaign'); ?></td>
									<td></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('reason_recruitment'); ?></td>
									<td><?php echo html_entity_decode($proposals->reason_recruitment); ?></td>
								</tr>

							</tbody>
						</table>

					</div>
					<div class="col-md-6 padding-left-right-0">
						<table class="table table-striped margin-top-0">
							<tbody>
								<tr class="project-overview">
									<td class="bold" width="40%"><?php echo app_lang('position'); ?></td>
									<td><?php echo get_rec_position_name($proposals->position); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('amount_recruiment'); ?></td>
									<td><?php echo html_entity_decode($proposals->amount_recruiment); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('workplace'); ?></td>
									<td><?php echo html_entity_decode($proposals->workplace); ?></td>
								</tr>

								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('recruiment_duration'); ?></td>
									<td><?php echo format_to_date($proposals->from_date) .' - '. format_to_date($proposals->to_date) ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('status'); ?></td>
									<td>
										<?php 
										if($proposals->status == 1 ){
											echo '<span class="badge bg-info large mt-4">'.app_lang('_proposal').'</span>';
										}elseif($proposals->status == 2 ){
											echo '<span class="badge bg-success large mt-4">'.app_lang('approved').'</span>';
										}elseif($proposals->status == 3 ){
											echo '<span class="badge bg-primary large mt-4">'.app_lang('made_finish').'</span>';
										}elseif($proposals->status == 4 ){
											echo '<span class="badge bg-danger large mt-4">'.app_lang('reject').'</span>';
										}
										?>
									</td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('add_from'); ?></td>
									<td><a href="<?php echo get_uri('team_member/view/'.$proposals->add_from.'/general'); ?>"><?php echo get_staff_full_name1($proposals->add_from); ?></a></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="col-md-12 padding-left-10">
						<p class="bold text-muted"><?php echo app_lang('job_description').': '.$proposals->job_description; ?></p>

					</div>
					<div class="row col-md-12">
						<h4 class="candidate_request-color"><?php echo app_lang('candidate_request') ?></h4>
						<hr class="candidate_request-hr" />
					</div>
					<div class="col-md-6 ">
						<table class="table border table-striped margin-top-0">
							<tbody>
								<tr class="project-overview">
									<td class="bold" width="30%"><?php echo app_lang('gender'); ?></td>
									<td><?php echo app_lang($proposals->gender); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('height'); ?></td>
									<td><?php echo html_entity_decode($proposals->height); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('literacy'); ?></td>
									<td><?php echo app_lang($proposals->literacy); ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="col-md-6 padding-left-right-0">
						<table class="table table-striped margin-top-0">
							<tbody>
								<tr class="project-overview">
									<td class="bold" width="30%"><?php echo app_lang('ages'); ?></td>
									<td><?php echo html_entity_decode($proposals->ages_from.' - '.$proposals->ages_to); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('weight'); ?></td>
									<td><?php echo html_entity_decode($proposals->weight); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('experience'); ?></td>
									<td><?php echo app_lang($proposals->experience); ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="col-md-12">
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

			<div class="card">
				<div class="container-fluid">
					<div class="">
						<div class="btn-bottom-toolbar text-right mb20 mt20">
							<a href="<?php echo get_uri('recruitment/recruitment_proposal'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
						</div>
					</div>
					<div class="btn-bottom-pusher"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php require 'plugins/Recruitment/assets/js/recruitment_plans/recruitment_plan_manage_js.php';?>

</body>
</html>

