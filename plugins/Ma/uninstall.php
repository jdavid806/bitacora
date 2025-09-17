<?php
$db = db_connect('default');
$dbprefix = get_db_prefix();

if ($db->tableExists($dbprefix . 'ma_categories')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_categories`;');
}

if ($db->tableExists($dbprefix . 'ma_stages')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_stages`;');
}

if ($db->tableExists($dbprefix . 'ma_segments')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_segments`;');
}

if ($db->tableExists($dbprefix . 'ma_segment_filters')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_segment_filters`;');
}

if ($db->tableExists($dbprefix . 'ma_forms')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_forms`;');
}

if ($db->tableExists($dbprefix . 'ma_assets')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_assets`;');
}

if ($db->tableExists($dbprefix . 'ma_point_actions')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_point_actions`;');
}

if ($db->tableExists($dbprefix . 'ma_point_triggers')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_point_triggers`;');
}

if ($db->tableExists($dbprefix . 'ma_marketing_messages')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_marketing_messages`;');
}

if ($db->tableExists($dbprefix . 'ma_emails')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_emails`;');
}

if ($db->tableExists($dbprefix . 'ma_text_messages')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_text_messages`;');
}

if ($db->tableExists($dbprefix . 'ma_campaigns')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_campaigns`;');
}

if ($db->tableExists($dbprefix . 'ma_email_templates')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_email_templates`;');
}

if ($db->tableExists($dbprefix . 'ma_campaign_flows')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_campaign_flows`;');
}

if ($db->tableExists($dbprefix . 'ma_lead_segments')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_lead_segments`;');
}

if ($db->tableExists($dbprefix . 'ma_lead_stages')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_lead_stages`;');
}

if ($db->tableExists($dbprefix . 'ma_email_logs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_email_logs`;');
}

if ($db->tableExists($dbprefix . 'ma_sms_logs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_sms_logs`;');
}

if ($db->tableExists($dbprefix . 'ma_asset_download_logs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_asset_download_logs`;');
}

if ($db->tableExists($dbprefix . 'ma_point_action_logs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_point_action_logs`;');
}

if ($db->tableExists($dbprefix . 'ma_campaign_lead_exceptions')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_campaign_lead_exceptions`;');
}

if ($db->tableExists($dbprefix . 'ma_sms')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_sms`;');
}

if ($db->tableExists($dbprefix . 'ma_email_click_logs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_email_click_logs`;');
}

if ($db->tableExists($dbprefix . 'ma_permissions')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_permissions`;');
}

if ($db->tableExists($dbprefix . 'ma_email_designs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_email_designs`;');
}

if ($db->tableExists($dbprefix . 'ma_email_template_designs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_email_template_designs`;');
}

if ($db->tableExists($dbprefix . 'ma_point_action_details')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_point_action_details`;');
}

if ($db->tableExists($dbprefix . 'ma_campaign_client_exceptions')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_campaign_client_exceptions`;');
}

if ($db->tableExists($dbprefix . 'ma_asset_logs')) {
    $db->query('DROP TABLE `'.$dbprefix .'ma_asset_logs`;');
}
