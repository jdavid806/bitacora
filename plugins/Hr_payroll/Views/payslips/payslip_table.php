<?php

$Hr_payroll_model = model("Hr_payroll\Models\Hr_payroll_model");

$aColumns = [
	'id',
	'payslip_name',
	'payslip_template_id',
	'payslip_month',
	'payslip_range',
	'staff_id_created',
	'date_created',
	'payslip_status',
	'1',
	'2',
];
$sIndexColumn = 'id';
$sTable = get_db_prefix() . 'hrp_payslips';

$where = [];
$join= [];

$array_staffid_by_permission = get_array_staffid_by_permission();

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['id'], '', [], $dataPost);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	//load by staff
	if(!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_payslip') && $aRow['staff_id_created'] != get_staff_user_id1()){
  	//View own
		$staffids = $Hr_payroll_model->payslip_of_staff($aRow['id']);

		if(count($staffids) > 0){
			$check_dp=false;

			foreach ($staffids as $staffid) {
			    if(in_array($staffid, $array_staffid_by_permission)){
			    	$check_dp = true;//jump
			    }

			    if($check_dp == true){
					break;//jump
				}
			}

			if($check_dp == false){
					continue;//jump
			}
		}else{
			continue;//jump
		}
	}

	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id') {
			$_data = $aRow['id'];

		}elseif ($aColumns[$i] == 'payslip_name') {

			//load by manager
			if(!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_payslip')){
			//View own
				$code = '<a href="' . get_uri('hr_payroll/view_payslip_detail_v2/' . $aRow['id']) . '">' . $aRow['payslip_name'] . '</a>';
			}else{
			//admin or view global
				$code = '<a href="' . get_uri('hr_payroll/view_payslip_detail/' . $aRow['id']) . '">' . $aRow['payslip_name'] . '</a>';
			}

			$_data = $code;

		}elseif($aColumns[$i] == 'payslip_template_id'){
			$_data = get_payslip_template_name($aRow['payslip_template_id']);

		}elseif($aColumns[$i] == 'payslip_month'){
			$_data =  date('m-Y', strtotime($aRow['payslip_month']));

		}elseif($aColumns[$i] == 'payslip_range'){
			$_data =  $aRow['payslip_range'];

		} elseif ($aColumns[$i] == 'staff_id_created') {
			$_data = get_staff_image($aRow['staff_id_created'], false);
			$_data .= get_staff_full_name1($aRow['staff_id_created']);

		} elseif ($aColumns[$i] == 'date_created') {
			$_data = format_to_datetime($aRow['date_created'], false);
		}elseif ($aColumns[$i] == 'payslip_status') {
			if($aRow['payslip_status'] == 'payslip_closing'){
				$_data = ' <span class="badge bg-success large mt-0 "> '.app_lang($aRow['payslip_status']).' </span>';
			}else{
				$_data = ' <span class="badge bg-warning large mt-0"> '.app_lang($aRow['payslip_status']).' </span>';
			}

		}elseif($aColumns[$i] == '1') {

			if((hrp_has_permission('hr_payroll_can_delete_hrp_payslip')) && $aRow['payslip_status'] == 'payslip_closing' ){

				$_data = '<a class="btn btn-primary btn-xs mleft5" id="confirmDelete" data-toggle="tooltip" title="" href="'. admin_url('hr_payroll/payslip_update_status/'.$aRow['id']).'"  data-original-title="'.app_lang('payslip_opening').'"><span data-feather="check" class="icon-16"></span></a>';

				$_data .= '&nbsp<a class="btn btn-success btn-xs mleft5 hrp_payslip_download ``" data-toggle="tooltip" title="" href="'. admin_url('hr_payroll/payslip_manage_export_pdf/'.$aRow['id']).'"  data-original-title="'.app_lang('hrp_payslip_download').'" data-loading-text="Waitting..."><span data-feather="download" class="icon-16"></span></a>';
				
			}else{
				$_data ='';
			}

		}elseif($aColumns[$i] == '2') {
			$view = '';
			$edit = '';
			$delete = '';


			if(!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_payslip')){
			//View own

				$view = '<li role="presentation"><a href="'.get_uri('hr_payroll/view_payslip_detail_v2/' . $aRow['id']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
			}else{
			//admin or view global
				$view = '<li role="presentation"><a href="'.get_uri('hr_payroll/view_payslip_detail/' . $aRow['id']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
			}

			if (hrp_has_permission('hr_payroll_can_edit_hrp_payslip') || is_admin()) {
				$edit = '<li role="presentation"><a href="#" onclick="edit_payslip(this, '.$aRow['id'] .', '.$aRow['payslip_template_id'] .'); return false;" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';

			}


			if (hrp_has_permission('hr_payroll_can_delete_hrp_payslip') || is_admin()) {
				$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_payroll/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_payslip', "class" => 'dropdown-item' )). '</li>';
			}

			$_data = '
			<span class="dropdown inline-block">
			<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
			<i data-feather="tool" class="icon-16"></i>
			</button>
			<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
			</span>';
			$row[] = $_data;
		}

		$row[] = $_data;
	}

	$output['aaData'][] = $row;
}
