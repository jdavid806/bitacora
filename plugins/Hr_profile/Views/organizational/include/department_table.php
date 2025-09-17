<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'id',
	'title',
	'parent_id',
	'manager_id',
];
$sIndexColumn = 'id';
$sTable       = db_prefix().'team';
$where = array();

$where[] = 'AND deleted = 0';

if(isset($dataPost['dept'])){
	$dept = $dataPost['dept'];
}

if(isset($dept) && strlen($dept) > 0){
	$departmentgroup = $Hr_profile_model->get_department_by_parent($dept);
	if (count($departmentgroup) > 0) {
		$where[] = 'AND id IN (' . implode(', ', $departmentgroup) . ')';
	}
}

/*load deparment by manager*/
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_organizational_chart')){
	/*View own*/
	$array_department = $Hr_profile_model->get_department_by_manager();
	if (count($array_department) > 0) {
		$where[] = 'AND '.db_prefix().'team.id IN (' . implode(', ', $array_department) . ')';

	}else{
		$where[] = 'AND 1=2';
	}
}


$result  = data_tables_init1($aColumns, $sIndexColumn, $sTable, [],$where, ['id', 'parent_id', 'manager_id'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		$_data = $aRow[$aColumns[$i]];
		
		if ($aColumns[$i] == 'id') {
			$_data = $aRow['id'];
		}elseif ($aColumns[$i] == 'title') {
			$_data = $aRow['title'];
			
		}elseif($aColumns[$i] == 'parent_id'){

			if(is_numeric($aRow['parent_id']) && (int)$aRow['parent_id'] != null){
				$dpm = hr_profile_get_department_name($aRow['parent_id']);
			}
			if(isset($dpm) && $dpm){
				$_data = $dpm->title;
			}else{
				$_data = '';
			}
		}elseif($aColumns[$i] == 'manager_id'){
			if($aRow['manager_id'] != 0){
				$_data = '<a href="' . site_url('team_members/view/' . $aRow['manager_id']) . '/general">' . get_staff_image($aRow['manager_id']). '</a>';
			}else{
				$_data = '';
			}
		}
		$row[] = $_data;
	}

	$options = '';
	$edit = '';
	if(is_admin() || hr_has_permission('hr_profile_can_edit_organizational_chart')){

		$edit = '<li role="presentation">'.modal_anchor(get_uri("hr_profile/new_deparment_modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'),array("class" => "edit", "title" => app_lang('hr_edit_unit'), "data-post-id" => $aRow['id'], "class" => 'dropdown-item')). '</li>';


		// $edit .= '<li role="presentation"><a href="#" onclick="edit_department(this,' . $aRow['id'] . '); return false" class="dropdown-item" data-title="'.$aRow['title'].'" data-parent_id="'.$aRow['parent_id'].'" data-manager_id="'.$aRow['manager_id'].'"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
	}

	$delete = '';
	if(is_admin() || hr_has_permission('hr_profile_can_delete_organizational_chart')){

		$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete', "class" => 'dropdown-item' )). '</li>';;
	}

	$options = '';
	if(strlen($edit) > 0 ||strlen($delete) > 0){

		$options = '
		<span class="dropdown inline-block">
		<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
		<i data-feather="tool" class="icon-16"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" role="menu">'. $edit .$delete.'</ul>
		</span>';
	}


	$row[] = $options;

	$output['aaData'][] = $row;
}
