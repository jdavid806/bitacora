<?php

namespace App\Models;

class Twilio_calls_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'twilio_calls';
        parent::__construct($this->table);
    }

    function get_by_call_sid($call_sid) {
        $t_calls_table = $this->db->prefixTable('twilio_calls');

        $sql = "SELECT * FROM $t_calls_table
        WHERE $t_calls_table.twilio_call_sid='$call_sid'";
        
        return $this->db->query($sql)->getRow();
    }
}
