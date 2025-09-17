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

if (!function_exists('hr_payroll_payroll_column_exist')) {

	function hr_payroll_payroll_column_exist($key)
	{ 
		
		$db = db_connect('default');
		$db_builder = $db->table(get_db_prefix() . 'hrp_payroll_columns');

		$count = $db_builder->where('function_name', $key)->countAllResults();

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

if (!$db->tableExists($dbprefix . "hr_payroll_option")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hr_payroll_option` (
      `option_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `option_name` varchar(200) NOT NULL,
      `option_val` longtext NULL,
      `auto` tinyint(1) NULL,
      PRIMARY KEY (`option_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


//Payslip table
if (!$db->tableExists($dbprefix . "hrp_payslips")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_payslips` (

      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `payslip_name` VARCHAR(100) NOT NULL,
      `payslip_template_id` INT(11) NULL,
      `payslip_month` DATE NOT NULL,
      `staff_id_created` int(11) NOT NULL,
      `date_created` DATETIME NOT NULL,
      `payslip_data` LONGTEXT NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("file_name" ,$dbprefix . "hrp_payslips")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslips`
    ADD COLUMN `file_name` TEXT NULL ;");
}

if (!$db->fieldExists("payslip_status" ,$dbprefix . "hrp_payslips")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslips`
    ADD COLUMN `payslip_status` VARCHAR(100) DEFAULT 'payslip_opening' ;");
}

if (!$db->tableExists($dbprefix . "hrp_payslip_templates")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_payslip_templates` (

      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `templates_name` VARCHAR(100) NOT NULL,
      `payslip_columns` LONGTEXT NULL,
      `payslip_id_copy` INT(11) UNSIGNED NOT NULL,

      `department_id`  LONGTEXT  NULL,
      `role_employees`  LONGTEXT  NULL,
      `staff_employees`  LONGTEXT  NULL,
     
      `payslip_template_data` LONGTEXT NULL,
      `date_created` DATETIME NOT NULL,
      `staff_id_created` INT(11) NOT NULL,
      `cell_data` LONGTEXT  NULL,


      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("except_staff" ,$dbprefix . "hrp_payslip_templates")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslip_templates`
    ADD COLUMN `except_staff` TEXT NULL ;");
}

if (!$db->tableExists($dbprefix . "hrp_payroll_columns")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_payroll_columns` (

      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `column_key` TEXT NULL,
      `taking_method` TEXT NULL COMMENT 'get from system, caculator, constant... ',
      `function_name` TEXT NULL COMMENT 'get value for method system',
      `value_related_to` TEXT NULL COMMENT 'salary, allowance value...',
      `display_with_staff` VARCHAR(10) DEFAULT 'true',
      `description` TEXT NULL,
      `date_created` DATETIME NOT NULL,
      `staff_id_created` INT(11) NOT NULL,
      `order_display` INT(11)  NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("is_edit" ,$dbprefix . "hrp_payroll_columns")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payroll_columns`
    ADD COLUMN `is_edit` VARCHAR(100) NULL DEFAULT 'yes';");
}

if (!$db->tableExists($dbprefix . "hrp_bonus_kpi")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_bonus_kpi` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `month_bonus_kpi` VARCHAR(45) NULL,
      `staffid` INT(11) NUll,
      `bonus_kpi` varchar(100) NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


//Insert into data to table payroll_columns

// Pay_Slip_Number: system - generate
// Payment_Run_Date: system - generate
// Employee_Number: system - generate or get from hr records
// Employee_Name: system - get tbl staff (fist+lastname)
// Dept: system - get tbl staffdepartment 
// Standard_Working_Time: system - get tblhrp_employees_timesheets
// Actual_Working_Time: system - get tblhrp_employees_timesheets
// Paid_Leave_Time: system - get tblhrp_employees_timesheets
// Unpaid_Leave_Time : system - get tblhrp_employees_timesheets
//earnings list: generate earning list (take from payroll or Hr records dependent on data integrated)
//Gross Pay: formulate - (total earings)
//Income Tax (PAYE): system (must caculating base on Income tax rates) 
//deduct list: generate deduct list (take from payroll setting)
//Total Deductions: formulate - (total deduct)
//Net Pay: formulate
//IT Rebate Code: system (get code)

if (hr_payroll_payroll_column_exist("staff_id") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Staff ID", "system", "staff_id", "", "true", "Staff ID", "'.date("Y-m-d H:i:s").'", "1", "1", "no");
');
}

if (hr_payroll_payroll_column_exist("pay_slip_number") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Payslip Number", "system", "pay_slip_number", "", "true", "Pay Slip Number", "'.date("Y-m-d H:i:s").'", "1", "2", "no");
');
}

if (hr_payroll_payroll_column_exist("payment_run_date") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Payment Run Date", "system", "payment_run_date", "", "true", "Payment Run Date", "'.date("Y-m-d H:i:s").'", "1", "3", "no");
');
}

if (hr_payroll_payroll_column_exist("employee_number") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Employee Number", "system", "employee_number", "", "true", "Employee Number", "'.date("Y-m-d H:i:s").'", "1", "4", "no");
');
}

if (hr_payroll_payroll_column_exist("employee_name") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Employee Name", "system", "employee_name", "", "true", "Employee Name", "'.date("Y-m-d H:i:s").'", "1", "5", "no");
');
}

if (hr_payroll_payroll_column_exist("dept_name") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Deparment Name", "system", "dept_name", "", "true", "Dept name Name", "'.date("Y-m-d H:i:s").'", "1", "6", "no");
');
}

//Standard_Working_Time
if (hr_payroll_payroll_column_exist("standard_workday") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Standard Working Time", "system", "standard_workday", "", "true", "Standard working time of the month (hours)", "'.date("Y-m-d H:i:s").'", "1", "7", "no");
');
}

//Actual_Working_Time
if (hr_payroll_payroll_column_exist("actual_workday") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Actual Working Time of Formal contract", "system", "actual_workday", "", "true", "Actual working time (hours)", "'.date("Y-m-d H:i:s").'", "1", "8", "no");
');
}

if (hr_payroll_payroll_column_exist("actual_workday_probation") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Actual Working Time of Probation contract", "system", "actual_workday_probation", "", "true", "Actual Working Time of Probation contract (hours)", "'.date("Y-m-d H:i:s").'", "1", "9", "no");
');
}


//Paid_Leave_Time
if (hr_payroll_payroll_column_exist("paid_leave") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Paid Leave Time", "system", "paid_leave", "", "true", "Paid Leave Time (hours)", "'.date("Y-m-d H:i:s").'", "1", "10", "no");
');
}

//Unpaid_Leave_Time
if (hr_payroll_payroll_column_exist("unpaid_leave") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Unpaid Leave Time", "system", "unpaid_leave", "", "true", "Unpaid Leave Time (hours)", "'.date("Y-m-d H:i:s").'", "1", "11", "no");
');
}

if (hr_payroll_payroll_column_exist("salary_of_the_probationary_contract") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Salary of the probationary contract", "caculator", "salary_of_the_probationary_contract", "", "true", "Salary of the probationary contract", "'.date("Y-m-d H:i:s").'", "1", "12", "no");
');
}

if (hr_payroll_payroll_column_exist("salary_of_the_formal_contract") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Salary of the formal contract", "caculator", "salary_of_the_formal_contract", "", "true", "Salary of the formal contract", "'.date("Y-m-d H:i:s").'", "1", "13", "no");
');
}

//Gross Pay formulas
if (hr_payroll_payroll_column_exist("gross_pay") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Gross Pay", "caculator", "gross_pay", "", "true", "Gross Pay", "'.date("Y-m-d H:i:s").'", "1", "14", "no");
');
}


//Total Deductions formulas
if (hr_payroll_payroll_column_exist("total_deductions") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Total Deductions", "caculator", "total_deductions", "", "true", "Total Deductions", "'.date("Y-m-d H:i:s").'", "1", "15", "no");
');
}


if (hr_payroll_payroll_column_exist("total_insurance") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Total Insurance", "caculator", "total_insurance", "", "true", "Total Insurance", "'.date("Y-m-d H:i:s").'", "1", "16", "no");
');
}

//Income Tax Rebate Code
if (hr_payroll_payroll_column_exist("it_rebate_code") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Income Tax Rebate Code", "system", "it_rebate_code", "", "true", "IT Rebate Code", "'.date("Y-m-d H:i:s").'", "1", "17", "no");
');
}

//Income_Tax Rebate Value
if (hr_payroll_payroll_column_exist("it_rebate_value") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Income Tax Rebate Value", "system", "it_rebate_value", "", "true", "IT Rebate Value", "'.date("Y-m-d H:i:s").'", "1", "18", "no");
');
}


if (hr_payroll_payroll_column_exist("taxable_salary") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Taxable salary", "caculator", "taxable_salary", "", "true", "Taxable salary", "'.date("Y-m-d H:i:s").'", "1", "19", "no");
');
}

//Income Tax Rate code
if (hr_payroll_payroll_column_exist("income_tax_code") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Income Tax code", "system", "income_tax_code", "", "true", "Income Tax code", "'.date("Y-m-d H:i:s").'", "1", "20", "no");
');
}

//Income Tax PAYE system, need caculating
if (hr_payroll_payroll_column_exist("income_tax_paye") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Personal Income Tax", "system", "income_tax_paye", "", "true", "Personal Income Tax", "'.date("Y-m-d H:i:s").'", "1", "21", "no");
');
}


if (hr_payroll_payroll_column_exist("commission_amount") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Commission Amount", "system", "commission_amount", "", "true", "Commission", "'.date("Y-m-d H:i:s").'", "1", "22", "no");
');
}

if (hr_payroll_payroll_column_exist("bonus_kpi") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Bonus Kpi", "system", "bonus_kpi", "", "true", "Bonus Kpi", "'.date("Y-m-d H:i:s").'", "1", "23", "no");
');
}


//Net Pay formulas
if (hr_payroll_payroll_column_exist("net_pay") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Net Pay", "caculator", "net_pay", "", "true", "Net Pay", "'.date("Y-m-d H:i:s").'", "1", "24", "no");
');
}

if (hr_payroll_payroll_column_exist("total_cost") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Total Cost", "caculator", "total_cost", "", "true", "Total cost", "'.date("Y-m-d H:i:s").'", "1", "25", "no");
');
}

if (hr_payroll_payroll_column_exist("total_hours_by_tasks") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Total hours by tasks", "system", "total_hours_by_tasks", "", "true", "Total hours by tasks", "'.date("Y-m-d H:i:s").'", "1", "16", "no");
');
}

if (hr_payroll_payroll_column_exist("salary_from_tasks") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Salary from tasks", "system", "salary_from_tasks", "", "true", "Salary from tasks", "'.date("Y-m-d H:i:s").'", "1", "16", "no");
');
}

if (hr_payroll_payroll_column_exist("bank_name") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Bank Name", "system", "bank_name", "", "true", "Bank Name", "'.date("Y-m-d H:i:s").'", "1", "17", "no");
');
}

if (hr_payroll_payroll_column_exist("account_number") == 0){
  $db->query('INSERT INTO `' . $dbprefix . 'hrp_payroll_columns` (`column_key`, `taking_method`, `function_name`, `value_related_to`, `display_with_staff`, `description`, `date_created`, `staff_id_created`, `order_display`, `is_edit`) VALUES ("Account Number", "system", "account_number", "", "true", "Account Number", "'.date("Y-m-d H:i:s").'", "1", "17", "no");
');
}


if (!$db->tableExists($dbprefix . "hrp_income_tax_rates")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_income_tax_rates` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `tax_bracket_value_from` DECIMAL(15,2)  NULL,
      `tax_bracket_value_to` DECIMAL(15,2)  NULL,
      `tax_rate` DECIMAL(15,2)  NULL,
      `equivalent_value` DECIMAL(15,2)  NULL,
      `effective_rate` DECIMAL(15,2)  NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hrp_income_tax_rebates")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_income_tax_rebates` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(200) NULL,
      `description` VARCHAR(200) NULL,
      `value` DECIMAL(15,2)  NULL,
      `total` DECIMAL(15,2)  NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hrp_earnings_list")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_earnings_list` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(200) NULL,
      `description` VARCHAR(200) NULL,
      `short_name` VARCHAR(200) NULL,
      `taxable` DECIMAL(15,2)  NULL,
      `basis_type` VARCHAR(200) NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hrp_salary_deductions_list")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_salary_deductions_list` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(200) NULL,
      `description` VARCHAR(200) NULL,
      `rate` DECIMAL(15,2)  NULL,
      `basis` VARCHAR(200) NULL,
      `earn_inclusion` VARCHAR(200) NULL,
      `earn_exclusion` VARCHAR(200) NULL,
      `earnings_max` DECIMAL(15,2)  NULL,
      `tax` DECIMAL(15,2)  NULL,
      `annual_tax_limit` DECIMAL(15,2)  NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hrp_company_contributions_list")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_company_contributions_list` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(200) NULL,
      `description` VARCHAR(200) NULL,
      `rate` DECIMAL(15,2)  NULL,
      `basis` VARCHAR(200) NULL,
      `earn_inclusion` VARCHAR(200) NULL,
      `earn_exclusion` VARCHAR(200) NULL,
      `earnings_max` DECIMAL(15,2)  NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->tableExists($dbprefix . "hrp_earnings_list_hr_records")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_earnings_list_hr_records` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(200) NULL,
      `description` VARCHAR(200) NULL,
      `short_name` VARCHAR(200) NULL,
      `taxable` DECIMAL(15,2)  NULL,
      `basis_type` VARCHAR(200) NULL,
      `rel_type` VARCHAR(200) NULL,
      `rel_id` INT(11) NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

//employees manage
//Save header: eg staff name, deparment name, earning 1: only header
//rel_type: 'hr_records' integration hr records module, 'none' don't integration hr records module
if (!$db->tableExists($dbprefix . "hrp_employees_value")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_employees_value` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,
      `job_title` VARCHAR(200) NULL,
      `income_tax_number` VARCHAR(200) NULL,
      `residential_address` TEXT NULL,
      `income_rebate_code` VARCHAR(200) NULL,
      `income_tax_rate` VARCHAR(200) NULL,

      `probationary_contracts` LONGTEXT NULL,
      `primary_contracts` LONGTEXT NULL,
      `rel_type` VARCHAR(100),

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("probationary_effective" ,$dbprefix . "hrp_employees_value")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_employees_value`

    ADD COLUMN `probationary_effective` DATE NULL ,
    ADD COLUMN `probationary_expiration` DATE NULL ,
    ADD COLUMN `primary_effective` DATE NULL ,
    ADD COLUMN `primary_expiration` DATE NULL 

;");
}


//Timesheet integration
//rel_type: 'hr_timesheets' integration timesheets module, 'none' don't integration timesheets module
if (!$db->tableExists($dbprefix . "hrp_employees_timesheets")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_employees_timesheets` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,

      `day_1`  DECIMAL(15,2)  DEFAULT '0',
      `day_2`  DECIMAL(15,2)  DEFAULT '0',
      `day_3`  DECIMAL(15,2)  DEFAULT '0',
      `day_4`  DECIMAL(15,2)  DEFAULT '0',
      `day_5`  DECIMAL(15,2)  DEFAULT '0',
      `day_6`  DECIMAL(15,2)  DEFAULT '0',
      `day_7`  DECIMAL(15,2)  DEFAULT '0',
      `day_8`  DECIMAL(15,2)  DEFAULT '0',
      `day_9`  DECIMAL(15,2)  DEFAULT '0',
      `day_10`  DECIMAL(15,2)  DEFAULT '0',
      `day_11`  DECIMAL(15,2)  DEFAULT '0',
      `day_12`  DECIMAL(15,2)  DEFAULT '0',
      `day_13`  DECIMAL(15,2)  DEFAULT '0',
      `day_14`  DECIMAL(15,2)  DEFAULT '0',
      `day_15`  DECIMAL(15,2)  DEFAULT '0',
      `day_16`  DECIMAL(15,2)  DEFAULT '0',
      `day_17`  DECIMAL(15,2)  DEFAULT '0',
      `day_18`  DECIMAL(15,2)  DEFAULT '0',
      `day_19`  DECIMAL(15,2)  DEFAULT '0',
      `day_20`  DECIMAL(15,2)  DEFAULT '0',
      `day_21`  DECIMAL(15,2)  DEFAULT '0',
      `day_22`  DECIMAL(15,2)  DEFAULT '0',
      `day_23`  DECIMAL(15,2)  DEFAULT '0',
      `day_24`  DECIMAL(15,2)  DEFAULT '0',
      `day_25`  DECIMAL(15,2)  DEFAULT '0',
      `day_26`  DECIMAL(15,2)  DEFAULT '0',
      `day_27`  DECIMAL(15,2)  DEFAULT '0',
      `day_28`  DECIMAL(15,2)  DEFAULT '0',
      `day_29`  DECIMAL(15,2)  DEFAULT '0',
      `day_30`  DECIMAL(15,2)  DEFAULT '0',
      `day_31`  DECIMAL(15,2)  DEFAULT '0',

      `standard_workday` DECIMAL(15,2)  NULL,
      `actual_workday` DECIMAL(15,2)  NULL,
      `paid_leave` DECIMAL(15,2)  NULL,
      `unpaid_leave` DECIMAL(15,2)  NULL,

      `rel_type` VARCHAR(100),

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("actual_workday_probation" ,$dbprefix . "hrp_employees_timesheets")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_employees_timesheets`
    ADD COLUMN `actual_workday_probation` DECIMAL(15,2) DEFAULT '0' ;");
}

if (!$db->tableExists($dbprefix . "hrp_salary_deductions")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_salary_deductions` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,
      
      `deduction_list` LONGTEXT NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


//Commission integration
//rel_type: 'commission' integration commission module, 'none' don't integration commission module
if (!$db->tableExists($dbprefix . "hrp_commissions")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_commissions` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,

      `commission_amount` DECIMAL(15,2)  NULL,
      `rel_type` VARCHAR(100),

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hrp_income_taxs")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_income_taxs` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,

      `gross_amount` DECIMAL(15,2)  NULL,
      `total_deduction_amount` DECIMAL(15,2)  NULL,
      `income_tax` DECIMAL(15,2)  NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("payslip_id" ,$dbprefix . "hrp_income_taxs")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_income_taxs`
    ADD COLUMN `payslip_id` INT(11) NOT NULL ;");
}


if (!$db->tableExists($dbprefix . "hrp_payslip_details")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_payslip_details` (

      `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
      `payslip_id` INT(11) NULL,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,

      `pay_slip_number` TEXT NULL,
      `payment_run_date` DATE NOT NULL,
      `employee_number` TEXT NULL,
      `employee_name` TEXT NULL,
      `dept_name` TEXT NULL,
      `standard_workday` DECIMAL(15,2)  DEFAULT '0',
      `actual_workday` DECIMAL(15,2)  DEFAULT '0',
      `paid_leave` DECIMAL(15,2)  DEFAULT '0',
      `unpaid_leave` DECIMAL(15,2)  DEFAULT '0',
      `gross_pay` DECIMAL(15,2)  DEFAULT '0',
      `income_tax_paye` DECIMAL(15,2)  DEFAULT '0',
      `total_deductions` DECIMAL(15,2)  DEFAULT '0',
      `net_pay` DECIMAL(15,2)  DEFAULT '0',
      `it_rebate_code` TEXT NULL,
      `it_rebate_value` DECIMAL(15,2)  DEFAULT '0',
      `income_tax_code` TEXT NULL,
      `commission_amount` DECIMAL(15,2)  DEFAULT '0',
      `bonus_kpi` DECIMAL(15,2)  DEFAULT '0',
      `total_cost` DECIMAL(15,2)  DEFAULT '0',

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("total_insurance" ,$dbprefix . "hrp_payslip_details")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslip_details`
    ADD COLUMN `total_insurance` DECIMAL(15,2)  DEFAULT '0' ;");
}

if (!$db->fieldExists("json_data" ,$dbprefix . "hrp_payslip_details")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslip_details`
    ADD COLUMN `json_data` LONGTEXT NULL ;");
}


if (!$db->fieldExists("salary_of_the_probationary_contract" ,$dbprefix . "hrp_payslip_details")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslip_details`

    ADD COLUMN `salary_of_the_probationary_contract`  DECIMAL(15,2)  DEFAULT '0',
    ADD COLUMN `salary_of_the_formal_contract`  DECIMAL(15,2)  DEFAULT '0',
    ADD COLUMN `taxable_salary`  DECIMAL(15,2)  DEFAULT '0',
    ADD COLUMN `actual_workday_probation`  DECIMAL(15,2)  DEFAULT '0'

;");
}


if (!$db->tableExists($dbprefix . "hrp_insurance_list")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_insurance_list` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(200) NULL,
      `description` VARCHAR(200) NULL,
      `rate` DECIMAL(15,2)  NULL,
      `basis` VARCHAR(200) NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}


if (!$db->tableExists($dbprefix . "hrp_staff_insurances")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_staff_insurances` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,
      
      `insurance_list` LONGTEXT NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

add_setting('integrated_hrprofile', "0", 1);
add_setting('integrated_timesheets', "0", 1);
add_setting('integration_actual_workday', "W,B", 1);
add_setting('integration_paid_leave', "AL,HO,EB", 1);
add_setting('integration_unpaid_leave', "U,SI,UB,P", 1);
add_setting('standard_working_time', "160", 1);
add_setting('integrated_commissions', "0", 1);



if (!$db->fieldExists("salary_from_tasks" ,$dbprefix . "hrp_payslip_details")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslip_details`
    ADD COLUMN `total_hours_by_tasks`  DECIMAL(15,2)  DEFAULT '0',
    ADD COLUMN `salary_from_tasks`  DECIMAL(15,2)  DEFAULT '0'

    ;");
}

if (!$db->fieldExists("bank_name" ,$dbprefix . "hrp_employees_value")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_employees_value`

    ADD COLUMN `bank_name` VARCHAR(500) ,
    ADD COLUMN `account_number` VARCHAR(200)

;");
}

if (!$db->fieldExists("plugins_permissions1" ,$dbprefix . "roles")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "roles`
    ADD COLUMN `plugins_permissions1` LONGTEXT NULL
;");
}

if (!$db->tableExists($dbprefix . "hrp_employees_timeshee_leaves")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_employees_timeshee_leaves` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `staff_id` INT(11) NULL,
      `month` DATE NOT NULL,

      `day_1`  TEXT  DEFAULT '',
      `day_2`  TEXT  DEFAULT '',
      `day_3`  TEXT  DEFAULT '',
      `day_4`  TEXT  DEFAULT '',
      `day_5`  TEXT  DEFAULT '',
      `day_6`  TEXT  DEFAULT '',
      `day_7`  TEXT  DEFAULT '',
      `day_8`  TEXT  DEFAULT '',
      `day_9`  TEXT  DEFAULT '',
      `day_10`  TEXT  DEFAULT '',
      `day_11`  TEXT  DEFAULT '',
      `day_12`  TEXT  DEFAULT '',
      `day_13`  TEXT  DEFAULT '',
      `day_14`  TEXT  DEFAULT '',
      `day_15`  TEXT  DEFAULT '',
      `day_16`  TEXT  DEFAULT '',
      `day_17`  TEXT  DEFAULT '',
      `day_18`  TEXT  DEFAULT '',
      `day_19`  TEXT  DEFAULT '',
      `day_20`  TEXT  DEFAULT '',
      `day_21`  TEXT  DEFAULT '',
      `day_22`  TEXT  DEFAULT '',
      `day_23`  TEXT  DEFAULT '',
      `day_24`  TEXT  DEFAULT '',
      `day_25`  TEXT  DEFAULT '',
      `day_26`  TEXT  DEFAULT '',
      `day_27`  TEXT  DEFAULT '',
      `day_28`  TEXT  DEFAULT '',
      `day_29`  TEXT  DEFAULT '',
      `day_30`  TEXT  DEFAULT '',
      `day_31`  TEXT  DEFAULT '',

      `paid_leave` DECIMAL(15,2)  NULL,
      `unpaid_leave` DECIMAL(15,2)  NULL,

      `rel_type` VARCHAR(100),

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("payslip_range" ,$dbprefix . "hrp_payslips")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslips`
    ADD COLUMN `payslip_range` VARCHAR(500)
;");
}

// Verison 1.0.2

if (!$db->tableExists($dbprefix . "hrp_payslip_pdf_templates")) {
    $db->query("CREATE TABLE `" . $dbprefix . "hrp_payslip_pdf_templates` (

    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` TEXT NULL,
    `payslip_template_id` INT(11) NULL,
    `content` LONGTEXT NULL,

      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");
}

if (!$db->fieldExists("pdf_template_id" ,$dbprefix . "hrp_payslips")) { 
  $db->query("ALTER TABLE `" . $dbprefix . "hrp_payslips`
    ADD COLUMN `pdf_template_id` int(11)
;");
}
