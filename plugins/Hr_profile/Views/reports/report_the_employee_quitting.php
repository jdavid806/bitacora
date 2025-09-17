<div id="report_the_employee_quitting" class="hide reports">
	<div class="table-responsive">

		<?php
		$table_data = array(
			app_lang('id'),
			app_lang('hr_employee_code'),
			app_lang('hr_hr_staff_name'),
			app_lang('hr_hr_job_position'),
			app_lang('hr_department'),
			app_lang('hr_day_to_do'),
			app_lang('hr_day_off'),
		);

		render_datatable1($table_data,'report_the_employee_quitting',
			array('customizable-table'),
			array(
				'id'=>'table-report_the_employee_quitting',
				'data-last-order-identifier'=>'report_the_employee_quitting',
			)); ?>

		</div>
	</div>

