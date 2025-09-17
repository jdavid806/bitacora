<div id="insurance_cost_summary_report" class="hide reports">
	<div class="table-responsive">

		<?php
		$table_data = array(
			app_lang('department_name'),
			app_lang('ps_total_insurance'),
		);

		render_datatable1($table_data,'table-insurance_cost_summary_report',
			array('customizable-table'),
			array(
				'id'=>'table-table-insurance_cost_summary_report',
				'data-last-order-identifier'=>'table-insurance_cost_summary_report',
			)); ?>

		</div>
	</div>
