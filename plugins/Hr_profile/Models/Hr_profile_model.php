<?php

namespace Hr_profile\Models;

use App\Models\Crud_model;
use App\Controllers\Security_Controller;

class Hr_profile_model extends Crud_model {


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
	public function hr_profile_run_query($query_string)
	{
		return  $this->db->query("$query_string")->getResultArray();
	}

	/**
	 * count items
	 * @return [type] 
	 */
	public function count_all_items($where = '')
	{
		$items = $this->db->table(get_get_db_prefix().'items');
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

		$path = HR_VIEWPATH . 'admin/tables/' . $table . EXT;


		if (!file_exists($path)) {
			$path = $table;

			if (!endsWith($path, EXT)) {
				$path .= EXT;
			}
		} else {
			$myPrefixedPath = HR_VIEWPATH . 'admin/tables/my_' . $table . EXT;
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
	 * get hr profile dashboard data
	 * @return array 
	 */
	public function get_hr_profile_dashboard_data(){
		$data_hrm = [];
		$Users_model = model("Models\Users_model");
		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
		);
		$staff = $Users_model->get_details($options)->getResultArray();

		$total_staff = count($staff);
		$new_staff_in_month = $this->db->query('SELECT * FROM '.get_db_prefix().'users WHERE MONTH(created_at) = '.date('m').' AND YEAR(created_at) = '.date('Y'))->getResultArray();
		$staff_working = $this->db->query('SELECT * FROM '.get_db_prefix().'users WHERE status_work = "working"')->getResultArray();
		$staff_birthday = $this->db->query('SELECT * FROM '.get_db_prefix().'users WHERE status_work = "working" AND MONTH(dob) = '.date('m').' ORDER BY dob ASC')->getResultArray();
		$staff_inactivity = $this->db->query('SELECT * FROM '.get_db_prefix().'users WHERE status_work = "inactivity" AND id in (SELECT staffid FROM '.get_db_prefix().'hr_list_staff_quitting_work where dateoff >= \''.date('Y-m-01').' 00:00:00'.'\' and dateoff <= \''.date('Y-m-t').' 23:59:59'.'\')')->getResultArray();
		$overdue_contract = $this->db->query('SELECT * FROM '.get_db_prefix().'hr_staff_contract WHERE end_valid < "'.get_my_local_time('Y-m-d').'" AND contract_status = "valid"')->getResultArray();
		$expire_contract = $this->db->query('SELECT * FROM '.get_db_prefix().'hr_staff_contract WHERE end_valid <= "'.date('Y-m-d',strtotime('+7 day',strtotime(get_my_local_time('Y-m-d')))).'" AND end_valid >= "'.get_my_local_time('Y-m-d').'" AND contract_status = "valid"')->getResultArray();

		$data_hrm['staff_birthday'] = $staff_birthday;
		$data_hrm['total_staff'] = $total_staff;
		$data_hrm['new_staff_in_month'] = count($new_staff_in_month);
		$data_hrm['staff_working'] = count($staff_working);
		$data_hrm['staff_inactivity'] = count($staff_inactivity);
		$data_hrm['overdue_contract'] = count($overdue_contract);
		$data_hrm['expire_contract'] = count($expire_contract);
		$data_hrm['overdue_contract_data'] = $overdue_contract;
		$data_hrm['expire_contract_data'] = $expire_contract;
		return $data_hrm;
	}
	/**
	 * staff chart by age
	 * @return array 
	 */
	public function staff_chart_by_age()
	{
		$Users_model = model("Models\Users_model");
		$options = array(
			"user_type" => "staff",
			"deleted" => 0,
		);
		$staffs = $Users_model->get_details($options)->getResultArray();

		$chart = [];
		$status_1 = ['name' => _l('18_24_age'), 'color' => '#777', 'y' => 0, 'z' => 100];
		$status_2 = ['name' => _l('25_29_age'), 'color' => '#fc2d42', 'y' => 0, 'z' => 100];
		$status_3 = ['name' => _l('30_39_age'), 'color' => '#03a9f4', 'y' => 0, 'z' => 100];
		$status_4 = ['name' => _l('40_60_age'), 'color' => '#ff6f00', 'y' => 0, 'z' => 100];
		foreach ($staffs as $staff) {
			$diff = date_diff(date_create(), date_create($staff['dob']));
			$age = $diff->format('%Y');

			if($age >= 18 && $age <= 24)
			{
				$status_1['y'] += 1;
			}elseif ($age >= 25 && $age <= 29) {
				$status_2['y'] += 1;
			}elseif ($age >= 30 && $age <= 39) {
				$status_3['y'] += 1;
			}elseif ($age >= 40 && $age <= 60) {
				$status_4['y'] += 1;
			}
		}
		if($status_1['y'] > 0){
			array_push($chart, $status_1);
		}
		if($status_2['y'] > 0){
			array_push($chart, $status_2);
		}
		if($status_3['y'] > 0){
			array_push($chart, $status_3);
		}
		if($status_4['y'] > 0){
			array_push($chart, $status_4);
		}
		return $chart;
	}


	/**
	 * contract type chart
	 * @return  array
	 */
	public function contract_type_chart()
	{
		$contracts = $this->db->query('SELECT * FROM '.get_db_prefix().'hr_staff_contract')->getResultArray();
		$statuses = $this->get_contracttype();
		$color_data = ['#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
		'#63b598', '#ce7d78', '#ea9e70', '#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1' ,
		'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];

		$_data                         = [];
		$total_value =0;
		$has_permission = has_permission('pw_mana_projects', '', 'view');
		$sql            = '';
		foreach ($statuses as $status) {
			$sql .= ' SELECT COUNT(*) as total';
			$sql .= ' FROM ' . get_db_prefix() . 'hr_staff_contract';
			$sql .= ' WHERE name_contract=' . $status['id_contracttype'];
			$sql .= ' UNION ALL ';
			$sql = trim($sql);
		}

		$result = [];
		if ($sql != '') {
			$sql    = substr($sql, 0, -10);
			$result = $this->db->query($sql)->getResultObject();
		}
		foreach ($statuses as $key => $status) {
			$total_value+=(int)$result[$key]->total;
		}
		foreach ($statuses as $key => $status) {
			if($total_value > 0){
				array_push($_data,
					[ 
						'name' => $status['name_contracttype'],
						'y'    => (int)$result[$key]->total,
						'z'    => (number_format(((int)$result[$key]->total/$total_value), 4, '.',""))*100,
						'color'=>$color_data[$key]
					]);
			}else{
				array_push($_data,
					[ 
						'name' => $status['name_contracttype'],
						'y'    => (int)$result[$key]->total,
						'z'    => (number_format(((int)$result[$key]->total/1), 4, '.',""))*100,
						'color'=>$color_data[$key]
					]);
			}
		}
		return $_data;
	}


	/**
	 * staff chart by departments
	 * @return [type] 
	 */
	public function staff_chart_by_departments()
	{
		$chart = [];
		$color_data = ['#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
		'#63b598', '#ce7d78', '#ea9e70' ,
		'#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00' ];


		$builder = $this->db->table(get_db_prefix().'team');
		$staff_departments = $builder->get()->getResultArray();

		/*get staff working*/
		$arr_staff_working = [];
		$builder = $this->db->table(get_db_prefix().'users');
		$builder->where('deleted = 0 AND user_type = "staff" AND status_work = "working"');
		$users = $builder->get()->getResultArray();
		foreach ($users as $value) {
		    $arr_staff_working[] = $value['id'];
		}

		$color_index=0;
		foreach ($staff_departments as $key => $value) {
			$total_staff = explode(",", $value['members']);

			$total_staff_ids = count(array_intersect($arr_staff_working, $total_staff));

			if(isset($color_data[$color_index])){
				array_push($chart, [
					'name' 		=> $value['title'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$total_staff_ids,
					'z' 		=> 100
				]);
			}else{
				$color_index = 0;
				array_push($chart, [
					'name' 		=> $value['department_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$total_staff_ids,
					'z' 		=> 100
				]);
			}
			$color_index++;
		}

		return $chart;
	}


	/**
	 * staff chart by job positions
	 * @return [type] 
	 */
	public function staff_chart_by_job_positions()
	{
		$chart = [];
		$color_data = ['#d2737d' ,'#c0a43c' ,'#f2510e' ,'#651be6' ,'#79806e' ,'#61da5e' ,'#cd2f00','#00FF7F', '#0cffe95c','#80da22','#f37b15','#da1818','#176cea','#5be4f0', '#57c4d8', '#a4d17a', '#225b8', '#be608b', '#96b00c', '#088baf',
		'#63b598', '#ce7d78', '#ea9e70', '#a48a9e', '#c6e1e8', '#648177' ,'#0d5ac1' ];

		$builder = $this->db->table(get_db_prefix().'users');

		$builder->select(get_db_prefix().'hr_job_position.position_name, count(id) as total_staff, job_position');
		$builder->join(get_db_prefix() . 'hr_job_position', get_db_prefix() . 'hr_job_position.position_id = ' . get_db_prefix() . 'users.job_position', 'left');
		$builder->where('deleted = 0 AND user_type = "staff" AND status_work != "inactivity"');
		$builder->groupBy('job_position');
		$staff_departments = $builder->get()->getResultArray();

		$color_index=0;
		foreach ($staff_departments as $key => $value) {
			if(isset($color_data[$color_index])){
				array_push($chart, [
					'name' 		=> $value['position_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=>	(int)$value['total_staff'],
					'z' 		=> 100
				]);
			}else{
				$color_index = 0;
				array_push($chart, [
					'name' 		=> $value['position_name'],
					'color' 	=> $color_data[$color_index],
					'y' 		=> (int)$value['total_staff'],
					'z' 		=> 100
				]);
			}
			$color_index++;
		}

		return $chart;
	}


	/**
	 * report by staffs
	 * @return [type] 
	 */
	public function report_by_staffs()
	{
		$custom_date_select = '';

		$current_year = date('Y');
		for($_month = 1 ; $_month <= 12; $_month++){
			$month_t = date('m',mktime(0, 0, 0, $_month, 04, 2016));

			if($_month == 5){
				$chart['categories'][] = _l('month_05');
			}else{
				$chart['categories'][] = _l('month_'.$_month);
			}

			$month = $current_year.'-'.$month_t;

			$chart['hr_new_staff'][] = $this->new_staff_by_month($month);
			$chart['hr_staff_are_working'][] = $this->staff_working_by_month($month);
			$chart['hr_staff_quit'][] = $this->staff_quit_work_by_month($month);
		}
		return $chart;
	}


	/**
	 * new staff by month
	 * @param  [type] $from 
	 * @param  [type] $to   
	 * @return [type]       
	 */
	public function new_staff_by_month($month)
	{
		$builder = $this->db->table(get_db_prefix().'users');
		$builder->select('count(id) as total_staff');
		$sql_where = "deleted = 0 AND user_type = 'staff' AND date_format(created_at, '%Y-%m') = '".$month."'";
		$builder->where($sql_where);
		$result = $builder->get()->getRow();

		if($result){
			return (int)$result->total_staff;
		}
		return 0;
	}


	/**
	 * staff working by_month
	 * @param  [type] $from 
	 * @param  [type] $to   
	 * @return [type]       
	 */
	public function staff_working_by_month($month)
	{
		$builder = $this->db->table(get_db_prefix().'users');

		$builder->select('count(id) as total_staff');
		$sql_where = "deleted = 0 AND user_type = 'staff' AND status_work = 'working' AND date_format(created_at, '%Y-%m') < '".$month."'";
		$builder->where($sql_where);
		$result = $builder->get()->getRow();

		if($result){
			return (int)$result->total_staff;
		}
		return 0;

	}


	/**
	 * staff quit work by month
	 * @param  [type] $month 
	 * @return [type]        
	 */
	public function staff_quit_work_by_month($month)
	{	
		$builder = $this->db->table(get_db_prefix().'users');
		$builder->select('count(id) as total_staff');
		$sql_where = 'deleted = 0 AND user_type = "staff" AND id in (SELECT staffid FROM '.get_db_prefix().'hr_list_staff_quitting_work where date_format(dateoff, "%Y-%m") = "'.$month.'" AND approval = "approved") OR (status_work = "inactivity" AND date_format(date_update, "%Y-%m") = "'.$month.'")';
		$builder->where($sql_where);
		$result = $builder->get()->getRow();

		if($result){
			return (int)$result->total_staff;
		}
		return 0;

	}
	

	
	/**
	 * get contracttype
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_contracttype($id = false){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_staff_contract_type');
			$builder->where('id_contracttype', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'hr_staff_contract_type order by id_contracttype desc')->getResultArray();
		}

	}

	/**
	 * get data departmentchart
	 * @return array 
	 */
	public function get_data_departmentchart(){        
		$department =  $this->db->query('select  id as id, parent_id as pid, title, manager_id
			from '.get_db_prefix().'team as d where deleted = 0 order by d.parent_id, d.id')->getResultArray();

		$dep_tree = [];
		foreach ($department as $dep) {
			if($dep['pid']==0){
				$job_pst = hr_profile_job_position_by_staff($dep['manager_id']);


				array_push($dep_tree, 
					[
						'id' => $dep['id'],
						'name' =>$dep['title'],
						'title'    => $dep['manager_id'] != 0 ? get_staff_full_name1($dep['manager_id']) : '',
						'image' => get_staff_image($dep['manager_id'], false),
						
						'children'=>$this->get_child_node_chart($dep['id'], $department),
						'reality_now' => _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']),
						'dp_user_icon' => '"fa fa-user-o"',
						'job_position' => $job_pst,
					]
				);
			} else {
				break;
			}            
		}  
		return $dep_tree;
	}
	/**
	 * get child node chart
	 * @param  integer $id      
	 * @param  integer $arr_dep 
	 * @return array          
	*/
	private function get_child_node_chart($id, $arr_dep){
		$dep_tree = array();
		foreach ($arr_dep as $dep) {
			if($dep['pid']==$id){   
				$node = array();  
				$node['id'] = $dep['id'];           
				$node['name'] = $dep['title'];
				$node['title'] = $dep['manager_id'] != 0 ? get_staff_full_name1($dep['manager_id']) : '';
				$node['image'] = get_staff_image($dep['manager_id'], false);
				
				$node['dp_user_icon'] = '"fa fa-user-o"';
				$node['job_position'] = hr_profile_job_position_by_staff($dep['manager_id']);
				

				$node['children'] = $this->get_child_node_chart($dep['id'], $arr_dep);
				$node['reality_now'] = _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']);
				if(count($node['children'])==0){
					unset($node['children']);
				}
				$dep_tree[] = $node;
			} 
		} 
		return $dep_tree;
	}

	/**
	 * get data departmentchart v2
	 * @return [type] 
	 */
	public function get_data_departmentchart_v2(){ 
		$manager_id = get_staff_user_id1();

		$department =  $this->db->query('select  id as id, parent_id as pid, title, manager_id
			from '.get_db_prefix().'team as d where deleted = 0 order by d.parent_id, d.id')->getResultArray();

		$dep_tree = [];
		foreach ($department as $dep) {
			if($dep['pid']==0 && $dep['manager_id'] == get_staff_user_id1()){
				$job_pst = hr_profile_job_position_by_staff($dep['manager_id']);

				array_push($dep_tree, 
					[
						'id' => $dep['id'],
						'name' =>$dep['title'],
						'title'    => $dep['manager_id'] != 0 ? get_staff_full_name1($dep['manager_id']) : '',
						'image' => get_staff_image($dep['manager_id'], false),
						
						'children'=>$this->get_child_node_chart($dep['id'], $department),
						'reality_now' => _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']),
						'dp_user_icon' => '"fa fa-user-o"',
						'job_position' => $job_pst,
					]
				);
			} elseif($dep['pid'] ==0 && $dep['manager_id'] != get_staff_user_id1()){

				$job_pst = hr_profile_job_position_by_staff($dep['manager_id']);
				$child_node = $this->get_child_node_chart_v2($dep['id'], $department);
				$check_is_manager = $this->check_is_manager($child_node, $manager_id);

				if(preg_match('/true/', json_encode($check_is_manager))){

					array_push($dep_tree, 
						[
							'id' => $dep['id'],
							'name' =>$dep['title'],
							'title'    => $dep['manager_id'] != 0 ? get_staff_full_name1($dep['manager_id']) : '',
							'image' => get_staff_image($dep['manager_id'], false),
							'children'=>$this->get_child_node_chart_v2($dep['id'], $department),
							'reality_now' => _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']),
							'dp_user_icon' => '"fa fa-user-o"',
							'job_position' => $job_pst,
						]
					);
				}

			}            
		} 
		return $dep_tree;
	}


	/**
	 * get child node chart v2
	 * @param  [type] $id      
	 * @param  [type] $arr_dep 
	 * @return [type]          
	 */
	private function get_child_node_chart_v2($id, $arr_dep){
		$dep_tree = array();
		foreach ($arr_dep as $dep) {
			if($dep['pid']==$id){

				$node = array();  
				$node['id'] = $dep['id'];           
				$node['name'] = $dep['title'];
				$node['manager_id'] = $dep['manager_id'];
				$node['title'] = $dep['manager_id'] != 0 ? get_staff_full_name1($dep['manager_id']) : '';
				$node['image'] = get_staff_image($dep['manager_id'], false);
				$node['dp_user_icon'] = '"fa fa-user-o"';
				$node['job_position'] = hr_profile_job_position_by_staff($dep['manager_id']);
				

				$node['children'] = $this->get_child_node_chart_v2($dep['id'], $arr_dep);
				$node['reality_now'] = _l('hr_current_personnel').': '.$this->count_reality_now($dep['id']);
				if(count($node['children'])==0){
					unset($node['children']);
				}
				$dep_tree[] = $node;
			} 
		} 
		return $dep_tree;
	}

	/**
	 * check is manager
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function check_is_manager($data, $manager_id)
	{	
		$check_array = array();
		foreach ($data as $key => $value) {
			if($value['manager_id'] == $manager_id){
				$check_array[] = true;
			}elseif(isset($value['children'])){
				$check_array[] = $this->check_is_manager($value['children'], $manager_id);
			}
		}
		return $check_array;
	}

	/**
	 * count reality now
	 * @param  integer $department 
	 * @return integer             
	 */
	public function count_reality_now($department){
		$total_staff = 0;

		$builder = $this->db->table(get_db_prefix().'team');
		$builder->where('id', $department);
		$team = $builder->get()->getRow();
		if($team){
			if( $team->members != null && strlen( $team->members) > 0){
				$total_staff = count(explode(",", $team->members));
			}
		}
		return $total_staff;
	}

	/**
	 * get data chart
	 * @return array 
	 */
	public function get_data_chart()
	{
		$department =  $this->db->query('select s.id as id, s.team_manage as pid, CONCAT(s.first_name," ",s.last_name) as name,  r.title as rname, j.position_name as job_position_name
			from '.get_db_prefix().'users as s left join '.get_db_prefix().'roles as r on s.role_id = r.id left join '.get_db_prefix().'hr_job_position as j on j.position_id = s.job_position where s.status_work != "inactivity" AND s.deleted = 0       
			order by s.team_manage, s.id')->getResultArray();

		$dep_tree = array();
		foreach ($department as $dep) {
			if($dep['pid'] == 0){
				$dpm = $this->getdepartment_name($dep['id']);
				$node = array();
				$node['name'] = $dep['name'];
				$node['job_position_name'] = '';

				if($dep['job_position_name'] != null && $dep['job_position_name'] != 'undefined'){
					$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';

					$node['job_position_name'] = $dep['job_position_name'];
				}

				if($dep['rname'] != null){
					$node['title'] = $dep['rname'];
					$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
				}else{
					$node['title'] = '';
				}
				if($dpm->name != null){
					$node['departmentname'] = $dpm->name;
					$node['dp_icon'] = '"fa fa-sitemap"';
				}else{
					$node['departmentname'] = '';
				}
				$node['image'] = get_staff_image($dep['id'], false);

				$node['children'] = $this->get_child_node_staff_chart($dep['id'], $department);
				$dep_tree[] = $node;
			} else {
				break;
			}            
		}   
		return $dep_tree;
	}

	/**
	 * get data chart v2
	 * @return [type] 
	 */
	public function get_data_chart_v2()
	{
		$team_manage = get_staff_user_id1();
		$staffs =  $this->db->query('select s.id as id, s.team_manage as pid, CONCAT(s.first_name," ",s.last_name) as name,  r.title as rname, j.position_name as job_position_name
			from '.get_db_prefix().'users as s left join '.get_db_prefix().'roles as r on s.role_id = r.id left join '.get_db_prefix().'hr_job_position as j on j.position_id = s.job_position where s.status_work != "inactivity" AND s.deleted = 0      
			order by s.team_manage, s.id ')->getResultArray();
		$dep_tree = array();
		foreach ($staffs as $dep) {
			if($dep['pid'] == 0 && $dep['id'] == $team_manage){
				$dpm = $this->getdepartment_name($dep['id']);
				$node = array();
				$node['name'] = $dep['name'];
				$node['job_position_name'] = '';

				if($dep['job_position_name'] != null && $dep['job_position_name'] != 'undefined'){
					$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';

					$node['job_position_name'] = $dep['job_position_name'];
				}

				if($dep['rname'] != null){
					$node['title'] = $dep['rname'];
					$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
				}else{
					$node['title'] = '';
				}
				if($dpm->name != null){
					$node['departmentname'] = $dpm->name;
					$node['dp_icon'] = '"fa fa-sitemap"';
				}else{
					$node['departmentname'] = '';
				}
				$node['image'] = get_staff_image($dep['id'], false);

				$node['children'] = $this->get_child_node_staff_chart($dep['id'], $staffs);
				$dep_tree[] = $node;

			} elseif($dep['pid'] ==0 && $dep['id'] != $team_manage){
				
				$child_node = $this->get_child_node_staff_chart($dep['id'], $staffs);
				$check_is_team_manage = $this->check_is_team_manage($child_node, $team_manage);

				if(preg_match('/true/', json_encode($check_is_team_manage))){

					$dpm = $this->getdepartment_name($dep['id']);
					$node = array();
					$node['name'] = $dep['name'];
					$node['job_position_name'] = '';

					if($dep['job_position_name'] != null && $dep['job_position_name'] != 'undefined'){
						$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';

						$node['job_position_name'] = $dep['job_position_name'];
					}

					if($dep['rname'] != null){
						$node['title'] = $dep['rname'];
						$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
					}else{
						$node['title'] = '';
					}

					if($dpm->name != null){
						$node['departmentname'] = $dpm->name;
						$node['dp_icon'] = '"fa fa-sitemap"';
					}else{
						$node['departmentname'] = '';
					}
					$node['image'] = get_staff_image($dep['id'], false);
					$node['children'] = $this->get_child_node_staff_chart($dep['id'], $staffs);

					$dep_tree[] = $node;
				}

			}            
		}   
		return $dep_tree;
	}

	/**
	 * check is team manage
	 * @param  [type] $data       
	 * @param  [type] $manager_id 
	 * @return [type]             
	 */
	public function check_is_team_manage($data, $manager_id)
	{	
		$check_array = array();
		foreach ($data as $key => $value) {
			if($value['team_manage'] == $manager_id){
				$check_array[] = true;
			}elseif(isset($value['children'])){
				$check_array[] = $this->check_is_team_manage($value['children'], $manager_id);
			}
		}
		return $check_array;
	}

	/**
	 * get department tree
	 * @return array 
	 */
	public function get_department_tree(){
		$department =  $this->db->query('select  id as id, parent_id as pid, title from '.get_db_prefix().'team as d where deleted = 0 order by d.parent_id, d.id')->getResultArray();

		$dep_tree = array();

		$node = array();
		$node['id'] = 0;
		$node['title'] = app_lang('dropdown_non_selected_tex');
		$node['subs'] = array();
		$dep_tree[] = $node;

		foreach ($department as $dep) {
			if($dep['pid']==0){
				$node = array();
				$node['id'] = $dep['id'];
				$node['title'] = $dep['title'];
				$node['subs'] = $this->get_child_node($dep['id'], $department);
				$dep_tree[] = $node;
			} else {
				break;
			}            
		}     
		return $dep_tree;
	}


	 /**
	 * Get child node of department tree
	 * @param  $id      current department id
	 * @param  $arr_dep department array
	 * @return current department tree
	 */
	 private function get_child_node($id, $arr_dep){
	 	$dep_tree = array();
	 	foreach ($arr_dep as $dep) {
	 		if($dep['pid']==$id){   
	 			$node = array();             
	 			$node['id'] = $dep['id'];
	 			$node['title'] = $dep['title'];
	 			$node['subs'] = $this->get_child_node($dep['id'], $arr_dep);
	 			if(count($node['subs'])==0){
	 				unset($node['subs']);
	 			}
	 			$dep_tree[] = $node;
	 		} 
	 	} 
	 	return $dep_tree;
	 }


	/**
	 * get department name
	 * @param  integer $departmentid 
	 * @return object               
	 */
	public function hr_profile_get_department_name($departmentid = 0){
		$builder = $this->db->table(get_db_prefix().'team');
		$builder->where('id', $departmentid);
		return $builder->get()->getRow();
	}
	/**
	 * get all staff not in record
	 * @return array object
	 */
	public function get_all_staff_not_in_record(){
		return $this->db->query('select * from '.get_db_prefix().'users where status = "active" AND deleted = 0 AND user_type = "staff" AND id not in (select staffid from '.get_db_prefix().'hr_rec_transfer_records)')->getResultArray();
	}
	/**
	 * get setting transfer records
	 * @return array 
	 */
	public function get_setting_transfer_records(){
		$builder = $this->db->table(get_db_prefix().'setting_transfer_records');
		return $builder->get()->getResultArray();
	}
	/**
	 * get_staff_tree
	 * @return array 
	 */
	public function get_staff_tree(){
		$department =  $this->db->query('select s.id, s.team_manage as pid, CONCAT(s.first_name," ",s.last_name) as name
			from '.get_db_prefix().'users as s         
			order by s.team_manage, s.id')->getResultArray();
		$dep_tree = array();
		foreach ($department as $dep) {
			if($dep['pid'] == 0){
				$node = array();
				$node['id'] = $dep['id'];
				$node['title'] = $dep['name'];

				$node['subs'] = $this->get_child_node_staff($dep['id'], $department);
				$dep_tree[] = $node;
			} else {
				break;
			}            
		}     
		return $dep_tree;
	}
		/**
	 * Get child node of department tree
	 * @param  $id      current department id
	 * @param  $arr_dep department array
	 * @return current department tree
	 */
		private function get_child_node_staff($id, $arr_dep){
			$dep_tree = array();
			foreach ($arr_dep as $dep) {
				if($dep['pid']==$id){   
					$node = array();             
					$node['id'] = $dep['id'];
					$node['title'] = $dep['name'];
					$node['subs'] = $this->get_child_node_staff($dep['id'], $arr_dep);
					if(count($node['subs']) == 0){
						unset($node['subs']);
					}
					$dep_tree[] = $node;
				} 
			} 
			return $dep_tree;
		}
	/**
	 * get all jp interview training
	 * @return object 
	 */
	public function get_all_jp_interview_training(){
		return $this->db->query('select * from '.get_db_prefix().'hr_jp_interview_training')->getRow();
	}
	/**
	 * get setting asset allocation
	 * @return array 
	 */
	public function get_setting_asset_allocation(){
		$builder = $this->db->table(get_db_prefix().'setting_asset_allocation');
		return $builder->get()->getResultArray();
	}

	/**
	 * get list record meta
	 * @return array 
	 */
	public function get_list_record_meta(){
		$builder = $this->db->table(get_db_prefix().'records_meta');
		return $builder->get()->getResultArray();
	}
	/**
	 * add setting transfer records
	 */
	public function add_setting_transfer_records($data_transfer_meta){
		$builder = $this->db->table(get_db_prefix().'setting_transfer_records');

		$builder->emptyTable();
		$list_meta = $this->get_list_record_meta();
		foreach ($data_transfer_meta['meta'] as $key => $value) {
			if($value != ''){
				$name='';
				foreach ($list_meta as $list_item) {

					if($list_item['meta']==$value){
						$name=$list_item['name'];
					}
				}
				$builder->insert([
					'name' => $name,
					'meta' => $value
				]);
			}
		}  
	}
	/**
	 * add setting asset allocation
	 * @param array $data_asset_name 
	 */
	public function add_setting_asset_allocation($data_asset_name){
		$builder = $this->db->table(get_db_prefix().'setting_asset_allocation');

		$builder->emptyTable();       
		foreach ($data_asset_name['name'] as $key => $value) {  
			if($value != ''){
				$builder->insert([
					'name' => $value,
					'meta' => ''
				]);
			}              
		}
	} 


	/**
	 * add rec transfer records
	 * @param array $data_asset_name 
	 */
	public function add_rec_transfer_records($data)
	{     
		$builder = $this->db->table(get_db_prefix().'hr_rec_transfer_records');
		$builder->insert([
			'staffid' => $data['staffid'],
			'creator' => get_staff_user_id1(),
			'firstname' => $data['firstname'],
			'birthday' => $data['birthday'],
			'staff_identifi' => $data['staffidentifi']
		]);

		$insert_id = $this->db->insertID();

		if ($insert_id) {
			return $insert_id;
		}
		return false;

	}


	/**
	 * group checklist
	 * @return array 
	 */
	public function group_checklist(){
		$builder = $this->db->table(get_db_prefix().'group_checklist');
		return $builder->get()->getResultArray();
	}
	/**
	 * get setting training 
	 * @return object
	 */
	public function get_setting_training(){
		$builder = $this->db->table(get_db_prefix().'setting_training');
		return $builder->get()->getRow();
	}


	/**
	 * get job position
	 * @param  integer $id 
	 * @return array or object     
	 */
	public function get_job_position($id = false)
	{
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_job_position');

			$builder->where('position_id', $id);
			return $builder->get()->getRow();
		}
		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'hr_job_position')->getResultArray();
		}
	}




	/**
	 * get allowance type
	 * @param  integer $id 
	 * @return array or object      
	 */
	public function get_allowance_type($id = false){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_allowance_type');
			$builder->where('type_id', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'hr_allowance_type');
			return  $builder->get()->getResultArray();
		}

	}


	/**
	 * get salary form
	 * @param  integer $id 
	 * @return array or object
	 */
	public function get_salary_form($id = false){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_salary_form');
			$builder->where('form_id', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'hr_salary_form order by form_id desc')->getResultArray();
		}

	}



	/**
	 * get procedure retire
	 * @param  integer $id 
	 * @return array     
	 */
	public function get_procedure_retire($id = ''){
		if($id == ''){
			$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');
			return $builder->get()->getResultArray();
		}else{
			$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');

			$builder->where('procedure_retire_id', $id);
			return $builder->get()->getResultArray();
		}
	}


	/**
	 * get allowance type tax
	 * @param  integer $id 
	 */
	public function get_allowance_type_tax($id = false){
		$builder = $this->db->table(get_db_prefix().'hr_allowance_type');
		$builder->where('taxable', "1");
		return  $builder->get()->getResultArray();
	}



	/**
	 * add contract type
	 * @param array $data 
	 */
	public function add_contract_type($data){
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract_type');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}


	/**
	 * delete contract type
	 * @param  integer $id 
	 */
	public function delete_contract_type($id){
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract_type');

		$builder->where('id_contracttype', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add allowance type
	 * @param array $data 
	 */
	public function add_allowance_type($data){
		$data['allowance_val'] = hr_profile_reformat_currency($data['allowance_val']);

		$builder = $this->db->table(get_db_prefix().'hr_allowance_type');

		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}


	/**
	 * update allowance type
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_allowance_type($data, $id)
	{   
		$data['allowance_val'] = hr_profile_reformat_currency($data['allowance_val']);
		
		$builder = $this->db->table(get_db_prefix().'hr_allowance_type');
		$builder->where('type_id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * update contract type
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_contract_type($data, $id)
	{   
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract_type');

		$builder->where('id_contracttype', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete allowance type
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_allowance_type($id){
		$builder = $this->db->table(get_db_prefix().'hr_allowance_type');

		$builder->where('type_id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}



	/**
	 * add salary form
	 * @param array $data 
	 */
	public function add_salary_form($data){
		$data['salary_val'] = hr_profile_reformat_currency($data['salary_val']);

		$builder = $this->db->table(get_db_prefix().'hr_salary_form');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}


	/**
	 * update salary form
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_salary_form($data, $id)
	{   
		$data['salary_val'] = hr_profile_reformat_currency($data['salary_val']);

		$builder = $this->db->table(get_db_prefix().'hr_salary_form');
		$builder->where('form_id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete salary form
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_salary_form($id){
		$builder = $this->db->table(get_db_prefix().'hr_salary_form');

		$builder->where('form_id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}



	/**
	 * add procedure form manage
	 * @param array $data 
	 */
	public function add_procedure_form_manage($data)
	{
		if(isset($data['departmentid']) && count($data['departmentid']) > 0){
			$data['department'] = implode(",", $data['departmentid']);
			unset($data['departmentid']);
		}

		$data['datecreator'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);

		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_manage');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if($insert_id){
			return $insert_id;
		}
		return false;
	}


	/**
	 * update procedure form manage
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_procedure_form_manage($data,$id)
	{

		if(isset($data['departmentid']) && count($data['departmentid']) > 0){
			$data['department'] = implode(",", $data['departmentid']);
			unset($data['departmentid']);
		}else{
			$data['department'] = '';
		}

		if(isset($data['name_procedure_retire_edit'])){
			$data['name_procedure_retire'] = $data['name_procedure_retire_edit'];
			unset($data['name_procedure_retire_edit']);
		}

		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_manage');
		$builder->where('id',$id);
		$affectedRows = $builder->update($data);
		if ($affectedRows > 0) {
			return true;
		}
		return false;

	}


	/**
	 * get procedure form manage
	 * @param  integer $id 
	 * @return array or object     
	 */
	public function get_procedure_form_manage($id = '')
	{
		if ($id != '') {
			$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_manage');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}
		if ($id == '') {
			return $this->db->query('select * from '.get_db_prefix().'hr_procedure_retire_manage order by id desc')->getResultArray();
		}
	}


	/**
	 * delete procedure form manage
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_procedure_form_manage($id){
		$affected_rows = 0;
		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_manage');

		$builder->where('id', $id);
		$affectedRows = $builder->delete();
		if ($affectedRows > 0) {
			$affected_rows++;
		}

		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');

		$builder->where('procedure_retire_id', $id);
		$affectedRows = $builder->delete();
		if ($affectedRows > 0) {
			$affected_rows++;
		}
		
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * check department on procedure
	 * @param  integer $departmentid 
	 * @return array               
	 */
	public function check_department_on_procedure($departmentid)
	{
		$data = $this->get_procedure_form_manage();

		$data_val = '';
		foreach ($data as $key => $value) {
			$departments = explode(",", $value['department']);

			if(in_array((int)$departmentid,$departments)){
				$data_val = $value['id'];
				return $data_val;
			}
		}
		return $data_val;

	}


	/**
	 * add procedure retire
	 * @param array $data 
	 */
	public function add_procedure_retire($data){

		$data['option_name'] = json_encode($data['option_name'][1]);
		$data['rel_name'] = implode($data['rel_name']);

		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');
		$builder->insert($data);

		$insert_id = $this->db->insertID();

		return $insert_id;

	}

	/**
	 * delete procedure retire
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_procedure_retire($id){
		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');
		$builder->where('id', $id);
		$affectedRows = $builder->delete();
		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * get edit procedure retire
	 * @param  integer $id 
	 * @return object     
	 */
	public function get_edit_procedure_retire($id){
		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');

		$builder->where('id', $id);
		return $builder->get()->getRow();
	}


	/**
	 * edit procedure retire
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function edit_procedure_retire($data, $id){
		$data['option_name'] = json_encode($data['option_name'][1]);
		$data['rel_name'] = implode($data['rel_name']);

		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire');

		$builder->where('id', $id);
		$affectedRows = $builder->update($data);
		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}
	
	

	/**
	 * get job position training process
	 * @param  integer $id 
	 * @return array      
	 */
	public function get_job_position_training_process($id = false){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');

			$builder->where('job_position_id', $id);
			return  $builder->get()->getResultArray();
		}

		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');

			return  $builder->get()->getResultArray();
		} 
	}
 /**
  * get job position interview process
  * @param  integer $id 
  * @return array or object      
  */
 public function get_job_position_interview_process($id = false){
 	if(is_numeric($id)){
 		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');

 		$builder->where('interview_process_id', $id);
 		return  $builder->get()->getRow();
 	}

 	if($id == false){
 		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');

 		return  $builder->get(get_db_prefix() . 'hr_jp_interview_training')->getResultArray();
 	}
 }

	/**
	 * add position training
	 * @param [type] $data 
	 */
	public function add_position_training($data)
	{
		if (isset($data['disabled'])) {
			$data['active'] = 0;
			unset($data['disabled']);
		} else {
			$data['active'] = 1;
		}
		if (isset($data['iprestrict'])) {
			$data['iprestrict'] = 1;
		} else {
			$data['iprestrict'] = 0;
		}
		if (isset($data['onlyforloggedin'])) {
			$data['onlyforloggedin'] = 1;
		} else {
			$data['onlyforloggedin'] = 0;
		}
		$datecreated = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);

		$builder = $this->db->table(get_db_prefix().'hr_position_training');
		$builder->insert([
			'subject'         => $data['subject'],
			'training_type'   => $data['training_type'],
			'slug'            => to_slug($data['subject']),
			'viewdescription' => $data['viewdescription'],
			'datecreated'     => $datecreated,
			'active'          => $data['active'],
			'onlyforloggedin' => $data['onlyforloggedin'],
			'iprestrict'      => $data['iprestrict'],
			'hash'            => md5($datecreated),
		]);
		$trainingid = $this->db->insertID();
		if (!$trainingid) {
			return false;
		}
		return $trainingid;
	}


	/**
	 * update position training
	 * @param  [type] $data        
	 * @param  [type] $training_id 
	 * @return [type]              
	 */
	public function update_position_training($data, $training_id)
	{
		if (isset($data['disabled'])) {
			$data['active'] = 0;
			unset($data['disabled']);
		} else {
			$data['active'] = 1;
		}
		if (isset($data['onlyforloggedin'])) {
			$data['onlyforloggedin'] = 1;
		} else {
			$data['onlyforloggedin'] = 0;
		}
		if (isset($data['iprestrict'])) {
			$data['iprestrict'] = 1;
		} else {
			$data['iprestrict'] = 0;
		}
		$builder = $this->db->table(get_db_prefix().'hr_position_training');
		$builder->where('training_id', $training_id);
		$affectedRows = $builder->update([
			'subject'         => $data['subject'],
			'training_type'   => $data['training_type'],
			'slug'            => to_slug($data['subject']),
			'viewdescription' => $data['viewdescription'],
			'iprestrict'      => $data['iprestrict'],
			'active'          => $data['active'],
			'onlyforloggedin' => $data['onlyforloggedin'],
		]);
		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}


	/**
	 * get position training
	 * @param  integer $id 
	 * @return array     
	 */
	public function get_position_training($id = '')
	{
		$builder = $this->db->table(get_db_prefix().'hr_position_training');
		$builder->where('training_id', $id);
		$position_training = $builder->get()->getRow();
		if (!$position_training) {
			return false;
		}

		$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
		$builder->where('rel_id', $position_training->training_id);
		$builder->where('rel_type', 'position_training');
		$builder->orderBy('question_order', 'asc');
		$questions = $builder->get()->getResultArray();
		$i         = 0;
		foreach ($questions as $question) {
			$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box');
			$builder->where('questionid', $question['questionid']);
			$box                      = $builder->get()->getRow();
			$questions[$i]['boxid']   = $box->boxid;
			$questions[$i]['boxtype'] = $box->boxtype;
			if ($box->boxtype == 'checkbox' || $box->boxtype == 'radio') {

				$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
				$builder->orderBy('questionboxdescriptionid', 'asc');
				$builder->where('boxid', $box->boxid);
				$boxes_description = $builder->get()->getResultArray();

				if (count($boxes_description) > 0) {
					$questions[$i]['box_descriptions'] = [];
					foreach ($boxes_description as $box_description) {
						$questions[$i]['box_descriptions'][] = $box_description;
					}
				}
			}
			$i++;
		}
		$position_training->questions = $questions;

		return $position_training;
	}


	/**
	 * add training question
	 * @param [type] $data 
	 */
	public function add_training_question($data)
	{
		$questionid = $this->insert_training_question($data['training_id']);
		if ($questionid) {
			$boxid    = $this->insert_question_type($data['type'], $questionid);
			$response = [
				'questionid' => $questionid,
				'boxid'      => $boxid,
			];
			if ($data['type'] == 'checkbox' or $data['type'] == 'radio') {
				$questionboxdescriptionid = $this->add_box_description($questionid, $boxid);
				array_push($response, [
					'questionboxdescriptionid' => $questionboxdescriptionid,
				]);
			}

			return $response;
		}

		return false;
	}


	/**
	 * insert training question
	 * @param  [type] $training_id 
	 * @param  string $question    
	 * @return [type]              
	 */
	private function insert_training_question($training_id, $question = '')
	{
		$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
		$builder->insert([
			'rel_id'   => $training_id,
			'rel_type' => 'position_training',
			'question' => $question,
		]);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}


	/**
	 * Add new question type
	 * @param  string $type       checkbox/textarea/radio/input
	 * @param  mixed $questionid question id
	 * @return mixed
	 */
	private function insert_question_type($type, $questionid)
	{
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box');
		$builder->insert([
			'boxtype'    => $type,
			'questionid' => $questionid,
		]);

		return $this->db->insertID();
	}


	/**
	 * update question
	 * @param  array $data 
	 * @return boolean        
	 */
	public function update_question($data)
	{
		$_required = 1;
		if ($data['question']['required'] == 'false') {
			$_required = 0;
		}
		$affectedRows = 0;

		$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
		$builder->where('questionid', $data['questionid']);
		$affected_rows = $builder->update([
			'question' => $data['question']['value'],
			'required' => $_required,
			'point' => $data['question']['point'],
		]);
		if ($affected_rows > 0) {
			$affectedRows++;
		}
		if (isset($data['boxes_description'])) {
			foreach ($data['boxes_description'] as $box_description) {
				$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
				$builder->where('questionboxdescriptionid', $box_description[0]);
				$affected_rows = $builder->update([
					'description' => $box_description[1],
				]);
				if ($affected_rows > 0) {
					$affectedRows++;
				}
			}
		}
		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}


	/**
	 * update survey questions orders
	 * @param  array $data 
	 */
	public function update_survey_questions_orders($data)
	{
		foreach ($data['data'] as $question) {
			$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
			$builder->where('questionid', $question[0]);
			$builder->update([
				'question_order' => $question[1],
			]);
		}
	}


	/**
	 * remove question
	 * @param  integer $questionid 
	 * @return boolean             
	 */
	public function remove_question($questionid)
	{
		$affectedRows = 0;
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
		$builder->where('questionid', $questionid);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			$affectedRows++;
		}

		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box');
		$builder->where('questionid', $questionid);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			$affectedRows++;
		}

		$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
		$builder->where('questionid', $questionid);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			$affectedRows++;
		}
		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}


	/**
	 * remove box description
	 * @param  integer $questionbod 
	 * @return boolean                           
	 */
	public function remove_box_description($questionboxdescriptionid)
	{
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
		$builder->where('questionboxdescriptionid', $questionboxdescriptionid);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}


	/**
	 * add box description
	 * @param integer $questionid  
	 * @param integer $boxid       
	 * @param string $description
	 * @return  integer
	 */
	public function add_box_description($questionid, $boxid, $description = '')
	{
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
		$builder->insert([
			'questionid'  => $questionid,
			'boxid'       => $boxid,
			'description' => $description,
		]);

		return $this->db->insertID();
	}
	

	/**
	 * add training result
	 * @param integer $id     
	 * @param array $result 
	 */
	public function add_training_result($id, $result)
	{
		$builder = $this->db->table(get_db_prefix().'hr_p_t_surveyresultsets');
		$builder->insert([
			'date'      => to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true),
			'trainingid'  => $id,
			'staff_id'  => get_staff_user_id1(),
		]);
		$resultsetid = $this->db->insertID();
		if ($resultsetid) {
			if (isset($result['selectable']) && sizeof($result['selectable']) > 0) {
				foreach ($result['selectable'] as $boxid => $question_answers) {
					foreach ($question_answers as $questionid => $answer) {
						$count = count($answer);
						for ($i = 0; $i < $count; $i++) {
							$builder = $this->db->table(get_db_prefix().'hr_p_t_form_results');

							$builder->insert([
								'boxid'            => $boxid,
								'boxdescriptionid' => $answer[$i],
								'rel_id'           => $id,
								'rel_type'         => 'position_training',
								'questionid'       => $questionid,
								'answer'      	   => $answer[$i],
								'resultsetid'      => $resultsetid,
							]);
						}
					}
				}
			}
			unset($result['selectable']);
			if (isset($result['question'])) {
				foreach ($result['question'] as $questionid => $val) {
					$boxid = $this->get_training_question_box_id($questionid);

					$builder = $this->db->table(get_db_prefix().'hr_p_t_form_results');
					$builder->insert([
						'boxid'       => $boxid,
						'rel_id'      => $id,
						'rel_type'    => 'position_training',
						'questionid'  => $questionid,
						'answer'      => $val[0],
						'resultsetid' => $resultsetid,
					]);
				}
			}

			return true;
		}

		return false;
	}



	/**
	 * get training question box id
	 * @param  integer $questionid 
	 * @return integer             
	 */
	private function get_training_question_box_id($questionid)
	{
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box');
		$builder->select('boxid');
		$builder->where('questionid', $questionid);
		$box = $builder->get()->getRow();

		return $box->boxid;
	}



	/**
	 * update answer question
	 * @param  array $data 
	 * @return array       
	 */
	public function update_answer_question($data)
	{
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
		$builder->where('questionboxdescriptionid', $data['questionboxdescriptionid']);
		$affected_rows = $builder->update([
			'correct' => $data['correct'],
		]);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get child training type
	 * @param  integer $id 
	 * @return array     
	 */
	public function get_child_training_type($id){
		$builder = $this->db->table(get_db_prefix().'hr_position_training');
		$builder->where('training_type',$id);
		$builder->orderBy('datecreated', 'desc');
		$rs = $builder->get()->getResultArray();
		return  $rs;
	}


	/**
	 * add job position training process
	 * @param array $data 
	 */
	public function add_job_position_training_process($data){
		if(isset($data['department_id'])){
			unset($data['department_id']);
		}

		if(isset($data['additional_training'])){
			$data_staff_id = $data['staff_id'];
			if(isset($data['staff_id'])){
				$data['staff_id'] = implode(',', $data['staff_id']);
			}

			$data['time_to_start'] = to_sql_date1($data['time_to_start']);
			$data['time_to_end'] = to_sql_date1($data['time_to_end']);
		}

		$data['date_add'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		$data['position_training_id'] = implode(',',$data['position_training_id']);

		if(isset($data['job_position_id'])){
			$data['job_position_id'] = implode(',',$data['job_position_id']);
		}

		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if ($insert_id) {
			if(isset($data['additional_training'])){
				if(isset($data_staff_id) && count($data_staff_id) > 0){

					$mes = 'a_new_training_program_is_assigned_to_you';

					foreach ($data_staff_id as $staff_id) {
					//send notification
						if(is_numeric($staff_id)){
							/*Send notify*/
							$notify_data = ['hr_send_training_staff_id' => $staff_id];
							hr_log_notification($mes, $notify_data, get_staff_user_id1() ,$staff_id);
						}

					}
				}
			}

			return $insert_id;
		}
		return false;
	}


	/**
	 * update job position training process
	 * @param  array $data 
	 * @param  integer $id   
	 * @return integer or boolean       
	 */
	public function update_job_position_training_process($data, $id){
		if(isset($data['department_id'])){
			unset($data['department_id']);
		}

		if(isset($data['additional_training'])){
			if(isset($data['staff_id'])){
				$data_staff_id = $data['staff_id'];
				$data['staff_id'] = implode(',', $data['staff_id']);
			}else{
				$data['staff_id'] = '';
			}

			$data['time_to_start'] = to_sql_date1($data['time_to_start']);
			$data['time_to_end'] = to_sql_date1($data['time_to_end']);

			$data['job_position_id'] = null;

		}else{
			$data['staff_id'] = '';
			$data['time_to_start'] = null;
			$data['time_to_end'] = null;
			$data['additional_training'] = '';

			if(isset($data['job_position_id'])){
				$data['job_position_id'] = implode(',',$data['job_position_id']);
			}else{
				$data['job_position_id'] = null;
			}

			$data['staff_id'] = null;
			$data['time_to_start'] = null;
			$data['time_to_end'] = null;
		}

		$data['date_add'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		$data['position_training_id'] = implode(',',$data['position_training_id']);

		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');
		$builder->where('training_process_id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {

			if(isset($data['additional_training'])){
				if(isset($data_staff_id) && count($data_staff_id) > 0){

					$mes = 'a_new_training_program_is_assigned_to_you';

					foreach ($data_staff_id as $staff_id) {
						$notify_data = ['hr_send_training_staff_id' => $staff_id];
						hr_log_notification($mes, $notify_data, get_staff_user_id1() ,$staff_id);
					}
				}
			}

			return true;
		}
		return false;
	}


	/**
	 * get jobposition by department
	 * @param integer $department_id 
	 * @param  integer $status        
	 * @return string                
	 */
	// public function get_jobposition_by_department($department_id = '', $status)
	public function get_jobposition_by_department($status, $department_id = '')
	{
		$arr_staff_id=[];
		$index_dep = 0;
		if(is_array($department_id)){
			/*get staff in deaprtment start*/
			foreach ($department_id as $key => $value) {
				/*get staff in department*/
				$this->db->select('staffid');
				$this->db->where('departmentid', $value);

				$arr_staff = $this->db->get(get_db_prefix().'staff_departments')->getResultArray();
				if(count($arr_staff) > 0){
					foreach ($arr_staff as $value) {
						if(!in_array($value['staffid'], $arr_staff_id)){
							$arr_staff_id[$index_dep] = $value['staffid'];
							$index_dep++;
						}
					}
				}
			}
			/*get staff in deaprtment end*/
			$options = '';
			if(count($arr_staff_id) == 0){
				return $options;
			}
			/*get position start*/
			$arr_staff_id = implode(",", $arr_staff_id);
			$sql_where = 'SELECT '.get_db_prefix().'hr_job_position.position_id, position_name FROM '.get_db_prefix().'staff left join '.get_db_prefix().'hr_job_position on '.get_db_prefix().'staff.job_position = '.get_db_prefix().'hr_job_position.position_id WHERE '.get_db_prefix().'staff.job_position != "0" AND '.get_db_prefix().'staff.staffid IN ('.$arr_staff_id.')';
			
			
			$arr_job_position = $this->db->query($sql_where)->getResultArray();
			$arr_check_exist=[];
			foreach ($arr_job_position as $k => $note) {
				if(!in_array($note['position_id'], $arr_check_exist)){
					$select = ' selected';
					$options .= '<option value="' . $note['position_id'] . '" '.$select.'>' . $note['position_name'] . '</option>';
					$arr_check_exist[$k] = $note['position_id'];
				}
			}
			/*get position end*/
			return $options;
		}else{
			$arr_job_position = $this->get_job_position();
			$options = '';
			foreach ($arr_job_position as $note) {
				$options .= '<option value="' . $note['position_id'] . '">' . $note['position_name'] . '</option>';
			}
			return $options;
		}
	}


  /**
   * get job position
   * @param  integer $id 
   * @return object or array      
   */
  public function get_job_p($id = false)
  {
  	if (is_numeric($id)) {
  		$builder = $this->db->table(get_db_prefix().'hr_job_p');
  		$builder->where('job_id', $id);

  		return $builder->get()->getRow();
  	}

  	if ($id == false) {
  		return $this->db->query('select * from '.get_db_prefix().'hr_job_p')->getResultArray();
  	}
  }


	/**
	 * add job position
	 * @param array $data 
	 */
	public function add_job_p($data)
	{
		$option = 'off';

		if(isset($data['create_job_position'])){
			$option = $data['create_job_position'];
			unset($data['create_job_position']);
		}

		$builder = $this->db->table(get_db_prefix().'hr_job_p');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if($insert_id){
			if($option == 'on'){
				$data_position['position_name'] = $data['job_name'];
				$data_position['job_position_description'] = $data['description'];
				$data_position['job_p_id'] = $insert_id;
				$this->add_job_position($data_position);
			}
		}

		return $insert_id;
	}


	/**
	 * update job position
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_job_p($data, $id)
	{ 
		$builder = $this->db->table(get_db_prefix().'hr_job_p');
		$builder->where('job_id', $id);
		$affectedRows = $builder->update($data);

		if ($affectedRows > 0) {
			return true;
		}

		return true;
	}


	/**
	 * delete job position
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_job_p($id)
	{

		$builder = $this->db->table(get_db_prefix().'hr_job_p');
		$builder->where('job_id', $id);
		$affectedRows = $builder->delete();

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}


	/**
	 * add job position
	 * @param aray $data 
	 */
	public function add_job_position($data)
	{
		if(isset($data['file'])){
			$files = $data['file'];
			unset($data['file']);
		}

		if(isset($data['description'])){
			$descriptions = $data['description'];
			unset($data['description']);
		}

		if(isset($data['department_id'])){
			$data['department_id'] = implode(',', $data['department_id']);
		}

		$builder = $this->db->table(get_db_prefix().'hr_job_position');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if ($insert_id) {
			if(isset($tags)){
				handle_tags_save($tags, $insert_id, 'job_position');
			}

			/*update next number setting*/
			$this->update_prefix_number(['job_position_number' =>  get_setting('job_position_number')+1]);
		}

		return $insert_id;
	}


	 /**
	 * update job position
	 * @param aray $data 
	 */
	 public function update_job_position($data, $id)
	 {   
	 	$affected_rows = 0;

	 	if(isset($data['file'])){
	 		$files = $data['file'];
	 		unset($data['file']);
	 	}


	 	if(isset($data['tags']) && strlen($data['tags']) > 0){

	 		$this->db->where('rel_id', $id);
	 		$this->db->where('rel_type', 'job_position');
	 		$arr_tag = $this->db->get(get_db_prefix() . 'taggables')->getResultArray();

	 		if(count($arr_tag) > 0){
				//update
	 			$arr_tag_insert =  explode(',', $data['tags']);
	 			/*get order last*/
	 			$total_tag = count($arr_tag);
	 			$tag_order_last = $arr_tag[$total_tag-1]['tag_order']+1;

	 			foreach ($arr_tag_insert as $value) {
	 				$this->db->insert(get_db_prefix() . 'tags', ['name' => $value]);
	 				$insert_tag_id = $this->db->insertID();

	 				if($insert_tag_id){
	 					$this->db->insert(get_db_prefix() . 'taggables', ['rel_id' => $id, 'rel_type'=>'job_position', 'tag_id' => $insert_tag_id, 'tag_order' => $tag_order_last]);
	 					$this->db->insertID();

	 					$tag_order_last++;

	 					$affected_rows++;
	 				}

	 			}

	 		}else{
				//insert
	 			handle_tags_save($data['tags'], $id, 'job_position');
	 			$affected_rows++;

	 		}
	 	}

	 	if (isset($data['tags'])) {
	 		unset($data['tags']);
	 	}


	 	if(isset($data['department_id'])){
	 		$data['department_id'] = implode(',', $data['department_id']);
	 	}else{
	 		$data['department_id'] = null;
	 	}

		$builder = $this->db->table(get_db_prefix().'hr_job_position');
	 	$builder->where('position_id', $id);
	 	$affectedRows = $builder->update($data);
	 	if ($affectedRows > 0) {
	 		$affected_rows++;
	 	}

	 	if($affected_rows > 0){
	 		return true;
	 	}
	 	return false;
	 }


	/**
 * delete job position
 * @param aray $data 
 */
	public function delete_job_position($id){
		
		$affected_rows = 0;

			//delete salary scale
		$builder = $this->db->table(get_db_prefix().'hr_jp_salary_scale');
		$builder->where('job_position_id', $id);
		$affectedRows = $builder->delete();
		if ($affectedRows > 0) {
			$affected_rows++;
		}

			//delete table job position
		$builder = $this->db->table(get_db_prefix().'hr_job_position');
		$builder->where('position_id', $id);
		$affectedRows = $builder->delete();
		if ($affectedRows > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}


	/**
	 * get list job position tags file
	 * @param  [type] $job_position_id 
	 * @return [type]                  
	 */
	public function get_list_job_position_tags_file($job_position_id)
	{
		$data=[];
		$arr_file = $this->get_hrm_attachments_file($job_position_id, 'job_position');

		/* get list tinymce start*/
		$this->db->from(get_db_prefix() . 'taggables');
		$this->db->join(get_db_prefix() . 'tags', get_db_prefix() . 'tags.id = ' . get_db_prefix() . 'taggables.tag_id', 'left');

		$this->db->where(get_db_prefix() . 'taggables.rel_id', $job_position_id);
		$this->db->where(get_db_prefix() . 'taggables.rel_type', 'job_position');
		$this->db->orderBy('tag_order', 'ASC');

		$job_position_tags = $this->db->get()->getResultArray();

		$html_tags='';
		foreach ($job_position_tags as $tag_value) {
			$html_tags .='<li class="tagit-choice ui-widget-content ui-state-default ui-corner-all tagit-choice-editable tag-id-'.$tag_value['id'].' true" value="'.$tag_value['id'].'">
			<span class="tagit-label">'.$tag_value['name'].'</span>
			<a class="tagit-close">
			<span class="text-icon">×</span>
			<span class="ui-icon ui-icon-close"></span>
			</a>
			</li>';
		}

		$htmlfile='';
		//get file attachment html
		if(isset($arr_file)){
			$htmlfile = '<div class="row col-md-12" id="attachment_file">';
			foreach($arr_file as $attachment) {
				$href_url = site_url('modules/hrm/uploads/job_position/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
				if(!empty($attachment['external'])){
					$href_url = $attachment['external_link'];
				}

				$htmlfile .= '<div class="display-block contract-attachment-wrapper">';
				$htmlfile .= '<div class="col-md-10">';
				$htmlfile .= '<div class="col-md-1 mr-5">';
				$htmlfile .= '<a name="preview-btn" onclick="preview_file_job_position(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'._l("preview_file").'">';
				$htmlfile .= '<i class="fa fa-eye"></i>'; 
				$htmlfile .= '</a>';
				$htmlfile .= '</div>';
				$htmlfile .= '<div class=col-md-9>';
				$htmlfile .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
				$htmlfile .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
				$htmlfile .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
				$htmlfile .= '</div>';
				$htmlfile .= '</div>';
				$htmlfile .= '<div class="col-md-2 text-right">';
				if(hr_has_permission('hr_profile_can_delete_job_description')){
					$htmlfile .= '<a href="#" class="text-danger" onclick="delete_job_position_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
				}

				$htmlfile .= '</div>';
				$htmlfile .= '<div class="clearfix"></div><hr/>';
				$htmlfile .= '</div>';
			}

			$htmlfile .= '</div>';
		}

		$data['htmltag']    = $html_tags;  
		$data['htmlfile']   = $htmlfile;  

		return $data;
	}


	/**
	 * get hrm attachments file
	 * @param  [type] $rel_id   
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function get_hrm_attachments_file($rel_id, $rel_type){
		//contract : //rel_id = $id_contract, rel_type = 'hrm_contract'
		
		$builder = $this->db->table(get_db_prefix().'files');
		$builder->orderBy('dateadded', 'desc');
		$builder->where('rel_id', $rel_id);
		$builder->where('rel_type', $rel_type);

		return $builder->get()->getResultArray();

	}

	/**
	 * get department from job p
	 * @param  integer $job_p_id 
	 * @return array           
	 */
	public function get_department_from_job_p($job_p_id)
	{   
		$data=[];
		$index=0;

		$builder = $this->db->table(get_db_prefix().'hr_job_position');
		$builder->where('job_p_id', $job_p_id);
		$job_position =  $builder->get()->getResultArray();
		if(count($job_position) > 0){
			foreach ($job_position as $job_value) {
				if($job_value['department_id'] != null && $job_value['department_id'] != ''){

					$arr = explode(',', $job_value['department_id']);
					foreach ($arr as $arr_value) {
						if(!in_array($arr_value, $data)){
							$data[$index] = $arr_value;
							$index ++;
						}
					}
				}
			}
		}
		return $data;
	}


	/**
	 * check child in job position
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function check_child_in_job_p($id)
	{
		$this->db->where('job_p_id', $id);
		$arr_job_chil = $this->db->get(get_db_prefix() . 'hr_job_position')->getResultArray();

		foreach ($arr_job_chil as $key => $value) {
			if (is_reference_in_table('job_position', get_db_prefix() . 'staff', $value['position_id'])) {
				return true;;
			}
		}
		return false;
	}


/**
 * get array job position
 * @param  integer $id 
 * @return boolean      
 */
public function get_array_job_position($id = false)
{
	if (is_numeric($id)) {
		$this->db->where('job_p_id', $id);
		return $this->db->get(get_db_prefix() . 'hr_job_position')->getResultArray();
	}
	return false;
}
/**
 * get job position tag
 * @param  integer $id 
 */
public function get_job_position_tag($id=''){
	/* get list tinymce start*/
	$this->db->from(get_db_prefix() . 'taggables');
	$this->db->join(get_db_prefix() . 'tags', get_db_prefix() . 'tags.id = ' . get_db_prefix() . 'taggables.tag_id', 'left');
	$this->db->where(get_db_prefix() . 'taggables.rel_id', $id);
	$this->db->where(get_db_prefix() . 'taggables.rel_type', 'job_position');
	$this->db->orderBy('tag_order', 'ASC');
	$job_position_tags = $this->db->get()->getResultArray();
	return $job_position_tags;
}
	/**
	* get array interview process by position id
	* @param  integer $id
	* @return  array
	*/
	public function get_interview_process_byposition($id = false){
		if (is_numeric($id)) {
			$sql_where ='find_in_set("'.$id.'", job_position_id)';
			$this->db->where($sql_where);
			$this->db->orderBy('interview_process_id', 'desc');
			return  $this->db->get(get_db_prefix() . 'jp_interview_process')->getResultArray();
		}

	}
	/**
	* get array training process by position id
	* @param  integer $id
	* @return  array
	*/
	public function get_traing_process_byposition($id = false){
		if (is_numeric($id)) {
			$sql_where ='find_in_set("'.$id.'", job_position_id)';
			$this->db->where($sql_where);
			$this->db->orderBy('training_process_id', 'desc');
			return  $this->db->get(get_db_prefix() . 'hr_jp_interview_training')->getResultArray();
		}
	}
	/**
	 * get job position salary scale
	 * @param  integer $job_position_id 
	 * @return array                  
	 */
	public function get_job_position_salary_scale($job_position_id){
		$data=[];
		$salary_insurance = 0;
		$salary_form = [];        
		$salary_allowance = [];   

		$this->db->where('job_position_id', $job_position_id);
		$arr_salary_sacale = $this->db->get(get_db_prefix() . 'hr_jp_salary_scale')->getResultArray();

		foreach ($arr_salary_sacale as $key => $value) {
			switch ($value['rel_type']) {
				case 'insurance':
					# code...
				$salary_insurance = $value['value'];
				break;
				
				case 'salary':
					# code...
				array_push($salary_form, $arr_salary_sacale[$key]);
				break;
				
				case 'allowance':
					# code...
				array_push($salary_allowance, $arr_salary_sacale[$key]);
				break;
			}

		}
		$data['insurance'] = $salary_insurance;
		$data['salary'] = $salary_form;
		$data['allowance'] = $salary_allowance;

		return $data;
	}
	/**
	 * get hr profile attachments file
	 * @param  integer $rel_id   
	 * @param  integer $rel_type 
	 * @return array           
	 */
	public function get_hr_profile_attachments_file($rel_id, $rel_type){
		$builder = $this->db->table(get_db_prefix().'files');        
		$builder->orderBy('dateadded', 'desc');
		$builder->where('rel_id', $rel_id);
		$builder->where('rel_type', $rel_type);
		return $builder->get()->getResultArray();
	}
	
	/**
	 * get department from position department
	 * @param  array $arr_value 
	 * @param  integer $position  
	 * @return string            
	 */
	public function get_department_from_position_department($arr_value, $position)
	{
		$job_p_id='';

		$job_p=[];
		$index_dep = 0;

		if($position == false){

			foreach ($arr_value as $key => $value) {
				$sql_where = 'find_in_set('.$value.', department_id)';
				$builder = $this->db->table(get_db_prefix().'hr_job_position');
				$builder->where($sql_where);
				$arr_job_position = $builder->get()->getResultArray();

				if(count($arr_job_position) > 0){
					foreach ($arr_job_position as $value) {
						if(!in_array($value['job_p_id'], $job_p)){

							$job_p[$index_dep] = $value['job_p_id'];
							$index_dep++;

						}
					}
				}
			}

			if(count($job_p) > 0){
				$job_p_id .= implode(',', $job_p);
			}

		}else{
			foreach ($arr_value as $key => $value) {

				$builder = $this->db->table(get_db_prefix().'hr_job_position');

				$builder->where('position_id', $value);
				$arr_job_position = $builder->get()->getResultArray();

				if(count($arr_job_position) > 0){
					foreach ($arr_job_position as $value) {
						if(!in_array($value['job_p_id'], $job_p)){

							$job_p[$index_dep] = $value['job_p_id'];
							$index_dep++;

						}
					}
				}
			}
			if(count($job_p) > 0){
				$job_p_id .= implode(',', $job_p);
			}
		}
		return $job_p_id;
	}

/**
 * get position by department
 * @param integer $department_id 
 * @param  integer $status        
 * @return string                
 */
public function get_position_by_department($department_id, $status)
{

	$job_position=[];
	$index_dep = 0;
	$options = '';

	if(is_array($department_id))
	{
		/*get staff in deaprtment start*/
		foreach ($department_id as $key => $value) {
			$sql_where = 'find_in_set('.$value.', department_id)';

			$builder = $this->db->table(get_db_prefix().'hr_job_position');
			$builder->where($sql_where);
			$arr_job_position = $builder->get()->getResultArray();

			if(count($arr_job_position) > 0){
				foreach ($arr_job_position as $value) {
					if(!in_array($value['position_id'], $job_position)){
						$options .= '<option value="' . $value['position_id'] . '">' . $value['position_name'] . '</option>';

						$job_position[$index_dep] = $value['position_id'];
						$index_dep++;
					}
				}
			}
		}
		return $options;
	}else{

		$arr_job_position = $this->get_job_position();
		$options = '';
		foreach ($arr_job_position as $note) {

			$options .= '<option value="' . $note['position_id'] . '">' . $note['position_name'] . '</option>';
		}
		return $options;
	}
}


	/**
	 * job position add update salary scale
	 * @param  array $data 
	 * @return boolean       
	 */
	public function job_position_add_update_salary_scale($data){
		if(isset($data['job_position_id'])){
			$job_position_id = $data['job_position_id'];
			unset($data['job_position_id']);
		}
		$this->db->where('job_position_id', $job_position_id);
		$this->db->delete(get_db_prefix().'hr_jp_salary_scale');

		$this->db->insert(get_db_prefix().'hr_jp_salary_scale',[
			'job_position_id' => $job_position_id,
			'rel_type' => 'insurance',
			'value' => hr_profile_reformat_currency($data['premium_rates']),
		]);
		foreach($data['salary_form'] as $salary_key => $salary_value){

			$this->db->insert(get_db_prefix().'hr_jp_salary_scale', [
				'job_position_id' => $job_position_id,
				'rel_type' => 'salary',
				'rel_id' => $salary_value,
				'value' =>  hr_profile_reformat_currency($data['contract_expense'][$salary_key]),
			]);
		}
		foreach($data['allowance_type'] as $allowance_key => $allowance_value){

			$this->db->insert(get_db_prefix().'hr_jp_salary_scale', [
				'job_position_id' => $job_position_id,
				'rel_type' => 'allowance',
				'rel_id' => $allowance_value,
				'value' =>  hr_profile_reformat_currency($data['allowance_expense'][$allowance_key]),
			]);
		}
		return true;
	}


	/**
	 * get staff
	 * @param  integer $id    
	 * @param  array  $where 
	 * @return array        
	 */
	public function get_staff($id = '', $where = [])
	{
		$select_str = '*,CONCAT(first_name," ",last_name) as full_name';
		
		$builder = $this->db->table(get_db_prefix().'users');
		$builder->where($where);
		$builder->where('deleted', 0);

		if (is_numeric($id)) {
			$builder->where('id', $id);
			$staff = $builder->get()->getRow();


			return $staff;
		}
		$builder->orderBy('first_name', 'desc');

		return $builder->get()->getResultArray();
	}


	/**
	 * add manage info reception
	 * @param array $data 
	 */
	public function add_manage_info_reception($data)
	{	
		$builder = $this->db->table(get_db_prefix().'group_checklist');
		$builder->emptyTable();

		$builder = $this->db->table(get_db_prefix().'checklist');
		$builder->emptyTable();       

		foreach ($data['title_name'] as $key => $menu) {
			if($menu != ''){
				$data_s['group_name'] = $menu;

				$builder = $this->db->table(get_db_prefix().'group_checklist');
				$builder->insert($data_s);
				$insert_id = $this->db->insertID();

				if(isset($data['sub_title_name'][$key])){
					foreach ($data['sub_title_name'][$key] as $sub_menu) {
						if($sub_menu != ''){
							$data_ss['name'] = $sub_menu;
							$data_ss['group_id'] = $insert_id;

							$builder = $this->db->table(get_db_prefix().'checklist');
							$builder->insert($data_ss);
						}                      
					}
				}

			}         
		}
	}


	/**
	 * add setting training
	 */
	public function add_setting_training($data)
	{
		if(isset($data['training_type'])){
			$builder = $this->db->table(get_db_prefix().'setting_training');
			$builder->emptyTable();  
			$builder->insert($data);  
		}   
	}


	/**
	 * checklist by group
	 * @param  integer $group_id 
	 * @return array           
	 */
	public function checklist_by_group($group_id = ''){
		$builder = $this->db->table(get_db_prefix().'checklist');
		$builder->where('group_id', $group_id);
		return $builder->get()->getResultArray();
	}


	/**
	 * count max checklist
	 * @return [type] 
	 */
	public function count_max_checklist()
	{
		$sql_where = "SELECT count(id) as total_sub_item  FROM ".get_db_prefix()."checklist
		group by group_id
		order by total_sub_item desc limit 1";
		$max_sub_item = $this->db->query($sql_where)->getRow();

		if($max_sub_item){
			return (float)$max_sub_item->total_sub_item;
		}

		return 1;
	}


	/**
	 * get staff info id
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function get_staff_info_id($staffid){
		$builder = $this->db->table(get_db_prefix().'users');
		$builder->where('id', $staffid);
		return $builder->get()->getRow();
	}

	/**
	 * add_manage_info_reception_for_staff
	 * @param integer $id_staff 
	 * @param integer $data     
	 */
	public function add_manage_info_reception_for_staff($id_staff, $data)
	{
		if(isset($data['sub_title_name'])&&isset($data['title_name'])){
			foreach ($data['title_name'] as $key => $menu) {
				if($menu != ''){
					$builder = $this->db->table(get_db_prefix().'hr_group_checklist_allocation');
					$data_s['group_name'] = $menu;
					$data_s['staffid'] = $id_staff;
					$builder->insert($data_s);
					$insert_id = $this->db->insertID();

					$builder = $this->db->table(get_db_prefix().'hr_checklist_allocation');

					if(isset($data['sub_title_name'][$key])){
						foreach ($data['sub_title_name'][$key] as $sub_menu) {
							if($sub_menu != ''){
								$data_ss['name'] = $sub_menu;
								$data_ss['group_id'] = $insert_id;
								$builder->insert($data_ss);
							}                      
						}
					}

				}         
			}
		}            
	} 


	/**
	 * add asset staff
	 * @param integer $id   
	 * @param array $data 
	 */
	public function add_asset_staff($id,$data){  
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');
		foreach ($data as $key => $value) {
			$builder->insert([
				'staff_id'      => $id,
				'asset_name' => $value['name'],
				'assets_amount' => '1']);
		}
	}


	/**
	 * get jp interview training
	 * @param  integer $position_id   
	 * @param  integer $training_type 
	 * @return object                
	 */
	public function get_jp_interview_training($position_id, $training_type = ''){
		if($training_type==''){
			$type_training = $this->getTraining_Setting();    
			if($type_training){
				return $this->db->query('select * from '.get_db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) and training_type = \''.$type_training->training_type.'\' ORDER BY date_add desc limit 1')->getRow();
			}
			else{
				return $this->db->query('select * from '.get_db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) ORDER BY date_add desc limit 1')->getRow();
			}
		}
		else{
			return $this->db->query('select * from '.get_db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) and training_type = \''.$training_type.'\' ORDER BY date_add desc limit 1')->getRow();
		}
	}


	/**
	 * add training staff
	 * @param integer $data_training 
	 * @param integer $id_staff      
	 */
	public function add_training_staff($data_training,$id_staff){
		$data['staffid'] = $id_staff;
		$explode = explode(',', $data_training->position_training_id);
		$data['training_process_id'] = implode(',',array_unique($explode));
		$data['training_type'] = $data_training->training_type;
		$data['training_name'] = $data_training->training_name;
		$data['jp_interview_training_id'] = $data_training->training_process_id;

		$builder = $this->db->table(get_db_prefix().'hr_training_allocation');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}


/**
 * add transfer records reception
 * @param array $data    
 * @param integer $staffid 
 */
public function add_transfer_records_reception($data,$staffid){
	$list_meta = $this->get_list_record_meta();

	$builder = $this->db->table(get_db_prefix().'hr_transfer_records_reception');
	foreach ($data as $key => $value) {
		$name='';
		foreach ($list_meta as $list_item) {
			if($list_item['meta']==$value){
				$name=$list_item['name'];
			}
		}
		$builder->insert([
			'name' => $name,
			'meta' => $value,
			'staffid' => $staffid
		]);
	}  
}
/**
 * getPercent
 * @param  integer $total  
 * @param  integer $effect 
 * @return foat         
 */
public function getPercent($total,$effect){
	if($total == 0){
		return 0;
	}
	return number_format(($effect * 100 / $total), 0);
}


	/**
	 * get group checklist allocation by staff id
	 * @param  integer $staffid 
	 * @return integer          
	 */
	public function get_group_checklist_allocation_by_staff_id($staffid){
		$builder = $this->db->table(get_db_prefix().'hr_group_checklist_allocation');
		$builder->where('staffid', $staffid);
		return $builder->get()->getResultArray();
	}


	/**
	 * get checklist allocation by group id
	 * @param  integer $id_group 
	 * @return array           
	 */
	public function get_checklist_allocation_by_group_id($id_group){
		$builder = $this->db->table(get_db_prefix().'hr_checklist_allocation');

		$builder->where('group_id', $id_group);
		return $builder->get()->getResultArray();
	}


	/**
	 * get resultset training
	 * @param  integer $id 
	 * @return integer     
	 */
	public function get_resultset_training($id, $training_process_id){
		return $this->db->query('select * from '.get_db_prefix().'hr_p_t_surveyresultsets where staff_id = \''.$id.'\' AND trainingid IN ('.$training_process_id.') order by date desc')->getResultArray();
	}


	/**
	 * get allocation asset
	 * @param  integer $staff_id 
	 * @return array           
	 */
	public function get_allocation_asset($staff_id){
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');

		$builder->where('staff_id',$staff_id);
		return $builder->get()->getResultArray();
	}


/**
 * get result training staff
 * @param  integer $list_resultsetid 
 * @return array                   
 */
public function get_result_training_staff($list_resultsetid){
	return $this->db->query('select * from '.get_db_prefix().'hr_p_t_form_results where resultsetid in ('.$list_resultsetid.')')->getResultArray();
}

	/**
	 * get id result correct
	 * @param  integer $id_question 
	 * @return object              
	 */
	public function get_id_result_correct($question_id){
		$boxdescriptionids =[];
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');

		$builder->where('questionid', $question_id);
		$builder->where('correct', 0);
		$result = $builder->get()->getResultArray();

		foreach ($result as $value) {
			array_push($boxdescriptionids, $value['questionboxdescriptionid']);
		}
		return $boxdescriptionids;
	}


	/**
	 * get point training question form
	 * @param  [type] $id_question 
	 * @return [type]              
	 */
	public function get_point_training_question_form($id_question){
		$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');

		$builder->where('questionid',$id_question);
		return $builder->get()->getRow();
	}


	/**
	 * delete manage info reception
	 * @param  integer $id 
	 */
	public function delete_manage_info_reception($id){
		$builder = $this->db->table(get_db_prefix().'hr_group_checklist_allocation');
		$builder->where('staffid', $id);
		$list = $builder->get()->getResultArray();

		$builder = $this->db->table(get_db_prefix().'hr_group_checklist_allocation');
		$builder->where('staffid', $id);
		$builder->delete();
		foreach ($list as $sub_menu) {
			$builder = $this->db->table(get_db_prefix().'hr_checklist_allocation');
			$builder->where('group_id', $sub_menu['id']);
			$builder->delete();
		}                         
	}


	/**
	 * delete setting training
	 * @param  integer $id 
	 */
	public function delete_setting_training($id){
		$builder = $this->db->table(get_db_prefix().'hr_training_allocation');
		$builder->where('staffid', $id);
		
		$affected_rows = $builder->delete();

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete setting asset allocation
	 * @param  integer $id 
	 * @return integer     
	 */
	public function delete_setting_asset_allocation($id){
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');
		$builder->where('staff_id', $id);
		$affected_rows = $builder->delete();

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete reception
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_reception($id){
		$builder = $this->db->table(get_db_prefix().'hr_rec_transfer_records');
		$builder->where('staffid', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			$builder = $this->db->table(get_db_prefix().'hr_training_allocation');
			$builder->where('staffid', $id);
			$builder->delete();

			$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');
			$builder->where('staff_id', $id);
			$builder->delete();


			$builder = $this->db->table(get_db_prefix().'hr_group_checklist_allocation');
			$builder->where('staffid', $id);
			$data_checklist = $builder->get()->getResultArray();
			if(isset($data_checklist)){
				if($data_checklist){
					$builder = $this->db->table(get_db_prefix().'hr_group_checklist_allocation');
					$builder->where('staffid', $id);
					$builder->delete();
					foreach ($data_checklist as $key => $checklist) {

						$builder = $this->db->table(get_db_prefix().'hr_checklist_allocation');
						$builder->where('group_id', $checklist['id']);
						$builder->delete();                                         
					}                    
				}
			}
			return true;
		}
		return false;
	}


	/**
	 * get department by staffid
	 * @param  integer $id_staff 
	 * @return object           
	 */
	public function get_department_by_staffid($id_staff){
		$this->db->where('staffid',$id_staff);
		$departments = $this->db->get(get_db_prefix().'staff_departments')->getResultArray();
		$w = '0';
		if(isset($departments[0]['departmentid'])){
			$w = $departments[0]['departmentid'];
		}
		return $this->db->query('select * from '.get_db_prefix().'departments where departmentid = '.$w)->getRow();
	}


/**
 * get transfer records reception staff
 * @param  integer $id 
 * @return integer     
 */
public function get_transfer_records_reception_staff($id){
	$builder = $this->db->table(get_db_prefix().'hr_transfer_records_reception');
	$builder->where('staffid',$id);
	return $builder->get()->getResultArray();
}
/**
 * update checklist
 * @param  array $data 
 * @return boolean       
 */
public function update_checklist($data){ 
	$builder = $this->db->table(get_db_prefix().'hr_checklist_allocation');
	$builder->where('id', $data['checklist_id']);
	$affected_rows = $builder->update(['status' => $data['status_checklist']]);
	if ($affected_rows > 0) {
		return true;
	}
	return false;
}
/**
 * delete tag item
 * @param  array $data 
 * @return boolean       
 */
public function delete_tag_item($tag_id){
	$count_af = 0;
	$this->db->where(get_db_prefix() . 'taggables.tag_id', $tag_id);
	$this->db->delete(get_db_prefix() . 'taggables');
	if ($this->db->affected_rows() > 0) {
		$count_af++;
	}
	$this->db->where(get_db_prefix() . 'tags.id', $tag_id);
	$this->db->delete(get_db_prefix() . 'tags');
	if ($this->db->affected_rows() > 0) {
		$count_af++;
	}
	return $count_af > 0 ?  true :  false;
}


	/**
	 * add new asset staff
	 * @param integer $id   
	 * @param array $data 
	 */
	public function add_new_asset_staff($id,$data)
	{  
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');

		foreach ($data as $key => $value) {
			if($value != ''){
				$builder->insert([
					'staff_id'      => $id,
					'asset_name' => $value,
					'assets_amount' => '1',
				]);
			}
		}

	}


	/**
	 * update asset staff
	 * @param  array $data 
	 * @return boolean       
	 */
	public function update_asset_staff($data){ 
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');

		$builder->where('allocation_id', $data['allocation_id']);
		$affected_rows = $builder->update(['status_allocation' => $data['status_allocation']]);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete allocation asset
	 * @param  integer $allocation_id 
	 * @return boolean                
	 */
	public function delete_allocation_asset($allocation_id){
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');

		$builder->where('allocation_id',$allocation_id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get training allocation staff
	 * @param  integer $id 
	 * @return object     
	 */
	public function get_training_allocation_staff($id){

		$builder = $this->db->table(get_db_prefix().'hr_training_allocation');
		$builder->where('staffid',$id);
		return $builder->get()->getRow();
	}



 /**
	 * @param  integer ID (option)
	 * @param  boolean (optional)
	 * @return mixed
	 * Get departments where staff belongs
	 * If $onlyids passed return only departmentsID (simple array) if not returns array of all departments
	 */
 public function get_staff_departments($userid = false, $onlyids = false)
 {
 	$builder = $this->db->table(get_db_prefix().'team');
 	if ($userid == false) {
 		$userid = get_staff_user_id1();
 	}
 	
 	$builder->where('(find_in_set(' . $userid . ', ' . get_db_prefix() . 'team.members))');
 	$departments = $builder->get()->getResultArray();
 	if ($onlyids == true) {
 		$departmentsid = [];
 		foreach ($departments as $department) {
 			array_push($departmentsid, $department['id']);
 		}
 		return $departmentsid;
 	}
 	return $departments;
 }
  /**
	 * Get staff permissions
	 * @param  mixed $id staff id
	 * @return array
	 */
  public function get_staff_permissions($id)
  {
		// Fix for version 2.3.1 tables upgrade
  	if (defined('DOING_DATABASE_UPGRADE')) {
  		return [];
  	}

  	$permissions = $this->app_object_cache->get('staff-' . $id . '-permissions');

  	if (!$permissions && !is_array($permissions)) {
  		$this->db->where('staff_id', $id);
  		$permissions = $this->db->get('staff_permissions')->getResultArray();

  		$this->app_object_cache->add('staff-' . $id . '-permissions', $permissions);
  	}

  	return $permissions;
  }

  public function get_job_position_arrayid()
  {
  	$position = $this->db->query('select * from '.get_db_prefix().'hr_job_position')->getResultArray();
  	$position_arrray = [];
  	foreach ($position as $value) {
  		array_push($position_arrray, $value['position_id']);
  	}
  	return $position_arrray;
  }


	/**
	 * get workplace array id
	 * @return [type] 
	 */
	public function get_workplace_array_id()
	{
		$workplace = $this->db->query('select * from '.get_db_prefix().'hr_workplace')->getResultArray();
		$workpalce_array =[];
		foreach ($workplace as $value) {
			array_push($workpalce_array, $value['id']);
		}
		return $workpalce_array;
	}

	
	/**
	 * get workplace
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_workplace($id = false)
	{
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_workplace');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}
		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'hr_workplace');

			return  $builder->get()->getResultArray();
		}

	}


	/**
	 * add workplace
	 * @param [type] $data 
	 */
	public function add_workplace($data){
		$builder = $this->db->table(get_db_prefix().'hr_workplace');

		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if ($insert_id) {
			return $insert_id;
		}
		return false;
	}


	/**
	 * update workplace
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_workplace($data, $id)
	{   
		$builder = $this->db->table(get_db_prefix().'hr_workplace');

		$builder->where('id', $id);
		$affectedRows = $builder->update($data);

		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete workplace
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_workplace($id){
		$builder = $this->db->table(get_db_prefix().'hr_workplace');

		$builder->where('id', $id);
		$affectedRows = $builder->delete();

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}


 /**
	 * format date
	 * @param  date $date     
	 * @return date           
	 */
 public function format_date($date){
 	if(!$this->check_format_date_ymd($date)){
 		$date = to_sql_date1($date);
 	}
 	return $date;
 }            

	/**
	 * format date time
	 * @param  date $date     
	 * @return date           
	 */
	public function format_date_time($date){
		if(!$this->check_format_date($date)){
			$date = to_sql_date1($date, true);
		}
		return $date;
	}
	 /**
	 * check format date ymd
	 * @param  date $date 
	 * @return boolean       
	 */
	 public function check_format_date_ymd($date) {
	 	if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
	 		return true;
	 	} else {
	 		return false;
	 	}
	 }
	/**
	 * check format date
	 * @param  date $date 
	 * @return boolean 
	 */
	public function check_format_date($date){
		if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])\s(0|[0-1][0-9]|2[0-4]):?((0|[0-5][0-9]):?(0|[0-5][0-9])|6000|60:00)$/",$date)) {
			return true;
		} else {
			return false;
		}
	}


	/**
	* @param  integer (optional)
	* @return object
	* Get single goal
	*/
	public function add_staff($data)
	{
		$affectedRows = 0;

		$Users_model = model("Models\Users_model");
		$Social_links_model = model("Models\Social_links_model");
		$Email_templates_model = model("Models\Email_templates_model");

		$password = isset($data["password"]) ? $data["password"] : password_hash($password, PASSWORD_DEFAULT);
		$job_title = hr_profile_get_job_position_name($data['job_position']);

		$user_data = array(
			"email" => $data['email'] ? $data['email'] : null,
			"first_name" => $data['first_name'] ? $data['first_name'] : '',
			"last_name" => $data['last_name'] ? $data['last_name'] : '',
			"is_admin" => 0,
			"address" => $data['address'] ? $data['address'] : null,
			"phone" => $data['phone'] ? $data['phone'] : null,
			"gender" => $data['gender'] ? $data['gender'] : null,
			"job_title" => $job_title,
			"phone" => $data['phone'] ? $data['phone'] : null,
			"gender" => $data['gender'] ? $data['gender'] : null,
			"user_type" => "staff",
			"created_at" => get_current_utc_time(),
			"staff_identifi" => $data['staff_identifi'] ? $data['staff_identifi'] : null,
			"team_manage" => $data['team_manage'] ? $data['team_manage'] : null,
			"workplace" => $data['workplace'] ? $data['workplace'] : null,
			"status_work" => $data['status_work'] ? $data['status_work'] : null,
			"job_position" => $data['job_position'] ? $data['job_position'] : null,
			"literacy" => $data['literacy'] ? $data['literacy'] : null,
			"marital_status" => $data['marital_status'] ? $data['marital_status'] : null,
			"account_number" => $data['account_number'] ? $data['account_number'] : null,
			"name_account" => $data['name_account'] ? $data['name_account'] : null,
			"issue_bank" => $data['issue_bank'] ? $data['issue_bank'] : null,
			"Personal_tax_code" => $data['Personal_tax_code'] ? $data['Personal_tax_code'] : null,
			"nation" => $data['nation'] ? $data['nation'] : null,
			"religion" => $data['religion'] ? $data['religion'] : null,
			"identification" => $data['identification'] ? $data['identification'] : null,
			"days_for_identity" => $data['days_for_identity'] ? to_sql_date1($data['days_for_identity']) : null,
			"home_town" => $data['home_town'] ? $data['home_town'] : null,
			"resident" => $data['resident'] ? $data['resident'] : null,
			"address" => $data['address'] ? $data['address'] : null,
			"orther_infor" => $data['orther_infor'] ? $data['orther_infor'] : null,
			"hourly_rate" => $data['hourly_rate'] ? $data['hourly_rate'] : '0.00',
			"dob" => to_sql_date1($data['dob']),
			"birthplace" => $data['birthplace'] ? $data['birthplace'] : null,
			"place_of_issue" => $data['place_of_issue'] ? $data['place_of_issue'] : null,
			"skype" => $data['skype'] ? $data['skype'] : null,
		);

		if ($password) {
			$user_data["password"] = password_hash($password, PASSWORD_DEFAULT);
		}

        		//make role id or admin permission 
		$role = $data['role'];
		$role_id = $role;

		$ci = new Security_Controller(false);
		if ($ci->login_user->is_admin && $role === "admin") {
			$user_data["is_admin"] = 1;
			$user_data["role_id"] = 0;
		} else {
			$user_data["is_admin"] = 0;
			$user_data["role_id"] = $role_id;
		}

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

			if(!isset($data['import'])){
				/*save social link*/
				$id = 0;
				$has_social_links = $Social_links_model->get_one($user_id);
				if (isset($has_social_links->id)) {
					$id = $has_social_links->id;
				}

				$social_link_data = array(
					"facebook" => $data['facebook'],
					"twitter" => $data['twitter'],
					"linkedin" => $data['linkedin'],
					"digg" => $data['digg'],
					"youtube" => $data['youtube'],
					"pinterest" => $data['pinterest'],
					"instagram" => $data['instagram'],
					"github" => $data['github'],
					"tumblr" => $data['tumblr'],
					"vine" => $data['vine'],
					"whatsapp" => $data['whatsapp'],
					"user_id" => $user_id,
					"id" => $id ? $id : $user_id
				);

				$social_link_data = clean_data($social_link_data);
				if($Social_links_model->ci_save($social_link_data, $id)){
					$affectedRows++;
				}
			}

			/*save departments*/
			if(isset($data['departments']) && null !== $data['departments']){
				$departments = $data['departments'] ? $data['departments'] : null;
				if($departments != null && count($departments) > 0){
					if($this->add_staff_into_department($departments, $user_id)){
						$affectedRows++;
					}
				}
			}

            		//send login details to user
			if (isset($data['email_login_details']) && null !== $data['email_login_details']) {

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
	 * update staff
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_staff($data, $id)
	{
		$affectedRows = 0;

		$Users_model = model("Models\Users_model");
		$Social_links_model = model("Models\Social_links_model");
		$Email_templates_model = model("Models\Email_templates_model");

		$job_title = hr_profile_get_job_position_name($data['job_position']);

		$user_data = array(
			"email" => $data['email'] ? $data['email'] : null,
			"first_name" => $data['first_name'] ? $data['first_name'] : '',
			"last_name" => $data['last_name'] ? $data['last_name'] : '',
			"is_admin" => 0,
			"address" => $data['address'] ? $data['address'] : null,
			"phone" => $data['phone'] ? $data['phone'] : null,
			"gender" => $data['gender'] ? $data['gender'] : null,
			"job_title" => $job_title,
			"phone" => $data['phone'] ? $data['phone'] : null,
			"gender" => $data['gender'] ? $data['gender'] : null,
			"user_type" => "staff",
			"created_at" => get_current_utc_time(),
			"staff_identifi" => $data['staff_identifi'] ? $data['staff_identifi'] : null,
			"team_manage" => $data['team_manage'] ? $data['team_manage'] : null,
			"workplace" => $data['workplace'] ? $data['workplace'] : null,
			"status_work" => $data['status_work'] ? $data['status_work'] : null,
			"job_position" => $data['job_position'] ? $data['job_position'] : null,
			"literacy" => $data['literacy'] ? $data['literacy'] : null,
			"marital_status" => $data['marital_status'] ? $data['marital_status'] : null,
			"account_number" => $data['account_number'] ? $data['account_number'] : null,
			"name_account" => $data['name_account'] ? $data['name_account'] : null,
			"issue_bank" => $data['issue_bank'] ? $data['issue_bank'] : null,
			"Personal_tax_code" => $data['Personal_tax_code'] ? $data['Personal_tax_code'] : null,
			"nation" => $data['nation'] ? $data['nation'] : null,
			"religion" => $data['religion'] ? $data['religion'] : null,
			"identification" => $data['identification'] ? $data['identification'] : null,
			"days_for_identity" => $data['days_for_identity'] != '' ? to_sql_date1($data['days_for_identity']) : null,
			"home_town" => $data['home_town'] ? $data['home_town'] : null,
			"resident" => $data['resident'] ? $data['resident'] : null,
			"address" => $data['address'] ? $data['address'] : null,
			"orther_infor" => $data['orther_infor'] ? $data['orther_infor'] : null,
			"hourly_rate" => $data['hourly_rate'] ? $data['hourly_rate'] : '0.00',
			"dob" => $data['dob'] != '' ? to_sql_date1($data['dob']) : null,
			"birthplace" => $data['birthplace'] ? $data['birthplace'] : null,
			"place_of_issue" => $data['place_of_issue'] ? $data['place_of_issue'] : null,
			"skype" => $data['skype'] ? $data['skype'] : null,
		);

		if ($data["password"] && strlen($data["password"]) > 0) {
			$user_data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
		}
		if(isset($data['role'])){

        		//make role id or admin permission 
			$role = $data['role'];
			$role_id = $role;

			$ci = new Security_Controller(false);
			if ($ci->login_user->is_admin && $role === "admin") {
				$user_data["is_admin"] = 1;
				$user_data["role_id"] = 0;
			} else {
				$user_data["is_admin"] = 0;
				$user_data["role_id"] = $role_id;
			}
		}else{
			$options = array(
				"user_type" => "staff",
				"id" => $id,
			);
			$user = $Users_model->get_details($options)->getRow();

			if($user->is_admin){
				$user_data["is_admin"] = 1;
				$user_data["role_id"] = 0;
			}else{
				$user_data["is_admin"] = 0;
				$user_data["role_id"] = 0;
			}
		}

        //update a new team member
		$user_id = $Users_model->ci_save($user_data, $id);

		if ($id) {
			$affectedRows++;

			if(!isset($data['import'])){
				/*save social link*/
				$has_social_links = $Social_links_model->get_one($user_id);
				if (isset($has_social_links->id)) {
					$id = $has_social_links->id;
				}

				$social_link_data = array(
					"facebook" => $data['facebook'],
					"twitter" => $data['twitter'],
					"linkedin" => $data['linkedin'],
					"digg" => $data['digg'],
					"youtube" => $data['youtube'],
					"pinterest" => $data['pinterest'],
					"instagram" => $data['instagram'],
					"github" => $data['github'],
					"tumblr" => $data['tumblr'],
					"vine" => $data['vine'],
					"whatsapp" => $data['whatsapp'],
					"user_id" => $user_id,
					"id" => $id ? $id : $user_id,
				);

				$social_link_data = clean_data($social_link_data);
				if($Social_links_model->ci_save($social_link_data, $id)){
					$affectedRows++;
				}
			}

			/*update departments*/
			if(isset($data['departments']) && null !== $data['departments']){
				$departments = $data['departments'] ? $data['departments'] : null;
				if($departments != null && count($departments) > 0){
					if($this->add_staff_into_department($departments, $id)){
						$affectedRows++;
					}
				}
			}else{
				if($this->add_staff_into_department([], $id)){
					$affectedRows++;
				}
			}
		}

		if($affectedRows > 0){
			return $id;
		}
		return false;
	}


	/**
	 * get department name
	 * @param  integer $staffid 
	 */
	public function getdepartment_name($staffid){
		$department = new \stdClass;
		$list_department_name = '';

		$builder = $this->db->table(get_db_prefix().'team');
		$builder->where('find_in_set(' . $staffid . ', ' . get_db_prefix() . 'team.members)');
		$departments = $builder->get()->getResultArray();
		foreach ($departments as $key => $value) {
			if(strlen($list_department_name) > 0){
				$list_department_name .= ', '.$value['title'];
			}else{
				$list_department_name .= $value['title'];
			}
		}
		$department->name = $list_department_name;
		return $department;
	}
	/**
	 * get child node staff chart
	 * @param  integer $id      
	 * @param  integer $arr_dep 
	 * @return array          
	 */
	private function get_child_node_staff_chart($id, $arr_dep){
		$dep_tree = array();
		foreach ($arr_dep as $dep) {
			if($dep['pid']==$id){ 
				$dpm = $this->getdepartment_name($dep['id']);  
				$node = array();             
				$node['name'] = $dep['name'];
				$node['team_manage'] = $dep['pid'];
				$node['job_position_name'] = '';
				
				if($dep['job_position_name'] != null && $dep['job_position_name'] != 'undefined'){
					$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';

					$node['job_position_name'] = $dep['job_position_name'];
				}
				if($dep['rname'] != null){
					$node['title'] = $dep['rname'];
					$node['dp_user_icon'] = '"fa fa-map-pin menu-icon"';
				}else{
					$node['title'] = '';
				}
				if($dpm->name != null){
					$node['departmentname'] = $dpm->name;
					$node['dp_icon'] = '"fa fa-sitemap"';
				}else{
					$node['departmentname'] = ' ';
				}
				$node['image'] = get_staff_image($dep['id'], false);
				
				$node['children'] = $this->get_child_node_staff_chart($dep['id'], $arr_dep);
				if(count($node['children']) == 0){
					unset($node['children']);
				}
				$dep_tree[] = $node;
			} 
		} 
		return $dep_tree;
	}

	
	/**
	 * get hr profile attachments
	 * @param  integer $staffid 
	 * @return array          
	 */
	public function get_hr_profile_attachments($staffid){
		$this->db->orderBy('dateadded', 'desc');
		$this->db->where('rel_id', $staffid);
		$this->db->where('rel_type', 'hr_staff_file');

		return $this->db->get(get_db_prefix() . 'files')->getResultArray();

	}
	
	/**
	 * get records received
	 * @param  integer $id
	 * @return object     
	*/
	public function get_records_received($id)
	{
		return $this->db->query('select '.get_db_prefix().'staff.records_received from '.get_db_prefix().'staff where staffid = '.$id)->getRow();
	}




	/**
	 * get hr profile profile file
	 * @param  integer $staffid 
	 * @return array          
	 */
	public function get_hr_profile_profile_file($staffid){
		$builder = $this->db->table(get_db_prefix().'files');
		$builder->orderBy('dateadded', 'desc');
		$builder->where('rel_id', $staffid);
		$builder->where('rel_type', 'staff_profile_images');

		return $builder->get()->getResultArray();

	}


	/**
	 * get duration
	 * @return array 
	 */
	public function get_duration(){
		return $this->db->query('SELECT duration, unit FROM '.get_db_prefix().'hr_staff_contract_type group by duration, unit')->getResultArray();
	}


	/**
	 * add education
	 * @param array $data 
	 */
	public function add_education($data){
		$data['date_create'] = to_sql_date1(get_my_local_time("Y-m-d"), true);
		$builder = $this->db->table(get_db_prefix().'hr_education');
		$insert_id = $builder->insert($data);
		if ($insert_id) {
			return $insert_id;
		}
		return false;

	}


	/**
	 * update education
	 * @param array $data 
	 */
	public function update_education($data)
	{   
		$builder = $this->db->table(get_db_prefix().'hr_education');
		$builder->where('id', $data['id']);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete education
	 * @param integer $id 
	 */
	public function delete_education($id){
		$builder = $this->db->table(get_db_prefix().'hr_education');
		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


/**
 * member get evaluate form
 * @param  integer $staffid 
 * @return array          
 */
public function member_get_evaluate_form($staffid){
	$arr_evaluate_form = $this->get_evaluate_form_status();
	$sql = "SELECT staffid, staff_identifi, firstname FROM ".get_db_prefix().'staff WHERE staffid ='.$staffid;
	$arr_staff = $this->db->query($sql)->getResultArray();
	$data_object =[];

	foreach ($arr_evaluate_form as $evaluate_value) {
		$data =[];
		if(strlen(json_encode($arr_staff)) != 2){
			$evalute_staff = $this->get_dataobject_result_evaluate($evaluate_value['id'], $arr_staff);
			if(count($evalute_staff[0]) != 0){
				$data['id'] = $evaluate_value['id'];
				$data['hr_code'] = $arr_staff[0]['staff_identifi'];
				$data['eval_form_name'] = $this->get_evaluation_form($evaluate_value['evaluate_form'])->eval_form_name;
				$start_month = $this->get_evaluation_form($evaluate_value['evaluate_form'])->start_month;
				$end_month = $this->get_evaluation_form($evaluate_value['evaluate_form'])->end_month;
				$data['period_eval'] =  date("m/Y", strtotime($evaluate_value['start_month'])).' - '. date("m/Y", strtotime($evaluate_value['end_month']));
				$data['total_kpi'] =  array_reverse($evalute_staff[0])[0];

			}
		}
		if(count($data) != 0){
			array_push($data_object, $data);
		}
	}
	return $data_object;
}
/**
 * get evaluate form status
 * @return array 
 */
public function get_evaluate_form_status(){
	$this->db->where('status', '1');
	return  $this->db->get(get_db_prefix() . 'evaluate_form')->getResultArray();

}
/**
 * get dataobject result evaluate
 * @param  integer  $id       
 * @param  boolean $arrstaff 
 * @return integer            
 */
public function get_dataobject_result_evaluate($id, $arrstaff = false){
	$evaluation_form = $this->get_evaluate_form($id);
	$emp_marks = json_decode($evaluation_form->emp_marks);
	if(isset($evaluation_form->percent)){
		$percent = json_decode($evaluation_form->percent);
	}else{
		$percent = (float)0;
	}

	$evaluation_form_detail = $this->get_evaluation_form_detail($evaluation_form->evaluate_form);
	$evaluate_result = $this->get_assessor_from($id);

	if($arrstaff != false){
		$arr_staff = $arrstaff;
	}else{
		$sql = "SELECT staffid, staff_identifi, firstname FROM ".get_db_prefix().'staff WHERE 1 = 1';           

		if(isset($evaluation_form->department_id) && $evaluation_form->department_id != 'null' && $evaluation_form->department_id != '0'&&$evaluation_form->apply_for=='department'){
			$searchVal = array('[', ']', '"');
			$replaceVal = array('(', ')', '');
			$department_array = str_replace($searchVal, $replaceVal, $evaluation_form->department_id);
			$sql .= ' AND staffid in ( select staffid from '.get_db_prefix().'staff_departments where departmentid in '.$department_array.' )';
		}
		if(isset($evaluation_form->role_id) && $evaluation_form->role_id != 'null' && $evaluation_form->role_id != '0'&&$evaluation_form->apply_for=='role'){
			$searchVal = array('[', ']', '"');
			$replaceVal = array('(', ')', '');
			$role_array = str_replace($searchVal, $replaceVal, $evaluation_form->roles_id);
			$sql .= ' AND role in '.$role_array.'';
		} 
		if(isset($evaluation_form->staff_id) && $evaluation_form->staff_id != 'null' && $evaluation_form->staff_id != '0'&&$evaluation_form->apply_for=='staff'){
			$searchVal = array('[', ']', '"');
			$replaceVal = array('(', ')', '');
			$staff_array = str_replace($searchVal, $replaceVal, $evaluation_form->staff_id);
			$sql .= ' AND staffid in '.$staff_array.'';
		} 
		$arr_staff = $this->db->query($sql)->getResultArray();
	}

	$arr_object =[];
	$flag_member_evaluate = 0;
	foreach ($arr_staff as $staff) {
		$kpi_staff = 0;
		$staff_info =[];
		$staff_info[] = $staff['staff_identifi'];
		$staff_info[] = $staff['firstname'];
		foreach ($evaluation_form_detail as $eval_det_key => $eval_det_value) {
			$arr_income = json_decode($eval_det_value['income']);
			$arr_kpi_percent = json_decode($eval_det_value['kpi_percent']);
			$arr_kpi_formula = json_decode($eval_det_value['kpi_formula']);

			$kpi_temp = 0;
			foreach (json_decode($eval_det_value['kpi_key']) as $kpi_key => $kpi_value) {
				$staff_info[] = $arr_income[$kpi_key] ;
				foreach ($emp_marks as $emp_marks_key =>  $staff_id) {
					$kpi_formula1 = '';
					$kpi_formula2 = '';
					foreach ($evaluate_result as $evaluate_result_value) {
						if($evaluate_result_value['assessor_id'] == $staff_id){
							$arr_result = json_decode($evaluate_result_value['result']);
							foreach ($arr_result as $arr_result_value) {
								if($arr_result_value->staff_id == $staff['staff_identifi']){

									$staff_info[] = $arr_result_value->$kpi_value ;
									$formula = $arr_kpi_formula[$kpi_key];
									if($arr_result_value->$kpi_value != ''){
										$result_value = $arr_result_value->$kpi_value;
									}else{
										$result_value = 0;
									}


									$formula = str_replace($kpi_value,$result_value,$formula);
									$formula = eval('return '.$formula.';');

									$kpi_formula2 .= (($formula*$percent[$emp_marks_key]/100)/$arr_income[$kpi_key])*$arr_kpi_percent[$kpi_key]/100;
									$kpi_temp += (float)eval('return '.$kpi_formula2.';');
								}

							}
							if($arrstaff != false){
								if(count($staff_info) == 3){
									$flag_member_evaluate = 1;
								}
							}

						}
					}

				}
				$staff_info[] = number_format($kpi_temp, 3);
				$kpi_staff += $kpi_temp;
			}
		}
		if($arrstaff != false && $flag_member_evaluate == 1){
			$member_evaluate = [];
			array_push($arr_object, $member_evaluate);
		}else{
			$staff_info[] = number_format($kpi_staff, 3);
			array_push($arr_object, $staff_info);
		}

	}
	return $arr_object;
}
/**
 * add attachment to database
 * @param integer  $rel_id     
 * @param string  $rel_type   
 * @param string  $attachment 
 * @param integer $insert_id
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
	 * function get file for hrm staff
	 * @param  integer  $id     
	 * @param  boolean $rel_id 
	 * @return object          
	 */
	public function get_file($id, $rel_id = false)
	{
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
	 * delete staff attchement
	 * @param  integer $attachment_id 
	 * @return integer                
	 */
	public function delete_hr_profile_staff_attachment($attachment_id)
	{
		$deleted    = false;
		$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id.'/'.$attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(get_db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('Contract Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
			}

			if (is_dir(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id)) {
				$other_attachments = list_files(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id);
				if (count($other_attachments) == 0) {
					delete_dir(HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER.'/' .$attachment->rel_id);
				}
			}
		}
		return $deleted;
	}



	/**
	 * get hr profile attachments delete
	 * @param  integer $id 
	 * @return object     
	 */
	public function get_hr_profile_attachments_delete($id){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}
	}


	/**
	 * update staff permissions
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_staff_permissions($data){
		if($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $data['permissions']), $data['id'])) {
			$affectedRows++;
		}
		if ($affectedRows > 0) {
			hooks()->do_action('staff_member_updated', $data['id']);
			log_activity('Staff Member Updated [ID: ' . $data['id'] . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
			return true;
		}
		return false;
	}

	/**
	 * update permissions
	 * @param  array $permissions 
	 * @param  integer $id          
	 * @return boolean              
	 */
	public function update_permissions($permissions, $id)
	{
		$this->db->where('staff_id', $id);
		$this->db->delete('staff_permissions');
		$is_staff_member = is_staff_member($id);
		foreach ($permissions as $feature => $capabilities) {
			foreach ($capabilities as $capability) {
				if ($feature == 'leads' && !$is_staff_member) {
					continue;
				}
				$this->db->insert('staff_permissions', ['staff_id' => $id, 'feature' => $feature, 'capability' => $capability]);
			}
		}
		return true;
	}


	/**
	 * get file info
	 * @param  integer $id       
	 * @param  string $rel_type 
	 * @return object           
	 */
	public function get_file_info($id,$rel_type){
		$this->db->where('rel_id', $id);
		$this->db->where('rel_type', $rel_type);
		return $this->db->get(get_db_prefix().'files')->getRow();
	}
   /**
	* update staff profile
	* @param  array $data 
	* @return boolean       
	*/
	public function update_staff_profile($data){
		$id = $data['id'];
		unset($data['id']);
		$data['date_update']          = to_sql_date1(get_my_local_time("Y-m-d"), true);
		$data['birthday']             = to_sql_date1($data['birthday']);
		$data['days_for_identity']    = to_sql_date1($data['days_for_identity']);
		if (isset($data['fakeusernameremembered'])) {
			unset($data['fakeusernameremembered']);
		}
		if (isset($data['fakepasswordremembered'])) {
			unset($data['fakepasswordremembered']);
		}
		if (isset($data['nationality'])) {
			unset($data['nationality']);
		}
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

		$affectedRows = 0;
		if (isset($data['departments'])) {
			$departments = $data['departments'];
			unset($data['departments']);
		}

		$permissions = [];
		if (isset($data['permissions'])) {
			$permissions = $data['permissions'];
			unset($data['permissions']);
		}

		if (isset($data['custom_fields'])) {
			$custom_fields = $data['custom_fields'];
			if (handle_custom_fields_post($id, $custom_fields)) {
				$affectedRows++;
			}
			unset($data['custom_fields']);
		}
		if (!isset($data['password'])) {
			unset($data['password']);
		} else {
			$data['password']             = app_hash_password($data['password']);
			$data['last_password_change'] = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		}


		if (isset($data['two_factor_auth_enabled'])) {
			$data['two_factor_auth_enabled'] = 1;
		} else {
			$data['two_factor_auth_enabled'] = 0;
		}

		if (isset($data['is_not_staff'])) {
			$data['is_not_staff'] = 1;
		} else {
			$data['is_not_staff'] = 0;
		}

		if (isset($data['admin']) && $data['admin'] == 1) {
			$data['is_not_staff'] = 0;
		}

		if(isset($data['year_requisition'])){
			unset($data['year_requisition']);
		}


   // First check for all cases if the email exists.

		$this->db->where('email', $data['email']);
		$email = $this->db->get(get_db_prefix() . 'staff')->getRow();
		

		$data['admin'] = 0;
		if (is_admin()) {
			if (isset($data['administrator'])) {
				$data['admin'] = 1;
				unset($data['administrator']);
			}
		}

		$send_welcome_email = true;
		$original_password  = $data['password'];
		if (!isset($data['send_welcome_email'])) {
			$send_welcome_email = false;
		} else {
			unset($data['send_welcome_email']);
		}
		if ($data['admin'] == 1) {
			$data['is_not_staff'] = 0;
		}


		$data['email_signature'] = nl2br_save_html($data['email_signature']);

		$this->load->model('departments_model');
		$staff_departments = $this->departments_model->get_staff_departments($id);
		if (sizeof($staff_departments) > 0) {
			if (!isset($data['departments'])) {
				$this->db->where('staffid', $id);
				$this->db->delete(get_db_prefix() . 'staff_departments');
			} else {
				foreach ($staff_departments as $staff_department) {
					if (isset($departments)) {
						if (!in_array($staff_department['departmentid'], $departments)) {
							$this->db->where('staffid', $id);
							$this->db->where('departmentid', $staff_department['departmentid']);
							$this->db->delete(get_db_prefix() . 'staff_departments');
							if ($this->db->affected_rows() > 0) {
								$affectedRows++;
							}
						}
					}
				}
			}
			if (isset($departments)) {
				foreach ($departments as $department) {
					$this->db->where('staffid', $id);
					$this->db->where('departmentid', $department);
					$_exists = $this->db->get(get_db_prefix() . 'staff_departments')->getRow();
					if (!$_exists) {
						$this->db->insert(get_db_prefix() . 'staff_departments', [
							'staffid'      => $id,
							'departmentid' => $department,
						]);
						if ($this->db->affected_rows() > 0) {
							$affectedRows++;
						}
					}
				}
			}
		} else {
			if (isset($departments)) {
				foreach ($departments as $department) {
					$this->db->insert(get_db_prefix() . 'staff_departments', [
						'staffid'      => $id,
						'departmentid' => $department,
					]);
					if ($this->db->affected_rows() > 0) {
						$affectedRows++;
					}
				}
			}
		}
		$this->db->where('staffid', $id);
		$this->db->update(get_db_prefix() . 'staff', $data);
		if ($this->db->affected_rows() > 0) {
			$affectedRows++;
		}
		/*update avatar end*/
		if ($this->update_permissions((isset($data['admin']) && $data['admin'] == 1 ? [] : $permissions), $id)) {
			$affectedRows++;
		}
		if ($affectedRows > 0) {
			hooks()->do_action('staff_member_updated', $id);
			log_activity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
			return true;
		}
		return false;
	}
   /**
	* get staff in deparment
	* @param  integer $department_id 
	* @return integer                
	*/
	public function get_staff_in_deparment($department_id)
	{
		$data = [];
		$sql = 'select 
		id 
		from    (select * from '.get_db_prefix().'team
		order by '.get_db_prefix().'team.parent_id, '.get_db_prefix().'team.id) team_sorted,
		(select @pv := '.$department_id.') initialisation
		where   find_in_set(parent_id, @pv)
		and     length(@pv := concat(@pv, ",", id)) OR id = '.$department_id.'';
		$result_arr = $this->db->query($sql)->getResultArray();

		foreach ($result_arr as $key => $value) {
			$data[$key] = $value['id'];
		}
		return $data;
	}

	/**
	 * get staff role
	 * @param  [type] $staff_id 
	 * @return [type]           
	 */
	public function get_staff_role($staff_id){

		return $this->db->query('select r.title
			from '.get_db_prefix().'users as s 
			left join '.get_db_prefix().'roles as r on r.id = s.role_id
			where s.id ='.$staff_id)->getRow();
	}


	/**
	 * delete hr profile permission
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_hr_profile_permission($id)
	{
		$str_permissions ='';
		foreach (list_hr_profile_permisstion() as $per_key =>  $per_value) {
			if(strlen($str_permissions) > 0){
				$str_permissions .= ",'".$per_value."'";
			}else{
				$str_permissions .= "'".$per_value."'";
			}
		}

		$sql_where = " feature IN (".$str_permissions.") ";

		$this->db->where('staff_id', $id);
		$this->db->where($sql_where);
		$this->db->delete(get_db_prefix() . 'staff_permissions');

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}


	/**
	 * get data dpm chart
	 * @param  [type] $dpm 
	 * @return [type]      
	 */
	public function get_data_dpm_chart($dpm)
	{
		
		$department =  $this->db->query('select s.staffid as id,s.job_position, s.phonenumber, s.staff_identifi, s.email as staff_email, s.team_manage as pid, s.firstname as name
			from '.get_db_prefix().'staff as s 
			left join '.get_db_prefix().'staff_departments as sd on sd.staffid = s.staffid
			left join '.get_db_prefix().'departments d on d.departmentid = sd.departmentid where d.departmentid = "'.$dpm.'" and s.status_work != "inactivity"
			order by s.team_manage, s.staffid')->getResultArray();

		$dep_tree = array(); 

		$list_id = [];
		foreach ($department as $ds ) {
			$list_id[] = $ds['id'];
		}

		foreach ($department as $dep) {

			if($dep['pid'] == 0 ||  !in_array($dep['pid'], $list_id) ){
				$dpm = $this->getdepartment_name($dep['id']);
				$node = array();
				$node['name'] = $dep['name'];
				
				$node['staff_identifi'] = $dep['staff_identifi'];
				$node['identifi_icon'] = '"fa fa-qrcode"';
				$node['staff_email'] = $dep['staff_email'];
				$node['mail_icon'] = '"fa fa-envelope"';
				$node['dp_phonenumber'] = '"fa fa-phone"';
				$node['dp_user_icon'] = '"fa fa-user-o"';


				if($dep['job_position'] != null && $dep['job_position'] != 0){
					$node['job_position'] = $this->get_job_position($dep['job_position']);
					$node['job_position_url'] = admin_url('hrm/job_position_view_edit/'.$dep['job_position']);
				}else{
					$node['job_position'] = '';
					$node['job_position_url'] = '';
				}

				if($dep['phonenumber'] != null){
					$node['phonenumber'] = $dep['phonenumber'];
					
				}else{
					$node['phonenumber'] = '';
				}

				if($dpm->name != null){
					$node['departmentname'] = $dpm->name;
					$node['dp_icon'] = '"fa fa-sitemap"';
				}else{
					$node['departmentname'] = '';
				}

				$node['image'] = staff_profile_image($dep['id'], [
					'staff-profile-image-small staff-chart-padding',
				]);
				$node['children'] = $this->get_child_node_staff_dpm_chart($dep['id'], $department);
				
				$dep_tree[] = $node;
			}        
		}   
		return $dep_tree;

	}


	/**
	 * list job department
	 * @param  [type] $department 
	 * @return [type]             
	 */
	public function list_job_department($department){
		$this->db->select('staffid');
		$this->db->where('departmentid', $department);
		$arr_staff_id = [];
		$arr_staff = $this->db->get(get_db_prefix().'staff_departments')->getResultArray();
		$index_dep = 0;
		if(count($arr_staff) > 0){
			foreach ($arr_staff as $value) {
				if(!in_array($value['staffid'], $arr_staff_id)){
					$arr_staff_id[$index_dep] = $value['staffid'];
					$index_dep++;
				}                
			}
		}

		$rs = [];
		if(count($arr_staff_id) > 0){


			$arr_staff_id = implode(",", $arr_staff_id);
			$sql_where = 'SELECT '.get_db_prefix().'hr_job_position.position_id, position_name FROM '.get_db_prefix().'staff left join '.get_db_prefix().'hr_job_position on '.get_db_prefix().'staff.job_position = '.get_db_prefix().'hr_job_position.position_id WHERE '.get_db_prefix().'staff.job_position != "0" AND '.get_db_prefix().'staff.staffid IN ('.$arr_staff_id.')';

			$arr_job_position = $this->db->query($sql_where)->getResultArray();

			
			$arr_check_exist=[];
			foreach ($arr_job_position as $k => $note) {
				if(!in_array($note['position_id'], $arr_check_exist)){
					$rs[] = $note['position_id'];
					$arr_check_exist[$k] = $note['position_id'];
				}


			}
		}

		return $rs;
	}


	/**
	 * delete hr job position attachment file
	 * @param  [type] $attachment_id 
	 * @return [type]                
	 */
	public function delete_hr_job_position_attachment_file($attachment_id)
	{
		$deleted    = false;
		$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id.'/'.$attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(get_db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('job_position Attachment Deleted [job_positionID: ' . $attachment->rel_id . ']');
			}

			if (is_dir(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(get_hr_profile_upload_path_by_type('job_position') .$attachment->rel_id);
				}
			}
		}

		return $deleted;
	}


	/**
	 * get hrm profile file
	 * @param  [type] $rel_id   
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function get_hr_profile_file($rel_id, $rel_type){
		$builder = $this->db->table(get_db_prefix().'files');
		$builder->orderBy('dateadded', 'desc');
		$builder->where('rel_id', $rel_id);
		$builder->where('rel_type', $rel_type);

		return $builder->get()->getResultArray();
	}


	/**
	 * get job position training de
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_job_position_training_de($id = false){
		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');
		$builder->where('training_process_id', $id);
		return  $builder->get()->getRow();
	}


	/**
	 * delete job position training process
	 * @param  [type] $trainingid 
	 * @return [type]             
	 */
	public function delete_job_position_training_process($trainingid){
		//delete general info
		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');
		
		$builder->where('training_process_id', $trainingid);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;

	}

	/**
	 * delete position training
	 * @param  [type] $trainingid 
	 * @return [type]             
	 */
	public function delete_position_training($trainingid)
	{
		$affectedRows = 0;

		$builder = $this->db->table(get_db_prefix().'hr_position_training');
		$builder->where('training_id', $trainingid);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			$affectedRows++;
			// get all questions from the survey
			$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
			$builder->where('rel_id', $trainingid);
			$builder->where('rel_type', 'position_training');
			$questions = $builder->get()->getResultArray();

			// Delete the question boxes
			foreach ($questions as $question) {
				$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box');
				$builder->where('questionid', $question['questionid']);
				$builder->delete();

				$builder = $this->db->table(get_db_prefix().'hr_p_t_form_question_box_description');
				$builder->where('questionid', $question['questionid']);
				$builder->delete();
			}

			$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
			$builder->where('rel_id', $trainingid);
			$builder->where('rel_type', 'position_training');
			$builder->delete();

			$builder = $this->db->table(get_db_prefix().'hr_p_t_form_results');
			$builder->where('rel_id', $trainingid);
			$builder->where('rel_type', 'position_training');
			$builder->delete();

			$builder = $this->db->table(get_db_prefix().'hr_p_t_surveyresultsets');
			$builder->where('trainingid', $trainingid);
			$builder->delete();
		}
		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * get list position training by id training
	 * @param  array $training_id_aray 
	 * @return array                   
	 */
	public function get_list_position_training_by_id_training($training_id_aray){
		return $this->db->query('select * from '.get_db_prefix().'hr_position_training where training_id in ('.$training_id_aray.')')->getResultArray();
	}


	/**
	 * get contract
	 * @param  integer $id 
	 * @return array     
	 */
	public function get_contract($id){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
			$builder->where('id_contract', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
			return  $builder->get()->getResultArray();
		}

	}

	/**
	 * get contract detail
	 * @param  integer $id 
	 * @return array     
	 */
	public function get_contract_detail($id){
		$staff_contract_detail = $this->db->query('select * from '.get_db_prefix().'hr_staff_contract_detail where staff_contract_id = '.$id)->getResultArray();
		return $staff_contract_detail;
	}


	/**
	 * add contract
	 * @param array $data 
	 */
	public function add_contract($data){

		$data['start_valid']    = to_sql_date1($data['start_valid']);
		$data['end_valid']      = to_sql_date1($data['end_valid']);
		$data['sign_day']       = to_sql_date1($data['sign_day']);

		if(isset($data['job_position'])){
			$job_position = $data['job_position'];
			unset($data['job_position']);
		}

		if (isset($data['staff_contract_hs'])) {
			$staff_contract_hs = $data['staff_contract_hs'];
			unset($data['staff_contract_hs']);
		}
		
		$data['content'] = $this->hr_get_contract_template_by_staff($data['staff']);
		$data['hash'] = app_generate_hash();

		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if(isset($staff_contract_hs)){
			$staff_contract_detail = json_decode($staff_contract_hs);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];

			$header[] = 'type';
			$header[] = 'rel_type';
			$header[] = 'rel_value';
			$header[] = 'since_date';
			$header[] = 'contract_note';

			foreach ($staff_contract_detail as $key => $value) {

				if($value[0] != ''){
					$es_detail[] = array_combine($header, $value);
				}
			}
		}

		if (isset($insert_id)) {

			/*insert detail*/
			foreach($es_detail as $key => $rqd){
				$es_detail[$key]['staff_contract_id'] = $insert_id;
			}

			if(count($es_detail) != 0){
				$builder = $this->db->table(get_db_prefix().'hr_staff_contract_detail');
				$builder->insertBatch($es_detail);
			}
			/*update next number setting*/
			$this->update_prefix_number(['contract_code_number' =>  get_setting('contract_code_number')+1]);

		}


		return $insert_id;
	}


	/**
	 * update contract
	 * @param  array $data 
	 * @param  integer $id   
	 * @return boolean       
	 */
	public function update_contract($data, $id)
	{   
		$affectedRows = 0;

		$data['start_valid']    = to_sql_date1($data['start_valid']);
		$data['end_valid']      = to_sql_date1($data['end_valid']);
		$data['sign_day']       = to_sql_date1($data['sign_day']);

		if(isset($data['job_position'])){
			$job_position = $data['job_position'];
			unset($data['job_position']);
		}

		if (isset($data['staff_contract_hs'])) {
			$staff_contract_hs = $data['staff_contract_hs'];
			unset($data['staff_contract_hs']);
		}

		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
		$builder->where('id_contract', $id);
		$affected_rows = $builder->update($data);

		if ($affected_rows > 0) {
			$affectedRows++;
		}

		if(isset($staff_contract_hs)){
			$staff_contract_detail = json_decode($staff_contract_hs);

			$es_detail = [];
			$row = [];
			$rq_val = [];
			$header = [];


			$header[] = 'type';
			$header[] = 'rel_type';
			$header[] = 'rel_value';
			$header[] = 'since_date';
			$header[] = 'contract_note';
			$header[] = 'contract_detail_id';
			$header[] = 'staff_contract_id';

			foreach ($staff_contract_detail as $key => $value) {
				if($value[0] != ''){
					$es_detail[] = array_combine($header, $value);
				}
			}
		}

		$row = [];
		$row['update'] = []; 
		$row['insert'] = []; 
		$row['delete'] = [];
		$total = [];

		$total['total_amount'] = 0;

		foreach ($es_detail as $key => $value) {
			if($value['contract_detail_id'] != ''){
				$row['delete'][] = $value['contract_detail_id'];
				$row['update'][] = $value;
			}else{
				unset($value['contract_detail_id']);
				$value['staff_contract_id'] = $id;
				$row['insert'][] = $value;
			}

		}

		if(empty($row['delete'])){
			$row['delete'] = ['0'];
		}
		$row['delete'] = implode(",",$row['delete']);
		
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract_detail');
		$builder->where('contract_detail_id NOT IN ('.$row['delete'] .') and staff_contract_id ='.$id);
		$affected_rows = $builder->delete();
		if($affected_rows > 0){
			$affectedRows++;
		}

		if(count($row['insert']) != 0){
			$builder = $this->db->table(get_db_prefix().'hr_staff_contract_detail');
			$affected_rows = $builder->insertBatch($row['insert']);
			if($affected_rows > 0){
				$affectedRows++;
			}
		}
		if(count($row['update']) != 0){
			$builder = $this->db->table(get_db_prefix().'hr_staff_contract_detail');
			$affected_rows = $builder->updateBatch($row['update'], 'contract_detail_id');
			if($affected_rows > 0){
				$affectedRows++;
			}
		}

		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete contract
	 * @param  integer $id 
	 * @return boolean     
	 */
	public function delete_contract($id){
		$affectedRows = 0;

		$staff_name='';
		$staff_id='';
		$staff_contract_id=$id;


		$staff_contract = $this->get_contract($id);

		if($staff_contract){

			$staff_name .=  get_staff_full_name1($staff_contract->staff);
			$staff_id .= $staff_contract->staff;
		}

		$builder = $this->db->table(get_db_prefix().'hr_staff_contract_detail');
		$builder->where('staff_contract_id', $id);
		$affected_rows = $builder->delete();
		if($affected_rows > 0){
			$affectedRows++;
		}
		
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
		$builder->where('id_contract', $id);
		$affected_rows = $builder->delete();
		if($affected_rows > 0){
			$affectedRows++;
		}

		//delete atachement file
		$hr_contract_file = $this->get_hr_profile_file($id, 'hr_contract');
		foreach ($hr_contract_file as $file_key => $file_value) {
			$this->delete_hr_contract_attachment_file($file_value['id']);
		}

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}


	/**
	 * get contracttype by id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_contracttype_by_id($id){
		return $this->db->query('select * from '.get_db_prefix().'hr_staff_contract_type where id_contracttype = '.$id)->getResultArray();
	}


	/**
	 * get staff active
	 * @return array 
	 */
	public function get_staff_active()
	{
		$staff = $this->db->query('select * from '.get_db_prefix().'users as s where s.status = "active" AND deleted = 0 AND user_type = "staff"  order by s.staffid')->getResultArray();
		return $staff;
	}

	/**
	 * get staff active has contract
	 * @return array 
	 */
	public function get_staff_active_has_contract()
	{
		$where = '(select count(*) from '.get_db_prefix().'hr_staff_contract where staff = '.get_db_prefix().'staff.staffid and start_valid <="'.get_my_local_time('Y-m-d').'" and IF(end_valid != null, end_valid >="'.get_my_local_time('Y-m-d').'",1=1)) > 0 and (status_work="working" OR status_work="maternity_leave") and active=1';

		$this->db->where($where);
		return $this->db->get(get_db_prefix().'staff')->getResultArray();
	}


	/**
	 *  update prefix number
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_prefix_number($data)
	{
		$affected_rows=0;

		$hr_profile_hide_menu = 0;
		if(isset($data['hr_profile_hide_menu'])){
			$hr_profile_hide_menu = $data['hr_profile_hide_menu'];
			unset($data['hr_profile_hide_menu']);
		}
		$builder = $this->db->table(get_db_prefix().'settings');
		$builder->where('setting_name', 'hr_profile_hide_menu');
		$affectedRows = $builder->update([
			'setting_value' => $hr_profile_hide_menu,
		]);

		if($affectedRows){
			$affected_rows++;
		}

		foreach ($data as $key => $value) {
			 $builder = $this->db->table(get_db_prefix().'settings');
			$builder->where('setting_name',$key);
			$affectedRows = $builder->update([
				'setting_value' => $value,
			]);

			if ($affectedRows > 0) {
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
	 * create code
	 * @param  [type] $rel_type 
	 * @return [type]           
	 */
	public function create_code($rel_type) {
		//rel_type: position_code, staff_contract, ...
		$str_result ='';

		$prefix_str ='';
		switch ($rel_type) {
			case 'position_code':
			$prefix_str .= get_setting('job_position_prefix');
			$next_number = (int) get_setting('job_position_number');
			$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
			break;
			case 'staff_contract_code':
			$prefix_str .= get_setting('contract_code_prefix');
			$next_number = (int) get_setting('contract_code_number');
			$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT).'-'.date('M-Y');
			break;
			case 'staff_code':
			$prefix_str .= get_setting('staff_code_prefix');
			$next_number = (int) get_setting('staff_code_number');
			$str_result .= $prefix_str.str_pad($next_number,5,'0',STR_PAD_LEFT);
			break;
			
			default:
				# code...
			break;
		}

		return $str_result;

	}


	/**
	 * check department format
	 * @param  [type] $department 
	 * @return [type]             
	 */
	public function check_department_format($departments)
	{
		$str_error = '';
		$department = [];

		$arr_department = explode(',', $departments);
		for ($i = 0; $i < count($arr_department); $i++) {

			$builder = $this->db->table(get_db_prefix().'team');
			$builder->like('id', $arr_department[$i]);
			$department_value = $builder->get()->getRow();

			if($department_value){
				$department[$i] = $department_value->id;
			}else{

				$str_error .= $arr_department[$i].', ';
				return ['status' => false, 'result' => $str_error];
			}
		}

		return ['status' => true, 'result' => $department];
	}


	/**
	 * get dependent person
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_dependent_person($id = false)
	{
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_dependent_person');
			$builder->where('id', $id);

			return $builder->get()->getRow();
		}

		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'hr_dependent_person');

			return  $builder->get()->getResultArray();
		}

	}    


	/**
	 * get dependent person bytstaff
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function get_dependent_person_bytstaff($staffid)
	{
		$builder = $this->db->table(get_db_prefix().'hr_dependent_person');

		$builder->where('staffid', $staffid);
		return $builder->get()->getResultArray();
	}


	/**
	 * add dependent person
	 * @param [type] $data 
	 */
	public function add_dependent_person($data)
	{
		if(!isset($data['staffid'])){
			$data['staffid'] = get_staff_user_id1();
		}

		$data['dependent_bir'] = to_sql_date1($data['dependent_bir']);

		if(isset($data['start_month'])){
			$data['start_month'] = to_sql_date1($data['start_month']);
		}

		if(isset($data['end_month'])){
			$data['end_month'] = to_sql_date1($data['end_month']);
		}
		
		$builder = $this->db->table(get_db_prefix().'hr_dependent_person');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		if($insert_id){
			return $insert_id;
		}
		return false;
	}


	/**
	 * update dependent person
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_dependent_person($data, $id)
	{   
		if(isset($data['start_month'])){
			$data['start_month'] = to_sql_date1($data['start_month']);
		}
		
		if(isset($data['end_month'])){
			$data['end_month'] = to_sql_date1($data['end_month']);
		}

		$builder = $this->db->table(get_db_prefix().'hr_dependent_person');

		$builder->where('id', $id);
		$data['dependent_bir'] = to_sql_date1($data['dependent_bir']);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete dependent person
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_dependent_person($id)
	{
		$builder = $this->db->table(get_db_prefix().'hr_dependent_person');

		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * update approval status
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_approval_dependent_person($data)
	{
		$data_obj['start_month'] = to_sql_date1($data['start_month']);
		$data_obj['end_month'] = to_sql_date1($data['end_month']);
		$data_obj['status_comment'] = $data['reason'];
		$data_obj['status'] = $data['status'];

		$builder = $this->db->table(get_db_prefix().'hr_dependent_person');

		$builder->where('id', $data['id']);
		$affected_rows = $builder->update($data_obj);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * update approval status
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_approval_status($data){
		$data_obj['start_month'] = to_sql_date1($data['start_month']);
		$data_obj['end_month'] = to_sql_date1($data['end_month']);
		$data_obj['status_comment'] = $data['reason'];
		$data_obj['status'] = $data['status'];

		$builder = $this->db->table(get_db_prefix().'hr_dependent_person');

		$builder->where('id', $data['id']);
		$affected_rows = $builder->update($data_obj);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * add resignation procedure
	 * @param [type] $data 
	 */
	public function add_resignation_procedure($data)
	{
		$data['dateoff'] = to_sql_date1($data['dateoff'], true);
		$data['staff_name'] = get_staff_full_name1($data['staffid']);
		$staffid = $data['staffid'];

		$builder = $this->db->table(get_db_prefix().'hr_list_staff_quitting_work');
		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if($insert_id){
			$asset = $this->get_data_asset($staffid);

			if(count($asset) > 0){
				$rel_id_asset = $this->add_data_of_staff_quit_work_by_id( app_lang('asset'));
				foreach ($asset as $key => $name) {
					if($rel_id_asset){
						$option_name_by_id = $this->add_data_of_staff_quit_work($rel_id_asset, $name['asset_name'], $staffid);
					}
				}
			}

			$department_staff = $this->get_staff_departments($staffid);

			if(count($department_staff) > 0){
				foreach ($department_staff as $deparment) {
					$check = $this->check_department_on_procedure($deparment['id']);
					if(strlen($check) > 0){
						break;
					}
				}

			}else{
				$check = '';
			}
			if($check != ''){

				$result = $this->get_procedure_retire($check);
				$arr_handle_id = [];

				if(count($result) > 0){
					foreach ($result as $key => $name) {
						if($name['rel_name']){
							$rel_id = $this->add_data_of_staff_quit_work_by_id($name['rel_name'], $name['people_handle_id']);
							if($rel_id){
								$name['option_name'] = json_decode($name['option_name']);
								foreach ($name['option_name'] as $option) {
									$option_name_by_id = $this->add_data_of_staff_quit_work($rel_id, $option, $staffid);
								}
							}

							if(!in_array($name['people_handle_id'], $arr_handle_id)){
								$arr_handle_id[] = $name['people_handle_id'];
							}
						}
					}

					if(count($arr_handle_id) > 0){
						foreach ($arr_handle_id as $people_handle_id) {

							if(is_numeric($people_handle_id)){
								/*send notify*/
								$mes = 'hr_resignation_procedures_are_waiting_for_your_confirmation';
								$notify_data = ['hr_send_layoff_checklist_handle_staff_id' => $staffid];
								hr_log_notification($mes, $notify_data, get_staff_user_id1() ,$people_handle_id);
							}
						}
					}


				}

			}

			return $insert_id;
		}

		return false;        
	}

	/**
	 * get data asset
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function get_data_asset($staffid)
	{	
		$builder = $this->db->table(get_db_prefix().'hr_allocation_asset');
		$builder->where('staff_id', $staffid);
		return $builder->get()->getResultArray();
	}


	/**
	 * add data of staff quit work by id
	 * @param [type] $rel_name         
	 * @param string $people_handle_id 
	 */
	public function add_data_of_staff_quit_work_by_id($rel_name, $people_handle_id = '')
	{

		if($people_handle_id == ''){
			$people_handle_id = get_staff_user_id1();
		}
		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_of_staff_by_id');

		$builder->insert([
			'rel_name' => $rel_name,
			'people_handle_id' => $people_handle_id
		]);
		$insert_id = $this->db->insertID();

		if ($insert_id) {
			return $insert_id;
		}
		return false;        

	}


	/**
	 * add data of staff quit work
	 * @param [type] $rel_id      
	 * @param [type] $option_name 
	 * @param [type] $staffid     
	 */
	public function add_data_of_staff_quit_work($rel_id, $option_name, $staffid)
	{
		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_of_staff');

		$insert_id = $builder->insert([
			'rel_id' => $rel_id,
			'option_name' => $option_name,
			'status' => 0,
			'staffid' => $staffid
		]);

		if ($insert_id) {
			return $insert_id;
		}
		return false;        

	}


	/**
	 * get resignation procedure by staff
	 * @param  [type] $staff_id 
	 * @return [type]           
	 */
	public function get_resignation_procedure_by_staff($staff_id)
	{
		$builder = $this->db->table('hr_list_staff_quitting_work');
		$builder->where('staffid', $staff_id);
		$resignation_procedure = $builder->get()->getRow();

		return $resignation_procedure;
	}


	/**
	 * delete procedures for quitting work
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function delete_procedures_for_quitting_work($staffid)
	{
		$affectedRows = 0;
		$builder = $this->db->table('hr_list_staff_quitting_work');
		$builder->where('staffid', $staffid);
		$affected_rows = $builder->delete();

		if ($affected_rows > 0) {
			$affectedRows++;
		}

		$builder = $this->db->table('hr_procedure_retire_of_staff');
		$builder->where('staffid', $staffid);
		$affected_rows = $builder->delete();
		
		if ($affected_rows > 0) {
			$affectedRows++;
		}

		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * get data procedure retire of staff
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function get_data_procedure_retire_of_staff($staffid)
	{
		$builder = $this->db->table('hr_procedure_retire_of_staff');
		$builder->select(get_db_prefix().'hr_procedure_retire_of_staff.id, '.get_db_prefix().'hr_procedure_retire_of_staff.staffid, '.get_db_prefix().'hr_procedure_retire_of_staff.rel_id, '.get_db_prefix().'hr_procedure_retire_of_staff.option_name, '.get_db_prefix().'hr_procedure_retire_of_staff.status, b.rel_name, b.people_handle_id');
		$builder->join(get_db_prefix().'hr_procedure_retire_of_staff_by_id as b','b.id = '.get_db_prefix().'hr_procedure_retire_of_staff.rel_id');
		$builder->where('staffid', $staffid);
		return $builder->get()->getResultArray();
	}


	/**
	 * update status quit work
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function update_status_quit_work($staffid, $id)
	{
		$affectedRows = 0;
		$builder = $this->db->table(get_db_prefix().'hr_list_staff_quitting_work');
		$builder->where('id', $id);
		$builder->update([
			'approval' => 'approved'
		]);

		if ($affectedRows > 0) {
			return true;
		}

		if($staffid){

			$builder = $this->db->table(get_db_prefix().'users');
			$builder->where('id',$staffid);
			$builder->update([
				'status' => 'inactive',
				'status_work' => 'inactivity',
				'date_update' => to_sql_date1(get_my_local_time("Y-m-d"), true)
			]);
			if ($affectedRows > 0) {
				return true;
			}

		}

		if ($affectedRows > 0) {
			return true;
		}
		return false;

	}


	/**
	 * update status procedure retire of staff
	 * @param  array  $where 
	 * @return [type]        
	 */
	public function update_status_procedure_retire_of_staff($where =[])
	{
		$builder = $this->db->table(get_db_prefix().'hr_procedure_retire_of_staff');
		$builder->where($where);
		$affected_rows = $builder->update([
			'status' => 1
		]);

		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete hr q a attachment file
	 * @param  [type] $attachment_id 
	 * @return [type]                
	 */
	public function delete_hr_q_a_attachment_file($attachment_id)
	{
		$deleted    = false;
		$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id.'/'.$attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(get_db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('kb article files Attachment Deleted [job_positionID: ' . $attachment->rel_id . ']');
			}

			if (is_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
				}
			}
		}

		return $deleted;
	}


	/**
	 * get salary allowance handsontable
	 * @return [type] 
	 */
	public function get_salary_allowance_handsontable()
	{

		$salary_type        = _l('hr_salary_type');
		$allowance_type     = _l('hr_allowance_type');
		$salary_symbol      = 'st';
		$allowance_symbol   = 'al';

		$salary_types = $this->db->query('select CONCAT("'.$salary_symbol.'","_",form_id) as id, CONCAT("'.$salary_type.'",": ",form_name) as label from ' . get_db_prefix() . 'hr_salary_form ')->getResultArray();

		$allowance_types = $this->db->query('select CONCAT("'.$allowance_symbol.'","_",type_id) as id, CONCAT("'.$allowance_type.'",": ",type_name) as label from ' . get_db_prefix() . 'hr_allowance_type ')->getResultArray();

		return array_merge($salary_types, $allowance_types);

	}


	/**
	 * delete hr contract attachment file
	 * @param  [type] $attachment_id 
	 * @return [type]               
	 */
	public function delete_hr_contract_attachment_file($attachment_id)
	{
		$deleted    = false;
		$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
		if ($attachment) {

			if (empty($attachment->external)) {
				unlink(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id.'/'.$attachment->file_name);
			}
			$builder = $this->db->table(get_db_prefix().'files');
			$builder->where('id', $attachment->id);
			$affected_rows = $builder->delete();
			if ($affected_rows > 0) {
				$deleted = true;
			}

			if (is_dir(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(get_hr_profile_upload_path_by_type('staff_contract') .$attachment->rel_id);
				}
			}
		}

		return $deleted;
	}


	/**
	 * get salary allowance for table
	 * @param  [type] $contract_id 
	 * @return [type]              
	 */
	public function get_salary_allowance_for_table($contract_id)
	{   
		$salary_allowance = '';
		$contract_details = $this->get_contract_detail($contract_id);

		if(count($contract_details) > 0){
			foreach ($contract_details as $key => $value) {
				$type_name ='';
				if(preg_match('/^st_/', $value['rel_type'])){
					$rel_value = str_replace('st_', '', $value['rel_type']);
					$salary_type = $this->get_salary_form($rel_value);

					$type = 'salary';
					if($salary_type){
						$type_name = $salary_type->form_name;
					}

				}elseif(preg_match('/^al_/', $value['rel_type'])){
					$rel_value = str_replace('al_', '', $value['rel_type']);
					$allowance_type = $this->get_allowance_type($rel_value);

					$type = 'allowance';
					if($allowance_type){
						$type_name = $allowance_type->type_name;
					}
				}
				$salary_allowance .= $type_name.': '. app_format_money($value['rel_value'],'').'('._l('hr_start_month').':'._d($value['since_date']).')'.'<br>';

			}
		}

		return $salary_allowance;
	}


	/**
	 * send mail training
	 * @param  [type] $email       
	 * @param  [type] $sender_name 
	 * @param  [type] $subject     
	 * @param  [type] $body        
	 * @return [type]              
	 */
	public function send_mail_training($email,$sender_name,$subject,$body){
		$staff_id = get_staff_user_id1();
		$inbox = array();
		$inbox['to'] = $email;
		$inbox['sender_name'] = get_option('companyname');
		$inbox['subject'] = _strip_tags($subject);
		$inbox['body'] = _strip_tags($body);        
		$inbox['body'] = nl2br_save_html($inbox['body']);
		$inbox['date_received']      = to_sql_date1(get_my_local_time("Y-m-d H:i:s"), true);
		
		if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0){
			$ci = &get_instance();
			$ci->email->initialize();
			$ci->load->library('email');    
			$ci->email->clear(true);
			$ci->email->from(get_option('smtp_email'), $inbox['sender_name']);
			$ci->email->to($inbox['to']);
			
			$ci->email->subject($inbox['subject']);
			$ci->email->message($inbox['body']);

			$ci->email->send(true);
		}
		return true;
	}

	/**
	 * get board mark form
	 * @param  [type] $rel_id 
	 * @return [type]         
	 */
	public function get_board_mark_form($rel_id){
		$builder = $this->db->table(get_db_prefix().'hr_position_training');

		$builder->where('training_id',$rel_id);
		return $builder->get()->getRow();
	}


	public function report_by_leave_statistics()
	{
		$months_report = $this->input->post('months_report');
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

				$custom_date_select = '(hrl.start_time BETWEEN "' . $beginMonth . '" AND "' . $endMonth . '")';
			} elseif ($months_report == 'this_month') {
				$custom_date_select = '(hrl.start_time BETWEEN "' . date('Y-m-01') . '" AND "' . date('Y-m-t') . '")';
			} elseif ($months_report == 'this_year') {
				$custom_date_select = '(hrl.start_time BETWEEN "' .
				date('Y-m-d', strtotime(date('Y-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date('Y-12-31'))) . '")';
			} elseif ($months_report == 'last_year') {
				$custom_date_select = '(hrl.start_time BETWEEN "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-01-01'))) .
				'" AND "' .
				date('Y-m-d', strtotime(date(date('Y', strtotime('last year')) . '-12-31'))) . '")';
			} elseif ($months_report == 'custom') {
				$from_date = to_sql_date1($this->input->post('report_from'));
				$to_date   = to_sql_date1($this->input->post('report_to'));
				if ($from_date == $to_date) {
					$custom_date_select =  'hrl.start_time ="' . $from_date . '"';
				} else {
					$custom_date_select = '(hrl.start_time BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
				}
			}

		}

		$chart = [];
		$dpm = $this->departments_model->get();
		foreach($dpm as $d){
			$chart['categories'][] = $d['name'];

			$chart['sick_leave'][] = $this->count_type_leave($d['departmentid'],1,$custom_date_select);
			$chart['maternity_leave'][] = $this->count_type_leave($d['departmentid'],2,$custom_date_select);
			$chart['private_work_with_pay'][] = $this->count_type_leave($d['departmentid'],3,$custom_date_select);
			$chart['private_work_without_pay'][] = $this->count_type_leave($d['departmentid'],4,$custom_date_select);
			$chart['child_sick'][] = $this->count_type_leave($d['departmentid'],5,$custom_date_select);
			$chart['power_outage'][] = $this->count_type_leave($d['departmentid'],6,$custom_date_select);
			$chart['meeting_or_studying'][] = $this->count_type_leave($d['departmentid'],7,$custom_date_select);
		}

		return $chart;
	}


	/**
	 * get list quiting work
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function get_list_quiting_work($staffid = ''){
		if($staffid != ''){
			$builder = $this->db->table(get_db_prefix().'hr_list_staff_quitting_work');
			$builder->where('staffid', $staffid);
			return $builder->get()->getRow();
		}else{
			$builder = $this->db->table(get_db_prefix().'hr_list_staff_quitting_work');
			return $builder->get()->getResultArray();
		}
	}

	/**
	 * get staff by _month
	 * @param  [type] $from_month 
	 * @param  [type] $to_month   
	 * @return [type]             
	 */
	public function get_staff_by_month($from_month, $to_month)
	{
		return $this->db->query('select * from '.get_db_prefix().'users where deleted = 0 AND user_type = "staff" AND status_work != "inactivity" AND date_format(created_at, "%Y-%m-%d") between "'.$from_month.'" and "'.$to_month.'"')->getResultArray();
	}

	/**
	 * get dstafflist by year
	 * @param  [type] $year  
	 * @param  [type] $month 
	 * @return [type]        
	 */
	public function get_dstafflist_by_year($year,$month)
	{
		return $this->db->query('select * from '.get_db_prefix().'users where year(created_at) = \''.$year.'\' and month(created_at) >= \''.$month.'\' and id not in (select staffid from '.get_db_prefix().'hr_list_staff_quitting_work)')->getResultArray();
	}


	/**
	 * get staff by department id and time
	 * @param  [type] $id_department 
	 * @param  [type] $from_time     
	 * @param  [type] $to_time       
	 * @return [type]                
	 */
	public function get_staff_by_department_id_and_time($id_department, $from_time, $to_time)
	{
		$format_from_date = preg_replace('/\//','-', $from_time); 
		$format_to_date = preg_replace('/\//','-', $to_time);
		$start_date = strtotime(date_format(date_create($format_from_date),"Y/m/d"));
		$end_date = strtotime(date_format(date_create($format_to_date),"Y/m/d"));
		$list_staff = $this->db->query('select * from '.get_db_prefix().'staff where staffid in (SELECT staffid FROM '.get_db_prefix().'staff_departments where departmentid = '.$id_department.')')->getResultArray();

		$list_id_staff = [];
		$list_id=[];
		foreach ($list_staff as $key => $value) {
			$list_staff_contract = $this->db->query('select * from '.get_db_prefix().'hr_staff_contract where staff = '.$value['staffid'].'')->getResultArray();
			$min = 9999999999;
			$max = 0;
			foreach ($list_staff_contract as $key => $item_contract) {
				$format_date1 = preg_replace('/\//','-', $item_contract['start_valid']); 
				$date = date_format(date_create($format_date1),"Y/m/d");                                                                 
				$start_date = strtotime($date);
				if($start_date < $min){
					$min = $start_date;
				}

				$format_date2 = preg_replace('/\//','-', $item_contract['end_valid']); 
				$date = date_format(date_create($format_date2),"Y/m/d");                     
				$to_date = strtotime($date);
				if($to_date > $max){
					$max = $to_date;
				}
			}
			if(($min >= $start_date)&&($min <= $end_date)){
				$list_id[] = $value['staffid'];
			}
			else{
				if(($max>=$end_date)&&($max<=$end_date)){
					$list_id[] = $value['staffid'];
				}
			}
		}
		$implode = '0';
		if(isset($list_id)){
			if(count($list_id)>0){
				$implode = implode(',', $list_id);
			}
		}
		return $this->db->query('SELECT * FROM '.get_db_prefix().'staff where staffid in ('.$implode.')')->getResultArray();
	}


	/**
	 * get department by list id
	 * @param  string $list_id 
	 * @return [type]          
	 */
	public function get_department_by_list_id($list_id = '')
	{
		if($list_id==''){
			return $this->db->query('select * from '.get_db_prefix().'team')->getResultArray();
		}
		else{
			return $this->db->query('select * from '.get_db_prefix().'team where id in ('.$list_id.')')->getResultArray();
		}
	}


	/**
	 * get list contract detail staff
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function get_list_contract_detail_staff($staffid)
	{

		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
		$builder->where('staff', $staffid);
		$builder->orderBy('start_valid', 'desc');
		$builder->limit(2);
		$staff_contracts = $builder->get()->getResultArray();

		if(count($staff_contracts) == 2){

			$new_salary=0;
			$old_salary=0;
			$staff_contract_ids = [];
			foreach ($staff_contracts as $key => $staff_contract) {
				if($key == 0){
					$date_effect = $staff_contract['start_valid'];
				}
				array_push($staff_contract_ids, $staff_contract['id_contract']);
			}

			$builder = $this->db->table(get_db_prefix().'hr_staff_contract_detail');
			$builder->select('sum(rel_value) as rel_value, staff_contract_id');
			$sql_where = 'staff_contract_id IN ("'.implode('", "', $staff_contract_ids).'")';
			$builder->where($sql_where);
			$builder->groupBy('staff_contract_id');
			$staff_contract_details = $builder->get()->getResultArray();

			$contract_detail=[];
			foreach ($staff_contract_details as $d_key => $staff_contract_detail) {
				$contract_detail[$staff_contract_detail['staff_contract_id']] = $staff_contract_detail['rel_value'];
			}

			foreach ($staff_contract_ids as $key => $value) {
				if($key == 0){
					//new
					if(isset($contract_detail[$value])){
						$new_salary = $contract_detail[$value];
					}
				}else{
					//old
					if(isset($contract_detail[$value])){
						$old_salary = $contract_detail[$value];
					}
				}
			}

			$result_array=[];
			$result_array['new_salary']=$new_salary;
			$result_array['old_salary']=$old_salary;
			$result_array['date_effect']=$date_effect;
			$result_array;
			return $result_array;

		}else{
			return false;
		}

	}


	/**
	 * get list staff by year
	 * @param  [type] $year 
	 * @return [type]       
	 */
	public function get_list_staff_by_year($year)
	{
		return $this->db->query('select * from '.get_db_prefix().'staff where year(datecreated) = \''.$year.'\' and staffid not in (select staffid from '.get_db_prefix().'hr_list_staff_quitting_work)')->getResultArray();
	}


	/**
	 * count staff by department literacy
	 * @param  string $department_ids 
	 * @return [type]                 
	 */
	public function count_staff_by_department_literacy($department_ids='')
	{
		$result =[];

		$this->db->select('count(staffdepartmentid) as total_staff, departmentid, literacy');
		if($department_ids != ''){
			$sql_where = get_db_prefix().'staff_departments.departmentid in ('.$department_ids.')';
			$this->db->where($sql_where);
		}
		$this->db->join(get_db_prefix() . 'staff', get_db_prefix() . 'staff.staffid = ' . get_db_prefix() . 'staff_departments.staffid', 'left');
		$this->db->group_by('departmentid, literacy');
		$this->db->orderBy('departmentid', 'asc');
		$staff_departments = $this->db->get(get_db_prefix().'staff_departments')->getResultArray();

		$department_id= 0;
		$temp=[];
		foreach ($staff_departments as $key => $value) {
			if($value['literacy'] != ''){
				$temp[$value['literacy']] = $value['total_staff'];

				if(count($staff_departments) != $key+1){
					if($value['departmentid'] != $staff_departments[$key+1]['departmentid']){
						$result[$value['departmentid']] = $temp;
						$temp=[];
					}
				}else{
					$result[$value['departmentid']] = $temp;

				}
			}

		}
		return $result;
	}


	/**
	 * report by staffs month
	 * @param  [type] $from_date 
	 * @param  [type] $to_date   
	 * @return [type]            
	 */
	public function report_by_staffs_month($from_date, $to_date)
	{

		$new_staff_by_month = $this->report_new_staff_by_month($from_date, $to_date);
		$staff_working_by_month = $this->report_staff_working_by_month($from_date, $to_date);
		$staff_quit_work_by_month = $this->report_staff_quit_work_by_month($from_date, $to_date);

		for($_month = 1 ; $_month <= 12; $_month++){
			$month_t = date('m',mktime(0, 0, 0, $_month, 04, 2016));

			if($_month == 5){
				$chart['categories'][] = _l('month_05');
			}else{
				$chart['categories'][] = _l('month_'.$_month);
			}


			$chart['hr_new_staff'][] = isset($new_staff_by_month[$month_t]) ? $new_staff_by_month[$month_t] : 0;
			$chart['hr_staff_are_working'][] = isset($staff_working_by_month[$month_t]) ? $staff_working_by_month[$month_t] : 0;
			$chart['hr_staff_quit'][] = isset($staff_quit_work_by_month[$month_t]) ? $staff_quit_work_by_month[$month_t] : 0;
		}

		return $chart;
	}


	/**
	 * [report_new_staff_by_month
	 * @param  [type] $month 
	 * @return [type]        
	 */
	public function report_new_staff_by_month($from_date ,$to_date)
	{
		$result =[];
		$this->db->select('count(staffid) as total_staff, date_format(datecreated, "%m") as datecreated');
		$sql_where = "deleted = 0 AND user_type = 'staff' AND date_format(datecreated, '%Y-%m-%d') >= '".$from_date."' AND date_format(datecreated, '%Y-%m-%d') <= '".$to_date."'";
		$this->db->where($sql_where);
		$this->db->group_by("date_format(datecreated, '%m')");
		$staffs = $this->db->get(get_db_prefix().'staff')->getResultArray();

		foreach ($staffs as $key => $value) {
			$result[$value['datecreated']] = (int)$value['total_staff'];
		}
		return $result;
		
	}


	/**
	 * report staff working by month
	 * @param  [type] $month 
	 * @return [type]        
	 */
	public function report_staff_working_by_month($from_date ,$to_date)
	{
		$result =[];
		$this->db->select('count(staffid) as total_staff, date_format(datecreated, "%m") as datecreated');

		$sql_where = "deleted = 0 AND user_type = 'staff' AND date_format(datecreated, '%Y-%m-%d') >= '".$from_date."' AND date_format(datecreated, '%Y-%m-%d') <= '".$to_date."' AND status_work = 'working'";
		$this->db->where($sql_where);
		$this->db->group_by("date_format(datecreated, '%m')");

		$staffs = $this->db->get(get_db_prefix().'staff')->getResultArray();

		foreach ($staffs as $key => $value) {
			$result[$value['datecreated']] = (int)$value['total_staff'];

		}
		return $result;

	}


	/**
	 * report staff quit work by month
	 * @param  [type] $month 
	 * @return [type]        
	 */
	public function report_staff_quit_work_by_month($from_date ,$to_date)
	{	
		$result =[];

		$this->db->select('count(id) as total_staff, date_format(dateoff, "%m") as datecreated');
		$sql_where = " date_format(dateoff, '%Y-%m') <= '".$to_date."' AND approval = 'approved'";
		$this->db->where($sql_where);
		$this->db->group_by("date_format(dateoff, '%m')");
		$quitting_works = $this->db->get(get_db_prefix().'hr_list_staff_quitting_work')->getResultArray();


		//
		$this->db->select('count(staffid) as total_staff, date_format(date_update, "%m") as datecreated');
		$sql_where1 = " status_work = 'inactivity' AND date_format(date_update, '%Y-%m') <= '".$to_date."' ";
		$this->db->where($sql_where1);
		$this->db->group_by("date_format(date_update, '%m')");
		$staffs = $this->db->get(get_db_prefix().'staff')->getResultArray();

		$arr_result =[];
		foreach ($quitting_works as $value) {
			if(isset($arr_result[$value['datecreated']])){
				$arr_result[$value['datecreated']] += (int)$value['total_staff'];
			}else{
				$arr_result[$value['datecreated']] = $value['total_staff'];
			}
		}

		foreach ($staffs as $value) {
			if(isset($arr_result[$value['datecreated']])){
				$arr_result[$value['datecreated']] += (int)$value['total_staff'];
			}else{
				$arr_result[$value['datecreated']] = $value['total_staff'];
			}
		}
		
		return $arr_result;

	}


	/**
	 * hr get training question form by relid
	 * @param  [type] $relid 
	 * @return [type]        
	 */
	public function hr_get_training_question_form_by_relid($rel_id)
	{
		$builder = $this->db->table(get_db_prefix().'hr_position_training_question_form');
		$builder->where('rel_id', $rel_id);
		$training_question_forms = $builder->get()->getResultArray();
		return $training_question_forms;
	}	


	/**
	 * hr get form results by resultsetid
	 * @param  [type] $resultsetid 
	 * @return [type]              
	 */
	public function hr_get_form_results_by_resultsetid($resultsetid, $questionid)
	{

		$boxdescriptionids =[];
		$builder = $this->db->table(get_db_prefix().'hr_p_t_form_results');

		$builder->where('resultsetid', $resultsetid);
		$builder->where('questionid', $questionid);
		$form_results = $builder->get()->getResultArray();

		foreach ($form_results as $value) {
			array_push($boxdescriptionids, $value['boxdescriptionid']);
		}
		return $boxdescriptionids;
	}


	/**
	 * delete hr article attachment file
	 * @param  [type] $attachment_id 
	 * @return [type]                
	 */
	public function delete_hr_article_attachment_file($attachment_id)
	{
		$deleted    = false;
		$attachment = $this->get_hr_profile_attachments_delete($attachment_id);
		if ($attachment) {
			if (empty($attachment->external)) {
				unlink(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id.'/'.$attachment->file_name);
			}
			$this->db->where('id', $attachment->id);
			$this->db->delete(get_db_prefix() . 'files');
			if ($this->db->affected_rows() > 0) {
				$deleted = true;
				log_activity('kb_article_files Attachment Deleted [kb_article_filesID: ' . $attachment->rel_id . ']');
			}

			if (is_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id)) {
				// Check if no attachments left, so we can delete the folder also
				$other_attachments = list_files(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
				if (count($other_attachments) == 0) {
					// okey only index.html so we can delete the folder also
					delete_dir(get_hr_profile_upload_path_by_type('kb_article_files') .$attachment->rel_id);
				}
			}
		}

		return $deleted;
	}

	/**
	 * get type of training
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_type_of_training($id = false){
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_type_of_trainings');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}

		if ($id == false) {
			return $this->db->query('select * from '.get_db_prefix().'hr_type_of_trainings order by id desc')->getResultArray();
		}

	}

	/**
	 * add type of training
	 * @param [type] $data 
	 */
	public function add_type_of_training($data)
	{

		$builder = $this->db->table(get_db_prefix().'hr_type_of_trainings');

		$builder->insert($data);
		$insert_id = $this->db->insertID();
		return $insert_id;
	}

	/**
	 * update type of training
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_type_of_training($data, $id)
	{   
		$builder = $this->db->table(get_db_prefix().'hr_type_of_trainings');

		$builder->where('id', $id);
		$affected_rows = $builder->update($data);
		if ($affected_rows > 0) {
			return true;
		}
		return false;
	}


	/**
	 * delete type of training
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_type_of_training($id)
	{
		$builder = $this->db->table(get_db_prefix().'hr_type_of_trainings');

		$builder->where('id', $id);
		$affected_rows = $builder->delete();
		if ($affected_rows > 0) {
			return true;
		}

		return false;
	}
	

	/**
	 * get list training program
	 * @param  [type] $position_id   
	 * @param  [type] $training_type 
	 * @return [type]                
	 */
	public function get_list_training_program($position_id, $training_type)
	{
		$options='';
		$first_id='';

		if($training_type != 0){

			$training_programs = $this->db->query('select * from '.get_db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id) and training_type = \''.$training_type.'\' ORDER BY date_add desc')->getResultArray();
		}else{
			$training_programs = $this->db->query('select * from '.get_db_prefix().'hr_jp_interview_training where find_in_set('.$position_id.',job_position_id)  ORDER BY date_add desc')->getResultArray();

		}

		foreach ($training_programs as $training_program) {
			if(!is_numeric($first_id)){
				$first_id = $training_program['training_process_id'];
			}
			$options .= '<option value="' . $training_program['training_process_id'] . '">' . $training_program['training_name'] . '</option>';
		}

		return ['options' => $options, 'first_id' => $first_id];
	}


	/**
	 * delete tranining result by staffid
	 * @param  [type] $staff_id 
	 * @return [type]           
	 */
	public function delete_tranining_result_by_staffid($staff_id)
	{	
		$affected_rows =0;
		$resultset_training = $this->get_resultset_training($staff_id);
		if($resultset_training){
			$this->db->where('resultsetid', $resultset_training->resultsetid);
			$this->db->delete(get_db_prefix().'hr_p_t_form_results');
			if ($this->db->affected_rows() > 0) {
				$affected_rows++;
			}
		}

		$this->db->where('staff_id', $staff_id);
		$this->db->delete(get_db_prefix().'hr_p_t_surveyresultsets');

		if ($this->db->affected_rows() > 0) {
			$affected_rows++;
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}

	/**
	 * get additional training
	 * @param  [type] $staff_id 
	 * @return [type]           
	 */
	public function get_additional_training($staff_id)
	{
		$sql_where ='find_in_set("'.$staff_id.'", staff_id)';
		$builder = $this->db->table(get_db_prefix().'hr_jp_interview_training');
		$builder->where($sql_where);
		$builder->orderBy('training_process_id', 'desc');
		$interview_trainings = $builder->get()->getResultArray();

		return $interview_trainings;
	}


	/**
	 * get mark staff from resultsetid
	 * @param  [type] $resultsetid 
	 * @return [type]              
	 */
	public function get_mark_staff_from_resultsetid($resultsetid, $id, $staff_id)
	{

		$result_data=[];
		$array_training_point=[];
		$training_program_point=0;

		//Get the latest employee's training result.
		$trainig_resultset = $this->db->query('select * from '.get_db_prefix().'hr_p_t_surveyresultsets where resultsetid = \''.$resultsetid.'\'')->getResultArray();

		$array_training_resultset = [];
		$array_resultsetid = [];
		$list_resultset_id='';

		foreach ($trainig_resultset as $item) {
			if(count($array_training_resultset)==0){
				array_push($array_training_resultset, $item['trainingid']);
				array_push($array_resultsetid, $item['resultsetid']);

				$list_resultset_id.=''.$item['resultsetid'].',';
			}
			if(!in_array($item['trainingid'], $array_training_resultset)){
				array_push($array_training_resultset, $item['trainingid']);
				array_push($array_resultsetid, $item['resultsetid']);

				$list_resultset_id.=''.$item['resultsetid'].',';
			}
		}

		$list_resultset_id = rtrim($list_resultset_id,",");
		$count_out = 0;
		if($list_resultset_id==""){
			$list_resultset_id = '0';
		}else{
			$count_out = count($array_training_resultset);
		}

		$array_result = [];
		foreach ($array_training_resultset as $key => $training_id) {
			$total_question = 0;
			$total_question_point = 0;

			$total_point = 0;
			$training_library_name = '';
			$training_question_forms = $this->hr_get_training_question_form_by_relid($training_id);
			$hr_position_training = $this->get_board_mark_form($training_id);
			$total_question = count($training_question_forms);
			if($hr_position_training){
				$training_library_name .= $hr_position_training->subject;
			}
			foreach ($training_question_forms as $question) {
				$flag_check_correct = true;

				$get_id_correct = $this->get_id_result_correct($question['questionid']);
				$form_results = $this->hr_get_form_results_by_resultsetid($array_resultsetid[$key], $question['questionid']);

				$result_data[$question['questionid']] = [
					'array_id_correct' => $get_id_correct,
					'form_results' => $form_results
				];


				if(count($get_id_correct) == count($form_results)){
					foreach ($get_id_correct as $correct_key => $correct_value) {
						if(!in_array($correct_value, $form_results)){
							$flag_check_correct = false;
						}
					}
				}else{
					$flag_check_correct = false;
				}

				$result_point = $question['point'];
				$total_question_point += $result_point;

				if($flag_check_correct == true){
					$total_point += $result_point;
					$training_program_point += $result_point;
				}
				
			}

			array_push($array_training_point, [
				'training_name' => $training_library_name,
				'total_point'	=> $total_point,
				'training_id'	=> $training_id,
				'total_question'	=> $total_question,
				'total_question_point'	=> $total_question_point,
			]);
		}

		$response = [];
		$response['training_program_point'] = $training_program_point;
		$response['staff_training_result'] = $array_training_point;
		$response['result_data'] = $result_data;
		$response['staff_name'] = get_staff_full_name1($staff_id);
		return $response;
	}


	/**
	 * get training library
	 * @return [type] 
	 */
	public function get_training_library()
	{
		$builder = $this->db->table(get_db_prefix().'hr_position_training');
		$builder->orderBy('datecreated', 'desc');
		$rs = $builder->get()->getResultArray();
		return  $rs;
	}

	/**
	 * get training result by training program
	 * @param  [type] $training_program_id 
	 * @return [type]                      
	 */
	public function get_training_result_by_training_program($training_program_id)
	{
		$data=[];
		$training_results=[];

		$training_program = $this->get_job_position_training_de($training_program_id);

		if($training_program){
			$training_library = $training_program->position_training_id;

			if($training_program->additional_training == 'additional_training'){
				$staff_ids = $training_program->staff_id;
			}else{
				//get list staff by job position
				$builder = $this->db->table(get_db_prefix().'users');
				$builder->where('job_position IN ('. $training_program->job_position_id.') ');
				$builder->select('*');
				$staffs = $builder->get()->getResultArray();

				$arr_staff_id =[];
				$staff_ids = '';
				foreach ($staffs as $value) {
					$arr_staff_id[] = $value['id'];
				}

				if(count($arr_staff_id) > 0){
					$staff_ids = implode(',', $arr_staff_id);
				}
			}

			if(strlen($staff_ids) > 0){
				//get training result by staff and training library
				$sql_where="SELECT * FROM ".get_db_prefix()."hr_p_t_surveyresultsets
				where  trainingid IN (". $training_library.") AND staff_id IN (". $staff_ids.")
				order by date asc
				";
				$results = $this->db->query($sql_where)->getResultArray();

				foreach ($results as $value) {
					$training_results[$value['staff_id'].$value['trainingid']] = $value;
				}
				
			}

			foreach ($training_results as $training_result) {

				$training_temp=[];

					//Get the latest employee's training result.
				$get_mark_staff=$this->get_mark_staff_v2($training_result['trainingid'], $training_result['resultsetid']);

				if(count($get_mark_staff['staff_training_result']) > 0){
					$get_mark_staff['staff_id'] = $training_result['staff_id'];

					$get_mark_staff['staff_training_result'][0]['staff_id'] = $training_result['staff_id'];
					$get_mark_staff['staff_training_result'][0]['resultsetid'] = $training_result['resultsetid'];
					$get_mark_staff['staff_training_result'][0]['hash'] = hr_get_training_hash($training_result['trainingid']);
					$get_mark_staff['staff_training_result'][0]['date'] = $training_result['date'];

					if(isset($data[$get_mark_staff['staff_training_result'][0]['staff_id']])){
						$data[$training_result['staff_id']]['staff_training_result'][] = $get_mark_staff['staff_training_result'][0];
						$data[$training_result['staff_id']]['training_program_point'] += (float)$get_mark_staff['training_program_point'];
					}else{
						$data[$training_result['staff_id']] = $get_mark_staff;
					}

				}

			}
		}

		return $data;
	}

	/**
	 * get mark staff v2
	 * @param  [type] $id_staff            
	 * @param  [type] $training_process_id 
	 * @return [type]                      
	 */
	public function get_mark_staff_v2($trainingid, $resultsetid){
		$array_training_point=[];
		$training_program_point=0;


		$array_training_resultset = [];
		$array_resultsetid = [];
		$list_resultset_id='';

		if(count($array_training_resultset)==0){
			array_push($array_training_resultset, $trainingid);
			array_push($array_resultsetid, $resultsetid);

			$list_resultset_id.=''.$resultsetid.',';
		}
		if(!in_array($trainingid, $array_training_resultset)){
			array_push($array_training_resultset, $trainingid);
			array_push($array_resultsetid, $resultsetid);

			$list_resultset_id.=''.$resultsetid.',';
		}

		$list_resultset_id = rtrim($list_resultset_id,",");
		$count_out = 0;
		if($list_resultset_id==""){
			$list_resultset_id = '0';
		}else{
			$count_out = count($array_training_resultset);
		}


		$array_result = [];
		foreach ($array_training_resultset as $key => $training_id) {
			$total_question = 0;
			$total_question_point = 0;

			$total_point = 0;
			$training_library_name = '';
			$training_question_forms = $this->hr_get_training_question_form_by_relid($training_id);
			$hr_position_training = $this->get_board_mark_form($training_id);
			$total_question = count($training_question_forms);
			if($hr_position_training){
				$training_library_name .= $hr_position_training->subject;
			}

			foreach ($training_question_forms as $question) {
				$flag_check_correct = true;

				$get_id_correct = $this->get_id_result_correct($question['questionid']);
				$form_results = $this->hr_get_form_results_by_resultsetid($array_resultsetid[$key], $question['questionid']);

				if(count($get_id_correct) == count($form_results)){
					foreach ($get_id_correct as $correct_key => $correct_value) {
						if(!in_array($correct_value, $form_results)){
							$flag_check_correct = false;
						}
					}
				}else{
					$flag_check_correct = false;
				}

				$result_point = $this->get_point_training_question_form($question['questionid']);
				$total_question_point += $result_point->point;

				if($flag_check_correct == true){
					$total_point += $result_point->point;
					$training_program_point += $result_point->point;
				}
				
			}

			array_push($array_training_point, [
				'training_name' => $training_library_name,
				'total_point'	=> $total_point,
				'training_id'	=> $training_id,
				'total_question'	=> $total_question,
				'total_question_point'	=> $total_question_point,
			]);
		}

		$response = [];
		$response['training_program_point'] = $training_program_point;
		$response['staff_training_result'] = $array_training_point;

		return $response;
	}

	/**
	 * get staff from training program
	 * @param  [type] $training_programs 
	 * @return [type]                    
	 */
	public function get_staff_from_training_program($training_programs)
	{

		$sql_where = 'training_process_id IN ("'.implode(",", $training_programs).'")';
		$this->db->where($sql_where);
		$training_programs = $this->db->get(get_db_prefix().'hr_jp_interview_training')->getResultArray();

		$arr_staff_id=[];
		foreach ($training_programs as $training_program) {
			if($training_program['additional_training'] == 'additional_training'){
				$training_program_staff=explode(',', $training_program['staff_id']);

				foreach ($training_program_staff as $training_staff_id) {
					if(!in_array($training_staff_id, $arr_staff_id)){

						$arr_staff_id[] = $training_staff_id;
					}
				}

			}else{
				//get list staff by job position

				$this->db->where('job_position in ('. $training_program['job_position_id'].') ');
				$this->db->select('*');
				$staffs = $this->db->get(get_db_prefix().'staff')->getResultArray();

				foreach ($staffs as $value) {
					if(!in_array($value['staffid'], $arr_staff_id)){

						$arr_staff_id[] = $value['staffid'];
					}
				}
			}
		}

		if(count($arr_staff_id) > 0){
			return implode(',', $arr_staff_id);
		}else{
			return '';
		}

	}

	/**
	 * get department by manager
	 * @return [type] 
	 */
	public function get_department_by_manager()
	{
		$department_ids=[];

		$builder = $this->db->table(get_db_prefix().'team');
		$builder->where('manager_id', get_staff_user_id1());
		$departments = $builder->get()->getResultArray();
		foreach ($departments as $department) {
			$department_id =  $this->get_staff_in_deparment($department['id']);
			$department_ids = array_merge($department_ids, $department_id);
		}

		$department_ids = array_unique($department_ids);

		return $department_ids;
	}

	/**
	 * get staff by manager
	 * @return [type] 
	 */
	public function get_staff_by_manager()
	{
		$staff_id=[];

		//get staff by deparment
		$department_id = $this->get_department_by_manager();
		if(count($department_id) > 0){
			$builder = $this->db->table(get_db_prefix().'team');

			$builder->where('id IN ('.implode(",", $department_id) .') ');
			$staff_departments = $builder->get()->getResultArray();
			foreach ($staff_departments as $staff_department) {
				if($staff_department['members'] != '' && strlen($staff_department['members']) > 0 ){
					$staff_id = array_merge($staff_id, explode(",", $staff_department['members']));
				}
			}
		}

		$staff_id = array_unique($staff_id);

		//get staff by manager with children

		$builder = $this->db->table(get_db_prefix().'users');
		$builder->where('team_manage', get_staff_user_id1());
		$builder->orWhere('id', get_staff_user_id1());
		$staffs = $builder->get()->getResultArray();
		foreach ($staffs as $staff) {
			$staff_by_manager =  $this->get_staff_in_teammanage($staff['id']);
			$staff_id = array_merge($staff_id, $staff_by_manager);
		}
		//remove same staffid
		$staff_id = array_unique($staff_id);

		return $staff_id;
	}

	/**
	 * get staff in teammanage
	 * @param  [type] $teammanage 
	 * @return [type]             
	 */
	public function get_staff_in_teammanage($teammanage)
	{

		$data =[];
		$sql = 'select 
		id 
		from    (select * from '.get_db_prefix().'users
		order by '.get_db_prefix().'users.team_manage, '.get_db_prefix().'users.id) teammanage_sorted,
		(select @pv := '.$teammanage.') initialisation
		where   find_in_set(team_manage, @pv)
		and     length(@pv := concat(@pv, ",", id)) OR id = '.$teammanage.'';
		
		$result_arr = $this->db->query($sql)->getResultArray();
		foreach ($result_arr as $key => $value) {
			$data[$key] = $value['id'];
		}

		return $data;
	}

	/**
	 * get staff by job position
	 * @param  [type] $job_position_id 
	 * @return [type]                  
	 */
	public function get_staff_by_job_position($job_position_id)
	{
		$staff_id=[];

		$builder = $this->db->table(get_db_prefix().'users');
		$builder->where('job_position IN ('.$job_position_id .') ');
		$staffs = $builder->get()->getResultArray();
		foreach ($staffs as $staff) {
			$staff_id[] = $staff['id'];
		}

		return $staff_id;   
	}

	/**
	 * contract clear signature
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function contract_clear_signature($id)
	{
		$this->db->select('signature');
		$this->db->where('id_contract', $id);
		$contract = $this->db->get(get_db_prefix() . 'hr_staff_contract')->getRow();

		if ($contract) {

			$this->db->where('id_contract', $id);
			$this->db->update(get_db_prefix() . 'hr_staff_contract', ['signature' => null]);

			if (!empty($contract->signature)) {
				unlink(HR_PROFILE_CONTRACT_SIGN .$contract->id_contract.'/'.$contract->signature);
			}

			return true;
		}

		return false;
	}

	public function hr_get_staff_contract_pdf($id = '', $where = [], $for_editor = false)
	{
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
		$builder->where($where);
		$builder->join(get_db_prefix() . 'hr_staff_contract_type', '' . get_db_prefix() . 'hr_staff_contract_type.id_contracttype = ' . get_db_prefix() . 'hr_staff_contract.name_contract', 'left');
		$builder->join(get_db_prefix() . 'users', '' . get_db_prefix() . 'users.id = ' . get_db_prefix() . 'hr_staff_contract.staff');

		if (is_numeric($id)) {
			$builder->where(get_db_prefix() . 'hr_staff_contract.id_contract', $id);
			$contract = $builder->get()->getRow();
			if ($contract) {

				if ($for_editor == false) {
					$staff_contract_variables = staff_contract_variables();

					$logo_url = '';
					$signature = '';

					//staff
					$signature .= '<div class="row">';
					$signature .= '<div class="col-md-6  text-left">';
					$signature .= '<p class="bold ">'. app_lang('staff_signature');

					$signature .= '<div class="bold">';

					if(is_numeric($contract->staff)){
						$contracts_staff_signer = get_staff_full_name1($contract->staff);
					}else {
						$contracts_staff_signer = ' ';
					}


					$signature .= '<p class="no-mbot">'. app_lang('contract_signed_by') . ': '.$contracts_staff_signer.'</p>';
					$signature .= '<p class="no-mbot">'. app_lang('contract_signed_date') . ': ' . _d($contract->staff_sign_day) .'</p>';
					$signature .= '</div>';
					$signature .= '<p class="bold">'. app_lang('hr_signature_text');

					$signature .= '</p>';
					$signature .= '<div class="pull-left">';
					if(strlen($contract->staff_signature) > 0){

						if (file_exists(HR_PROFILE_CONTRACT_SIGN . $contract->id_contract . '/staff_signature.png') ){ 

							$signature .= '<img src="'. base_url('plugins/Hr_profile/Uploads/contract_sign/'.$contract->id_contract.'/staff_signature.png').'" class="img-responsive">';
						}else{ 
							$signature .= '<img src="'. base_url('plugins/Hr_profile/Uploads/image_not_available.jpg').'" class="img-responsive">';
						} 
					}else{
						$signature .= '<img src="" class="img-responsive" alt="">';

					}

					$signature .= '</div>';
					$signature .= '</div>';

					//company
					$signature .= '<div class="col-md-6  text-right">';
					$signature .= '<p class="bold">'. app_lang('company_signature');

					$signature .= '<div class="bold">';

					if(is_numeric($contract->signer)){
						$contracts_signer = get_staff_full_name1($contract->signer);
					}else {
						$contracts_signer = ' ';
					}


					$signature .= '<p class="no-mbot">'. app_lang('contract_signed_by') . ': '.$contracts_signer.'</p>';
					$signature .= '<p class="no-mbot">'. app_lang('contract_signed_date') . ': ' . _d($contract->sign_day) .'</p>';
					$signature .= '</div>';
					$signature .= '<p class="bold">'. app_lang('hr_signature_text');

					$signature .= '</p>';
					$signature .= '<div class="pull-right">';
					if(strlen($contract->signature) > 0){

						if (file_exists(HR_PROFILE_CONTRACT_SIGN . $contract->id_contract . '/signature.png') ){ 
							$signature .= '<img src="'.base_url('plugins/Hr_profile/Uploads/contract_sign/'.$contract->id_contract.'/signature.png').'" class="img-responsive">';

						}else{ 
							$signature .= '<img src="'.base_url('plugins/Hr_profile/Uploads/image_not_available.jpg').'" class="img-responsive">';
						} 
					}else{

						$signature .= '<img src="" class="img-responsive" alt="">';
					}

					$signature .= '</div>';
					$signature .= '</div>';
					$signature .= '</div>';


					foreach ($staff_contract_variables as $key) {

						if($key == '{LOGO_URL}'){
							$val = '<img src="'.get_file_from_setting("invoice_logo", true).'" />';
							
						}elseif($key == '{COMPANY_NAME}'){
							$val = get_default_company_name(get_default_company_id());
						}elseif($key == '{SIGNATURE}'){
							$val = $signature;
						}else{
							$val = staff_contract_map_variables($key, $contract);
						}

						if (stripos($contract->content, $key) !== false) {
							$contract->content = str_ireplace($key, $val, $contract->content);
						} else {
							$contract->content = str_ireplace($key, '', $contract->content);
						}
					}



				}
			}

			return $contract;
		}
		$contracts = $builder->get()->getResultArray();

		return $contracts;
	}

	/**
	 * hr_get_staff_contract_pdf_only_for_pdf
	 * @param  string  $id         
	 * @param  array   $where      
	 * @param  boolean $for_editor 
	 * @return [type]              
	 */
	public function hr_get_staff_contract_pdf_only_for_pdf($id = '', $where = [], $for_editor = false)
	{
		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');
		$builder->where($where);
		$builder->join(get_db_prefix() . 'hr_staff_contract_type', '' . get_db_prefix() . 'hr_staff_contract_type.id_contracttype = ' . get_db_prefix() . 'hr_staff_contract.name_contract', 'left');
		$builder->join(get_db_prefix() . 'users', '' . get_db_prefix() . 'users.id = ' . get_db_prefix() . 'hr_staff_contract.staff');

		if (is_numeric($id)) {
			$builder->where(get_db_prefix() . 'hr_staff_contract.id_contract', $id);
			$contract = $builder->get()->getRow();
			if ($contract) {

				if ($for_editor == false) {
					
					$staff_contract_variables = staff_contract_variables();

					$logo_url = '';
					$signature = '';

					if(is_numeric($contract->staff)){
						$contracts_staff_signer = get_staff_full_name1($contract->staff);
					}else {
						$contracts_staff_signer = ' ';
					}

					if(is_numeric($contract->signer)){
						$contracts_signer = get_staff_full_name1($contract->signer);
					}else {
						$contracts_signer = ' ';
					}

					$signature .= '<table class="table">
					<tbody>

					<tr>
					<td  width="50%" class="text-left"><b>'. _l('staff_signature').'</b></td>
					<td width="50%" class="text_right"><b>'. _l('company_signature').'</b></td>
					</tr>

					<tr>
					<td  width="50%" class="text-left"><b>'. _l('contract_signed_by') . '</b>: '.$contracts_staff_signer.'</td>
					<td  width="50%" class="text_right"><b>'. _l('contract_signed_by') . '</b>: '.$contracts_signer.'</td>
					</tr>

					<tr>
					<td  width="50%" class="text-left"><b>'.  _l('contract_signed_date') . '</b>: ' . _d($contract->staff_sign_day) .'</td>
					<td  width="50%" class="text_right"><b>'. _l('contract_signed_date') . '</b>: ' . _d($contract->sign_day).'</td>
					</tr>

					<tr>';
					if(strlen($contract->staff_signature) > 0){

						$signature .= '<td  width="50%" class="text-left">';
						if(strlen($contract->staff_signature) > 0){

							if (file_exists(HR_PROFILE_CONTRACT_SIGN . $contract->id_contract . '/staff_signature.png') ){ 

								$signature .= '<img src="'. base_url('plugins/Hr_profile/Uploads/contract_sign/'.$contract->id_contract.'/staff_signature.png').'" class="img-responsive">';
							}else{ 
								$signature .= '<img src="'. base_url('plugins/Hr_profile/Uploads/image_not_available.jpg').'" class="img-responsive">';
							} 
						}else{
							$signature .= '<img src="" class="img-responsive" alt="">';

						}

						$signature .= '</td>';
					}else{
						$signature .= '<td  width="50%" class="text-left"><img src="" class="img-responsive" alt=""></td>';
					}

					if(strlen($contract->signature) > 0){
						$signature .='<td  width="50%" class="text_right">';
						if(strlen($contract->signature) > 0){

							if (file_exists(HR_PROFILE_CONTRACT_SIGN . $contract->id_contract . '/signature.png') ){ 
								$signature .= '<img src="'.base_url('plugins/Hr_profile/Uploads/contract_sign/'.$contract->id_contract.'/signature.png').'" class="img-responsive">';

							}else{ 
								$signature .= '<img src="'.base_url('plugins/Hr_profile/Uploads/image_not_available.jpg').'" class="img-responsive">';
							} 
						}else{

							$signature .= '<img src="" class="img-responsive" alt="">';
						}

						
						$signature .='</tr>';
					}else{
						$signature .='<td  width="50%" class="text_right"><img src="" class="img-responsive" alt=""></td>
						</tr>';

					}


					$contract->content .='</tbody>
					</table>';

					foreach ($staff_contract_variables as $key) {

						if($key == '{LOGO_URL}'){
							$val = '<img src="'.get_file_from_setting("invoice_logo", true).'" />';
							
						}elseif($key == '{COMPANY_NAME}'){
							$val = get_default_company_name(get_default_company_id());
						}elseif($key == '{SIGNATURE}'){
							$val = $signature;
						}else{
							$val = staff_contract_map_variables($key, $contract);
						}

						if (stripos($contract->content, $key) !== false) {
							$contract->content = str_ireplace($key, $val, $contract->content);
						} else {
							$contract->content = str_ireplace($key, '', $contract->content);
						}
					}
					
					$contract->content  .= '<link href="' . base_url('plugins/Hr_profile/assets/css/pdf_style.css') . '?v=' . HR_PROFILE_REVISION. '"  rel="stylesheet" type="text/css" />';

				}
			}

			return $contract;
		}
		$contracts = $builder->get()->getResultArray();

		return $contracts;
	}

	/**
	 * get contract template
	 * @param  boolean $id 
	 * @return [type]      
	 */
	public function get_contract_template($id = false)
	{
		if (is_numeric($id)) {
			$builder = $this->db->table(get_db_prefix().'hr_contract_template');
			$builder->where('id', $id);
			return $builder->get()->getRow();
		}
		if ($id == false) {
			$builder = $this->db->table(get_db_prefix().'hr_contract_template');
			return  $builder->get()->getResultArray();
		}

	}

	/**
	 * add contract template
	 * @param [type] $data 
	 */
	public function add_contract_template($data){
		$data['content'] = $data['content'];
		$data['job_position'] = implode(',', $data['job_position']);

		$builder = $this->db->table(get_db_prefix().'hr_contract_template');

		$builder->insert($data);
		$insert_id = $this->db->insertID();

		if ($insert_id) {
			return $insert_id;
		}
		return false;
	}

	/**
	 * update contract template
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function update_contract_template($data, $id)
	{   
		$data['content'] = $data['content'];
		$data['job_position'] = implode(',', $data['job_position']);

		$builder = $this->db->table(get_db_prefix().'hr_contract_template');

		$builder->where('id', $id);
		$affectedRows = $builder->update($data);

		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * delete contract template 
	 * @param  [type] $id [
	 * @return [type]     [
	 */
	public function delete_contract_template($id){
		$builder = $this->db->table(get_db_prefix().'hr_contract_template');

		$builder->where('id', $id);
		$affectedRows = $builder->delete();

		if ($affectedRows > 0) {
			return true;
		}

		return false;
	}

	/**
	 * hr get contract template by staff
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	public function hr_get_contract_template_by_staff($staffid)
	{	
		$content ='';
		$staff = $this->get_staff($staffid);
		if($staff){
			if( is_numeric($staff->job_position) && $staff->job_position != 0 && $staff->job_position != '' ){

				$sql_where ='find_in_set("'.$staff->job_position.'", job_position)';

				$builder = $this->db->table(get_db_prefix().'hr_contract_template');

				$builder->where($sql_where);
				$builder->orderBy('id', 'desc');
				$contract_template = $builder->get()->getRow();

				if($contract_template){
					$content = $contract_template->content;
				}
			}
		}

		return $content;
	}

	/**
	 * update hr staff contract content
	 * @param  [type] $id      
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	function update_hr_staff_contract_content($id, $staffid)
	{
		$content = $this->hr_get_contract_template_by_staff($staffid);

		$builder = $this->db->table(get_db_prefix().'hr_staff_contract');

		$builder->where('id_contract', $id);
		$affectedRows = $builder->update(['content' => $content]);

		if ($affectedRows > 0) {
			return true;
		}
		return false;
	}

	/**
	 * add_department
	 * @param [type] $data 
	 */
	public function add_department($data)
	{
		$builder = $this->db->table(get_db_prefix().'team');
		$builder->insert($data);
		$insert_id = $this->db->insertID();
		if (isset($insert_id)) {
			return $insert_id;
		}
		return false;
	}

	/**
	 * update department
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function update_department($data, $id)
	{

		$builder = $this->db->table(get_db_prefix().'team');
		$builder->where('id', $id);
		$affectedrows = $builder->update($data);
		if ($affectedrows > 0) {
			return $id;
		}
		return false;
	}

	/**
	 * delete dependent
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function delete_department($id)
	{
		$builder = $this->db->table(get_db_prefix().'team');
		$builder->where('id', $id);
		$affectedRows = $builder->update(['deleted' => 1]);
		if ($affectedRows > 0) {

			return true;
		}
		return false;
	}
	
	/**
	 * add staff into department
	 * @param [type] $departments 
	 * @param [type] $staff_id    
	 */
	public function add_staff_into_department($departments, $staff_id)
	{	
		$affected_rows = 0;
		/*get old departments*/
		$staff_departments = $this->get_staff_departments($staff_id, true);

		foreach ($departments as $department_id) {
			if(in_array($department_id, $staff_departments)){
				foreach ($staff_departments as $key => $old_department_id) {
				    if($department_id == $old_department_id){
				    	unset($staff_departments[$key]);
				    }
				}
			}

			$builder = $this->db->table(get_db_prefix().'team');
			$builder->where('id', $department_id);
			$team = $builder->get()->getRow();

			if($team){
				$members = $team->members.','.$staff_id;
				/*remore empty, or ","*/
				$members = explode(',', $members);

				if(isset($members[count($members)-1]) && (strlen($members[count($members)-1]) == 0 || $members[count($members)-1] == ',')){
					unset($members[count($members)-1]);
				}

				if(isset($members[0]) && strlen($members[0]) ==0){
					unset($members[0]);
				}

				$members = array_unique($members);
				$members = implode(',', $members);


				$builder = $this->db->table(get_db_prefix().'team');
				$builder->where('id', $department_id);
				$affectedRows = $builder->update(['members' => $members]);
				if ($affectedRows > 0) {
					$affected_rows++;
				}
			}
		}


		if(count($staff_departments) > 0){
			foreach ($staff_departments as $department_id) {
				$builder = $this->db->table(get_db_prefix().'team');
				$builder->where('id', $department_id);
				$team = $builder->get()->getRow();

				if($team){
					$members = str_replace(',,', '', $team->members);

					$members_arr = explode(',', $members);
					foreach ($members_arr as $key => $value) {
					    if($value == ',' || !is_numeric($value) || $value == $staff_id){
					    	unset($members_arr[$key]);
					    }
					}

					/*remore empty, or ","*/
					if(isset($members_arr[count($members_arr)-1]) && ( strlen($members_arr[count($members_arr)-1]) == 0 || $members_arr[count($members_arr)-1] == ',' )){
						unset($members_arr[count($members_arr)-1]);
					}

					if(isset($members_arr[0]) && strlen($members_arr[0]) ==0){
						unset($members_arr[0]);
					}

					$members_arr = array_unique($members_arr);
					$members_str = implode(',', $members_arr);

					$builder = $this->db->table(get_db_prefix().'team');
					$builder->where('id', $department_id);
					$affectedRows = $builder->update(['members' => $members_str]);
					if ($affectedRows > 0) {
						$affected_rows++;
					}
				}
			    
			}
		}

		if($affected_rows > 0){
			return true;
		}
		return false;
	}

	/**
	 * import add staff
	 * @param  [type] $data 
	 * @return [type]       
	 */
	public function import_add_staff($data)
	{
		$affectedRows = 0;

		$Users_model = model("Models\Users_model");
		$Social_links_model = model("Models\Social_links_model");
		$Email_templates_model = model("Models\Email_templates_model");

		$password = $data["password"];
		$job_title = hr_profile_get_job_position_name($data['job_position']);

		$user_data = array(
			"email" => isset($data['email']) ? $data['email'] : null,
			"first_name" => isset($data['first_name']) ? $data['first_name'] : '',
			"last_name" => isset($data['last_name']) ? $data['last_name'] : '',
			"is_admin" => 0,
			"address" => isset($data['address']) ? $data['address'] : null,
			"phone" => isset($data['phone']) ? $data['phone'] : null,
			"gender" => isset($data['gender']) && strlen($data['gender']) > 0 ? $data['gender'] : null,
			"job_title" => $job_title,
			"phone" => isset($data['phone']) ? $data['phone'] : null,
			"user_type" => "staff",
			"created_at" => get_current_utc_time(),
			"staff_identifi" => isset($data['staff_identifi']) ? $data['staff_identifi'] : null,
			"team_manage" => isset($data['team_manage']) ? $data['team_manage'] : null,
			"workplace" => isset($data['workplace']) ? $data['workplace'] : null,
			"status_work" => isset($data['status_work']) ? $data['status_work'] : null,
			"job_position" => isset($data['job_position']) ? $data['job_position'] : null,
			"literacy" => isset($data['literacy']) ? $data['literacy'] : null,
			"marital_status" => isset($data['marital_status']) ? $data['marital_status'] : null,
			"account_number" => isset($data['account_number']) ? $data['account_number'] : null,
			"name_account" => isset($data['name_account']) ? $data['name_account'] : null,
			"issue_bank" => isset($data['issue_bank']) ? $data['issue_bank'] : null,
			"Personal_tax_code" => isset($data['Personal_tax_code']) ? $data['Personal_tax_code'] : null,
			"nation" => isset($data['nation']) ? $data['nation'] : null,
			"religion" => isset($data['religion']) ? $data['religion'] : null,
			"identification" => isset($data['identification']) ? $data['identification'] : null,
			"days_for_identity" => isset($data['days_for_identity']) && $data['days_for_identity'] != '' ? $data['days_for_identity'] : null,
			"home_town" => isset($data['home_town']) ? $data['home_town'] : null,
			"resident" => isset($data['resident']) ? $data['resident'] : null,
			"address" => isset($data['address']) ? $data['address'] : null,
			"orther_infor" => isset($data['orther_infor']) ? $data['orther_infor'] : null,
			"hourly_rate" => isset($data['hourly_rate']) ? $data['hourly_rate'] : '0.00',
			"dob" => isset($data['dob']) && $data['dob'] != '' ? $data['dob'] : null,
			"birthplace" => isset($data['birthplace']) ? $data['birthplace'] : null,
			"place_of_issue" => isset($data['place_of_issue']) ? $data['place_of_issue'] : null,
		);

		if ($password) {
			$user_data["password"] = password_hash($password, PASSWORD_DEFAULT);
		}

        		//make role id or admin permission 
		$role = $data['role'];
		$role_id = $role;
		//import default not is admin
		$user_data["is_admin"] = 0;
		$user_data["role_id"] = $role_id;

		$ci = new Security_Controller(false);

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

			/*save departments*/
			if(isset($data['departments']) && null !== $data['departments']){
				$departments = $data['departments'] ? $data['departments'] : null;
				if($departments != null && count($departments) > 0){
					if($this->add_staff_into_department($departments, $user_id)){
						$affectedRows++;
					}
				}
			}

            		//send login details to user
			if (isset($data['email_login_details']) && null !== $data['email_login_details']) {

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
	 * update staff
	 * @param  [type] $data 
	 * @param  [type] $id   
	 * @return [type]       
	 */
	public function import_update_staff($data, $id)
	{
		$affectedRows = 0;

		$Users_model = model("Models\Users_model");
		$Social_links_model = model("Models\Social_links_model");
		$Email_templates_model = model("Models\Email_templates_model");

		$job_title = hr_profile_get_job_position_name($data['job_position']);

		$user_data = array(
			"email" => isset($data['email']) ? $data['email'] : null,
			"first_name" => isset($data['first_name']) ? $data['first_name'] : '',
			"last_name" => isset($data['last_name']) ? $data['last_name'] : '',
			"address" => isset($data['address']) ? $data['address'] : null,
			"phone" => isset($data['phone']) ? $data['phone'] : null,
			"gender" => isset($data['gender']) && strlen($data['gender']) > 0 ? $data['gender'] : null,
			"job_title" => $job_title,
			"phone" => isset($data['phone']) ? $data['phone'] : null,
			"team_manage" => isset($data['team_manage']) ? $data['team_manage'] : null,
			"workplace" => isset($data['workplace']) ? $data['workplace'] : null,
			"status_work" => isset($data['status_work']) ? $data['status_work'] : null,
			"job_position" => isset($data['job_position']) ? $data['job_position'] : null,
			"literacy" => isset($data['literacy']) ? $data['literacy'] : null,
			"marital_status" => isset($data['marital_status']) ? $data['marital_status'] : null,
			"account_number" => isset($data['account_number']) ? $data['account_number'] : null,
			"name_account" => isset($data['name_account']) ? $data['name_account'] : null,
			"issue_bank" => isset($data['issue_bank']) ? $data['issue_bank'] : null,
			"Personal_tax_code" => isset($data['Personal_tax_code']) ? $data['Personal_tax_code'] : null,
			"nation" => isset($data['nation']) ? $data['nation'] : null,
			"religion" => isset($data['religion']) ? $data['religion'] : null,
			"identification" => isset($data['identification']) ? $data['identification'] : null,
			"days_for_identity" => isset($data['days_for_identity']) && $data['days_for_identity'] != '' ? $data['days_for_identity'] : null,
			"home_town" => isset($data['home_town']) ? $data['home_town'] : null,
			"resident" => isset($data['resident']) ? $data['resident'] : null,
			"address" => isset($data['address']) ? $data['address'] : null,
			"orther_infor" => isset($data['orther_infor']) ? $data['orther_infor'] : null,
			"hourly_rate" => isset($data['hourly_rate']) ? $data['hourly_rate'] : '0.00',
			"dob" => isset($data['dob']) && $data['dob'] != '' ? $data['dob'] : null,
			"birthplace" => isset($data['birthplace']) ? $data['birthplace'] : null,
			"place_of_issue" => isset($data['place_of_issue']) ? $data['place_of_issue'] : null,
		);

        		//make role id or admin permission 
		$role = $data['role'];
		$role_id = $role;
		$user_data["role_id"] = $role_id;

        //update a new team member
		$user_id = $Users_model->ci_save($user_data, $id);

		if ($id) {
			$affectedRows++;

			/*update departments*/
			if(isset($data['departments']) && null !== $data['departments']){
				$departments = $data['departments'] ? $data['departments'] : null;
				if($departments != null && count($departments) > 0){
					if($this->add_staff_into_department($departments, $id)){
						$affectedRows++;
					}
				}
			}
		}

		if($affectedRows > 0){
			return $id;
		}
		return false;
	}

	/**
	 * hr_create_notification
	 * @param  [type]  $event      
	 * @param  [type]  $user_id    
	 * @param  array   $options    
	 * @param  integer $to_user_id 
	 * @return [type]              
	 */
	function hr_create_notification($event, $user_id, $options = array(), $to_user_id = 0) {
		$notification_settings_table = $this->db->prefixTable('notification_settings');
		$users_table = $this->db->prefixTable('users');
		$roles_table = $this->db->prefixTable('roles');
		$clients_table = $this->db->prefixTable('clients');

		$where = "";
		$options = $this->escape_array($options);
		$project_id = get_array_value($options, "project_id");
		$task_id = get_array_value($options, "task_id");
		$leave_id = get_array_value($options, "leave_id");
		$ticket_id = get_array_value($options, "ticket_id");
		$project_comment_id = get_array_value($options, "project_comment_id");
		$ticket_comment_id = get_array_value($options, "ticket_comment_id");
		$project_file_id = get_array_value($options, "project_file_id");
		$post_id = get_array_value($options, "post_id");
		$activity_log_id = get_array_value($options, "activity_log_id");
		$client_id = get_array_value($options, "client_id");
		$invoice_payment_id = get_array_value($options, "invoice_payment_id");
		$invoice_id = get_array_value($options, "invoice_id");
		$estimate_id = get_array_value($options, "estimate_id");
		$order_id = get_array_value($options, "order_id");
		$estimate_request_id = get_array_value($options, "estimate_request_id");
		$actual_message_id = get_array_value($options, "actual_message_id");
		$parent_message_id = get_array_value($options, "parent_message_id");
		$event_id = get_array_value($options, "event_id");
		$announcement_id = get_array_value($options, "announcement_id");
		$exclude_ticket_creator = get_array_value($options, "exclude_ticket_creator");
		$notify_to_admins_only = get_array_value($options, "notify_to_admins_only");
		$notification_multiple_tasks = get_array_value($options, "notification_multiple_tasks");
		$lead_id = get_array_value($options, "lead_id");
		$contract_id = get_array_value($options, "contract_id");
		$proposal_id = get_array_value($options, "proposal_id");
		$estimate_comment_id = get_array_value($options, "estimate_comment_id");


		$hr_send_training_staff_id = get_array_value($options, "hr_send_training_staff_id");
		$hr_send_layoff_checklist_handle_staff_id = get_array_value($options, "hr_send_layoff_checklist_handle_staff_id");


		$to_user_id = get_array_value($options, "to_user_id");


		$extra_data = array();

		$notify_to_terms = array();

		$extra_where = "";


		$exclude_notification_creator = " $users_table.id!=$user_id ";

		$web_notify_to = "";
		$email_notify_to = "";


		$data = array(
			"user_id" => $user_id,
			"description" => "",
			"created_at" => get_current_utc_time(),
			"notify_to" => $to_user_id,
			"read_by" => "",
            "event" => $event, //Subject of notify
            "project_id" => $project_id ? $project_id : "",
            "task_id" => $task_id ? $task_id : "",
            "project_comment_id" => $project_comment_id ? $project_comment_id : "",
            "ticket_id" => $ticket_id ? $ticket_id : "",
            "ticket_comment_id" => $ticket_comment_id ? $ticket_comment_id : "",
            "project_file_id" => $project_file_id ? $project_file_id : "",
            "leave_id" => $leave_id ? $leave_id : "",
            "post_id" => $post_id ? $post_id : "",
            "to_user_id" => $to_user_id ? $to_user_id : "",
            "activity_log_id" => $activity_log_id ? $activity_log_id : "",
            "client_id" => $client_id ? $client_id : "",
            "invoice_payment_id" => $invoice_payment_id ? $invoice_payment_id : "",
            "invoice_id" => $invoice_id ? $invoice_id : "",
            "estimate_request_id" => $estimate_request_id ? $estimate_request_id : "",
            "estimate_id" => $estimate_id ? $estimate_id : "",
            "contract_id" => $contract_id ? $contract_id : "",
            "proposal_id" => $proposal_id ? $proposal_id : "",
            "order_id" => $order_id ? $order_id : "",
            "actual_message_id" => $actual_message_id ? $actual_message_id : "",
            "parent_message_id" => $parent_message_id ? $parent_message_id : "",
            "event_id" => $event_id ? $event_id : "",
            "announcement_id" => $announcement_id ? $announcement_id : "",
            "lead_id" => $lead_id ? $lead_id : "",
            "estimate_comment_id" => $estimate_comment_id ? $estimate_comment_id : "",

            "hr_send_training_staff_id" => $hr_send_training_staff_id ? $hr_send_training_staff_id : "",
            "hr_send_layoff_checklist_handle_staff_id" => $hr_send_layoff_checklist_handle_staff_id ? $hr_send_layoff_checklist_handle_staff_id : "",
         
        );

        //get data from plugin by persing 'plugin_'
		foreach ($options as $key => $value) {
			if (strpos($key, 'plugin_') !== false) {
				$data[$key] = $value;
			}
		}

		$builder = $this->db->table(get_db_prefix().'notifications');
		$builder->insert($data);
		$notification_id = $this->db->insertID();


        //send push notifications
		if (get_setting("enable_push_notification")) {
            //send push notifications to all web notifiy to users
            //but in receiving portal, it will be checked if the user disable push notification or not
			send_push_notifications($event, $web_notify_to, $user_id, $notification_id);
		}

        //send slack notifications
		if(1 == 2){
			$Notifications_model = model("Models\Notifications_model");
			$Notifications_model->prepare_sending_slack_notification($event, $user_id, $notification_id, $notification_settings, $project_id);
		}

	}

	/**
	 * get type of training has training program
	 * @return [type] 
	 */
	public function get_type_of_training_has_training_program(){
		
		return $this->db->query('select * from '.get_db_prefix().'hr_type_of_trainings where id IN (select training_type from '.get_db_prefix().'hr_jp_interview_training where '.get_db_prefix().'hr_jp_interview_training.additional_training != "additional_training") order by id desc')->getResultArray();
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
	 * get department by parent
	 * @param  [type] $id 
	 * @return [type]     
	 */
	public function get_department_by_parent($id){
		$department =  $this->db->query('select  id as id, parent_id as pid, title from '.get_db_prefix().'team as d where deleted = 0 order by d.parent_id, d.id')->getResultArray();
		$dep_tree = $this->get_recursive_children($id, $department);   
		if($dep_tree != ''){
			$dep_tree .= ','.$id;
			return explode(',', $dep_tree);
		}
		return [$id];
	}

	/**
	 * get_recursive_children
	 * @param  [type] $id    
	 * @param  [type] $items 
	 * @return [type]        
	 */
	function get_recursive_children($id, $items)
	{
		$childs = '';
		foreach ($items as $key => $item) {
			if ($item['pid'] === $id) {
				if($childs != ''){
					$childs .= ','.$item['id'];
				}else{
					$childs .= $item['id'];
				}

				if ($id !== $item['id']) {
					if($this->get_recursive_children($item['id'], $items) != ''){

						$childs .= ','.$this->get_recursive_children($item['id'], $items);
					}
				}
			}
		}
		return $childs;
	}

}