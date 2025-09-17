<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('rec_interview_schedules'); ?></h4>
				</div>
				<div class="modal-body clearfix">

					<div class="table-responsive">		
						<table class="table dt-table table-invoices" data-order-col="1" data-order-type="desc">
							<thead>
								<tr>
									<th class="hide"><?php echo app_lang('add_from'); ?></th>
									<th><?php echo app_lang('interview_schedules_name'); ?></th>
									<th><?php echo app_lang('rec_time'); ?></th>
									<th><?php echo app_lang('interview_day'); ?></th>
									<th><?php echo app_lang('recruitment_campaign'); ?></th>
									<th><?php echo app_lang('interviewer'); ?></th>
									<th><?php echo app_lang('date_add'); ?></th>
									<th><?php echo app_lang('interview_location'); ?></th>
									<th><?php echo app_lang('status'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($list_interview as $li) {
									?>
									<tr>
										<td class="hide">
											<?php
											echo get_staff_image($li['added_from'], true);

											?>
										</td>
										<td><?php echo html_entity_decode($li['is_name']) ?></td>
										<td><?php echo html_entity_decode($li['from_time'] . ' - ' . $li['to_time']); ?></td>
										<td><?php echo format_to_date($li['interview_day'],false); ?></td>
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
									<td><?php echo format_to_date($li['added_date'],false); ?></td>
									<td><?php echo html_entity_decode($li['interview_location']); ?></td>
									<td>
										<?php 
										echo re_render_status_html($li['in_id'], 'interview', $li['status']);
										?>
									</td>
								</tr>
							<?php }?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
