<?php
defined('PLUGINPATH') or exit('No direct script access allowed');

/*
Plugin Name: Recruitment
Description: Recruitment Management plugin
Version: 1.0.0
Requires at least: 3.0
Author: GreenTech Solutions
Author URI: https://codecanyon.net/user/greentech_solutions
*/
use App\Libraries\Template;
use App\Controllers\Security_Controller;

if(!defined('RE_REVISION')){
	define('RE_REVISION', 100);
}

/*Modules Path*/
if(!defined('APP_MODULES_PATH')){
	define('APP_MODULES_PATH', FCPATH . 'plugins/');
}
if(!defined('RECRUITMENT_MODULE_UPLOAD_FOLDER')){
	define('RECRUITMENT_MODULE_UPLOAD_FOLDER', 'plugins/Recruitment/Uploads');
}
if(!defined('RECRUITMENT_CAMPAIGN_MODULE_UPLOAD_FOLDER')){
	define('RECRUITMENT_CAMPAIGN_MODULE_UPLOAD_FOLDER', 'plugins/Recruitment/Uploads/campaign');
}
if(!defined('RECRUITMENT_PLAN_MODULE_UPLOAD_FOLDER')){
	define('RECRUITMENT_PLAN_MODULE_UPLOAD_FOLDER', 'plugins/Recruitment/Uploads/proposal');
}

if(!defined('RECRUITMENT_CANDIDATE_FILE_MODULE_UPLOAD_FOLDER')){
	define('RECRUITMENT_CANDIDATE_FILE_MODULE_UPLOAD_FOLDER', 'plugins/Recruitment/Uploads/candidate/files');
}
if(!defined('RECRUITMENT_PATH')){
	define('RECRUITMENT_PATH', 'plugins/Recruitment/Uploads/');
}
if(!defined('RECRUITMENT_COMPANY_UPLOAD')){
	define('RECRUITMENT_COMPANY_UPLOAD', 'plugins/Recruitment/Uploads/company_images/');
}
if(!defined('TEMFOLDER_EXPORT_CANDIDATE')){
	define('TEMFOLDER_EXPORT_CANDIDATE', 'plugins/Recruitment/Uploads/export_candidate/');
}
if(!defined('CANDIDATE_IMAGE_UPLOAD')){
	define('CANDIDATE_IMAGE_UPLOAD', 'plugins/Recruitment/Uploads/candidate/avartar/');
}
if(!defined('CANDIDATE_CV_UPLOAD')){
	define('CANDIDATE_CV_UPLOAD', 'plugins/Recruitment/Uploads/candidate/files/');
}

if(!defined('SET_TRANSFER_UPLOAD')){
	define('SET_TRANSFER_UPLOAD', 'plugins/Recruitment/Uploads/set_transfer/');
}
if(!defined('EXT')){
	define('EXT', '.php');
}

if(!defined('RECRUITMENT_VIEWPATH')){
    define('RECRUITMENT_VIEWPATH', 'plugins/Recruitment');    
}

app_hooks()->add_filter('app_hook_head_extension', function () {
	$viewuri = $_SERVER['REQUEST_URI'];
	/*add css file*/
	if (!(strpos($viewuri, '/recruitment') === false) || !(strpos($viewuri, 'index.php/forms/') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/styles.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/main.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
		
	}
	if (!(strpos($viewuri, '/recruitment/dashboard') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/dashboard.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/candidates') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/candidate.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/candidate') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/candidate_detail.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/setting') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/setting.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/interview_schedule') === false) || !(strpos($viewuri, '/recruitment/view_interview_schedule') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/interview_schedule_preview.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/recruitment_campaign') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/campaign_preview.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/candidate_profile') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/candidate_profile.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/recruitment_proposal') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/recruitment_proposal.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/recruitment_campaign') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/recruitment_proposal.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, '/recruitment/setting?group=company') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/company.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}

	if (!(strpos($viewuri, '/recruitment/recruitment_portal/job_detail') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/recruitment_proposal.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, 'index.php/recruitment_portal') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/recruitment_portal.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	if (!(strpos($viewuri, 'index.php/recruitment_portal/job_detail') === false)) {
		echo '<link href="' . base_url('plugins/Recruitment/assets/css/recruitment_portal.css') . '?v=' . RE_REVISION . '"  rel="stylesheet" type="text/css" />';
	}
	
});

