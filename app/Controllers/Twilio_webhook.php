<?php

namespace App\Controllers;

use Exception;
use Twilio\TwiML\VoiceResponse;

class Twilio_webhook extends App_Controller {


    private $twilioNumber;

    public function __construct() {
        parent::__construct();
        
        require '../vendor/autoload.php';
        $this->twilioNumber = '+14149375633';
    }

    public function make_client_call() {
        $to = $this->request->getPost('To');
        $userId = $this->request->getPost('user_id');
        $clientId = $this->request->getPost('client_id');

        $response = new VoiceResponse();

        if ($to) {
            $statusCallbackUrl = 'https://app.monaros.co/sistema/index.php/twilio_webhook/update_call_status' .
            '?user_id='.$userId .
            '&client_id=' . $clientId;

            $dial = $response->dial('', [
                'callerId' => $this->twilioNumber
            ]);
            $dial->number($to, [
                'statusCallback' => $statusCallbackUrl,
                'statusCallbackEvent' => 'initiated ringing answered completed',
                'statusCallbackMethod' => 'POST'
            ]);
        } else {
            $response->say("Gracias por usar nuestro servicio de llamada.");
        }

        echo $response;
    }

    public function update_call_status() {
        $this->validate_submitted_data(array(
            "CallSid" => "required",
            "CallStatus" => "required",
            "user_id" => "required|numeric",
            "client_id" => "required|numeric"
        ));
    
        $call_sid = $this->request->getPost('CallSid');
        $call_status = $this->request->getPost('CallStatus');
        $from_number = $this->request->getPost('From');
        $to_number = $this->request->getPost('To');
        $duration = $this->request->getPost('CallDuration');
        $user_id = $this->request->getGet('user_id');
        $client_id = $this->request->getGet('client_id');
    
        $t_call_data = array(
            "twilio_call_sid" => $call_sid,
            "from_number" => $from_number,
            "to_number" => $to_number,
            "status" => $call_status,
            "duration" => $duration
        );
    
        $existing_twilio_call = $this->Twilio_calls_model->get_by_call_sid($call_sid);

        if ($call_status === 'completed' && $call_sid) {
            $result = $this->getPriceWithRetries($call_sid);
    
            if ($result !== null) {
                $t_call_data['price'] = $result['price'];
                $t_call_data['currency'] = $result['currency'];
            }
        }
    
        if ($existing_twilio_call) {
            $t_call_saved_id = $this->Twilio_calls_model->ci_save($t_call_data, $existing_twilio_call->id);
        } else {
            $t_call_saved_id = $this->Twilio_calls_model->ci_save($t_call_data);
        }
    
        if ($t_call_saved_id) {
            $c_call_data = array(
                "user_id" => $user_id,
                "client_id" => $client_id,
                "twilio_call_id" => $t_call_saved_id
            );
    
            $existing_client_call = $this->Client_calls_model->get_by_twilio_id($t_call_saved_id);
    
            if ($existing_client_call) {
                $this->Client_calls_model->ci_save($c_call_data, $existing_client_call->id);
            } else {
                $this->Client_calls_model->ci_save($c_call_data);
            }
        }
    }

    public function test_call_data($call_sid) {
        echo var_dump(get_twilio_call_details($call_sid));
    }

    private function getPriceWithRetries($callSid, $maxRetries = 5, $waitSeconds = 2) {
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $call = get_twilio_call_details($callSid);
    
            if (!is_null($call->price)) {
                return [
                    'price' => $call->price,
                    'currency' => $call->priceUnit
                ];
            }

            sleep($waitSeconds);
        }

        return null;
    }
}
