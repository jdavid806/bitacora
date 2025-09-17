<?php


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


$this_is_required = true;
if (!$this_is_required) {
	echo json_encode(array("success" => false, "message" => "This is required!"));
	exit();
}

//run installation sql
$db = db_connect('default');
$dbprefix = get_db_prefix();


if (!$db->fieldExists('manager_id', $dbprefix.'team')) {
	$db->query('ALTER TABLE `' . $dbprefix . "team`
		ADD COLUMN `manager_id` INT(11) NULL DEFAULT 0;");
}
if (!$db->fieldExists('parent_id', $dbprefix.'team')) {
	$db->query('ALTER TABLE `' . $dbprefix . "team`
		ADD COLUMN `parent_id` INT(11) NULL DEFAULT 0;");
}

if (!$db->tableExists($dbprefix . "rec_transfer_records")) {

	$db->query("CREATE TABLE `" . $dbprefix . "rec_transfer_records` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`staffid` int(11) NOT NULL,
		`firstname` varchar(100) NULL,
		`lastname` varchar(100) NULL,
		`birthday` date NULL,
		`gender` varchar(11) NULL,
		`staff_identifi` varchar(20) NULL,
		`creator` int(11) NULL,
		`datecreator` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "setting_transfer_records")) {

	$db->query("CREATE TABLE `" . $dbprefix . "setting_transfer_records` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name`varchar(150),  
		`meta` varchar(50),  
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "rec_set_transfer_record")) {

	$db->query("CREATE TABLE `" . $dbprefix . "rec_set_transfer_record` (
		`set_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`send_to` varchar(45) NOT NULL,
		`email_to` text NULL,
		`add_from` int(11) NOT NULL,
		`add_date` date NOT NULL,
		`subject` text NOT NULL,
		`content` text NULL,
		`order` int(11) NOT NULL,
		PRIMARY KEY (`set_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "setting_asset_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "setting_asset_allocation` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` varchar(150),     
		`meta` varchar(50),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "records_meta")) {

	$db->query("CREATE TABLE `" . $dbprefix . "records_meta` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` varchar(150),  
		`meta` varchar(100),  
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

	$data_array = array( 
		array("staff_identifi", "staff_identifi"), 
		array("firstname", "firstname"), 
		array("email", "email"), 
		array("phonenumber", "phonenumber"), 
		array("facebook", "facebook"), 
		array("skype", "skype"), 
		array("birthday", "birthday"), 
		array("birthplace", "birthplace"), 
		array("home_town", "home_town"), 
		array("marital_status", "marital_status"), 
		array("nation", "nation"), 
		array("religion", "religion"), 
		array("identification", "identification"), 
		array("days_for_identity", "days_for_identity"), 
		array("place_of_issue", "place_of_issue"), 
		array("resident", "resident"), 
		array("literacy", "literacy"), 
	); 

	$db_builder = $db->table(get_db_prefix() . "records_meta");
	foreach ($data_array as $key => $value) {
		$data['name']=$value[0];
		$data['meta']=$value[1];
		$db_builder->insert($data);
	}    
}

if (!$db->tableExists($dbprefix . "group_checklist")) {
	$db->query("CREATE TABLE `" . $dbprefix . "group_checklist` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`group_name` VARCHAR(100) NOT NULL,
		`meta` VARCHAR(100) NULL,
		PRIMARY KEY (`id`)

	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "setting_training")) {
	$db->query("CREATE TABLE `" . $dbprefix . "setting_training` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`training_type` INT(11) NOT NULL,
		`position_training` VARCHAR(100) NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "rec_criteria")) {

	$db->query("CREATE TABLE `" . $dbprefix . "rec_criteria` (
		`criteria_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`criteria_type` varchar(45) NOT NULL,
		`criteria_title` varchar(200) NOT NULL,
		`group_criteria` int(11)  NULL,
		`description` text NULL,
		`add_from` int(11) NOT NULL,
		`add_date` date NULL,
		`score_des1` text NULL,
		`score_des2` text NULL,
		`score_des3` text NULL,
		`score_des4` text NULL,
		`score_des5` text NULL,
		PRIMARY KEY (`criteria_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "position_training_question_form")) {

	$db->query("CREATE TABLE `" . $dbprefix . "position_training_question_form` (
		`questionid` int(11) NOT NULL AUTO_INCREMENT,
		`rel_id` int(11) NOT NULL,
		`rel_type` varchar(20) DEFAULT NULL,
		`question` mediumtext NOT NULL,
		`required` tinyint(1) NOT NULL DEFAULT '0',
		`question_order` int(11) NOT NULL,
		`point`int(11) NOT NULL,

		PRIMARY KEY (`questionid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "p_t_form_question_box_description")) {

	$db->query("CREATE TABLE `" . $dbprefix . "p_t_form_question_box_description` (
		`questionboxdescriptionid` int(11) NOT NULL AUTO_INCREMENT,
		`description` mediumtext NOT NULL,
		`boxid` mediumtext NOT NULL,
		`questionid` int(11) NOT NULL,
		`correct` int(11) NULL DEFAULT '1' COMMENT'0: correct 1: incorrect',

		PRIMARY KEY (`questionboxdescriptionid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "checklist")) {

	$db->query("CREATE TABLE `" . $dbprefix . "checklist` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(100) NOT NULL,
		`group_id` int(11) NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "group_checklist_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "group_checklist_allocation` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`group_name` VARCHAR(100) NOT NULL,
		`meta` VARCHAR(100) NULL,
		`staffid` INT(11) NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "checklist_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "checklist_allocation` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(100) NOT NULL,
		`group_id` INT(11) NULL,
		`staffid` INT(11) NULL,
		`status` INT(11) NULL DEFAULT 0,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "training_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "training_allocation` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`training_process_id` VARCHAR(100) NOT NULL,
		`staffid` INT(11) NULL,
		`training_type` int(11) NULL,
		`date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`training_name` varchar(150) NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "transfer_records_reception")) {

	$db->query("CREATE TABLE `" . $dbprefix . "transfer_records_reception` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name`varchar(150),  
		`meta` varchar(50), 
		`staffid` int(11) NULL, 
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "rec_job_position")) {

	$db->query("CREATE TABLE `" . $dbprefix . "rec_job_position` (
		`position_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`position_name` varchar(200) NOT NULL,
		`position_description` text NULL,
		PRIMARY KEY (`position_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "bonus_discipline")) {

	$db->query("CREATE TABLE `" . $dbprefix . "bonus_discipline` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(100) NULL,
		`id_criteria`  VARCHAR(200)  NULL,
		`type` int(3)  NOT NULL,
		`apply_for` varchar(50) NULL, 
		`from_time` DATETIME NULL ,
		`lever_bonus` int(11)  NULL,
		`approver` int(11)  NULL,
		`url_file` longtext NULL ,
		`create_time` DATETIME NULL,
		`id_admin` int(3) NULL,
		`status` int(3) NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "bonus_discipline_detail")) {

	$db->query("CREATE TABLE `" . $dbprefix . "bonus_discipline_detail` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_bonus_discipline` int(11) NOT NULL,
		`from_time` DATETIME NULL ,
		`staff_id` int(11)  NULL,
		`department_id` longtext NULL ,
		`lever_bonus` int(11)  NULL,
		`formality` varchar(50) NULL,
		`formality_value` varchar(100) NULL,
		`description` longtext NULL ,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_workplace")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_workplace` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` varchar(200) NOT NULL,
		`workplace_address` varchar(400) NULL,
		`latitude` double,
		`longitude` double,
		`default` bit NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

//table setting staff contract type
if (!$db->tableExists($dbprefix . "hr_staff_contract_type")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_staff_contract_type` (
		`id_contracttype` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name_contracttype` varchar(200) NOT NULL,
		`description` longtext NULL ,
		`duration` int(11) NULL,
		`unit` varchar(20) NULL,
		`insurance` boolean NULL,
		PRIMARY KEY (`id_contracttype`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_salary_form")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_salary_form` (
		`form_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`form_name` varchar(200) NOT NULL,
		`salary_val` decimal(15,2) NOT NULL,
		`tax` boolean NOT NULL,
		PRIMARY KEY (`form_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_allowance_type")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_allowance_type` (
		`type_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`type_name` varchar(200) NOT NULL,
		`allowance_val` decimal(15,2) NOT NULL,
		`taxable` boolean NOT NULL,
		PRIMARY KEY (`type_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_procedure_retire")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_procedure_retire` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`rel_name` TEXT DEFAULT NULL,
		`option_name` TEXT DEFAULT NULL,
		`status` int(11) NULL DEFAULT 1,
		`people_handle_id` int(11) NOT NULL,
		`procedure_retire_id` int(11) NOT NULL,

		PRIMARY KEY (`id`)

	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_procedure_retire_manage")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_procedure_retire_manage` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`name_procedure_retire` TEXT NOT NULL,
		`department` varchar(250) NOT NULL,
		`datecreator` datetime NOT NULL ,

		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


//job position table
if (!$db->tableExists($dbprefix . "hr_job_p")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_job_p` (
		`job_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`job_name` VARCHAR(100) NULL,
		`description` TEXT NULL,
		PRIMARY KEY (`job_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hr_job_position")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_job_position` (
		`position_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`position_name` varchar(200) NOT NULL,
		`job_position_description` TEXT NULL,
		`job_p_id` int(11) UNSIGNED NOT NULL,
		`position_code` VARCHAR(50) NULL,
		`department_id` TEXT NULL,

		PRIMARY KEY (`position_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_jp_salary_scale")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_jp_salary_scale` (
		`salary_scale_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`job_position_id` int(11) UNSIGNED NOT NULL ,
		`rel_type` VARCHAR(100) NULL COMMENT 'salary:allowance:insurance',
		`rel_id` int(11) NULL,
		`value` TEXT NULL,

		PRIMARY KEY (`salary_scale_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_jp_interview_training")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_jp_interview_training` (
		`training_process_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`job_position_id` LONGTEXT NULL,
		`training_name` VARCHAR(100) NULL,
		`training_type` int(11) NULL,
		`description` TEXT NULL,
		`date_add` datetime NULL,
		`position_training_id` TEXT NULL,
		`mint_point` INT(11) NULL,
		`additional_training` VARCHAR(100) NULL DEFAULT '',
		`staff_id` TEXT NULL ,
		`time_to_start` DATE NULL ,
		`time_to_end` DATE NULL,

		PRIMARY KEY (`training_process_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_allocation_asset")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_allocation_asset` (
		`allocation_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`staff_id` int(11) UNSIGNED NOT NULL ,
		`asset_name` VARCHAR(100) NULL,
		`assets_amount` int(11) UNSIGNED NOT NULL ,
		`status_allocation` int(11) UNSIGNED  NULL DEFAULT 0 COMMENT '1: Allocated 0: Unallocated',

		PRIMARY KEY (`allocation_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_group_checklist_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_group_checklist_allocation` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`group_name` VARCHAR(100) NOT NULL,
		`meta` VARCHAR(100) NULL,
		`staffid` INT(11) NULL,

		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists('group_name', $dbprefix.'hr_group_checklist_allocation')) {
	$db->query('ALTER TABLE `' . $dbprefix . "hr_group_checklist_allocation`
		ADD COLUMN `group_name` VARCHAR(100) NOT NULL;");
}

if (!$db->tableExists($dbprefix . "hr_checklist_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_checklist_allocation` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`name` VARCHAR(100) NOT NULL,
		`group_id` INT(11) NULL,
		`status` INT(11) NULL DEFAULT 0,

		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_training_allocation")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_training_allocation` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`training_process_id` VARCHAR(100) NOT NULL,
		`staffid` INT(11) NULL,
		`training_type` int(11) NULL,
		`date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`training_name` varchar(150) NULL,

		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("jp_interview_training_id", $dbprefix."hr_training_allocation")) {

	$db->query("ALTER TABLE `" . $dbprefix . "hr_training_allocation`
		ADD COLUMN `jp_interview_training_id` INT(11) NULL ;");
}

if (!$db->tableExists($dbprefix . "hr_p_t_surveyresultsets")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_p_t_surveyresultsets` (
		`resultsetid` int(11) NOT NULL AUTO_INCREMENT,
		`trainingid` int(11) NOT NULL,
		`ip` varchar(40) NOT NULL,
		`useragent` varchar(150) NOT NULL,
		`date` datetime NOT NULL,
		`staff_id` int(11) UNSIGNED NOT NULL,

		PRIMARY KEY (`resultsetid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_p_t_form_results")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_p_t_form_results` (

		`resultid` int(11) NOT NULL AUTO_INCREMENT,
		`boxid` int(11) NOT NULL,
		`boxdescriptionid` int(11) DEFAULT NULL,
		`rel_id` int(11) NOT NULL,
		`rel_type` varchar(20) DEFAULT NULL,
		`questionid` int(11) NOT NULL,
		`answer` text,
		`resultsetid` int(11) NOT NULL,

		PRIMARY KEY (`resultid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_rec_transfer_records")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_rec_transfer_records` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`staffid` int(11) NOT NULL,
		`firstname` varchar(100) NULL,
		`lastname` varchar(100) NULL,
		`birthday` date NULL,
		`gender` varchar(11) NULL,
		`staff_identifi` varchar(20) NULL,
		`creator` int(11) NOT NULL,

		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_position_training")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_position_training` (
		`training_id` int(11) NOT NULL AUTO_INCREMENT,
		`subject` mediumtext NOT NULL,
		`training_type` int(11) UNSIGNED NOT NULL,
		`slug` mediumtext NOT NULL,
		`description` text  NULL,
		`viewdescription` text,
		`datecreated` datetime NOT NULL,
		`redirect_url` varchar(100) DEFAULT NULL,
		`send` tinyint(1) NOT NULL DEFAULT '0',
		`onlyforloggedin` int(11) DEFAULT '0',
		`fromname` varchar(100) DEFAULT NULL,
		`iprestrict` tinyint(1) NOT NULL,
		`active` tinyint(1) NOT NULL DEFAULT '1',
		`hash` varchar(32) NOT NULL,
		`mint_point` VARCHAR(20) NULL,

		PRIMARY KEY (`training_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_position_training_question_form")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_position_training_question_form` (
		`questionid` int(11) NOT NULL AUTO_INCREMENT,
		`rel_id` int(11) NOT NULL,
		`rel_type` varchar(20) DEFAULT NULL,
		`question` mediumtext NOT NULL,
		`required` tinyint(1) NOT NULL DEFAULT '0',
		`question_order` int(11) NOT NULL,
		`point`int(11) NOT NULL,

		PRIMARY KEY (`questionid`)

	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_p_t_form_question_box")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_p_t_form_question_box` (
		`boxid` int(11) NOT NULL AUTO_INCREMENT,
		`boxtype` varchar(10) NOT NULL,
		`questionid` int(11) NOT NULL,

		PRIMARY KEY (`boxid`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_p_t_form_question_box_description")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_p_t_form_question_box_description` (
		`questionboxdescriptionid` int(11) NOT NULL AUTO_INCREMENT,
		`description` mediumtext NOT NULL,
		`boxid` mediumtext NOT NULL,
		`questionid` int(11) NOT NULL,
		`correct` int(11) NULL DEFAULT '1' COMMENT'0: correct 1: incorrect',

		PRIMARY KEY (`questionboxdescriptionid`)

	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_staff_contract")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_staff_contract` (
		`id_contract` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`contract_code` varchar(200) NOT NULL,
		`name_contract` int(11) NOT NULL,
		`staff` int(11) NOT NULL,
		`start_valid` date NULL,
		`end_valid` date NULL,
		`contract_status` varchar(100) NULL,
		`sign_day` date NULL,
		`staff_delegate` int(11) NULL,
		`hourly_or_month` LONGTEXT NULL,
		`content` LONGTEXT NULL,
		`hash` VARCHAR(32) NULL,
		`signature` VARCHAR(40) NULL,
		`signer` INT(11) NULL,
		`staff_signature` VARCHAR(40) NULL,
		`staff_sign_day` DATE NULL,

		PRIMARY KEY (`id_contract`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hr_staff_contract_detail")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_staff_contract_detail` (
		`contract_detail_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`staff_contract_id` int(11) UNSIGNED NOT NULL,
		`type` text NULL,
		`rel_type` text NULL,
		`rel_value` decimal(15,2) DEFAULT '0.00',
		`since_date` date NULL,
		`contract_note` text NULL,

		PRIMARY KEY (`contract_detail_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


//add column for tbl staff
if (!$db->fieldExists("staff_identifi", $dbprefix."users")) {

	$db->query("ALTER TABLE `" . $dbprefix . "users`
		ADD COLUMN `staff_identifi` VARCHAR(200) NULL,
		ADD COLUMN `team_manage` int(11) DEFAULT '0',
		ADD COLUMN `workplace` int(11) NULL,
		ADD COLUMN `status_work` VARCHAR(100) NULL DEFAULT 'working',
		ADD COLUMN `job_position` int(11) NULL,
		ADD COLUMN `literacy` varchar(50) NULL,
		ADD COLUMN `marital_status` varchar(25) NULL,
		ADD COLUMN `account_number` varchar(50) NULL,
		ADD COLUMN `name_account` varchar(50) NULL,
		ADD COLUMN `issue_bank` varchar(200) NULL,
		ADD COLUMN `Personal_tax_code` varchar(50) NULL,
		ADD COLUMN `date_update` DATE NULL

		;");
}

if (!$db->fieldExists("nation", $dbprefix."users")) {

	$db->query("ALTER TABLE `" . $dbprefix . "users`
		ADD COLUMN `nation` VARCHAR(200) NULL,
		ADD COLUMN `religion` VARCHAR(200) NULL,
		ADD COLUMN `identification` VARCHAR(200) NULL,
		ADD COLUMN `days_for_identity` date NULL,
		ADD COLUMN `home_town` VARCHAR(200) NULL,
		ADD COLUMN `resident` VARCHAR(200) NULL,
		ADD COLUMN `current_address` VARCHAR(200) NULL,
		ADD COLUMN `orther_infor` VARCHAR(200) NULL
		;");
}

if (!$db->fieldExists("hourly_rate", $dbprefix."users")) {
	$db->query("ALTER TABLE `" . $dbprefix . "users`
		ADD COLUMN `hourly_rate` DECIMAL(15,2) NULL DEFAULT '0.00'
		;");
}
if (!$db->fieldExists("birthplace", $dbprefix."users")) {
	$db->query("ALTER TABLE `" . $dbprefix . "users`
		ADD COLUMN `birthplace` VARCHAR(200) NULL
		;");
}
if (!$db->fieldExists("place_of_issue", $dbprefix."users")) {
	$db->query("ALTER TABLE `" . $dbprefix . "users`
		ADD COLUMN `place_of_issue` varchar(50) NULL
		;");
}

/*general settings*/
add_setting('job_position_prefix', "#JOB", 1);
add_setting('job_position_number', 1, 1);
add_setting('contract_code_prefix', "#CONTRACT", 1);
add_setting('contract_code_number', 1, 1);


if (!$db->tableExists($dbprefix . "hr_dependent_person")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_dependent_person` (
		`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`staffid` int(11) UNSIGNED  NULL,
		`dependent_name` varchar(100) NULL ,
		`relationship` varchar(100) NULL ,
		`dependent_bir` date NULL ,
		`start_month` date NULL ,
		`end_month` date NULL ,
		`dependent_iden` varchar(20) NOT NULL ,
		`reason` longtext NULL ,
		`status` int(11) UNSIGNED  NULL DEFAULT 0 ,
		`status_comment` longtext NULL,


		PRIMARY KEY (`id`,`dependent_iden`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_list_staff_quitting_work")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_list_staff_quitting_work` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`staffid` int(11) DEFAULT NULL,
		`staff_name` TEXT NULL,
		`department_name` TEXT NULL,
		`role_name` TEXT NULL,
		`email` TEXT NULL,
		`dateoff` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`approval` varchar(100) NULL DEFAULT NULL,

		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hr_procedure_retire_of_staff")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_procedure_retire_of_staff` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`rel_id` int(11) DEFAULT NULL,
		`option_name` TEXT DEFAULT NULL,
		`status` int(11) NULL DEFAULT 0,
		`staffid` int(11) DEFAULT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_procedure_retire_of_staff_by_id")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_procedure_retire_of_staff_by_id` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`rel_name` TEXT DEFAULT NULL,
		`people_handle_id` int(11),
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hr_views_tracking")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_views_tracking` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`rel_id` int(11) NOT NULL,
		`rel_type` varchar(40) NOT NULL,
		`date` datetime NOT NULL,
		`view_ip` varchar(40) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hr_education")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_education` (
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`staff_id` INT(11) NOT NULL,
		`admin_id` INT(11) NOT NULL,
		`programe_id` INT(11) NULL,
		`training_programs_name` text NOT NULL,
		`training_places` text NULL,
		`training_time_from` DATETIME  NULL,
		`training_time_to` DATETIME  NULL,
		`date_create` DATETIME NULL,
		`training_result` VARCHAR(150) NULL,
		`degree` VARCHAR(150) NULL,
		`notes` text NULL,
		PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


add_setting('staff_code_prefix', "EC", 1);
add_setting('staff_code_number', 1, 1);


/*add Type of training menu*/
if (!$db->tableExists($dbprefix . "hr_type_of_trainings")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_type_of_trainings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` TEXT NULL,

	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hr_contract_template")) {

	$db->query("CREATE TABLE `" . $dbprefix . "hr_contract_template` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` TEXT NULL,
	`job_position` LONGTEXT NULL,
	`content` LONGTEXT NULL,

	PRIMARY KEY (`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

add_setting('hr_profile_hide_menu', 1, 1);

if (!$db->tableExists($dbprefix . "files")) {
  $db->query("CREATE TABLE `" . $dbprefix . "files` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `rel_id` int(11) NOT NULL,
    `rel_type` varchar(20) NOT NULL,
    `file_name` varchar(191) NOT NULL,
    `filetype` varchar(40) DEFAULT NULL,
    `visible_to_customer` int(11) NOT NULL DEFAULT '0',
    `attachment_key` varchar(32) DEFAULT NULL,
    `external` varchar(40) DEFAULT NULL,
    `external_link` text,
    `thumbnail_link` text COMMENT 'For external usage',
    `staffid` int(11) NOT NULL,
    `contact_id` int(11) DEFAULT '0',
    `task_comment_id` int(11) NOT NULL DEFAULT '0',
    `dateadded` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `rel_id` (`rel_id`),
    KEY `rel_type` (`rel_type`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

if (!$db->fieldExists('file_size', $dbprefix.'files')) {
	$db->query('ALTER TABLE `' . $dbprefix . "files`
		ADD COLUMN `file_size` double NOT NULL DEFAULT '0';");
}

if (!$db->fieldExists('hr_send_training_staff_id', $dbprefix.'notifications')) {
	$db->query('ALTER TABLE `' . $dbprefix . "notifications`
		ADD COLUMN `hr_send_training_staff_id` int(1) NOT NULL DEFAULT '0',
		ADD COLUMN `hr_send_layoff_checklist_handle_staff_id` int(1) NOT NULL DEFAULT '0'

		;");
}
if (!$db->fieldExists("plugins_permissions1" ,$dbprefix . "roles")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "roles`
    ADD COLUMN `plugins_permissions1` LONGTEXT NULL
;");
}

if ($db->fieldExists("gender" ,$dbprefix . "users")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "users`
    CHANGE COLUMN `gender` `gender` enum('male','female','other') COLLATE utf8_unicode_ci DEFAULT NULL
;");
}
