<?php



namespace App\Models;



class Segmentation_model extends Crud_model {



    protected $table = null;



    function __construct() {

        $this->table = 'segmentation';

        parent::__construct($this->table);

    }



    function get_unread_segmentation($user_id, $user_type, $client_group_ids = "") {

        $segmentation_table = $this->db->prefixTable('segmentation');

        $user_id = $this->_get_clean_value($user_id);

        $user_type = $this->_get_clean_value($user_type);

        $client_group_ids = $this->_get_clean_value($client_group_ids);



        $now = get_my_local_time("Y-m-d");

        $where = $this->prepare_share_with_query($segmentation_table, $user_type, $client_group_ids);



        $sql = "SELECT $segmentation_table.*

        FROM $segmentation_table

        WHERE $segmentation_table.deleted=0 AND start_date<='$now' AND end_date>='$now' AND FIND_IN_SET($user_id,$segmentation_table.read_by) = 0 $where";

        return $this->db->query($sql);

    }



    private function prepare_share_with_query($segmentation_table, $user_type, $client_group_ids) {

        $where = "";

        if ($user_type) { //if no user type found, we'll assume the user has permission to access all

            if ($user_type === "staff") {

                $where = " AND FIND_IN_SET('all_members',$segmentation_table.share_with)";

            } else if($client_group_ids){

                $client_groups_where = "";



                $client_group_ids = explode(',', $client_group_ids);

                foreach ($client_group_ids as $group_id) {

                    $client_groups_where .= " OR FIND_IN_SET('cg:$group_id', $segmentation_table.share_with)";

                }



                $where = " AND (FIND_IN_SET('all_clients', $segmentation_table.share_with) $client_groups_where )";

            }

        }



        return $where;

    }



    function get_details($options = array()) {

        $segmentation_table = $this->db->prefixTable('segmentation');

        $users_table = $this->db->prefixTable('users');



        $where = "";

        $id = $this->_get_clean_value($options, "id");

        if ($id) {

            $where .= " AND $segmentation_table.id=$id";

        }



        $client_group_ids = $this->_get_clean_value($options, "client_group_ids");

        $user_type = $this->_get_clean_value($options, "user_type");

        $where .= $this->prepare_share_with_query($segmentation_table, $user_type, $client_group_ids);



        $sql = "SELECT $segmentation_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS created_by_user, $users_table.image AS created_by_avatar

        FROM $segmentation_table

        LEFT JOIN $users_table ON $users_table.id= $segmentation_table.created_by

        WHERE $segmentation_table.deleted=0 $where";

        return $this->db->query($sql);

    }



    function mark_as_read($id, $user_id) {

        $id = $this->_get_clean_value(array("id"=>$id), "id");

        $user_id = $this->_get_clean_value(array("user_id"=>$user_id), "user_id");



        $segmentation_table = $this->db->prefixTable('segmentation');

        $sql = "UPDATE $segmentation_table SET $segmentation_table.read_by = CONCAT($segmentation_table.read_by,',',$user_id)

        WHERE $segmentation_table.id=$id AND FIND_IN_SET($user_id,$segmentation_table.read_by) = 0";

        return $this->db->query($sql);

    }



    function get_last_announcement($options = array()) {

        $segmentation_table = $this->db->prefixTable('segmentation');



        $where = "";

        $client_group_ids = $this->_get_clean_value($options, "client_group_ids");

        $user_type = $this->_get_clean_value($options, "user_type");

        $where .= $this->prepare_share_with_query($segmentation_table, $user_type, $client_group_ids);



        $sql = "SELECT $segmentation_table.id, $segmentation_table.title

        FROM $segmentation_table

        WHERE $segmentation_table.deleted=0 $where

        ORDER BY $segmentation_table.id DESC

        LIMIT 1";

        return $this->db->query($sql)->getRow();

    }



}

