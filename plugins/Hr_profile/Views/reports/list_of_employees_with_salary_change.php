<div id="list_of_employees_with_salary_change" class="hide reports">
	<div class="table-responsive">

		<?php
		$table_data = array(
			app_lang('id'),
			app_lang('hr_employee_code'),
			app_lang('hr_hr_staff_name'),
			app_lang('hr_hr_job_position'),
			app_lang('hr_department'),
			app_lang('hr_start_month'),
			app_lang('hr_old_value'),
			app_lang('hr_new_value'),
		);

		render_datatable1($table_data,'list_of_employees_with_salary_change',
			array('customizable-table'),
			array(
				'id'=>'table-list_of_employees_with_salary_change',
				'data-last-order-identifier'=>'list_of_employees_with_salary_change',
			)); ?>

		</div>
	</div>
