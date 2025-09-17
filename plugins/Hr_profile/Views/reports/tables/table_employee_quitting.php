<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'id',
	'staff_identifi',
	'first_name',
	'job_position',
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

if (isset($months_report) && $months_report == 'this_month') {
	$from_date = date('Y-m-01') . ' 00:00:00';
	$to_date = date('Y-m-t') . ' 23:59:59';
}
if (isset($months_report) && $months_report == '1') {
	$from_date = date('Y-m-01', strtotime('first day of last month')) . ' 00:00:00';
	$to_date = date('Y-m-t', strtotime('last day of last month')) . ' 23:59:59';
}
if (isset($months_report) && $months_report == 'this_year') {
	$from_date = date('Y-m-d', strtotime(date('Y-01-01'))) . ' 00:00:00';
	$to_date = date('Y-m-d', strtotime(date('Y-12-31'))) . ' 23:59:59';
}
if (isset($months_report) && $months_report == 'last_year') {
	$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) . ' 00:00:00';
	$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . ' 23:59:59';
}

if (isset($months_report) && $months_report == '3') {
	$months_report--;
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
	$to_date = date('Y-m-t') . ' 23:59:59';
}
if (isset($months_report) && $months_report == '6') {
	$months_report--;
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
	$to_date = date('Y-m-t') . ' 23:59:59';

}
if (isset($months_report) && $months_report == '12') {
	$months_report--;
	$from_date = date('Y-m-01', strtotime("-$months_report MONTH")) . ' 00:00:00';
	$to_date = date('Y-m-t') . ' 23:59:59';

}
if (isset($months_report) && $months_report == 'custom') {
	$from_date = to_sql_date1($dataPost['report_from']) . ' 00:00:00';
	$to_date = to_sql_date1($dataPost['report_to']) . ' 23:59:59';
}


if(isset($from_date) && isset($to_date)){

	array_push($where, 'AND id IN (SELECT staffid FROM ' . db_prefix() . 'hr_list_staff_quitting_work where dateoff >= \'' . $from_date . '\' and dateoff <= \'' . $to_date . '\' AND ' . db_prefix() . 'hr_list_staff_quitting_work.approval = "approved")');
}else{
	array_push($where, 'AND id IN (SELECT staffid FROM ' . db_prefix() . 'hr_list_staff_quitting_work where  ' . db_prefix() . 'hr_list_staff_quitting_work.approval = "approved")');
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

	$row[] = format_to_datetime($aRow['created_at'], false);

	$data_quiting = $Hr_profile_model->get_list_quiting_work($aRow['id']);
	$date_off = '';
	if (isset($data_quiting)) {
		$date_off = format_to_date($data_quiting->dateoff);
	}
	$row[] = $date_off;

	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
