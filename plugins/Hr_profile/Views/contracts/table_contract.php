<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'staff_delegate',
	get_db_prefix() . 'hr_staff_contract.id_contract as id',
	'contract_code',
	'name_contract',
	'staff',
	'1',
	'start_valid',
	'end_valid',
	'contract_status',
	'sign_day',
];

$sIndexColumn = 'id_contract';
$sTable       = get_db_prefix() . 'hr_staff_contract';

$join = [
	'LEFT JOIN ' . get_db_prefix() . 'hr_staff_contract_type ON ' . get_db_prefix() . 'hr_staff_contract_type.id_contracttype = ' . get_db_prefix() . 'hr_staff_contract.name_contract',
	'LEFT JOIN ' . get_db_prefix() . 'users ON ' . get_db_prefix() . 'hr_staff_contract.staff = ' . get_db_prefix() . 'users.id',
];

$where  = [];
$filter = [];

if(isset($dataPost['memberid'])){
	$memberid = $dataPost['memberid'];
}
if(isset($dataPost['draft'])){
	$draft = $dataPost['draft'];
}
if(isset($dataPost['valid'])){
	$valid = $dataPost['valid'];
}
if(isset($dataPost['invalid'])){
	$invalid = $dataPost['invalid'];
}
if(isset($dataPost['hr_contract_is_about_to_expire'])){
	$hr_contract_is_about_to_expire = $dataPost['hr_contract_is_about_to_expire'];
}
if(isset($dataPost['hr_overdue_contract'])){
	$hr_overdue_contract = $dataPost['hr_overdue_contract'];
}
if(isset($dataPost['hrm_deparment'])){
	$hrm_deparment = $dataPost['hrm_deparment'];
}
if(isset($dataPost['hrm_staff'])){
	$hrm_staff = $dataPost['hrm_staff'];
}
if(isset($dataPost['validity_start_date'])){
	$validity_start_date = $dataPost['validity_start_date'];
}
if(isset($dataPost['validity_end_date'])){
	$validity_end_date = $dataPost['validity_end_date'];
}
if(isset($dataPost['member_view'])){
	$member_view = $dataPost['member_view'];
}

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_contract')){
	  //View own
	$staff_ids = $Hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.get_db_prefix().'hr_staff_contract.staff IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

if(isset($memberid)){

	$where_staff = '';
	$staffs = $memberid;
	if($staffs != '')
	{
		if($where_staff == ''){
			$where_staff .= 'AND staff = "'.$staffs. '"';
		}else{
			$where_staff .= 'or staff = "' .$staffs.'"';
		}
	}
	if($where_staff != '')
	{
		array_push($where, $where_staff);
	}
}

if (isset($draft)) {

	array_push($filter, 'AND contract_status = "draft"');
}

if (isset($valid)) {

	array_push($filter, 'AND contract_status = "valid"');
}

if (isset($invalid)) {

	array_push($filter, 'AND contract_status = "invalid"');
}

if (isset($hr_contract_is_about_to_expire)) {

	array_push($filter, 'AND end_valid <= "'.date('Y-m-d',strtotime('+7 day',strtotime(date('Y-m-d')))).'" AND end_valid >= "'.date('Y-m-d').'" AND contract_status = "valid"');
}

if (isset($hr_overdue_contract)) {

	array_push($filter, 'AND end_valid < "'.date('Y-m-d').'" AND contract_status = "valid"');
}


if(isset($hrm_deparment) && strlen($hrm_deparment) > 0){

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
			$where[] = 'AND '.get_db_prefix().'hr_staff_contract.staff IN ('.$staff_id.')';
		}else{
			$where[] = 'AND 1 = 2';
		}
	}
}


