<?php
$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

$aColumns = [
	'id',
	'staffid',
	'dependent_name',
	'dependent_bir',
	'start_month',
	'dependent_iden',
	'reason',
	'status',
	'status_comment',
	'1',
];

$sIndexColumn = 'id';
$sTable       = get_db_prefix() . 'hr_dependent_person';

$join = [];
$where  = [];
$filter = [];

/*load deparment by manager*/
if(!is_admin() && !hr_has_permission('hr_profile_can_view_global_dependent_persons')){
	/*View own*/
	$staff_ids = $Hr_profile_model->get_staff_by_manager();
	if (count($staff_ids) > 0) {
		$where[] = 'AND '.get_db_prefix().'hr_dependent_person.staffid IN (' . implode(', ', $staff_ids) . ')';

	}else{
		$where[] = 'AND 1=2';
	}

}

if(isset($dataPost['memberid'])){
	$memberid = $dataPost['memberid'];
}
if(isset($dataPost['member_view'])){
	$member_view = $dataPost['member_view'];
}
if(isset($dataPost['staff_id'])){
	$staff_id = $dataPost['staff_id'];
}
if(isset($dataPost['status_id'])){
	$status_id = $dataPost['status_id'];
}

if(isset($memberid)){

	$where_staff = '';
	$staffs = $memberid;
	if($staffs != '')
	{
		if($where_staff == ''){
			$where_staff .= 'AND staffid = "'.$staffs. '"';
		}else{
			$where_staff .= 'or staffid = "' .$staffs.'"';
		}
	}
	if($where_staff != '')
	{
		array_push($where, $where_staff);
	}
}

if(isset($staff_id)){
	$where_staff = '';
	foreach ($staff_id as $staffid) {

		if($staffid != '')
		{
			if($where_staff == ''){
				$where_staff .= ' ('.get_db_prefix().'hr_dependent_person.staffid in ('.$staffid.')';
			}else{
				$where_staff .= ' or '.get_db_prefix().'hr_dependent_person.staffid in ('.$staffid.')';
			}
		}
	}
	if($where_staff != '')
	{
		$where_staff .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_staff);
		}else{
			array_push($where, $where_staff);
		}

	}
}

if(isset($status_id)){
	$where_status = '';
	foreach ($status_id as $statusid) {

		if($statusid != '')
		{
			if($where_status == ''){
				$where_status .= ' ('.get_db_prefix().'hr_dependent_person.status in ('.$statusid.')';
			}else{
				$where_status .= ' or '.get_db_prefix().'hr_dependent_person.status in ('.$statusid.')';
			}
		}
	}
	if($where_status != '')
	{
		$where_status .= ')';
		if($where != ''){
			array_push($where, 'AND'. $where_status);
		}else{
			array_push($where, $where_status);
		}

	}
}


