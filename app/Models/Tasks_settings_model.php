<?php

namespace App\Models;

class Tasks_settings_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'tasks_settings';
        parent::__construct($this->table);
    }

    function get_details($options = array())
    {

        $tasks_settings_table = $this->db->prefixTable('tasks_settings');

        $where = "";
        $user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " WHERE $tasks_settings_table.user_id=$user_id";
        }

        $sql = "SELECT $tasks_settings_table.*
        FROM $tasks_settings_table $where";
        return $this->db->query($sql);
    }

    function delete_by_user($user_id)
    {
        $tasks_settings_table = $this->db->prefixTable('tasks_settings');

        $query_delete = "DELETE FROM $tasks_settings_table WHERE $tasks_settings_table.user_id = $user_id";
        return $this->db->query($query_delete);
    }
}
