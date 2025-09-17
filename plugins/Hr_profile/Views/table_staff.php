<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$has_permission_delete = hr_has_permission('hr_profile_can_delete_hr_records');
$has_permission_edit   = hr_has_permission('hr_profile_can_edit_hr_records');
$has_permission_create = hr_has_permission('hr_profile_can_create_hr_records');


$aColumns = [
	'nation',
	'first_name',
	'staff_identifi',
	'email',
	'team_manage',
	'gender',
	get_db_prefix().'hr_job_position.position_name',
    get_db_prefix().'roles.title',
	'status',
	'status_work',
	'1',
];
$sIndexColumn = 'id';
$sTable       = get_db_prefix().'users';
$join         = [
	'LEFT JOIN '.get_db_prefix().'roles ON '.get_db_prefix().'roles.id = '.get_db_prefix().'users.role_id',
	'LEFT JOIN '.get_db_prefix().'hr_job_position ON '.get_db_prefix().'hr_job_position.position_id = '.get_db_prefix().'users.job_position',
];

$where = array();

$where[] = 'AND '.get_db_prefix().'users.deleted = 0';
$where[] = 'AND '.get_db_prefix().'users.user_type = "staff"';

if(isset($dataPost['hr_profile_deparment'])){
    $department_id = $dataPost['hr_profile_deparment'];
}
if(isset($dataPost['status_work'])){
    $status_work = $dataPost['status_work'];
}
if(isset($dataPost['staff_role'])){
    $staff_role = $dataPost['staff_role'];
}
if(isset($dataPost['staff_teammanage'])){
    $staff_teammanage = $dataPost['staff_teammanage'];
}

if(isset($department_id) && strlen($department_id) > 0){
	$departmentgroup = $Hr_profile_model->get_staff_in_deparment($department_id);
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

if(isset($status_work)){
	$where_status = '';
	$status = $status_work;
	foreach ($status as $statues) {
		if($status != '')
		{
			if($where_status == ''){
				$where_status .= ' ('.get_db_prefix().'users.status_work in ("'.$statues.'")';
			}else{
				$where_status .= ' or '.get_db_prefix().'users.status_work in ("'.$statues.'")';
			}
		}
	}
	if($where_status != '')
	{
		$where_status .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_status);
		}else{
			array_push($where, $where_status);
		}
		
	}
}          



if(isset($staff_role)){
	$where_role = '';

	foreach ($staff_role as $staff_id) {
		if($staff_id != '')
		{
			if($where_role == ''){
				$where_role .= '( '.get_db_prefix().'users.job_position in ('.$staff_id.')';
			}else{
				$where_role .= ' or '.get_db_prefix().'users.job_position in ('.$staff_id.')';
			}
		}
	}

	if($where_role != '')
	{
		$where_role .= ' )';
		if($where_role != ''){
			array_push($where, 'AND '. $where_role);
		}else{
			array_push($where, $where_role);
		}

	}
	
}


