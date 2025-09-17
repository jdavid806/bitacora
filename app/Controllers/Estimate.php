<?php

namespace App\Controllers;

use stdClass;

class Estimate extends Security_Controller
{

    function __construct()
    {
        parent::__construct(false);
    }

    function index()
    {
        app_redirect("forbidden");
    }

    function preview($estimate_id = 0, $public_key = "")
    {

        if (!($estimate_id && $public_key)) {
            show_404();
        }

        validate_numeric_value($estimate_id);

        //check public key
        $estimate_info = $this->Estimates_model->get_one($estimate_id);
        if ($estimate_info->public_key !== $public_key) {
            show_404();
        }

        $view_data = array();

        $estimate_data = get_estimate_making_data($estimate_id);
        if (!$estimate_data) {
            show_404();
        }

        $view_data['estimate_preview'] = prepare_estimate_pdf($estimate_data, "html");
        $view_data['show_close_preview'] = false; //don't show back button
        $view_data['estimate_id'] = $estimate_id;
        $view_data['estimate_type'] = "public";
        $view_data['public_key'] = clean_data($public_key);

        return view("estimates/estimate_public_preview", $view_data);
    }

    //update estimate status
    function update_estimate_status($estimate_id, $public_key, $status)
    {
        validate_numeric_value($estimate_id);
        if (!($estimate_id && $public_key && $status)) {
            show_404();
        }

        $estimate_info = $this->Estimates_model->get_one($estimate_id);
        if (!($estimate_info->id && $estimate_info->public_key === $public_key)) {
            show_404();
        }

        //client can only update the status once and the value should be either accepted or declined
        if ($status == "accepted" || $status == "declined") {
            $estimate_data = array("status" => $status);
            $estimate_id = $this->Estimates_model->ci_save($estimate_data, $estimate_id);

            //create notification
            if ($status == "accepted") {
                log_notification("estimate_accepted", array("estimate_id" => $estimate_id), isset($this->login_user->id) ? $this->login_user->id : "999999996");
                $this->session->setFlashdata("success_message", app_lang("estimate_accepted"));
            } else if ($status == "declined") {
                log_notification("estimate_rejected", array("estimate_id" => $estimate_id), isset($this->login_user->id) ? $this->login_user->id : "999999996");
                $this->session->setFlashdata("error_message", app_lang('estimate_rejected'));
            }
        }
    }

    function accept_estimate_modal_form($estimate_id = 0, $public_key = "")
    {
        validate_numeric_value($estimate_id);
        if (!$estimate_id) {
            show_404();
        }

        $estimate_info = $this->Estimates_model->get_details(array("id" => $estimate_id))->getRow();
        if (!$estimate_info->id) {
            show_404();
        }

        if ($public_key) {
            //public estimate
            if ($estimate_info->public_key !== $public_key) {
                show_404();
            }

            $view_data["show_info_fields"] = true;
        } else {
            //estimate preview, should be logged in client contact
            $this->access_only_clients();
            if ($this->login_user->user_type === "client" && $this->login_user->client_id !== $estimate_info->client_id) {
                show_404();
            }

            $view_data["show_info_fields"] = false;
        }
        $view_data["model_info"] = $estimate_info;
        return $this->template->view('estimates/accept_estimate_modal_form', $view_data);
    }

    function send_estimate_client_authorization_template_wpp($estimate_id)
    {

        $estimate_info = $this->Estimates_model->get_details(array("id" => $estimate_id))->getRow();
        $estimate_info->total = $this->Estimates_model->get_estimate_total_summary($estimate_id);
        $is_lead = $estimate_info->is_lead ? 'lead' : 'client';
        $estimate_info->contacts = $this->Users_model->get_details(array("user_type" => $is_lead, "client_id" => $estimate_info->client_id))->getResult();
        $template_name = $estimate_info->is_lead ? "estimate_sent_lead" : "estimate_sent_client";
        $whatsapp_template = $this->Templates_model->get_final_template($template_name, true);
        $setting_info = $this->Settings_model->get_setting('can_approve_budget_users');
        $user_approve_ids = explode(',', $setting_info);

        $contact_info = "";
        foreach ($estimate_info->contacts as $contact) {
            if ($contact->is_primary_contact) {
                $contact_info = $contact;
            }
        }

        $parser_data["ESTIMATE_ID"] = $estimate_info->id;
        $parser_data["CONTACT_FIRST_NAME"] = count($estimate_info->contacts) > 0 ?  $contact_info->first_name : '';
        $parser_data["CONTACT_LAST_NAME"] = count($estimate_info->contacts) > 0 ? $contact_info->last_name : '';
        $parser_data["CLIENT_NAME"] = $estimate_info->company_name;
        $parser_data["ESTIMATE_URL"] = get_uri("estimate/preview/" . $estimate_info->id . "/" . $estimate_info->public_key);
        $parser_data["SPECIALTY"] = $estimate_info->especialidad;
        $parser_data["CITY"] = $estimate_info->city;
        $parser_data["ESTIMATE_DATE"] = $estimate_info->estimate_date;
        $parser_data["TOTAL"] = to_currency($estimate_info->total->estimate_total, $estimate_info->total->currency_symbol);
        $parser_data["TOTAL_AFTER_DISCOUNT"] = to_currency($estimate_info->total->estimate_subtotal - $estimate_info->total->discount_total, $estimate_info->total->currency_symbol);
        $parser_data["SUBTOTAL"] = to_currency($estimate_info->total->estimate_subtotal);
        $parser_data["DISCOUNT"] = to_currency($estimate_info->total->discount_total);

        $parser = \Config\Services::parser();

        $content = $parser->setData($parser_data)->renderString($whatsapp_template['message_default']);
        $content = convertHtmlToPlainText($content);

        $wpp_linkkey_type = $is_lead == "lead" ? "SALES" : "SUPPORT";

        foreach ($user_approve_ids as $user_id) {
            $user_info = $this->Users_model->get_details(array("id" => $user_id))->getRow();
            $this->whatsapp_sent_EA($user_info->phone, $content, 'sendText', $wpp_linkkey_type);
        }
    }

