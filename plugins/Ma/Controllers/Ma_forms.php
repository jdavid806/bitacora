<?php

namespace Ma\Controllers;

use App\Controllers\App_Controller;

class Ma_forms extends App_Controller
{
    public function index()
    {
        show_404();
    }

    /**
     * Estimate request form
     * User no need to see anything like estimate request in the url, this is the reason the method is named quote
     * @param  string $key Estimate request form key identifier
     * @return mixed
     */
    public function quote($key)
    {
        $this->load->model('estimate_request_model');
        $form = $this->estimate_request_model->get_form([
            'form_key' => $key,
        ]);

        if (!$form) {
            show_404();
        }

        // Change the locale so the validation loader function can load
        // the proper localization file
        $GLOBALS['locale'] = get_locale_key($form->language);

        $data['form_fields'] = json_decode($form->form_data);
        if (!$data['form_fields']) {
            $data['form_fields'] = [];
        }

        //TODO: Submit form logic
        if ($this->request->getPost('key')) {
            // TODO: CREATE/SEND EMAIL TEMPLATE FOR NEW ESTIMATE REQUEST AND ASSIGNED

            if ($this->request->getPost('key') == $key) {
                $post_data  = $this->request->getPost();
                $required   = [];
                $submission = [];

                foreach ($data['form_fields'] as $index => $field) {
                    if (isset($field->name)) {
                        if ($field->name == 'file-input') {
                            $submission[] = [
                            'label' => $field->label,
                            'name'  => $field->name,
                            'value' => null,
                            ];

                            continue;
                        }

                        if (!isset($post_data[$field->name])) {
                            $submission[] = [
                            'label' => $field->label,
                            'name'  => $field->name,
                            'value' => '',
                            ];

                            continue;
                        }

                        if ($field->type == 'radio-group') {
                            $index        = array_search($post_data[$field->name], array_column($field->values, 'value'));
                            $submission[] = [
                                'label' => $field->label,
                                'name'  => $field->name,
                                'value' => $field->values[$index]->label,
                            ];
                        } elseif (in_array($field->type, ['select', 'checkbox-group'])) {
                            if (is_array($post_data[$field->name])) {
                                $value = '';
                                foreach ($post_data[$field->name] as $selected) {
                                    $index = array_search($selected, array_column($field->values, 'value'));
                                    $value .= $field->values[$index]->label . '<br>';
                                }
                            } else {
                                $index = array_search($post_data[$field->name], array_column($field->values, 'value'));
                                $value = $field->values[$index]->label;
                            }

                            $submission[] = [
                                'label' => $field->label,
                                'name'  => $field->name,
                                'value' => $value,
                            ];
                        } elseif ($field->type == 'date') {
                            $submission[] = [
                                'label' => $field->label,
                                'name'  => $field->name,
                                'value' => $post_data[$field->name],
                            ];
                        } else {
                            $submission[] = [
                                'label' => $field->label,
                                'name'  => $field->name,
                                'value' => $post_data[$field->name],
                            ];
                        }
                    }

                    if (isset($field->required)) {
                        $required[] = $field->name;
                    }
                }

                if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions_estimate_request_form') == 1) {
                    $required[] = 'accept_terms_and_conditions';
                }

                foreach ($required as $field) {
                    if ($field == 'file-input') {
                        continue;
                    }
                    if (!isset($post_data[$field]) || isset($post_data[$field]) && empty($post_data[$field])) {
                        $this->output->set_status_header(422);
                        die;
                    }
                }


                if (show_recaptcha() && $form->recaptcha == 1) {
                    if (!do_recaptcha_validation($post_data['g-recaptcha-response'])) {
                        echo json_encode(['success' => false,
                            'message'               => _l('recaptcha_error'),
                        ]);
                        die;
                    }
                }

                if (isset($post_data['g-recaptcha-response'])) {
                    unset($post_data['g-recaptcha-response']);
                }

                unset($post_data['key']);
                $success      = false;
                $insert_to_db = true;
            }

            if ($insert_to_db == true) {
                $regular_fields['email']        = $post_data['email'];
                $regular_fields['status']       = $form->status;
                $regular_fields['assigned']     = $form->responsible;
                $regular_fields['date_added']   = date('Y-m-d H:i:s');
                $regular_fields['from_ma_form_id'] = $form->id;
                $regular_fields['submission']   = json_encode($submission);

                $this->db->insert(db_prefix() . 'estimate_requests', $regular_fields);
                $estimate_request_id = $this->db->insert_id();

                hooks()->do_action('estimate_requests_created', [
                    'estimate_request_id'   => $estimate_request_id,
                    'estimate_request_form' => true,
                ]);

                $success = false;
                if ($estimate_request_id) {
                    $success = true;

                    $this->estimate_request_model->assigned_member_notification($estimate_request_id, $form->responsible, true);

                    handle_estimate_request_attachments($estimate_request_id, 'file-input', $form->name);

                    if ($form->notify_request_submitted != 0) {
                        $staff = [];
                        if ($form->notify_type != 'assigned') {
                            $ids = @unserialize($form->notify_ids);

                            if (is_array($ids) && count($ids) > 0) {
                                $this->db->where('active', 1)
                                ->where_in($form->notify_type == 'specific_staff' ? 'staffid' : 'role', $ids);

                                $staff = $this->db->get(db_prefix() . 'staff')->result_array();
                            }
                        } elseif ($form->responsible) {
                            $staff = [
                                [
                                    'staffid' => $form->responsible,
                                    'email'   => get_staff($form->responsible)->email,
                                ],
                            ];
                        }

                        $notifiedUsers = [];

                        foreach ($staff as $member) {
                            if (add_notification([
                                    'description' => 'new_estimate_request_submitted_from_form',
                                    'touserid' => $member['staffid'],
                                    'fromcompany' => 1,
                                    'fromuserid' => 0,
                                    'additional_data' => serialize([
                                        $form->name,
                                    ]),
                                    'link' => 'estimate_request/view/' . $estimate_request_id,
                                ])) {
                                array_push($notifiedUsers, $member['staffid']);
                            }

                            send_mail_template('estimate_request_form_submitted', $estimate_request_id, $member['email']);
                        }

                        pusher_trigger_notification($notifiedUsers);
                    }

                    send_mail_template('estimate_request_received_to_user', $estimate_request_id, $regular_fields['email']);
                }
            }
            // end insert_to_db
            if ($success == true) {
                if (!isset($estimate_request_id)) {
                    $estimate_request_id = 0;
                }

                hooks()->do_action('estimate_request_form_submitted', [
                    'estimate_request_id' => $estimate_request_id,
                    'form_id'             => $form->id,
                ]);
            }

            echo json_encode([
                'success' => $success,
                'message' => $form->success_submit_msg,
            ]);
            die;
        }
        $data['form'] = $form;
        $this->load->view('forms/estimate_request', $data);
    }

    /**
     * Web to lead form
     * User no need to see anything like LEAD in the url, this is the reason the method is named wtl
     * @param  string $key web to lead form key identifier
     * @return mixed
     */
    public function wtl($key)
    {   
        $Ma_model = new \Ma\Models\Ma_model();
        $form = $Ma_model->get_form([
            'form_key' => $key,
        ]);

        if (!$form) {
            show_404();
        }

        $data['form_fields'] = json_decode($form->form_data);
        if (!$data['form_fields']) {
            $data['form_fields'] = [];
        }
        if ($this->request->getPost('key')) {
            if ($this->request->getPost('key') == $key) {
                $post_data = $this->request->getPost();
                $required  = [];

                foreach ($data['form_fields'] as $field) {
                    if (isset($field->required)) {
                        $required[] = $field->name;
                    }
                }

                foreach ($required as $field) {
                    if ($field == 'file-input') {
                        continue;
                    }
                    if (!isset($post_data[$field]) || isset($post_data[$field]) && empty($post_data[$field])) {
                        $this->output->set_status_header(422);
                        die;
                    }
                }

                if (isset($post_data['g-recaptcha-response'])) {
                    unset($post_data['g-recaptcha-response']);
                }

                unset($post_data['key']);

                $regular_fields = [];
                $custom_fields  = [];

                $db = db_connect('default');
                foreach ($post_data as $name => $val) {
                    if (strpos($name, 'form-cf-') !== false) {
                        array_push($custom_fields, [
                            'name'  => $name,
                            'value' => $val,
                        ]);
                    } else {
                        if ($db->fieldExists($name, get_db_prefix() . 'clients')) {
                            if ($name == 'address') {
                                $val = trim($val);
                                $val = nl2br($val);
                            }

                            $regular_fields[$name] = $val;
                        }
                    }
                }
                $success      = false;
                $insert_to_db = true;

                if ($form->allow_duplicate == 0) {
                    $where = [];
                    if (!empty($form->track_duplicate_field) && isset($regular_fields[$form->track_duplicate_field])) {
                        $where[$form->track_duplicate_field] = $regular_fields[$form->track_duplicate_field];
                    }
                    if (!empty($form->track_duplicate_field_and) && isset($regular_fields[$form->track_duplicate_field_and])) {
                        $where[$form->track_duplicate_field_and] = $regular_fields[$form->track_duplicate_field_and];
                    }

                    if (count($where) > 0) {
                        $total = total_rows(get_db_prefix() . 'clients', $where);

                        $duplicateLead = false;
                        /**
                         * Check if the lead is only 1 time duplicate
                         * Because we wont be able to know how user is tracking duplicate and to send the email template for
                         * the request
                         */
                        if ($total == 1) {
                            $db_builder = $db->table(get_db_prefix() . 'clients');

                            $db_builder->where($where);
                            $duplicateLead = $db_builder->get()->getRow();
                        }

                        if ($total > 0) {
                            // Success set to true for the response.
                            $success      = true;
                            $insert_to_db = false;
                           
                        }
                    }
                }

                if ($insert_to_db == true) {
                    $regular_fields['lead_status_id'] = $form->lead_status;
                    if ((isset($regular_fields['company_name']) && empty($regular_fields['company_name'])) || !isset($regular_fields['company_name'])) {
                        $regular_fields['company_name'] = 'Unknown';
                    }

                    $regular_fields['lead_source_id']       = $form->lead_source;
                    $regular_fields['created_by']    = 0;
                    $regular_fields['is_lead']    = 1;
                    $regular_fields['owner_id']     = $form->responsible;
                    $regular_fields['created_date']    = get_current_utc_time();
                    $regular_fields['from_ma_form_id'] = $form->id;
                    $db_builder = $db->table(get_db_prefix() . 'clients');
                    $db_builder->insert($regular_fields);

                    $lead_id = $db->insertID();

                    $success = false;
                    if ($lead_id) {
                        $success = true;

                        if(isset($post_data['email'])){
                            $db_builder = $db->table(db_prefix() . 'custom_fields');

                            $db_builder->where('related_to', 'leads');
                            $db_builder->where('field_type', 'email');
                            $db_builder->where('title', 'Email');
                            $email_field = $db_builder->get()->getRow();

                            if($email_field){

                                $db_builder = $db->table(db_prefix() . 'custom_field_values');
                                $db_builder->insert([
                                    'custom_field_id' => $email_field->id,
                                    'value' => $post_data['email'],
                                    'related_to_id' => $lead_id,
                                    'related_to_type' => 'leads',
                                ]);

                            }

                        }

                        if ($form->notify_lead_imported != 0) {
                            $staff = [];
                            if ($form->notify_type != 'assigned') {
                                $ids = @unserialize($form->notify_ids);

                                if (is_array($ids) && count($ids) > 0) {
                                    $db_builder = $db->table(db_prefix() . 'users');

                                    $db_builder->where('status', 'active')->where('deleted', 0)->where('user_type', 'staff')
                                    ->whereIn($form->notify_type == 'specific_staff' ? 'id' : 'role_id', $ids);
                                    $list_staff = $db_builder->get()->getResultArray();

                                    foreach ($list_staff as $value) {
                                        $staff[] = $value['id'];
                                    }
                                }
                            } elseif ($form->responsible) {
                                $staff = [$form->responsible];
                            }

                            $db_builder = $db->table(db_prefix() . 'notifications');
                            $db_builder->insert([
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'notify_to' => implode(',', $staff),
                                    'user_id' => 0,
                                    'event' => 'lead_created',
                                    'lead_id' => $lead_id,
                                ]);
                        }
                    }
                } // end insert_to_db
                if ($success == true) {
                    if (!isset($lead_id)) {
                        $lead_id = 0;
                    }
                    if (!isset($task_id)) {
                        $task_id = 0;
                    }
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $form->success_submit_msg,
                ]);
                die;
            }
        }

        $data['form'] = $form;
        $data['styled'] = $this->request->getGet('styled');
        $data['col'] = $this->request->getGet('col');
        $data['with_logo'] = $this->request->getGet('with_logo');

        echo view('Ma\Views\components/forms/web_to_lead', $data);
    }

    /**
     * Web to lead form
     * User no need to see anything like LEAD in the url, this is the reason the method is named eq lead
     * @param  string $hash lead unique identifier
     * @return mixed
     */
    public function l($hash)
    {
        if (get_option('gdpr_enable_lead_public_form') == '0') {
            show_404();
        }
        $this->load->model('leads_model');
        $this->load->model('gdpr_model');
        $lead = $this->leads_model->get('', ['hash' => $hash]);

        if (!$lead || count($lead) > 1) {
            show_404();
        }

        $lead = array_to_object($lead[0]);
        load_lead_language($lead->id);

        if ($this->request->getPost('update')) {
            $data = $this->request->getPost();
            unset($data['update']);
            $this->leads_model->update($data, $lead->id);
            redirect($_SERVER['HTTP_REFERER']);
        } elseif ($this->request->getPost('export') && get_option('gdpr_data_portability_leads') == '1') {
            $this->load->library('gdpr/gdpr_lead');
            $this->gdpr_lead->export($lead->id);
        } elseif ($this->request->getPost('removal_request')) {
            $success = $this->gdpr_model->add_removal_request([
                'description'  => nl2br($this->request->getPost('removal_description')),
                'request_from' => $lead->name,
                'lead_id'      => $lead->id,
            ]);
            if ($success) {
                send_gdpr_email_template('gdpr_removal_request_by_lead', $lead->id);
                set_alert('success', _l('data_removal_request_sent'));
            }
            redirect($_SERVER['HTTP_REFERER']);
        }

        $lead->attachments = $this->leads_model->get_lead_attachments($lead->id);
        $this->disableNavigation();
        $this->disableSubMenu();
        $data['title'] = $lead->name;
        $data['lead']  = $lead;
        $this->view('forms/lead');
        $this->data($data);
        $this->layout(true);
    }

    public function public_ticket($key)
    {
        $this->load->model('tickets_model');

        if (strlen($key) != 32) {
            show_error('Invalid ticket key.');
        }

        $ticket = $this->tickets_model->get_ticket_by_id($key);

        if (!$ticket) {
            show_404();
        }

        if (!is_client_logged_in() && $ticket->userid) {
            load_client_language($ticket->userid);
        }

        if ($this->request->getPost()) {
            $this->form_validation->set_rules('message', _l('ticket_reply'), 'required');

            if ($this->form_validation->run() !== false) {
                $replyData = ['message' => $this->request->getPost('message')];

                if ($ticket->userid && $ticket->contactid) {
                    $replyData['userid']    = $ticket->userid;
                    $replyData['contactid'] = $ticket->contactid;
                } else {
                    $replyData['name']  = $ticket->from_name;
                    $replyData['email'] = $ticket->ticket_email;
                }

                $replyid = $this->tickets_model->add_reply($replyData, $ticket->ticketid);

                if ($replyid) {
                    set_alert('success', _l('replied_to_ticket_successfully', $ticket->ticketid));
                }

                redirect(get_ticket_public_url($ticket));
            }
        }

        $data['title']          = $ticket->subject;
        $data['ticket_replies'] = $this->tickets_model->get_ticket_replies($ticket->ticketid);
        $data['ticket']         = $ticket;
        hooks()->add_action('app_customers_footer', 'ticket_public_form_customers_footer');
        $data['single_ticket_view'] = $this->load->view($this->createThemeViewPath('single_ticket'), $data, true);

        $navigationDisabled = hooks()->apply_filters('disable_navigation_on_public_ticket_view', true);
        if ($navigationDisabled) {
            $this->disableNavigation();
        }

        $this->disableSubMenu();

        $this->data($data);

        $this->view('forms/public_ticket');
        no_index_customers_area();
        $this->layout(true);
    }

    public function ticket()
    {
        $form            = new stdClass();
        $form->language  = get_option('active_language');
        $form->recaptcha = 1;

        $this->lang->load($form->language . '_lang', $form->language);
        if (file_exists(APPPATH . 'language/' . $form->language . '/custom_lang.php')) {
            $this->lang->load('custom_lang', $form->language);
        }

        $form->success_submit_msg = _l('success_submit_msg');

        $form = hooks()->apply_filters('ticket_form_settings', $form);

        if ($this->request->getPost() && $this->input->is_ajax_request()) {
            $post_data = $this->request->getPost();

            $required = ['subject', 'department', 'email', 'name', 'message', 'priority'];

            if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions_ticket_form') == 1) {
                $required[] = 'accept_terms_and_conditions';
            }

            foreach ($required as $field) {
                if (!isset($post_data[$field]) || isset($post_data[$field]) && empty($post_data[$field])) {
                    $this->output->set_status_header(422);
                    die;
                }
            }

            if (show_recaptcha() && $form->recaptcha == 1) {
                if (!do_recaptcha_validation($post_data['g-recaptcha-response'])) {
                    echo json_encode([
                        'success' => false,
                        'message' => _l('recaptcha_error'),
                    ]);
                    die;
                }
            }

            $post_data = [
                'email'      => $post_data['email'],
                'name'       => $post_data['name'],
                'subject'    => $post_data['subject'],
                'department' => $post_data['department'],
                'priority'   => $post_data['priority'],
                'service'    => isset($post_data['service']) && is_numeric($post_data['service'])
                    ? $post_data['service']
                    : null,
                'custom_fields' => isset($post_data['custom_fields']) && is_array($post_data['custom_fields'])
                    ? $post_data['custom_fields']
                    : [],
                'message' => $post_data['message'],
            ];

            $success = false;

            $this->db->where('email', $post_data['email']);
            $result = $this->db->get(db_prefix() . 'contacts')->row();

            if ($result) {
                $post_data['userid']    = $result->userid;
                $post_data['contactid'] = $result->id;
                unset($post_data['email']);
                unset($post_data['name']);
            }

            $this->load->model('tickets_model');

            $post_data = hooks()->apply_filters('ticket_external_form_insert_data', $post_data);
            $ticket_id = $this->tickets_model->add($post_data);

            if ($ticket_id) {
                $success = true;
            }

            if ($success == true) {
                hooks()->do_action('ticket_form_submitted', [
                    'ticket_id' => $ticket_id,
                ]);
            }

            echo json_encode([
                'success' => $success,
                'message' => $form->success_submit_msg,
            ]);

            die;
        }

        $this->load->model('tickets_model');
        $this->load->model('departments_model');
        $data['departments'] = $this->departments_model->get();
        $data['priorities']  = $this->tickets_model->get_priority();

        $data['priorities']['callback_translate'] = 'ticket_priority_translate';
        $data['services']                         = $this->tickets_model->get_service();

        $data['form'] = $form;
        $this->load->view('forms/ticket', $data);
    }
}
