<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				

				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('general_infor'); ?></h4>
				</div>
				<div class="modal-body clearfix row ml2 mr5 mt15">
					<div class="col-md-12 padding-left-right-0">
						<table class="table border table-striped margin-top-0">
							<tbody>
								<tr class="project-overview">
									<td class="bold" width="40%"><?php echo app_lang('form_name'); ?></td>
									<td><?php echo html_entity_decode($recruitment_channel->r_form_name); ?></td>
								</tr>
								<tr class="project-overview">
									<td class="bold" width="40%"><?php echo app_lang('total_cv_reciver'); ?></td>
									<td><?php echo html_entity_decode($total_cv_form); ?></td>
								</tr>

								<tr class="project-overview">
									<td class="bold"><?php echo app_lang('responsible_person'); ?></td>
									<td>
										<?php echo  get_staff_image($recruitment_channel->responsible, true); ?>
									</tr>

									<?php 
									$arr_status=[];
									$arr_status['1']=app_lang('application');
									$arr_status['2']=app_lang('potential');
									$arr_status['3']=app_lang('interview');
									$arr_status['4']=app_lang('won_interview');
									$arr_status['5']=app_lang('send_offer');
									$arr_status['6']=app_lang('elect');
									$arr_status['7']=app_lang('non_elect');
									$arr_status['8']=app_lang('unanswer');
									$arr_status['9']=app_lang('transferred');
									$arr_status['10']=app_lang('preliminary_selection');

									$_data = ($arr_status[$recruitment_channel->lead_status]);

									?>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('status_after_submit_form'); ?></td>
										<td><?php echo get_status_candidate($recruitment_channel->lead_status);; ?></td>
									</tr>            

								</tbody>
							</table>
						</div>
						<div class="row">
							<div class="col-md-12">
								<h4 class="general-infor-color"><?php echo app_lang('intergate_form_website') ?></h4>

							</div>
						</div>
						<div class="row">
							<div class="col-md-12"><hr class="general-infor-hr"/></div>

						</div>
						<h4 class="bold">Form Info</h4>
						<p class="p-overflow"><b>Form url:</b>
							<span class="label label-default">
								<a href="<?php echo site_url('forms/wtl/0/'.$recruitment_channel->form_key); ?>" target="_blank"><?php echo site_url('forms/wtl/0/'.$recruitment_channel->form_key); ?></a>
							</span>
						</p>
						<hr>
						<h4 class="bold">Embed form</h4>
						<p>Copy &amp; Paste the code anywhere in your site to show the form, additionally you can adjust the width and height px to fit for your website.</p>

						<textarea class="form-control width-height-738-66 textarea-width" rows="1">&lt;iframe width="600" height="850" src="<?php echo site_url('recruitment/forms/wtl/0/'.$recruitment_channel->form_key); ?>" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</textarea>

					</div> 

					<div class="card">
						<div class="container-fluid">
							<div class="">
								<div class="btn-bottom-toolbar text-right mb20 mt20">
									<a href="<?php echo get_uri('recruitment/recruitment_channel'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
								</div>
							</div>
							<div class="btn-bottom-pusher"></div>
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>