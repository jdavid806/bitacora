<?php

$aColumns = [
	'campaign_name',
	get_db_prefix() . 'rec_campaign.company_id',
	'cp_position',
	'cp_form_work',
	'cp_department',
	'cp_from_date',
	'cp_amount_recruiment',
	'rec_channel_form_id',
	'cp_manager',
	'cp_status',
	'1',
];
$sIndexColumn = 'cp_id';
$sTable = get_db_prefix() . 'rec_campaign';
$join = [
	'LEFT JOIN ' . get_db_prefix() . 'rec_job_position on ' . get_db_prefix() . 'rec_job_position.position_id = ' . get_db_prefix() . 'rec_campaign.cp_position',
	'LEFT JOIN ' . get_db_prefix() . 'team on ' . get_db_prefix() . 'team.id = ' . get_db_prefix() . 'rec_campaign.cp_department',
	'LEFT JOIN ' . get_db_prefix() . 'rec_campaign_form_web on ' . get_db_prefix() . 'rec_campaign_form_web.id = ' . get_db_prefix() . 'rec_campaign.rec_channel_form_id',
];
$where = [];

if (isset($dataPost['posiotion_ft'])) {
	$posiotion_ft = $dataPost['posiotion_ft'];
	$where_posiotion_ft = '';
	foreach ($posiotion_ft as $y) {
		if ($y != '') {
			if ($where_posiotion_ft == '') {
				$where_posiotion_ft .= 'AND (' . get_db_prefix() . 'rec_campaign.cp_position = "' . $y . '"';
			} else {
				$where_posiotion_ft .= ' or ' . get_db_prefix() . 'rec_campaign.cp_position = "' . $y . '"';
			}
		}
	}
	if ($where_posiotion_ft != '') {
		$where_posiotion_ft .= ')';
		array_push($where, $where_posiotion_ft);
	}
}
if (isset($dataPost['dpm'])) {
	$dpm = $dataPost['dpm'];
	$where_dpm = '';
	foreach ($dpm as $y) {
		if ($y != '') {
			if ($where_dpm == '') {
				$where_dpm .= 'AND (' . get_db_prefix() . 'rec_campaign.cp_department = "' . $y . '"';
			} else {
				$where_dpm .= ' or ' . get_db_prefix() . 'rec_campaign.cp_department = "' . $y . '"';
			}
		}
	}
	if ($where_dpm != '') {
		$where_dpm .= ')';
		array_push($where, $where_dpm);
	}
}
if (isset($dataPost['status'])) {
	$status = $dataPost['status'];
	$where_status = '';
	foreach ($status as $y) {
		if ($y != '') {
			if ($where_status == '') {
				$where_status .= 'AND (' . get_db_prefix() . 'rec_campaign.cp_status = "' . $y . '"';
			} else {
				$where_status .= ' or ' . get_db_prefix() . 'rec_campaign.cp_status = "' . $y . '"';
			}
		}
	}
	if ($where_status != '') {
		$where_status .= ')';
		array_push($where, $where_status);
	}
}
if (isset($dataPost['company_filter'])) {
	$company_filter = $dataPost['company_filter'];
	$where_company_filter = '';
	foreach ($company_filter as $y) {
		if ($y != '') {
			if ($where_company_filter == '') {
				$where_company_filter .= 'AND ('.get_db_prefix().'rec_campaign.company_id = "' . $y . '"';
			} else {
				$where_company_filter .= ' or '.get_db_prefix().'rec_campaign.company_id = "' . $y . '"';
			}
		}
	}
	if ($where_company_filter != '') {
		$where_company_filter .= ')';
		array_push($where, $where_company_filter);
	}
}

