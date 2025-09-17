<?php

namespace App\Controllers;

use stdClass;

class Api_evolution extends App_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function save_instance_EA()
    {

        $inputs = [
            "reject_call",
            "groups_ignore",
            "always_online",
            "read_messages",
            "read_status",
            "sync_full_history"
        ];
        $inputsObj = new stdClass;
        $inputsObj->qrcode = true;
        $inputsObj->integration = "WHATSAPP-BAILEYS";
        $inputsObj->name_ = $this->request->getPost("name_");
        $inputsObj->phone = $this->request->getPost("phone");
        $inputsObj->msg_call = $this->request->getPost("msg_call");

        foreach ($inputs as $input) {
            $inputsObj->$input = $this->request->getPost($input);
            if ($inputsObj->$input === "") {
                $inputsObj->$input = true;
            } else {
                $inputsObj->$input = false;
            }
        }

        $response =  $this->api_evolution_whatsapp($inputsObj, "GLOBAL", "instance/create", "POST", "create_instance");
        $qrcode_b64 = str_replace("~", ":", $response->qrcode->base64);
        $qrcode_path = serialize(move_temp_file("qrcode_ea.png", get_setting("timeline_file_path"), "", $qrcode_b64));

        $data = [
            "name_" => $inputsObj->name_,
            "phone" => $inputsObj->phone,
            "identification" => $response->instance->instanceId,
            "api_key" => $response->hash,
            "status_" => "connect",
            "settings" => json_encode($response->settings),
            "qrcode_path" => $qrcode_path,
            "client_id" => $this->request->getPost("client_id"),
        ];

        $save_id = $this->Api_evolution_instances_model->ci_save($data);

        if ($save_id) {
            $info_instance = $this->Api_evolution_instances_model->get_details(["client_id" => $this->request->getPost("client_id")])->getRow();
            $info_client = $this->Clients_model->get_details(["id" => $info_instance->client_id])->getRow();
            $this->send_info_media($info_client, $info_instance);
        }

        if ($save_id) {
            echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    public function connect_instance($client_id)
    {

        if ($client_id) {
            $view_data["info_instance"] = $this->Api_evolution_instances_model->get_details(["client_id" => $client_id])->getRow();

            $instance = [
                "instance_name" => $view_data["info_instance"]->name_,
                "api_key" => $view_data["info_instance"]->api_key
            ];

            $status_instance = $this->status_conection_instance($instance);

            if ($status_instance == "close" || $status_instance == "connecting") {
                $response = $this->api_evolution_whatsapp([], $instance, "instance/connect", "GET", "connection_instance");

                if ($response->base64) {
                    $qrcode_b64 = str_replace("~", ":", $response->base64);
                    $qrcode_path = serialize(move_temp_file("qrcode_ea.png", get_setting("timeline_file_path"), "", $qrcode_b64));
                }



                $data = [
                    "qrcode_path" => $qrcode_path,
                    "status_" => "connect"
                ];

                $status_saved = $this->Api_evolution_instances_model->ci_save($data, $view_data["info_instance"]->id);

                if ($status_saved) {

                    $view_data["info_instance_new_connection"] = $this->Api_evolution_instances_model->get_details(["id" => $view_data["info_instance"]->id])->getRow();
                    $view_data["client_info"] = $this->Clients_model->get_details(["id" => $view_data["info_instance_new_connection"]->client_id])->getRow();
                    $this->send_info_media($view_data["client_info"], $view_data["info_instance_new_connection"]);
                    return $this->template->view("clients/serviciosActivos/modal_instance_connect_EA", $view_data);
                }
            } else {
                $view_data["client_info"] = $this->Clients_model->get_details(["id" => $view_data["info_instance"]->client_id])->getRow();
                return $this->template->view("clients/serviciosActivos/modal_instance_connect_EA", $view_data);
            }
        }
    }

    public function status_conection_instance($instance = array())
    {
        $response = $this->api_evolution_whatsapp([], $instance, "instance/connectionState", "GET", "status_connection");

        return $response->instance->state;
    }

    public function restart_instance($client_id)
    {

        if ($client_id) {
            $info_instance = $this->Api_evolution_instances_model->get_details(["client_id" => $client_id])->getRow();

            $instance = [
                "instance_name" => $info_instance->name_,
                "api_key" => $info_instance->api_key
            ];

            $this->api_evolution_whatsapp([], $instance, "instance/restart", "POST", "restart_instance");

            echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
        }
    }


    public function logout_instance($client_id)
    {
        $info_instance = $this->Api_evolution_instances_model->get_details(["client_id" => $client_id])->getRow();

        $instance = [
            "instance_name" => $info_instance->name_,
            "api_key" => $info_instance->api_key
        ];

        $response = $this->api_evolution_whatsapp([], $instance, "instance/logout", "DELETE", "logout_instance");

        // echo var_dump($response);
        // die();
        $data = ["status_" => "logout"];
        if ($response->status == "SUCCESS") {
            $status_save = $this->Api_evolution_instances_model->ci_save($data, $info_instance->id);
            if ($status_save) {
                echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
            }
        }
    }

    public function delete_instance($client_id)
    {
        $info_instance = $this->Api_evolution_instances_model->get_details(["client_id" => $client_id])->getRow();

        $instance = [
            "instance_name" => $info_instance->name_,
            "api_key" => $info_instance->api_key
        ];

        $response = $this->api_evolution_whatsapp([], $instance, "instance/delete", "DELETE", "delete_instance");

        if ($response->status == "SUCCESS") {
            $data = ["status_" => "delete"];
            $status_save = $this->Api_evolution_instances_model->ci_save($data, $info_instance->id);
            if ($status_save) {
                echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
            }
        }
    }

    public function send_info_media($info_client, $info_instance)
    {

        $data_send_media = [
            "number" => (string) $info_client->phone,
            "mediatype" => "image",
            "mimetype" => "image/png",
            "caption" => app_lang("msg_qrcode_info_instance_EA"),
            "media" => get_file_url($info_instance->qrcode_path), /* url or base64 */
            "fileName" => "Imagem.png"
        ];
        $options_media = ["attachments" => [
            "file_path" => get_file_url($info_instance->qrcode_path),
            "file_name" => "imagen.png"
        ]];
        $this->api_evolution_whatsapp($data_send_media, "SUPPORT", "message/sendMedia", "POST", "send_media");
        send_app_mail($info_client->email, app_lang("subject_instance_EA"), app_lang("msg_info_codeQR"), $options_media);
    }
}
