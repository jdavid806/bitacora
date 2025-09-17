<div class="card rounded-0">
	<div class="tab-title clearfix">
		<h4><?php echo app_lang('hr_pay_slips') ?></h4>
		<div class="title-button-group">
		</div>
		
		<div class="table-responsive">
			<?php
			$table_data = array(

				app_lang('id'),
				app_lang('ps_pay_slip_number'),
				app_lang('month'),
				app_lang('hrp_probation_contract'),
				app_lang('hrp_formal_contract'),
				app_lang('ps_gross_pay'),
				app_lang('ps_total_deductions'),
				app_lang('ps_income_tax_paye'),
				app_lang('ps_it_rebate_value'),
				app_lang('ps_commission_amount'),
				app_lang('ps_bonus_kpi'),
				app_lang('ps_total_insurance'), 
				app_lang('ps_net_pay'), 
				app_lang('ps_total_cost'), 

			);
			
			render_datatable1($table_data,'staff_payslip');
			?>

		</div>
	</div>

	<div id="contract_modal_wrapper"></div>
	<?php echo form_hidden('memberid',$user_id); ?>
	<?php echo form_hidden('member_view',1); ?>

	<?php require 'plugins/Hr_payroll/assets/js/employee_payslip/payslip_js.php';?>
