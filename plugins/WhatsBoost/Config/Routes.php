<?php



namespace Config;



$routes = Services::routes();



$whatsBoostNamespace = ['namespace' => 'WhatsBoost\Controllers'];



// Campaign Section

$routes->get('whatsboost/campaigns', 'CampaignsController::index', $whatsBoostNamespace);

$routes->get('whatsboost/campaigns/campaign', 'CampaignsController::campaign', $whatsBoostNamespace);

$routes->get('whatsboost/campaigns/campaignCopy', 'CampaignsController::campaignCopy', $whatsBoostNamespace);

$routes->post('whatsboost/campaigns/save', 'CampaignsController::save', $whatsBoostNamespace);

$routes->match(['GET', 'POST'], 'whatsboost/campaigns/get_template_map', 'CampaignsController::getTemplateMap', $whatsBoostNamespace);

$routes->post('whatsboost/table', 'CampaignsController::table', $whatsBoostNamespace);

$routes->get('whatsboost/campaigns/campaign/(:num)', 'CampaignsController::campaign/$1', $whatsBoostNamespace);

$routes->get('whatsboost/campaigns/campaignCopy/(:num)', 'CampaignsController::campaignCopy/$1', $whatsBoostNamespace);

$routes->post('whatsboost/campaigns/delete/(:num)', 'CampaignsController::delete/$1', $whatsBoostNamespace);

$routes->get('whatsboost/campaigns/view/(:num)', 'CampaignsController::view/$1', $whatsBoostNamespace);

$routes->post('whatsboost/dailyTaskTable/(:num)', 'CampaignsController::dailyTaskTable/$1', $whatsBoostNamespace);

$routes->post('whatsboost/campaigns/pause_resume_campaign/(:num)', 'CampaignsController::pause_resume_campaign/$1', $whatsBoostNamespace);

$routes->post('whatsboost/campaigns/delete_campaign_file/(:num)', 'CampaignsController::delete_campaign_file/$1', $whatsBoostNamespace);



$routes->match(['GET', 'POST'], 'whatsboost/connect_account', 'WhatsBoostController::connectAccount', $whatsBoostNamespace);

$routes->post('whatsboost/set_default_phone_number_id', 'WhatsBoostController::setDefaultPhoneNumberId', $whatsBoostNamespace);

$routes->get('whatsboost/disconnect', 'WhatsBoostController::disconnect', $whatsBoostNamespace);



$routes->get('whatsboost/template', 'TemplateController::index', $whatsBoostNamespace);

$routes->post('whatsboost/template/get_table_data', 'TemplateController::getTableData', $whatsBoostNamespace);

$routes->get('whatsboost/template/load_templates', 'TemplateController::loadTemplates', $whatsBoostNamespace);



$routes->get('whatsboost/bots', 'BotsController::index', $whatsBoostNamespace);

$routes->post('whatsboost/bots/table/(:any)', 'BotsController::table/$1', $whatsBoostNamespace);

$routes->get('whatsboost/bots/template', 'BotsController::template', $whatsBoostNamespace);

$routes->get('whatsboost/bots/template/(:num)', 'BotsController::template/$1', $whatsBoostNamespace);

$routes->get('whatsboost/bots/message', 'BotsController::message', $whatsBoostNamespace);

$routes->get('whatsboost/bots/message/(:num)', 'BotsController::message/$1', $whatsBoostNamespace);

$routes->post('whatsboost/bots/template/save', 'BotsController::saveTemplates', $whatsBoostNamespace);

$routes->post('whatsboost/bots/message/save', 'BotsController::saveMessages', $whatsBoostNamespace);

$routes->post('whatsboost/bots/active_deactive_bot', 'BotsController::changeActiveStatus', $whatsBoostNamespace);

$routes->get('whatsboost/bots/template_bot', 'BotsController::manageTemplateBot', $whatsBoostNamespace);

$routes->get('whatsboost/bots/message_bot', 'BotsController::manageMessageBot', $whatsBoostNamespace);

$routes->post('whatsboost/bots/delete/(:any)', 'BotsController::deleteBot/$1', $whatsBoostNamespace);

$routes->post('whatsboost/bots/delete_bot_file/(:any)', 'BotsController::delete_bot_file/$1', $whatsBoostNamespace);



$routes->match(['GET', 'POST'], 'whatsboost/settings', 'WhatsBoostController::settings', $whatsBoostNamespace);

$routes->post('whatsboost/save_settings', 'WhatsBoostController::saveSettings', $whatsBoostNamespace);



$routes->get('whatsboost/chat', 'WhatsBoostController::chat', $whatsBoostNamespace);

$routes->post('whatsboost/chat_mark_as_read', 'WhatsBoostController::chat_mark_as_read', $whatsBoostNamespace);

$routes->get('whatsboost/interactions', 'WhatsBoostController::interactions', $whatsBoostNamespace);

$routes->post('whatsboost/send_message', 'WhatsappWebhookController::send_message', $whatsBoostNamespace);

$routes->post('whatsboost/mark_interaction_as_read', 'WhatsappWebhookController::mark_interaction_as_read', $whatsBoostNamespace);



$routes->match(['GET', 'POST'], 'whatsboost/whatsapp_webhook', 'WhatsappWebhookController::index', $whatsBoostNamespace);



// Activity log routes

$routes->get('whatsboost/log', 'WhatsBoostController::manageLog', $whatsBoostNamespace);

$routes->get('whatsboost/clear_log', 'WhatsBoostController::clearLog', $whatsBoostNamespace);

$routes->post('whatsboost/log_table', 'WhatsBoostController::logTable', $whatsBoostNamespace);

$routes->get('whatsboost/view_log/(:num)', 'WhatsBoostController::viewLog/$1', $whatsBoostNamespace);

$routes->post('whatsboost/delete_log/(:num)', 'WhatsBoostController::deleteLog/$1', $whatsBoostNamespace);



$routes->post('whatsboost/delete_chat', 'WhatsBoostController::deleteChat', $whatsBoostNamespace);



// v1.1.0

$routes->match(['GET', 'POST'], 'whatsboost/bots/clone_bot', 'BotsController::cloneBot', $whatsBoostNamespace);

$routes->post('whatsboost/get_ai_response', 'WhatsBoostController::aiResponse', $whatsBoostNamespace);

$routes->get('whatsboost/custom_prompts', 'CustomPromptsController::index', $whatsBoostNamespace);

$routes->post('whatsboost/custom_prompts/table', 'CustomPromptsController::promptTable', $whatsBoostNamespace);

$routes->post('whatsboost/custom_prompt', 'CustomPromptsController::promptModal', $whatsBoostNamespace);

$routes->post('whatsboost/save_prompts', 'CustomPromptsController::save', $whatsBoostNamespace);

$routes->post('whatsboost/get_prompts', 'CustomPromptsController::getPrompts', $whatsBoostNamespace);

$routes->post('whatsboost/delete_prompt', 'CustomPromptsController::delete', $whatsBoostNamespace);

$routes->post('whatsboost/assign_staff', 'WhatsBoostController::assignStaff', $whatsBoostNamespace);

$routes->post('whatsboost/createWhatsboostFile', 'WhatsappWebhookController::createWhatsboostFile', $whatsBoostNamespace);

