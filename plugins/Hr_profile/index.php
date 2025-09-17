<?php

/*
Plugin Name: HR Records
Description: The primary function of HR Records is to provide a central database containing records for all employees past and present
Version: 1.0.0
Requires at least: 3.0
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/

use App\Libraries\Template;
use App\Controllers\Security_Controller;
if(!defined('HR_PROFILE_REVISION')){
    define('HR_PROFILE_REVISION', 10010);    
}

/*Modules Path*/
if(!defined('APP_MODULES_PATH')){
	define('APP_MODULES_PATH', FCPATH . 'plugins/');
}

if(!defined('EXT')){
	define('EXT', '.php');
}
if(!defined('HR_PROFILE_MODULE_NAME')){
    define('HR_PROFILE_MODULE_NAME', 'Hr_profile');    
}
if(!defined('HR_PROFILE_PATH_LIBRARIES')){
    define('HR_PROFILE_PATH_LIBRARIES', 'plugins/Hr_profile/Libraries');    
}
if(!defined('HR_PROFILE_PATH')){
    define('HR_PROFILE_PATH', 'plugins/Hr_profile/Uploads/');    
}
if(!defined('HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER')){
    define('HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER', 'plugins/Hr_profile/Uploads/contracts/');    
}
if(!defined('HR_PROFILE_JOB_POSIITON_ATTACHMENTS_UPLOAD_FOLDER')){
    define('HR_PROFILE_JOB_POSIITON_ATTACHMENTS_UPLOAD_FOLDER', 'plugins/Hr_profile/Uploads/job_position/');    
}
if(!defined('HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER')){
    define('HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER', 'plugins/Hr_profile/Uploads/q_a/');    
}
if(!defined('HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER')){
    define('HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER', 'plugins/Hr_profile/Uploads/att_file/');    
}
if(!defined('HR_PROFILE_ERROR')){
    define('HR_PROFILE_ERROR', 'plugins/Hr_profile/Uploads/file_error_response/');    
}
if(!defined('HR_PROFILE_CREATE_EMPLOYEES_SAMPLE')){
    define('HR_PROFILE_CREATE_EMPLOYEES_SAMPLE', 'plugins/Hr_profile/Uploads/employees_sample_file/');    
}
if(!defined('HR_PROFILE_CONTRACT_SIGN')){
    define('HR_PROFILE_CONTRACT_SIGN', 'plugins/Hr_profile/Uploads/contract_sign/');    
}

