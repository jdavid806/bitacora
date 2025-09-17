<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'id',
	'first_name',
	'staff_identifi',
	'id',
	'id',
	'id',
	'id',
	'id',
];

$sIndexColumn = 'id';
$sTable       = get_db_prefix() . 'users';

$join = [];

$where  = [];
$filter = [];
$where[] = 'AND '.get_db_prefix().'users.deleted = 0';
$where[] = 'AND '.get_db_prefix().'users.user_type = "staff"';


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
$from_date = date('Y-m-d', strtotime('1997-01-01'));
	$to_date =  date('Y-m-01', strtotime("+100 MONTH"));

				
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
	$from_date = to_sql_date1($dataPost['report_from']);
	$to_date = to_sql_date1($dataPost['report_to']);
}


if(isset($position_filter)){
	$where[] = 'AND '.get_db_prefix().'users.job_position IN (' . implode(', ', $position_filter) . ')';
}

if(isset($staff_filter)){
	$where[] = 'AND '.get_db_prefix().'users.id IN (' . implode(', ', $staff_filter) . ')';
}


if(isset($department_filter)){

	foreach ($department_filter as $hrm_deparment) {
		$departmentgroup = $Hr_profile_model->get_staff_in_deparment($hrm_deparment);
		if (count($departmentgroup) > 0) {
			$staff_id = '';
			$list_department = $Hr_profile_model->get_department_by_list_id(implode(",", $departmentgroup));
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
}


$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['first_name','last_name','staff_identifi','job_position','created_at','email', get_db_prefix().'users.id as id'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['id'];
	$row[] = $aRow['staff_identifi'];
	$row[] = $aRow['first_name'] . ' ' . $aRow['last_name'];
	$position = $Hr_profile_model->get_job_position($aRow['job_position']);
	$name_position = '';
	if (isset($position) && !is_array($position)) {
		$name_position = $position->position_name;
	}
	$row[] = $name_position;

	$department = $Hr_profile_model->getdepartment_name($aRow['id']);
	$name_department = '';
	if (isset($department)) {
		$name_department = $department->name;
	}
	$row[] = $name_department;

	$has_change = 0;
	$old_salary = 0;
	$new_salary = 0;
	$date_effect = '1970-01-01 00:00:00';
	$list_contract_staff = $Hr_profile_model->get_list_contract_detail_staff($aRow['id']);

	if ($list_contract_staff) {
		$has_change = 1;
		$old_salary = $list_contract_staff['old_salary'];
		$new_salary = $list_contract_staff['new_salary'];
		$date_effect = $list_contract_staff['date_effect'];

	}

	$strtotime_from_date = strtotime($from_date);
	$strtotime_to_date = strtotime($to_date);
	$strtotime_date_effect = strtotime($date_effect);

	if (($strtotime_date_effect >= $strtotime_from_date) && ($strtotime_date_effect <= $strtotime_to_date)) {

		$row[] = format_to_date($date_effect);
		$row[] = to_decimal_format($old_salary);
		$row[] = to_decimal_format($new_salary);
		if ($has_change == 1) {
			$row['DT_RowClass'] = 'has-row-options';
			$output['aaData'][] = $row;
		}
	}



}
