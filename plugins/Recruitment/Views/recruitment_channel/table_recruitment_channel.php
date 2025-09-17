<?php

$aColumns = [
	'id',
	'r_form_name',
	'responsible',
	'form_type',
	'lead_status',
	'1',

];
$sIndexColumn = 'id';
$sTable       = get_db_prefix().'rec_campaign_form_web';
$join         = ['LEFT JOIN '.get_db_prefix().'rec_campaign ON '.get_db_prefix().'rec_campaign.cp_id = '.get_db_prefix().'rec_campaign_form_web.rec_campaign_id'];
$where = [];

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['id'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		if(isset($aRow[$aColumns[$i]])){
			$_data = $aRow[$aColumns[$i]];
		}
		
		if ($aColumns[$i] == 'r_form_name') {
			if(re_has_permission("recruitment_can_view_global")){
				
				$name = '<a href="' . site_url('recruitment/view_recruitment_channel/' . $aRow['id'] ).'" >' . $aRow['r_form_name'] . '</a>';
			}else{

				$name = $aRow['r_form_name'];
			}

			$_data = $name;
		} elseif($aColumns[$i] == 'responsible'){

			$name = get_staff_image($aRow['responsible'], true);

			$_data = $name;
		}
		elseif($aColumns[$i] == 'form_type'){
			if($aRow['form_type'] == '1'){
				$_data = app_lang('form');
			}else{
				$_data = '';

			}
		}elseif($aColumns[$i] == 'lead_status'){
			$arr_status=[];
			$arr_status['1']=app_lang('application');
			$arr_status['2']=app_lang('potential');
			$arr_status['3']=app_lang('interview');
			$arr_status['4']=app_lang('won_interview');
			$arr_status['5']=app_lang('send_offer');
			$arr_status['6']=app_lang('elect');
			$arr_status['7']=app_lang('non_elect');
			$arr_status['8']=app_lang('unanswer');
			$arr_status['9']=app_lang('transferred');
			$arr_status['10']=app_lang('preliminary_selection');

			$_data = ($arr_status[$aRow['lead_status']]);
		}elseif($aColumns[$i] == '1'){

			$view = '';
			if(re_has_permission("recruitment_can_view_global")){
				$view = '<li role="presentation"><a href="' . site_url('recruitment/view_recruitment_channel/' . $aRow['id'] ).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
			}

			$edit = '';
			if(re_has_permission("recruitment_can_edit")){

				$edit = '<li role="presentation"><a href="' . site_url('recruitment/add_edit_recruitment_channel/' . $aRow['id'] ) .'" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
			}

			$duplicate = '';
			if (re_has_permission("recruitment_can_create") || re_has_permission("recruitment_can_edit") || is_admin()) {

				$duplicate = '<li role="presentation"><a href="#" onclick=' . '"' . 'duplicate_recruitment_channel(this,' . $aRow['id'] . '); return false;'. '" class="dropdown-item"><span data-feather="copy" class="icon-16"></span> ' . app_lang('r_duplicate') . '</a></li>';
			}
			
			$delete = '';
			if (re_has_permission("recruitment_can_delete")) {

				$delete .= '<li role="presentation">' .modal_anchor(get_uri("recruitment/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_recruitment_channel', "class" => 'dropdown-item' )). '</li>';
			}
			$_data = '';
			if(strlen($view) > 0 || strlen($edit) > 0 || strlen($delete) > 0 || strlen($duplicate) > 0 ){
				$_data = '
				<span class="dropdown inline-block">
				<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
				<i data-feather="tool" class="icon-16"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $duplicate . $delete. '</ul>
				</span>';
			}

		}

		$row[] = $_data;
	}
	$output['aaData'][] = $row;

}
