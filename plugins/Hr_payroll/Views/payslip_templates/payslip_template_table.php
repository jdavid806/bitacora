<?php

$Hr_payroll_model = model("Hr_payroll\Models\Hr_payroll_model");

$aColumns = [
	'id',
	'templates_name',
	'staff_id_created',
	'date_created',
	'1',
];
$sIndexColumn = 'id';
$sTable = get_db_prefix() . 'hrp_payslip_templates';

$where = [];
$join= [];

$array_staffid_by_permission = get_array_staffid_by_permission();

$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, ['id', 'department_id', 'role_employees', 'staff_employees', 'except_staff'], '', [], $dataPost);

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];

	/*load by staff*/
	if(!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_payslip_template') && $aRow['staff_id_created'] != get_staff_user_id1()){
		/*View own*/
		$staffids = $Hr_payroll_model->payslip_template_get_staffid($aRow['department_id'], $aRow['role_employees'], $aRow['staff_employees'], $aRow['except_staff']);

		if($staffids != false){
			$check_dp=false;

			foreach (explode(',', $staffids) as $staffid) {
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

			if($aColumns[$i] == 'id'){
				$_data = $aRow['id'];
			}elseif ($aColumns[$i] == 'templates_name') {
				$_data = $aRow['templates_name'];

			}elseif($aColumns[$i] == 'date_created'){
				$_data = format_to_datetime($aRow['date_created'], false);

			} elseif ($aColumns[$i] == 'staff_id_created') {
				$_data = get_staff_image($aRow['staff_id_created'], false);
				$_data .= get_staff_full_name1($aRow['staff_id_created']);

			} elseif($aColumns[$i] == '1'){
				$view = '';
				$edit = '';
				$delete = '';


				if(is_admin() || hrp_has_permission('hr_payroll_can_view_global_hrp_payslip_template')){	

					$view = '<li role="presentation"><a href="'.get_uri('hr_payroll/view_payslip_templates_detail/' . $aRow['id']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';
				}

				if (hrp_has_permission('hr_payroll_can_edit_hrp_payslip_template') || is_admin()) {
					$edit = '<li role="presentation"><a href="#" onclick="edit_payslip_template(this, '.$aRow['id'] .'); return false;" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
				}

				if (hrp_has_permission('hr_payroll_can_delete_hrp_payslip_template') || is_admin()) {
					$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_payroll/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_payslip_template', "class" => 'dropdown-item' )). '</li>';
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

