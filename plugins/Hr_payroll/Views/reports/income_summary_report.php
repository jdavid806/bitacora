<div id="income_summary_report" class="hide reports">
	<div class="table-responsive">

		<?php
		$table_data = array(
			app_lang('ps_pay_slip_number'),
			app_lang('employee_name'),
			app_lang('department_name'),
			
			app_lang('month_1'),
			app_lang('month_2'),
			app_lang('month_3'),
			app_lang('month_4'),
			app_lang('month_05'),
			app_lang('month_6'),
			app_lang('month_7'),
			app_lang('month_8'),
			app_lang('month_9'),
			app_lang('month_10'),
			app_lang('month_11'),
			app_lang('month_12'),
			app_lang('average_income'),
		);

		render_datatable1($table_data,'table-income_summary_report',
			array('customizable-table'),
			array(
				'id'=>'table-table-income_summary_report',
				'data-last-order-identifier'=>'table-income_summary_report',
			)); ?>

		</div>
	</div>
