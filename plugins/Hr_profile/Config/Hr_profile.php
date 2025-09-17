<?php

namespace Hr_profile\Config;

use CodeIgniter\Config\BaseConfig;
use Hr_profile\Models\Hr_profile_model;

class Hr_profile extends BaseConfig {

	public $app_settings_array = array(
		"hr_profile_file_path" => PLUGIN_URL_PATH . "Hr_profile/files/hr_profile_files/"
	);

	public function __construct() {
		
	}

}
