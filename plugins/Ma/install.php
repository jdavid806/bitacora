<?php

$db = db_connect('default');
$dbprefix = get_db_prefix();

/**
 * Add setting
 *
 * @since  Version 1.0.0
 *
 * @param string  $name      Option name (required|unique)
 * @param string  $value     Option value
 *
 */

if (!function_exists('add_setting')) {

  function add_setting($name, $value = '')
  {
      if (!setting_exists($name)) {
        $db = db_connect('default');
        $db_builder = $db->table(get_db_prefix() . 'settings');
        $newData = [
                'setting_name'  => $name,
                'setting_value' => $value,
            ];

        $db_builder->insert($newData);

        $insert_id = $db->insertID();

        if ($insert_id) {
            return true;
        }

        return false;
      }

      return false;
  }
}

/**
 * @since  1.0.0
 * Check whether an setting exists
 *
 * @param  string $name setting name
 *
 * @return boolean
 */
if (!function_exists('setting_exists')) {

  function setting_exists($name)
  { 
   
    $db = db_connect('default');
    $db_builder = $db->table(get_db_prefix() . 'settings');

    $count = $db_builder->where('setting_name', $name)->countAllResults();

    return $count > 0;
  }
}

if (!$db->tableExists($dbprefix . 'ma_categories')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_categories (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` TEXT NOT NULL,
      `type` TEXT NULL,
      `published` INT(11) NOT NULL DEFAULT 1,
	  `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_stages')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_stages (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` TEXT NOT NULL,
      `weight` TEXT NULL,
      `category` INT(255) NULL,
	  `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_segments')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_segments (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
	  `name` TEXT NOT NULL,
      `category` INT(255) NULL,
      `public_segment` INT(255) NOT NULL DEFAULT 1,
      `published` INT(11) NOT NULL DEFAULT 1,
	  `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_segment_filters')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_segment_filters (
  	  `id` INT(11) NOT NULL AUTO_INCREMENT,
      `segment_id` INT(255) NULL,
	  `type` TEXT NULL,
	  `sub_type_1` TEXT NULL,
	  `sub_type_2` TEXT NULL,
	  `value` TEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_forms')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_forms (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `form_key` VARCHAR(32) NOT NULL,
      `lead_source` INT(11) NOT NULL,
      `lead_status` INT(11) NOT NULL,
      `notify_lead_imported` INT(11) NOT NULL,
      `notify_type` VARCHAR(20) NULL,
      `notify_ids` MEDIUMTEXT NULL,
      `responsible` INT(11) NOT NULL DEFAULT 0,
      `name` VARCHAR(191) NOT NULL,
      `form_data` MEDIUMTEXT NULL,
      `recaptcha` INT(11) NOT NULL DEFAULT 0,
      `submit_btn_name` VARCHAR(40) NULL,
      `success_submit_msg` TEXT NULL,
      `language` VARCHAR(40) NULL,
      `allow_duplicate` INT(11) NOT NULL DEFAULT 1,
      `mark_public` INT(11) NOT NULL DEFAULT 0,
      `track_duplicate_field` VARCHAR(20) NULL,
      `track_duplicate_field_and` VARCHAR(20) NULL,
      `create_task_on_duplicate` INT(11) NOT NULL DEFAULT 0,
      `dateadded` DATETIME NOT NULL,
      `addedfrom` INT(11) NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('from_ma_form_id' ,$dbprefix . 'clients')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'clients`
  ADD COLUMN `from_ma_form_id` INT(11) NOT NULL DEFAULT 0');
}

if (!$db->tableExists($dbprefix . 'ma_assets')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_assets (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `language` VARCHAR(40) NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_point_actions')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_point_actions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `change_points` FLOAT(11) NOT NULL,
      `action` TEXT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_point_triggers')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_point_triggers (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `minimum_number_of_points` FLOAT(11) NOT NULL,
      `contact_color` TEXT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_marketing_messages')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_marketing_messages (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `type` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `email_template` INT(11) NOT NULL DEFAULT 0,
      `web_notification_description` TEXT NULL,
      `web_notification_link` TEXT NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_emails')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_emails (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `type` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `segment` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` VARCHAR(40) NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_text_messages')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_text_messages (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` VARCHAR(40) NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('color' ,$dbprefix . 'ma_segments')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_segments`
  ADD COLUMN `color` TEXT NULL');
}

if (!$db->fieldExists('color' ,$dbprefix . 'ma_stages')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_stages`
  ADD COLUMN `color` TEXT NULL');
}

if (!$db->fieldExists('color' ,$dbprefix . 'ma_categories')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_categories`
  ADD COLUMN `color` TEXT NULL');
}

if (!$db->fieldExists('color' ,$dbprefix . 'ma_emails')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_emails`
  ADD COLUMN `color` TEXT NULL');
}

if (!$db->fieldExists('color' ,$dbprefix . 'ma_assets')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_assets`
  ADD COLUMN `color` TEXT NULL');
}

if (!$db->tableExists($dbprefix . 'ma_campaigns')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_campaigns (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `color` TEXT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `start_date` DATE NULL,
      `end_date` DATE NULL,
      `workflow` LONGTEXT NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('published' ,$dbprefix . 'ma_stages')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_stages`
  ADD COLUMN `published` INT(11) NOT NULL DEFAULT 1');
}

if (!$db->fieldExists('addedfrom' ,$dbprefix . 'ma_segments')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_segments`
          ADD COLUMN `addedfrom` INT(11) NULL,
          ADD COLUMN `dateadded` DATETIME NOT NULL');
}

if (!$db->fieldExists('addedfrom' ,$dbprefix . 'ma_stages')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_stages`
          ADD COLUMN `addedfrom` INT(11) NULL,
          ADD COLUMN `dateadded` DATETIME NOT NULL');
}


if (!$db->tableExists($dbprefix . 'ma_email_templates')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_email_templates (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `color` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` TEXT NULL,
      `data_html` LONGTEXT NULL,
      `data_design` LONGTEXT NULL,
      `description` TEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_campaign_flows')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_campaign_flows (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `campaign_id` INT(11) NOT NULL,
      `node_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_lead_segments')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_lead_segments (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `segment_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_lead_stages')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_lead_stages (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `stage_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_email_logs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_email_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `email_template_id` INT(11) NULL,
      `delivery` INT(11) NOT NULL DEFAULT 0,
      `open` INT(11) NOT NULL DEFAULT 0,
      `click` INT(11) NOT NULL DEFAULT 0,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('addedfrom' ,$dbprefix . 'ma_categories')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_categories`
          ADD COLUMN `addedfrom` INT(11) NULL,
          ADD COLUMN `dateadded` DATETIME NOT NULL');
}

if (!$db->fieldExists('deleted' ,$dbprefix . 'ma_lead_segments')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_lead_segments`
        ADD COLUMN `deleted` INT(11) NOT NULL DEFAULT 0,
        ADD COLUMN `date_delete` DATETIME NULL');
}

if (!$db->fieldExists('campaign_id' ,$dbprefix . 'ma_lead_segments')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_lead_segments`
        ADD COLUMN `campaign_id` INT(11) NULL');
}

if (!$db->fieldExists('deleted' ,$dbprefix . 'ma_lead_stages')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_lead_stages`
        ADD COLUMN `deleted` INT(11) NOT NULL DEFAULT 0,
        ADD COLUMN `date_delete` DATETIME NULL');
}

if (!$db->fieldExists('campaign_id' ,$dbprefix . 'ma_lead_stages')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_lead_stages`
        ADD COLUMN `campaign_id` INT(11) NULL');
}

if (!$db->fieldExists('ma_point' ,$dbprefix . 'clients')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'clients`
        ADD COLUMN `ma_point` INT(11) NOT NULL DEFAULT 0');
}

if (!$db->fieldExists('delivery_time' ,$dbprefix . 'ma_email_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_logs`
          ADD COLUMN `delivery_time` DATETIME NULL,
          ADD COLUMN `open_time` DATETIME NULL,
          ADD COLUMN `click_time` DATETIME NULL');
}

if (!$db->tableExists($dbprefix . 'ma_sms_logs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_sms_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `text_message_id` INT(11) NULL,
      `delivery` INT(11) NOT NULL DEFAULT 0,
      `delivery_time` DATETIME NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_asset_download_logs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_asset_download_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `asset_id` INT(11) NOT NULL,
      `ip` TEXT NULL,
      `browser_name` TEXT NULL,
      `http_user_agent` TEXT NULL,
      `time` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_point_action_logs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_point_action_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `point_action_id` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('output' ,$dbprefix . 'ma_campaign_flows')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_campaign_flows`
        ADD COLUMN `output` TEXT NULL');
}

if (!$db->tableExists($dbprefix . 'ma_campaign_lead_exceptions')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_campaign_lead_exceptions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('subject' ,$dbprefix . 'ma_emails')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_emails`
        ADD COLUMN `subject` TEXT NULL');
}

if (!$db->fieldExists('email_template' ,$dbprefix . 'ma_emails')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_emails`
          ADD COLUMN `email_template` INT(11) NULL,
          ADD COLUMN `from_name` TEXT NULL,
          ADD COLUMN `from_address` TEXT NULL,
          ADD COLUMN `reply_to_address` TEXT NULL,
          ADD COLUMN `bcc_address` TEXT NULL,
          ADD COLUMN `attachment` TEXT NULL,
          ADD COLUMN `data_design` LONGTEXT NULL,
          ADD COLUMN `data_html` LONGTEXT NULL');
}

if (!$db->fieldExists('email_id' ,$dbprefix . 'ma_email_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_logs`
        ADD COLUMN `email_id` INT(11) NULL');
}


if (!$db->tableExists($dbprefix . 'ma_sms')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_sms (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` TEXT NOT NULL,
      `category` INT(11) NOT NULL DEFAULT 0,
      `sms_template` INT(11) NOT NULL DEFAULT 0,
      `color` TEXT NULL,
      `published` INT(11) NOT NULL DEFAULT 1,
      `language` VARCHAR(40) NULL,
      `content` TEXT NULL,
      `description` LONGTEXT NULL,
      `addedfrom` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('sms_id' ,$dbprefix . 'ma_sms_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_sms_logs`
        ADD COLUMN `sms_id` INT(11) NULL');
}

if (!$db->tableExists($dbprefix . 'ma_email_click_logs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_email_click_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `lead_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `email_id` INT(11) NULL,
      `url` TEXT NULL,
      `time` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

add_setting('ma_insert_email_template_default', 0);

if(get_setting('ma_insert_email_template_default') == 0){

    $db->query(file_get_contents(ROOTPATH . 'plugins/Ma/database/email_template_default.sql'));

    $db->query('UPDATE ' . $dbprefix . 'settings 
       SET setting_value = 1
       WHERE setting_name = "ma_insert_email_template_default"');
}

if (!$db->fieldExists('failed' ,$dbprefix . 'ma_email_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_logs`
          ADD COLUMN `failed` INT(11) NOT NULL DEFAULT 0,
          ADD COLUMN `failed_time` DATETIME NULL');
}

if (!$db->fieldExists('point' ,$dbprefix . 'ma_point_action_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_point_action_logs`
        ADD COLUMN `point` FLOAT(11) NULL');
}

if (!$db->fieldExists('files' ,$dbprefix . 'ma_assets')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_assets`
        ADD COLUMN `files` TEXT NULL');
}

add_setting('ma_asset_file_path', 'plugins/Ma/uploads/assets/');


add_setting('ma_insert_custom_field', 0);

if(get_setting('ma_insert_custom_field') == 0){

    $db->query(file_get_contents(ROOTPATH . 'plugins/Ma/database/custom_field.sql'));

    $db->query('UPDATE ' . $dbprefix . 'settings 
       SET setting_value = 1
       WHERE setting_name = "ma_insert_custom_field"');
}


if (!$db->tableExists($dbprefix . 'ma_permissions')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_permissions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `user_id` INT(11) NOT NULL,
      `permissions` TEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}


if (!$db->fieldExists('hash' ,$dbprefix . 'ma_email_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_logs`
          ADD COLUMN `hash` TEXT NULL');
}

if (!$db->tableExists($dbprefix . 'ma_email_designs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_email_designs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `email_id` INT(11) NOT NULL,
      `language` VARCHAR(40) NULL,
      `data_design` LONGTEXT NULL,
      `data_html` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_email_template_designs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_email_template_designs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `email_template_id` INT(11) NOT NULL,
      `language` VARCHAR(40) NULL,
      `data_design` LONGTEXT NULL,
      `data_html` LONGTEXT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('add_points_by_country' ,$dbprefix . 'ma_point_actions')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_point_actions`
        ADD COLUMN `add_points_by_country` INT(11) NULL');
}

if (!$db->tableExists($dbprefix . 'ma_point_action_details')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_point_action_details (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `point_action_id` INT(11) NOT NULL,
      `country` TEXT NULL,
      `change_points` FLOAT(11) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('client_id' ,$dbprefix . 'ma_point_action_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_point_action_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$db->fieldExists('client_id' ,$dbprefix . 'ma_email_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$db->fieldExists('client_id' ,$dbprefix . 'ma_campaign_flows')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_campaign_flows`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$db->fieldExists('client_id' ,$dbprefix . 'ma_email_click_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_click_logs`
        ADD COLUMN `client_id` INT(11) NULL');
}

if (!$db->tableExists($dbprefix . 'ma_campaign_client_exceptions')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_campaign_client_exceptions (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `client_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->tableExists($dbprefix . 'ma_asset_logs')) {
    $db->query('CREATE TABLE ' . $dbprefix . "ma_asset_logs (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `asset_id` INT(11) NOT NULL,
      `lead_id` INT(11) NOT NULL,
      `client_id` INT(11) NOT NULL,
      `campaign_id` INT(11) NULL,
      `hash` TEXT NULL,
      `download` INT(11) NOT NULL DEFAULT 0,
      `download_time` DATETIME NOT NULL,
      `dateadded` DATETIME NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=" . $db->charset . ';');
}

if (!$db->fieldExists('change_points' ,$dbprefix . 'ma_assets')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_assets`
  ADD COLUMN `change_points` TEXT NULL');
}

if (!$db->fieldExists('asset_log_id' ,$dbprefix . 'ma_asset_download_logs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_asset_download_logs`
        ADD COLUMN `asset_log_id` INT(11) NULL');
}


if (!$db->fieldExists('ma_unsubscribed' ,$dbprefix . 'clients')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'clients`
        ADD COLUMN `ma_unsubscribed` INT(11) NOT NULL DEFAULT 0');
}

if (!$db->fieldExists('country' ,$dbprefix . 'ma_email_designs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_designs`
        ADD COLUMN `country` TEXT NULL');
}

if (!$db->fieldExists('country' ,$dbprefix . 'ma_email_template_designs')) {
    $db->query('ALTER TABLE `' . $dbprefix . 'ma_email_template_designs`
        ADD COLUMN `country` TEXT NULL');
}

add_setting('ma_smtp_type', 'system_default_smtp');
add_setting('ma_email_sent_from_address');
add_setting('ma_email_sent_from_name');
add_setting('ma_email_protocol');
add_setting('ma_email_smtp_host');
add_setting('ma_email_smtp_user');
add_setting('ma_email_smtp_pass');
add_setting('ma_email_smtp_port');
add_setting('ma_email_smtp_security_type');
