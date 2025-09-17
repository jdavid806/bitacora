<?php

$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");


$aColumns = [
	'position_id',
	'position_code',
	'position_name',
	'job_position_description',
	'department_id',
	'job_p_id',

];

$sIndexColumn = 'position_id';
$sTable = get_db_prefix() . 'hr_job_position';

$join = [];

$where = [];
$filter = [];

/*load deparment by manager*/
if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_job_description')) {
	/*View own*/
	$array_department = $Hr_profile_model->get_department_by_manager();
	if (count($array_department) > 0) {
		$department_m_where = '';
		foreach ($array_department as $department_id_m) {
			if ($department_id_m != '') {

				if ($department_m_where == '') {
					$department_m_where .= 'AND (find_in_set(' . $department_id_m . ', ' . get_db_prefix() . 'hr_job_position.department_id) ';

				} else {
					$department_m_where .= ' OR find_in_set(' . $department_id_m . ', ' . get_db_prefix() . 'hr_job_position.department_id) ';
				}

			}
		}

		if ($department_m_where != '') {
			$department_m_where .= ')';

			$where[] = $department_m_where;
		}

	} else {
		$where[] = 'AND 1=2';
	}

}

if(isset($dataPost['department_id'])){
	$department_ids = $dataPost['department_id'];
}
if(isset($dataPost['job_p_id'])){
	$job_p_ids = $dataPost['job_p_id'];
}

if (isset($department_ids)) {

	$department_where = '';
	foreach ($department_ids as $department_id) {
		if ($department_id != '') {

			if ($department_where == '') {
				$department_where .= 'AND (find_in_set(' . $department_id . ', ' . get_db_prefix() . 'hr_job_position.department_id) ';

			} else {
				$department_where .= ' OR find_in_set(' . $department_id . ', ' . get_db_prefix() . 'hr_job_position.department_id) ';
			}

		}
	}

	if ($department_where != '') {
		$department_where .= ')';

		$where[] = $department_where;
	}

}

if (isset($job_p_ids)) {
	$job_p_where = '';
	foreach ($job_p_ids as $job_p_id) {
		if ($job_p_id != '') {

			if ($job_p_where == '') {
				$job_p_where .= 'AND (( ' . get_db_prefix() . 'hr_job_position.job_p_id) = ' . $job_p_id;

			} else {
				$job_p_where .= ' OR (' . get_db_prefix() . 'hr_job_position.job_p_id = ' . $job_p_id . ') ';
			}

		}
	}

	if ($job_p_where != '') {
		$job_p_where .= ')';

		$where[] = $job_p_where;
	}

}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['position_id', 'department_id'], '', [], $dataPost);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['position_id'] . '" class="form-check-input"><label></label></div>';
	$row[] = $aRow['position_id'];

	$subjectOutput = '';

	if (hr_has_permission('hr_profile_can_view_global_job_description') || hr_has_permission('hr_profile_can_view_own_job_description') || is_admin()) {

		$subjectOutput .= '<a href="' . site_url('hr_profile/job_position_view_edit/' . $aRow['position_id']) . '">' . $aRow['position_code'] . '</a>';
	}else{
		$subjectOutput .= '<a href="#">' . $aRow['position_code'] . '</a>';
	}

	$row[] = $subjectOutput;
	$row[] = $aRow['position_name'];

	/*get frist 100 character */
	if (strlen($aRow['job_position_description']) > 200) {
		$pos = strpos($aRow['job_position_description'], ' ', 200);
		$description_sub = substr($aRow['job_position_description'], 0, $pos);
	} else {
		$description_sub = $aRow['job_position_description'];
	}

	$row[] = $description_sub;

	/*get department*/
	if ($aRow['department_id'] != null && $aRow['department_id'] != '') {
		$members = explode(',', $aRow['department_id']);
		$str = '';
		$j = 0;
		foreach ($members as $key => $member_id) {
			$member = hr_profile_get_department_name($member_id);

			$j++;
			$str .= '<span class="badge bg-success large mt-0">' . $member->title . '</span>&nbsp';

			if ($j % 2 == 0) {
				$str .= '<br><br/>';
			}

		}
		$_data = $str;
	} else {
		$_data = '';
	}

	$row[] = $_data;

	/*get parent name*/
	$job_p = $Hr_profile_model->get_job_p($aRow['job_p_id']);
	$row[] = isset($job_p) ? $job_p->job_name : '';

	/*options*/
	$view = '';
	if (hr_has_permission('hr_profile_can_view_global_job_description') || hr_has_permission('hr_profile_can_view_own_job_description') || is_admin()) {
		$view = '<li role="presentation"><a href="' . site_url('hr_profile/job_position_view_edit/' . $aRow['position_id'] ).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
	}

	$edit = '';
	if (hr_has_permission('hr_profile_can_edit_job_description') || is_admin()) {
		$edit = '<li role="presentation">'.modal_anchor(get_uri("hr_profile/new_job_position_modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'),array("class" => "edit", "title" => app_lang('hr_edit_job_position'), "data-post-id" => $aRow['position_id'], "class" => 'dropdown-item')). '</li>';

	}

	$delete = '';
	if (hr_has_permission('hr_profile_can_delete_job_description') || is_admin()) {

		$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['position_id'], "data-post-function" => 'delete_job_position', "class" => 'dropdown-item' )). '</li>';
	}

	$_data = '
	<span class="dropdown inline-block">
	<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
	<i data-feather="tool" class="icon-16"></i>
	</button>
	<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit . $delete. '</ul>
	</span>';
	$row[] = $_data;


	$output['aaData'][] = $row;
}
