<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('re_applied_jobs'); ?></h4>
				</div>
				<div class="modal-body clearfix">
					
					<?php if(count($candidate->applied_jobs) > 0){ ?>
						<div class="table-responsive">
							<table class="table dt-table table-invoices table-responsive pt15 pl15 pr15" data-order-col="1" data-order-type="desc">
								<thead>
									<tr>
										<th class="th-campaign"><?php echo app_lang('campaign'); ?></th>
										<th class="th-date_applied"><?php echo app_lang('date_applied'); ?></th>
										<th class="th-status"><?php echo app_lang('status'); ?></th>
										<th class="th-status"><?php echo app_lang('rec_options'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($candidate->applied_jobs as $applied_job) {?>
										<?php if($applied_job['activate'] == '1'){ ?>
											<tr class="">
												<td><?php
												$cp = get_rec_campaign_hp($applied_job['campaign_id']);
												$datas = '';
												if (isset($cp)) {
													$datas = '<a href="' . site_url('recruitment_portal/job_detail/' . $cp->cp_id) . '">' . $cp->campaign_code . ' - ' . $cp->campaign_name . '</a>';
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
												<a href="<?php echo site_url('recruitment_portal/delete_applied_job/'.$applied_job['id']) ?>" class="btn btn-danger btn-icon _delete" data-original-title="<?php echo app_lang('delete'); ?>" data-toggle="tooltip" data-placement="top">
													<span data-feather="x" class="icon-16"></span>
												</a>
											</td>

										</tr>
									<?php } ?>
								<?php } ?>
							</tbody>
						</table>
					</div>

				<?php }else{ ?>
					<?php echo app_lang('You_haven_applied_for_any_jobs_yet'); ?>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
</div>
