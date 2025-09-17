<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">

				<div class="modal-body clearfix" id="" data-name="<?php echo app_lang('hrm'); ?>">
					<div class="row ">
						<div class="col-md-12 mb-5">

							<h4 class=" font-bold mb-5"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('recruitment_dashboard'); ?></h4>

							<div class="row">
								<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
									<div class="top_stats_wrapper minheight85">
										<a class="text-success mbot15">
											<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo app_lang('total_campaign'); ?>
										</p>
										<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['total']); ?></span>
									</a>
									<div class="clearfix"></div>
									<div class="progress no-margin progress-bar-mini">
										<div class="progress-bar progress-bar-success no-percent-text not-dynamic width-100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['total']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['total']); ?>" data-percent="100%">
										</div>
									</div>
								</div>
							</div>
							<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
								<div class="top_stats_wrapper minheight85">
									<a class="text mbot15">
										<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo app_lang('campaign_progress'); ?>
									</p>
									<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['inprogress']) ?></span>
								</a>
								<div class="clearfix"></div>
								<div class="progress no-margin progress-bar-mini">
									<?php
									$percentage = 0;
									if ($cp_count['total'] > 0) {
										$percentage = $cp_count['inprogress'] / $cp_count['total'] * 100;
									}
									?>
									<div class="progress-bar progress-bar no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['inprogress']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['total']); ?>" style=" width: <?php echo html_entity_decode($percentage); ?>%" data-percent="<?php echo html_entity_decode($percentage); ?>%">
									</div>
								</div>
							</div>
						</div>
						<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
							<div class="top_stats_wrapper minheight85">
								<a class="text-info mbot15">
									<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-envelope"></i> <?php echo app_lang('campaign_planning'); ?>
								</p>
								<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['planning']); ?></span>
							</a>
							<div class="clearfix"></div>
							<div class="progress no-margin progress-bar-mini">
								<?php
								$percentage = 0;
								if ($cp_count['total'] > 0) {
									$percentage = $cp_count['planning'] / $cp_count['total'] * 100;
								}
								?>
								<div class="progress-bar progress-bar-info no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['planning']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['total']); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent=" <?php echo html_entity_decode($percentage); ?>%">
								</div>
							</div>
						</div>
					</div>

					<div class="quick-stats-invoices col-xs-12 col-md-3 col-sm-6">
						<div class="top_stats_wrapper minheight85">
							<a class="text-warning mbot15">
								<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-ok"></i> <?php echo app_lang('campaign_finish'); ?>
							</p>
							<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['finish']); ?></span>
						</a>
						<div class="clearfix"></div>
						<div class="progress no-margin progress-bar-mini">
							<?php
							$percentage = 0;
							if ($cp_count['total'] > 0) {
								$percentage = $cp_count['finish'] / $cp_count['total'] * 100;
							}
							?>
							<div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['finish']) ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['total']); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent=" <?php echo html_entity_decode($percentage); ?>%">
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="col-md-6">
			<div id="rec_plan_chart_by_status" class="minwidth310">
			</div>
			<br>
		</div>
		<div class="col-md-6">
			<div id="rec_campaign_chart_by_status" class="minwidth310">
			</div>
			<br>
		</div>

		<div class="col-md-6">
			<div class="row">
				<div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6">
					<div class="top_stats_wrapper minheight85">
						<a class="text-success mbot15">
							<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo app_lang('candidates_need_to_recruit'); ?>
						</p>
						<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['candidate_need']); ?></span>
					</a>
					<div class="clearfix"></div>
					<div class="progress no-margin progress-bar-mini">
						<div class="progress-bar progress-bar-success no-percent-text not-dynamic width100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['candidate_need']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['candidate_need']); ?>" data-percent="100%">
						</div>
					</div>
				</div>
			</div>
			<div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6">
				<div class="top_stats_wrapper minheight85">
					<a class="text mbot15">
						<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-edit"></i> <?php echo app_lang('recruited_candidate'); ?>
					</p>
					<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['recruited']) ?></span>
				</a>
				<div class="clearfix"></div>
				<div class="progress no-margin progress-bar-mini">
					<?php
					$percentage = 0;
					if ($cp_count['total'] > 0) {
						$percentage = $cp_count['recruited'] / $cp_count['total'] * 100;
					}
					?>
					<div class="progress-bar progress-bar no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['recruited']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['candidate_need']); ?>" style=" width: <?php echo html_entity_decode($percentage); ?>%" data-percent="<?php echo html_entity_decode($percentage); ?>%">
					</div>
				</div>
			</div>
		</div>
		<div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6">
			<div class="top_stats_wrapper minheight85">
				<a class="text-info mbot15">
					<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-envelope"></i> <?php echo app_lang('upcoming_interview'); ?>
				</p>
				<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['upcomming_intv']); ?></span>
			</a>
			<div class="clearfix"></div>
			<div class="progress no-margin progress-bar-mini">
				<div class="progress-bar progress-bar-info no-percent-text not-dynamic width100" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['upcomming_intv']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['total']); ?>" data-percent=" 100%">
				</div>
			</div>
		</div>
	</div>

	<div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6">
		<div class="top_stats_wrapper minheight85">
			<a class="text-warning mbot15">
				<p class="text-uppercase mtop5 minheight35"><i class="hidden-sm glyphicon glyphicon-ok"></i> <?php echo app_lang('campaign_recruiting'); ?>
			</p>
			<span class="pull-right bold no-mtop fontsize24"><?php echo html_entity_decode($cp_count['recruiting']); ?></span>
		</a>
		<div class="clearfix"></div>
		<div class="progress no-margin progress-bar-mini">
			<?php
			$percentage = 0;
			if ($cp_count['total'] > 0) {
				$percentage = $cp_count['recruiting'] / $cp_count['total'] * 100;
			}
			?>
			<div class="progress-bar progress-bar-warning no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($cp_count['recruiting']) ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($cp_count['candidate_need']); ?>" style="width: <?php echo html_entity_decode($percentage); ?>%" data-percent=" <?php echo html_entity_decode($percentage); ?>%">
			</div>
		</div>
	</div>
</div>
</div>
</div>
<div class="col-md-6">
	<p class="bold margintop15-color"><?php echo app_lang('upcoming_interview'); ?></p>
	<hr class="margintop15-color" />
	<table class="table dt-table">
		<thead>
			<th><?php echo app_lang('interview_schedules_name'); ?></th>
			<th><?php echo app_lang('recruitment_campaign'); ?></th>
			<th><?php echo app_lang('rec_time'); ?></th>
			<th><?php echo app_lang('interview_day'); ?></th>
		</thead>
		<tbody>
			<?php foreach ($upcoming_interview as $intv) {
				?>
				<tr>
					<td><?php echo html_entity_decode($intv['is_name']); ?></td>
					<td><?php $cp = get_rec_campaign_hp($intv['campaign']);
					$_data = '';
					if (isset($cp)) {
						$_data = $cp->campaign_code . ' - ' . $cp->campaign_name;
					} else {
						$_data = '';
					}
					echo html_entity_decode($_data);
				?></td>
				<td><?php echo html_entity_decode($intv['from_time'] . ' - ' . $intv['to_time']); ?></td>
				<td><?php echo format_to_date($intv['interview_day'], false); ?></td>
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
</div>
<?php require 'plugins/Recruitment/assets/js/dashboard_js.php';?>