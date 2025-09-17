<?php

namespace Recruitment\Models;

use App\Models\Crud_model;
use App\Controllers\Security_Controller;

class Recruitment_model extends Crud_model {


	function __construct() {

		parent::__construct();
	}


	/*general functions start*/

	/**
	 * prefixed table fields wildcard
	 * @param  [type] $table 
	 * @param  [type] $alias 
	 * @param  [type] $field 
	 * @return [type]        
	 */
	public function prefixed_table_fields_wildcard($table, $alias, $field)
	{

		$columns     = $this->db->query("SHOW COLUMNS FROM $table")->getResultArray();
		$field_names = [];
		foreach ($columns as $column) {
			$field_names[] = $column['Field'];
		}
		$prefixed = [];
		foreach ($field_names as $field_name) {
			if ($field == $field_name) {
				$prefixed[] = "`{$alias}`.`{$field_name}` AS `{$alias}.{$field_name}`";
			}
		}

		return implode(', ', $prefixed);
	}

	/**
	 * hr profile run query
	 * @param  [type] $query_string 
	 * @return [type]               
	 */
	public function recruitment_run_query($query_string)
	{
		return  $this->db->query("$query_string")->getResultArray();
	}

	/**
	 * count items
	 * @return [type] 
	 */
	public function count_all_items($where = '')
	{
		$items = $this->db->table(get_db_prefix().'items');
		$items->where('deleted', 0);
		if(strlen($where) > 0){
			$items->groupStart();
			$items->where($where);
			$items->groupEnd();
		}
		$list_item = $items->get()->getResultArray();
		return count($list_item);
	}

	/**
	 * Function that will parse table data from the tables folder for amin area
	 * @param  string $table  table filename
	 * @param  array  $params additional params
	 * @return void
	 */
	public function get_table_data($table, $dataPost, $params = [])
	{

		$params = app_hooks()->apply_filters('table_params', $params, $table);

		foreach ($params as $key => $val) {
			$key = $val;
		}

		$customFieldsColumns = [];

		$path = RECRUITMENT_VIEWPATH . 'admin/tables/' . $table . EXT;


		if (!file_exists($path)) {
			$path = $table;

			if (!endsWith($path, EXT)) {
				$path .= EXT;
			}
		} else {
			$myPrefixedPath = RECRUITMENT_VIEWPATH . 'admin/tables/my_' . $table . EXT;
			if (file_exists($myPrefixedPath)) {
				$path = $myPrefixedPath;
			}
		}

		include_once($path);

		echo json_encode($output);
		die;
	}

	/*general functions end*/

