<?php

namespace Hr_profile\Controllers;

use App\Controllers\Security_Controller;
use App\Models\Crud_model;
use App\Libraries\Pdf;

class Hr_profile extends Security_Controller {

	protected $hr_profile_model;
	function __construct() {

		parent::__construct();
		$this->hr_profile_model = new \Hr_profile\Models\Hr_profile_model();
		app_hooks()->do_action('app_hook_hrrecord_init');

	}


	/* List all announcements */
	public function dashboard() {
		if (!hr_has_permission('hr_profile_can_view_global_hr_dashboard')) {
			app_redirect("forbidden");
		}

		$data['title'] = 'HR Profile';
		return $this->template->rander('Hr_profile\Views\hr_profile_dashboard', $data);
	}

	/**
	 * Organizational chart
	 * @return view
	 */
	public function organizational_chart() {
		if (!hr_has_permission('hr_profile_can_view_global_organizational_chart') && !hr_has_permission('hr_profile_can_view_own_organizational_chart')) {
			app_redirect("forbidden");
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['list_department'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*load deparment by manager*/
		if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_organizational_chart')) {
			/*View own*/
			$data['deparment_chart'] = json_encode($this->hr_profile_model->get_data_departmentchart_v2());
		} else {
			/*admin or view global*/
			$data['deparment_chart'] = json_encode($this->hr_profile_model->get_data_departmentchart());
		}

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['title'] = app_lang('hr_organizational_chart');
		$data['dep_tree'] = json_encode($this->hr_profile_model->get_department_tree());

		return $this->template->rander('Hr_profile\Views\organizational/organizational_chart', $data);
	}

	/**
	 * email exist as staff
	 * @return integer
	 */
	private function email_exist_as_staff() {
		return total_rows(db_prefix() . 'departments', 'email IN (SELECT email FROM ' . db_prefix() . 'staff)') > 0;
	}

	/**
	 * get data department
	 * @return json
	 */
	public function get_data_department() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'organizational/include/department_table'), $dataPost);
	}

	/**
	 * Delete department from database
	 * @param  integer $id
	 */
	public function delete() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_profile/organizational_chart'));
		}

		$response = $this->hr_profile_model->delete_department($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("hr_is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect(('hr_profile/organizational_chart'));
	}

	/* Edit or add new department */
	public function department($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();

			if (!$this->request->getPost('id')) {
				$id = $this->hr_profile_model->add_department($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect("hr_profile/organizational_chart");

			} else {

				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_department($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect("hr_profile/organizational_chart");
			}
		}
	}

	/**
	 * email exists
	 * @return [type]
	 */
	public function email_exists() {
		// First we need to check if the email is the same
		$departmentid = $this->request->getPost('departmentid');
		if ($departmentid) {
			$this->db->where('departmentid', $departmentid);
			$_current_email = $this->db->get(db_prefix() . 'departments')->row();
			if ($_current_email->email == $this->request->getPost('email')) {
				echo json_encode(true);
				die();
			}
		}
		$exists = total_rows(db_prefix() . 'departments', [
			'email' => $this->request->getPost('email'),
		]);
		if ($exists > 0) {
			echo 'false';
		} else {
			echo 'true';
		}
	}

	/**
	 * test imap connection
	 * @return [type]
	 */
	public function test_imap_connection() {
		app_check_imap_open_function();

		$email = $this->request->getPost('email');
		$password = $this->request->getPost('password', false);
		$host = $this->request->getPost('host');
		$imap_username = $this->request->getPost('username');
		if ($this->request->getPost('encryption')) {
			$encryption = $this->request->getPost('encryption');
		} else {
			$encryption = '';
		}

		require_once APPPATH . 'third_party/php-imap/Imap.php';

		$mailbox = $host;

		if ($imap_username != '') {
			$username = $imap_username;
		} else {
			$username = $email;
		}

		$password = $password;
		$encryption = $encryption;
		// open connection
		$imap = new Imap($mailbox, $username, $password, $encryption);
		if ($imap->isConnected() === true) {
			echo json_encode([
				'alert_type' => 'success',
				'message' => app_lang('lead_email_connection_ok'),
			]);
		} else {
			echo json_encode([
				'alert_type' => 'warning',
				'message' => $imap->getError(),
			]);
		}
	}

	/**
	 * reception_staff
	 * @return view
	 */
	public function reception_staff() {
		if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_onboarding') && !hr_has_permission('hr_profile_can_view_own_onboarding')) {
			app_redirect("forbidden");
		}

		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
		);
		$data['staff_members'] = $this->Users_model->get_details($options)->getResultArray();

		$data['title'] = app_lang('staff_infor');
		$data['list_staff_not_record'] = $this->hr_profile_model->get_all_staff_not_in_record();
		$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
		$data['staff_dep_tree'] = json_encode($this->hr_profile_model->get_staff_tree());
		$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart());
		$data['list_training'] = $this->hr_profile_model->get_all_jp_interview_training();
		$data['list_reception_staff_asset'] = $this->hr_profile_model->get_setting_asset_allocation();
		$data['list_record_meta'] = $this->hr_profile_model->get_list_record_meta();
		$data['group_checklist'] = $this->hr_profile_model->group_checklist();
		$data['setting_training'] = $this->hr_profile_model->get_setting_training();
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();

		$data['title'] = app_lang('hr_reception_staff');
		return $this->template->rander('Hr_profile\Views\reception_staff/reception_staff', $data);
	}

	public function add_reception_staff() {
		if (!is_admin() && !hr_has_permission('hr_profile_can_create_onboarding') && !hr_has_permission('hr_profile_can_create_onboarding')) {
			app_redirect("forbidden");
		}

		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
		);
		$data['staff_members'] = $this->Users_model->get_details($options)->getResultArray();

		$data['list_staff_not_record'] = $this->hr_profile_model->get_all_staff_not_in_record();
		$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
		$data['staff_dep_tree'] = json_encode($this->hr_profile_model->get_staff_tree());
		$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart());
		$data['list_training'] = $this->hr_profile_model->get_all_jp_interview_training();
		$data['list_reception_staff_asset'] = $this->hr_profile_model->get_setting_asset_allocation();
		$data['list_record_meta'] = $this->hr_profile_model->get_list_record_meta();
		$data['group_checklist'] = $this->hr_profile_model->group_checklist();
		$data['setting_training'] = $this->hr_profile_model->get_setting_training();
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training_has_training_program();


		$data['title'] = app_lang('hr_add_reception');
		return $this->template->rander('Hr_profile\Views\reception_staff/add_reception_staff', $data);
	}

	/**
	 * table reception staff
	 */
	public function table_reception_staff() {
		if ($this->request->getPost()) {
			$dataPost = $this->request->getPost();

			$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'reception_staff/reception_staff_table'), $dataPost);
		}
	}

	/**
	 * setting
	 * @return view
	 */
	public function setting() {

		if (!hr_has_permission('hr_profile_can_view_global_setting')) {
			app_redirect("forbidden");
		}

		$this->load->model('staff_model');
		$data['group'] = $this->request->getGet('group');
		$data['title'] = app_lang('setting');
		$data['tab'][] = 'contract_type';
		$data['tab'][] = 'salary_type';
		$data['tab'][] = 'allowance_type';
		$data['tab'][] = 'procedure_retire';
		$data['tab'][] = 'type_of_training';
		$data['tab'][] = 'reception_staff';
		$data['tab'][] = 'workplace';
		$data['tab'][] = 'contract_template';
		if (is_admin()) {
			$data['tab'][] = 'hr_profile_permissions';
		}
		$data['tab'][] = 'prefix_number';
		//reset data
		if (is_admin()) {
			$data['tab'][] = 'reset_data';
		}

		if ($data['group'] == '') {
			$data['group'] = 'contract_type';
			$data['contract'] = $this->hr_profile_model->get_contracttype();
		} elseif ($data['group'] == 'contract_type') {
			$data['contract'] = $this->hr_profile_model->get_contracttype();

		} elseif ($data['group'] == 'salary_type') {
			$data['salary_form'] = $this->hr_profile_model->get_salary_form();

		} elseif ($data['group'] == 'allowance_type') {
			$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();

		} elseif ($data['group'] == 'procedure_retire') {
			$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();

		} elseif ($data['group'] == 'type_of_training') {
			$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();

		} elseif ($data['group'] == 'reception_staff') {
			$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
			$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
			$data['list_reception_staff_asset'] = $this->hr_profile_model->get_setting_asset_allocation();
			$data['setting_training'] = $this->hr_profile_model->get_setting_training();

			$data['group_checklist'] = $this->hr_profile_model->group_checklist();
			$data['max_checklist'] = $this->hr_profile_model->count_max_checklist();

		} elseif ($data['group'] == 'workplace') {
			$data['workplace'] = $this->hr_profile_model->get_workplace();
		} elseif ($data['group'] == 'contract_template') {
			$data['contract_templates'] = $this->hr_profile_model->get_contract_template();
		}

		$data['job_position'] = $this->hr_profile_model->get_job_position();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['positions'] = $this->hr_profile_model->get_job_position();

		$data['staff'] = $this->staff_model->get();
		$data['department'] = $this->departments_model->get();
		$data['procedure_retire'] = $this->hr_profile_model->get_procedure_retire();
		$data['str_allowance_type'] = $this->hr_profile_model->get_allowance_type_tax();

		$this->load->model('currencies_model');
		$data['base_currency'] = $this->currencies_model->get_base_currency();
		$data['title'] = app_lang('hr_settings');
		$data['tabs']['view'] = 'includes/' . $data['group'];
		return $this->template->view('Hr_profile\Views\manage_setting', $data);
	}

	/**
	 * commodity types
	 * @return [type] 
	 */
	public function contract_types() {
		$data['contract_types'] = $this->hr_profile_model->get_contracttype();
		return $this->template->rander("Hr_profile\Views\includes\contract_type", $data);
	}
	
	/**
	 * list commodity type data
	 * @return [type] 
	 */
	public function list_contract_type_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_contracttype();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_contract_type_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make commodity type row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_contract_type_row($data) {

		$options = '';
		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){
			$options .= modal_anchor(get_uri("hr_profile/contract_type_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('hr_edit_contract_type'), "data-post-id" => $data['id_contracttype']));
		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id_contracttype'], "data-action-url" => get_uri("hr_profile/delete_contract_type/".$data['id_contracttype']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['name_contracttype']),
			nl2br($data['description']),
			$options
		);
	}

	/**
	 * commodity type modal form
	 * @return [type] 
	 */
	public function contract_type_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$contract_type_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['contract_type_data'] = $this->hr_profile_model->get_contracttype($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		return $this->template->view('Hr_profile\Views\includes\modal_forms\contract_type_modal', $data);
	}


	/**
	 * contract_type
	 * @param  integer $id
	 */
	public function contract_type($id = '') {

		if ($this->request->getPost()) {
			$message = '';

			$data = $this->request->getPost();
			if(isset($data['files'])){
				unset($data['files']);
			}
			if (!is_numeric($id)) {
				$id = $this->hr_profile_model->add_contract_type($data);
				if ($id) {
					$success = true;
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('hr_profile/contract_types');
			} else {
				$success = $this->hr_profile_model->update_contract_type($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/contract_types');
			}
			die;
		}
	}
	/**
	 * delete contract type
	 * @param  integer $id
	 */
	public function delete_contract_type($id) {
		if (!$id) {
			app_redirect('hr_profile/contract_types');
		}

		$response = $this->hr_profile_model->delete_contract_type($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));

		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * salary types
	 * @return [type] 
	 */
	public function allowance_types() {
		$data['allowance_types'] = $this->hr_profile_model->get_allowance_type();
		return $this->template->rander("Hr_profile\Views\includes\allowance_type", $data);
	}
	
	/**
	 * list_allowance_type_data
	 * @return [type] 
	 */
	public function list_allowance_type_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_allowance_type();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_allowance_type_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make salary type row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_allowance_type_row($data) {

		$options = '';
		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){
			$options .= modal_anchor(get_uri("hr_profile/allowance_type_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('hr_edit_allowance_type'), "data-post-id" => $data['type_id']));
		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['type_id'], "data-action-url" => get_uri("hr_profile/delete_allowance_type/".$data['type_id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['type_name']),
			to_decimal_format($data['allowance_val']),
			$options
		);
	}

	public function allowance_type_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$allowance_type_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['allowance_type_data'] = $this->hr_profile_model->get_allowance_type($id);
		}else{
			$id = '';
		}

		$data['id'] = $id;

		return $this->template->view('Hr_profile\Views\includes\modal_forms\allowance_type_modal', $data);
	}

	/**
	 * allowancetype
	 * @param  integer $id
	 */
	public function allowance_type($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->hr_profile_model->add_allowance_type($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('hr_profile/allowance_types');
			} else {
				$success = $this->hr_profile_model->update_allowance_type($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/allowance_types');
			}
			die;
		}
	}

	/**
	 * delete_allowance_type
	 * @param  integer $id
	 */
	public function delete_allowance_type($id) {
		if (!$id) {
			app_redirect('hr_profile/allowance_types');
		}
		$response = $this->hr_profile_model->delete_allowance_type($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("warning" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("warning" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * insurance type
	 */
	public function insurance_type() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {
				$add = $this->hr_profile_model->add_insurance_type($data);
				if ($add) {
					$message = app_lang('added_successfully', app_lang('insurance_type'));
					set_alert('success', $message);
				}
				app_redirect(('hr_profile/setting?group=insurrance'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_insurance_type($data, $id);
				if ($success == true) {
					$message = app_lang('updated_successfully', app_lang('insurance_type'));
					set_alert('success', $message);
				}
				app_redirect(('hr_profile/setting?group=insurrance'));
			}

		}
	}
	/**
	 * delete insurance type
	 * @param  integer $id
	 */
	public function delete_insurance_type($id) {
		if (!$id) {
			app_redirect(('hr_profile/setting?group=insurrance'));
		}
		$response = $this->hr_profile_model->delete_insurance_type($id);
		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', app_lang('hr_is_referenced', app_lang('insurance_type')));
		} elseif ($response == true) {
			set_alert('success', app_lang('deleted', app_lang('insurance_type')));
		} else {
			set_alert('warning', app_lang('problem_deleting', app_lang('insurance_type')));
		}
		app_redirect(('hr_profile/setting?group=insurrance'));
	}
	/**
	 * insurance conditions setting
	 */
	public function insurance_conditions_setting() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$success = $this->hr_profile_model->update_insurance_conditions($data);
			if ($success > 0) {
				set_alert('success', app_lang('setting_updated_successfullyfully'));
			}
			app_redirect(('hr_profile/setting?group=insurrance'));
		}
	}

	/**
	 * salary types
	 * @return [type] 
	 */
	public function salary_types() {
		$data['salary_types'] = $this->hr_profile_model->get_salary_form();
		return $this->template->rander("Hr_profile\Views\includes\salary_type", $data);
	}
	
	/**
	 * list_salary_type_data
	 * @return [type] 
	 */
	public function list_salary_type_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_salary_form();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_salary_type_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make salary type row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_salary_type_row($data) {

		$options = '';
		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){
			$options .= modal_anchor(get_uri("hr_profile/salary_type_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('hr_edit_salary_form'), "data-post-id" => $data['form_id']));
		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['form_id'], "data-action-url" => get_uri("hr_profile/delete_salary_form/".$data['form_id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['form_name']),
			to_decimal_format($data['salary_val']),
			$options
		);
	}

	/**
	 * commodity type modal form
	 * @return [type] 
	 */
	public function salary_type_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$salary_type_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['salary_type_data'] = $this->hr_profile_model->get_salary_form($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		return $this->template->view('Hr_profile\Views\includes\modal_forms\salary_type_modal', $data);
	}

	/**
	 * salary form
	 * @param  integer $id
	 */
	public function salary_form($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();

			if (!is_numeric($id)) {
				$id = $this->hr_profile_model->add_salary_form($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				
				app_redirect('hr_profile/salary_types');
			} else {
				$success = $this->hr_profile_model->update_salary_form($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/salary_types');
			}
			die;
		}
	}

	/**
	 * delete salary form
	 * @param  integer $id
	 */
	public function delete_salary_form($id) {
		if (!$id) {
			app_redirect('hr_profile/salary_types');
		}

		if (!hr_has_permission('hr_profile_can_delete_setting') && !is_admin()) {
			app_redirect("forbidden");
		}

		$response = $this->hr_profile_model->delete_salary_form($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}


	/**
	 * workplaces
	 * @return [type] 
	 */
	public function procedure_retires() {
		$data['procedure_retires'] = $this->hr_profile_model->get_procedure_form_manage();
		return $this->template->rander("Hr_profile\Views\includes\procedure_retire", $data);
	}

	/**
	 * list procedure_retire data
	 * @return [type] 
	 */
	public function list_procedure_retire_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_procedure_form_manage();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_procedure_retire_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make procedure_retire row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_procedure_retire_row($data) {

		$options = '';

		$options .= '<a href="' . site_url('hr_profile/procedure_procedure_retire_details/' . $data['id']) . '"><i data-feather="eye" class="icon-16"></i></a>';

		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){
			$options .= modal_anchor(get_uri("hr_profile/procedure_retire_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('hr_edit_procedure_retire'), "data-post-id" => $data['id']));
		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("hr_profile/delete_procedure_form_manage/".$data['id']), "data-action" => "delete-confirmation"));
		}
		
		$department = '';
		$departments = explode(",", $data['department']);

		foreach ($departments as $key => $value) {
			$department_name = '';
			$get_department_name = hr_profile_get_department_name($value);
			if($get_department_name){
				$department_name = $get_department_name->title;
			}

			$department .= '<span class="badge bg-success large mt-0">' . $department_name.' </span>&nbsp';
			if($key%3 ==0){
				$department .='<br/>';
			}
		}

		return array(
			nl2br($data['name_procedure_retire']),
			$department,
			format_to_datetime($data['datecreator'], false),
			$options
		);
	}

	/**
	 * procedure_retire modal form
	 * @return [type] 
	 */
	public function procedure_retire_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$procedure_retire_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['procedure_retire_data'] = $this->hr_profile_model->get_procedure_form_manage($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		return $this->template->view('Hr_profile\Views\includes\modal_forms\procedure_retire_modal', $data);
	}

	/**
	 * table procedure retire
	 */
	public function table_procedure_retire() {
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'procedure_retire/table_procedure_retire'));
	}

	/**
	 * add procedure form manage
	 */
	public function add_procedure_form_manage($id = '') {
		$data = $this->request->getPost();

		if (!is_numeric($id)) {
			$response = $this->hr_profile_model->add_procedure_form_manage($data);

			if ($response) {
				$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				app_redirect('hr_profile/procedure_procedure_retire_details/'.$response);
			}else{
				app_redirect('hr_profile/procedure_retires');
			}
		} else {

			$response = $this->hr_profile_model->update_procedure_form_manage($data, $id);
			if ($response) {
				$this->session->setFlashdata("success_message", app_lang("hr_updated_successfully"));
			}
			app_redirect(('hr_profile/procedure_retires'));
		}
	}

	/**
	 * delete procedure form manage
	 * @param  integer $id
	 */
	public function delete_procedure_form_manage($id) {
		if (!hr_has_permission('hr_profile_can_delete_setting') && !is_admin()) {
			app_redirect("forbidden");
		}

		$success = $this->hr_profile_model->delete_procedure_form_manage($id);
		if ($success == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("warning" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * procedure procedure retire details
	 * @param  integer $id
	 * @return view
	 */
	public function procedure_procedure_retire_details($id = '') {
		if (!$id) {
			app_redirect(('hr_profile/procedure_retires'));
		}

		$data['title'] = app_lang('hr_procedure_retire');
		$data['id'] = $id;
		$data['procedure_retire'] = $this->hr_profile_model->get_procedure_retire($id);
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		return $this->template->rander('Hr_profile\Views/procedure_retire/details', $data);

	}

	/**
	 * procedure form
	 */
	public function procedure_form() {
		$data = $this->request->getPost();
		$result = $this->hr_profile_model->add_procedure_retire($data);

		if ($result) {
			$this->session->setFlashdata("success_message", app_lang("hr_added_successfully"));
		}
		app_redirect(('hr_profile/procedure_procedure_retire_details/' . $data['procedure_retire_id']));
	}

	/**
	 * delete procedure retire
	 * @param  integer $id
	 * @return integer
	 */
	public function delete_procedure_retire() {
		$id_detail = $this->request->getPost('id');
		$id = $this->request->getPost('id2');
		$result = $this->hr_profile_model->delete_procedure_retire($id_detail);

		if ($result) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("warning" => false, "message" => app_lang('problem_deleting')));
		}
		app_redirect('hr_profile/procedure_procedure_retire_details/' . $id);

	}

	/**
	 * edit procedure retire
	 * @param  integer $id
	 */
	public function edit_procedure_retire($id) {
		$data = $this->hr_profile_model->get_edit_procedure_retire($id);
		$id = $data->id;
		$procedure_retire_id = $data->procedure_retire_id;
		$people_handle_id = $data->people_handle_id;
		$option_name = json_decode($data->option_name);

		$count_option_value = count(get_object_vars(json_decode($data->option_name))) + 1;

		$rel_name = $data->rel_name;

		echo json_encode([
			'id' => $id,
			'option_name' => $option_name,
			'rel_name' => $rel_name,
			'procedure_retire_id' => $procedure_retire_id,
			'people_handle_id' => $people_handle_id,
			'count_option_value' => $count_option_value,
		]);

	}

	/**
	 * edit procedure form
	 */
	public function edit_procedure_form() {
		$data = $this->request->getPost();
		if (isset($data['id'])) {
			$id = $data['id'];
			unset($data['id']);
		}
		$success = $this->hr_profile_model->edit_procedure_retire($data, $id);
		if ($success) {
			$this->session->setFlashdata("success_message", app_lang("hr_updated_successfully"));
		}
		app_redirect('hr_profile/procedure_procedure_retire_details/' . $data['procedure_retire_id']);

	}

	/**
	 * training
	 * @return view
	 */
	public function training() {
		if (!hr_has_permission('hr_profile_can_view_global_hr_training') && !hr_has_permission('hr_profile_can_view_own_hr_training') && !is_admin()) {
			app_redirect("forbidden");
		}
		$data['group'] = $this->request->getGet('group');
		$data['title'] = app_lang('hr_training');
		$data['tab'][] = 'training_program';
		$data['tab'][] = 'training_library';
		$data['tab'][] = 'training_result';

		if ($data['group'] == '') {
			$data['group'] = 'training_program';
		}
		$data['tabs']['view'] = 'training/' . $data['group'];

		$data['training_table'] = $this->hr_profile_model->get_job_position_training_process();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();

		$department_options = array(
			"deleted" => 0,
		);
		$data['hr_profile_get_department_name'] = $this->Team_model->get_details($department_options)->getResultArray();
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
		$data['staffs'] = $this->hr_profile_model->get_staff_active();

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['training_libraries'] = $this->hr_profile_model->get_training_library();
		$data['training_programs'] = $this->hr_profile_model->get_job_position_training_process();

		return $this->template->view('Hr_profile\Views\training/manage_training', $data);
	}

	/**
	 * training libraries
	 * @return [type] 
	 */
	public function training_libraries() {
		$data = [];
		return $this->template->rander("Hr_profile\Views/training/training_library", $data);
	}

	/**
	 * training_results
	 * @return [type] 
	 */
	public function training_results() {
		$data = [];

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['training_libraries'] = $this->hr_profile_model->get_training_library();
		$data['training_programs'] = $this->hr_profile_model->get_job_position_training_process();
		return $this->template->rander("Hr_profile\Views/training/training_result", $data);
	}

	/**
	 * training programs
	 * @return [type] 
	 */
	public function training_programs() {
		$data = [];

		$data['training_table'] = $this->hr_profile_model->get_job_position_training_process();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();

		$department_options = array(
			"deleted" => 0,
		);
		$data['hr_profile_get_department_name'] = $this->Team_model->get_details($department_options)->getResultArray();
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		return $this->template->rander("Hr_profile\Views/training/training_program", $data);
	}

	/**
	 * Add new position training or update existing
	 * @param integer id
	 */
	public function position_training($id = '') {
		if (!hr_has_permission('hr_profile_can_view_global_hr_training')) {
			app_redirect("forbidden");
		}
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$id = $this->request->getPost('id');
			if (!is_numeric($id)) {
				if (!hr_has_permission('hr_profile_can_create_hr_training')) {
					app_redirect("forbidden");
				}
				$id = $this->hr_profile_model->add_position_training($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
					app_redirect('hr_profile/position_training/' . $id);
				}
			} else {
				if (!hr_has_permission('hr_profile_can_edit_hr_training')) {
					app_redirect("forbidden");
				}
				$success = $this->hr_profile_model->update_position_training($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/position_training/' . $id);
			}
		}
		if ($id == '') {
			$title = app_lang('hr__training_library');
		} else {
			$position_training = $this->hr_profile_model->get_position_training($id);
			$data['position_training'] = $position_training;
			$title = $position_training->subject;
		}
		
		$data['title'] = $title;
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();

		return $this->template->rander('Hr_profile\Views/training/job_position_manage/position_training', $data);
	}

	/* New survey question */
	public function add_training_question() {
		if (!hr_has_permission('hr_profile_can_edit_hr_training') && !hr_has_permission('hr_profile_can_create_hr_training')) {
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die();
		}

		if ($this->request->getPost()) {
			echo json_encode([
				'data' => $this->hr_profile_model->add_training_question($this->request->getPost()),
				'survey_question_only_for_preview' => app_lang('hr_survey_question_only_for_preview'),
				'survey_question_required' => app_lang('hr_survey_question_required'),
				'survey_question_string' => app_lang('hr_question_string'),
			]);
			die();
		}
	}

	/* Update question */
	public function update_training_question() {
		if (!hr_has_permission('hr_profile_can_edit_hr_training') && !hr_has_permission('hr_profile_can_create_hr_training')) {
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die();
		}
		if ($this->request->getPost()) {
			$this->hr_profile_model->update_question($this->request->getPost());
		}
	}

	/* Reorder surveys */
	public function update_training_questions_orders() {
		if (hr_has_permission('hr_profile_can_edit_hr_training') || hr_has_permission('hr_profile_can_create_hr_training')) {
			if ($this->request->getPost()) {
				$this->hr_profile_model->update_survey_questions_orders($this->request->getPost());
			}
		}
	}

	/* Remove survey question */
	public function remove_question($questionid) {
		if (!hr_has_permission('hr_profile_can_edit_hr_training') && !hr_has_permission('hr_profile_can_create_hr_training')) {
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die();
		}

		if ($this->request->getGet()) {
			echo json_encode([
				'success' => $this->hr_profile_model->remove_question($questionid),
			]);
		}
	}

	/* Removes survey checkbox/radio description*/
	public function remove_box_description($questionboxdescriptionid) {
		if (!hr_has_permission('hr_profile_can_edit_hr_training') && !hr_has_permission('hr_profile_can_create_hr_training')) {
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die();
		}
		if ($this->request->getGet()) {
			echo json_encode([
				'success' => $this->hr_profile_model->remove_box_description($questionboxdescriptionid),
			]);
		}
	}

	/* Add box description */
	public function add_box_description($questionid, $boxid) {
		if (!hr_has_permission('hr_profile_can_edit_hr_training') && !hr_has_permission('hr_profile_can_create_hr_training')) {
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die();
		}
		if ($this->request->getGet()) {

			$boxdescriptionid = $this->hr_profile_model->add_box_description($questionid, $boxid);
			echo json_encode([
				'boxdescriptionid' => $boxdescriptionid,
			]);
		}
	}

	/* Update question */
	public function update_training_question_answer() {
		if (!hr_has_permission('hr_profile_can_edit_hr_training') && !hr_has_permission('hr_profile_can_create_hr_training')) {
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die();
		}
		if ($this->request->getPost()) {
			$this->hr_profile_model->update_answer_question($this->request->getPost());
		}
	}

	/**
	 * get training type child
	 * @param  integer $id
	 * @return json
	 */
	public function get_training_type_child($id) {
		$list = $this->hr_profile_model->get_child_training_type($id);
		$html = '';
		foreach ($list as $li) {
			$html .= '<option value="' . $li['training_id'] . '">' . $li['subject'] . '</option>';
		}
		echo json_encode([
			'html' => $html,
		]);
	}

	/**
	 * job position training add edit
	 */
	public function job_position_training_add_edit() {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if(isset($data['files'])){
				unset($data['files']);
			}
			if (!$this->request->getPost('id_training')) {
				$id = $this->hr_profile_model->add_job_position_training_process($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('hr_profile/training_programs');
			} else {
				$id = $data['id_training'];
				unset($data['id_training']);
				$success = $this->hr_profile_model->update_job_position_training_process($data, $id);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("hr_updated_successfully"));
				}
				app_redirect('hr_profile/training_programs');
			}
			die;
		}
	}

	/**
	 * get jobposition fill data
	 * @return json
	 */
	public function get_jobposition_fill_data() {
		$data = $this->request->getPost();
		if ($data['status'] == 'true') {
			$job_position = $this->hr_profile_model->get_position_by_department($data['department_id'], true);

		} else {
			$job_position = $this->hr_profile_model->get_position_by_department(1, false);

		}
		echo json_encode([
			'job_position' => $job_position,
		]);
	}

	/**
	 * job position manage
	 * @return view
	 */
	public function job_position_manage() {
		if (!hr_has_permission('hr_profile_can_view_global_job_description') && !is_admin() && !hr_has_permission('hr_profile_can_view_own_job_description')) {
			app_redirect("forbidden");
		}

		$data['job_p'] = $this->hr_profile_model->get_job_p();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();
		$department_options = array(
			"deleted" => 0,
		);
		$data['hr_profile_get_department_name'] = $this->Team_model->get_details($department_options)->getResultArray();
		$data['title'] = app_lang('hr_position_groups');
		return $this->template->rander('Hr_profile\Views\job_position_manage/job_manage/job_manage', $data);
	}

	/**
	 * table job
	 */
	public function table_job() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'table_job'), $dataPost);
	}

	/**
	 * add job position
	 * @param  integer $id
	 */
	public function job_p($id = '') {

		if ($this->request->getPost()) {

			$message = '';
			$data = $this->request->getPost();

			if (!$this->request->getPost('id')) {
				$id = $this->hr_profile_model->add_job_p($data);

				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect(('hr_profile/job_position_manage'));

			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_profile_model->update_job_p($data, $id);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}

				app_redirect(('hr_profile/job_position_manage'));
			}
			die;
		}
	}

	/**
	 * get job position edit
	 * @param  integer $id
	 * @return json
	 */
	public function get_job_p_edit($id) {

		$list = $this->hr_profile_model->get_job_p($id);

		if (isset($list)) {
			$description = $list->description;
		} else {
			$description = '';
		}

		echo json_encode([
			'description' => $description,
		]);

	}

	/**
	 * delete job position
	 * @param  integer $id
	 */
	public function delete_job_p() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_profile/job_position_manage'));
		}

		$response = $this->hr_profile_model->delete_job_p($id);
		if ($response) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_profile/job_position_manage');
	}

	/**
	 * import job p, Import Job
	 * @return [type]
	 */
	public function import_job_p() {
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		$data['job_positions'] = $this->hr_profile_model->get_job_position();

		$data_staff = $this->hr_profile_model->get_staff(get_staff_user_id1());

		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;

			} else {

				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		return $this->template->view('Hr_profile\Views\hr_profile/job_position_manage/job_manage/import_job', $data);
	}

	/**
	 * import job p excel
	 * @return [type]
	 */
	public function import_job_p_excel() {
		if (!is_admin() && !hr_has_permission('hr_profile_can_create_job_description')) {
			app_redirect("forbidden");
		}
		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		$filename = '';

		if ($this->request->getPost()) {

			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
				//do_action('before_import_leads');

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$rows = [];

						$objReader = new PHPExcel_Reader_Excel2007();
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($newFilePath);
						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
						$sheet = $objPHPExcel->getActiveSheet();

						//init file error start
						$dataError = new PHPExcel();
						$dataError->setActiveSheetIndex(0);
						//create title
						$dataError->getActiveSheet()->setTitle('error');
						$dataError->getActiveSheet()->getColumnDimension('A')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('B')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('C')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('D')->setWidth(20);
						//Set bold for header
						$dataError->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true);

						$dataError->getActiveSheet()->setCellValue('A1', app_lang('job_name'));
						$dataError->getActiveSheet()->setCellValue('B1', app_lang('description'));
						$dataError->getActiveSheet()->setCellValue('C1', app_lang('hr_create_job_position_default'));
						$dataError->getActiveSheet()->setCellValue('D1', app_lang('error'));
						//init file error end

						// start Write data error from line 2
						$styleArray = array(
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => 'ff0000'),
							));

						$numRow = 2;
						$total_rows = 0;
						//get data for compare

						foreach ($rowIterator as $row) {
							$rowIndex = $row->getRowIndex();
							if ($rowIndex > 1) {

								$rd = array();
								$flag = 0;

								$string_error = '';

								$value_job_name = $sheet->getCell('A' . $rowIndex)->getValue();
								$value_description = $sheet->getCell('B' . $rowIndex)->getValue();
								$value_create_default = $sheet->getCell('C' . $rowIndex)->getValue();

								if (is_null($value_job_name) == true) {
									$string_error .= app_lang('job_name') . app_lang('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_description) == true) {
									$string_error .= app_lang('description') . app_lang('not_yet_entered');
									$flag = 1;
								}

								if (($flag == 1)) {
									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $string_error)->getStyle('D' . $numRow)->applyFromArray($styleArray);

									$numRow++;
								}

								if (($flag == 0)) {

									if (is_numeric($value_create_default) && $value_create_default == '0') {
										$rd['create_job_position'] = 'on';
									}
									$rd['job_name'] = $sheet->getCell('A' . $rowIndex)->getValue();
									$rd['description'] = $sheet->getCell('B' . $rowIndex)->getValue();

								}

								if (get_staff_user_id1() != '' && $flag == 0) {
									$rows[] = $rd;
									$this->hr_profile_model->add_job_p($rd);
								}
								$total_rows++;
							}
						}

						$total_rows = $total_rows;
						$data['total_rows_post'] = count($rows);
						$total_row_success = count($rows);
						$total_row_false = $total_rows - (int) count($rows);
						$dataerror = $dataError;
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);

							$filename = 'Import_job_error_' . get_staff_user_id1() . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$objWriter->save(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));

						}
						$import_result = true;
						@delete_dir($tmpDir);

					}
				} else {
					set_alert('warning', app_lang('import_upload_failed'));
				}
			}

		}
		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id1(),
			'filename' => HR_PROFILE_ERROR . $filename,
		]);

	}

	/**
	 * job positions
	 * @param  integer $id
	 * @return view
	 */
	public function job_positions($id = '') {
		if (!hr_has_permission('hr_profile_can_view_global_job_description') && !hr_has_permission('hr_profile_can_view_own_job_description')) {
			app_redirect("forbidden");
		}
		$get_department_by_manager = $this->hr_profile_model->get_department_by_manager();

		$data['job_p_id'] = $this->hr_profile_model->get_job_p();
		$department_options = array(
			"deleted" => 0,
		);
		$data['hr_profile_get_department_name'] = $this->Team_model->get_details($department_options)->getResultArray();

		$data['get_job_position'] = $this->hr_profile_model->get_job_position();
		$data['title'] = app_lang('hr_job_descriptions');

		return $this->template->rander('Hr_profile\Views/job_position_manage/position_manage/position_manage', $data);
	}

	/**
	 * new job position modal form
	 * @return [type] 
	 */
	public function new_job_position_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$contract_type_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['job_position_data'] = $this->hr_profile_model->get_job_position($id);
		}else{
			$id = '';
		}

		$data['job_p_id'] = $this->hr_profile_model->get_job_p();

		$department_options = array(
			"deleted" => 0,
		);
		$data['hr_profile_get_department_name'] = $this->Team_model->get_details($department_options)->getResultArray();
		$data['get_job_position'] = $this->hr_profile_model->get_job_position();
		$data['job_position_code_sample'] = $this->hr_profile_model->create_code('position_code');
		$data['id'] = $id;
		return $this->template->view('Hr_profile\Views\job_position_manage\modals\position_modal', $data);
	}

	/**
	 * add or update job position
	 * @param  integer $id
	 */
	public function job_position($id = '') {

		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if(isset($data['files'])){
				unset($data['files']);
			}
			if (!$this->request->getPost('position_id')) {
				$id = $this->hr_profile_model->add_job_position($data);

				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}

				app_redirect('hr_profile/job_positions');

			} else {

				$position_id = $data['position_id'];
				unset($data['position_id']);
				$success = $this->hr_profile_model->update_job_position($data, $position_id);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/job_positions');
			}
		}
	}

	/**
	 * table job position
	 * @return [type]
	 */
	public function table_job_position() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'job_position_manage/position_manage/table_job_position'), $dataPost);
	}

	/**
	 * job position delete tag item
	 * @param  String $tag_id
	 * @return json
	 */
	public function job_position_delete_tag_item($tag_id) {

		$result = $this->hr_profile_model->delete_tag_item($tag_id);
		if ($result == 'true') {
			$message = app_lang('hr_deleted');
			$status = 'true';
		} else {
			$message = app_lang('problem_deleting');
			$status = 'fasle';
		}

		echo json_encode([
			'message' => $message,
			'status' => $status,
		]);
	}

	/**
	 * hrm preview jobposition file
	 * @param  [type] $id
	 * @param  [type] $rel_id
	 * @return [type]
	 */
	public function preview_job_position_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id1());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Hr_profile\Views\hr_profile/job_position_manage/position_manage/preview_position_file', $data);
	}

	public function delete_hr_profile_job_position_attachment_file($attachment_id) {
		if (!hr_has_permission('hr_profile_can_delete_job_description') && !is_admin()) {
			app_redirect("forbidden");
		}

		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->hr_profile_model->delete_hr_job_position_attachment_file($attachment_id),
		]);
	}

	/**
	 * job position view edit
	 * @param  string $id
	 * @return view
	 */
	public function job_position_view_edit($id = '', $parent_id = '') {

		if (!hr_has_permission('hr_profile_can_view_global_job_description') && !hr_has_permission('hr_profile_can_view_own_job_description')) {
			app_redirect("forbidden");
		}
		if ($id == '') {
			$title = app_lang('add_new', app_lang('hr_training'));
		} else {

			$data['job_position_general'] = $this->hr_profile_model->get_job_position($id);
			$data['job_position_id'] = $id;

			$data['job_position_attachment'] = $this->hr_profile_model->get_hr_profile_attachments_file($id, 'job_position');

		}

		$data['list_job_p'] = $this->hr_profile_model->get_job_p();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['parent_id'] = $parent_id;
		$department_options = array(
			"deleted" => 0,
		);
		$data['hr_profile_get_department_name'] = $this->Team_model->get_details($department_options)->getResultArray();

		return $this->template->rander('Hr_profile\Views/job_position_manage/view_edit_jobposition', $data);

	}

	/**
	 * get list job position tags file
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_list_job_position_tags_file($id) {
		$list = $this->hr_profile_model->get_list_job_position_tags_file($id);

		$job_position_de = $this->hr_profile_model->get_job_position($id);
		if (isset($job_position_de)) {
			$description = $job_position_de->job_position_description;

			$job_p = $this->hr_profile_model->get_job_p($job_position_de->job_p_id);
			$job_p = isset($job_p) ? $job_p->job_id : 0;
		} else {
			$description = '';
			$job_p = 0;

		}

		if((get_tags_in($id,'job_position') != null)){
			$item_value = implode(',', get_tags_in($id,'job_position')) ;
		}else{

			$item_value = '';
		}

		echo json_encode([
			'description' => $description,
			'htmltag' => $list['htmltag'],
			'htmlfile' => $list['htmlfile'],
			'job_position_html' => render_tags(get_tags_in($id, 'job_position')),
			'job_p' => $job_p,
			'item_value' => $item_value,
			
		]);
	}

	/**
	 * get position by department
	 * @return json
	 */
	public function get_position_by_department() {
		$data = $this->request->getPost();
		if ($data['status'] == 'true') {
			$job_position = $this->hr_profile_model->get_position_by_department($data['department_id'], true);
		} else {
			$job_position = $this->hr_profile_model->get_position_by_department(1, false);
		}
		echo json_encode([
			'job_position' => $job_position,
		]);

	}

	/**
	 * delete job position
	 * @param  integer $id
	 * @param  integer $job_p_id
	 */
	public function delete_job_position() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_profile/job_positions'));
		}
		$response = $this->hr_profile_model->delete_job_position($id);

		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("hr_is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_profile/job_positions');
	}

	/**
	 * get staff salary form
	 * @return json
	 */
	public function get_staff_salary_form() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				$id = $this->request->getPost('id');
				$name_object = $this->hr_profile_model->get_salary_form($id);
			}
		}
		if ($name_object) {
			echo json_encode([
				'salary_val' => (String) hr_profile_reformat_currency($name_object->salary_val),
			]);
		}

	}

	/**
	 * get staff allowance type
	 * @return json
	 */
	public function get_staff_allowance_type() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				$id = $this->request->getPost('id');
				$name_object = $this->hr_profile_model->get_allowance_type($id);
			}
		}

		if ($name_object) {
			echo json_encode([
				'allowance_val' => (String) hr_profile_reformat_currency($name_object->allowance_val),
			]);
		}
	}

	/**
	 * job position salary add edit
	 */
	public function job_position_salary_add_edit() {
		if (!hr_has_permission('hr_profile_can_create_job_description')) {
			app_redirect("forbidden");
		}

		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if ($this->request->getPost()) {
				$job_position_id = $data['job_position_id'];
				$id = $this->hr_profile_model->job_position_add_update_salary_scale($data);
				if ($id) {
					$success = true;
					$message = app_lang('added_successfully');
					set_alert('success', $message);
				}
				app_redirect(('hr_profile/job_position_view_edit/' . $job_position_id . '?tab=salary_scale'));
			}
			die;
		}
	}

	/**
	 * save setting reception staff
	 */
	public function save_setting_reception_staff() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$data_asset['name'] = $data['asset_name'];
			$data_training['training_type'] = $data['training_type'];
			$this->hr_profile_model->add_manage_info_reception($data);
			$this->hr_profile_model->add_setting_training($data_training);
			$this->hr_profile_model->add_setting_asset_allocation($data_asset);

			$this->session->setFlashdata("success_message", app_lang("hr_updated_successfully"));
			app_redirect('hr_profile/reception_staffs');
		}
	}

	/**
	 * add new reception
	 */
	public function add_new_reception() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			$list_staff = $this->hr_profile_model->get_staff_info_id($data['staff_id']);
			$data_rec_tranfer['staffid'] = $list_staff->id;
			$data_rec_tranfer['firstname'] = isset($list_staff->first_name) ? $list_staff->first_name : '';
			$data_rec_tranfer['birthday'] = isset($list_staff->dob) ? $list_staff->dob : '';
			$data_rec_tranfer['staffidentifi'] = isset($list_staff->staffidentifi) ? $list_staff->staffidentifi : '';

			// Create records for management reception
			$this->hr_profile_model->add_rec_transfer_records($data_rec_tranfer);

			//1 Reception information board
			$this->hr_profile_model->add_manage_info_reception_for_staff($list_staff->id, $data);

			//2 Create a property allocation record
			if (isset($data['asset_name'])) {
				$list_asset = [];
				foreach ($data['asset_name'] as $key => $value) {
					array_push($list_asset, ['name' => $value]);
				}
				if ($list_asset) {
					$this->hr_profile_model->add_asset_staff($list_staff->id, $list_asset);
				}
			}

			//3 Create a training record

			if ($list_staff->job_position != '' && isset($data['training_program'])) {

				$jp_interview_training = $this->hr_profile_model->get_job_position_training_de($data['training_program']);

				if ($jp_interview_training) {
					$this->hr_profile_model->add_training_staff($jp_interview_training, $list_staff->id);
					if (isset($list_staff->email)) {
						if ($list_staff->email != '') {
							$this->send_training_staff($list_staff->email, $list_staff->job_position, $data['training_type'], $jp_interview_training->position_training_id, $list_staff->id);
						}
					}
				}
			}

			//4 Create a record with additional profile information
			if (isset($data['info_name'])) {
				if ($data['info_name']) {
					$this->hr_profile_model->add_transfer_records_reception($data['info_name'], $data['staff_id']);
				}
			}

			$this->session->setFlashdata("success_message", app_lang("added_successfully"));
			app_redirect('hr_profile/reception_staff');
		}

	}

	/**
	 * send training staff
	 * @param  [type] $email
	 * @param  [type] $position_id
	 * @param  string $training_type
	 * @return [type]
	 */
	public function send_training_staff($email, $position_id, $training_type = '', $position_training_id = '', $staffid = '') {
		$mes = 'hr_please_complete_the_tests_below_to_complete_the_training_program';

		if ($position_training_id != '') {
			$data_training = $this->hr_profile_model->get_list_position_training_by_id_training($position_training_id);

			$data['description'] = '
			<div >
			<div> ' . app_lang('hr_please_complete_the_tests_below_to_complete_the_training_program') . '</div>
			<div> ' . app_lang('hr_please_log_in_training') . '</div>
			<div></div>';
			foreach ($data_training as $key => $value) {
				$data['description'] .= '<div>';
				$data['description'] .= '&#9755; <a href="' . get_uri('hr_profile/training_detail/'.$value['training_id'] . '/' . $value['hash']) . '">' . get_uri('hr_profile/training_detail/') . '' . $value['slug'] . '</a>';
				$data['description'] .= '</div>';
			}

			$data['description'] .= '</div>';

			if(is_numeric($staffid)){
				/*Send notify*/
				$notify_data = ['hr_send_training_staff_id' => $staffid];
				hr_log_notification($mes, $notify_data, get_staff_user_id1() ,$staffid);
			}

			if(strlen($email) > 0){
				/*send mail*/
				$subject = get_default_company_name() . ': ' . app_lang('hr_new_training_for_you');
				$message = $data['description'];
				send_app_mail($email, $subject, $message);
			}

		}
	}

	/**
	 * get percent complete
	 * @param  string $id
	 * @return [type]
	 */
	public function get_percent_complete($id = '') {
		if ($id != '') {

			$options = array(
				"user_type" => "staff",
				"id" => $id,
			);
			$data['staff'] = $this->Users_model->get_details($options)->getResultArray();

			$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
			$staff_array = json_decode(json_encode($data['staff']), true);
			$count_info = 0;
			$count_info_total = 0;
			$count_effect_total = 0;
			$count_total = 0;
			//check list
			$checklist_effect = 0;
			$listchecklist = $this->hr_profile_model->get_group_checklist_allocation_by_staff_id($id);
			$count_total = count($listchecklist);

			foreach ($listchecklist as $value) {
				$checklist = $this->hr_profile_model->get_checklist_allocation_by_group_id($value['id']);
				$total = count($checklist);
				$effect_checklist = 0;
				foreach ($checklist as $item) {
					if ((int) $item['status'] == 1) {
						$effect_checklist += 1;
					}
				}
				if ($effect_checklist == $total) {
					$count_effect_total += 1;
				}
			}

			//recpetion
			foreach ($data['list_reception_staff_transfer'] as $value) {
				$count_info_total += 1;
				if ($staff_array[$value['meta']] != '') {
					$count_info += 1;
				}
			}

			$percent_info_total = $this->hr_profile_model->getPercent($count_info_total, $count_info);
			if ($percent_info_total >= 100) {
				$count_effect_total += 1;
			}
			if ($count_info_total > 0) {
				$count_total += 1;
			}

			$data['list_staff_asset'] = $this->hr_profile_model->get_allocation_asset($id);
			$count_asset = 0;
			$count_asset_total = 0;
			foreach ($data['list_staff_asset'] as $value) {
				$count_asset_total += 1;
				if ($value['status_allocation'] == 1) {
					$count_asset += 1;
				}
			}

			$percent_asset_total = $this->hr_profile_model->getPercent($count_asset_total, $count_asset);
			if ($percent_asset_total >= 100) {
				$count_effect_total += 1;
			}
			if ($count_asset_total > 0) {
				$count_total += 1;
			}

			//Get the latest employee's training result.
			$list_training_allocation = $this->hr_profile_model->get_training_allocation_staff($id);
			if ($list_training_allocation) {

				$data_marks = $this->get_mark_staff($id, $list_training_allocation->training_process_id);

				if (count($data_marks['staff_training_result']) > 0) {
					$count_total += 1;

					$training_allocation_min_point = 0;

					if (isset($list_training_allocation)) {

						$job_position_training = $this->hr_profile_model->get_job_position_training_de($list_training_allocation->jp_interview_training_id);

						if ($job_position_training) {
							$training_allocation_min_point = $job_position_training->mint_point;
						}
					}

					if ((float) $data_marks['training_program_point'] >= (float) $training_allocation_min_point) {
						$count_effect_total += 1;
					}

				}
			}

			return $this->hr_profile_model->getPercent($count_total, $count_effect_total);
		}
	}

	/**
	 * get mark staff
	 * @param  integer $id_staff
	 * @return array
	 */
	public function get_mark_staff($id_staff, $training_process_id) {
		$array_training_point = [];
		$training_program_point = 0;

		//Get the latest employee's training result.
		$trainig_resultset = $this->hr_profile_model->get_resultset_training($id_staff, $training_process_id);

		$array_training_resultset = [];
		$array_resultsetid = [];
		$list_resultset_id = '';

		foreach ($trainig_resultset as $item) {
			if (count($array_training_resultset) == 0) {
				array_push($array_training_resultset, $item['trainingid']);
				array_push($array_resultsetid, $item['resultsetid']);

				$list_resultset_id .= '' . $item['resultsetid'] . ',';
			}
			if (!in_array($item['trainingid'], $array_training_resultset)) {
				array_push($array_training_resultset, $item['trainingid']);
				array_push($array_resultsetid, $item['resultsetid']);

				$list_resultset_id .= '' . $item['resultsetid'] . ',';
			}
		}

		$list_resultset_id = rtrim($list_resultset_id, ",");
		$count_out = 0;
		if ($list_resultset_id == "") {
			$list_resultset_id = '0';
		} else {
			$count_out = count($array_training_resultset);
		}

		$array_result = [];
		foreach ($array_training_resultset as $key => $training_id) {
			$total_question = 0;
			$total_question_point = 0;

			$total_point = 0;
			$training_library_name = '';
			$training_question_forms = $this->hr_profile_model->hr_get_training_question_form_by_relid($training_id);
			$hr_position_training = $this->hr_profile_model->get_board_mark_form($training_id);
			$total_question = count($training_question_forms);
			if ($hr_position_training) {
				$training_library_name .= $hr_position_training->subject;
			}

			foreach ($training_question_forms as $question) {
				$flag_check_correct = true;

				$get_id_correct = $this->hr_profile_model->get_id_result_correct($question['questionid']);
				$form_results = $this->hr_profile_model->hr_get_form_results_by_resultsetid($array_resultsetid[$key], $question['questionid']);

				if (count($get_id_correct) == count($form_results)) {
					foreach ($get_id_correct as $correct_key => $correct_value) {
						if (!in_array($correct_value, $form_results)) {
							$flag_check_correct = false;
						}
					}
				} else {
					$flag_check_correct = false;
				}

				$result_point = $this->hr_profile_model->get_point_training_question_form($question['questionid']);
				$total_question_point += $result_point->point;

				if ($flag_check_correct == true) {
					$total_point += $result_point->point;
					$training_program_point += $result_point->point;
				}

			}

			array_push($array_training_point, [
				'training_name' => $training_library_name,
				'total_point' => $total_point,
				'training_id' => $training_id,
				'total_question' => $total_question,
				'total_question_point' => $total_question_point,
			]);
		}

		$response = [];
		$response['training_program_point'] = $training_program_point;
		$response['staff_training_result'] = $array_training_point;

		return $response;
	}

	/**
	 * delete reception
	 * @param  integer $id
	 */
	public function delete_reception() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect('hr_profile/reception_staff');
		}

		$this->hr_profile_model->delete_manage_info_reception($id);
		$this->hr_profile_model->delete_setting_training($id);
		$this->hr_profile_model->delete_setting_asset_allocation($id);

		$success = $this->hr_profile_model->delete_reception($id);
		if ($success == true) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		}
		app_redirect('hr_profile/reception_staff');
	}

	/**
	 * get_reception_modal
	 * @return [type] 
	 */
	public function get_reception_modal() {
		$this->access_only_team_members();
		$id = $this->request->getPost('id');
		
		$options = array(
			"user_type" => "staff",
			"id" => $id,
		);
		$data['staff'] = $this->Users_model->get_details($options)->getRow();

		if (isset($data['staff'])) {
			$data['position'] = $this->hr_profile_model->get_job_position($data['staff']->job_position);
			$data['department'] = $this->hr_profile_model->getdepartment_name($data['staff']->id);
			$data['group_checklist'] = $this->hr_profile_model->get_group_checklist_allocation_by_staff_id($data['staff']->id);
			$data['list_staff_asset'] = $this->hr_profile_model->get_allocation_asset($data['staff']->id);

			if (($data['staff']->job_position) && (is_numeric($data['staff']->job_position))) {
				$has_training = 1;
				$data['training_allocation_min_point'] = 0;
				$data['list_training_allocation'] = $this->hr_profile_model->get_training_allocation_staff($data['staff']->id);

				if (isset($data['list_training_allocation'])) {

					$job_position_training = $this->hr_profile_model->get_job_position_training_de($data['list_training_allocation']->jp_interview_training_id);

					if ($job_position_training) {
						$data['training_allocation_min_point'] = $job_position_training->mint_point;
					}

					if ($data['list_training_allocation']) {
						$training_process_id = $data['list_training_allocation']->training_process_id;

						$data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($data['list_training_allocation']->training_process_id);

						//Get the latest employee's training result.
						$training_results = $this->get_mark_staff($data['staff']->id, $training_process_id);

						$data['training_program_point'] = $training_results['training_program_point'];

						//have not done the test data
						$staff_training_result = [];
						foreach ($data['list_training'] as $key => $value) {
							$staff_training_result[$value['training_id']] = [
								'training_name' => $value['subject'],
								'total_point' => 0,
								'training_id' => $value['training_id'],
								'total_question' => 0,
								'total_question_point' => 0,
							];
						}

						//did the test
						if (count($training_results['staff_training_result']) > 0) {

							foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
								if (isset($staff_training_result[$result_value['training_id']])) {
									unset($staff_training_result[$result_value['training_id']]);
								}
							}

							$data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

						} else {
							$data['staff_training_result'] = $staff_training_result;
						}

						if ((float) $training_results['training_program_point'] >= (float) $data['training_allocation_min_point']) {
							$data['complete'] = 0;
						} else {
							$data['complete'] = 1;
						}

					}
				}
			}
			return $this->template->view('Hr_profile\Views/reception_staff/reception_staff_sidebar', $data);
		}

	}

	/**
	 * get reception
	 * @param  integer $id
	 * @return json
	 */
	public function get_reception($id = '') {
		$this->load->model('departments_model');
		$this->load->model('staff_model');
		$data['staff'] = $this->staff_model->get($id);

		if (isset($data['staff'])) {
			$data['position'] = $this->hr_profile_model->get_job_position($data['staff']->job_position);
			$data['department'] = $this->hr_profile_model->getdepartment_name($data['staff']->staffid);
			$data['group_checklist'] = $this->hr_profile_model->get_group_checklist_allocation_by_staff_id($data['staff']->staffid);
			$data['list_staff_asset'] = $this->hr_profile_model->get_allocation_asset($data['staff']->staffid);

			if (($data['staff']->job_position) && (is_numeric($data['staff']->job_position))) {
				$has_training = 1;
				$data['training_allocation_min_point'] = 0;
				$data['list_training_allocation'] = $this->hr_profile_model->get_training_allocation_staff($data['staff']->staffid);

				if (isset($data['list_training_allocation'])) {

					$job_position_training = $this->hr_profile_model->get_job_position_training_de($data['list_training_allocation']->jp_interview_training_id);

					if ($job_position_training) {
						$data['training_allocation_min_point'] = $job_position_training->mint_point;
					}

					if ($data['list_training_allocation']) {
						$training_process_id = $data['list_training_allocation']->training_process_id;

						$data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($data['list_training_allocation']->training_process_id);

						//Get the latest employee's training result.
						$training_results = $this->get_mark_staff($data['staff']->staffid, $training_process_id);

						$data['training_program_point'] = $training_results['training_program_point'];

						//have not done the test data
						$staff_training_result = [];
						foreach ($data['list_training'] as $key => $value) {
							$staff_training_result[$value['training_id']] = [
								'training_name' => $value['subject'],
								'total_point' => 0,
								'training_id' => $value['training_id'],
								'total_question' => 0,
								'total_question_point' => 0,
							];
						}

						//did the test
						if (count($training_results['staff_training_result']) > 0) {

							foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
								if (isset($staff_training_result[$result_value['training_id']])) {
									unset($staff_training_result[$result_value['training_id']]);
								}
							}

							$data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

						} else {
							$data['staff_training_result'] = $staff_training_result;
						}

						if ((float) $training_results['training_program_point'] >= (float) $data['training_allocation_min_point']) {
							$data['complete'] = 0;
						} else {
							$data['complete'] = 1;
						}

					}
				}
			}

			echo json_encode([
				'data' => $this->template->rander('Hr_profile\Views\reception_staff/reception_staff_sidebar', $data, true),
				'success' => true,
			]);
		}
	}

