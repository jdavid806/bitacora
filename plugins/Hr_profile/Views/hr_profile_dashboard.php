<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$data_dash = $Hr_profile_model->get_hr_profile_dashboard_data();

$staff_chart_by_age = json_encode($Hr_profile_model->staff_chart_by_age());
$contract_type_chart = json_encode($Hr_profile_model->contract_type_chart());
$staff_departments_chart = json_encode($Hr_profile_model->staff_chart_by_departments());
$staff_chart_by_job_positions = json_encode($Hr_profile_model->staff_chart_by_job_positions());
?>

<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					
				</div>

				<div class="row pt15 pl15 pr15">
					
					<div class="col-md-6">
						<div id="staff_departments_chart">
						</div>
					</div>
					<div class="col-md-6">
						<div id="staff_chart_by_job_positions">
						</div>
					</div>

					<div class="col-md-6">
						<div id="staff_chart_by_age">
						</div>
					</div>
					<div class="col-md-6">
						<div id="staff_chart_by_fluctuate_according_to_seniority">
						</div>
					</div>
					<div class="col-md-12">
						<div id="report_by_staffs">
						</div>
					</div>

					<hr class="hr-panel-heading-dashboard">

					<div class="quick-stats-invoices col-md-6"  >
						<div class="top_stats_wrapper min-height-85">
							<a class="text-warning  mbot15">
								<p class="text-uppercase mtop5 min-height-35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo app_lang('hr_contract_is_about_to_expire'); ?>
								<a href="<?php echo get_uri('hr_profile/contracts?to_expire') ?>" >
									<i class="pull-right hidden-sm fa fa-eye" data-toggle="tooltip" data-original-title="<?php echo app_lang('view') ?>"></i>
								</a>
							</p>
							<span class="pull-right bold no-mtop font-size-24"><?php echo html_entity_decode($data_dash['expire_contract']); ?></span>
						</a>
						<div class="clearfix"></div>
						<div class="progress no-margin progress-bar-mini">
							<div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($data_dash['expire_contract']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($data_dash['total_staff']); ?>" style     =     "width:  <?php echo ($data_dash['expire_contract']/$data_dash['total_staff'])*100; ?>%" data-percent=" <?php echo ($data_dash['expire_contract']/$data_dash['total_staff'])*100; ?>%">
							</div>
						</div>
					</div>
				</div>

				<div class="quick-stats-invoices col-md-6">
					<div class="top_stats_wrapper min-height-85">
						<a class="text-danger mbot15">
							<p class="text-uppercase mtop5 min-height-35"><i class="hidden-sm glyphicon glyphicon-remove"></i> <?php echo app_lang('hr_overdue_contract'); ?>
							<a href="<?php echo get_uri('hr_profile/contracts?overdue_contract') ?>">
								<i class="pull-right hidden-sm fa fa-eye" data-toggle="tooltip" data-original-title="<?php echo app_lang('view') ?>"></i>
							</a>
						</p>
						<span class="pull-right bold no-mtop font-size-24"><?php echo html_entity_decode($data_dash['overdue_contract']); ?></span>
					</a>
					<div class="clearfix"></div>
					<div class="progress no-margin progress-bar-mini">
						<div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo html_entity_decode($data_dash['overdue_contract']); ?>" aria-valuemin="0" aria-valuemax="<?php echo html_entity_decode($data_dash['total_staff']); ?>" style    =    "width:  <?php echo ($data_dash['overdue_contract']/$data_dash['total_staff'])*100; ?>%" data-percent=" <?php echo ($data_dash['overdue_contract']/$data_dash['total_staff'])*100; ?>%">
						</div>
					</div>
				</div>
			</div>


			<div class="col-md-12 pt15 pl15 pr15">

				<h4><p class="padding-5 bold"><?php echo app_lang('hr_birthday_in_month'); ?></p></h4>
				<hr class="hr-panel-heading-dashboard">
				<table class="table dt-table scroll-responsive">
					<thead>
						<th><?php echo app_lang('hr_hr_staff_name'); ?></th>
						<th><?php echo app_lang('email'); ?></th>
						<th><?php echo app_lang('phone'); ?></th>
						<th><?php echo app_lang('date_of_birth'); ?></th>
						<th><?php echo app_lang('gender'); ?></th>
						<th><?php echo app_lang('departments'); ?></th>
					</thead>
					<tbody>

						<?php 
						$list_member_id = [];
						foreach($data_dash['staff_birthday'] as $staff){
							?>

							<tr>
								<td>
									<a href="<?php echo html_entity_decode(site_url('hr_profile/staff_profile/' . $staff['id']).'/general'); ?>"><?php  echo get_staff_image($staff['id'], false).$staff['first_name'] . ' ' . $staff['last_name'] ?></a>
								</td>
								<td><?php echo html_entity_decode($staff['email']); ?></td>
								<td><?php echo html_entity_decode($staff['phone']); ?></td>
								<td><?php echo format_to_date($staff['dob']); ?></td>
								<td><?php echo app_lang($staff['gender']); ?></td>
								<td> 
									<?php
									$_data = '';

									if($staff['id'] != ''){
										$team = $Hr_profile_model->get_staff_departments($staff['id']);
										$str = '';
										$j = 0;
										foreach ($team as $value) {
											$j++;
											$str .= '<span class="badge bg-success large mt-0">' . $value['title'] . '</span>&nbsp';
											if($j%2 == 0){
												$str .= '<br/>';
											}

										}
										$_data = $str;
									}
									else{
										$_data = '';
									}
									echo html_entity_decode($_data);

									?>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>

			<div class="col-md-12 pt15 pl15 pr15">
				<h4><p class="padding-5 bold"><?php echo app_lang('hr_unfinished_staff_received'); ?></p></h4>
				<hr class="hr-panel-heading-dashboard">
				<?php
				$table_data = array(
					app_lang('hr_staff_code'),
					app_lang('hr_hr_staff_name'),
					app_lang('hr_hr_job_position'),
					app_lang('departments'),
					app_lang('hr_hr_finish'));

				render_datatable1($table_data,'table_staff');
				?>

			</div>

		</div>

	</div>


</div>
</div>
</div>
</div>

<?php require 'plugins/Hr_profile/assets/js/hr_profile_dashboard_js.php';?>

</body>
</html>

