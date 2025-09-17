<?php

namespace Config;

$routes = Services::routes();

$routes->get('recruitment', 'Recruitment::index', ['namespace' => 'Recruitment\Controllers']);
$routes->get('recruitment/(:any)', 'Recruitment::$1', ['namespace' => 'Recruitment\Controllers']);

$routes->post('recruitment/(:any)', 'Recruitment::$1', ['namespace' => 'Recruitment\Controllers']);

$routes->get('forms', 'Forms::index', ['namespace' => 'Recruitment\Controllers']);
$routes->get('forms/(:any)', 'Forms::$1', ['namespace' => 'Recruitment\Controllers']);
$routes->post('forms/(:any)', 'Forms::$1', ['namespace' => 'Recruitment\Controllers']);

$routes->get('recruitment_portal', 'Recruitment_portal::index', ['namespace' => 'Recruitment\Controllers']);
$routes->get('recruitment_portal/(:any)', 'Recruitment_portal::$1', ['namespace' => 'Recruitment\Controllers']);
$routes->post('recruitment_portal/(:any)', 'Recruitment_portal::$1', ['namespace' => 'Recruitment\Controllers']);

$routes->get('candidate_signin', 'Candidate_signin::index', ['namespace' => 'Recruitment\Controllers']);
$routes->get('candidate_signin/(:any)', 'Candidate_signin::$1', ['namespace' => 'Recruitment\Controllers']);
$routes->post('candidate_signin/(:any)', 'Candidate_signin::$1', ['namespace' => 'Recruitment\Controllers']);

$routes->get('candidate_signup', 'Candidate_signup::index', ['namespace' => 'Recruitment\Controllers']);
$routes->get('candidate_signup/(:any)', 'Candidate_signup::$1', ['namespace' => 'Recruitment\Controllers']);
$routes->post('candidate_signup/(:any)', 'Candidate_signup::$1', ['namespace' => 'Recruitment\Controllers']);