    function get_send_estimate_client_template_wpp($estimate_id)
    {

        $estimate_info = $this->Estimates_model->get_details(array("id" => $estimate_id))->getRow();
        $estimate_info->total = $this->Estimates_model->get_estimate_total_summary($estimate_id);
        $is_lead = $estimate_info->is_lead ? 'lead' : 'client';
        $estimate_info->contacts = $this->Users_model->get_details(array("user_type" => $is_lead, "client_id" => $estimate_info->client_id))->getResult();
        $template_name = $estimate_info->is_lead ? "estimate_accepted_lead_external" : "estimate_accepted_client_external";
        $whatsapp_template = $this->Templates_model->get_final_template($template_name, true);
        $url = "app.monaros.co";

        $contact_info = "";
        foreach ($estimate_info->contacts as $contact) {
            if ($contact->is_primary_contact) {
                $contact_info = $contact;
            }
        }

        $content = str_replace('{ESTIMATE_ID}', $estimate_info->id, $whatsapp_template['message_default']);
        $content = str_replace('{CONTACT_FIRST_NAME}', count($estimate_info->contacts) > 0 ?  $contact_info->first_name : '', $content);
        $content = str_replace('{CONTACT_LAST_NAME}', count($estimate_info->contacts) > 0 ?  $contact_info->last_name : '', $content);
        $content = str_replace('{ESTIMATE_URL}', get_uri("public_routes/no_auth_preview/{$estimate_info->id}/{$estimate_info->public_key}"), $content);
        $content = str_replace('{SPECIALTY}', $estimate_info->especialidad, $content);
        $content = str_replace('{CITY}', $estimate_info->city, $content);
        $content = str_replace('{ESTIMATE_DATE}', $estimate_info->estimate_date, $content);
        $content = str_replace('{TOTAL}', to_currency($estimate_info->total->estimate_total, $estimate_info->total->currency_symbol), $content);
        $content = str_replace('{TOTAL_AFTER_DISCOUNT}', to_currency($estimate_info->total->estimate_subtotal - $estimate_info->total->discount_total, $estimate_info->total->currency_symbol), $content);
        $content = str_replace('{SUBTOTAL}', to_currency($estimate_info->total->estimate_subtotal), $content);
        $content = str_replace('{DISCOUNT}', to_currency($estimate_info->total->discount_total), $content);

        $content = convertHtmlToPlainText($content);
        $content = htmlspecialchars($content);

        $wpp_linkkey_type = $is_lead == "lead" ? "SALES" : "SUPPORT";

        $this->whatsapp_sent_EA($estimate_info->phone, $content, 'sendText', $wpp_linkkey_type);
    }

