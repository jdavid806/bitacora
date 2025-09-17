<?php


$aColumns = [
	get_db_prefix().'hrp_payslip_details.month',
	'pay_slip_number',
	'gross_pay',
	'total_deductions',
	'income_tax_paye',
	'it_rebate_value',
	'commission_amount',
	'bonus_kpi',
	'total_insurance',
	'net_pay',
	'total_cost',
];

$sIndexColumn = 'id';
$sTable       = get_db_prefix() . 'hrp_payslip_details';

$join = [
	'LEFT JOIN ' . get_db_prefix() . 'hrp_payslips ON ' . get_db_prefix() . 'hrp_payslip_details.payslip_id = ' . get_db_prefix() . 'hrp_payslips.id',
];

$where  = [];
$filter = [];

if(isset($dataPost['memberid'])){
	$memberid = $dataPost['memberid'];
}

if(isset($memberid)){
	$where_staff = '';
	$staffs = $memberid;
	if($staffs != '')
	{
		if($where_staff == ''){
			$where_staff .= ' where '.get_db_prefix().'hrp_payslip_details.staff_id = "'.$staffs. '"';
		}else{
			$where_staff .= ' or '.get_db_prefix().'hrp_payslip_details.staff_id = "' .$staffs.'"';
		}
	}
	if($where_staff != '')
	{
		array_push($where, $where_staff);
	}
}
array_push($where, 'AND '.get_db_prefix().'hrp_payslips.payslip_status = "payslip_closing"');



$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix().'hrp_payslip_details.id', get_db_prefix().'hrp_payslip_details.json_data', get_db_prefix().'hrp_payslip_details.actual_workday_probation'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['id'];

	if (has_permission('hrm_contract', '', 'view') || is_admin()) {

		$subjectOutput = modal_anchor(get_uri("hr_payroll/staff_payslip_modal_form"), $aRow['pay_slip_number'], array("class" => "edit", "title" => app_lang('payslip_detail'), "data-post-id" => $aRow['id']));
	}else{
		$subjectOutput = $aRow['pay_slip_number'];
	}

	if(1 == 2){

		$subjectOutput .= '<div class="row-options">';
		$subjectOutput .= '<a href="#" onclick="member_view_payslip(' . $aRow['id'] . ');return false;">' . app_lang('hr_view') .' </a>';
		$subjectOutput .= '| <a href="'.admin_url('hr_payroll/employee_export_pdf/'.$aRow['id'].'?output_type=I').'" target="_blank">' . app_lang('view_pdf_in_new_window') .' </a>';
		$subjectOutput .= '</div>';
	}


	$row[] = $subjectOutput;

	$row[] = date('m-Y',strtotime($aRow[get_db_prefix().'hrp_payslip_details.month']));

	$hrp_payslip_salary_allowance = hrp_payslip_json_data_decode($aRow['json_data']);


	if( $hrp_payslip_salary_allowance['integration_hr']){
		//probation contract
		$probation_salary ='';
		$probation_salary .= app_lang('hrp_salary').': '.to_decimal_format((float)$hrp_payslip_salary_allowance['probation_salary']).'<br>';
		$probation_salary .= app_lang('hrp_allowance').': '.to_decimal_format((float)$hrp_payslip_salary_allowance['probation_allowance']);

		$row[] = $probation_salary;

		//formal contract
		$formal_salary ='';
		$formal_salary .= app_lang('hrp_salary').': '.to_decimal_format((float)$hrp_payslip_salary_allowance['formal_salary']).'<br>';
		$formal_salary .= app_lang('hrp_allowance').': '.to_decimal_format((float)$hrp_payslip_salary_allowance['formal_allowance']);

		$row[] = $formal_salary;

	}else{

		$probation_salary ='';
		$probation_salary .= app_lang('hrp_salary').' + '.app_lang('hrp_allowance').': '.to_decimal_format((float)$hrp_payslip_salary_allowance['probation_salary']).'<br>';

		$row[] = $probation_salary;

		//formal contract
		$formal_salary ='';
		$formal_salary .= app_lang('hrp_salary').' + '.app_lang('hrp_allowance').': '.to_decimal_format((float)$hrp_payslip_salary_allowance['formal_salary']).'<br>';

		$row[] = $formal_salary;

	}


	$row[] = to_decimal_format((float)$aRow['gross_pay']);
	$row[] = to_decimal_format((float)$aRow['total_deductions']);
	$row[] = to_decimal_format((float)$aRow['income_tax_paye']);
	$row[] = to_decimal_format((float)$aRow['it_rebate_value'],'');
	$row[] = to_decimal_format((float)$aRow['commission_amount']);
	$row[] = to_decimal_format((float)$aRow['bonus_kpi']);
	$row[] = to_decimal_format((float)$aRow['total_insurance']);
	$row[] = to_decimal_format((float)$aRow['net_pay']);
	$row[] = to_decimal_format((float)$aRow['total_cost']);

	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
