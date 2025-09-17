<?php

namespace Recruitment\Models;
use App\Models\Crud_model;

class Candidates_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'rec_candidate';
        parent::__construct($this->table);
    }

    function authenticate($email, $password) {

        if ($email) {
            $email = $this->db->escapeString($email);
        }

        $this->db_builder->select("id,password");
        $result = $this->db_builder->getWhere(array('email' => $email, 'active' => 1));

        if (count($result->getResult()) !== 1) {
            return false;
        }

        $user_info = $result->getRow();

        //there has two password encryption method for legacy (md5) compatibility
        //check if anyone of them is correct
        if ((strlen($user_info->password) === 60 && password_verify($password, $user_info->password)) || $user_info->password === md5($password)) {

                $session = \Config\Services::session();
                $session->set('candidate_id', $user_info->id);

                try {
                    app_hooks()->do_action('app_hook_after_signin');
                } catch (\Exception $ex) {
                    log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
                }

                return true;
            
        }
    }

    function login_user_id() {
        $session = \Config\Services::session();
        return $session->has("candidate_id") ? $session->get("candidate_id") : "";
    }

    function sign_out() {
        try {
            app_hooks()->do_action('app_hook_before_candidate_signout');
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        $session = \Config\Services::session();
        $session->remove('candidate_id');
        app_redirect('recruitment_portal');
    }

    function is_email_exists($email, $id = 0) {
        $users_table = $this->db->prefixTable('rec_candidate');
        $id = $id ? $this->db->escapeString($id) : $id;

        $sql = "SELECT $users_table.* FROM $users_table   
        WHERE $users_table.active=1 AND $users_table.email='$email'";

        $result = $this->db->query($sql);

        if ($result->resultID->num_rows && $result->getRow()->id != $id) {
            return $result->getRow();
        } else {
            return false;
        }
    }


    function get_access_info($user_id = 0) {
        $users_table = $this->db->prefixTable('rec_candidate');

        if (!$user_id) {
            $user_id = 0;
        }

        $sql = "SELECT $users_table.id, $users_table.email,
            $users_table.candidate_name,$users_table.candidate_name as first_name, $users_table.last_name
        FROM $users_table
        WHERE $users_table.id=$user_id";
        return $this->db->query($sql)->getRow();
    }


}
