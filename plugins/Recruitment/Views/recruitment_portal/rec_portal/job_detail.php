<div id="page-content" class="page-wrapper clearfix help-page-container ">

	<div class="help-page view-container-large min-height-300">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s">
					<div class="panel-body">
						<!-- job , and company infor -->
						<div class="row">

							<div class="col-md-6">
								<div class="card">
									<div class="card-body">
										<h2 class="card-title bold campaign-title"><?php echo html_entity_decode($rec_campaingn->campaign_name) ?></h2>

										<h6 class="card-title bold  duration-title <?php if(strtotime(date("Y-m-d")) > strtotime($rec_campaingn->cp_to_date)){echo 'text-danger' ;}else{ echo 'text-muted' ;} ?> "><?php echo app_lang('duration') ?>: <?php echo html_entity_decode($rec_campaingn->cp_from_date.' - '.$rec_campaingn->cp_to_date); ?></h6>

										<?php if($rec_campaingn->display_salary == 1){ ?>

											<h6 class="card-title bold text-muted "><?php echo app_lang('monthly_salary') ?> <span class="bold"><?php echo html_entity_decode(to_currency($rec_campaingn->cp_salary_from, get_base_currency()).' - '.to_currency($rec_campaingn->cp_salary_to, get_base_currency())) ?></span></h6>


										<?php } ?>

										<h6 class="card-title bold text-muted "><?php echo app_lang('job_position') ?>: <span class="badge bg-info large mt-0"><?php echo html_entity_decode($rec_campaingn->position_name) ?></span></h6>


									</div>
								</div>
							</div>

							<div class="col-md-6">
								<?php if(isset($rec_campaingn->company_id) && ($rec_campaingn->company_id != '0')){ ?>
									<div class="row no-gutters">
										<div class="col-md-8">
											<div class="card text-right">
												<div class="card-body">
													<h3 class="card-title bold company-title"><?php echo html_entity_decode($rec_campaingn->company_name) ?></h3>
													<h6 class="card-title bold text-muted"><?php echo html_entity_decode($rec_campaingn->company_address) ?></h6>

												</div>
											</div>
										</div>
										<div class="col-md-4">
											<img class="images_w_table_detail card-img" src="<?php echo html_entity_decode($rec_campaingn->company_logo) ?>" alt="<?php echo html_entity_decode($rec_campaingn->alt_logo) ?>">

										</div>

									</div>
								<?php } ?>

							</div>
						</div>
						<hr class="mt-0">
						<!-- action -->
						<div class="row">

							<div class="col-md-12 ">
								<div class="">
									<?php if(!(strtotime(date("Y-m-d")) > strtotime($rec_campaingn->cp_to_date))){?>
										<?php if($rec_channel){ ?>
											<?php 
											$text_apply = 'btn-success';
											$apply_ms = '';
											if(in_array($rec_campaingn->cp_id, $applied_job_activate)){
												$text_apply = 'btn-danger';
												$apply_ms = app_lang('You_have_applied_for_this_position');
											}

											?>
											<?php if(is_candidate_logged_in()){ ?>
												<a class="btn <?php echo html_entity_decode($text_apply); ?>" href="<?php echo site_url('recruitment_portal/applied_now/'.$rec_campaingn->cp_id.'/'.$rec_channel->form_key); ?>" title="<?php echo html_entity_decode($apply_ms); ?>"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('apply_now') ?></a>
											<?php }else{ ?>
												<a class="btn <?php echo html_entity_decode($text_apply); ?>" href="<?php echo site_url('forms/wtl/'.$rec_campaingn->cp_id.'/'.$rec_channel->form_key); ?>" target="_blank" title="<?php echo html_entity_decode($apply_ms); ?>"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('apply_now') ?></a>
											<?php } ?>

										<?php } ?>
									<?php } ?>
									<button type="button" class="btn btn-primary pull-left display-block" onclick="send_mail_candidate(this,<?php echo html_entity_decode($id); ?>, '\'<?php echo html_entity_decode($rec_campaingn->company_name) ?>\''); return false;" ><span data-feather="mail" class="icon-16"></span> <?php echo app_lang('email_to_friend') ?></button>
									<a href="<?php echo site_url('recruitment_portal') ?>" class="btn btn-default appointment_go_back"><?php echo app_lang('go_back') ?></a>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="row">

			<div class="col-md-8 ">
				<div class="panel_s">
					<div class="panel-body">

						<div class="card">
							<div class="card-body">
								<h3 class="card-title  bold company-title"><?php echo app_lang('job_description') ?></h3>
								<p class="card-text"><?php echo html_entity_decode($rec_campaingn->cp_job_description) ?></p>

							</div>
						</div>

						<!-- job skill -->
						<div class="card">
							<div class="card-body">
								<h3 class="card-title  bold company-title"><?php echo app_lang('skill_recquired') ?></h3>

								<?php if(isset($rec_campaingn->rec_job_skill)){ ?>
									<?php if(count($rec_campaingn->rec_job_skill) >0) {?>
										<?php foreach ($rec_campaingn->rec_job_skill as $value) {?>
											<button type="button" class="btn btn-primary skill-margin"><?php echo html_entity_decode($value['skill_name']) ?></button>

										<?php } ?>
									<?php } ?>
								<?php } ?>

							</div>
						</div>

						<?php if(!(strtotime(date("Y-m-d")) > strtotime($rec_campaingn->cp_to_date))){?>
							<?php if($rec_channel){ ?>
								<div class="card text-right buton-margin skill-margin">
									<div class="card-body">
										<?php 
										$text_apply = 'btn-success';
										$apply_ms = '';
										if(in_array($rec_campaingn->cp_id, $applied_job_activate)){
											$text_apply = 'btn-danger';
											$apply_ms = app_lang('You_have_applied_for_this_position');
										}

										?>
										<?php if(is_candidate_logged_in()){ ?>
											<a class="btn <?php echo html_entity_decode($text_apply); ?>" href="<?php echo site_url('recruitment_portal/applied_now/'.$rec_campaingn->cp_id.'/'.$rec_channel->form_key); ?>" title="<?php echo html_entity_decode($apply_ms); ?>"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('apply_now') ?></a>
										<?php }else{ ?>
											<a class="btn <?php echo html_entity_decode($text_apply); ?>" href="<?php echo site_url('forms/wtl/'.$rec_campaingn->cp_id.'/'.$rec_channel->form_key); ?>" target="_blank" title="<?php echo html_entity_decode($apply_ms); ?>"><span data-feather="send" class="icon-16"></span> <?php echo app_lang('apply_now') ?></a>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						<?php } ?>

					</div>
				</div>

				<!-- job related  start-->
				<?php if(isset($rec_campaingn->job_in_company)){ ?>
					<?php if(count($rec_campaingn->job_in_company) > 0){ ?>

						<h3 class="card-title  bold company-title"><?php echo app_lang('related_jobs') ?></h3>
						<div class="panel_s">
							<div class="panel-body" id="panel_body_job">

								<?php foreach ($rec_campaingn->job_in_company as $rec_value) { ?>
									<div class="job" id="job_68268">
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="job_content_related col-md-12">

														<div class="job-company-logo col-md-2">
															<img class="images_w_table_detail" src="<?php echo html_entity_decode($rec_value['company_logo']) ?>" alt="<?php echo html_entity_decode($rec_value['alt_logo']) ?>">
														</div>

														<div class="job__description_detail col-md-6">
															<div class="job__body">
																<div class="details">
																	<h3 class="title h3-job-related"><a class="bold a-color" data-controller="utm-tracking" href="<?php echo site_url('recruitment_portal/job_detail/'.$rec_value['cp_id']) ?>"><?php echo html_entity_decode($rec_value['campaign_name']) ?></a>
																</h3>

																<div class="salary not-signed-in">

																	<a class="view-salary text-muted " data-toggle="modal" data-target="#sign-in-modal" rel="nofollow" href="#"><?php echo html_entity_decode(app_lang($rec_value['company_name'])) ?></a>
																</div>

																<div class="salary not-signed-in">

																	<div class="job-bottom">
																		<div class="tag-list ">
																			<?php if($rec_value['cp_form_work']){ ?>
																				<a class="job__skill ilabel mkt-track <?php echo html_entity_decode($rec_value['cp_form_work']) ?>-color" data-controller="utm-tracking" href="#">
																					<span>
																						<?php echo app_lang($rec_value['cp_form_work']) ?>
																					</span>
																				</a>
																			<?php } ?>

																			<a class="job__skill ilabel-cp-workplace  mkt-track " data-controller="utm-tracking" href="#">

																				<span> - <?php echo html_entity_decode($rec_value['cp_workplace']) ?></span>
																			</a>

																		</div>

																	</div>

																</div>

																<div class="salary not-signed-in">

																	<h6 class="view-salary bold " data-toggle="modal" data-target="#sign-in-modal" rel="nofollow" href="#"><?php echo html_entity_decode(app_lang($rec_value['position_name'])) ?></h6>
																</div>


																<div class="job-description">

																	<p>
																		<?php echo html_entity_decode($rec_value['cp_job_description'].' ...') ?>

																	</p>
																</div>

															</div>
														</div>

													</div>

													<div class="city_and_posted_date hidden-xs col-md-3">
														<div class="feature-view_detail_related new text ">
															<a class="bold a-color text-uppercase" data-controller="utm-tracking" href="<?php echo site_url('recruitment_portal/job_detail/'.$rec_value['cp_id']) ?>"><?php echo app_lang('view_detail') ?></a>
														</div>

														<?php  if(strtotime(date("Y-m-d")) > strtotime($rec_value['cp_to_date'])){?>
															<div class="feature new text "><?php echo app_lang('overdue') ?></div>
														<?php }else{ ?>
															<div class=""></div>
														<?php } ?>

														<div class="distance-time-job-posted">
															<span class="distance-time highlight">
																<?php echo html_entity_decode($rec_value['cp_from_date'].' - '.$rec_value['cp_to_date']); ?>
															</span>
														</div>
													</div>

												</div>
											</div>

										</div>
									</div>  

								</div>
							<?php } ?>


						</div>
					</div>
				<?php } ?>
			<?php } ?>
			<!-- job related  end-->


		</div>

		<div class="col-md-4 ">
			<div class="panel_s">
				<div class="panel-body">

					<div class="row"> 
						<div class="col-md-12">
							<div class="card">
								<h4 class="card-title bold company-title"><?php echo app_lang('job_detail') ?></h4>
							</div>
						</div>

						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									<h6 class="card-title bold text-muted"><?php echo app_lang('location_') ?></h6>
								</div>
								<div class="col-md-9">

									<?php 
									$cp_workplace='' ;
									if($rec_campaingn->cp_workplace != ''){
										$cp_workplace .= $rec_campaingn->cp_workplace;
									}else{
										$cp_workplace .= '...';

									}
									?>
									<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($cp_workplace) ?></h6>
								</div>
							</div>

							<div class="row">
								<div class="col-md-3">
									<h6 class="card-title bold text-muted"><?php echo app_lang('company') ?></h6>
								</div>
								<div class="col-md-9">
									<?php 
									$company_name='' ;
									if($rec_campaingn->company_name != ''){
										$company_name .= $rec_campaingn->company_name;
									}else{
										$company_name .= '...';

									}
									?>

									<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($company_name) ?></h6>
								</div>
							</div>

							<div class="col-md-3 hide">
								<h6 class="card-title bold text-muted"><?php echo app_lang('type_') ?></h6>
							</div>
							<div class="col-md-9 hide">
								<?php 
								$cp_form_work='' ;
								if($rec_campaingn->cp_form_work != ''){
									$cp_form_work .= app_lang($rec_campaingn->cp_form_work);
								}else{
									$cp_form_work .= '...';

								}
								?>

								<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($cp_form_work) ?></h6>
							</div>

							<div class="col-md-3 hide">
								<h6 class="card-title bold text-muted"><?php echo app_lang('positions_') ?></h6>
							</div>
							<div class="col-md-9 hide">
								<?php 
								$cp_position='' ;
								if($rec_campaingn->cp_position != ''){
									$cp_position .= $rec_campaingn->cp_position;
								}else{
									$cp_position .= '...';

								}
								?>
								<h6 class="card-title bold   text-warning"><?php echo html_entity_decode(isset($rec_campaingn->cp_position) ? $rec_campaingn->cp_position : '...') ?></h6>
							</div>
							<?php if(get_setting('display_quantity_to_be_recruited') == 1){ ?>
								<div class="row">
									<div class="col-md-3">
										<h6 class="card-title bold text-muted"><?php echo app_lang('amount_recruiment') ?></h6>
									</div>
									<div class="col-md-9">
										<?php 
										$cp_amount_recruiment='' ;
										if($rec_campaingn->cp_amount_recruiment != ''){
											$cp_amount_recruiment .= $rec_campaingn->cp_amount_recruiment;
										}else{
											$cp_amount_recruiment .= '...';

										}
										?>

										<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($cp_amount_recruiment) ?></h6>
									</div>
								</div>
							<?php } ?>

							<div class="row">
								<div class="col-md-3">
									<h6 class="card-title bold text-muted"><?php echo app_lang('experience') ?></h6>
								</div>
								<div class="col-md-9">
									<?php 
									$cp_experience='' ;
									if($rec_campaingn->cp_experience != ''){
										$cp_experience .= app_lang($rec_campaingn->cp_experience);
									}else{
										$cp_experience .= '...';

									}
									?>

									<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($cp_experience) ?></h6>
								</div>
							</div>

							<div class="row">
								<div class="col-md-3">

									<h6 class="card-title bold text-muted"><?php echo app_lang('degree') ?></h6>
								</div>
								<div class="col-md-9">
									<?php 
									$cp_literacy='' ;
									if($rec_campaingn->cp_literacy != ''){
										$cp_literacy .= app_lang($rec_campaingn->cp_literacy);
									}else{
										$cp_literacy .= '...';

									}
									?>

									<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($cp_literacy) ?></h6>
								</div>
							</div>

							<div class="row">
								<div class="col-md-3">
									<h6 class="card-title bold text-muted"><?php echo app_lang('apply_before') ?></h6>
								</div>
								<div class="col-md-9">
									<?php 
									$cp_to_date='' ;
									if($rec_campaingn->cp_to_date != ''){
										$cp_to_date .= format_to_date($rec_campaingn->cp_to_date, false);
									}else{
										$cp_to_date .= '...';

									}
									?>

									<h6 class="card-title bold   text-warning"><?php echo html_entity_decode($cp_to_date) ?></h6>
								</div>
							</div>



						</div>


					</div>  


				</div>
			</div>
		</div>

	</div>


	<div class="modal fade" id="mail_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">


			<?php echo form_open_multipart(site_url('recruitment_portal/send_mail_list_candidate'), array('id' => 'mail_candidate-form', 'class'=> 'general-form')); ?>
			<div class="modal-content width-100">
				<div class="modal-header">
					<h4 class="modal-title">
						<?php echo app_lang('send_mail'); ?>
					</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<?php 
						echo form_hidden('job_detail_id', $id);
						?>
						<div class="col-md-12">
							<div class="form-group" app-field-wrapper="email">
								<label for="email" class="control-label"> <small class="req text-danger">* </small><?php echo app_lang('send_to'); ?></label>
								<input type="text" id="email" name="email" class="form-control" value="" required="true">
							</div>

						</div>

						<div class="col-md-12">
							<div class="form-group" app-field-wrapper="subject">
								<label for="subject" class="control-label"> <small class="req text-danger">* </small><?php echo app_lang('subject'); ?></label>
								<input type="text" id="subject" name="subject" class="form-control" value="" required="true">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group" app-field-wrapper="content">
								<label for="content" class="control-label"> <small class="req text-danger">* </small><?php echo app_lang('content'); ?></label>
								<textarea id="content" name="content" class="form-control" rows="7"></textarea>
							</div>
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


</div>
</div>
<?php require 'plugins/Recruitment/assets/js/job_detail_portal_js.php';?>

