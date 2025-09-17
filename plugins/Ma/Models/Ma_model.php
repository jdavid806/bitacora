<?php

namespace Ma\Models;

use Twilio\Rest\Client;
use Clickatell\ClickatellException;
use Ma\Helpers\Rest;
use App\Models\Crud_model;
use Config\Services;


/**
 * Marketing Automation model
 */
class Ma_model extends Crud_model {
    protected $table = null;
    protected $db_builder = null;

    function __construct() {
        $this->table = 'demo_settings';
        parent::__construct($this->table);
    }

    /**
     * Add new category
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_category($data)
    {   
        $db_builder = $this->db->table(db_prefix() . 'ma_categories');
        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update new category
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_category($data, $id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_categories');
        $data['description'] = nl2br($data['description']);
        $db_builder->where('id', $id);
        $db_builder->update($data);
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete category from database, if used return array with key referenced
     */
    public function delete_category($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_categories');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get category
     * @param  mixed $id category id (Optional)
     * @return mixed     object or array
     */
    public function get_category($id = '', $type = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_categories');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            return $db_builder->get()->getRow();
        }

        if ($type != '') {
            $db_builder->where('type', $type);
        }

        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new stage
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_stage($data)
    {
        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update new stage
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_stage($data, $id)
    {
        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        $db_builder->where('id', $id);
        $db_builder->update($data);
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete stage from database
     */
    public function delete_stage($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
            $db_builder->where('stage_id', $id);
            $db_builder->delete();

            return true;
        }

        return false;
    }

    /**
     * Get stage
     * @param  mixed $id stage id (Optional)
     * @return mixed     object or array
     */
    public function get_stage($id = '', $where = [], $count = false, $is_kanban = false, $page = 1)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $stage = $db_builder->get()->getRow();

            return $stage;
        }

        $db_builder->where($where);

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * 10);
                $db_builder->limit(10, $position);
            } else {
                $db_builder->limit(10);
            }
        }

        if($is_kanban == false){
            $db_builder->where('published', 1);
        }
        $db_builder->orderBy('name', 'asc');

        if($count == true){
            return $db_builder->countAllResults();
        }else{
            return $db_builder->get()->getResultArray();
        }
    }

    /**
     * Add new segment
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_segment($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['type'])) {
            $type = $data['type'];
            unset($data['type']);
        }

        if (isset($data['sub_type_1'])) {
            $sub_type_1 = $data['sub_type_1'];
            unset($data['sub_type_1']);
        }

        if (isset($data['sub_type_2'])) {
            $sub_type_2 = $data['sub_type_2'];
            unset($data['sub_type_2']);
        }

        if (isset($data['value'])) {
            $value = $data['value'];
            unset($data['value']);
        }

        $data['description'] = nl2br($data['description']);
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');

        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            if($type){
                foreach($type as $k => $t){
                    $node = [];
                    $node['segment_id'] = $insert_id;
                    $node['type'] = $t;
                    $node['sub_type_1'] = $sub_type_1[$k];
                    $node['sub_type_2'] = $sub_type_2[$k];
                    $node['value'] = $value[$k];

                    $db_builder = $this->db->table(db_prefix() . 'ma_segment_filters');
                    $db_builder->insert($node);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get segment
     * @param  mixed $id segment id (Optional)
     * @return mixed     object or array
     */
    public function get_segment($id = '', $where = [], $count = false, $is_kanban = false, $page = 1)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $segment = $db_builder->get()->getRow();

            if($segment){
                $db_builder = $this->db->table(db_prefix() . 'ma_segment_filters');
                $db_builder->where('segment_id', $id);
                $segment->filters = $db_builder->get()->getResultArray();
            }

            return $segment;
        }

        $db_builder->where($where);

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * 10);
                $db_builder->limit(10, $position);
            } else {
                $db_builder->limit(10);
            }
        }

        if($is_kanban == false){
            $db_builder->where('published', 1);
        }
        $db_builder->orderBy('name', 'asc');

        if($count == true){
            return $db_builder->countAllResults();
        }else{
            return $db_builder->get()->getResultArray();
        }
    }

    /**
     * Add new segment
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_segment($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (isset($data['type'])) {
            $type = $data['type'];
            unset($data['type']);
        }

        if (isset($data['sub_type_1'])) {
            $sub_type_1 = $data['sub_type_1'];
            unset($data['sub_type_1']);
        }

        if (isset($data['sub_type_2'])) {
            $sub_type_2 = $data['sub_type_2'];
            unset($data['sub_type_2']);
        }

        if (isset($data['value'])) {
            $value = $data['value'];
            unset($data['value']);
        }

        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        $db_builder = $this->db->table(db_prefix() . 'ma_segment_filters');
        $db_builder->where('segment_id', $id);
        $db_builder->delete();

        if($type){
            foreach($type as $k => $t){
                $node = [];
                $node['segment_id'] = $id;
                $node['type'] = $t;
                $node['sub_type_1'] = $sub_type_1[$k];
                $node['sub_type_2'] = $sub_type_2[$k];
                $node['value'] = $value[$k];

                $db_builder = $this->db->table(db_prefix() . 'ma_segment_filters');
                $db_builder->insert($node);
            }
        }

        return true;
    }

    /**
     * delete segment
     * @param  integer ID
     * @return mixed
     */
    public function delete_segment($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            $db_builder = $this->db->table(db_prefix() . 'ma_segment_filters');
            $db_builder->where('segment_id', $id);
            $db_builder->delete();

            $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
            $db_builder->where('segment_id', $id);
            $db_builder->delete();

            return true;
        }

        return false;
    }

    /**
     * add form
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_form($data)
    {
        $data                       = $this->_do_lead_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);
        $data['form_key']           = app_generate_hash();

        $data['create_task_on_duplicate'] = (int) isset($data['create_task_on_duplicate']);
        $data['mark_public']              = (int) isset($data['mark_public']);

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate']           = 1;
            $data['track_duplicate_field']     = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate']  = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        $data['dateadded'] = date('Y-m-d H:i:s');

        $db_builder = $this->db->table(db_prefix() . 'ma_forms');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {

            return $insert_id;
        }

        return false;
    }

    /**
     * update form
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_form($id, $data)
    {
        $data                       = $this->_do_lead_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);

        $data['create_task_on_duplicate'] = (int) isset($data['create_task_on_duplicate']);
        $data['mark_public']              = (int) isset($data['mark_public']);

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate']           = 1;
            $data['track_duplicate_field']     = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate']  = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        $db_builder = $this->db->table(db_prefix() . 'ma_forms');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        return ($this->db->affectedRows() > 0 ? true : false);
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete stage from database
     */
    public function delete_form($id)
    {   
        $affected_rows = 0;
        $db_builder = $this->db->table(db_prefix() . 'ma_forms');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            $affected_rows++;
        }

        $db_builder = $this->db->table(db_prefix() . 'clients');
        $db_builder->where('from_ma_form_id', $id);
        $db_builder->update([
            'from_ma_form_id' => 0,
        ]);

        if ($this->db->affectedRows() > 0) {
            $affected_rows++;
        }

        if ($affected_rows > 0) {
            return true;
        }

        return false;
    }

    /**
     *  do lead form responsibles
     * @param  array
     * @return array
     */
    private function _do_lead_form_responsibles($data)
    {
        if (isset($data['notify_lead_imported'])) {
            $data['notify_lead_imported'] = 1;
        } else {
            $data['notify_lead_imported'] = 0;
        }

        if ($data['responsible'] == '') {
            $data['responsible'] = 0;
        }
        if ($data['notify_lead_imported'] != 0) {
            if ($data['notify_type'] == 'specific_staff') {
                if (isset($data['notify_ids_staff'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_staff']);
                    unset($data['notify_ids_staff']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_staff']);
                }
                if (isset($data['notify_ids_roles'])) {
                    unset($data['notify_ids_roles']);
                }
            } else {
                if (isset($data['notify_ids_roles'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_roles']);
                    unset($data['notify_ids_roles']);
                } else {
                    $data['notify_ids'] = serialize([]);
                    unset($data['notify_ids_roles']);
                }
                if (isset($data['notify_ids_staff'])) {
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
            $data['notify_ids']  = serialize([]);
            $data['notify_type'] = null;
            if (isset($data['notify_ids_staff'])) {
                unset($data['notify_ids_staff']);
            }
            if (isset($data['notify_ids_roles'])) {
                unset($data['notify_ids_roles']);
            }
        }

        return $data;
    }

    /**
     * get form
     * @param  array or String
     * @return object
     */
    public function get_form($where)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_forms');
        $db_builder->where($where);

        return $db_builder->get()->getRow();
    }

    /**
     * get forms
     * @param  array
     * @return array
     */
    public function get_forms($where = [])
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_forms');
        $db_builder->where($where);

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new asset
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_asset($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if(isset($data['file_names'])){
            unset($data['file_names']);
        }
        if(isset($data['file_sizes'])){
            unset($data['file_sizes']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);

        $db_builder = $this->db->table(db_prefix() . 'ma_assets');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get asset
     * @param  mixed $id asset id (Optional)
     * @return mixed     object or array
     */
    public function get_asset($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_assets');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $asset = $db_builder->get()->getRow();

            if($asset){
                $asset->attachment            = '';
                $asset->filetype              = '';
                $asset->attachment_added_from = 0;
            }

            return $asset;
        }
        $db_builder->where('published', 1);
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new asset
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_asset($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if(isset($data['file_names'])){
            unset($data['file_names']);
        }
        if(isset($data['file_sizes'])){
            unset($data['file_sizes']);
        }

        $data['description'] = nl2br($data['description']);

        $db_builder = $this->db->table(db_prefix() . 'ma_assets');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete asset from database, if used return array with key referenced
     */
    public function delete_asset($id)
    {   
        $asset = $this->get_asset($id);
        $db_builder = $this->db->table(db_prefix() . 'ma_assets');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            $db_builder = $this->db->table(db_prefix() . 'ma_asset_download_logs');
            $db_builder->where('asset_id', $id);
            $db_builder->delete();

            $target_path = get_setting("ma_asset_file_path");
            $files = unserialize($asset->files);
            foreach ($files as $file) {
                delete_app_files($target_path, array($file));
            }

            return true;
        }

        return false;
    }

    /**
     * Add new point_action
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_point_action($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (!isset($data['add_points_by_country'])) {
            $data['add_points_by_country'] = 0;
        }

        if (isset($data['country'])) {
            $country = $data['country'];
            unset($data['country']);
        }

        if (isset($data['list_change_points'])) {
            $list_change_points = $data['list_change_points'];
            unset($data['list_change_points']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);

        $db_builder = $this->db->table(db_prefix() . 'ma_point_actions');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {

            foreach ($country as $key => $value) {
                if($value != ''){
                    $db_builder = $this->db->table(db_prefix() . 'ma_point_action_details');
                    $db_builder->insert([
                        'point_action_id' => $insert_id,
                        'country' => $value,
                        'change_points' => $list_change_points[$key],
                    ]);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get point_action
     * @param  mixed $id point_action id (Optional)
     * @return mixed     object or array
     */
    public function get_point_action($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_actions');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $point_action = $db_builder->get()->getRow();

            if($point_action){
                $db_builder = $this->db->table(db_prefix() . 'ma_point_action_details');
                $db_builder->where('point_action_id', $id);
                $point_action->change_point_details = $db_builder->get()->getResultArray();
            }

            return $point_action;
        }

        $db_builder->where('published', 1);
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new point_action
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_point_action($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if (!isset($data['add_points_by_country'])) {
            $data['add_points_by_country'] = 0;
        }

        if (isset($data['country'])) {
            $country = $data['country'];
            unset($data['country']);
        }

        if (isset($data['list_change_points'])) {
            $list_change_points = $data['list_change_points'];
            unset($data['list_change_points']);
        }

        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_point_actions');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        $db_builder = $this->db->table(db_prefix() . 'ma_point_action_details');
        $db_builder->where('point_action_id', $id);
        $db_builder->delete();

        foreach ($country as $key => $value) {
            if($value != ''){
                $db_builder = $this->db->table(db_prefix() . 'ma_point_action_details');
                $db_builder->insert([
                    'point_action_id' => $id,
                    'country' => $value,
                    'change_points' => $list_change_points[$key],
                ]);
            }
        }

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete point_action from database, if used return array with key referenced
     */
    public function delete_point_action($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_actions');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add new point_trigger
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_point_trigger($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_point_triggers');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get point_trigger
     * @param  mixed $id point_trigger id (Optional)
     * @return mixed     object or array
     */
    public function get_point_trigger($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_triggers');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $point_trigger = $db_builder->get()->getRow();

            return $point_trigger;
        }
        
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new point_trigger
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_point_trigger($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_point_triggers');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete point_trigger from database, if used return array with key referenced
     */
    public function delete_point_trigger($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_triggers');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add new marketing_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_marketing_message($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_marketing_messages');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get marketing_message
     * @param  mixed $id marketing_message id (Optional)
     * @return mixed     object or array
     */
    public function get_marketing_message($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_marketing_messages');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $marketing_message = $db_builder->get()->getRow();

            return $marketing_message;
        }
        
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new marketing_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_marketing_message($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_marketing_messages');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete marketing_message from database, if used return array with key referenced
     */
    public function delete_marketing_message($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_marketing_messages');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Add new email
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_email($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_emails');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            if ($data['email_template'] != '') {
                $email_template = $this->get_email_template($data['email_template']);
                foreach($email_template->data_design as $design){
                    $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
                    $db_builder->insert([
                        'email_id' => $insert_id,
                        'country' => $design['country'],
                        'data_design' => $design['data_design'],
                        'data_html' => $design['data_html'],
                    ]);
                }
            }

            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get email
     * @param  mixed $id email id (Optional)
     * @return mixed     object or array
     */
    public function get_email($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_emails');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $email = $db_builder->get()->getRow();

            if($email){
                $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
                $db_builder->where('email_id', $id);
                $email->data_design = $db_builder->get()->getResultArray();
            }

            return $email;
        }
        
        $db_builder->where('published', 1);
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new email
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_email($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if ($data['email_template'] != '') {
            $email = $this->get_email($id);
        }


        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_emails');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 

            if ($data['email_template'] != '') {
                if($email->email_template != $data['email_template']){
                    $this->db->where('email_id', $id);
                    $this->db->delete(db_prefix() . 'ma_email_designs');

                    $email_template = $this->get_email_template($data['email_template']);
                    foreach($email_template->data_design as $design){
                        $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
                        $db_builder->insert([
                            'email_id' => $id,
                            'country' => $design['country'],
                            'data_design' => $design['data_design'],
                            'data_html' => $design['data_html'],
                        ]);
                    }
                }
            }

            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete email from database
     */
    public function delete_email($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_emails');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
            $db_builder->where('email_id', $id);
            $db_builder->delete();

            return true;
        }

        return false;
    }

    /**
     * Add new text_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_text_message($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $db_builder = $this->db->table(db_prefix() . 'ma_text_messages');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get text_message
     * @param  mixed $id text_message id (Optional)
     * @return mixed     object or array
     */
    public function get_text_message($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_text_messages');
        if (is_numeric($id)) {

            $db_builder->where('id', $id);
            $text_message = $db_builder->get()->getRow();

            return $text_message;
        }
        
        $db_builder->where('published', 1);
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new text_message
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_text_message($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $db_builder = $this->db->table(db_prefix() . 'ma_text_messages');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete text_message from database
     */
    public function delete_text_message($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_text_messages');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Change segment published
     * @param  mixed $id     segment id
     * @param  mixed $status status(0/1)
     */
    public function change_segment_published($id, $status)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        $db_builder->where('id', $id);
        $db_builder->update([
            'published' => $status,
        ]);

        if ($this->db->affectedRows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_data_segment_pie_chart()
    {
        $request = Services::request();
        $where = '';

        $categories = $this->get_category('', 'segment');
        $categoryIds = [];

        $where = '';
        foreach ($categories as $category) {
            if ($request->getPost('segment_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $db_builder = $this->db->table(db_prefix() . 'ma_segments');
            $db_builder->where('category', $category['id']);
            $segment = $db_builder->countAllResults();

            $data_chart[] = ['name' => $category['name'], 'y' => $segment, 'color' => $category['color']];
        }

        return $data_chart;
    }

    /**
     * @return array
     */
    public function get_data_segment_column_chart()
    {
        $request = Services::request();
        $categoryIds = [];

        $categories = $this->get_category('', 'segment');
        $categoryIds = [];
        $where = '';
        foreach ($categories as $category) {
            if ($request->getPost('segment_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        if (count($categoryIds) > 0) {
            $where = 'category IN (' . implode(', ', $categoryIds) . ')';
        }

        $header = [];
        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $header[] = $category['name'];

            $db_builder = $this->db->table(db_prefix() . 'ma_segments');
            $db_builder->where('category', $category['id']);
            $segment = $db_builder->countAllResults();

            $data_chart[] = ['name' => $category['name'], 'y' => $segment, 'color' => $category['color']];
        }

        return ['header' => $header, 'data' => $data_chart];
    }

    /**
     * Does a segment kanban query.
     *
     * @param      int   $staff_id  The staff identifier
     * @param      integer  $page      The page
     * @param      array    $where     The where
     * @param      boolean  $count     The count
     *
     * @return     object
     */
    public function do_segment_kanban_query($category, $page = 1, $where = [], $count = false)
    {
        return $this->get_segment('', $where, $count, true, $page);
    }

    /**
     * update segment category
     *
     * @param      object  $data   The data
     */
    public function update_segment_category($data)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        $db_builder->where('id', $data['segment_id']);
        $db_builder->update(['category' => $data['category']]);
    }

    /**
     * Add new campaign
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_campaign($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Add new campaign
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_campaign($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);

        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get campaign
     * @param  mixed $id campaign id (Optional)
     * @return mixed     object or array
     */
    public function get_campaign($id = '', $where = [], $count = false, $is_kanban = false, $page = 1)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $campaign = $db_builder->get()->getRow();

            return $campaign;
        }

        $db_builder->where($where);

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * 10);
                $db_builder->limit(10, $position);
            } else {
                $db_builder->limit(10);
            }
        }

        if($is_kanban == false){
            $db_builder->where('published', 1);
        }
        $db_builder->orderBy('name', 'asc');

        if($count == true){
            return $db_builder->countAllResults();
        }else{
            return $db_builder->get()->getResultArray();
        }
    }
    /**
     * @param  array
     * @return boolean
     */
    public function workflow_builder_save($data){
        if(isset($data['campaign_id']) && $data['campaign_id'] != ''){
            $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
            $db_builder->where('id', $data['campaign_id']);
            $db_builder->update(['workflow' => json_encode($data['workflow'])]);

            if ($this->db->affectedRows() > 0) {
                return true;
            }
        }

        return false;
    }


    /**
     * Change campaign published
     * @param  mixed $id     campaign id
     * @param  mixed $status status(0/1)
     */
    public function change_campaign_published($id, $status)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('id', $id);
        $db_builder->update([
            'published' => $status,
        ]);

        if ($this->db->affectedRows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_data_campaign_pie_chart()
    {
        $request = Services::request();
        $where = '';

        $categories = $this->get_category('', 'campaign');
        $categoryIds = [];

        $where = '';
        foreach ($categories as $category) {
            if ($request->getPost('campaign_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
            $db_builder->where('category', $category['id']);
            $campaign = $db_builder->countAllResults();

            $data_chart[] = ['name' => $category['name'], 'y' => $campaign, 'color' => $category['color']];
        }

        return $data_chart;
    }

    /**
     * @return array
     */
    public function get_data_campaign_column_chart()
    {
        $request = Services::request();
        $categoryIds = [];

        $categories = $this->get_category('', 'campaign');
        $categoryIds = [];
        $where = '';
        foreach ($categories as $category) {
            if ($request->getPost('campaign_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        if (count($categoryIds) > 0) {
            $where = 'category IN (' . implode(', ', $categoryIds) . ')';
        }

        $header = [];
        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $header[] = $category['name'];

            $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
            $db_builder->where('category', $category['id']);
            $campaign = $db_builder->countAllResults();

            $data_chart[] = ['name' => $category['name'], 'y' => $campaign, 'color' => $category['color']];
        }

        return ['header' => $header, 'data' => $data_chart];
    }

    /**
     * Does a campaign kanban query.
     *
     * @param      int   $staff_id  The staff identifier
     * @param      integer  $page      The page
     * @param      array    $where     The where
     * @param      boolean  $count     The count
     *
     * @return     object
     */
    public function do_campaign_kanban_query($category, $page = 1, $where = [], $count = false)
    {
        return $this->get_campaign('', $where, $count, true, $page);
    }

    /**
     * update campaign category
     *
     * @param      object  $data   The data
     */
    public function update_campaign_category($data)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('id', $data['campaign_id']);
        $db_builder->update(['category' => $data['category']]);
    }

    /**
     * Change stage published
     * @param  mixed $id     stage id
     * @param  mixed $status status(0/1)
     */
    public function change_stage_published($id, $status)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        $db_builder->where('id', $id);
        $db_builder->update([
            'published' => $status,
        ]);

        if ($this->db->affectedRows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function get_data_stage_pie_chart()
    {
        $request = Services::request();
        $where = '';

        $categories = $this->get_category('', 'stage');
        $categoryIds = [];

        $where = '';
        foreach ($categories as $category) {
            if ($request->getPost('stage_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $db_builder = $this->db->table(db_prefix() . 'ma_stages');
            $db_builder->where('category', $category['id']);
            $stage = $db_builder->countAllResults();

            $data_chart[] = ['name' => $category['name'], 'y' => $stage, 'color' => $category['color']];
        }

        return $data_chart;
    }

    /**
     * @return array
     */
    public function get_data_stage_column_chart()
    {
        $request = Services::request();
        $categoryIds = [];

        $categories = $this->get_category('', 'stage');
        $categoryIds = [];
        $where = '';
        foreach ($categories as $category) {
            if ($request->getPost('stage_category_' . $category['id'])) {
                array_push($categoryIds, $category['id']);
            }
        }

        if (count($categoryIds) > 0) {
            $where = 'category IN (' . implode(', ', $categoryIds) . ')';
        }

        $header = [];
        $data_chart = [];
        foreach($categories as $category){
            if (count($categoryIds) > 0 && !in_array($category['id'], $categoryIds)) {
                continue;
            }

            $header[] = $category['name'];

            $db_builder = $this->db->table(db_prefix() . 'ma_stages');
            $db_builder->where('category', $category['id']);
            $stage = $db_builder->countAllResults();

            $data_chart[] = ['name' => $category['name'], 'y' => $stage, 'color' => $category['color']];
        }

        return ['header' => $header, 'data' => $data_chart];
    }

    /**
     * Does a stage kanban query.
     *
     * @param      int   $staff_id  The staff identifier
     * @param      integer  $page      The page
     * @param      array    $where     The where
     * @param      boolean  $count     The count
     *
     * @return     object
     */
    public function do_stage_kanban_query($category, $page = 1, $where = [], $count = false)
    {
        return $this->get_stage('', $where, $count, true, $page);
    }

    /**
     * update stage category
     *
     * @param      object  $data   The data
     */
    public function update_stage_category($data)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        $db_builder->where('id', $data['stage_id']);
        $db_builder->update(['category' => $data['category']]);
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete category from database
     */
    public function delete_campaign($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * email template design save
     * @param  array
     * @return boolean
     */
    public function email_template_design_save($data){
        if(isset($data['id']) && $data['id'] != ''){
            $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');
            $db_builder->where('id', $data['id']);
            $db_builder->update(['data_html' => json_encode($data['data_html']), 'data_design' => json_encode($data['data_design'])]);

            if ($this->db->affectedRows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add new email_template
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_email_template($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        
        $db_builder = $this->db->table(db_prefix() . 'ma_email_templates');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get email_template
     * @param  mixed $id email_template id (Optional)
     * @return mixed     object or array
     */
    public function get_email_template($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_templates');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $email_template = $db_builder->get()->getRow();

            if($email_template){
                $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');
                $db_builder->where('email_template_id', $id);
                $email_template->data_design = $db_builder->get()->getResultArray();
            }

            return $email_template;
        }
        
        $db_builder->where('published', 1);
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new email_template
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_email_template($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        $data['description'] = nl2br($data['description']);
        $db_builder = $this->db->table(db_prefix() . 'ma_email_templates');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete email template from database
     */
    public function delete_email_template($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_templates');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  string
     * @return array or boolean
     */
    public function get_lead_by_segment($id, $return_type = 'leads'){
        $segment = $this->get_segment($id);

        $where = '';
        if($segment){
            foreach ($segment->filters as $filter) {
                if($where != ''){
                    $where .= ' '. strtoupper($filter['sub_type_1']).' ';
                }

                $column = $filter['type'];
                if($filter['type'] == 'name'){
                    $column = 'company_name';
                }
                if($filter['type'] == 'specialty'){
                    $column = 'especialidad';
                }
                if($filter['type'] == 'users_number'){
                    $column = 'cantidad_usuarios';
                }

                switch ($filter['sub_type_2']) {
                    case 'equals':
                        $where .= db_prefix().'clients.'.$column.' = "'.$filter['value'].'"';
                        break;
                    case 'not_equal':
                        $where .= db_prefix().'clients.'.$column.' != "'.$filter['value'].'"';
                        break;
                    case 'greater_than':
                        $where .= db_prefix().'clients.'.$column.' > "'.$filter['value'].'"';
                        break;
                    case 'greater_than_or_equal':
                        $where .= db_prefix().'clients.'.$column.' >= "'.$filter['value'].'"';
                        break;
                    case 'less_than':
                        $where .= db_prefix().'clients.'.$column.' < "'.$filter['value'].'"';
                        break;
                    case 'less_than_or_equal':
                        $where .= db_prefix().'clients.'.$column.' <= "'.$filter['value'].'"';
                        break;
                    case 'between':
                        $values = explode(",", $filter['value']);
                        $min = $values[0];
                        $max = $values[1];
                        $where .= db_prefix().'clients.'.$column.' BETWEEN "'.$min.'" AND "'.$max.'"';
                        break;
                    case 'empty':
                        $where .= db_prefix().'clients.'.$column.' = ""';
                        break;
                    case 'not_empty':
                        $where .= db_prefix().'clients.'.$column.' != ""';
                        break;
                    case 'like':
                        $where .= db_prefix().'clients.'.$column.' LIKE "%'.$filter['value'].'%"';
                        break;
                    case 'not_like':
                        $where .= db_prefix().'clients.'.$column.' NOT LIKE "%'.$filter['value'].'%"';
                        break;
                    default:
                        break;
                }
            }
        }

        $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
        $db_builder->where('segment_id', $id);
        $db_builder->where('deleted', 0);
        $lead_segments = $db_builder->get()->getResultArray();
        $where_lead_segment = '';
        foreach ($lead_segments as $value) {
            if($where_lead_segment != ''){
              $where_lead_segment .= ','.$value['lead_id'];
            }else{
              $where_lead_segment .= $value['lead_id'];
            }
        }

        if($where_lead_segment != ''){
            $where .= ' OR '.db_prefix().'clients.id in ('.$where_lead_segment.')';
        }

        if($where != ''){
          $where = '('.$where.' and '.db_prefix().'clients.is_lead = 1 and '.db_prefix().'clients.deleted = 0 and ma_unsubscribed = 0)';
        }else{
          $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_segment($id){
        $where = 'workflow LIKE \'%\\\\\\\\"segment\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';
        
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $campaigns = $db_builder->get()->getResultArray();

        $db_builder->where('end_date <= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $old_campaigns = $db_builder->get()->getResultArray();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @param  string
     * @return array or string
     */
    public function get_lead_by_campaign($id, $return_type = 'leads'){
        $campaign = $this->get_campaign($id);
        $where = '';

        if($campaign->workflow != ''){
            $workflow = json_decode(json_decode($campaign->workflow), true);

            foreach($workflow['drawflow']['Home']['data'] as $data){
                if($data['class'] == 'flow_start'){
                    if(!isset($data['data']['data_type']) || $data['data']['data_type'] == 'lead'){
                        if(!isset($data['data']['lead_data_from']) || $data['data']['lead_data_from'] == 'segment'){
                            if(isset($data['data']['segment'])){
                                $where = $this->get_lead_by_segment($data['data']['segment'], 'where');
                            }
                        }else{
                            if(isset($data['data']['form'])){
                                $where .= 'from_ma_form_id = '.$data['data']['form'];
                            }
                        }
                    }
                }
            }
        }   

        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_lead_exceptions');
        $db_builder->where('campaign_id', $id);
        $lead_exception = $db_builder->get()->getResultArray();
        $lead_exception_where = '';

        foreach($lead_exception as $lead){
            if($lead_exception_where == ''){
                $lead_exception_where = $lead['lead_id'];
            }else{
                $lead_exception_where .= ','.$lead['lead_id'];
            }
        }

        if($lead_exception_where != ''){
            if($where != ''){
                $where .= ' AND '.db_prefix().'clients.id not in ('.$lead_exception_where.')';
            }else{
                $where .= db_prefix().'clients.id not in ('.$lead_exception_where.')';
            }
        }

        if($where == ''){
            $where = '1=0';
        }else{
            $where .= ' AND '.db_prefix().'clients.deleted = 0 AND ma_unsubscribed = 0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');

            $db_builder->select('*, '.db_prefix() . 'clients.id as id, '.db_prefix() . 'clients.company_name as name, '.db_prefix() . 'custom_field_values.value as email');
            $db_builder->join(get_db_prefix() . 'custom_fields', get_db_prefix() . 'custom_fields.related_to = "leads" AND ' . get_db_prefix() . 'custom_fields.field_type = "email"', 'left');
            $db_builder->join(get_db_prefix() . 'custom_field_values', get_db_prefix() . 'custom_fields.id = ' . get_db_prefix() . 'custom_field_values.custom_field_id AND ' . get_db_prefix() . 'clients.id = ' . get_db_prefix() . 'custom_field_values.related_to_id AND ' . get_db_prefix() . 'custom_field_values.related_to_type = "leads"', 'left');

            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return boolean
     */
    public function run_campaigns($id){
        $campaign = $this->get_campaign($id);
        $workflow = json_decode(json_decode($campaign->workflow), true);

        $workflow = $workflow['drawflow']['Home']['data'];
        $data_flow = [];

        
        $data = [];
        $data['campaign'] = $campaign;
        $data['workflow'] = $workflow;
        $leads = $this->get_lead_by_campaign($id);
        foreach($leads as $lead){
            $data['lead'] = $lead;
            $data['contact'] = $lead;
            foreach($workflow as $data_workflow){
                $data['node'] = $data_workflow;

                if($data_workflow['class'] == 'flow_start'){
                    if(!$this->check_workflow_node_log($data)){
                        $this->save_workflow_node_log($data);
                    }

                    foreach ($data_workflow['outputs']['output_1']['connections'] as $connection) {
                        $data['node'] = $workflow[$connection['node']];
                        $this->run_workflow_node($data);
                    }
                }
            }
        }

        $clients = $this->get_client_by_campaign($id);
        foreach($clients as $client){
            $data['client'] = $client;
            $data['contact'] = $client;
            foreach($workflow as $data_workflow){
                $data['node'] = $data_workflow;

                if($data_workflow['class'] == 'flow_start'){
                    if(!$this->check_workflow_node_log($data)){
                        $this->save_workflow_node_log($data);
                    }

                    foreach ($data_workflow['outputs']['output_1']['connections'] as $connection) {
                        $data['node'] = $workflow[$connection['node']];
                        $this->run_workflow_node($data);
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function run_workflow_node($data){
        $output = $this->check_workflow_node_log($data);
        highlight_string("<?php\n" . var_export($data['node']['id'].' - '.$data['node']['class'], true) . ";\n?>");

        if(!$output){
            switch ($data['node']['class']) {
                case 'email':
                    $success = $this->handle_email_node($data);

                    if($success){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'sms':
                    $success = $this->handle_sms_node($data);

                    if($success){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'action':
                    $success = $this->handle_action_node($data);

                    if($success){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'condition':
                    $success = $this->handle_condition_node($data);
                    if($success == 'output_1'){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }

                    }elseif($success == 'output_2'){
                        $this->save_workflow_node_log($data, 'output_2');

                        foreach ($data['node']['outputs']['output_2']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }

                    break;

                case 'filter':
                    $success = $this->handle_filter_node($data);
                    if($success == 'output_1'){
                        $this->save_workflow_node_log($data);

                        foreach ($data['node']['outputs']['output_1']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }

                    }elseif($success == 'output_2'){
                        $this->save_workflow_node_log($data, 'output_2');

                        foreach ($data['node']['outputs']['output_2']['connections'] as $connection) {
                            $data['node'] = $data['workflow'][$connection['node']];
                            $this->run_workflow_node($data);
                        }
                    }
                    break;

                default:
                    // code...
                    break;
            }
        }else{
            foreach ($data['node']['outputs'][$output]['connections'] as $connection) {
                $data['node'] = $data['workflow'][$connection['node']];
                $this->run_workflow_node($data);
            }
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_email_node($data){
        highlight_string("<?php\n" . var_export($data['contact']['email'], true) . ";\n?>");

        if(isset($data['node']['data']['email']) && $data['contact']['email'] != ''){
            if(!isset($data['node']['data']['complete_action'])){
                $data['node']['data']['complete_action'] = 'right_away';
            }

            switch ($data['node']['data']['complete_action']) {
                case 'right_away':
                    $email = $this->get_email($data['node']['data']['email']);
                    $log_id = $this->save_email_log([
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['id'] : 0), 
                        'email_id' => $email->id, 
                        'email_template_id' => $email->email_template, 
                        'campaign_id' => $data['campaign']->id
                    ]);

                    $this->ma_send_email($data['contact']['email'], $email, $data, $log_id);

                    return true;

                    break;
                case 'after':
                    if(!isset($data['node']['data']['waiting_number'])){
                        $data['node']['data']['waiting_number'] = 1;
                    }
                    
                    if(!isset($data['node']['data']['waiting_type'])){
                        $data['node']['data']['waiting_type'] = 'minutes';
                    }

                    foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
                        $db_builder->where('campaign_id', $data['campaign']->id);
                        if(isset($data['lead'])){
                            $db_builder->where('lead_id', $data['lead']['id']);
                        }else{
                            $db_builder->where('client_id', $data['client']['id']);
                        }
                        $db_builder->where('node_id', $connection['node']);
                        $logs = $db_builder->get()->getRow();

                        if($logs){
                            $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                            if(date('Y-m-d H:i:s') >= $time){
                                $email = $this->get_email($data['node']['data']['email']);
                                $log_id = $this->save_email_log([
                                    'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                                    'client_id' => (isset($data['client']) ? $data['client']['id'] : 0), 
                                    'email_id' => $email->id, 
                                    'email_template_id' => $email->email_template, 
                                    'campaign_id' => $data['campaign']->id
                                ]);

                                $this->ma_send_email($data['lead']['email'], $email, $data, $log_id);

                                return true;
                            }
                        }
                    }

                    break;
                case 'exact_time':
                    $time = date('Y-m-d H:i:s', strtotime(date('Y-m-d').' '.$data['node']['data']['exact_time']));

                    if(date('Y-m-d H:i:s') >= $time){
                        $email = $this->get_email($data['node']['data']['email']);
                        $log_id = $this->save_email_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['id'] : 0), 
                            'email_id' => $email->id, 
                            'email_template_id' => $email->email_template, 
                            'campaign_id' => $data['campaign']->id
                        ]);

                        $this->ma_send_email($data['lead']['email'], $email, $data, $log_id);

                        return true;
                    }

                    break;
                case 'exact_time_and_date':
                    $time = $data['node']['data']['exact_time_and_date'];

                    if(date('Y-m-d H:i:s') >= $time){
                        $email = $this->get_email($data['node']['data']['email']);
                        $log_id = $this->save_email_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['id'] : 0), 
                            'email_id' => $email->id, 
                            'email_template_id' => $email->email_template, 
                            'campaign_id' => $data['campaign']->id
                        ]);

                        $this->ma_send_email($data['lead']['email'], $email, $data, $log_id);

                        return true;
                    }
                    
                    break;
                
                default:
                    // code...
                    break;
            }
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_sms_node($data){
        if(isset($data['node']['data']['sms']) && $data['lead']['phone'] != ''){
            if(!isset($data['node']['data']['complete_action'])){
                $data['node']['data']['complete_action'] = 'right_away';
            }

            switch ($data['node']['data']['complete_action']) {
                case 'right_away':
                    $sms = $this->get_sms($data['node']['data']['sms']);

                    $this->sendSMS($sms->content, $data['lead']['phone'], $data['lead']);
                    $this->save_sms_log(['lead_id' => $data['lead']['id'], 'sms_id' => $sms->id, 'text_message_id' => $sms->sms_template, 'campaign_id' => $data['campaign']->id]);

                    return true;

                    break;
                case 'after':
                    if(!isset($data['node']['data']['waiting_number'])){
                        $data['node']['data']['waiting_number'] = 1;
                    }
                    
                    if(!isset($data['node']['data']['waiting_type'])){
                        $data['node']['data']['waiting_type'] = 'minutes';
                    }

                    foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
                        $db_builder->where('campaign_id', $data['campaign']->id);
                        if(isset($data['lead'])){
                            $db_builder->where('lead_id', $data['lead']['id']);
                        }else{
                            $db_builder->where('client_id', $data['client']['id']);
                        }
                        $db_builder->where('node_id', $connection['node']);
                        $logs = $db_builder->get()->getRow();

                        if($logs){
                            $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                            if(date('Y-m-d H:i:s') >= $time){
                                $sms = $this->get_sms($data['node']['data']['sms']);

                                $this->sendSMS($sms->content, $data['lead']['phone'], $data['lead']);
                                $this->save_sms_log(['lead_id' => $data['lead']['id'], 'sms_id' => $sms->id, 'text_message_id' => $sms->sms_template, 'campaign_id' => $data['campaign']->id]);

                                return true;
                            }
                        }
                    }

                    break;
                case 'exact_time':
                    $time = date('Y-m-d H:i:s', strtotime(date('Y-m-d').' '.$data['node']['data']['exact_time']));

                    if(date('Y-m-d H:i:s') >= $time){
                        $sms = $this->get_sms($data['node']['data']['sms']);

                        $this->sendSMS($sms->content, $data['lead']['phone'], $data['lead']);
                        $this->save_sms_log(['lead_id' => $data['lead']['id'], 'sms_id' => $sms->id, 'text_message_id' => $sms->sms_template, 'campaign_id' => $data['campaign']->id]);

                        return true;
                    }

                    break;
                case 'exact_time_and_date':
                    $time = $data['node']['data']['exact_time_and_date'];

                    if(date('Y-m-d H:i:s') >= $time){
                        $sms = $this->get_sms($data['node']['data']['sms']);

                        $this->sendSMS($sms->content, $data['lead']['phone'], $data['lead']);
                        $this->save_sms_log(['lead_id' => $data['lead']['id'], 'sms_id' => $sms->id, 'text_message_id' => $sms->sms_template, 'campaign_id' => $data['campaign']->id]);

                        return true;
                    }
                    
                    break;
                
                default:
                    // code...
                    break;
            }
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_action_node($data){
        if(!isset($data['node']['data']['action'])){
            $data['node']['data']['action'] = 'change_segments';
        }

        switch ($data['node']['data']['action']) {
            case 'change_segments':
                if(isset($data['node']['data']['segment']) && isset($data['lead'])){
                    $this->change_segment($data['lead']['id'], $data['node']['data']['segment'], $data['campaign']->id);

                    return true;
                }

                break;
            case 'change_stages':
                if(isset($data['node']['data']['stage']) && isset($data['lead'])){
                    $this->change_stage($data['lead']['id'], $data['node']['data']['stage'], $data['campaign']->id);

                    return true;
                }

                break;
            case 'change_points':
                if(isset($data['node']['data']['point'])){
                    $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
                    $db_builder->insert([
                        'campaign_id' => $data['campaign']->id, 
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['id'] : 0),
                        'point_action_id' => 0, 
                        'point' => $data['node']['data']['point'],
                        'dateadded' => date('Y-m-d H:i:s'), 
                    ]);

                    return true;
                }

                break;

            case 'point_action':
                if(isset($data['node']['data']['point_action'])){
                    $change_points = $this->get_change_point_by_contact($data['node']['data']['point_action'], $data['contact']);

                    $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
                    $db_builder->insert([
                        'campaign_id' => $data['campaign']->id, 
                        'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                        'client_id' => (isset($data['client']) ? $data['client']['id'] : 0), 
                        'point_action_id' => $data['node']['data']['point_action'], 
                        'point' => $change_points,
                        'dateadded' => date('Y-m-d H:i:s'), 
                    ]);

                    return true;
                }

                break;

            case 'delete_lead':
                if(isset($data['lead'])){
                    $Clients_model = model('App\Models\Clients_model');
                    $Clients_model->delete_client_and_sub_items($data['lead']['id']);
                    
                    return true;
                }

                break;

            case 'remove_from_campaign':
                
                $this->remove_from_campaign($data['campaign']->id, $data['lead']['id']);

                return true;

                break;

            case 'convert_to_customer':

                if(isset($data['lead'])){
                    $this->convert_lead_to_customer($data['lead']);
                    return true;
                }
                
                break; 
            
            default:
                // code...
                break;
        }
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_filter_node($data){
        if(!isset($data['node']['data']['complete_action'])){
            $data['node']['data']['complete_action'] = 'right_away';
        }

        switch ($data['node']['data']['complete_action']) {
            case 'right_away':
                if($this->check_contact_filter($data['contact'], $data['node'])){
                    return 'output_1';
                }else{
                    return 'output_2';
                }

                break;
            case 'after':
                if(!isset($data['node']['data']['waiting_number'])){
                    $data['node']['data']['waiting_number'] = 1;
                }
                
                if(!isset($data['node']['data']['waiting_type'])){
                    $data['node']['data']['waiting_type'] = 'minutes';
                }

                foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                    $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
                    $db_builder->where('campaign_id', $data['campaign']->id);
                    if(isset($data['lead'])){
                        $db_builder->where('lead_id', $data['lead']['id']);
                    }else{
                        $db_builder->where('client_id', $data['client']['id']);
                    }
                    $db_builder->where('node_id', $connection['node']);
                    $logs = $db_builder->get()->getRow();

                    if($logs){
                        $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                        if(date('Y-m-d H:i:s') >= $time){
                            if($this->check_contact_filter($data['contact'], $data['node'])){
                                return 'output_1';
                            }else{
                                return 'output_2';
                            }
                        }
                    }
                }
            
                break;
            default:
                // code...
                break;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function handle_condition_node($data){


        foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
            $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
            $db_builder->where('campaign_id', $data['campaign']->id);
            if(isset($data['lead'])){
                $db_builder->where('lead_id', $data['lead']['id']);
            }else{
                $db_builder->where('client_id', $data['client']['id']);
            }
            $db_builder->where('node_id', $connection['node']);
            $logs = $db_builder->get()->getRow();

            if($logs){
                if(!isset($data['node']['data']['waiting_number'])){
                    $data['node']['data']['waiting_number'] = 1;
                }

                if(!isset($data['node']['data']['waiting_type'])){
                    $data['node']['data']['waiting_type'] = 'minutes';
                }

                $time = date('Y-m-d H:i:s', strtotime($logs->dateadded." +".$data['node']['data']['waiting_number']." ".$data['node']['data']['waiting_type']));

                if(date('Y-m-d H:i:s') >= $time){

                    if(!isset($data['node']['data']['track'])){
                        $data['node']['data']['track'] = 'delivery';
                    }

                    switch ($data['node']['data']['track']) {
                        case 'delivery':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'delivery')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'opens':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'open')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;

                        case 'clicks':
                            foreach ($data['node']['inputs']['input_1']['connections'] as $connection) {
                                $node = $data['workflow'][$connection['node']];
                                if($node['class'] == 'email'){
                                    if(isset($node['data']['email'])){
                                        if($this->check_condition_email($data, $node['data']['email'], 'click')){
                                            return 'output_1';
                                        }else{
                                            return 'output_2';
                                        }
                                    }
                                }
                            }
                            break;
                        
                        default:
                            
                            break;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param  array
     * @param  string
     * @return boolean
     */
    public function save_workflow_node_log($data, $output = 'output_1'){
        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
        $db_builder->where('campaign_id', $data['campaign']->id);
        if(isset($data['lead'])){
            $db_builder->where('lead_id', $data['lead']['id']);
        }else{
            $db_builder->where('client_id', $data['client']['id']);
        }
        $db_builder->where('node_id', $data['node']['id']);
        $logs = $db_builder->get()->getRow();

        if(!$logs){
            $db_builder->insert([
                'campaign_id' => $data['campaign']->id, 
                'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                'client_id' => (isset($data['client']) ? $data['client']['id'] : 0), 
                'node_id' => $data['node']['id'], 
                'output' => $output, 
                'dateadded' => date('Y-m-d H:i:s'), 
            ]);
        }

        return true;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function check_workflow_node_log($data){
        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
        $db_builder->where('campaign_id', $data['campaign']->id);
        if(isset($data['lead'])){
            $db_builder->where('lead_id', $data['lead']['id']);
        }else{
            $db_builder->where('client_id', $data['client']['id']);
        }

        $db_builder->where('node_id', $data['node']['id']);
        $logs = $db_builder->get()->getRow();

        if($logs){
            return $logs->output;
        }

        return false;
    }

    //send sms with setting
    /**
     * sendSMS
     * @param  [type] $request 
     * @return [type]          
     */
    public function sendSMS($request, $to, $lead = []) {

        $content = $this->parse_content_merge_fields($request, $lead);

        if (get_setting('sms_twilio_active') == 1) {
            return $this->twilioSms($content,$to);
        }
        else if (get_setting('sms_clickatell_active') == 1) {

            return $this->clickatellSms($content,$to);
            
        }
        else if (get_setting('sms_msg91_active') == 1) {
            return $this->msg91Sms($content,$to);
        }
    }

    /**
     * twilioSms
     * @param  [type] $request 
     * @param  [type] $to      
     * @return [type]          
     */
    public function twilioSms($mess,$to) {
    /*$request: message, to : phonenumber */

        $account_sid   = get_setting('sms_twilio_account_sid');
        $auth_token   = get_setting('sms_twilio_auth_token');
        $twilio_number   = get_setting('sms_twilio_phone_number');

        $client = new Client($account_sid, $auth_token);

        $message = $client->messages->create(
            $to,
            array(
                'from' => $twilio_number,
                'body' => $mess
            )
        );

        if ($message->sid) {
            return true;
        }
       
        return false;
    }

    /**
     * msg91Sms
     * @param  [type] $request 
     * @param  [type] $to      
     * @return [type]          
     */
    public function msg91Sms($message,$to) {

        $authKey = get_setting('sms_msg91_auth_key');
                    
        $mobileNumber = $to;

        $senderId =  get_setting('sms_msg91_sender_id');

        $message = urlencode($message);

        $route = "define";

        $postData = array(
            'authkey' => $authKey,
            'mobiles' => $mobileNumber,
            'message' => $message,
            'sender' => $senderId,
            'route' => $route
        );

        $url="http://world.msg91.com/api/sendhttp.php";

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ));

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $output = curl_exec($ch);

        if(curl_errno($ch))
        {
            echo 'error:' . curl_error($ch);
        }

        curl_close($ch);

        if ($output !== null) {
            return true;
            
        }
        return false;
    }

    /**
     * clickatellSms
     * @param  [type] $request 
     * @param  [type] $to      
     * @return [type]          
     */
    public function clickatellSms($message,$to) {
    

        $clickatell = new Rest(get_setting('sms_clickatell_api_key'));
        try {

            $result = $clickatell->sendMessage(['to' => [$to], 'content' => $message]);
  
            return true;
            
        } catch (ClickatellException $e) {

            return false;

        }
    }

    /**
     * @param  integer
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function change_segment($lead_id, $segment_id, $campaign_id){
        $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
        $db_builder->where('campaign_id', $campaign_id);
        $db_builder->where('lead_id', $lead_id);
        $db_builder->where('segment_id', $segment_id);
        $logs = $db_builder->get()->getRow();

        if(!$logs){
            $segment = $this->get_segment($segment_id);
            $segments = $this->get_segment('', 'category = '.$segment->category);

            foreach ($segments as $value) {
                $db_builder->where('segment_id', $value['id']);
                $db_builder->where('lead_id', $lead_id);
                $db_builder->update( [
                    'deleted' => 1, 
                    'date_delete' => date('Y-m-d H:i:s'), 
                ]);               
            }

            $db_builder->insert( [
                'campaign_id' => $campaign_id, 
                'lead_id' => $lead_id, 
                'segment_id' => $segment_id, 
                'dateadded' => date('Y-m-d H:i:s'), 
            ]);
        }

        return true;
    }

    /**
     * @param  integer
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function change_stage($lead_id, $stage_id, $campaign_id){
        $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
        $db_builder->where('campaign_id', $campaign_id);
        $db_builder->where('lead_id', $lead_id);
        $db_builder->where('stage_id', $stage_id);
        $logs = $db_builder->get()->getRow();

        if(!$logs){
            $stage = $this->get_stage($stage_id);
            $stages = $this->get_stage('', 'category = '.$stage->category);

            foreach ($stages as $value) {
                $db_builder->where('stage_id', $value['id']);
                $db_builder->where('lead_id', $lead_id);
                $db_builder->update([
                    'deleted' => 1, 
                    'date_delete' => date('Y-m-d H:i:s'), 
                ]);               
            }

            $db_builder->insert([
                'campaign_id' => $campaign_id, 
                'lead_id' => $lead_id, 
                'stage_id' => $stage_id, 
                'dateadded' => date('Y-m-d H:i:s'), 
            ]);
        }

        return true;
    }

    /**
     * Convert lead to client
     * @since  version 1.0.1
     * @return mixed
     */
    public function convert_lead_to_customer($lead)
    {
        $lead_status_title = '';
        $db_builder = $this->db->table(db_prefix() . 'lead_status');
        $db_builder->where('id', $lead['lead_status_id']);
        $lead_status = $db_builder->get()->getRow();

        if($lead_status){
            $lead_status_title = $lead_status->title;
        }

        $db_builder = $this->db->table(db_prefix() . 'clients');
        $db_builder->where('id', $lead['id']);
        $db_builder->update([
            'is_lead'      => 0,
            "client_migration_date" => get_current_utc_time(),
            "last_lead_status" => $lead_status_title,
        ]);
    }

    /**
     * @param  array
     * @param  array
     * @return boolean
     */
    public function check_contact_filter($contact, $node){

        if(!isset($node['data']['name_of_variable'])){
            $node['data']['name_of_variable'] = 'name';
        }
        
        if(!isset($node['data']['condition'])){
            $node['data']['condition'] = 'equals';
        }

        if(!isset($node['data']['value_of_variable'])){
            $node['data']['value_of_variable'] = '';
        }

        if($node['data']['name_of_variable'] == 'tag'){
            return false;
        }

        switch ($node['data']['condition']) {
            case 'equals':
                if($node['data']['value_of_variable'] == $contact[$node['data']['name_of_variable']]){
                    return true;
                }
                break;
            case 'not_equal':
                if($node['data']['value_of_variable'] != $contact[$node['data']['name_of_variable']]){
                    return true;
                }
                break;
            case 'greater_than':
                if($node['data']['value_of_variable'] = $contact[$node['data']['name_of_variable']]){
                    return true;
                }
                break;
            case 'greater_than_or_equal':
                if($node['data']['value_of_variable'] <= $contact[$node['data']['name_of_variable']]){
                    return true;
                }
                break;
            case 'less_than':
                if($node['data']['value_of_variable'] > $contact[$node['data']['name_of_variable']]){
                    return true;
                }
                break;
            case 'less_than_or_equal':
                if($node['data']['value_of_variable'] <= $contact[$node['data']['name_of_variable']]){
                    return true;
                }
                break;
            case 'empty':
                if($contact[$node['data']['name_of_variable']] == ''){
                    return true;
                }
                break;
            case 'not_empty':
                if($contact[$node['data']['name_of_variable']] != ''){
                    return true;
                }
                break;
            case 'like':
                if (!(strpos($contact[$node['data']['name_of_variable']], $node['data']['value_of_variable']) === false)) {
                    return true;
                }
                break;
            case 'not_like':
                if (!(strpos($contact[$node['data']['name_of_variable']], $node['data']['value_of_variable']) !== false)) {
                    return true;
                }
                break;
            default:
                break;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function save_email_log($data){
        
        $data['hash'] = app_generate_hash();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->insert($data);

        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function save_sms_log($data){
        
        $data['delivery'] = 1;
        $data['delivery_time'] = date('Y-m-d H:i:s');
        $data['dateadded'] = date('Y-m-d H:i:s');
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->insert($data);

        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * download asset
     * @param  string $hash_share
     * @return boolean
     */
    public function download_asset($asset_id, $asset_log_id = 0) {
        $browser = $this->getBrowser();

        $db_builder = $this->db->table(db_prefix() . 'ma_asset_download_logs');
        $db_builder->insert([
            'ip' => $this->get_client_ip(),
            'browser_name' => $browser['name'],
            'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'asset_id' => $asset_id,
            'asset_log_id' => $asset_log_id,
            'time' => date('Y-m-d H:i:s'),
        ]);

        $insert_id = $this->db->insertID();

        if ($insert_id) {
            return true;
        }

        return false;
    }

    /**
     * get Browser info
     * @return array
     */
    public function getBrowser() {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/coc_coc_browser/i', $u_agent)) {
            $bname = 'Cốc Cốc';
            $ub = "coc_coc_browser";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {$version = "?";}

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern,
        );
    }

    /**
     * Function to get the client IP address
     * @return string
     */
    public function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_asset_download_chart($asset_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_asset_download_logs');
        $db_builder->select('date_format(time, \'%Y-%m-%d\') as time, COUNT(*) as count_download');
        if($asset_id != ''){
            $db_builder->where('asset_id', $asset_id);
        }
        $db_builder->groupBy('date_format(time, \'%Y-%m-%d\')');
        $asset_download = $db_builder->get()->getResultArray();
        $data_asset_download = [];
        foreach($asset_download as $download){
            $data_asset_download[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_download']];
        }
        
        return $data_asset_download;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_email_template($id, $return_type = 'leads'){
        
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('lead_id');
        $db_builder->where('email_template_id', $id);
        $db_builder->groupBy('lead_id');
        $email_logs = $db_builder->get()->getResultArray();

        $where = '';
        foreach ($email_logs as $key => $value) {
            if($where != ''){
                $where .= ','. $value['lead_id'];
            }else{
                $where .= $value['lead_id'];
            }
        }

        if($where != ''){
          $where = '('.db_prefix().'clients.id in ('.$where.') and '.db_prefix().'clients.deleted = 0 AND ma_unsubscribed = 0)';
        }else{
          $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_email_template($id){
        $where = 'workflow LIKE \'%\\\\\\\\"email_template\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $campaigns = $db_builder->get()->getResultArray();

        $db_builder->where('end_date <= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $old_campaigns = $db_builder->get()->getResultArray();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_email_template_chart($email_template_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');

        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_template_id != ''){
            $db_builder->where('email_template_id', $email_template_id);
        }
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_template_id != ''){
            $db_builder->where('email_template_id', $email_template_id);
        }
        $db_builder->where('open', 1);
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_open = [];
        foreach($email_logs as $download){
            $data_open[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_template_id != ''){
            $db_builder->where('email_template_id', $email_template_id);
        }
        $db_builder->where('click', 1);
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_click = [];
        foreach($email_logs as $download){
            $data_click[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_email_template_by_campaign_chart($email_template_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('campaign_id');
        $db_builder->where('email_template_id', $email_template_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_delivery = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $db_builder->where('email_template_id', $email_template_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $count_delivery = $db_builder->countAllResults();
            $data_delivery[] = $count_delivery;

            $db_builder->where('email_template_id', $email_template_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $db_builder->where('open', 1);
            $count_open = $db_builder->countAllResults();
            $data_open[] = $count_open;

            $db_builder->where('email_template_id', $email_template_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $db_builder->where('click', 1);
            $count_click = $db_builder->countAllResults();
            $data_click[] = $count_click;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_point_action($id, $return_type = 'leads'){
        
        $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
        $db_builder->select('lead_id');
        $db_builder->where('point_action_id', $id);
        $db_builder->groupBy('lead_id');
        $point_action_logs = $db_builder->get()->getResultArray();

        $where = '';
        foreach ($point_action_logs as $key => $value) {
            if($where != ''){
                $where .= ','. $value['lead_id'];
            }else{
                $where .= $value['lead_id'];
            }
        }

        if($where != ''){
            $where = '('.db_prefix().'clients.id in ('.$where.') and '.db_prefix().'clients.deleted = 0 AND ma_unsubscribed = 0)';
        }else{
            $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_point_action_chart($point_action_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($point_action_id != ''){
            $db_builder->where('point_action_id', $point_action_id);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $point_action = $db_builder->get()->getResultArray();

        $data_point_action = [];
        foreach($point_action as $action){
            $data_point_action[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_point_action;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_point_action_by_campaign_chart($point_action_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
        $db_builder->select('campaign_id');
        $db_builder->where('point_action_id', $point_action_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_action = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
            $db_builder->where('point_action_id', $point_action_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $count_action = $db_builder->countAllResults();
            $data_action[] = $count_action;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('point_action'), 'data' => $data_action, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * Gets the where report period.
     *
     * @param      string  $field  The field
     *
     * @return     string  The where report period.
     */
    private function get_where_report_period($field = 'date')
    {
        $request = Services::request();
        $months_report      = $request->getGet('date_filter');
        
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                // Last month
                if ($months_report == '1') {
                    $beginMonth = date('Y-m-01', strtotime('first day of last month'));
                    $endMonth   = date('Y-m-t', strtotime('last day of last month'));
                } else {
                    $months_report = (int) $months_report;
                    $months_report--;
                    $beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
                    $endMonth   = date('Y-m-t');
                }

                $custom_date_select = '(' . $field . ' BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
            } elseif ($months_report == 'last_30_days') {
                $custom_date_select = '(' . $field . ' BETWEEN "' . date('Y-m-d', strtotime('today - 30 days')) . '" AND "' . date('Y-m-d') . '")';
            } elseif ($months_report == 'this_month') {
                $custom_date_select = '(' . $field . ' BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
            } elseif ($months_report == 'last_month') {
                $this_month = date('m') - 1;
                $custom_date_select = '(' . $field . ' BETWEEN "' . date("Y-m-d", strtotime("first day of previous month")) . '" AND "' . date("Y-m-d", strtotime("last day of previous month")) . '")';
            }elseif ($months_report == 'this_quarter') {
                $current_month = date('m');
                  $current_year = date('Y');
                  if($current_month>=1 && $current_month<=3)
                  {
                    $start_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // timestamp or 1-Januray 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM means end of 31 March
                  }
                  else  if($current_month>=4 && $current_month<=6)
                  {
                    $start_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM means end of 30 June
                  }
                  else  if($current_month>=7 && $current_month<=9)
                  {
                    $start_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM means end of 30 September
                  }
                  else  if($current_month>=10 && $current_month<=12)
                  {
                    $start_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-January-'.($current_year+1)));  // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
                  }
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                $start_date .
                '" AND "' .
                $end_date . '")';

            }elseif ($months_report == 'last_quarter') {
                $current_month = date('m');
                    $current_year = date('Y');

                  if($current_month>=1 && $current_month<=3)
                  {
                    $start_date = date('Y-m-d', strtotime('1-October-'.($current_year-1)));  // timestamp or 1-October Last Year 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // // timestamp or 1-January  12:00:00 AM means end of 31 December Last year
                  } 
                  else if($current_month>=4 && $current_month<=6)
                  {
                    $start_date = date('Y-m-d', strtotime('1-January-'.$current_year));  // timestamp or 1-Januray 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM means end of 31 March
                  }
                  else  if($current_month>=7 && $current_month<=9)
                  {
                    $start_date = date('Y-m-d', strtotime('1-April-'.$current_year));  // timestamp or 1-April 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM means end of 30 June
                  }
                  else  if($current_month>=10 && $current_month<=12)
                  {
                    $start_date = date('Y-m-d', strtotime('1-July-'.$current_year));  // timestamp or 1-July 12:00:00 AM
                    $end_date = date('Y-m-d', strtotime('1-October-'.$current_year));  // timestamp or 1-October 12:00:00 AM means end of 30 September
                  }
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                $start_date .
                '" AND "' .
                $end_date . '")';

            }elseif ($months_report == 'this_year') {
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date('Y-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
            } elseif ($months_report == 'last_year') {
                $custom_date_select = '(' . $field . ' BETWEEN "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
                '" AND "' .
                date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
            } elseif ($months_report == 'custom') {
                $from_date = to_sql_date($request->getPost('report_from'));
                $to_date   = to_sql_date($request->getPost('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = '' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = '(' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            } elseif(!(strpos($months_report, 'financial_year') === false)){
                $year = explode('financial_year_', $months_report);

                $custom_date_select = '(' . $field . ' BETWEEN "' . date($year[1].'-01-01') . '" AND "' . date(($year[1]).'-12-t') . '")';
            }
        }

        return $custom_date_select;
    }

    /**
     * @param  string
     * @param  array
     * @return array
     */
    public function get_data_form_submit_chart($form_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('created_date');

        $db_builder = $this->db->table(db_prefix() . 'clients');
        $db_builder->select('created_date as time, COUNT(*) as count_submit');
        $db_builder->where('from_ma_form_id != 0');
        if($form_id != ''){
            $db_builder->where('from_ma_form_id', $form_id);
        }

        if($where != ''){
            $db_builder->where($where);
        }

        $db_builder->groupBy('created_date');
        $form_submit = $db_builder->get()->getResultArray();
        $data_form_submit = [];
        foreach($form_submit as $submit){
            $data_form_submit[] = [strtotime($submit['time'].' 00:00:00') * 1000, (int)$submit['count_submit']];
        }
        
        return $data_form_submit;
    }

    /**
     * @param  array
     * @return array
     */
    public function get_data_lead_chart($data_filter = [])
    {
        $where = $this->get_where_report_period('created_date');

        $db_builder = $this->db->table(db_prefix() . 'clients');
        $db_builder->select('created_date as time, COUNT(*) as count_lead');
       
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('created_date');
        $lead_created = $db_builder->get()->getResultArray();

        $data_created = [];
        foreach($lead_created as $lead){
            $data_created[] = [strtotime($lead['time'].' 00:00:00') * 1000, (int)$lead['count_lead']];
        }

        $where = $this->get_where_report_period('client_migration_date');

        $db_builder->select('client_migration_date as time, COUNT(*) as count_lead');
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('client_migration_date');
        $lead_converted = $db_builder->get()->getResultArray();
        $data_converted = [];
        foreach($lead_converted as $lead){
            $data_converted[] = [strtotime($lead['time'].' 00:00:00') * 1000, (int)$lead['count_lead']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('created'), 'data' => $data_created, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('converted'), 'data' => $data_converted, 'color' => '#84c529'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_stage($id, $return_type = 'leads'){
        
        $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
        $db_builder->select('lead_id');
        $db_builder->where('stage_id', $id);
        $db_builder->where('deleted', 0);
        $db_builder->groupBy('lead_id');
        $lead_stages = $db_builder->get()->getResultArray();

        $where = '';
        foreach ($lead_stages as $key => $value) {
            if($where != ''){
                $where .= ','. $value['lead_id'];
            }else{
                $where .= $value['lead_id'];
            }
        }

        if($where != ''){
          $where = '('.db_prefix().'clients.id in ('.$where.') and '.db_prefix().'clients.deleted = 0 AND ma_unsubscribed = 0)';
        }else{
          $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_stage($id){
        $where = 'workflow LIKE \'%\\\\\\\\"stage\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $campaigns = $db_builder->get()->getResultArray();

        $db_builder->where('end_date <= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $old_campaigns = $db_builder->get()->getResultArray();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_segment_detail_chart($segment_id){

        $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $db_builder->where('segment_id', $segment_id);
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $db_builder->where('segment_id', $segment_id);
        $db_builder->where('deleted', 1);
        $db_builder->groupBy('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_segment_by_campaign_chart($segment_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
        $db_builder->select('campaign_id');
        $db_builder->where('segment_id', $segment_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_lead = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $db_builder->where('segment_id', $segment_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $count_lead = $db_builder->countAllResults();
            $data_lead[] = $count_lead;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('lead'), 'data' => $data_lead, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_stage_detail_chart($stage_id){

        $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $db_builder->where('stage_id', $stage_id);
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        $db_builder->where('stage_id', $stage_id);
        $db_builder->where('deleted', 1);
        $db_builder->groupBy('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_stage_by_campaign_chart($stage_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
        $db_builder->select('campaign_id');
        $db_builder->where('stage_id', $stage_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_lead = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $db_builder->where('stage_id', $stage_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $count_lead = $db_builder->countAllResults();
            $data_lead[] = $count_lead;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('point_lead'), 'data' => $data_lead, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function remove_from_campaign($campaign_id, $contact_id, $contact_type){
        if($contact_type == 'lead'){
            $db_builder = $this->db->table(db_prefix() . 'ma_campaign_lead_exceptions');
            $db_builder->insert(['campaign_id' => $campaign_id, 'lead_id' => $contact_id, 'dateadded' => date('Y-m-d H:i:s')]);
        }else{
            $db_builder = $this->db->table(db_prefix() . 'ma_campaign_client_exceptions');
            $db_builder->insert(['campaign_id' => $campaign_id, 'client_id' => $contact_id, 'dateadded' => date('Y-m-d H:i:s')]);
        }


        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  integer
     * @return boolean
     */
    public function check_lead_exception($campaign_id, $lead_id){
        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_lead_exceptions');
        $db_builder->where('campaign_id', $campaign_id);
        $db_builder->where('lead_id', $lead_id);
        $lead_exception = $db_builder->get()->getRow();

        if ($lead_exception) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  string
     * @param  string
     * @return mixed
     */
    public function get_object_by_campaign($campaign_id, $type = '', $return = 'id'){
        $campaign = $this->get_campaign($campaign_id);

        $workflow = explode('\"'.$type.'\":\"',$campaign->workflow ?? '');

        $where = '';
        $object = [];
        if(isset($workflow[1])){
            foreach($workflow as $k => $data){
                if($k != 0){
                    $_workflow = explode('\"',$data);
                    if(isset($_workflow[1]) && !in_array($_workflow[0], $object)){
                        $object[] = $_workflow[0];
                    }
                }
            }
        }

        $data_return = [];
        if($return == 'object'){
            foreach($object as $id){
                switch ($type) {
                    case 'point_action':
                        $point_action = $this->get_point_action($id);
                        if($point_action){
                            $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
                            $db_builder->where('point_action_id', $id);
                            $db_builder->where('campaign_id', $campaign_id);
                            $point_action->total = $db_builder->countAllResults();
                            $data_return[] = $point_action;
                        }
                        break;
                    case 'email':
                        $email_template = $this->get_email($id);
                        if($email_template){
                            $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
                            $db_builder->where('email_id', $id);
                            $db_builder->where('campaign_id', $campaign_id);
                            $email_template->total = $db_builder->countAllResults();
                            $data_return[] = $email_template;
                        }
                        break;
                    case 'segment':
                        $segment = $this->get_segment($id);
                        if($segment){
                            $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
                            $db_builder->where('segment_id', $id);
                            $db_builder->where('campaign_id', $campaign_id);
                            $segment->total = $db_builder->countAllResults();
                            $data_return[] = $segment;
                        }
                        break;
                    case 'stage':
                        $stage = $this->get_stage($id);
                        if($stage){
                            $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
                            $db_builder->where('stage_id', $id);
                            $db_builder->where('campaign_id', $campaign_id);
                            $stage->total = $db_builder->countAllResults();

                            $data_return[] = $stage;
                        }
                        break;
                    case 'sms':
                        $sms = $this->get_sms($id);
                        if($sms){
                            $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
                            $db_builder->where('sms_id', $id);
                            $db_builder->where('campaign_id', $campaign_id);
                            $sms->total = $db_builder->countAllResults();
                            
                            $data_return[] = $sms;
                        }
                        break;
                    
                    default:
                        // code...
                        break;
                }
            }

            return $data_return;
        }
        
        return $object;
    }

    /**
     * @return boolean
     */
    public function ma_cron_campaign(){
        $where = 'start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'" AND published = 1';
        $campaigns = $this->get_campaign('', $where);

        foreach($campaigns as $campaign){
            $this->run_campaigns($campaign['id']);
        }

        return true;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_email_chart($campaign_id = '')
    {

        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->where('open', 1);
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_open = [];
        foreach($email_logs as $download){
            $data_open[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->where('click', 1);
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_click = [];
        foreach($email_logs as $download){
            $data_click[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_segment_chart($campaign_id = ''){

        $db_builder = $this->db->table(db_prefix() . 'ma_lead_segments');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }

        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->where('deleted', 1);
        $db_builder->groupBy('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_stage_chart($campaign_id = ''){

        $db_builder = $this->db->table(db_prefix() . 'ma_lead_stages');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_added = [];
        foreach($email_logs as $download){
            $data_added[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(date_delete, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->where('deleted', 1);
        $db_builder->groupBy('date_format(date_delete, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_removed = [];
        foreach($email_logs as $download){
            $data_removed[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('added'), 'data' => $data_added, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('removed'), 'data' => $data_removed, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_text_message($id, $return_type = 'leads'){
        
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('lead_id');
        $db_builder->where('text_message_id', $id);
        $db_builder->groupBy('lead_id');
        $email_logs = $db_builder->get()->getResultArray();

        $where = '';
        foreach ($email_logs as $key => $value) {
            if($where != ''){
                $where .= ','. $value['lead_id'];
            }else{
                $where .= $value['lead_id'];
            }
        }

        if($where != ''){
          $where = '('.db_prefix().'clients.id in ('.$where.') and '.db_prefix().'clients.deleted = 0)';
        }else{
          $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_text_message($id){
        $where = 'workflow LIKE \'%\\\\\\\\"text_message\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $campaigns = $db_builder->get()->getResultArray();

        $db_builder->where('end_date <= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $old_campaigns = $db_builder->get()->getResultArray();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_text_message_by_campaign_chart($text_message_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('campaign_id');
        $db_builder->where('text_message_id', $text_message_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_action = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
            $db_builder->where('text_message_id', $text_message_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $count_action = $db_builder->countAllResults();
            $data_action[] = $count_action;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('text_message'), 'data' => $data_action, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_text_message_chart($text_message_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($text_message_id != ''){
            $db_builder->where('text_message_id', $text_message_id);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $text_message = $db_builder->get()->getResultArray();

        $data_text_message = [];
        foreach($text_message as $action){
            $data_text_message[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_text_message;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_text_message_chart($campaign_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $text_message = $db_builder->get()->getResultArray();

        $data_text_message = [];
        foreach($text_message as $action){
            $data_text_message[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_text_message;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_point_action_chart($campaign_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_point_action_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $point_action = $db_builder->get()->getResultArray();

        $data_point_action = [];
        foreach($point_action as $action){
            $data_point_action[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_point_action;
    }

    /**
     * @param  string
     * @param  integer
     * @param  integer
     * @return string
     */
    public function parse_content_merge_fields($content, $data = [], $log_id = ''){

        $merge_fields = [];

        $Company_model = model('App\Models\Company_model');
        $company_info = $Company_model->get_one_where(array("is_default" => true));
        $merge_fields["COMPANY_NAME"] = $company_info->name;
        $merge_fields["LOGO_URL"] = get_logo_url();

        if(isset($data['lead'])){
            $merge_fields["LEAD_NAME"] = $data['lead']['company_name'];
            $merge_fields["LEAD_PHONE"] = $data['lead']['phone'];
            $merge_fields["LEAD_WEBSITE"] = $data['lead']['website'];
            $merge_fields["LEAD_ADDRESS"] = $data['lead']['address'];
            $merge_fields["LEAD_CITY"] = $data['lead']['city'];
            $merge_fields["LEAD_STATE"] = $data['lead']['state'];
            $merge_fields["LEAD_ZIP"] = $data['lead']['zip'];
            $merge_fields["LEAD_COUNTRY"] = $data['lead']['country'];

        }

        if(isset($data['client'])){

            $merge_fields["CLIENT_NAME"] = $data['client']['company_name'];
            $merge_fields["CLIENT_PHONE"] = $data['client']['phone'];
            $merge_fields["CLIENT_WEBSITE"] = $data['client']['website'];
            $merge_fields["CLIENT_ADDRESS"] = $data['client']['address'];
            $merge_fields["CLIENT_CITY"] = $data['client']['city'];
            $merge_fields["CLIENT_STATE"] = $data['client']['state'];
            $merge_fields["CLIENT_ZIP"] = $data['client']['zip'];
            $merge_fields["CLIENT_COUNTRY"] = $data['client']['country'];
        }

        $parser = \Config\Services::parser();
        $content = $parser->setData($merge_fields)->renderString($content);

        if($log_id != ''){
            $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
            $db_builder->where('id', $log_id);
            $email_log = $db_builder->get()->getRow();

            $content = str_replace('href="', 'href="'.site_url('ma_public/click/'.$email_log->hash.'?href='), $content);
            $content .= '<img alt="" src="'.site_url('ma_public/images/'.$email_log->hash.'.jpg').'" width="1" height="1" />';
        }

        return $content;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_form_chart($form_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'clients');
        $db_builder->select('created_date as time, COUNT(*) as count_submit');
        if($form_id != ''){
            $db_builder->where('form_id', $form_id);
        }
        $db_builder->where('(from_ma_form_id != 0)');
        $db_builder->groupBy('created_date');
        $point_action = $db_builder->get()->getResultArray();

        $data_point_action = [];
        foreach($point_action as $action){
            $data_point_action[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_submit']];
        }
        
        return $data_point_action;
    }

    /**
     * { mfa setting by admin }
     *
     * @param         $data   The data
     *
     * @return     boolean  
     */
    public function ma_sms_setting($data){
        
        $affectedRows = 0;

        $setting_dt = []; 
        if(isset($data['settings'])){
            $setting_dt['settings'] = $data['settings'];
            unset($data['settings']);
        }

        if(count($setting_dt) > 0){
            $db_builder = $this->db->table(get_db_prefix().'settings');
            foreach ($setting_dt['settings'] as $name => $val) {

                $db_builder->where('setting_name', $name);
                $db_builder->update([
                        'setting_value' => $val,
                    ]);

                if ($this->db->affectedRows() > 0) {
                    $affectedRows++;
                }
            }
        }

        if($affectedRows > 0){
            return true;
        }
        return false;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_email($id, $return_type = 'leads'){
        
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('lead_id');
        $db_builder->where('email_id', $id);
        $db_builder->groupBy('lead_id');
        $email_logs = $db_builder->get()->getResultArray();

        $where = '';
        foreach ($email_logs as $key => $value) {
            if($where != ''){
                $where .= ','. $value['lead_id'];
            }else{
                $where .= $value['lead_id'];
            }
        }

        if($where != ''){
          $where = '('.db_prefix().'clients.id in ('.$where.') and '.db_prefix().'clients.deleted = 0 AND ma_unsubscribed = 0)';
        }else{
          $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_email($id){
        $where = 'workflow LIKE \'%\\\\\\\\"email\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $campaigns = $db_builder->get()->getResultArray();

        $db_builder->where('end_date <= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $old_campaigns = $db_builder->get()->getResultArray();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @param  array
     * @return array
     */
    public function get_data_email_chart($email_id = '', $data_filter = [])
    {
        $where = $this->get_where_report_period('date_format(dateadded, \'%Y-%m-%d\')');
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_id != ''){
            $db_builder->where('email_id', $email_id);
        }
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_delivery = [];
        foreach($email_logs as $download){
            $data_delivery[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_id != ''){
            $db_builder->where('email_id', $email_id);
        }
        $db_builder->where('open', 1);
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_open = [];
        foreach($email_logs as $download){
            $data_open[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_email');
        if($email_id != ''){
            $db_builder->where('email_id', $email_id);
        }
        $db_builder->where('click', 1);
        if($where != ''){
            $db_builder->where($where);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $email_logs = $db_builder->get()->getResultArray();
        $data_click = [];
        foreach($email_logs as $download){
            $data_click[] = [strtotime($download['time'].' 00:00:00') * 1000, (int)$download['count_email']];
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];
        
        return $data_return;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_email_by_campaign_chart($email_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        $db_builder->select('campaign_id');
        $db_builder->where('email_id', $email_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_delivery = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            if($campaign){
                $data_header[] = $campaign->name;

                $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
                $db_builder->where('email_id', $email_id);
                $db_builder->where('campaign_id', $value['campaign_id']);
                $count_delivery = $db_builder->countAllResults();
                $data_delivery[] = $count_delivery;

                $db_builder->where('email_id', $email_id);
                $db_builder->where('campaign_id', $value['campaign_id']);
                $db_builder->where('open', 1);
                $count_open = $db_builder->countAllResults();
                $data_open[] = $count_open;

                $db_builder->where('email_id', $email_id);
                $db_builder->where('campaign_id', $value['campaign_id']);
                $db_builder->where('click', 1);
                $count_click = $db_builder->countAllResults();
                $data_click[] = $count_click;
            }
        }

        $data_return = [];
        $data_return[] = ['name' => _l('delivery'), 'data' => $data_delivery, 'color' => '#008ece'];
        $data_return[] = ['name' => _l('read'), 'data' => $data_open, 'color' => '#84c529'];
        $data_return[] = ['name' => _l('click'), 'data' => $data_click, 'color' => '#ff6f00'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  array
     * @return boolean
     */
    public function email_design_save($data){
        if(isset($data['id']) && $data['id'] != ''){
            $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
            $db_builder->where('id', $data['id']);
            $db_builder->update(['data_html' => json_encode($data['data_html']), 'data_design' => json_encode($data['data_design'])]);

            if ($this->db->affectedRows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add new sms
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_sms($data)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if ($data['sms_template'] != '') {
            $sms_template = $this->get_text_message($data['sms_template']);

            $data['content'] = $sms_template->description;
        }

        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        
        $db_builder = $this->db->table(db_prefix() . 'ma_sms');
        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }
    
    /**
     * Get sms
     * @param  mixed $id sms id (Optional)
     * @return mixed     object or array
     */
    public function get_sms($id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $sms = $db_builder->get()->getRow();

            return $sms;
        }
        
        $db_builder->where('published', 1);
        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * Add new sms
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_sms($data, $id)
    {
        if (isset($data['id'])) {
            unset($data['id']);
        }

        if ($data['sms_template'] != '') {
            $sms_template = $this->get_text_message($data['sms_template']);
            $data['content'] = $sms_template->description;
        }

        $data['description'] = nl2br($data['description']);

        $db_builder = $this->db->table(db_prefix() . 'ma_sms');
        $db_builder->where('id', $id);
        $db_builder->update($data);

        if($this->db->affectedRows() > 0){ 
            return true;
        }
       
        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete sms from database
     */
    public function delete_sms($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
            $db_builder->where('sms_id', $id);
            $db_builder->delete();

            return true;
        }

        return false;
    }

    /**
     * @param  integer
     * @param  string
     * @return array
     */
    public function get_lead_by_sms($id, $return_type = 'leads'){
        
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('lead_id');
        $db_builder->where('sms_id', $id);
        $db_builder->groupBy('lead_id');
        $email_logs = $db_builder->get()->getResultArray();

        $where = '';
        foreach ($email_logs as $key => $value) {
            if($where != ''){
                $where .= ','. $value['lead_id'];
            }else{
                $where .= $value['lead_id'];
            }
        }

        if($where != ''){
          $where = '('.db_prefix().'clients.id in ('.$where.') and '.db_prefix().'clients.deleted = 0 AND ma_unsubscribed = 0)';
        }else{
          $where = '1=0';
        }

        if($return_type == 'leads'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where($where);
            $leads = $db_builder->get()->getResultArray();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_campaign_by_sms($id){
        $where = 'workflow LIKE \'%\\\\\\\\"sms\\\\\\\\":\\\\\\\\"'.$id.'\\\\\\\\"%\'';

        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('start_date <= "'.date('Y-m-d').'" AND end_date >= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $campaigns = $db_builder->get()->getResultArray();

        $db_builder->where('end_date <= "'.date('Y-m-d').'"');
        $db_builder->where($where);
        $old_campaigns = $db_builder->get()->getResultArray();

        return ['campaigns' => count($campaigns), 'old_campaigns' => count($old_campaigns)];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_sms_by_campaign_chart($sms_id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('campaign_id');
        $db_builder->where('sms_id', $sms_id);
        $db_builder->groupBy('campaign_id');
        $campaign_ids = $db_builder->get()->getResultArray();

        $data_header = [];
        $data_action = [];
        $data_open = [];
        $data_click = [];

        foreach($campaign_ids as $value){
            $campaign = $this->get_campaign($value['campaign_id']);
            $data_header[] = $campaign->name;

            $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
            $db_builder->where('sms_id', $sms_id);
            $db_builder->where('campaign_id', $value['campaign_id']);
            $count_action = $db_builder->countAllResults();
            $data_action[] = $count_action;
        }

        $data_return = [];
        $data_return[] = ['name' => _l('sms'), 'data' => $data_action, 'color' => '#008ece'];

        return ['header' => $data_header, 'data' => $data_return];
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_sms_chart($sms_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($sms_id != ''){
            $db_builder->where('sms_id', $sms_id);
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $sms = $db_builder->get()->getResultArray();

        $data_sms = [];
        foreach($sms as $action){
            $data_sms[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_sms;
    }

    /**
     * @param  integer
     * @return array
     */
    public function get_data_campaign_sms_chart($campaign_id = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_sms_logs');
        $db_builder->select('date_format(dateadded, \'%Y-%m-%d\') as time, COUNT(*) as count_action');
        if($campaign_id != ''){
            $db_builder->where('campaign_id', $campaign_id);
        }else{
            $db_builder->where('campaign_id > 0');
        }
        $db_builder->groupBy('date_format(dateadded, \'%Y-%m-%d\')');
        $sms = $db_builder->get()->getResultArray();

        $data_sms = [];
        foreach($sms as $action){
            $data_sms[] = [strtotime($action['time'].' 00:00:00') * 1000, (int)$action['count_action']];
        }
        
        return $data_sms;
    }

    /**
     * Send email - No templates used only simple string
     * @since Version 1.0.2
     * @param  string $email   email
     * @param  string $ma_email_object email object
     * @param  integer $log_id   email log ID
     * @return boolean
     */
    public function ma_send_email($to, $ma_email_object, $data = [], $log_id = '', $email_design_id = '')
    {   
        $subject = $ma_email_object->subject;

        $content = $this->get_email_content_by_contact($ma_email_object->id, $data, $email_design_id);
        $message = $this->parse_content_merge_fields(json_decode($content), $data, $log_id);
        $optoins = [];

        if($ma_email_object->attachment != '0' && $ma_email_object->attachment != ''){
            $db_builder = $this->db->table(db_prefix() . 'ma_assets');
            $db_builder->where('id', $ma_email_object->attachment);
            $asset = $db_builder->get()->getRow();
            if($asset){
                if($data){
                    $asset_hash = app_generate_hash();
                    $this->save_asset_log([
                            'lead_id' => (isset($data['lead']) ? $data['lead']['id'] : 0), 
                            'client_id' => (isset($data['client']) ? $data['client']['id'] : 0),
                            'asset_id' => $ma_email_object->attachment, 
                            'hash' => $asset_hash,
                            'campaign_id' => $data['campaign']->id
                        ]);
                }

                $attachments = prepare_attachment_of_files(get_setting("ma_asset_file_path"), $asset->files);
                $optoins['attachments'] = $attachments;
            }
        }

        if($log_id != ''){
            $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
            $db_builder->where('id', $log_id);
            $email_log = $db_builder->get()->getRow();

            $message .= '<hr><br><a href="'.site_url('ma_public/unsubscribe/'.$email_log->hash).'" target="_blank">'._l('unsubscribe').'</a>  here';
        }


        if (get_setting("ma_smtp_type") === "other_smtp") {
            $email_sent_from_name = get_setting('ma_email_sent_from_name');
            $email_sent_from_address = get_setting('ma_email_sent_from_address');
            $email_protocol = get_setting('ma_email_protocol');
            $email_smtp_host = get_setting('ma_email_smtp_host');
            $email_smtp_port = get_setting('ma_email_smtp_port');
            $email_smtp_user = get_setting('ma_email_smtp_user');
            $email_smtp_pass = decode_password(get_setting('ma_email_smtp_pass'), "ma_email_smtp_pass");
            $email_smtp_security_type = get_setting('ma_email_smtp_security_type');
        }else{
            $email_sent_from_name = get_setting('email_sent_from_name');
            $email_sent_from_address = get_setting('email_sent_from_address');
            $email_protocol = get_setting('email_protocol');
            $email_smtp_host = get_setting('email_smtp_host');
            $email_smtp_port = get_setting('email_smtp_port');
            $email_smtp_user = get_setting('email_smtp_user');
            $email_smtp_pass = decode_password(get_setting('email_smtp_pass'), "email_smtp_pass");
            $email_smtp_security_type = get_setting('email_smtp_security_type');
        }

        $from_name = $email_sent_from_name;
        if($ma_email_object->from_name != ''){
            $from_name = $ma_email_object->from_name;
        }
        $from_email = $email_sent_from_address;
        if($ma_email_object->from_address != ''){
            $from_email = $ma_email_object->from_address;
        }

        $bcc_address = '';
        if($ma_email_object->bcc_address != ''){
            $bcc_address = $ma_email_object->bcc_address;
        }

        $reply_to = '';
        if($ma_email_object->reply_to_address != ''){
            $reply_to = $ma_email_object->reply_to_address;
        }

        $cc = '';

        $email_config = Array(
            'charset' => 'utf-8',
            'mailType' => 'html'
        );

        //check mail sending method from settings
        if ($email_protocol === "smtp") {
            $email_config["protocol"] = "smtp";
            $email_config["SMTPHost"] = $email_smtp_host;
            $email_config["SMTPPort"] = $email_smtp_port;
            $email_config["SMTPUser"] = $email_smtp_user;
            $email_config["SMTPPass"] = $email_smtp_pass;
            $email_config["SMTPCrypto"] = $email_smtp_security_type;

            if (!$email_config["SMTPCrypto"]) {
                $email_config["SMTPCrypto"] = "tls"; //for old clients, we have to set this by default
            }

            if ($email_config["SMTPCrypto"] === "none") {
                $email_config["SMTPCrypto"] = "";
            }
        }

        $email = \CodeIgniter\Config\Services::email();
        $email->initialize($email_config);
        $email->clear(true); //clear previous message and attachment

        $email->setNewline("\r\n");
        $email->setCRLF("\r\n");
        $email->setFrom($from_email, $from_name);

        $email->setTo($to);
        $email->setSubject($subject);

        $message = htmlspecialchars_decode($message);

        $email->setMessage($message);

        //add attachment
        $attachments = get_array_value($optoins, "attachments");
        if (is_array($attachments)) {
            foreach ($attachments as $value) {
                $file_path = get_array_value($value, "file_path");
                $file_name = get_array_value($value, "file_name");
                $email->attach(trim($file_path), "attachment", $file_name);
            }
        }

        //check reply-to
        if ($reply_to != '') {
            $email->setReplyTo($reply_to);
        }

        //check cc
        if ($cc != '') {
            $email->setCC($cc);
        }

        //check bcc
        if ($bcc_address != '') {
            $email->setBCC($bcc_address);
        }

        //send email
        if ($email->send()) {
            if($log_id != ''){
                $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
                $db_builder->where('id', $log_id);
                $db_builder->update(['delivery' => 1, 'delivery_time' => date('Y-m-d H:i:s')]);
            }

            return true;
        }else{
            if($log_id != ''){
                $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
                $db_builder->where('id', $log_id);
                $db_builder->update(['failed' => 1, 'failed_time' => date('Y-m-d H:i:s')]);
            }
        }

        return false;
    }

    /**
     * @param  array
     * @param  integer
     * @param  string
     * @return boolean
     */
    public function check_condition_email($data, $email_id, $type){
        $db_builder = $this->db->table(db_prefix() . 'ma_email_logs');
        if(isset($data['lead'])){
            $db_builder->where('lead_id', $data['lead']['id']);
        }else{
            $db_builder->where('client_id', $data['client']['id']);
        }
        $db_builder->where('campaign_id', $data['campaign']->id);
        $db_builder->where('email_id', $email_id);
        $db_builder->where($type, 1);
        $check = $db_builder->get()->getRow();
        if($check){
            return true;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function clone_email_template($data){
        
        $email_template = $this->get_email_template($data['id']);
        $data_insert = (array)$email_template;

        unset($data_insert['id']);
        $data_insert['name'] = $data['name'];
        $data_insert['addedfrom'] = get_staff_user_id();
        $data_insert['dateadded'] = date('Y-m-d H:i:s');
        $data_design = $data_insert['data_design'];
        unset($data_insert['data_design']);

        $db_builder = $this->db->table(db_prefix() . 'ma_email_templates');
        $db_builder->insert($data_insert);

        $insert_id = $this->db->insertID();
        if ($insert_id) {

            foreach ($data_design as $key => $value) {
                $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');
                $db_builder->insert([
                    'email_template_id' => $insert_id,
                    'language' => $value['language'],
                    'country' => $value['country'],
                    'data_design' => $value['data_design'],
                    'data_html' => $value['data_html'],
                ]);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * get leads details
     * @param  array  $options
     * @return [type]         
     */
    function get_leads_details($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $projects_table = $this->db->prefixTable('projects');
        $users_table = $this->db->prefixTable('users');
        $invoices_table = $this->db->prefixTable('invoices');
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $invoice_items_table = $this->db->prefixTable('invoice_items');
        $taxes_table = $this->db->prefixTable('taxes');
        $client_groups_table = $this->db->prefixTable('client_groups');
        $lead_status_table = $this->db->prefixTable('lead_status');
        $estimates_table = $this->db->prefixTable('estimates');
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $tickets_table = $this->db->prefixTable('tickets');
        $orders_table = $this->db->prefixTable('orders');
        $proposals_table = $this->db->prefixTable('proposals');

        $where = "";
        $id = get_array_value($options, "id");
        if ($id) {
            $id = $this->db->escapeString($id);
            $where .= " AND $clients_table.id=$id";
        }

        $custom_field_type = "clients";

        $leads_only = get_array_value($options, "leads_only");
        if ($leads_only) {
            $custom_field_type = "leads";
            $where .= " AND $clients_table.is_lead=1";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $clients_table.lead_status_id='$status'";
        }

        $source = get_array_value($options, "source");
        if ($source) {
            $where .= " AND $clients_table.lead_source_id='$source'";
        }

        $owner_id = get_array_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $clients_table.created_by=$created_by";
        }

        $show_own_clients_only_user_id = get_array_value($options, "show_own_clients_only_user_id");
        if ($show_own_clients_only_user_id) {
            $where .= " AND ($clients_table.created_by=$show_own_clients_only_user_id OR $clients_table.owner_id=$show_own_clients_only_user_id)";
        }

        if (!$id && !$leads_only) {
            //only clients
            $where .= " AND $clients_table.is_lead=0";
        }

        $group_id = get_array_value($options, "group_id");
        if ($group_id) {
            $where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
        }

        $start_date = get_array_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($clients_table.created_date)>='$start_date'";
        }
        $end_date = get_array_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($clients_table.created_date)<='$end_date'";
        }

        $rel_type = get_array_value($options, "rel_type");
        if ($rel_type) {
            $rel_id = get_array_value($options, "rel_id");

            switch ($rel_type) {
                case 'stage':
                    $where_stage = $this->get_lead_by_stage($rel_id, 'where');
                    $where .= " AND ".$where_stage;
                    break;
                
                case 'segment':
                    $where_segment = $this->get_lead_by_segment($rel_id, 'where');
                    $where .= " AND ".$where_segment;
                    break;

                case 'campaign':
                    $where_campaign = $this->get_lead_by_campaign($rel_id, 'where');
                    $where .= " AND ".$where_campaign;
                    break;

                case 'email_template':
                    $where_email_template = $this->get_lead_by_email_template($rel_id, 'where');
                    $where .= " AND ".$where_email_template;
                    break;

                case 'email':
                    $where_email = $this->get_lead_by_email($rel_id, 'where');
                    $where .= " AND ".$where_email;
                    break;

                case 'sms':
                    $where_sms = $this->get_lead_by_sms($rel_id, 'where');
                    $where .= " AND ".$where_sms;
                    break;

                case 'point_action':
                    $where_point_action = $this->get_lead_by_point_action($rel_id, 'where');
                    $where .= " AND ".$where_point_action;
                    break;

                case 'text_message':
                    $where_text_message = $this->get_lead_by_text_message($rel_id, 'where');
                    $where .= " AND ".$where_text_message;
                    break;
                default:
                    // code...
                    break;
            }
        }

        $client_groups = get_array_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $clients_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $invoice_value_calculation_query = "(SUM" . _get_invoice_value_calculation_query($invoices_table) . ")";

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $invoice_value_select = "IFNULL(invoice_details.invoice_value,0)";
        $payment_value_select = "IFNULL(invoice_details.payment_received,0)";

        $sql = "SELECT $clients_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact, $users_table.id AS primary_contact_id, $users_table.image AS contact_avatar,  project_table.total_projects, $payment_value_select AS payment_received $select_custom_fieds,
                IF((($invoice_value_select > $payment_value_select) AND ($invoice_value_select - $payment_value_select) <0.05), $payment_value_select, $invoice_value_select) AS invoice_value,
                (SELECT GROUP_CONCAT($client_groups_table.title) FROM $client_groups_table WHERE FIND_IN_SET($client_groups_table.id, $clients_table.group_ids)) AS client_groups, $lead_status_table.title AS lead_status_title,  $lead_status_table.color AS lead_status_color,
                owner_details.owner_name, owner_details.owner_avatar
        FROM $clients_table
        LEFT JOIN $users_table ON $users_table.client_id = $clients_table.id AND $users_table.deleted=0 AND $users_table.is_primary_contact=1 
        LEFT JOIN (SELECT client_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 GROUP BY client_id) AS project_table ON project_table.client_id= $clients_table.id
        LEFT JOIN (SELECT client_id, SUM(payments_table.payment_received) as payment_received, $invoice_value_calculation_query as invoice_value FROM $invoices_table
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $invoices_table.tax_id
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $invoices_table.tax_id2 
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table3 ON tax_table3.id = $invoices_table.tax_id3 
                   LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id=$invoices_table.id AND $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   LEFT JOIN (SELECT invoice_id, SUM(total) AS invoice_value FROM $invoice_items_table WHERE deleted=0 GROUP BY invoice_id) AS items_table ON items_table.invoice_id=$invoices_table.id AND $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   WHERE $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   GROUP BY $invoices_table.client_id    
                   ) AS invoice_details ON invoice_details.client_id= $clients_table.id 
        LEFT JOIN $lead_status_table ON $clients_table.lead_status_id = $lead_status_table.id 
        LEFT JOIN (SELECT $users_table.id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar FROM $users_table WHERE $users_table.deleted=0 AND $users_table.user_type='staff') AS owner_details ON owner_details.id=$clients_table.owner_id
        $join_custom_fieds               
        WHERE $clients_table.deleted=0 $where $custom_fields_where";
        return $this->db->query($sql);
    }

    /**
     * Add new permission
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function add_permission($data)
    {   
        $db_builder = $this->db->table(db_prefix() . 'ma_permissions');
        $db_builder->where('user_id', $data['user']);
        $permission = $db_builder->get()->getRow();

        if($permission){
            $this->update_permission($data, $permission->id);

            return true;
        }

        $data_insert = [];
        $data_insert['user_id'] = $data['user'];
        unset($data['id']);
        unset($data['user']);
        $data_insert['permissions'] = json_encode($data);


        $db_builder = $this->db->table(db_prefix() . 'ma_permissions');
        $db_builder->insert($data_insert);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * update new permission
     * @param mixed $data All $_POST data
     * @return boolean
     */
    public function update_permission($data, $id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_permissions');
        $data_insert = [];
        $data_insert['user_id'] = $data['user'];
        unset($data['id']);
        unset($data['user']);
        $data_insert['permissions'] = json_encode($data);

        $db_builder->where('id', $id);
        $db_builder->update($data_insert);
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete permission from database, if used return array with key referenced
     */
    public function delete_permission($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_permissions');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get permission
     * @param  mixed $id permission id (Optional)
     * @return mixed     object or array
     */
    public function get_permission($id = '', $type = '')
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_permissions');
        if (is_numeric($id)) {
            $db_builder->where('id', $id);

            $permission = $db_builder->get()->getRow();
            if($permission){
                $permission->permissions = json_decode($permission->permissions, true);
            }

            return $permission;
        }

        if ($type != '') {
            $db_builder->where('type', $type);
        }

        $db_builder->orderBy('name', 'asc');

        return $db_builder->get()->getResultArray();
    }

    /**
     * segment change category
     * @param  integer $id       
     * @param  integer $category 
     * @return boolean           
     */
    public function segment_change_category($id, $category){
        $db_builder = $this->db->table(db_prefix() . 'ma_segments');
        $db_builder->where('id', $id);
        $db_builder->update(['category' => $category]);
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * campaign change category
     * @param  integer $id       
     * @param  integer $category 
     * @return boolean           
     */
    public function campaign_change_category($id, $category){
        $db_builder = $this->db->table(db_prefix() . 'ma_campaigns');
        $db_builder->where('id', $id);
        $db_builder->update(['category' => $category]);
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * stage change category
     * @param  integer $id       
     * @param  integer $category 
     * @return boolean           
     */
    public function stage_change_category($id, $category){
        $db_builder = $this->db->table(db_prefix() . 'ma_stages');
        $db_builder->where('id', $id);
        $db_builder->update(['category' => $category]);
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * add email template language
     * @param array $data
     */
    public function add_email_template_language($data){
        $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');

        $db_builder->where('email_template_id', $data['email_template_id']);
        $db_builder->where('country', $data['country']);
        $email_template_design = $db_builder->get()->getRow();

        if($email_template_design){
            return false;
        }

        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get email_template
     * @param  mixed $id email_template id (Optional)
     * @return mixed     object or array
     */
    public function get_email_template_design($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');
        $db_builder->where('id', $id);

        $email_template_design = $db_builder->get()->getRow();

        return $email_template_design;
    }

    /**
     * clone email template design
     * @param  array $data 
     * @return [type]       
     */
    public function clone_email_template_design($data){
        $design = $this->get_email_template_design($data['from_country']);

        $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');
        $db_builder->where('email_template_id', $data['email_template_id']);
        $db_builder->where('country', $data['to_country']);
        $email_template_design = $db_builder->get()->getRow();

        if($email_template_design){
            $db_builder->where('id', $email_template_design->id);
            $db_builder->update([
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);

            if ($this->db->affectedRows() > 0) {
                return true;
            }
        }else{
            $db_builder->insert([
                'email_template_id' => $data['email_template_id'],
                'country' => $data['to_country'],
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);
            $insert_id = $this->db->insertID();
            if ($insert_id) {
                return $insert_id;
            }
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete email template design from database
     */
    public function delete_email_template_design($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_template_designs');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }


    /**
     * add email language
     * @param array $data
     */
    public function add_email_language($data){
        $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
        $db_builder->where('email_id', $data['email_id']);
        $db_builder->where('country', $data['country']);
        $email_design = $db_builder->get()->getRow();

        if($email_design){
            return false;
        }

        $db_builder->insert($data);
        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Get email
     * @param  mixed $id email id (Optional)
     * @return mixed     object or array
     */
    public function get_email_design($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
        $db_builder->where('id', $id);

        $email_design = $db_builder->get()->getRow();

        return $email_design;
    }

    /**
     * clone email design
     * @param  array $data 
     * @return [type]       
     */
    public function clone_email_design($data){
        $design = $this->get_email_design($data['from_country']);

        $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
        $db_builder->where('email_id', $data['email_id']);
        $db_builder->where('country', $data['to_country']);
        $email_design = $db_builder->get()->getRow();

        if($email_design){
            $db_builder->where('id', $email_design->id);
            $db_builder->update([
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);

            if ($this->db->affectedRows() > 0) {
                return true;
            }
        }else{
            $db_builder->insert([
                'email_id' => $data['email_id'],
                'country' => $data['to_country'],
                'data_design' => $design->data_design,
                'data_html' => $design->data_html,
            ]);
            $insert_id = $this->db->insertID();
            if ($insert_id) {
                return $insert_id;
            }

        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete email design from database
     */
    public function delete_email_design($id)
    {
        $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
        $db_builder->where('id', $id);
        $db_builder->delete();
        if ($this->db->affectedRows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * get email content by contact
     * @param  integer $email_id 
     * @param  integer $lead   
     * @return string       
     */
    public function get_email_content_by_contact($email_id, $data, $email_design_id = ''){
        if($email_design_id == ''){
            $country = '';
            if(isset($data['lead'])){
                $country = $data['lead']['country'];
            }else{
                $country = $data['client']['country'];
            }

            $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
            $db_builder->where('email_id', $email_id);
            $db_builder->where('country', $country);
            $design = $db_builder->get()->getRow();
            
            if ($design) {
                return $design->data_html;
            }else{
                $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
                $db_builder->where('email_id', $email_id);
                $design2 = $db_builder->get()->getRow();

                if ($design2) {
                    return $design2->data_html;
                }
            }
        }else{
            $db_builder = $this->db->table(db_prefix() . 'ma_email_designs');
            $db_builder->where('id', $email_design_id);
            $design = $db_builder->get()->getRow();

            if ($design) {
                return $design->data_html;
            }
        }


        return '';
    }

    /**
     * get change point by lead
     * @param  object $sms_id 
     * @param  integer $lead   
     * @return string       
     */
    public function get_change_point_by_contact($point_action_id, $contact){
        $point_action = $this->get_point_action($point_action_id);

        if($contact['country'] != '' && $point_action->add_points_by_country == 1){
            $db_builder = $this->db->table(db_prefix() . 'ma_point_action_details');
            $db_builder->where('point_action_id', $point_action_id);
            $db_builder->where('country', $contact['country']);
            $detail = $db_builder->get()->getRow();

            if ($detail) {
                return $detail->change_points;
            }
        }

        return $point_action->change_points;
    }

    /**
     * get campaign log by lead
     * @param  integer $lead_id 
     * @return array         
     */
    public function get_campaigns_by_lead($lead_id){
        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_flows');
        $db_builder->distinct();
        $db_builder->select('campaign_id');
        $db_builder->where('lead_id', $lead_id);
        $campaign_logs = $db_builder->get()->getResultArray();
        return $campaign_logs;
    }

    /**
     * @param  integer
     * @param  string
     * @return array or string
     */
    public function get_client_by_campaign($id, $return_type = 'clients'){
        $campaign = $this->get_campaign($id);
        $where = '';

        if($campaign->workflow != ''){
            $workflow = json_decode(json_decode($campaign->workflow), true);

            foreach($workflow['drawflow']['Home']['data'] as $data){
                if($data['class'] == 'flow_start'){
                    if(isset($data['data']['data_type']) && $data['data']['data_type'] == 'customer'){
                        if(isset($data['data']['customer_group'])){
                            $where = $this->get_client_by_group($data['data']['customer_group'], 'where');
                        }
                    }
                }
            }
        }   

        $db_builder = $this->db->table(db_prefix() . 'ma_campaign_client_exceptions');
        $db_builder->where('campaign_id', $id);
        $client_exception = $db_builder->get()->getResultArray();
        $client_exception_where = '';

        foreach($client_exception as $client){
            if($client_exception_where == ''){
                $client_exception_where = $client['client_id'];
            }else{
                $client_exception_where .= ','.$client['client_id'];
            }
        }

        if($client_exception_where != ''){
            if($where != ''){
                $where .= ' AND '.db_prefix().'clients.id not in ('.$client_exception_where.')';
            }else{
                $where .= db_prefix().'clients.id not in ('.$client_exception_where.')';
            }
        }

        if($where == ''){
            $where = '1=0';
        }else{
            $where .= ' AND '.db_prefix() . 'clients.deleted = 0 AND ma_unsubscribed = 0';
        }

        if($return_type == 'clients'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->select('*, '.db_prefix() . 'clients.id as id');
            $db_builder->where($where);
            $db_builder->where('is_lead', 0);
            $db_builder->join(db_prefix() . 'users', '' . db_prefix() . 'users.client_id = ' . db_prefix() . 'clients.id AND is_primary_contact = 1 AND user_type = "client"', 'left');
            $clients = $db_builder->get()->getResultArray();

            return $clients;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  string
     * @return array or boolean
     */
    public function get_client_by_group($id, $return_type = 'clients'){
        $where = 'find_in_set('.$id.', '.db_prefix() . 'clients.group_ids)';
        if($return_type == 'clients'){
            $db_builder = $this->db->table(db_prefix() . 'clients');
            $db_builder->where('find_in_set('.$id.',group_ids)');
            $db_builder->where('active', 1);
            $db_builder->where('is_lead', 0);
            $db_builder->where('deleted', 0);
            $db_builder->where('ma_unsubscribed', 0);
            $leads = $db_builder->get()->result_array();

            return $leads;
        }elseif($return_type == 'where'){
            return $where;
        }

        return false;
    }

    /**
     * @param  array
     * @return boolean
     */
    public function save_asset_log($data){
        
        $data['dateadded'] = date('Y-m-d H:i:s');
        $db_builder = $this->db->table(db_prefix() . 'ma_asset_logs');
        $db_builder->insert($data);

        $insert_id = $this->db->insertID();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * Update smtp settings
     * @param  array $data all settings
     * @return integer
     */
    public function save_smtp_setting($data){
        $affectedRows = 0;
        foreach ($data['settings'] as $name => $val) {
            if($name == 'ma_smtp_password'){
                if (!empty($val)) {
                    $val = $this->encryption->encrypt($val);
                }
            }

            if (update_option($name, $val)) {
                $affectedRows++;
            }
        }

        return $affectedRows;
    }

    /**
     * [get_clients_details description]
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    function get_clients_details($options = array()) {
        $clients_table = $this->db->prefixTable('clients');
        $projects_table = $this->db->prefixTable('projects');
        $users_table = $this->db->prefixTable('users');
        $invoices_table = $this->db->prefixTable('invoices');
        $invoice_payments_table = $this->db->prefixTable('invoice_payments');
        $invoice_items_table = $this->db->prefixTable('invoice_items');
        $taxes_table = $this->db->prefixTable('taxes');
        $client_groups_table = $this->db->prefixTable('client_groups');
        $lead_status_table = $this->db->prefixTable('lead_status');
        $estimates_table = $this->db->prefixTable('estimates');
        $estimate_requests_table = $this->db->prefixTable('estimate_requests');
        $tickets_table = $this->db->prefixTable('tickets');
        $orders_table = $this->db->prefixTable('orders');
        $proposals_table = $this->db->prefixTable('proposals');

        $where = "";

        $rel_type = get_array_value($options, "rel_type");
        if ($rel_type) {
            $rel_id = get_array_value($options, "rel_id");

            switch ($rel_type) {
                case 'campaign':
                    $where_campaign = $this->get_client_by_campaign($rel_id, 'where');
                    $where .= " AND ".$where_campaign;
                    break;
                default:
                    // code...
                    break;
            }
        }

        $id = get_array_value($options, "id");
        if ($id) {
            $id = $this->db->escapeString($id);
            $where .= " AND $clients_table.id=$id";
        }

        $custom_field_type = "clients";

        $leads_only = get_array_value($options, "leads_only");
        if ($leads_only) {
            $custom_field_type = "leads";
            $where .= " AND $clients_table.is_lead=1";
        }

        $status = get_array_value($options, "status");
        if ($status) {
            $where .= " AND $clients_table.lead_status_id='$status'";
        }

        $source = get_array_value($options, "source");
        if ($source) {
            $where .= " AND $clients_table.lead_source_id='$source'";
        }

        $owner_id = get_array_value($options, "owner_id");
        if ($owner_id) {
            $where .= " AND $clients_table.owner_id=$owner_id";
        }

        $created_by = get_array_value($options, "created_by");
        if ($created_by) {
            $where .= " AND $clients_table.created_by=$created_by";
        }

        $show_own_clients_only_user_id = get_array_value($options, "show_own_clients_only_user_id");
        if ($show_own_clients_only_user_id) {
            $where .= " AND ($clients_table.created_by=$show_own_clients_only_user_id OR $clients_table.owner_id=$show_own_clients_only_user_id)";
        }

        if (!$id && !$leads_only) {
            //only clients
            $where .= " AND $clients_table.is_lead=0";
        }

        $group_id = get_array_value($options, "group_id");
        if ($group_id) {
            $where .= " AND FIND_IN_SET('$group_id', $clients_table.group_ids)";
        }

        $quick_filter = get_array_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $clients_table, $projects_table, $invoices_table, $taxes_table, $invoice_payments_table, $invoice_items_table, $estimates_table, $estimate_requests_table, $tickets_table, $orders_table, $proposals_table);
        }

        $start_date = get_array_value($options, "start_date");
        if ($start_date) {
            $where .= " AND DATE($clients_table.created_date)>='$start_date'";
        }
        $end_date = get_array_value($options, "end_date");
        if ($end_date) {
            $where .= " AND DATE($clients_table.created_date)<='$end_date'";
        }

        $client_groups = get_array_value($options, "client_groups");
        $where .= $this->prepare_allowed_client_groups_query($clients_table, $client_groups);

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string($custom_field_type, $custom_fields, $clients_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $invoice_value_calculation_query = "(SUM" . _get_invoice_value_calculation_query($invoices_table) . ")";

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $invoice_value_select = "IFNULL(invoice_details.invoice_value,0)";
        $payment_value_select = "IFNULL(invoice_details.payment_received,0)";

        $sql = "SELECT $clients_table.*, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS primary_contact, $users_table.id AS primary_contact_id, $users_table.image AS contact_avatar,  project_table.total_projects, $payment_value_select AS payment_received $select_custom_fieds,
                IF((($invoice_value_select > $payment_value_select) AND ($invoice_value_select - $payment_value_select) <0.05), $payment_value_select, $invoice_value_select) AS invoice_value,
                (SELECT GROUP_CONCAT($client_groups_table.title) FROM $client_groups_table WHERE FIND_IN_SET($client_groups_table.id, $clients_table.group_ids)) AS client_groups, $lead_status_table.title AS lead_status_title,  $lead_status_table.color AS lead_status_color,
                owner_details.owner_name, owner_details.owner_avatar
        FROM $clients_table
        LEFT JOIN $users_table ON $users_table.client_id = $clients_table.id AND $users_table.deleted=0 AND $users_table.is_primary_contact=1 
        LEFT JOIN (SELECT client_id, COUNT(id) AS total_projects FROM $projects_table WHERE deleted=0 GROUP BY client_id) AS project_table ON project_table.client_id= $clients_table.id
        LEFT JOIN (SELECT client_id, SUM(payments_table.payment_received) as payment_received, $invoice_value_calculation_query as invoice_value FROM $invoices_table
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table ON tax_table.id = $invoices_table.tax_id
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table2 ON tax_table2.id = $invoices_table.tax_id2 
                   LEFT JOIN (SELECT $taxes_table.* FROM $taxes_table) AS tax_table3 ON tax_table3.id = $invoices_table.tax_id3 
                   LEFT JOIN (SELECT invoice_id, SUM(amount) AS payment_received FROM $invoice_payments_table WHERE deleted=0 GROUP BY invoice_id) AS payments_table ON payments_table.invoice_id=$invoices_table.id AND $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   LEFT JOIN (SELECT invoice_id, SUM(total) AS invoice_value FROM $invoice_items_table WHERE deleted=0 GROUP BY invoice_id) AS items_table ON items_table.invoice_id=$invoices_table.id AND $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   WHERE $invoices_table.deleted=0 AND $invoices_table.status='not_paid'
                   GROUP BY $invoices_table.client_id    
                   ) AS invoice_details ON invoice_details.client_id= $clients_table.id 
        LEFT JOIN $lead_status_table ON $clients_table.lead_status_id = $lead_status_table.id 
        LEFT JOIN (SELECT $users_table.id, CONCAT($users_table.first_name, ' ', $users_table.last_name) AS owner_name, $users_table.image AS owner_avatar FROM $users_table WHERE $users_table.deleted=0 AND $users_table.user_type='staff') AS owner_details ON owner_details.id=$clients_table.owner_id
        $join_custom_fieds               
        WHERE $clients_table.deleted=0 $where $custom_fields_where";
        return $this->db->query($sql);
    }
}