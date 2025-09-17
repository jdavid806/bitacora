

<div id="page-content" class="page-wrapper clearfix help-page-container ">
	<div id="search-box-wrapper">
		<div class="help-search-box-container">
			<h2><?php
			echo app_lang("recruitment_portal");
		?></h2>
		<?php echo form_open_multipart(site_url('recruitment_portal/search_job'), array('id' => 'search_job')); ?>

		<div class="input-group d-block search-box">
			<div class="awesomplete">
				
				<?php
				echo form_input(array(
					"id" => "help-search-box",
					"name" => "search",
					"value" => isset($search) ? $search : '' ,
					"autocomplete" => "false",
					"class" => "form-control help-search-box kb-search-input",
					"placeholder" => app_lang('rp_key_search')
				));
				?>
			</div>
			<span class="spinning-btn"></span>
		</div>
		<?php echo form_close(); ?>

	</div>
</div>

<div class="help-page view-container-large min-height-300">

	<?php if (isset($rec_campaingn_total)) {?>
		<h2 class="title title-search text-danger"><a class="text-danger" href="#"><?php echo html_entity_decode($rec_campaingn_total) . ' ' . $search . app_lang('job_for_you') ?> </a></h2>
	<?php }?>

	<?php if (count($rec_campaingn) > 0) {?>

		<div class="panel_s">
			<div class="panel-body" id="panel_body_job">

				<?php foreach ($rec_campaingn as $rec_value) {?>
					<div class="job" id="job_68268">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="job_content col-md-12">

										<div class="job-company-logo col-md-2 <?php if (!isset($rec_value['company_id']) || ($rec_value['company_id'] == '0')) {echo 'hide';}?>">
											<img class="images_w_table1" src="<?php echo html_entity_decode($rec_value['company_logo']) ?>" alt="<?php echo html_entity_decode($rec_value['alt_logo']) ?>">
										</div>

										<div class="job__description col-md-7 <?php if (!isset($rec_value['company_id']) || ($rec_value['company_id'] == '0')) {echo 'job__description_margin';}?>">
											<div class="job__body">
												<div class="details">
													<h2 class="title "><a class="bold a-color" data-controller="utm-tracking" href="<?php echo site_url('recruitment_portal/job_detail/' . $rec_value['cp_id']) ?>"><?php echo html_entity_decode($rec_value['campaign_name']) ?></a>
													</h2>

													<div class="salary not-signed-in">

														<a class="view-salary text-muted " data-toggle="modal" data-target="#sign-in-modal" rel="nofollow" href="#"><?php echo html_entity_decode($rec_value['company_name']) ?></a>
													</div>

													<div class="salary not-signed-in">

														<div class="job-bottom">
															<div class="tag-list ">
																<?php if ($rec_value['cp_form_work']) {?>
																	<a class="job__skill ilabel mkt-track <?php echo html_entity_decode($rec_value['cp_form_work']) ?>-color" data-controller="utm-tracking" href="#">
																		<span>
																			<?php echo app_lang($rec_value['cp_form_work']) ?>
																		</span>
																	</a>
																<?php }?>

																<a class="job__skill ilabel-cp-workplace  mkt-track " data-controller="utm-tracking" href="#">

																	<span> - <?php echo html_entity_decode($rec_value['cp_workplace']) ?></span>
																</a>

															</div>

														</div>

													</div>

													<div class="salary not-signed-in">

														<h5 class="view-salary bold " data-toggle="modal" data-target="#sign-in-modal" rel="nofollow" href="#"><?php echo html_entity_decode($rec_value['position_name']) ?></h5>
													</div>


													<div class="job-description">
														<p>
															<?php echo html_entity_decode($rec_value['cp_job_description'] . ' ...') ?>

														</p>
													</div>

												</div>
											</div>

										</div>

										<div class="city_and_posted_date col-md-3">
											<div class="feature-view_detail new text ">
												<a class="bold a-color text-uppercase" data-controller="utm-tracking" href="<?php echo site_url('recruitment_portal/job_detail/' . $rec_value['cp_id']) ?>"><?php echo app_lang('view_detail') ?></a>
											</div>

											<?php if (strtotime(date("Y-m-d")) > strtotime($rec_value['cp_to_date'])) {?>
												<div class="feature new text "><?php echo app_lang('overdue') ?></div>
											<?php } else {?>
												<div class=""></div>
											<?php }?>

											<div class="distance-time-job-posted">
												<span class="distance-time highlight">
													<?php echo html_entity_decode($rec_value['cp_from_date']); ?>
												</span> -
												<span class="distance-time highlight">
													<?php echo html_entity_decode($rec_value['cp_to_date']); ?>
												</span>
											</div>
										</div>

									</div>
								</div>

							</div>
						</div>

					</div>
				<?php }?>


			</div>
		</div>
	<?php }?>

	<?php if (count($rec_campaingn) == 0) {?>
		<div class="panel_s">
			<div class="panel-body">
				<p class="no-margin text-center"><?php echo app_lang('recruitment_portal_not_found'); ?></p>
			</div>
		</div>
	<?php }?>

	<?php if (count($rec_campaingn) > 0) {?>
		<div class="panel_s">
			<div class="panel-body">
				<div class="card text-center">
					<div class="card-body" id="disbled_button">
						<button type="button" class="btn btn-danger recruitment_showmore " onclick="show_more_job(this); return false;" ><?php echo app_lang('show_more_job') ?></button>
					</div>
				</div>

			</div>
		</div>
	<?php }?>

	<div id="additional">
		<input type="hidden" name="current_page" value="<?php if (isset($page)) {echo html_entity_decode($page);} else {echo '2';}
		;?>">
	</div>

</div>
</div>
<?php require 'plugins/Recruitment/assets/js/recruitment_portals/recruitment_portal_js.php';?>




