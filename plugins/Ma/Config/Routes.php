<?php

namespace Config;

$routes = Services::routes();

$routes->get('ma', 'Ma::index', ['namespace' => 'Ma\Controllers']);
$routes->get('ma/(:any)', 'Ma::$1', ['namespace' => 'Ma\Controllers']);
$routes->post('ma/(:any)', 'Ma::$1', ['namespace' => 'Ma\Controllers']);

$routes->get('ma_public', 'Ma_public::index', ['namespace' => 'Ma\Controllers']);
$routes->get('ma_public/(:any)', 'Ma_public::$1', ['namespace' => 'Ma\Controllers']);
$routes->post('ma_public/(:any)', 'Ma_public::$1', ['namespace' => 'Ma\Controllers']);

$routes->get('ma_forms', 'Ma_forms::index', ['namespace' => 'Ma\Controllers']);
$routes->get('ma_forms/(:any)', 'Ma_forms::$1', ['namespace' => 'Ma\Controllers']);
$routes->post('ma_forms/(:any)', 'Ma_forms::$1', ['namespace' => 'Ma\Controllers']);