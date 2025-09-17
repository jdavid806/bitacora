<?php

namespace App\Models;

class Api_evolution_instances_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'instances_ea';
        parent::__construct($this->table);
    }

    function get_details($options = array())
    {
        $instances_table = $this->db->prefixTable('instances_ea');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $instances_table.id=$id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where = " AND $instances_table.client_id=$client_id";
        }

        $phone = $this->_get_clean_value($options, "phone");
        if ($phone) {
            $where .= " AND $instances_table.phone LIKE '%$phone%'";
        }

        $sql = "SELECT $instances_table.*
        FROM $instances_table
        WHERE $instances_table.status_ != '' $where";
        return $this->db->query($sql);
    }
}