if(isset($hrm_staff)){

	$staff_id = $hrm_staff;
	$where_staff = '';
	foreach ($staff_id as $staffid) {

		if($staffid != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.get_db_prefix().'users.id in ('.$staffid.')';
			}else{
				$where_staff .= ' or '.get_db_prefix().'users.id in ('.$staffid.')';
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
}

if(isset($validity_start_date) && $validity_start_date != ''){

	$start_date = to_sql_date1($validity_start_date);

	array_push($where, 'AND date_format(start_valid, "%Y-%m-%d") >= "'.$start_date.'"');
}

if(isset($validity_end_date) && $validity_end_date != ''){

	$end_date = to_sql_date1($validity_end_date);

	array_push($where, 'AND date_format(end_valid, "%Y-%m-%d") <= "'.$end_date.'"');

}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'hr_staff_contract.id_contract', 'name_contracttype', 'first_name', 'duration', 'unit', get_db_prefix() . 'hr_staff_contract.id_contract as id', 'last_name', 'signature'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox"  class="form-check-input" value="' . $aRow['id'] . '"><label></label></div>';
	$row[] = $aRow['id'];

	if (hr_has_permission('hr_profile_can_view_global_hr_contract') || hr_has_permission('hr_profile_can_view_own_hr_contract') || ($login_user_id = $aRow['staff']) || is_admin()) {
		$subjectOutput = '<a href="' . get_uri('hr_profile/view_staff_contract/' . $aRow['id'] ).'" >' . $aRow['contract_code'] . '</a>';
	}else{
		$subjectOutput = $aRow['contract_code'];
	}

	$row[] = $subjectOutput;

	$row[] = $aRow['name_contracttype'];
	$row[] = ' <a href="' . get_uri('hr_profile/staff_profile/' . $aRow['staff'].'/staff_contract') . '">' . $aRow['first_name'] .' '.  $aRow['last_name'] .'</a>';

	$team = $Hr_profile_model->get_staff_departments($aRow['staff']);
	$str = '';
	$j = 0;
	foreach ($team as $value) {
		$j++;
		$str .= '<span class="badge bg-success large mt-0">' . $value['title'] . '</span>&nbsp';
		if($j%2 == 0){
			$str .= '<br/>';
		}

	}
	$row[]  = $str;


	$row[] = format_to_date($aRow['start_valid'], false);

	$row[] = format_to_date($aRow['end_valid'], false);

	if($aRow['contract_status'] == 'draft' ){
		$row[] = ' <span class="badge bg-warning large mt-0" > '.app_lang('hr_hr_draft').' </span>';
	}elseif($aRow['contract_status'] == 'valid'){
		$row[] = ' <span class="badge bg-success large mt-0"> '.app_lang('hr_hr_valid').' </span>';
	}elseif($aRow['contract_status'] == 'invalid'){
		$row[] = ' <span class="badge bg-danger large mt-0"> '.app_lang('hr_hr_expired').' </span>';
	}elseif($aRow['contract_status'] == 'finish'){
		$row[] = ' <span class="badge bg-primary large mt-0"> '.app_lang('hr_hr_finish').' </span>';
	}else{
		$row[] = '';
	}
	
	$row[] = format_to_date($aRow['sign_day'], false);

	$view = '';
	$edit = '';
	$delete = '';
	if(isset($member_view) && $member_view == '1'){
		$view .= '<a href="#" onclick="member_view_contract(' . $aRow['id'] . ');return false;">' . app_lang('hr_view') .' </a>';
		if($aRow['signature'] != ''){
			$view .= ' | <a href="' . get_uri('hr_profile/contract_sign/'.$aRow['id']).'" >' . app_lang('hr_view_detail') .' </a>';
		}
	}else{

		if(is_admin() || hr_has_permission('hr_profile_can_view_hr_contract')){	

			$view = '<li role="presentation"><a href="'.get_uri('hr_profile/view_staff_contract/' . $aRow['id']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
		}

		if (hr_has_permission('hr_profile_can_edit_hr_contract') || is_admin()) {
			$edit = '<li role="presentation"><a href="'.get_uri('hr_profile/contract/' . $aRow['id']).'"  class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('hr_edit') . '</a></li>';
		}

		if (hr_has_permission('hr_profile_can_delete_hr_contract') || is_admin()) {
			$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_contract', "class" => 'dropdown-item' )). '</li>';
		}
	}

	$_data = '';
	if(strlen($view) > 0 ||strlen($edit) > 0 ||strlen($delete) > 0 ){
		$_data = '
		<span class="dropdown inline-block">
		<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
		<i data-feather="tool" class="icon-16"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
		</span>';
	}

	$row[] = $_data;


	$row['DT_RowClass'] = 'has-row-options';
	
	$output['aaData'][] = $row;
}
