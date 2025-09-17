<?php

$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");
$Hr_profile_controller = model("Hr_profile\Controllers\Hr_profile");

$aColumns = [
	get_db_prefix().'hr_rec_transfer_records.staffid',
	get_db_prefix().'hr_rec_transfer_records.firstname',  
	get_db_prefix().'hr_rec_transfer_records.staff_identifi',
	get_db_prefix().'hr_rec_transfer_records.birthday',
	get_db_prefix().'hr_rec_transfer_records.staffid',
];
$sIndexColumn = 'lastname';
$sTable       = get_db_prefix().'hr_rec_transfer_records';
$join         = [];
$i            = 0;

$join         = [
	'LEFT JOIN '.get_db_prefix().'users on '.get_db_prefix().'users.id = '.get_db_prefix().'hr_rec_transfer_records.staffid',
];



$where = array();
$where = [];
$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [
	get_db_prefix().'hr_rec_transfer_records.id',
	get_db_prefix().'users.last_name',
	get_db_prefix().'users.first_name',
	get_db_prefix().'hr_rec_transfer_records.staffid',
	get_db_prefix().'users.staff_identifi',
	get_db_prefix().'users.dob',
	get_db_prefix().'users.job_position',
], '', [], $dataPost);
$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['staff_identifi'];  

	$_data = '';
	$_data .= '<a href="' . site_url('hr_profile/staff_profile/' . $aRow[get_db_prefix().'hr_rec_transfer_records.staffid'].'/general') . '">' .get_staff_image($aRow[get_db_prefix().'hr_rec_transfer_records.staffid'], false) . '</a>';

	$_data .= ' <a href="' . site_url('hr_profile/staff_profile/' . $aRow[get_db_prefix().'hr_rec_transfer_records.staffid'].'/general') . '">' . $aRow['first_name'] . ' ' . $aRow['last_name'] . '</a><br/>';

	$row[] = $_data; 


	$name_position = '';
	if($aRow['job_position']){
		if($aRow['job_position'] != ''){
			$position = $Hr_profile_model->get_job_position($aRow['job_position']); 
			if(isset($position)){
				if(isset($position->position_name)){
					$name_position = $position->position_name;
				} 
			} 
		}
	}
	$row[] = $name_position;  

	$name_department = $Hr_profile_model->getdepartment_name($aRow[get_db_prefix().'hr_rec_transfer_records.staffid']);
	$row[] = $name_department->name;


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


	if($percent<100){
		$output['aaData'][] = $row;
	} 
}
