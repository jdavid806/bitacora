<?php
$db = db_connect('default');
$dbprefix = get_db_prefix();

if ($db->tableExists($dbprefix . 'rec_job_position')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_job_position`;');
}
if ($db->tableExists($dbprefix . 'rec_proposal')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_proposal`;');
}
if ($db->tableExists($dbprefix . 'rec_campaign')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_campaign`;');
}
if ($db->tableExists($dbprefix . 'rec_candidate')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_candidate`;');
}
if ($db->tableExists($dbprefix . 'cd_work_experience')) {
	$db->query('DROP TABLE `'.$dbprefix .'cd_work_experience`;');
}
if ($db->tableExists($dbprefix . 'cd_literacy')) {
	$db->query('DROP TABLE `'.$dbprefix .'cd_literacy`;');
}
if ($db->tableExists($dbprefix . 'cd_family_infor')) {
	$db->query('DROP TABLE `'.$dbprefix .'cd_family_infor`;');
}
if ($db->tableExists($dbprefix . 'rec_interview')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_interview`;');
}
if ($db->tableExists($dbprefix . 'cd_interview')) {
	$db->query('DROP TABLE `'.$dbprefix .'cd_interview`;');
}
if ($db->tableExists($dbprefix . 'cd_care')) {
	$db->query('DROP TABLE `'.$dbprefix .'cd_care`;');
}
if ($db->tableExists($dbprefix . 'rec_criteria')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_criteria`;');
}
if ($db->tableExists($dbprefix . 'rec_evaluation_form')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_evaluation_form`;');
}
if ($db->tableExists($dbprefix . 'rec_list_criteria')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_list_criteria`;');
}
if ($db->tableExists($dbprefix . 'rec_cd_evaluation')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_cd_evaluation`;');
}
if ($db->tableExists($dbprefix . 'rec_set_transfer_record')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_set_transfer_record`;');
}
if ($db->tableExists($dbprefix . 'rec_campaign_form_web')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_campaign_form_web`;');
}
if ($db->tableExists($dbprefix . 'web_to_recruitment')) {
	$db->query('DROP TABLE `'.$dbprefix .'web_to_recruitment`;');
}
if ($db->tableExists($dbprefix . 'cd_skill')) {
	$db->query('DROP TABLE `'.$dbprefix .'cd_skill`;');
}
if ($db->tableExists($dbprefix . 'rec_skill')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_skill`;');
}
if ($db->tableExists($dbprefix . 'rec_company')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_company`;');
}
if ($db->tableExists($dbprefix . 'job_industry')) {
	$db->query('DROP TABLE `'.$dbprefix .'job_industry`;');
}
if ($db->tableExists($dbprefix . 'rec_activity_log')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_activity_log`;');
}
if ($db->tableExists($dbprefix . 'rec_applied_jobs')) {
	$db->query('DROP TABLE `'.$dbprefix .'rec_applied_jobs`;');
}
