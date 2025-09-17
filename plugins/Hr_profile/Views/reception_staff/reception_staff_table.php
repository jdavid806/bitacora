<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");
$Hr_profile_controller = model("Hr_profile\Controllers\Hr_profile");


$aColumns = [
	get_db_prefix().'hr_rec_transfer_records.staffid',
	get_db_prefix().'hr_rec_transfer_records.staff_identifi',
	get_db_prefix().'hr_rec_transfer_records.firstname',  
	get_db_prefix().'hr_rec_transfer_records.birthday',
	get_db_prefix().'hr_rec_transfer_records.staffid',
	'1',
];
$sIndexColumn = 'staffid';
$sTable       = get_db_prefix().'hr_rec_transfer_records';

$join         = [
	'LEFT JOIN '.get_db_prefix().'users on '.get_db_prefix().'users.id = '.get_db_prefix().'hr_rec_transfer_records.staffid',
];

$where = [];

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_onboarding')){
	  //View own
	$staff_ids = $Hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.get_db_prefix().'hr_rec_transfer_records.staffid IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}
}


$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [
	get_db_prefix().'hr_rec_transfer_records.id',
	get_db_prefix().'users.last_name',
	get_db_prefix().'users.first_name',
	get_db_prefix().'hr_rec_transfer_records.staffid',
	get_db_prefix().'users.staff_identifi',
	get_db_prefix().'users.dob',
], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow[get_db_prefix().'hr_rec_transfer_records.staffid'] . '" class="form-check-input"><label></label></div>';
	$row[] = $aRow['staffid']; 

	$_data ='';

	$_data .= '<a href="' . site_url('team_members/view/' . $aRow[get_db_prefix().'hr_rec_transfer_records.staffid']) . '">' .get_staff_image($aRow[get_db_prefix().'hr_rec_transfer_records.staffid'], false) . '</a>';

	$_data .= ' <a href="' . site_url('team_members/view/' . $aRow[get_db_prefix().'hr_rec_transfer_records.staffid']) . '">' . $aRow['first_name'] . ' ' . $aRow['last_name'] . '</a><br/>';

	$row[] = $_data;  
	$row[] = $aRow['staff_identifi'];  
	$row[] = format_to_date($aRow['dob'], false);

	$percent = round((float)$Hr_profile_controller->get_percent_complete($aRow['staffid']), 2);

	ob_start();

	$progress_bar_percent = $percent / 100; ?>
	<input type="hidden" value="<?php
	echo html_entity_decode($progress_bar_percent); ?>" name="percent">
	<div class="goal-progress" data-reverse="true">
		<strong class="goal-percent"><?php
		echo html_entity_decode($percent); ?>%</strong>
	</div>
	<?php
	$progress = ob_get_contents();
	ob_end_clean();

	$row[]              = $progress;

	$view = '';
	if(is_admin() || hr_has_permission('hr_profile_can_edit_onboarding')){

		$view = '<li role="presentation">'.modal_anchor(get_uri("hr_profile/get_reception_modal"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('hr_edit'),array("class" => "edit", "title" => $aRow['first_name'].' '.$aRow['last_name'], "data-post-id" => $aRow[get_db_prefix().'hr_rec_transfer_records.staffid'], "class" => 'dropdown-item')). '</li>';

	}

	$delete = '';
	if(is_admin() || hr_has_permission('hr_profile_can_delete_onboarding')){
		$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow[get_db_prefix().'hr_rec_transfer_records.staffid'], "data-post-function" => 'delete_reception', "class" => 'dropdown-item' )). '</li>';
	}

	$_data = '';
	if(strlen($view) > 0 || strlen($delete) > 0){
		$_data = '
		<span class="dropdown inline-block">
		<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
		<i data-feather="tool" class="icon-16"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $delete. '</ul>
		</span>';
	}
	$row[] = $_data;



	$output['aaData'][] = $row;
}
