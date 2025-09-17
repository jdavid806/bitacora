<?php

namespace App\Models;

class Client_calls_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'client_calls';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $calls_table = $this->db->prefixTable('client_calls');
        
        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $calls_table.id=$id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where = " AND $calls_table.client_id=$client_id";
        }

        $sql = "
            SELECT 
                $calls_table.id,
                $calls_table.user_id AS user_id,
                $calls_table.client_id AS client_id,
                CONCAT(u.first_name, ' ', u.last_name) AS caller_name,
                tc.twilio_call_sid AS twilio_call_sid,
                tc.created_at AS call_started_at,
                tc.from_number AS from_number,
                tc.duration AS duration,
                tc.price AS price,
                tc.status AS status
            FROM 
                crm_client_calls
            JOIN 
                crm_twilio_calls tc ON $calls_table.twilio_call_id = tc.id
            JOIN 
                crm_users u ON $calls_table.user_id = u.id
            WHERE 1 " . $where;
        return $this->db->query(trim($sql));
    }

    function get_by_twilio_id($call_sid) {
        $t_calls_table = $this->db->prefixTable('client_calls');

        $sql = "SELECT * FROM $t_calls_table
        WHERE $t_calls_table.twilio_call_id='$call_sid'";
        
        return $this->db->query($sql)->getRow();
    }
}