    function get_send_estimate_client_ext_template($estimate_id)
    {

        $estimate_info = $this->Estimates_model->get_details(array('id' => $estimate_id))->getRow();
        $is_lead = $estimate_info->is_lead ? 'lead' : 'client';
        $estimate_info->contacts = $this->Users_model->get_details(array("user_type" => $is_lead, "client_id" => $estimate_info->client_id))->getResult();
        $estimate_info->total = $this->Estimates_model->get_estimate_total_summary($estimate_id);
        $contact_info = "";

        foreach ($estimate_info->contacts as $contact) {
            if ($contact->is_primary_contact) {
                $contact_info = $contact;
            }
        }


        $contact_language = count($estimate_info->contacts) > 0 ?  $contact_info->language : '';

        $email_template = $this->Templates_model->get_final_template("estimate_sent_client_external", true);

        $parser_data["ESTIMATE_ID"] = $estimate_info->id;
        $parser_data["PUBLIC_ESTIMATE_URL"] = get_uri("estimate/preview/" . $estimate_info->id . "/" . $estimate_info->public_key);
        $parser_data["CONTACT_FIRST_NAME"] = count($estimate_info->contacts) > 0 ?  $contact_info->first_name : '';
        $parser_data["CONTACT_LAST_NAME"] = count($estimate_info->contacts) > 0 ?  $contact_info->last_name : '';
        $parser_data["PROJECT_TITLE"] = $estimate_info->project_title;
        $parser_data["ESTIMATE_URL"] = get_uri("estimates/preview/" . $estimate_info->id);
        $parser_data['SIGNATURE'] = get_array_value($email_template, "signature_$contact_language") ? get_array_value($email_template, "signature_$contact_language") : get_array_value($email_template, "signature_default");
        $parser_data["LOGO_URL"] = get_logo_url();
        $parser_data["RECIPIENTS_EMAIL_ADDRESS"] = count($estimate_info->contacts) > 0 ? $contact_info->email : '';
        $parser_data["SPECIALTY"] = $estimate_info->especialidad;
        $parser_data["CITY"] = $estimate_info->city;
        $parser_data["ESTIMATE_DATE"] = $estimate_info->estimate_date;
        $parser_data["TOTAL"] = to_currency($estimate_info->total->estimate_total, $estimate_info->total->currency_symbol);
        $parser_data["TOTAL_AFTER_DISCOUNT"] = to_currency($estimate_info->total->estimate_subtotal - $estimate_info->total->discount_total, $estimate_info->total->currency_symbol);
        $parser_data["SUBTOTAL"] = to_currency($estimate_info->total->estimate_subtotal);
        $parser_data["DISCOUNT"] = to_currency($estimate_info->total->discount_total);

        $parser = \Config\Services::parser();

        $message = get_array_value($email_template, "message_$contact_language") ? get_array_value($email_template, "message_$contact_language") : get_array_value($email_template, "message_default");
        $subject = get_array_value($email_template, "subject_$contact_language") ? get_array_value($email_template, "subject_$contact_language") : get_array_value($email_template, "subject_default");

        $message = $parser->setData($parser_data)->renderString($message);
        $subject = $parser->setData($parser_data)->renderString($subject);
        $message = htmlspecialchars_decode($message);
        $subject = htmlspecialchars_decode($subject);

        send_app_mail($estimate_info->email, $subject, $message);
    }

    function accept_estimate()
    {
        $validation_array = array(
            "id" => "numeric|required",
            "public_key" => "required"
        );

        if (get_setting("add_signature_option_on_accepting_estimate")) {
            $validation_array["signature"] = "required";
        }

        $this->validate_submitted_data($validation_array);

        $estimate_id = $this->request->getPost("id");
        $estimate_info = $this->Estimates_model->get_one($estimate_id);
        if (!$estimate_info->id) {
            show_404();
        }

        $public_key = $this->request->getPost("public_key");
        if ($estimate_info->public_key !== $public_key) {
            show_404();
        }

        $name = $this->request->getPost("name");
        $email = $this->request->getPost("email");
        $signature = $this->request->getPost("signature");

        $meta_data = array();
        $estimate_data = array();

        if ($signature) {
            $signature = explode(",", $signature);
            $signature = get_array_value($signature, 1);
            $signature = base64_decode($signature);
            $signature = serialize(move_temp_file("signature.jpg", get_setting("timeline_file_path"), "estimate", NULL, "", $signature));

            $meta_data["signature"] = $signature;
            $meta_data["signed_date"] = get_current_utc_time();
        }

        if ($name) {
            //from public estimate
            if (!$email) {
                show_404();
            }

            $meta_data["name"] = $name;
            $meta_data["email"] = $email;
        } else {
            //from preview, should be logged in client contact
            $this->init_permission_checker("estimate");
            $this->access_only_allowed_members_or_client_contact($estimate_info->client_id);
            if ($this->login_user->user_type === "client" && $this->login_user->client_id !== $estimate_info->client_id) {
                show_404();
            }

            $estimate_data["accepted_by"] = $this->login_user->id;
        }

        $this->get_send_estimate_client_template_wpp($estimate_id);
        $this->get_send_estimate_client_ext_template($estimate_id);
        $estimate_data["meta_data"] = serialize($meta_data);
        $estimate_data["status"] = "accepted";

        if ($this->Estimates_model->ci_save($estimate_data, $estimate_id)) {
            log_notification("estimate_accepted", array("estimate_id" => $estimate_id), ($name ? "999999996" : $this->login_user->id));
            echo json_encode(array("success" => true, "message" => app_lang("estimate_accepted")));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang("error_occurred")));
        }
    }
}

/* End of file Estimate.php */
/* Location: ./app/controllers/Estimate.php */