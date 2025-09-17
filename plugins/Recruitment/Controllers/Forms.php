<?php

namespace Recruitment\Controllers;

use App\Controllers\App_Controller;

class Forms extends App_Controller
{
	protected $recruitment_model;
	function __construct() {

		parent::__construct();
		$this->recruitment_model = new \Recruitment\Models\Recruitment_model();
		app_hooks()->do_action('app_hook_recruitment_form_init');

	}

	public function index()
	{
		show_404();
	}

	public function wtl($rec_campaignid="",$key="")
	{   
		$form = $this->recruitment_model->get_form([
            'form_key' => $key

            ]);

        if (!$form) {
            show_404();
        }

        $data['form_fields'] = json_decode($form->form_data);
        if (!$data['form_fields']) {
            $data['form_fields'] = [];
        }
        if ($this->request->getPost('key')) {
            $data1 = $this->request->getPost();
            if ($this->request->getPost('key') == $key) {
                if(isset($data1['csrf_token_name'])){
                    unset($data1['csrf_token_name']);
                }
                $data['new_candidate'] = true;
                $ids = $this->recruitment_model->add_candidate_forms($data1, $key);
                if ($ids) {
                    portal_handle_rec_candidate_file_form($ids);
                    handle_rec_candidate_avar_file($ids);

                    $status = '1';
                    $builder = db_connect('default');
                    $builder = $builder->table(get_db_prefix().'rec_campaign_form_web');
                    $builder->where('form_key', $key);
                    $rec_campaign_form_web = $builder->get()->getRow();
                    if ($rec_campaign_form_web) {
                    	$status = $rec_campaign_form_web->lead_status;
                    }
                    $this->recruitment_model->candidate_apply($ids, $rec_campaignid, $status);

                    $success = true;
                    $message = app_lang('added_successfully').' '. app_lang('candidate_profile');

                    $data['form'] = $form;
                    $data['message'] =$form->success_submit_msg;

                    echo json_encode([
                        'success' => $success,
                        'message' => $form->success_submit_msg,
                    ]);
                    die;
                    
                }
            }
        }


		$data['form'] = $form;
        $data['rec_campaignid'] = $rec_campaignid;

		echo view('Recruitment\Views\forms/recruitment_channel_form', $data);
	}

	public function l($hash)
	{
		if (get_option('gdpr_enable_lead_public_form') == '0') {
			show_404();
		}
		$this->load->model('leads_model');
		$this->load->model('gdpr_model');
		$lead = $this->leads_model->get('', ['hash' => $hash]);

		if (!$lead || count($lead) > 1) {
			show_404();
		}

		$lead = array_to_object($lead[0]);
		load_lead_language($lead->id);

		if ($this->request->getPost('update')) {
			$data = $this->request->getPost();
			unset($data['update']);
			$this->leads_model->update($data, $lead->id);
			redirect($_SERVER['HTTP_REFERER']);
		} elseif ($this->request->getPost('export') && get_option('gdpr_data_portability_leads') == '1') {
			$this->load->library('gdpr/gdpr_lead');
			$this->gdpr_lead->export($lead->id);
		} elseif ($this->request->getPost('removal_request')) {
			$success = $this->gdpr_model->add_removal_request([
				'description'  => nl2br($this->request->getPost('removal_description')),
				'request_from' => $lead->name,
				'lead_id'      => $lead->id,
			]);
			if ($success) {
				send_gdpr_email_template('gdpr_removal_request_by_lead', $lead->id);
				set_alert('success', app_lang('data_removal_request_sent'));
			}
			redirect($_SERVER['HTTP_REFERER']);
		}

		$lead->attachments = $this->leads_model->get_lead_attachments($lead->id);
		$this->disableNavigation();
		$this->disableSubMenu();
		$data['title'] = $lead->name;
		$data['lead']  = $lead;
		$this->view('forms/lead');
		$this->data($data);
		$this->layout(true);
	}


}