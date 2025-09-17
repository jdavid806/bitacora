<?php

namespace Recruitment\Controllers;

use App\Controllers\App_Controller;
use App\Controllers\Security_Controller;

class Recruitment_portal extends App_Controller
{
	protected $recruitment_model;
	function __construct() {

		parent::__construct();
		$this->recruitment_model = new \Recruitment\Models\Recruitment_model();
		$this->candidates_model = new \Recruitment\Models\Candidates_model();
		app_hooks()->do_action('app_hook_recruitment_portal_init');
	}

	/*Lay out start*/

	/**
	 * hrp rander
	 * @param  [type] $view 
	 * @param  array  $data 
	 * @return [type]       
	 */
	public function rander($view, $data = array()) {
		$view_data['content_view'] = $view;
		$view_data['topbar'] = "Recruitment\Views\\recruitment_portal/layout/includes/topbar";

		$view_data = array_merge($view_data, $data);

		return $this->view('Recruitment\Views\recruitment_portal\layout/index', $view_data);
	}

	/**
	 * hrp view
	 * @param  [type] $view 
	 * @param  array  $data 
	 * @return [type]       
	 */
	public function view($view, $data = array()) {
		$view_data = array();

		$users_model = model("Recruitment\Models\Candidates_model", false);
		if ($users_model->login_user_id()) {
			//user logged in, prepare login user data
			$login_user_id = $this->candidates_model->login_user_id();
			if (!$login_user_id) {

			}else{
				$view_data["login_user"] = $this->candidates_model->get_access_info($login_user_id);
			}

		}

		$view_data = array_merge($view_data, $data);

		return view($view, $view_data);
	}

	/*Lay out end*/

	public function index()
	{   
		$data['title']            = app_lang('recruitment_portal');
		$data['rec_campaingn'] = $this->recruitment_model->do_recruitment_portal_search(true, '', $page = 1, $count = false, $where = []);

		return $this->rander('Recruitment\Views\recruitment_portal/rec_portal/portal', $data);
	}

	/**
     * job detail
     * @return view 
     */
    public function job_detail($id ='')
    {   
        $data['title']            = app_lang('recruitment_portal');
        $data['rec_campaingn'] = $this->recruitment_model->get_rec_campaign_detail($id);
        $data['rec_channel'] = $this->recruitment_model->get_recruitment_channel_form_campaingn($id);
        $data['id'] = $id;

        if(is_candidate_logged_in()){ 
            $candidate_id = get_candidate_id();
            $get_candidates = $this->recruitment_model->get_candidates($candidate_id);
            if(isset($get_candidates->applied_job_activate)){
                $data['applied_job_activate'] = $get_candidates->applied_job_activate;
            }else{
                $data['applied_job_activate'] = [];
            }
        }else{
            $data['applied_job_activate'] = [];
        }

        return $this->rander('Recruitment\Views\recruitment_portal/rec_portal/job_detail', $data);
    }

    /**
     * search job
     * 
     */
    public function search_job()
    {

        $search = $this->request->getPost('search');
        $page = $this->request->getPost('page');
        $status = true;

        $data['title']            = app_lang('showing_search_result', $search);
        $data['rec_campaingn'] = $this->recruitment_model->do_recruitment_portal_search($status, $search, $page = 1, $count = false, $where = []);
        $data['rec_campaingn_total'] = $this->recruitment_model->do_recruitment_portal_search($status, $search, $page = 1, $count = true, $where = []);

        $data['search'] = $search;
        $data['page'] = (float)$page+1;
        
        return $this->rander('Recruitment\Views\recruitment_portal/rec_portal/portal', $data);
    }

    /**
     * show more job
     *  
     */
    public function show_more_job(){

        $search = $this->request->getPost('search');
        $page = $this->request->getPost('page');

        $status = true;

        $data = $this->recruitment_model->do_recruitment_show_more_job($status, $search, $page, $count = false, $where = []);

        echo json_encode([
            'page'=> $data['page'],
            'data' => $data['value'],
            'status' => $data['status']
        ]);
        die;

    }

    /**
     * job live search
     * @return json 
     */
    public function job_live_search()
    {
        $search = $this->request->getPost('search');
        $page = $this->request->getPost('page');
        $status = true;
        
        $data = $this->recruitment_model->do_recruitment_show_more_job($status, $search, $page = 1, $count = false, $where = []);

        $rec_campaingn_total = $this->recruitment_model->do_recruitment_portal_search($status, $search, $page = 1, $count = true, $where = []);

        echo json_encode([
            'page'=> $data['page'],
            'data' => $data['value'],
            'status' => $data['status'],
            'rec_campaingn_total' => $rec_campaingn_total
        ]);
        die;


    }

    /**
     * send mail list candidate
     * @return redirect
     */
    public function send_mail_list_candidate() {
        if ($this->request->getPost()) {
            $data = $this->request->getPost();
            $job_detail_id = '';
            if(isset($data['job_detail_id'])){
                $job_detail_id .= $data['job_detail_id'] ;
                unset($data['job_detail_id']);
            }

            $rs = $this->recruitment_model->portal_send_mail_to_friend($data);
            if ($rs == true) {
                $this->session->setFlashdata("success_message", app_lang("send_mail_successfully"));
            }

            if(isset($job_detail_id)){
                app_redirect('recruitment_portal/job_detail/'.$job_detail_id);

            }else{
                app_redirect('recruitment_portal');
            }
        }
    }