if(isset($staff_teammanage) && strlen($staff_teammanage) > 0){
	$manages = $staff_teammanage;
	$where[] = '  AND id IN (select 
	id 
	from    (select * from '.get_db_prefix().'users as s
	order by s.team_manage, s.id) departments_sorted,
	(select @pv := '.$manages.') initialisation
	where   find_in_set(team_manage, @pv)
	and     length(@pv := concat(@pv, ",", id)) OR id ='.$manages.')';
}

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_records')){
	  //View own
	$staff_ids = $Hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.get_db_prefix().'users.id IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [
	'first_name',
	'email',
	'staff_identifi',
	'last_name',
	'is_admin',
	get_db_prefix().'users.id',
], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
			$_data = $aRow[strafter($aColumns[$i], 'as ')];
		} else {
			$_data = $aRow[$aColumns[$i]];
		}

		if($aColumns[$i] == 'staff_identifi'){
			$_data = $aRow['staff_identifi'];
		}elseif($aColumns[$i] == 'gender'){
			if(strlen($aRow['gender']) > 0){
				$_data = app_lang($aRow['gender']);

			}else{
				$_data = '';
			}
        
        }elseif($aColumns[$i] == 'status_work'){
			$_data = app_lang($aRow['status_work']);
		}         
		elseif ($aColumns[$i] == 'status') {
			$checked = '';
			if ($aRow['status'] == 'active') {
				$checked = 'checked';
			}
			$_data = '<div class=" form-check form-switch onoffswitch">
			<input type="checkbox" class="form-check-input " ' . (($aRow['id'] == get_staff_user_id1() || (is_admin($aRow['id']) || !hr_has_permission('hr_profile_can_edit_hr_records')) && !is_admin()) ? 'disabled' : '') . ' data-switch-url="' . site_url('hr_profile/change_staff_status').'" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . $checked . '>
			<label class="form-check-label onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
			</div>';

			$_data .= '<span class="hide">' . ($checked == 'checked' ? app_lang('is_active_export') : app_lang('is_not_active_export')) . '</span>';
		} elseif ($aColumns[$i] == 'first_name') {
			if (hr_has_permission('hr_profile_can_view_global_hr_records') || hr_has_permission('hr_profile_can_view_own_hr_records') || ($aRow['id'] == get_staff_user_id1()) ) {
				$_data  = '<a href="' .  site_url('hr_profile/staff_profile/' . $aRow['id']).'/general" class="dropdown-item">'.get_staff_image($aRow['id'], false).$aRow['first_name'] . ' ' . $aRow['last_name'].'</a>';
			}else{

				$_data = get_staff_image($aRow['id'], false);
				$_data .= $aRow['first_name'] . ' ' . $aRow['last_name'];
			}

		} elseif ($aColumns[$i] == 'email') {
			$_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
		} elseif ($aColumns[$i] == 'team_manage') {
			if($aRow['id'] != ''){
				$team = $Hr_profile_model->get_staff_departments($aRow['id']);
				$str = '';
				$j = 0;
				foreach ($team as $value) {
					$j++;
					$str .= '<span class="badge bg-success large mt-0">' . $value['title'] . '</span>&nbsp';
					if($j%2 == 0){
						$str .= '<br/>';
					}
					
				}
				$_data = $str;
			}
			else{
				$_data = '';
			}
		}elseif($aColumns[$i] == 'nation'){
			$_data = '<div class="checkbox"><input type="checkbox" class="form-check-input" value="' . $aRow['id'] . '"><label></label></div>';
		}elseif($aColumns[$i] == get_db_prefix().'roles.title'){
			if($aRow['is_admin']){
				$_data = app_lang('admin');
			}else{
				$_data = $aRow[get_db_prefix().'roles.title'];
			}
		}elseif($aColumns[$i] == '1'){
			/*options*/
			$view = '';
			if (hr_has_permission('hr_profile_can_view_global_hr_records') || hr_has_permission('hr_profile_can_view_own_hr_records') || ($aRow['id'] == get_staff_user_id1()) ) {
				$view = '<li role="presentation"><a href="' .  site_url('hr_profile/staff_profile/' . $aRow['id']).'/general" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
			}

			$edit = '';
			if (hr_has_permission('hr_profile_can_edit_hr_records') || ($aRow['id'] == get_staff_user_id1()) || is_admin()) {

				$edit = '<li role="presentation"><a href="' . site_url('hr_profile/new_member/' . $aRow['id'] ).'" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
			}

			$delete = '';
			if (hr_has_permission('hr_profile_can_delete_hr_records') || is_admin()) {

				$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_staff', "class" => 'dropdown-item' )). '</li>';;
			}

			$_data = '
			<span class="dropdown inline-block">
			<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
			<i data-feather="tool" class="icon-16"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit . $delete. '</ul>
			</span>';
			$row[] = $_data;
		}

		$row[] = $_data;
	}

	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
