
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="row ">
					<div class="col-md-6">

						<div  class="bill_to_color">
							<?php
							echo company_widget(get_default_company_id());
							?>
						</div>
					</div>
					<div class="col-md-6 text-right">
						<p class="no-mbot">
							<span class="bold"><?php echo app_lang('ps_pay_slip_number'); ?>: </span>
							<?php echo html_entity_decode($payslip_detail->pay_slip_number); ?></p>
						</div>
					</div>
					<br>

					<div class="row">
						<div class="col-md-12">
							<table class="table border table-striped table-margin-none">
								<thead>
									<th class="th-color"><?php echo app_lang('employee_details'); ?></th>
								</thead>
							</table>
						</div>
					</div>
					<div>
						<div class="col-md-8">
							<table class="table border table-striped ">
								<tbody>
									<tr class="project-overview">
										<td class="bold" width="30%" ><?php echo app_lang('employee_name'); ?></td>
										<td class="text-left"><?php echo html_entity_decode($payslip_detail->employee_name); ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('residential_address'); ?></td>
										<td><?php echo html_entity_decode(isset($employee['residential_address']) ? $employee['residential_address'] : '' )?></td>
									</tr>

									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('employee_number'); ?></td>
										<td><?php echo html_entity_decode(isset($payslip_detail) ? $payslip_detail->employee_number : ''); ?></td>
									</tr>

									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('job_title'); ?></td>
										<td><?php echo html_entity_decode(isset($employee['job_title']) ? $employee['job_title'] : '') ?></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="col-md-4">
							<table class="table border table-striped ">
								<tbody>
									<tr class="project-overview">
										<td class="bold" width="30%" >Tax Number</td>
										<td><?php echo html_entity_decode(isset($employee['income_tax_number']) ? $employee['income_tax_number'] : '') ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold">Deparment</td>
										<td><?php echo html_entity_decode($list_department) ?></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<table class="table border table-striped table-margin-none">
								<thead>
									<th class="th-color"><?php echo app_lang('monthly_pay_for').' ' .isset($payslip_detail) ? date('m-Y',strtotime($payslip_detail->month)) : '' ; ?></th>
								</thead>
							</table >
						</div>
					</div>

					<?php 

					$hrp_payslip_salary_allowance = hrp_payslip_json_data_decode($payslip_detail->json_data);


					?>

					<h5><?php echo app_lang('hrp_salary').', '.app_lang('hrp_allowance'); ?></h5>
					<div>
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6">
									<table class="table border table-striped table-margin-none">
										<tbody>
											<tr class="project-overview">
												<td  width="50%" ><b><?php echo app_lang('hrp_probation_contract'); ?></b></td>
												<td  width="50%" ></td>
											</tr>
											<?php echo isset($hrp_payslip_salary_allowance['probation_contract_list']) ? $hrp_payslip_salary_allowance['probation_contract_list'] : '' ?>

										</tbody>
									</table>
								</div>

								<div class="col-md-6">
									<table class="table border table-striped table-margin-none">
										<tbody>
											<tr class="project-overview">
												<td  width="50%" ><b><?php echo app_lang('hrp_formal_contract'); ?></b></td>
												<td  width="50%" ></td>

											</tr>
											<?php echo isset($hrp_payslip_salary_allowance['formal_contract_list']) ? $hrp_payslip_salary_allowance['formal_contract_list'] : '' ?>
											

										</tbody>
									</table>
								</div>
							</div>
							<div class="col-md-12">
								<hr class="hr-color">
							</div>

						</div>
					</div>

					<h5><?php echo app_lang('Earnings'); ?></h5>
					<div>
						<div class="col-md-12">
							<table class="table border table-striped table-margin-none">
								<tbody>
									<tr class="project-overview">
										<td  width="30%" ><?php echo app_lang('ps_gross_pay'); ?></td>
										<td class="text-left"><?php echo html_entity_decode(isset($payslip_detail) ?  to_decimal_format((float)$payslip_detail->gross_pay) : 0); ?></td>
									</tr>
									<tr class="project-overview">
										<td ><?php echo app_lang('commission_amount'); ?></td>
										<td><?php echo (isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->commission_amount) : 0); ?></td>
									</tr>

									<tr class="project-overview">
										<td ><?php echo app_lang('ps_bonus_kpi'); ?></td>
										<td><?php echo isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->bonus_kpi) : 0; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold" ><?php echo app_lang('total'); ?></td>
										<td><?php echo isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->gross_pay+$payslip_detail->commission_amount+$payslip_detail->bonus_kpi) : 0; ?></td>
									</tr>
								</tbody>
							</table>
							<hr class="hr-color">
						</div>

						<h5><?php echo app_lang('deduction_list'); ?></h5>
						<div class="col-md-12">
							<table class="table border table-striped table-margin-none">
								<tbody>
									<tr class="project-overview">
										<td  width="30%" ><?php echo app_lang('income_tax'); ?></td>
										<td class="text-left"><?php echo html_entity_decode( isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->income_tax_paye) : ''); ?></td>
									</tr>
									<tr class="project-overview">
										<td ><?php echo app_lang('hrp_insurrance'); ?></td>
										<td><?php echo isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->total_insurance) : 0; ?></td>
									</tr>

									<tr class="project-overview">
										<td ><?php echo app_lang('hrp_deduction_manage'); ?></td>
										<td><?php echo isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->total_deductions) : 0; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold" ><?php echo app_lang('total'); ?></td>
										<td><?php echo isset($payslip_detail) ? to_decimal_format((float)$payslip_detail->income_tax_paye+$payslip_detail->total_insurance+$payslip_detail->total_deductions) : 0; ?></td>
									</tr>
								</tbody>
							</table>
							<hr class="hr-color">

						</div>
						<div class="row">
							<div class="col-md-12">
								<table class="table border table-striped table-margin-none">
									<tbody>
										<tr class="project-overview">
											<td class="bold"  width="30%" ><?php echo app_lang('ps_net_pay'); ?></td>
											<td class="text-left"><?php echo isset($payslip_detail) ? html_entity_decode(to_decimal_format((float)$payslip_detail->net_pay)) : 0; ?></td>
										</tr>

									</tbody>
								</table>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
		</div>
	</div>