    /**
     * profile
     * @return [type] 
     */
    public function profile()
    {
        if(!is_candidate_logged_in()){ 
            app_redirect('candidate_signin');
        }

        if ($this->request->getPost('profile')) {

            $data = $this->request->getPost();
            if(isset($data['profile'])){
                unset($data['profile']);
            }
            $success = $this->recruitment_model->update_cadidate($data, get_candidate_id());

            handle_rec_candidate_file(get_candidate_id());
            handle_rec_candidate_avar_file(get_candidate_id());
            if ($success == true) {
                $this->session->setFlashdata("success_message", app_lang("clients_profile_updated"));

            }

            app_redirect('recruitment_portal/profile');
        } elseif ($this->request->getPost('change_password')) {

            $success = $this->recruitment_model->change_candidate_password(
                get_candidate_id(),
                $this->request->getPost('oldpassword'),
                $this->request->getPost('newpasswordr')
            );

            if (is_array($success) && isset($success['old_password_not_match'])) {
                $this->session->setFlashdata("error_message", app_lang("client_old_password_incorrect"));

            } elseif ($success == true) {
                $this->session->setFlashdata("success_message", app_lang("client_password_changed"));

            }

            app_redirect('recruitment_portal/profile');
        }
        $candidate_id = get_candidate_id();
        $data['title'] = app_lang('clients_profile_heading');
        $data['candidate'] = $this->recruitment_model->get_candidates($candidate_id);
        $data['csv'] = $this->recruitment_model->get_candidate_attachments($candidate_id);
        $data['skills'] = $this->recruitment_model->get_skill();

        return $this->rander('Recruitment\Views\recruitment_portal/candidates/profiles/candidate_profile', $data);

    }

    /**
     * applied jobs
     * @return [type] 
     */
    public function applied_jobs()
    {
        if(!is_candidate_logged_in()){ 
            app_redirect('candidate_signin');
        }

        $candidate_id = get_candidate_id();
        $data['title'] = app_lang('re_applied_jobs');
        $data['candidate'] = $this->recruitment_model->get_candidates($candidate_id);

        return $this->rander('Recruitment\Views\recruitment_portal/candidates/applied_jobs/applied_job', $data);
    }

    /**
     * applied now
     * @param  [type] $campaingn_id 
     * @return [type]               
     */
    public function applied_now($campaingn_id, $form_key)
    {
        if(!is_candidate_logged_in()){ 
            app_redirect('candidate_signin');
        }

        $candidate_id = get_candidate_id();

        $status = '1';
        $message = 'rec_Thank_you_for_your_apply_for_for_this_position';

        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'rec_campaign_form_web');

        $builder->where('form_key', $form_key);
        $rec_campaign_form_web = $builder->get()->getRow();
        if ($rec_campaign_form_web) {
            $status = $rec_campaign_form_web->lead_status;
        }

        $data['title'] = app_lang('re_applied_jobs');
        $data['candidate'] = $this->recruitment_model->candidate_apply($candidate_id, $campaingn_id, $status);
        $this->session->setFlashdata("success_message", app_lang($message));

        app_redirect('recruitment_portal/job_detail/'.$campaingn_id);
    }

    /**
     * interview schedules
     * @return [type] 
     */
    public function interview_schedules()
    {
        if(!is_candidate_logged_in()){ 
            app_redirect('candidate_signin');
        }

        $candidate_id = get_candidate_id();
        $data['title'] = app_lang('rec_interview_schedules');
        $data['list_interview'] = $this->recruitment_model->get_interview_by_candidate($candidate_id);

        return $this->rander('Recruitment\Views\recruitment_portal/candidates/interview_schedules/interview_schedule', $data);

    }

    /**
     * remove profile image
     * @param  [type] $image_id 
     * @return [type]           
     */
    public function remove_profile_image($image_id)
    {
        $id = get_candidate_id();

        app_hooks()->do_action('before_remove_candidate_profile_image', $id);

        if (file_exists(CANDIDATE_IMAGE_UPLOAD . $id)) {
            delete_dir(CANDIDATE_IMAGE_UPLOAD . $id);
        }

        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'files');
        $builder->where('id', $image_id);
        $affected_rows = $builder->delete();

        if ($affected_rows > 0) {
            app_redirect('recruitment_portal/profile');
        }
    }

    /**
     * remove candidate cv
     * @param  [type] $cv_id 
     * @return [type]        
     */
    public function remove_candidate_cv($cv_id)
    {
        $id = get_candidate_id();

        if (file_exists(CANDIDATE_CV_UPLOAD . $id)) {
            delete_dir(CANDIDATE_CV_UPLOAD . $id);
        }

        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'files');
        $builder->where('id', $cv_id);
        $affected_rows = $builder->delete();

        if ($affected_rows > 0) {
            app_redirect('recruitment_portal/profile');
        }
    }

    /**
     * candidate_file
     * @param  [type] $id     
     * @param  [type] $rel_id 
     * @return [type]         
     */
    public function candidate_file($id, $rel_id) {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin'] = is_admin();
        $data['file'] = $this->recruitment_model->get_file($id, $rel_id);
        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }
        $this->data($data);
        $this->view('candidate_profile/_file');
        $this->layout();
    }

    /**
     * delete applied job
     * @param  [type] $applied_job_id 
     * @return [type]                 
     */
    public function delete_applied_job($applied_job_id)
    {

        $builder = db_connect('default');
        $builder = $builder->table(get_db_prefix().'rec_applied_jobs');
        $builder->where('id', $applied_job_id);
        $builder->update(['activate' => '0']);
        $this->session->setFlashdata("success_message", app_lang('deleted'));

        app_redirect('recruitment_portal/applied_jobs');
    }

}