app_hooks()->add_action('app_hook_head_extension', function (){
	$viewuri = $_SERVER['REQUEST_URI'];
	if (!(strpos($viewuri, '/hr_profile') === false)) {  
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/main.css') .'?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if(!(strpos($viewuri,'/hr_profile') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/style.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if(!(strpos($viewuri,'/hr_profile/organizational_chart') === false) || !(strpos($viewuri,'/hr_profile/staff_infor') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/plugins/ComboTree/style.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/style.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . base_url('plugins/Hr_profile//assets/plugins/OrgChart-master/jquery.orgchart.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}


	if(!(strpos($viewuri,'/hr_profile/organizational_chart') === false) || !(strpos($viewuri,'/hr_profile/staff_infor') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/organizational/organizational.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
		echo '<link href="https://fonts.googleapis.com/css?family=Gochi+Hand" rel="stylesheet">';
	}

	if(!(strpos($viewuri,'/hr_profile/training') === false)){
		if(!(strpos($viewuri,'insurrance') === false)){
			echo '<link href="' . base_url('plugins/Hr_profile/assets/css/setting/insurrance.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />'; 
		}
	}

	if(!(strpos($viewuri,'/hr_profile/job_position_view_edit') === false) || !(strpos($viewuri,'/hr_profile/job_positions') === false)|| !(strpos($viewuri,'/hr_profile/reception_staff') === false)|| !(strpos($viewuri,'/hr_profile/training') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/job/job_position_view_edit.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if(!(strpos($viewuri,'/hr_profile/member') === false) || !(strpos($viewuri,'/hr_profile/new_member') === false)|| !(strpos($viewuri,'/hr_profile/staff_infor') === false)){
		if(!(strpos($viewuri,'profile') === false)){
			echo '<link href="' . base_url('plugins/Hr_profile/assets/css/hr_record/includes/profile.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
		}
	}

	if(!(strpos($viewuri,'/hr_profile/import_job_p') === false) || !(strpos($viewuri,'/hr_profile/import_xlsx_dependent_person') === false) || !(strpos($viewuri,'/hr_profile/importxlsx') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/box_loading/box_loading.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if(!(strpos($viewuri,'/hr_profile/contracts') === false) || !(strpos($viewuri,'/hr_profile/staff_infor') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/plugins/ComboTree/style.css') .'?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/ribbons.css') .'?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if( !(strpos($viewuri,'/hr_profile/staff_infor') === false)){
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/hr_record/hr_record.css') .'?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, '/hr_profile/contract') === false)) {  
		echo '<link href="' . base_url('plugins/Hr_profile/assets/plugins/handsontable/handsontable.full.min.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . base_url('plugins/Hr_profile/assets/plugins/handsontable/chosen.css') . '"  rel="stylesheet" type="text/css" />';
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/handsontable/handsontable.full.min.js') . '"></script>';
		
	}

	if (!(strpos($viewuri, '/hr_profile/dashboard') === false)) { 
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/dashboard/dashboard.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}


	if (!(strpos($viewuri, '/hr_profile/contract_templates') === false)) {
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/setting/contract_template.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';

	}

	if (!(strpos($viewuri, '/hr_profile/position_training') === false)) {
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/training/position_training.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, '/hr_profile/training_detail') === false)) {
		echo '<link href="' . base_url('plugins/Hr_profile/assets/css/training/training_post.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';
	}
});

app_hooks()->add_action('app_hook_head_extension', function (){
	$viewuri = $_SERVER['REQUEST_URI'];

	if (!(strpos($viewuri, '/hr_profile') === false)) {   
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/main/main.js').'?v=' . HR_PROFILE_REVISION.'"></script>';

		/*load tynimce*/
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/tinymce/tinymce.min.js').'?v=' . HR_PROFILE_REVISION.'"></script>';

	}
	
	if (!(strpos($viewuri, '/hr_profile') === false)) {   
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/main/circle-progress.min.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/dashboard') === false)){

		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/highcharts.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/variable-pie.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/export-data.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/accessibility.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/exporting.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/highcharts-3d.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/reports') === false)){

		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/highcharts.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/exporting.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/highcharts/series-label.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}


	//settings
	if(!(strpos($viewuri,'/hr_profile/setting?group=contract_type') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/contract_type.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=allowance_type') === false)){

		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/allowance_type.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=payroll') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/payroll.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=type_of_training') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/type_of_training.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=income_tax_individual') === false)){
		echo '<script src="https://cdn.jsdelivr.net/npm/handsontable@7.2.2/dist/handsontable.full.min.js"></script>';
		echo '<link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@7.2.2/dist/handsontable.full.min.css">';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=procedure_retire') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/procedure_retire.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=salary_type') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/salary_type.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/setting?group=workplace') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/setting/workplace.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}


	if(!(strpos($viewuri,'/hr_profile/training') === false)){
		if(!(strpos($viewuri,'training_library') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/training/training_library.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
	}

	if(!(strpos($viewuri,'/hr_profile/job_position_manage') === false)){
	}
	if(!(strpos($viewuri,'/hr_profile/job_positions') === false)){
		
	}

	if(!(strpos($viewuri,'/hr_profile/job_position_view_edit') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/js/job_position/job_position_view_edit.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
	}

	if(!(strpos($viewuri,'/hr_profile/member') === false)){
		if(!(strpos($viewuri,'insurrance') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/insurrance.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
		if(!(strpos($viewuri,'income_tax') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/income_tax.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
		if(!(strpos($viewuri,'profile') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/profile.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}

		if(!(strpos($viewuri,'dependent_person') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/dependent_person.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
		if(!(strpos($viewuri,'bonus_discipline') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/bonus_discipline.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
		if(!(strpos($viewuri,'application_submitted') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/application_submitted.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
		if(!(strpos($viewuri,'attach') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/attach.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
		if(!(strpos($viewuri,'permission') === false)){
			echo '<script src="'.base_url('plugins/Hr_profile/assets/js/hr_record/includes/permission.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		}
	}

	if(!(strpos($viewuri,'/hr_profile/contracts') === false) || !(strpos($viewuri,'/hr_profile/staff_infor') === false)|| !(strpos($viewuri,'/hr_profile/organizational_chart') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/ComboTree/icontains.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/OrgChart-master/jquery.orgchart.js').'?v=' . HR_PROFILE_REVISION.'"></script>';

	}

	if(!(strpos($viewuri,'/hr_profile/contracts') === false) || !(strpos($viewuri,'/hr_profile/staff_infor') === false) || !(strpos($viewuri,'/hr_profile/organizational_chart') === false)){
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . HR_PROFILE_REVISION.'"></script>';
		echo '<script src="'.base_url('plugins/Hr_profile/assets/plugins/ComboTree/comboTreePlugin.js').'?v=' . HR_PROFILE_REVISION.'"></script>';


	}

	if (!(strpos($viewuri, '/hr_profile/contract') === false)) {
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/handsontable/chosen.jquery.js') . '"></script>';
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/handsontable/handsontable-chosen-editor.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/hr_profile/contract_sign') === false)) {   
		echo '<script src="' . base_url('plugins/Hr_profile/assets/plugins/signature_pad.min.js') . '"></script>';
	}
});


app_hooks()->add_action('app_hook_role_permissions_extension_plugins', function ($permissions){
	$viewuri = $_SERVER['REQUEST_URI'];

	$permission_data = [];
	if((strpos($viewuri,'hr_profile/role_permissions') === false)){
		/*dashboard*/
		$permission_data['hr_profile_can_view_global_hr_dashboard'] = get_array_value($permissions, "hr_profile_can_view_global_hr_dashboard");

		/*organizational_chart*/
		$permission_data['hr_profile_can_view_own_organizational_chart'] = get_array_value($permissions, "hr_profile_can_view_own_organizational_chart");
		$permission_data['hr_profile_can_view_global_organizational_chart'] = get_array_value($permissions, "hr_profile_can_view_global_organizational_chart");
		$permission_data['hr_profile_can_create_organizational_chart'] = get_array_value($permissions, "hr_profile_can_create_organizational_chart");
		$permission_data['hr_profile_can_edit_organizational_chart'] = get_array_value($permissions, "hr_profile_can_edit_organizational_chart");
		$permission_data['hr_profile_can_delete_organizational_chart'] = get_array_value($permissions, "hr_profile_can_delete_organizational_chart");

		/*onboarding*/
		$permission_data['hr_profile_can_view_own_onboarding'] = get_array_value($permissions, "hr_profile_can_view_own_onboarding");
		$permission_data['hr_profile_can_view_global_onboarding'] = get_array_value($permissions, "hr_profile_can_view_global_onboarding");
		$permission_data['hr_profile_can_create_onboarding'] = get_array_value($permissions, "hr_profile_can_create_onboarding");
		$permission_data['hr_profile_can_edit_onboarding'] = get_array_value($permissions, "hr_profile_can_edit_onboarding");
		$permission_data['hr_profile_can_delete_onboarding'] = get_array_value($permissions, "hr_profile_can_delete_onboarding");
		/*hr_records*/
		$permission_data['hr_profile_can_view_own_hr_records'] = get_array_value($permissions, "hr_profile_can_view_own_hr_records");
		$permission_data['hr_profile_can_view_global_hr_records'] = get_array_value($permissions, "hr_profile_can_view_global_hr_records");
		$permission_data['hr_profile_can_create_hr_records'] = get_array_value($permissions, "hr_profile_can_create_hr_records");
		$permission_data['hr_profile_can_edit_hr_records'] = get_array_value($permissions, "hr_profile_can_edit_hr_records");
		$permission_data['hr_profile_can_delete_hr_records'] = get_array_value($permissions, "hr_profile_can_delete_hr_records");
		/*job_description*/
		$permission_data['hr_profile_can_view_own_job_description'] = get_array_value($permissions, "hr_profile_can_view_own_job_description");
		$permission_data['hr_profile_can_view_global_job_description'] = get_array_value($permissions, "hr_profile_can_view_global_job_description");
		$permission_data['hr_profile_can_create_job_description'] = get_array_value($permissions, "hr_profile_can_create_job_description");
		$permission_data['hr_profile_can_edit_job_description'] = get_array_value($permissions, "hr_profile_can_edit_job_description");
		$permission_data['hr_profile_can_delete_job_description'] = get_array_value($permissions, "hr_profile_can_delete_job_description");
		/*hr_training*/
		$permission_data['hr_profile_can_view_own_hr_training'] = get_array_value($permissions, "hr_profile_can_view_own_hr_training");
		$permission_data['hr_profile_can_view_global_hr_training'] = get_array_value($permissions, "hr_profile_can_view_global_hr_training");
		$permission_data['hr_profile_can_create_hr_training'] = get_array_value($permissions, "hr_profile_can_create_hr_training");
		$permission_data['hr_profile_can_edit_hr_training'] = get_array_value($permissions, "hr_profile_can_edit_hr_training");
		$permission_data['hr_profile_can_delete_hr_training'] = get_array_value($permissions, "hr_profile_can_delete_hr_training");
		/*hr_contract*/
		$permission_data['hr_profile_can_view_own_hr_contract'] = get_array_value($permissions, "hr_profile_can_view_own_hr_contract");
		$permission_data['hr_profile_can_view_global_hr_contract'] = get_array_value($permissions, "hr_profile_can_view_global_hr_contract");
		$permission_data['hr_profile_can_create_hr_contract'] = get_array_value($permissions, "hr_profile_can_create_hr_contract");
		$permission_data['hr_profile_can_edit_hr_contract'] = get_array_value($permissions, "hr_profile_can_edit_hr_contract");
		$permission_data['hr_profile_can_delete_hr_contract'] = get_array_value($permissions, "hr_profile_can_delete_hr_contract");
		/*dependent_persons*/
		$permission_data['hr_profile_can_view_own_dependent_persons'] = get_array_value($permissions, "hr_profile_can_view_own_dependent_persons");
		$permission_data['hr_profile_can_view_global_dependent_persons'] = get_array_value($permissions, "hr_profile_can_view_global_dependent_persons");
		$permission_data['hr_profile_can_create_dependent_persons'] = get_array_value($permissions, "hr_profile_can_create_dependent_persons");
		$permission_data['hr_profile_can_edit_dependent_persons'] = get_array_value($permissions, "hr_profile_can_edit_dependent_persons");
		$permission_data['hr_profile_can_delete_dependent_persons'] = get_array_value($permissions, "hr_profile_can_delete_dependent_persons");
		/*layoff_checklists*/
		$permission_data['hr_profile_can_view_own_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_view_own_layoff_checklists");
		$permission_data['hr_profile_can_view_global_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_view_global_layoff_checklists");
		$permission_data['hr_profile_can_create_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_create_layoff_checklists");
		$permission_data['hr_profile_can_edit_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_edit_layoff_checklists");
		$permission_data['hr_profile_can_delete_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_delete_layoff_checklists");
		/*_global_report*/
		$permission_data['hr_profile_can_view_global_report'] = get_array_value($permissions, "hr_profile_can_view_global_report");
		/*global_setting*/
		$permission_data['hr_profile_can_view_global_setting'] = get_array_value($permissions, "hr_profile_can_view_global_setting");
		$permission_data['hr_profile_can_create_setting'] = get_array_value($permissions, "hr_profile_can_create_setting");
		$permission_data['hr_profile_can_edit_setting'] = get_array_value($permissions, "hr_profile_can_edit_setting");
		$permission_data['hr_profile_can_delete_setting'] = get_array_value($permissions, "hr_profile_can_delete_setting");

		$Template = new Template(false);

		$ci = new Security_Controller(false);
		$access_hr_profile = get_array_value($permissions, "hr_profile");
		if (is_null($access_hr_profile)) {
			$access_hr_profile = "";
		}

		echo  $Template->view('Hr_profile\Views\includes/hr_permissions', $permission_data);
	}else{
		echo '';
	}
});

app_hooks()->add_filter('app_filter_role_permissions_save_data_plugin', function ($permissions,$data) {
	/*data*/
	$hr_profile_data=[];

	$hr_profile_data['hr_profile_can_view_global_hr_dashboard'] = isset($data['hr_profile_can_view_global_hr_dashboard']) ? $data['hr_profile_can_view_global_hr_dashboard'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_organizational_chart'] = isset($data['hr_profile_can_view_own_organizational_chart']) ? $data['hr_profile_can_view_own_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_organizational_chart'] = isset($data['hr_profile_can_view_global_organizational_chart']) ? $data['hr_profile_can_view_global_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_create_organizational_chart'] = isset($data['hr_profile_can_create_organizational_chart']) ? $data['hr_profile_can_create_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_edit_organizational_chart'] = isset($data['hr_profile_can_edit_organizational_chart']) ? $data['hr_profile_can_edit_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_delete_organizational_chart'] = isset($data['hr_profile_can_delete_organizational_chart']) ? $data['hr_profile_can_delete_organizational_chart'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_organizational_chart'] = isset($data['hr_profile_can_view_own_organizational_chart']) ? $data['hr_profile_can_view_own_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_organizational_chart'] = isset($data['hr_profile_can_view_global_organizational_chart']) ? $data['hr_profile_can_view_global_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_create_organizational_chart'] = isset($data['hr_profile_can_create_organizational_chart']) ? $data['hr_profile_can_create_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_edit_organizational_chart'] = isset($data['hr_profile_can_edit_organizational_chart']) ? $data['hr_profile_can_edit_organizational_chart'] : NULL;
	$hr_profile_data['hr_profile_can_delete_organizational_chart'] = isset($data['hr_profile_can_delete_organizational_chart']) ? $data['hr_profile_can_delete_organizational_chart'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_onboarding'] = isset($data['hr_profile_can_view_own_onboarding']) ? $data['hr_profile_can_view_own_onboarding'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_onboarding'] = isset($data['hr_profile_can_view_global_onboarding']) ? $data['hr_profile_can_view_global_onboarding'] : NULL;
	$hr_profile_data['hr_profile_can_create_onboarding'] = isset($data['hr_profile_can_create_onboarding']) ? $data['hr_profile_can_create_onboarding'] : NULL;
	$hr_profile_data['hr_profile_can_edit_onboarding'] = isset($data['hr_profile_can_edit_onboarding']) ? $data['hr_profile_can_edit_onboarding'] : NULL;
	$hr_profile_data['hr_profile_can_delete_onboarding'] = isset($data['hr_profile_can_delete_onboarding']) ? $data['hr_profile_can_delete_onboarding'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_hr_records'] = isset($data['hr_profile_can_view_own_hr_records']) ? $data['hr_profile_can_view_own_hr_records'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_hr_records'] = isset($data['hr_profile_can_view_global_hr_records']) ? $data['hr_profile_can_view_global_hr_records'] : NULL;
	$hr_profile_data['hr_profile_can_create_hr_records'] = isset($data['hr_profile_can_create_hr_records']) ? $data['hr_profile_can_create_hr_records'] : NULL;
	$hr_profile_data['hr_profile_can_edit_hr_records'] = isset($data['hr_profile_can_edit_hr_records']) ? $data['hr_profile_can_edit_hr_records'] : NULL;
	$hr_profile_data['hr_profile_can_delete_hr_records'] = isset($data['hr_profile_can_delete_hr_records']) ? $data['hr_profile_can_delete_hr_records'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_job_description'] = isset($data['hr_profile_can_view_own_job_description']) ? $data['hr_profile_can_view_own_job_description'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_job_description'] = isset($data['hr_profile_can_view_global_job_description']) ? $data['hr_profile_can_view_global_job_description'] : NULL;
	$hr_profile_data['hr_profile_can_create_job_description'] = isset($data['hr_profile_can_create_job_description']) ? $data['hr_profile_can_create_job_description'] : NULL;
	$hr_profile_data['hr_profile_can_edit_job_description'] = isset($data['hr_profile_can_edit_job_description']) ? $data['hr_profile_can_edit_job_description'] : NULL;
	$hr_profile_data['hr_profile_can_delete_job_description'] = isset($data['hr_profile_can_delete_job_description']) ? $data['hr_profile_can_delete_job_description'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_hr_training'] = isset($data['hr_profile_can_view_own_hr_training']) ? $data['hr_profile_can_view_own_hr_training'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_hr_training'] = isset($data['hr_profile_can_view_global_hr_training']) ? $data['hr_profile_can_view_global_hr_training'] : NULL;
	$hr_profile_data['hr_profile_can_create_hr_training'] = isset($data['hr_profile_can_create_hr_training']) ? $data['hr_profile_can_create_hr_training'] : NULL;
	$hr_profile_data['hr_profile_can_edit_hr_training'] = isset($data['hr_profile_can_edit_hr_training']) ? $data['hr_profile_can_edit_hr_training'] : NULL;
	$hr_profile_data['hr_profile_can_delete_hr_training'] = isset($data['hr_profile_can_delete_hr_training']) ? $data['hr_profile_can_delete_hr_training'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_hr_contract'] = isset($data['hr_profile_can_view_own_hr_contract']) ? $data['hr_profile_can_view_own_hr_contract'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_hr_contract'] = isset($data['hr_profile_can_view_global_hr_contract']) ? $data['hr_profile_can_view_global_hr_contract'] : NULL;
	$hr_profile_data['hr_profile_can_create_hr_contract'] = isset($data['hr_profile_can_create_hr_contract']) ? $data['hr_profile_can_create_hr_contract'] : NULL;
	$hr_profile_data['hr_profile_can_edit_hr_contract'] = isset($data['hr_profile_can_edit_hr_contract']) ? $data['hr_profile_can_edit_hr_contract'] : NULL;
	$hr_profile_data['hr_profile_can_delete_hr_contract'] = isset($data['hr_profile_can_delete_hr_contract']) ? $data['hr_profile_can_delete_hr_contract'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_dependent_persons'] = isset($data['hr_profile_can_view_own_dependent_persons']) ? $data['hr_profile_can_view_own_dependent_persons'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_dependent_persons'] = isset($data['hr_profile_can_view_global_dependent_persons']) ? $data['hr_profile_can_view_global_dependent_persons'] : NULL;
	$hr_profile_data['hr_profile_can_create_dependent_persons'] = isset($data['hr_profile_can_create_dependent_persons']) ? $data['hr_profile_can_create_dependent_persons'] : NULL;
	$hr_profile_data['hr_profile_can_edit_dependent_persons'] = isset($data['hr_profile_can_edit_dependent_persons']) ? $data['hr_profile_can_edit_dependent_persons'] : NULL;
	$hr_profile_data['hr_profile_can_delete_dependent_persons'] = isset($data['hr_profile_can_delete_dependent_persons']) ? $data['hr_profile_can_delete_dependent_persons'] : NULL;

	$hr_profile_data['hr_profile_can_view_own_layoff_checklists'] = isset($data['hr_profile_can_view_own_layoff_checklists']) ? $data['hr_profile_can_view_own_layoff_checklists'] : NULL;
	$hr_profile_data['hr_profile_can_view_global_layoff_checklists'] = isset($data['hr_profile_can_view_global_layoff_checklists']) ? $data['hr_profile_can_view_global_layoff_checklists'] : NULL;
	$hr_profile_data['hr_profile_can_create_layoff_checklists'] = isset($data['hr_profile_can_create_layoff_checklists']) ? $data['hr_profile_can_create_layoff_checklists'] : NULL;
	$hr_profile_data['hr_profile_can_edit_layoff_checklists'] = isset($data['hr_profile_can_edit_layoff_checklists']) ? $data['hr_profile_can_edit_layoff_checklists'] : NULL;
	$hr_profile_data['hr_profile_can_delete_layoff_checklists'] = isset($data['hr_profile_can_delete_layoff_checklists']) ? $data['hr_profile_can_delete_layoff_checklists'] : NULL;

	$hr_profile_data['hr_profile_can_view_global_report'] = isset($data['hr_profile_can_view_global_report']) ? $data['hr_profile_can_view_global_report'] : NULL;

	$hr_profile_data['hr_profile_can_view_global_setting'] = isset($data['hr_profile_can_view_global_setting']) ? $data['hr_profile_can_view_global_setting'] : NULL;
	$hr_profile_data['hr_profile_can_create_setting'] = isset($data['hr_profile_can_create_setting']) ? $data['hr_profile_can_create_setting'] : NULL;
	$hr_profile_data['hr_profile_can_edit_setting'] = isset($data['hr_profile_can_edit_setting']) ? $data['hr_profile_can_edit_setting'] : NULL;
	$hr_profile_data['hr_profile_can_delete_setting'] = isset($data['hr_profile_can_delete_setting']) ? $data['hr_profile_can_delete_setting'] : NULL;
	
	$viewuri = $_SERVER['REQUEST_URI'];
	if((strpos($viewuri,'hr_profile/role_permissions') === false)){
		$permissions = array_merge($permissions, $hr_profile_data);
	}

	return $permissions;
});

app_hooks()->add_filter('app_filter_notification_config', function ($events) {
	$hr_staff_training_link = function ($options) {
		$url = "";
		if (isset($options->hr_send_training_staff_id)) {
			$url = get_uri("hr_profile/staff_profile/" . $options->hr_send_training_staff_id.'/staff_training');
		}

		return array("url" => $url);
	};

	$hr_lay_off_checklist_link = function ($options) {
		$url = "";
		if (isset($options->hr_send_layoff_checklist_handle_staff_id)) {
			$url = get_uri("hr_profile/resignation_procedures?detail=" . $options->hr_send_layoff_checklist_handle_staff_id);
		}

		return array("url" => $url);
	};

	$events["hr_please_complete_the_tests_below_to_complete_the_training_program"] = [
			"notify_to" => array("team_members"),
			"info" => $hr_staff_training_link
	];

	$events["a_new_training_program_is_assigned_to_you"] = [
		"notify_to" => array("team_members"),
		"info" => $hr_staff_training_link
	];

	$events["hr_resignation_procedures_are_waiting_for_your_confirmation"] = [
			"notify_to" => array("team_members"),
			"info" => $hr_lay_off_checklist_link
	];

	return $events;
});


if(!defined('HR_VIEWPATH')){
    define('HR_VIEWPATH', 'plugins/Hr_profile');    
}


/*add menu item to left menu*/
app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
	$hr_profile_submenu = array();
	$ci = new Security_Controller(false);

	if ($ci->login_user->is_admin || hr_has_permission("hr_profile_can_view_global_hr_dashboard") || hr_has_permission("hr_profile_can_view_own_organizational_chart") || hr_has_permission("hr_profile_can_view_global_organizational_chart") || hr_has_permission("hr_profile_can_view_own_onboarding") || hr_has_permission("hr_profile_can_view_global_onboarding") || hr_has_permission("hr_profile_can_view_own_hr_records") || hr_has_permission("hr_profile_can_view_global_hr_records") || hr_has_permission("hr_profile_can_view_own_job_description") || hr_has_permission("hr_profile_can_view_global_job_description") || hr_has_permission("hr_profile_can_view_own_hr_training") || hr_has_permission("hr_profile_can_view_global_hr_training") || hr_has_permission("hr_profile_can_view_own_hr_contract") || hr_has_permission("hr_profile_can_view_global_hr_contract") || hr_has_permission("hr_profile_can_view_own_dependent_persons") || hr_has_permission("hr_profile_can_view_global_dependent_persons") || hr_has_permission("hr_profile_can_view_own_layoff_checklists") || hr_has_permission("hr_profile_can_view_global_layoff_checklists") || hr_has_permission("hr_profile_can_view_global_report") || hr_has_permission("hr_profile_can_view_global_setting") ) {

		if(hr_has_permission('hr_profile_can_view_global_hr_dashboard')){
			$hr_profile_submenu["dashboard"] = array(
				"name" => "hr_dashboard", 
				"url" => "hr_profile/dashboard", 
				"class" => "users",
			);
		}

		if(hr_has_permission('hr_profile_can_view_own_job_description') || hr_has_permission('hr_profile_can_view_global_job_description') ){

			$hr_profile_submenu["job_positions"] = array(
				"name" => "hr_job_descriptions", 
				"url" => "hr_profile/job_positions", 
				"class" => "users",
			);
		}

		if(hr_has_permission('hr_profile_can_view_own_organizational_chart') || hr_has_permission('hr_profile_can_view_global_organizational_chart') ){
			$hr_profile_submenu["organizational_chart"] = array(
				"name" => "hr_organizational_chart", 
				"url" => "hr_profile/organizational_chart", 
				"class" => "users",
			);
		}
		
		if(hr_has_permission('hr_profile_can_view_own_onboarding') || hr_has_permission('hr_profile_can_view_global_onboarding')){
			$hr_profile_submenu["reception_staff"] = array(
				"name" => "hr_receiving_staff_lable", 
				"url" => "hr_profile/reception_staff", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_own_hr_records') || hr_has_permission('hr_profile_can_view_global_hr_records')){

			$hr_profile_submenu["staff_infor"] = array(
				"name" => "hr_hr_records", 
				"url" => "hr_profile/staff_infor", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_own_hr_training') || hr_has_permission('hr_profile_can_view_global_hr_training')){

			$hr_profile_submenu["training_program"] = array(
				"name" => "hr_training", 
				"url" => "hr_profile/training_programs", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_own_hr_contract') || hr_has_permission('hr_profile_can_view_global_hr_contract')){

			$hr_profile_submenu["contracts"] = array(
				"name" => "hr_hr_contracts", 
				"url" => "hr_profile/contracts", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_own_dependent_persons') || hr_has_permission('hr_profile_can_view_global_dependent_persons')){

			$hr_profile_submenu["dependent_persons"] = array(
				"name" => "hr_dependent_persons", 
				"url" => "hr_profile/dependent_persons", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_own_layoff_checklists') || hr_has_permission('hr_profile_can_view_global_layoff_checklists')){

			$hr_profile_submenu["resignation_procedures"] = array(
				"name" => "hr_resignation_procedures", 
				"url" => "hr_profile/resignation_procedures", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_global_report')){

			$hr_profile_submenu["reports"] = array(
				"name" => "hr_reports", 
				"url" => "hr_profile/reports", 
				"class" => "users",
			);
		}
		if(hr_has_permission('hr_profile_can_view_global_setting')){
			$hr_profile_submenu["contract_type"] = array(
				"name" => "hr_settings", 
				"url" => "hr_profile/contract_types", 
				"class" => "users",
			);

		}
		$sidebar_menu["hr_profile"] = array(
			"name" => "hr_profile",
			"url" => "hr_profile",
			"class" => "users",
			"submenu" => $hr_profile_submenu,
			"position" => 7,

		);
	}

	return $sidebar_menu;

});


/*install dependencies*/
register_installation_hook("Hr_profile", function ($item_purchase_code) {
/*
* you can verify the item puchase code from here if you want. 
* you'll get the inputted puchase code with $item_purchase_code variable
* use exit(); here if there is anything doesn't meet it's requirements
*/
include PLUGINPATH . "Hr_profile/lib/gtsverify.php";
require_once(__DIR__ . '/install.php');
});

/*Active action*/
register_activation_hook("Hr_profile", function ($item_purchase_code) {	
	require_once(__DIR__ . '/install.php');
});

/*add setting link to the plugin setting*/
app_hooks()->add_filter('app_filter_action_links_of_Hr_profile', function () {
	$action_links_array = array(
	);

	return $action_links_array;
});

/*update plugin*/
register_update_hook("Hr_profile", function () {
	require_once __DIR__ . '/install.php';
});

/*uninstallation: remove data from database*/
register_uninstallation_hook("Hr_profile", function () {
	require_once __DIR__ . '/uninstall.php';
});
app_hooks()->add_action('app_hook_hrrecord_init', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_hrrecord = new HRRecordLic();
    $hrrecord_gtssres = $lic_hrrecord->verify_license(true);    
    if(!$hrrecord_gtssres || ($hrrecord_gtssres && isset($hrrecord_gtssres['status']) && !$hrrecord_gtssres['status'])){
        echo '<strong>YOUR HR RECORD PLUGIN FAILED ITS VERIFICATION. PLEASE <a href="/index.php/Plugins">REINSTALL</a> OR CONTACT SUPPORT</strong>';
        exit();
    } 
});
app_hooks()->add_action('app_hook_uninstall_plugin_Hr_profile', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_hrrecord = new HRRecordLic();
    $lic_hrrecord->deactivate_license();    
});