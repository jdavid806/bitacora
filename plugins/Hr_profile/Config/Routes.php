<?php

namespace Config;

$routes = Services::routes();

$routes->get('hr_profile', 'Hr_profile::index', ['namespace' => 'Hr_profile\Controllers']);
$routes->get('hr_profile/(:any)', 'Hr_profile::$1', ['namespace' => 'Hr_profile\Controllers']);

$routes->post('hr_profile/(:any)', 'Hr_profile::$1', ['namespace' => 'Hr_profile\Controllers']);


