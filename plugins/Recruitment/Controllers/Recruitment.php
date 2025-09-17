<?php

namespace Recruitment\Controllers;

use App\Controllers\Security_Controller;
use App\Models\Crud_model;

class Recruitment extends Security_Controller {

	protected $recruitment_model;
	function __construct() {

		parent::__construct();
		$this->recruitment_model = new \Recruitment\Models\Recruitment_model();
		app_hooks()->do_action('app_hook_recruitment_init');

	}

	/**
	 * setting
	 * @return view
	 */
	public function setting() {
		if (!re_has_permission("recruitment_can_edit") && !is_admin()) {
			access_denied('recruitment');
		}
		$data['group'] = $this->request->getGet('group');
		$data['title'] = app_lang('setting');
		$data['tab'][] = 'job_position';
		$data['tab'][] = 'evaluation_criteria';
		$data['tab'][] = 'evaluation_form';
		$data['tab'][] = 'tranfer_personnel';
		$data['tab'][] = 'skills';
		$data['tab'][] = 'company_list';
		$data['tab'][] = 'industry_list';
		$data['tab'][] = 'recruitment_campaign_setting';


		if ($data['group'] == '') {
			$data['group'] = 'job_position';
		}
		$data['tabs']['view'] = 'includes/' . $data['group'];

		$data['positions'] = $this->recruitment_model->get_job_position();

		$data['list_group'] = $this->recruitment_model->get_group_evaluation_criteria();

		$data['group_criterias'] = $this->recruitment_model->get_list_child_criteria();

		$data['list_form'] = $this->recruitment_model->get_list_evaluation_form();

		$data['list_set_tran'] = $this->recruitment_model->get_list_set_transfer();

		$data['skills'] = $this->recruitment_model->get_skill();

		$data['company_list'] = $this->recruitment_model->get_company();

		$data['industry_list'] = $this->recruitment_model->get_industry();


		return $this->template->rander('Recruitment\Views/manage_setting', $data);
	}

	/**
	 * job positions
	 * @return [type] 
	 */
	public function job_positions() {
		$data['job_positions'] = $this->recruitment_model->get_job_position();
		return $this->template->rander("Recruitment\Views\includes\job_position", $data);
	}
	
	/**
	 * list commodity type data
	 * @return [type] 
	 */
	public function list_job_position_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_job_position();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_job_position_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make commodity type row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_job_position_row($data) {

		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/job_position_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_job_position'), "data-post-id" => $data['position_id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['position_id'], "data-action-url" => get_uri("recruitment/delete_job_position/".$data['position_id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['position_id']),
			nl2br($data['position_name']),
			get_rec_industry_name($data['industry_id']),
			$options
		);
	}

	/**
	 * commodity type modal form
	 * @return [type] 
	 */
	public function job_position_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$job_position_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['job_position_data'] = $this->recruitment_model->get_job_position($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;
		$data['skills'] = $this->recruitment_model->get_skill();
		$data['industry_list'] = $this->recruitment_model->get_industry();

		return $this->template->view('Recruitment\Views\includes\modal_forms\job_position_modal', $data);
	}


	/**
	 * job position
	 * @return redirect
	 */
	public function job_position($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->recruitment_model->add_job_position($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/job_positions');
			} else {
				$success = $this->recruitment_model->update_job_position($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/job_positions');
			}
			die;
		}
	}

	/**
	 * delete job_position
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_job_position($id) {
		if (!$id) {
			app_redirect('recruitment/job_positions');
		}
		$response = $this->recruitment_model->delete_job_position($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * recruitmentproposal
	 * @param  string $id 
	 * @return view
	 */
	public function recruitment_proposal($id = '') {
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['proposal_id'] = $id;

		$data['title'] = app_lang('recruitment_proposal');
		return $this->template->rander('Recruitment\Views/recruitment_plans/recruitment_proposal', $data);
	}

	/**
	 * proposal
	 * @return redirect
	 */
	public function proposal() {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			
			if (!$this->request->getPost('id')) {
				$id = $this->recruitment_model->add_recruitment_proposal($data);
				if ($id) {
					handle_rec_proposal_file($id);
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/recruitment_proposal');
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_recruitment_proposal($data, $id);
				handle_rec_proposal_file($id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/recruitment_proposal');
			}
			die;
		}
	}

	/**
	 * delete recruitment proposal
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_recruitment_proposal() {
		$id = $this->request->getPost('id');
		if (!re_has_permission("recruitment_can_delete") && !is_admin()) {
			app_redirect("forbidden");
		}

		if (!$id) {
			app_redirect('recruitment/recruitment_proposal');
		}
		$response = $this->recruitment_model->delete_recruitment_proposal($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('recruitment/recruitment_proposal');
	}

	/**
	 * table proposal
	 * @return
	 */
	public function table_proposal() {
		$dataPost = $this->request->getPost();
		$this->recruitment_model->get_table_data(module_views_path('Recruitment', 'recruitment_plans/table_proposal'), $dataPost);
	}

	/**
	 * add recruitment plan
	 */
	public function add_recruitment_plan($id = '') {
		if (!is_admin() && !re_has_permission("recruitment_can_edit") && !re_has_permission("recruitment_can_create")) {
			app_redirect("forbidden");
		}
		if ($id == '') {
			$data['title'] = app_lang('_new_proposal');
		}else{
			$data['title'] = app_lang('_edit_proposal');
			$data['recruitment_plan'] = $this->recruitment_model->get_rec_proposal($id);
			$data['proposal_attachment'] = $this->recruitment_model->get_proposal_attachments($id);

		}
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();
		$data['positions'] = $this->recruitment_model->get_job_position();
		return $this->template->rander('Recruitment\Views\recruitment_plans/add_edit_recruitment_plan', $data);
	}

	/**
	 * view recruitment plan
	 * @param  string $id 
	 * @return [type]     
	 */
	public function view_recruitment_plan($id = '')
	{
		$data['proposals'] = $this->recruitment_model->get_rec_proposal($id);
		$data['proposal_attachment'] = $this->recruitment_model->get_proposal_attachments($id);
		$data['proposal_id'] = $id;

		$data['title'] = app_lang('recruitment_proposal');
		return $this->template->rander('Recruitment\Views/recruitment_plans/view_recruitment_plan', $data);
	}


	/**
	 * table campaign
	 * @return
	 */
	public function table_campaign() {
		$dataPost = $this->request->getPost();
		$this->recruitment_model->get_table_data(module_views_path('Recruitment', 'recruitment_campaign/table_campaign'), $dataPost);
	}

	/**
	 * get proposal data ajax
	 * @param  integer $id
	 * @return view
	 */
	public function get_proposal_data_ajax($id) {

		$data['id'] = $id;
		$data['proposals'] = $this->recruitment_model->get_rec_proposal($id);
		$data['proposal_file'] = $this->recruitment_model->get_proposal_file($id);

		return $this->template->rander('Recruitment\Views/proposal_preview', $data);
	}

	/**
	 * delete proposal attachment
	 * @param  int $id
	 * @return
	 */
	public function delete_proposal_attachment($attachment_id) {

		$file = $this->recruitment_model->get_proposal_attachments('', $attachment_id);
		echo json_encode([
			'success' => $this->recruitment_model->delete_proposal_attachment($attachment_id),
		]);
	}

	/**
	 * plan file
	 * @param  [type] $id     
	 * @param  [type] $rel_id 
	 * @return [type]         
	 */
	public function plan_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] =  get_staff_image(get_staff_user_id1(), false);
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Recruitment\Views/recruitment_plans/file', $data);
	}

