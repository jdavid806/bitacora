<?php

namespace App\Controllers;

class Templates extends Security_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    private function _templates()
    {
        $templates_array["account"] = array(
            "login_info" => array("USER_FIRST_NAME", "USER_LAST_NAME", "DASHBOARD_URL", "USER_LOGIN_EMAIL", "USER_LOGIN_PASSWORD", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "reset_password" => array("ACCOUNT_HOLDER_NAME", "RESET_PASSWORD_URL", "SITE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "team_member_invitation" => array("INVITATION_SENT_BY", "INVITATION_URL", "SITE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "new_client_greetings" => array("CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "COMPANY_NAME", "DASHBOARD_URL", "CONTACT_LOGIN_EMAIL", "CONTACT_LOGIN_PASSWORD", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "client_contact_invitation" => array("INVITATION_SENT_BY", "INVITATION_URL", "SITE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "verify_email" => array("VERIFY_EMAIL_URL", "SITE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
        );

        if (get_setting("module_announcement")) {
            $templates_array["announcement"] = array(
                "announcement_created" => array("ANNOUNCEMENT_TITLE", "ANNOUNCEMENT_CONTENT", "USER_NAME", "APP_TITLE", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        $templates_array["common"] = array(
            "general_notification" => array("EVENT_TITLE", "EVENT_DETAILS", "APP_TITLE", "COMPANY_NAME", "NOTIFICATION_URL", "LOGO_URL", "SIGNATURE", "TO_USER_NAME", "RECIPIENTS_EMAIL_ADDRESS"),
            "signature" => array()
        );

        if (get_setting("module_contract")) {
            $templates_array["contract"] = array(
                "contract_sent" => array("CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "CONTRACT_ID", "CONTRACT_URL", "PUBLIC_CONTRACT_URL", "PROJECT_TITLE", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "contract_accepted" => array("CONTRACT_ID", "CONTRACT_URL", "PROJECT_TITLE", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS", "PUBLIC_CONTRACT_URL"),
                "contract_rejected" => array("CONTRACT_ID", "CONTRACT_URL", "PROJECT_TITLE", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS", "PUBLIC_CONTRACT_URL"),
            );
        }

        if (get_setting("module_estimate")) {
            $templates_array["estimate"] = array(
                "estimate_sent" => array("ESTIMATE_ID", "CLIENT_NAME", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SIGNATURE", "ESTIMATE_URL", "LOGO_URL", "PUBLIC_ESTIMATE_URL", "RECIPIENTS_EMAIL_ADDRESS", "SPECIALTY", 'CITY', 'ESTIMATE_DATE', 'TOTAL', 'TOTAL_AFTER_DISCOUNT', 'SUBTOTAL', 'DISCOUNT'),
                "estimate_accepted" => array("ESTIMATE_ID", "CLIENT_NAME", "SIGNATURE", "ESTIMATE_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS", "SPECIALTY", 'CITY', 'ESTIMATE_DATE', 'TOTAL', 'TOTAL_AFTER_DISCOUNT', 'SUBTOTAL', 'DISCOUNT'),
                "estimate_rejected" => array("ESTIMATE_ID", "CLIENT_NAME", "SIGNATURE", "ESTIMATE_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "estimate_request_received" => array("ESTIMATE_REQUEST_ID", "CLIENT_NAME", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SIGNATURE", "ESTIMATE_REQUEST_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "estimate_commented" => array("ESTIMATE_ID", "USER_NAME", "CLIENT_NAME", "COMMENT_CONTENT", "ESTIMATE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "estimate_view" => array("ESTIMATE_ID", "IP", "COUNTRY", "CITY", "CLIENT_NAME", "ESTIMATE_VALUE", "ESTIMATE_URL", "ESTIMATE_CREATED_BY"),
            );
        }

        $templates_array["events"] = array(
            "add_meet" => array("CUSTOMER_NAME", "LOCATION", "DATE", "HOUR_START", "HOUR_END", "SELLER", "SPECIALTY", "REASON_EVENT", "COUNTRY"),
            "modify_meet" => array("CUSTOMER_NAME", "LOCATION", "DATE", "HOUR_START", "HOUR_END", "SELLER", "SPECIALTY", "REASON_EVENT", "COUNTRY"),
            "reminder" => array("CUSTOMER_NAME", "LOCATION", "DATE", "HOUR_START", "HOUR_END", "SELLER", "SPECIALTY", "REASON_EVENT", "COUNTRY"),
        );

        if (get_setting("module_invoice")) {
            $templates_array["invoice"] = array(
                "send_invoice" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "SIGNATURE", "INVOICE_URL", "LOGO_URL", "PUBLIC_PAY_INVOICE_URL", "INVOICE_FULL_ID", "RECIPIENTS_EMAIL_ADDRESS"),
                "invoice_payment_confirmation" => array("INVOICE_ID", "PAYMENT_AMOUNT", "INVOICE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "invoice_due_reminder_before_due_date" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "SIGNATURE", "INVOICE_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "invoice_overdue_reminder" => array("INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "BALANCE_DUE", "DUE_DATE", "SIGNATURE", "INVOICE_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "recurring_invoice_creation_reminder" => array("CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "APP_TITLE", "INVOICE_URL", "NEXT_RECURRING_DATE", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "invoice_manual_payment_added" => array("INVOICE_ID", "PAYMENT_AMOUNT", "INVOICE_URL", "ADDED_BY", "PAYMENT_NOTE", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "send_credit_note" => array("CREDIT_NOTE_ID", "INVOICE_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROJECT_TITLE", "SIGNATURE", "CREDIT_NOTE_URL", "LOGO_URL", "CREDIT_NOTE_FULL_ID", "INVOICE_FULL_ID", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        if (get_setting("module_message")) {
            $templates_array["message"] = array(
                "message_received" => array("SUBJECT", "USER_NAME", "MESSAGE_CONTENT", "MESSAGE_URL", "APP_TITLE", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        if (get_setting("module_order")) {
            $templates_array["order"] = array(
                "new_order_received" => array("ORDER_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SIGNATURE", "ORDER_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "order_status_updated" => array("ORDER_ID", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SIGNATURE", "ORDER_URL", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        $templates_array["project"] = array(
            "project_completed" => array("PROJECT_ID", "PROJECT_TITLE", "USER_NAME", "PROJECT_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "project_task_deadline_reminder" => array("APP_TITLE", "DEADLINE", "SIGNATURE", "TASKS_LIST", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
        );

        if (get_setting("module_proposal")) {
            $templates_array["proposal"] = array(
                "proposal_sent" => array("CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "PROPOSAL_ID", "PROPOSAL_URL", "PUBLIC_PROPOSAL_URL", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
                "proposal_accepted" => array("PROPOSAL_ID", "PROPOSAL_URL", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS", "PUBLIC_PROPOSAL_URL"),
                "proposal_rejected" => array("PROPOSAL_ID", "PROPOSAL_URL", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS", "PUBLIC_PROPOSAL_URL"),
                "proposal_commented" => array("PROPOSAL_ID", "USER_NAME", "COMMENT_CONTENT", "PROPOSAL_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        if (get_setting("module_subscription")) {
            $templates_array["subscription"] = array(
                "subscription_request_sent" => array("SUBSCRIPTION_ID", "SUBSCRIPTION_TITLE", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SUBSCRIPTION_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "subscription_started" => array("SUBSCRIPTION_ID", "SUBSCRIPTION_TITLE", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SUBSCRIPTION_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "subscription_cancelled" => array("SUBSCRIPTION_ID", "SUBSCRIPTION_TITLE", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "CANCELLED_BY", "SUBSCRIPTION_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "subscription_invoice_created_via_cron_job" => array("SUBSCRIPTION_TITLE", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "INVOICE_ID", "INVOICE_FULL_ID", "BALANCE_DUE", "DUE_DATE", "INVOICE_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "subscription_renewal_reminder" => array("SUBSCRIPTION_ID", "SUBSCRIPTION_TITLE", "CONTACT_FIRST_NAME", "CONTACT_LAST_NAME", "SUBSCRIPTION_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        $templates_array["task"] = array(
            "task_commented" => array("TASK_ID", "TASK_TITLE", "TASK_DESCRIPTION", "TASK_COMMENT", "CONTEXT_LABEL", "CONTEXT_TITLE", "USER_NAME", "TASK_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "task_assigned" => array("TASK_ID", "TASK_TITLE", "TASK_DESCRIPTION", "CONTEXT_LABEL", "CONTEXT_TITLE", "USER_NAME", "ASSIGNED_TO_USER_NAME", "TASK_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
            "task_general" => array("EVENT_TITLE", "TASK_ID", "TASK_TITLE", "TASK_DESCRIPTION", "CONTEXT_LABEL", "CONTEXT_TITLE", "USER_NAME", "TASK_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
        );

        if (get_setting("module_ticket")) {
            $templates_array["ticket"] = array(
                "ticket_created" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_CONTENT", "TICKET_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "ticket_commented" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_CONTENT", "TICKET_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "ticket_closed" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_URL", "LOGO_URL", "SIGNATURE", "RECIPIENTS_EMAIL_ADDRESS"),
                "ticket_reopened" => array("TICKET_ID", "TICKET_TITLE", "USER_NAME", "TICKET_URL", "SIGNATURE", "LOGO_URL", "RECIPIENTS_EMAIL_ADDRESS"),
            );
        }

        $tickets_template_variables = $this->Custom_fields_model->get_email_template_variables_array("tickets", 0, $this->login_user->is_admin, $this->login_user->user_type);
        if ($tickets_template_variables) {
            //marge custom variables with default variables
            $templates_array["ticket"]["ticket_created"] = array_merge($templates_array["ticket"]["ticket_created"], $tickets_template_variables);
            $templates_array["ticket"]["ticket_commented"] = array_merge($templates_array["ticket"]["ticket_commented"], $tickets_template_variables);
            $templates_array["ticket"]["ticket_closed"] = array_merge($templates_array["ticket"]["ticket_closed"], $tickets_template_variables);
            $templates_array["ticket"]["ticket_reopened"] = array_merge($templates_array["ticket"]["ticket_reopened"], $tickets_template_variables);
        }

        $templates_array = app_hooks()->apply_filters("app_filter_email_templates", $templates_array);

        return $templates_array;
    }

    function index()
    {
        $view_data["templates"] = $this->_templates();
        return $this->template->rander("templates/index", $view_data);
    }

    function save()
    {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        $data = array(
            "subject_" => $this->request->getPost('subject_'),
            "custom_content" => decode_ajax_post_data($this->request->getPost('custom_content'))
        );
        $save_id = $this->Templates_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function restore_to_default()
    {

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $template_id = $this->request->getPost('id');

        $data = array(
            "custom_content" => ""
        );
        $save_id = $this->Templates_model->ci_save($data, $template_id);
        if ($save_id) {
            $default_content = $this->Templates_model->get_one($save_id)->default_content;
            echo json_encode(array("success" => true, "data" => $default_content, 'message' => app_lang('template_restored')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* load template edit form */

    function form($template_name = "", $template_language = "")
    {
        $template_name_formatted = preg_replace('/_(lead|client|lead_internal|client_external|lead_external|client_internal)$/', '', $template_name);
        $view_data['model_info'] = $this->Templates_model->get_one_where(array("template_name" => $template_name, "language_" => $template_language));
        $variables_array = array_column($this->_templates(), $template_name_formatted);
        $variables = get_array_value($variables_array, 0);
        $view_data['variables'] = $variables ? $variables : array();

        $view_data["different_language_templates"] = $this->Templates_model->get_details(array("template_name" => $template_name, "template_type" => "whatsapp"))->getResult();
        return $this->template->view('templates/form', $view_data);
    }

    function add_template_modal_form()
    {
        $template_name = $this->request->getPost('template_name');
        $template_info = $this->Templates_model->get_details(array("template_name" => $template_name))->getResult();

        $template_languages = array();
        foreach ($template_info as $template) {
            $template_languages[] = $template->language_;
        }

        $available_languages = array_diff(get_language_list("list"), $template_languages);
        sort($available_languages);

        $language_dropdown = array();
        foreach ($available_languages as $language) {
            $language_dropdown[$language] = ucfirst($language);
        }

        $view_data['language_dropdown'] = $language_dropdown;
        $view_data['template_name'] = $template_name;

        return $this->template->view('templates/add_template_modal_form', $view_data);
    }

    function save_template()
    {
        $id = $this->request->getPost('id');
        validate_numeric_value($id);

        $template_name = $this->request->getPost('template_name');
        $language = $this->request->getPost('language_');
        // $tamplate_type = $this->request->getPost('tamplate_type');

        $template_info = $this->Templates_model->get_one_where(array("template_name" => $template_name));

        if ($template_info->custom_content) {
            $default_content = $template_info->custom_content;
        } else {
            $default_content = $template_info->default_content;
        }

        $data = array(
            "template_name" => $template_name,
            "subject_" => $template_info->subject_,
            "default_content" => decode_ajax_post_data($default_content),
            "template_type" => "whatsapp",
            "language_" => $language
        );

        $save_id = $this->Templates_model->ci_save($data, $id);
        if ($save_id) {
            $view_data['tab_data'] = $this->Templates_model->get_details(array("id" => $save_id))->getRow();
            $tab_view = $this->template->view("templates/tab_view", $view_data);
            echo json_encode(array("success" => true, 'data' => $tab_view, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function different_language_form($id = 0)
    {
        validate_numeric_value($id);
        $view_data['model_info'] = $this->Templates_model->get_one_where(array("id" => $id));
        $template_name_formatted = preg_replace('/_(lead|client|lead_internal|client_external|lead_external|client_internal)$/', '', $view_data['model_info']->template_name);
        $variables_array = array_column($this->_templates(), $template_name_formatted);
        $variables = get_array_value($variables_array, 0);
        $view_data['variables'] = $variables ? $variables : array();
        $view_data['unsupported_title_variables'] = json_encode(array("SIGNATURE", "TASKS_LIST", "TICKET_CONTENT", "MESSAGE_CONTENT", "EVENT_DETAILS"));

        return $this->template->view('templates/different_language_form', $view_data);
    }
}

/* End of file Templates.php */
/* Location: ./app/controllers/Templates.php */