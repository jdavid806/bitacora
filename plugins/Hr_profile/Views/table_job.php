<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'job_id',
	'job_name',
	'description',
	'1',

];

$sIndexColumn = 'job_id';
$sTable       = get_db_prefix() . 'hr_job_p';

$join = [];

$where  = [];
$filter = [];

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_job_description')){
 //View own
	$array_department = $Hr_profile_model->get_department_by_manager();

	if (count($array_department) == 0) {
		$where[] = 'AND 1=2';
	}
}

if(isset($dataPost['department_id'])){
	$department_id = $dataPost['department_id'];
}
if(isset($dataPost['job_position_id'])){
	$job_position_id = $dataPost['job_position_id'];
}


if(isset($department_id)){
	if(isset($job_position_id)){
		$job_p_id = $Hr_profile_model->get_department_from_position_department($job_position_id, true);

		if(strlen($job_p_id) != 0){
			$where[] = 'AND '.get_db_prefix().'hr_job_p.job_id IN ('.$job_p_id.')';

		}else{
			$where[] = 'AND '.get_db_prefix().'hr_job_p.job_id IN ("")';

		}

	}else{
		$job_p_id = $Hr_profile_model->get_department_from_position_department($department_id, false);

		if(strlen($job_p_id) != 0){
			$where[] = 'AND '.get_db_prefix().'hr_job_p.job_id IN ('.$job_p_id.')';

		}else{
			$where[] = 'AND '.get_db_prefix().'hr_job_p.job_id IN ("")';


		}
	}

}elseif(isset($job_position_id)){
	$job_p_id = $Hr_profile_model->get_department_from_position_department($job_position_id, true);

	if(strlen($job_p_id) != 0){
		$where[] = 'AND '.get_db_prefix().'hr_job_p.job_id IN ('.$job_p_id.')';
	}else{
		$where[] = 'AND '.get_db_prefix().'hr_job_p.job_id IN ("")';
	}
}



$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['job_id'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['job_id'] . '" class="form-check-input"><label></label></div>';
	$row[] = $aRow['job_id'];
	$subjectOutput = $aRow['job_name'];

	$row[] = $subjectOutput;

	/*get frist 100 character */
	if(strlen($aRow['description']) > 200){
		$pos=strpos($aRow['description'], ' ', 200);
		$description_sub = substr($aRow['description'],0,$pos ); 
	}else{
		$description_sub = $aRow['description'];
	}


	$row[] = $description_sub;

// get department
	$arr_department = $Hr_profile_model->get_department_from_job_p($aRow['job_id']);

	if(count($arr_department) > 0){

		$str = '';
		$j = 0;
		foreach ($arr_department as $key => $member_id) {
			$member   = hr_profile_get_department_name($member_id);

			$j++;
			$str .= '<span class="badge bg-success large mt-0">' . $member->title . '</span>';

			if($j%2 == 0){
				$str .= '<br/>';
			}

		}
		$_data = $str;
	}
	else{
		$_data = '';
	}

	if(isset($array_department)){

		if(count($arr_department) == 0){
		continue;
	}else{
		$check_dp=false;
		foreach ($arr_department as $dp_id) {
			if(in_array($dp_id, $array_department)){
				$check_dp = true;
			}
		}

		if($check_dp == false){
			continue;
		}
	}
	
}

$row[] = $_data;

/*options*/
$edit = '';
if (hr_has_permission('hr_profile_can_edit_job_description') || is_admin()) {
	$edit .= '<li role="presentation"><a href="#" onclick="edit_job_p(this,' . $aRow['job_id'] . '); return false" class="dropdown-item" data-name="'.$aRow['job_name'].'"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
}

$delete = '';
if (hr_has_permission('hr_profile_can_delete_job_description') || is_admin()) {

	$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['job_id'], "data-post-function" => 'delete_job_p', "class" => 'dropdown-item' )). '</li>';;
}


$_data = '
<span class="dropdown inline-block">
<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
<i data-feather="tool" class="icon-16"></i>
</button>
<ul class="dropdown-menu dropdown-menu-end" role="menu">'. $edit . $delete. '</ul>
</span>';
$row[] = $_data;


$output['aaData'][] = $row;
}
