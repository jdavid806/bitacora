<div id="payslip_report" class="hide reports">
	<div class="table-responsive">

		<?php
		$table_data = array(
			app_lang('id'),
			app_lang('month'),
			app_lang('ps_pay_slip_number'),
			app_lang('employee_name'),
			app_lang('ps_gross_pay'),
			app_lang('ps_total_deductions'),
			app_lang('ps_income_tax_paye'),
			app_lang('ps_it_rebate_value'),
			app_lang('commission_amount'),
			app_lang('ps_bonus_kpi'),
			app_lang('ps_total_insurance'),
			app_lang('ps_net_pay'),
			app_lang('ps_total_cost'),
		);

		render_datatable1($table_data,'table-payslip_report',
			array('customizable-table'),
			array(
				'id'=>'table-table-payslip_report',
				'data-last-order-identifier'=>'table-payslip_report',
			)); ?>

		</div>
	</div>
