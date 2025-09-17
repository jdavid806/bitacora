<?php


$aColumns = [
	'training_programs_name',
	'training_places',
	'training_time_from',
	'training_time_to',
	'training_result',
	'degree',
	'notes',
];
$sIndexColumn = 'id';
$sTable = get_db_prefix() . 'hr_education';
$join = [];
$where = [];

if(isset($dataPost['staff_id'])){
	$staff_id = $dataPost['staff_id'];
}

if (isset($staff_id)) {
	array_push($where, 'AND staff_id=' . $staff_id);
}
$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'training_programs_name', 'training_places', 'training_time_from', 'training_time_to', 'training_result', 'degree', 'notes'], '', [], $dataPost);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = $aRow['training_programs_name'];
	$row[] = $aRow['training_places'];
	$row[] = format_to_date($aRow['training_time_from'], false);
	$row[] = format_to_date($aRow['training_time_to'], false);
	$row[] = $aRow['training_result'];
	$row[] = $aRow['degree'];
	$row[] = $aRow['notes'];

	$edit = '';
	$delete = '';

	$edit = '<li role="presentation"><a href="#" onclick="update_education(this); return false" class="dropdown-item" data-time_from="' . format_to_date($aRow['training_time_from'], false) . '" data-time_to="' . format_to_date($aRow['training_time_to'], false) . '" data-result="' . $aRow['training_result'] . '" data-degree="' . $aRow['degree'] . '" data-notes="' . $aRow['notes'] . '" data-name_programe="' . $aRow['training_programs_name'] . '" data-training_pl="' . $aRow['training_places'] . '" data-id="' . $aRow['id'] . '"><span data-feather="edit" class="icon-16"></span> ' . app_lang('hr_edit') . '</a></li>';

	$delete = '<li role="presentation"><a href="#" onclick="delete_education(this); return false" class="dropdown-item" data-id="' . $aRow['id'] . '"><span data-feather="x" class="icon-16"></span> ' . app_lang('delete') . '</a></li>';

	$_data = '
	<span class="dropdown inline-block">
	<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
	<i data-feather="tool" class="icon-16"></i>
	</button>
	<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$edit . $delete.'</ul>
	</span>';
	$row[] = $_data;
	

	$row[] = $aRow['notes'];
	$output['aaData'][] = $row;
}
