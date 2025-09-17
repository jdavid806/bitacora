<?php
defined('PLUGINPATH') or exit('No direct script access allowed');

/*
  Plugin Name: Marketing Automation
  Description: This plugin helps you to identify potential customers, automating the process of nurturing those leads to sales-readiness.
  Version: 1.0.0
  Author: GreenTech Solutions  
  Author URI: https://codecanyon.net/user/greentech_solutions
*/
use App\Controllers\Security_Controller;

if(!defined('MA_REVISION')){
    define('MA_REVISION', 1002);    
}

if(!defined('TEMP_FOLDER')){
    define('TEMP_FOLDER', ROOTPATH . 'files/temp' . '/');    
}

//add menu item to left menu
app_hooks()->add_filter('app_filter_staff_left_menu', function ($sidebar_menu) {

    $ma_submenu = array();
    if (ma_has_permission('dashboard_view') || ma_has_permission('segments_view') || ma_has_permission('components_view') || ma_has_permission('campaigns_view') || ma_has_permission('channels_view') || ma_has_permission('points_view') || ma_has_permission('stages_view') || ma_has_permission('reports_view') || ma_has_permission('settings_view')) {

        if(ma_has_permission('dashboard_view')){
            $ma_submenu["ma_dashboard"] = array(
                "name" => "dashboard", 
                "url" => "ma/dashboard", 
                "class" => "home"
            );
        }

        if(ma_has_permission('segments_view')){
            $ma_submenu["ma_segments"] = array(
                "name" => "segments", 
                "url" => "ma/segments", 
                "class" => "repeat"
            );
        }

        if(ma_has_permission('components_view')){
            $ma_submenu["ma_components"] = array(
                "name" => "components", 
                "url" => "ma/components?group=assets", 
                "class" => "repeat"
            );
        }

        if(ma_has_permission('campaigns_view')){
            $ma_submenu["ma_campaigns"] = array(
                "name" => "campaigns", 
                "url" => "ma/campaigns", 
                "class" => "list"
            );
        }

        if(ma_has_permission('channels_view')){
            $ma_submenu["ma_channels"] = array(
                "name" => "channels", 
                "url" => "ma/channels", 
                "class" => "repeat"
            );
        }

        if(ma_has_permission('points_view')){
            $ma_submenu["ma_points"] = array(
                "name" => "points", 
                "url" => "ma/points?group=point_actions", 
                "class" => "home"
            );
        }

        if(ma_has_permission('stages_view')){
            $ma_submenu["ma_stages"] = array(
                "name" => "stages", 
                "url" => "ma/stages", 
                "class" => "home"
            );
        }

        if(ma_has_permission('reports_view')){
            $ma_submenu["ma_reports"] = array(
                "name" => "reports", 
                "url" => "ma/reports", 
                "class" => "home"
            );
        }

        if(ma_has_permission('settings_view')){
            $ma_submenu["ma_setting"] = array(
                "name" => "setting", 
                "url" => "ma/setting?group=category", 
                "class" => "home"
            );
        }

        $sidebar_menu["ma"] = array(
            "name" => "marketing_automation",
            "url" => "ma/dashboard",
            "class" => "hash",
            "submenu" => $ma_submenu,
            "position" => 3,
        );
    }


    return $sidebar_menu;
});


//install dependencies
register_installation_hook("Ma", function ($item_purchase_code) {      
    include PLUGINPATH . "Ma/lib/gtsverify.php";
    require_once __DIR__ . '/install.php';
});

//activation
register_activation_hook("Ma", function ($item_purchase_code) {    
    require_once __DIR__ . '/install.php';
});


//update plugin
register_update_hook("Ma", function () {
    require_once __DIR__ . '/install.php';
});

