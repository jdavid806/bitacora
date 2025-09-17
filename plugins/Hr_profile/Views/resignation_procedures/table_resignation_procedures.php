<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'id',
	'staff_name',
	'department_name',
	'role_name',
	'email',
	'dateoff',
	'(SELECT count(*) FROM '.get_db_prefix().'hr_procedure_retire_of_staff'.' where '.get_db_prefix().'hr_procedure_retire_of_staff.staffid= '.get_db_prefix().'hr_list_staff_quitting_work.staffid) as total',
	'(select count(*) FROM '.get_db_prefix().'hr_procedure_retire_of_staff'.'  where '.get_db_prefix().'hr_procedure_retire_of_staff.staffid= '.get_db_prefix().'hr_list_staff_quitting_work.staffid and status = 1 ) as total_check',
	'(select status FROM '.get_db_prefix().'users'.'  where '.get_db_prefix().'users.id= '.get_db_prefix().'hr_list_staff_quitting_work.staffid) as staff_active',
	'1',

];

$sIndexColumn = 'id';
$sTable       = get_db_prefix().'hr_list_staff_quitting_work';
$join = [''];
$where = [];

//load deparment by manager
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_layoff_checklists')){
	  //View own
	$staff_ids = $Hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.get_db_prefix().'hr_list_staff_quitting_work.staffid IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

$result  = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['staffid','staff_name','department_name','role_name','email','dateoff', 'approval'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

	if($aRow['total'] == 0 && $aRow['total_check'] == 0){
		$ces = 100;
	}else{
		$ces = round($aRow['total_check'] * 100 / $aRow['total'], 2);
	}
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['staffid'] . '" class="form-check-input"><label></label></div>';
	
	$row[] = $aRow['id'];

	$staff_n = '';
	if(is_admin() || hr_has_permission('hr_profile_can_edit_layoff_checklists')){
		$staff_n .=  '<a href="#" data-id="'.$aRow['staffid'].'" onclick="detail_checklist_staff(this);">' . get_staff_image($aRow['staffid'], false) . '</a><a href="#" onclick="detail_checklist_staff(this)" data-id="'.$aRow['staffid'].'">' . $aRow['staff_name']. '</a>';
	}else{
		$staff_n .= get_staff_image($aRow['staffid'], false).' '. $aRow['staff_name'];
	}

	$row[] = $staff_n;

	$row[] = $aRow['department_name'];
	$row[] = $aRow['role_name'];
	$row[] = $aRow['email'];
	$row[] = format_to_date($aRow['dateoff'], false); 

	ob_start();
	$progress_bar_percent = $ces / 100; ?>
	<input type="hidden" value="<?php
	echo html_entity_decode($progress_bar_percent); ?>" name="percent">
	<div class="goal-progress" data-reverse="true">
		<strong class="goal-percent"><?php
		echo html_entity_decode($ces); ?>%</strong>
	</div>
	<?php
	$progress = ob_get_contents();
	ob_end_clean();

	$row[]              = $progress;



	if($aRow['approval'] == 'approved' ){
		$row[] = '<span class="badge bg-success large mt-0">'.app_lang('hr_agree_label').'</span>';

	}else{
		$row[] ='<span class="badge bg-warning large mt-0">'.app_lang('hr_pending_label').'</span>';
	}

	/*options*/
	$view = '';
	if(is_admin() || hr_has_permission('hr_profile_can_view_own_layoff_checklists') || hr_has_permission('hr_profile_can_view_global_layoff_checklists')){	

		$view = '<li role="presentation"><a href="#" onclick="detail_checklist_staff(this); return false" data-id="'.$aRow['staffid'].'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
	}

	$approval = '';
	if(($ces == 100) && (is_admin() || hr_has_permission('hr_profile_can_edit_layoff_checklists'))){
		if($aRow['approval'] == null ){

			$approval = '<li role="presentation"><a href="#" id="'.$aRow['staffid'].'" resignation_id="'.$aRow['id'].'"  onclick="update_status_quit_work(this); return false" class="dropdown-item"><span data-feather="check-circle" class="icon-16"></span> ' . app_lang('hr_agree_label') . '</a></li>';
		}
	}

	$delete = '';
	if((is_admin() || (hr_has_permission('hr_profile_can_delete_layoff_checklists')) && $aRow['approval'] == null) ){
		$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['staffid'], "data-post-function" => 'delete_procedures_for_quitting_work', "class" => 'dropdown-item' )). '</li>';
	}

	$_data = '';
	if(strlen($view) > 0 ||strlen($approval) > 0 ||strlen($delete) > 0){
		$_data = '
		<span class="dropdown inline-block">
		<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
		<i data-feather="tool" class="icon-16"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view .$approval. $delete. '</ul>
		</span>';
	}
	$row[] = $_data;

	$output['aaData'][] = $row;
}