if (isset($dataPost['cp_from_date_filter']) && $dataPost['cp_from_date_filter'] != '') {
	$cp_from_date_filter = $dataPost['cp_from_date_filter'];
	array_push($where, "AND date_format(cp_from_date, '%Y-%m-%d') >= '" . date('Y-m-d', strtotime(to_sql_date1($cp_from_date_filter))) . "'");
}
if (isset($dataPost['cp_to_date_filter']) && $dataPost['cp_to_date_filter'] != '') {
	$cp_to_date_filter = $dataPost['cp_to_date_filter'];

	array_push($where, "AND date_format(cp_to_date, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date1($cp_to_date_filter))) . "'");
}

if (isset($dataPost['rec_channel_form_id_filter'])) {
	$rec_channel_form_id_filter = $dataPost['rec_channel_form_id_filter'];
	$where_rec_channel_form_id_filter = '';
	foreach ($rec_channel_form_id_filter as $y) {
		if ($y != '') {
			if ($where_rec_channel_form_id_filter == '') {
				$where_rec_channel_form_id_filter .= 'AND ('.get_db_prefix().'rec_campaign.rec_channel_form_id = "' . $y . '"';
			} else {
				$where_rec_channel_form_id_filter .= ' or '.get_db_prefix().'rec_campaign.rec_channel_form_id = "' . $y . '"';
			}
		}
	}
	if ($where_rec_channel_form_id_filter != '') {
		$where_rec_channel_form_id_filter .= ')';
		array_push($where, $where_rec_channel_form_id_filter);
	}
}

if (isset($dataPost['cp_manager_filter'])) {
	$cp_manager_filter = $dataPost['cp_manager_filter'];

	$where_cp_manager_filter = '';
	foreach ($cp_manager_filter as $y) {
		if ($y != '') {
			if ($where_cp_manager_filter == '') {
				$where_cp_manager_filter .= 'AND (FIND_IN_SET('.$y.', '.get_db_prefix().'rec_campaign.cp_manager)';
			} else {
				$where_cp_manager_filter .= ' or FIND_IN_SET('.$y.', '.get_db_prefix().'rec_campaign.cp_manager)';
			}
		}
	}

	if ($where_cp_manager_filter != '') {
		$where_cp_manager_filter .= ')';
		array_push($where, $where_cp_manager_filter);
	}
}

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['campaign_code', 'cp_id', 'position_name', get_db_prefix() . 'team.title as dpm_name', 'cp_workplace', 'cp_salary_from', 'cp_salary_to', 'cp_from_date', 'cp_to_date', 'cp_ages_to', 'cp_ages_from', 'cp_height', 'cp_weight', 'cp_job_description', 'cp_reason_recruitment', 'cp_manager', 'cp_follower', 'cp_gender', 'cp_experience', 'cp_literacy', 'cp_proposal','rec_channel_form_id','display_salary',get_db_prefix() . 'rec_campaign.company_id', 'cp_to_date', 'r_form_name', ], '', [], $dataPost);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		
		if ($aColumns[$i] == 'campaign_name') {

			$name = '<a href="' . site_url('recruitment/view_recruitment_campaign/' . $aRow['cp_id'] ).'">'.$aRow['campaign_name'].'</a>';

			$_data = $name;
		}elseif($aColumns[$i] == get_db_prefix() . 'rec_campaign.company_id'){
			$_data = get_rec_company_name($aRow[get_db_prefix() . 'rec_campaign.company_id']);

		} elseif ($aColumns[$i] == 'cp_form_work') {
			if(strlen($aRow['cp_form_work']) > 0){

				$_data = app_lang($aRow['cp_form_work']);
			}else{

				$_data = $aRow['cp_form_work'];
			}
		} elseif ($aColumns[$i] == 'cp_position') {
			$_data = $aRow['position_name'];
		} elseif ($aColumns[$i] == 'cp_department') {
			$_data = $aRow['dpm_name'];
		} elseif ($aColumns[$i] == 'cp_status') {

			if($aRow['cp_status'] == 1 ){
				$_data = '<span class="badge bg-info large mt-0">'.app_lang('planning').'</span>';
			}elseif($aRow['cp_status'] == 2 ){
				$_data = '<span class="badge bg-info large mt-0">'.app_lang('overdue').'</span>';
			}elseif($aRow['cp_status'] == 3 ){
				$_data = '<span class="badge bg-success large mt-0">'.app_lang('in_progress').'</span>';
			}elseif($aRow['cp_status'] == 4){
				$_data = '<span class="badge bg-primary large mt-0">'.app_lang('finish').'</span>';
			}elseif($aRow['cp_status'] == 5 ){
				$_data = '<span class="badge bg-danger large mt-0">'.app_lang('cancel').'</span>';
			}

		} elseif ($aColumns[$i] == 'rec_channel_form_id'){
			$_data = $aRow['r_form_name'];
		} elseif ($aColumns[$i] == 'cp_manager'){
			$manager_data = '';
			$manager = explode(',', $aRow['cp_manager']);

			foreach ($manager as $f) {
				if(is_numeric($f)){
					$manager_data .= get_staff_image($f, false);
				}
			}
			$_data = $manager_data;

		} elseif($aColumns[$i] == 'cp_from_date'){
			$_data = format_to_date($aRow['cp_from_date'], false).' - '. format_to_date($aRow['cp_to_date'], false);
		}elseif($aColumns[$i] == 'cp_amount_recruiment'){
			$_data = $aRow['cp_amount_recruiment'];
		}elseif($aColumns[$i] == '1'){

			$view = '<li role="presentation"><a href="' . site_url('recruitment/view_recruitment_campaign/' . $aRow['cp_id'] ).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';

			$edit = '';
			if(re_has_permission("recruitment_can_edit")){
				$edit = '<li role="presentation"><a href="' . site_url('recruitment/add_recruitment_campaign/' . $aRow['cp_id'] ) .'" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
			}
			$delete = '';
			if (re_has_permission("recruitment_can_delete")) {

				$delete .= '<li role="presentation">' .modal_anchor(get_uri("recruitment/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['cp_id'], "data-post-function" => 'delete_recruitment_campaign', "class" => 'dropdown-item' )). '</li>';
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