/**
 * change status checklist
 * @return json
 */
public function change_status_checklist() {
	if ($this->request->getPost()) {
		$data = $this->request->getPost();
		$success = $this->hr_profile_model->update_checklist($data);
		if ($success == true) {
			echo json_encode([
				'success' => true,
			]);
		}
	}
}
/**
 * add new asset
 * @param integer $id
 */
public function add_new_asset($id) {
	if ($this->request->getPost()) {
		$data = $this->request->getPost();
		$list_tt = explode(',', $data['name']);
		$this->hr_profile_model->add_new_asset_staff($id, $list_tt);
		$list_asset = $this->hr_profile_model->get_allocation_asset($id);

		$html = '';
		foreach ($list_asset as $value) {
			$checked = '';
			if ($value['status_allocation'] == 1) {
				$checked = 'checked';
			}
			$html .= '<div class="row item_hover">
			<div class="col-md-7">
			<div class="checkbox">
			<input data-can-view="" type="checkbox" class="capability" id="' . $value['asset_name'] . '" name="asset_staff[]" data-id="' . $value['allocation_id'] . '" value="' . $value['status_allocation'] . '" ' . $checked . ' onclick="active_asset(this);">
			<label for="' . $value['asset_name'] . '">
			' . $value['asset_name'] . '
			</label>
			</div>
			</div>
			<div class="col-md-3 pt-10">
			<a href="#" class="text-danger" onclick="delete_asset(this);"  data-id="' . $value['allocation_id'] . '" >' . app_lang('delete') . '</a>
			</div>
			</div>';
		}
		echo json_encode([
			'data' => $html,
			'success' => true,
		]);
	}
}
/**
 * change status allocation asset
 * @return json
 */
public function change_status_allocation_asset() {
	if ($this->request->getPost()) {
		$data = $this->request->getPost();
		$success = $this->hr_profile_model->update_asset_staff($data);
		if ($success == true) {
			echo json_encode([
				'success' => true,
			]);
		}
	}
}
/**
 * delete asset
 * @param  integer $id
 * @param  integer $id2
 * @return json
 */
