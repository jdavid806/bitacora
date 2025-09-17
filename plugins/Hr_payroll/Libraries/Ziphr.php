<?php

// include_once(__DIR__ . '/Zip.php');
// use Hr_payroll\Libraries\Zip;


// // class Ziphr extends \CI_Zip {
//     abstract class Ziphr extends CI_Zip{

//     public function __construct() {
//         parent::__construct();
//     }

// }


// namespace Plugins\Hr_payroll\Libraries;

require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/Libraries/Zip.php';


class Ziphr extends CI_Zip {

    public function __construct() {
        parent::__construct();
    }

}