	/**
	 * file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return view
	 */
	public function file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = get_staff_image(get_staff_user_id1(), false);
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Recruitment\Views/_file', $data);
	}

	/**
	 * approve reject proposal
	 * @param  int $type
	 * @param  int $id
	 * @return redirect
	 */
	public function approve_reject_proposal($type, $id) {
		$result = $this->recruitment_model->approve_reject_proposal($type, $id);
		if ($result == 'approved') {
			$this->session->setFlashdata("success_message", app_lang('approved') . ' ' . app_lang('recruitment_proposal') . ' ' . app_lang('successfully'));
		} elseif ($result == 'reject') {
			$this->session->setFlashdata("success_message", app_lang('reject') . ' ' . app_lang('recruitment_proposal') . ' ' . app_lang('successfully'));
		} else {
			$this->session->setFlashdata("error_message", app_lang('action') . ' ' . app_lang('fail') );
		}
		app_redirect(('recruitment/view_recruitment_plan/' . $id));
	}

	/**
	 * recruitment campaign
	 * @param  int $id
	 * @return view
	 */
	public function recruitment_campaign($id = '') {
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['rec_proposal'] = $this->recruitment_model->get_rec_proposal_by_status(2);
		$data['campaign_id'] = $id;
		$data['rec_channel_form']	= $this->recruitment_model->get_recruitment_channel();
		$data['company_list'] = $this->recruitment_model->get_company();
		
		$data['title'] = app_lang('recruitment_campaign');
		return $this->template->rander('Recruitment\Views/recruitment_campaign/recruitment_campaign', $data);
	}

	/**
	 * [dd recruitment campaign
	 * @param string $id 
	 */
	public function add_recruitment_campaign($id = '') {
		if (!is_admin() && !re_has_permission("recruitment_can_edit") && !re_has_permission("recruitment_can_create")) {
			app_redirect("forbidden");
		}
		if ($id == '') {
			$data['title'] = app_lang('new_campaign');
		}else{
			$data['title'] = app_lang('edit_campaign');
			$data['recruitment_campaign'] = $this->recruitment_model->get_rec_campaign($id);
			$data['campaign_attachment'] = $this->recruitment_model->get_campaign_attachments($id);

		}
		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['rec_proposal'] = $this->recruitment_model->get_rec_proposal_by_status(2);
		$data['campaign_id'] = $id;
		$data['rec_channel_form']	= $this->recruitment_model->get_recruitment_channel();
		$data['company_list'] = $this->recruitment_model->get_company();

		return $this->template->rander('Recruitment\Views\recruitment_campaign/add_edit_recruitment_campaign', $data);
	}

	/**
	 * campaign
	 * @return redirect
	 */
	public function campaign() {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			
			if (!$this->request->getPost('cp_id')) {
				$id = $this->recruitment_model->add_recruitment_campaign($data);
				if ($id) {
					handle_rec_campaign_file($id);
					$success = true;
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect(('recruitment/recruitment_campaign'));
			} else {
				$id = $data['cp_id'];
				unset($data['cp_id']);
				$success = $this->recruitment_model->update_recruitment_campaign($data, $id);
				handle_rec_campaign_file($id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect(('recruitment/recruitment_campaign'));
			}
			die;
		}
	}

	/**
	 * delete recruitment campaign
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_recruitment_campaign() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('recruitment/recruitment_campaign'));
		}
		$response = $this->recruitment_model->delete_recruitment_campaign($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect(('recruitment/recruitment_campaign'));
	}

	/**
	 * campaign code exists
	 * @return
	 */
	public function campaign_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				// First we need to check if the email is the same
				$cp_id = $this->request->getPost('cp_id');
				if ($cp_id != '') {
					$this->db->where('cp_id', $cp_id);
					$campaign = $this->db->get('tblrec_campaign')->row();
					if ($campaign->campaign_code == $this->request->getPost('campaign_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('campaign_code', $this->request->getPost('campaign_code'));
				$total_rows = $this->db->count_all_results('tblrec_campaign    ');
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
	 * get campaign data ajax
	 * @param  int $id
	 * @return view
	 */
	public function get_campaign_data_ajax($id) {
		$this->load->model('departments_model');
		$data['id'] = $id;
		$data['campaigns'] = $this->recruitment_model->get_rec_campaign($id);
		$data['campaign_file'] = $this->recruitment_model->get_campaign_file($id);
		$data['departments'] = $this->departments_model->get();
		$data['rec_channel_form'] = $this->recruitment_model->get_recruitment_channel($data['campaigns']->rec_channel_form_id);
		return $this->template->rander('Recruitment\Views/recruitment_campaign/campaign_preview', $data);
	}

	/**
	 * campaign file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return
	 */
	public function campaign_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = get_staff_image(get_staff_user_id1());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Recruitment\Views/recruitment_campaign/_file', $data);
	}

	/**
	 * delete campaign attachment
	 * @param  int $id
	 * @return
	 */
	public function delete_campaign_attachment($attachment_id) {

		$file = $this->recruitment_model->get_campaign_attachments('', $attachment_id);
		echo json_encode([
			'success' => $this->recruitment_model->delete_campaign_attachment($attachment_id),
		]);

	}

	/**
	 * [iew recruitment campaign
	 * @param  string $id 
	 * @return [type]     
	 */
	public function view_recruitment_campaign($id = '')
	{
		$data['campaigns'] = $this->recruitment_model->get_rec_campaign($id);
		$data['campaign_attachment'] = $this->recruitment_model->get_campaign_file($id);
		$data['campaign_id'] = $id;
		$data['rec_channel_form'] = $this->recruitment_model->get_recruitment_channel($data['campaigns']->rec_channel_form_id);

		$data['title'] = app_lang('recruitment_proposal');
		return $this->template->rander('Recruitment\Views/recruitment_campaign/view_recruitment_campaign', $data);
	}

	/**
	 * candidate profile
	 * @return view
	 */
	public function candidate_profile() {
		if ($this->request->getGet('kanban')) {
			$this->switch_kanban(0, true);
		}

		$data['switch_kanban'] = false;
		$data['bodyclass']     = 'tasks-page';


		$session = \Config\Services::session();
        $candidate_profile_kanban_view = $session->has("candidate_profile_kanban_view");
		if ($candidate_profile_kanban_view && $session->get('candidate_profile_kanban_view') == 'true') {
			$data['switch_kanban'] = true;
			$data['bodyclass']     = 'tasks-page kan-ban-body';
		}
		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();
		
		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['skills'] = $this->recruitment_model->get_skill();
		$data['job_titles'] = $this->recruitment_model->get_job_position();
		$data['company_list'] = $this->recruitment_model->get_company();
		$data['title'] = app_lang('candidate_profile');
		return $this->template->rander('Recruitment\Views/candidate_profile/candidate_profile', $data);
	}

	/**
	 * candidates
	 * @param  int $id
	 * @return
	 */
	public function candidates($id = '') {
		if ($id != '') {
			$data['candidate'] = $this->recruitment_model->get_candidates($id);
			$data['title'] = $data['candidate']->candidate_name.' '.$data['candidate']->last_name;
		} else {
			$data['title'] = app_lang('new_candidate');
		}

		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();
		$data['skills'] = $this->recruitment_model->get_skill();
		$data['candidate_code_default'] = $this->recruitment_model->create_code('candidate_code');

		return $this->template->rander('Recruitment\Views/candidate_profile/candidate', $data);
	}

	/**
	 * add update candidate
	 * @param int $id
	 */
	public function add_update_candidate($id = '') {

		$data = $this->request->getPost();
		if ($data) {
			if ($id == '') {
				$ids = $this->recruitment_model->add_candidate($data);
				if ($ids) {
					handle_rec_candidate_file($ids);
					handle_rec_candidate_avar_file($ids);
					
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect(('recruitment/candidate_profile'));
			} else {
				$success = $this->recruitment_model->update_cadidate($data, $id);
				if ($success == true) {
					handle_rec_candidate_file($id);
					handle_rec_candidate_avar_file($id);
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect(('recruitment/candidate_profile'));
			}
		}
	}

	/**
	 * table candidates
	 * @return
	 */
	public function table_candidates() {
		$dataPost = $this->request->getPost();
		$this->recruitment_model->get_table_data(module_views_path('Recruitment', 'candidate_profile/table_candidates'), $dataPost);
	}

	/**
	 * change status campaign
	 * @param  int $status
	 * @param  int $cp_id
	 * @return
	 */
	public function change_status_campaign($status, $cp_id) {
		$change = $this->recruitment_model->change_status_campaign($status, $cp_id);
		if ($change == true) {

			$message = app_lang('change_status_campaign') . ' ' . app_lang('successfully');
			echo json_encode([
				'result' => $message,
			]);
		} else {
			$message = app_lang('change_status_campaign') . ' ' . app_lang('fail');
			echo json_encode([
				'result' => $message,
			]);
		}

	}

	/**
	 * candidate code exists
	 * @return
	 */
	public function candidate_code_exists() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				// First we need to check if the email is the same
				$candidate = $this->request->getPost('candidate');
				if ($candidate != '') {
					$this->db->where('id', $candidate);
					$cd = $this->db->get('tblrec_candidate')->row();
					if ($cd->candidate_code == $this->request->getPost('candidate_code')) {
						echo json_encode(true);
						die();
					}
				}
				$this->db->where('candidate_code', $this->request->getPost('candidate_code'));
				$total_rows = $this->db->count_all_results('tblrec_candidate');
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
	 * candidate email exists
	 * @return
	 */
	public function candidate_email_exists()
    {
    	if(is_numeric($this->request->getPost('id'))){
    		/*edit*/
    		$builder = db_connect('default');
    		$builder = $builder->table(get_db_prefix().'rec_candidate');
    		$builder->where('id', $this->request->getPost('id'));
    		$user = $builder->get()->getRow();

    		if($user->email == $this->request->getPost('email')){
    			echo json_encode(array("success" => true, 'message' => app_lang('email not exist')));
    			die;
    		}else{
    			$builder = db_connect('default');
    			$builder = $builder->table(get_db_prefix().'rec_candidate');
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

    		if ($this->recruitment_model->is_candidate_email_exists($this->request->getPost('email'))) {
    			echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
    			die;
    		}else{
    			echo json_encode(array("success" => true, 'message' => app_lang('email not exist')));
    			die;
    		}
    	}
    }

	/**
	 * interview schedule
	 * @param  int $id
	 * @return view
	 */
	public function interview_schedule($id = '') {
		$data['candidates'] = $this->recruitment_model->get_candidates('', 'status != 9');
		$data['list_cd'] = $this->recruitment_model->get_list_cd();
		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign('', 'cp_status = 3');
		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['interview_id'] = $id;
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		$data['from_date_filter'] = format_to_date(date('Y-m-d', strtotime( date('Y-m-d') . "-7 day")), false);
		$data['title'] = app_lang('interview_schedule');

		return $this->template->rander('Recruitment\Views/interview_schedule/interview_schedule', $data);
	}

	/**
	 * get candidate infor change
	 * @param  object $candidate
	 * @return json
	 */
	public function get_candidate_infor_change($candidate) {
		$infor = $this->recruitment_model->get_candidates($candidate);
		echo json_encode([
			'email' => $infor->email,
			'phonenumber' => $infor->phonenumber,

		]);
	}

	/**
	 * interview schedules
	 * @return redirect
	 */
	public function interview_schedules() {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$id = $this->recruitment_model->add_interview_schedules($data);
				if ($id) {
					
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));

				}
				app_redirect(('recruitment/interview_schedule'));
			} else {
				$id = $data['id'];
				unset($data['id']);
				$success = $this->recruitment_model->update_interview_schedules($data, $id);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect(('recruitment/interview_schedule'));
			}
			die;
		}
	}

	/**
	 * deletecandidate
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_candidate() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('recruitment/candidate_profile'));
		}
		$response = $this->recruitment_model->delete_candidate($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));

		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));

		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));

		}
		app_redirect(('recruitment/candidate_profile'));
	}

	/**
	 * table interview
	 * @return
	 */
	public function table_interview() {
		$dataPost = $this->request->getPost();
		$this->recruitment_model->get_table_data(module_views_path('Recruitment', 'interview_schedule/table_interview'), $dataPost);
	}

	/**
	 * view interview schedule
	 * @param  string $id 
	 * @return [type]     
	 */
	public function view_interview_schedule($id = '')
	{
		$data['id'] = $id;
		$data['intv_sch'] = $this->recruitment_model->get_interview_schedule($id);
		$data['activity_log'] = $this->recruitment_model->re_get_activity_log($id,'rec_interview');

		$data['title'] = app_lang('interview_schedule_detail');
		return $this->template->rander('Recruitment\Views/interview_schedule/view_interview_schedule', $data);
	}

	/**
	 * candidate
	 * @param  int $id
	 * @return view
	 */
	public function candidate($id) {
		if($this->request->getGet('tab')){
			$data['tab'] = $this->request->getGet('tab');
		}else{
			$data['tab'] = 'applied_job';
		}

		$data['title'] = app_lang('candidate_detail');

		$data['candidate'] = $this->recruitment_model->get_candidates($id);
		$data['skill_name'] ='';

		if($data['candidate']){
			if($data['candidate']->skill){
				$skill_array = explode(',', $data['candidate']->skill);
				foreach ($skill_array as $value) {
					if($value){
						$skill_value = $this->recruitment_model->get_skill($value);
						if($skill_value){
							$data['skill_name'] .= $skill_value->skill_name.', ';
						}
					}

				}
			}
		}



		if ($data['candidate']->rec_campaign > 0) {
			$campaign = $this->recruitment_model->get_rec_campaign($data['candidate']->rec_campaign);
			if($campaign){
				$data['evaluation'] = $this->recruitment_model->get_evaluation_form_by_position($campaign->cp_position);
			}else{
				$data['evaluation'] = '';
			}

		} else {
			$data['evaluation'] = '';
		}

		$data['list_interview'] = $this->recruitment_model->get_interview_by_candidate($id);
		$data['cd_evaluation'] = $this->recruitment_model->get_cd_evaluation($id);
		$data['assessor'] = '';
		$data['feedback'] = '';
		$data['evaluation_date'] = '';
		$data['avg_score'] = 0;
		$data['data_group'] = [];
		$rs_evaluation = [];
		if (count($data['cd_evaluation']) > 0) {
			$data['assessor'] = $data['cd_evaluation'][0]['assessor'];
			$data['feedback'] = $data['cd_evaluation'][0]['feedback'];
			$data['evaluation_date'] = $data['cd_evaluation'][0]['evaluation_date'];

			foreach ($data['cd_evaluation'] as $eval) {
				$data['avg_score'] += ($eval['rate_score'] * $eval['percent']) / 100;

				if (!isset($rs_evaluation[$eval['group_criteria']])) {
					$rs_evaluation[$eval['group_criteria']]['toltal_percent'] = 0;
					$rs_evaluation[$eval['group_criteria']]['result'] = 0;
				}
				$rs_evaluation[$eval['group_criteria']]['toltal_percent'] += $eval['percent'];
				$rs_evaluation[$eval['group_criteria']]['result'] += ($eval['rate_score'] * $eval['percent']) / 100;
			}

			$data['data_group'] = $rs_evaluation;

		}

		return $this->template->rander('Recruitment\Views/candidate_profile/candidate_detail', $data);
	}

	/**
	 * candidate file
	 * @param  int $id
	 * @param  int $rel_id
	 * @return view
	 */
	public function candidate_file($id, $rel_id) {
		$data['discussion_user_profile_image_url'] = get_staff_image(get_staff_user_id1());
		$data['current_user_is_admin'] = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->view('Recruitment\Views/candidate_profile/_file', $data);
	}


	/**
	 * deletec andidate attachment
	 * @param  int $id
	 * @return
	 */
	public function delete_candidate_attachment($attachment_id) {

		$file = $this->recruitment_model->get_candidate_attachments('', $attachment_id);
		if ($file && $file->staffid == get_staff_user_id1() || is_admin()) {
			echo json_encode([
				'success' => $this->recruitment_model->delete_candidate_attachment($attachment_id),
			]);
		}else{
			echo json_encode([
				'success' => app_lang('access_denied'),
			]);
		}

	}

	/**
	 * care candidate
	 * @return json
	 */
	public function care_candidate() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			$id = $this->recruitment_model->add_care_candidate($data);
			if ($id) {
				$mess = app_lang('care_candidate_success');
				echo json_encode([
					'mess' => $mess,
				]);
			} else {
				$mess = app_lang('care_candidate_fail');
				echo json_encode([
					'mess' => $mess,
				]);
			}

		}
	}

	/**
	 * rating candidate
	 * @return json
	 */
	public function rating_candidate() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			$id = $this->recruitment_model->rating_candidate($data);
			if ($id == true) {
				$mess = app_lang('rating_candidate_success');
				echo json_encode([
					'mess' => $mess,
					'rate' => $data['rating'],
				]);
			} else {
				$mess = app_lang('rating_candidate_fail');
				echo json_encode([
					'mess' => $mess,
					'rate' => 0,
				]);
			}
		}
	}

	/**
	 * send mail candidate
	 * @return redirect
	 */
	public function send_mail_candidate() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$rs = $this->recruitment_model->send_mail_candidate($data);
			$this->session->setFlashdata("success_message", app_lang("send_mail_successfully"));
			
			app_redirect(('recruitment/candidate/' . $data['candidate']));
		}
	}

	/**
	 * send mail list candidate
	 * @return redirect
	 */
	public function send_mail_list_candidate() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			foreach ($data['candidate'] as $cd) {
				$cdate = $this->recruitment_model->get_candidates($cd);
				$data['email'][] = $cdate->email;
			}
			$rs = $this->recruitment_model->send_mail_list_candidate($data);
			$this->session->setFlashdata("success_message", app_lang("send_mail_successfully"));
			app_redirect(('recruitment/candidate_profile'));

		}
	}

	/**
	 * check time interview
	 * @return json
	 */
	public function check_time_interview() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if ($data['candidate'] != '') {
				if ($data['interview_day'] == '' || $data['from_time'] == '' || $data['to_time'] == '') {
					$rs = app_lang('please_enter_the_full_interview_time');
					echo json_encode([
						'return' => true,
						'rs' => $rs,
					]);
				} elseif ($data['interview_day'] != '' && $data['from_time'] != '' && $data['to_time'] != '') {

					$check = $this->recruitment_model->check_candidate_interview($data);

					if (count($check) > 0) {
						$rs = app_lang('check_candidate_interview_1');
						echo json_encode([
							'return' => true,
							'rs' => $rs,
						]);
					} else {
						echo json_encode([
							'return' => false,
						]);
					}

				}
			}

		}
	}

	/**
	 * [get_candidate_edit_interview description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function get_candidate_edit_interview($id) {
		$list_cd = $this->recruitment_model->get_list_candidates_interview($id);
		$cd = $this->recruitment_model->get_candidates();
		$html = '';
		$count = 0;
		$total_candidate = 0;
		foreach ($list_cd as $l) {
			if ($count == 0) {
				$class = 'success';
				$class_btn = 'new_candidates';
				$i = 'check-circle';
			} else {
				$class_btn = 'remove_candidates';
				$class = 'danger';
				$i = 'x';
			}
			$html .= '<div class="row col-md-12" id="candidates-item">
			<div class="col-md-4 form-group select_candidate_class2">
			<select name="candidate[' . $count . ']" onchange="candidate_infor_change(this); return false;" id="candidate[' . $count . ']" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="' . app_lang('ticket_settings_none_assigned') . '" required>
			<option value=""></option>';
			foreach ($cd as $s) {
				$attr = '';
				if ($s['id'] == $l['candidate']) {
					$attr = 'selected';
				}
				$html .= '<option value="' . $s['id'] . '" ' . $attr . ' >' . $s['candidate_code'] . ' ' . $s['candidate_name']. ' ' . $s['last_name'] . '</option>';
			}
			$html .= '</select>
			</div>

			<div class="col-md-3">
			<label id="email'. $count .'">'.$l['email'].'</label><br/>
			<label id="phonenumber'. $count .'">'.$l['phonenumber'].'</label>
			</div>

			<div class="col-md-4">
			'. render_input1('cd_from_hours['.$count.']', '', date("H:i", strtotime($l['cd_from_hours'])), 'time', ['placeholder' => 'from_time'], [],'', 'cd_from_time').'

			'. render_input1('cd_to_hours['.$count.']', '', date("H:i", strtotime($l['cd_to_hours'])), 'time', ['placeholder' => 'from_time'], [],'', 'cd_from_time').'
			</div>

			<div class="col-md-1 lightheight-34-nowrap">
			<span class="input-group-btn pull-bot">
			<button name="add" class="btn ' . $class_btn . ' btn-' . $class . ' border-radius-4" data-ticket="true" type="button"><span data-feather="'.$i.'" class="icon-16" ></span></button>
			</span>
			</div>
			</div>';
			$count++;
			$total_candidate++;
		}
		echo json_encode([
			'html' => $html,
			'total_candidate' => $total_candidate,
			'custom_fields_html' => '',
		]);
	}

	/**
	 * delete interview schedule
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_interview_schedule() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('recruitment/interview_schedule'));
		}
		$response = $this->recruitment_model->delete_interview_schedule($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect(('recruitment/interview_schedule'));
	}

	/**
	 * get interview data ajax
	 * @param  int $id
	 * @return view
	 */
	public function get_interview_data_ajax($id) {
		$data['id'] = $id;
		$data['intv_sch'] = $this->recruitment_model->get_interview_schedule($id);
		$data['activity_log'] = $this->recruitment_model->re_get_activity_log($id,'rec_interview');

		return $this->template->rander('Recruitment\Views/interview_schedule/intv_sch_preview', $data);
	}

	/**
	 * evaluation criterias
	 * @return [type] 
	 */
	public function evaluation_criterias() {
		$data['evaluation_criterias'] = $this->recruitment_model->get_group_evaluation_criteria();
		return $this->template->rander("Recruitment\Views\includes\\evaluation_criteria", $data);
	}
	
	/**
	 * list evaluation criteria data
	 * @return [type] 
	 */
	public function list_evaluation_criteria_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_list_child_criteria();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_evaluation_criteria_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make evaluation criteria row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_evaluation_criteria_row($data) {

		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/evaluation_criteria_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_evaluation_criteria'), "data-post-id" => $data['criteria_id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['criteria_id'], "data-action-url" => get_uri("recruitment/delete_evaluation_criteria/".$data['criteria_id']), "data-action" => "delete-confirmation"));
		}

		$staff_avatar = get_staff_image($data['add_from'], false).get_staff_full_name1($data['add_from']);

		if ($data['criteria_type'] == 'group_criteria') {
			$criteria = '<strong><span data-feather="folder-plus" class="icon-16" ></span> ' . ' ' . $data['criteria_title'] . '</strong>';
		} else {
			$criteria = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $data['criteria_title'];
		}
		
		return array(
			$staff_avatar,
			$criteria,
			app_lang($data['criteria_type']),
			format_to_date($data['add_date']),
			$options
		);
	}

	/**
	 * evaluation criteria modal form
	 * @return [type] 
	 */
	public function evaluation_criteria_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$evaluation_criteria_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['evaluation_criteria_data'] = $this->recruitment_model->get_evaluation_criteria($id);
		}else{
			$id = '';
		}

		$data['id'] = $id;
		$data['list_group'] = $this->recruitment_model->get_group_evaluation_criteria();

		return $this->template->view('Recruitment\Views\includes\modal_forms\evaluation_criteria_modal', $data);
	}

	/**
	 * evaluation criteria
	 * @return redirect
	 */
	public function evaluation_criteria($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->recruitment_model->add_evaluation_criteria($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/evaluation_criterias');
			} else {
				
				$success = $this->recruitment_model->update_evaluation_criteria($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/evaluation_criterias');
			}
			die;
		}
	}

	/**
	 * delete evaluation criteria
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_evaluation_criteria($id) {
		if (!$id) {
			app_redirect(('recruitment/evaluation_criterias'));
		}
		$response = $this->recruitment_model->delete_evaluation_criteria($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * get criteria by group
	 * @param  int $id
	 * @return json
	 */
	public function get_criteria_by_group($id) {
		$list = $this->recruitment_model->get_criteria_by_group($id);
		$html = '<option value=""></option>';
		foreach ($list as $li) {
			$html .= '<option value="' . $li['criteria_id'] . '">' . $li['criteria_title'] . '</option>';
		}
		echo json_encode([
			'html' => $html,
		]);
	}

	/**
	 * evaluation forms
	 * @return [type] 
	 */
	public function evaluation_forms() {
		$data['evaluation_forms'] = $this->recruitment_model->get_list_evaluation_form();
		return $this->template->rander("Recruitment\Views\includes\\evaluation_form", $data);
	}
	
	/**
	 * list evaluation form data
	 * @return [type] 
	 */
	public function list_evaluation_form_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_list_evaluation_form();

		$result = array();
		foreach ($list_data as $data) {

			$result[] = $this->_make_evaluation_form_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make evaluation criteria row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_evaluation_form_row($data) {

		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_view_global")){
			$options .= '<a href="' . site_url('recruitment/evaluation_form_detail/' . $data['form_id'] ).'" class=""><span data-feather="eye" class="icon-16"></span> </a>';
		}
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/evaluation_form_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_evaluation_form'), "data-post-id" => $data['form_id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['form_id'], "data-action-url" => get_uri("recruitment/delete_evaluation_form/".$data['form_id']), "data-action" => "delete-confirmation"));
		}

		$staff_avatar = get_staff_image($data['add_from'], false).get_staff_full_name1($data['add_from']);

		$get_rec_position_name = get_rec_position_name($data['position']);
		if($get_rec_position_name != ''){
			$rec_position_name = $get_rec_position_name;
		}else{
			$rec_position_name = app_lang('all');
		}
		
		return array(
			$staff_avatar,
			$data['form_name'],
			$rec_position_name,
			format_to_date($data['add_date']),
			$options
		);
	}

	/**
	 * evaluation criteria modal form
	 * @return [type] 
	 */
	public function evaluation_form_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$evaluation_form_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['evaluation_form_data'] = $this->recruitment_model->get_list_evaluation_form($id);
		}else{
			$id = '';
		}

		$data['id'] = $id;
		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['list_group'] = $this->recruitment_model->get_group_evaluation_criteria();

		return $this->template->view('Recruitment\Views\includes\modal_forms\evaluation_form_modal', $data);
	}

	/**
	 * evaluation form
	 * @return redirect
	 */
	public function evaluation_form($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->recruitment_model->add_evaluation_form($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/evaluation_form_detail/'.$id);
			} else {
				$success = $this->recruitment_model->update_evaluation_form($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/evaluation_form_detail/'.$id);
			}
			die;
		}
	}

	/**
	 * delete evaluation form
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_evaluation_form($id) {
		if (!$id) {
			app_redirect('recruitment/evaluation_forms');
		}
		$response = $this->recruitment_model->delete_evaluation_form($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * get list criteria edit
	 * @param  int $id
	 * @return json
	 */
	public function get_list_criteria_edit($id) {
		$list = $this->recruitment_model->get_list_criteria_edit($id);
		echo json_encode([
			'html' => $list['html'],
			'group_criteria' => $list['group_criteria'],
			'evaluation_criteria' => $list['evaluation_criteria'],
		]);
	}

	/**
	 * change status candidate
	 * @param  int $status
	 * @param  int $id
	 * @return json
	 */
	public function change_status_candidate($status, $id) {
		$change = $this->recruitment_model->change_status_candidate($status, $id);
		if ($change == true) {

			$message = app_lang('change_status_campaign') . ' ' . app_lang('successfully');
			echo json_encode([
				'result' => $message,
			]);
		} else {
			$message = app_lang('change_status_campaign') . ' ' . app_lang('fail');
			echo json_encode([
				'result' => $message,
			]);
		}
	}

	/**
	 * change send to
	 * @param  int $type
	 * @return json
	 */
	public function change_send_to($type) {
		if ($type == 'staff') {
			$options = array(
				"status" => "active",
				"user_type" => "staff",
			);
			$staff = $this->Users_model->get_details($options)->getResultArray();

			echo json_encode([
				'type' => $type,
				'list' => $staff,
			]);
		} elseif ($type == 'department') {

			$department_options = array(
				"deleted" => 0,
			);
			$dpm = $this->Team_model->get_details($department_options)->getResultArray();

			echo json_encode([
				'type' => $type,
				'list' => $dpm,
			]);
		}
	}

	/**
	 * on boardings
	 * @return [type] 
	 */
	public function on_boardings() {
		$data['on_boardings'] = $this->recruitment_model->get_list_set_transfer();
		return $this->template->rander("Recruitment\Views\includes/tranfer_personnel", $data);
	}

	/**
	 * list on onboarding data
	 * @return [type] 
	 */
	public function list_on_boarding_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_list_set_transfer();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_on_boarding_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make skill row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_on_boarding_row($data) {
		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/on_boarding_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_setting_tranfer'), "data-post-id" => $data['set_id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['set_id'], "data-action-url" => get_uri("recruitment/delete_setting_tranfer/".$data['set_id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['subject']),
			get_staff_full_name1($data['add_from']),
			format_to_date($data['add_date']),
			$options
		);
	}

	/**
	 * skill modal form
	 * @return [type] 
	 */
	public function on_boarding_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$on_boarding_data = [];


		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['on_boarding_data'] = $this->recruitment_model->get_list_set_transfer($id);
			$data['model_info'] = $this->recruitment_model->get_list_set_transfer($this->request->getPost('id'));
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		$arr_staff = [];
		$arr_dpm = [];
		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$staffs = $this->Users_model->get_details($options)->getResultArray();
		foreach ($staffs as $staff) {
		    $arr_staff[] = [
		    	'name' => $staff['email'],
		    	'label' => $staff['first_name'].' '.$staff['last_name'],
		    ];
		}
		$department_options = array(
			"deleted" => 0,
		);
		$dpms = $this->Team_model->get_details($department_options)->getResultArray();
		foreach ($dpms as $dpm) {
		    $arr_dpm[] = [
		    	'name' => $dpm['id'],
		    	'label' => $dpm['title'],
		    ];
		}
		$data['arr_staff'] = $arr_staff;
		$data['arr_dpm'] = $arr_dpm;

		return $this->template->view('Recruitment\Views\includes\modal_forms\on_boarding_modal', $data);
	}

	/**
	 * setting tranfer
	 * @return redirect
	 */
	public function setting_tranfer($id = '') {

		$data = $this->request->getPost();
		if ($data) {
			$id = $this->request->getPost('id');

			$target_path = SET_TRANSFER_UPLOAD;
			$files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "re_set_transfer");
			$new_files = unserialize($files_data);

			if ($id) {
				$item_info = $this->recruitment_model->get_list_set_transfer($id);
				$timeline_file_path = SET_TRANSFER_UPLOAD;

				$new_files = update_saved_files($timeline_file_path, $item_info->files, $new_files);
			}
			$data["files"] = serialize($new_files);

			if (!$id) {
				if(isset($data['id'])){
					unset($data['id']);
				}
				$result = $this->recruitment_model->add_setting_tranfer($data);
				if ($result) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				} else {
					$this->session->setFlashdata("error_message", app_lang("add_failed"));
				}
				app_redirect("recruitment/on_boardings");

			} else {

				$id = $data['id'];
				if(isset($data['id'])){
					unset($data['id']);
				}
				$result = $this->recruitment_model->update_setting_tranfer($data, $id);

				if ($result) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect("recruitment/on_boardings");
			}
		}
	}

	/**
	 * delete setting tranfer
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_setting_tranfer($id) {
		if (!$id) {
			app_redirect(('recruitment/on_boarding_process'));
		}
		$response = $this->recruitment_model->delete_setting_tranfer($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * transfer to hr
	 * @param  int $candidate
	 * @return view
	 */
	public function transfer_to_hr($candidate) {
		$data['candidate'] = $this->recruitment_model->get_candidates($candidate);
		$data['title'] = app_lang('tranfer_personnels');

		$roles_data = [];
		$role_options = array(
			"deleted" => 0,
		);
		$list_roles = $this->Roles_model->get_details($role_options)->getResultArray();
		foreach ($list_roles as $list_role) {
			$roles_data[$list_role['id']] = $list_role;
		}

		$data['roles'] = $roles_data;

		if(rec_get_status_modules('Hr_profile')){
			$prefix_str = get_setting('staff_code_prefix');
			$next_number = (int) get_setting('staff_code_number');
			$data['staff_code'] = $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);

			$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

			$position_id = '';
			//get job position from recruitment campaign
			if($data['candidate']){
				if(is_numeric($data['candidate']->rec_campaign)){
					$position_id = $this->recruitment_model->check_job_position_exist_hr_records($data['candidate']->rec_campaign);
				}	
			}

			$data['position_id'] = $position_id;
			$data['positions'] = $Hr_profile_model->get_job_position();

		}else{
			$prefix_str = 'EC';
			$next_number = (int)$this->recruitment_model->get_last_staff_id();
			$data['staff_code'] = $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
		}

		return $this->template->rander('Recruitment\Views/candidate_profile/transfer_to_hr', $data);
	}

	/**
	 * transfer hr
	 * @param  int $candidate
	 * @return redirect
	 */
	public function transfer_hr($candidate) {

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$id = $this->recruitment_model->rec_add_staff($data);
			if ($id) {
				$change = $this->recruitment_model->change_status_candidate(9, $candidate);
				if ($change == true) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}

				app_redirect(('recruitment/candidate_profile'));
			}
		}
		app_redirect(('recruitment/candidate_profile'));
	}

	/**
	 * action transfer hr
	 * @param  int $candidate
	 * @return json
	 */
	public function action_transfer_hr($candidate) {
		$this->load->model('departments_model');
		$this->load->model('staff_model');
		$cd = $this->recruitment_model->get_candidates($candidate);
		$step_setting = $this->recruitment_model->get_step_transfer_setting();
		$step = [];
		foreach ($step_setting as $st) {
			$step['id'] = $st['set_id'];
			$step['subject'] = $st['subject'];
			$step['content'] = $st['content'];
			if ($st['send_to'] = 'candidate') {
				$step['email'] = $cd->email;
				$action_step = $this->recruitment_model->action_transfer_hr($step);
			}

			if ($st['send_to'] = 'staff') {
				$step['email'] = $st['email_to'];
				$action_step = $this->recruitment_model->action_transfer_hr($step);
			}

			if ($st['send_to'] = 'department') {
				$dpm = [];
				if (strlen($st['email_to']) == 1) {
					$dpm[] = $st['email_to'];
				} else {
					$dpm[] = explode(',', $st['email_to']);
				}
				$list_mail = [];
				foreach ($dpm as $dp) {
					$dpment = $this->departments_model->get($dp);
					if (isset($dpment->manager_id) && $dpment->manager_id != '') {
						$mng_dpm = $this->staff_model->get($dpment->manager_id);
						if ($mng_dpm != '') {
							$list_mail[] = $mng_dpm->email;
						} else {
							$list_mail[] = '';
						}
					}

				}
				$step['email'] = implode(',', $list_mail);
				$action_step = $this->recruitment_model->action_transfer_hr($step);
			}

		}
		echo json_encode([
			'rs' => app_lang('successful_personnel_file_transfer'),
		]);
	}

	/**
	 * dashboard
	 * @return view
	 */
	public function dashboard() {
		$data['title'] = app_lang('dashboard');

		$data['rec_campaign_chart_by_status'] = json_encode($this->recruitment_model->rec_campaign_chart_by_status());
		$data['rec_plan_chart_by_status'] = json_encode($this->recruitment_model->rec_plan_chart_by_status());
		$data['cp_count'] = $this->recruitment_model->get_rec_dashboard_count();
		$data['upcoming_interview'] = $this->recruitment_model->get_upcoming_interview();
		return $this->template->rander('Recruitment\Views/dashboard', $data);
	}

	/**
	 * get recruitment proposal edit
	 * @param  int $id
	 * @return
	 */
	public function get_recruitment_proposal_edit($id) {
		$list = $this->recruitment_model->get_rec_proposal($id);
		if (isset($list)) {
			$description = $list->job_description;
		} else {
			$description = '';

		}

		$custom_fields_html = render_custom_fields('plan', $id);

		echo json_encode([
			'description' => $description,
			'custom_fields_html' => $custom_fields_html,

		]);
	}

	/**
	 * get recruitment campaign edit
	 * @param  int $id
	 * @return json
	 */
	public function get_recruitment_campaign_edit($id) {
		$list = $this->recruitment_model->get_rec_campaign($id);
		if (isset($list)) {
			$description = $list->cp_job_description;
		} else {
			$description = '';

		}
		$custom_fields_html = render_custom_fields('campaign', $id);
		echo json_encode([
			'description' => $description,
			'custom_fields_html' => $custom_fields_html,

		]);
	}

	/**
	 * get tranfer personnel edit
	 * @param  int $id
	 * @return json
	 */
	public function get_tranfer_personnel_edit($id) {
		$list = $this->recruitment_model->get_list_set_transfer($id);
		//get attachment file
		$tranfer_personnel_file = $this->recruitment_model->get_tranfer_personnel_file($id);

		if (isset($list)) {
			$description = $list->content;
		} else {
			$description = '';

		}
		echo json_encode([
			'description' => $description,
			'htmlfile' => $tranfer_personnel_file['htmlfile'],
		]);
	}

	/**
	 * recruitment channel
	 * @param  int $id
	 * @return view
	 */
	public function recruitment_channel($id = '') {
		if (!re_has_permission("recruitment_can_view_global") && !is_admin()) {
			access_denied('_recruitment_channel');
		}
		$data['rec_channel_id'] = $id;
		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['title'] = app_lang('_recruitment_channel');

		return $this->template->rander('Recruitment\Views/recruitment_channel/manage_recruitment_channel', $data);
	}

	/**
	 * add edit recruitment channel
	 * @param string $id [description]
	 */
	public function add_edit_recruitment_channel($id = '') {

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (!isset($data['recruitment_channel_id'])) {

				if (!re_has_permission("recruitment_can_create") && !is_admin()) {
					access_denied('_recruitment_channel');
				}

				$ids = $this->recruitment_model->add_recruitment_channel($data);
				if ($ids) {
			
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect(('recruitment/recruitment_channel'));
			} else {

				$id = $data['recruitment_channel_id'];

				if (!re_has_permission("recruitment_can_edit") && !is_admin()) {
					access_denied('_recruitment_channel');
				}

				if (isset($data['recruitment_channel_id'])) {
					unset($data['recruitment_channel_id']);
				}

				$success = $this->recruitment_model->update_recruitment_channel($data, $id);
				if ($success == true) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect(('recruitment/recruitment_channel'));
			}
		}

		if ($id != '') {
			/*edit*/
			$data['form'] = $this->recruitment_model->get_recruitment_channel($id);
			$data['formData'] = $data['form']->form_data;
			$data['recruitment_channel_id'] = $id;

		} else {
			/*add*/
			$data['title'] = app_lang('new_candidate');
			$data['formData'] = [];
		}

		$data['languages'] = get_language_list();
		$data['cfields'] = [];

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['members'] = $this->Users_model->get_details($options)->getResultArray();

		$db_fields = [];
		$fields = [
			'candidate_name',
			'last_name',
			'candidate_code',
			'birthday',
			'gender',
			'desired_salary',
			'birthplace',
			'home_town',
			'identification',
			'place_of_issue',
			'marital_status',
			'nation',
			'religion',
			'height',
			'weight',
			'email',
			'phonenumber',
			'company',
			'resident',
			'nationality',
			'zip',
			'introduce_yourself',
			'skype',
			'facebook',
			'linkedin',
			'current_accommodation',
			'position',
			'contact_person',
			'salary',
			'reason_quitwork',
			'job_description',
			'diploma',
			'training_places',
			'specialized',
			'training_form',
			'days_for_identity',
			'year_experience',
			'skill',
			'interests'
		];
		$className = 'form-control';

		foreach ($fields as $f) {
			$_field_object = new \stdClass();
			$type = 'text';
			$subtype = '';
			$class = $className;
			if ($f == 'email') {
				$subtype = 'email';
			} elseif ($f == 'current_accommodation' || $f == 'address') {
				$type = 'textarea';
			} elseif ($f == 'nationality') {
				$type = 'select';
			} elseif ($f == 'marital_status') {
				$type = 'select';
			} elseif ($f == 'gender') {
				$type = 'select';
			} elseif ($f == 'diploma') {
				$type = 'select';
			} elseif ($f == 'days_for_identity') {
				$type = 'text';
				$class .= ' datepicker';
			}elseif ($f == 'birthday') {
				$type = 'text';
				$class .= ' datepicker';
			} elseif ($f == 'position') {
				$type = 'text';
			} elseif ($f == 'year_experience') {
				$type = 'select';
			} elseif ($f == 'skill') {
				$type = 'select';
			} elseif ($f == 'interests') {
				$type = 'textarea';
			}

			if ($f == 'candidate_name') {
				$label = app_lang('first_name');
			} elseif ($f == 'last_name') {
				$label = app_lang('last_name');
			}elseif ($f == 'email') {
				$label = app_lang('re_lead_add_edit_email');
			} elseif ($f == 'phonenumber') {
				$label = app_lang('re_lead_add_edit_phonenumber');
			} elseif ($f == 'candidate_code') {
				$label = app_lang('candidate_code');
			} elseif ($f == 'birthday') {
				$label = app_lang('birthday');
			} elseif ($f == 'gender') {
				$label = app_lang('gender');
			} elseif ($f == 'desired_salary') {
				$label = app_lang('desired_salary');
			} elseif ($f == 'birthplace') {
				$label = app_lang('birthplace');
			} elseif ($f == 'home_town') {
				$label = app_lang('home_town');
			} elseif ($f == 'identification') {
				$label = app_lang('identification');
			} elseif ($f == 'place_of_issue') {
				$label = app_lang('place_of_issue');
			} elseif ($f == 'marital_status') {
				$label = app_lang('marital_status');
			} elseif ($f == 'nationality') {
				$label = app_lang('nationality');
			} elseif ($f == 'nation') {
				$label = app_lang('nation');
			} elseif ($f == 'religion') {
				$label = app_lang('religion');
			} elseif ($f == 'height') {
				$label = app_lang('height');
			} elseif ($f == 'weight') {
				$label = app_lang('weight');
			} elseif ($f == 'introduce_yourself') {
				$label = app_lang('introduce_yourself');
			} elseif ($f == 'skype') {
				$label = app_lang('skype');
			} elseif ($f == 'facebook') {
				$label = app_lang('facebook');
			} elseif ($f == 'linkedin') {
				$label = app_lang('linkedin');
			} elseif ($f == 'resident') {
				$label = app_lang('resident');
			} elseif ($f == 'current_accommodation') {
				$label = app_lang('current_accommodation');
			} elseif ($f == 'position') {
				$label = app_lang('position_in_the_old_company');
			} elseif ($f == 'contact_person') {
				$label = app_lang('contact_person');
			} elseif ($f == 'reason_quitwork') {
				$label = app_lang('reason_quitwork');
			} elseif ($f == 'salary') {
				$label = app_lang('salary');
			} elseif ($f == 'job_description') {
				$label = app_lang('job_description');
			} elseif ($f == 'diploma') {
				$label = app_lang('diploma');
			} elseif ($f == 'training_places') {
				$label = app_lang('training_places');
			} elseif ($f == 'specialized') {
				$label = app_lang('specialized');
			} elseif ($f == 'training_form') {
				$label = app_lang('training_form');
			} elseif ($f == 'diploma') {
				$label = app_lang('diploma');
			} elseif ($f == 'days_for_identity') {
				$label = app_lang('days_for_identity');
			} elseif ($f == 'year_experience') {
				$label = app_lang('experience');
			} elseif($f == 'skill'){
				$label = app_lang('skill');
			} elseif($f == 'interests'){
				$label = app_lang('interests');
			} elseif($f == 'company'){
				$label = app_lang('re_company');
			}elseif($f == 'zip'){
				$label = app_lang('re_zip');
			}else {
				$label = app_lang('lead_' . $f);
			}

			$field_array = [
				'subtype' => $subtype,
				'type' => $type,
				'label' => $label,
				'className' => $class,
				'name' => $f,
			];

			if ($f == 'nationality') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];

			}

			if ($f == 'skill') {
				$field_array['values'] = [];

				
				$field_array['multiple'] = true;

				$skills = $this->recruitment_model->get_skill();
				foreach ($skills as $skill) {
					$selected = false;
					
					{
						array_push($field_array['values'], [
							'label' => $skill['skill_name'],
							'value' => (int) $skill['id'],
							'selected' => $selected,
						]);

					}
				}
			}

			if ($f == 'marital_status') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];
				array_push($field_array['values'], [
					'label' => app_lang('single'),
					'value' => 'single',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('married'),
					'value' => 'married',
					'selected' => false,
				]);
			}
			if ($f == 'gender') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];
				array_push($field_array['values'], [
					'label' => app_lang('male'),
					'value' => 'male',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('female'),
					'value' => 'female',
					'selected' => false,
				]);
			}
			if ($f == 'diploma') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => '',
					'value' => '',
					'selected' => false,
				];

				array_push($field_array['values'], [
					'label' => app_lang('primary_level'),
					'value' => 'primary_level',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('intermediate_level'),
					'value' => 'intermediate_level',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('college_level'),
					'value' => 'college_level',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('masters'),
					'value' => 'masters',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('doctor'),
					'value' => 'doctor',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('bachelor'),
					'value' => 'bachelor',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('engineer'),
					'value' => 'engineer',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('university'),
					'value' => 'university',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('intermediate_vocational'),
					'value' => 'intermediate_vocational',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('college_vocational'),
					'value' => 'college_vocational',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('in-service'),
					'value' => 'in-service',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('high_school'),
					'value' => 'high_school',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('intermediate_level_pro'),
					'value' => 'intermediate_level_pro',
					'selected' => false,
				]);
			}
			if ($f == 'year_experience') {
				$field_array['values'] = [];

				$field_array['values'][] = [
					'label' => app_lang('no_experience_yet'),
					'value' => 'no_experience_yet',
					'selected' => false,
				];
				array_push($field_array['values'], [
					'label' => app_lang('less_than_1_year'),
					'value' => 'less_than_1_year',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('1_year'),
					'value' => '1_year',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('2_years'),
					'value' => '2_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('3_years'),
					'value' => '3_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('4_years'),
					'value' => '4_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('5_years'),
					'value' => '5_years',
					'selected' => false,
				]);
				array_push($field_array['values'], [
					'label' => app_lang('over_5_years'),
					'value' => 'over_5_years',
					'selected' => false,
				]);
			}
			if ($f == 'name') {
				$field_array['required'] = true;
			}

			$_field_object->label = $label;
			$_field_object->name = $f;
			$_field_object->fields = [];
			$_field_object->fields[] = $field_array;
			$db_fields[] = $_field_object;
		}

		$data['bodyclass'] = 'web-to-lead-form';
		$data['db_fields'] = $db_fields;
		$data['par_id'] = $id;

		$data['list_rec_campaign'] = $this->recruitment_model->get_rec_campaign();

		$roles_data = [];
		$role_options = array(
			"deleted" => 0,
		);
		$list_roles = $this->Roles_model->get_details($role_options)->getResultArray();
		foreach ($list_roles as $list_role) {
			$roles_data[$list_role['id']] = $list_role;
		}

		$data['roles'] = $roles_data;

		return $this->template->rander('Recruitment\Views/recruitment_channel/recruitment_channel_detail', $data);

	}

	/**
	 * table recruitment channel
	 * @return
	 */
	public function table_recruitment_channel() {
		$dataPost = $this->request->getPost();
		$this->recruitment_model->get_table_data(module_views_path('Recruitment', 'recruitment_channel/table_recruitment_channel'), $dataPost);
	}

	/**
	 * delete recruitment channel
	 * @param  int $id
	 * @return [type]
	 */
	public function delete_recruitment_channel() {
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('recruitment/recruitment_campaign'));
		}

		if (!re_has_permission("recruitment_can_delete") && !is_admin()) {
			access_denied('_recruitment_channel');
		}

		$response = $this->recruitment_model->delete_recruitment_channel($id);

		if ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}

		app_redirect(('recruitment/recruitment_channel'));
	}

	/**
	 * view recruitment plan
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function view_recruitment_channel($id)
	{
		$data['id'] = $id;
		$data['total_cv_form'] = $this->recruitment_model->count_cv_from_recruitment_channel($id, 1);
		$data['recruitment_channel'] = $this->recruitment_model->get_recruitment_channel($id);
		$campaign_name = '';
		if($data['recruitment_channel']){
			$rec_campaign = $this->recruitment_model->get_rec_campaign($data['recruitment_channel']->rec_campaign_id);
			if($rec_campaign){
				$campaign_name =$rec_campaign->campaign_name;
			}
		}
		$data['campaign_name'] = $campaign_name;

		$data['title'] = app_lang('recruitment_channel');
		return $this->template->rander('Recruitment\Views/recruitment_channel/recruitment_channel_preview', $data);
	}

	/**
	 * add candidate form recruitment channel
	 * @param redirect
	 */
	public function add_candidate_form_recruitment_channel($form_key) {
		$data = $this->request->getPost();
		if ($data) {
			$ids = $this->recruitment_model->add_candidate_forms($data, $form_key);
			if ($ids) {
				handle_rec_candidate_file_form($ids);
				handle_rec_candidate_avar_file($ids);
				
				$this->session->setFlashdata("success_message", app_lang("added_successfully"));

				app_redirect(site_url('recruitment/forms/wtl/' . $form_key));
			}
		}
	}


	/**
	 * calendar interview schedule
	 * @return view 
	 */
	public function calendar_interview_schedule(){

		$options = array(
			"status" => "active",
			"user_type" => "staff",
		);
		$data['staffs'] = $this->Users_model->get_details($options)->getResultArray();

		$data['candidates'] = $this->recruitment_model->get_candidates();
		$data['list_cd'] = $this->recruitment_model->get_list_cd();
		$data['rec_campaigns'] = $this->recruitment_model->get_rec_campaign();

		$data['title'] = app_lang('interview_schedule');

		$data['google_calendar_api']  = get_option('google_calendar_api_key');
		$data['title']                = app_lang('calendar');
		add_calendar_assets();
		return $this->template->rander('Recruitment\Views/interview_schedule/calendar', $data);
	}

	/**
	 * get calendar interview schedule data
	 * @return json 
	 */
	public function get_calendar_interview_schedule_data()
	{
		if ($this->input->is_ajax_request()) {
			$data = $this->recruitment_model->get_calendar_interview_schedule_data(
				$this->request->getPost('start'),
				$this->request->getPost('end'),
				'',
				'',
				$this->request->getPost()
			);
			echo json_encode($data);
			die();
		}
	}

	/**
	 * switch kanban, recruitment switch kan ban
	 * @param  integer $set    
	 * @param  boolean $manual 
	 * @return redirect         
	 */
	public function switch_kanban($set = 0, $manual = false)
	{
		if ($set == 1) {
			$set = 'false';
		} else {
			$set = 'true';
		}

		$data_new['candidate_profile_kanban_view'] = $set;
		$session = \Config\Services::session();
		$session->set($data_new);

		if ($manual == false) {
			// clicked on VIEW KANBAN from projects area and will redirect again to the same view
			if (strpos($_SERVER['HTTP_REFERER'], 'project_id') !== false) {
				app_redirect(('tasks'));
			} else {
				app_redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	/**
	 * kanban
	 * @return [type] 
	 */
	public function kanban()
	{	
		echo html_entity_decode($this->template->rander('Recruitment\Views/candidate_profile/kan_ban', [], true));
	}

	/**
	 * recruitment tasks kanban load more
	 * 
	 */
	public function recruitment_kanban_load_more()
	{
		$status = $this->request->getGet('status');
		$page   = $this->request->getGet('page');

		$candidates = $this->recruitment_model->do_kanban_query($status, $this->request->getGet('search'), $page, false, []);

		foreach ($candidates as $candidate) {
			return $this->template->rander('Recruitment\Views/candidate_profile/_kan_ban_card', [
				'candidate'   => $candidate,
				'status' => $status,
			]);
		}
	}


	/**
	 * candidate change status
	 * @param  integer $status 
	 * @param  integer $id     
	 *          
	 */
	public function candidate_change_status($status, $id)
	{
		$change = $this->recruitment_model->change_status_candidate($status, $id);
		if ($change == true) {

			$message = app_lang('change_status_campaign') . ' ' . app_lang('successfully');
			echo json_encode([
				'success'=> 'true',
				'message' => $message,
			]);

		} else {
			$message = app_lang('change_status_campaign') . ' ' . app_lang('fail');
			echo json_encode([
				'success'=>'false',
				'message' => $message,
			]);
		}
	}

	/**
	 * skills
	 * @return [type] 
	 */
	public function skills() {
		$data['skills'] = $this->recruitment_model->get_skill();
		return $this->template->rander("Recruitment\Views\includes\skills", $data);
	}

	/**
	 * list skill data
	 * @return [type] 
	 */
	public function list_skill_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_skill();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_skill_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make skill row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_skill_row($data) {
		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/skill_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_skill'), "data-post-id" => $data['id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("recruitment/delete_skill/".$data['id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['id']),
			nl2br($data['skill_name']),
			$options
		);
	}

	/**
	 * skill modal form
	 * @return [type] 
	 */
	public function skill_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$skill_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['skill_data'] = $this->recruitment_model->get_skill($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		return $this->template->view('Recruitment\Views\includes\modal_forms\skill_modal', $data);
	}

	/**
	 * skill
	 * @return redirect
	 */
	public function skill($id = '') {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->recruitment_model->add_skill($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/skills');
			} else {
				$success = $this->recruitment_model->update_skill($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/skills');
			}
			die;
		}
	}

	/**
	 * delete job_position
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_skill($id) {
		if (!$id) {
			app_redirect(('recruitment/skills'));
		}
		$response = $this->recruitment_model->delete_skill($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	 /**
	 * get position fill data
	 * @return html 
	 */
	 public function get_position_fill_data()
	 {
	 	$data = $this->request->getPost();

	 	$position = $this->recruitment_model->list_position_by_campaign($data['campaign']);

	 	echo json_encode([
	 		'position' => $position
	 	]);

	 }

	 /**
	 * recruitment campaign setting
	 * @return  json
	 */
	 public function recruitment_campaign_setting(){
	 	$data = $this->request->getPost();
	 	if($data != 'null'){
	 		$value = $this->recruitment_model->recruitment_campaign_setting($data);
	 		if($value){
	 			$success = true;
	 			$message = app_lang('updated_successfully');
	 		}else{
	 			$success = false;
	 			$message = app_lang('updated_false');
	 		}
	 		echo json_encode([
	 			'message' => $message,
	 			'success' => $success,
	 		]);
	 		die;
	 	}
	 }

	/**
	 * companies
	 * @return [type] 
	 */
	public function companies() {
		$data['companies'] = $this->recruitment_model->get_company();
		return $this->template->rander("Recruitment\Views\includes\company_list", $data);
	}

	/**
	 * list company data
	 * @return [type] 
	 */
	public function list_company_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_company();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_company_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make skill row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_company_row($data) {
		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/company_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_company'), "data-post-id" => $data['id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("recruitment/delete_company/".$data['id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['company_name']),
			nl2br($data['company_address']),
			nl2br($data['company_industry']),
			$options
		);
	}

	/**
	 * skill modal form
	 * @return [type] 
	 */
	public function company_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$company_data = [];


		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['company_data'] = $this->recruitment_model->get_company($id);
			$data['model_info'] = $this->recruitment_model->get_company($this->request->getPost('id'));

			$builder = db_connect('default');
    		$builder = $builder->table(get_db_prefix().'files');
			$builder->where('rel_id', $id);
			$builder->where('rel_type', "rec_company");
			$data['logo'] = $builder->get()->getRow();

		}else{
			$id = '';
		}
		
		$data['id'] = $id;

		return $this->template->view('Recruitment\Views\includes\modal_forms\company_modal', $data);
	}

	/**
	 * company add edit
	 * @param  string $id 
	 * @return json     
	 */
	public function company_add_edit($id = '') {
		$data = $this->request->getPost();
		if ($data) {
			$id = $this->request->getPost('id');

			if (!$id) {

				$result = $this->recruitment_model->add_company($data);
				if ($result) {
					handle_company_attachments($result);

					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				} else {
					$this->session->setFlashdata("error_message", app_lang("add_failed"));
				}
				app_redirect("recruitment/companies");

			} else {

				$id = $data['id'];
				if(isset($data['id'])){
					unset($data['id']);
				}
				$result = $this->recruitment_model->update_company($data, $id);

				handle_company_attachments($id);

				if ($result) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect("recruitment/companies");
			}
		}
	}

	/**
	 * add company attachment
	 * @param integer $id 
	 */
	public function add_company_attachment($id) {

		handle_company_attachments($id);
		echo json_encode([

			'url' => ('recruitment/setting?group=company_list'),
		]);
	}


	/**
	 * get company file url
	 * @param  integer $company_id 
	 * @return json             
	 */
	public function get_company_file_url($company_id) {
		$arr_company_file = $this->recruitment_model->get_company_attachments($company_id);
		/*get images old*/
		$images_old_value = '';

		if (count($arr_company_file) > 0) {
			foreach ($arr_company_file as $key => $value) {
				$images_old_value .= '<div class="dz-preview dz-image-preview image_old' . $value["id"] . '">';

				$images_old_value .= '<div class="dz-image">';
				if (file_exists(RECRUITMENT_COMPANY_UPLOAD . $value["rel_id"] . '/' . $value["file_name"])) {
					$images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/recruitment/uploads/company_images/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
				} else {
					$images_old_value .= '<img class="image-w-h" data-dz-thumbnail alt="' . $value["file_name"] . '" src="' . site_url('modules/purchase/uploads/company/company_images/' . $value["rel_id"] . '/' . $value["file_name"]) . '">';
				}

				$images_old_value .= '</div>';

				$images_old_value .= '<div class="dz-error-mark">';
				$images_old_value .= '<a class="dz-remove" data-dz-remove>Remove file';
				$images_old_value .= '</a>';
				$images_old_value .= '</div>';

				$images_old_value .= '<div class="remove_file">';

				$images_old_value .= '<a href="#" class="text-danger" onclick="delete_company_attachment(this,' . $value["id"] . '); return false;"><i class="fa fa fa-times"></i></a>';

				$images_old_value .= '</div>';

				$images_old_value .= '</div>';
			}
		}

		echo json_encode([
			'arr_images' => $images_old_value,
		]);
		die();

	}

	/**
	 * delete company file
	 * @param  integer $attachment_id 
	 * @return json                
	 */
	public function delete_company_file($attachment_id) {
		if (!re_has_permission("recruitment_can_delete") && !is_admin()) {
			access_denied('recruitment');
		}

		echo json_encode([
			'success' => $this->recruitment_model->delete_company_file($attachment_id),
		]);
	}


	/**
	 * delete company
	 * @param  integer $id 
	 * @return redirect     
	 */
	public function delete_company($id) {
		if (!$id) {
			app_redirect(('recruitment/companies'));
		}
		$response = $this->recruitment_model->delete_company($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * industries
	 * @return [type] 
	 */
	public function industries() {
		$data['skills'] = $this->recruitment_model->get_skill();
		return $this->template->rander("Recruitment\Views\includes\industry_list", $data);
	}

	/**
	 * list skill data
	 * @return [type] 
	 */
	public function list_industry_data() {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_industry();

		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_industry_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make skill row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_industry_row($data) {
		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/industry_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_industry'), "data-post-id" => $data['id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("recruitment/delete_industry/".$data['id']), "data-action" => "delete-confirmation"));
		}
		
		return array(
			nl2br($data['industry_name']),
			$options
		);
	}

	/**
	 * skill modal form
	 * @return [type] 
	 */
	public function industry_modal_form() {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$industry_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$data['industry_data'] = $this->recruitment_model->get_industry($id);
		}else{
			$id = '';
		}
		
		$data['id'] = $id;
		return $this->template->view('Recruitment\Views\includes\modal_forms\industry_modal', $data);
	}

	/**
	 * industry
	 * @return redirect 
	 */
	public function industry($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->recruitment_model->add_industry($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/industries');
			} else {
				
				$success = $this->recruitment_model->update_industry($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/industries');
			}
			die;
		}
	}

	/**
	 * delete job_position
	 * @param  integer $id
	 * @return redirect
	 */
	public function delete_industry($id) {
		if (!$id) {
			app_redirect(('recruitment/industries'));
		}
		$response = $this->recruitment_model->delete_industry($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * generals
	 * @return [type] 
	 */
	public function generals() {
		$data['skills'] = $this->recruitment_model->get_skill();
		return $this->template->rander("Recruitment\Views\includes/recruitment_campaign_setting", $data);
	}

	/**
	 * delete transfer personnal attachment file
	 * @param  [type] $attachment_id 
	 * @return [type]                
	 */
	public function delete_transfer_personnal_attachment_file($attachment_id)
	{
		if (!re_has_permission("recruitment_can_delete") && !is_admin()) {
			access_denied('recruitment');
		}

		$file = $this->misc_model->get_file($attachment_id);
		$result = $this->recruitment_model->delete_transfer_personnal_attachment_file($attachment_id);

		if($result == true){
			$message = app_lang('transfer_personnel_file_s');
		}else{
			$message =  app_lang('transfer_personnel_file_f');
		}

		echo json_encode([
			'message' => $message,
			'success' => $result,
		]);
	}

	/**
	 * re preview transfer personnal file
	 * @param  [type] $id     
	 * @param  [type] $rel_id 
	 * @return [type]         
	 */
	public function re_preview_transfer_personnal_file($id, $rel_id)
	{
		$data['discussion_user_profile_image_url'] = get_staff_image(get_staff_user_id1());
		$data['current_user_is_admin']             = is_admin();
		$data['file'] = $this->recruitment_model->get_file($id, $rel_id);
		if (!$data['file']) {
			header('HTTP/1.0 404 Not Found');
			die;
		}
		return $this->template->rander('Recruitment\Views/recruitment/includes/tranfer_personnel_file', $data);
	}

	/**
	 * get candidate sample
	 * @return [type] 
	 */
	public function get_candidate_sample()
	{
		$data = $this->request->getGet();
		$cd = $this->recruitment_model->get_candidates('', 'status != 9');
		$html = '';
		$total_candidate = 1;
		$count = 0;

		$class = 'success';
		$class_btn = 'new_candidates';
		$i = 'check-circle';
		$select_candidate_class = 'select_candidate_class1';

		if(isset($data['count'])){
			$count = $data['count'];
			$select_candidate_class = 'select_candidate_class2';
		}
		if(isset($data['class'])){
			$class = $data['class'];
		}
		if(isset($data['class_btn'])){
			$class_btn = $data['class_btn'];
		}
		if(isset($data['i'])){
			$i = $data['i'];
		}
		

		$html .= '<div class="row col-md-12" id="candidates-item">
		<div class="col-md-4 form-group '.$select_candidate_class.'">
		<select name="candidate[' . $count . ']" onchange="candidate_infor_change(this); return false;" id="candidate[' . $count . ']" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="' . app_lang('candidate') . '" required>
		<option value=""></option>';
		foreach ($cd as $s) {
			$attr = '';
			$html .= '<option value="' . $s['id'] . '" ' . $attr . ' >' . $s['candidate_code'] . ' ' . $s['candidate_name'] .' '. $s['last_name'] . '</option>';
		}
		$html .= '</select>
		</div>
		<div class="col-md-3">
		<label id="email'. $count .'"></label><br/>
		<label id="phonenumber'. $count .'"></label>
		</div>

		<div class="col-md-4">
		'. render_input1('cd_from_hours['.$count.']', '', '', 'time', ['placeholder' => 'from_time'], [],'', 'cd_from_time').'

		'. render_input1('cd_to_hours['.$count.']', '', '', 'time', ['placeholder' => 'from_time'], [],'', 'cd_from_time').'
		</div>
		<div class="col-md-1 lightheight-34-nowrap">
		<span class="input-group-btn pull-bot">
		<button name="add" class="btn ' . $class_btn . ' btn-' . $class . ' border-radius-4" data-ticket="true" type="button"><span data-feather="'.$i.'" class="icon-16" ></span> </button>
		</span>
		</div>
		</div>';

		echo json_encode([
			'html' => $html,
			'total_candidate' => $total_candidate,
			'custom_fields_html' => '',

		]);
	}

	/**
	 * download candidate profile
	 * @return [type] 
	 */
	function download_candidate_profile() {
		$mode = "download";
		$data = $this->request->getPost();

		$candidate_ids = $data['item_select_print_candidate'];
		$get_candidate_profile = $this->recruitment_model->get_candidate_profile_by_id($candidate_ids);

		$candidate_profile = $get_candidate_profile['candidate'];
		$candidate_literacy = $get_candidate_profile['candidate_literacy'];
		$candidate_experience = $get_candidate_profile['candidate_experience'];
		$cadidate_avatar = $get_candidate_profile['cadidate_avatar'];

		foreach ($candidate_profile as $candidate) {
			$temp_candidate_literacy='';
			$temp_candidate_experience='';
			$temp_cadidate_avatar='';

			if(isset($candidate_literacy[$candidate['id']])){
				$temp_candidate_literacy = $candidate_literacy[$candidate['id']];
			}

			if(isset($candidate_experience[$candidate['id']])){
				$temp_candidate_experience = $candidate_experience[$candidate['id']];
			}

			if(isset($cadidate_avatar[$candidate['id']])){
				$temp_cadidate_avatar = $cadidate_avatar[$candidate['id']];
			}

			$data=[];
			$data['candidate'] =$candidate;
			$data['temp_candidate_literacy'] =$temp_candidate_literacy;
			$data['temp_candidate_experience'] =$temp_candidate_experience;
			$data['cadidate_avatar'] =$temp_cadidate_avatar;
			$data['rec_skill'] =$get_candidate_profile['rec_skill'];
			$data['job_positions'] =$get_candidate_profile['job_positions'];

			prepare_dowload_candidate_pdf($data, $mode);
		}
	}

	/**
	 * re save to dir
	 * @param  [type] $pdf       
	 * @param  [type] $file_name 
	 * @return [type]            
	 */
	private function re_save_to_dir($pdf, $file_name)
	{
		$dir = TEMFOLDER_EXPORT_CANDIDATE;
		
		$dir .= $file_name;

		$pdf->Output($dir, 'F');
	}

	/**
	 * get criteria group
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_criteria_group()
	{
		if ($this->input->is_ajax_request()) {
			$criteria_id = $this->request->getGet('id');
			$group_criteria = $this->request->getGet('group_criteria');
			$status = $this->request->getGet('status');

			if($status == 'edit'){
				$this->db->where('criteria_id !=', $criteria_id);
			}
			$this->db->where('group_criteria', 0);
			$group_criterias = $this->db->get(db_prefix() . 'rec_criteria')->result_array();

			$html = '<option value=""></option>';
			foreach ($group_criterias as $li) {
				$selected = '';
				if($li['criteria_id'] == $group_criteria){
					$selected = ' selected';
				}
				$html .= '<option value="' . $li['criteria_id'] . '" '.$selected.'>' . $li['criteria_title'] . '</option>';
			}
			echo json_encode([
				'html' => $html,
			]);
		}
	}

	/**
	 * duplicate recruitment channel
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function duplicate_recruitment_channel($id)
	{
		$message = '';
		$status = '';

		$result = $this->recruitment_model->duplicate_recruitment_channel($id);
		if($result){
			$message = app_lang('Clone_Recruitment_channel_successful');
			$status = true;
		}else{
			$message = app_lang('Clone_Recruitment_channel_failure');
			$status = false;
		}
		
		echo json_encode([
			'message' => $message,
			'status' => $status,

		]);
	}

	public function re_add_activity()
	{
		$interview_schedule_id = $this->request->getPost('interview_schedule_id');
		if (!re_has_permission("recruitment_can_edit") && !is_admin() && !re_has_permission("recruitment_can_create")) {
			access_denied('recruitment');
		}

		if ($this->request->getPost()) {
			$description = $this->request->getPost('activity');
			$rel_type = $this->request->getPost('rel_type');
			$aId     = $this->recruitment_model->log_re_activity($interview_schedule_id, $rel_type, $description);
			
			if($aId){
				$status = true;
				$message = app_lang('added_successfully');
			}else{
				$status = false;
				$message = app_lang('added_failed');
			}

			echo json_encode([
				'status' => $status,
				'message' => $message,
			]);
		}
	}

	/**
	 * delete activitylog
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_activitylog($id)
	{
		if (!$this->input->is_ajax_request()) {
			show_404();
		}
		
		$delete = $this->recruitment_model->delete_activitylog($id);
		if($delete){
			$status = true;
		}else{
			$status = false;
		}

		echo json_encode([
			'success' => $status,
		]);
	}

	/**
	 * send interview schedule
	 * @param  [type] $interview_id 
	 * @return [type]               
	 */
	public function send_interview_schedule($interview_id)
	{
		if (!re_has_permission("recruitment_can_edit") && !is_admin()) {
			access_denied('recruitment');
		}
		$this->recruitment_model->send_interview_schedule($interview_id);

		$this->session->setFlashdata("success_message", app_lang("The_interview_schedule_has_been_sent_successfully"));

		app_redirect(('recruitment/interview_schedule'));
	}

	/**
	 * get_recruitment_campaign_add
	 * @return [type] 
	 */
	public function get_recruitment_campaign_add() {
		
		$custom_fields_html = render_custom_fields('campaign', 0);
		echo json_encode([
			'custom_fields_html' => $custom_fields_html,

		]);
	}

	/**
	 * get recruitment proposal add
	 * @return [type] 
	 */
	public function get_recruitment_proposal_add() {
		$custom_fields_html = render_custom_fields('plan', 0);

		echo json_encode([
			'custom_fields_html' => $custom_fields_html,

		]);
	}

	/**
     * prefix number
     * @return [type] 
     */
	public function prefix_number() {
		$data = $this->request->getPost();

		if ($data) {
			$success = $this->recruitment_model->update_prefix_number($data);

			if ($success == true) {
				$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
			}

			app_redirect('recruitment/generals');
		}
	}

	/**
	 * upload_file
	 * @return [type] 
	 */
	function upload_file() {
		$this->access_only_team_members();
		upload_file_to_temp();
	}

	/**
	 * validate items file
	 * @return [type] 
	 */
	function validate_items_file() {
		$this->access_only_team_members();
		$file_name = $this->request->getPost("file_name");
		if (!is_valid_file_to_upload($file_name)) {
			echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
			exit();
		}

		if (is_image_file($file_name)) {
			echo json_encode(array("success" => true));
		} else {
			echo json_encode(array("success" => false, 'message' => app_lang('please_upload_valid_image_files')));
		}
	}

	/**
	 * validate onboarding file
	 * @return [type] 
	 */
	function validate_onboarding_file() {
		$this->access_only_team_members();
		$file_name = $this->request->getPost("file_name");
		if (!is_valid_file_to_upload($file_name)) {
			echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
			exit();
		}

		echo json_encode(array("success" => true));
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
			return $this->template->view('Recruitment\Views\includes\confirm_delete_modal_form', $data);
		}
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
		$success = $this->recruitment_model->re_status_mark_as($status, $id, $type);
		$message = '';

		if ($success) {
			$message = app_lang('re_change_status_successfully');
		}
		echo json_encode([
			'success'  => $success,
			'message'  => $message
		]);
	}

	/*ROLE for module Start*/

	//load the role view
	function roles() {
		return $this->template->rander("Recruitment\Views/roles/index");
	}

    //load the role add/edit modal
	function role_modal_form() {

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));

		$view_data['model_info'] = $this->Roles_model->get_one($this->request->getPost('id'));
		$view_data['roles_dropdown'] = array("" => "-") + $this->Roles_model->get_dropdown_list(array("title"), "id");
		return $this->template->view('Recruitment\Views\roles/modal_form', $view_data);
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

			$view_data['recruitment_can_view_global'] = get_array_value($permissions, "recruitment_can_view_global");
			$view_data['recruitment_can_create'] = get_array_value($permissions, "recruitment_can_create");
			$view_data['recruitment_can_edit'] = get_array_value($permissions, "recruitment_can_edit");
			$view_data['recruitment_can_delete'] = get_array_value($permissions, "recruitment_can_delete");

			$view_data['permissions'] = $permissions;

			return $this->template->view("Recruitment\Views/roles/permissions", $view_data);
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

		$permissions['recruitment_can_view_global'] = isset($data['recruitment_can_view_global']) ? $data['recruitment_can_view_global'] : NULL;
		$permissions['recruitment_can_create'] = isset($data['recruitment_can_create']) ? $data['recruitment_can_create'] : NULL;
		$permissions['recruitment_can_edit'] = isset($data['recruitment_can_edit']) ? $data['recruitment_can_edit'] : NULL;
		$permissions['recruitment_can_delete'] = isset($data['recruitment_can_delete']) ? $data['recruitment_can_delete'] : NULL;

        

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

	/*ROLE for module End*/

	/**
	 * evaluation forms
	 * @return [type] 
	 */
	public function evaluation_form_detail($id) {
		$data = [];
		$data['id'] = $id;

		return $this->template->rander("Recruitment\Views\includes\\evaluation_form_detail", $data);
	}
	
	/**
	 * list evaluation form data
	 * @return [type] 
	 */
	public function list_evaluation_form_detail_data($id) {
		$this->access_only_team_members();

		$list_data = $this->recruitment_model->get_list_criteria_by_evaluation_id($id);
		$result = array();
		foreach ($list_data as $data) {
			$result[] = $this->_make_evaluation_form_detail_row($data);
		}
		echo json_encode(array("data" => $result));
	}

	/**
	 * _make evaluation criteria row
	 * @param  [type] $data 
	 * @return [type]       
	 */
	private function _make_evaluation_form_detail_row($data) {

		$options = '';
		if(is_admin() || re_has_permission("recruitment_can_edit")){
			$options .= modal_anchor(get_uri("recruitment/evaluation_detail_form_modal_form/".$data['id'].'/'.$data['evaluation_form']), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('re_add_criteria'), "data-post-id" => $data['id']));
		}
		if(is_admin() || re_has_permission("recruitment_can_delete")){
			$options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data['id'], "data-action-url" => get_uri("recruitment/delete_evaluation_form/".$data['id']), "data-action" => "delete-confirmation"));
		}

		return array(
			$data['criteria_title'],
			$options
		);
	}

	/**
	 * evaluation criteria modal form
	 * @return [type] 
	 */
	public function evaluation_detail_form_modal_form($id, $evaluation_id) {
		$this->access_only_team_members();

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));
		$data = [];
		$evaluation_form_data = [];

		$id = $this->request->getPost('id');
		if($id && is_numeric($id)){
			$evaluation_form_detail_data = $this->recruitment_model->get_evaluation_form_detail($evaluation_id, $id);
			$data['evaluation_form_detail_data'] = $evaluation_form_detail_data;

			if(isset($evaluation_form_detail_data) && count($evaluation_form_detail_data) > 0){
				$group_criteria = $evaluation_form_detail_data[0]['group_criteria'];
				$data['e_group_criteria_data'] = $this->recruitment_model->get_criteria_by_group($group_criteria);
			}

		}else{
			$id = '';
		}

		$data['evaluation_id'] = $evaluation_id;
		$data['id'] = $id;
		$data['positions'] = $this->recruitment_model->get_job_position();
		$data['list_group'] = $this->recruitment_model->get_group_evaluation_criteria();

		return $this->template->view('Recruitment\Views\includes\modal_forms\evaluation_form_detail_modal', $data);
	}

	/**
	 * evaluation form
	 * @return redirect
	 */
	public function evaluation_form_add_criteria($id = '') {
		if ($this->request->getPost()) {
			$message = '';
			$data = $this->request->getPost();
			if (!is_numeric($id)) {
				$id = $this->recruitment_model->add_evaluation_form_detail($data);
				if ($id) {
					$this->session->setFlashdata("success_message", app_lang("added_successfully"));
				}
				app_redirect('recruitment/evaluation_form_detail/'.$data['evaluation_form']);
			} else {
				$success = $this->recruitment_model->update_evaluation_form_detail($data, $id);
				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
				}
				app_redirect('recruitment/evaluation_form_detail/'.$data['evaluation_form']);
			}
			die;
		}
	}

	/**
	 * delete evaluation form
	 * @param  int $id
	 * @return redirect
	 */
	public function delete_evaluation_criteria_form($id) {
		if (!$id) {
			app_redirect('recruitment/evaluation_forms');
		}
		$response = $this->recruitment_model->delete_evaluation_form($id);
		if (is_array($response) && isset($response['referenced'])) {
			echo json_encode(array("success" => false, "message" => app_lang('is_referenced')));
		} elseif ($response == true) {
			echo json_encode(array("success" => true, "message" => app_lang('deleted')));
		} else {
			echo json_encode(array("success" => false, "message" => app_lang('problem_deleting')));
		}
	}

	/**
	 * get evaluation from criteria sample
	 * @return [type] 
	 */
	public function get_evaluation_from_criteria_sample()
	{
		$data = $this->request->getGet();
		$cd = $this->recruitment_model->get_candidates();
		$html = '';
		$total_candidate = 1;
		$count = 0;

		$group_criteria = $data['group_criteria'];
		$group_criteria_data = $this->recruitment_model->get_criteria_by_group($group_criteria);

		$class = 'success';
		$class_btn = 'new_candidates';
		$i = 'check-circle';
		$select_candidate_class = 'select_candidate_class1';

		if(isset($data['count'])){
			$count = $data['count'];
			$select_candidate_class = 'select_candidate_class2';
		}
		if(isset($data['class'])){
			$class = $data['class'];
		}
		if(isset($data['class_btn'])){
			$class_btn = $data['class_btn'];
		}
		if(isset($data['i'])){
			$i = $data['i'];
		}
		

		$html .= '<div id="new_kpi" class="row paddig-top-height-0-75">
		<div class="col-md-7 form-group '.$select_candidate_class.'">
		<select name="evaluation_criteria[' . $count . ']"  id="evaluation_criteria[' . $count . ']" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="' . app_lang('evaluation_criteria') . '" required>
		<option value=""></option>';
		foreach ($group_criteria_data as $s) {
			$attr = '';
			$html .= '<option value="' . $s['criteria_id'] . '" ' . $attr . ' >' . $s['criteria_title'].'</option>';
		}
		$html .= '</select>
		</div>

		<div class="col-md-3">
		'. render_input1('percent['.$count.']', '', '', '', ['min' => 1, 'max' => 100, 'step' => 1], [], '', '', true).'

		</div>
		<div class="col-md-1 lightheight-34-nowrap">
		<span class="input-group-btn pull-bot">
		<button name="add" class="btn ' . $class_btn . ' btn-' . $class . ' border-radius-4" data-ticket="true" type="button"><span data-feather="'.$i.'" class="icon-16" ></span> </button>
		</span>
		</div>
		</div>';

		echo json_encode([
			'html' => $html,
			'total_candidate' => $total_candidate,
			'custom_fields_html' => '',

		]);
	}
}