app_hooks()->add_filter('app_hook_head_extension', function () {
	$viewuri = $_SERVER['REQUEST_URI'];
	/*add js file*/

	if (!(strpos($viewuri, '/recruitment') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/main/main.js') . '?v=' . RE_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, '/recruitment/dashboard') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/highcharts/highcharts.js') . '"></script>';
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
		echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
	}

	if (!(strpos($viewuri, '/recruitment/setting?group=evaluation_criteria') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/evaluation_criteria.js') . '?v=' . RE_REVISION . '"></script>';
	}
	if (!(strpos($viewuri, '/recruitment/setting?group=evaluation_form') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/evaluation_form.js') . '?v=' . RE_REVISION . '"></script>';
	}
	if (!(strpos($viewuri, '/recruitment/setting?group=job_position') === false) || !(strpos($viewuri, '/recruitment/setting') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/job_position.js') . '?v=' . RE_REVISION . '"></script>';
	}
	if (!(strpos($viewuri, '/recruitment/setting?group=tranfer_personnel') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/tranfer_personnel.js') . '?v=' . RE_REVISION . '"></script>';
	}

	
	if (!(strpos($viewuri, '/recruitment/calendar_interview_schedule') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/interview_schedule.js') . '?v=' . RE_REVISION . '"></script>';
	}
	if (!(strpos($viewuri, '/recruitment/setting?group=skills') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/skill.js') . '?v=' . RE_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, '/recruitment/setting?group=recruitment_campaign_setting') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/recruitment_campaign_setting.js') . '?v=' . RE_REVISION . '"></script>';
	}

	if (!(strpos($viewuri, '/recruitment/setting?group=industry_list') === false)) {
		echo '<script src="' . base_url('plugins/Recruitment/assets/js/industry.js') . '?v=' . RE_REVISION . '"></script>';
	}


	if (!(strpos($viewuri, 'index.php/forms/wtl/') === false)) {
        echo '<script src="' . base_url('plugins/Recruitment/assets/plugins/validation/app-form-validation.js') . '?v=' . RE_REVISION . '"></script>';
    }
});

app_hooks()->add_action('app_hook_role_permissions_extension_plugins', function ($permissions){
	$viewuri = $_SERVER['REQUEST_URI'];

	$permission_data = [];
	if((strpos($viewuri,'recruitment/role_permissions') === false)){

		$permission_data['recruitment_can_view_global'] = get_array_value($permissions, "recruitment_can_view_global");
		$permission_data['recruitment_can_create'] = get_array_value($permissions, "recruitment_can_create");
		$permission_data['recruitment_can_edit'] = get_array_value($permissions, "recruitment_can_edit");
		$permission_data['recruitment_can_delete'] = get_array_value($permissions, "recruitment_can_delete");

		$Template = new Template(false);

		$ci = new Security_Controller(false);
		$access_recruitment = get_array_value($permissions, "recruitment");
		if (is_null($access_recruitment)) {
			$access_recruitment = "";
		}

		echo  $Template->view('Recruitment\Views\includes/recruitment_permissions', $permission_data);
	}else{
		echo '';
	}
});

app_hooks()->add_filter('app_filter_role_permissions_save_data_plugin', function ($permissions,$data) {
	/*data*/
	$recruitment_data=[];

	$recruitment_data['recruitment_can_view_global'] = isset($data['recruitment_can_view_global']) ? $data['recruitment_can_view_global'] : NULL;
	$recruitment_data['recruitment_can_create'] = isset($data['recruitment_can_create']) ? $data['recruitment_can_create'] : NULL;
	$recruitment_data['recruitment_can_edit'] = isset($data['recruitment_can_edit']) ? $data['recruitment_can_edit'] : NULL;
	$recruitment_data['recruitment_can_delete'] = isset($data['recruitment_can_delete']) ? $data['recruitment_can_delete'] : NULL;
	
	$viewuri = $_SERVER['REQUEST_URI'];
	if((strpos($viewuri,'recruitment/role_permissions') === false)){
		$permissions = array_merge($permissions, $recruitment_data);
	}

	return $permissions;
});

app_hooks()->add_filter('app_filter_notification_config', function ($events) {

	return $events;
});


/*add menu item to left menu*/
app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {
	$recruitment_submenu = array();
	$ci = new Security_Controller(false);
	$permissions = $ci->login_user->permissions;

	if ($ci->login_user->is_admin || re_has_permission("recruitment_can_view_global")) {
		$recruitment_submenu["dashboard"] = array(
			"name" => "dashboard", 
			"url" => "recruitment/dashboard", 
			"class" => "book",
		);

		if (get_setting('recruitment_create_campaign_with_plan') == 1) {
			$recruitment_submenu["organizational_chart"] = array(
				"name" => "_proposal", 
				"url" => "recruitment/recruitment_proposal", 
				"class" => "home",
			);
		}
		
		$recruitment_submenu["reception_staff"] = array(
			"name" => "campaign", 
			"url" => "recruitment/recruitment_campaign", 
			"class" => "o",
		);
		$recruitment_submenu["staff_infor"] = array(
			"name" => "candidate_profile", 
			"url" => "recruitment/candidate_profile", 
			"class" => "sitemap",
		);
		$recruitment_submenu["job_positions"] = array(
			"name" => "interview_schedule", 
			"url" => "recruitment/interview_schedule", 
			"class" => "o",
		);
		$recruitment_submenu["training_program"] = array(
			"name" => "_recruitment_channel", 
			"url" => "recruitment/recruitment_channel", 
			"class" => "calendar",
		);
		$recruitment_submenu["knowledge_base_q_a"] = array(
			"name" => "recruitment_portal", 
			"url" => "recruitment_portal", 
			"class" => "feed",
		);
		$recruitment_submenu["contracts"] = array(
			"name" => "setting", 
			"url" => "recruitment/job_positions", 
			"class" => "icon",
		);

		$sidebar_menu["recruitment"] = array(
			"name" => "recruitments",
			"url" => "recruitment",
			"class" => "book",
			"submenu" => $recruitment_submenu,
			"position" => 7,
		);
	}

	return $sidebar_menu;

});


/*install dependencies*/
register_installation_hook("Recruitment", function ($item_purchase_code) {
/*
* you can verify the item puchase code from here if you want. 
* you'll get the inputted puchase code with $item_purchase_code variable
* use exit(); here if there is anything doesn't meet it's requirements
*/
include PLUGINPATH . "Recruitment/lib/gtsverify.php";
require_once(__DIR__ . '/install.php');
});

/*Active action*/
register_activation_hook("Recruitment", function ($item_purchase_code) {
	require_once(__DIR__ . '/install.php');
});

/*add setting link to the plugin setting*/
app_hooks()->add_filter('app_filter_action_links_of_Recruitment', function () {
	$action_links_array = array(
	);

	return $action_links_array;
});

/*update plugin*/
register_update_hook("Recruitment", function () {
	require_once __DIR__ . '/install.php';
});

/*uninstallation: remove data from database*/
register_uninstallation_hook("Recruitment", function () {
	require_once __DIR__ . '/uninstall.php';
});

app_hooks()->add_action('app_hook_recruitment_init', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_rec = new RecruitmentLic();
    $rec_gtssres = $lic_rec->verify_license(true);    
    if(!$rec_gtssres || ($rec_gtssres && isset($rec_gtssres['status']) && !$rec_gtssres['status'])){
        echo '<strong>YOUR RECRUITMENT MANAGEMENT PLUGIN FAILED ITS VERIFICATION. PLEASE <a href="/index.php/Plugins">REINSTALL</a> OR CONTACT SUPPORT</strong>';
        exit();
    } 
});
app_hooks()->add_action('app_hook_uninstall_plugin_Recruitment', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_rec = new RecruitmentLic();
    $lic_rec->deactivate_license();    
});
