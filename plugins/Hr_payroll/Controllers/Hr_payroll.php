<?php

namespace Hr_payroll\Controllers;

use App\Controllers\Security_Controller;
use App\Models\Crud_model;
use App\Libraries\Pdf;


class Hr_payroll extends Security_Controller {

	protected $hr_payroll_model;
	function __construct() {

		parent::__construct();
		$this->hr_payroll_model = new \Hr_payroll\Models\Hr_payroll_model();
		app_hooks()->do_action('app_hook_hrpayroll_init');

	}

	
	/**
	 * income tax rates
	 * @return [type] 
	 */
	public function income_tax_rates()
	{
		$data['title'] = app_lang('income_tax_rates');
		$data['income_tax_rates'] = $this->hr_payroll_model->get_income_tax_rate();

		return $this->template->rander("Hr_payroll\Views\includes\income_tax_rates", $data);
	}

	/**
	 * setting incometax rates
	 * @return [type]
	 */
	public function setting_incometax_rates() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_income_tax_rates($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/income_tax_rates'));
			}
		}
	}

	/**
	 * income_tax_rebates
	 * @return [type] 
	 */
	public function income_tax_rebates()
	{

		$data['title'] = app_lang('income_tax_rebates');
		$data['income_tax_rebates'] = json_encode($this->hr_payroll_model->get_income_tax_rebates());

		return $this->template->rander("Hr_payroll\Views\includes\income_tax_rebates", $data);
	}

	/**
	 * setting incometax rebates
	 * @return [type]
	 */
	public function setting_incometax_rebates() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_income_tax_rebates($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/income_tax_rebates'));
			}
		}
	}

	/**
	 * earnings list
	 * @return [type] 
	 */
	public function earnings_list()
	{

		$earnings_value = [];
		$earnings_value[] = [
			'id' => 'monthly',
			'label' => app_lang('monthly'),
		];
		$earnings_value[] = [
			'id' => 'annual',
			'label' => app_lang('annual'),
		];

		$data['title'] = app_lang('earnings_list');
		$data['basis_value'] = $earnings_value;
		$data['earnings_list'] = json_encode($this->hr_payroll_model->get_earnings_list());

		return $this->template->rander("Hr_payroll\Views\includes\\earnings_list", $data);
	}

	/**
	 * setting earnings list
	 * @return [type]
	 */
	public function setting_earnings_list() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_earnings_list($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/earnings_list'));
			}
		}
	}

	/**
	 * salary deductions list
	 * @return [type] 
	 */
	public function salary_deductions_list()
	{

		$earn_inclusion_value = [];
		$earn_inclusion_value[] = [
			'id' => 'fullvalue',
			'label' => app_lang('fullvalue'),
		];
		$earn_inclusion_value[] = [
			'id' => 'taxable',
			'label' => app_lang('taxable'),
		];

		$basis_value = [];
		$basis_value[] = [
			'id' => 'gross',
			'label' => app_lang('gross'),
		];
		$basis_value[] = [
			'id' => 'fixed_amount',
			'label' => app_lang('fixed_amount'),
		];

		if (hr_payroll_get_status_modules('Hr_profile') && (get_setting('integrated_hrprofile') == 1)) {
			$earnings_list = $this->hr_payroll_model->hr_records_get_earnings_list();

			foreach ($earnings_list as $value) {
				switch ($value['rel_type']) {
					case 'salary':

					$basis_value[] = [
						'id' => 'st_'.$value['rel_id'],
						'label' => $value['description'],
					];
					break;

					case 'allowance':
					$basis_value[] = [
						'id' => 'al_'.$value['rel_id'],
						'label' => $value['description'],
					];

					break;

					default:
						# code...
					break;
				}

			}


		} else {
			$earnings_list = $this->hr_payroll_model->get_earnings_list();

			foreach ($earnings_list as $value) {
				$basis_value[] = [
					'id' => 'earning_'.$value['id'],
					'label' => $value['description'],
				];
			}
		}

		$data['title'] = app_lang('salary_deductions_list');
		$data['basis_value'] = $basis_value;
		$data['earn_inclusion'] = $earn_inclusion_value;
		$data['salary_deductions_list'] = json_encode($this->hr_payroll_model->get_salary_deductions_list());

		return $this->template->rander("Hr_payroll\Views\includes\salary_deductions_list", $data);
	}

	/**
	 * setting salary deductions list
	 * @return [type]
	 */
	public function setting_salary_deductions_list() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_salary_deductions_list($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/salary_deductions_list'));

			}
		}
	}

	/**
	 * insurance list
	 * @return [type] 
	 */
	public function insurance_list()
	{
		$basis_value = [];
		$basis_value[] = [
			'id' => 'gross',
			'label' => app_lang('gross'),
		];
		$basis_value[] = [
			'id' => 'fixed_amount',
			'label' => app_lang('fixed_amount'),
		];

		$data['title'] = app_lang('insurance_list');
		$data['basis_value'] = $basis_value;
		$data['insurance_list'] = json_encode($this->hr_payroll_model->get_insurance_list());

		return $this->template->rander("Hr_payroll\Views\includes\insurance_list", $data);
	}

	/**
	 * setting insurance list
	 * @return [type]
	 */
	public function setting_insurance_list() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_insurance_list($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/insurance_list'));

			}

		}
	}

	/**
	 * setting company contributions list
	 * @return [type]
	 */
	public function setting_company_contributions_list() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_company_contributions_list($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/setting?group=company_contributions_list'));

			}

		}
	}

	/**
	 * data integrations
	 * @return [type] 
	 */
	public function data_integrations()
	{
		$data['title'] = app_lang('hrp_data_integration');
		$data['hr_profile_active'] = hr_payroll_get_status_modules('Hr_profile');
		$data['timesheets_active'] = hr_payroll_get_status_modules('Timesheets');
		$data['commissions_active'] = hr_payroll_get_status_modules('Commission');

		$hr_profile_title = '';
		$timesheets_title = '';
			//title
		if ($data['hr_profile_active'] == false) {
			$hr_profile_title = app_lang('active_hr_profile_to_integration');
		} else {
			$hr_profile_title = app_lang('hr_profile_integration_data');
		}

		if ($data['timesheets_active'] == false) {
			$timesheets_title = app_lang('active_timesheets_to_integration');
		} else {
			$timesheets_title = app_lang('timesheets_to_integration');
		}

		if ($data['commissions_active'] == false) {
			$commissions_title = app_lang('active_commissions_to_integration');
		} else {
			$commissions_title = app_lang('commissions_to_integration');
		}

		$data['hr_profile_title'] = $hr_profile_title;
		$data['timesheets_title'] = $timesheets_title;
		$data['commissions_title'] = $commissions_title;

		$get_attendance_type = $this->hr_payroll_model->setting_get_attendance_type();

		$data['actual_workday_type'] = $get_attendance_type['actual_workday'];
		$data['paid_leave_type'] = $get_attendance_type['paid_leave'];
		$data['unpaid_leave_type'] = $get_attendance_type['unpaid_leave'];

		return $this->template->rander("Hr_payroll\Views\includes\data_integration", $data);
	}

	/**
	 * data integration
	 * @return [type]
	 */
	public function data_integration() {
		if (!is_admin()) {
			app_redirect("forbidden");
		}

		$data = $this->request->getPost();

		$mess = $this->hr_payroll_model->update_data_integration($data);
		if ($mess) {
			$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

		} else {
			$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
		}

		app_redirect(('hr_payroll/data_integrations'));

	}

	/**
	 * timesheet integration type change
	 * @return [type]
	 */
	public function timesheet_integration_type_change() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			$results = $this->hr_payroll_model->get_timesheet_type_for_setting($data);

			echo json_encode([
				'actual_workday_v' => $results['actual_workday'],
				'paid_leave_v' => $results['paid_leave'],
				'unpaid_leave_v' => $results['unpaid_leave'],
			]);
			die;
		}
	}

	/**
	 * hr records earnings list
	 * @return [type] 
	 */
	public function hr_records_earnings_list()
	{
		$earnings_value = [];
		$earnings_value[] = [
			'id' => 'monthly',
			'label' => app_lang('monthly'),
		];
		$earnings_value[] = [
			'id' => 'annual',
			'label' => app_lang('annual'),
		];

		$data['title'] = app_lang('earnings_list');
		$data['basis_value'] = $earnings_value;
		$data['earnings_list_hr_records'] = json_encode($this->hr_payroll_model->hr_records_get_earnings_list());

		return $this->template->rander("Hr_payroll\Views\includes\hr_records_earnings_list", $data);
	}

	/**
	 * setting earnings list hr records
	 * @return [type]
	 */
	public function setting_earnings_list_hr_records() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->earnings_list_synchronization($data);
				$this->session->setFlashdata("success_message", app_lang("hrp_successful_data_synchronization"));

				app_redirect(('hr_payroll/hr_records_earnings_list'));
			}
		}
	}

	/**
	 * hr payroll permission table
	 * @return [type]
	 */
	public function hr_payroll_permission_table() {
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

			$arr_staff_id = hr_payroll_get_staff_id_hr_permissions();

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

				if (has_permission('hrm_setting', '', 'edit')) {
					$options = icon_btn('#', 'edit', 'btn-default', [
						'title' => app_lang('edit'),
						'onclick' => 'hr_payroll_permissions_update(' . $aRow['staffid'] . ', ' . $aRow['role'] . ', ' . $not_hide . '); return false;',
					]);
				}

				if (has_permission('hrm_setting', '', 'delete')) {
					$options .= icon_btn('hr_payroll/delete_hr_payroll_permission/' . $aRow['staffid'], 'remove', 'btn-danger _delete', ['title' => app_lang('delete')]);
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
			$data['staffs'] = hr_payroll_get_staff_id_dont_permissions();
			$add_new = $this->request->getPost('add_new');

			if ($add_new == ' hide') {
				$data['add_new'] = ' hide';
				$data['display_staff'] = '';
			} else {
				$data['add_new'] = '';
				$data['display_staff'] = ' hide';
			}

			$this->template->rander('Hr_payroll\Views\includes/permission_modal', $data);
		}
	}


	/**
	 * hr payroll update permissions
	 * @param  string $id
	 * @return [type]
	 */
	public function hr_payroll_update_permissions($id = '') {
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
				$this->session->setFlashdata("error_message", app_lang('staff_cant_remove_main_admin'));
			} elseif (isset($response['cant_remove_yourself_from_admin'])) {
				$this->session->setFlashdata("error_message", app_lang('staff_cant_remove_yourself_from_admin'));
			}
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang('updated_successfully', app_lang('staff_member')));
		}
		app_redirect(('hr_payroll/setting?group=permissions'));

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
	 * delete hr payroll permission
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_hr_payroll_permission($id) {
		if (!is_admin()) {
			access_denied('hr_profile');
		}

		$response = $this->hr_payroll_model->delete_hr_payroll_permission($id);

		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang('hr_is_referenced', app_lang('department_lowercase')));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang('deleted', app_lang('hr_department')));
		} else {
			$this->session->setFlashdata("error_message", app_lang('problem_deleting', app_lang('department_lowercase')));
		}
		app_redirect(('hr_payroll/setting?group=permissions'));

	}

	/**
	 * manage employees
	 * @return [type]
	 */
	public function manage_employees() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_employee') && !hrp_has_permission('hr_payroll_can_view_own_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}


		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$employees_data = $this->hr_payroll_model->get_employees_data($current_month, $rel_type);
		$employees_value = [];
		foreach ($employees_data as $key => $value) {
			$employees_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}
		//get employee data for the first
		$format_employees_value = $this->hr_payroll_model->get_format_employees_data($rel_type);

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		//get current month

		$data_object_kpi = [];

		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);
				
				$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

			} else {
				$data_object_kpi[$staff_key]['employee_number'] = '';
				$data_object_kpi[$staff_key]['employee_name'] = '';
				$data_object_kpi[$staff_key]['department_name'] = '';
			}

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['job_title'] = $staff_value['position_name'];
				$data_object_kpi[$staff_key]['income_tax_number'] = $staff_value['Personal_tax_code'];
				$data_object_kpi[$staff_key]['residential_address'] = $staff_value['resident'];
			} else {
				if (isset($employees_value[$staff_value['staffid'] . '_' . $current_month])) {
					$data_object_kpi[$staff_key]['job_title'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['job_title'];
					$data_object_kpi[$staff_key]['income_tax_number'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['income_tax_number'];
					$data_object_kpi[$staff_key]['residential_address'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['residential_address'];

				} else {
					$data_object_kpi[$staff_key]['job_title'] = '';
					$data_object_kpi[$staff_key]['income_tax_number'] = '';
					$data_object_kpi[$staff_key]['residential_address'] = '';
				}
			}

			if (isset($employees_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['income_rebate_code'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['income_rebate_code'];
				$data_object_kpi[$staff_key]['income_tax_rate'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['income_tax_rate'];

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($employees_value[$staff_value['staffid'] . '_' . $current_month]['contract_value'])) {

					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $employees_value[$staff_value['staffid'] . '_' . $current_month]['contract_value']);
				} else {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);
				}

				$data_object_kpi[$staff_key]['probationary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['probationary_effective'];
				$data_object_kpi[$staff_key]['probationary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['probationary_expiration'];
				$data_object_kpi[$staff_key]['primary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['primary_effective'];
				$data_object_kpi[$staff_key]['primary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['primary_expiration'];

				$data_object_kpi[$staff_key]['id'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['id'];
				$data_object_kpi[$staff_key]['bank_name'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['bank_name'];
				$data_object_kpi[$staff_key]['account_number'] = $employees_value[$staff_value['staffid'] . '_' . $current_month]['account_number'];


			} else {
				$data_object_kpi[$staff_key]['income_rebate_code'] = 'A';
				$data_object_kpi[$staff_key]['income_tax_rate'] = 'A';

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);

				$data_object_kpi[$staff_key]['probationary_effective'] = '';
				$data_object_kpi[$staff_key]['probationary_expiration'] = '';
				$data_object_kpi[$staff_key]['primary_effective'] = '';
				$data_object_kpi[$staff_key]['primary_expiration'] = '';

				$data_object_kpi[$staff_key]['id'] = 0;
				$data_object_kpi[$staff_key]['bank_name'] = '';
				$data_object_kpi[$staff_key]['account_number'] = '';

			}

			$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
		}
		//check is add new or update data
		if (count($employees_value) > 0) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_employees_value['column_format']);
		$data['col_header'] = json_encode($format_employees_value['header']);

		return $this->template->rander('Hr_payroll\Views\employees/employees_manage', $data);

	}

	/**
	 * employees filter
	 * @return [type]
	 */
	public function employees_filter() {

		$data = $this->request->getPost();

		$rel_type = hrp_get_hr_profile_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$employees_data = $this->hr_payroll_model->get_employees_data($month_filter, $rel_type);
		$employees_value = [];
		foreach ($employees_data as $key => $value) {
			$employees_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_employees_value = $this->hr_payroll_model->get_format_employees_data($rel_type);

		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load deparment by manager
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
				if ($staff_i) {

					if ($rel_type == 'hr_records') {
						$data_object_kpi[$staff_key]['employee_number'] = $staff_i->staff_identifi;
					} else {
						$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
					}

					$data_object_kpi[$staff_key]['employee_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

					$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

					$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

				} else {
					$data_object_kpi[$staff_key]['employee_number'] = '';
					$data_object_kpi[$staff_key]['employee_name'] = '';
					$data_object_kpi[$staff_key]['department_name'] = '';
				}

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['job_title'] = $staff_value['position_name'];
					$data_object_kpi[$staff_key]['income_tax_number'] = $staff_value['Personal_tax_code'];
					$data_object_kpi[$staff_key]['residential_address'] = $staff_value['resident'];
				} else {
					if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {
						$data_object_kpi[$staff_key]['job_title'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['job_title'];
						$data_object_kpi[$staff_key]['income_tax_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_number'];
						$data_object_kpi[$staff_key]['residential_address'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['residential_address'];

					} else {
						$data_object_kpi[$staff_key]['job_title'] = '';
						$data_object_kpi[$staff_key]['income_tax_number'] = '';
						$data_object_kpi[$staff_key]['residential_address'] = '';
					}
				}

				if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {

					$data_object_kpi[$staff_key]['income_rebate_code'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_rebate_code'];
					$data_object_kpi[$staff_key]['income_tax_rate'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_rate'];

					$data_object_kpi[$staff_key]['probationary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_effective'];
					$data_object_kpi[$staff_key]['probationary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_expiration'];
					$data_object_kpi[$staff_key]['primary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_effective'];
					$data_object_kpi[$staff_key]['primary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_expiration'];

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value'])) {

						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value']);
					} else {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);
					}

					$data_object_kpi[$staff_key]['id'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['id'];
					$data_object_kpi[$staff_key]['bank_name'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['bank_name'];
					$data_object_kpi[$staff_key]['account_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['account_number'];


				} else {
					$data_object_kpi[$staff_key]['income_rebate_code'] = 'A';
					$data_object_kpi[$staff_key]['income_tax_rate'] = 'A';

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_employees_value['probationary'], $format_employees_value['formal']);

					$data_object_kpi[$staff_key]['id'] = 0;
					$data_object_kpi[$staff_key]['bank_name'] = '';
					$data_object_kpi[$staff_key]['account_number'] = '';

				}

				$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
			}

		}

		//check is add new or update data
		if (count($employees_value) > 0) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * add manage employees
	 */
	public function add_manage_employees() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_employee') && !hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if ($data['hrp_employees_rel_type'] == 'synchronization') {
				//synchronization
				$success = $this->hr_payroll_model->employees_synchronization($data);
			} elseif ($data['hrp_employees_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->employees_update($data);
			} else {
				$success = false;
			}

			if ($success) {
				$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));
			}

			app_redirect('hr_payroll/manage_employees');
		}

	}

	/**
	 * render filter query
	 * @param  [type] $data_month
	 * @param  [type] $data_staff
	 * @param  [type] $data_department
	 * @param  [type] $data_role_attendance
	 * @return [type]
	 */
	public function render_filter_query($data_month, $data_staff, $data_department, $data_role_attendance) {

		$months_filter = $data_month;
		$querystring = ' status="active"';
		$department = $data_department;

		$staff = '';
		if (isset($data_staff)) {
			$staff = $data_staff;
		}
		$staff_querystring = '';
		$department_querystring = '';
		$role_querystring = '';

		if ($department != '') {
			$arrdepartment = $this->hr_payroll_model->hr_payroll_run_query('select * from '.get_db_prefix().'team where id = ' . $department);

			$temp = '';
			foreach ($arrdepartment as $value) {
				$members = explode(",", $value['members']);

				foreach ($members as $member_id) {
				    if(strlen($member_id) > 0){
				    	$temp = $temp . $member_id . ',';
				    }
				}
			}
			$temp = rtrim($temp, ",");
			$department_querystring = 'FIND_IN_SET(id, "' . $temp . '")';
		}

		if ($staff != '') {
			$temp = '';
			$araylengh = count($staff);
			for ($i = 0; $i < $araylengh; $i++) {
				$temp = $temp . $staff[$i];
				if ($i != $araylengh - 1) {
					$temp = $temp . ',';
				}
			}
			$staff_querystring = 'FIND_IN_SET(id, "' . $temp . '")';
		}

		if (isset($data_role_attendance) && $data_role_attendance != '') {
			$role_admin = false;

			$temp = '';
			$araylengh = count($data_role_attendance);
			for ($i = 0; $i < $araylengh; $i++) {
				if($data_role_attendance[$i] != 'admin'){
					$temp = $temp . $data_role_attendance[$i];
					if ($i != $araylengh - 1) {
						$temp = $temp . ',';
					}
				}else{
					$role_admin = true;
				}
			}

			if($role_admin){
				$role_querystring = '(FIND_IN_SET(role_id, "' . $temp . '") OR is_admin = 1)';
			}else{
				$role_querystring = '(FIND_IN_SET(role_id, "' . $temp . '") AND is_admin = 0)';
			}
		}

		$arrQuery = array($staff_querystring, $department_querystring, $querystring, $role_querystring);

		$newquerystring = '';
		foreach ($arrQuery as $string) {
			if ($string != '') {
				$newquerystring = $newquerystring . $string . ' AND ';
			}
		}

		$newquerystring = rtrim($newquerystring, "AND ");
		if ($newquerystring == '') {
			$newquerystring = [];
		}

		return $newquerystring;
	}

	/**
	 * manage attendance
	 * @return [type]
	 */
	public function manage_attendance() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_attendance') && !hrp_has_permission('hr_payroll_can_view_own_hrp_attendance') && !is_admin()) {
			access_denied('hrp_attendance');
		}


		$rel_type = hrp_get_timesheets_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));

		//get day header in month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($current_month, $rel_type);

		$attendances = $this->hr_payroll_model->get_hrp_attendance($current_month);
		$attendances_value = [];

		foreach ($attendances as $key => $value) {
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//load deparment by manager
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];

		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/

			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
				}

				$data_object_kpi[$staff_key]['staff_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;
				$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

				$data_object_kpi[$staff_key]['staff_departments'] = $list_department->name;

			} else {
				$data_object_kpi[$staff_key]['hr_code'] = '';
				$data_object_kpi[$staff_key]['staff_name'] = '';
				$data_object_kpi[$staff_key]['staff_departments'] = '';

			}

			if (isset($attendances_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['standard_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['standard_workday'];
				$data_object_kpi[$staff_key]['actual_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday'];
				$data_object_kpi[$staff_key]['actual_workday_probation'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday_probation'];
				$data_object_kpi[$staff_key]['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['paid_leave'];
				$data_object_kpi[$staff_key]['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['unpaid_leave'];
				$data_object_kpi[$staff_key]['id'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $attendances_value[$staff_value['staffid'] . '_' . $current_month]);

			} else {
				$data_object_kpi[$staff_key]['standard_workday'] = get_setting('standard_working_time');
				$data_object_kpi[$staff_key]['actual_workday_probation'] = 0;
				$data_object_kpi[$staff_key]['actual_workday'] = 0;
				$data_object_kpi[$staff_key]['paid_leave'] = 0;
				$data_object_kpi[$staff_key]['unpaid_leave'] = 0;
				$data_object_kpi[$staff_key]['id'] = 0;
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $days_header_in_month['days_header']);

			}
			$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
			$data_object_kpi[$staff_key]['month'] = $current_month;
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

		}

		//check is add new or update data
		if (count($attendances_value) > 0) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['staffs'] = $staffs;
		$data['data_object_kpi'] = $data_object_kpi;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($days_header_in_month['columns_type']);
		$data['col_header'] = json_encode($days_header_in_month['headers']);

		return $this->template->rander('Hr_payroll\Views\attendances/attendance_manage', $data);
	}

	/**
	 * add attendance
	 */
	public function add_attendance() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_attendance') && !hrp_has_permission('hr_payroll_can_edit_hrp_attendance') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if (isset($data)) {

				if ($data['hrp_attendance_rel_type'] == 'update') {
					$success = $this->hr_payroll_model->add_update_attendance($data);
				} elseif ($data['hrp_attendance_rel_type'] == 'synchronization') {
					$success = $this->hr_payroll_model->synchronization_attendance($data);
				} elseif ($data['hrp_attendance_rel_type'] == 'daily_synchronization') {
					$success = $this->hr_payroll_model->daily_time_synchronization($data);
				} else {
					$success = false;
				}

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));
				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}
				app_redirect(('hr_payroll/manage_attendance'));
			}

		}
	}

	/**
	 * import xlsx employees
	 * @return [type]
	 */
	public function import_xlsx_employees() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_employee') && !hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$user_id = $this->login_user->id;
		$_personal_language = get_setting('user_' . $user_id . '_personal_language');
		if(strlen($_personal_language) == 0){
			$_personal_language = get_setting("language");
		}

		$data['active_language'] = $_personal_language;

		return $this->template->rander('Hr_payroll\Views\employees/import_employees', $data);
	}

	/**
	 * create employees sample file
	 * @return [type]
	 */
	public function create_employees_sample_file() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_employee') && !hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}


		$month_employees = $this->request->getPost('month_employees');

		$user_id = $this->login_user->id;

		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');


		$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE);

		//get current month
		$rel_type = hrp_get_hr_profile_status();
		$month_filter = date('Y-m-d', strtotime($month_employees . '-01'));

		$employees_data = $this->hr_payroll_model->get_employees_data($month_filter, $rel_type);
		$employees_value = [];
		foreach ($employees_data as $key => $value) {
			$employees_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_employees_value = $this->hr_payroll_model->get_format_employees_data($rel_type);
		$staff_information_key = $format_employees_value['staff_information'];
		$probationary_key = $format_employees_value['probationary_key'];
		$primary_key = $format_employees_value['primary_key'];
		$staff_probationary_key = array_keys($format_employees_value['probationary']);
		$staff_formal_key = array_keys($format_employees_value['formal']);

		$header_key = array_merge($staff_information_key, $staff_probationary_key, $probationary_key, $staff_formal_key, $primary_key);

		//create header value
		$writer_header = [];
		$widths = [];

		$writer_header[app_lang('month')] = 'string';
		$widths[] = 30;

		foreach ($format_employees_value['header'] as $header_value) {
			$writer_header[$header_value] = 'string';
			$widths[] = 30;
		}

		$writer = new \XLSXWriter();

		$col_style1 = [0, 1, 2, 3, 4, 5, 7];
		$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1);

		//load deparment by manager
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		foreach ($staffs as $staff_key => $staff_value) {
			$data_object_kpi = [];

			/*check value from database*/
			$data_object_kpi['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

			$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);
				
			$data_object_kpi['department_name'] = $list_department->name;

			if ($rel_type == 'hr_records') {
				$data_object_kpi['job_title'] = $staff_value['position_name'];
				$data_object_kpi['income_tax_number'] = $staff_value['Personal_tax_code'];
				$data_object_kpi['residential_address'] = $staff_value['resident'];
			} else {
				if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {
					$data_object_kpi['job_title'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['job_title'];
					$data_object_kpi['income_tax_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_number'];
					$data_object_kpi['residential_address'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['residential_address'];

				} else {
					$data_object_kpi['job_title'] = '';
					$data_object_kpi['income_tax_number'] = '';
					$data_object_kpi['residential_address'] = '';
				}
			}

			if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter])) {

				$data_object_kpi['income_rebate_code'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_rebate_code'];
				$data_object_kpi['income_tax_rate'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['income_tax_rate'];
				$data_object_kpi['bank_name'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['bank_name'];
				$data_object_kpi['account_number'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['account_number'];

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value'])) {
					$data_object_kpi = array_merge($data_object_kpi, $employees_value[$staff_value['staffid'] . '_' . $month_filter]['contract_value']);
				}

				$data_object_kpi['probationary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_effective'];
				$data_object_kpi['probationary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['probationary_expiration'];
				$data_object_kpi['primary_effective'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_effective'];
				$data_object_kpi['primary_expiration'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['primary_expiration'];

				$data_object_kpi['id'] = $employees_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

			} else {
				$data_object_kpi['income_rebate_code'] = 'A';
				$data_object_kpi['income_tax_rate'] = 'A';
				$data_object_kpi['bank_name'] = '';
				$data_object_kpi['account_number'] = '';

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi = array_merge($data_object_kpi, $format_employees_value['probationary'], $format_employees_value['formal']);

				$data_object_kpi['probationary_effective'] = '';
				$data_object_kpi['probationary_expiration'] = '';
				$data_object_kpi['primary_effective'] = '';
				$data_object_kpi['primary_expiration'] = '';

				$data_object_kpi['id'] = 0;

			}

			$data_object_kpi['rel_type'] = $rel_type;

			$data_object = array_values($data_object_kpi);
			$temp = [];
			$temp['month'] = $month_filter;
			foreach ($header_key as $_key) {
				$temp[] = isset($data_object_kpi[$_key]) ? $data_object_kpi[$_key] : '';
			}

			if ($staff_key == 0) {
				$writer->writeSheetRow('Sheet1', array_merge([0 => 'month'], $header_key));
			}
			$writer->writeSheetRow('Sheet1', $temp);

		}

		$filename = 'employees_sample_file' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE . $filename, $filename));
		$filename = HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE.$filename;


		echo json_encode([
			'success' => true,
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => $filename,
		]);

	}

	/**
	 * import employees excel
	 * @return [type]
	 */
	public function import_employees_excel() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_employee') && !hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}
		$user_id = $this->login_user->id;
		
		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();
				$rel_type = hrp_get_hr_profile_status();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

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
							app_lang('staffid') => 'string',
							app_lang('id') => 'string',
							app_lang('hr_code') => 'string',
							app_lang('staff_name') => 'string',
							app_lang('department') => 'string',
							app_lang('integration_actual_workday') => 'string',
							app_lang('integration_paid_leave') => 'string',
							app_lang('integration_unpaid_leave') => 'string',
							app_lang('standard_working_time_of_month') => 'string',
							app_lang('month') => 'string',
							app_lang('error') => 'string',
						);

						$writer = new \XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						$xlsx = new \XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);
						$arr_header = [];

						$arr_header['staff_id'] = 0;
						$arr_header['id'] = 1;
						$arr_header['hr_code'] = 2;
						$arr_header['staff_name'] = 3;
						$arr_header['staff_departments'] = 4;
						$arr_header['actual_workday'] = 5;
						$arr_header['paid_leave'] = 6;
						$arr_header['unpaid_leave'] = 7;
						$arr_header['standard_workday'] = 8;
						$arr_header['month'] = 9;

						$total_rows = 0;
						$total_row_false = 0;

						$column_key = $data[1];

						for ($row = 2; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';

							$flag_staff_id = 0;

							if (($flag == 1) || $flag2 == 1) {
								//write error file
								$writer->writeSheetRow('Sheet1', [

								]);

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd = array_combine($column_key, $data[$row]);
								unset($rd['employee_number']);
								unset($rd['employee_name']);
								unset($rd['department_name']);

								array_push($arr_insert, $rd);

							}

						}

						if (count($arr_insert) > 0) {
							$this->hr_payroll_model->import_employees_data($arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($arr_insert) ? count($arr_insert) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_attendance_error_' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PAYROLL_ERROR . $filename, $filename));
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
			'filename' => HR_PAYROLL_ERROR . $filename,
		]);
	}

	/**
	 * attendance filter
	 * @return [type]
	 */
	public function attendance_filter() {
		$data = $this->request->getPost();

		$rel_type = hrp_get_timesheets_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);


		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		//get day header in month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($month_filter, $rel_type);

		$attendances = $this->hr_payroll_model->get_hrp_attendance($month_filter);
		$attendances_value = [];
		foreach ($attendances as $key => $value) {
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			foreach ($staffs as $staff_key => $staff_value) {

				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
				if ($staff_i) {

					if (isset($staff_i->staff_identifi)) {
						$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
					} else {
						$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
					}

					$data_object_kpi[$staff_key]['staff_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

					$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

					$data_object_kpi[$staff_key]['staff_departments'] = $list_department->name;

				} else {
					$data_object_kpi[$staff_key]['hr_code'] = '';
					$data_object_kpi[$staff_key]['staff_name'] = '';
					$data_object_kpi[$staff_key]['staff_departments'] = '';

				}

				if (isset($attendances_value[$staff_value['staffid'] . '_' . $month_filter])) {

					$data_object_kpi[$staff_key]['standard_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['standard_workday'];
					$data_object_kpi[$staff_key]['actual_workday_probation'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['actual_workday_probation'];
					$data_object_kpi[$staff_key]['actual_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['actual_workday'];
					$data_object_kpi[$staff_key]['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['paid_leave'];
					$data_object_kpi[$staff_key]['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['unpaid_leave'];
					$data_object_kpi[$staff_key]['id'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['id'];
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $attendances_value[$staff_value['staffid'] . '_' . $month_filter]);

				} else {
					$data_object_kpi[$staff_key]['standard_workday'] = get_setting('standard_working_time');
					$data_object_kpi[$staff_key]['actual_workday_probation'] = 0;
					$data_object_kpi[$staff_key]['actual_workday'] = 0;
					$data_object_kpi[$staff_key]['paid_leave'] = 0;
					$data_object_kpi[$staff_key]['unpaid_leave'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $days_header_in_month['days_header']);

				}

				$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
				$data_object_kpi[$staff_key]['month'] = $month_filter;

			}

		}

		//check is add new or update data
		if (count($attendances_value) > 0) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'columns' => $days_header_in_month['columns_type'],
			'col_header' => $days_header_in_month['headers'],
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * import xlsx attendance
	 * @return [type]
	 */
	public function import_xlsx_attendance() {
		$user_id = $this->login_user->id;
		$_personal_language = get_setting('user_' . $user_id . '_personal_language');
		if(strlen($_personal_language) == 0){
			$_personal_language = get_setting("language");
		}

		$data['active_language'] = $_personal_language;

		return $this->template->rander('Hr_payroll\Views/attendances/import_attendance', $data);
	}

	/**
	 * create attendance sample file
	 * @return [type]
	 */
	public function create_attendance_sample_file() {
		$user_id = $this->login_user->id;
		$month_attendance = $this->request->getPost('month_attendance');

		if (!class_exists('XLSXReader_fin')) {
			require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
		}
		require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');


		$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_ATTENDANCE_SAMPLE);

		$rel_type = hrp_get_timesheets_status();
		//get attendance data
		$current_month = date('Y-m-d', strtotime($month_attendance . '-01'));
		//get day header in month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($current_month, $rel_type);
		$header_key = array_merge($days_header_in_month['staff_key'], $days_header_in_month['days_key'], $days_header_in_month['attendance_key']);

		$attendances = $this->hr_payroll_model->get_hrp_attendance($current_month);
		$attendances_value = [];
		foreach ($attendances as $key => $value) {
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		//Writer file
		$writer_header = [];
		$widths = [];
		foreach ($days_header_in_month['headers'] as $value) {
			$writer_header[$value] = 'string';
			$widths[] = 30;
		}

		$writer = new \XLSXWriter();

		$col_style1 = [0, 1, 2, 3, 4, 5, 6];
		$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1);

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			$data_object_kpi = [];
			$staffid = 0;
			$hr_code = '';
			$id = 0;
			$staff_name = '';
			$staff_departments = '';
			$actual_workday_probation = 0;
			$actual_workday = 0;
			$paid_leave = 0;
			$unpaid_leave = 0;
			$standard_workday = 0;

			/*check value from database*/
			$staffid = $staff_value['staffid'];

			/*check value from database*/
			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi['hr_code'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
				}

				$data_object_kpi['staff_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;


				$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

				$data_object_kpi['staff_departments'] = $list_department->name;

			} else {
				$data_object_kpi['hr_code'] = '';
				$data_object_kpi['staff_name'] = '';
				$data_object_kpi['staff_departments'] = '';

			}

			if (isset($attendances_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi['standard_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['standard_workday'];
				$data_object_kpi['actual_workday_probation'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday_probation'];
				$data_object_kpi['actual_workday'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['actual_workday'];
				$data_object_kpi['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['paid_leave'];
				$data_object_kpi['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['unpaid_leave'];
				$data_object_kpi['id'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

				$data_object_kpi = array_merge($data_object_kpi, $attendances_value[$staff_value['staffid'] . '_' . $current_month]);

			} else {
				$data_object_kpi['standard_workday'] = get_setting('standard_working_time');
				$data_object_kpi['actual_workday_probation'] = 0;
				$data_object_kpi['actual_workday'] = 0;
				$data_object_kpi['paid_leave'] = 0;
				$data_object_kpi['unpaid_leave'] = 0;
				$data_object_kpi['id'] = 0;
				$data_object_kpi = array_merge($data_object_kpi, $days_header_in_month['days_header']);

			}
			$data_object_kpi['rel_type'] = $rel_type;
			$data_object_kpi['month'] = $current_month;
			$data_object_kpi['staff_id'] = $staff_value['staffid'];

			if ($staff_key == 0) {
				$writer->writeSheetRow('Sheet1', $header_key);
			}

			$get_values_for_keys = $this->get_values_for_keys($data_object_kpi, $header_key);
			$writer->writeSheetRow('Sheet1', $get_values_for_keys);

		}

		$filename = 'attendance_sample_file' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_ATTENDANCE_SAMPLE . $filename, $filename));
		$filename = HR_PAYROLL_CREATE_ATTENDANCE_SAMPLE.$filename;


		echo json_encode([
			'success' => true,
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => $filename,
		]);

	}

	/**
	 * get values for keys
	 * @param  [type] $mapping
	 * @param  [type] $keys
	 * @return [type]
	 */
	function get_values_for_keys($mapping, $keys) {
		foreach ($keys as $key) {
			$output_arr[] = $mapping[$key];
		}
		return $output_arr;
	}

	/**
	 * import attendance excel
	 * @return [type]
	 */
	public function import_attendance_excel() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_employee') && !hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$user_id = $this->login_user->id;

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();
				$rel_type = hrp_get_timesheets_status();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

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
							app_lang('staffid') => 'string',
							app_lang('id') => 'string',
							app_lang('hr_code') => 'string',
							app_lang('staff_name') => 'string',
							app_lang('department') => 'string',
							app_lang('integration_actual_workday') => 'string',
							app_lang('integration_paid_leave') => 'string',
							app_lang('integration_unpaid_leave') => 'string',
							app_lang('standard_working_time_of_month') => 'string',
							app_lang('month') => 'string',
							app_lang('error') => 'string',
						);

						$writer = new \XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new \XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['staff_id'] = 0;
						$arr_header['id'] = 1;
						$arr_header['hr_code'] = 2;
						$arr_header['staff_name'] = 3;
						$arr_header['staff_departments'] = 4;
						$arr_header['actual_workday'] = 5;
						$arr_header['paid_leave'] = 6;
						$arr_header['unpaid_leave'] = 7;
						$arr_header['standard_workday'] = 8;
						$arr_header['month'] = 9;

						$total_rows = 0;
						$total_row_false = 0;

						$column_key = $data[1];
						for ($row = 1; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$flag_staff_id = 0;

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

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd = array_combine($column_key, $data[$row]);
								unset($rd['employee_number']);
								unset($rd['employee_name']);
								unset($rd['department_name']);
								unset($rd['hr_code']);
								unset($rd['staff_name']);
								unset($rd['staff_departments']);

								$rows[] = $rd;
								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {
							$this->hr_payroll_model->import_attendance_data($arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_attendance_error_' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PAYROLL_ERROR . $filename, $filename));
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
			'filename' => HR_PAYROLL_ERROR . $filename,
		]);
	}

	/**
	 * attendance calculation
	 * @return [type]
	 */
	public function attendance_calculation() {
		if (!hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$data = $this->request->getPost();
		$this->hr_payroll_model->attendance_calculation($data);
		$message = app_lang('updated_successfully');
		echo json_encode([
			'message' => $message,
		]);
	}

	/**
	 * manage deductions
	 * @return [type]
	 */
	public function manage_deductions() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_deduction') && !hrp_has_permission('hr_payroll_can_view_own_hrp_deduction') && !is_admin()) {
			access_denied('hrp_deduction');
		}

		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$deductions_data = $this->hr_payroll_model->get_deductions_data($current_month);
		$deductions_value = [];
		if (count($deductions_data) > 0) {
			foreach ($deductions_data as $key => $value) {
				$deductions_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get deduction data for the first
		$format_deduction_value = $this->hr_payroll_model->get_format_deduction_data();

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

			$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);

			$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

			if (isset($deductions_value[$staff_value['staffid'] . '_' . $current_month])) {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($deductions_value[$staff_value['staffid'] . '_' . $current_month]['deduction_value'])) {

					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $deductions_value[$staff_value['staffid'] . '_' . $current_month]['deduction_value']);
				} else {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);
				}

				$data_object_kpi[$staff_key]['id'] = $deductions_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);

				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;

		}

		//check is add new or update data
		if (count($deductions_value) > 0) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_deduction_value['column_format']);
		$data['col_header'] = json_encode($format_deduction_value['header']);

		return $this->template->rander('Hr_payroll\Views\deductions/deductions_manage', $data);
	}

	/**
	 * add manage deductions
	 */
	public function add_manage_deductions() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_deduction') && !hrp_has_permission('hr_payroll_can_edit_hrp_deduction') && !is_admin()) {
			access_denied('hrp_deduction');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if ($data['hrp_deductions_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->deductions_update($data);
			} else {
				$success = false;
			}

			if ($success) {
				$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
			} else {
				$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
			}

			app_redirect(('hr_payroll/manage_deductions'));
		}

	}

	/**
	 * deductions filter
	 * @return [type]
	 */
	public function deductions_filter() {
		$data = $this->request->getPost();

		$rel_type = hrp_get_hr_profile_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);
		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$deductions_data = $this->hr_payroll_model->get_deductions_data($month_filter);
		$deductions_value = [];
		foreach ($deductions_data as $key => $value) {
			$deductions_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_deduction_value = $this->hr_payroll_model->get_format_deduction_data();

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);


				$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

				if (isset($deductions_value[$staff_value['staffid'] . '_' . $month_filter])) {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					if (isset($deductions_value[$staff_value['staffid'] . '_' . $month_filter]['deduction_value'])) {

						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $deductions_value[$staff_value['staffid'] . '_' . $month_filter]['deduction_value']);
					} else {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);
					}

					$data_object_kpi[$staff_key]['id'] = $deductions_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

				} else {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_deduction_value['array_deduction']);

					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $month_filter;
			}

		}

		//check is add new or update data
		if (count($deductions_value) > 0) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * manage commissions
	 * @return [type]
	 */
	public function manage_commissions() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_commission') && !hrp_has_permission('hr_payroll_can_view_own_hrp_commission') && !is_admin()) {
			access_denied('hrp_commission');
		}


		$rel_type = hrp_get_hr_profile_status();
		$commission_type = hrp_get_commission_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$commissions_data = $this->hr_payroll_model->get_commissions_data($current_month);
		$commissions_value = [];
		if (count($commissions_data) > 0) {
			foreach ($commissions_data as $key => $value) {
				$commissions_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get deduction data for the first
		$format_commission_value = $this->hr_payroll_model->get_format_commission_data();

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

			$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);

			$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

			if (isset($commissions_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['commission_amount'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['commission_amount'];
				$data_object_kpi[$staff_key]['id'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				$data_object_kpi[$staff_key]['commission_amount'] = 0;
				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;
			$data_object_kpi[$staff_key]['rel_type'] = $commission_type;

		}

		//check is add new or update data
		if (count($commissions_value) > 0) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);
		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_commission_value['column_format']);
		$data['col_header'] = json_encode($format_commission_value['headers']);

		return $this->template->rander('Hr_payroll\Views\commissions/commissions_manage', $data);
	}

	/**
	 * add manage commissions
	 */
	public function add_manage_commissions() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_commission') && !hrp_has_permission('hr_payroll_can_edit_hrp_commission') && !is_admin()) {
			access_denied('hrp_commission');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if ($data['hrp_commissions_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->commissions_update($data);
			} elseif ($data['hrp_commissions_rel_type'] == 'synchronization') {
				//synchronization
				$success = $this->hr_payroll_model->commissions_synchronization($data);

			} else {
				$success = false;
			}

			if ($success) {
				$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
			} else {
				$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
			}

			app_redirect('hr_payroll/manage_commissions');
		}

	}

	/**
	 * commissions filter
	 * @return [type]
	 */
	public function commissions_filter() {
		$data = $this->request->getPost();

		$rel_type = hrp_get_hr_profile_status();
		$commission_type = hrp_get_commission_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$commissions_data = $this->hr_payroll_model->get_commissions_data($month_filter);
		$commissions_value = [];
		if (count($commissions_data) > 0) {
			foreach ($commissions_data as $key => $value) {
				$commissions_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);

				$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

				if (isset($commissions_value[$staff_value['staffid'] . '_' . $month_filter])) {

					$data_object_kpi[$staff_key]['commission_amount'] = $commissions_value[$staff_value['staffid'] . '_' . $month_filter]['commission_amount'];
					$data_object_kpi[$staff_key]['id'] = $commissions_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

				} else {

					$data_object_kpi[$staff_key]['commission_amount'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $month_filter;
				$data_object_kpi[$staff_key]['rel_type'] = $commission_type;
			}

		}

		//check is add new or update data
		if (count($commissions_value) > 0) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * [import_xlsx_commissions
	 * @return [type]
	 */
	public function import_xlsx_commissions() {
		$user_id = $this->login_user->id;
		$_personal_language = get_setting('user_' . $user_id . '_personal_language');
		if(strlen($_personal_language) == 0){
			$_personal_language = get_setting("language");
		}

		$data['active_language'] = $_personal_language;

		return $this->template->rander('Hr_payroll\Views/commissions/import_commissions', $data);
	}

	/**
	 * create commissions sample file
	 * @return [type]
	 */
	public function create_commissions_sample_file() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_commission') && !hrp_has_permission('hr_payroll_can_edit_hrp_commission') && !is_admin()) {
			access_denied('hrp_commission');

		}
		
		$user_id = $this->login_user->id;

		$month_commission = $this->request->getPost('month_commissions');

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_COMMISSIONS_SAMPLE);

		$rel_type = hrp_get_commission_status();
		//get commission data
		$current_month = date('Y-m-d', strtotime($month_commission . '-01'));
		//get day header in month
		$format_commission_data = $this->hr_payroll_model->get_format_commission_data($current_month, $rel_type);
		$header_key = $format_commission_data['staff_information'];

		$commissions = $this->hr_payroll_model->get_commissions_data($current_month);
		$commissions_value = [];
		foreach ($commissions as $key => $value) {
			$commissions_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		//Writer file
		$writer_header = [];
		$widths = [];
		foreach ($format_commission_data['headers'] as $value) {
			$writer_header[$value] = 'string';
			$widths[] = 30;
		}

		$writer = new \XLSXWriter();

		$col_style1 = [0, 1, 2, 3, 4, 5, 6];
		$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

		$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
			$col_style1, $style1);

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			$staffid = 0;
			$id = 0;
			$staff_name = '';
			$staff_departments = '';
			$commissions_amount = 0;

			/*check value from database*/
			$staffid = $staff_value['staffid'];

			/*check value from database*/
			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi['employee_number'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
				}

				$data_object_kpi['employee_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

				$data_object_kpi['department_name'] = $list_department->name;

			} else {
				$data_object_kpi['employee_number'] = '';
				$data_object_kpi['employee_name'] = '';
				$data_object_kpi['department_name'] = '';

			}

			if (isset($commissions_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi['commission_amount'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['commission_amount'];
				$data_object_kpi['id'] = $commissions_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {
				$data_object_kpi['commission_amount'] = 0;
				$data_object_kpi['id'] = 0;

			}
			$data_object_kpi['rel_type'] = $rel_type;
			$data_object_kpi['month'] = $current_month;
			$data_object_kpi['staff_id'] = $staff_value['staffid'];

			if ($staff_key == 0) {
				$writer->writeSheetRow('Sheet1', $header_key);
			}
			$get_values_for_keys = $this->get_values_for_keys($data_object_kpi, $header_key);

			$writer->writeSheetRow('Sheet1', $get_values_for_keys);

		}

		$filename = 'commission_sample_file' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
		$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_COMMISSIONS_SAMPLE . $filename, $filename));

		echo json_encode([
			'success' => true,
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => HR_PAYROLL_CREATE_COMMISSIONS_SAMPLE . $filename,
		]);

	}

	/**
	 * import commissions excel
	 * @return [type]
	 */
	public function import_commissions_excel() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_commission') && !hrp_has_permission('hr_payroll_can_edit_hrp_commission') && !is_admin()) {
			access_denied('hrp_commission');
		}
		$user_id = $this->login_user->id;

		if (!class_exists('XLSXReader_fin')) {
			require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXReader/XLSXReader.php';
		}
		require_once module_dir_path(HR_PAYROLL_MODULE_NAME) . '/assets/plugins/XLSXWriter/xlsxwriter.class.php';

		$filename = '';
		if ($this->request->getPost()) {
			if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {

				$this->delete_error_file_day_before();
				$rel_type = hrp_get_timesheets_status();

				// Get the temp file path
				$tmpFilePath = $_FILES['file_csv']['tmp_name'];
				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					$rows = [];
					$arr_insert = [];

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
							app_lang('staffid') => 'string',
							app_lang('id') => 'string',
							app_lang('hr_code') => 'string',
							app_lang('staff_name') => 'string',
							app_lang('department') => 'string',
							app_lang('integration_actual_workday') => 'string',
							app_lang('integration_paid_leave') => 'string',
							app_lang('integration_unpaid_leave') => 'string',
							app_lang('standard_working_time_of_month') => 'string',
							app_lang('month') => 'string',
							app_lang('error') => 'string',
						);

						$writer = new \XLSXWriter();
						$writer->writeSheetHeader('Sheet1', $writer_header, $col_options = ['widths' => [40, 40, 40, 50, 40, 40, 40, 40, 50, 50]]);

						//Reader file
						$xlsx = new \XLSXReader_fin($newFilePath);
						$sheetNames = $xlsx->getSheetNames();
						$data = $xlsx->getSheetData($sheetNames[1]);

						$arr_header = [];

						$arr_header['staff_id'] = 0;
						$arr_header['id'] = 1;
						$arr_header['hr_code'] = 2;
						$arr_header['staff_name'] = 3;
						$arr_header['staff_departments'] = 4;
						$arr_header['actual_workday'] = 5;
						$arr_header['paid_leave'] = 6;
						$arr_header['unpaid_leave'] = 7;
						$arr_header['standard_workday'] = 8;
						$arr_header['month'] = 9;

						$total_rows = 0;
						$total_row_false = 0;

						$column_key = $data[1];
						for ($row = 2; $row < count($data); $row++) {

							$total_rows++;

							$rd = array();
							$flag = 0;
							$flag2 = 0;

							$string_error = '';
							$flag_position_group;
							$flag_department = null;

							$flag_staff_id = 0;

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

								$total_row_false++;
							}

							if ($flag == 0 && $flag2 == 0) {

								$rd = array_combine($column_key, $data[$row]);
								unset($rd['employee_number']);
								unset($rd['employee_name']);
								unset($rd['department_name']);
								unset($rd['hr_code']);
								unset($rd['staff_name']);
								unset($rd['staff_departments']);

								$rows[] = $rd;
								array_push($arr_insert, $rd);

							}

						}

						//insert batch
						if (count($arr_insert) > 0) {
							$this->hr_payroll_model->import_commissions_data($arr_insert);
						}

						$total_rows = $total_rows;
						$total_row_success = isset($rows) ? count($rows) : 0;
						$dataerror = '';
						$message = 'Not enought rows for importing';

						if ($total_row_false != 0) {
							$filename = 'Import_commissions_error_' . $user_id . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
							$writer->writeToFile(str_replace($filename, HR_PAYROLL_ERROR . $filename, $filename));
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
			'filename' => HR_PAYROLL_ERROR . $filename,
		]);
	}

	/**
	 * manage income taxs
	 * @return [type]
	 */
	public function income_taxs_manage() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_income_tax') && !hrp_has_permission('hr_payroll_can_view_own_hrp_income_tax') && !is_admin()) {
			access_denied('hrp_income_tax');
		}

		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$income_taxs_data = $this->hr_payroll_model->get_income_tax_data($current_month);
		$income_taxs_value = [];
		if (count($income_taxs_data) > 0) {
			foreach ($income_taxs_data as $key => $value) {
				$income_taxs_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get tax for year
		$total_income_tax_in_year = $this->hr_payroll_model->get_total_income_tax_in_year($current_month);
		$tax_in_year = [];
		foreach ($total_income_tax_in_year as $t_key => $t_value) {
			$tax_in_year[$t_value['staff_id']] = $t_value;
		}

		//get deduction data for the first
		$format_income_tax_value = $this->hr_payroll_model->get_format_income_tax_data();

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

			$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);

			$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

			if (isset($income_taxs_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['income_tax'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['income_tax'];
				$data_object_kpi[$staff_key]['id'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				$data_object_kpi[$staff_key]['income_tax'] = 0;
				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;

			if (isset($tax_in_year[$staff_value['staffid']])) {
				$data_object_kpi[$staff_key]['tax_for_year'] = $tax_in_year[$staff_value['staffid']]['tax_for_year'];
			} else {
				$data_object_kpi[$staff_key]['tax_for_year'] = 0;
			}
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_income_tax_value['column_format']);
		$data['col_header'] = json_encode($format_income_tax_value['headers']);

		return $this->template->rander('Hr_payroll\Views\income_tax/income_tax_manage', $data);
	}

	/**
	 * income taxs filter
	 * @return [type]
	 */
	public function income_taxs_filter() {
		$data = $this->request->getPost();

		$rel_type = hrp_get_hr_profile_status();
		$commission_type = hrp_get_commission_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$current_month = date('Y-m-d', strtotime($data['month'] . '-01'));
		$income_taxs_data = $this->hr_payroll_model->get_income_tax_data($current_month);
		$income_taxs_value = [];
		if (count($income_taxs_data) > 0) {
			foreach ($income_taxs_data as $key => $value) {
				$income_taxs_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get tax for year
		$total_income_tax_in_year = $this->hr_payroll_model->get_total_income_tax_in_year($current_month);
		$tax_in_year = [];
		foreach ($total_income_tax_in_year as $t_key => $t_value) {
			$tax_in_year[$t_value['staff_id']] = $t_value;
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);


				$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

				if (isset($income_taxs_value[$staff_value['staffid'] . '_' . $current_month])) {

					$data_object_kpi[$staff_key]['income_tax'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['income_tax'];
					$data_object_kpi[$staff_key]['id'] = $income_taxs_value[$staff_value['staffid'] . '_' . $current_month]['id'];

				} else {

					$data_object_kpi[$staff_key]['income_tax'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $current_month;
				if (isset($tax_in_year[$staff_value['staffid']])) {
					$data_object_kpi[$staff_key]['tax_for_year'] = $tax_in_year[$staff_value['staffid']]['tax_for_year'];
				} else {
					$data_object_kpi[$staff_key]['tax_for_year'] = 0;
				}
			}

		}

		echo json_encode([
			'data_object' => $data_object_kpi,
		]);
		die;
	}

	/**
	 * manage insurances
	 * @return [type]
	 */
	public function manage_insurances() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_insurrance') && !hrp_has_permission('hr_payroll_can_view_own_hrp_insurrance') && !is_admin()) {
			access_denied('hrp_insurrance');
		}


		$rel_type = hrp_get_hr_profile_status();

		//get current month
		$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		$insurances_data = $this->hr_payroll_model->get_insurances_data($current_month);
		$insurances_value = [];
		if (count($insurances_data) > 0) {
			foreach ($insurances_data as $key => $value) {
				$insurances_value[$value['staff_id'] . '_' . $value['month']] = $value;
			}
		}

		//get insurance data for the first
		$format_insurance_value = $this->hr_payroll_model->get_format_insurance_data();

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

			if ($rel_type == 'hr_records') {
				$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
			} else {
				$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
			}

			$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

			$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);

			$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

			if (isset($insurances_value[$staff_value['staffid'] . '_' . $current_month])) {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				if (isset($insurances_value[$staff_value['staffid'] . '_' . $current_month]['insurance_value'])) {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $insurances_value[$staff_value['staffid'] . '_' . $current_month]['insurance_value']);
				} else {
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);
				}

				$data_object_kpi[$staff_key]['id'] = $insurances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

			} else {

				// array merge: staff information + earning list (probationary contract) + earning list (formal)
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);

				$data_object_kpi[$staff_key]['id'] = 0;

			}
			$data_object_kpi[$staff_key]['month'] = $current_month;

		}

		//check is add new or update data
		if (count($insurances_value) > 0) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['staffs'] = $staffs;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($format_insurance_value['column_format']);
		$data['col_header'] = json_encode($format_insurance_value['header']);

		return $this->template->rander('Hr_payroll\Views\insurances/insurances_manage', $data);
	}

	/**
	 * add manage insurances
	 */
	public function add_manage_insurances() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_insurrance') && !hrp_has_permission('hr_payroll_can_edit_hrp_insurrance') && !is_admin()) {
			access_denied('hrp_insurrance');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if ($data['hrp_insurances_rel_type'] == 'update') {
				// update data
				$success = $this->hr_payroll_model->insurances_update($data);
			} else {
				$success = false;
			}

			if ($success) {
				$this->session->setFlashdata("success_message", app_lang("updated_successfully"));
			} else {
				$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
			}

			app_redirect(('hr_payroll/manage_insurances'));
		}

	}

	/**
	 * insurances filter
	 * @return [type]
	 */
	public function insurances_filter() {
		$data = $this->request->getPost();

		$rel_type = hrp_get_hr_profile_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		//get current month
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		$insurances_data = $this->hr_payroll_model->get_insurances_data($month_filter);
		$insurances_value = [];
		foreach ($insurances_data as $key => $value) {
			$insurances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//get employee data for the first
		$format_insurance_value = $this->hr_payroll_model->get_format_insurance_data();

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			$data_object_kpi = [];

			foreach ($staffs as $staff_key => $staff_value) {
				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				if ($rel_type == 'hr_records') {
					$data_object_kpi[$staff_key]['employee_number'] = $staff_value['staff_identifi'];
				} else {
					$data_object_kpi[$staff_key]['employee_number'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_value['staffid'], 5);
				}

				$data_object_kpi[$staff_key]['employee_name'] = $staff_value['first_name'] . ' ' . $staff_value['last_name'];

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);


				$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

				if (isset($insurances_value[$staff_value['staffid'] . '_' . $month_filter])) {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					if (isset($insurances_value[$staff_value['staffid'] . '_' . $month_filter]['insurance_value'])) {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $insurances_value[$staff_value['staffid'] . '_' . $month_filter]['insurance_value']);
					} else {
						$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);
					}

					$data_object_kpi[$staff_key]['id'] = $insurances_value[$staff_value['staffid'] . '_' . $month_filter]['id'];

				} else {

					// array merge: staff information + earning list (probationary contract) + earning list (formal)
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $format_insurance_value['array_insurance']);

					$data_object_kpi[$staff_key]['id'] = 0;

				}
				$data_object_kpi[$staff_key]['month'] = $month_filter;
			}

		}

		//check is add new or update data
		if (count($insurances_value) > 0) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'button_name' => $button_name,
		]);
		die;
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
			$folder = HR_PAYROLL_ERROR;
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
	 * payslip manage
	 * @param  string $id
	 * @return [type]
	 */
	public function payslip_manage($id = '') {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_payslip') && !hrp_has_permission('hr_payroll_can_view_own_hrp_payslip') && !is_admin()) {
			access_denied('hrp_payslip');
		}
		$data['internal_id'] = $id;
		$data['title'] = app_lang('hr_pay_slips');
		return $this->template->rander('Hr_payroll\Views\payslips/payslip_manage', $data);
	}

	/**
	 * payslip table
	 * @return table
	 */
	public function payslip_table() {
		$dataPost = $this->request->getPost();
		$this->hr_payroll_model->get_table_data(module_views_path('Hr_payroll', 'payslips/payslip_table'), $dataPost);
	}

	/**
	 * delete payslip
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_payslip() {
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_delete_hrp_payslip')) {
			access_denied('hrp_payslip');
		}
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_payroll/payslip_manage'));
		}

		$response = $this->hr_payroll_model->delete_payslip($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect(('hr_payroll/payslip_manage'));
	}




	/**
	 * payslip manage
	 * @param  string $id
	 * @return [type]
	 */
	public function payslip_templates_manage($id = '') {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_payslip_template') && !hrp_has_permission('hr_payroll_can_view_own_hrp_payslip_template') && !is_admin()) {
			access_denied('hrp_payslip_template');
		}

		$data['staffs'] = $this->hr_payroll_model->get_staff_timekeeping_applicable_object('status= "active"');
		$data['internal_id'] = $id;

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['title'] = app_lang('payslip_template');
		return $this->template->rander('Hr_payroll\Views\payslip_templates/payslip_template_manage', $data);
	}

	/**
	 * payslip table
	 * @return table
	 */
	public function payslip_template_table() {
		$dataPost = $this->request->getPost();
		$this->hr_payroll_model->get_table_data(module_views_path('Hr_payroll', 'payslip_templates/payslip_template_table'), $dataPost);
	}

	/**
	 * payroll columns
	 * @return [type] 
	 */
	public function payroll_columns()
	{
		hr_payroll_get_status_modules('hr_payroll');

		$data['title'] = app_lang('hrp_payroll_columns');
		$data['payroll_column_value'] = $this->hr_payroll_model->get_hrp_payroll_columns();
		$data['order_display_in_paylip'] = $this->hr_payroll_model->count_payroll_column();

		return $this->template->rander("Hr_payroll\Views\includes\payroll_columns", $data);
	}

	/**
	 * get column key html add
	 * @return [type]
	 */
	public function get_payroll_column_method_html_add() {
		$method_option = $this->hr_payroll_model->get_list_payroll_column_method(['id' => '']);
		$order_display = $this->hr_payroll_model->count_payroll_column();

		echo json_encode([
			'method_option' => $method_option['method_option'],
			'order_display' => $order_display,

		]);
	}

	/**
	 * get payroll column function name html
	 * @return [type]
	 */
	public function get_payroll_column_function_name_html() {
		$method_option = $this->hr_payroll_model->get_list_payroll_column_function_name(['function_name' => '']);

		echo json_encode([
			'method_option' => $method_option['method_option'],

		]);
	}

	/**
	 * payroll column
	 * @return [type]
	 */
	public function payroll_column() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				if (!is_admin() && !hrp_has_permission('hr_payroll_can_create_hrp_setting')) {
					app_redirect("forbidden");
				}

				$add = $this->hr_payroll_model->add_payroll_column($data);
				if ($add) {
					$message = app_lang('added_successfully', app_lang('payroll_column'));
					$this->session->setFlashdata("success_message", app_lang("hrp_added_successfully"));
				}
				app_redirect(('hr_payroll/payroll_columns'));
			} else {

				if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_setting')) {
					app_redirect("forbidden");
				}

				$id = $data['id'];
				unset($data['id']);
				$success = $this->hr_payroll_model->update_payroll_column($data, $id);
				if ($success == true) {
					$message = app_lang('updated_successfully', app_lang('payroll_column'));
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));
				}
				app_redirect(('hr_payroll/payroll_columns'));
			}

		}
	}

	/**
	 * get payroll column
	 * @param  [type] $id
	 * @return [type]
	 */
	public function get_payroll_column($id) {
		//get data
		$payroll_column = $this->hr_payroll_model->get_hrp_payroll_columns($id);
		$method_option_selected = '';
		$function_name_selected = '';
		//get taking method html
		if ($payroll_column) {
			$method_option = $this->hr_payroll_model->get_list_payroll_column_method(['taking_method' => $payroll_column->taking_method]);
		} else {
			$method_option = $this->hr_payroll_model->get_list_payroll_column_method(['taking_method' => '']);
		}
		//get function name html
		if ($payroll_column) {
			$function_name = $this->hr_payroll_model->get_list_payroll_column_function_name(['function_name' => $payroll_column->function_name]);
		} else {
			$function_name = $this->hr_payroll_model->get_list_payroll_column_function_name(['function_name' => '']);
		}

		echo json_encode([
			'payroll_column' => $payroll_column,
			'method_option' => $method_option,
			'function_name' => $function_name,
			'method_option_selected' => $method_option['method_option_selected'],
			'function_name_selected' => $function_name['function_name_selected'],
		]);
		die;

	}

	/**
	 * delete payroll column setting
	 * @param  string $id
	 * @return [type]
	 */
	public function delete_payroll_column_setting() {
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_delete_hrp_setting')) {
			app_redirect("forbidden");
		}
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_payroll/payroll_columns'));
		}

		$response = $this->hr_payroll_model->delete_payroll_column($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_payroll/payroll_columns');
	}

	/**
	 * get payslip template
	 * @param  string $id
	 * @return [type]
	 */
	public function get_payslip_template($id = '') {
		$payslip_template_data = '';
		if (isset($id) && $id != '') {
			$payslip_template = $this->hr_payroll_model->get_hrp_payslip_templates($id);
			// update
			$payslip_template_selected = $this->hr_payroll_model->get_payslip_template_selected_html($payslip_template->payslip_id_copy);
			$payslip_column_selected = $this->hr_payroll_model->get_payslip_column_html($payslip_template->payslip_columns);
			$payslip_template_data = $payslip_template;

		} else {
			// create
			$payslip_template_selected = $this->hr_payroll_model->get_payslip_template_selected_html('');
			$payslip_column_selected = $this->hr_payroll_model->get_payslip_column_html('');
		}

		echo json_encode([
			'payslip_template_selected' => $payslip_template_selected,
			'payslip_column_selected' => $payslip_column_selected,
			'payslip_template_data' => $payslip_template_data,
		]);
		die;

	}

	/**
	 * payslip template
	 * @return [type]
	 */
	public function payslip_template() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_payslip_template') && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip_template') && !is_admin()) {
			access_denied('hrp_payslip_template');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (!$this->request->getPost('id')) {

				if (!is_admin() && !hrp_has_permission('hr_payroll_can_create_hrp_payslip_template')) {
					access_denied('hrp_payslip_template');
				}

				$insert_id = $this->hr_payroll_model->add_payslip_template($data);
				if ($insert_id) {
					$this->hr_payroll_model->add_payslip_templates_detail_first($insert_id);
					$this->session->setFlashdata("success_message", app_lang("hrp_added_successfully"));
				}
				app_redirect(('hr_payroll/view_payslip_templates_detail/' . $insert_id));
			} else {

				if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip_template')) {
					access_denied('hrp_payslip_template');
				}

				$id = $data['id'];
				unset($data['id']);

				$edit_payslip_column = false;
				if (isset($data['edit_payslip_column']) && $data['edit_payslip_column'] == 'true') {
					$edit_payslip_column = true;
					unset($data['edit_payslip_column']);
				}

				$check_update_detail = false;
				$check_update_detail = $this->hr_payroll_model->check_update_payslip_template_detail($data, $id);
				$success = $this->hr_payroll_model->update_payslip_template($data, $id);

				if ($success == true) {
					if ($check_update_detail['status']) {
						$this->hr_payroll_model->update_payslip_templates_detail_first($check_update_detail['old_column_formular'], $id);
					}
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_successfully"));
				}
				app_redirect(('hr_payroll/view_payslip_templates_detail/' . $id));
			}

		}
	}

	/**
	 * delete payslip template
	 * @param  [type] $id
	 * @return [type]
	 */
	public function delete_payslip_template() {
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_delete_hrp_payslip_template')) {
			app_redirect("forbidden");
		}
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_payroll/payslip_templates_manage'));
		}

		$response = $this->hr_payroll_model->delete_payslip_template($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_payroll/payslip_templates_manage');
	}

	/**
	 * view payslip templates detail, add or edit
	 * @param [type] $parent_id
	 * @param string $id
	 */
	public function view_payslip_templates_detail($id = "") {

		$data_form = $this->request->getPost();
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip_template') && !hrp_has_permission('hr_payroll_can_create_hrp_payslip_template')) {
				$message = app_lang('access_denied');
				echo json_encode(['danger' => false, 'message' => $message]);
				die;
			}

			$id = $data['id'];
			unset($data['id']);
			$success = $this->hr_payroll_model->update_payslip_templates_detail($data, $id);

			if ($success == true) {
				$message = app_lang('payslip_template') . ' ' . app_lang('updated_successfully');
				$file_excel = $this->hr_payroll_model->get_hrp_payslip_templates($id);
				echo json_encode(['success' => true, 'message' => $message, 'name_excel' => $file_excel->templates_name]);
				die;
			} else {
				$message = app_lang('payslip_template') . ' ' . app_lang('updated_failed');
				echo json_encode(['success' => false, 'message' => $message]);
				die;
			}

		}

		if ($id != '') {
			$data['id'] = $id;
			$data['file_excel'] = $this->hr_payroll_model->get_hrp_payslip_templates($data['id']);
			$data['data_form'] = $data['file_excel']->payslip_template_data;

		}
		if (hrp_has_permission('hr_payroll_can_create_hrp_payslip_template') || hrp_has_permission('hr_payroll_can_edit_hrp_payslip_template')) {

			$permission_actions = '<button id="luckysheet_info_detail_save" class="BTNSS btn btn-info luckysheet_info_detail_save pull-right">Save</button><a id="luckysheet_info_detail_export" class="btn btn-info luckysheet_info_detail_export pull-right"> Download</a><a href="' . get_uri('hr_payroll/payslip_templates_manage') . '" class="btn mright5 btn-default pull-right" >Back</a>';
		} else {
			$permission_actions = '<a id="luckysheet_info_detail_export" class="btn btn-info luckysheet_info_detail_export pull-right"> Download</a><a href="' . get_uri('hr_payroll/payslip_templates_manage') . '" class="btn mright5 btn-default pull-right" >Back</a>';
		}

		$data['permission_actions'] = $permission_actions;

		$data['title'] = app_lang('view_payslip_templates_detail');

		return $this->hrp_rander('Hr_payroll\Views\payslip_templates/add_payslip_template', $data);

	}

	/**
	 * hrp rander
	 * @param  [type] $view 
	 * @param  array  $data 
	 * @return [type]       
	 */
	public function hrp_rander($view, $data = array()) {
		$view_data['content_view'] = $view;
        $view_data['topbar'] = "includes/topbar";

        $view_data = array_merge($view_data, $data);

		return $this->hrp_view('Hr_payroll\Views\includes\layout/index', $view_data);
	}

	/**
	 * hrp view
	 * @param  [type] $view 
	 * @param  array  $data 
	 * @return [type]       
	 */
	public function hrp_view($view, $data = array()) {
		$view_data = array();

		$users_model = model("App\Models\Users_model", false);
		if ($users_model->login_user_id()) {
            //user logged in, prepare login user data
			$Security_Controller = new Security_Controller(false);
			$view_data["login_user"] = $Security_Controller->login_user;
		}

		$view_data = array_merge($view_data, $data);

		return view($view, $view_data);
	}

	/**
	 * view payslip
	 * @param  string $id
	 * @return [type]
	 */
	public function view_payslip_detail($id = "") {

		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_payslip')) {
			access_denied('view_payslip');
		}

		$data_form = $this->request->getPost();

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip') && !hrp_has_permission('hr_payroll_can_create_hrp_payslip')) {
				$message = app_lang('access_denied');
				echo json_encode(['danger' => false, 'message' => $message]);
				die;
			}
			$id = $data['id'];
			unset($data['id']);
			$success = $this->hr_payroll_model->update_payslip($data, $id);
			if ($success == true) {
				$message = app_lang('payslip_template') . ' ' . app_lang('updated_successfully');
				echo json_encode(['success' => true, 'message' => $message]);
				die;
			} else {
				$message = app_lang('payslip_template') . ' ' . app_lang('updated_failed');
				echo json_encode(['success' => false, 'message' => $message]);
				die;
			}

		}

		if ($id != '') {
			$data['id'] = $id;
			$payslip = $this->hr_payroll_model->get_hrp_payslip($data['id']);

			$data['payslip'] = $payslip;

			$path = HR_PAYROLL_PAYSLIP_FILE . $payslip->file_name;
			if(!file_exists($path)){
				$this->session->setFlashdata("error_message", app_lang("hrp_The_physical_file_has_been_deleted"));

				app_redirect(('hr_payroll/payslip_manage'));
			}
			$mystring = file_get_contents($path, true);

			$data['data_form'] = $mystring;

		}

		if (hrp_has_permission('hr_payroll_can_create_hrp_payslip') || hrp_has_permission('hr_payroll_can_edit_hrp_payslip')) {
			$permission_actions = '<button id="save_data" class="btn mright5 btn-primary pull-right luckysheet_info_detail_save" >Save</button>&nbsp<a href="#" class="btn mright5 btn-success pull-right payslip_download hide" >Download</a>&nbsp<button  class="btn mright5 btn-info pull-right luckysheet_info_detail_exports ">Create file</button>&nbsp<button id="payslip_close" class="btn mright5 btn-warning pull-right luckysheet_info_detail_payslip_close" >Payslip closing</button>&nbsp<a href="' . get_uri('hr_payroll/payslip_manage') . '" class="btn mright5 btn-default pull-right " >Back</a>';
		} else {
			$permission_actions = '<a href="#" class="btn mright5 btn-success pull-right payslip_download hide" >Download</a><button  class="btn mright5 btn-info pull-right luckysheet_info_detail_exports ">Create file</button><a href="' . get_uri('hr_payroll/payslip_manage') . '" class="btn mright5 btn-default pull-right" >Back</a>';
		}
		$data['permission_actions'] = $permission_actions;

		$data['title'] = app_lang('payslip_detail');

		return $this->hrp_rander('Hr_payroll\Views\payslips/payslip', $data);

	}

	/**
	 * view payslip detail v2
	 * @param  string $id
	 * @return [type]
	 */
	public function view_payslip_detail_v2($id = "") {
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_own_hrp_payslip')) {
			access_denied('view_payslip');
		}

		$data_form = $this->request->getPost();

		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip') && !hrp_has_permission('hr_payroll_can_create_hrp_payslip')) {
				$message = app_lang('access_denied');
				echo json_encode(['danger' => false, 'message' => $message]);
				die;
			}
			$id = $data['id'];
			unset($data['id']);
			$success = $this->hr_payroll_model->update_payslip($data, $id);
			if ($success == true) {
				$message = app_lang('payslip_template') . ' ' . app_lang('updated_successfully');
				echo json_encode(['success' => true, 'message' => $message]);
				die;
			} else {
				$message = app_lang('payslip_template') . ' ' . app_lang('updated_failed');
				echo json_encode(['success' => false, 'message' => $message]);
				die;
			}

		}

		if ($id != '') {

			$data['id'] = $id;
			$payslip = $this->hr_payroll_model->get_hrp_payslip($data['id']);

			$data['payslip'] = $payslip;

			$path = HR_PAYROLL_PAYSLIP_FILE . $payslip->file_name;
			if(!file_exists($path)){
				$this->session->setFlashdata("error_message", app_lang("hrp_The_physical_file_has_been_deleted"));

				app_redirect(('hr_payroll/payslip_manage'));
			}
			$mystring = file_get_contents($path, true);

			//remove employees not under management
			$mystring = $this->hr_payroll_model->remove_employees_not_under_management_on_payslip($mystring);

			$data['data_form'] = $mystring;

		}

		$permission_actions = '<a href="#" class="btn mright5 btn-success pull-right payslip_download hide" >Download</a><button  class="btn mright5 btn-info pull-right luckysheet_info_detail_exports ">Create file</button><a href="' . get_uri('hr_payroll/payslip_manage') . '" class="btn mright5 btn-default pull-right" >Back</a>';
		$data['permission_actions'] = $permission_actions;

		$data['title'] = app_lang('view_payslip');

		return $this->hrp_rander('Hr_payroll\Views\payslips/payslip_view_own', $data);

	}

	/**
	 * manage bonus
	 * @return [type]
	 */
	public function manage_bonus() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_bonus_kpi') && !hrp_has_permission('hr_payroll_can_view_own_hrp_bonus_kpi') && !is_admin()) {
			access_denied('hrp_bonus_kpi');
		}

		//get current month
		$current_month = date('Y-m');

		//load staff
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];
		$bonus_status = true;

		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/
			$data_object_kpi[$staff_key]['staffid'] = $staff_value['staffid'];

			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
				}

				$data_object_kpi[$staff_key]['staff_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

				$data_object_kpi[$staff_key]['job_position'] = '';


				$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);


				$data_object_kpi[$staff_key]['staff_departments'] = $list_department->name;

			} else {
				$data_object_kpi[$staff_key]['hr_code'] = '';
				$data_object_kpi[$staff_key]['staff_name'] = '';
				$data_object_kpi[$staff_key]['job_position'] = $staff_value['staffid'];
				$data_object_kpi[$staff_key]['staff_departments'] = '';

			}

			//get_data from hrm_allowance_commodity_fill
			$bonus_kpi = $this->hr_payroll_model->get_bonus_by_month($staff_value['staffid'], $current_month);
			if ($bonus_kpi) {

				$data_object_kpi[$staff_key]['bonus_kpi'] = $bonus_kpi->bonus_kpi;

			} else {
				$data_object_kpi[$staff_key]['bonus_kpi'] = 0;
				$bonus_status = false;
			}

		}

		/*bonus Kpi*/
		//check is add new or update data
		if ($bonus_status == true) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$data['staffs_li'] = $staffs;

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['staffs'] = $staffs;
		$data['data_object_kpi'] = $data_object_kpi;

		return $this->template->rander('Hr_payroll\Views\bonus/bonus_kpi', $data);
	}

	/**
	 * add bonus kpi
	 * @return redirect
	 */
	public function add_bonus_kpi() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_bonus_kpi') && !hrp_has_permission('hr_payroll_can_edit_hrp_bonus_kpi') && !is_admin()) {
			access_denied('hrp_bonus_kpi');
		}
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (isset($data)) {

				$success = $this->hr_payroll_model->add_bonus_kpi($data);

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));
				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}
				app_redirect(('hr_payroll/manage_bonus'));
			}

		}
	}

	/**
	 * bonus kpi filter
	 * @return array
	 */
	public function bonus_kpi_filter() {
		$data = $this->request->getPost();

		$months_filter = $data['month'];
		$year = date('Y', strtotime(($data['month'] . '-01')));
		$g_month = date('m', strtotime(($data['month'] . '-01')));

		$querystring = '';

		$department = $data['department'];

		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, '');

		// data return
		$data_object = [];
		$index_data_object = 0;
		$bonus_status = true;

		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			foreach ($staffs as $staffs_key => $staff_value) {

				$bonus_value = $this->hr_payroll_model->get_bonus_by_month($staff_value['staffid'], $months_filter);

				if ($bonus_value) {

					$data_object[$index_data_object]['staffid'] = $staff_value['staffid'];

					$data_object[$index_data_object]['hr_code'] = $staff_value['staff_identifi'];
					$data_object[$index_data_object]['staff_name'] = $staff_value['full_name'];

					$data_object[$index_data_object]['job_position'] = '';

					$data_object[$index_data_object]['bonus_kpi'] = $bonus_value->bonus_kpi;

				} else {
					$data_object[$index_data_object]['staffid'] = $staff_value['staffid'];

					$data_object[$index_data_object]['hr_code'] = $staff_value['staff_identifi'];
					$data_object[$index_data_object]['staff_name'] = $staff_value['full_name'];

					$data_object[$index_data_object]['job_position'] = '';

					$data_object[$index_data_object]['bonus_kpi'] = 0;

					$bonus_status = false;

				}

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_value['staffid']);


				$data_object[$index_data_object]['staff_departments'] = $list_department->name;

				$index_data_object++;

			}

		}

		//check is add new or update data
		if ($bonus_status == true) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object,
			'button_name' => $button_name,
		]);
		die;
	}

	/**
	 * payslip
	 * @param  string $value
	 * @return [type]
	 */
	public function payslip($value = '') {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			if (!$this->request->getPost('id')) {

				if (!is_admin() && !hrp_has_permission('hr_payroll_can_create_hrp_payslip')) {
					access_denied('hrp_payslip');
				}

				$insert_id = $this->hr_payroll_model->add_payslip($data);
				if ($insert_id) {
					$this->session->setFlashdata("success_message", app_lang("hrp_added_successfully"));
				}
				app_redirect('hr_payroll/payslip_manage');
			}
		}
	}

	/**
	 * payslip closing
	 * @return [type]
	 */
	public function payslip_closing() {
		if (!hrp_has_permission('hr_payroll_can_edit_hrp_payslip') && !is_admin()) {
			$message = app_lang('access_denied');
			echo json_encode(['danger' => false, 'message' => $message]);
			die;
		}
		if ($this->request->getPost()) {
			$data = $this->request->getPost();

			$hrp_payslip = $this->hr_payroll_model->get_hrp_payslip($data['id']);

			if ($hrp_payslip) {
				$payslip_checked = $this->hr_payroll_model->payslip_checked($hrp_payslip->payslip_month, $hrp_payslip->payslip_template_id, true);
				// if ($payslip_checked) {

					$result = $this->hr_payroll_model->payslip_close($data);
					if ($result == true) {
						$message = app_lang('hrp_updated_successfully');
						$status = true;
					} else {
						$message = app_lang('hrp_updated_failed');
						$status = false;
					}
				// } else {
				// 	$status = false;
				// 	$message = app_lang('payslip_for_the_month_of');
				// }

			} else {
				$message = app_lang('hrp_updated_failed');
				$status = false;
			}

			echo json_encode([
				'message' => $message,
				'status' => $status,
			]);
		}
	}

	/**
	 * payslip update status
	 * @param  [type] $id
	 * @return [type]
	 */
	public function payslip_update_status($id) {
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip')) {
			access_denied('hrp_payslip');
		}

		$result = $this->hr_payroll_model->update_payslip_status($id, 'payslip_opening');
		if ($result) {
			$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
		}
		app_redirect(('hr_payroll/payslip_manage'));
	}

	/**
	 * table staff payslip
	 * @return [type]
	 */
	public function table_staff_payslip() {
		$dataPost = $this->request->getPost();
		$this->hr_payroll_model->get_table_data(module_views_path('Hr_payroll', 'employee_payslip/table_staff_payslip'), $dataPost);

	}

	/**
	 * view staff payslip modal
	 * @return [type]
	 */
	public function view_staff_payslip_modal() {
		if (!$this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model('departments_model');

		if ($this->request->getPost('slug') === 'view') {
			$payslip_detail_id = $this->request->getPost('payslip_detail_id');

			$data['payslip_detail'] = $this->hr_payroll_model->get_payslip_detail($payslip_detail_id);

			$list_department = $this->hr_payroll_model->getdepartment_name($data['payslip_detail']->staff_id);

			$employee = $this->hr_payroll_model->get_employees_data($data['payslip_detail']->month, '', ' staff_id = ' . $data['payslip_detail']->staff_id);

			$data['employee'] = count($employee) > 0 ? $employee[0] : [];
			$data['list_department'] = $list_department->name;

			$this->template->rander('Hr_payroll\Views\employee_payslip/staff_payslip_modal_view', $data);
		}
	}

	/**
	 * reports
	 * @return [type]
	 */
	public function reports() {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_report') && !is_admin()) {
			access_denied('reports');
		}

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
		return $this->template->rander('Hr_payroll\Views\reports/manage_reports', $data);

	}

	/**
	 * payslip report
	 * @return [type]
	 */
	public function payslip_report() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {

				$months_report = $this->request->getPost('months_filter');
				$position_filter = $this->request->getPost('position_filter');
				$department_filter = $this->request->getPost('department_filter');
				$staff_filter = $this->request->getPost('staff_filter');

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
					$from_date = to_sql_date($this->request->getPost('report_from'));
					$to_date = to_sql_date($this->request->getPost('report_to'));
				}

				$select = [
					'month',
					'pay_slip_number',
					'employee_name',
					'gross_pay',
					'total_deductions',
					'income_tax_paye',
					'it_rebate_value',
					'commission_amount',
					'bonus_kpi',
					'total_insurance',
					'net_pay',
					'total_cost',
				];
				$query = '';

				if (isset($from_date) && isset($to_date)) {

					$query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
				} else {
					$query = '';
				}

				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . $staffid_list . ') and ';
				}
				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= db_prefix() . 'hrp_payslip_details.staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';
				}

				$query .= db_prefix() . 'hrp_payslips.payslip_status = "payslip_closing" and ';

				$total_query = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'id';
				$sTable = db_prefix() . 'hrp_payslip_details';
				$join = [
					'LEFT JOIN ' . db_prefix() . 'hrp_payslips ON ' . db_prefix() . 'hrp_payslip_details.payslip_id = ' . db_prefix() . 'hrp_payslips.id',
				];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
					db_prefix() . 'hrp_payslip_details.id',
					db_prefix() . 'hrp_payslip_details.month',
				]);

				$output = $result['output'];
				$rResult = $result['rResult'];
				foreach ($rResult as $aRow) {
					$row = [];

					$row[] = $aRow['id'];
					$row[] = $aRow['month'];
					$row[] = $aRow['pay_slip_number'];
					$row[] = $aRow['employee_name'];
					$row[] = app_format_money($aRow['gross_pay'], '');
					$row[] = app_format_money($aRow['total_deductions'], '');
					$row[] = app_format_money($aRow['income_tax_paye'], '');
					$row[] = app_format_money($aRow['it_rebate_value'], '');
					$row[] = app_format_money($aRow['commission_amount'], '');
					$row[] = app_format_money($aRow['bonus_kpi'], '');
					$row[] = app_format_money($aRow['total_insurance'], '');
					$row[] = app_format_money($aRow['net_pay'], '');
					$row[] = app_format_money($aRow['total_cost'], '');

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();
			}
		}
	}

	/**
	 * income summary report
	 * @return [type]
	 */
	public function income_summary_report() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				$this->load->model('departments_model');

				$months_report = $this->request->getPost('months_filter');
				$position_filter = $this->request->getPost('position_filter');
				$department_filter = $this->request->getPost('department_filter');
				$staff_filter = $this->request->getPost('staff_filter');

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
					$from_date = to_sql_date($this->request->getPost('report_from'));
					$to_date = to_sql_date($this->request->getPost('report_to'));
				}

				$select = [
					'staffid',

				];
				$query = '';
				$staff_query = '';

				if (isset($from_date) && isset($to_date)) {

					$staff_query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
				} else {
					$staff_query = '';
				}

				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= db_prefix() . 'staff.staffid in (' . $staffid_list . ') and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . $staffid_list . ') and ';
				}

				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= db_prefix() . 'staff.staffid in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';
				}

				$query .= db_prefix() . 'staff.active = "1" and ';

				$total_query = '';
				$staff_query_trim = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				if (($staff_query) && ($staff_query != '')) {
					$staff_query_trim = rtrim($staff_query, ' and');

				}
				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'staffid';
				$sTable = db_prefix() . 'staff';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['firstname', 'lastname']);

				$output = $result['output'];
				$rResult = $result['rResult'];
				$rel_type = hrp_get_hr_profile_status();
				$staff_income = $this->hr_payroll_model->get_income_summary_report($staff_query_trim);

				$staffs_data = [];
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
				foreach ($staffs as $value) {
					$staffs_data[$value['staffid']] = $value;
				}

				$temp = 0;
				foreach ($rResult as $staff_key => $aRow) {
					$row = [];

					$list_department = $this->hr_payroll_model->getdepartment_name($aRow['staffid']);

					$data_object_kpi[$staff_key]['department_name'] = $list_department->name;

					if ($rel_type == 'hr_records') {
						if (isset($staffs_data[$aRow['staffid']])) {
							$row[] = $staffs_data[$aRow['staffid']]['staff_identifi'];
						} else {
							$row[] = '';
						}
					} else {
						$row[] = $this->hr_payroll_model->hrp_format_code('EXS', $aRow['staffid'], 5);
					}

					$row[] = $aRow['firstname'] . ' ' . $aRow['lastname'];

					$row[] = $list_department;

					if (isset($staff_income[$aRow['staffid']]['01'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['01'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['02'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['02'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['03'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['03'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['04'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['04'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['05'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['05'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['06'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['06'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['07'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['07'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['08'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['08'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['09'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['09'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['10'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['10'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['11'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['11'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if (isset($staff_income[$aRow['staffid']]['12'])) {
						$row[] = app_format_money($staff_income[$aRow['staffid']]['12'], '');
						$temp++;
					} else {
						$row[] = 0;
					}

					if ($temp != 0) {
						if (isset($staff_income[$aRow['staffid']]['average_income'])) {

							$row[] = app_format_money($staff_income[$aRow['staffid']]['average_income'] / $temp, '');
						} else {
							$row[] = 0;
						}
					} else {
						$row[] = 0;
					}

					$temp = 0;
					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();

			}
		}
	}

	/**
	 * insurance cost summary report
	 * @return [type]
	 */
	public function insurance_cost_summary_report() {
		if ($this->input->is_ajax_request()) {
			if ($this->request->getPost()) {
				$this->load->model('departments_model');

				$months_report = $this->request->getPost('months_filter');
				$position_filter = $this->request->getPost('position_filter');
				$department_filter = $this->request->getPost('department_filter');
				$staff_filter = $this->request->getPost('staff_filter');

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
					$from_date = to_sql_date($this->request->getPost('report_from'));
					$to_date = to_sql_date($this->request->getPost('report_to'));
				}

				$select = [
					'departmentid',

				];
				$query = '';
				$staff_query = '';

				if (isset($from_date) && isset($to_date)) {

					$staff_query = ' month >= \'' . $from_date . '\' and month <= \'' . $to_date . '\' and ';
				} else {
					$staff_query = '';
				}

				if (isset($staff_filter)) {
					$staffid_list = implode(',', $staff_filter);
					$query .= db_prefix() . 'staff.staffid in (' . $staffid_list . ') and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (' . $staffid_list . ') and ';
				}

				if (isset($department_filter)) {
					$department_list = implode(',', $department_filter);
					$query .= db_prefix() . 'departments.departmentid in  (' . $department_list . ') and ';

					$staff_query .= db_prefix() . 'hrp_payslip_details.staff_id in (SELECT staffid FROM ' . db_prefix() . 'staff_departments where departmentid in (' . $department_list . ')) and ';
				}

				$total_query = '';
				$staff_query_trim = '';
				if (($query) && ($query != '')) {
					$total_query = rtrim($query, ' and');
					$total_query = ' where ' . $total_query;
				}

				if (($staff_query) && ($staff_query != '')) {
					$staff_query_trim = rtrim($staff_query, ' and');

				}

				$where = [$total_query];

				$aColumns = $select;
				$sIndexColumn = 'departmentid';
				$sTable = db_prefix() . 'departments';
				$join = [];

				$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['name']);

				$output = $result['output'];
				$rResult = $result['rResult'];
				$rel_type = hrp_get_hr_profile_status();

				$staff_insurance = $this->hr_payroll_model->get_insurance_summary_report($staff_query_trim);

				$temp_insurance = 0;
				foreach ($rResult as $der_key => $aRow) {
					$row = [];

					$row[] = $aRow['name'];

					$staff_ids = $this->hr_payroll_model->get_staff_in_deparment($aRow['departmentid']);

					foreach ($staff_ids as $key => $value) {
						if (isset($staff_insurance[$value])) {
							$temp_insurance += $staff_insurance[$value];
						}
					}

					$row[] = $temp_insurance;
					$temp_insurance = 0;

					$output['aaData'][] = $row;
				}

				echo json_encode($output);
				die();

			}
		}
	}

	/**
	 * payslip chart
	 * @return [type]
	 */
	public function payslip_chart() {
		if ($this->request->getPost()) {

			$months_report = $this->request->getPost('months_filter');
			$staff_id = $this->request->getPost('staff_id');
			$filter_by_year = '';

			$filter_by_year .= 'date_format(month, "%Y") = ' . $months_report;

			echo json_encode($this->hr_payroll_model->payslip_chart($filter_by_year, $staff_id));
		}
	}

	/**
	 * department payslip chart
	 * @return [type]
	 */
	public function department_payslip_chart() {
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
				$from_date = to_sql_date($this->request->getPost('report_from'));
				$to_date = to_sql_date($this->request->getPost('report_to'));
			}

			$id_department = '';
			if (isset($department_filter)) {
				$id_department = implode(',', $department_filter);
			}
			$circle_mode = false;
			$list_diploma = array(
				"ps_total_insurance",
				"ps_income_tax_paye",
				"ps_total_deductions",
				"ps_net_pay",
			);
			$list_result = array();
			$list_data_department = [];

			$staff_payslip = $this->hr_payroll_model->get_department_payslip_chart($from_date, $to_date);
			$base_currency = get_base_currency();

			$current_name = '';

			echo json_encode([
				'department' => $staff_payslip['department_name'],
				'data_result' => $staff_payslip['list_result'],
				'circle_mode' => $circle_mode,
				'current_name' => $current_name,
			]);
			die;
		}
	}

	/**
	 * payslip template checked
	 * @return [type]
	 */
	public function payslip_template_checked() {
		$data = $this->request->getPost();
		$payslip_template_checked = $this->hr_payroll_model->payslip_template_checked($data);
		if ($payslip_template_checked === true) {
			$status = true;
		} else {
			$status = false;
		}

		echo json_encode([
			'status' => $status,
			'staff_name' => $payslip_template_checked,
		]);
	}

	/**
	 * payslip checked
	 * @return [type]
	 */
	public function payslip_checked() {
		$data = $this->request->getPost();
		$payslip_checked = $this->hr_payroll_model->payslip_checked($data['payslip_month'], $data['payslip_template_id']);

		if ($payslip_checked) {
			$status = true;
			$message = '';
		} else {
			$status = false;
			$message = app_lang('payslip_for_the_month_of');
		}
		$status = true;

		echo json_encode([
			'status' => $status,
			'message' => $message,
		]);
	}

	/**
	 * create payslip file
	 * @return [type]
	 */
	public function create_payslip_file() {

		$data = $this->request->getPost();
		$get_data = $this->hr_payroll_model->payslip_download($data);
		if ($get_data) {
			$user_id = $this->login_user->id;

			if (!class_exists('XLSXReader_fin')) {
				require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/assets/plugins/XLSXReader/XLSXReader.php');
			}
			require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/assets/plugins/XLSXWriter/xlsxwriter.class.php');

			$this->delete_error_file_day_before('1', HR_PAYROLL_CREATE_PAYSLIP_EXCEL);

			$payroll_system_columns_dont_format = payroll_system_columns_dont_format();

			//Writer file
			$writer_header = [];
			$widths = [];
			$col_style1 = [];

			$payroll_column_key = $get_data['payroll_column_key'];
			foreach ($get_data['payroll_header'] as $key => $value) {
				if (!in_array($payroll_column_key[$key], $payroll_system_columns_dont_format)) {

					$writer_header[$value] = '#,##0.00';
				} else {
					$writer_header[$value] = 'string';

				}
				$widths[] = 30;
				$col_style1[] = $key;
			}

			$writer = new \XLSXWriter();

			$style1 = ['widths' => $widths, 'fill' => '#ff9800', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13];

			$writer->writeSheetHeader_v2('Sheet1', $writer_header, $col_options = ['widths' => $widths, 'fill' => '#03a9f46b', 'font-style' => 'bold', 'color' => '#0a0a0a', 'border' => 'left,right,top,bottom', 'border-color' => '#0a0a0a', 'font-size' => 13],
				$col_style1, $style1);

			$data_object_kpi = [];
			$writer->writeSheetRow('Sheet1', $get_data['payroll_header']);

			foreach ($get_data['payslip_detail'] as $data_key => $payslip_detail) {

				$writer->writeSheetRow('Sheet1', array_values($payslip_detail));

			}

			$filename = 'Payslip_' . date('Y-m', strtotime($get_data['month'])) . '_' . strtotime(date('Y-m-d H:i:s')) . '.xlsx';
			$writer->writeToFile(str_replace($filename, HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE . $filename, $filename));

			echo json_encode([
				'success' => true,
				'message' => app_lang('create_a_payslip_for_successful_download'),
				'site_url' => base_url(),
				'staff_id' => $user_id,
				'filename' => HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE . $filename,
			]);
			die;
		}

		echo json_encode([
			'success' => false,
			'message' => app_lang('an_error_occurred_while_creating_a_payslip_to_download'),
			'site_url' => base_url(),
			'staff_id' => $user_id,
			'filename' => HR_PAYROLL_CREATE_EMPLOYEES_SAMPLE,
		]);
		die;

	}

	/**
	 *employees copy
	 * @return [type]
	 */
	public function employees_copy() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_employee') && !hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			$results = $this->hr_payroll_model->employees_copy($data);

			if ($results) {
				$message = app_lang('updated_successfully');
			} else {
				$message = app_lang('hrp_updated_failed');
			}

			echo json_encode([
				'message' => $results['message'],
				'status' => $results['status'],
			]);
		}

	}

	/**
	 * reset datas
	 * @return [type] 
	 */
	public function reset_datas() {
		return $this->template->rander("Hr_payroll\Views\includes\\reset_data", []);
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

		//delete hrp_employees_value
		$hrp_employees_value = $builder->table(get_db_prefix().'hrp_employees_value');
		$hrp_employees_value->truncate();

		//delete hrp_employees_timesheets
		$hrp_employees_timesheets = $builder->table(get_db_prefix().'hrp_employees_timesheets');
		$hrp_employees_timesheets->truncate();

		//delete hrp_commissions
		$hrp_commissions = $builder->table(get_db_prefix().'hrp_commissions');
		$hrp_commissions->truncate();

		//delete hrp_salary_deductions
		$hrp_salary_deductions = $builder->table(get_db_prefix().'hrp_salary_deductions');
		$hrp_salary_deductions->truncate();

		//delete hrp_bonus_kpi
		$hrp_bonus_kpi = $builder->table(get_db_prefix().'hrp_bonus_kpi');
		$hrp_bonus_kpi->truncate();

		//delete hrp_staff_insurances
		$hrp_staff_insurances = $builder->table(get_db_prefix().'hrp_staff_insurances');
		$hrp_staff_insurances->truncate();

		//delete hrp_payslips
		$hrp_payslips = $builder->table(get_db_prefix().'hrp_payslips');
		$hrp_payslips->truncate();

		//delete hrp_payslip_details
		$hrp_payslip_details = $builder->table(get_db_prefix().'hrp_payslip_details');
		$hrp_payslip_details->truncate();

		//delete attendance_sample_file
		
		foreach (glob('plugins/Hr_payroll/uploads/attendance_sample_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				
				if ($filename != 'index.html') {
					unlink('plugins/Hr_payroll/uploads/attendance_sample_file/' . $filename);
				}
			}

		}

		foreach (glob('plugins/Hr_payroll/uploads/commissions_sample_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				
				if ($filename != 'index.html') {
					unlink('plugins/Hr_payroll/uploads/commissions_sample_file/' . $filename);
				}
			}

		}

		foreach (glob('plugins/Hr_payroll/uploads/employees_sample_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				
				if ($filename != 'index.html') {
					unlink('plugins/Hr_payroll/uploads/employees_sample_file/' . $filename);
				}
			}

		}

		foreach (glob('plugins/Hr_payroll/uploads/file_error_response/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				
				if ($filename != 'index.html') {
					unlink('plugins/Hr_payroll/uploads/file_error_response/' . $filename);
				}
			}

		}

		foreach (glob('plugins/Hr_payroll/uploads/payslip/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				
				if ($filename != 'index.html') {
					unlink('plugins/Hr_payroll/uploads/payslip/' . $filename);
				}
			}

		}

		foreach (glob('plugins/Hr_payroll/uploads/payslip_excel_file/' . '*') as $file) {
			$file_arr = explode("/", $file);
			$filename = array_pop($file_arr);

			if (file_exists($file)) {
				
				if ($filename != 'index.html') {
					unlink('plugins/Hr_payroll/uploads/payslip_excel_file/' . $filename);
				}
			}

		}

		$this->session->setFlashdata("success_message", app_lang("reset_data_successful"));
		app_redirect(('hr_payroll/reset_datas'));

	}

	/**
	 * employee export pdf
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function employee_export_pdf($id) {
		if (!$id) {
			show_404();
		}

		$builder = db_connect('default');
		$builder = $builder->table('hrp_payslip_details');
		$builder->where('parent_id', $id);

		$hrp_payslip_details = $builder->get()->getResultArray();

		$data = [];
		$data['payslip_detail'] = $hrp_payslip_details[0];

		$arr_department = $this->hr_payroll_model->get_staff_departments($data['payslip_detail']['staff_id'], true);
		$list_department = '';
		if (count($arr_department) > 0) {

			foreach ($arr_department as $key => $department) {
				$this->load->model('departments_model');

				$department_value = $this->departments_model->get($department);

				if ($department_value) {
					if (strlen($list_department) != 0) {
						$list_department .= ', ' . $department_value->name;
					} else {
						$list_department .= $department_value->name;
					}
				}
			}
		}

		$employee = $this->hr_payroll_model->get_employees_data($data['payslip_detail']['month'], '', ' staff_id = ' . $data['payslip_detail']['staff_id']);
		$data['employee'] = count($employee) > 0 ? $employee[0] : [];
		$data['list_department'] = $list_department;


		$html = $this->template->rander('Hr_payroll\Views/employee_payslip/export_employee_payslip', $data, true);
		$html .= '<link href="' . module_dir_url(HR_PAYROLL_MODULE_NAME, 'assets/css/export_employee_pdf.css') . '"  rel="stylesheet" type="text/css" />';


		try {
			$pdf = $this->hr_payroll_model->employee_export_pdf($html);

		} catch (Exception $e) {
			echo html_entity_decode($e->getMessage());
			die;
		}

		$type = 'D';

		if ($this->input->get('output_type')) {
			$type = $this->input->get('output_type');
		}

		if ($this->input->get('print')) {
			$type = 'I';
		}

		$pdf->Output($data['payslip_detail']['employee_number'].'_'.date('m-Y', strtotime($data['payslip_detail']['month'])).'_'.strtotime(date('Y-m-d H:i:s')).'.pdf', $type);
	}

	/**
	 * payslip manage export pdf
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function payslip_manage_export_pdf($id)
	{
		if (!$id) {
			show_404();
		}

		require_once(module_dir_path(HR_PAYROLL_MODULE_NAME).'/Libraries/Ziphr.php');

		$data = $this->request->getPost();

		//delete sub folder STOCK_EXPORT
		foreach(glob(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP . '*') as $file) { 
			$file_arr = explode("/",$file);
			$filename = array_pop($file_arr);

			if(file_exists($file)) {
				if ($filename != 'index.html') {
					unlink(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP.$filename);
				}
			}
		}

		$payslip = $this->hr_payroll_model->get_hrp_payslip($id);
		$payslip_details = $this->hr_payroll_model->get_payslip_detail_by_payslip_id($id);
		$has_pdf_template = false;

		foreach ($payslip_details as $payslip_detail) {
			$check_payslip_has_pdf_template = check_payslip_has_pdf_template($payslip_detail['payslip_id']);
			if(is_numeric($check_payslip_has_pdf_template) && is_numeric($check_payslip_has_pdf_template) &&  $check_payslip_has_pdf_template != 0 ){
				$has_pdf_template = true;
			}


			$data = [];
			$data['payslip_detail'] = $payslip_detail;

			$list_department = $this->hr_payroll_model->getdepartment_name($payslip_detail['staff_id']);

			$employee = $this->hr_payroll_model->get_employees_data($payslip_detail['month'], '', ' staff_id = ' . $payslip_detail['staff_id']);
			$data['employee'] = count($employee) > 0 ? $employee[0] : [];
			$data['list_department'] = $list_department->name;

			if($has_pdf_template){
				$hr_payroll_get_payslip_pdf_only_for_pdf = $this->hr_payroll_model->hr_payroll_get_payslip_pdf_only_for_pdf($payslip_detail['id']);
				$html = $this->template->view('Hr_payroll\Views/employee_payslip/export_employee_payslip_v2', ['html' => $hr_payroll_get_payslip_pdf_only_for_pdf->content]);
				$html .= '<link href="' . module_dir_path(HR_PAYROLL_MODULE_NAME, 'assets/css/export_employee_pdf_v2.css') . '"  rel="stylesheet" type="text/css" />';
			}else{

				$html = $this->template->view('Hr_payroll\Views/employee_payslip/export_employee_payslip', $data);
				$html .= '<link href="' . module_dir_path(HR_PAYROLL_MODULE_NAME, 'assets/css/export_employee_pdf.css') . '"  rel="stylesheet" type="text/css" />';
			}
			$pdf = new Pdf();
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$pdf->SetCellPadding(1.5);
			$pdf->setImageScale(1.42);
			$pdf->AddPage();
			$pdf->SetFontSize(9);


			$pdf->writeHTML($html, true, false, true, false, '');
			$pdf_file_name = $payslip_detail['employee_number'].'_'.date('m-Y', strtotime($payslip_detail['month'])) . '.pdf';

			$dir = getcwd() . "/" . HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP . $pdf_file_name;

				// save to dir
			$pdf->Output($dir, "F");
		}

		$zip = new \Ziphr();

		//get list file
		foreach(glob(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP . '*') as $file) { 
			$file_arr = explode("/",$file);
			$filename = array_pop($file_arr);

			$zip->read_file(HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP. $filename);
		}

		$zip->download($payslip->payslip_name .'_'. date('m-Y', strtotime($payslip->payslip_month)). '.zip');
		$zip->clear_data();
	}

	/**
	 * re save to dir
	 * @param  [type] $pdf       
	 * @param  [type] $file_name 
	 * @return [type]            
	 */
	private function re_save_to_dir($pdf, $file_name)
	{
		$dir = HR_PAYROLL_EXPORT_EMPLOYEE_PAYSLIP;

		$dir .= $file_name;

		$pdf->Output($dir, 'F');
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
			return $this->template->view('Hr_payroll\Views\includes\confirm_delete_modal_form', $data);
		}
	}

	/**
	 * table payslip report
	 * @return [type] 
	 */
	public function table_payslip_report() {
		$dataPost = $this->request->getPost();
		$this->hr_payroll_model->get_table_data(module_views_path('Hr_payroll', 'reports/tables/payslip_report_table'), $dataPost);
	}

	/**
	 * table income summary report
	 * @return [type] 
	 */
	public function table_income_summary_report() {
		$dataPost = $this->request->getPost();
		$this->hr_payroll_model->get_table_data(module_views_path('Hr_payroll', 'reports/tables/income_summary_report_table'), $dataPost);
	}

	/**
	 * table insurance cost summary report
	 * @return [type] 
	 */
	public function table_insurance_cost_summary_report() {
		$dataPost = $this->request->getPost();
		$this->hr_payroll_model->get_table_data(module_views_path('Hr_payroll', 'reports/tables/insurance_cost_summary_report_table'), $dataPost);
	}

	/**
	 * staff pay slips
	 * @param  [type] $user_id 
	 * @return [type]          
	 */
	public function staff_pay_slips($user_id)
	{
		$data['title'] = app_lang('hr_pay_slips');
		$data['user_id'] = $user_id;
		return $this->template->view('Hr_payroll\Views\employee_payslip/staff_payslip_tab_content', $data);
	}

	/**
	 * staff payslip modal form
	 * @return [type] 
	 */
	public function staff_payslip_modal_form() {
		$this->access_only_team_members();

		$payslip_detail_id = $this->request->getPost('id');

		$data['payslip_detail'] = $this->hr_payroll_model->get_payslip_detail($payslip_detail_id);

		$list_department = $this->hr_payroll_model->getdepartment_name($data['payslip_detail']->staff_id);

		$employee = $this->hr_payroll_model->get_employees_data($data['payslip_detail']->month, '', ' staff_id = ' . $data['payslip_detail']->staff_id);

		$data['employee'] = count($employee) > 0 ? $employee[0] : [];
		$data['list_department'] = $list_department->name;

		return $this->template->view('Hr_payroll\Views\employee_payslip\staff_payslip_modal_view', $data);
	}

	/*ROLE for module Start*/

	//load the role view
	function roles() {
		return $this->template->rander("Hr_payroll\Views/roles/index");
	}

    //load the role add/edit modal
	function role_modal_form() {

		$this->validate_submitted_data(array(
			"id" => "numeric"
		));

		$view_data['model_info'] = $this->Roles_model->get_one($this->request->getPost('id'));
		$view_data['roles_dropdown'] = array("" => "-") + $this->Roles_model->get_dropdown_list(array("title"), "id");
		return $this->template->view('Hr_payroll\Views\roles/modal_form', $view_data);
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

			$view_data["hr_payroll_can_view_own_hrp_employee"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_employee");
			$view_data["hr_payroll_can_view_global_hrp_employee"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_employee");
			$view_data["hr_payroll_can_create_hrp_employee"] = get_array_value($permissions, "hr_payroll_can_create_hrp_employee");
			$view_data["hr_payroll_can_edit_hrp_employee"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_employee");
			$view_data["hr_payroll_can_delete_hrp_employee"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_employee");
			$view_data["hr_payroll_can_view_own_hrp_attendance"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_attendance");
			$view_data["hr_payroll_can_view_global_hrp_attendance"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_attendance");
			$view_data["hr_payroll_can_create_hrp_attendance"] = get_array_value($permissions, "hr_payroll_can_create_hrp_attendance");
			$view_data["hr_payroll_can_edit_hrp_attendance"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_attendance");
			$view_data["hr_payroll_can_delete_hrp_attendance"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_attendance");
			$view_data["hr_payroll_can_view_own_hrp_commission"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_commission");
			$view_data["hr_payroll_can_view_global_hrp_commission"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_commission");
			$view_data["hr_payroll_can_create_hrp_commission"] = get_array_value($permissions, "hr_payroll_can_create_hrp_commission");
			$view_data["hr_payroll_can_edit_hrp_commission"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_commission");
			$view_data["hr_payroll_can_delete_hrp_commission"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_commission");
			$view_data["hr_payroll_can_view_own_hrp_deduction"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_deduction");
			$view_data["hr_payroll_can_view_global_hrp_deduction"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_deduction");
			$view_data["hr_payroll_can_create_hrp_deduction"] = get_array_value($permissions, "hr_payroll_can_create_hrp_deduction");
			$view_data["hr_payroll_can_edit_hrp_deduction"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_deduction");
			$view_data["hr_payroll_can_delete_hrp_deduction"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_deduction");
			$view_data["hr_payroll_can_view_own_hrp_bonus_kpi"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_bonus_kpi");
			$view_data["hr_payroll_can_view_global_hrp_bonus_kpi"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_bonus_kpi");
			$view_data["hr_payroll_can_create_hrp_bonus_kpi"] = get_array_value($permissions, "hr_payroll_can_create_hrp_bonus_kpi");
			$view_data["hr_payroll_can_edit_hrp_bonus_kpi"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_bonus_kpi");
			$view_data["hr_payroll_can_delete_hrp_bonus_kpi"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_bonus_kpi");
			$view_data["hr_payroll_can_view_own_hrp_insurrance"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_insurrance");
			$view_data["hr_payroll_can_view_global_hrp_insurrance"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_insurrance");
			$view_data["hr_payroll_can_create_hrp_insurrance"] = get_array_value($permissions, "hr_payroll_can_create_hrp_insurrance");
			$view_data["hr_payroll_can_edit_hrp_insurrance"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_insurrance");
			$view_data["hr_payroll_can_delete_hrp_insurrance"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_insurrance");
			$view_data["hr_payroll_can_view_own_hrp_payslip"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_payslip");
			$view_data["hr_payroll_can_view_global_hrp_payslip"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_payslip");
			$view_data["hr_payroll_can_create_hrp_payslip"] = get_array_value($permissions, "hr_payroll_can_create_hrp_payslip");
			$view_data["hr_payroll_can_edit_hrp_payslip"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_payslip");
			$view_data["hr_payroll_can_delete_hrp_payslip"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_payslip");
			$view_data["hr_payroll_can_view_own_hrp_payslip_template"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_payslip_template");
			$view_data["hr_payroll_can_view_global_hrp_payslip_template"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_payslip_template");
			$view_data["hr_payroll_can_create_hrp_payslip_template"] = get_array_value($permissions, "hr_payroll_can_create_hrp_payslip_template");
			$view_data["hr_payroll_can_edit_hrp_payslip_template"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_payslip_template");
			$view_data["hr_payroll_can_delete_hrp_payslip_template"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_payslip_template");
			$view_data["hr_payroll_can_view_own_hrp_income_tax"] = get_array_value($permissions, "hr_payroll_can_view_own_hrp_income_tax");
			$view_data["hr_payroll_can_view_global_hrp_income_tax"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_income_tax");
			$view_data["hr_payroll_can_view_global_hrp_report"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_report");
			$view_data["hr_payroll_can_view_global_hrp_setting"] = get_array_value($permissions, "hr_payroll_can_view_global_hrp_setting");
			$view_data["hr_payroll_can_create_hrp_setting"] = get_array_value($permissions, "hr_payroll_can_create_hrp_setting");
			$view_data["hr_payroll_can_edit_hrp_setting"] = get_array_value($permissions, "hr_payroll_can_edit_hrp_setting");
			$view_data["hr_payroll_can_delete_hrp_setting"] = get_array_value($permissions, "hr_payroll_can_delete_hrp_setting");

			$view_data['permissions'] = $permissions;

			return $this->template->view("Hr_payroll\Views/roles/permissions", $view_data);
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

		$permissions["hr_payroll_can_view_own_hrp_employee"] = isset($data["hr_payroll_can_view_own_hrp_employee"]) ? $data["hr_payroll_can_view_own_hrp_employee"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_employee"] = isset($data["hr_payroll_can_view_global_hrp_employee"]) ? $data["hr_payroll_can_view_global_hrp_employee"] : NULL;
		$permissions["hr_payroll_can_create_hrp_employee"] = isset($data["hr_payroll_can_create_hrp_employee"]) ? $data["hr_payroll_can_create_hrp_employee"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_employee"] = isset($data["hr_payroll_can_edit_hrp_employee"]) ? $data["hr_payroll_can_edit_hrp_employee"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_employee"] = isset($data["hr_payroll_can_delete_hrp_employee"]) ? $data["hr_payroll_can_delete_hrp_employee"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_attendance"] = isset($data["hr_payroll_can_view_own_hrp_attendance"]) ? $data["hr_payroll_can_view_own_hrp_attendance"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_attendance"] = isset($data["hr_payroll_can_view_global_hrp_attendance"]) ? $data["hr_payroll_can_view_global_hrp_attendance"] : NULL;
		$permissions["hr_payroll_can_create_hrp_attendance"] = isset($data["hr_payroll_can_create_hrp_attendance"]) ? $data["hr_payroll_can_create_hrp_attendance"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_attendance"] = isset($data["hr_payroll_can_edit_hrp_attendance"]) ? $data["hr_payroll_can_edit_hrp_attendance"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_attendance"] = isset($data["hr_payroll_can_delete_hrp_attendance"]) ? $data["hr_payroll_can_delete_hrp_attendance"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_commission"] = isset($data["hr_payroll_can_view_own_hrp_commission"]) ? $data["hr_payroll_can_view_own_hrp_commission"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_commission"] = isset($data["hr_payroll_can_view_global_hrp_commission"]) ? $data["hr_payroll_can_view_global_hrp_commission"] : NULL;
		$permissions["hr_payroll_can_create_hrp_commission"] = isset($data["hr_payroll_can_create_hrp_commission"]) ? $data["hr_payroll_can_create_hrp_commission"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_commission"] = isset($data["hr_payroll_can_edit_hrp_commission"]) ? $data["hr_payroll_can_edit_hrp_commission"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_commission"] = isset($data["hr_payroll_can_delete_hrp_commission"]) ? $data["hr_payroll_can_delete_hrp_commission"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_deduction"] = isset($data["hr_payroll_can_view_own_hrp_deduction"]) ? $data["hr_payroll_can_view_own_hrp_deduction"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_deduction"] = isset($data["hr_payroll_can_view_global_hrp_deduction"]) ? $data["hr_payroll_can_view_global_hrp_deduction"] : NULL;
		$permissions["hr_payroll_can_create_hrp_deduction"] = isset($data["hr_payroll_can_create_hrp_deduction"]) ? $data["hr_payroll_can_create_hrp_deduction"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_deduction"] = isset($data["hr_payroll_can_edit_hrp_deduction"]) ? $data["hr_payroll_can_edit_hrp_deduction"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_deduction"] = isset($data["hr_payroll_can_delete_hrp_deduction"]) ? $data["hr_payroll_can_delete_hrp_deduction"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_bonus_kpi"] = isset($data["hr_payroll_can_view_own_hrp_bonus_kpi"]) ? $data["hr_payroll_can_view_own_hrp_bonus_kpi"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_bonus_kpi"] = isset($data["hr_payroll_can_view_global_hrp_bonus_kpi"]) ? $data["hr_payroll_can_view_global_hrp_bonus_kpi"] : NULL;
		$permissions["hr_payroll_can_create_hrp_bonus_kpi"] = isset($data["hr_payroll_can_create_hrp_bonus_kpi"]) ? $data["hr_payroll_can_create_hrp_bonus_kpi"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_bonus_kpi"] = isset($data["hr_payroll_can_edit_hrp_bonus_kpi"]) ? $data["hr_payroll_can_edit_hrp_bonus_kpi"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_bonus_kpi"] = isset($data["hr_payroll_can_delete_hrp_bonus_kpi"]) ? $data["hr_payroll_can_delete_hrp_bonus_kpi"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_insurrance"] = isset($data["hr_payroll_can_view_own_hrp_insurrance"]) ? $data["hr_payroll_can_view_own_hrp_insurrance"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_insurrance"] = isset($data["hr_payroll_can_view_global_hrp_insurrance"]) ? $data["hr_payroll_can_view_global_hrp_insurrance"] : NULL;
		$permissions["hr_payroll_can_create_hrp_insurrance"] = isset($data["hr_payroll_can_create_hrp_insurrance"]) ? $data["hr_payroll_can_create_hrp_insurrance"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_insurrance"] = isset($data["hr_payroll_can_edit_hrp_insurrance"]) ? $data["hr_payroll_can_edit_hrp_insurrance"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_insurrance"] = isset($data["hr_payroll_can_delete_hrp_insurrance"]) ? $data["hr_payroll_can_delete_hrp_insurrance"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_payslip"] = isset($data["hr_payroll_can_view_own_hrp_payslip"]) ? $data["hr_payroll_can_view_own_hrp_payslip"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_payslip"] = isset($data["hr_payroll_can_view_global_hrp_payslip"]) ? $data["hr_payroll_can_view_global_hrp_payslip"] : NULL;
		$permissions["hr_payroll_can_create_hrp_payslip"] = isset($data["hr_payroll_can_create_hrp_payslip"]) ? $data["hr_payroll_can_create_hrp_payslip"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_payslip"] = isset($data["hr_payroll_can_edit_hrp_payslip"]) ? $data["hr_payroll_can_edit_hrp_payslip"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_payslip"] = isset($data["hr_payroll_can_delete_hrp_payslip"]) ? $data["hr_payroll_can_delete_hrp_payslip"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_payslip_template"] = isset($data["hr_payroll_can_view_own_hrp_payslip_template"]) ? $data["hr_payroll_can_view_own_hrp_payslip_template"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_payslip_template"] = isset($data["hr_payroll_can_view_global_hrp_payslip_template"]) ? $data["hr_payroll_can_view_global_hrp_payslip_template"] : NULL;
		$permissions["hr_payroll_can_create_hrp_payslip_template"] = isset($data["hr_payroll_can_create_hrp_payslip_template"]) ? $data["hr_payroll_can_create_hrp_payslip_template"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_payslip_template"] = isset($data["hr_payroll_can_edit_hrp_payslip_template"]) ? $data["hr_payroll_can_edit_hrp_payslip_template"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_payslip_template"] = isset($data["hr_payroll_can_delete_hrp_payslip_template"]) ? $data["hr_payroll_can_delete_hrp_payslip_template"] : NULL;
		$permissions["hr_payroll_can_view_own_hrp_income_tax"] = isset($data["hr_payroll_can_view_own_hrp_income_tax"]) ? $data["hr_payroll_can_view_own_hrp_income_tax"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_income_tax"] = isset($data["hr_payroll_can_view_global_hrp_income_tax"]) ? $data["hr_payroll_can_view_global_hrp_income_tax"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_report"] = isset($data["hr_payroll_can_view_global_hrp_report"]) ? $data["hr_payroll_can_view_global_hrp_report"] : NULL;
		$permissions["hr_payroll_can_view_global_hrp_setting"] = isset($data["hr_payroll_can_view_global_hrp_setting"]) ? $data["hr_payroll_can_view_global_hrp_setting"] : NULL;
		$permissions["hr_payroll_can_create_hrp_setting"] = isset($data["hr_payroll_can_create_hrp_setting"]) ? $data["hr_payroll_can_create_hrp_setting"] : NULL;
		$permissions["hr_payroll_can_edit_hrp_setting"] = isset($data["hr_payroll_can_edit_hrp_setting"]) ? $data["hr_payroll_can_edit_hrp_setting"] : NULL;
		$permissions["hr_payroll_can_delete_hrp_setting"] = isset($data["hr_payroll_can_delete_hrp_setting"]) ? $data["hr_payroll_can_delete_hrp_setting"] : NULL;
        
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
	 * manage attendance timesheet leaves
	 * @return [type] 
	 */
	public function manage_attendance_timesheet_leaves($month = '') {
		if (!hrp_has_permission('hr_payroll_can_view_global_hrp_attendance') && !hrp_has_permission('hr_payroll_can_view_own_hrp_attendance') && !is_admin()) {
			access_denied('hrp_attendance');
		}


		$rel_type = hrp_get_timesheets_status();

		//get current month
		if(strlen($month) > 0){
			$current_month = date('Y-m-d', strtotime($month . '-01'));
			$data['current_month'] = date('Y-m-d', strtotime($month . '-01'));
		}else{
			$current_month = date('Y-m-d', strtotime(date('Y-m') . '-01'));
			$data['current_month'] = date('Y-m-d', strtotime(date('Y-m') . '-01'));
		}

		//get day header in month
		$hrp_timesheet_leave_data_sample = hrp_timesheet_leave_data_sample();
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($current_month, $rel_type, false);

		$attendances = $this->hr_payroll_model->get_hrp_attendance_timesheet_leave($current_month);
		$attendances_value = [];
		$cell_background = [];
		$cell_background_data = [];

		foreach ($attendances as $key => $value) {
			$dt_cell_bg = [];
			foreach ($value as $hearder_key => $cell_value) {

				if($hearder_key != 'id' && $hearder_key != 'staff_id' && $hearder_key != 'month' && $hearder_key != 'paid_leave' && $hearder_key != 'unpaid_leave' && $hearder_key != 'rel_type'){
					if(strlen($cell_value) > 0){
						if(preg_match('/^PL:/', $cell_value) && strlen($cell_value) < 7){
							$dt_cell_bg[$hearder_key] = '#0c0';

						}elseif(preg_match('/^UPL:/', $cell_value) && strlen($cell_value) < 7){
							$dt_cell_bg[$hearder_key] = '#c00';

						}elseif(preg_match('/PL:/', $cell_value) && preg_match('/UPL:/', $cell_value)){
							$dt_cell_bg[$hearder_key] = '#FF9800';
						}else{
							$dt_cell_bg[$hearder_key] = '#fff';
						}
					}else{
						$dt_cell_bg[$hearder_key] = '#fff';
					}
				}elseif($hearder_key == 'paid_leave' && (float)$cell_value > 0){
					$dt_cell_bg[$hearder_key] = '#0c0';
				}elseif($hearder_key == 'unpaid_leave' && (float)$cell_value > 0){
					$dt_cell_bg[$hearder_key] = '#c00';
				}else{
					$dt_cell_bg[$hearder_key] = '#fff';
				}
			}
			$cell_background[$value['staff_id'] . '_' . $value['month']] = $dt_cell_bg;
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		//load deparment by manager
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
			//View own
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission());
		} else {
			//admin or view global
			$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object();
		}

		$data_object_kpi = [];

		foreach ($staffs as $staff_key => $staff_value) {
			/*check value from database*/

			$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
			if ($staff_i) {

				if (isset($staff_i->staff_identifi)) {
					$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
				} else {
					$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
				}

				$data_object_kpi[$staff_key]['staff_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

				$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

				$data_object_kpi[$staff_key]['staff_departments'] = $list_department->name;

			} else {
				$data_object_kpi[$staff_key]['hr_code'] = '';
				$data_object_kpi[$staff_key]['staff_name'] = '';
				$data_object_kpi[$staff_key]['staff_departments'] = '';

			}

			if (isset($attendances_value[$staff_value['staffid'] . '_' . $current_month])) {

				$data_object_kpi[$staff_key]['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['paid_leave'];
				$data_object_kpi[$staff_key]['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['unpaid_leave'];
				$data_object_kpi[$staff_key]['id'] = $attendances_value[$staff_value['staffid'] . '_' . $current_month]['id'];

				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $attendances_value[$staff_value['staffid'] . '_' . $current_month]);

				$cell_background_data[] = $cell_background[$staff_value['staffid'] . '_' . $current_month];
			} else {
			
				$data_object_kpi[$staff_key]['paid_leave'] = 0;
				$data_object_kpi[$staff_key]['unpaid_leave'] = 0;
				$data_object_kpi[$staff_key]['id'] = 0;
				$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $days_header_in_month['days_header']);
				$cell_background_data[] = $hrp_timesheet_leave_data_sample;

			}
			$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
			$data_object_kpi[$staff_key]['month'] = $current_month;
			$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

		}

		//check is add new or update data
		if (count($attendances_value) > 0) {
			$data['button_name'] = app_lang('hrp_update');
		} else {
			$data['button_name'] = app_lang('submit');
		}

		$department_options = array(
			"deleted" => 0,
		);
		$data['departments'] = $this->Team_model->get_details($department_options)->getResultArray();

		/*Get roles data*/
		$role_dropdown[] = [
			'id' => 0,
			'title' => app_lang('team_member'),
		];

		$role_dropdown[] = [
			'id' => 'admin',
			'title' => app_lang('admin'),
		];

		$role_options = array(
			"deleted" => 0,
		);
		$roles = $this->Roles_model->get_details($role_options)->getResultArray();
		$data['roles'] = array_merge($role_dropdown, $roles);

		$data['staffs'] = $staffs;
		$data['data_object_kpi'] = $data_object_kpi;

		$data['body_value'] = json_encode($data_object_kpi);
		$data['columns'] = json_encode($days_header_in_month['columns_type']);
		$data['col_header'] = json_encode($days_header_in_month['headers']);
		$data['cell_background'] = json_encode($cell_background_data);

		return $this->template->rander('Hr_payroll\Views\timesheet_leaves/timesheet_leave_manage', $data);
	}

	/**
	 * add timesheets leave
	 */
	public function add_timesheets_leave() {
		if (!hrp_has_permission('hr_payroll_can_create_hrp_attendance') && !hrp_has_permission('hr_payroll_can_edit_hrp_attendance') && !is_admin()) {
			access_denied('hrp_attendance');
		}

		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if (isset($data)) {

				if ($data['hrp_attendance_rel_type'] == 'update') {
					$success = $this->hr_payroll_model->add_update_attendance_timesheets_leave($data);
				} elseif ($data['hrp_attendance_rel_type'] == 'synchronization') {
					$success = $this->hr_payroll_model->synchronization_attendance($data);
				} else {
					$success = false;
				}

				if ($success) {
					$this->session->setFlashdata("success_message", app_lang('hrp_updated_successfully'));
				}
				if(isset($data['attendance_fill_month'])){
					app_redirect(('hr_payroll/manage_attendance_timesheet_leaves/'.$data['attendance_fill_month']));
				}else{
					app_redirect(('hr_payroll/manage_attendance_timesheet_leaves'));
					
				}
			}

		}
	}

	/**
	 * timesheets leave filter
	 * @return [type] 
	 */
	public function timesheets_leave_filter() {
		$data = $this->request->getPost();

		$rel_type = hrp_get_timesheets_status();

		$months_filter = $data['month'];
		$department = $data['department'];
		$staff = '';
		if (isset($data['staff'])) {
			$staff = $data['staff'];
		}
		$role_attendance = '';
		if (isset($data['role_attendance'])) {
			$role_attendance = $data['role_attendance'];
		}

		$newquerystring = $this->render_filter_query($months_filter, $staff, $department, $role_attendance);

		$hrp_timesheet_leave_data_sample = hrp_timesheet_leave_data_sample();
		$month_filter = date('Y-m-d', strtotime($data['month'] . '-01'));
		//get day header in month
		$days_header_in_month = $this->hr_payroll_model->get_day_header_in_month($month_filter, $rel_type, false);

		$attendances = $this->hr_payroll_model->get_hrp_attendance_timesheet_leave($month_filter);
		$attendances_value = [];
		$cell_background = [];
		$cell_background_data = [];

		foreach ($attendances as $key => $value) {
			$dt_cell_bg = [];
			foreach ($value as $hearder_key => $cell_value) {

				if($hearder_key != 'id' && $hearder_key != 'staff_id' && $hearder_key != 'month' && $hearder_key != 'paid_leave' && $hearder_key != 'unpaid_leave' && $hearder_key != 'rel_type'){
					if(strlen($cell_value) > 0){
						if(preg_match('/^PL:/', $cell_value) && strlen($cell_value) < 7){
							$dt_cell_bg[$hearder_key] = '#0c0';

						}elseif(preg_match('/^UPL:/', $cell_value) && strlen($cell_value) < 7){
							$dt_cell_bg[$hearder_key] = '#c00';

						}elseif(preg_match('/PL:/', $cell_value) && preg_match('/UPL:/', $cell_value)){
							$dt_cell_bg[$hearder_key] = '#FF9800';
						}else{
							$dt_cell_bg[$hearder_key] = '#fff';
						}
					}else{
						$dt_cell_bg[$hearder_key] = '#fff';
					}
				}else{
					$dt_cell_bg[$hearder_key] = '#fff';
				}

			}
			$cell_background[$value['staff_id'] . '_' . $value['month']] = $dt_cell_bg;
			$attendances_value[$value['staff_id'] . '_' . $value['month']] = $value;
		}

		// data return
		$data_object_kpi = [];
		$index_data_object = 0;
		if ($newquerystring != '') {

			//load staff
			if (!is_admin() && !hrp_has_permission('hr_payroll_can_view_global_hrp_employee')) {
				//View own
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object(get_staffid_by_permission($newquerystring));
			} else {
				//admin or view global
				$staffs = $this->hr_payroll_model->get_staff_timekeeping_applicable_object($newquerystring);
			}

			foreach ($staffs as $staff_key => $staff_value) {

				/*check value from database*/
				$data_object_kpi[$staff_key]['staff_id'] = $staff_value['staffid'];

				$staff_i = $this->hr_payroll_model->get_staff_info($staff_value['staffid']);
				if ($staff_i) {

					if (isset($staff_i->staff_identifi)) {
						$data_object_kpi[$staff_key]['hr_code'] = $staff_i->staff_identifi;
					} else {
						$data_object_kpi[$staff_key]['hr_code'] = $this->hr_payroll_model->hrp_format_code('EXS', $staff_i->id, 5);
					}

					$data_object_kpi[$staff_key]['staff_name'] = $staff_i->first_name . ' ' . $staff_i->last_name;

					$list_department = $this->hr_payroll_model->getdepartment_name($staff_i->id);

				$data_object_kpi[$staff_key]['staff_departments'] = $list_department->name;

				} else {
					$data_object_kpi[$staff_key]['hr_code'] = '';
					$data_object_kpi[$staff_key]['staff_name'] = '';
					$data_object_kpi[$staff_key]['staff_departments'] = '';

				}

				if (isset($attendances_value[$staff_value['staffid'] . '_' . $month_filter])) {

					
					$data_object_kpi[$staff_key]['paid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['paid_leave'];
					$data_object_kpi[$staff_key]['unpaid_leave'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['unpaid_leave'];
					$data_object_kpi[$staff_key]['id'] = $attendances_value[$staff_value['staffid'] . '_' . $month_filter]['id'];
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $attendances_value[$staff_value['staffid'] . '_' . $month_filter]);
					$cell_background_data[] = $cell_background[$staff_value['staffid'] . '_' . $month_filter];


				} else {
					
					$data_object_kpi[$staff_key]['paid_leave'] = 0;
					$data_object_kpi[$staff_key]['unpaid_leave'] = 0;
					$data_object_kpi[$staff_key]['id'] = 0;
					$data_object_kpi[$staff_key] = array_merge($data_object_kpi[$staff_key], $days_header_in_month['days_header']);
					$cell_background_data[] = $hrp_timesheet_leave_data_sample;

				}

				$data_object_kpi[$staff_key]['rel_type'] = $rel_type;
				$data_object_kpi[$staff_key]['month'] = $month_filter;

			}

		}

		//check is add new or update data
		if (count($attendances_value) > 0) {
			$button_name = app_lang('hrp_update');
		} else {
			$button_name = app_lang('submit');
		}

		echo json_encode([
			'data_object' => $data_object_kpi,
			'columns' => $days_header_in_month['columns_type'],
			'col_header' => $days_header_in_month['headers'],
			'button_name' => $button_name,
			'cell_background' => $cell_background_data

		]);
		die;
	}

	/**
	 * timesheet leave calculation
	 * @return [type] 
	 */
	public function timesheet_leave_calculation()
	{
		if (!hrp_has_permission('hr_payroll_can_edit_hrp_employee') && !is_admin()) {
			access_denied('hrp_employee');
		}

		$data = $this->request->getPost();
		$this->hr_payroll_model->timesheet_leave_calculation($data);
		$message = app_lang('updated_successfully');
		echo json_encode([
			'message' => $message,
		]);
	}

	public function pdf_payslip_templates()
	{
		$data['title'] = app_lang('pdf_payslip_templates');
		$data['pdf_payslip_templates'] = $this->hr_payroll_model->get_pdf_payslip_template();

		return $this->template->rander("Hr_payroll\Views\includes\pdf_payslip_templates", $data);
	}

	/**
	 * setting incometax rates
	 * @return [type]
	 */
	public function setting_pdf_payslip_templates() {
		if ($this->request->getPost()) {

			$data = $this->request->getPost();
			if (!$this->request->getPost('id')) {

				$mess = $this->hr_payroll_model->update_pdf_payslip_templates($data);
				if ($mess) {
					$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));

				} else {
					$this->session->setFlashdata("error_message", app_lang("hrp_updated_failed"));
				}

				app_redirect(('hr_payroll/pdf_payslip_templates'));
			}
		}
	}

	// TODO
	
	/**
	 * payslip pdf template
	 * @param  string $id 
	 * @return [type]     
	 */
		public function payslip_pdf_template($id = '') {

			if ($this->request->getPost()) {
				$message = '';
				$data = $this->request->getPost();

				$data['content'] = decode_ajax_post_data($this->request->getPost('content'));

				if ($id == '') {
					$id = $this->hr_payroll_model->add_pdf_payslip_template($data);

					if ($id) {
						$this->session->setFlashdata("success_message", app_lang("hrp_added_successfully"));
					}

					app_redirect(('hr_payroll/payslip_pdf_template/'.$id));
				} else {

					$success = $this->hr_payroll_model->update_pdf_payslip_template($data, $id);

					if ($success) {
						$this->session->setFlashdata("success_message", app_lang("hrp_updated_successfully"));
					}

					app_redirect(('hr_payroll/payslip_pdf_template/'.$id));
				}

			}
			$data = [];

			if ($id == '') {
			//add
				$title = _l('add_pdf_payslip_template');
				$data['title'] = $title;

				/*sample contract template*/
				$model_info = new \stdClass;
				$model_info->id = 0;
				$model_info->template_name = 'sample_pdf_payslip_template';
				$model_info->default_message = $this->template->view('Hr_payroll\Views\includes\modal_forms\sample_pdf_payslip_html');
				$model_info->content = '';
				$model_info->delete = 0;

			} else {
			//update
				$title = _l('update_pdf_payslip_template');
				$data['title'] = $title;
				$get_pdf_payslip_template = $this->hr_payroll_model->get_pdf_payslip_template($id);

				$data['pdf_payslip_template'] = $get_pdf_payslip_template; 
				$model_info = $get_pdf_payslip_template;
				$model_info->default_message = $this->template->view('Hr_payroll\Views\includes\modal_forms\sample_pdf_payslip_html');

			}

			$view_data['model_info'] = $model_info;
			$view_data['column_name'] = 'content';
			$payslip_available_variables = payslip_available_variables();
			$variables = $payslip_available_variables['payslip_columns'];
			$view_data['variables'] = $variables ? $variables : array();

			$view_data['unsupported_title_variables'] = json_encode(array("SIGNATURE", "TASKS_LIST", "TICKET_CONTENT", "MESSAGE_CONTENT", "EVENT_DETAILS"));

			$data['sample_pdf_payslip_template'] = $this->template->view('Hr_payroll\Views\includes\modal_forms\pdf_payslip_template_form', $view_data);
			$data['id'] = $id;

			$data['payslip_templates'] = $this->hr_payroll_model->get_hrp_payslip_templates();

			return $this->template->rander('Hr_payroll\Views\includes/pdf_payslip_template_detail', $data);

		}

	/**
	 * delete payslip pdf template
	 * @param  [type] $id
	 * @return [type]    
	 */
	public function delete_payslip_pdf_template() {
		if (!is_admin() && !hrp_has_permission('hr_payroll_can_delete_hrp_setting')) {
			app_redirect("forbidden");
		}
		$id = $this->request->getPost('id');
		if (!$id) {
			app_redirect(('hr_payroll/pdf_payslip_templates'));
		}

		$response = $this->hr_payroll_model->delete_pdf_payslip_template($id);
		if (is_array($response) && isset($response['referenced'])) {
			$this->session->setFlashdata("error_message", app_lang("is_referenced"));
		} elseif ($response == true) {
			$this->session->setFlashdata("success_message", app_lang("deleted"));
		} else {
			$this->session->setFlashdata("error_message", app_lang("problem_deleting"));
		}
		app_redirect('hr_payroll/pdf_payslip_templates');
	}

	/**
	 * save pdf payslip data
	 * @return [type] 
	 */
	public function save_pdf_payslip_data()
	{
		if (!has_permission('hrp_setting', '', 'edit')) {
			header('HTTP/1.0 400 Bad error');
			echo json_encode([
				'success' => false,
				'message' => _l('access_denied'),
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
		$message = _l('updated_successfully', _l('contract'));

		echo json_encode([
			'success' => $success,
			'message' => $message,
		]);
	}

	/**
	 * get pdf payslip template
	 * @param  string $pdf_payslip_template_id 
	 * @return [type]                          
	 */
	public function get_pdf_payslip_template($id = '', $payslip_template_id = '') {
		
		$payslip_template_data = '';
		$payslip_name = '';
		if (isset($id) && $id != '' && $id != 0) {

			// update
			$get_hrp_payslip = $this->hr_payroll_model->get_hrp_payslip($id);
			$pdf_payslip_template_id = '';
			if($get_hrp_payslip){
				$pdf_payslip_template_id = $get_hrp_payslip->pdf_template_id;
				$payslip_name = $get_hrp_payslip->payslip_name;
			}

			$pdf_payslip_template_selected = $this->hr_payroll_model->get_pdf_payslip_template_selected_html($payslip_template_id, $pdf_payslip_template_id);

		} else {
			// create
			$pdf_payslip_template_selected = $this->hr_payroll_model->get_pdf_payslip_template_selected_html($payslip_template_id, '');
		}

		echo json_encode([
			'pdf_payslip_template_selected' => $pdf_payslip_template_selected,
			'payslip_name' => $payslip_name,
		]);
		die;
	}

	/**
	 * edit payslip
	 * @return [type] 
	 */
	public function edit_payslip() {
		if ($this->request->getPost()) {
			$data = $this->request->getPost();
			if (isset($data['id'])) {

				if (!is_admin() && !hrp_has_permission('hr_payroll_can_edit_hrp_payslip')) {
					access_denied('hrp_payslip');
				}
				$update_data = [];
				$update_data['payslip_name'] = $data['payslip_name'];
				$update_data['pdf_template_id'] = $data['pdf_template_id'];

				$builder = db_connect('default');
				$hrp_payslips = $builder->table(get_db_prefix().'hrp_payslips');

				$hrp_payslips->where('id', $data['id']);
				$affected_rows = $hrp_payslips->update($update_data);
				if($affected_rows > 0){
					$this->session->setFlashdata("added_successfully", app_lang("hrp_payslip"));
				}
				app_redirect(('hr_payroll/payslip_manage'));

			}
		}
	}

//End file

}