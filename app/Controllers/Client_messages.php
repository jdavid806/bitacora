<?php

namespace App\Controllers;

class Client_messages extends Security_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function save()
    {
        $message_id = $this->request->getPost('message_id');
        $client_id = $this->request->getPost('client_id');
        $this->validate_submitted_data(array(
            "message_id" => "numeric",
            "client_id" => "numeric"
        ));

        $client = $this->Clients_model->get_one($client_id);
        $content = replace_client_data_in_template($client, $this->request->getPost('content'));

        $data = array(
            "user_id" => $this->login_user->id,
            "client_id" => $this->request->getPost('client_id'),
            "content" => $content,
            "status" => $this->request->getPost('status') ? $this->request->getPost('status') : 'sent',
            "is_deleted" => 0,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s')
        );

        $save_id = $this->Client_messages_model->ci_save($data, $message_id);

        if ($save_id) {
            $target_path = get_setting("timeline_file_path");
            $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "client_messages");

            $data["attachment"] = $files_data;
            $this->Client_messages_model->ci_save($data, $save_id);

            $wpp_linkkey_type = $client->is_lead == 1 ? "SALES" : "SUPPORT";

            $file = @unserialize($files_data);
            $media = "";

            if (count($file) > 0) {
                $file_name = get_array_value($file[0], "file_name");
                $file = get_source_url_of_file($file, get_setting("timeline_file_path"), "client_messages");

                $media = $file . $file_name;

                $mediaData = getMediaData(filename: $file_name);

                if (
                    in_array($mediaData['mediaType'], ['audio', 'video']) &&
                    in_array($mediaData['mimeType'], ['video/webm'])
                ) {
                    $this->whatsapp_send_audio_EA(
                        $client->phone,
                        $wpp_linkkey_type,
                        $media
                    );
                } else {
                    $this->whatsapp_send_media_EA(
                        $client->phone,
                        $content,
                        $wpp_linkkey_type,
                        $media,
                        $file_name
                    );
                }
            } else {
                $this->whatsapp_sent_EA(
                    $client->phone,
                    $content,
                    'sendText',
                    $wpp_linkkey_type
                );
            }

            echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
        } else {
            echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
        }
    }

    function save_massive()
    {
        $this->validate_submitted_data(array(
            "message_id" => "numeric"
        ));

        $clients_ids_csv = str_replace("-", ",", $this->request->getPost('leads_ids'));
        $target_path = get_setting("timeline_file_path");
        $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "client_messages");

        $batch_size = 10;
        $batch_item_delay = 2;

        $clients = $this->Clients_model->get_by_ids($clients_ids_csv)->getResult();

        foreach (array_chunk($clients, $batch_size) as $batch) {
            foreach ($batch as $client) {
                $content = $this->request->getPost('content');

                $save_id = save_message($client, $this->login_user->id, $content, $files_data);

                if ($save_id) {
                    $target_path = get_setting("timeline_file_path");
                    $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "client_messages");

                    $data["attachment"] = $files_data;
                    $this->Client_messages_model->ci_save($data, $save_id);

                    $wpp_linkkey_type = $client->is_lead == 1 ? "SALES" : "SUPPORT";

                    $file = @unserialize($files_data);
                    $media = "";

                    if (count($file) > 0) {
                        $file_name = get_array_value($file[0], "file_name");
                        $file = get_source_url_of_file($file, get_setting("timeline_file_path"), "client_messages");

                        $media = $file . $file_name;

                        $mediaData = getMediaData(filename: $file_name);

                        if (
                            in_array($mediaData['mediaType'], ['audio', 'video']) &&
                            in_array($mediaData['mimeType'], ['video/webm'])
                        ) {
                            $this->whatsapp_send_audio_EA(
                                $client->phone,
                                $wpp_linkkey_type,
                                $media
                            );
                        } else {
                            $this->whatsapp_send_media_EA(
                                $client->phone,
                                $content,
                                $wpp_linkkey_type,
                                $media,
                                $file_name
                            );
                        }
                    } else {
                        $this->whatsapp_sent_EA(
                            $client->phone,
                            $content,
                            'sendText',
                            $wpp_linkkey_type
                        );
                    }
                }

                sleep($batch_item_delay);
            }
        }

        echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
    }

    function modal_form($client_id)
    {
        validate_numeric_value($client_id);

        $client = $this->Clients_model->get_one($client_id);
        $template_type = $client->is_lead == 1 ? "client-wpp-msg" : "lead-wpp-msg";
        $message_templates = $this->Templates_model->get_details(["template_type" => $template_type])->getResult();
        $message_templates_dropdown = array();
        $message_templates_dropdown[""] = "- " . app_lang('template') . " -";
        foreach ($message_templates as $template) {
            $message_templates_dropdown[$template->id] = $template->template_name . " | " . $template->language_;
        }
        $view_data["client_id"] = $client_id;
        $view_data["message_templates_dropdown"] = $message_templates_dropdown;
        return $this->template->view('leads/messages/modal_form', $view_data);
    }

    function modal_form_massive()
    {
        return $this->template->view('leads/messages/massive/modal_form');
    }

    function list_data()
    {
        $list_data = $this->Client_messages_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    function list_data_of_client($client_id)
    {
        $list_data = $this->Client_messages_model->get_details(["client_id" => $client_id])->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_row($data)
    {
        $sender = $this->Users_model->get_one($data->user_id);
        $sender_name = $sender ? $sender->first_name . " " . $sender->last_name : "-";

        $created_at = $data->created_at;
        $message = $data->content;

        $file = @unserialize($data->attachment);
        $attachment = "-";

        if ($file && count($file) > 0) {
            $file_name = get_array_value($file[0], "file_name");
            $file = get_source_url_of_file($file, get_setting("timeline_file_path"), "client_messages");

            $attachment = '<a href="' . $file . $file_name . '" target="blank">' . app_lang('see_attachment') . '</a>';
        }

        return array(
            $sender_name,
            $created_at,
            $message,
            $attachment
        );
    }

    public function templates($client_id)
    {
        $client = $this->Clients_model->get_one($client_id);
        $view_data["template_type"] = $client->is_lead == 1 ? "client-wpp-msg" : "lead-wpp-msg";
        $view_data["client_id"] = $client_id;
        return $this->template->view('leads/messages/templates/index', $view_data);
    }

    public function get_message_template()
    {
        $template_id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');

        $template = $this->Templates_model->get_one($template_id);
        $client = $this->Clients_model->get_one($client_id);

        echo json_encode([
            "success" => true,
            "data" => replace_client_data_in_template($client, $template->default_content)
        ]);
    }

    public function template_form()
    {
        $template_id = $this->request->getPost('id');
        $view_data = [];
        $view_data["client_id"] = $this->request->getPost('client_id');
        $view_data["id"] = "";
        $view_data["template_name"] = "";
        $view_data["subject"] = "";
        $view_data["default_content"] = "";
        $view_data["template_type"] = "";
        $view_data["language"] = "";

        if ($template_id) {
            $template = $this->Templates_model->get_one($template_id);
            $view_data["id"] = $template->id;
            $view_data["template_name"] = $template->template_name;
            $view_data["subject"] = $template->subject_;
            $view_data["default_content"] = $template->default_content;
            $view_data["template_type"] = $template->template_type;
            $view_data["language"] = $template->language_;
        }

        return $this->template->view('leads/messages/templates/template_form', $view_data);
    }

    public function render_template_preview()
    {
        $template = $this->request->getPost('template');
        $client = $this->Clients_model->get_one($this->request->getPost('client_id'));
        return replace_client_data_in_template($client, $template);
    }


    public function template_preview()
    {
        $id = $this->request->getPost('id');
        $client = $this->Clients_model->get_one($this->request->getPost('client_id'));
        $view_data["template"] = $this->Templates_model->get_one($id);
        $view_data["client"] = $client;
        return $this->template->view('leads/messages/templates/template_preview', $view_data);
    }

    public function save_template()
    {
        $this->validate_submitted_data(array(
            "template_name" => "required",
            "template_type" => "required",
            "language" => "required"
        ));
        $data = array(
            "template_name" => $this->request->getPost('template_name'),
            "subject_" => $this->request->getPost('subject'),
            "default_content" => $this->request->getPost('default_content'),
            "template_type" => $this->request->getPost('template_type'),
            "language_" => $this->request->getPost('language')
        );

        $id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');

        $save_id = $this->Templates_model->ci_save($data, $id);
        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_template_data($save_id, $client_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, "message" => app_lang('error_occurred')));
        }
    }

    function delete_template()
    {
        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->Templates_model->delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    public function list_templates($type, $client_id)
    {
        $list_data = $this->Templates_model->get_details(["template_type" => $type, "include_general_items" => true])->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_template_row($data, $client_id);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_template_data($id, $client_id)
    {
        $template = $this->Templates_model->get_one($id);
        return $this->_make_template_row($template, $client_id);
    }

    private function _make_template_row($data, $client_id)
    {

        $preview = modal_anchor(get_uri("client_messages/template_preview"), "<i data-feather='eye' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('preview'), "data-post-id" => $data->id, "data-post-client_id" => $client_id));
        $edit = modal_anchor(get_uri("client_messages/template_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_template'), "data-post-id" => $data->id, "data-post-client_id" => $client_id));
        $delete = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_template'), "class" => "delete lead-delete", "data-id" => $data->id, "data-action-url" => get_uri("client_messages/delete_template"), "data-action" => "delete-confirmation"));

        return array(
            $data->template_name,
            $preview . $edit . $delete
        );
    }
}
