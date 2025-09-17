<?php
$Hr_payroll_model = model("Hr_payroll\Models\Hr_payroll_model");

$aColumns = [
	'id',
];

$sIndexColumn = 'id';
$sTable       = get_db_prefix() . 'team';

$join = [
];

$where  = [];
$filter = [];

$where[] = 'AND '.get_db_prefix().'team.deleted = 0';


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
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
	$to_date = date('Y-m-t');
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

$query = '';
$staff_query = '';

if(isset($from_date) && isset($to_date)){

	$staff_query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
}else {
	$staff_query = '';
}



if(isset($staff_filter)){
	

	$where_staff = '';
	foreach ($staff_filter as $staffid) {

		if($staffid != '')
		{
			if($where_staff == ''){
				$where_staff .= ' (find_in_set('.$staffid.',members)';
			}else{
				$where_staff .= ' or find_in_set('.$staffid.',members)';
			}
		}
	}
	if($where_staff != '')
	{
		$where_staff .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_staff);
		}else{
			array_push($where, $where_staff);
		}
	}


	$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . implode(',', $staff_filter). ') and ';

}


if(isset($department_filter)){

	$where[] = 'AND '.get_db_prefix().'team.id IN (' . implode(',', $department_filter) . ')';

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

		}
	}

	if(isset($staff_id) && strlen($staff_id) > 0){
		$staff_query .= get_db_prefix() . 'hrp_payslip_details.staff_id in ('.$staff_id.') and ';
	}else{
		$staff_query .= get_db_prefix() . 'hrp_payslip_details.staff_id in (0) and ';
	}
}

$staff_query_trim = '';
if (($staff_query) && ($staff_query != '')) {
	$staff_query_trim = rtrim($staff_query, ' and');

}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['title'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

$rel_type = hrp_get_hr_profile_status();
$staff_insurance = $Hr_payroll_model->get_insurance_summary_report($staff_query_trim);

$temp_insurance = 0;

foreach ($rResult as $der_key => $aRow) {
	$row = [];

	$row[] = $aRow['title'];

	$staff_ids = [];
	$departments = $Hr_payroll_model->get_department_by_list_id($aRow['id']);
	foreach ($departments as $department) {
		if($department['members'] != '' && strlen($department['members']) > 0){
			$members = explode(",", $department['members']);

			foreach ($members as $member) {
			    if(strlen($member) > 0 && !in_array($member, $staff_ids)){
			    	$staff_ids[] = $member;
			    }
			}
		}
	}


	foreach ($staff_ids as $key => $value) {
		if (isset($staff_insurance[$value])) {
			$temp_insurance += $staff_insurance[$value];
		}
	}

	$row[] = to_decimal_format((float)$temp_insurance);
	$temp_insurance = 0;

	$output['aaData'][] = $row;
}