<?php

namespace App\Controllers;

class Client_calls extends Security_Controller {

    function __construct() {
        parent::__construct();
    }

    function list_data() {
        $list_data = $this->Client_calls_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    function list_data_of_client($client_id) {
        $list_data = $this->Client_calls_model->get_details(["client_id" => $client_id])->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_row($data) {

        return array(
            $data->id,
            $data->caller_name,
            $data->from_number,
            $data->call_started_at,
            $data->duration,
            $data->price,
            translateCallStatus($data->status)
        );
    }

}