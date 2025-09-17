<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'resultsetid',
	'staff_id',
	'trainingid',
	'date',
];


$sIndexColumn = 'resultsetid';
$sTable       = get_db_prefix() . 'hr_p_t_surveyresultsets';

$join = [
	'LEFT JOIN ' . get_db_prefix() . 'hr_position_training ON ' . get_db_prefix() . 'hr_p_t_surveyresultsets.trainingid = ' . get_db_prefix() . 'hr_position_training.training_id',
];

$where =[];

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_training')){
	  //View own
	$staff_ids = $Hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.get_db_prefix().'hr_p_t_surveyresultsets.staff_id IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

if(isset($dataPost['training_program'])){
	$training_program = $dataPost['training_program'];
}
if(isset($dataPost['hr_staff'])){
	$staff_id = $dataPost['hr_staff'];
}
if(isset($dataPost['training_library'])){
	$training_libraries = $dataPost['training_library'];
}

if(isset($training_program)){
//get staff from training program
	$str_staff = $Hr_profile_model->get_staff_from_training_program($training_program);
	if(strlen($str_staff) > 0){
		$where[] = 'AND ('  .get_db_prefix().'hr_p_t_surveyresultsets.staff_id IN ('.$str_staff.') )';
	}else{
		$where[] = 'AND (1=3 )';
	}

}

if(isset($staff_id)){
	$where_staff = '';
	foreach ($staff_id as $staffid) {

		if($staffid != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.get_db_prefix().'hr_p_t_surveyresultsets.staff_id in ('.$staffid.')';
			}else{
				$where_staff .= ' or '.get_db_prefix().'hr_p_t_surveyresultsets.staff_id in ('.$staffid.')';
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

if(isset($training_libraries)){
	$where_staff = '';
	foreach ($training_libraries as $training_library) {

		if($training_library != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.get_db_prefix().'hr_p_t_surveyresultsets.trainingid in ('.$training_library.')';
			}else{
				$where_staff .= ' or '.get_db_prefix().'hr_p_t_surveyresultsets.trainingid in ('.$training_library.')';
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


$result       = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix() . 'hr_position_training.subject',get_db_prefix() . 'hr_position_training.training_type', get_db_prefix() . 'hr_position_training.hash' , get_db_prefix() . 'hr_position_training.training_id' ], '', [], $dataPost);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

	$position_training = $Hr_profile_model->get_board_mark_form($aRow['resultsetid']);

	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" class="form-check-input" value="' . $aRow['resultsetid'] . '"><label></label></div>';
	$row[] = $aRow['resultsetid'];

	$subject = get_staff_full_name1($aRow['staff_id']);

	if (hr_has_permission('hr_profile_can_delete_hr_training')) {
		$subject .= ' <a href="' . admin_url('hr_profile/delete_job_position_training_process/' . $aRow['resultsetid']) . '" class="text-danger _delete hide">' . app_lang('delete') . '</a>';
	}
	
	$row[] = $subject;

	$row[] =$aRow['subject'];
	$row[] = get_type_of_training_by_id($aRow['training_type']);
	$row[] = format_to_datetime($aRow['date'], false);

	$view = '';
	$edit = '';
	$delete = '';
	/*options*/

	if (hr_has_permission('hr_profile_can_view_global_hr_training') || hr_has_permission('hr_profile_can_view_own_hr_training')) {

		$view = '<li role="presentation"><a href="'.get_uri('hr_profile/view_staff_training_result/'.$aRow['staff_id'].'/'.$aRow['resultsetid'].'/' . $aRow['training_id'] . '/' . $aRow['hash']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
	}


	$_data = '
	<span class="dropdown inline-block">
	<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
	<i data-feather="tool" class="icon-16"></i>
	</button>
	<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
	</span>';

	$row[] = $_data;


	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
