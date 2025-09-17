<?php

namespace App\Models;

class Client_messages_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'client_messages';
        parent::__construct($this->table);
    }

    function get_details($options = array())
    {
        $messages_table = $this->db->prefixTable('client_messages');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $messages_table.id=$id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where = " AND $messages_table.client_id=$client_id";
        }

        $sql = "SELECT $messages_table.*
        FROM $messages_table
        WHERE $messages_table.is_deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_last_id()
    {
        $sql = "SELECT * FROM crm_client_messages ORDER BY id LIMIT 1";
        return $this->db->query($sql)->getRow();
    }
}