	/**
	 * get job position
	 * @param  boolean $id
	 * @return object
	 */
	public function get_job_position($id = false) {

		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'rec_job_position');
			$builder->where('position_id', $id);

			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'rec_job_position')->getResultArray();
		}
	}

	/**
	 * add job position
	 * @param object $data
	 */
	public function add_job_position($data) {

		$builder = $this->db->table(get_db_prefix().'rec_job_position');
		if (isset($data['job_skill'])) {
			$data['job_skill'] = implode(',', $data['job_skill']);
		}

		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * update job position
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_job_position($data, $id) {
		if (isset($data['job_skill'])) {
			$data['job_skill'] = implode(',', $data['job_skill']);
		} else {
			$data['job_skill'] = '';
		}

		$builder = $this->db->table(get_db_prefix().'rec_job_position');
		$builder->where('position_id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete job position
	 * @param  int $id
	 * @return bool
	 */
	public function delete_job_position($id) {
		$builder = $this->db->table(get_db_prefix().'rec_job_position');
		$builder->where('position_id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add recruitment proposal
	 * @param object $data
	 */
	public function add_recruitment_proposal($data) {
		if (isset($data['file'])) {
			unset($data['file']);
		}
		$data['salary_from'] = reformat_currency_rec($data['salary_from']);
		$data['salary_to'] = reformat_currency_rec($data['salary_to']);
		$data['from_date'] = to_sql_date1($data['from_date']);
		$data['to_date'] = to_sql_date1($data['to_date']);
		$data['date_add'] = get_my_local_time('Y-m-d');
		$data['status'] = 1;
		$data['add_from'] = get_staff_user_id1();

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}
		$builder = $this->db->table(get_db_prefix().'rec_proposal');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		if($insert_id){
			if (isset($custom_fields)) {
				handle_custom_fields_post($insert_id, $custom_fields);
			}
		}
		return $insert_id;
	}

	/**
	 * update recruitment proposal
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_recruitment_proposal($data, $id) {
		if (isset($data['file'])) {
			unset($data['file']);
		}
		$data['salary_from'] = reformat_currency_rec($data['salary_from']);
		$data['salary_to'] = reformat_currency_rec($data['salary_to']);
		$data['from_date'] = to_sql_date1($data['from_date']);
		$data['to_date'] = to_sql_date1($data['to_date']);

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			if (handle_custom_fields_post($id, $custom_fields)) {
				$affectedRows++;
			}
			unset($data['custom_fields']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_proposal');

		$builder->where('id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete recruitment proposal
	 * @param  int $id
	 * @return bool
	 */
	public function delete_recruitment_proposal($id) {
		$builder = $this->db->table(get_db_prefix().'files');
		$builder->where('rel_id', $id);
		$builder->where('rel_type', 'rec_proposal');
		$attachments = $builder->get()->getResultArray();
		foreach ($attachments as $attachment) {
			$this->delete_proposal_attachment($attachment['id']);
		}
		$builder = $this->db->table(get_db_prefix().'rec_proposal');

		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get rec proposal
	 * @param  string $id
	 * @return object
	 */
	public function get_rec_proposal($id = '') {
		if ($id != '') {
			$builder = $this->db->table(get_db_prefix().'rec_proposal');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		} elseif ($id == '') {
			$builder = $this->db->table(get_db_prefix().'rec_proposal');
			return $builder->get()->getResultArray();
		}
	}

	/**
	 * get rec proposal by status
	 * @param  int $status
	 * @return object
	 */
	public function get_rec_proposal_by_status($status) {
		$builder = $this->db->table(get_db_prefix().'rec_proposal');
		$builder->where('status', $status);
		return $builder->get()->getResultArray();
	}

	/**
	 * get proposal file
	 * @param  object $proposal
	 * @return int
	 */
	public function get_proposal_file($proposal) {
		$builder = $this->db->table(get_db_prefix().'files');

		$builder->where('rel_id', $proposal);
		$builder->where('rel_type', 'rec_proposal');
		return $builder->get()->getResultArray();
	}

	/**
	 * delete proposal attachment
	 * @param  int $id
	 * @return bool
	 */
	public function delete_proposal_attachment($id) {
		$attachment = $this->get_proposal_attachments('', $id);
		$deleted = false;
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/proposal/' . $attachment->rel_id . '/' . $attachment->file_name);
			}
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $attachment->id);
			$affected_rows = $builder->delete();
			if ($affected_rows > 0) {
				$deleted = true;
			}

			if (is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/proposal/' . $attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/proposal/' . $attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/proposal/' . $attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * get proposal attachments
	 * @param  object $proposal
	 * @param  string $id
	 * @return int
	 */
	public function get_proposal_attachments($proposal, $id = '') {
		// If is passed id get return only 1 attachment
		$builder = $this->db->table(get_db_prefix().'files');
		if (is_numeric($id)) {
			$builder->where('id', $id);
		} else {
			$builder->where('rel_id', $proposal);
		}
		$builder->where('rel_type', 'rec_proposal');
		if (is_numeric($id)) {
			return $builder->get()->getRow();
		}

		return $builder->get()->getResultArray();
	}

	/**
	 * get file
	 * @param  int  $id
	 * @param  boolean $rel_id
	 * @return object
	 */
	public function get_file($id, $rel_id = false) {
		$builder = $this->db->table(get_db_prefix().'files');
		$builder->where('id', $id);
		$file = $builder->get()->getRow();

		if ($file && $rel_id) {
			if ($file->rel_id != $rel_id) {
				return false;
			}
		}
		return $file;
	}

	/**
	 * approve reject proposal
	 * @param  int $type
	 * @param  int $id
	 * @return bool
	 */
	public function approve_reject_proposal($type, $id) {
		if ($type == 'approved') {
			$builder = $this->db->table(get_db_prefix().'rec_proposal');
			$builder->where('id', $id);
			$affected_rows = $builder->update(['status' => 2]);
			if ($affected_rows > 0) {
				return 'approved';
			}
			return false;
		} elseif ($type == 'reject') {
			$builder = $this->db->table(get_db_prefix().'rec_proposal');
			$builder->where('id', $id);
			$builder->update(['status' => 4]);
			if ($affected_rows > 0) {
				return 'reject';
			}
			return false;
		}
	}

	/**
	 * add recruitment campaign
	 * @param object $data
	 */
	public function add_recruitment_campaign($data) {
		if (isset($data['display_salary'])) {
			$data['display_salary'] = 1;
		} else {
			$data['display_salary'] = 0;

		}

		if (isset($data['file'])) {
			unset($data['file']);
		}
		if (isset($data['cp_proposal'])) {
			$data['cp_proposal'] = implode(',', $data['cp_proposal']);
		}

		if (isset($data['cp_manager'])) {
			$data['cp_manager'] = implode(',', $data['cp_manager']);
		}

		if (isset($data['cp_follower'])) {
			$data['cp_follower'] = implode(',', $data['cp_follower']);
		}

		$data['cp_salary_from'] = reformat_currency_rec($data['cp_salary_from']);
		$data['cp_salary_to'] = reformat_currency_rec($data['cp_salary_to']);
		$data['cp_from_date'] = to_sql_date1($data['cp_from_date']);
		$data['cp_to_date'] = to_sql_date1($data['cp_to_date']);
		$data['cp_date_add'] = get_my_local_time('Y-m-d');
		$data['cp_status'] = 1;
		$data['cp_add_from'] = get_staff_user_id1();
		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if($insert_id){
			if (isset($custom_fields)) {
				handle_custom_fields_post($insert_id, $custom_fields);
			}
		}
		return $insert_id;
	}

	/**
	 * update recruitment campaign
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_recruitment_campaign($data, $id) {
		if (isset($data['display_salary'])) {
			$data['display_salary'] = 1;
		} else {
			$data['display_salary'] = 0;

		}

		if (isset($data['file'])) {
			unset($data['file']);
		}
		if (isset($data['cp_proposal'])) {
			$data['cp_proposal'] = implode(',', $data['cp_proposal']);
		}else{
			$data['cp_proposal'] = null;
		}

		if (isset($data['cp_manager'])) {
			$data['cp_manager'] = implode(',', $data['cp_manager']);
		}else{
			$data['cp_manager'] = null;
		}

		if (isset($data['cp_follower'])) {
			$data['cp_follower'] = implode(',', $data['cp_follower']);
		}else{
			$data['cp_follower'] = null;
		}
		$data['cp_salary_from'] = reformat_currency_rec($data['cp_salary_from']);
		$data['cp_salary_to'] = reformat_currency_rec($data['cp_salary_to']);
		$data['cp_from_date'] = to_sql_date1($data['cp_from_date']);
		$data['cp_to_date'] = to_sql_date1($data['cp_to_date']);
		$data['cp_add_from'] = get_staff_user_id1();

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			if (handle_custom_fields_post($id, $custom_fields)) {
				$affectedRows++;
			}
			unset($data['custom_fields']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_campaign');

		$builder->where('cp_id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete recruitment campaign
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_recruitment_campaign($id) {
		$builder = $this->db->table(get_db_prefix().'files');

		$builder->where('rel_id', $id);
		$builder->where('rel_type', 'rec_campaign');
		$attachments = $builder->get()->getResultArray();
		foreach ($attachments as $attachment) {
			$this->delete_campaign_attachment($attachment['id']);
		}
		$builder = $this->db->table(get_db_prefix().'rec_campaign');

		$builder->where('cp_id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get rec campaign
	 * @param  string $id
	 * @return object
	 */
	public function get_rec_campaign($id = '', $where = '') {
		if ($id != '') {
			$builder = $this->db->table(get_db_prefix().'rec_campaign');

			$builder->where('cp_id', $id);
			return $builder->get()->getRow();
		} elseif ($id == '') {
			$builder = $this->db->table(get_db_prefix().'rec_campaign');
			if(strlen($where) > 0){
				$builder->where($where);
			}

			return $builder->get()->getResultArray();
		}
	}

	/**
	 * get campaign_file
	 * @param  object $proposal
	 * @return object
	 */
	public function get_campaign_file($proposal) {
		$builder = $this->db->table(get_db_prefix().'files');

		$builder->where('rel_id', $proposal);
		$builder->where('rel_type', 'rec_campaign');
		return $builder->get()->getresultArray();
	}

	/**
	 * delete campaign attachment
	 * @param  int $id
	 * @return bool
	 */
	public function delete_campaign_attachment($id) {
		$attachment = $this->get_campaign_attachments('', $id);
		$deleted = false;
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/campaign/' . $attachment->rel_id . '/' . $attachment->file_name);
			}
			$builder = $this->db->table(get_db_prefix().'files');

			$builder->where('id', $attachment->id);
			$affected_rows = $builder->delete();
			if ($affected_rows > 0) {
				$deleted = true;
			}

			if (is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/campaign/' . $attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/campaign/' . $attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/campaign/' . $attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * get campaign attachments
	 * @param  object $campaign
	 * @param  int $id
	 * @return object
	 */
	public function get_campaign_attachments($campaign, $id = '') {
		// If is passed id get return only 1 attachment
		$builder = $this->db->table(get_db_prefix().'files');
		if (is_numeric($id)) {
			$builder->where('id', $id);
		} else {
			$builder->where('rel_id', $campaign);
		}
		$builder->where('rel_type', 'rec_campaign');
		if (is_numeric($id)) {
			return $builder->get()->getRow();
		}

		return $builder->get()->getResultArray();
	}

	/**
	 * add candidate
	 * @param object $data
	 */
	public function add_candidate($data) {

		if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $password_before_hash = '';
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $data['password'] = md5($data['password']);
        }else{
        	$password_before_hash = '123456a@';
            $data['password'] = md5('123456a@');
        }

		if(isset($data['birthday'])){
			$data['birthday'] = $data['birthday'];

			if (!$this->check_format_date($data['birthday'])) {
				$data['birthday'] = to_sql_date1($data['birthday']);
			}
		}

		if(isset($data['days_for_identity'])){
			$data['days_for_identity'] = $data['days_for_identity'];

			if (!$this->check_format_date($data['days_for_identity'])) {
				$data['days_for_identity'] = to_sql_date1($data['days_for_identity']);
			}
		}

		if(isset($data['desired_salary'])){
			$data['desired_salary'] = reformat_currency_rec($data['desired_salary']);
		}
		$data['status'] = 1;
		$data['date_add'] = get_my_local_time('Y-m-d');

		if (isset($data['from_date'])) {
			$from_date = $data['from_date'];
			unset($data['from_date']);
		}

		if (isset($data['to_date'])) {
			$to_date = $data['to_date'];
			unset($data['to_date']);
		}

		if (isset($data['company'])) {
			$company = $data['company'];
			unset($data['company']);
		}

		if (isset($data['position'])) {
			$position = $data['position'];
			unset($data['position']);
		}

		if (isset($data['contact_person'])) {
			$contact_person = $data['contact_person'];
			unset($data['contact_person']);
		}

		if (isset($data['salary'])) {
			$salary = $data['salary'];
			unset($data['salary']);
		}

		if (isset($data['reason_quitwork'])) {
			$reason_quitwork = $data['reason_quitwork'];
			unset($data['reason_quitwork']);
		}

		if (isset($data['job_description'])) {
			$job_description = $data['job_description'];
			unset($data['job_description']);
		}

		if (isset($data['literacy_from_date'])) {
			$literacy_from_date = $data['literacy_from_date'];
			unset($data['literacy_from_date']);
		}

		if (isset($data['literacy_to_date'])) {
			$literacy_to_date = $data['literacy_to_date'];
			unset($data['literacy_to_date']);
		}

		if (isset($data['diploma'])) {
			$diploma = $data['diploma'];
			unset($data['diploma']);
		}

		if (isset($data['training_places'])) {
			$training_places = $data['training_places'];
			unset($data['training_places']);
		}

		if (isset($data['specialized'])) {
			$specialized = $data['specialized'];
			unset($data['specialized']);
		}

		if (isset($data['training_form'])) {
			$training_form = $data['training_form'];
			unset($data['training_form']);
		}

		if (isset($data['relationship'])) {
			$relationship = $data['relationship'];
			unset($data['relationship']);
		}

		if (isset($data['name'])) {
			$name = $data['name'];
			unset($data['name']);
		}

		if (isset($data['fi_birthday'])) {
			$fi_birthday = $data['fi_birthday'];
			unset($data['fi_birthday']);
		}

		if (isset($data['job'])) {
			$job = $data['job'];
			unset($data['job']);
		}

		if (isset($data['address'])) {
			$address = $data['address'];
			unset($data['address']);
		}

		if (isset($data['phone'])) {
			$phone = $data['phone'];
			unset($data['phone']);
		}

		if (isset($data['skill_name'])) {
			$skill_name = $data['skill_name'];
			unset($data['skill_name']);
		}

		if (isset($data['skill_description'])) {
			$skill_description = $data['skill_description'];
			unset($data['skill_description']);
		}

		if (isset($data['skill'])) {
			$data['skill'] = implode(',', $data['skill']);
		}

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		if(!isset($data['candidate_code'])){
			$data['candidate_code'] = $this->create_code('candidate_code');
		}

		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		if ($insert_id) {
			$this->update_prefix_number(['candidate_code_number' =>  get_setting('candidate_code_number')+1]);

			if (isset($custom_fields)) {
				handle_custom_fields_post($insert_id, $custom_fields);
			}

			if(isset($from_date)){				
				foreach ($from_date as $key => $val) {
					if ($from_date[$key] != '') {

						if (!$this->check_format_date($val)) {
							$val = to_sql_date1($val);
						}
						if (!$this->check_format_date($to_date[$key])) {
							$to_date[$key] = to_sql_date1($to_date[$key]);
						}

						$builder = $this->db->table(get_db_prefix().'cd_work_experience');
						$builder->insert([
							'candidate' => $insert_id,
							'from_date' => $val,
							'to_date' => $to_date[$key],
							'company' => $company[$key],
							'position' => $position[$key],
							'contact_person' => $contact_person[$key],
							'salary' => $salary[$key],
							'reason_quitwork' => $reason_quitwork[$key],
							'job_description' => $job_description[$key],
						]);
					}
				}
			}

			if(isset($literacy_from_date)){				
				foreach ($literacy_from_date as $key => $val) {
					if ($literacy_from_date[$key] != '') {

						if (!$this->check_format_date($val)) {
							$val = to_sql_date1($val);
						}

						if (!$this->check_format_date($literacy_to_date[$key])) {
							$literacy_to_date[$key] = to_sql_date1($literacy_to_date[$key]);
						}

						$builder = $this->db->table(get_db_prefix().'cd_literacy');
						$builder->insert([
							'candidate' => $insert_id,
							'literacy_from_date' => $val,
							'literacy_to_date' => $literacy_to_date[$key],
							'diploma' => $diploma[$key],
							'training_places' => $training_places[$key],
							'specialized' => $specialized[$key],
							'training_form' => $training_form[$key],
						]);
					}
				}
			}

			if(isset($relationship)){				
				foreach ($relationship as $key => $val) {
					if ($relationship[$key] != '') {

						if (!$this->check_format_date($fi_birthday[$key])) {
							$fi_birthday[$key] = to_sql_date1($fi_birthday[$key]);
						}

						$builder = $this->db->table(get_db_prefix().'cd_family_infor');
						$builder->insert([
							'candidate' => $insert_id,
							'relationship' => $val,
							'name' => $name[$key],
							'fi_birthday' => $fi_birthday[$key],
							'job' => $job[$key],
							'address' => $address[$key],
							'phone' => $phone[$key],
						]);
					}
				}
			}

			if(isset($skill_name)){				
				foreach ($skill_name as $key => $val) {
					if ($skill_name[$key] != '') {
						$builder = $this->db->table(get_db_prefix().'cd_skill');
						$builder->insert(db_prefix() . 'cd_skill', [
							'candidate' => $insert_id,
							'skill_name' => $val,
							'skill_description' => $skill_description[$key],
						]);
					}
				}
			}

			if(get_setting('send_email_welcome_for_new_contact') == 1){
                $this->send_candidate_welcome_mail($data, $password_before_hash);
            }

			return $insert_id;
		}
	}

	/**
	 * send candidate welcome mail
	 * @param  [type] $data                
	 * @param  [type] $password_before_hash
	 * @return [type]                      
	 */
	public function send_candidate_welcome_mail($data, $password_before_hash)
	{
        $html = '';
        $html .= app_lang('re_dear').' '.$data['candidate_name'] .' '.$data['last_name'].'. '.app_lang('re_welcome_contact').'. <br>'.app_lang('re_click_here_to_login') .': <a href="'.site_url('candidate_signin').'">link</a> <br>'.app_lang('your_password').': '.$password_before_hash;

        send_app_mail($data['email'], app_lang('re_welcome'), $html);
        return true;
    }

	/**
	 * change status campaign
	 * @param  int $status
	 * @param  int $id
	 * @return bool
	 */
	public function change_status_campaign($status, $id) {
		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->where('cp_id', $id);
		$affected_rows = $builder->update(['cp_status' => $status]);
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get candidates
	 * @param  string $id
	 * @return object
	 */
	public function get_candidates($id = '', $where = '') {
		if ($id == '') {
			$builder = $this->db->table(get_db_prefix().'rec_candidate');
			if(strlen($where) > 0){
				$builder->where($where);
			}
			return $builder->get()->getResultArray();
		} else {
			$builder = $this->db->table(get_db_prefix().'rec_candidate');
			$builder->where('id', $id);
			$candidate = $builder->get()->getRow();

			$builder = $this->db->table(get_db_prefix().'cd_literacy');
			$builder->where('candidate', $id);
			$candidate->literacy = $builder->get()->getResultArray();

			$builder = $this->db->table(get_db_prefix().'cd_family_infor');
			$builder->where('candidate', $id);
			$candidate->family_infor = $builder->get()->getResultArray();

			$builder = $this->db->table(get_db_prefix().'cd_work_experience');
			$builder->where('candidate', $id);
			$candidate->work_experience = $builder->get()->getResultArray();

			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('rel_id', $id);
			$builder->where('rel_type', 'rec_cadidate_avar');
			$candidate->avar = $builder->get()->getRow();

			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('rel_id', $id);
			$builder->where('rel_type', 'rec_cadidate_file');
			$candidate->file = $builder->get()->getResultArray();

			$builder = $this->db->table(get_db_prefix().'cd_care');
			$builder->where('candidate', $id);
			$candidate->care = $builder->get()->getResultArray();

			$builder = $this->db->table(get_db_prefix().'rec_applied_jobs');
			$builder->where('candidate_id', $id);
			$candidate->applied_jobs = $builder->get()->getResultArray();

			$arr_applied_job_activate = [];
			$builder = $this->db->table(get_db_prefix().'rec_applied_jobs');
			$builder->where('candidate_id', $id);
			$builder->where('activate', '1');
			$applied_job_activates = $builder->get()->getResultArray();
			foreach ($applied_job_activates as $applied_job_activate) {
			    $arr_applied_job_activate[] = $applied_job_activate['campaign_id'];
			}

			$candidate->applied_job_activate = $arr_applied_job_activate;

			return $candidate;
		}
	}

	/**
	 * update cadidate
	 * @param  object $data
	 * @param  int $id
	 * @return
	 */
	public function update_cadidate($data, $id) {

		$data['birthday'] = $data['birthday'];

		if (!$this->check_format_date($data['birthday'])) {
			$data['birthday'] = to_sql_date1($data['birthday']);
		}

		$data['days_for_identity'] = $data['days_for_identity'];
		if (!$this->check_format_date($data['days_for_identity'])) {
			$data['days_for_identity'] = to_sql_date1($data['days_for_identity']);
		}

		$data['desired_salary'] = reformat_currency_rec($data['desired_salary']);

		if (isset($data['from_date'])) {
			$from_date = $data['from_date'];
			unset($data['from_date']);
		}

		if (isset($data['to_date'])) {
			$to_date = $data['to_date'];
			unset($data['to_date']);
		}

		if (isset($data['company'])) {
			$company = $data['company'];
			unset($data['company']);
		}

		if (isset($data['position'])) {
			$position = $data['position'];
			unset($data['position']);
		}

		if (isset($data['contact_person'])) {
			$contact_person = $data['contact_person'];
			unset($data['contact_person']);
		}

		if (isset($data['salary'])) {
			$salary = $data['salary'];
			unset($data['salary']);
		}

		if (isset($data['reason_quitwork'])) {
			$reason_quitwork = $data['reason_quitwork'];
			unset($data['reason_quitwork']);
		}

		if (isset($data['job_description'])) {
			$job_description = $data['job_description'];
			unset($data['job_description']);
		}

		if (isset($data['literacy_from_date'])) {
			$literacy_from_date = $data['literacy_from_date'];
			unset($data['literacy_from_date']);
		}

		if (isset($data['literacy_to_date'])) {
			$literacy_to_date = $data['literacy_to_date'];
			unset($data['literacy_to_date']);
		}

		if (isset($data['diploma'])) {
			$diploma = $data['diploma'];
			unset($data['diploma']);
		}

		if (isset($data['training_places'])) {
			$training_places = $data['training_places'];
			unset($data['training_places']);
		}

		if (isset($data['specialized'])) {
			$specialized = $data['specialized'];
			unset($data['specialized']);
		}

		if (isset($data['training_form'])) {
			$training_form = $data['training_form'];
			unset($data['training_form']);
		}

		if (isset($data['relationship'])) {
			$relationship = $data['relationship'];
			unset($data['relationship']);
		}

		if (isset($data['name'])) {
			$name = $data['name'];
			unset($data['name']);
		}

		if (isset($data['fi_birthday'])) {
			$fi_birthday = $data['fi_birthday'];
			unset($data['fi_birthday']);
		}

		if (isset($data['job'])) {
			$job = $data['job'];
			unset($data['job']);
		}

		if (isset($data['address'])) {
			$address = $data['address'];
			unset($data['address']);
		}

		if (isset($data['phone'])) {
			$phone = $data['phone'];
			unset($data['phone']);
		}

		if (isset($data['skill_name'])) {
			$skill_name = $data['skill_name'];
			unset($data['skill_name']);
		}

		if (isset($data['skill_description'])) {
			$skill_description = $data['skill_description'];
			unset($data['skill_description']);
		}

		if (isset($data['skill'])) {
			$data['skill'] = implode(',', $data['skill']);
		}

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			if (handle_custom_fields_post($id, $custom_fields)) {
				$affectedRows++;
			}
			unset($data['custom_fields']);
		}

		if(isset($data['password'])){
			if (empty($data['password'])) {
				unset($data['password']);
			} else {
				$data['password']             = md5($data['password']);
				$data['last_password_change'] = date('Y-m-d H:i:s');
			}
		}

		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$builder->where('id', $id);
		$builder->update($data);

		$builder = $this->db->table(get_db_prefix().'cd_work_experience');
		$builder->where('candidate', $id);
		$builder->delete();
		if(isset($from_date)){

			foreach ($from_date as $key => $val) {
				if ($from_date[$key] != '') {

					if (!$this->check_format_date($val)) {
						$val = to_sql_date1($val);
					}
					if (!$this->check_format_date($to_date[$key])) {
						$to_date[$key] = to_sql_date1($to_date[$key]);
					}

					$builder = $this->db->table(get_db_prefix().'cd_work_experience');
					$builder->insert([
						'candidate' => $id,
						'from_date' => $val,
						'to_date' => $to_date[$key],
						'company' => $company[$key],
						'position' => $position[$key],
						'contact_person' => $contact_person[$key],
						'salary' => $salary[$key],
						'reason_quitwork' => $reason_quitwork[$key],
						'job_description' => $job_description[$key],
					]);
				}
			}
		}
		$builder = $this->db->table(get_db_prefix().'cd_literacy');
		$builder->where('candidate', $id);
		$builder->delete();
		if(isset($literacy_from_date)){
			foreach ($literacy_from_date as $key => $val) {
				if ($literacy_from_date[$key] != '') {

					if (!$this->check_format_date($val)) {
						$val = to_sql_date1($val);
					}

					if (!$this->check_format_date($literacy_to_date[$key])) {
						$literacy_to_date[$key] = to_sql_date1($literacy_to_date[$key]);
					}

					$builder = $this->db->table(get_db_prefix().'cd_literacy');
					$builder->insert([
						'candidate' => $id,
						'literacy_from_date' => $val,
						'literacy_to_date' => $literacy_to_date[$key],
						'diploma' => $diploma[$key],
						'training_places' => $training_places[$key],
						'specialized' => $specialized[$key],
						'training_form' => $training_form[$key],
					]);
				}
			}
		}
		$builder = $this->db->table(get_db_prefix().'cd_family_infor');
		$builder->where('candidate', $id);
		$builder->delete();
		if(isset($relationship)){
			foreach ($relationship as $key => $val) {
				if ($relationship[$key] != '') {

					if (!$this->check_format_date($fi_birthday[$key])) {
						$fi_birthday[$key] = to_sql_date1($fi_birthday[$key]);
					}

					$builder = $this->db->table(get_db_prefix().'cd_family_infor');
					$builder->insert([
						'candidate' => $id,
						'relationship' => $val,
						'name' => $name[$key],
						'fi_birthday' => $fi_birthday[$key],
						'job' => $job[$key],
						'address' => $address[$key],
						'phone' => $phone[$key],
					]);
				}
			}
		}

		$builder = $this->db->table(get_db_prefix().'cd_skill');
		$builder->where('candidate', $id);
		$builder->delete();
		if(isset($skill_name)){

			foreach ($skill_name as $key => $val) {
				if ($skill_name[$key] != '') {
					$builder->insert([
						'candidate' => $id,
						'skill_name' => $val,
						'skill_description' => $skill_description[$key],
					]);
				}
			}
		}

		$builder = $this->db->table(get_db_prefix().'files');
		$builder->where('rel_id', $id);
		$builder->where('rel_type', 'rec_cadidate_avar');
		$avar = $builder->get()->getRow();

		if ($avar && (isset($_FILES['cd_avar']['name']) && $_FILES['cd_avar']['name'] != '')) {
			if (empty($avar->external)) {
				unlink(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/avartar/' . $avar->rel_id . '/' . $avar->file_name);
			}
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $avar->id);
			$builder->delete();

			if (is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/' . $avar->rel_id)) {
				// Check if no avars left, so we can delete the folder also
				$other_avars = list_files(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/' . $avar->rel_id);
				if (count($other_avars) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/' . $avar->rel_id);
				}
			}
		}

		return true;
	}

	/**
	 * add interview schedules
	 * @param object $data
	 */
	public function add_interview_schedules($data) {

		$data['interview_day'] = $data['interview_day'];

		if (!$this->check_format_date($data['interview_day'])) {
			$data['interview_day'] = to_sql_date1($data['interview_day']);
		}

		$data['interviewer'] = implode(',', $data['interviewer']);
		$data['added_from'] = get_staff_user_id1();
		$data['added_date'] = get_my_local_time('Y-m-d');

		$data['from_hours'] = ($data['interview_day'] . ' ' . $data['from_time'] . ':00');
		$data['to_hours'] = $data['interview_day'] . ' ' . $data['to_time'] . ':00';

		if (!$this->check_format_date($data['interview_day'])) {
			$data['from_hours'] = to_sql_date1($data['interview_day']) . ' ' . $data['from_time'] . ':00';
		}

		if (!$this->check_format_date($data['interview_day'])) {
			$data['to_hours'] = to_sql_date1($data['interview_day']) . ' ' . $data['to_time'] . ':00';
		}

		if (isset($data['candidate'])) {
			$candidate = $data['candidate'];
			unset($data['candidate']);
		}

		if (isset($data['cd_from_hours'])) {
			$cd_from_time = $data['cd_from_hours'];
			unset($data['cd_from_hours']);
		}
		if (isset($data['cd_to_hours'])) {
			$cd_to_time = $data['cd_to_hours'];
			unset($data['cd_to_hours']);
		}

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			unset($data['custom_fields']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_interview');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		if ($insert_id) {

			if (isset($custom_fields)) {
				handle_custom_fields_post($insert_id, $custom_fields);
			}

			if (count($candidate) > 0) {
				foreach ($candidate as $key => $can) {

					$cd_from_hour = ($data['interview_day'] . ' ' . $cd_from_time[$key] . ':00');
					$cd_to_hour = $data['interview_day'] . ' ' . $cd_to_time[$key] . ':00';

					if (!$this->check_format_date($data['interview_day'])) {
						$cd_from_hour = to_sql_date1($data['interview_day']) . ' ' . $cd_from_time[$key] . ':00';
					}

					if (!$this->check_format_date($data['interview_day'])) {
						$cd_to_hour = to_sql_date1($data['interview_day']) . ' ' . $cd_to_time[$key] . ':00';
					}

					$builder = $this->db->table(get_db_prefix().'cd_interview');
					$builder->insert([
						'candidate' => $can,
						'interview' => $insert_id,
						'cd_from_hours' => $cd_from_hour,
						'cd_to_hours' => $cd_to_hour,
					]);
				}
			}
			return $insert_id;
		}
	}

	/**
	 * update interview schedules
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_interview_schedules($data, $id) {
		$data['interview_day'] = to_sql_date1($data['interview_day']);
		$data['interviewer'] = implode(',', $data['interviewer']);
		$data['added_from'] = get_staff_user_id1();
		$data['added_date'] = get_my_local_time('Y-m-d');

		$data['from_hours'] = ($data['interview_day'] . ' ' . $data['from_time'] . ':00');
		$data['to_hours'] = $data['interview_day'] . ' ' . $data['to_time'] . ':00';

		if (!$this->check_format_date($data['interview_day'])) {
			$data['from_hours'] = to_sql_date1($data['interview_day']) . ' ' . $data['from_time'] . ':00';
		}

		if (!$this->check_format_date($data['interview_day'])) {
			$data['to_hours'] = to_sql_date1($data['interview_day']) . ' ' . $data['to_time'] . ':00';
		}

		if (isset($data['candidate'])) {
			$candidate = $data['candidate'];
			unset($data['candidate']);
		}

		if (isset($data['cd_from_hours'])) {
			$cd_from_time = $data['cd_from_hours'];
			unset($data['cd_from_hours']);
		}
		if (isset($data['cd_to_hours'])) {
			$cd_to_time = $data['cd_to_hours'];
			unset($data['cd_to_hours']);
		}

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			if (handle_custom_fields_post($id, $custom_fields)) {
				$affectedRows++;
			}
			unset($data['custom_fields']);
		}
		
		$builder = $this->db->table(get_db_prefix().'cd_interview');
		$builder->where('interview', $id);
		$builder->delete();

		if (count($candidate) > 0) {
			foreach ($candidate as $key => $can) {
				$cd_from_hour = ($data['interview_day'] . ' ' . $cd_from_time[$key] . ':00');
				$cd_to_hour = $data['interview_day'] . ' ' . $cd_to_time[$key] . ':00';

				if (!$this->check_format_date($data['interview_day'])) {
					$cd_from_hour = to_sql_date1($data['interview_day']) . ' ' . $cd_from_time[$key] . ':00';
				}

				if (!$this->check_format_date($data['interview_day'])) {
					$cd_to_hour = to_sql_date1($data['interview_day']) . ' ' . $cd_to_time[$key] . ':00';
				}
				$builder = $this->db->table(get_db_prefix().'cd_interview');
				$builder->insert([
					'candidate' => $can,
					'interview' => $id,
					'cd_from_hours' => $cd_from_hour,
					'cd_to_hours' => $cd_to_hour,
				]);
			}
		}
		$builder = $this->db->table(get_db_prefix().'rec_interview');
		$builder->where('id', $id);
		$builder->update($data);

		return true;

	}

	/**
	 * delete candidate
	 * @param  int $id
	 * @return bool
	 */
	public function delete_candidate($id) {
		$builder = $this->db->table(get_db_prefix().'files');

		$builder->where('rel_id', $id);
		$builder->where('rel_type', 'rec_cadidate_file');
		$attachments = $builder->get()->getResultArray();
		foreach ($attachments as $attachment) {
			$this->delete_candidate_attachment($attachment['id']);

		}

		$builder = $this->db->table(get_db_prefix().'files');
		$builder->where('rel_id', $id);
		$builder->where('rel_type', 'rec_cadidate_avar');
		$avartar = $builder->get()->getResultArray();
		foreach ($avartar as $avar) {
			$this->delete_candidate_avar_attachment($avar['id']);

		}

		$builder = $this->db->table(get_db_prefix().'cd_interview');
		$builder->where('candidate', $id);
		$builder->delete();

		$builder = $this->db->table(get_db_prefix().'cd_skill');
		$builder->where('candidate', $id);
		$builder->delete();

		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * delete candidate attachment
	 * @param  int $id
	 * @return bool
	 */
	public function delete_candidate_attachment($id) {
		$attachment = $this->get_candidate_attachments('', $id);
		$deleted = false;
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/files/' . $attachment->rel_id . '/' . $attachment->file_name);
			}
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $attachment->id);
			$affected_rows = $builder->delete();
			if ($affected_rows > 0) {
				$deleted = true;
			}

			if (is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/files/' . $attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/files/' . $attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/files/' . $attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * delete candidate avar attachment
	 * @param  int $id
	 * @return bool
	 */
	public function delete_candidate_avar_attachment($id) {
		$attachment = $this->get_candidate_avar_attachments('', $id);
		$deleted = false;
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/avartar/' . $attachment->rel_id . '/' . $attachment->file_name);
			}
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $attachment->id);
			$affected_rows = $builder->delete();
			if ($affected_rows > 0) {
				$deleted = true;
			}

			if (is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/avartar/' . $attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/avartar/' . $attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/candidate/avartar/' . $attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * get candidate avar attachments
	 * @param  object $candidate
	 * @param  string $id
	 * @return object
	 */
	public function get_candidate_avar_attachments($candidate, $id = '') {
		// If is passed id get return only 1 attachment
		$builder = $this->db->table(get_db_prefix().'files');
		if (is_numeric($id)) {
			$builder->where('id', $id);
		} else {
			$builder->where('rel_id', $candidate);
		}
		$builder->where('rel_type', 'rec_cadidate_avar');
		$result = $builder->get();
		if (is_numeric($id)) {
			return $builder->get()->getRow();

		}

		return $builder->get()->getResultArray();
		
	}

	/**
	 * get candidate attachments
	 * @param  object $candidate
	 * @param  string $id
	 * @return object
	 */
	public function get_candidate_attachments($candidate, $id = '') {
		// If is passed id get return only 1 attachment\
		$builder = $this->db->table(get_db_prefix().'files');
		if (is_numeric($id)) {
			$builder->where('id', $id);
		} else {
			$builder->where('rel_id', $candidate);
		}
		$builder->where('rel_type', 'rec_cadidate_file');
		if (is_numeric($id)) {
			return $builder->get()->getRow();
		}

		return $builder->get()->getResultArray();
	}

	/**
	 * add care candidate
	 * @param object $data
	 */
	public function add_care_candidate($data) {
		$data['care_time'] = to_sql_date1($data['care_time'], true);
		$data['add_from'] = get_staff_user_id1();
		$data['add_time'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		$builder = $this->db->table(get_db_prefix().'cd_care');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * rating candidate
	 * @param  object $data
	 * @return bool
	 */
	public function rating_candidate($data) {
		$rs = 0;
		$assessor = get_staff_user_id1();
		$evaluation_date = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		
		$builder = $this->db->table(get_db_prefix().'rec_cd_evaluation');
		$builder->where('candidate', $data['candidate']);
		$rate = $builder->get()->getResultArray();
		if (count($rate) > 0) {
			$builder = $this->db->table(get_db_prefix().'rec_cd_evaluation');
			$builder->where('candidate', $data['candidate']);
			$builder->delete();
		}

		foreach ($data['rating'] as $key => $value) {

			$builder = $this->db->table(get_db_prefix().'rec_cd_evaluation');
			$builder->insert([
				'criteria' => $key,
				'rate_score' => $value,
				'assessor' => $assessor,
				'evaluation_date' => $evaluation_date,
				'percent' => $data['percent'][$key],
				'candidate' => $data['candidate'],
				'feedback' => $data['feedback'],
				'group_criteria' => $data['group'][$key],
			]);
			if ($this->db->insertID()) {
				$rs++;
			}

		}
		if ($rs > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * send mail candidate
	 * @param  object $data
	 * @return bool
	 */
	public function send_mail_candidate($data) {
		$staff_id = get_staff_user_id1();

		$inbox = array();

		$inbox['to'] = $data['email'];
		$inbox['sender_name'] = get_staff_full_name1($staff_id);
		$inbox['subject'] = $data['subject'];
		$inbox['body'] = $data['content'];
		$inbox['body'] = $inbox['body'];
		$inbox['date_received'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);

		send_app_mail($data['email'], $inbox['subject'], $inbox['body'] );

		$care = array();
		$care['care_time'] = $inbox['date_received'];
		$care['add_from'] = $staff_id;
		$care['add_time'] = $inbox['date_received'];
		$care['candidate'] = $data['candidate'];
		$care['care_result'] = 'Sent';
		$care['type'] = 'send_mail';
		$builder  = $this->db->table(get_db_prefix().'cd_care');
		$builder->insert($care);

		return true;
	}

	/**
	 * send mail list candidate
	 * @param  object $data
	 * @return object
	 */
	public function send_mail_list_candidate($data) {
		$staff_id = get_staff_user_id1();

		$inbox = array();

		$inbox['to'] = implode(',', $data['email']);
		$inbox['sender_name'] = get_staff_full_name1($staff_id);
		$inbox['subject'] = $data['subject'];
		$inbox['body'] = $data['content'];
		$inbox['body'] = $inbox['body'];
		$inbox['date_received'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);

		send_app_mail($data['email'], $inbox['subject'], $inbox['body'] );

		$care = array();
		foreach ($data['candidate'] as $cd) {
			$care['care_time'] = $inbox['date_received'];
			$care['add_from'] = $staff_id;
			$care['add_time'] = $inbox['date_received'];
			$care['candidate'] = $cd;
			$care['care_result'] = 'Sent';
			$care['type'] = 'send_mail';

			$builder  = $this->db->table(get_db_prefix().'cd_care');
			$builder->insert($care);
		}

		return true;
	}

	/**
	 * check candidate interview
	 * @param  object $data
	 * @return object
	 */
	public function check_candidate_interview($data) {
		$data['interview_day'] = to_sql_date1($data['interview_day']);
		$cd = $data['candidate'];

		$from_hours = $data['interview_day'] . ' ' . $data['from_time'] . ':00';
		$to_hours = $data['interview_day'] . ' ' . $data['to_time'] . ':00';

		if (!isset($data['id'])) {
			$list = $this->db->query('SELECT * FROM '.get_db_prefix().'rec_interview ri LEFT JOIN '.get_db_prefix().'cd_interview ON '.get_db_prefix().'cd_interview.interview = ri.id WHERE '.get_db_prefix().'cd_interview.candidate = ' . $cd . ' AND (((ri.from_hours <= "' . $from_hours . '") AND (ri.to_hours >= "' . $from_hours . '")) OR  ((ri.from_hours <= "' . $to_hours . '") AND (ri.to_hours >= "' . $to_hours . '")) OR  ((ri.from_hours >= "' . $from_hours . '") AND (ri.to_hours <= "' . $to_hours . '")) )')->getResultArray();
			return $list;

		} else {
			$lists = $this->db->query('SELECT * FROM '.get_db_prefix().'rec_interview ri LEFT JOIN '.get_db_prefix().'cd_interview ON '.get_db_prefix().'cd_interview.interview = ri.id WHERE '.get_db_prefix().'cd_interview.candidate = ' . $cd . ' AND ri.id != ' . $data['id'] . ' AND (((ri.from_hours <= "' . $from_hours . '") AND (ri.to_hours >= "' . $from_hours . '")) OR  ((ri.from_hours <= "' . $to_hours . '") AND (ri.to_hours >= "' . $to_hours . '")) OR  ((ri.from_hours >= "' . $from_hours . '") AND (ri.to_hours <= "' . $to_hours . '")) )')->getResultArray();
			return $lists;

		}

	}

	/**
	 * get list cd
	 * @return object
	 */

	public function get_list_cd() {
		$builder = $this->db->table(get_db_prefix().'rec_candidate');

		$builder->select('id, CONCAT(candidate_name," ",last_name) as label');
		return $builder->get()->getResultArray();
	}

	/**
	 * get list candidates interview
	 * @param  int $id
	 * @return object
	 */
	public function get_list_candidates_interview($id) {
		return $this->db->query('SELECT * FROM '.get_db_prefix().'cd_interview LEFT JOIN '.get_db_prefix().'rec_candidate on '.get_db_prefix().'rec_candidate.id = '.get_db_prefix().'cd_interview.candidate where '.get_db_prefix().'cd_interview.interview = ' . $id)->getResultArray();
	}

	/**
	 * delete interview schedule
	 * @param  int $id
	 * @return bool
	 */
	public function delete_interview_schedule($id) {
		$builder = $this->db->table(get_db_prefix().'cd_interview');
		$builder->where('interview', $id);
		$builder->delete();

		$builder = $this->db->table(get_db_prefix().'rec_interview');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get interview schedule
	 * @param  string $id
	 * @return object
	 */
	public function get_interview_schedule($id = '') {
		if ($id == '') {
			$builder = $this->db->table(get_db_prefix().'rec_interview');
			return $builder->get()->getResultArray();
		} else {
			$builder = $this->db->table(get_db_prefix().'rec_interview');
			$builder->where('id', $id);
			$intv_sch = $builder->get()->getRow();
			$intv_sch->list_candidate = $this->get_list_candidates_interview($id);

			return $intv_sch;
		}
	}

	/**
	 * add evaluation criteria
	 * @param object $data
	 */
	public function add_evaluation_criteria($data) {

		if($data['criteria_type'] == 'group_criteria'){
			$data['group_criteria'] = 0;
		}
		if(isset($data['files'])){
			unset($data['files']);
		}
		$data['add_from'] = get_staff_user_id1();
		$data['add_date'] = get_my_local_time('Y-m-d');
		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * update evaluation criteria
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_evaluation_criteria($data, $id) {
		if(isset($data['files'])){
			unset($data['files']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		$builder->where('group_criteria', null);
		$builder->update(['criteria_type' => 'group_criteria', 'group_criteria' => 0]);

		if($data['criteria_type'] == 'group_criteria'){
			$data['group_criteria'] = 0;
		}

		if($data['criteria_type'] == 'criteria'){
			$builder = $this->db->table(get_db_prefix().'rec_criteria');
			$builder->where('group_criteria', $id);
			$builder->update(['criteria_type' => 'group_criteria', 'group_criteria' => 0]);
		}

		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		$builder->where('criteria_id', $id);
		
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete evaluation criteria
	 * @param  int $id
	 * @return bool
	 */
	public function delete_evaluation_criteria($id) {
		$affected_rows = 0;
		
		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		$builder->where('group_criteria', $id);
		$rs = $builder->get()->getResultArray();
		foreach ($rs as $value) {
			$builder = $this->db->table(get_db_prefix().'rec_criteria');
			$builder->where('criteria_id', $value['criteria_id']);
			$affected_row = $builder->delete();
			if ($affected_row > 0) {
				$affected_rows++;
			}
		}

		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		$builder->where('criteria_id', $id);
		$affected_row = $builder->delete();
		if ($affected_row > 0) {
			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get group evaluation criteria
	 * @param  string $id
	 * @return object
	 */
	public function get_group_evaluation_criteria($id = '') {
		if ($id == '') {
			$builder = $this->db->table(get_db_prefix().'rec_criteria');
			$builder->where('group_criteria', 0);
			$group = $builder->get()->getResultArray();
		} else {
			$builder = $this->db->table(get_db_prefix().'rec_criteria');

			$builder->where('group_criteria', $id);
			$group = $builder->get()->getRow();
		}
		return $group;
	}

	/**
	 * get evaluation criteria
	 * @param  string $id 
	 * @return [type]     
	 */
	public function get_evaluation_criteria($id = '') {
		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		$builder->where('criteria_id', $id);
		$group = $builder->get()->getRow();
		
		return $group;
	}

	/**
	 * get list child criteria
	 * @return object
	 */
	public function get_list_child_criteria() {
		$list_group = $this->get_group_evaluation_criteria();
		$rs = array();
		$list = array();
		$parent = array();
		foreach ($list_group as $gr) {
			$parent[] = $gr;
			$builder = $this->db->table(get_db_prefix().'rec_criteria');

			$builder->where('group_criteria', $gr['criteria_id']);
			$rs = $builder->get()->getResultArray();
			foreach ($rs as $value) {
				$parent[] = $value;
			}
		}
		return $parent;
	}

	/**
	 * get criteria by group
	 * @param  int $id
	 * @return object
	 */
	public function get_criteria_by_group($id) {
		$builder = $this->db->table(get_db_prefix().'rec_criteria');
		if(is_numeric($id)){
			$builder->where('group_criteria', $id);
		}
		$rs = $builder->get()->getResultArray();
		return $rs;
	}

	/**
	 * add evaluation form
	 * @param object $data
	 */
	public function add_evaluation_form($data) {
		$data['add_from'] = get_staff_user_id1();
		$data['add_date'] = get_my_local_time('Y-m-d');

		if (isset($data['job_position'])) {
			$data['position'] = $data['job_position'];
			unset($data['job_position']);
		}

		if (isset($data['group_criteria'])) {
			$group_criteria = $data['group_criteria'];
			unset($data['group_criteria']);
		}

		if (isset($data['evaluation_criteria'])) {
			$evaluation_criteria = $data['evaluation_criteria'];
			unset($data['evaluation_criteria']);
		}

		if (isset($data['percent'])) {
			$percent = $data['percent'];
			unset($data['percent']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');

		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;

	}

	/**
	 * update evaluation form
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_evaluation_form($data, $id) {

		if (isset($data['job_position'])) {
			$data['position'] = $data['job_position'];
			unset($data['job_position']);
		}

		if (isset($data['group_criteria'])) {
			$group_criteria = $data['group_criteria'];
			unset($data['group_criteria']);
		}

		if (isset($data['evaluation_criteria'])) {
			$evaluation_criteria = $data['evaluation_criteria'];
			unset($data['evaluation_criteria']);
		}

		if (isset($data['percent'])) {
			$percent = $data['percent'];
			unset($data['percent']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');
		$builder->where('form_id', $id);
		$builder->update($data);

		return true;
	}

	/**
	 * delete evaluation form
	 * @param  int $id
	 * @return bool
	 */
	public function delete_evaluation_form($id) {
		$affected_rows = 0;

		$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');
		$builder->where('form_id', $id);
		$affected_row = $builder->delete();
		if ($affected_row > 0) {
			$affected_rows++;
		}

		$builder = $this->db->table(get_db_prefix().'rec_list_criteria');

		$builder->where('evaluation_form', $id);
		$affected_row = $builder->delete();
		if ($affected_row > 0) {
			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get list evaluation form
	 * @param  string $id
	 * @return object
	 */
	public function get_list_evaluation_form($id = '') {
		if ($id == '') {
			$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');
			return $builder->get()->getResultArray();
		} else {
			$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');
			$builder->where('form_id', $id);
			return $builder->get()->getRow();
		}
	}

	/**
	 * get list criteria edit
	 * @param  int $id
	 * @return object
	 */
	public function get_list_criteria_edit($id) {
		$group_criteria = 0;
		$evaluation_criteria = 0;

		$groups = $this->get_group_evaluation_criteria();
		$list_group = $this->db->query('SELECT distinct('.get_db_prefix().'rec_list_criteria.group_criteria) as id, criteria_title FROM '.get_db_prefix().'rec_list_criteria
			LEFT JOIN '.get_db_prefix().'rec_criteria on '.get_db_prefix().'rec_criteria.criteria_id = '.get_db_prefix().'rec_list_criteria.group_criteria where '.get_db_prefix().'rec_list_criteria.evaluation_form = ' . $id)->getResultArray();
		$html = '<div class="new-kpi-group-al">';
		$count_group = 0;
		foreach ($list_group as $gr) {
			$group_criteria++;

			$list_criter = $this->db->query('select evaluation_criteria, criteria_title, percent from '.get_db_prefix().'rec_list_criteria
				left join '.get_db_prefix().'rec_criteria on '.get_db_prefix().'rec_criteria.criteria_id = '.get_db_prefix().'rec_list_criteria.evaluation_criteria
				where '.get_db_prefix().'rec_list_criteria.evaluation_form = ' . $id . ' AND '.get_db_prefix().'rec_list_criteria.group_criteria = ' . $gr['id'])->getResultArray();
			$criterias = $this->get_criteria_by_group($gr['id']);
			$i = 'fa-plus';
			$class = 'success';
			$click = 'new_kpi_group';
			if ($count_group > 0) {
				$i = 'fa-minus';
				$class = 'danger';
				$click = 'remove_kpi_group';
			}

			$html .= '<div id="new_kpi_group" class="col-md-12">
			<div class="row margin-top-10">
			<div class="col-md-12">
			<label for="group_criteria[' . $count_group . ']" class="control-label"><span class="text-danger">* </span>' . app_lang('group_criteria') . '</label>
			<select onchange="group_criteria_change(this)" name="group_criteria[' . $count_group . ']" class="selectpicker" id="group_criteria[' . $count_group . ']" data-width="100%" placeholder="' . app_lang('dropdown_non_selected_tex') . '" required>
			<option value=""></option>';
			foreach ($groups as $kpi_coll) {
				$select = '';
				if ($kpi_coll['criteria_id'] == $gr['id']) {
					$select = 'selected';
				}
				$html .= '<option value="' . $kpi_coll['criteria_id'] . '" ' . $select . '> ' . $kpi_coll['criteria_title'] . '</option>';
			}
			$html .= '</select>
			</div>

			</div>
			<br>
			<div class="row " >

			<div class="col-md-11 new-kpi-al pull-right margin-left-right-20-0">';
			$count_criter = 0;
			foreach ($list_criter as $li) {
				$evaluation_criteria++;

				$l_i = 'fa-plus';
				$l_class = 'success';
				$l_click = 'new_kpi';
				if ($count_criter > 0) {
					$l_i = 'fa-minus';
					$l_class = 'danger';
					$l_click = 'remove_kpi';
				}

				$html .= '<div id ="new_kpi" class="row padding-bottom-5">';

				$html .= '<div class="col-md-7 padding-right-0">
				<label for="evaluation_criteria[' . $count_group . '][' . $count_criter . ']" class="control-label get_id_row " value ="' . $count_criter . '" ><span class="text-danger">* </span>' . app_lang('evaluation_criteria') . '</label>
				<select name="evaluation_criteria[' . $count_group . '][' . $count_criter . ']" class="selectpicker" id="evaluation_criteria[' . $count_group . '][' . $count_criter . ']" data-width="100%" placeholder="' . app_lang('dropdown_non_selected_tex') . '" data-sl-id="e_criteria[' . $count_group . ']" required>
				<option value=""></option>';
				foreach ($criterias as $cr) {
					$select_cr = '';
					if ($cr['criteria_id'] == $li['evaluation_criteria']) {
						$select_cr = 'selected';
					}
					$html .= '<option value="' . $cr['criteria_id'] . '" ' . $select_cr . '> ' . $cr['criteria_title'] . '</option>';
				}

				$html .= '</select>
				</div>

				<div class="col-md-3 padding-right-0">
				<label for="percent[' . $count_group . '][' . $count_criter . ']" class="control-label"><span class="text-danger">* </span>' . app_lang('proportion') . '</label>
				<input type="number" id="percent[' . $count_group . '][' . $count_criter . ']" name="percent[' . $count_group . '][' . $count_criter . ']" class="form-control" min="1" max="100" step="1" value="' . $li['percent'] . '" aria-invalid="false" required>
				</div>
				<div class="col-md-1 lightheight-84-nowrap" name="button_add_kpi">
				<button name="add" class="btn ' . $l_click . ' btn-' . $l_class . ' border-radius-20" data-ticket="true" type="button"><i class="fa ' . $l_i . '"></i></button>
				</div>
				</div>';
				$count_criter++;
			}
			$html .= '</div>

			</div>

			<div class="row">
			<div class="col-md-2 lightheight-84-nowrap" name="button_add_kpi_group">
			<button name="add_kpi_group" class="btn ' . $click . ' btn-' . $class . ' border-radius-20" data-ticket="true" type="button"><i class="fa ' . $i . '"></i></button>
			</div>
			</div>

			</div>';

			$count_group++;
		}

		$result = [];
		$result['html'] = $html;
		$result['group_criteria'] = $group_criteria;
		$result['evaluation_criteria'] = $evaluation_criteria;

		return $result;
	}

	/**
	 * get evaluation form by position
	 * @param  string $position
	 * @return object
	 */
	public function get_evaluation_form_by_position($position = '') {
		$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');
		$builder->where('position', $position);
		$e_form = $builder->get()->getRow();

		if (!isset($e_form)) {
			$builder = $this->db->table(get_db_prefix().'rec_evaluation_form');
			$builder->where('position', 0);
			$builder->orWhere('position', null);
			$e_form = $builder->get()->getRow();
		}

		if ($e_form) {
			$rs['groups'] = $this->db->query('SELECT distinct('.get_db_prefix().'rec_list_criteria.group_criteria) as id, criteria_title FROM '.get_db_prefix().'rec_list_criteria
				LEFT JOIN '.get_db_prefix().'rec_criteria on '.get_db_prefix().'rec_criteria.criteria_id = '.get_db_prefix().'rec_list_criteria.group_criteria where '.get_db_prefix().'rec_list_criteria.evaluation_form = ' . $e_form->form_id)->getResultArray();

			$rs['criteria'] = $this->db->query('select '.get_db_prefix().'rec_list_criteria.group_criteria as group_cr, evaluation_criteria, criteria_title, percent from '.get_db_prefix().'rec_list_criteria
				left join '.get_db_prefix().'rec_criteria on '.get_db_prefix().'rec_criteria.criteria_id = '.get_db_prefix().'rec_list_criteria.evaluation_criteria
				where '.get_db_prefix().'rec_list_criteria.evaluation_form = ' . $e_form->form_id)->getResultArray();
			return $rs;
		} else {
			return '';
		}

	}

	/**
	 * get cd evaluation
	 * @param  object $candidate
	 * @return object
	 */
	public function get_cd_evaluation($candidate) {
		$builder = $this->db->table(get_db_prefix().'rec_cd_evaluation');
		$builder->where('candidate', $candidate);
		return $builder->get()->getResultArray();
	}

	/**
	 * get interview by candidate
	 * @param  object $candidate
	 * @return object
	 */
	public function get_interview_by_candidate($candidate) {
		return $this->db->query('SELECT * FROM '.get_db_prefix().'cd_interview LEFT JOIN '.get_db_prefix().'rec_interview on '.get_db_prefix().'rec_interview.id = '.get_db_prefix().'cd_interview.interview where '.get_db_prefix().'cd_interview.candidate = ' . $candidate)->getResultArray();
	}

	/**
	 * change status candidate
	 * @param  int $status
	 * @param  int $id
	 * @return bool
	 */
	public function change_status_candidate($status, $id) {
		$builder = $this->db->table(get_db_prefix().'rec_candidate');

		$builder->where('id', $id);
		$affected_rows = $builder->update(['status' => $status]);
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * add setting tranfer
	 * @param object $data
	 */
	public function add_setting_tranfer($data) {
		if(isset($data['file_names'])){
			unset($data['file_names']);
		}
		if(isset($data['file_sizes'])){
			unset($data['file_sizes']);
		}

		$data['add_from'] = get_staff_user_id1();
		$data['add_date'] = get_my_local_time('Y-m-d');
		if (isset($data['email_to'])) {
			$data['email_to'] = implode(',', $data['email_to']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_set_transfer_record');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		if (isset($insert_id)) {
			return $insert_id;
		}
	}

	/**
	 * update setting tranfer
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_setting_tranfer($data, $id) {
		if(isset($data['file_names'])){
			unset($data['file_names']);
		}
		if(isset($data['file_sizes'])){
			unset($data['file_sizes']);
		}
		if(isset($data['delete_file'])){
			unset($data['delete_file']);
		}

		$rs = 0;
		if (isset($data['email_to'])) {
			$data['email_to'] = implode(',', $data['email_to']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_set_transfer_record');
		$builder->where('set_id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			$rs++;
		}

		if ($rs > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete setting tranfer
	 * @param  int $id
	 * @return object
	 */
	public function delete_setting_tranfer($id) {

		$arr_files = [];
		$list_set_transfer = $this->get_list_set_transfer($id);
		if($list_set_transfer){
			$files = unserialize($list_set_transfer->files);
			foreach ($files as $key => $value) {
				$arr_files[] = $value['file_name'];
			}
		}
		
		if(count($arr_files) > 0){
			re_delete_company_files(SET_TRANSFER_UPLOAD, $arr_files);
		}

		
		$rs = 0;
		$builder = $this->db->table(get_db_prefix().'rec_set_transfer_record');
		$builder->where('set_id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			$rs++;
		}

		if ($rs > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get list set transfer
	 * @param  boolean $id
	 * @return object
	 */
	public function get_list_set_transfer($id = false) {
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'rec_set_transfer_record');
			$builder->where('set_id', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'rec_set_transfer_record');
			return $builder->get()->getResultArray();
		}
	}

	/**
	 * get step transfer setting
	 * @return object
	 */
	public function get_step_transfer_setting() {
		return $this->db->query('SELECT * FROM '.get_db_prefix().'rec_set_transfer_record order by '.get_db_prefix().'rec_set_transfer_record.order ASC;')->getResultArray();
	}

	/**
	 * action transfer hr
	 * @param  object $data
	 * @return object
	 */
	public function action_transfer_hr($data) {

		$this->db->where('rel_id', $data['id']);
		$this->db->where('rel_type', 'rec_set_transfer');
		$file = $this->db->get(get_db_prefix() . 'files')->getRow();

		$inbox = array();

		$inbox['to'] = $data['email'];
		$inbox['sender_name'] = get_option('companyname');
		$inbox['subject'] = _strip_tags($data['subject']);
		$inbox['body'] = _strip_tags($data['content']);
		$inbox['body'] = nl2br_save_html($inbox['body']);
		$inbox['date_received'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		$inbox['from_email'] = get_option('smtp_email');

		if (strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0) {

			$ci = &get_instance();
			$ci->email->initialize();
			$ci->load->library('email');
			$ci->email->clear(true);
			$ci->email->from($inbox['from_email'], $inbox['sender_name']);
			$ci->email->to($inbox['to']);

			$ci->email->subject($inbox['subject']);
			$ci->email->message($inbox['body']);

			if ($file) {
				$attachment_url = site_url(RECRUITMENT_PATH . 'set_transfer/' . $data['id'] . '/' . $file->file_name);
				$ci->email->attach($attachment_url);
			}

			$ci->email->send(true);
		}
	}

	/**
	 * get rec dashboard count
	 * @return object
	 */
	public function get_rec_dashboard_count() {
		$rs = [];

		$total = $this->db->query('Select * from '.get_db_prefix().'rec_campaign')->getResultArray();
		$inprogress = $this->db->query('Select * from '.get_db_prefix().'rec_campaign where cp_status = 3')->getResultArray();
		$planning = $this->db->query('Select * from '.get_db_prefix().'rec_campaign where cp_status = 1')->getResultArray();
		$finish = $this->db->query('Select * from '.get_db_prefix().'rec_campaign where cp_status = 4')->getResultArray();
		$candidate_need = $this->db->query('Select amount_recruiment from '.get_db_prefix().'rec_proposal')->getResultArray();
		$recruited = $this->db->query('Select * from '.get_db_prefix().'rec_candidate where status = 6')->getResultArray();
		$upcomming_intv = $this->get_upcoming_interview();

		$rs['candidate_need'] = 0;
		foreach ($candidate_need as $cd) {
			$rs['candidate_need'] += $cd['amount_recruiment'];
		}

		$rs['recruiting'] = 0;
		foreach ($inprogress as $cd) {
			$rs['recruiting'] += $cd['cp_amount_recruiment'];
		}

		$rs['upcomming_intv'] = count($upcomming_intv);
		$rs['recruited'] = count($recruited);
		$rs['total'] = count($total);
		$rs['inprogress'] = count($inprogress);
		$rs['planning'] = count($planning);
		$rs['finish'] = count($finish);

		return $rs;
	}

	/**
	 * rec plan chart by status
	 * @return object
	 */
	public function rec_plan_chart_by_status() {
		$plans = $this->get_rec_proposal();

		$chart = [];
		$status_1 = ['name' => app_lang('planning'), 'color' => '#777', 'y' => 0, 'z' => 100];
		$status_2 = ['name' => app_lang('approved'), 'color' => '#ff6f00', 'y' => 0, 'z' => 100];
		$status_3 = ['name' => app_lang('made_finish'), 'color' => '#03a9f4', 'y' => 0, 'z' => 100];
		$status_4 = ['name' => app_lang('reject'), 'color' => '#fc2d42', 'y' => 0, 'z' => 100];

		foreach ($plans as $pl) {

			if ($pl['status'] == 1) {
				$status_1['y'] += 1;
			} elseif ($pl['status'] == 2) {
				$status_2['y'] += 1;
			} elseif ($pl['status'] == 3) {
				$status_3['y'] += 1;
			} elseif ($pl['status'] == 4) {
				$status_4['y'] += 1;
			}

		}

		if ($status_1['y'] > 0) {
			array_push($chart, $status_1);
		}
		if ($status_2['y'] > 0) {
			array_push($chart, $status_2);
		}
		if ($status_3['y'] > 0) {
			array_push($chart, $status_3);
		}
		if ($status_4['y'] > 0) {
			array_push($chart, $status_4);
		}

		return $chart;
	}

	/**
	 * rec campaign chart by status
	 * @return object
	 */
	public function rec_campaign_chart_by_status() {
		$campaign = $this->get_rec_campaign();

		$chart = [];
		$status_1 = ['name' => app_lang('planning'), 'color' => '#c53da9', 'y' => 0, 'z' => 100];
		$status_2 = ['name' => app_lang('in_progress'), 'color' => '#28B8DA', 'y' => 0, 'z' => 100];
		$status_3 = ['name' => app_lang('finish'), 'color' => '#84C529', 'y' => 0, 'z' => 100];
		$status_4 = ['name' => app_lang('cancel'), 'color' => '#fb3b3b', 'y' => 0, 'z' => 100];

		foreach ($campaign as $cp) {

			if ($cp['cp_status'] == 1) {
				$status_1['y'] += 1;
			} elseif ($cp['cp_status'] == 3) {
				$status_2['y'] += 1;
			} elseif ($cp['cp_status'] == 4) {
				$status_3['y'] += 1;
			} elseif ($cp['cp_status'] == 5) {
				$status_4['y'] += 1;
			}

		}

		if ($status_1['y'] > 0) {
			array_push($chart, $status_1);
		}
		if ($status_2['y'] > 0) {
			array_push($chart, $status_2);
		}
		if ($status_3['y'] > 0) {
			array_push($chart, $status_3);
		}
		if ($status_4['y'] > 0) {
			array_push($chart, $status_4);
		}

		return $chart;
	}

	/**
	 * get upcoming interview
	 * @return object
	 */
	public function get_upcoming_interview() {
		return $this->db->query('select * from '.get_db_prefix().'rec_interview where from_hours >= "' . to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true) . '"')->getResultArray();
	}

	/**
	 * get form
	 * @param  string $where
	 * @return object
	 */
	public function get_form($where) {
		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->where($where);
		return $builder->get()->getRow();
	}

	/**
	 * add recruitment channel
	 * @param [object $data
	 */
	public function add_recruitment_channel($data) {

		if (isset($data['r_form_name'])) {
			$r_form_name = $data['r_form_name'];
		}

		$data['form_data'] = preg_replace('/=\\\\/m', "=''", $data['form_data']);
		if (isset($data['notify_lead_imported'])) {
			$data['notify_lead_imported'] = 1;
		} else {
			$data['notify_lead_imported'] = 0;
		}

		$data = $this->convert_data_campaign($data);
		$data['success_submit_msg'] = nl2br($data['success_submit_msg']);
		$data['form_key'] = app_generate_hash();

		if (isset($data['notify_ids_staff']) && $data['notify_ids_staff'] != null) {
			$data['notify_ids_staff'] = implode(',', $data['notify_ids_staff']);

		}

		if (isset($data['notify_ids_roles']) && $data['notify_ids_roles'] != null) {
			$data['notify_ids_roles'] = implode(',', $data['notify_ids_roles']);

		}

		$data['r_form_name'] = $r_form_name;

		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;

	}

	/**
	 * convert data campaign
	 * @param  object $data
	 * @return object
	 */
	public function convert_data_campaign($data) {

		$data_out['rec_campaign_id'] = isset($data['rec_campaign_id']) ? $data['rec_campaign_id'] : '';
		$data_out['form_type'] = isset($data['form_type']) ? $data['form_type'] : '';

		$data_out['lead_status'] = isset($data['lead_status']) ? $data['lead_status'] : '';
		$data_out['notify_ids_staff'] = isset($data['notify_ids_staff']) ? $data['notify_ids_staff'] : '';
		$data_out['notify_ids_roles'] = isset($data['notify_ids_roles']) ? $data['notify_ids_roles'] : '';
		$data_out['form_key'] = isset($data['form_key']) ? $data['form_key'] : '';
		$data_out['notify_lead_imported'] = isset($data['notify_lead_imported']) ? $data['notify_lead_imported'] : '';
		$data_out['notify_type'] = isset($data['notify_type']) ? $data['notify_type'] : '';
		$data_out['notify_ids'] = isset($data['notify_ids']) ? $data['notify_ids'] : '';
		$data_out['responsible'] = isset($data['responsible']) ? $data['responsible'] : '';
		$data_out['form_data'] = isset($data['form_data']) ? $data['form_data'] : '';
		$data_out['recaptcha'] = isset($data['recaptcha']) ? $data['recaptcha'] : '';
		$data_out['submit_btn_name'] = isset($data['submit_btn_name']) ? $data['submit_btn_name'] : '';
		$data_out['success_submit_msg'] = isset($data['success_submit_msg']) ? $data['success_submit_msg'] : '';
		$data_out['language'] = isset($data['language']) ? $data['language'] : '';
		$data_out['allow_duplicate'] = isset($data['allow_duplicate']) ? $data['allow_duplicate'] : '';
		$data_out['mark_public'] = isset($data['mark_public']) ? $data['mark_public'] : '';
		$data_out['track_duplicate_field'] = isset($data['track_duplicate_field']) ? $data['track_duplicate_field'] : '';
		$data_out['track_duplicate_field_and'] = isset($data['track_duplicate_field_and']) ? $data['track_duplicate_field_and'] : '';
		$data_out['create_task_on_duplicate'] = isset($data['create_task_on_duplicate']) ? $data['create_task_on_duplicate'] : '';

		return $data_out;
	}

	/**
	 * get recruitment channel
	 * @param  boolean $id
	 * @return object
	 */
	public function get_recruitment_channel($id = false) {
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
			$builder->where('id', $id);

			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'rec_campaign_form_web')->getResultArray();
		}

	}

	/**
	 * delete recruitment channel
	 * @param  int $id
	 * @return bool
	 */
	public function delete_recruitment_channel($id) {
		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * count cv from recruitment channel
	 * @param  int $id
	 * @param  object $recruitment_channel
	 * @return object
	 */
	public function count_cv_from_recruitment_channel($id, $recruitment_channel) {
		//get recruitment campaign from recruitment channel
		$campaign_ids = [];
		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->where('rec_channel_form_id', $id);
		$rec_campaign = $builder->get()->getResultArray();
		if(count($rec_campaign) > 0){
			foreach ($rec_campaign as $key => $value) {
				$campaign_ids[] = $value['cp_id'];
			}
		}

		if(count($campaign_ids) > 0){
			$builder = $this->db->table(get_db_prefix().'rec_candidate');
			$builder->where('rec_campaign IN (' . implode(",", $campaign_ids) . ')');
			$builder->where('recruitment_channel', $recruitment_channel);
			$total_rec_candidate = $builder->get()->getResultArray();

			return count($total_rec_candidate);
		}
		return 0;
	}

	/**
	 * count row all candidate profile
	 * @return object
	 */
	public function count_row_all_candidate_profile() {
		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$rec_candidate = $builder->get()->getResultArray();
		return count($rec_candidate);
	}

	/**
	 * add candidate forms
	 * @param object $data
	 * @param string $form_key
	 */
	public function add_candidate_forms($data, $form_key = '') {

		//remove costomfields if exist
		foreach ($data as $key => $value) {
			if(preg_match('/^form-cf-/', $key)){
				unset($data[$key]);
			}    
		}
		

		//Remove terms conditions checkbox
		if (isset($data['accept_terms_and_conditions'])) {
			unset($data['accept_terms_and_conditions']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->where('form_key', $form_key);
		$rec_campaign_form_web = $builder->get()->getRow();
		$count_row = $this->count_row_all_candidate_profile();

		if (isset($data['birthday'])) {
			$data['birthday'] = $data['birthday'];
			if (!$this->check_format_date($data['birthday'])) {
				$data['birthday'] = to_sql_date1($data['birthday']);
			}

		}

		if (isset($data['days_for_identity'])) {

			$data['days_for_identity'] = $data['days_for_identity'];
			if (!$this->check_format_date($data['days_for_identity'])) {
				$data['days_for_identity'] = to_sql_date1($data['days_for_identity']);
			}
		}

		/*general candidate code*/
		$sql_where = 'SELECT * FROM ' . get_db_prefix() . 'rec_candidate order by id desc limit 1';
		$last_candidate_id = $this->db->query($sql_where)->getRow();

		if ($last_candidate_id) {
			$last_id = (int) ($last_candidate_id->id) + 1;
			$data['candidate_code'] = "WEB_" . $last_id;
		} else {
			$data['candidate_code'] = "WEB_1";
		}

		$data['recruitment_channel'] = 1; /*type: forms*/

		if (isset($data['rec_campaignid'])) {
			$data['rec_campaign'] = $data['rec_campaignid'];
			unset($data['rec_campaignid']);

		}

		if (isset($data['desired_salary'])) {
			$data['desired_salary'] = $data['desired_salary'];
		}

		if ($rec_campaign_form_web) {
			$data['status'] = $rec_campaign_form_web->lead_status;
		}

		$data['date_add'] = get_my_local_time('Y-m-d');

		if (isset($data['from_date'])) {
			$from_date = $data['from_date'];

			if (!$this->check_format_date($data['from_date'])) {
				$from_date = to_sql_date1($data['from_date']);
			}

			unset($data['from_date']);
		} else {
			$from_date = '';
		}

		if (isset($data['to_date'])) {
			$to_date = $data['to_date'];

			if (!$this->check_format_date($data['to_date'])) {
				$to_date = to_sql_date1($data['to_date']);
			}

			unset($data['to_date']);
		} else {
			$to_date = '';
		}

		if (isset($data['company'])) {
			$company = $data['company'];
			unset($data['company']);
		} else {
			$company = '';

		}

		if (isset($data['contact_person'])) {
			$contact_person = $data['contact_person'];
			unset($data['contact_person']);
		} else {
			$contact_person = '';

		}

		if (isset($data['salary'])) {
			$salary = $data['salary'];
			unset($data['salary']);
		} else {
			$salary = '';
		}

		if (isset($data['reason_quitwork'])) {
			$reason_quitwork = $data['reason_quitwork'];
			unset($data['reason_quitwork']);
		} else {

			$reason_quitwork = '';
		}

		if (isset($data['job_description'])) {
			$job_description = $data['job_description'];
			unset($data['job_description']);
		} else {
			$job_description = '';

		}

		if (isset($data['literacy_from_date'])) {
			$literacy_from_date = $data['literacy_from_date'];

			if (!$this->check_format_date($data['literacy_from_date'])) {
				$literacy_from_date = to_sql_date1($data['literacy_from_date']);
			}

			unset($data['literacy_from_date']);

		} else {
			$literacy_from_date = '';
		}

		if (isset($data['literacy_to_date'])) {
			$literacy_to_date = $data['literacy_to_date'];

			if (!$this->check_format_date($data['literacy_to_date'])) {
				$literacy_to_date = to_sql_date1($data['literacy_to_date']);
			}

			unset($data['literacy_to_date']);
		} else {
			$literacy_to_date = '';
		}

		if (isset($data['diploma'])) {
			$diploma = $data['diploma'];
			unset($data['diploma']);
		}

		if (isset($data['training_places'])) {
			$training_places = $data['training_places'];
			unset($data['training_places']);
		}

		if (isset($data['specialized'])) {
			$specialized = $data['specialized'];
			unset($data['specialized']);
		}

		if (isset($data['training_form'])) {
			$training_form = $data['training_form'];
			unset($data['training_form']);
		}

		if (isset($data['relationship'])) {
			$relationship = $data['relationship'];
			unset($data['relationship']);
		}

		if (isset($data['name'])) {
			$name = $data['name'];
			unset($data['name']);
		}

		if (isset($data['fi_birthday'])) {
			$fi_birthday = $data['fi_birthday'];

			if (!$this->check_format_date($data['fi_birthday'])) {
				$fi_birthday = to_sql_date1($data['fi_birthday']);
			}

			unset($data['fi_birthday']);
		} else {
			$fi_birthday = '';
		}

		if (isset($data['job'])) {
			$job = $data['job'];
			unset($data['job']);
		}

		if (isset($data['address'])) {
			$address = $data['address'];
			unset($data['address']);
		}

		if (isset($data['phone'])) {
			$phone = $data['phone'];
			unset($data['phone']);
		}
		if (isset($data['position'])) {
			$position_id = $data['position'];
			unset($data['position']);
		}
		if (isset($data['year_experience'])) {
			$data['year_experience'] = $data['year_experience'];
		}

		if (isset($data['key'])) {
			unset($data['key']);
		}

		if (isset($data['key'])) {
			unset($data['key']);
		}
		if (isset($data['zip'])) {
			unset($data['zip']);
		}

		if (isset($data['position_id'])) {
			$data['position_id'] = $data['position_id'];
			unset($data['position_id']);
		}

		if (isset($data['skill'])) {
			$data['skill'] = implode(',', $data['skill']);
		}

		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if ($insert_id) {

			if (isset($position_id)) {
				$builder = $this->db->table(get_db_prefix().'cd_work_experience');

				$builder->insert([
					'candidate' => $insert_id,
					'from_date' => $from_date,
					'to_date' => $to_date,
					'company' => $company,
					'position' => $position_id,
					'contact_person' => $contact_person,
					'salary' => $salary,
					'reason_quitwork' => $reason_quitwork,
					'job_description' => $job_description,
				]);

			}

			if (isset($diploma)) {

				$builder = $this->db->table(get_db_prefix().'cd_literacy');
				$builder->insert([

					'candidate' => $insert_id,
					'literacy_from_date' => $literacy_from_date,
					'literacy_to_date' => $literacy_to_date,
					'diploma' => isset($diploma) ? $diploma : '',
					'training_places' => isset($training_places) ? $training_places : '',
					'specialized' => isset($specialized) ? $specialized : '',
					'training_form' => isset($training_form) ? $training_form : '',
				]);
			}

			if (isset($relationship)) {
				$builder = $this->db->table(get_db_prefix().'cd_family_infor');
				$builder->insert([
					'candidate' => $insert_id,
					'relationship' => isset($training_form) ? $cd_family_infor : '',
					'name' => isset($name) ? $name : '',
					'fi_birthday' => $fi_birthday,
					'job' => isset($job) ? $job : '',
					'address' => isset($address) ? $address : '',
					'phone' => isset($phone) ? $phone : '',
				]);
			}

			/*send notifi to personal related*/

			if ($rec_campaign_form_web->notify_lead_imported == 1) {

				$additional_data = '';
				$mes = 'notify_new_candidate';
				$link = 'recruitment/candidate/' . $insert_id;

				if ($rec_campaign_form_web->notify_type == 'assigned') {
					if(1==2){
						$notified = add_notification([
							'description' => $mes,
							'touserid' => $rec_campaign_form_web->responsible,
							'link' => $link,
							'additional_data' => serialize([
								$additional_data,
							]),
						]);
						if ($notified) {
							pusher_trigger_notification([$rec_campaign_form_web->responsible]);
						}
					}

				} elseif ($rec_campaign_form_web->notify_type == 'roles') {

					$str_roles = $rec_campaign_form_web->notify_ids_roles;
					if (strlen($str_roles) > 0) {

						$sql_role = 'role_id IN (' . $str_roles . ')';

						$builder = $this->db->table(get_db_prefix().'users');
						$builder->where($sql_role);
						$arr_staff = $builder->get()->getResultArray();

						if (count($arr_staff) > 0) {
							foreach ($arr_staff as $staff_value) {
								if(1==2){

									$notified = add_notification([
										'description' => $mes,
										'touserid' => $staff_value['staffid'],
										'link' => $link,
										'additional_data' => serialize([
											$additional_data,
										]),
									]);

									if ($notified) {
										pusher_trigger_notification([$staff_value['staffid']]);
									}
								}

							}
						}

					}

				} elseif ($rec_campaign_form_web->notify_type == 'specific_staff') {
					$str_staff = $rec_campaign_form_web->notify_ids_staff;
					if (strlen($str_staff) > 0) {
						$arr_staff = explode(",", $str_staff);
						foreach ($arr_staff as $staff_value) {
							if(1==2){

								$notified = add_notification([
									'description' => $mes,
									'touserid' => $staff_value,
									'link' => $link,
									'additional_data' => serialize([
										$additional_data,
									]),
								]);

								if ($notified) {
									pusher_trigger_notification([$staff_value]);
								}
							}

						}

					}

				}
			}

			return $insert_id;
		}

	}

	/**
	 * update recruitment channel
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_recruitment_channel($data, $id) {
		if (isset($data['r_form_name'])) {
			$r_form_name = $data['r_form_name'];
		}

		$data['form_data'] = preg_replace('/=\\\\/m', "=''", $data['form_data']);
		if (isset($data['notify_lead_imported'])) {
			$data['notify_lead_imported'] = 1;
		} else {
			$data['notify_lead_imported'] = 0;
		}

		$data = $this->convert_data_campaign($data);
		$data['success_submit_msg'] = nl2br($data['success_submit_msg']);
		if(isset($data['form_key'])){
			unset($data['form_key']);
		}

		if (isset($data['notify_ids_staff']) && $data['notify_ids_staff'] != null) {
			$data['notify_ids_staff'] = implode(',', $data['notify_ids_staff']);

		}

		if (isset($data['notify_ids_roles']) && $data['notify_ids_roles'] != null) {
			$data['notify_ids_roles'] = implode(',', $data['notify_ids_roles']);

		}

		$data['r_form_name'] = $r_form_name;
		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->where('id', $id);
		$affected_rows = $builder->update($data);

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get calendar interview schedule data
	 * @param  [type]  $start
	 * @param  [type]  $end
	 * @param  boolean $filters
	 * @return [type]
	 */
	public function get_calendar_interview_schedule_data($start, $end, $data) {
		$data = [];

		if(is_admin()){
			/*view global*/
		}else{
			/*View own*/
			$this->db->where('(FIND_IN_SET('.get_staff_user_id1().', '.get_db_prefix().'rec_interview.interviewer) OR ('.get_db_prefix().'rec_interview.added_from = '.get_staff_user_id1().'))');
		}

		$list_interview = $this->db->get(get_db_prefix() . 'rec_interview')->getResultArray();

		foreach ($list_interview as $interview) {

			$calendar['title'] = $interview['is_name'];
			$calendar['color'] = '#7cb342';
			$calendar['_tooltip'] = $interview['is_name'] . "\n" . ' Day: ' . to_decimal_format($interview['interview_day']) . "\n" . ' Start: ' . ($interview['from_time']) . ' End: ' . ($interview['to_time']);

			$calendar['url'] = site_url('recruitment/interview_schedule/' . $interview['id']);
			$calendar['start'] = $interview['from_hours'];
			$calendar['end'] = $interview['to_hours'];
			array_push($data, $calendar);
		}

		return $data;

	}

	/**
	 * check format date Y-m-d
	 *
	 * @param      String   $date   The date
	 *
	 * @return     boolean
	 */
	public function check_format_date($date) {
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * do kanban query
	 * @param  [type]  $status
	 * @param  string  $search
	 * @param  integer $page
	 * @param  boolean $count
	 * @param  array   $where
	 * @return [type]
	 */
	public function do_kanban_query($status, $search = '', $page = 1, $count = false, $where = []) {
		$candidates_profile_limit = 50;
		$candidate_where = '';

		$this->db->select('*');

		$this->db->from(get_db_prefix() . 'rec_candidate');
		$this->db->where('status', $status);

		$this->db->where($where);

		if ($candidate_where != '') {
			$this->db->where($candidate_where);
		}

		$this->db->orderBy('id', 'desc');

		if ($count == false) {
			if ($page > 1) {
				$page--;
				$position = ($page * $candidates_profile_limit);
				$this->db->limit($candidates_profile_limit, $position);
			} else {
				$this->db->limit($candidates_profile_limit);
			}
		}

		if ($count == false) {
			return $this->db->get()->getResultArray();
		}

		return $this->db->count_all_results();
	}

	/**
	 * get recruitment channel form rec campaingn
	 * @param  integer $id
	 * @return array
	 */
	public function get_recruitment_channel_form_campaingn($campaingn_id) {
		$form_id = '';
		/*get form id from rec_campain*/
		$rec_campain_value = $this->get_rec_campaign($campaingn_id);
		if ($rec_campain_value) {
			$form_id = $rec_campain_value->rec_channel_form_id;
		}

		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->where('id', $form_id);
		$data = $builder->get()->getRow();
		return $data;

	}

	/**
	 * get skill
	 * @param  boolean $id
	 * @return object
	 */
	public function get_skill($id = false) {

		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'rec_skill');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'rec_skill')->getResultArray();
		}

	}

	/**
	 * add skill
	 * @param object $data
	 */
	public function add_skill($data) {
		$builder = $this->db->table(get_db_prefix().'rec_skill');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * update skill
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_skill($data, $id) {
		$builder = $this->db->table(get_db_prefix().'rec_skill');
		$builder->where('id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete skill
	 * @param  int $id
	 * @return bool
	 */
	public function delete_skill($id) {
		$builder = $this->db->table(get_db_prefix().'rec_skill');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * do kanban query
	 * @param  [type]  $status
	 * @param  string  $search
	 * @param  integer $page
	 * @param  boolean $count
	 * @param  array   $where
	 * @return [type]
	 */
	public function do_recruitment_portal_search($status, $search = '', $page = 1, $count = false, $where = []) {

		$rec_campaign_limit = 10;

		$rec_campaign_where = '';

		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->select('*,' . get_db_prefix() . 'rec_campaign.company_id,'.get_db_prefix() . 'rec_job_position.position_name as position_name');

		$builder->join(get_db_prefix() . 'rec_job_position', '' . get_db_prefix() . 'rec_job_position.position_id = ' . get_db_prefix() . 'rec_campaign.cp_position', 'left');

		$builder->join(get_db_prefix() . 'rec_company', '' . get_db_prefix() . 'rec_campaign.company_id = ' . get_db_prefix() . 'rec_company.id', 'left');

		$builder->join(get_db_prefix() . 'job_industry', '' . get_db_prefix() . 'rec_job_position.industry_id = ' . get_db_prefix() . 'job_industry.id', 'left');

		$builder->where('cp_status', '3');

		$builder->groupStart();

		$builder->like('campaign_code', $search);
		$builder->orLike('campaign_name', $search);
		$builder->orLike('cp_proposal', $search);
		$builder->orLike('position_name', $search);

		$builder->orLike('cp_form_work', $search);
		$builder->orLike('cp_form_work', str_replace(' ', '_', $search));
		$builder->orLike('cp_workplace', $search);
		$builder->orLike('cp_salary_from', $search);
		$builder->orLike('cp_salary_to', $search);
		$builder->orLike('cp_ages_from', $search);
		$builder->orLike('cp_ages_to', $search);
		$builder->orLike('cp_gender', $search);
		$builder->orLike('cp_literacy', $search);

		$builder->orLike('cp_experience', $search);
		$builder->orLike('cp_experience', str_replace(' ', '_', $search));
		$builder->orLike('company_name', $search);
		$builder->orLike('company_industry', $search);
		$builder->orLike('company_address', $search);
		$builder->orLike('industry_name', $search);
		$builder->orLike('industry_description', $search);

		$builder->groupEnd();

		$builder->where($where);

		if ($rec_campaign_where != '') {
			$builder->where($rec_campaign_where);
		}

		$builder->orderBy('cp_id', 'desc');

		if ($count == false) {
			if ($page > 1) {
				$page--;
				$position = ($page * $rec_campaign_limit);
				$builder->limit($rec_campaign_limit, $position);
			} else {
				$builder->limit($rec_campaign_limit);
			}
		}

		if ($count == false) {
			$data = $builder->get()->getResultArray();

			/*get company logo*/
			foreach ($data as $key => $value) {

				$data[$key]['company_logo'] = get_file_uri('plugins/Recruitment/Uploads/no_logo.jpg');
				$data[$key]['alt_logo'] = 'no_logo.jpg';

				if (($value['company_id'] != '') && ($value['company_id'] != 0)) {
					$builder = $this->db->table(get_db_prefix().'files');
					$builder->where('rel_id', $value['company_id']);
					$builder->where('rel_type', "rec_company");
					$logo = $builder->get()->getRow();
					if ($logo) {
						$data[$key]['company_logo'] = get_file_uri('plugins/Recruitment/Uploads/company_images/'.$value['company_id'] . '/' . $logo->file_name);

						$data[$key]['alt_logo'] = $logo->file_name;

					}

				}
			}
			return $data;

		}

		return count($builder->get()->getResultArray());
	}

	/**
	 * [do_recruitment_portal_search
	 * @param  [type]  $status
	 * @param  string  $search
	 * @param  integer $page
	 * @param  boolean $count
	 * @param  array   $where
	 * @return [type]
	 */
	public function do_recruitment_show_more_job($status, $search = '', $page = 1, $count = false, $where = []) {

		$arr_job = $this->do_recruitment_portal_search($status, $search, $page, $count, $where = []);

		$string_job = '';
		if (count($arr_job) > 0) {
			foreach ($arr_job as $key => $rec_value) {

				$string_job .= '<div class="job" id="job_68268">';

				$string_job .= '<div class="row">';
				$string_job .= '<div class="col-md-12">';
				$string_job .= '<div class="row">';

				$string_job .= '<div class="job_content col-md-12">';

				if (!isset($rec_value["company_id"]) || ($rec_value["company_id"] == "0")) {

					$string_job .= '<div class="job-company-logo col-md-2 hide">';
				} else {
					$string_job .= '<div class="job-company-logo col-md-2 ">';

				}

				$string_job .= '<img class="images_w_table" src="' . site_url($rec_value['company_logo']) . '" alt="' . $rec_value['alt_logo'] . '">';
				$string_job .= '</div>';
				if (!isset($rec_value["company_id"]) || ($rec_value["company_id"] == "0")) {
					$string_job .= '<div class="job__description col-md-7 job__description_margin">';

				} else {
					$string_job .= '<div class="job__description col-md-7 ">';

				}

				$string_job .= '<div class="job__body">';
				$string_job .= '<div class="details">';

				$string_job .= '<h2 class="title"><a class="bold a-color" data-controller="utm-tracking" href="' . site_url("recruitment_portal/job_detail/" . $rec_value['cp_id']) . '">' . $rec_value['campaign_name'] . '</a>';
				$string_job .= '</h2>';

				$string_job .= '<div class="salary not-signed-in">';

				$string_job .= '<a class="view-salary text-muted " data-toggle="modal" data-target="#sign-in-modal" rel="nofollow" href="#">' . app_lang($rec_value['company_name']) . '</a>';
				$string_job .= '</div>';

				$string_job .= '<div class="salary not-signed-in">';

				$string_job .= '<div class="job-bottom">';
				$string_job .= '<div class="tag-list ">';
				if ($rec_value['cp_form_work']) {
					$string_job .= '<a class="job__skill ilabel mkt-track ' . $rec_value['cp_form_work'] . '-color" data-controller="utm-tracking" href="#">
					<span>
					' . app_lang($rec_value['cp_form_work']) . '
					</span>
					</a>';
				}

				$string_job .= '<a class="job__skill ilabel-cp-workplace  mkt-track " data-controller="utm-tracking" href="#">

				<span> - ' . $rec_value['cp_workplace'] . '</span>
				</a>';

				$string_job .= '</div>';

				$string_job .= '</div>';

				$string_job .= '</div>';

				$string_job .= '<div class="salary not-signed-in">';

				$string_job .= '<h5 class="view-salary bold " data-toggle="modal" data-target="#sign-in-modal" rel="nofollow" href="#">' . app_lang($rec_value['position_name']) . '</h5>';
				$string_job .= '</div>';

				$string_job .= '<div class="job-description">';

				$string_job .= '<p>' . $rec_value['cp_job_description'] . ' ...' . '</p>';
				$string_job .= '</div>';

				$string_job .= '</div>';
				$string_job .= '</div>';

				$string_job .= '</div>';

				$string_job .= '<div class="city_and_posted_date hidden-xs col-md-3">';

				$string_job .= '<div class="feature-view_detail new text ">';
				$string_job .= '<a class="bold a-color text-uppercase" data-controller="utm-tracking" href="' . site_url('recruitment/recruitment_portal/job_detail/' . $rec_value['cp_id']) . '">' . app_lang('view_detail') . '</a>';
				$string_job .= '</div>';

				if (strtotime(date("Y-m-d")) > strtotime($rec_value['cp_to_date'])) {
					$string_job .= '<div class="feature new text ">' . app_lang('overdue') . '</div>';
				} else {
					$string_job .= '<div class=""></div>';
				}

				$string_job .= '<div class="distance-time-job-posted">';
				$string_job .= '<span class="distance-time highlight">' .
				$rec_value['cp_from_date'] . ' - ' . $rec_value['cp_to_date'] . '
				</span>';
				$string_job .= '</div>';

				$string_job .= '</div>';
				$string_job .= '</div>';
				$string_job .= '</div>';

				$string_job .= '</div>';
				$string_job .= '</div> ';

				$string_job .= '</div>';
			}

			$status = true;
		} else {

			$status = false;
		}

		$data = [];
		$data['value'] = $string_job;
		$data['status'] = $status;
		$data['page'] = (int) $page + 1;

		return $data;

	}

	/**
	 * list position by campaign
	 * @param  integer $campaingn_id
	 * @return string
	 */
	public function list_position_by_campaign($campaingn_id) {
		$options = '';
		if ($campaingn_id) {
			$builder = $this->db->table(get_db_prefix().'rec_campaign');
			$builder->where('cp_id', $campaingn_id);
			$rec_campaign = $builder->get()->getRow();
			if ($rec_campaign) {
				$position = $this->get_job_position($rec_campaign->cp_position);
				if ($position) {
					$options .= '<option value="">- '.app_lang('position').'</option>';
					$options .= '<option value="' . $position->position_id . '">' . $position->position_name . '</option>';

				}
			}
		} else {
			$position = $this->get_job_position();
			if (count($position) > 0) {
				$options .= '<option value="">- '.app_lang('position').'</option>';

				foreach ($position as $po_value) {
					$options .= '<option value="' . $po_value['position_id'] . '">' . $po_value['position_name'] . '</option>';
				}
			}

		}
		return $options;

	}

	/**
	 * { recruitment campaign setting }
	 *
	 * @param      <type>   $data   The data
	 *
	 * @return     boolean
	 */
	public function recruitment_campaign_setting($data) {

		$val = $data['input_name_status'] == 'true' ? 1 : 0;
		$builder = $this->db->table(get_db_prefix().'settings');
		$builder->where('setting_name', $data['input_name']);
		$affected_rows = $builder->update([
			'setting_value' => $val,
		]);
		if ($affected_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * get skill
	 * @param  boolean $id
	 * @return object
	 */
	public function get_company($id = false) {

		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'rec_company');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'rec_company')->getResultArray();
		}

	}

	/**
	 * add skill
	 * @param object $data
	 */
	public function add_company($data) {
		if(isset($data['file_names'])){
			unset($data['file_names']);
		}
		if(isset($data['file_sizes'])){
			unset($data['file_sizes']);
		}
		if(isset($data['file'])){
			unset($data['file']);
		}
		$builder = $this->db->table(get_db_prefix().'rec_company');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * update skill
	 * @param  object $data
	 * @param  int $id
	 * @return bool
	 */
	public function update_company($data, $id) {
		if(isset($data['file_names'])){
			unset($data['file_names']);
		}
		if(isset($data['file_sizes'])){
			unset($data['file_sizes']);
		}
		if(isset($data['delete_file'])){
			unset($data['delete_file']);
		}
		if(isset($data['file'])){
			unset($data['file']);
		}
		$builder = $this->db->table(get_db_prefix().'rec_company');
		$builder->where('id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete skill
	 * @param  int $id
	 * @return bool
	 */
	public function delete_company($id) {

		/*delete file*/
		$arr_files = [];
		$get_company = $this->get_company($id);
		
		if(count($arr_files) > 0){
			re_delete_company_files(RECRUITMENT_COMPANY_UPLOAD, $arr_files);
		}

		$builder = $this->db->table(get_db_prefix().'rec_company');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get company attachments
	 * @param  integer $company_id
	 * @return array
	 */
	public function get_company_attachments($company_id) {

		$builder = $this->db->table(get_db_prefix().'files');
		$builder->orderBy('dateadded', 'desc');
		$builder->where('rel_id', $company_id);
		$builder->where('rel_type', 'rec_company');

		return $this->db->get()->getResultArray();

	}

	/**
	 * delete company file
	 * @param  integer $attachment_id
	 * @return boolean
	 */
	public function delete_company_file($attachment_id) {
		$deleted = false;
		$attachment = $this->get_company_attachments_delete($attachment_id);

		if ($attachment) {
			if (empty($attachment->external)) {
				if (file_exists(RECRUITMENT_COMPANY_UPLOAD . $attachment->rel_id . '/' . $attachment->file_name)) {
					unlink(RECRUITMENT_COMPANY_UPLOAD . $attachment->rel_id . '/' . $attachment->file_name);
				} else {
					unlink('plugins/Recruitment/Uploads/company_images/' . $attachment->rel_id . '/' . $attachment->file_name);
				}
			}
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $attachment->id);
			$affected_rows = $builder->delete();
			if ($affected_rows > 0) {
				$deleted = true;
			}
			if (file_exists(RECRUITMENT_COMPANY_UPLOAD . $attachment->rel_id . '/' . $attachment->file_name)) {
				if (is_dir(RECRUITMENT_COMPANY_UPLOAD . $attachment->rel_id)) {

					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files(RECRUITMENT_COMPANY_UPLOAD . $attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir(RECRUITMENT_COMPANY_UPLOAD . $attachment->rel_id);
					}
				}
			} else {
				if (is_dir('plugins/Recruitment/Uploads/company_images/' . $attachment->rel_id)) {

					// Check if no attachments left, so we can delete the folder also
					$other_attachments = list_files('plugins/Recruitment/Uploads/company_images/' . $attachment->rel_id);
					if (count($other_attachments) == 0) {
						// okey only index.html so we can delete the folder also
						delete_dir('plugins/Recruitment/Uploads/company_images/' . $attachment->rel_id);
					}
				}
			}

		}

		return $deleted;
	}

	/**
	 * get company attachments delete
	 * @param  integer $id
	 * @return object
	 */
	public function get_company_attachments_delete($id) {

		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $id);

			return $builder->get()->getRow();
		}
	}

	/**
	 * get industry
	 * @param  boolean $id
	 * @return array
	 */
	public function get_industry($id = false) {

		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'job_industry');
			$builder->where('id', $id);

			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'job_industry')->getResultArray();
		}

	}

	/**
	 * add industry
	 * @param array $data
	 */
	public function add_industry($data) {
		$builder = $this->db->table(get_db_prefix().'job_industry');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * update industry
	 * @param  array $data
	 * @param  integer $id
	 * @return boolean
	 */
	public function update_industry($data, $id) {
		$builder = $this->db->table(get_db_prefix().'job_industry');

		$builder->where('id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete industry
	 * @param  integer $id
	 * @return boolean
	 */
	public function delete_industry($id) {
		$builder = $this->db->table(get_db_prefix().'job_industry');

		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get rec campaign detail
	 * @param  integer $id
	 * @return  object
	 */
	public function get_rec_campaign_detail($id) {

		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->where('cp_id', $id);
		$rec_campaign = $builder->get()->getRow();

		if ($rec_campaign) {
			/*get rec job position*/
			$builder = $this->db->table(get_db_prefix().'rec_job_position');

			$builder->where('position_id', $rec_campaign->cp_position);
			$rec_job_position = $builder->get()->getRow();

			$rec_campaign->position_name = '';
			$rec_campaign->position_description = '';
			$rec_campaign->industry_name = '';
			$rec_campaign->industry_description = '';

			$rec_campaign->company_name = '';
			$rec_campaign->company_description = '';
			$rec_campaign->company_address = '';
			$rec_campaign->company_industry = '';


			$rec_campaign->company_logo = get_file_uri('plugins/Recruitment/Uploads/no_logo.jpg');
			$rec_campaign->alt_logo = 'no_logo.jpg';

			if ($rec_job_position) {
				$rec_campaign->position_name = $rec_job_position->position_name;
				$rec_campaign->position_description = $rec_job_position->position_description;

				/*get job industry*/
				$builder = $this->db->table(get_db_prefix().'job_industry');

				$builder->where('id', $rec_job_position->industry_id);
				$rec_job_industry = $builder->get()->getRow();

				if ($rec_job_industry) {
					$rec_campaign->industry_name = $rec_job_industry->industry_name;
					$rec_campaign->industry_description = $rec_job_industry->industry_description;

				}

				/*get job skill*/
				if ($rec_job_position->job_skill) {

					$builder = $this->db->table(get_db_prefix().'rec_skill');

					$builder->where('id IN ' . '(' . $rec_job_position->job_skill . ')');
					$rec_job_skill = $builder->get()->getResultArray();

					$rec_campaign->rec_job_skill = $rec_job_skill;

				}

				/*get job company*/
				$builder = $this->db->table(get_db_prefix().'rec_company');

				$builder->where('id', $rec_campaign->company_id);
				$rec_company = $builder->get()->getRow();

				if ($rec_company) {
					$rec_campaign->company_name = $rec_company->company_name;
					$rec_campaign->company_description = $rec_company->company_description;
					$rec_campaign->company_address = $rec_company->company_address;
					$rec_campaign->company_industry = $rec_company->company_industry;

					/*get company logo*/

					$builder = $this->db->table(get_db_prefix().'files');

					$builder->where('rel_id', $rec_campaign->company_id);
					$builder->where('rel_type', "rec_company");
					$logo = $builder->get()->getRow();
					if ($logo) {

						$rec_campaign->company_logo = get_file_uri('plugins/Recruitment/Uploads/company_images/'.$rec_campaign->company_id . '/' . $logo->file_name);
						$rec_campaign->alt_logo = $logo->file_name;

					}

					/*get job in company*/
					$builder = $this->db->table(get_db_prefix().'rec_campaign');

					$builder->select('*,' . get_db_prefix() . 'rec_campaign.company_id');

					$builder->join(get_db_prefix() . 'rec_job_position', '' . get_db_prefix() . 'rec_job_position.position_id = ' . get_db_prefix() . 'rec_campaign.cp_position', 'left');

					$builder->join(get_db_prefix() . 'rec_company', '' . get_db_prefix() . 'rec_campaign.company_id = ' . get_db_prefix() . 'rec_company.id', 'left');

					$builder->join(get_db_prefix() . 'job_industry', '' . get_db_prefix() . 'rec_job_position.industry_id = ' . get_db_prefix() . 'job_industry.id', 'left');

					$builder->where(get_db_prefix() . 'rec_campaign.company_id', $rec_company->id);
					$builder->where('cp_id !=', $id);
					$builder->where('cp_status =', '3');

					$builder->orderBy('cp_id', 'desc');
					$builder->limit(10);

					$job_in_company = $builder->get()->getResultArray();
					/*get company logo*/
					foreach ($job_in_company as $key => $value) {
						$job_in_company[$key]['company_logo'] = get_file_uri('plugins/Recruitment/Uploads/no_logo.jpg');
						$job_in_company[$key]['alt_logo'] = 'no_logo.jpg';

						if (($value['company_id'] != '') && ($value['company_id'] != 0)) {
							$builder = $this->db->table(get_db_prefix().'files');

							$builder->where('rel_id', $value['company_id']);
							$builder->where('rel_type', "rec_company");
							$logo = $builder->get()->getRow();
							if ($logo) {
								

								$job_in_company[$key]['company_logo'] = get_file_uri('plugins/Recruitment/Uploads/company_images/'. $value['company_id'] . '/' . $logo->file_name);
								$job_in_company[$key]['alt_logo'] = $logo->file_name;

							}

						}
					}

					$rec_campaign->job_in_company = $job_in_company;

				}
			}

		}
		return $rec_campaign;

	}

	/**
	 * portal send mail to friend
	 * @param  [type] $data
	 * @return [type]
	 */
	public function portal_send_mail_to_friend($data) {

		$inbox['body'] = $data['content'];
		$inbox['body'] = $inbox['body'];
		
		$result = send_app_mail($data['email'], $data['subject'], $inbox['body'] );

		if ($result) {
			return true;
		}
		return false;
	}

	public function get_tranfer_personnel_file($id) {
		$data = [];
		$arr_file = $this->re_get_attachments_file($id, 'rec_set_transfer');

		$htmlfile = '';
		//get file attachment html
		if (isset($arr_file)) {
			$htmlfile = '<div class="row col-md-12" id="attachment_file">';
			foreach ($arr_file as $attachment) {
				$href_url = site_url('modules/recruitment/uploads/set_transfer/' . $attachment['rel_id'] . '/' . $attachment['file_name']) . '" download';
				if (!empty($attachment['external'])) {
					$href_url = $attachment['external_link'];
				}
				$htmlfile .= '<div class="display-block contract-attachment-wrapper">';
				$htmlfile .= '<div class="col-md-10">';
				$htmlfile .= '<div class="col-md-1 mr-5">';
				$htmlfile .= '<a name="preview-btn" onclick="preview_file_tranfer_personnel(this); return false;" rel_id = "' . $attachment['rel_id'] . '" id = "' . $attachment['id'] . '" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="' . app_lang("preview_file") . '">';
				$htmlfile .= '<i class="fa fa-eye"></i>';
				$htmlfile .= '</a>';
				$htmlfile .= '</div>';
				$htmlfile .= '<div class=col-md-9>';
				$htmlfile .= '<div class="pull-left"><i class="' . get_mime_class($attachment['filetype']) . '"></i></div>';
				$htmlfile .= '<a href="' . $href_url . '>' . $attachment['file_name'] . '</a>';
				$htmlfile .= '<p class="text-muted">' . $attachment["filetype"] . '</p>';
				$htmlfile .= '</div>';
				$htmlfile .= '</div>';
				$htmlfile .= '<div class="col-md-2 text-right">';
				if (is_admin() || hrm_permissions('recruitment', '', 'delete')) {
					$htmlfile .= '<a href="#" class="text-danger" onclick="delete_tranfer_personnel_attachment(this,' . $attachment['id'] . '); return false;"><i class="fa fa fa-times"></i></a>';
				}
				$htmlfile .= '</div>';
				$htmlfile .= '<div class="clearfix"></div><hr/>';
				$htmlfile .= '</div>';
			}
			$htmlfile .= '</div>';
		}

		$data['htmlfile'] = $htmlfile;

		return $data;

	}

	/**
	 * re get attachments file
	 * @param  [type] $rel_id
	 * @param  [type] $rel_type
	 * @return [type]
	 */
	public function re_get_attachments_file($rel_id, $rel_type, $id = false) {
		if (is_numeric($id)) {
			$this->db->where('id', $id);
			$this->db->where('rel_type', $rel_type);
			$result = $this->db->get(get_db_prefix() . 'files');

			return $result->row();
		}

		if ($id == false) {
			$this->db->orderBy('dateadded', 'desc');
			$this->db->where('rel_id', $rel_id);
			$this->db->where('rel_type', $rel_type);

			return $this->db->get(get_db_prefix() . 'files')->getResultArray();
		}

	}

	/**
	 * delete transfer personnal attachment file
	 * @param  [type] $attachment_id
	 * @return [type]
	 */
	public function delete_transfer_personnal_attachment_file($id) {
		$attachment = $this->re_get_attachments_file('', 'rec_set_transfer', $id);
		$deleted = false;
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/set_transfer/' . $attachment->rel_id . '/' . $attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(''.get_db_prefix().'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
			}

			if (is_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/set_transfer/' . $attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/set_transfer/' . $attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(RECRUITMENT_MODULE_UPLOAD_FOLDER . '/set_transfer/' . $attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * rec add staff
	 * @param  [type] $data
	 * @return [type]
	 */
	public function rec_add_staff($data) {
		$affectedRows = 0;
		$Users_model = model("Models\Users_model");
		$Social_links_model = model("Models\Social_links_model");
		$Email_templates_model = model("Models\Email_templates_model");

		// First check for all cases if the email exists.
		$builder = $this->db->table(get_db_prefix().'users');
		$builder->where('email', $data['email']);
		$email = $builder->get()->getRow();
		if ($email) {
			die('Email already exists');
		}

		$job_title = '';
		$password = isset($data["password"]) ? $data["password"] : password_hash($password, PASSWORD_DEFAULT);
		$data['user_type'] = 'staff';

		if(rec_get_status_modules('Hr_profile')){
			$job_title = hr_profile_get_job_position_name($data['job_position']);
			$data['status_work'] = 'working';

			$user_data = array(
				"email" => $data['email'] ? $data['email'] : null,
				"first_name" => $data['first_name'] ? $data['first_name'] : '',
				"last_name" => $data['last_name'] ? $data['last_name'] : '',
				"is_admin" => 0,
				"phone" => $data['phone'] ? $data['phone'] : null,
				"job_title" => $job_title,
				"phone" => $data['phone'] ? $data['phone'] : null,
				"user_type" => "staff",
				"created_at" => get_current_utc_time(),
				"staff_identifi" => $data['staff_identifi'] ? $data['staff_identifi'] : null,
				"status_work" => $data['status_work'] ? $data['status_work'] : null,
				"job_position" => $data['job_position'] ? $data['job_position'] : null,
				"literacy" => $data['literacy'] ? $data['literacy'] : null,
				"marital_status" => $data['marital_status'] ? $data['marital_status'] : null,
				"nation" => $data['nation'] ? $data['nation'] : null,
				"religion" => $data['religion'] ? $data['religion'] : null,
				"identification" => $data['identification'] ? $data['identification'] : null,
				"days_for_identity" => $data['days_for_identity'] ? to_sql_date1($data['days_for_identity']) : null,
				"home_town" => $data['home_town'] ? $data['home_town'] : null,
				"resident" => $data['resident'] ? $data['resident'] : null,
				"address" => $data['address'] ? $data['address'] : null,
				"dob" => to_sql_date1($data['dob']),
				"birthplace" => $data['birthplace'] ? $data['birthplace'] : null,
				"place_of_issue" => $data['place_of_issue'] ? $data['place_of_issue'] : null,
				"skype" => $data['skype'] ? $data['skype'] : null,
			);
		}else{
			$user_data = array(
				"email" => $data['email'] ? $data['email'] : null,
				"first_name" => $data['first_name'] ? $data['first_name'] : '',
				"last_name" => $data['last_name'] ? $data['last_name'] : '',
				"is_admin" => 0,
				"phone" => $data['phone'] ? $data['phone'] : null,
				"job_title" => $job_title,
				"phone" => $data['phone'] ? $data['phone'] : null,
				"user_type" => "staff",
				"created_at" => get_current_utc_time(),
				"dob" => to_sql_date1($data['dob']),
				"skype" => $data['skype'] ? $data['skype'] : null,
			);
		}

		if ($password) {
			$user_data["password"] = password_hash($password, PASSWORD_DEFAULT);
		}

		$user_data["is_admin"] = 0;
		$user_data["role_id"] = $data['role_id'];
		        		//add a new team member
		$user_id = $Users_model->ci_save($user_data);

		if ($user_id) {
			$affectedRows++;

			/*update next number setting*/
			$this->update_prefix_number(['staff_code_number' =>  get_setting('staff_code_number')+1]);

			//user added, now add the job info for the user
			$job_data = array(
				"user_id" => $user_id,
				"salary" => 0,
				"salary_term" => '',
				"date_of_hire" => null
			);

			if($Users_model->save_job_info($job_data)){
				$affectedRows++;
			}

					//send login details to user
			if (isset($data['email_login_details']) && null !== $data['email_login_details']) {
				$ci = new Security_Controller(false);
                	//get the login details template
				$email_template = $Email_templates_model->get_final_template("login_info");

				$parser_data["SIGNATURE"] = $email_template->signature;
				$parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
				$parser_data["USER_LAST_NAME"] = $user_data["last_name"];
				$parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
				$parser_data["USER_LOGIN_PASSWORD"] = $data['password'];
				$parser_data["DASHBOARD_URL"] = base_url();
				$parser_data["LOGO_URL"] = get_logo_url();

				$message = $ci->parser->setData($parser_data)->renderString($email_template->message);
				send_app_mail($data['email'], $email_template->subject, $message);
			}

		}

		if($affectedRows > 0){
			return $user_id;
		}

		return false;
	}

	/**
	 * rec update permissions
	 * @param  [type] $permissions
	 * @param  [type] $id
	 * @return [type]
	 */
	public function rec_update_permissions($permissions, $id) {
		$this->db->where('staff_id', $id);
		$this->db->delete('staff_permissions');

		$is_staff_member = is_staff_member($id);

		foreach ($permissions as $feature => $capabilities) {
			foreach ($capabilities as $capability) {

				// Maybe do this via hook.
				if ($feature == 'leads' && !$is_staff_member) {
					continue;
				}

				$this->db->insert('staff_permissions', ['staff_id' => $id, 'feature' => $feature, 'capability' => $capability]);
			}
		}

		return true;
	}

	/**
	 * candidate export pdf
	 * @param  [type] $export_candidate
	 * @return [type]
	 */
	public function candidate_export_pdf($export_candidate) {
		return app_pdf('export_candidate', module_dir_path(RECRUITMENT_MODULE_NAME, 'libraries/pdf/Export_candidate_pdf.php'), $export_candidate);
	}

	/**
	 * get candidate profile by id
	 * @param  [type] $ids
	 * @return [type]
	 */
	public function get_candidate_profile_by_id($ids) {
		$arr_id = implode(",", $ids);

		$builder = $this->db->table(get_db_prefix().'rec_candidate');	
		$builder->where('id IN (' . $arr_id . ')');
		$candidates = $builder->get()->getResultArray();

		$builder = $this->db->table(get_db_prefix().'cd_literacy');	
		$builder->where('candidate IN (' . $arr_id . ')');
		$literacy = $builder->get()->getResultArray();

		$candidate_literacy = [];
		foreach ($literacy as $value) {
			$candidate_literacy[$value['candidate']][] = $value;
		}

		$builder = $this->db->table(get_db_prefix().'cd_work_experience');	
		$builder->where('candidate IN (' . $arr_id . ')');
		$work_experience = $builder->get()->getResultArray();

		$candidate_experience = [];
		foreach ($work_experience as $w_value) {
			$candidate_experience[$w_value['candidate']][] = $w_value;
		}

		$builder = $this->db->table(get_db_prefix().'files');	
		$builder->where('rel_id IN (' . $arr_id . ')');
		$builder->where('rel_type', 'rec_cadidate_avar');
		$result = $builder->get()->getResultArray();

		$cadidate_avatar = [];
		foreach ($result as $avatar) {
			$cadidate_avatar[$avatar['rel_id']] = $avatar;
		}

		/*get job skill*/
		$builder = $this->db->table(get_db_prefix().'rec_skill');	
		$rec_job_skill = $builder->get()->getResultArray();
		$rec_skill = [];
		foreach ($rec_job_skill as $value) {
			$rec_skill[$value['id']] = $value['skill_name'];
		}

		//get job name by campaign
		$sql_where = 'SELECT cp.cp_id, jp.position_name FROM ' . get_db_prefix() . 'rec_campaign as cp
		left join ' . get_db_prefix() . 'rec_job_position as jp on cp.cp_position = jp.position_id
		';
		$campaigns = $this->db->query($sql_where)->getResultArray();

		$job_positions = [];
		foreach ($campaigns as $campaign) {
			$job_positions[$campaign['cp_id']] = $campaign['position_name'];
		}

		$data = [];
		$data['candidate'] = $candidates;
		$data['candidate_literacy'] = $candidate_literacy;
		$data['candidate_experience'] = $candidate_experience;
		$data['cadidate_avatar'] = $cadidate_avatar;
		$data['rec_skill'] = $rec_skill;
		$data['job_positions'] = $job_positions;

		return $data;
	}

	/**
	 * get last staff id
	 * @return [type]
	 */
	public function get_last_staff_id() {
		$staff =  $this->db->query("SELECT * FROM " . get_db_prefix() . "users
			order by id desc
			limit 1")->getRow();

		if ($staff) {
			return $staff->id;
		} else {
			return 1;
		}
	}

	/**
	 * check job position exist hr records
	 * @param  [type] $rec_campaign_id 
	 * @return [type]                  
	 */
	public function check_job_position_exist_hr_records($rec_campaign_id)
	{
		$position_id = '';
		if(rec_get_status_modules('Hr_profile')){

			$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

		//from recruitmetn campaign -> get job  positon name -> check in hr records module exists , if exists -> return id othewise create new -> return id
			$rec_campaign = $this->get_rec_campaign($rec_campaign_id);
			if($rec_campaign){
				if(is_numeric($rec_campaign->cp_position)){
					$job_position = $this->get_job_position($rec_campaign->cp_position);

					if($job_position){
						$position_name_upper = strtoupper($job_position->position_name);

								//check if job position exist in hr records module
						$builder = $this->db->table(get_db_prefix().'hr_job_position');
						$builder->where('upper(position_name)', $position_name_upper);
						$hr_job_position = $builder->get()->getRow();

						if(!$hr_job_position){
							$str_result ='';
							$prefix_str ='';
							$prefix_str .= get_setting('job_position_prefix');
							$next_number = (int) get_setting('job_position_number');
							$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);

							$job_position_data = [
								'position_name' => $job_position->position_name,
								'position_code' => $str_result,
							];
							$builder = $this->db->table(get_db_prefix().'hr_job_position');
							$builder->insert($job_position_data);
							$insert_id = $this->db->insertID();

							if ($insert_id) {
								/*update next number setting*/
								$Hr_profile_model->update_prefix_number(['job_position_number' =>  get_setting('job_position_number')+1]);
							}

							$position_id = $insert_id;

						}else{
							$position_id = $hr_job_position->position_id;
						}

					}

				}
			}
		}

		return $position_id;
	}

	/**
	 * get recruitment campaign by company
	 * @param  [type] $company_id 
	 * @return [type]             
	 */
	public function get_recruitment_campaign_by_company($company_id)
	{
		$arr_campaign_id = [];
		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->where('company_id', $company_id);
		$rec_campaign = $builder->get()->getResultArray();   
		foreach ($rec_campaign as $key => $value) {
			$arr_campaign_id[] = $value['cp_id'];
		}
		return $arr_campaign_id;
	}

	/**
	 * get recruitment campaign by job
	 * @param  [type] $job_id 
	 * @return [type]         
	 */
	public function get_recruitment_campaign_by_job($job_ids)
	{
		$arr_campaign_id = [];
		$builder = $this->db->table(get_db_prefix().'rec_campaign');
		$builder->where('cp_position IN (' . implode(",", $job_ids) . ')');
		$rec_campaign = $builder->get()->getResultArray();   
		foreach ($rec_campaign as $key => $value) {
			$arr_campaign_id[] = $value['cp_id'];
		}
		return $arr_campaign_id;
	}

	/**
	 * duplicate recruitment channel
	 * @param  [type] $rec_channel_id 
	 * @return [type]                 
	 */
	public function duplicate_recruitment_channel($rec_channel_id)
	{
		$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
		$builder->where('id', $rec_channel_id);
		$recruitment_channel = $builder->get()->getResultArray();
		if(count($recruitment_channel) > 0){
			if(isset($recruitment_channel[0]['id'])){
				unset($recruitment_channel[0]['id']);
			}

			$recruitment_channel[0]['r_form_name'] = $recruitment_channel[0]['r_form_name'].' (Copy)';
			$builder = $this->db->table(get_db_prefix().'rec_campaign_form_web');
			$builder->insert($recruitment_channel[0]);
			$insert_id = $this->db->insertID();
			return $insert_id;
		}
		return  false;
	}

	/**
	 * wh get activity log
	 * @param  [type] $id   
	 * @param  [type] $type 
	 * @return [type]       
	 */
	public function re_get_activity_log($id, $rel_type)
	{
		$builder = $this->db->table(get_db_prefix().'rec_activity_log');
		$builder->where('rel_id', $id);
		$builder->where('rel_type', $rel_type);
		$builder->orderBy('date', 'ASC');

		return $builder->get()->getResultArray();
	}

	/**
	 * log wh activity
	 * @param  [type] $id              
	 * @param  [type] $description     
	 * @param  string $additional_data 
	 * @return [type]                  
	 */
	public function log_re_activity($id, $rel_type, $description, $date = '')
	{
		if(strlen($date) == 0){
			$date = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		}
		$log = [
			'date'            => $date,
			'description'     => $description,
			'rel_id'          => $id,
			'rel_type'          => $rel_type,
			'staffid'         => get_staff_user_id1(),
			'full_name'       => get_staff_full_name1(get_staff_user_id1()),
		];
		$builder = $this->db->table(get_db_prefix().'rec_activity_log');

		$builder->insert($log);
		$insert_id = $this->db->insertID();
		if($insert_id){

			return true;
		}
		return false;
	}

	/**
	 * delete activitylog
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_activitylog($id)
	{
		$builder = $this->db->table(get_db_prefix().'rec_activity_log');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();

		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * send interview schedule
	 * @param  [type] $interview_id 
	 * @return [type]               
	 */
	public function send_interview_schedule($interview_id)
	{
		/*get interview data*/
		$builder = $this->db->table(get_db_prefix().'rec_interview');
		$builder->join(get_db_prefix() . 'cd_interview', '' . get_db_prefix() . 'cd_interview.interview = ' . get_db_prefix() . 'rec_interview.id', 'left');
		$builder->join(get_db_prefix() . 'rec_candidate', '' . get_db_prefix() . 'cd_interview.candidate = ' . get_db_prefix() . 'rec_candidate.id', 'left');
		$builder->where(get_db_prefix().'rec_interview.id', $interview_id);
		$cd_interview_data = $builder->get()->getResultArray();

		$get_staff_user_id = get_staff_user_id1();


		/*send notify to interviewer*/
		$interview_location = '';
		$interview_subject = pg_get_default_company_name();
		$position_name = '';
		$interview_time = '';
		$link = '';

		$send_notify = 1;
		$interview_schedule = $this->get_interview_schedule($interview_id);
		if($interview_schedule){
			$interview_location = $interview_schedule->interview_location;
			$interview_subject .= ' '.app_lang('Invitation_to_Interview').' '.$interview_schedule->is_name;
			$position_name .= get_rec_position_name($interview_schedule->position);
			$interview_time = format_to_date(date("Y-m-d", strtotime($interview_schedule->interview_day)), false) .': '.$interview_schedule->from_time.' - '.$interview_schedule->to_time;
			$link = 'recruitment/view_interview_schedule/' . $interview_schedule->id;
			$send_notify = (int)$interview_schedule->send_notify+1;


		}

		if(1==2){
			if($interview_schedule){
				$interviewer = explode(",", $interview_schedule->interviewer);
				foreach ($interviewer as $staff_id) {

					if($get_staff_user_id != $staff_id){
						$notified = add_notification([
							'description' => $interview_subject,
							'touserid' => $staff_id,
							'link' => $link,
							'additional_data' => serialize([]),
						]);
						if ($notified) {
							pusher_trigger_notification([$staff_id]);
						}
					}

				}
			}
		}

		/*send notify to candidate*/
		foreach ($cd_interview_data as $value) {
			if($value['email'] != ''){
				if($value['cd_from_hours'] != null){
					$cd_interview_time = format_to_date(date("Y-m-d", strtotime($interview_schedule->interview_day)), false) .': '.date("H:i", strtotime($value['cd_from_hours'])).' - '.date("H:i", strtotime($value['cd_to_hours']));
				}else{
					$cd_interview_time = $interview_time;
				}

				$interview_message = '<span style="font-size: 12pt;">Dear '.$value['candidate_name'].' </span><br /> <br />
				<span style="font-size: 12pt;">'.app_lang('Thank_you_for_your_application_to_the').' '.$position_name.'</span><br /> <br />
				<span style="font-size: 12pt;">'.app_lang('After_reviewing_your_application').$cd_interview_time.'</span><br /> <br />

				<span style="font-size: 12pt;">'.app_lang('Our_office_is_located_at').' '.$interview_location.'</span><br /> <br />
				<span style="font-size: 12pt;">'.app_lang('Please_reply_directly_to_this_email_with_your_availability_during_the_suggested_times').'</span><br /> <br />
				<span style="font-size: 12pt;">'.app_lang('Kind_Regards').',</span><br />
				<span style="font-size: 12pt;">{email_signature}</span>';

					//send mail
				send_app_mail($value['email'], $interview_subject, $interview_message);
			}
		}

		$builder = $this->db->table(get_db_prefix().'rec_interview');
		$builder->where('id', $interview_id);
		$builder->update(['send_notify' => $send_notify]);

		return true;

	}

	/**
	 * add attachment to database
	 * @param [type]  $rel_id     
	 * @param [type]  $rel_type   
	 * @param [type]  $attachment 
	 * @param boolean $external   
	 */
	public function add_attachment_to_database($rel_id, $rel_type, $attachment, $external = false)
	{
		$data['dateadded'] = get_current_utc_time();
		$data['rel_id']    = $rel_id;
		if (!isset($attachment[0]['staffid'])) {
			$data['staffid'] = get_staff_user_id1();
		} else {
			$data['staffid'] = $attachment[0]['staffid'];
		}

		if (isset($attachment[0]['task_comment_id'])) {
			$data['task_comment_id'] = $attachment[0]['task_comment_id'];
		}

		$data['rel_type'] = $rel_type;

		if (isset($attachment[0]['contact_id'])) {
			$data['contact_id']          = $attachment[0]['contact_id'];
			$data['visible_to_customer'] = 1;
			if (isset($data['staffid'])) {
				unset($data['staffid']);
			}
		}

		$data['attachment_key'] = app_generate_hash();

		if ($external == false) {
			$data['file_name'] = $attachment[0]['file_name'];
			$data['filetype']  = $attachment[0]['filetype'];
		} else {
			$path_parts            = pathinfo($attachment[0]['name']);
			$data['file_name']     = $attachment[0]['name'];
			$data['external_link'] = $attachment[0]['link'];
			$data['filetype']      = !isset($attachment[0]['mime']) ? get_mime_by_extension('.' . $path_parts['extension']) : $attachment[0]['mime'];
			$data['external']      = $external;
			if (isset($attachment[0]['thumbnailLink'])) {
				$data['thumbnail_link'] = $attachment[0]['thumbnailLink'];
			}
		}

		$builder = $this->db->table(db_prefix().'files');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		return $insert_id;
	}

	/**
	 * plugin get access info
	 * @param  integer $user_id 
	 * @return [type]           
	 */
	function plugin_get_access_info($user_id = 0) {
		
		$users_table = $this->db->prefixTable('users');
		$roles_table = $this->db->prefixTable('roles');
		$team_table = $this->db->prefixTable('team');

		if (!$user_id) {
			$user_id = 0;
		}

		$sql = "SELECT $users_table.id, $users_table.user_type, $users_table.is_admin, $users_table.role_id, $users_table.email,
		$users_table.first_name, $users_table.last_name, $users_table.image, $users_table.message_checked_at, $users_table.notification_checked_at, $users_table.client_id, $users_table.enable_web_notification,
		$users_table.is_primary_contact, $users_table.sticky_note,
		$roles_table.title as role_title, $roles_table.plugins_permissions1 as permissions,
		(SELECT GROUP_CONCAT(id) team_ids FROM $team_table WHERE FIND_IN_SET('$user_id', `members`)) as team_ids
		FROM $users_table
		LEFT JOIN $roles_table ON $roles_table.id = $users_table.role_id AND $roles_table.deleted = 0
		WHERE $users_table.deleted=0 AND $users_table.id=$user_id";
		return $this->db->query($sql)->getRow();
	}

	/**
	 * create code
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function create_code($rel_type) {
		$str_result ='';

		$prefix_str ='';
		switch ($rel_type) {
			case 'candidate_code':
				$prefix_str .= get_setting('candidate_code_prefix');
				$next_number = (int) get_setting('candidate_code_number');
				$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
				break;
			default:
				# code...
				break;
		}

		return $str_result;
	}

	/**
	 * update prefix number
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_prefix_number($data)
	{
		$affected_rows=0;
		foreach ($data as $key => $value) {
			$builder = $this->db->table(get_db_prefix().'settings');
			$builder->where('setting_name',$key);
			$affected_rows = $builder->update([
				'setting_value' => $value,
			]);

			if ($affected_rows > 0) {
				$affected_rows++;
			}
		}

		if($affected_rows > 0){
			return true;
		}else{
			return false;
		}
	}

	/**
	 * change candidate password
	 * @param  [type] $id          
	 * @param  [type] $oldPassword 
	 * @param  [type] $newPassword 
	 * @return [type]              
	 */
	public function change_candidate_password($id, $oldPassword, $newPassword)
	{
        // Get current password
		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$builder->where('id', $id);
		$client = $builder->get()->getRow();

		

		if (!($client->password === md5($oldPassword))) {
			return [
				'old_password_not_match' => true,
			];
		}
		$builder = $this->db->table(get_db_prefix().'rec_candidate');
		$builder->where('id', $id);
		$affected_rows = $builder->update([
			'last_password_change' => date('Y-m-d H:i:s'),
			'password'             => md5($newPassword),
		]);

		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * re status mark as
	 * @param  [type] $status 
	 * @param  [type] $id     
	 * @param  [type] $type   
	 * @return [type]         
	 */
	public function re_status_mark_as($status, $id, $type)
	{
		$status_f = false;
		if($type == 'applied_job'){
			$builder = $this->db->table(get_db_prefix().'rec_applied_jobs');
			$builder->where('id', $id);
			$affected_rows = $builder->update(['status' => $status]);
			if ($affected_rows > 0) {
				$status_f = true;
			}
		}elseif($type == 'interview'){
			$builder = $this->db->table(get_db_prefix().'cd_interview');

			$builder->where('in_id', $id);
			$affected_rows = $builder->update(['status' => $status]);
			if ($affected_rows > 0) {
				$status_f = true;
			}
		}
		return true;
	}

	/**
	 * candidate apply
	 * @param  [type] $candidate_id 
	 * @param  [type] $campaign_id  
	 * @return [type]               
	 */
	public function candidate_apply($candidate_id, $campaign_id, $status)
	{
		$builder = $this->db->table(get_db_prefix().'rec_applied_jobs');
		$builder->insert([
			'candidate_id' => $candidate_id,
			'campaign_id' => $campaign_id,
			'date_created' => date('Y-m-d H:i:s'),
			'status' => $status,
			'activate' => '1',
		]);

		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * is candidate email exists
	 * @param  [type]  $email 
	 * @param  integer $id    
	 * @return boolean        
	 */
	function is_candidate_email_exists($email, $id = 0) {
		$rec_candidate = $this->db->prefixTable('rec_candidate');
		$id = $id ? $this->db->escapeString($id) : $id;

		$sql = "SELECT $rec_candidate.* FROM $rec_candidate   
		WHERE $rec_candidate.email='$email'";

		$result = $this->db->query($sql);

		if ($result->resultID->num_rows && $result->getRow()->id != $id) {
			return $result->getRow();
		} else {
			return false;
		}
	}

	/**
	 * get list criteria by evaluation_id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_list_criteria_by_evaluation_id($id) {
		$list_group = $this->db->query('SELECT distinct('.get_db_prefix().'rec_list_criteria.group_criteria) as id, criteria_title, evaluation_form FROM '.get_db_prefix().'rec_list_criteria
			LEFT JOIN '.get_db_prefix().'rec_criteria on '.get_db_prefix().'rec_criteria.criteria_id = '.get_db_prefix().'rec_list_criteria.group_criteria where '.get_db_prefix().'rec_list_criteria.evaluation_form = ' . $id)->getResultArray();
		return $list_group;

	}

	/**
	 * add evaluation form detail
	 * @param [type] $data 
	 */
	public function add_evaluation_form_detail($data) {

		if(isset($data['evaluation_form'])){
			$evaluation_form = $data['evaluation_form'];
			unset($data['evaluation_form']);
		}
		if(isset($data['evaluation_criteria'])){
			$evaluation_criteria = $data['evaluation_criteria'];
			unset($data['evaluation_criteria']);
		}
		if(isset($data['percent'])){
			$percent = $data['percent'];
			unset($data['percent']);
		}

		if (isset($evaluation_form)) {
			if(isset($evaluation_criteria)){
				foreach ($evaluation_criteria as $key => $value) {
					$builder = $this->db->table(get_db_prefix().'rec_list_criteria');
					$builder->insert([
						'evaluation_form' => $evaluation_form,
						'group_criteria' => $data['group_criteria'],
						'evaluation_criteria' => $value,
						'percent' => $percent[$key],
					]);
					
				}
			}
		}
		return $evaluation_form;
	}

	/**
	 * update evaluation form detail
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_evaluation_form_detail($data, $id) {

		if(isset($data['evaluation_form'])){
			$evaluation_form = $data['evaluation_form'];
			unset($data['evaluation_form']);
		}
		if(isset($data['evaluation_criteria'])){
			$evaluation_criteria = $data['evaluation_criteria'];
			unset($data['evaluation_criteria']);
		}
		if(isset($data['percent'])){
			$percent = $data['percent'];
			unset($data['percent']);
		}


		$builder = $this->db->table(get_db_prefix().'rec_list_criteria');
		$builder->where('group_criteria', $data['group_criteria']);
		$builder->delete();

		if (isset($evaluation_form)) {
			if(isset($evaluation_criteria)){
				foreach ($evaluation_criteria as $key => $value) {
					$builder = $this->db->table(get_db_prefix().'rec_list_criteria');
					$builder->insert([
						'evaluation_form' => $evaluation_form,
						'group_criteria' => $data['group_criteria'],
						'evaluation_criteria' => $value,
						'percent' => $percent[$key],
					]);
					
				}
			}
		}

		return true;
	}

	/**
	 * delete evaluation form detail
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_evaluation_form_detail($id) {
		$affected_rows = 0;

		$builder->where('group_criteria', $id);
		$affected_row = $builder->delete(get_db_prefix() . 'rec_list_criteria');
		if ($affected_row > 0) {
			$affected_rows++;
		}

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	public function get_evaluation_form_detail($evaluation_form_id, $id)
	{
		$builder = $this->db->table(get_db_prefix().'rec_list_criteria');
		$builder->where('evaluation_form', $evaluation_form_id);
		$builder->where('group_criteria', $id);
		$rec_list_criteria = $builder->get()->getResultArray();
		return $rec_list_criteria;
	}

}