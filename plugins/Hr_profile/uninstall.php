<?php
$db = db_connect('default');
$dbprefix = get_db_prefix();

if ($db->tableExists($dbprefix . 'hr_rec_transfer_records')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_rec_transfer_records`;');
}

if ($db->tableExists($dbprefix . 'hr_group_checklist_allocation')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_group_checklist_allocation`;');
}

if ($db->tableExists($dbprefix . 'hr_allocation_asset')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_allocation_asset`;');
}

if ($db->tableExists($dbprefix . 'hr_training_allocation')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_training_allocation`;');
}

if ($db->tableExists($dbprefix . 'hr_jp_interview_training')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_jp_interview_training`;');
}

if ($db->tableExists($dbprefix . 'hr_position_training')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_position_training`;');
}

if ($db->tableExists($dbprefix . 'hr_position_training_question_form')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_position_training_question_form`;');
}

if ($db->tableExists($dbprefix . 'hr_p_t_form_question_box')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_p_t_form_question_box`;');
}

if ($db->tableExists($dbprefix . 'hr_p_t_form_question_box_description')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_p_t_form_question_box_description`;');
}

if ($db->tableExists($dbprefix . 'hr_p_t_form_results')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_p_t_form_results`;');
}

if ($db->tableExists($dbprefix . 'hr_p_t_surveyresultsets')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_p_t_surveyresultsets`;');
}

if ($db->tableExists($dbprefix . 'hr_staff_contract_detail')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_staff_contract_detail`;');
}

if ($db->tableExists($dbprefix . 'hr_staff_contract')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_staff_contract`;');
}

if ($db->tableExists($dbprefix . 'hr_dependent_person')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_dependent_person`;');
}
if ($db->tableExists($dbprefix . 'hr_list_staff_quitting_work')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_list_staff_quitting_work`;');
}
if ($db->tableExists($dbprefix . 'hr_procedure_retire_of_staff')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_procedure_retire_of_staff`;');
}
if ($db->tableExists($dbprefix . 'hr_views_tracking')) {
	$db->query('DROP TABLE `'.$dbprefix .'hr_views_tracking`;');
}


