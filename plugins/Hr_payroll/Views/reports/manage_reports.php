<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					
				</div>

				<div class="row pt15 pl15 pr15">
					<div class="col-md-4 border-right">
						<h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo app_lang('hrp_reports'); ?></h4>
						<hr class="hr-10" />
						<p><a href="#" class="font-medium" onclick="init_report(this,'payslip_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo app_lang('hr_payslip_report'); ?></a></p>
						<hr class="hr-10" />
						<p><a href="#" class="font-medium" onclick="init_report(this,'income_summary_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo app_lang('hr_income_summary_report'); ?></a></p>
						<hr class="hr-10" />
						<p><a href="#" class="font-medium" onclick="init_report(this,'insurance_cost_summary_report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo app_lang('hr_insurance_cost_summary_report'); ?></a></p>

					</div>
					<div class="col-md-4 border-right">
						<h4 class="no-margin font-medium"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo app_lang('charts_based_report'); ?></h4>
						<hr class="hr-10" />
						<p><a href="#" class="font-medium" onclick="init_report(this,'payslip_chart'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo app_lang('payslip_chart'); ?></a></p>
						<hr class="hr-10" />
						<p><a href="#" class="font-medium" onclick="init_report(this,'department_payslip_chart'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo app_lang('department_payslip_chart'); ?></a></p>
						<hr class="hr-10" />

						
					</div>
					<div class="col-md-4">
						<div class="bg-light-gray border-radius-4">
							<div class="p8">

								<div id="currency" class="form-group hide">
									<label for="currency"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo app_lang('report_sales_base_currency_select_explanation'); ?>"></i> <?php echo app_lang('currency'); ?></label><br />
									<select class="select2 validate-hidden" name="currency" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">

									</select>
								</div>


								<div class="form-group" id="report-time">
									<label for="months-report"><?php echo app_lang('period_datepicker'); ?></label><br />
									<select class="select2 validate-hidden" name="months-report" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>">
										<option value=""><?php echo app_lang('report_sales_months_all_time'); ?></option>
										<option value="this_month"><?php echo app_lang('this_month'); ?></option>
										<option value="1"><?php echo app_lang('last_month'); ?></option>
										<option value="this_year"><?php echo app_lang('this_year'); ?></option>
										<option value="last_year"><?php echo app_lang('last_year'); ?></option>
										<option value="3" data-subtext="<?php echo format_to_date(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo format_to_date(date('Y-m-t')); ?>"><?php echo app_lang('report_sales_months_three_months'); ?></option>
										<option value="6" data-subtext="<?php echo format_to_date(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo format_to_date(date('Y-m-t')); ?>"><?php echo app_lang('report_sales_months_six_months'); ?></option>
										<option value="12" data-subtext="<?php echo format_to_date(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo format_to_date(date('Y-m-t')); ?>"><?php echo app_lang('report_sales_months_twelve_months'); ?></option>
										<option value="custom"><?php echo app_lang('period_datepicker'); ?></option>
									</select>

								</div>

								<?php $current_year = date('Y');
								$y0 = (int)$current_year;
								$y1 = (int)$current_year - 1;
								$y2 = (int)$current_year - 2;
								$y3 = (int)$current_year - 3;
								?>


								<div class="form-group hide" id="year_requisition">
									<label for="months-report"><?php echo app_lang('period_datepicker'); ?></label><br />
									<select  name="year_requisition" id="year_requisition"  class="select2 validate-hidden"  data-width="100%" placeholder="<?php echo app_lang('filter_by').' '.app_lang('year'); ?>">
										<option value="<?php echo html_entity_decode($y0) ; ?>" <?php echo 'selected' ?>><?php echo app_lang('year').' '. $y0 ; ?></option>
										<option value="<?php echo html_entity_decode($y1) ; ?>"><?php echo app_lang('year').' '. $y1 ; ?></option>
										<option value="<?php echo html_entity_decode($y2) ; ?>"><?php echo app_lang('year').' '. $y2 ; ?></option>
										<option value="<?php echo html_entity_decode($y3) ; ?>"><?php echo app_lang('year').' '. $y3 ; ?></option>

									</select>
								</div>


								<div id="date-range" class="hide mbot15">
									<div class="row">
										<div class="col-md-6">
											<label for="report-from" class="control-label"><?php echo app_lang('report_sales_from_date'); ?></label>
											<div class="input-group date">
												<input type="text" class="form-control datepicker" id="report-from" autocomplete="off" name="report-from">
												<div class="input-group-addon">
													<i class="fa fa-calendar calendar-icon"></i>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<label for="report-to" class="control-label"><?php echo app_lang('report_sales_to_date'); ?></label>
											<div class="input-group date">
												<input type="text" class="form-control datepicker" disabled="disabled" autocomplete="off" id="report-to" name="report-to">
												<div class="input-group-addon">
													<i class="fa fa-calendar calendar-icon"></i>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div id="report" class="hide pl15 pr15">
					<hr class="hr-panel-heading" />
					<div class="row d-flex justify-content-center">
						<h5 class="title_table"></h5>                 
					</div>

					<div class="row sorting_table hide">

						<div class="col-md-4">
							<div class="form-group">
								<label for="annual_leave"><?php echo app_lang('staff_departments'); ?></label>
								<select name="department[]" class="select2 validate-hidden" data-live-search="true" multiple data-width="100%" data-actions-box="true" placeholder="<?php echo app_lang('invoice_status_report_all'); ?>">
									<?php foreach($department as $value){ ?>
										<option value="<?php echo html_entity_decode($value['id']); ?>"><?php echo html_entity_decode($value['title']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="annual_leave"><?php echo app_lang('staff'); ?></label>
								<select name="staff[]" class="select2 validate-hidden" data-live-search="true" multiple data-width="100%" data-actions-box="true" placeholder="<?php echo app_lang('invoice_status_report_all'); ?>">
									<?php foreach($staff as $item){ ?>
										<option value="<?php echo html_entity_decode($item['id']); ?>"><?php echo html_entity_decode($item['first_name'].' '.$item['last_name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>

					</div>

					<div class="row payslip_chart_table hide">
						<div class="col-md-4">
							<div class="form-group">
								<label for="annual_leave"><?php echo app_lang('staff'); ?></label>
								<select name="staff" class="select2 validate-hidden" data-live-search="true" data-width="100%" data-actions-box="true" placeholder="<?php echo app_lang('invoice_status_report_all'); ?>">
									<?php foreach($staff as $item){ ?>
										<option value="<?php echo html_entity_decode($item['id']); ?>"><?php echo html_entity_decode($item['first_name'].' '.$item['last_name']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<?php echo  view('Hr_payroll\Views\reports\payslip_report.php'); ?>  
					<?php echo  view('Hr_payroll\Views\reports\income_summary_report.php'); ?>  
					<?php echo  view('Hr_payroll\Views\reports\insurance_cost_summary_report.php'); ?>  
					<?php echo  view('Hr_payroll\Views\reports\payslip_chart.php'); ?>  
					<?php echo  view('Hr_payroll\Views\reports\department_payslip_chart.php'); ?>  



				</div>


			</div>


		</div>
	</div>
</div>
</div>

<?php require 'plugins/Hr_payroll/assets/js/reports/report_js.php';?>


</body>
</html>