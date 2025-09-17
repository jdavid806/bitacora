<?php

namespace Config;

$routes = Services::routes();

$routes->get('hr_payroll', 'Hr_payroll::index', ['namespace' => 'Hr_payroll\Controllers']);
$routes->get('hr_payroll/(:any)', 'Hr_payroll::$1', ['namespace' => 'Hr_payroll\Controllers']);

$routes->post('hr_payroll/(:any)', 'Hr_payroll::$1', ['namespace' => 'Hr_payroll\Controllers']);