$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['end_month', 'relationship'], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '" class="form-check-input"><label></label></div>';
	$row[] = $aRow['id'];

	$subjectOutput ='';
	$subjectOutput .= $aRow['dependent_name'].' ('.$aRow['relationship'].')';
	$row[] = $subjectOutput;

	$row[] = get_staff_full_name1($aRow['staffid']);
	$row[] = format_to_date($aRow['dependent_bir'], false);
	$row[] = $aRow['dependent_iden'];
	$row[] = format_to_date($aRow['start_month'], false) .' - '. format_to_date($aRow['end_month'], false);
	$row[] = $aRow['reason'];

	$status_str = '';
	if($aRow['status'] == 1){ 
		$status_str .= '<span class="badge bg-success large mt-0">'.app_lang('hr_agree_label').'</span>';

	} elseif($aRow['status'] == 2){
		$status_str .= '<span class="badge bg-danger large mt-0">'.app_lang('hr_rejected_label').'</span>';
	} else{
		$status_str .= '<span class="badge bg-warning large mt-0">'.app_lang('hr_pending_label').'</span>';
	}
	$row[] = $status_str;

	$options_str = '';

	if(isset($member_view) && $member_view == 1){

		if ( (get_staff_user_id1() == $aRow['staffid']) &&  ($aRow['status'] == 0)){
			$subjectOutput .='<a href="#" onclick="edit_dependent_person(this,'.$aRow['id'].'); return false"  data-toggle="sidebar-right" data-dependent_name="'.$aRow['dependent_name'].'" data-relationship="'.$aRow['relationship'].'"  data-dependent_iden="'.$aRow['dependent_iden'].'" data-reason="'.$aRow['reason'].'" data-dependent_bir="'._d($aRow['dependent_bir']).'"  >'.app_lang('hr_edit').'</a> |';
		}

		if (hr_has_permission('hr_profile_can_delete_dependent_persons') || is_admin() || (get_staff_user_id1() == $aRow['staffid'])){
			$subjectOutput .='<a href="'.site_url('hr_profile/delete_dependent_person/'.$aRow['id']).'" class="text-danger" >'. app_lang('delete').'</a>';
		}

	}else{
		if($aRow['status'] == 0){

			if( is_admin() || hr_has_permission('hr_profile_can_edit_dependent_persons')){ 

				$options_str .= '<div id="accept_reject_'.$aRow['id'].'">';


				$options_str .= '<a class="btn btn-sm btn-success btn-xs mleft5" data-toggle="tooltip" title=""  onclick="approval(this);" data-original-title="'.app_lang('hr_agree_label').'" data-dependent_id="'.$aRow['id'].'" data-start_month="'.format_to_date($aRow['start_month'], false).'" data-end_month="'.format_to_date($aRow['end_month'], false).'"> <span data-feather="check-square" class="icon-16"></span> </a>';

				$options_str .= '&nbsp<a class="btn btn-sm btn-danger btn-xs mleft5" data-toggle="tooltip" title=""  onclick="reject(this);" data-original-title="'.app_lang('hr_rejected_label').'" data-dependent_id="'.$aRow['id'].'"  data-start_month="'.format_to_date($aRow['start_month'], false).'" data-end_month="'.format_to_date($aRow['end_month'], false).'"><span data-feather="x-square" class="icon-16"></span> </a>';

			}

		}
	}
	$row[] = $options_str;


	$row[] = $aRow['status_comment'];


	if(isset($member_view) && $member_view == 1){

		$edit = '';
		if ( (get_staff_user_id1() == $aRow['staffid']) &&  ($aRow['status'] == 0)){
			$edit = '<li role="presentation"><a href="#" onclick="edit_dependent_person(this,'.$aRow['id'].'); return false" class="dropdown-item" data-dependent_name="'.$aRow['dependent_name'].'" data-relationship="'.$aRow['relationship'].'"  data-dependent_iden="'.$aRow['dependent_iden'].'" data-reason="'.$aRow['reason'].'" data-dependent_bir="'._d($aRow['dependent_bir']).'"><span data-feather="edit" class="icon-16"></span> ' . app_lang('hr_edit') . '</a></li>';
		}

		$delete = '';
		if (hr_has_permission('hr_profile_can_delete_dependent_persons') || is_admin() || (get_staff_user_id1() == $aRow['staffid'])){

			$delete = '<li role="presentation">' . modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'],"data-post-function" => 'delete_dependent_person', "class" => "dropdown-item")) . '</li>';
		}
	}else{
		$edit = '';
		if ((hr_has_permission('hr_profile_can_edit_dependent_persons') || is_admin()) && ($aRow['status'] == 0)){

			$edit = '<li role="presentation">'.modal_anchor(get_uri("hr_profile/dependent_person_modal"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'),array("class" => "edit", "title" => app_lang('hr_edit_dependent_person'), "data-post-id" => $aRow['id'], "data-post-manage" => true, "class" => 'dropdown-item')). '</li>';
		}

		$delete = '';
		if (hr_has_permission('hr_profile_can_delete_dependent_persons') || is_admin()){

			$delete = '<li role="presentation">' . modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'],"data-post-function" => 'admin_delete_dependent_person', "class" => "dropdown-item")) . '</li>';
		}

	}

	$_data = '';
	if(strlen($edit) > 0 ||strlen($delete) > 0 ){
		$_data = '
		<span class="dropdown inline-block">
		<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
		<i data-feather="tool" class="icon-16"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$edit . $delete.'</ul>
		</span>';
	}
	$row[] = $_data;



	$output['aaData'][] = $row;
}
