<?php

$aColumns = [

	'is_name',
	'from_time',
	'interview_day',
	'campaign', 
	get_db_prefix().'rec_interview.id',
	'interviewer',
	'added_date',
	'added_from', 
	'send_notify', 
	'1', 
];
$sIndexColumn = 'id';
$sTable       = get_db_prefix().'rec_interview';
$join         = [];
$where = [];

if (isset($dataPost['cp_from_date_filter']) && $dataPost['cp_from_date_filter'] != '') {
	$cp_from_date_filter = $dataPost['cp_from_date_filter'];
	array_push($where, "AND date_format(interview_day, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime(to_sql_date1($cp_from_date_filter))) . "'");
}
if (isset($dataPost['cp_to_date_filter']) && $dataPost['cp_to_date_filter'] != '') {
	$cp_to_date_filter = $dataPost['cp_to_date_filter'];

	array_push($where, "AND date_format(interview_day, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date1($cp_to_date_filter))) . "'");
}

if(is_admin()){
	/*view global*/
	if (isset($dataPost['cp_manager_filter'])) {
		$arr_interviewer_filter = $dataPost['cp_manager_filter'];

		$interviewer_filter = '';
		foreach ($arr_interviewer_filter as $y) {
			if ($y != '') {
				if ($interviewer_filter == '') {
					$interviewer_filter .= 'AND (FIND_IN_SET('.$y.', '.get_db_prefix().'rec_interview.interviewer)';
				} else {
					$interviewer_filter .= ' or FIND_IN_SET('.$y.', '.get_db_prefix().'rec_interview.interviewer)';
				}
			}
		}

		if ($interviewer_filter != '') {
			$interviewer_filter .= ')';
			array_push($where, $interviewer_filter);
		}
	}
}else{
	/*View own*/
	array_push($where, 'AND (FIND_IN_SET('.get_staff_user_id1().', '.get_db_prefix().'rec_interview.interviewer) OR ('.get_db_prefix().'rec_interview.added_from = '.get_staff_user_id1().'))');
}


$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix().'rec_interview.id as id','to_time','position', 'from_hours','to_hours', 'interview_location'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'added_from'){
			$_data = get_staff_image($aRow['added_from'], true);

		}elseif($aColumns[$i] == 'is_name'){
			$name = '';
			if(re_has_permission("recruitment_can_view_global")){
				$name = '<a href="' . site_url('recruitment/view_interview_schedule/' . $aRow['id'] ).'" >' . $aRow['is_name'] . '</a>';
			}

			$_data = $name;

		}elseif($aColumns[$i] == 'from_time'){
			$from_hours_format='';
			$to_hours_format='';

			$from_hours = format_to_datetime($aRow['from_hours'], false);
			$from_hours = explode(" ", $from_hours);

			foreach ($from_hours as $key => $value) {
				if($key != 0){
					$from_hours_format .= $value;
				}
			}

			$to_hours = format_to_datetime($aRow['to_hours'], false);
			$to_hours = explode(" ", $to_hours);
			foreach ($to_hours as $key => $value) {
				if($key != 0){
					$to_hours_format .= $value;
				}
			}

			$_data = $from_hours_format.' - '.$to_hours_format;
		}elseif ($aColumns[$i] == 'interview_day') {
			$_data = format_to_date($aRow['interview_day'], false);
		}elseif ($aColumns[$i] == 'campaign') {
			$cp = get_rec_campaign_hp($aRow['campaign']);
			if(isset($cp)){
				$_data = $cp->campaign_code.' - '.$cp->campaign_name;
			}else{
				$_data = '';
			}
			
		}elseif($aColumns[$i] == 'id'){
			$can = get_candidate_interview($aRow['id']);
			$ata = '';
			foreach($can as $cad){
				$ata .= '<a href="' . site('recruitment/candidate/' . $cad) . '">'.candidate_profile_image($cad,[
					'staff-profile-image-small mright5',
				], 'small', [
					'data-toggle' => 'tooltip',
					'data-title'  =>  get_candidate_name($cad),
				]).'</a>';
			}
			$_data = $ata;
		}elseif($aColumns[$i] == 'interviewer'){
			$inv = explode(',', $aRow['interviewer']);
			$ata = '';
			foreach($inv as $iv){
				$ata .= get_staff_image($iv, false);
			}
			$_data = $ata;
		}elseif($aColumns[$i] == 'added_date'){
			$_data = format_to_date($aRow['added_date'], false);

		}elseif($aColumns[$i] == 'send_notify'){
			$option = '';

			$title = '';
			$btn_color = '';
			if($aRow['send_notify'] != 0){
				$btn_color = 'btn-warning';
				$title .= app_lang("The_interview_schedule_has_been_sent").' ';
				$title .= app_lang("to_the_interviewer_and_the_interviewees");
			}else{
				$btn_color = 'btn-success';
				$title .= app_lang("send_the_interview_schedule_to_the_interviewer_and_the_interviewees");
			}

			$option .='<a href="' . site_url('recruitment/send_interview_schedule/' . $aRow['id'] ).'" class="btn btn-sm btn-success" data-original-title="'.$title.'" data-toggle="tooltip"><span data-feather="send" class="icon-16" ></span></a>';

			$_data = $option;
		}elseif($aColumns[$i] == '1'){

			$view = '';
			if(re_has_permission("recruitment_can_view_global")){
				$view = '<li role="presentation"><a href="' . site_url('recruitment/view_interview_schedule/' . $aRow['id'] ).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
			}

			$edit = '';
			if(re_has_permission("recruitment_can_edit")){

				$edit = '<li role="presentation"><a class="dropdown-item" href="#" onclick='.'"'.'edit_interview_schedule(this,' . $aRow['id'] . '); return false;'.'"'.' data-is_name="'.$aRow['is_name'].'" data-campaign="'.$aRow['campaign'].'" data-interview_day="'.format_to_date($aRow['interview_day'], false).'" data-from_time="'.$aRow['from_time'].'" data-to_time="'.$aRow['to_time'].'" data-interviewer="'.$aRow['interviewer'].'" data-position="'. $aRow['position'].'" data-interview_location="'. $aRow['interview_location'].'" ><span data-feather="edit" class="icon-16"></span>' .app_lang('edit') . '</a></li>';
			}
			$delete = '';
			if (re_has_permission("recruitment_can_delete")) {

				$delete .= '<li role="presentation">' .modal_anchor(get_uri("recruitment/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_interview_schedule', "class" => 'dropdown-item' )). '</li>';
			}
			$_data = '';
			if(strlen($view) > 0 || strlen($edit) > 0 || strlen($delete) > 0 ){
				$_data = '
				<span class="dropdown inline-block">
				<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
				<i data-feather="tool" class="icon-16"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
				</span>';
			}

		}

		$row[] = $_data;
	}
	$output['aaData'][] = $row;

}