//uninstallation: remove data from database
register_uninstallation_hook("Ma", function () {    
    require_once __DIR__ . '/uninstall.php';
});
app_hooks()->add_action('app_hook_ma_init', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_ma = new MaLic();
    $ma_gtssres = $lic_ma->verify_license(true);    
    if(!$ma_gtssres || ($ma_gtssres && isset($ma_gtssres['status']) && !$ma_gtssres['status'])){
        echo '<strong>YOUR MARKETING AUTOMATION PLUGIN FAILED ITS VERIFICATION. PLEASE <a href="/index.php/Plugins">REINSTALL</a> OR CONTACT SUPPORT</strong>';
        exit();
    } 
});
app_hooks()->add_action('app_hook_uninstall_plugin_Ma', function (){
    require_once __DIR__ .'/lib/gtsslib.php';
    $lic_ma = new MaLic();
    $lic_ma->deactivate_license();    
});


/**
 * init add head component
 */
app_hooks()->add_action('app_hook_head_extension', function (){
    $viewuri = $_SERVER['REQUEST_URI'];
    echo '<link href="' . base_url('plugins/Ma/assets/css/menu_custom.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';

    if (!(strpos($viewuri, '/ma/') === false) || !(strpos($viewuri, '/ma_forms/') === false)) {
        echo '<link href="' . base_url('plugins/Ma/assets/css/main.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . base_url('plugins/Ma/assets/css/custom.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/ma/campaign_detail') === false)) {
        echo '<link href="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/docs/drawflow.min.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/docs/beautiful.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/ma/workflow_builder') === false)) {
        echo '<link href="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/docs/drawflow.min.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
        echo '<link href="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/docs/beautiful.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/ma/email_template_design') === false)) {
        echo '<link href="' . base_url('plugins/Ma/assets/plugins/react-email-editor-master/src/style.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }

    if (!(strpos($viewuri, '/ma/email_design') === false)) {
        echo '<link href="' . base_url('plugins/Ma/assets/plugins/react-email-editor-master/src/style.css') . '?v=' . MA_REVISION . '"  rel="stylesheet" type="text/css" />';
    }
});

/**
 * init add footer component
 */
app_hooks()->add_action('app_hook_head_extension', function(){
    $viewuri = $_SERVER['REQUEST_URI'];

    if (!(strpos($viewuri, '/ma/') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/js/main.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/stages') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }


    if (!(strpos($viewuri, '/ma/segments') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }
    
    if (!(strpos($viewuri, '/ma/segment_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/points?group=point_triggers') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/js/points/point_triggers_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/marketing_message') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/js/channels/marketing_message.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/channels?group=marketing_messages') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/js/channels/marketing_messages_manage.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/text_message') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/js/channels/text_message.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/sms_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/src/drawflow.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/campaigns') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/campaign_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/src/drawflow.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/workflow_builder') === false)) {
       echo '<script src="' . base_url('plugins/Ma/assets/plugins/Drawflow-master/src/drawflow.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/email_template_design') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/react-email-editor-master/src/loadScript.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/email_template_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/asset_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/point_action_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/dashboard') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/stage_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/text_message_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/asset_report') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/lead_and_point_report') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/form_report') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/email_report') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/sms_report') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/campaign_report') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/email_detail') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/variable-pie.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/export-data.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/accessibility.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/modules/exporting.js') . '"></script>';
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/highcharts/highcharts-3d.js') . '"></script>';
    }

    if (!(strpos($viewuri, '/ma/email_design') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/react-email-editor-master/src/loadScript.js') . '?v=' . MA_REVISION . '"></script>';
    }

    if (!(strpos($viewuri, '/ma_forms/wtl/') === false)) {
        echo '<script src="' . base_url('plugins/Ma/assets/plugins/validation/app-form-validation.js') . '?v=' . MA_REVISION . '"></script>';
    }

});


app_hooks()->add_action('app_hook_after_cron_run', function(){
    $Ma_model = model("Ma\Models\Ma_model"); 
    $Ma_model->ma_cron_campaign();
});

app_hooks()->add_filter('app_filter_lead_details_ajax_tab', function ($hook_tabs, $lead_id) {
    $hook_tabs[] = [
        'title' => _l('campaigns'),
        'url' => get_uri('ma/lead_campaign_tab/'.$lead_id),
        'target' => 'ma_campaigns',
    ];

    return $hook_tabs;
});