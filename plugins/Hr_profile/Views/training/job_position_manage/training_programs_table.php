<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'training_process_id',
	'training_name',
	'training_type',
	'description',
	'mint_point',
	'date_add',
];


$sIndexColumn = 'training_process_id';
$sTable       = db_prefix() . 'hr_jp_interview_training';

$join =[];
$where =[];

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_training')){
      //View own
	$array_staff = $Hr_profile_model->get_staff_by_manager();

	if (count($array_staff) == 0) {
		$where[] = 'AND 1=2';
	}
}

$result       = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['job_position_id', 'position_training_id', 'additional_training', 'staff_id', 'time_to_start', 'time_to_end'], '', [], $dataPost);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" class="form-check-input" value="' . $aRow['training_process_id'] . '"><label></label></div>';
	$row[] = $aRow['training_process_id'];

	if (hr_has_permission('hr_profile_can_view_global_hr_training') || hr_has_permission('hr_profile_can_view_own_hr_training')) {
		$subject = '<a href="' . get_uri('hr_profile/view_training_program/' . $aRow['training_process_id']) . '" >' .  $aRow['training_name']  . '</a>';

	}else{
		$subject = $aRow['training_name'];
	}

	$row[] = $subject;

	$row[] = get_type_of_training_by_id($aRow['training_type']);

	/*get frist 100 character */
	if(strlen($aRow['description']) > 300){
		$pos=strpos($aRow['description'], ' ', 300);
		$description_sub = substr($aRow['description'],0,$pos ); 
	}else{
		$description_sub = $aRow['description'];
	}

	$row[] = $description_sub;
	$row[] = $aRow['mint_point'];
	$row[] = format_to_datetime($aRow['date_add'], false);

	$view = '';
	$edit = '';
	$delete = '';
	/*options*/
	if (hr_has_permission('hr_profile_can_view_global_hr_training') || hr_has_permission('hr_profile_can_view_own_hr_training')) {
		$view = '<li role="presentation"><a href="'. get_uri('hr_profile/view_training_program/' . $aRow['training_process_id']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
	}

	if (hr_has_permission('hr_profile_can_edit_hr_training')) {
		$edit = '<li role="presentation"><a href="#" onclick="edit_training_process(this,' . $aRow['training_process_id'] . ', '.$aRow['training_process_id'].');return false;"  data-id_training= "'.$aRow['training_process_id'].'" data-training_name= "'.$aRow['training_name'].'"  data-job_position_training_type= "'.$aRow['training_type'].'" data-job_position_mint_point= "'.$aRow['mint_point'].'"  data-job_position_training_id= "'.$aRow['position_training_id'].'" data-job_position_id= "'.$aRow['job_position_id'].'" data-additional_training= "'.$aRow['additional_training'].'" data-staff_id= "'.$aRow['staff_id'].'" data-time_to_start= "'.format_to_date($aRow['time_to_start'], false).'" data-time_to_end= "'.format_to_date($aRow['time_to_end'], false).'" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('hr_edit') . '</a></li>';
	}

	if (hr_has_permission('hr_profile_can_delete_hr_training')) {
		$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['training_process_id'], "data-post-function" => 'delete_job_position_training_process', "class" => 'dropdown-item' )). '</li>';
	}


	$_data = '
	<span class="dropdown inline-block">
	<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
	<i data-feather="tool" class="icon-16"></i>
	</button>
	<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
	</span>';
	$row[] = $_data;


		//view own
	if(strlen($aRow['job_position_id']) > 0){
		$training_program_staff = $Hr_profile_model->get_staff_by_job_position($aRow['job_position_id']);
	}else{
		$training_program_staff = explode(",", $aRow['staff_id']);
	}

	if(isset($array_staff)){

		if(count($training_program_staff) == 0){
				continue;
			}else{
				$check_staff=false;
				foreach ($training_program_staff as $staff_id) {
					if(in_array($staff_id, $array_staff)){
						$check_staff = true;
					}
				}

				if($check_staff == false){
					continue;
				}
			}
		}

		$row['DT_RowClass'] = 'has-row-options';
		$output['aaData'][] = $row;
	}
