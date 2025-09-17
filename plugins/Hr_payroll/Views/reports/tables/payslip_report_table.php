<?php
$Hr_payroll_model = model("Hr_payroll\Models\Hr_payroll_model");

$aColumns = [
	'month',
	'pay_slip_number',
	'employee_name',
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

$where[] = 'AND '.get_db_prefix().'hrp_payslips.payslip_status = "payslip_closing"';


if(isset($dataPost['months_filter'])){
	$months_report = $dataPost['months_filter'];
}
if(isset($dataPost['position_filter'])){
	$position_filter = $dataPost['position_filter'];
}
if(isset($dataPost['department_filter'])){
	$department_filter = $dataPost['department_filter'];
}
if(isset($dataPost['staff_filter'])){
	$staff_filter = $dataPost['staff_filter'];
}
if(isset($dataPost['report_from'])){
	$report_from = $dataPost['report_from'];
}
if(isset($dataPost['report_to'])){
	$report_to = $dataPost['report_to'];
}

if (isset($months_report) && $months_report == 'this_month') {
	$from_date = date('Y-m-01');
	$to_date = date('Y-m-t');
}
if (isset($months_report) && $months_report == '1') {
	$from_date = date('Y-m-01', strtotime('first day of last month'));
	$to_date = date('Y-m-t', strtotime('last day of last month'));
}
if (isset($months_report) && $months_report == 'this_year') {
	$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
	$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
}
if (isset($months_report) && $months_report == 'last_year') {
	$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
	$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
}

if (isset($months_report) && $months_report == '3') {
	$months_report--;
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
	$to_date = date('Y-m-t') . ' 23:59:59';
}
if (isset($months_report) && $months_report == '6') {
	$months_report--;
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
	$to_date = date('Y-m-t');

}
if (isset($months_report) && $months_report == '12') {
	$months_report--;
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
	$to_date = date('Y-m-t');

}
if (isset($months_report) && $months_report == 'custom') {
	$from_date = to_sql_date($report_from);
	$to_date = to_sql_date($report_to);
}


if(isset($from_date) && isset($to_date)){

	array_push($where, 'AND month >= "' . $from_date . '" and month <= "' . $to_date . '"');
}


if(isset($staff_filter)){
	$where[] = 'AND '.get_db_prefix().'hrp_payslip_details.staff_id IN (' . implode(',', $staff_filter) . ')';
}


if(isset($department_filter)){

	foreach ($department_filter as $hrm_deparment) {
		$departmentgroup = $Hr_payroll_model->get_staff_in_deparment($hrm_deparment);
		if (count($departmentgroup) > 0) {
			$staff_id = '';
			$list_department = $Hr_payroll_model->get_department_by_list_id(implode(",", $departmentgroup));
			foreach ($list_department as $value) {
				if($value['members'] != '' && strlen($value['members']) > 0){
					if(strlen($staff_id) > 0){
						$staff_id .= ','.$value['members'];
					}else{
						$staff_id .= $value['members'];
					}
				}
			}

			if(strlen($staff_id ) > 0){
				$where[] = 'AND '.get_db_prefix().'hrp_payslip_details.staff_id IN ('.$staff_id.')';
			}else{
				$where[] = 'AND 1 = 2';
			}
		}
	}
}


$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'hrp_payslip_details.id',
	get_db_prefix() . 'hrp_payslip_details.month',], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = $aRow['id'];
	$row[] = $aRow['month'];
	$row[] = $aRow['pay_slip_number'];
	$row[] = $aRow['employee_name'];
	$row[] = to_decimal_format($aRow['gross_pay']);
	$row[] = to_decimal_format($aRow['total_deductions']);
	$row[] = to_decimal_format($aRow['income_tax_paye']);
	$row[] = to_decimal_format($aRow['it_rebate_value']);
	$row[] = to_decimal_format($aRow['commission_amount']);
	$row[] = to_decimal_format($aRow['bonus_kpi']);
	$row[] = to_decimal_format($aRow['total_insurance']);
	$row[] = to_decimal_format($aRow['net_pay']);
	$row[] = to_decimal_format($aRow['total_cost']);

	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
