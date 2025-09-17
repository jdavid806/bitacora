<?php

namespace Recruitment\Config;

use CodeIgniter\Config\BaseConfig;
use Recruitment\Models\Recruitment_model;

class Recruitment extends BaseConfig {

	public $app_settings_array = array(
		"recruitment_file_path" => PLUGIN_URL_PATH . "Recruitment/files/recruitment_files/"
	);

	public function __construct() {
		
	}

}
