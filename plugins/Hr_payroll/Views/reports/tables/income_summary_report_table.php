<?php
$Hr_payroll_model = model("Hr_payroll\Models\Hr_payroll_model");

$aColumns = [
	'id',
];

$sIndexColumn = 'id';
$sTable       = get_db_prefix() . 'users';

$join = [
];

$where  = [];
$filter = [];

$where[] = 'AND '.get_db_prefix().'users.deleted = 0 AND status = "active" AND user_type = "staff"';


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
	$where[] = 'AND '.get_db_prefix().'users.id IN (' . implode(',', $staff_filter) . ')';

	$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . implode(',', $staff_filter). ') and ';

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
				$where[] = 'AND '.get_db_prefix().'users.id IN ('.$staff_id.')';
			}else{
				$where[] = 'AND 1 = 2';
			}
		}
	}

	if(isset($staff_id) && strlen($staff_id) > 0){
		$staff_query .= get_db_prefix() . 'hrp_payslip_details.staff_id IN ('.$staff_id.') and ';
	}else{
		$staff_query .= get_db_prefix() . 'hrp_payslip_details.staff_id IN (0) and ';
	}
}

$staff_query_trim = '';
if (($staff_query) && ($staff_query != '')) {
	$staff_query_trim = rtrim($staff_query, ' and');

}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['first_name', 'last_name', 'deleted', 'status', 'user_type'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

$rel_type = hrp_get_hr_profile_status();

$staff_income = $Hr_payroll_model->get_income_summary_report($staff_query_trim);
$staffs_data = [];
$staffs = $Hr_payroll_model->get_staff_timekeeping_applicable_object();
foreach ($staffs as $value) {
	$staffs_data[$value['staffid']] = $value;
}

$temp = 0;

foreach ($rResult as $staff_key => $aRow) {
	$row = [];

	$list_department = $Hr_payroll_model->getdepartment_name($aRow['id']);

	if ($rel_type == 'hr_records') {
		if (isset($staffs_data[$aRow['id']])) {
			$row[] = $staffs_data[$aRow['id']]['staff_identifi'];
		} else {
			$row[] = '';
		}
	} else {
		$row[] = $Hr_payroll_model->hrp_format_code('EXS', $aRow['id'], 5);
	}

	$row[] = $aRow['first_name'] . ' ' . $aRow['last_name'];

	$row[] = $list_department->name;

	if (isset($staff_income[$aRow['id']]['01'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['01']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['02'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['02']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['03'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['03']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['04'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['04']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['05'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['05']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['06'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['06']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['07'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['07']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['08'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['08']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['09'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['09']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['10'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['10']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['11'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['11']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if (isset($staff_income[$aRow['id']]['12'])) {
		$row[] = to_decimal_format($staff_income[$aRow['id']]['12']);
		$temp++;
	} else {
		$row[] = 0;
	}

	if ($temp != 0) {
		if (isset($staff_income[$aRow['id']]['average_income'])) {

			$row[] = to_decimal_format($staff_income[$aRow['id']]['average_income'] / $temp);
		} else {
			$row[] = 0;
		}
	} else {
		$row[] = 0;
	}

	$temp = 0;
	$output['aaData'][] = $row;
}
