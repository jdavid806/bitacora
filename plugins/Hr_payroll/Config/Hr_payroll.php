<?php

namespace Hr_payroll\Config;

use CodeIgniter\Config\BaseConfig;
use Hr_payroll\Models\Hr_payroll_model;

class Hr_payroll extends BaseConfig {

	public $app_settings_array = array(
		"hr_payroll_file_path" => PLUGIN_URL_PATH . "Hr_payroll/files/hr_payroll_files/"
	);

	public function __construct() {
		
	}

}
