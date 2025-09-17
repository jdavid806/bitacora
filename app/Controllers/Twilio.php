<?php

namespace App\Controllers;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\TwiML\VoiceResponse;
use Twilio\Rest\Client;

class Twilio extends App_Controller {

    private $twilioSid;
    private $twilioToken;
    private $twilioNumber;

    public function __construct() {
        parent::__construct();
        
        require '../vendor/autoload.php';
        $this->twilioSid = 'AC6b0e817ff1ce14e92db315b18ce3ea11';
        $this->twilioToken = '6d52c1c30524df76d126d952979e3af2';
        $this->twilioNumber = '+14149375633';
    }

    public function make_call() {
        $to = $this->request->getPost('To');

        $response = new VoiceResponse();

        if ($to) {
            $dial = $response->dial('', ['callerId' => $this->twilioNumber]);
            $dial->number($to);
        } else {
            $response->say("Gracias por usar nuestro servicio de llamada.");
        }

        echo $response;
    }

    public function make_voice_call() {
        $to_number = $this->request->getPost('phonenumber');
        
        $client = new Client($this->twilioSid, $this->twilioToken);

        $call = $client->calls->create(
            $to_number,
            $this->twilioNumber,
            [
                'url' => 'http://demo.twilio.com/docs/voice.xml'
            ]
        );

        echo "Llamada iniciada con SID: " . $call->sid;
    }
}