public function delete_asset($id, $id2) {
	$success = $this->hr_profile_model->delete_allocation_asset($id);
	if ($success == true) {

		$list_asset = $this->hr_profile_model->get_allocation_asset($id2);

		$html = '';
		foreach ($list_asset as $value) {
			$checked = '';
			if ($value['status_allocation'] == 1) {
				$checked = 'checked';
			}
			$html .= '<div class="row item_hover">
			<div class="col-md-7">
			<div class="checkbox">
			<input data-can-view="" type="checkbox" class="capability" name="asset_staff[]" data-id="' . $value['allocation_id'] . '" value="' . $value['status_allocation'] . '" ' . $checked . ' onclick="active_asset(this);">
			<label>
			' . $value['asset_name'] . '
			</label>
			</div>
			</div>
			<div class="col-md-3 pt-10">
			<a href="#" class="text-danger" onclick="delete_asset(this);"  data-id="' . $value['allocation_id'] . '" >' . app_lang('delete') . '</a>
			</div>
			</div>';
		}
		echo json_encode([
			'data' => $html,
			'success' => true,
		]);
	} else {
		echo json_encode([
			'success' => false,
		]);
	}
}

	/**
	 * staff infor
	 * @return view
	 */
	public function staff_infor() {
		if (!hr_has_permission('hr_profile_can_view_global_hr_records') && !hr_has_permission('hr_profile_can_view_own_hr_records')) {
			app_redirect("forbidden");
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staff_members'] = $this->Users_model->get_details($options)->getResultArray();

		$data['title'] = app_lang('hr_hr_profile');
		$data['dep_tree'] = json_encode($this->hr_profile_model->get_department_tree());
		$data['staff_dep_tree'] = json_encode($this->hr_profile_model->get_staff_tree());

		//load deparment by manager
		if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_records')) {
			//View own
			$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart_v2());

		} else {
			//admin or view global
			$data['staff_members_chart'] = json_encode($this->hr_profile_model->get_data_chart());
		}

		$data['staff_role'] = $this->hr_profile_model->get_job_position();
		$data['title'] = app_lang("HR_records");
		return $this->template->rander('Hr_profile\Views\hr_record/manage_staff', $data);
	}

	/**
	 * table
	 */
	public function table() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'table_staff'), $dataPost);
	}

	/**
	 * importxlsx
	 * @return view
	 */
	public function importxlsx() {
		if (!$this->login_user->is_admin && !hr_has_permission('hr_profile_can_create_hr_records')) {
			app_redirect("forbidden");
		}

		$user_id = $this->login_user->id;
		$_personal_language = get_setting('user_' . $user_id . '_personal_language');
		if(strlen($_personal_language) == 0){
			$_personal_language = get_setting("language");
		}

		$data['active_language'] = $_personal_language;
		return $this->template->rander('Hr_profile\Views/import_xlsx', $data);
	}

	/**
	 * import employees excel
	 * @return [type]
	 */
	public function import_employees_excel() {
		if (!hr_has_permission('hr_profile_can_create_hr_records') && !hr_has_permission('hr_profile_can_edit_hr_records') && !is_admin()) {
			app_redirect("forbidden");
		}

		$user_id = $this->login_user->id;

		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		$filename = '';
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before(1);

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];
					$arr_update = [];

					$temp_file_path = get_setting("temp_file_path");
					$tmpDir = getcwd() . '/' . $temp_file_path;
					if (!is_dir($tmpDir)) {
						if (!mkdir($tmpDir, 0777, true)) {
							die('Failed to create file folders.');
						}
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {

						//Writer file
						$writer_header = array(

							app_lang('id') => 'string',
							app_lang('hr_staff_code') => 'string',
							app_lang('hr_firstname') => 'string',
							app_lang('hr_lastname') => 'string',
							app_lang('hr_sex') => 'string',
							app_lang('hr_hr_birthday') => 'string',
							app_lang('email') => 'string',
							app_lang('phone') => 'string',
							app_lang('hr_hr_workplace') => 'string',
							app_lang('hr_status_work') => 'string',
							app_lang('hr_hr_job_position') => 'string',
							app_lang('hr_team_manage') => 'string',
							app_lang('staff_add_edit_role') => 'string',
							app_lang('hr_hr_literacy') => 'string',
							app_lang('staff_hourly_rate') => 'string',
							app_lang('staff_add_edit_departments') => 'string',
							app_lang('staff_add_edit_password') => 'string',
							app_lang('hr_hr_home_town') => 'string',
							app_lang('hr_hr_marital_status') => 'string',
							app_lang('hr_current_address') => 'string',
							app_lang('hr_hr_nation') => 'string',
							app_lang('hr_hr_birthplace') => 'string',
							app_lang('hr_hr_religion') => 'string',
							app_lang('hr_citizen_identification') => 'string',
							app_lang('hr_license_date') => 'string',
							app_lang('hr_hr_place_of_issue') => 'string',
							app_lang('hr_hr_resident') => 'string',
							app_lang('hr_bank_account_number') => 'string',
							app_lang('hr_bank_account_name') => 'string',
							app_lang('hr_bank_name') => 'string',
							app_lang('hr_Personal_tax_code') => 'string',

							app_lang('error') => 'string',
						);

						$writer = new \XLSXWriter();

						$widths = [40, 40, 40, 50, 40, 40, 40, 40, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50];
						//orange: do not update
						$col_style1 = [0, 1];
						$style1 = ['widths' => $widths, 'fill' => '#fc2d42', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

						//red: required
						$col_style2 = [2, 3, 6, 9, 10];
						$style2 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

						//otherwise blue: can be update

						$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
							$col_style1, $style1, $col_style2, $style2);

						$row_style1 = array('fill' => '#F8CBAD', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
						$row_style2 = array('fill' => '#FCE4D6', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

						//Reader file
						$xlsx = new \XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);
						$arr_header = [];

						$arr_header['id'] = 0;
						$arr_header['staff_identifi'] = 1;
						$arr_header['first_name'] = 2;
						$arr_header['last_name'] = 3;
						$arr_header['gender'] = 4;
						$arr_header['dob'] = 5;
						$arr_header['email'] = 6;
						$arr_header['phone'] = 7;
						$arr_header['workplace'] = 8;
						$arr_header['status_work'] = 9;
						$arr_header['job_position'] = 10;
						$arr_header['team_manage'] = 11;
						$arr_header['role_id'] = 12;
						$arr_header['literacy'] = 13;
						$arr_header['hourly_rate'] = 14;
						$arr_header['department'] = 15;
						$arr_header['password'] = 16;
						$arr_header['home_town'] = 17;
						$arr_header['marital_status'] = 18;
						$arr_header['address'] = 19;
						$arr_header['nation'] = 20;
						$arr_header['birthplace'] = 21;
						$arr_header['religion'] = 22;
						$arr_header['identification'] = 23;
						$arr_header['days_for_identity'] = 24;
						$arr_header['place_of_issue'] = 25;
						$arr_header['resident'] = 26;
						$arr_header['account_number'] = 27;
						$arr_header['name_account'] = 28;
						$arr_header['issue_bank'] = 29;
						$arr_header['Personal_tax_code'] = 30;

						$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';
						$reg_day = '#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#';

						$staff_str_result = '';
						$staff_prefix_str = '';
						$staff_prefix_str .= get_setting('staff_code_prefix');
						$staff_next_number = (int) get_setting('staff_code_number');
						$staff_str_result .= $staff_prefix_str . str_pad($staff_next_number, 5, '0', STR_PAD_LEFT);

						//job position data
						$job_position_data = [];
						$job_positions = $this->hr_profile_model->get_job_position();

						foreach ($job_positions as $key => $job_position) {
							$job_position_data[$job_position['position_code']] = $job_position;
						}

						//direct manager
						$staff_data = [];
						$list_staffs = $this->hr_profile_model->get_staff();
						foreach ($list_staffs as $key => $list_staff) {
							$staff_data[$list_staff['staff_identifi']] = $list_staff;
						}

						//get role data
						$roles_data = [];
						$role_options = array(
							"deleted" => 0,
						);
						$list_roles = $this->Roles_model->get_details($role_options)->getResultArray();
						foreach ($list_roles as $list_role) {
							$roles_data[$list_role['id']] = $list_role;
						}

						//get workplace data
						$list_workplaces = $this->hr_profile_model->get_workplace();

						//get list department
						$department_options = array(
							"deleted" => 0,
						);
						$list_departments = $this->Team_model->get_details($department_options)->getResultArray();


						$total_rows = 0;
						$total_row_false = 0;
						$total_row_success = 0;

						$column_key = $data[1];

						//write the next row (row2)
						$writer->writeSheetRow('Sheet1', array_keys($arr_header));

						for ($row = 2; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;
							$flag_mail = 0;

							$string_error = '';

							$flag_value_job_position = 0;
							$flag_value_team_manage = 0;
							$flag_value_workplace = 0;
							$flag_value_role = 0;
							$flag_value_department = [];
							$permissions = [];

							$value_staffid = isset($data[$row][$arr_header['id']]) ? $data[$row][$arr_header['id']] : '';
							$value_staff_identifi = isset($data[$row][$arr_header['staff_identifi']]) ? $data[$row][$arr_header['staff_identifi']] : '';
							$value_firstname = isset($data[$row][$arr_header['first_name']]) ? $data[$row][$arr_header['first_name']] : '';
							$value_lastname = isset($data[$row][$arr_header['last_name']]) ? $data[$row][$arr_header['last_name']] : '';
							$value_sex = isset($data[$row][$arr_header['gender']]) ? $data[$row][$arr_header['gender']] : '';

							$value_birthday = isset($data[$row][$arr_header['dob']]) ? $data[$row][$arr_header['dob']] : '';
							$value_email = isset($data[$row][$arr_header['email']]) ? $data[$row][$arr_header['email']] : '';
							$value_phonenumber = isset($data[$row][$arr_header['phone']]) ? $data[$row][$arr_header['phone']] : '';
							$value_workplace = isset($data[$row][$arr_header['workplace']]) ? $data[$row][$arr_header['workplace']] : '';
							$value_status_work = isset($data[$row][$arr_header['status_work']]) ? $data[$row][$arr_header['status_work']] : '';
							$value_job_position = isset($data[$row][$arr_header['job_position']]) ? $data[$row][$arr_header['job_position']] : '';
							$value_team_manage = isset($data[$row][$arr_header['team_manage']]) ? $data[$row][$arr_header['team_manage']] : '';
							$value_role = isset($data[$row][$arr_header['role_id']]) ? $data[$row][$arr_header['role_id']] : '';
							$value_literacy = isset($data[$row][$arr_header['literacy']]) ? $data[$row][$arr_header['literacy']] : '';
							$value_hourly_rate = isset($data[$row][$arr_header['hourly_rate']]) ? $data[$row][$arr_header['hourly_rate']] : '';
							$value_department = isset($data[$row][$arr_header['department']]) ? $data[$row][$arr_header['department']] : '';
							$value_password = isset($data[$row][$arr_header['password']]) ? $data[$row][$arr_header['password']] : '';
							$value_home_town = isset($data[$row][$arr_header['home_town']]) ? $data[$row][$arr_header['home_town']] : '';
							$value_marital_status = isset($data[$row][$arr_header['marital_status']]) ? $data[$row][$arr_header['marital_status']] : '';
							$value_current_address = isset($data[$row][$arr_header['address']]) ? $data[$row][$arr_header['address']] : '';
							$value_nation = isset($data[$row][$arr_header['nation']]) ? $data[$row][$arr_header['nation']] : '';
							$value_birthplace = isset($data[$row][$arr_header['birthplace']]) ? $data[$row][$arr_header['birthplace']] : '';
							$value_religion = isset($data[$row][$arr_header['religion']]) ? $data[$row][$arr_header['religion']] : '';
							$value_identification = isset($data[$row][$arr_header['identification']]) ? $data[$row][$arr_header['identification']] : '';
							$value_days_for_identity = isset($data[$row][$arr_header['days_for_identity']]) ? $data[$row][$arr_header['days_for_identity']] : '';
							$value_place_of_issue = isset($data[$row][$arr_header['place_of_issue']]) ? $data[$row][$arr_header['place_of_issue']] : '';
							$value_resident = isset($data[$row][$arr_header['resident']]) ? $data[$row][$arr_header['resident']] : '';
							$value_account_number = isset($data[$row][$arr_header['account_number']]) ? $data[$row][$arr_header['account_number']] : '';
							$value_name_account = isset($data[$row][$arr_header['name_account']]) ? $data[$row][$arr_header['name_account']] : '';
							$value_issue_bank = isset($data[$row][$arr_header['issue_bank']]) ? $data[$row][$arr_header['issue_bank']] : '';
							$value_Personal_tax_code = isset($data[$row][$arr_header['Personal_tax_code']]) ? $data[$row][$arr_header['Personal_tax_code']] : '';

							/*check null*/
							if (is_null($value_firstname) == true || $value_firstname == '') {
								$string_error .= app_lang('hr_firstname') . ' ' . app_lang('not_yet_entered') . '; ';
								$flag = 1;
							}

							/*check null*/
							if (is_null($value_lastname) == true || $value_lastname == '') {
								$string_error .= app_lang('hr_lastname') . ' ' . app_lang('not_yet_entered') . '; ';
								$flag = 1;
							}

							if (is_null($value_status_work) == true || $value_status_work == '') {
								$string_error .= app_lang('hr_status_work') . ' ' . app_lang('not_yet_entered') . '; ';
								$flag = 1;
							}

							if (is_null($value_job_position) == true || $value_job_position == '') {
								$string_error .= app_lang('hr_hr_job_position') . ' ' . app_lang('not_yet_entered') . '; ';
								$flag = 1;
							}
							if (is_null($value_sex) != true && $value_sex != '') {

								if ($value_sex != 'male' && $value_sex != 'female') {
									$string_error .= app_lang('hr_sex') . ' ' . app_lang('does_not_exist') . '; ';
									$flag2 = 1;
								}
							}

							if (is_null($value_email) == true || $value_email == '') {
								$string_error .= app_lang('email') . ' ' . app_lang('not_yet_entered') . '; ';
								$flag = 1;
							} else {
								if (preg_match($pattern, $value_email, $match) != 1) {
									$string_error .= app_lang('email') . ' ' . app_lang('invalid') . '; ';
									$flag = 1;
								} else {
									$flag_mail = 1;
								}
							}

							//check mail exist
							if ($flag_mail == 1) {

								if ($value_staffid == '' || is_null($value_staffid) == true) {
									if ($this->Users_model->is_email_exists($value_email)) {
										$string_error .= app_lang('email') . ' ' . app_lang('exist') . '; ';
										$flag2 = 1;
									}
								}

							}

							//check start_time
							if (is_null($value_birthday) != true && $value_birthday != '') {

								if (is_null($value_birthday) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_birthday, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= app_lang('hr_hr_birthday') . ' ' . app_lang('invalid') . '; ';
									}
								}
							}

							//check start_time
							if (is_null($value_days_for_identity) != true && $value_days_for_identity != '') {

								if (is_null($value_days_for_identity) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_days_for_identity, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= app_lang('days_for_identity') . ' ' . app_lang('invalid') . '; ';
									}
								}
							}

							//check position is int
							if (is_null($value_job_position) != true && strlen($value_job_position) > 0) {

								if (!isset($job_position_data[$value_job_position])) {
									$string_error .= app_lang('hr_hr_job_position') . ' ' . app_lang('does_not_exist') . '; ';
									$flag2 = 1;
								} else {
									$flag_value_job_position = $job_position_data[$value_job_position]['position_id'];
								}

							}

							//value_team_manage
							if (is_null($value_team_manage) != true && strlen($value_team_manage) > 0) {

								if (!isset($staff_data[$value_team_manage])) {
									$string_error .= app_lang('hr_team_manage') . ' ' . app_lang('does_not_exist') . '; ';
									$flag2 = 1;
								} else {

									$flag_value_team_manage = $staff_data[$value_team_manage]['staffid'];
								}
							}

							//check workplace is int
							if (is_null($value_workplace) != true && strlen($value_workplace) > 0) {

								$workplaces_flag = false;
								foreach ($list_workplaces as $list_workplace) {
									if ($list_workplace['name'] == $value_workplace) {
										$workplaces_flag = true;

										$flag_value_workplace = $list_workplace['id'];
									}
								}

								if ($workplaces_flag == false) {
									$string_error .= app_lang('hr_hr_workplace') . ' ' . app_lang('does_not_exist') . '; ';
									$flag2 = 1;

								} else {
								}

							}

							//check role
							if (is_null($value_role) != true && strlen($value_role) > 0) {

								$roles_flag = false;
								foreach ($list_roles as $list_role) {
									if ($list_role['title'] == $value_role) {
										$roles_flag = true;

										$flag_value_role = $list_role['id'];
									}
								}

								if ($roles_flag == false) {
									$string_error .= app_lang('staff_add_edit_role') . ' ' . app_lang('does_not_exist') . '; ';
									$flag2 = 1;

								}

							}

							//check department
							if (is_null($value_department) != true && strlen($value_department) > 0) {
								$arr_department_value = explode(';', $value_department);

								$deparments_flag = true;
								$str_deparments_not_exist = '';
								$temp_str_deparments_not_exist = explode(';', $value_department);

								foreach ($list_departments as $list_department) {

									if (in_array($list_department['title'], $arr_department_value)) {

										$flag_value_department[] = $list_department['id'];

										foreach ($temp_str_deparments_not_exist as $key => $str_deparments_not_exist) {
											if ($str_deparments_not_exist == $list_department['title']) {

												unset($temp_str_deparments_not_exist[$key]);

											}
										}
									}

								}

								if (count($temp_str_deparments_not_exist) > 0) {
									$string_error .= app_lang('staff_add_edit_departments') . ': ' . implode(';', $temp_str_deparments_not_exist) . ' ' . app_lang('does_not_exist');
									$flag2 = 1;

								}

							}

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_staffid,
									$value_staff_identifi,
									$value_firstname,
									$value_lastname,
									$value_sex,
									$value_birthday,
									$value_email,
									$value_phonenumber,
									$value_workplace,
									$value_status_work,
									$value_job_position,
									$value_team_manage,
									$value_role,
									$value_literacy,
									$value_hourly_rate,
									$value_department,
									$value_password,
									$value_home_town,
									$value_marital_status,
									$value_current_address,
									$value_nation,
									$value_birthplace,
									$value_religion,
									$value_identification,
									$value_days_for_identity,
									$value_place_of_issue,
									$value_resident,
									$value_account_number,
									$value_name_account,
									$value_issue_bank,
									$value_Personal_tax_code,
									$string_error,
								]);

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd['id'] = $value_staffid;
								$rd['staff_identifi'] = $staff_prefix_str . str_pad($staff_next_number, 5, '0', STR_PAD_LEFT);
								$rd['first_name'] = $value_firstname;
								$rd['last_name'] = $value_lastname;
								$rd['gender'] = $value_sex;
								$rd['dob'] = $value_birthday;
								$rd['email'] = $value_email;
								$rd['phone'] = $value_phonenumber;
								$rd['workplace'] = $flag_value_workplace;
								$rd['status_work'] = $value_status_work;
								$rd['job_position'] = $flag_value_job_position;
								$rd['team_manage'] = $flag_value_team_manage;
								$rd['role'] = $flag_value_role;
								$rd['literacy'] = $value_literacy;
								$rd['hourly_rate'] = $value_hourly_rate;
								$rd['departments'] = $flag_value_department;

								if (strlen($value_password) > 0) {
									$rd['password'] = $value_password;
								} else {
									$rd['password'] = '123456a@';
								}

								$rd['home_town'] = $value_home_town;
								$rd['marital_status'] = $value_marital_status;
								$rd['address'] = $value_current_address;
								$rd['nation'] = $value_nation;
								$rd['birthplace'] = $value_birthplace;
								$rd['religion'] = $value_religion;
								$rd['identification'] = $value_identification;
								$rd['days_for_identity'] = $value_days_for_identity;
								$rd['place_of_issue'] = $value_place_of_issue;
								$rd['resident'] = $value_resident;
								$rd['account_number'] = $value_account_number;
								$rd['name_account'] = $value_name_account;
								$rd['issue_bank'] = $value_issue_bank;
								$rd['Personal_tax_code'] = $value_Personal_tax_code;

								$rows[] = $rd;
								array_push($arr_insert, $rd);

								$staff_next_number++;
							}

							if ($flag == 0 && $flag2 == 0) {

								if ($rd['id'] == '' || $rd['id'] == 0) {

									//insert staff
									$response = $this->hr_profile_model->import_add_staff($rd);
									if ($response) {
										$total_row_success++;
									}
								} else {
									//update staff
									unset($data['staff_identifi']);
									unset($data['password']);

									$response = $this->hr_profile_model->import_update_staff($rd, $rd['id']);
									if ($response) {
										$total_row_success++;
									}
								}

							}

						}

						$total_rows = $total_rows;
						$total_row_success = $total_row_success;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_employee_error_' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));
							$filename = HR_PROFILE_ERROR.$filename;

						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => $filename,
		]);
	}

	/**
	 * importxlsx2
	 * @return  json
	 */
	public function importxlsx2() {
		if (!is_admin() && get_option('allow_non_admin_members_to_import_leads') != '1') {
			app_redirect("forbidden");
		}
		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';
					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}
					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}
					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$rows = [];

						$objReader = new PHPExcel_Reader_Excel2007();
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($newFilePath);
						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
						$sheet = $objPHPExcel->getActiveSheet();

						$dataError = new PHPExcel();
						$dataError->setActiveSheetIndex(0);
						$dataError->getActiveSheet()->setTitle('Data is not allowed');
						$dataError->getActiveSheet()->getColumnDimension('A')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('B')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('C')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('D')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('E')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('F')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('G')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('H')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('I')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('J')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('K')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('L')->setWidth(30);
						$dataError->getActiveSheet()->getColumnDimension('M')->setWidth(30);
						$dataError->getActiveSheet()->getColumnDimension('N')->setWidth(30);
						$dataError->getActiveSheet()->getColumnDimension('O')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('P')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('Q')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('R')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('S')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('T')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('U')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('V')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('W')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('X')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('Y')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('Z')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('AA')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('AB')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('AC')->setWidth(20);
						$dataError->getActiveSheet()->getStyle('A1:AA1')->getFont()->setBold(true);

						$dataError->getActiveSheet()->setCellValue('A1', app_lang('hr_staff_code'));
						$dataError->getActiveSheet()->setCellValue('B1', app_lang('hr_firstname'));
						$dataError->getActiveSheet()->setCellValue('C1', app_lang('hr_lastname'));
						$dataError->getActiveSheet()->setCellValue('D1', app_lang('email'));
						$dataError->getActiveSheet()->setCellValue('E1', app_lang('hr_gender'));
						$dataError->getActiveSheet()->setCellValue('F1', app_lang('birthday'));
						$dataError->getActiveSheet()->setCellValue('G1', app_lang('phonenumber'));
						$dataError->getActiveSheet()->setCellValue('H1', app_lang('nation'));
						$dataError->getActiveSheet()->setCellValue('I1', app_lang('religion'));
						$dataError->getActiveSheet()->setCellValue('J1', app_lang('birthplace'));
						$dataError->getActiveSheet()->setCellValue('K1', app_lang('home_town'));
						$dataError->getActiveSheet()->setCellValue('L1', app_lang('resident'));
						$dataError->getActiveSheet()->setCellValue('M1', app_lang('hr_current_address'));
						$dataError->getActiveSheet()->setCellValue('N1', app_lang('marital_status'));
						$dataError->getActiveSheet()->setCellValue('O1', app_lang('identification'));
						$dataError->getActiveSheet()->setCellValue('P1', app_lang('days_for_identity'));
						$dataError->getActiveSheet()->setCellValue('Q1', app_lang('place_of_issue'));
						$dataError->getActiveSheet()->setCellValue('R1', app_lang('literacy'));
						$dataError->getActiveSheet()->setCellValue('S1', app_lang('job_position'));
						$dataError->getActiveSheet()->setCellValue('T1', app_lang('hr_job_rank'));
						$dataError->getActiveSheet()->setCellValue('U1', app_lang('workplace'));
						$dataError->getActiveSheet()->setCellValue('V1', app_lang('departments'));
						$dataError->getActiveSheet()->setCellValue('W1', app_lang('account_number'));
						$dataError->getActiveSheet()->setCellValue('X1', app_lang('hr_name_account'));
						$dataError->getActiveSheet()->setCellValue('Y1', app_lang('hr_issue_bank'));
						$dataError->getActiveSheet()->setCellValue('Z1', app_lang('hr_Personal_tax_code'));
						$dataError->getActiveSheet()->setCellValue('AA1', app_lang('hr_status_work'));
						$dataError->getActiveSheet()->setCellValue('AB1', app_lang('password'));
						$dataError->getActiveSheet()->setCellValue('AC1', app_lang('error'));

						$styleArray = array(
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => 'ff0000'),

							));
						$numRow = 2;
						$total_rows = 0;

						//get data for compare
						foreach ($rowIterator as $row) {

							$rowIndex = $row->getRowIndex();

							if ($rowIndex > 1) {
								$rd = array();
								$flag = 0;
								$flag2 = 0;
								$flag_mail = 0;
								$string_error = '';
								$value_cell_hrcode = $sheet->getCell('A' . $rowIndex)->getValue();
								$value_cell_first_name = $sheet->getCell('B' . $rowIndex)->getValue();
								$value_cell_last_name = $sheet->getCell('C' . $rowIndex)->getValue();
								$value_cell_email = $sheet->getCell('D' . $rowIndex)->getValue();
								$value_cell_sex = $sheet->getCell('E' . $rowIndex)->getValue();
								$value_cell_birthday = $sheet->getCell('F' . $rowIndex)->getValue();
								$value_cell_maries_status = $sheet->getCell('N' . $rowIndex)->getValue();

								$value_cell_status = $sheet->getCell('AA' . $rowIndex)->getValue();
								$value_cell_day_identity = $sheet->getCell('P' . $rowIndex)->getValue();
								$value_cell_position = $sheet->getCell('S' . $rowIndex)->getValue();
								$value_cell_workplace = $sheet->getCell('U' . $rowIndex)->getValue();
								$value_cell_password = $sheet->getCell('AB' . $rowIndex)->getValue();
								$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';
								$reg_day = '#^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$#';
								$position_array = $this->hr_profile_model->get_job_position_arrayid();
								$workplace_array = $this->hr_profile_model->get_workplace_array_id();
								$sex_array = ['0', '1'];
								$status_array = ['0', '1', '2'];

								if (is_null($value_cell_hrcode) == true) {
									$string_error .= app_lang('hr_hr_code') . app_lang('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_first_name) == true) {
									$string_error .= app_lang('hr_firstname') . app_lang('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_last_name) == true) {
									$string_error .= app_lang('hr_lastname') . app_lang('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_email) == true) {
									$string_error .= app_lang('email') . app_lang('not_yet_entered');
									$flag = 1;
								} else {
									if (preg_match($pattern, $value_cell_email, $match) != 1) {
										$string_error .= app_lang('email') . app_lang('invalid');
										$flag = 1;
									} else {
										$flag_mail = 1;
									}
								}

								//check hr_code exist
								if (is_null($value_cell_hrcode) != true) {
									$this->db->where('staff_identifi', $value_cell_hrcode);
									$hrcode = $this->db->count_all_results('tblstaff');
									if ($hrcode > 0) {
										$string_error .= app_lang('hr_hr_code') . app_lang('exist');
										$flag2 = 1;
									}

								}
								//check mail exist
								if ($flag_mail == 1) {
									$this->db->where('email', $value_cell_email);
									$total_rows_email = $this->db->count_all_results(db_prefix() . 'staff');
									if ($total_rows_email > 0) {
										$string_error .= app_lang('email') . app_lang('exist');
										$flag2 = 1;
									}
								}

								//check sex is int
								if (is_null($value_cell_sex) != true) {
									if (is_string($value_cell_sex)) {
										$string_error .= app_lang('hr_sex') . app_lang('invalid');
										$flag2 = 1;

									} elseif (in_array($value_cell_sex, $sex_array) != true) {
										$string_error .= app_lang('hr_sex') . app_lang('does_not_exist');
										$flag2 = 1;
									}
								}

								//check position is int
								if (is_null($value_cell_position) != true) {
									if (is_string($value_cell_position)) {
										$string_error .= app_lang('job_position') . app_lang('invalid');
										$flag2 = 1;

									} elseif (in_array($value_cell_position, $position_array) != true) {
										$string_error .= app_lang('job_position') . app_lang('does_not_exist');
										$flag2 = 1;
									}

								}
								//check status is int
								if (is_null($value_cell_status) != true) {
									if (is_string($value_cell_status)) {
										$string_error .= app_lang('hr_status_work') . app_lang('invalid');
										$flag2 = 1;

									} elseif (in_array($value_cell_status, $status_array) != true) {
										$string_error .= app_lang('hr_status_work') . app_lang('does_not_exist');
										$flag2 = 1;
									}
								}
								//check workplace is int
								if (is_null($value_cell_workplace) != true) {
									if (!is_numeric($value_cell_workplace)) {
										$string_error .= app_lang('workplace') . app_lang('invalid');
										$flag2 = 1;
									} elseif (in_array($value_cell_workplace, $workplace_array) != true) {
										$string_error .= app_lang('workplace') . app_lang('does_not_exist');
										$flag2 = 1;
									}
								}

								//check birday input
								if (is_null($value_cell_birthday) != true) {
									if (preg_match($reg_day, $value_cell_birthday, $match) != 1) {
										$string_error .= app_lang('birthday') . app_lang('invalid');
										$flag = 1;
									}
								}
								//check day identity
								if (is_null($value_cell_day_identity) != true) {
									if (preg_match($reg_day, $value_cell_day_identity, $match) != 1) {
										$string_error .= app_lang('days_for_identity') . app_lang('invalid');
										$flag = 1;
									}

								}

								if (($flag == 1) || ($flag2 == 1)) {
									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $sheet->getCell('D' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('E' . $numRow, $sheet->getCell('E' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('F' . $numRow, $sheet->getCell('F' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('G' . $numRow, $sheet->getCell('G' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('H' . $numRow, $sheet->getCell('H' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('I' . $numRow, $sheet->getCell('I' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('J' . $numRow, $sheet->getCell('J' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('K' . $numRow, $sheet->getCell('K' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('L' . $numRow, $sheet->getCell('L' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('M' . $numRow, $sheet->getCell('M' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('N' . $numRow, $sheet->getCell('N' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('O' . $numRow, $sheet->getCell('O' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('P' . $numRow, $sheet->getCell('P' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('Q' . $numRow, $sheet->getCell('Q' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('R' . $numRow, $sheet->getCell('R' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('S' . $numRow, $sheet->getCell('S' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('T' . $numRow, $sheet->getCell('T' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('U' . $numRow, $sheet->getCell('U' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('V' . $numRow, $sheet->getCell('V' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('W' . $numRow, $sheet->getCell('W' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('X' . $numRow, $sheet->getCell('X' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('Y' . $numRow, $sheet->getCell('Y' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('Z' . $numRow, $sheet->getCell('Z' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('AA' . $numRow, $sheet->getCell('AA' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('AB' . $numRow, $sheet->getCell('AB' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('AC' . $numRow, $string_error)->getStyle('AC' . $numRow)->applyFromArray($styleArray);

									$numRow++;
								}

								if (($flag == 0) && ($flag2 == 0)) {

									if (is_null($value_cell_sex)) {
										$rd['sex'] = '';
									} else {
										if ($value_cell_sex == 0) {
											$rd['sex'] = 'male';
										} else {
											$rd['sex'] = 'female';
										}
									}

									if (is_null($value_cell_status)) {
										$rd['status_work'] = '';
									} else {
										if ($value_cell_status == 0) {
											$rd['status_work'] = 'Working';
										} elseif ($value_cell_status == 1) {
											$rd['status_work'] = 'Maternity leave';
										} else {
											$rd['status_work'] = 'Inactivity';

										}
									}

									if (is_null($value_cell_maries_status)) {
										$rd['marital_status'] = '';
									} else {
										if ($value_cell_sex == 0) {
											$rd['marital_status'] = 'single';
										} else {
											$rd['marital_status'] = 'married';
										}
									}

									if (is_null($value_cell_birthday) == true) {
										$rd['birthday'] = '';
									} else {
										$rd['birthday'] = $value_cell_birthday;
									}

									if (is_null($value_cell_day_identity) == true) {
										$rd['days_for_identity'] = '';
									} else {
										$rd['days_for_identity'] = $value_cell_birthday;
									}

									if (is_null($value_cell_email) == true) {
										$rd['email'] = '';
									} else {
										$rd['email'] = $value_cell_email;
									}

									if (is_null($value_cell_position) == true) {
										$rd['job_position'] = '';
									} else {
										$rd['job_position'] = $value_cell_position;
									}

									if (is_null($value_cell_workplace) == true) {
										$rd['workplace'] = '';
									} else {
										$rd['workplace'] = $value_cell_workplace;
									}

									if (is_null($value_cell_password) == true) {
										$rd['password'] = '123456a@';
									} else {
										$rd['password'] = $value_cell_password;
									}
									$rd['staff_identifi'] = $sheet->getCell('A' . $rowIndex)->getValue();
									$rd['firstname'] = $sheet->getCell('B' . $rowIndex)->getValue();
									$rd['lastname'] = $sheet->getCell('C' . $rowIndex)->getValue();
									$rd['email'] = $sheet->getCell('D' . $rowIndex)->getValue();
									$rd['sex'] = $sheet->getCell('E' . $rowIndex)->getValue();
									$rd['birthday'] = $sheet->getCell('F' . $rowIndex)->getValue();
									$rd['phonenumber'] = $sheet->getCell('G' . $rowIndex)->getValue();
									$rd['nation'] = $sheet->getCell('H' . $rowIndex)->getValue();
									$rd['religion'] = $sheet->getCell('I' . $rowIndex)->getValue();
									$rd['birthplace'] = $sheet->getCell('J' . $rowIndex)->getValue();
									$rd['home_town'] = $sheet->getCell('K' . $rowIndex)->getValue();
									$rd['resident'] = $sheet->getCell('L' . $rowIndex)->getValue();
									$rd['current_address'] = $sheet->getCell('M' . $rowIndex)->getValue();
									$rd['marital_status'] = $sheet->getCell('N' . $rowIndex)->getValue();
									$rd['identification'] = $sheet->getCell('O' . $rowIndex)->getValue();
									$rd['days_for_identity'] = $sheet->getCell('P' . $rowIndex)->getValue();
									$rd['place_of_issue'] = $sheet->getCell('Q' . $rowIndex)->getValue();
									$rd['literacy'] = $sheet->getCell('R' . $rowIndex)->getValue();
									$rd['job_position'] = $sheet->getCell('S' . $rowIndex)->getValue();
									$rd['workplace'] = $sheet->getCell('U' . $rowIndex)->getValue();
									$rd['departments'] = explode(",", $sheet->getCell('V' . $rowIndex)->getValue());
									$rd['account_number'] = $sheet->getCell('W' . $rowIndex)->getValue();
									$rd['name_account'] = $sheet->getCell('X' . $rowIndex)->getValue();
									$rd['issue_bank'] = $sheet->getCell('Y' . $rowIndex)->getValue();
									$rd['Personal_tax_code'] = $sheet->getCell('Z' . $rowIndex)->getValue();
									$rd['status_work'] = $sheet->getCell('AA' . $rowIndex)->getValue();
									$rd['password'] = $sheet->getCell('AB' . $rowIndex)->getValue();
								}

								if (get_staff_user_id1() != '' && $flag == 0 && $flag2 == 0) {
									$rows[] = $rd;
									$this->hr_profile_model->add_staff($rd);
								}
								$total_rows++;
							}
						}

						$total_rows = $total_rows;
						$data['total_rows_post'] = count($rows);
						$total_row_success = count($rows);
						$total_row_false = $total_rows - (int) count($rows);
						$dataerror = $dataError;
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);
							$filename = 'file_error_hr_profile' . get_staff_user_id1() . '.xlsx';
							$objWriter->save($filename);

						}
						$import_result = true;
						@delete_dir($tmpDir);

					}
				} else {
					set_alert('warning', app_lang('import_upload_failed'));
				}
			}

		}
		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id1(),
		]);

	}

	/**
	 * delete staff
	 */
	public function delete_staff() {
		$id = $this->request->getPost('id');

		$options = array(
			"id" => $id,
		);
		$staff = $this->Users_model->get_details($options)->getRow();

		if (!is_admin() && $staff && $staff->is_admin && $id != $this->login_user->id) {
			die('Busted, you can\'t delete administrators');
		}

		if (hr_has_permission('hr_profile_can_delete_hr_records')) {
			if ($id != $this->login_user->id && $this->Users_model->delete($id)) {
				$this->session->setFlashdata("success_message", app_lang("deleted"));
			}else{
				$this->session->setFlashdata("error_message", app_lang("record_cannot_be_deleted"));
			}
		}
		app_redirect('hr_profile/staff_infor');
	}

	/**
	 * member
	 * @param  integer $id
	 * @param  integer $group
	 * @return view
	 */
	public function member($id = '', $group = '') {

		$data['staffid'] = $id;
		$data['group'] = $group;

		$data['tab'][] = 'profile';
		$data['tab'][] = 'contract';
		$data['tab'][] = 'dependent_person';
		$data['tab'][] = 'training';
		$data['tab'][] = 'staff_project';
		$data['tab'][] = 'attach';
		$data['tab'] = hooks()->apply_filters('hr_profile_tab_name', $data['tab']);

		if ($data['group'] == '') {
			$data['group'] = 'profile';
		}
		$data['hr_profile_member_add'] = false;
		if ($id == '') {
			if (!is_admin() && !hr_has_permission('hr_profile_can_create_hr_records') && !hr_has_permission('hr_profile_can_edit_hr_records')) {
				app_redirect("forbidden");
			}
			$data['hr_profile_member_add'] = true;
			$title = app_lang('add_new', app_lang('staff_member_lowercase'));
		} else {
			//View own
			$staff_ids = $this->hr_profile_model->get_staff_by_manager();

			if (!in_array($id, $staff_ids) && get_staff_user_id1() != $id && !is_admin() && !hr_has_permission('hr_profile_can_edit_hr_records') && !hr_has_permission('hr_profile_can_view_global_hr_records') && !hr_has_permission('hr_profile_can_create_hr_records')) {
				app_redirect("forbidden");
			}

			$member = $this->hr_profile_model->get_staff($id);
			if (!$member) {
				blank_page('Staff Member Not Found', 'danger');
			}
			$data['member'] = $member;
			$title = $member->firstname . ' ' . $member->lastname;

			if ($data['group'] == 'profile') {
				$data['staff_departments'] = $this->departments_model->get_staff_departments($id);

				$options = array(
					"status" => "active",
					"user_type" => "staff",
				);
				$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

				$recordsreceived = $this->hr_profile_model->get_records_received($id);
				$data['records_received'] = json_decode($recordsreceived->records_received, true);
				$data['checkbox'] = [];
				if (isset($data['records_received'])) {
					foreach ($data['records_received'] as $value) {
						$data['checkbox'][$value['datakey']] = $value['value'];
					}
				}
				$data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
				$data['staff_avatar'] = $this->hr_profile_model->get_hr_profile_profile_file($id);
				$data['staff_cover_image'] = $this->hr_profile_model->get_hr_profile_profile_file($id);

				$data['logged_time'] = $this->staff_model->get_logged_time_data($id);
				$data['staff_p'] = $this->staff_model->get($id);
				$data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
				// notifications
				$total_notifications = total_rows(db_prefix() . 'notifications', [
					'touserid' => get_staff_user_id1(),
				]);
				$data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());

			}
			if ($data['group'] == 'dependent_person') {
				$data['dependent_person'] = $this->hr_profile_model->get_dependent_person_bytstaff($id);
			}
			if ($data['group'] == 'attach') {
				$data['hr_profile_staff'] = $this->hr_profile_model->get_hr_profile_attachments($id);
			}
			if ($data['group'] == 'staff_project') {
				$data['logged_time'] = $this->staff_model->get_logged_time_data($id);
				$data['staff_p'] = $this->staff_model->get($id);
				$data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
				// notifications
				$total_notifications = total_rows(db_prefix() . 'notifications', [
					'touserid' => get_staff_user_id1(),
				]);
				$data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());

			}

			if ($data['group'] == 'training') {

				$training_data = [];
				//Onboarding training
				$training_allocation_staff = $this->hr_profile_model->get_training_allocation_staff($id);

				if ($training_allocation_staff != null) {

					$training_data['list_training_allocation'] = get_object_vars($training_allocation_staff);
				}

				if (isset($training_allocation_staff) && $training_allocation_staff != null) {
					$training_data['training_allocation_min_point'] = 0;

					$job_position_training = $this->hr_profile_model->get_job_position_training_de($training_allocation_staff->jp_interview_training_id);

					if ($job_position_training) {
						$training_data['training_allocation_min_point'] = $job_position_training->mint_point;
					}

					if ($training_allocation_staff) {
						$training_process_id = $training_allocation_staff->training_process_id;

						$training_data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($training_process_id);

						//Get the latest employee's training result.
						$training_results = $this->get_mark_staff($id, $training_process_id);

						$training_data['training_program_point'] = $training_results['training_program_point'];
						$training_data['staff_training_result'] = $training_results['staff_training_result'];

						//have not done the test data
						$staff_training_result = [];
						foreach ($training_data['list_training'] as $key => $value) {
							$staff_training_result[$value['training_id']] = [
								'training_name' => $value['subject'],
								'total_point' => 0,
								'training_id' => $value['training_id'],
								'total_question' => 0,
								'total_question_point' => 0,
							];
						}

						//did the test
						if (count($training_results['staff_training_result']) > 0) {

							foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
								if (isset($staff_training_result[$result_value['training_id']])) {
									unset($staff_training_result[$result_value['training_id']]);
								}
							}

							$training_data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

						} else {
							$training_data['staff_training_result'] = $staff_training_result;
						}

						if ((float) $training_results['training_program_point'] >= (float) $training_data['training_allocation_min_point']) {
							$training_data['complete'] = 0;
						} else {
							$training_data['complete'] = 1;
						}

					}
				}

				if (count($training_data) > 0) {
					$data['training_data'][] = $training_data;
				}

				//Additional training
				$additional_trainings = $this->hr_profile_model->get_additional_training($id);

				foreach ($additional_trainings as $key => $value) {
					$training_temp = [];

					$training_temp['training_allocation_min_point'] = $value['mint_point'];
					$training_temp['list_training_allocation'] = $value;
					$training_temp['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($value['position_training_id']);

					//Get the latest employee's training result.
					$training_results = $this->get_mark_staff($id, $value['position_training_id']);

					$training_temp['training_program_point'] = $training_results['training_program_point'];
					$training_temp['staff_training_result'] = $training_results['staff_training_result'];

					//have not done the test data
					$staff_training_result = [];
					foreach ($training_temp['list_training'] as $key => $value) {
						$staff_training_result[$value['training_id']] = [
							'training_name' => $value['subject'],
							'total_point' => 0,
							'training_id' => $value['training_id'],
							'total_question' => 0,
							'total_question_point' => 0,
						];
					}

					//did the test
					if (count($training_results['staff_training_result']) > 0) {

						foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
							if (isset($staff_training_result[$result_value['training_id']])) {
								unset($staff_training_result[$result_value['training_id']]);
							}
						}

						$training_temp['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

					} else {
						$training_temp['staff_training_result'] = $staff_training_result;
					}

					if ((float) $training_results['training_program_point'] >= (float) $training_temp['training_allocation_min_point']) {
						$training_temp['complete'] = 0;
					} else {
						$training_temp['complete'] = 1;
					}

					if (count($training_temp) > 0) {
						$data['training_data'][] = $training_temp;
					}

				}

			}
		}
		$this->load->model('currencies_model');
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$data['roles'] = $this->roles_model->get();
		$data['user_notes'] = $this->misc_model->get_notes($id, 'staff');
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		$data['title'] = $title;

		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['tabs']['view'] = 'hr_record/includes/' . $data['group'];

		$data['tabs']['view'] = hooks()->apply_filters('hr_profile_tab_content', $data['tabs']['view']);

		return $this->template->view('Hr_profile\Views\hr_record/member', $data);
	}

	/**
	 * table education position
	 */
	public function table_education_position() {
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'hr_record/table_education_by_position'));
	}

	/**
	 * table education
	 */
	public function table_education() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'hr_record/table_education'), $dataPost);
	}

	/**
	 * save update education
	 * @return json
	 */
	public function save_update_education() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$data['training_time_from'] = to_sql_date1($data['training_time_from']);
			$data['training_time_to'] = to_sql_date1($data['training_time_to']);
			$data['admin_id'] = get_staff_user_id1();
			$data['programe_id'] = '';
			$data['date_create'] = to_sql_date1(get_my_local_time("Y-m-d"), true);
			if ($data['id'] == '') {
				$success = $this->hr_profile_model->add_education($data);
				$message = app_lang('added_successfully');
				$message_f = app_lang('hr_added_failed');
				if ($success) {
					echo json_encode([
						'success' => true,
						'message' => $message,
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => $message_f,
					]);
				}
			} else {
				$success = $this->hr_profile_model->update_education($data);
				$message = app_lang('updated_successfully');
				$message_f = app_lang('hr_update_failed');
				if ($success) {
					echo json_encode([
						'success' => true,
						'message' => $message,
					]);
				} else {
					echo json_encode([
						'success' => false,
						'message' => $message_f,
					]);
				}
			}
		}

		die;
	}

/**
 * delete education
 * @return json
 */
public function delete_education() {
	if ($this->request->getPost()) {
		$data = $this->request->getPost();
		$success = $this->hr_profile_model->delete_education($data['id']);
		if ($success == true) {
			$message = app_lang('hr_deleted');
			echo json_encode([
				'success' => true,
				'message' => $message,
			]);
		} else {
			$message = app_lang('problem_deleting');
			echo json_encode([
				'success' => true,
				'message' => $message,
			]);
		}
	}
}
/**
 * table reception
 */
public function table_reception() {
	if ($this->request->getPost()) {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'includes/reception_table'), $dataPost);
	}
}
/**
 * general bonus
 * @param  integer $id
 * @return json
 */
public function general_bonus($id) {
	$select = [
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',
	];
	$where = [' where staff_id = ' . $id . ' and type = 1 and status = 2'];
	$aColumns = $select;
	$sIndexColumn = 'id';
	$sTable = db_prefix() . 'bonus_discipline_detail';
	$join = [' LEFT JOIN ' . db_prefix() . 'bonus_discipline ON ' . db_prefix() . 'bonus_discipline.id = ' . db_prefix() . 'bonus_discipline_detail.id_bonus_discipline'];

	$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.from_time',
		'staff_id',
		'apply_for',
		db_prefix() . 'bonus_discipline_detail.lever_bonus',
		db_prefix() . 'bonus_discipline.name',
		db_prefix() . 'bonus_discipline.type',
		db_prefix() . 'bonus_discipline.id_criteria',
		db_prefix() . 'bonus_discipline_detail.formality',
		db_prefix() . 'bonus_discipline_detail.formality_value',
		db_prefix() . 'bonus_discipline_detail.description',
	]);

	$output = $result['output'];
	$rResult = $result['rResult'];
	foreach ($rResult as $aRow) {
		$row = [];
		$row[] = $aRow['name'];
		$criterial = '';
		$list_criteria = json_decode($aRow['id_criteria']);
		if ($list_criteria) {
			foreach ($list_criteria as $key => $criteria) {
				$criterial = '<span class="badge inline-block project-status" class="bg-white text-dark"> ' . $this->hr_profile_model->get_criteria($criteria)->kpi_name . ' </span>  ';
			}
		}

		$row[] = $criterial;
		$row[] = _l($aRow['from_time']);
		$formality = '';
		$value_formality = '';
		if (isset($aRow['formality'])) {
			if ($aRow['formality'] == 'bonus_money') {
				$formality = app_lang('bonus_money');
				$value_formality = app_format_money($aRow['formality_value'], '');
			}
			if ($aRow['formality'] == 'indemnify') {
				$formality = app_lang('indemnify');
				$t = explode(',', $aRow['formality_value']);
				$value_formality = app_lang('amount_of_damage') . ': ' . app_format_money((int) $t[0], '') . '<br>' . app_lang('indemnify') . ': ' . app_format_money((int) $t[1], '');
			}
			if ($aRow['formality'] == 'commend') {
				$formality = app_lang('commend');
			}
			if ($aRow['formality'] == 'remind') {
				$formality = app_lang('remind');
			}
		}
		$row[] = $formality;
		$row[] = $value_formality;

		$output['aaData'][] = $row;
	}
	echo json_encode($output);
	die();
}
/**
 * general discipline
 * @param  integer $id
 * @return json
 */
public function general_discipline($id) {
	$select = [
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',

		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.id',
	];
	$where = [' where staff_id = ' . $id . ' and type = 2 and status = 2'];
	$aColumns = $select;
	$sIndexColumn = 'id';
	$sTable = db_prefix() . 'bonus_discipline_detail';
	$join = [' LEFT JOIN ' . db_prefix() . 'bonus_discipline ON ' . db_prefix() . 'bonus_discipline.id = ' . db_prefix() . 'bonus_discipline_detail.id_bonus_discipline'];

	$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
		db_prefix() . 'bonus_discipline_detail.id',
		db_prefix() . 'bonus_discipline_detail.from_time',
		'staff_id',
		'apply_for',
		db_prefix() . 'bonus_discipline_detail.lever_bonus',
		db_prefix() . 'bonus_discipline.name',
		db_prefix() . 'bonus_discipline.type',
		db_prefix() . 'bonus_discipline.id_criteria',
		db_prefix() . 'bonus_discipline_detail.formality',
		db_prefix() . 'bonus_discipline_detail.formality_value',
		db_prefix() . 'bonus_discipline_detail.description',
	]);

	$output = $result['output'];
	$rResult = $result['rResult'];
	foreach ($rResult as $aRow) {
		$row = [];
		$row[] = $aRow['name'];
		$criterial = '';
		$list_criteria = json_decode($aRow['id_criteria']);
		if ($list_criteria) {
			foreach ($list_criteria as $key => $criteria) {
				$criterial = '<span class="badge inline-block project-status" class="bg-white text-dark"> ' . $this->hr_profile_model->get_criteria($criteria)->kpi_name . ' </span>  ';
			}
		}
		$row[] = $criterial;
		$row[] = _l($aRow['from_time']);
		$formality = '';
		$value_formality = '';
		if (isset($aRow['formality'])) {
			if ($aRow['formality'] == 'bonus_money') {
				$formality = app_lang('bonus_money');
				$value_formality = app_format_money($aRow['formality_value'], '') . 'đ';
			}
			if ($aRow['formality'] == 'indemnify') {
				$formality = app_lang('indemnify');
				$t = explode(',', $aRow['formality_value']);
				$value_formality = app_lang('amount_of_damage') . ': ' . app_format_money((int) $t[0], '') . 'đ<br>' . app_lang('indemnify') . ': ' . app_format_money((int) $t[1], '');
			}
			if ($aRow['formality'] == 'commend') {
				$formality = app_lang('commend');
			}
			if ($aRow['formality'] == 'remind') {
				$formality = app_lang('remind');
			}
		}
		$row[] = $formality;
		$row[] = $value_formality;

		$output['aaData'][] = $row;
	}
	echo json_encode($output);
	die();
}
/**
 * records received
 * @return json
 */
public function records_received() {
	if ($this->input->is_ajax_request()) {
		if ($this->request->getPost() != null) {
			$data = $this->request->getPost();
			$data1 = $data['dt_record'];
			$this->db->set('records_received', $data1);
			$this->db->where('staffid', $data['staffid']);
			$this->db->update(db_prefix() . 'staff');
			$affected_rows = $this->db->affected_rows();
			if ($affected_rows > 0) {
				$message = 'Add records received success';
			} else {
				$message = 'Add records received false';
			}
			echo json_encode([
				'message' => $message,
			]);
		}
	}
}

	/**
	 * upload file
	 * @return json
	 */
	public function upload_file() {
		$staffid = $this->request->getPost('staffid');
		$files = handle_hr_profile_attachments_array($staffid, 'file');
		$success = false;
		$count_id = 0;
		$message = '';

		if ($files) {
			$i = 0;
			$len = count($files);
			foreach ($files as $file) {
				$insert_id = $this->hr_profile_model->add_attachment_to_database($staffid, 'hr_staff_file', [$file], false);
				if ($insert_id > 0) {
					$count_id++;
				}
				$i++;
			}
			if ($insert_id == $i) {
				$message = 'Upload file success';
			}
		}

		$hr_profile_staff = $this->hr_profile_model->get_hr_profile_attachments($staffid);
		$data = '';
		foreach ($hr_profile_staff as $key => $attachment) {
			$href_url = site_url('modules/hr_profile/uploads/att_file/' . $attachment['rel_id'] . '/' . $attachment['file_name']) . '" download';
			if (!empty($attachment['external'])) {
				$href_url = $attachment['external_link'];
			}
			$data .= '<div class="display-block contract-attachment-wrapper">';
			$data .= '<div class="col-md-10">';
			$data .= '<div class="col-md-1 mr-5">';
			$data .= '<a name="preview-btn" onclick="preview_file_staff(this); return false;" rel_id = "' . $attachment['rel_id'] . '" id = "' . $attachment['id'] . '" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="' . _l("preview_file") . '">';
			$data .= '<i class="fa fa-eye"></i>';
			$data .= '</a>';
			$data .= '</div>';
			$data .= '<div class=col-md-9>';
			$data .= '<div class="pull-left"><i class="' . get_mime_class($attachment['filetype']) . '"></i></div>';
			$data .= '<a href="' . $href_url . '>' . $attachment['file_name'] . '</a>';
			$data .= '<p class="text-muted">' . $attachment["filetype"] . '</p>';
			$data .= '</div>';
			$data .= '</div>';
			$data .= '<div class="col-md-2 text-right">';
			if ($attachment['staffid'] == get_staff_user_id1() || is_admin() || hr_has_permission('hr_profile_can_edit_hr_records')) {
				$data .= '<a href="#" class="text-danger" onclick="delete_hr_att_file_attachment(this,' . $attachment['id'] . '); return false;"><i class="fa fa fa-times"></i></a>';
			}
			$data .= '</div>';
			$data .= '<div class="clearfix"></div><hr/>';
			$data .= '</div>';
		}

		echo json_encode([
			'message' => app_lang('hr_attach_file_successfully'),
			'data' => $data,
		]);
	}

	/**
	 * hr profile file
	 * @param  integer $id
	 * @param  string $rel_id
	 */
	public function hr_profile_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id1());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Hr_profile\Views\hr_profile/includes/_file', $data);
	}

	/**
	 * delete hr profile staff attachment
	 * @param  integer $attachment_id
	 * @return json
	 */
	public function delete_hr_profile_staff_attachment($attachment_id) {
		$file = $this->misc_model->get_file($attachment_id);
		if ($file->staffid == get_staff_user_id1() || is_admin() || hr_has_permission('hr_profile_can_edit_hr_records')) {
			$result = $this->hr_profile_model->delete_hr_profile_staff_attachment($attachment_id);

			if ($result) {
				$status = true;
				$message = app_lang('hr_deleted');
			} else {
				$message = app_lang('problem_deleting');
				$status = false;

			}
			echo json_encode([
				'success' => $status,
				'message' => $message,
			]);
		} else {
			app_redirect("forbidden");
		}
	}

/**
 * update staff permission
 */
public function update_staff_permission() {
	$data = $this->request->getPost();
	if ($data['id'] != '') {
		if (!$data['id'] == get_staff_user_id1() && !is_admin() && !hr_profile_permissions('hr_profile', '', 'edit')) {
			app_redirect("forbidden");
		}
		$response = $this->hr_profile_model->update_staff_permissions($data);
		if ($response == true) {
			set_alert('success', app_lang('updated_successfully', app_lang('staff_member')));
		} else {
			set_alert('danger', app_lang('updated_failed', app_lang('staff_member')));
		}
	}
	app_redirect(('hr_profile/member/' . $data['id'] . '/permission'));
}
/**
 * update staff profile
 */
public function update_staff_profile() {
	$data = $this->request->getPost();
	if ($data['id'] == '') {
		unset($data['id']);
		if (!hr_has_permission('hr_profile_can_create_hr_records') && !hr_has_permission('hr_profile_can_edit_hr_records') && !is_admin()) {
			app_redirect("forbidden");
		}
		$id = $this->hr_profile_model->add_staff($data);
		if ($id) {
			hr_profile_handle_staff_profile_image_upload($id);
			set_alert('success', app_lang('added_successfully', app_lang('staff_member')));
			app_redirect(('hr_profile/member/' . $id . '/profile'));
		}
	} else {
		if (!$data['id'] == get_staff_user_id1() && !is_admin() && !hr_profile_permissions('hr_profile', '', 'edit')) {
			app_redirect("forbidden");
		}
		$response = $this->hr_profile_model->update_staff_profile($data);
		if ($response == true) {
			hr_profile_handle_staff_profile_image_upload($data['id']);
		}
		if (is_array($response)) {
			if (isset($response['cant_remove_main_admin'])) {
				set_alert('warning', app_lang('staff_cant_remove_main_admin'));
			} elseif (isset($response['cant_remove_yourself_from_admin'])) {
				set_alert('warning', app_lang('staff_cant_remove_yourself_from_admin'));
			}
		} elseif ($response == true) {
			set_alert('success', app_lang('updated_successfully', app_lang('staff_member')));
		}
		app_redirect(('hr_profile/member/' . $data['id'] . '/profile'));
	}
}

	/**
	 * add update staff bonus discipline
	 */
	public function add_update_staff_bonus_discipline() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$this->hr_profile_model->update_bonus_discipline($data['id_detail'], $data);
			$message = app_lang('hr_updated_successfully');
			set_alert('success', $message);
			app_redirect(('hr_profile/view_bonus_discipline/' . $data['id']));
		}
	}
	/**
	 * file view bonus discipline
	 * @param  integer $id
	 * @return view
	 */
	public function file_view_bonus_discipline($id) {
		$data['rel_id'] = $id;
		$data['file'] = $this->hr_profile_model->get_file_info($id, 'bonus_discipline');
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		$this->load->view('_file_bonus_discipline', $data);
	}

	/**
	 * workplaces
	 * @return [type] 
	 */
	public function workplaces() {
		$data['workplaces'] = $this->hr_profile_model->get_workplace();
		return $this->template->rander("Hr_profile\Views\includes\workplace", $data);
	}

	/**
	 * list workplace data
	 * @return [type] 
	 */
	public function list_workplace_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_workplace();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_workplace_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make workplace row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_workplace_row($data) {

		$options = '';
		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){
			$options .= modal_anchor(get_uri("hr_profile/workplace_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('hr_edit_workplace'), "data-post-id" => $data['id']));
		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("hr_profile/delete_workplace/".$data['id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['name']),
			nl2br($data['workplace_address']),
			$data['latitude'],
			$data['longitude'],
			$options
		);
	}

	/**
	 * workplace modal form
	 * @return [type] 
	 */
	public function workplace_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$workplace_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['workplace_data'] = $this->hr_profile_model->get_workplace($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		return $this->template->view('Hr_profile\Views\includes\modal_forms\workplace_modal', $data);
	}


	/**
	 * workplace
	 * @param  string $id
	 * @return [type]
	 */
	public function workplace($id = '') {

		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if(isset($data['files'])){
				unset($data['files']);
			}

			if (!is_numeric($id)) {
				$id = $this->hr_profile_model->add_workplace($data);

				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}

				app_redirect('hr_profile/workplaces');
			} else {
				$success = $this->hr_profile_model->update_workplace($data, $id);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/workplaces');
			}

		}
	}

	/**
	 * delete workplace
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_workplace($id) {
		if (!$id) {
			app_redirect('hr_profile/workplaces');
		}
		$response = $this->hr_profile_model->delete_workplace($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("warning" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("warning" => false, "message" => app_lang('problem_deleting')));
		}
	}

	public function hr_profile_permission_table() {
		if ($this->input->is_ajax_request()) {

			$select = [
				'staffid',
				'CONCAT(firstname," ",lastname) as full_name',
				'firstname',
				'email',
				'phonenumber',
			];
			$where = [];
			$where[] = 'AND ' . db_prefix() . 'staff.admin != 1';

			$arr_staff_id = hr_profile_get_staff_id_hr_permissions();

			if (count($arr_staff_id) > 0) {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN (' . implode(', ', $arr_staff_id) . ')';
			} else {
				$where[] = 'AND ' . db_prefix() . 'staff.staffid IN ("")';
			}

			$aColumns = $select;
			$sIndexColumn = 'staffid';
			$sTable = db_prefix() . 'staff';
			$join = ['LEFT JOIN ' . db_prefix() . 'roles ON ' . db_prefix() . 'roles.roleid = ' . db_prefix() . 'staff.role'];

			$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'roles.name as role_name', db_prefix() . 'staff.role']);

			$output = $result['output'];
			$rResult = $result['rResult'];

			$not_hide = '';

			foreach ($rResult as $aRow) {
				$row = [];

				$row[] = '<a href="' . admin_url('staff/member/' . $aRow['staffid']) . '">' . $aRow['full_name'] . '</a>';

				$row[] = $aRow['role_name'];
				$row[] = $aRow['email'];
				$row[] = $aRow['phonenumber'];

				$options = '';

				if (hr_has_permission('hr_profile_can_edit_setting')) {
					$options = icon_btn('#', 'edit', 'btn-default', [
						'title' => app_lang('hr_edit'),
						'onclick' => 'hr_profile_permissions_update(' . $aRow['staffid'] . ', ' . $aRow['role'] . ', ' . $not_hide . '); return false;',
					]);
				}

				if (hr_has_permission('hr_profile_can_delete_setting')) {
					$options .= icon_btn('hr_profile/delete_hr_profile_permission/' . $aRow['staffid'], 'remove', 'btn-danger _delete', ['title' => app_lang('delete')]);
				}

				$row[] = $options;

				$output['aaData'][] = $row;
			}

			echo json_encode($output);
			die();
		}
	}

	/**
	 * permission modal
	 * @return [type]
	 */
	public function permission_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->request->getPost('slug') === 'update') {
			$staff_id = $this->request->getPost('staff_id');
			$role_id = $this->request->getPost('role_id');

			$data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

			if (isset($staff_id)) {
				$data['member'] = $this->staff_model->get($staff_id);
			}

			$data['roles_value'] = $this->roles_model->get();
			$data['staffs'] = hr_profile_get_staff_id_dont_permissions();
			$add_new = $this->request->getPost('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}

			return $this->template->view('Hr_profile\Views\includes/permissions', $data);
		}
	}

	/**
	 * hr profile update permissions
	 * @param  string $id
	 * @return [type]
	 */
	public function hr_profile_update_permissions($id = '') {
		if (!is_admin()) {
			app_redirect("forbidden");
		}
		$data = $this->request->getPost();

		if (!isset($id) || $id == '') {
			$id = $data['staff_id'];
		}

		if (isset($id) && $id != '') {

			$data = hooks()->apply_filters('before_update_staff_member', $data, $id);

			if (is_admin()) {
				if (isset($data['administrator'])) {
					$data['admin'] = 1;
					unset($data['administrator']);
				} else {
					if ($id != get_staff_user_id1()) {
						if ($id == 1) {
							return [
								'cant_remove_main_admin' => true,
							];
						}
					} else {
						return [
							'cant_remove_yourself_from_admin' => true,
						];
					}
					$data['admin'] = 0;
				}
			}

			$this->db->where('staffid', $id);
			$this->db->update(db_prefix() . 'staff', [
				'role' => $data['role'],
			]);

			$response = $this->staff_model->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $id);
		} else {
			$this->load->model('roles_model');

			$role_id = $data['role'];
			unset($data['role']);
			unset($data['staff_id']);

			$data['update_staff_permissions'] = true;

			$response = $this->roles_model->update($data, $role_id);
		}

		if (is_array($response)) {
			if (isset($response['cant_remove_main_admin'])) {
				set_alert('warning', app_lang('staff_cant_remove_main_admin'));
			} elseif (isset($response['cant_remove_yourself_from_admin'])) {
				set_alert('warning', app_lang('staff_cant_remove_yourself_from_admin'));
			}
		} elseif ($response == true) {
			set_alert('success', app_lang('updated_successfully', app_lang('staff_member')));
		}
		app_redirect(('hr_profile/setting?group=hr_profile_permissions'));

	}

	/**
	 * staff id changed
	 * @param  [type] $staff_id
	 * @return [type]
	 */
	public function staff_id_changed($staff_id) {
		$role_id = '';
		$status = 'false';
		$r_permission = [];

		$staff = $this->staff_model->get($staff_id);

		if ($staff) {
			if (count($staff->permissions) > 0) {
				foreach ($staff->permissions as $permission) {
					$r_permission[$permission['feature']][] = $permission['capability'];
				}
			}

			$role_id = $staff->role;
			$status = 'true';

		}

		if (count($r_permission) > 0) {
			$data = ['role_id' => $role_id, 'status' => $status, 'permission' => 'true', 'r_permission' => $r_permission];
		} else {
			$data = ['role_id' => $role_id, 'status' => $status, 'permission' => 'false', 'r_permission' => $r_permission];
		}

		echo json_encode($data);
		die;
	}

	/**
	 * delete hr profile permission
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_hr_profile_permission($id) {
		if (!is_admin()) {
			app_redirect("forbidden");
		}

		$response = $this->hr_profile_model->delete_hr_profile_permission($id);

		if (is_array($response) && isset($response['referenced'])) {
			set_alert('warning', app_lang('hr_is_referenced', app_lang('department_lowercase')));
		} elseif ($response == true) {
			set_alert('success', app_lang('deleted', app_lang('hr_department')));
		} else {
			set_alert('warning', app_lang('problem_deleting', app_lang('department_lowercase')));
		}
		app_redirect(('hr_profile/setting?group=hr_profile_permissions'));

	}

	/**
	 * zen unit chart
	 * @param  [type] $department
	 * @return [type]
	 */
	public function zen_unit_chart($department) {
		$this->load->model('staff_model');
		$dpm = $this->departments_model->get($department);
		$dpm_data = $this->hr_profile_model->get_data_dpm_chart($department);
		$reality_now = $this->hr_profile_model->count_reality_now($department);

		$list_job = $this->hr_profile_model->list_job_department($department);

		$html = '<table class="table table-striped table-bordered text-nowrap dataTable no-footer dtr-inline collapsed"  ><tbody>';
		$html .= '<tr class="text-white">
		<th>' . app_lang('position') . '</th>
		<th>' . app_lang('hr_now') . '</th>
		<th>' . app_lang('hrplanning') . '</th>
		</tr>';

		$li_jobid = [];

		if (count($list_job) > 0) {
			foreach ($list_job as $lj) {
				if ($lj != '') {
					if (!in_array($lj, $li_jobid)) {
						$html .= '<tr class="text-white">
						<td class="text-left">' . job_name_by_id($lj) . '</td>
						<td>' . count_staff_job_unnit($department, $lj) . '</td>
						<td>' . count_staff_job_unnit($department, $lj) . '</td>
						</tr>';
					}
				}

			}
		}

		$html .= '</tbody></table>';

		echo json_encode([
			'dpm_name' => $dpm->name,
			'data' => $dpm_data,
			'reality_now' => $reality_now,
			'html' => $html,
		]);

	}

	/**
	 * get list job position training
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_list_job_position_training($id) {
		$list = $this->hr_profile_model->get_job_position_training_de($id);
		if (isset($list)) {
			$description = $list->description;
		} else {
			$description = '';

		}
		echo json_encode([
			'description' => $description,

		]);
	}

	/**
	 * delete job position training process
	 * @param  [type] $training_id
	 * @return [type]
	 */
	public function delete_job_position_training_process() {
		if (!hr_has_permission('hr_profile_can_delete_job_description')) {
			app_redirect("forbidden");
		}

		$training_id = $this->request->getPost('id');

		if (!$training_id) {
			app_redirect(('hr_profile/training_programs'));
		}
		$success = $this->hr_profile_model->delete_job_position_training_process($training_id);
		if ($success) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_profile/training_programs');
	}

	/**
	 * delete position training
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_position_training() {
		if (!hr_has_permission('hr_profile_can_delete_job_description')) {
			app_redirect("forbidden");
		}

		$id = $this->request->getPost('id');

		if (!$id) {
			app_redirect(('hr_profile/training_libraries'));
		}
		$success = $this->hr_profile_model->delete_position_training($id);
		if ($success) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect(('hr_profile/training_libraries'));
	}

	/**
	 * table contract
	 * @return [type]
	 */
	public function table_contract() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'contracts/table_contract'), $dataPost);
	}

	/**
	 * contracts
	 * @param  string $id
	 * @return [type]
	 */
	public function contracts($id = '') {

		if (!hr_has_permission('hr_profile_can_view_global_hr_contract') && !hr_has_permission('hr_profile_can_view_own_hr_contract') && !is_admin()) {
			app_redirect("forbidden");
		}

		//filter from dasboard
		$data_get = $this->request->getGet();
		if (isset($data_get['to_expire'])) {
			$data['to_expire'] = true;
		}

		if (isset($data_get['overdue_contract'])) {
			$data['overdue_contract'] = true;
		}

		$data['hrmcontractid'] = $id;
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
			"status" => "active",

		);
		$data['staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['duration'] = $this->hr_profile_model->get_duration();
		$data['contract_attachment'] = $this->hr_profile_model->get_hrm_attachments_file($id, 'hr_contract');
		$data['dep_tree'] = json_encode($this->hr_profile_model->get_department_tree());

		$data['title'] = app_lang('hr_hr_contracts');
		return $this->template->rander('Hr_profile\Views\contracts/manage_contract', $data);
	}

	/**
	 * contract
	 * @param  string $id
	 * @return [type]
	 */
	public function contract($id = '') {
		if (!hr_has_permission('hr_profile_can_view_global_hr_contract') && !hr_has_permission('hr_profile_can_view_own_hr_contract') && !is_admin()) {
			app_redirect("forbidden");
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$id = $this->request->getPost('id');
			if(isset($data['id'])){
				unset($data['id']);
			}
			$count_file = 0;
			if (!is_numeric($id)) {
				if (!hr_has_permission('hr_profile_can_create_hr_contract') && !is_admin()) {
					app_redirect("forbidden");
				}
				$id = $this->hr_profile_model->add_contract($data);

				//upload file
				if ($id) {
					$success = true;
					$_id = $id;
					$message = app_lang('added_successfully', app_lang('contract_attachment'));
					$uploadedFiles = hr_profile_handle_contract_attachments_array($id, 'file');
				}

				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
					app_redirect('hr_profile/contracts');
				}

			} else {
				if (!hr_has_permission('hr_profile_can_edit_hr_contract') && !is_admin()) {
					app_redirect("forbidden");
				}

				if(isset($data['isedit'])){
					unset($data['isedit']);
				}
				$response = $this->hr_profile_model->update_contract($data, $id);
				//upload file
				if ($id) {
					$success = true;
					$_id = $id;
					$message = app_lang('added_successfully', app_lang('contract_attachment'));
					$uploadedFiles = hr_profile_handle_contract_attachments_array($id, 'file');
				}

				if (is_array($response)) {
					if (isset($response['cant_remove_main_admin'])) {
						$this->session->setFlashdata("error_message", app_lang("staff_cant_remove_main_admin"));
					} elseif (isset($response['cant_remove_yourself_from_admin'])) {
						$this->session->setFlashdata("error_message", app_lang("staff_cant_remove_yourself_from_admin"));
					}
				} elseif ($response == true) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/contracts');
			}
		}

		$data['title'] = '';
		if ($id == '') {
			$title = app_lang('hr_hr_add');
			$data['title'] = $title;
			$data['staff_contract_code'] = $this->hr_profile_model->create_code('staff_contract_code');
		} else {

			$contract = $this->hr_profile_model->get_contract($id);

			//load deparment by manager
			if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_contract')) {
				//View own
				if ($contract) {
					$staff_ids = $this->hr_profile_model->get_staff_by_manager();
					if (count($staff_ids) > 0) {
						if (!in_array($contract->staff, $staff_ids)) {
							app_redirect("forbidden");
						}
					} else {
						app_redirect("forbidden");
					}
				}

			}

			$contract_detail = $this->hr_profile_model->get_contract_detail($id);
			$data['contract_attachment'] = $this->hr_profile_model->get_hrm_attachments_file($id, 'hr_contract');
			if (!$contract) {
				blank_page('Contract Not Found', 'danger');
			}

			$data['contracts'] = $contract;
			if ($contract) {
				$get_staff_role = $this->hr_profile_model->get_staff_role($contract->staff_delegate);
				$data['staff_delegate_role'] = $get_staff_role;
			}

			$data['contract_details'] = json_encode($contract_detail);
			if ($contract) {
				$title = $this->hr_profile_model->get_contracttype_by_id($contract->name_contract);
				if (isset($title[0]['name_contracttype'])) {
					$data['title'] = $title[0]['name_contracttype'];
				}
			}

		}

		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
			"status" => "active",

		);
		$data['staff'] = $this->Users_model->get_details($options)->getResultArray();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_allowance_type'] = $this->hr_profile_model->get_salary_allowance_handsontable();
		$types = [];
		$types[] = [
			'id' => 'salary',
			'label' => app_lang('salary'),
		];
		$types[] = [
			'id' => 'allowance',
			'label' => app_lang('allowance'),
		];

		$data['types'] = $types;

		return $this->template->rander('Hr_profile\Views/contracts/contract', $data);
	}

	/**
	 * delete contract
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_contract() {
		if (!hr_has_permission('hr_profile_can_delete_hr_contract') && !is_admin()) {
			app_redirect("forbidden");
		}
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_profile/contracts'));
		}

		$response = $this->hr_profile_model->delete_contract($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("hr_is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_profile/contracts');

	}

	/**
	 * contract code exists
	 * @return [type]
	 */
	public function contract_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				// First we need to check if the email is the same
				$contractid = $this->request->getPost('contractid');

				if ($contractid != '') {

					$staff_contract = $this->hr_profile_model->get_contract($contractid);
					if ($staff_contract->contract_code == $this->request->getPost('contract_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('contract_code', $this->request->getPost('contract_code'));
				$total_rows = $this->db->count_all_results(db_prefix() . 'hr_staff_contract');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/**
	 * get hrm contract data ajax
	 * @param  [type] $id
	 * @return [type]
	 */
	public function view_staff_contract($id) {
		$contract = $this->hr_profile_model->get_contract($id);
		$contract_detail = $this->hr_profile_model->get_contract_detail($id);
		if (!$contract) {
			blank_page('Contract Not Found', 'danger');
		}

		$data['contracts'] = $contract;
		if ($contract) {
			$data['staff_delegate_role'] = $this->hr_profile_model->get_staff_role($contract->staff_delegate);
			$title = $this->hr_profile_model->get_contracttype_by_id($contract->name_contract);
			$data['title'] = $contract->contract_code;

			//check update content from contract template (in case old data)
			if (strlen($contract->content) == 0) {

				$this->hr_profile_model->update_hr_staff_contract_content($id, $contract->staff);
			}

		}

		$data['contract_details'] = $contract_detail;
		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
			"status" => "active",

		);
		$data['staff'] = $this->Users_model->get_details($options)->getResultArray();
		$data['allowance_type'] = $this->hr_profile_model->get_allowance_type();
		$data['salary_form'] = $this->hr_profile_model->get_salary_form();
		$data['contract_attachment'] = $this->hr_profile_model->get_hrm_attachments_file($id, 'hr_contract');

		$model_info = new \stdClass;
		$model_info->id = 0;
		$model_info->content = $contract->content;
		$model_info->default_message = $this->template->view('Hr_profile\Views\includes\modal_forms\sample_contract_html');
		$view_data['model_info'] = $model_info;
		$view_data['column_name'] = 'content';
		$variables = staff_contract_variables();
		$view_data['variables'] = $variables ? $variables : array();
		$view_data['unsupported_title_variables'] = json_encode(array("SIGNATURE", "TASKS_LIST", "TICKET_CONTENT", "MESSAGE_CONTENT", "EVENT_DETAILS"));
		$data['sample_contract'] = $this->template->view('Hr_profile\Views\includes\modal_forms\contract_template_form', $view_data);

		return $this->template->rander('Hr_profile\Views/contracts/contract_preview_template', $data);
	}

	/**
	 * get staff role
	 * @return [type]
	 */
	public function get_staff_role() {
		$role_name = '';
		if ($this->request->getPost()) {

			$id = $this->request->getPost('id');
			$name_object = $this->hr_profile_model->hr_profile_run_query('select r.title from ' . db_prefix() . 'users as s join ' . db_prefix() . 'roles as r on s.role_id = r.id where s.id = ' . $id);
			if (count($name_object) > 0) {
				$role_name = $name_object[0]['title'];
			}
		}

		echo json_encode([
			'name' => $role_name,
		]);
	}

	/**
	 * get contract type
	 * @param  string $id
	 * @return [type]
	 */
	public function get_contract_type($id = '') {
		$contract_type = $this->hr_profile_model->get_contracttype($id);

		echo json_encode([
			'contract_type' => $contract_type,
		]);
		die;

	}

	/**
	 * prefix numbers
	 * @return [type] 
	 */
	public function prefix_numbers() {
		return $this->template->rander("Hr_profile\Views\includes\prefix_number", []);
	}

	/**
	 * inventory setting
	 * @return [type]
	 */
	public function prefix_number() {
		$data = $this->request->getPost();

		if ($data) {

			$success = $this->hr_profile_model->update_prefix_number($data);

			if ($success == true) {
				$this->session->setFlashdata("success_message", app_lang("hr_updated_successfully"));
			}

			app_redirect('hr_profile/prefix_numbers');
		}
	}

	/**
	 * get code
	 * @param  String $rel_type
	 * @return String
	 */
	public function get_code($rel_type) {
		//get data
		$code = $this->hr_profile_model->create_code($rel_type);

		echo json_encode([
			'code' => $code,
		]);
		die;

	}

	/**
	 * import job position
	 * @return [type]
	 */
	public function import_job_position() {

		$user_id = $this->login_user->id;

		$_personal_language = get_setting('user_' . $user_id . '_personal_language');
		if(strlen($_personal_language) == 0){
			$_personal_language = get_setting("language");
		}

		$data['active_language'] = $_personal_language;
		$data['title'] = _l('hr_import_job_positions');
		$data['site_url'] = base_url();

		return $this->template->rander("Hr_profile\Views\job_position_manage/position_manage/import_position", $data);
	}

	/**
	 * dependent person
	 * @param  string $id
	 * @return [type]
	 */
	public function dependent_person($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			$id = $this->request->getPost('id');

			if (!is_numeric($id)) {
				$manage = $this->request->getPost('manage');
				unset($data['manage']);

				$id = $this->hr_profile_model->add_dependent_person($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}

				if ($manage) {
					app_redirect('hr_profile/dependent_persons');
				} else {
					app_redirect(('hr_profile/staff_profile/' . get_staff_user_id1() . '/staff_dependent'));
				}
			} else {
				$manage = $this->request->getPost('manage');
				$id = $data['id'];
				unset($data['id']);
				unset($data['manage']);
				$success = $this->hr_profile_model->update_dependent_person($data, $id);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}

				if ($manage) {
					app_redirect('hr_profile/dependent_persons');
				} else {
					app_redirect(('hr_profile/staff_profile/' . get_staff_user_id1() . '/staff_dependent'));
				}
			}
		}
	}

	/**
	 * delete dependent person
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_dependent_person() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect("forbidden");
		}
		$response = $this->hr_profile_model->delete_dependent_person($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("hr_is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("record_cannot_be_deleted"));
		}
		app_redirect(('hr_profile/staff_profile/' . get_staff_user_id1() . '/staff_dependent'));
	}

	/**
	 * approval dependents
	 * @return [type]
	 */
	public function dependent_persons() {

		if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_dependent_persons') && !hr_has_permission('hr_profile_can_view_own_dependent_persons')) {
			app_redirect("forbidden");
		}

		$data['approval'] = $this->hr_profile_model->get_dependent_person();
		$options = array(
			"user_type" => "staff",
			"status" => "active",
			"deleted" => 0,
		);
		$data['staff'] = $this->Users_model->get_details($options)->getResultArray();
		$data['title'] = app_lang("hr_dependent_persons");

		return $this->template->rander('Hr_profile\Views/dependent_person/manage_dependent_person', $data);
	}

	/**
	 * approval status
	 * @return [type]
	 */
	public function approval_status() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			$success = $this->hr_profile_model->update_approval_status($data);
			if ($success) {
				$message = app_lang('hr_updated_successfully');
				echo json_encode([
					'success' => true,
					'message' => $message,
				]);
			} else {
				$message = app_lang('hr_updated_failed');
				echo json_encode([
					'success' => false,
					'message' => $message,
				]);
			}
		}
	}

	/**
	 * table dependent person
	 * @return [type]
	 */
	public function table_dependent_person() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'dependent_person/table_dependent_person'), $dataPost);
	}

	/**
	 * import xlsx dependent person
	 * @return [type]
	 */
	public function import_xlsx_dependent_person() {
		if (!is_admin() && !hr_has_permission('hr_profile_can_create_dependent_persons')) {
			app_redirect("forbidden");
		}

		$data_staff = $this->hr_profile_model->get_staff(get_staff_user_id1());

		/*get language active*/
		if ($data_staff) {
			if ($data_staff->default_language != '') {
				$data['active_language'] = $data_staff->default_language;

			} else {

				$data['active_language'] = get_option('active_language');
			}

		} else {
			$data['active_language'] = get_option('active_language');
		}

		return $this->template->view('Hr_profile\Views\hr_profile/dependent_person/import_dependent_person', $data);
	}

	/**
	 * import file xlsx dependent person
	 * @return [type]
	 */
	public function import_file_xlsx_dependent_person() {
		if (!is_admin() && !hr_has_permission('hr_profile_can_create_dependent_persons')) {
			app_redirect("forbidden");
		}

		$total_row_false = 0;
		$total_rows = 0;
		$dataerror = 0;
		$total_row_success = 0;
		if ($this->request->getPost()) {

			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$import_result = true;
						$rows = [];

						$objReader = new PHPExcel_Reader_Excel2007();
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($newFilePath);
						$rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
						$sheet = $objPHPExcel->getActiveSheet();

						$dataError = new PHPExcel();
						$dataError->setActiveSheetIndex(0);

						$dataError->getActiveSheet()->setTitle(app_lang('hr_error_data'));
						$dataError->getActiveSheet()->getColumnDimension('A')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('B')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('C')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('D')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('E')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('F')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('G')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('H')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('I')->setWidth(20);
						$dataError->getActiveSheet()->getColumnDimension('J')->setWidth(20);

						$dataError->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
						$dataError->getActiveSheet()->setCellValue('A1', app_lang('hr_hr_code'));
						$dataError->getActiveSheet()->setCellValue('B1', app_lang('hr_dependent_name'));
						$dataError->getActiveSheet()->setCellValue('C1', app_lang('relationship'));
						$dataError->getActiveSheet()->setCellValue('D1', app_lang('birth_date'));
						$dataError->getActiveSheet()->setCellValue('E1', app_lang('identification'));
						$dataError->getActiveSheet()->setCellValue('F1', app_lang('reason_'));
						$dataError->getActiveSheet()->setCellValue('G1', app_lang('hr_start_month'));
						$dataError->getActiveSheet()->setCellValue('H1', app_lang('hr_end_month'));
						$dataError->getActiveSheet()->setCellValue('I1', app_lang('status'));
						$dataError->getActiveSheet()->setCellValue('J1', app_lang('hr_error_data_description'));

						$styleArray = array(
							'font' => array(
								'bold' => true,
								'color' => array('rgb' => 'ff0000'),

							));

						//start write on line 2
						$numRow = 2;
						$total_rows = 0;
						$arr_insert = [];
						//get data for compare

						foreach ($rowIterator as $row) {
							$rowIndex = $row->getRowIndex();
							if ($rowIndex > 1) {
								$total_rows++;

								$rd = array();
								$flag = 0;
								$flag2 = 0;
								$flag_mail = 0;
								$string_error = '';

								$value_cell_hrcode = $sheet->getCell('A' . $rowIndex)->getValue();
								$value_cell_dependent_name = $sheet->getCell('B' . $rowIndex)->getValue();
								$value_cell_bir_of_day_dependent = $sheet->getCell('D' . $rowIndex)->getValue();
								$value_cell_dependent_identification = $sheet->getCell('E' . $rowIndex)->getValue();
								$value_cell_start_time = $sheet->getCell('G' . $rowIndex)->getValue();
								$value_cell_end_time = $sheet->getCell('H' . $rowIndex)->getValue();
								$value_cell_status = $sheet->getCell('I' . $rowIndex)->getValue();

								$pattern = '#^[a-z][a-z0-9\._]{2,31}@[a-z0-9\-]{3,}(\.[a-z]{2,4}){1,2}$#';
								$reg_day = '#^([0-2][0-9]|(3)[0-1])(\/)(((0)[0-9])|((1)[0-2]))(\/)\d{4}$#';

								/*check null*/
								if (is_null($value_cell_hrcode) == true) {
									$string_error .= app_lang('hr_hr_code') . app_lang('not_yet_entered');
									$flag = 1;
								}

								if (is_null($value_cell_dependent_name) == true) {
									$string_error .= app_lang('hr_dependent_name') . app_lang('not_yet_entered');
									$flag = 1;
								}

								//check hr_code exist
								if (is_null($value_cell_hrcode) != true) {
									$this->db->where('staff_identifi', $value_cell_hrcode);
									$hrcode = $this->db->count_all_results('tblstaff');
									if ($hrcode == 0) {
										$string_error .= app_lang('hr_hr_code') . app_lang('does_not_exist');
										$flag2 = 1;
									}

								}

								//check bir of day dependent person input
								if (is_null($value_cell_bir_of_day_dependent) != true) {
									if (preg_match($reg_day, $value_cell_bir_of_day_dependent, $match) != 1) {
										$string_error .= app_lang('days_for_identity') . app_lang('_check_invalid');
										$flag = 1;
									}

								}

								//check start_time
								if (is_null($value_cell_start_time) != true) {
									if (preg_match($reg_day, $value_cell_start_time, $match) != 1) {
										$string_error .= app_lang('hr_start_month') . app_lang('_check_invalid');
										$flag = 1;
									}

								}

								//check end_time
								if (is_null($value_cell_end_time) != true) {
									if (preg_match($reg_day, $value_cell_end_time, $match) != 1) {
										$string_error .= app_lang('hr_end_month') . app_lang('_check_invalid');
										$flag = 1;
									}

								}

								if (($flag == 1) || ($flag2 == 1)) {
									$dataError->getActiveSheet()->setCellValue('A' . $numRow, $sheet->getCell('A' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('B' . $numRow, $sheet->getCell('B' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('C' . $numRow, $sheet->getCell('C' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('D' . $numRow, $sheet->getCell('D' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('E' . $numRow, $sheet->getCell('E' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('F' . $numRow, $sheet->getCell('F' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('G' . $numRow, $sheet->getCell('G' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('H' . $numRow, $sheet->getCell('H' . $rowIndex)->getValue());
									$dataError->getActiveSheet()->setCellValue('I' . $numRow, $sheet->getCell('I' . $rowIndex)->getValue());

									$dataError->getActiveSheet()->setCellValue('J' . $numRow, $string_error)->getStyle('J' . $numRow)->applyFromArray($styleArray);

									$numRow++;
									$total_row_false++;
								}

								if (($flag == 0) && ($flag2 == 0)) {

									if (is_numeric($value_cell_status) && ($value_cell_status == '2')) {
										/*reject*/
										$rd['status'] = 2;
									} else {
										/*approval*/
										$rd['status'] = 1;
									}

									/*staff id is HR_code, input is HR_CODE, insert => staffid*/
									$rd['staffid'] = $sheet->getCell('A' . $rowIndex)->getValue();
									$rd['dependent_name'] = $sheet->getCell('B' . $rowIndex)->getValue();
									$rd['relationship'] = $sheet->getCell('C' . $rowIndex)->getValue();
									$rd['dependent_bir'] = date('Y-m-d', strtotime(str_replace('/', '-', $sheet->getCell('D' . $rowIndex)->getValue())));
									$rd['dependent_iden'] = $sheet->getCell('E' . $rowIndex)->getValue() != null ? $sheet->getCell('E' . $rowIndex)->getValue() : '';
									$rd['reason'] = $sheet->getCell('F' . $rowIndex)->getValue();
									$rd['start_month'] = date('Y-m-d', strtotime(str_replace('/', '-', $sheet->getCell('G' . $rowIndex)->getValue())));
									$rd['end_month'] = date('Y-m-d', strtotime(str_replace('/', '-', $sheet->getCell('H' . $rowIndex)->getValue())));

									array_push($arr_insert, $rd);
								}

							}

						}
						$total_rows = $total_rows;
						$total_row_success = count($arr_insert);
						$dataerror = $dataError;
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {

							$objWriter = new PHPExcel_Writer_Excel2007($dataError);
							$filename = 'Import_dependent_person_error_' . get_staff_user_id1() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$objWriter->save(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));

						} else {
							$this->db->insert_batch(db_prefix() . 'hr_dependent_person', $arr_insert);
						}
						$import_result = true;
						@delete_dir($tmpDir);

					}
				} else {
					set_alert('warning', app_lang('import_upload_failed'));
				}
			}

		}
		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id1(),
		]);

	}

	/**
	 * admin delete dependent person
	 * @param  [type] $id
	 * @return [type]
	 */
	public function admin_delete_dependent_person() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_profile/member' . get_staff_user_id1()));
		}
		$response = $this->hr_profile_model->delete_dependent_person($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("hr_is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("record_cannot_be_deleted"));
		}
		app_redirect('hr_profile/dependent_persons');
	}

	/**
	 * delete_error file day before
	 * @return [type]
	 */
	public function delete_error_file_day_before($before_day = '', $folder_name = '') {
		if ($before_day != '') {
			$day = $before_day;
		} else {
			$day = '7';
		}

		if ($folder_name != '') {
			$folder = $folder_name;
		} else {
			$folder = HR_PROFILE_ERROR;
		}

		//Delete old file before 7 day
		$date = date_create(date('Y-m-d H:i:s'));
		date_sub($date, date_interval_create_from_date_string($day . " days"));
		$before_7_day = strtotime(date_format($date, "Y-m-d H:i:s"));

		foreach (glob($folder . '*') as $file) {

			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				//don't delete index.html file
				if ($filename != 'index.html') {
					$file_name_arr = explode("_", $filename);
					$date_create_file = array_pop($file_name_arr);
					$date_create_file = str_replace('.xlsx', '', $date_create_file);

					if ((float) $date_create_file <= (float) $before_7_day) {
						unlink($folder . $filename);
					}
				}
			}
		}
		return true;
	}

	/**
	 * dependent person modal
	 * @return [type]
	 */
	public function dependent_person_modal() {
		$this->access_only_team_members();
		$id = $this->request->getPost('id');
		if(is_numeric($id)){
			$data['dependent_person'] = $this->hr_profile_model->get_dependent_person($id);
		}else{
			$id = '';
		}

		$options = array(
			"user_type" => "staff",
			"status" => "active",
			"deleted" => 0,
		);
		$data['staff_members'] = $this->Users_model->get_details($options)->getResultArray();
		$data['manage'] = $this->request->getPost('manage');
		$data['id'] = $id;

		return $this->template->view('Hr_profile\Views/dependent_person/dependent_person_modal', $data);
	}


	/**
	 * resignation procedures
	 * @return [type]
	 */
	public function resignation_procedures() {

		if (!hr_has_permission('hr_profile_can_view_global_layoff_checklists') && !hr_has_permission('hr_profile_can_view_own_layoff_checklists') && !is_admin()) {
			app_redirect("forbidden");
		}

		$arr_staff_quiting_work = [];
		$get_list_quiting_work = $this->hr_profile_model->get_list_quiting_work();
		foreach ($get_list_quiting_work as $value) {
			$arr_staff_quiting_work[] = $value['staffid'];
		}
		
		if(count($arr_staff_quiting_work) > 0){
			$data['staffs'] = $this->hr_profile_model->hr_profile_run_query('select * from '.get_db_prefix().'users where status = "active" AND user_type ="staff" AND id NOT IN ('.implode(",", $arr_staff_quiting_work).')');
		}else{
			$data['staffs'] = $this->hr_profile_model->hr_profile_run_query('select * from '.get_db_prefix().'users where status = "active" AND user_type ="staff"');
		}


		$data['detail'] = $this->request->getGet('detail');
		$data['title'] = app_lang("procedure_retires");

		return $this->template->rander('Hr_profile\Views\resignation_procedures/manage_resignation_procedures', $data);
	}

	/**
	 * add staff quitting work
	 */
	public function add_resignation_procedure() {
		if (!hr_has_permission('hr_profile_can_edit_layoff_checklists') && !hr_has_permission('hr_profile_can_create_layoff_checklists') && !is_admin()) {
			app_redirect("forbidden");
		}

		$data = $this->request->getPost();
		$response = $this->hr_profile_model->add_resignation_procedure($data);
		if ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("added_successfully"));
		} else if ($response == false) {
			$this->session->setFlashdata("error_message", app_lang("This_person_has_been_on_the_list_of_quit_work"));
		}
		app_redirect('hr_profile/resignation_procedures');
	}

	/**
	 * delete resignation procedure
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_resignation_procedure($id) {

		if (!hr_has_permission('hr_profile_can_edit_layoff_checklists') && !is_admin()) {
			app_redirect("forbidden");
		}

		$success = $this->hr_profile_model->delete_procedures_for_quitting_work($id);
		if ($success) {
			set_alert('success', app_lang('deleted', app_lang('hr_procedures_for_quitting_work')));
		}

		app_redirect(('hr_profile/resignation_procedures'));
	}

	/**
	 * table resignation procedures
	 * @return [type]
	 */
	public function table_resignation_procedures() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'resignation_procedures/table_resignation_procedures'), $dataPost);
	}

	/**
	 * get staff info of resignation procedures
	 * @param  [type] $staff_id
	 * @return [type]
	 */
	public function get_staff_info_of_resignation_procedures($staff_id) {
		$staff_email = '';
		$staff_department_name = '';
		$staff_job_position = '';
		$status = true;
		$message = '';

		//check resignation procedures exist
		$resignation_procedure = $this->hr_profile_model->get_resignation_procedure_by_staff($staff_id);

		if (!$resignation_procedure) {
			$options = array(
				"user_type" => "staff",
				"id" => $staff_id,
			);
			$staff = $this->Users_model->get_details($options)->getRow();

			if ($staff) {
				$staff_email = $staff->email;
				$staff_job_position = hr_profile_job_name_by_id($staff->job_position);
				$getdepartment_name = $this->hr_profile_model->getdepartment_name($staff_id);
				$staff_department_name = $getdepartment_name->name; 
			}
		} else {
			$status = false;
			$message = app_lang('hr_resignation_procedure_already_exists');
		}

		echo json_encode([
			'staff_email' => $staff_email,
			'staff_department_name' => $staff_department_name,
			'staff_job_position' => $staff_job_position,
			'status' => $status,
			'message' => $message,
		]);
		die;

	}

	/**
	 * delete procedures for quitting work
	 * @param  [type] $staffid
	 * @return [type]
	 */
	public function delete_procedures_for_quitting_work() {
		if (!hr_has_permission('hr_profile_can_edit_layoff_checklists') && !is_admin()) {
			app_redirect("forbidden");
		}

		$staffid = $this->request->getPost('id');

		$success = $this->hr_profile_model->delete_procedures_for_quitting_work($staffid);
		if ($success) {
			$this->session->setFlashdata("success_message", app_lang("hr_deleted"));
		}

		app_redirect(('hr_profile/resignation_procedures'));
	}

	/**
	 * set data detail staff checklist quit work
	 * @param [type] $staffid
	 */
	public function set_data_detail_staff_checklist_quit_work($staffid) {
		$results = $this->hr_profile_model->get_data_procedure_retire_of_staff($staffid);

		$html = '<input type="hidden" name="staffid" value="' . $staffid . '">';
		$rel_id = '';
		foreach ($results as $key => $value) {

			if ($value['people_handle_id'] == 0) {
				$value['people_handle_id'] = get_staff_user_id1();
			}
			if ($rel_id != $value['rel_id']) {
				$rel_id = $value['rel_id'];
				$html .= '<h5 class="no-margin font-bold text-danger"><span data-feather="check-circle" class="icon-16"></span>  ' . $value['rel_name'] . ' (' . get_staff_full_name1($value['people_handle_id']) . ')<span ></span></h5><br>';

				$html .= ' <a href="#" class="list-group-item1 list-group-item-action">
				<div class="row">
				<div class="col-md-10 resignation-procedures-modal1"><label for="' . $value['id'] . '">' . $value['option_name'] . ' </label></div>
				<div class="col-md-2 text-right">
				<div class="row">
				<div class="col-md-6 pt-1 pr-2">
				<div class="checkbox float-right">';
				if ($value['status'] == 1) {
					$html .= '<input type="checkbox" class="option_name form-check-input" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '" checked disabled>
					<label></label>';
				} else {
					$html .= '<input type="checkbox" class="option_name form-check-input"  name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '">
					<label></label>';
				}
				$html .= '</div>
				</div>
				</div>
				</div>
				</div>
				</a>';
			} else {
				$html .= ' <a href="#" class="list-group-item1 list-group-item-action" >
				<div class="row">
				<div class="col-md-10 resignation-procedures-modal1"><label for="' . $value['id'] . '">' . $value['option_name'] . ' </label></div>
				<div class="col-md-2 text-right">
				<div class="row">
				<div class="col-md-6 pt-1 pr-2">
				<div class="checkbox float-right">';
				if ($value['status'] == 1) {
					$html .= '<input type="checkbox" class="option_name form-check-input" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '" checked disabled>
					<label></label>';
				} else {
					$html .= '<input type="checkbox" class="option_name form-check-input" name="option_name[]" id="' . $value['id'] . '" data-id="' . $value['id'] . '" value="' . $value['id'] . '">
					<label></label>';
				}
				$html .= '</div>
				</div>
				</div>
				</div>
				</div>

				</a>';
			}
		}
		echo json_encode([
			'result' => $html,
			'staff_name' => get_staff_full_name1($staffid),
		]);

	}

	/**
	 * update status quit work
	 * @param  [type] $staffid
	 * @return [type]
	 */
	public function update_status_quit_work() {
		$data = $this->request->getPost();
		$staffid = $data['staffid'];
		$id = $data['id'];
		$result = $this->hr_profile_model->update_status_quit_work($staffid, $id);

		if ($result == 0) {
			$message = app_lang('hr_updated_successfully');
		} else {
			$message = app_lang('hr_update_failed');
		}

		echo json_encode([
			'status' => $result,
			'message' => $message,
		]);

	}

	/**
	 * update status option name
	 * @return [type]
	 */
	public function update_status_option_name() {
		$data = $this->request->getPost();
		if ($data['finish'] == 0) {
			foreach ($data['option_name'] as $id_option) {
				$result = $this->hr_profile_model->update_status_procedure_retire_of_staff(['id' => $id_option]);
			}
		} else {
			$result = $this->hr_profile_model->update_status_procedure_retire_of_staff(['staffid' => $data['staffid']]);
		}

		if ($result) {
			$this->session->setFlashdata("success_message", app_lang("hr_updated_successfully"));
		} else if ($response == false) {
			$this->session->setFlashdata("error_message", app_lang("hr_update_failed"));
		}
		app_redirect('hr_profile/resignation_procedures');
	}

	/**
	 * preview q a file
	 * @param  [type] $id
	 * @param  [type] $rel_id
	 * @return [type]
	 */
	public function preview_q_a_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id1());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Hr_profile\Views\hr_profile/knowledge_base_q_a/preview_q_a_file', $data);
	}

	/**
	 * delete hr profile q a attachment file
	 * @param  [type] $attachment_id
	 * @return [type]
	 */
	public function delete_hr_profile_q_a_attachment_file($attachment_id) {
		if (!has_permission('hr_manage_q_a', '', 'delete')) {
			app_redirect("forbidden");
		}

		$file = $this->misc_model->get_file($attachment_id);
		echo json_encode([
			'success' => $this->hr_profile_model->delete_hr_q_a_attachment_file($attachment_id),
		]);
	}

	/**
	 * get salary allowance value
	 * @param  [type] $rel_type
	 * @return [type]
	 */
	public function get_salary_allowance_value($rel_type) {

		if (preg_match('/^st_/', $rel_type)) {
			$rel_value = str_replace('st_', '', $rel_type);
			$salary_type = $this->hr_profile_model->get_salary_form($rel_value);

			$type = 'salary';
			if ($salary_type) {
				$value = $salary_type->salary_val;
			} else {
				$value = 0;
			}

		} elseif (preg_match('/^al_/', $rel_type)) {
			$rel_value = str_replace('al_', '', $rel_type);
			$allowance_type = $this->hr_profile_model->get_allowance_type($rel_value);

			$type = 'allowance';
			if ($allowance_type) {
				$value = $allowance_type->allowance_val;
			} else {
				$value = 0;
			}

		} else {

		}

		$effective_date = get_my_local_time('Y-m-d');

		echo json_encode([
			'type' => $type,
			'rel_value' => (float) $value,
			'effective_date' => $effective_date,
		]);
		die;
	}

	/**
	 * hrm file contract
	 * @param  [type] $id
	 * @param  [type] $rel_id
	 * @return [type]
	 */
	public function hrm_file_contract($id, $rel_id) {
		$data['discussion_user_profile_image_url'] =  get_staff_image(get_staff_user_id1(), false);
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->hr_profile_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Hr_profile\Views/contracts/preview_contract_file', $data);
	}

	/**
	 * delete hrm contract attachment file
	 * @param  [type] $attachment_id
	 * @return [type]
	 */
	public function delete_hrm_contract_attachment_file($attachment_id) {
		if (!hr_has_permission('hr_profile_can_delete_hr_contract') && !is_admin()) {
			app_redirect("forbidden");
		}

		$file = $this->hr_profile_model->get_hr_profile_attachments_delete($attachment_id);
		echo json_encode([
			'success' => $this->hr_profile_model->delete_hr_contract_attachment_file($attachment_id),
		]);
	}

	/**
	 * member modal
	 * @return [type]
	 */
	public function member_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->request->getPost('slug') === 'create') {

			return $this->template->view('Hr_profile\Views\hr_record/mew_member', $data);

		} else if ($this->request->getPost('slug') === 'update') {
			$staff_id = $this->request->getPost('staff_id');
			$role_id = $this->request->getPost('role_id');

			$data = ['funcData' => ['staff_id' => isset($staff_id) ? $staff_id : null]];

			if (isset($staff_id)) {
				$data['member'] = $this->staff_model->get($staff_id);
			}

			$data['roles_value'] = $this->roles_model->get();
			$data['staffs'] = hr_profile_get_staff_id_dont_permissions();
			$add_new = $this->request->getPost('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}
			$this->load->model('currencies_model');

			$options = array(
				"status" => "active",
				"user_type" => "staff",
			);
			$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

			$data['base_currency'] = $this->currencies_model->get_base_currency();
			$department_options = array(
				"deleted" => 0,
			);
			$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();
			$data['staff_departments'] = $this->departments_model->get_staff_departments($staff_id);
			$data['positions'] = $this->hr_profile_model->get_job_position();
			$data['workplace'] = $this->hr_profile_model->get_workplace();
			$data['staff_cover_image'] = $this->hr_profile_model->get_hr_profile_file($staff_id, 'staff_profile_images');
			$data['manage_staff'] = $this->request->getPost('manage_staff');
			return $this->template->view('Hr_profile\Views\hr_record/update_member', $data);
		}
	}

	/**
	 * new member
	 * @return [type]
	 */
	public function new_member($id = '') {

		if (!hr_has_permission('hr_profile_can_create_hr_records')) {
			app_redirect("forbidden");
		}

		$data['hr_profile_member_add'] = true;
		$title = app_lang('add_new', app_lang('staff_member_lowercase'));

		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();

		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];


		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		$data['title'] = $title;
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();

		$options = array(
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();
		$data['staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['funcData'] = ['staff_id' => isset($staff_id) ? $staff_id : null];
		$data['staff_code'] = $this->hr_profile_model->create_code('staff_code');
		$data['id'] = $id;

		if(is_numeric($id)){
			$options = array(
				"user_type" => "staff",
				"id" => $id,
			);
			$user = $this->Users_model->get_details($options)->getRow();

			$social_options = array(
				"user_id" => $id,
			);
			$data['social_link'] = $this->Social_links_model->get_details($social_options)->getRow();
			$data['member'] = $user;
			$data['id'] = $user->id;
			$data['staff_departments'] = $this->hr_profile_model->get_staff_departments($user->id);

		}


		if ($this->login_user->is_admin) {
			$role_dropdown[] = [
				'id' => 'admin',
				'title' => app_lang('admin'),
			];
		}else{
			if(is_numeric($id) && isset($user) && $user->is_admin){
				$role_dropdown[] = [
					'id' => 'admin',
					'title' => app_lang('admin'),
				];
			}
		}

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		return $this->template->rander('Hr_profile\Views\hr_record/new_member', $data);
	}

	/**
	 * add edit member
	 * @param string $id
	 */
	public function add_edit_member($id = '') {
		if (!hr_has_permission('hr_profile_can_view_global_hr_records') && !hr_has_permission('hr_profile_can_view_own_hr_records') && get_staff_user_id1() != $id) {
			app_redirect("forbidden");
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$id = $this->request->getPost('id');

			if (!is_numeric($id)) {
				if (!hr_has_permission('hr_profile_can_create_hr_records')) {
					app_redirect("forbidden");
				}
				$user_id = $this->hr_profile_model->add_staff($data);

				/*add avartar*/
				if ($_FILES) {
					$profile_image_file = get_array_value($_FILES, "profile_image");
					$image_file_name = get_array_value($profile_image_file, "tmp_name");

					if ($image_file_name) {

						$profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name));

                			//delete old file
						delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));

						$image_data = array("image" => $profile_image);

						$builder = db_connect('default');
						$builder = $builder->table(get_db_prefix().'users');
						$builder->where('id', $user_id);
						$builder->update($image_data);
					}
				}

				if ($user_id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
					app_redirect(('hr_profile/staff_infor'));
				}

			} else {
				if (!hr_has_permission('hr_profile_can_edit_hr_records') && get_staff_user_id1() != $id) {
					app_redirect("forbidden");
				}

				$response = $this->hr_profile_model->update_staff($data, $id);

				/*add avartar*/
				if ($_FILES) {
					$profile_image_file = get_array_value($_FILES, "profile_image");
					$image_file_name = get_array_value($profile_image_file, "tmp_name");

					if ($image_file_name) {

						$profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name));

                			//delete old file
						delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));

						$image_data = array("image" => $profile_image);

						$builder = db_connect('default');
						$builder = $builder->table(get_db_prefix().'users');
						$builder->where('id', $id);
						$builder->update($image_data);
					}
				}

				if ($response) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/staff_infor');
			}
		}

		$title = app_lang('add_new', app_lang('staff_member_lowercase'));

		$data['positions'] = $this->hr_profile_model->get_job_position();
		$data['workplace'] = $this->hr_profile_model->get_workplace();
		$data['base_currency'] = $this->currencies_model->get_base_currency();

		$data['roles_value'] = $this->roles_model->get();
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		$data['title'] = $title;
		$data['contract_type'] = $this->hr_profile_model->get_contracttype();
		$data['staff'] = $this->staff_model->get();

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();
		$data['funcData'] = ['staff_id' => isset($staff_id) ? $staff_id : null];
		$data['staff_code'] = $this->hr_profile_model->create_code('staff_code');

		return $this->template->rander('Hr_profile\Views\hr_record/new_member', $data);
	}

	/**
	 * change staff status: Change status to staff active or inactive
	 * @param  [type] $id
	 * @param  [type] $status
	 * @return [type]
	 */
	public function change_staff_status($id, $status) {
		if (hr_has_permission('hr_profile_can_edit_hr_records')) {
			if ($this->request->getGet()) {

				$builder = db_connect('default');
				$builder = $builder->table(get_db_prefix().'users');
				$builder->where('id', $id);
				$builder->update(['status' => $status]);
				echo json_encode(false);
			}
		}
	}

	/**
	 * hr code exists
	 * @return [type]
	 */
	public function hr_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				// First we need to check if the email is the same
				$memberid = $this->request->getPost('memberid');
				if ($memberid != '') {
					$this->db->where('staffid', $memberid);
					$staff = $this->db->get('tblstaff')->row();
					if ($staff->staff_identifi == $this->request->getPost('staff_identifi')) {
						echo json_encode(true);
						die();
					}
				}

				$this->db->where('staff_identifi', $this->request->getPost('staff_identifi'));
				$total_rows = $this->db->count_all_results('tblstaff');
				if ($total_rows > 0) {
					echo json_encode(false);
				} else {
					echo json_encode(true);
				}
				die();
			}
		}
	}

	/**
	 * view contract modal
	 * @return [type]
	 */
	public function view_contract_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		$this->load->model('staff_model');

		if ($this->request->getPost('slug') === 'view') {
			$contract_id = $this->request->getPost('contract_id');

			$data['contract'] = $this->hr_profile_model->get_contract($contract_id);
			$data['contract_details'] = $this->hr_profile_model->get_contract_detail($contract_id);

			return $this->template->view('Hr_profile\Views\hr_record/contract_modal_view', $data);
		}
	}

	/**
	 * reports
	 * @return [type]
	 */
	public function reports() {
		if (!hr_has_permission('hr_profile_can_view_global_report') && !is_admin()) {
			app_redirect("forbidden");
		}

		$data['position'] = $this->hr_profile_model->get_job_position();

		$department_options = array(
			"deleted" => 0,
		);
		$data['department'] = $this->Team_model->get_details($department_options)->getResultArray();
		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
		);
		$data['staff'] = $this->Users_model->get_details($options)->getResultArray();

		$data['title'] = app_lang('hr_reports');

		return $this->template->rander('Hr_profile\Views\reports/manage_reports', $data);
	}

	/**
	 * report by leave statistics
	 * @return [type]
	 */
	public function report_by_leave_statistics() {
		echo json_encode($this->hr_profile_model->report_by_leave_statistics());
	}

	/**
	 * report by working hours
	 * @return [type]
	 */
	public function report_by_working_hours() {
		echo json_encode($this->hr_profile_model->report_by_working_hours());
	}

	/**
	 * table report the employee quitting
	 * @return [type] 
	 */
	public function table_report_the_employee_quitting() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'reports/tables/table_employee_quitting'), $dataPost);
	}

	/**
	 * table list of employees with salary change
	 * @return [type] 
	 */
	public function table_list_of_employees_with_salary_change() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'reports/tables/table_list_of_employees_with_salary_change'), $dataPost);
	}

	/**
	 * get get base currency name
	 * @return [type]
	 */
	public function get_base_currency_name() {
		$currency = '';

		$this->load->model('currencies_model');
		$base_currency = $this->currencies_model->get_base_currency();

		if ($base_currency) {
			$currency .= $base_currency->name;
		}
		return $currency;
	}

	/**
	 * get chart senior staff
	 * @return [type]
	 */
	public function get_chart_senior_staff($sort_from, $months_report = '', $report_from = '', $report_to = '') {
		if ($this->request->getGet()) {

			$months_report = $months_report;
			if ($months_report == '' || !isset($months_report)) {

				$beginMonth = date('Y-m-d', strtotime('1997-01-01'));
				$endMonth =  date('Y-m-01', strtotime("+100 MONTH"));
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);

			}
			if ($months_report == 'this_month') {

				$beginMonth = date('Y-m-01');
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == '1') {
				$beginMonth = date('Y-m-01', strtotime('first day of last month'));
				$endMonth = date('Y-m-t', strtotime('last day of last month'));
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == 'this_year') {
				$from_year = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_year = date('Y-m-d', strtotime(date('Y-12-31')));
				$staff_list = $this->hr_profile_model->get_staff_by_month($from_year, $to_year);
			}
			if ($months_report == 'last_year') {

				$from_year = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_year = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));

				$staff_list = $this->hr_profile_model->get_staff_by_month($from_year, $to_year);

			}

			if ($months_report == '3') {
				$months_report = 3;
				$months_report--;
				$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);

			}
			if ($months_report == '6') {
				$months_report = 6;
				$months_report--;
				$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == '12') {
				$months_report = 12;
				$months_report--;
				$beginMonth = date('Y-m-01', strtotime("-$months_report MONTH"));
				$endMonth = date('Y-m-t');
				$staff_list = $this->hr_profile_model->get_staff_by_month($beginMonth, $endMonth);
			}
			if ($months_report == 'custom') {
				$from_date = to_sql_date1($report_from);
				$to_date = to_sql_date1($report_to);
				$staff_list = $this->hr_profile_model->get_staff_by_month($from_date, $to_date);

			}

			$list_count_month = array();
			$m1 = 0;
			$m3 = 0;
			$m6 = 0;
			$m9 = 0;
			$m12 = 0;
			$mp12 = 0;

			$p1 = 0;
			$p3 = 0;
			$p6 = 0;
			$p9 = 0;
			$p12 = 0;
			$pp12 = 0;
			$count_total_staff = count($staff_list);

			$current_date = new \DateTime(get_my_local_time('Y-m-d'));

			foreach ($staff_list as $key => $value) {
				if ($value['created_at'] != '') {

					$datecreated = new \DateTime(date("Y-m-d", strtotime($value['created_at'])));

					$total_month = $datecreated->diff($current_date)->m + ($datecreated->diff($current_date)->y * 12) + $datecreated->diff($current_date)->d / 30;

					if ($total_month <= 1) {
						$m1 += 1;
					}
					if (($total_month > 1) && ($total_month <= 3)) {
						$m3 += 1;
					}
					if (($total_month > 3) && ($total_month <= 6)) {
						$m6 += 1;
					}
					if (($total_month > 6) && ($total_month <= 9)) {
						$m9 += 1;
					}
					if (($total_month > 9) && ($total_month <= 12)) {
						$m12 += 1;
					}
					if ($total_month > 12) {
						$mp12 += 1;
					}
				}
			}

			$list_chart = array($m1, $m3, $m6, $m9, $m12, $mp12);
			if ($count_total_staff > 0) {
				foreach ($list_chart as $key => $value) {
					if ($key == 0) {
						$p1 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 1) {
						$p3 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 2) {
						$p6 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 3) {
						$p9 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 4) {
						$p12 = round(($value * 100) / $count_total_staff, 2);
					}
					if ($key == 5) {
						$pp12 = round(($value * 100) / $count_total_staff, 2);
					}
				}
			}

			$list_ratio = array($p1, $p3, $p6, $p9, $p12, $pp12);

			echo json_encode([
				'data' => $list_chart,
				'data_ratio' => $list_ratio,
			]);
		}
	}

	/**
	 * HR is working
	 */
	public function HR_is_working() {
		if ($this->request->getPost()) {

			$months_report = $this->request->getPost('months_filter');

			$from_date = date('Y-m-d', strtotime('1997-01-01'));
			$to_date = date('Y-m-d', strtotime(date('Y-12-31')));

			if ($months_report == 'this_month') {

				$from_date = date('Y-m-01');
				$to_date = date('Y-m-t');
			}
			if ($months_report == '1') {
				$from_date = date('Y-m-01', strtotime('first day of last month'));
				$to_date = date('Y-m-t', strtotime('last day of last month'));

			}
			if ($months_report == 'this_year') {
				$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
			}
			if ($months_report == 'last_year') {
				$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
				$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
			}

			if ($months_report == '3') {
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date = date('Y-m-t');

			}
			if ($months_report == '6') {
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date = date('Y-m-t');

			}
			if ($months_report == '12') {
				$months_report--;
				$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
				$to_date = date('Y-m-t');

			}
			if ($months_report == 'custom') {
				$from_date = to_sql_date1($this->request->getPost('report_from'));
				$to_date = to_sql_date1($this->request->getPost('report_to'));
			}

			echo json_encode($this->hr_profile_model->report_by_staffs());

		}
	}

	/**
	 * qualification department
	 * @return [type]
	 */
	public function qualification_department() {
		if ($this->request->getPost()) {
			if ($this->request->getPost()) {
				$months_report = $this->request->getPost('months_filter');
				$department_filter = $this->request->getPost('department_filter');

				$from_date = date('Y-m-d', strtotime('1997-01-01'));
				$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				if ($months_report == 'this_month') {

					$from_date = date('Y-m-01');
					$to_date = date('Y-m-t');
				}
				if ($months_report == '1') {
					$from_date = date('Y-m-01', strtotime('first day of last month'));
					$to_date = date('Y-m-t', strtotime('last day of last month'));

				}
				if ($months_report == 'this_year') {
					$from_date = date('Y-m-d', strtotime(date('Y-01-01')));
					$to_date = date('Y-m-d', strtotime(date('Y-12-31')));
				}
				if ($months_report == 'last_year') {
					$from_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01')));
					$to_date = date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31')));
				}

				if ($months_report == '3') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '6') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == '12') {
					$months_report--;
					$from_date = date('Y-m-01', strtotime("-$months_report MONTH"));
					$to_date = date('Y-m-t');

				}
				if ($months_report == 'custom') {
					$from_date = to_sql_date1($this->request->getPost('report_from'));
					$to_date = to_sql_date1($this->request->getPost('report_to'));
				}

				$id_department = '';
				if (isset($department_filter)) {
					$id_department = implode(',', $department_filter);
				}
				$circle_mode = false;
				$list_diploma = array(
					"primary_level",
					"intermediate_level",
					"college_level",
					"masters",
					"doctor",
					"bachelor",
					"engineer",
					"university",
					"intermediate_vocational",
					"college_vocational",
					"in-service",
					"high_school",
					"intermediate_level_pro",
				);
				$list_result = array();
				$list_data_department = [];

				$departement_by_literacy = $this->hr_profile_model->count_staff_by_department_literacy();

				if ($id_department == '') {
					$list_department = $this->hr_profile_model->get_department_by_list_id();

					foreach ($list_diploma as $diploma) {
						$list_data_count = [];
						foreach ($list_department as $department) {

							$count = 0;
							if (isset($departement_by_literacy[$department['departmentid']][$diploma])) {
								$count = (int) $departement_by_literacy[$department['departmentid']][$diploma];
							}
							$list_data_count[] = $count;
						}
						array_push($list_result, array('stack' => _l($diploma), 'data' => $list_data_count));
					}
				} else {
					if (count($department_filter) == 1) {
						//one department
						$circle_mode = true;
						$list_department = $this->hr_profile_model->get_department_by_list_id($id_department);
						$list_temp = [];
						$count_total = 0;
						foreach ($list_department as $department) {
							foreach ($list_diploma as $diploma) {
								$count = 0;
								if (isset($departement_by_literacy[$department['departmentid']][$diploma])) {
									$count = (int) $departement_by_literacy[$department['departmentid']][$diploma];
								}

								$count_total += $count;
								$list_temp[] = array('name' => _l($diploma), 'y' => $count);
							}
						}
						foreach ($list_temp as $key => $value) {
							if ($count_total <= 0) {
								$ca_percent = 0;
							} else {
								$ca_percent = ($value['y'] * 100) / $count_total;
							}
							array_push($list_result, array('name' => $value['name'], 'y' => $ca_percent));
						}
					} else {
						// multiple deparment
						$list_department = $this->hr_profile_model->get_department_by_list_id($id_department);
						foreach ($list_diploma as $diploma) {
							$list_data_count = [];
							foreach ($list_department as $department) {
								$count = 0;
								if (isset($departement_by_literacy[$department['departmentid']][$diploma])) {
									$count = (int) $departement_by_literacy[$department['departmentid']][$diploma];
								}
								$list_data_count[] = $count;
							}
							array_push($list_result, array('stack' => _l($diploma), 'data' => $list_data_count));
						}

					}
				}
				if (isset($list_department)) {
					foreach ($list_department as $key => $value) {
						$list_data_department[] = $value['name'];
					}
				}
				echo json_encode([
					'department' => $list_data_department,
					'data_result' => $list_result,
					'circle_mode' => $circle_mode,
				]);
				die;
			}
		}
	}

	/**
	 * report by staffs
	 * @return [type]
	 */
	public function report_by_staffs() {
		echo json_encode($this->hr_profile_model->report_by_staffs());
	}

	/**
	 * import job position excel
	 * @return [type]
	 */
	public function import_job_position_excel() {
		if (!is_admin() && !hr_has_permission('hr_profile_can_create_job_description')) {
			app_redirect("forbidden");
		}

		$user_id = $this->login_user->id;

		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		$filename = '';
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$temp_file_path = get_setting("temp_file_path");
					$tmpDir = getcwd() . '/' . $temp_file_path;
					if (!is_dir($tmpDir)) {
						if (!mkdir($tmpDir, 0777, true)) {
							die('Failed to create file folders.');
						}
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							app_lang('hr_position_code') => 'string',
							app_lang('hr_position_name') => 'string',
							app_lang('hr_job_p_id') => 'string',
							app_lang('hr_job_descriptions') => 'string',
							app_lang('department_id') => 'string',
							app_lang('error') => 'string',
						);
						$rowstyle[] = array('widths' => [10, 20, 30, 40]);

						$writer = new \XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 50], 'fill' => '#f44336',  'font-style'=>'bold', 'color' => '#0a0a0a', 'border'=>'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13]);

						//Reader file
						$xlsx = new \XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['position_code'] = 0;
						$arr_header['position_name'] = 1;
						$arr_header['job_p_id'] = 2;
						$arr_header['job_position_description'] = 3;
						$arr_header['department_id'] = 4;

						$total_rows = 0;
						$total_row_false = 0;

						for ($row = 1; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$value_position_code = isset($data[$row][$arr_header['position_code']]) ? $data[$row][$arr_header['position_code']] : '';
							$value_position_name = isset($data[$row][$arr_header['position_name']]) ? $data[$row][$arr_header['position_name']] : '';
							$value_position_group = isset($data[$row][$arr_header['job_p_id']]) ? $data[$row][$arr_header['job_p_id']] : 0;
							$value_description = isset($data[$row][$arr_header['job_position_description']]) ? $data[$row][$arr_header['job_position_description']] : '';
							$value_department = isset($data[$row][$arr_header['department_id']]) ? $data[$row][$arr_header['department_id']] : '';

							if (is_null($value_position_name) == true || $value_position_name == '') {
								$string_error .= app_lang('hr_position_name') . app_lang('not_yet_entered');
								$flag = 1;
							}

							//check position group exist  (input: id or name)
							$flag_position_group = 0;
							if (is_null($value_position_group) != true && ($value_position_group != '0')) {
								/*case input id*/
								if (is_numeric($value_position_group)) {

									$builder = db_connect('default');
									$builder = $builder->table(get_db_prefix().'hr_job_p');
									$builder->where('job_id', $value_position_group);
									$position_group_id_value = $builder->get()->getRow();

									if ($position_group_id_value) {
										/*get id job_id*/
										$flag_position_group = $value_position_group;
									} else {
										$string_error .= app_lang('hr_job_p_id') . app_lang('does_not_exist');
										$flag2 = 1;
									}

								} else {
									/*case input name*/
									$builder = db_connect('default');
									$builder = $builder->table(get_db_prefix().'hr_job_p');
									$builder->like(db_prefix() . 'hr_job_p.job_name', $value_position_group);

									$position_group_id_value = $builder->get()->getRow();
									if ($position_group_id_value) {
										/*get job_id*/
										$flag_position_group = $position_group_id_value->job_id;
									} else {
										$string_error .= app_lang('hr_job_p_id') . app_lang('does_not_exist');
										$flag2 = 1;
									}
								}

							}

							//check department
							if ($value_department != null && $value_department != '') {
								$department_result = $this->hr_profile_model->check_department_format($value_department);

								if ($department_result['status']) {
									$flag_department = $department_result['result'];
								} else {
									$string_error .= $department_result['result'] . app_lang('department_name') . app_lang('does_not_exist');
									$flag2 = 1;
								}

							}

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_position_code,
									$value_position_name,
									$value_position_group,
									$value_description,
									$value_department,
									$string_error,
								]);
								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								if ($value_position_code == '') {
									$rd['position_code'] = $this->hr_profile_model->create_code('position_code');
								} else {
									$rd['position_code'] = $value_position_code;
								}

								$rd['position_name'] = $value_position_name;
								$rd['job_p_id'] = $flag_position_group;
								$rd['job_position_description'] = $value_description;
								$rd['department_id'] = $flag_department;

								$rows[] = $rd;
								$response = $this->hr_profile_model->add_job_position($rd);
							}

						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_job_position_error_' .$user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));
							$filename = HR_PROFILE_ERROR.$filename;

						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			delete_file_from_directory($newFilePath); //delete temp file
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => $filename,
		]);
	}

	/**
	 * hrm delete bulk action
	 * @return [type]
	 */
	public function hrm_delete_bulk_action() {
		$this->access_only_team_members();

		$total_deleted = 0;

		if ($this->request->getPost()) {

			$ids = $this->request->getPost('ids');
			$rel_type = $this->request->getPost('rel_type');

			/*check permission*/
			switch ($rel_type) {
				case 'hrm_contract':
				if (!hr_has_permission('hr_profile_can_delete_hr_contract') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_staff':
				if (!hr_has_permission('hr_profile_can_delete_hr_records') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_training_library':
				if (!hr_has_permission('hr_profile_can_delete_hr_training') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_job_position':
				if (!hr_has_permission('hr_profile_can_delete_job_description') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_kb-articles':
				if (!has_permission('hr_manage_q_a', '', 'delete') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_reception_staff':
				if (!hr_has_permission('hr_profile_can_delete_onboarding') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_resignation_procedures':
				if (!hr_has_permission('hr_profile_can_delete_layoff_checklists') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				default:
				# code...
				break;
			}

			/*delete data*/
			if ($this->request->getPost('mass_delete')) {
				if (is_array($ids)) {
					foreach ($ids as $id) {

						switch ($rel_type) {
							case 'hrm_contract':
							if ($this->hr_profile_model->delete_contract($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							case 'hrm_staff':
							if ($this->Users_model->delete($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							case 'hrm_training_library':
							if ($this->hr_profile_model->delete_position_training($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							break;

							case 'hrm_job_position':
							if ($this->hr_profile_model->delete_job_position($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							break;

							case 'hrm_kb-articles':
							$this->load->model('knowledge_base_q_a_model');

							if ($this->knowledge_base_q_a_model->delete_article($id)) {
								$total_deleted++;
								break;
							} else {
								break;
							}

							break;

							case 'hrm_reception_staff':

							$this->hr_profile_model->delete_manage_info_reception($id);
							$this->hr_profile_model->delete_setting_training($id);
							$this->hr_profile_model->delete_setting_asset_allocation($id);
							$success = $this->hr_profile_model->delete_reception($id);
							if ($success) {
								$total_deleted++;
							} else {
								break;
							}

							break;

							case 'hrm_resignation_procedures':
							$success = $this->hr_profile_model->delete_procedures_for_quitting_work($id);
							if ($success) {
								$total_deleted++;
							} else {
								break;
							}

							break;

							default:
							# code...
							break;
						}

					}
				}

				/*return result*/
				switch ($rel_type) {
					case 'hrm_contract':
					$this->session->setFlashdata("success_message", app_lang("total_contract_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_staff':
					$this->session->setFlashdata("success_message", app_lang("total_staff_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_training_library':
					$this->session->setFlashdata("success_message", app_lang("total_training_libraries_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_job_position':
					$this->session->setFlashdata("success_message", app_lang("total_job_position_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_kb-articles':
					$this->session->setFlashdata("success_message", app_lang("total_kb_articles_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_reception_staff':
					$this->session->setFlashdata("success_message", app_lang("total_reception_staff_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_resignation_procedures':
					$this->session->setFlashdata("success_message", app_lang("total_layoff_checklist_deleted"). ": " .$total_deleted);
					break;

					default:
					# code...
					break;

				}

			}

		}

	}

	/**
	 * hrm delete bulk action v2
	 * @return [type]
	 * Delete data from ids array, don't use foreach
	 */
	public function hrm_delete_bulk_action_v2() {
		$this->access_only_team_members();

		$total_deleted = 0;

		if ($this->request->getPost()) {

			$ids = $this->request->getPost('ids');
			$rel_type = $this->request->getPost('rel_type');

			/*check permission*/
			switch ($rel_type) {

				case 'hrm_training_program':
				if (!hr_has_permission('hr_profile_can_delete_hr_training') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_job':
				if (!hr_has_permission('hr_profile_can_delete_job_description') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				case 'hrm_dependent_person':
				if (!hr_has_permission('hr_profile_can_delete_dependent_persons') && !is_admin()) {
					app_redirect("forbidden");
				}
				break;

				default:
				# code...
				break;
			}

			/*delete data*/
			$transfer_data_to = get_staff_user_id1();
			if ($this->request->getPost('mass_delete')) {
				if (is_array($ids)) {

					switch ($rel_type) {

						case 'hrm_training_program':
						$sql_where = " training_process_id  IN ( '" . implode("', '", $ids) . "' ) ";
						$builder = db_connect('default');
						$builder = $builder->table(get_db_prefix().'hr_jp_interview_training');
						$builder->where($sql_where);
						$builder->delete();
						$total_deleted = count($ids);
						break;

						case 'hrm_job':
						$sql_where = " job_id  IN ( '" . implode("', '", $ids) . "' ) ";
						$builder = db_connect('default');
						$builder = $builder->table(get_db_prefix().'hr_job_p');
						$builder->where($sql_where);
						$builder->delete();
						$total_deleted = count($ids);
						break;

						case 'hrm_dependent_person':
						$sql_where = " id  IN ( '" . implode("', '", $ids) . "' ) ";
						$builder = db_connect('default');
						$builder = $builder->table(get_db_prefix().'hr_dependent_person');
						$builder->where($sql_where);
						$builder->delete();
						$total_deleted = count($ids);
						break;

						default:
						# code...
						break;
					}

				}

				/*return result*/
				switch ($rel_type) {

					case 'hrm_training_program':
					$this->session->setFlashdata("success_message", app_lang("total_training_program_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_job':
					$this->session->setFlashdata("success_message", app_lang("total_job_deleted"). ": " .$total_deleted);
					break;

					case 'hrm_dependent_person':
					$this->session->setFlashdata("success_message", app_lang("total_dependent_person_deleted"). ": " .$total_deleted);
					break;

					default:
					# code...
					break;

				}

			}

		}

	}

	/**
	 * import dependent person excel
	 * @return [type]
	 */
	public function import_dependent_person_excel() {
		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		$filename = '';
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

					$tmpDir = TEMP_FOLDER . '/' . time() . uniqid() . '/';

					if (!file_exists(TEMP_FOLDER)) {
						mkdir(TEMP_FOLDER, 0755);
					}

					if (!file_exists($tmpDir)) {
						mkdir($tmpDir, 0755);
					}

					// Setup our new file path
					$newFilePath = $tmpDir . $_FILES['file_csv']['name'];

					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						//Writer file
						$writer_header = array(
							app_lang('hr_hr_code') => 'string',
							app_lang('hr_dependent_name') => 'string',
							app_lang('hr_hr_relationship') => 'string',
							app_lang('hr_dependent_bir') => 'string',
							app_lang('hr_dependent_iden') => 'string',
							app_lang('hr_reason_label') => 'string',
							app_lang('hr_start_month') => 'string',
							app_lang('hr_end_month') => 'string',
							app_lang('hr_status_label') => 'string',
							app_lang('error') => 'string',
						);
						$rowstyle[] = array('widths' => [10, 20, 30, 40]);

						$writer = new \XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new \XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['staffid'] = 0;
						$arr_header['dependent_name'] = 1;
						$arr_header['relationship'] = 2;
						$arr_header['dependent_bir'] = 3;
						$arr_header['dependent_iden'] = 4;
						$arr_header['reason'] = 5;
						$arr_header['start_month'] = 6;
						$arr_header['end_month'] = 7;
						$arr_header['status'] = 8;

						$total_rows = 0;
						$total_row_false = 0;

						for ($row = 1; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$value_staffid = isset($data[$row][$arr_header['staffid']]) ? $data[$row][$arr_header['staffid']] : '';
							$value_dependent_name = isset($data[$row][$arr_header['dependent_name']]) ? $data[$row][$arr_header['dependent_name']] : '';
							$value_relationship = isset($data[$row][$arr_header['relationship']]) ? $data[$row][$arr_header['relationship']] : '';
							$value_dependent_bir = isset($data[$row][$arr_header['dependent_bir']]) ? $data[$row][$arr_header['dependent_bir']] : '';
							$value_dependent_iden = isset($data[$row][$arr_header['dependent_iden']]) ? $data[$row][$arr_header['dependent_iden']] : '';
							$value_reason = isset($data[$row][$arr_header['reason']]) ? $data[$row][$arr_header['reason']] : '';
							$value_start_month = isset($data[$row][$arr_header['start_month']]) ? $data[$row][$arr_header['start_month']] : '';
							$value_end_month = isset($data[$row][$arr_header['end_month']]) ? $data[$row][$arr_header['end_month']] : '';
							$value_status = isset($data[$row][$arr_header['status']]) ? $data[$row][$arr_header['status']] : '';

							/*check null*/
							if (is_null($value_staffid) == true) {
								$string_error .= app_lang('hr_hr_code') . app_lang('not_yet_entered');
								$flag = 1;
							}

							$flag_staff_id = 0;
							//check hr_code exist
							if (is_null($value_staffid) != true) {
								$this->db->where('staff_identifi', $value_staffid);
								$hrcode = $this->db->get(db_prefix() . 'staff')->row();
								if ($hrcode) {
									$flag_staff_id = $hrcode->staffid;
								} else {
									$string_error .= app_lang('hr_hr_code') . app_lang('does_not_exist');
									$flag2 = 1;
								}

							}

							//check start_time
							if (is_null($value_dependent_bir) != true && $value_dependent_bir != '') {

								if (is_null($value_dependent_bir) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_dependent_bir, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= app_lang('hr_dependent_bir') . app_lang('invalid');
									}
								}
							}

							//check start_time
							if (is_null($value_start_month) != true && $value_start_month != '') {

								if (is_null($value_start_month) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_start_month, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= app_lang('hr_start_month') . app_lang('invalid');
									}
								}
							}

							if (is_null($value_end_month) != true && $value_end_month != '') {

								if (is_null($value_end_month) != true) {

									if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", trim($value_end_month, " "))) {
										$test = true;

									} else {
										$flag2 = 1;
										$string_error .= app_lang('hr_end_month') . app_lang('invalid');
									}
								}
							}

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [
									$value_staffid,
									$value_dependent_name,
									$value_relationship,
									$value_dependent_bir,
									$value_dependent_iden,
									$value_reason,
									$value_start_month,
									$value_end_month,
									$value_status,
									$string_error,
								]);

								// $numRow++;
								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								if (is_numeric($value_status) && ($value_status == '2')) {
									/*reject*/
									$rd['status'] = 2;
								} else {
									/*approval*/
									$rd['status'] = 1;
								}

								$rd['staffid'] = $flag_staff_id;
								$rd['dependent_name'] = $value_dependent_name;
								$rd['relationship'] = $value_relationship;
								$rd['dependent_bir'] = $value_dependent_bir;
								$rd['dependent_iden'] = $value_dependent_iden;
								$rd['reason'] = $value_reason;
								$rd['start_month'] = $value_start_month;
								$rd['end_month'] = $value_end_month;

								$rows[] = $rd;
								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {

							$this->db->insert_batch(db_prefix() . 'hr_dependent_person', $arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_dependent_person_error_' . get_staff_user_id1() . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PROFILE_ERROR . $filename, $filename));
						}

					}
				}
			}
		}

		if (file_exists($newFilePath)) {
			@unlink($newFilePath);
		}

		echo json_encode([
			'message' => $message,
			'total_row_success' => $total_row_success,
			'total_row_false' => $total_row_false,
			'total_rows' => $total_rows,
			'site_url' => site_url(),
			'staff_id' => get_staff_user_id1(),
			'filename' => HR_PROFILE_ERROR . $filename,
		]);
	}

	/**
	 * reset_datas
	 * @return [type] 
	 */
	public function reset_datas() {
		return $this->template->rander("Hr_profile\Views\includes\\reset_data", []);
	}

	/**
	 * reset data
	 * @return [type]
	 */
	public function reset_data() {

		if (!is_admin()) {
			app_redirect("forbidden");
		}

		$builder = db_connect('default');

		//delete Onboarding process
		$hr_rec_transfer_records = $builder->table(get_db_prefix().'hr_rec_transfer_records');
		$hr_rec_transfer_records->truncate();

		$hr_group_checklist_allocation = $builder->table(get_db_prefix().'hr_group_checklist_allocation');
		$hr_group_checklist_allocation->truncate();

		$hr_allocation_asset = $builder->table(get_db_prefix().'hr_allocation_asset');
		$hr_allocation_asset->truncate();

		$hr_training_allocation = $builder->table(get_db_prefix().'hr_training_allocation');
		$hr_training_allocation->truncate();

		//delete Training
		$hr_jp_interview_training = $builder->table(get_db_prefix().'hr_jp_interview_training');
		$hr_jp_interview_training->truncate();

		$hr_position_training = $builder->table(get_db_prefix().'hr_position_training');
		$hr_position_training->truncate();

		$hr_position_training_question_form = $builder->table(get_db_prefix().'hr_position_training_question_form');
		$hr_position_training_question_form->truncate();

		$hr_p_t_form_question_box = $builder->table(get_db_prefix().'hr_p_t_form_question_box');
		$hr_p_t_form_question_box->truncate();

		$hr_p_t_form_question_box_description = $builder->table(get_db_prefix().'hr_p_t_form_question_box_description');
		$hr_p_t_form_question_box_description->truncate();

		$hr_p_t_form_results = $builder->table(get_db_prefix().'hr_p_t_form_results');
		$hr_p_t_form_results->truncate();

		$hr_p_t_surveyresultsets = $builder->table(get_db_prefix().'hr_p_t_surveyresultsets');
		$hr_p_t_surveyresultsets->truncate();

		//delete contracs, file type "hr_contract"
		$hr_staff_contract_detail = $builder->table(get_db_prefix().'hr_staff_contract_detail');
		$hr_staff_contract_detail->truncate();

		$hr_staff_contract = $builder->table(get_db_prefix().'hr_staff_contract');
		$hr_staff_contract->truncate();

		//delete dependent persons
		$hr_dependent_person = $builder->table(get_db_prefix().'hr_dependent_person');
		$hr_dependent_person->truncate();

		//delete Resignation procedures
		$hr_list_staff_quitting_work = $builder->table(get_db_prefix().'hr_list_staff_quitting_work');
		$hr_list_staff_quitting_work->truncate();

		$hr_procedure_retire_of_staff = $builder->table(get_db_prefix().'hr_procedure_retire_of_staff');
		$hr_procedure_retire_of_staff->truncate();

		//delete Q&A

		$hr_views_tracking = $builder->table(get_db_prefix().'hr_views_tracking');
		$hr_views_tracking->truncate();

		//delete sub folder contract
		foreach (glob(HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (is_dir($file)) {
				delete_dir(HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER . $filename);
			}
		}

		//delete sub folder Q_A_ATTACHMENTS
		foreach (glob(HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (is_dir($file)) {
				delete_dir(HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER . $filename);
			}
		}

		//delete file error response
		foreach (glob('plugins/Hr_profile/Uploads/file_error_response/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (is_dir($file)) {
				delete_dir('plugins/Hr_profile/Uploads/file_error_response/' . $filename);
			}
		}

		//delete file
		$files = $builder->table(get_db_prefix().'files');
		
		$files->where('rel_type', 'staff_contract');
		$files->orWhere('rel_type', 'kb_article_files');
		$files->delete();

		$this->session->setFlashdata("success_message", app_lang("reset_data_successful"));
		app_redirect('hr_profile/reset_datas');
	}

	/**
	 * table training program
	 * @return [type]
	 */
	public function table_training_program() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'training/job_position_manage/training_programs_table'), $dataPost);
	}

	/**
	 * table training result
	 * @return [type]
	 */
	public function table_training_result() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'training/job_position_manage/training_result_table'), $dataPost);
	}

	/**
	 * training table
	 * @return [type]
	 */
	public function training_libraries_table() {
		$dataPost = $this->request->getPost();
		$this->hr_profile_model->get_table_data(module_views_path('Hr_profile', 'training/job_position_manage/training_table'), $dataPost);
	}

	/**
	 * type of trainings
	 * @return [type] 
	 */
	public function type_of_trainings() {
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
		return $this->template->rander("Hr_profile\Views\includes\\type_of_training", $data);
	}
	
	/**
	 * list type of training data
	 * @return [type] 
	 */
	public function list_type_of_training_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_type_of_training();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_type_of_training_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make type of training row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_type_of_training_row($data) {

		$options = '';
		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){
			$options .= modal_anchor(get_uri("hr_profile/type_of_training_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('hr_edit_type_of_training'), "data-post-id" => $data['id']));
		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("hr_profile/delete_type_of_training/".$data['id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['name']),
			$options
		);
	}

	/**
	 * type_of_training_modal_form 
	 * @return [type] [
	 */
	public function type_of_training_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$type_of_training_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['type_of_training_data'] = $this->hr_profile_model->get_type_of_training($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		return $this->template->view('Hr_profile\Views\includes\modal_forms\type_of_training_modal', $data);
	}


	/**
	 * type of training
	 * @param  string $id
	 * @return [type]
	 */
	public function type_of_training($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();

			if (!is_numeric($id)) {
				$id = $this->hr_profile_model->add_type_of_training($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('hr_profile/type_of_trainings');
			} else {

				$success = $this->hr_profile_model->update_type_of_training($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('hr_profile/type_of_trainings');
			}
			die;
		}
	}


	/**
	 * delete type of training
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_type_of_training($id) {
		if (!$id) {
			app_redirect('hr_profile/contract_types');
		}

		if (!hr_has_permission('hr_profile_can_delete_setting') && !is_admin()) {
			app_redirect("forbidden");
		}

		$response = $this->hr_profile_model->delete_type_of_training($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("warning" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("warning" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * reception staffs
	 * @return [type] 
	 */
	public function reception_staffs() {
		$data['type_of_trainings'] = $this->hr_profile_model->get_type_of_training();
		$data['list_reception_staff_transfer'] = $this->hr_profile_model->get_setting_transfer_records();
		$data['list_reception_staff_asset'] = $this->hr_profile_model->get_setting_asset_allocation();
		$data['setting_training'] = $this->hr_profile_model->get_setting_training();

		$data['group_checklist'] = $this->hr_profile_model->group_checklist();
		$data['max_checklist'] = $this->hr_profile_model->count_max_checklist();

		return $this->template->rander("Hr_profile\Views\includes\\reception_staff", $data);
	}

	/**
	 * get training program by type
	 * @return [type]
	 */
	public function get_training_program_by_type() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if ($data['training_type'] == '') {
				$training_type = 0;
			} else {
				$training_type = $data['training_type'];
			}

			$training_program_option = '';
			$first_id = '';
			$list_staff = $this->hr_profile_model->get_staff_info_id($data['staff_id']);
			if ($list_staff) {
				$get_list_training_program = $this->hr_profile_model->get_list_training_program($list_staff->job_position, $training_type);
				$training_program_option = $get_list_training_program['options'];
				$first_id = $get_list_training_program['first_id'];
			}

			echo json_encode([
				'training_program_html' => $training_program_option,
				'first_id' => $first_id,
			]);
		}
	}

	/**
	 * view training program
	 * @param  string $id
	 * @return [type]
	 */
	public function view_training_program($id = '') {
		if (!hr_has_permission('hr_profile_can_view_global_hr_training') && !hr_has_permission('hr_profile_can_view_own_hr_training')) {
			app_redirect("forbidden");
		}

		//load deparment by manager
		if (!is_admin() && !hr_has_permission('hr_profile_can_view_global_hr_training')) {
			//View own
			$array_staff = $this->hr_profile_model->get_staff_by_manager();
		}

		$data['title'] = app_lang('view_training_program');
		$data['training_program'] = $this->hr_profile_model->get_job_position_training_de($id);

		if (!$data['training_program']) {
			blank_page('Training program Not Found', 'danger');
		}

		$data['training_results'] = $this->hr_profile_model->get_training_result_by_training_program($id);
		if (isset($array_staff)) {
			foreach ($data['training_results'] as $key => $value) {
				if (!in_array($value['staff_id'], $array_staff)) {
					unset($data['training_results'][$key]);
				}
			}
		}

		return $this->template->rander('Hr_profile\Views/training/view_training_program', $data);
	}

	/* Get role permission for specific role id */
	public function hr_role_changed($id) {
		echo json_encode($this->roles_model->get($id)->permissions);
	}

	/**
	 * create staff excel file
	 * @return [type]
	 */
	public function create_staff_sample_file() {
		$user_id = $this->login_user->id;

		$data = $this->request->getPost();

		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(HR_PROFILE_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

		$this->delete_error_file_day_before('1', HR_PROFILE_CREATE_EMPLOYEES_SAMPLE);

		if (isset($data['sample_file'])) {

			$staffs = [];
		} else {

			//get list staff by id
			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'users');
			$builder->where('id  IN (' . implode(",", $data['ids']) . ') ');
			$staffs = $builder->get()->getResultArray();
		}

		$header_key = [
			'id',
			'staff_identifi',
			'first_name',
			'last_name',
			'gender',
			'dob',
			'email',
			'phone',
			'workplace',
			'status_work',
			'job_position',
			'team_manage',
			'role_id',
			'literacy',
			'hourly_rate',
			'department',
			'password',
			'home_town', //text
			'marital_status',
			'address',
			'nation',
			'birthplace',
			'religion',
			'identification',
			'days_for_identity',
			'place_of_issue',
			'resident',
			'account_number',
			'name_account',
			'issue_bank',
			'Personal_tax_code',
		];

		$header_label = [
			'id',
			'hr_staff_code', 
			'hr_firstname', 
			'hr_lastname', 
			'hr_sex',
			'hr_hr_birthday',
			'email', 
			'phone',
			'hr_hr_workplace',
			'hr_status_work', 
			'hr_hr_job_position', 
			'hr_team_manage',
			'role',
			'hr_hr_literacy',
			'staff_hourly_rate',
			'team',
			'password',
			'hr_hr_home_town', //text
			'hr_hr_marital_status',
			'hr_current_address',
			'hr_hr_nation',
			'hr_hr_birthplace',
			'hr_hr_religion',
			'hr_citizen_identification',
			'hr_license_date',
			'hr_hr_place_of_issue',
			'hr_hr_resident',
			'hr_bank_account_number',
			'hr_bank_account_name',
			'hr_bank_name',
			'hr_Personal_tax_code',
		];

		//Writer file
		//create header value
		$writer_header = [];
		$widths = [];

		$widths[] = 30;

		foreach ($header_label as $header_value) {
			$writer_header[_l($header_value)] = 'string';
			$widths[] = 30;
		}

		$writer = new \XLSXWriter();

		//orange: do not update
		$col_style1 = [0, 1];
		$style1 = ['widths' => $widths, 'fill' => '#fc2d42', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		//red: required
		$col_style2 = [2, 3, 6, 9, 10];
		$style2 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		//otherwise blue: can be update

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1, $col_style2, $style2);

		$row_style1 = array('fill' => '#F8CBAD', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');
		$row_style2 = array('fill' => '#FCE4D6', 'height' => 25, 'border' => 'left,right,top,bottom', 'border-color' => '#FFFFFF', 'font-size' => 12, 'font' => 'Calibri', 'color' => '#000000');

		//job position data
		$job_position_data = [];
		$job_positions = $this->hr_profile_model->get_job_position();

		foreach ($job_positions as $key => $job_position) {
			$job_position_data[$job_position['position_id']] = $job_position;
		}

		//direct manager
		$staff_data = [];
		$list_staffs = $this->hr_profile_model->get_staff();
		foreach ($list_staffs as $key => $list_staff) {
			$staff_data[$list_staff['id']] = $list_staff;
		}

		//get role data
		$role_data = [];
		$role_options = array(
			"deleted" => 0,
		);
		$list_roles = $this->Roles_model->get_details($role_options)->getResultArray();

		foreach ($list_roles as $key => $list_role) {
			$role_data[$list_role['id']] = $list_role;
		}

		//get workplace data
		$workplace_data = [];
		$list_workplaces = $this->hr_profile_model->get_workplace();

		foreach ($list_workplaces as $key => $list_workplace) {
			$workplace_data[$list_workplace['id']] = $list_workplace;
		}

		//write the next row (row2)
		$writer->writeSheetRow('Sheet1', $header_key);

		foreach ($staffs as $staff_key => $staff_value) {

			$arr_department = $this->hr_profile_model->get_staff_departments($staff_value['id'], true);

			$list_department = '';
			if (count($arr_department) > 0) {

				foreach ($arr_department as $key => $department) {
					$department_value = hr_profile_get_department_name($department);

					if ($department_value) {
						if (strlen($list_department) != 0) {
							$list_department .= ';' . $department_value->title;
						} else {
							$list_department .= $department_value->title;
						}
					}
				}
			}

			$temp = [];

			foreach ($header_key as $_key) {
				if ($_key == 'password') {
					$temp[] = '';
				} elseif ($_key == 'department') {
					$temp[] = $list_department;

				} elseif ($_key == 'job_position') {
					$temp[] = isset($job_position_data[$staff_value['job_position']]) ? $job_position_data[$staff_value['job_position']]['position_code'] : '';

				} elseif ($_key == 'team_manage') {
					$temp[] = isset($staff_data[$staff_value['team_manage']]) ? $staff_data[$staff_value['team_manage']]['staff_identifi'] : '';

				} elseif ($_key == 'role_id') {
					$temp[] = isset($role_data[$staff_value['role_id']]) ? $role_data[$staff_value['role_id']]['title'] : '';

				} elseif ($_key == 'workplace') {
					$temp[] = isset($workplace_data[$staff_value['workplace']]) ? $workplace_data[$staff_value['workplace']]['name'] : '';

				} else {
					$temp[] = isset($staff_value[$_key]) ? $staff_value[$_key] : '';
				}
			}

			if (($staff_key % 2) == 0) {
				$writer->writeSheetRow('Sheet1', $temp, $row_style1);
			} else {
				$writer->writeSheetRow('Sheet1', $temp, $row_style2);
			}

		}

		$filename = 'employees_sample_file' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PROFILE_CREATE_EMPLOYEES_SAMPLE . $filename, $filename));

		echo json_encode([
			'success' => true,
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => HR_PROFILE_CREATE_EMPLOYEES_SAMPLE . $filename,
		]);

	}

	/**
	 * view pdf
	 * @return [type] 
	 */
	public function view_pdf() {
		$data = [];
		return $this->template->view('Hr_profile\Views\hr_profile/contracts/view_contract_pdf', $data);
	}

	/**
	 * save contract data
	 * @return [type]
	 */
	public function save_hr_contract_data() {
		if (!hr_has_permission('hr_profile_can_edit_hr_contract')) {
			header('HTTP/1.0 400 Bad error');
			echo json_encode([
				'success' => false,
				'message' => app_lang('access_denied'),
			]);
			die;
		}

		$success = false;
		$message = '';

		$this->db->where('id_contract', $this->request->getPost('contract_id'));
		$this->db->update(db_prefix() . 'hr_staff_contract', [
			'content' => html_purify($this->request->getPost('content', false)),
		]);

		$success = $this->db->affected_rows() > 0;
		$message = app_lang('updated_successfully', app_lang('contract'));

		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
	}

	/**
	 * hr clear signature
	 * @param  [type] $id
	 * @return [type]
	 */
	public function hr_clear_signature($id) {
		if (hr_has_permission('hr_profile_can_delete_hr_contract')) {
			$this->hr_profile_model->contract_clear_signature($id);
		}

		app_redirect(('hr_profile/contracts#' . $id));
	}

	/**
	 * contract pdf
	 * @param  [type] $id
	 * @return [type]
	 */
	public function contract_pdf($id) {
		if (!hr_has_permission('hr_profile_can_view_global_hr_contract') && !hr_has_permission('hr_profile_can_view_own_hr_contract')) {
			app_redirect("forbidden");
		}

		if (!$id) {
			app_redirect(('hr_profile/hrm_contract'));
		}

		$pdf = new Pdf();
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetCellPadding(1.5);
		$pdf->setImageScale(1.42);
		$pdf->AddPage();
		$pdf->SetFontSize(9);

		
		$contract = $this->hr_profile_model->get_contract($id);
		$html = $this->hr_profile_model->hr_get_staff_contract_pdf_only_for_pdf($id)->content;

		$type = 'D';
		$send = '';

		if ($this->request->getGet('output_type')) {
			$type = $this->request->getGet('output_type');
		}

		if ($this->request->getGet('print')) {
			$type = 'I';
		}

		if ($type != "html") {
			$pdf->writeHTML($html, true, false, true, false, '');
		}

		if($send != ''){
			$type = 'send_email';
		}

		$pdf_file_name = $contract->contract_code.'.pdf';

		if ($type === "D") {
			$pdf->Output($pdf_file_name, "D");
		} else if ($type === "send_email") {
			$temp_download_path = getcwd() . "/" . get_setting("temp_file_path") . $pdf_file_name;
			$pdf->Output($temp_download_path, "F");
			return $temp_download_path;
		} else if ($type === "I") {
			$pdf->SetTitle($pdf_file_name);
			$pdf->Output($pdf_file_name, "I");
			exit;
		} 


	}

	/**
	 * contract sign
	 * @param  [type] $id
	 * @return [type]
	 */
	public function contract_sign($id) {
		$contract = $this->hr_profile_model->hr_get_staff_contract_pdf($id);

		if (!$contract) {
            $this->session->setFlashdata("warning_message", app_lang("No_employee_information_related_to_the_contract_was_found"));
			app_redirect('hr_profile/view_staff_contract/'.$id);
		}

		if ($this->request->getPost()) {

			if ($this->request->getPost('sign_by') == 'company') {
				hr_profile_process_digital_signature_image($this->request->getPost('signature', false), HR_PROFILE_CONTRACT_SIGN . $id);

				$signature = null;
				if (isset($GLOBALS['processed_digital_signature'])) {
					$signature = $GLOBALS['processed_digital_signature'];
					unset($GLOBALS['processed_digital_signature']);
				}

				$builder = db_connect('default');
				$builder = $builder->table(get_db_prefix().'hr_staff_contract');
				$builder->where('id_contract', $id);
				$builder->update(['signature' => $get_acceptance_info_array['signature'], 'signer' => get_staff_user_id1(), 'sign_day' => get_my_local_time('Y-m-d')]);
			} else {

				hr_profile_process_digital_signature_image($this->request->getPost('signature', false), HR_PROFILE_CONTRACT_SIGN . $id);
				$get_acceptance_info_array = get_acceptance_info_array();

				$this->db->where('id_contract', $id);
				$this->db->update(db_prefix() . 'hr_staff_contract', ['staff_signature' => $get_acceptance_info_array['signature'], 'staff_sign_day' => get_my_local_time('Y-m-d')]);

			}

			// Notify contract creator that customer signed the contract

			set_alert('success', app_lang('document_signed_successfully'));
			redirect($_SERVER['HTTP_REFERER']);

		}

		$data['title'] = $contract->contract_code;

		$data['contract'] = $contract;
		$data['bodyclass'] = 'contract contract-view';

		$data['identity_confirmation_enabled'] = true;
		$data['bodyclass'] .= ' identity-confirmation';

		return $this->template->rander('Hr_profile\Views/contracts/contracthtml', $data);
	}

	public function staff_contract_sign($id) {
		$data = $this->request->getPost();

		$success = false;
		$code = '';
		$signature = '';

		if (isset($data['signature'])) {
			$signature = $data['signature'];
			unset($data['signature']);
		}

		$path = HR_PROFILE_CONTRACT_SIGN . $id;

		if ($this->request->getPost('sign_by') == 'company') {
			hr_profile_process_digital_signature_image($signature, $path, 'signature');

			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'hr_staff_contract');
			$builder->where('id_contract', $id);
			$affectedrows = $builder->update(['signature' => $signature, 'signer' => get_staff_user_id1(), 'sign_day' => get_my_local_time('Y-m-d')]);
			if($affectedrows > 0){
				$success = true;
			}

		}else{

			hr_profile_process_digital_signature_image($signature, $path, 'staff_signature');
			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'hr_staff_contract');
			$builder->where('id_contract', $id);
			$affectedrows = $builder->update(['staff_signature' => $signature, 'staff_sign_day' => get_my_local_time('Y-m-d')]);
			if($affectedrows > 0){
				$success = true;
			}
		}

		echo json_encode([
			'success' => $success,
			'message' => app_lang('sign_successfully'),
		]);
		die();
	}


	/**
	 * workplaces
	 * @return [type] 
	 */
	public function contract_templates() {
		$data['contract_templates'] = $this->hr_profile_model->get_contract_template();
		return $this->template->rander("Hr_profile\Views\includes\contract_template", $data);
	}

	/**
	 * list contract_template data
	 * @return [type] 
	 */
	public function list_contract_template_data() {
		$this->access_only_team_members();

		$list_data = $this->hr_profile_model->get_contract_template();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_contract_template_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make contract_template row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_contract_template_row($data) {

		$options = '';
		if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')){

			$options .= '<a href="'.get_uri('hr_profile/contract_template/'.$data['id']).'"  class="btn btn-default btn-icon"><i data-feather="edit" class="icon-16"></i></a>';

		}
		if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("hr_profile/delete_contract_template_/".$data['id']), "data-action" => "delete-confirmation"));
		}

		$jobpositionOutput = '';
		$job_positions       = explode(",", $data['job_position']);


		$jobpositionOutput1 = '';
		$list_jobposition = '';
		$exportjob_positions = '';
		if($job_positions != null){
			foreach ($job_positions as $key => $position_id) {
				$position_name   = hr_profile_get_job_position_name($position_id);

				$list_jobposition .= '<li class="text-success mbot10 mtop"><a href="#" class="dropdown-item text-align-left" >'.$position_name. '</a></li>';

			}
		}

		if($job_positions != null){
			$jobpositionOutput .= '<span class="avatar bg-danger brround avatar-none">+'. (count($job_positions) ) .'</span>';
		}

		$jobpositionOutput1 = '<div class="task-info task-watched task-info-watched">
		<h5>
		<div class="btn-group">
		<span class=" dropdown task-single-menu task-menu-watched">
		<div class="avatar-list avatar-list-stacked dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true">'.$jobpositionOutput.'</div>
		<ul class="dropdown-menu" role="menu">
		'.$list_jobposition.'
		</ul>
		</span>
		</div>
		</h5>
		</div>';

		return array(
			nl2br($data['name']),
			$jobpositionOutput1,
			$options
		);
	}


	/**
	 * contract template
	 * @param  string $id
	 * @return [type]
	 */
	public function contract_template($id = '') {

		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			$id = $this->request->getPost('id');
			$data['content'] = decode_ajax_post_data($this->request->getPost('content'));

			if (!is_numeric($id)) {
				$id = $this->hr_profile_model->add_contract_template($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}

				app_redirect('hr_profile/contract_templates');
			} else {
				if(isset($data['id'])){
					unset($data['id']);
				}
				$success = $this->hr_profile_model->update_contract_template($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}

				app_redirect('hr_profile/contract_templates');
			}
		}
		$data = [];

		if ($id == '') {
			//add
			$title = app_lang('add_contract_template');
			$data['title'] = $title;

			/*sample contract template*/
			$model_info = new \stdClass;
			$model_info->id = 0;
			$model_info->template_name = 'sample_contract';
			$model_info->default_message = $this->template->view('Hr_profile\Views\includes\modal_forms\sample_contract_html');
			$model_info->content = '';
			$model_info->delete = 0;

		} else {
			//update
			$title = app_lang('update_contract_template');
			$data['title'] = $title;
			$get_contract_template = $this->hr_profile_model->get_contract_template($id);
			$data['contract_template'] = $get_contract_template; 
			$model_info = $get_contract_template;
			$model_info->default_message = $this->template->view('Hr_profile\Views\includes\modal_forms\sample_contract_html');

		}

		$data['job_positions'] = $this->hr_profile_model->get_job_position();

		$view_data['model_info'] = $model_info;
		$view_data['column_name'] = 'content';
		$variables = staff_contract_variables();
		$view_data['variables'] = $variables ? $variables : array();
		$view_data['unsupported_title_variables'] = json_encode(array("SIGNATURE", "TASKS_LIST", "TICKET_CONTENT", "MESSAGE_CONTENT", "EVENT_DETAILS"));

		$data['sample_contract'] = $this->template->view('Hr_profile\Views\includes\modal_forms\contract_template_form', $view_data);

		return $this->template->rander('Hr_profile\Views/includes/contract_template_detail', $data);
	}

	/**
	 * delete contract template
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_contract_template_($id) {
		if (!$id) {
			app_redirect('hr_profile/contract_templates');
		}
		$response = $this->hr_profile_model->delete_contract_template($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('hr_is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * confirm delete modal form
	 * @return [type] 
	 */
	public function confirm_delete_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));

		if($this->request->getPost('id')){
			$data['function'] = $this->request->getPost('function');
			$data['id'] = $this->request->getPost('id');
			$data['id2'] = $this->request->getPost('id2');
			return $this->template->view('Hr_profile\Views\includes\confirm_delete_modal_form', $data);
		}
	}

	function temp_upload_file() {
		$this->access_only_team_members();
		upload_file_to_temp();
	}

	/* check valid file for project */

	function validate_position_file() {
		return validate_post_file($this->request->getPost("file_name"));
	}

    /**
     * staff email exists
     * @return [type] 
     */
    public function staff_email_exists()
    {
    	if(is_numeric($this->request->getPost('id'))){
    		/*edit*/
    		$builder = db_connect('default');
    		$builder = $builder->table(get_db_prefix().'users');
    		$builder->where('id', $this->request->getPost('id'));
    		$user = $builder->get()->getRow();

    		if($user->email == $this->request->getPost('email')){
    			echo json_encode(array("success" => true, 'message' => app_lang('email not exist')));
    			die;
    		}else{
    			$builder = db_connect('default');
    			$builder = $builder->table(get_db_prefix().'users');
    			$builder->where('email', $this->request->getPost('email'));
    			$users = $builder->get()->getResultArray();
    			if(count($users) > 0){

    				echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
    				die;
    			}else{
    				echo json_encode(array("success" => true, 'message' => app_lang('email not exist')));
    				die;
    			}
    		}

    	}else{

    		if ($this->Users_model->is_email_exists($this->request->getPost('email'))) {
    			echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
    			die;
    		}else{
    			echo json_encode(array("success" => true, 'message' => app_lang('email not exist')));
    			die;
    		}
    	}
    }

    public function hr_create_notification($data = array()) {

		ini_set('max_execution_time', 300); //300 seconds 
		//validate notification request

		$event = '';
		$event = get_array_value($data, "event");

		$user_id = get_array_value($data, "user_id");
		$activity_log_id = get_array_value($data, "activity_log_id");

		$options = array(
			"project_id" => get_array_value($data, "project_id"),
			"task_id" => get_array_value($data, "task_id"),
			"project_comment_id" => get_array_value($data, "project_comment_id"),
			"ticket_id" => get_array_value($data, "ticket_id"),
			"ticket_comment_id" => get_array_value($data, "ticket_comment_id"),
			"project_file_id" => get_array_value($data, "project_file_id"),
			"leave_id" => get_array_value($data, "leave_id"),
			"post_id" => get_array_value($data, "post_id"),
			"to_user_id" => get_array_value($data, "to_user_id"),
			"activity_log_id" => get_array_value($data, "activity_log_id"),
			"client_id" => get_array_value($data, "client_id"),
			"invoice_payment_id" => get_array_value($data, "invoice_payment_id"),
			"invoice_id" => get_array_value($data, "invoice_id"),
			"estimate_id" => get_array_value($data, "estimate_id"),
			"order_id" => get_array_value($data, "order_id"),
			"estimate_request_id" => get_array_value($data, "estimate_request_id"),
			"actual_message_id" => get_array_value($data, "actual_message_id"),
			"parent_message_id" => get_array_value($data, "parent_message_id"),
			"event_id" => get_array_value($data, "event_id"),
			"announcement_id" => get_array_value($data, "announcement_id"),
			"exclude_ticket_creator" => get_array_value($data, "exclude_ticket_creator"),
			"notification_multiple_tasks" => get_array_value($data, "notification_multiple_tasks"),
			"contract_id" => get_array_value($data, "contract_id"),
			"lead_id" => get_array_value($data, "lead_id"),
			"proposal_id" => get_array_value($data, "proposal_id"),
			"estimate_comment_id" => get_array_value($data, "estimate_comment_id"),

			"hr_send_training_staff_id" => get_array_value($data, "hr_send_training_staff_id"),
			"hr_send_layoff_checklist_handle_staff_id" => get_array_value($data, "hr_send_layoff_checklist_handle_staff_id"),


		);

		//get data from plugin by persing 'plugin_'
		foreach ($data as $key => $value) {
			if (strpos($key, 'plugin_') !== false) {
				$options[$key] = $value;
			}
		}

		$this->hr_profile_model->hr_create_notification($event, $user_id, $options, $data['to_user_id']);
	}

	public function update_staff_contract_content() {

		if (!hr_has_permission('hr_profile_can_edit_hr_contract') && !is_admin()) {
			app_redirect("forbidden");
		}

		$data = $this->request->getPost();
		$data['content'] = decode_ajax_post_data($this->request->getPost('content'));

		if (!is_numeric($data['id'])) {
			app_redirect("forbidden");
		}

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_staff_contract');
		$builder->where('id_contract', $data['id']);
		$affectedrows = $builder->update(['content' => $data['content']]);

		if ($affectedrows > 0) {
			$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
		}
		app_redirect('hr_profile/view_staff_contract/'.$data['id']);
	}

	/**
	 * training detail
	 * @param  [type] $id   
	 * @param  [type] $hash 
	 * @return [type]       
	 */
	public function training_detail($id, $hash)
	{
		$training = $this->hr_profile_model->get_position_training($id);
		if (!$training
			|| ($training->hash != $hash)
			|| (!$hash || !$id)
			|| ($training->onlyforloggedin == 1 && !is_logged_in())
		) {
			show_404();
		}
		if ($this->request->getPost()) {
			$id = $this->request->getPost('id');
			$hash = $this->request->getPost('hash');
			$success = $this->hr_profile_model->add_training_result($id, $this->request->getPost());
			$link_redirect = site_url('admin/hr_profile/member/'.get_staff_user_id1());
			if ($success) {
				$training = $this->hr_profile_model->get_position_training($id);
				$this->session->setFlashdata("success_message", app_lang("hr_thank_you_for_participating_in_this_training"));

				if ($link_redirect !== '') {
					app_redirect('hr_profile/training_detail/'.$id.'/'.$hash);
				}
			}
		}

		$data = [];
		$data['training'] = $training;
		$data['id'] = $id;
		$data['hash'] = $hash;

		return $this->template->rander('Hr_profile\Views/participate', $data);
	}
	
	/**
	 * view staff training result
	 * @param  [type] $staff_id    
	 * @param  [type] $resultsetid 
	 * @param  [type] $id          
	 * @param  [type] $hash        
	 * @return [type]              
	 */
	public function view_staff_training_result($staff_id, $resultsetid, $id, $hash)
	{
		if (!hr_has_permission('hr_profile_can_view_global_hr_training') && !hr_has_permission('hr_profile_can_view_own_hr_training') && !is_admin() ) {
			access_denied('job_position');
		}

		$training = $this->hr_profile_model->get_position_training($id);
		$training_result = $this->hr_profile_model->get_mark_staff_from_resultsetid($resultsetid, $id, $staff_id);

		if (!$training
			|| ($training->hash != $hash)
			|| (!$hash || !$id)
			|| ($training->onlyforloggedin == 1 && !is_logged_in())
		) {
			show_404();
		}

		$data = [];
		$data['training'] = $training;
		$data['training_result'] = $training_result;
		$data['title'] = $training->subject;

		return $this->template->rander('Hr_profile\Views/participate_result', $data);

	}

	/**
	 * view
	 * @param  integer $id  
	 * @param  string  $tab 
	 * @return [type]       
	 */
	function staff_profile($id = 0, $tab = "") {
		if ($id * 1) {
			validate_numeric_value($id);

            //if team member's list is disabled, but the user can see his/her own profile.
			if (!$this->can_view_team_members_list() && $this->login_user->id != $id) {
				app_redirect("forbidden");
			}



            //we have an id. view the team_member's profie
			$options = array("id" => $id, "user_type" => "staff");
			$user_info = $this->Users_model->get_details($options)->getRow();
			if ($user_info) {



                //check which tabs are viewable for current logged in user
				$view_data['show_timeline'] = get_setting("module_timeline") ? true : false;

				$can_update_team_members_info = $this->hr_can_update_team_members_info($id);

				$view_data['show_general_info'] = $can_update_team_members_info;
				$view_data['show_job_info'] = false;

				if ($this->login_user->is_admin || $user_info->id === $this->login_user->id || $this->hr_has_job_info_manage_permission()) {
					$view_data['show_job_info'] = true;
				}

				$view_data['show_account_settings'] = false;

				$show_attendance = false;
				$show_leave = false;

				$expense_access_info = $this->get_access_info("expense");
				$view_data["show_expense_info"] = (get_setting("module_expense") == "1" && $expense_access_info->access_type == "all") ? true : false;

                //admin can access all members attendance and leave
                //none admin users can only access to his/her own information 

				if ($this->login_user->is_admin || $user_info->id === $this->login_user->id || get_array_value($this->login_user->permissions, "can_manage_user_role_and_permissions")) {
					$show_attendance = true;
					$show_leave = true;
					$view_data['show_account_settings'] = true;
				} else {
                    //none admin users but who has access to this team member's attendance and leave can access this info
					$access_timecard = $this->get_access_info("attendance");
					if ($access_timecard->access_type === "all" || in_array($user_info->id, $access_timecard->allowed_members)) {
						$show_attendance = true;
					}

					$access_leave = $this->get_access_info("leave");
					if ($access_leave->access_type === "all" || in_array($user_info->id, $access_leave->allowed_members)) {
						$show_leave = true;
					}
				}


                //check module availability
				$view_data['show_attendance'] = $show_attendance && get_setting("module_attendance") ? true : false;
				$view_data['show_leave'] = $show_leave && get_setting("module_leave") ? true : false;

                //check contact info view permissions
				$show_cotact_info = $this->hr_can_view_team_members_contact_info();
				$show_social_links = $this->hr_can_view_team_members_social_links();

                //own info is always visible
				if ($id == $this->login_user->id) {
					$show_cotact_info = true;
					$show_social_links = true;
				}

				$view_data['show_cotact_info'] = $show_cotact_info;
				$view_data['show_social_links'] = $show_social_links;

                //show projects tab to admin
				$view_data['show_projects'] = false;
				if ($this->login_user->is_admin) {
					$view_data['show_projects'] = true;
				}

				$view_data['show_projects_count'] = false;
				if ($this->can_manage_all_projects() && !$this->has_all_projects_restricted_role()) {
					$view_data['show_projects_count'] = true;
				}

                $view_data['tab'] = clean_data($tab); //selected tab
                $view_data['user_info'] = $user_info;
                $view_data['social_link'] = $this->Social_links_model->get_one($id);

                $hide_send_message_button = true;
                $this->init_permission_checker("message_permission");
                if ($this->check_access_on_messages_for_this_user() && $this->validate_sending_message($id)) {
                	$hide_send_message_button = false;
                }
                $view_data['hide_send_message_button'] = $hide_send_message_button;

                /*get data for related tab*/
                $view_data['staffid'] = $id;

                return $this->template->rander('Hr_profile\Views\hr_record/member', $view_data);

            } else {
            	show_404();
            }
        } else {

        	if (!$this->can_view_team_members_list()) {
        		app_redirect("forbidden");
        	}

            //we don't have any specific id to view. show the list of team_member
        	$view_data['team_members'] = $this->Users_model->get_details(array("user_type" => "staff", "status" => "active"))->getResult();
        	return $this->template->rander("team_members/profile_card", $view_data);
        }
    }

    /*clone funtion from Team member controller*/
    private function hr_can_update_team_members_info($user_id) {
    	$access_info = $this->get_access_info("team_member_update_permission");

    	if ($this->login_user->id === $user_id) {
            return true; //own profile
        } else if ($access_info->access_type == "all") {
            return true; //has access to change all user's profile
        } else if ($user_id && in_array($user_id, $access_info->allowed_members)) {
            return true; //has permission to update this user's profile
        } else {

        	return false;
        }
    }

    /**
     * hr has job info manage permission
     * @return [type] 
     */
    private function hr_has_job_info_manage_permission() {
    	return get_array_value($this->login_user->permissions, "job_info_manage_permission");
    }

    /**
     * hr can view team members contact info
     * @return [type] 
     */
    private function hr_can_view_team_members_contact_info() {
    	if ($this->login_user->user_type == "staff") {
    		if ($this->login_user->is_admin) {
    			return true;
    		} else if (get_array_value($this->login_user->permissions, "can_view_team_members_contact_info") == "1") {
    			return true;
    		}
    	}
    }

    /**
     * hr can view team members social links
     * @return [type] 
     */
    private function hr_can_view_team_members_social_links() {
    	if ($this->login_user->user_type == "staff") {
    		if ($this->login_user->is_admin) {
    			return true;
    		} else if (get_array_value($this->login_user->permissions, "can_view_team_members_social_links") == "1") {
    			return true;
    		}
    	}
    }

    /**
     * staff contracts info
     * @param  [type] $user_id 
     * @return [type]          
     */
    function staff_contracts_info($user_id) {
    	if ($user_id) {
    		validate_numeric_value($user_id);
    		$data['staffid'] = $user_id;

    		return $this->template->view('Hr_profile\Views\hr_record\includes\contract', $data);

    	}
    }

	/**
	 * staff dependent info
	 * @param  [type] $user_id 
	 * @return [type]          
	 */
	function staff_dependent_info($user_id) {
		if ($user_id) {
			validate_numeric_value($user_id);
			$data['dependent_person'] = $this->hr_profile_model->get_dependent_person_bytstaff($user_id);
			$data['user_id'] = $user_id;

			return $this->template->view('Hr_profile\Views\hr_record\includes\dependent_person', $data);

		}
	}

	/**
	 * staff training info
	 * @param  [type] $user_id 
	 * @return [type]          
	 */
	function staff_training_info($id) {
		if ($id) {
			$data = [];
			$data['staffid'] = $id;

			validate_numeric_value($id);
			$training_data = [];

			/*Onboarding training*/
			$training_allocation_staff = $this->hr_profile_model->get_training_allocation_staff($id);

			if ($training_allocation_staff != null) {
				$training_data['list_training_allocation'] = get_object_vars($training_allocation_staff);
			}

			if (isset($training_allocation_staff) && $training_allocation_staff != null) {

				$training_data['training_allocation_min_point'] = 0;
				$job_position_training = $this->hr_profile_model->get_job_position_training_de($training_allocation_staff->jp_interview_training_id);

				if ($job_position_training) {
					$training_data['training_allocation_min_point'] = $job_position_training->mint_point;
				}

				if ($training_allocation_staff) {
					$training_process_id = $training_allocation_staff->training_process_id;

					$training_data['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($training_process_id);

					/*Get the latest employee's training result.*/
					$training_results = $this->get_mark_staff($id, $training_process_id);

					$training_data['training_program_point'] = $training_results['training_program_point'];
					$training_data['staff_training_result'] = $training_results['staff_training_result'];

					/*have not done the test data*/
					$staff_training_result = [];
					foreach ($training_data['list_training'] as $key => $value) {
						$staff_training_result[$value['training_id']] = [
							'training_name' => $value['subject'],
							'total_point' => 0,
							'training_id' => $value['training_id'],
							'total_question' => 0,
							'total_question_point' => 0,
						];
					}

					/*did the test*/
					if (count($training_results['staff_training_result']) > 0) {
						foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
							if (isset($staff_training_result[$result_value['training_id']])) {
								unset($staff_training_result[$result_value['training_id']]);
							}
						}

						$training_data['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);
					} else {
						$training_data['staff_training_result'] = $staff_training_result;
					}

					if ((float) $training_results['training_program_point'] >= (float) $training_data['training_allocation_min_point']) {
						$training_data['complete'] = 0;
					} else {
						$training_data['complete'] = 1;
					}
				}
			}

			if (count($training_data) > 0) {
				$data['training_data'][] = $training_data;
			}

			/*Additional training*/
			$additional_trainings = $this->hr_profile_model->get_additional_training($id);

			foreach ($additional_trainings as $key => $value) {
				$training_temp = [];

				$training_temp['training_allocation_min_point'] = $value['mint_point'];
				$training_temp['list_training_allocation'] = $value;
				$training_temp['list_training'] = $this->hr_profile_model->get_list_position_training_by_id_training($value['position_training_id']);

				/*Get the latest employee's training result.*/
				$training_results = $this->get_mark_staff($id, $value['position_training_id']);

				$training_temp['training_program_point'] = $training_results['training_program_point'];
				$training_temp['staff_training_result'] = $training_results['staff_training_result'];

				/*have not done the test data*/
				$staff_training_result = [];
				foreach ($training_temp['list_training'] as $key => $value) {
					$staff_training_result[$value['training_id']] = [
						'training_name' => $value['subject'],
						'total_point' => 0,
						'training_id' => $value['training_id'],
						'total_question' => 0,
						'total_question_point' => 0,
					];
				}

				/*did the test*/
				if (count($training_results['staff_training_result']) > 0) {

					foreach ($training_results['staff_training_result'] as $result_key => $result_value) {
						if (isset($staff_training_result[$result_value['training_id']])) {
							unset($staff_training_result[$result_value['training_id']]);
						}
					}

					$training_temp['staff_training_result'] = array_merge($training_results['staff_training_result'], $staff_training_result);

				} else {
					$training_temp['staff_training_result'] = $staff_training_result;
				}

				if ((float) $training_results['training_program_point'] >= (float) $training_temp['training_allocation_min_point']) {
					$training_temp['complete'] = 0;
				} else {
					$training_temp['complete'] = 1;
				}

				if (count($training_temp) > 0) {
					$data['training_data'][] = $training_temp;
				}
			}

			return $this->template->view('Hr_profile\Views\hr_record\includes\training', $data);
		}
	}

	/*ROLE for module Start*/

	//load the role view
	function roles() {
		return $this->template->rander("Hr_profile\Views/roles/index");
	}

    //load the role add/edit modal
	function role_modal_form() {

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));

		$view_data['model_info'] = $this->Roles_model->get_one($this->request->getPost('id'));
		$view_data['roles_dropdown'] = array("" => "-") + $this->Roles_model->get_dropdown_list(array("title"), "id");
		return $this->template->view('Hr_profile\Views\roles/modal_form', $view_data);
	}

    //get permisissions of a role
	function role_permissions($role_id) {
		if ($role_id) {
			validate_numeric_value($role_id);
			$view_data['model_info'] = $this->Roles_model->get_one($role_id);

			$permissions = unserialize($view_data['model_info']->plugins_permissions1);

			if (!$permissions) {
				$permissions = array();
			}

			/*dashboard*/
			$view_data['hr_profile_can_view_global_hr_dashboard'] = get_array_value($permissions, "hr_profile_can_view_global_hr_dashboard");

			/*organizational_chart*/
			$view_data['hr_profile_can_view_own_organizational_chart'] = get_array_value($permissions, "hr_profile_can_view_own_organizational_chart");
			$view_data['hr_profile_can_view_global_organizational_chart'] = get_array_value($permissions, "hr_profile_can_view_global_organizational_chart");
			$view_data['hr_profile_can_create_organizational_chart'] = get_array_value($permissions, "hr_profile_can_create_organizational_chart");
			$view_data['hr_profile_can_edit_organizational_chart'] = get_array_value($permissions, "hr_profile_can_edit_organizational_chart");
			$view_data['hr_profile_can_delete_organizational_chart'] = get_array_value($permissions, "hr_profile_can_delete_organizational_chart");

			/*onboarding*/
			$view_data['hr_profile_can_view_own_onboarding'] = get_array_value($permissions, "hr_profile_can_view_own_onboarding");
			$view_data['hr_profile_can_view_global_onboarding'] = get_array_value($permissions, "hr_profile_can_view_global_onboarding");
			$view_data['hr_profile_can_create_onboarding'] = get_array_value($permissions, "hr_profile_can_create_onboarding");
			$view_data['hr_profile_can_edit_onboarding'] = get_array_value($permissions, "hr_profile_can_edit_onboarding");
			$view_data['hr_profile_can_delete_onboarding'] = get_array_value($permissions, "hr_profile_can_delete_onboarding");
			/*hr_records*/
			$view_data['hr_profile_can_view_own_hr_records'] = get_array_value($permissions, "hr_profile_can_view_own_hr_records");
			$view_data['hr_profile_can_view_global_hr_records'] = get_array_value($permissions, "hr_profile_can_view_global_hr_records");
			$view_data['hr_profile_can_create_hr_records'] = get_array_value($permissions, "hr_profile_can_create_hr_records");
			$view_data['hr_profile_can_edit_hr_records'] = get_array_value($permissions, "hr_profile_can_edit_hr_records");
			$view_data['hr_profile_can_delete_hr_records'] = get_array_value($permissions, "hr_profile_can_delete_hr_records");
			/*job_description*/
			$view_data['hr_profile_can_view_own_job_description'] = get_array_value($permissions, "hr_profile_can_view_own_job_description");
			$view_data['hr_profile_can_view_global_job_description'] = get_array_value($permissions, "hr_profile_can_view_global_job_description");
			$view_data['hr_profile_can_create_job_description'] = get_array_value($permissions, "hr_profile_can_create_job_description");
			$view_data['hr_profile_can_edit_job_description'] = get_array_value($permissions, "hr_profile_can_edit_job_description");
			$view_data['hr_profile_can_delete_job_description'] = get_array_value($permissions, "hr_profile_can_delete_job_description");
			/*hr_training*/
			$view_data['hr_profile_can_view_own_hr_training'] = get_array_value($permissions, "hr_profile_can_view_own_hr_training");
			$view_data['hr_profile_can_view_global_hr_training'] = get_array_value($permissions, "hr_profile_can_view_global_hr_training");
			$view_data['hr_profile_can_create_hr_training'] = get_array_value($permissions, "hr_profile_can_create_hr_training");
			$view_data['hr_profile_can_edit_hr_training'] = get_array_value($permissions, "hr_profile_can_edit_hr_training");
			$view_data['hr_profile_can_delete_hr_training'] = get_array_value($permissions, "hr_profile_can_delete_hr_training");
			/*hr_contract*/
			$view_data['hr_profile_can_view_own_hr_contract'] = get_array_value($permissions, "hr_profile_can_view_own_hr_contract");
			$view_data['hr_profile_can_view_global_hr_contract'] = get_array_value($permissions, "hr_profile_can_view_global_hr_contract");
			$view_data['hr_profile_can_create_hr_contract'] = get_array_value($permissions, "hr_profile_can_create_hr_contract");
			$view_data['hr_profile_can_edit_hr_contract'] = get_array_value($permissions, "hr_profile_can_edit_hr_contract");
			$view_data['hr_profile_can_delete_hr_contract'] = get_array_value($permissions, "hr_profile_can_delete_hr_contract");
			/*dependent_persons*/
			$view_data['hr_profile_can_view_own_dependent_persons'] = get_array_value($permissions, "hr_profile_can_view_own_dependent_persons");
			$view_data['hr_profile_can_view_global_dependent_persons'] = get_array_value($permissions, "hr_profile_can_view_global_dependent_persons");
			$view_data['hr_profile_can_create_dependent_persons'] = get_array_value($permissions, "hr_profile_can_create_dependent_persons");
			$view_data['hr_profile_can_edit_dependent_persons'] = get_array_value($permissions, "hr_profile_can_edit_dependent_persons");
			$view_data['hr_profile_can_delete_dependent_persons'] = get_array_value($permissions, "hr_profile_can_delete_dependent_persons");
			/*layoff_checklists*/
			$view_data['hr_profile_can_view_own_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_view_own_layoff_checklists");
			$view_data['hr_profile_can_view_global_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_view_global_layoff_checklists");
			$view_data['hr_profile_can_create_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_create_layoff_checklists");
			$view_data['hr_profile_can_edit_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_edit_layoff_checklists");
			$view_data['hr_profile_can_delete_layoff_checklists'] = get_array_value($permissions, "hr_profile_can_delete_layoff_checklists");
			/*_global_report*/
			$view_data['hr_profile_can_view_global_report'] = get_array_value($permissions, "hr_profile_can_view_global_report");
			/*global_setting*/
			$view_data['hr_profile_can_view_global_setting'] = get_array_value($permissions, "hr_profile_can_view_global_setting");
			$view_data['hr_profile_can_create_setting'] = get_array_value($permissions, "hr_profile_can_create_setting");
			$view_data['hr_profile_can_edit_setting'] = get_array_value($permissions, "hr_profile_can_edit_setting");
			$view_data['hr_profile_can_delete_setting'] = get_array_value($permissions, "hr_profile_can_delete_setting");

			$view_data['permissions'] = $permissions;

			return $this->template->view("Hr_profile\Views/roles/permissions", $view_data);
		}
	}

    //save a role
	function role_save() {
		$this->validate_submitted_data(array(
			"id" => "numeric",
			"title" => "required"
		));

		$id = $this->request->getPost('id');
		$copy_settings = $this->request->getPost('copy_settings');
		$data = array(
			"title" => $this->request->getPost('title'),
		);

		if ($copy_settings) {
			$role = $this->Roles_model->get_one($copy_settings);
			$data["permissions"] = $role->plugins_permissions1;
		}

		$save_id = $this->Roles_model->ci_save($data, $id);
		if ($save_id) {
			echo json_encode(array("success" => true, "data" => $this->role_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
		}
	}

    //save permissions of a role
	function role_save_permissions() {
		$this->validate_submitted_data(array(
			"id" => "numeric|required"
		));

		$id = $this->request->getPost('id');
		$data = $this->request->getPost();
		$permissions = [];

		$permissions['hr_profile_can_view_global_hr_dashboard'] = isset($data['hr_profile_can_view_global_hr_dashboard']) ? $data['hr_profile_can_view_global_hr_dashboard'] : NULL;

		$permissions['hr_profile_can_view_own_organizational_chart'] = isset($data['hr_profile_can_view_own_organizational_chart']) ? $data['hr_profile_can_view_own_organizational_chart'] : NULL;
		$permissions['hr_profile_can_view_global_organizational_chart'] = isset($data['hr_profile_can_view_global_organizational_chart']) ? $data['hr_profile_can_view_global_organizational_chart'] : NULL;
		$permissions['hr_profile_can_create_organizational_chart'] = isset($data['hr_profile_can_create_organizational_chart']) ? $data['hr_profile_can_create_organizational_chart'] : NULL;
		$permissions['hr_profile_can_edit_organizational_chart'] = isset($data['hr_profile_can_edit_organizational_chart']) ? $data['hr_profile_can_edit_organizational_chart'] : NULL;
		$permissions['hr_profile_can_delete_organizational_chart'] = isset($data['hr_profile_can_delete_organizational_chart']) ? $data['hr_profile_can_delete_organizational_chart'] : NULL;

		$permissions['hr_profile_can_view_own_organizational_chart'] = isset($data['hr_profile_can_view_own_organizational_chart']) ? $data['hr_profile_can_view_own_organizational_chart'] : NULL;
		$permissions['hr_profile_can_view_global_organizational_chart'] = isset($data['hr_profile_can_view_global_organizational_chart']) ? $data['hr_profile_can_view_global_organizational_chart'] : NULL;
		$permissions['hr_profile_can_create_organizational_chart'] = isset($data['hr_profile_can_create_organizational_chart']) ? $data['hr_profile_can_create_organizational_chart'] : NULL;
		$permissions['hr_profile_can_edit_organizational_chart'] = isset($data['hr_profile_can_edit_organizational_chart']) ? $data['hr_profile_can_edit_organizational_chart'] : NULL;
		$permissions['hr_profile_can_delete_organizational_chart'] = isset($data['hr_profile_can_delete_organizational_chart']) ? $data['hr_profile_can_delete_organizational_chart'] : NULL;

		$permissions['hr_profile_can_view_own_onboarding'] = isset($data['hr_profile_can_view_own_onboarding']) ? $data['hr_profile_can_view_own_onboarding'] : NULL;
		$permissions['hr_profile_can_view_global_onboarding'] = isset($data['hr_profile_can_view_global_onboarding']) ? $data['hr_profile_can_view_global_onboarding'] : NULL;
		$permissions['hr_profile_can_create_onboarding'] = isset($data['hr_profile_can_create_onboarding']) ? $data['hr_profile_can_create_onboarding'] : NULL;
		$permissions['hr_profile_can_edit_onboarding'] = isset($data['hr_profile_can_edit_onboarding']) ? $data['hr_profile_can_edit_onboarding'] : NULL;
		$permissions['hr_profile_can_delete_onboarding'] = isset($data['hr_profile_can_delete_onboarding']) ? $data['hr_profile_can_delete_onboarding'] : NULL;

		$permissions['hr_profile_can_view_own_hr_records'] = isset($data['hr_profile_can_view_own_hr_records']) ? $data['hr_profile_can_view_own_hr_records'] : NULL;
		$permissions['hr_profile_can_view_global_hr_records'] = isset($data['hr_profile_can_view_global_hr_records']) ? $data['hr_profile_can_view_global_hr_records'] : NULL;
		$permissions['hr_profile_can_create_hr_records'] = isset($data['hr_profile_can_create_hr_records']) ? $data['hr_profile_can_create_hr_records'] : NULL;
		$permissions['hr_profile_can_edit_hr_records'] = isset($data['hr_profile_can_edit_hr_records']) ? $data['hr_profile_can_edit_hr_records'] : NULL;
		$permissions['hr_profile_can_delete_hr_records'] = isset($data['hr_profile_can_delete_hr_records']) ? $data['hr_profile_can_delete_hr_records'] : NULL;

		$permissions['hr_profile_can_view_own_job_description'] = isset($data['hr_profile_can_view_own_job_description']) ? $data['hr_profile_can_view_own_job_description'] : NULL;
		$permissions['hr_profile_can_view_global_job_description'] = isset($data['hr_profile_can_view_global_job_description']) ? $data['hr_profile_can_view_global_job_description'] : NULL;
		$permissions['hr_profile_can_create_job_description'] = isset($data['hr_profile_can_create_job_description']) ? $data['hr_profile_can_create_job_description'] : NULL;
		$permissions['hr_profile_can_edit_job_description'] = isset($data['hr_profile_can_edit_job_description']) ? $data['hr_profile_can_edit_job_description'] : NULL;
		$permissions['hr_profile_can_delete_job_description'] = isset($data['hr_profile_can_delete_job_description']) ? $data['hr_profile_can_delete_job_description'] : NULL;

		$permissions['hr_profile_can_view_own_hr_training'] = isset($data['hr_profile_can_view_own_hr_training']) ? $data['hr_profile_can_view_own_hr_training'] : NULL;
		$permissions['hr_profile_can_view_global_hr_training'] = isset($data['hr_profile_can_view_global_hr_training']) ? $data['hr_profile_can_view_global_hr_training'] : NULL;
		$permissions['hr_profile_can_create_hr_training'] = isset($data['hr_profile_can_create_hr_training']) ? $data['hr_profile_can_create_hr_training'] : NULL;
		$permissions['hr_profile_can_edit_hr_training'] = isset($data['hr_profile_can_edit_hr_training']) ? $data['hr_profile_can_edit_hr_training'] : NULL;
		$permissions['hr_profile_can_delete_hr_training'] = isset($data['hr_profile_can_delete_hr_training']) ? $data['hr_profile_can_delete_hr_training'] : NULL;

		$permissions['hr_profile_can_view_own_hr_contract'] = isset($data['hr_profile_can_view_own_hr_contract']) ? $data['hr_profile_can_view_own_hr_contract'] : NULL;
		$permissions['hr_profile_can_view_global_hr_contract'] = isset($data['hr_profile_can_view_global_hr_contract']) ? $data['hr_profile_can_view_global_hr_contract'] : NULL;
		$permissions['hr_profile_can_create_hr_contract'] = isset($data['hr_profile_can_create_hr_contract']) ? $data['hr_profile_can_create_hr_contract'] : NULL;
		$permissions['hr_profile_can_edit_hr_contract'] = isset($data['hr_profile_can_edit_hr_contract']) ? $data['hr_profile_can_edit_hr_contract'] : NULL;
		$permissions['hr_profile_can_delete_hr_contract'] = isset($data['hr_profile_can_delete_hr_contract']) ? $data['hr_profile_can_delete_hr_contract'] : NULL;

		$permissions['hr_profile_can_view_own_dependent_persons'] = isset($data['hr_profile_can_view_own_dependent_persons']) ? $data['hr_profile_can_view_own_dependent_persons'] : NULL;
		$permissions['hr_profile_can_view_global_dependent_persons'] = isset($data['hr_profile_can_view_global_dependent_persons']) ? $data['hr_profile_can_view_global_dependent_persons'] : NULL;
		$permissions['hr_profile_can_create_dependent_persons'] = isset($data['hr_profile_can_create_dependent_persons']) ? $data['hr_profile_can_create_dependent_persons'] : NULL;
		$permissions['hr_profile_can_edit_dependent_persons'] = isset($data['hr_profile_can_edit_dependent_persons']) ? $data['hr_profile_can_edit_dependent_persons'] : NULL;
		$permissions['hr_profile_can_delete_dependent_persons'] = isset($data['hr_profile_can_delete_dependent_persons']) ? $data['hr_profile_can_delete_dependent_persons'] : NULL;

		$permissions['hr_profile_can_view_own_layoff_checklists'] = isset($data['hr_profile_can_view_own_layoff_checklists']) ? $data['hr_profile_can_view_own_layoff_checklists'] : NULL;
		$permissions['hr_profile_can_view_global_layoff_checklists'] = isset($data['hr_profile_can_view_global_layoff_checklists']) ? $data['hr_profile_can_view_global_layoff_checklists'] : NULL;
		$permissions['hr_profile_can_create_layoff_checklists'] = isset($data['hr_profile_can_create_layoff_checklists']) ? $data['hr_profile_can_create_layoff_checklists'] : NULL;
		$permissions['hr_profile_can_edit_layoff_checklists'] = isset($data['hr_profile_can_edit_layoff_checklists']) ? $data['hr_profile_can_edit_layoff_checklists'] : NULL;
		$permissions['hr_profile_can_delete_layoff_checklists'] = isset($data['hr_profile_can_delete_layoff_checklists']) ? $data['hr_profile_can_delete_layoff_checklists'] : NULL;

		$permissions['hr_profile_can_view_global_report'] = isset($data['hr_profile_can_view_global_report']) ? $data['hr_profile_can_view_global_report'] : NULL;

		$permissions['hr_profile_can_view_global_setting'] = isset($data['hr_profile_can_view_global_setting']) ? $data['hr_profile_can_view_global_setting'] : NULL;
		$permissions['hr_profile_can_create_setting'] = isset($data['hr_profile_can_create_setting']) ? $data['hr_profile_can_create_setting'] : NULL;
		$permissions['hr_profile_can_edit_setting'] = isset($data['hr_profile_can_edit_setting']) ? $data['hr_profile_can_edit_setting'] : NULL;
		$permissions['hr_profile_can_delete_setting'] = isset($data['hr_profile_can_delete_setting']) ? $data['hr_profile_can_delete_setting'] : NULL;
        

		$options = array("id" => $id);
		$data_role = $this->Roles_model->get_details($options)->getRow();
		$old_role_permissions = is_array(unserialize($data_role->plugins_permissions1)) ? unserialize($data_role->plugins_permissions1) : array();

        $permissions = app_hooks()->apply_filters('app_filter_role_permissions_save_data_plugin', $permissions, $this->request->getPost());

        foreach ($permissions as $key => $permission) {
        	$old_role_permissions[$key] = $permission;
        }

		$data = array(
			"plugins_permissions1" => serialize($old_role_permissions),
		);

		$save_id = $this->Roles_model->ci_save($data, $id);
		if ($save_id) {
			echo json_encode(array("success" => true, "data" => $this->role_row_data($id), 'id' => $save_id, 'message' => app_lang('record_saved')));
		} else {
			echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
		}
	}

    //get role list data
	function role_list_data() {
		$list_data = $this->Roles_model->get_details()->getResult();
		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->role_make_row($data);
		}
		echo json_encode(array("data" => $result));
	}

    //get a row of role list
	private function role_row_data($id) {
		$options = array("id" => $id);
		$data = $this->Roles_model->get_details($options)->getRow();
		return $this->role_make_row($data);
	}

    //make a row of role list table
	private function role_make_row($data) {
		return array("<a href='#' data-id='$data->id' class='role-row link'>" . $data->title . "</a>",
			"<a class='edit'><i data-feather='sliders' class='icon-16'></i></a>"
		);
	}

	function general_info($user_id) {
		validate_numeric_value($user_id);
		$this->update_only_allowed_members($user_id);

		$view_data['user_info'] = $this->Users_model->get_one($user_id);
		$view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

		return $this->template->view("Hr_profile\Views\hr_record/general_info", $view_data);
	}

	private function update_only_allowed_members($user_id) {
        if ($this->can_update_team_members_info($user_id)) {
            return true; //own profile
        } else {
            app_redirect("forbidden");
        }
    }

    //only admin can change other user's info
    //none admin users can only change his/her own info
    //allowed members can update other members info    
    private function can_update_team_members_info($user_id) {
        $access_info = $this->get_access_info("team_member_update_permission");

        if ($this->login_user->id === $user_id) {
            return true; //own profile
        } else if ($access_info->access_type == "all") {
            return true; //has access to change all user's profile
        } else if ($user_id && in_array($user_id, $access_info->allowed_members)) {
            return true; //has permission to update this user's profile
        } else {

            return false;
        }
    }

    //save general information of a team member
    function save_general_info($user_id) {
        validate_numeric_value($user_id);
        $this->update_only_allowed_members($user_id);

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required"
        ));

        $user_data = array(
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "address" => $this->request->getPost('address'),
            "phone" => $this->request->getPost('phone'),
            "skype" => $this->request->getPost('skype'),
            "gender" => $this->request->getPost('gender'),
            "alternative_address" => $this->request->getPost('alternative_address'),
            "alternative_phone" => $this->request->getPost('alternative_phone'),
            "dob" => $this->request->getPost('dob'),
            "ssn" => $this->request->getPost('ssn'),
            "account_number" => $this->request->getPost('account_number'),
            "name_account" => $this->request->getPost('name_account'),
            "issue_bank" => $this->request->getPost('issue_bank'),
        );

        $user_data = clean_data($user_data);

        $user_info_updated = $this->Users_model->ci_save($user_data, $user_id);

        save_custom_fields("team_members", $user_id, $this->login_user->is_admin, $this->login_user->user_type);

        if ($user_info_updated) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /**
     * new_deparment_modal_form
     * @return [type] 
     */
    public function new_deparment_modal_form() {
    	$this->access_only_team_members();

    	$this->validate_submitted_data(array(
    		"id" => "numeric"
    	));
    	$data = [];
    	$contract_type_data = [];

    	$id = $this->request->getPost('id');
    	if($id && is_numeric($id)){
			$data['department'] = hr_profile_get_department_name($id);

    	}else{
    		$id = '';
    	}

    	$department_options = array(
    		"deleted" => 0,
    	);
    	$data['list_department'] = $this->Team_model->get_details($department_options)->getResultArray();
    	$options = array(
    		"status" => "active",
    		"user_type" => "staff",
    	);
    	$data['list_staff'] = $this->Users_model->get_details($options)->getResultArray();

    	$data['id'] = $id;
    	return $this->template->view('Hr_profile\Views\organizational\organizational_modal', $data);
    }

    /**
     * new_resignation_procedures_form
     * @return [type] 
     */
    public function new_resignation_procedures_form() {
    	$this->access_only_team_members();

    	$this->validate_submitted_data(array(
    		"id" => "numeric"
    	));
    	$data = [];
    	$contract_type_data = [];

    	$id = $this->request->getPost('id');
    	if($id && is_numeric($id)){
    		$data['resignation'] = hr_profile_get_department_name($id);

    	}else{
    		$id = '';
    	}

    	$arr_staff_quiting_work = [];
		$get_list_quiting_work = $this->hr_profile_model->get_list_quiting_work();
		foreach ($get_list_quiting_work as $value) {
			$arr_staff_quiting_work[] = $value['staffid'];
		}
		
		if(count($arr_staff_quiting_work) > 0){
			$data['staffs'] = $this->hr_profile_model->hr_profile_run_query('select * from '.get_db_prefix().'users where status = "active" AND user_type ="staff" AND id NOT IN ('.implode(",", $arr_staff_quiting_work).')');
		}else{
			$data['staffs'] = $this->hr_profile_model->hr_profile_run_query('select * from '.get_db_prefix().'users where status = "active" AND user_type ="staff"');
		}

    	$data['id'] = $id;
    	return $this->template->view('Hr_profile\Views\resignation_procedures\resignation_procedure_modal', $data);
    }

	/*ROLE for module End*/

}