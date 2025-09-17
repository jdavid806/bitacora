<?php

namespace App\Models;

class Tickets_settings_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'tickets_settings';
        parent::__construct($this->table);
    }

    function count_tickets_by_setting()
    {
        $tickets_settings_table = $this->db->prefixTable('tickets_settings');
        $user_table = $this->db->prefixTable('users');
        $tickets_table = $this->db->prefixTable('tickets');
        $tickets_type_table = $this->db->prefixTable('ticket_types');
        $tasks_settings_table = $this->db->prefixTable('tasks_settings');
        $tasks_table = $this->db->prefixTable('tasks');

        $sql = "SELECT 
            u.id AS user_id,
            CONCAT(u.first_name, ' ', u.last_name) AS user_name,
            COALESCE(ticket_data.total_tickets, 0) AS total_tickets,
            COALESCE(settings_data.total_max_tickets, 0) AS total_max_tickets,
            COALESCE(settings_data.ticket_types, '') AS ticket_types,
            COALESCE(settings_data.ticket_type_ids, '') AS ticket_type_ids, -- Nueva columna
            (SELECT AVG(total_tickets) 
            FROM (
                SELECT COUNT(*) AS total_tickets
                FROM $tickets_table t
                WHERE t.status IN ('new', 'client_replied', 'open') -- Filtro por status
                GROUP BY t.assigned_to
            ) AS subquery) AS avg_total_tickets,
            COALESCE(task_data.total_tasks, 0) AS total_tasks, -- Nueva columna para total de tareas
            (SELECT AVG(total_tasks) 
            FROM (
                SELECT COUNT(*) AS total_tasks
                FROM $tasks_table t
                WHERE t.status_id IN (1,2) -- Filtro por status de tareas
                AND t.deleted = 0
                GROUP BY t.assigned_to
            ) AS subquery) AS avg_total_tasks, -- Nueva columna para la media de tareas
            COALESCE(tasks_settings_data.total_max_tasks, 0) AS max_tasks -- Nueva columna de max_tasks
            FROM $user_table u
            LEFT JOIN (
            SELECT t.assigned_to AS user_id, COUNT(*) AS total_tickets
            FROM $tickets_table t
            WHERE t.status IN ('new', 'client_replied', 'open') -- Filtro por status
            GROUP BY t.assigned_to
            ) AS ticket_data ON u.id = ticket_data.user_id
            LEFT JOIN (
            SELECT s.user_id, SUM(s.max_tickets) AS total_max_tickets, 
                GROUP_CONCAT(tt.title SEPARATOR ', ') AS ticket_types,
                GROUP_CONCAT(tt.id SEPARATOR ', ') AS ticket_type_ids  
            FROM $tickets_settings_table s
            LEFT JOIN $tickets_type_table tt ON s.ticket_type_id = tt.id
            GROUP BY s.user_id
            ) AS settings_data ON u.id = settings_data.user_id
            LEFT JOIN (
            SELECT t.assigned_to AS user_id, COUNT(*) AS total_tasks
            FROM $tasks_table t
            WHERE t.status_id IN (1,2) -- Filtro por status de tareas
            AND t.deleted = 0
            GROUP BY t.assigned_to
            ) AS task_data ON u.id = task_data.user_id
            LEFT JOIN (
            SELECT user_id, SUM(max_tasks) AS total_max_tasks -- Calculamos el total máximo de tareas por usuario
            FROM $tasks_settings_table
            GROUP BY user_id
            ) AS tasks_settings_data ON u.id = tasks_settings_data.user_id
            WHERE u.user_type = 'staff'";
        return $this->db->query($sql);
    }

    function get_details($options = array())
    {

        $tickets_settings_table = $this->db->prefixTable('tickets_settings');

        $where = "";
        $user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " WHERE $tickets_settings_table.user_id=$user_id";
        }

        $sql = "SELECT $tickets_settings_table.*
        FROM $tickets_settings_table $where";
        return $this->db->query($sql);
    }

    function delete_by_user($user_id)
    {
        $tickets_settings_table = $this->db->prefixTable('tickets_settings');

        $query_delete = "DELETE FROM $tickets_settings_table WHERE $tickets_settings_table.user_id = $user_id";
        return $this->db->query($query_delete);
    }
}
