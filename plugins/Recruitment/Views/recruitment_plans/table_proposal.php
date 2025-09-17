<?php

$aColumns = [
	'proposal_name',  
	'position',
	'form_work',
	'department',
	'amount_recruiment', 
	'status',
	'1',
];
$sIndexColumn = 'id';
$sTable       = get_db_prefix().'rec_proposal';
$join         = [
	'LEFT JOIN '.get_db_prefix().'rec_job_position on '.get_db_prefix().'rec_job_position.position_id = '.get_db_prefix().'rec_proposal.position',
	'LEFT JOIN '.get_db_prefix().'team on '.get_db_prefix().'team.id = '.get_db_prefix().'rec_proposal.department',
];
$where = [];

if(isset($dataPost['posiotion_ft']) && is_array($dataPost['posiotion_ft'])){
	$posiotion_ft = $dataPost['posiotion_ft'];
	$where_posiotion_ft = '';
	foreach ($posiotion_ft as $y) {
		if($y != '')
		{
			if($where_posiotion_ft == ''){
				$where_posiotion_ft .= 'AND ('.get_db_prefix().'rec_proposal.position = "'.$y.'"';
			}else{
				$where_posiotion_ft .= ' or '.get_db_prefix().'rec_proposal.position = "'.$y.'"';
			}
		}
	}
	if($where_posiotion_ft != '')
	{
		$where_posiotion_ft .= ')';
		array_push($where, $where_posiotion_ft);
	}
}
if(isset($dataPost['dpm']) && is_array($dataPost['dpm'])){

	$dpm = $dataPost['dpm'];
	$where_dpm = '';
	foreach ($dpm as $y) {
		if($y != '')
		{
			if($where_dpm == ''){
				$where_dpm .= 'AND ('.get_db_prefix().'rec_proposal.department = "'.$y.'"';
			}else{
				$where_dpm .= ' or '.get_db_prefix().'rec_proposal.department = "'.$y.'"';
			}
		}
	}
	if($where_dpm != '')
	{
		$where_dpm .= ')';
		array_push($where, $where_dpm);
	}
}
if(isset($dataPost['status'])){
	$status = $dataPost['status'];
	$where_status = '';
	foreach ($status as $y) {
		if($y != '')
		{
			if($where_status == ''){
				$where_status .= 'AND ('.get_db_prefix().'rec_proposal.status = "'.$y.'"';
			}else{
				$where_status .= ' or '.get_db_prefix().'rec_proposal.status = "'.$y.'"';
			}
		}
	}
	if($where_status != '')
	{
		$where_status .= ')';
		array_push($where, $where_status);
	}
}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix().'rec_proposal.id as id','position_name', get_db_prefix().'team.title as dpm_name','workplace','salary_from','salary_to','from_date','to_date','ages_to','ages_from','height','weight','job_description','reason_recruitment','approver','gender','experience','literacy'], '', [], $dataPost);


$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {

		if ($aColumns[$i] == 'proposal_name') {
			
			$_data = '<a href="' . site_url('recruitment/view_recruitment_plan/' . $aRow['id'] ).'">'.$aRow['proposal_name'].'</a>';
		}elseif ($aColumns[$i] == 'form_work') {
			if(strlen($aRow['form_work']) > 0){
				$_data = app_lang($aRow['form_work']);
			}else{
				$_data = $aRow['form_work'];
			}

		}elseif ($aColumns[$i] == 'position') {
			$_data = $aRow['position_name'];
		}elseif($aColumns[$i] == 'department'){
			$_data = $aRow['dpm_name'];
		}elseif($aColumns[$i] == 'amount_recruiment'){
			$_data = $aRow['amount_recruiment'];
		}elseif($aColumns[$i] == 'status'){
			if($aRow['status'] == 1 ){
				$_data = '<span class="badge bg-info large mt-0">'.app_lang('_proposal').'</span>';
			}elseif($aRow['status'] == 2 ){
				$_data = '<span class="badge bg-success large mt-0">'.app_lang('approved').'</span>';
			}elseif($aRow['status'] == 3 ){
				$_data = '<span class="badge bg-primary large mt-0">'.app_lang('made_finish').'</span>';
			}elseif($aRow['status'] == 4 ){
				$_data = '<span class="badge bg-danger large mt-0">'.app_lang('reject').'</span>';
			}
		}else{

			$view = '<li role="presentation"><a href="' . site_url('recruitment/view_recruitment_plan/' . $aRow['id'] ).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';

			$edit = '';
			if(re_has_permission("recruitment_can_edit")){
				$edit = '<li role="presentation"><a href="' . site_url('recruitment/add_recruitment_plan/' . $aRow['id'] ) .'" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
			}
			$delete = '';
			if (re_has_permission("recruitment_can_delete")) {

				$delete .= '<li role="presentation">' .modal_anchor(get_uri("recruitment/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_recruitment_proposal', "class" => 'dropdown-item' )). '</li>';
			}
			$_data = '
			<span class="dropdown inline-block">
			<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
			<i data-feather="tool" class="icon-16"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
			</span>';

		}
		$row[] = $_data;        
	}
	$output['aaData'][] = $row;
}
