<?php
use App\Controllers\App_Controller;
use App\Controllers\Security_Controller;
use Hr_profile\Controllers\Hr_profile;
use App\Libraries\Pdf;
use App\Libraries\Clean_data;


/**
 * job position by staff
 * @param  integer $staffid 
 * @return string          
 */
function hr_profile_job_position_by_staff($staffid){
	$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");
	$staff = $Hr_profile_model->get_staff($staffid);
	if($staff){
		$job_name = hr_profile_job_name_by_id($staff->job_position);
	}else{
		$job_name = '';
	}

	return $job_name;
}


	/**
	 * job name by id
	 * @param  integer $job_position 
	 * @return string               
	 */
	function hr_profile_job_name_by_id($job_position){
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_job_position');

		$builder->where('position_id', $job_position);
		$builder->select('position_name');
		$dpm = $builder->get()->getRow();
		if($dpm){
			return $dpm->position_name; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr profile reformat currency asset
	 * @param  string $value 
	 * @return string        
	 */
	function hr_profile_reformat_currency($value)
	{
		$f_dot = str_replace(',','', $value);
		return ((float)$f_dot + 0);
	}


	/**
	 * get department name
	 * @param  integer $departmentid 
	 * @return object               
	 */
	function hr_profile_get_department_name($departmentid){

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'team');

		$builder->where('id', $departmentid);
		return $builder->get()->getRow();
	}


	/**
	 * handle hr profile job position attachments array
	 * @param  [type] $jobposition_tid 
	 * @param  string $index_name      
	 * @return [type]                  
	 */
	function handle_hr_profile_job_position_attachments_array($jobposition_tid, $index_name = 'attachments')
	{
		$uploaded_files = [];
		$path           = get_hr_profile_upload_path_by_type('job_position').$jobposition_tid .'/';
		$CI             = &get_instance();
		if (isset($_FILES[$index_name]['name'])
			&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
			if (!is_array($_FILES[$index_name]['name'])) {
				$_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
				$_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
				$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
				$_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
				$_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
			}

			_file_attachments_index_fix($index_name);
			for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
				$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
						|| !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
						continue;
				}

				_maybe_create_upload_path($path);
				$filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
				$newFilePath = $path . $filename;
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					array_push($uploaded_files, [
						'file_name' => $filename,
						'filetype'  => $_FILES[$index_name]['type'][$i],
					]);
					if (is_image($newFilePath)) {
						create_img_thumb($path, $filename);
					}
				}
			}
		}
	}
	if (count($uploaded_files) > 0) {
		return $uploaded_files;
	}
	return false;
}


/**
 * get hr profile upload path by type
 * @param  string $type 
 */
function get_hr_profile_upload_path_by_type($type)
{
	$path = '';
	switch ($type) {
		case 'staff_contract':
		$path = HR_PROFILE_CONTRACT_ATTACHMENTS_UPLOAD_FOLDER;

		break;

		case 'job_position':
		$path = HR_PROFILE_JOB_POSIITON_ATTACHMENTS_UPLOAD_FOLDER;

		break;

		case 'kb_article_files':
		$path = HR_PROFILE_Q_A_ATTACHMENTS_UPLOAD_FOLDER;
		break;
		
		case 'att_files':
		$path = HR_PROFILE_FILE_ATTACHMENTS_UPLOAD_FOLDER;

		break;
		
		
	}

	return app_hooks()->apply_filters('get_hr_profile_upload_path_by_type', $path, $type);
}


	/**
	 * get status modules
	 * @param  string $module_name 
	 * @return boolean              
	 */
	function hr_profile_get_status_modules($module_name){
		$CI             = &get_instance();
		$CI->db->where('module_name',$module_name);
		$module = $CI->db->get(db_prefix().'modules')->row();
		if($module&&$module->active==1)
		{
			return true;
		}
		return false;
	}


		/**
		 * handle hr profile attachments array
		 * @param  [type] $staffid    
		 * @param  string $index_name 
		 * @return [type]             
		 */
		function handle_hr_profile_attachments_array($staffid, $index_name = 'attachments')
		{
			$uploaded_files = [];
			$path           = get_hr_profile_upload_path_by_type('att_files').$staffid .'/';

			$CI             = &get_instance();
			if (isset($_FILES[$index_name]['name'])
				&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
				if (!is_array($_FILES[$index_name]['name'])) {
					$_FILES[$index_name]['name']     = [$_FILES[$index_name]['name']];
					$_FILES[$index_name]['type']     = [$_FILES[$index_name]['type']];
					$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
					$_FILES[$index_name]['error']    = [$_FILES[$index_name]['error']];
					$_FILES[$index_name]['size']     = [$_FILES[$index_name]['size']];
				}

				_file_attachments_index_fix($index_name);
				for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {
						// Get the temp file path
					$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];

						// Make sure we have a filepath
					if (!empty($tmpFilePath) && $tmpFilePath != '') {
						if (_perfex_upload_error($_FILES[$index_name]['error'][$i])
							|| !_upload_extension_allowed($_FILES[$index_name]['name'][$i])) {
							continue;
					}

					_maybe_create_upload_path($path);
					$filename    = unique_filename($path, $_FILES[$index_name]['name'][$i]);
					$newFilePath = $path . $filename;

							// Upload the file into the temp dir
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						array_push($uploaded_files, [
							'file_name' => $filename,
							'filetype'  => $_FILES[$index_name]['type'][$i],
						]);
						if (is_image($newFilePath)) {
							create_img_thumb($path, $filename);
						}
					}
				}
			}
		}

		if (count($uploaded_files) > 0) {
			return $uploaded_files;
		}

		return false;
	}


	if(!function_exists('_file_attachments_index_fix')){
        /**
     * Performs fixes when $_FILES is array and the index is messed up
     * Eq user click on + then remove the file and then added new file
     * In this case the indexes will be 0,2 - 1 is missing because it's removed but they should be 0,1
     * @param  string $index_name $_FILES index name
     * @return null
     */
        function _file_attachments_index_fix($index_name)
        {
        	if (isset($_FILES[$index_name]['name']) && is_array($_FILES[$index_name]['name'])) {
        		$_FILES[$index_name]['name'] = array_values($_FILES[$index_name]['name']);
        	}

        	if (isset($_FILES[$index_name]['type']) && is_array($_FILES[$index_name]['type'])) {
        		$_FILES[$index_name]['type'] = array_values($_FILES[$index_name]['type']);
        	}

        	if (isset($_FILES[$index_name]['tmp_name']) && is_array($_FILES[$index_name]['tmp_name'])) {
        		$_FILES[$index_name]['tmp_name'] = array_values($_FILES[$index_name]['tmp_name']);
        	}

        	if (isset($_FILES[$index_name]['error']) && is_array($_FILES[$index_name]['error'])) {
        		$_FILES[$index_name]['error'] = array_values($_FILES[$index_name]['error']);
        	}

        	if (isset($_FILES[$index_name]['size']) && is_array($_FILES[$index_name]['size'])) {
        		$_FILES[$index_name]['size'] = array_values($_FILES[$index_name]['size']);
        	}
        }
    }



	/**
	 * hr profile staff profile image upload for staffmodel
	 * @param  integer $staff id 
	 * @return boolean           
	 */
	function hr_profile_handle_staff_profile_image_upload($user_id)
	{
		if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            
            if ($image_file_name) {
                if (!$this->check_profile_image_dimension($image_file_name)) {
                    echo json_encode(array("success" => false, 'message' => app_lang('profile_image_error_message')));
                    exit();
                }

                $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name));

                //delete old file
                delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));

                $image_data = array("image" => $profile_image);

                $this->Users_model->ci_save($image_data, $user_id);

                return true;
            }
        }
        return false;
	}


	/**
	 * list hr profile permisstion
	 * @return [type] 
	 */
	function list_hr_profile_permisstion()
	{
		$hr_profile_permissions=[];
		$hr_profile_permissions[]='hrm_dashboard';
		$hr_profile_permissions[]='staffmanage_orgchart';
		$hr_profile_permissions[]='hrm_reception_staff';
		$hr_profile_permissions[]='hrm_hr_records';
		$hr_profile_permissions[]='staffmanage_job_position';
		$hr_profile_permissions[]='staffmanage_training';
		$hr_profile_permissions[]='hr_manage_q_a';
		$hr_profile_permissions[]='hrm_contract';
		$hr_profile_permissions[]='hrm_dependent_person';
		$hr_profile_permissions[]='hrm_procedures_for_quitting_work';
		$hr_profile_permissions[]='hrm_report';
		$hr_profile_permissions[]='hrm_setting';

		return $hr_profile_permissions;
	}

	/**
	 * hr profile get staff id hr permissions
	 * @return [type] 
	 */
	function hr_profile_get_staff_id_hr_permissions()
	{
		$CI = & get_instance();
		$array_staff_id = [];
		$index=0;

		$str_permissions ='';
		foreach (list_hr_profile_permisstion() as $per_key =>  $per_value) {
			if(strlen($str_permissions) > 0){
				$str_permissions .= ",'".$per_value."'";
			}else{
				$str_permissions .= "'".$per_value."'";
			}

		}


		$sql_where = "SELECT distinct staff_id FROM ".db_prefix()."staff_permissions
		where feature IN (".$str_permissions.")
		";
		
		$staffs = $CI->db->query($sql_where)->result_array();

		if(count($staffs)>0){
			foreach ($staffs as $key => $value) {
				$array_staff_id[$index] = $value['staff_id'];
				$index++;
			}
		}
		return $array_staff_id;
	}


	/**
	 * hr profile get staff id dont permissions
	 * @return [type] 
	 */
	function hr_profile_get_staff_id_dont_permissions()
	{
		$CI = & get_instance();

		$CI->db->where('admin != ', 1);

		if(count(hr_profile_get_staff_id_hr_permissions()) > 0){
			$CI->db->where_not_in('staffid', hr_profile_get_staff_id_hr_permissions());
		}
		return $CI->db->get(db_prefix().'staff')->result_array();
		
	}


	/**
	 * hr profile handle contract attachments array
	 * @param  [type] $contractid 
	 * @param  string $index_name 
	 * @return [type]             
	 */
	function hr_profile_handle_contract_attachments_array($id, $index_name = 'attachments'){

		$path           = get_hr_profile_upload_path_by_type('staff_contract').$id .'/';
		$totalUploaded = 0;

		if (isset($_FILES[$index_name]['name'])
			&& ($_FILES[$index_name]['name'] != '' || is_array($_FILES[$index_name]['name']) && count($_FILES[$index_name]['name']) > 0)) {
			if (!is_array($_FILES[$index_name]['name'])) {
				$_FILES[$index_name]['name'] = [$_FILES[$index_name]['name']];
				$_FILES[$index_name]['type'] = [$_FILES[$index_name]['type']];
				$_FILES[$index_name]['tmp_name'] = [$_FILES[$index_name]['tmp_name']];
				$_FILES[$index_name]['error'] = [$_FILES[$index_name]['error']];
				$_FILES[$index_name]['size'] = [$_FILES[$index_name]['size']];
			}

			_file_attachments_index_fix($index_name);
			for ($i = 0; $i < count($_FILES[$index_name]['name']); $i++) {

            // Get the temp file path
				$tmpFilePath = $_FILES[$index_name]['tmp_name'][$i];
            // Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {


					_maybe_create_upload_path($path);
					$filename = unique_filename($path, $_FILES[$index_name]['name'][$i]);
					$newFilePath = $path . $filename;
                // Upload the file into the temp dir
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$attachment = [];
						$attachment[] = [
							'file_name' => $filename,
							'filetype' => $_FILES[$index_name]['type'][$i],
						];


						$Hr_profile_model = model('Hr_profile\Models\Hr_profile_model');
						$Hr_profile_model->add_attachment_to_database($id, 'hr_contract', $attachment);

						$totalUploaded++;
					}
				}
			}
		}

		return (bool) $totalUploaded;
	}


	/**
	 * get job name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_job_name($id)
	{   
		$job_name ='';

		if($id != 0 && $id != ''){
			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'hr_job_p');

			$builder->where('job_id',$id);
			$hr_job_p = $builder->get()->getRow();

			if($hr_job_p){
				$job_name .= $hr_job_p->job_name;
			}
		}
		return $job_name;
	}


	/**
	 * get department from strings
	 * @param  [type] $string_ids 
	 * @return [type]             
	 */
	function get_department_from_strings($string_ids, $department_on_line)
	{   
		$list_department_name ='';

		// get department
		if($string_ids != null && $string_ids != ''){
			$department_ids       = explode(',', $string_ids);
			$str = '';
			$j = 0;
			foreach ($department_ids as $key => $department_id) {
				$department_name ='';
				$member   = hr_profile_get_department_name($department_id);

				if($member){
					$department_name .= $member->title;
				}

				$j++;
				$str .= '<span class="badge bg-success large mt-0">' . $department_name . '</span>&nbsp';
				
				if($j%$department_on_line == 0){
					$str .= '<br><br/>';
				}

			}
			$list_department_name = $str;
		}else{
			$list_department_name = '';
		}

		return $list_department_name;

	}


	/**
	 * hr profile get kb groups
	 * @return [type] 
	 */
	function hr_profile_get_kb_groups()
	{
		$CI = & get_instance();

		return $CI->db->get(db_prefix() . 'hr_knowledge_base_groups')->result_array();
	}


	/**
	 * hr profile get all knowledge base articles grouped
	 * @param  boolean $only_customers 
	 * @param  array   $where          
	 * @return [type]                  
	 */
	function hr_profile_get_all_knowledge_base_articles_grouped($only_customers = true, $where = [])
	{
		$CI = & get_instance();
		$CI->load->model('knowledge_base_q_a_model');
		$groups = $CI->knowledge_base_q_a_model->get_kbg('', 1);
		$i      = 0;
		foreach ($groups as $group) {
			$CI->db->select('slug,subject,description,' . db_prefix() . 'hr_knowledge_base.active as active_article,articlegroup,articleid,staff_article');
			$CI->db->from(db_prefix() . 'hr_knowledge_base');
			$CI->db->where('articlegroup', $group['groupid']);
			$CI->db->where('active', 1);
			if ($only_customers == true) {
				$CI->db->where('staff_article', 0);
			}
			$CI->db->where($where);
			$CI->db->order_by('article_order', 'asc');
			$articles = $CI->db->get()->result_array();
			if (count($articles) == 0) {
				unset($groups[$i]);
				$i++;

				continue;
			}
			$groups[$i]['articles'] = $articles;
			$i++;
		}

		return array_values($groups);
	}

	/**
	 * hr profile handle kb article files upload
	 * @param  string $articleid  
	 * @param  string $index_name 
	 * @return [type]             
	 */
	function hr_profile_handle_kb_article_files_upload($articleid = '', $index_name = 'kb_article_files')
	{
		$path           = get_hr_profile_upload_path_by_type('kb_article_files') . $articleid . '/';
		$uploaded_files = [];
		if (isset($_FILES[$index_name])) {
			_file_attachments_index_fix($index_name);
			// Get the temp file path
			$tmpFilePath = $_FILES[$index_name]['tmp_name'];
			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				// Getting file extension
				$extension = strtolower(pathinfo($_FILES[$index_name]['name'], PATHINFO_EXTENSION));

				$allowed_extensions = explode(',', get_option('ticket_attachments_file_extensions'));
				$allowed_extensions = array_map('trim', $allowed_extensions);
				// Check for all cases if this extension is allowed
				
				_maybe_create_upload_path($path);
				$filename    = unique_filename($path, $_FILES[$index_name]['name']);
				$newFilePath = $path . $filename;
				
				// Upload the file into the temp dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$CI                       = & get_instance();

					$CI->db->insert(db_prefix().'files', [
						'rel_id' => $articleid,
						'rel_type' => 'hr_profile_kb_article',
						'file_name' => $_FILES['kb_article_files']['name'],
						'filetype' => $_FILES['kb_article_files']['type'],
						'staffid' => get_staff_user_id()
					]);
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * hr profile get workplace name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_profile_get_workplace_name($id){
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_workplace');

		$builder->where('id', $id);
		$workplace = $builder->get()->getRow();

		if($workplace){
			return $workplace->name; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr profile get job position name
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_profile_get_job_position_name($id){

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_job_position');

		$builder->where('position_id', $id);
		$job_position = $builder->get()->getRow();

		if($job_position){
			return $job_position->position_name; 
		}else{
			return ''; 
		} 
	}

	/**
	 * hr profile get job position description
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_profile_get_job_position_description($id){

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_job_position');

		$builder->where('position_id', $id);
		$job_position = $builder->get()->getRow();

		if($job_position){
			return $job_position->job_position_description; 
		}else{
			return ''; 
		} 
	}

	/**
	 * hr profile get hr_code
	 * @param  [type] $staff_id 
	 * @return [type]           
	 */
	function hr_profile_get_hr_code($staff_id){

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'users');
		$builder->where('id', $staff_id);
		$staff = $builder->get()->getRow();

		if($staff){
			return $staff->staff_identifi; 
		}else{
			return ''; 
		} 
	}


	/**
	 * hr get staff email by id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_get_staff_email_by_id($id)
	{
		$CI = & get_instance();

		$staff = $CI->app_object_cache->get('staff-email-by-id-' . $id);

		if (!$staff) {
			$CI->db->where('staffid', $id);
			$staff = $CI->db->select('email')->from(db_prefix() . 'staff')->get()->row();
			$CI->app_object_cache->add('staff-email-by-id-' . $id, $staff);
		}

		return ($staff ? $staff->email : '');
	}


	/**
	 * hr get training hash
	 * @param  [type] $training_id 
	 * @return [type]              
	 */
	function hr_get_training_hash($training_id)
	{
		$hash = '';
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_position_training');
		$builder->where('training_id', $training_id);
		$training = $builder->get()->getRow();

		if($training){
			$hash .= $training->hash;
		}
		return $hash;
	}

	/**
	 * hr profile type of training exists
	 * @param  [type] $name 
	 * @return [type]       
	 */
	function hr_profile_type_of_training_exists($name){
		$CI = & get_instance();
		$i = count($CI->db->query('Select * from '.db_prefix().'hr_type_of_trainings where name = '.$name)->result_array());
		if($i == 0){
			return 0;
		}
		if($i > 0){
			return 1;
		}
	}


	/**
	 * get type of training by id
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function get_type_of_training_by_id($id)
	{
		$type_of_training_name ='';

		if(is_numeric($id)){

			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'hr_type_of_trainings');
			$builder->where('id',$id);
			$type_of_training = $builder->get()->getRow();

			if($type_of_training){
				$type_of_training_name .= $type_of_training->name;
			}
		}

		return $type_of_training_name;
	}

	/**
	 * get training library name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function get_training_library_name($ids)
	{
		$training_name='';

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_position_training');
		$builder->where('training_id IN ('. $ids.') ');
		$hr_position_training = $builder->get()->getResultArray();

		foreach ($hr_position_training  as $value) {
			if(strlen($training_name) > 0){
				$training_name .=', '.$value['subject'];
			}else{
				$training_name .=$value['subject'];
			}
		}
		return $training_name;

	}

	/**
	 * hr get list staff name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function hr_get_list_staff_name($ids)
	{
		$staff_name='';

		if(strlen($ids) > 0){

			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'users');
			$builder->where('id IN ('. $ids.') ');
			$staffs = $builder->get()->getResultArray();

			foreach ($staffs  as $value) {
				if(strlen($staff_name) > 0){
					$staff_name .=', '.$value['first_name'].' '.$value['last_name'];
				}else{
					$staff_name .= $value['first_name'].' '.$value['last_name'];
				}
			}
		}

		return $staff_name;
	}

	/**
	 * hr get list job position name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function hr_get_list_job_position_name($ids)
	{
		$position_names='';

		if(strlen($ids) > 0){

			$builder = db_connect('default');
			$builder = $builder->table(get_db_prefix().'hr_job_position');
			$builder->where('position_id IN ('. $ids.') ');
			$job_position = $builder->get()->getResultArray();

			foreach ($job_position  as $value) {
				if(strlen($position_names) > 0){
					$position_names .=', '.$value['position_name'];
				}else{
					$position_names .= $value['position_name'];
				}
			}
		} 
		return $position_names;
	}

	/**
	 * hr contract pdf
	 * @param  [type] $contract 
	 * @return [type]           
	 */
	function hr_contract_pdf($contract)
	{
		return app_pdf('contract',  module_dir_path(HR_PROFILE_MODULE_NAME, 'libraries/pdf/Hr_contract_pdf'), $contract);
	}

	/**
	 * hr get contract type
	 * @param  [type] $id 
	 * @return [type]     
	 */
	function hr_get_contract_type($id)
	{
		$name='';

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'hr_staff_contract_type');
		$builder->where('id_contracttype', $id);
		$contract_type = $builder->get()->getRow();

		if($contract_type){
			$name .= $contract_type->name_contracttype;
		}

		return $name;
	}

	/**
	 * hr get role name
	 * @param  [type] $ids 
	 * @return [type]      
	 */
	function hr_get_role_name($id)
	{
		$roles_names='';

		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'roles');

		$builder->where('id', $id);
		$role = $builder->get()->getRow();

		if($role){
			$roles_names .= $role->title;
		}
		return $roles_names;
	}

	/**
	 * get staff department names
	 * @param  [type] $staffid 
	 * @return [type]          
	 */
	function get_staff_department_names($staffid)
	{
		$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

		$list_department = $Hr_profile_model->getdepartment_name($staffid);
		return $list_department->name;
	}

	/**
	 * hr render salary table
	 * @param  [type] $contract_id 
	 * @return [type]              
	 */
	function hr_render_salary_table($contract_id)
	{
		$table='';

		$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

		$contract_details = $Hr_profile_model->get_contract_detail($contract_id);

		$table  .= '<table border="1" class="width-100-height-55">';
		$table  .= '<tbody>';
		$table  .= '<tr class="height-27">';
		$table  .= '<td class="width-25-height-27-text-align"><strong>'._l('hr_hr_contract_rel_type').'</strong></td>';
		$table  .= '<td class="width-25-height-27"><strong>'._l('hr_hr_contract_rel_value').'</strong></td>';
		$table  .= '<td class="width-25-height-27"><strong>'._l('hr_start_month').'</strong></td>';
		$table  .= '<td class="width-25-height-27"><strong>'._l('note').'</strong></td>';
		$table  .= '</tr>';

		foreach($contract_details as $contract_detail){


			$type_name ='';
			if(preg_match('/^st_/', $contract_detail['rel_type'])){
				$rel_value = str_replace('st_', '', $contract_detail['rel_type']);
				$salary_type = $Hr_profile_model->get_salary_form($rel_value);

				$type = 'salary';
				if($salary_type){
					$type_name = $salary_type->form_name;
				}

			}elseif(preg_match('/^al_/', $contract_detail['rel_type'])){
				$rel_value = str_replace('al_', '', $contract_detail['rel_type']);
				$allowance_type = $Hr_profile_model->get_allowance_type($rel_value);

				$type = 'allowance';
				if($allowance_type){
					$type_name = $allowance_type->type_name;
				}
			}



			$table .= '<tr class="height-28">';
			$table .= '<td class="width-25-height-28"><span>'.$type_name.'</span></td>';
			$table .= '<td class="width-25-height-28"><span>'.to_decimal_format((float)$contract_detail['rel_value']).'</span></td>';
			$table .= '<td class="width-25-height-28"><span>'.format_to_date($contract_detail['since_date']).'</span></td>';
			$table .= '<td class="width-25-height-28">'.$contract_detail['contract_note'].'</td>';
			$table .= '</tr>';

		}

		$table .= '</tbody>';
		$table .= '</table>';

		return $table;
	}

	/**
	 * hr process digital signature image
	 * @param  [type] $partBase64 
	 * @param  [type] $path       
	 * @return [type]             
	 */
	function hr_process_digital_signature_image($partBase64, $path)
	{
		if (empty($partBase64)) {
			return false;
		}

		_maybe_create_upload_path($path);
		$filename = unique_filename($path, 'staff_signature.png');

		$decoded_image = base64_decode($partBase64);

		$retval = false;

		$path = rtrim($path, '/') . '/' . $filename;

		$fp = fopen($path, 'w+');

		if (fwrite($fp, $decoded_image)) {
			$retval                                 = true;
			$GLOBALS['processed_digital_signature'] = $filename;
		}

		fclose($fp);

		return $retval;
	}

	/**
	 * hr profile check hide menu
	 * @return [type] 
	 */
	function hr_profile_check_hide_menu()
	{
		$hide_menu = false;
		if(get_option('hr_profile_hide_menu')){
			$CI             = &get_instance();
			$CI->db->where('staffid', get_staff_user_id());
			$staff = $CI->db->get(db_prefix().'staff')->row();
			if($staff){
				if($staff->is_not_staff == 1){
					$hide_menu = true;
				}
			}
		}

		return $hide_menu;
	}

	if (!function_exists('get_default_company_name')) {
		function get_default_company_name($company_id = 0) {
			$company_name = '';

			if($company_id == 0){
				$company_id = get_default_company_id();
			}

			$options = array("is_default" => true);
			if ($company_id) {
				$options = array("id" => $company_id);
			}

			$options["deleted"] = 0;

			$Company_model = model('App\Models\Company_model');
			$company_info = $Company_model->get_one_where($options);

        //show default company when any specific company isn't exists
			if ($company_info) {
				$company_name = $company_info->name;
			}

			return $company_name;
		}
	}

	/**
	 * get staff image
	 * @param  [type]  $staff_id     
	 * @param  boolean $include_name 
	 * @return [type]                
	 */
	if (!function_exists('get_staff_image')) {
		
		function get_staff_image($staff_id, $include_name = true)
		{
			$staff_image = '';
			if(is_numeric($staff_id) && $staff_id != 0){

				$get_staff_infor = get_staff_infor($staff_id);
				if($get_staff_infor){
					$staff_image .= '<span class="avatar-xs avatar me-1" data-bs-toggle="tooltip" data-bs-original-title="'.$get_staff_infor->first_name.' '.$get_staff_infor->last_name.'">
					<img alt="..." src="'.get_avatar($get_staff_infor->image).'">
					</span>';

				}

				if( $include_name && $get_staff_infor){
					$staff_image .= '<span class="user-name ml10">'.$get_staff_infor->first_name.' '.$get_staff_infor->last_name.'</span>';
				}
			}

			return $staff_image;
		}
	}

	/**
	 * handle hr profile add attachments
	 * @param  [type] $file_name   
	 * @param  [type] $target_path 
	 * @param  [type] $rel_id      
	 * @param  [type] $rel_type    
	 * @return [type]              
	 */
	function handle_hr_profile_add_attachments($file_name, $target_path, $rel_id, $rel_type)
	{
		if ($files && get_array_value($files, 0)) {
			foreach ($files as $file) {
				$file_name = $this->request->getPost('file_name_' . $file);
				$file_info = move_temp_file($file_name, $target_path, "");

				if ($file_info) {
					$data = array(
						"rel_id" => $rel_id,
						"rel_type" => $rel_type,
						"file_name" => get_array_value($file_info, 'file_name'),
						"staffid" => get_staff_user_id1(),
						"file_size" => $this->request->getPost('file_size_' . $file),
						"filetype" => '',
						"attachment_key" => app_generate_hash(),
						"dateadded" => $now,
					);

					$data = clean_data($data);
				
					
				} else {
					$success = false;
				}
			}
		}
	}

	if (!function_exists('hr_log_notification')) {

		function hr_log_notification($event, $options = array(), $user_id = 0, $to_user_id = 0) {
			$ci = new Security_Controller(false);

			//send direct notification to the url
			$data = array(
				"event" => $event
			);

			if ($user_id) {
				$data["user_id"] = $user_id;
			} else if ($user_id === "0") {
				$data["user_id"] = $user_id; //if user id is 0 (string) we'll assume that it's system bot 
			} else if (isset($ci->login_user->id)) {
				$data["user_id"] = $ci->login_user->id;
			}

			$data['to_user_id'] = $to_user_id;

			foreach ($options as $key => $value) {
				$value = urlencode($value);
				$data[$key] = $value;
			}

			$hr_profile = new Hr_profile();
			$hr_profile->hr_create_notification($data);
		}
	}

	/**
	 * staff contract variables
	 * @return [type] 
	 */
	function staff_contract_variables()
	{

		return array("{LOGO_URL}", "{COMPANY_NAME}", "{STAFF_CODE}", "{STAFF_FIRST_NAME}", "{STAFF_LAST_NAME}", "{STAFF_GENDER}", "{STAFF_DAY_OF_BIRTH}", "{STAFF_EMAIL}", "{STAFF_PHONE}", "{STAFF_WORKPLACE}", "{STAFF_POSITION}", "{STAFF_POSITON_DESCRIPTION}", "{STAFF_ROLE}", "{STAFF_DEPARTMENT}", "{STAFF_DOMICILE}", "{STAFF_ADDRESS}", "{STAFF_NATION}", "{STAFF_PLACE_OF_BIRTH}", "{STAFF_RELIGION}", "{STAFF_CITIZEN_IDENTIFICATION}", "{STAFF_DATE_OF_ISSUE}", "{STAFF_RESIDENT}", "{STAFF_PERSONAL_TAX_CODE}", "{CONTRACT_CODE}", "{CONTRACT_TYPE}", "{CONTRACT_STATUS}", "{CONTRACT_EFFECTIVE_DATE}", "{CONTRACT_EXPIRATION_DATE}", "{HOURLY_OR_MONTH}", "{SALARY_AND_ALLOWANCE}", "{SIGNATURE}" );
	}

	/**
	 * staff contract map variables
	 * @param  [type] $key      
	 * @param  [type] $contract 
	 * @return [type]           
	 */
	function staff_contract_map_variables($key , $contract)
	{
		$fields['{STAFF_CODE}'] 				=  $contract->staff_identifi;
		$fields['{STAFF_FIRST_NAME}'] 			=  $contract->first_name;
		$fields['{STAFF_LAST_NAME}'] 		=  $contract->last_name;
		$fields['{STAFF_GENDER}'] 				=  $contract->gender;
		$fields['{STAFF_DAY_OF_BIRTH}'] 				=  $contract->dob;
		$fields['{STAFF_EMAIL}'] 				=  $contract->email;
		$fields['{STAFF_PHONE}'] 				=  $contract->phone;
		$fields['{STAFF_WORKPLACE}'] 			=  hr_profile_get_workplace_name($contract->workplace);
		$fields['{STAFF_POSITION}'] 			=  hr_profile_get_job_position_name($contract->job_position);
		$fields['{STAFF_POSITON_DESCRIPTION}'] 			=  hr_profile_get_job_position_description($contract->job_position);
		$fields['{STAFF_ROLE}'] 				=  hr_get_role_name($contract->role_id);
		$fields['{STAFF_DEPARTMENT}'] 		=  get_staff_department_names($contract->id_contract);
		$fields['{STAFF_DOMICILE}'] 			=  $contract->home_town;
		$fields['{STAFF_ADDRESS}'] 		=  $contract->address;
		$fields['{STAFF_NATION}'] 				=  $contract->nation;
		$fields['{STAFF_PLACE_OF_BIRTH}'] 		=  $contract->birthplace;
		$fields['{STAFF_RELIGION}'] 			=  $contract->religion;
		$fields['{STAFF_CITIZEN_IDENTIFICATION}'] =  $contract->identification;
		$fields['{STAFF_DATE_OF_ISSUE}'] 		=  format_to_date($contract->days_for_identity);
		$fields['{STAFF_RESIDENT}'] 			=  $contract->resident;
		$fields['{STAFF_PERSONAL_TAX_CODE}'] 	=  $contract->Personal_tax_code;
		$fields['{CONTRACT_CODE}'] 				=  $contract->contract_code;
		$fields['{CONTRACT_TYPE}'] 				=  hr_get_contract_type($contract->name_contract);
		$fields['{CONTRACT_STATUS}'] 			=  $contract->contract_status;
		$fields['{CONTRACT_EFFECTIVE_DATE}'] 	=  format_to_date($contract->start_valid);
		$fields['{CONTRACT_EXPIRATION_DATE}'] 	=  format_to_date($contract->end_valid);
		$fields['{HOURLY_OR_MONTH}'] 			=  $contract->hourly_or_month;
		$fields['{SALARY_AND_ALLOWANCE}'] 		=  hr_render_salary_table($contract->id_contract);

		if(isset($fields[$key])){
			return $fields[$key];
		}
		return '';
	}

	/**
	 * hr profile process digital signature image
	 * @param  [type] $partBase64 
	 * @param  [type] $path       
	 * @param  [type] $image_name 
	 * @return [type]             
	 */
	function hr_profile_process_digital_signature_image($partBase64, $path, $image_name)
	{
		if (empty($partBase64)) {
			return false;
		}

		if (!file_exists($path)) {
			mkdir($path, 0755);
			fopen(rtrim($path, '/') . '/' . 'index.html', 'w');
		}

		$filename = unique_filename($path, $image_name.'.png');
		$decoded_image = base64_decode($partBase64);

		$retval = false;

		$path = rtrim($path, '/') . '/' . $filename;

		$fp = fopen($path, 'w+');

		if (fwrite($fp, $decoded_image)) {
			$retval                                 = true;
			$GLOBALS['processed_digital_signature'] = $filename;
		}

		fclose($fp);

		return $retval;
	}

	/**
	 * unique_filename
	 * @param  [type] $dir      
	 * @param  [type] $filename 
	 * @return [type]           
	 */
	if (!function_exists('unique_filename')) {

		function unique_filename($dir, $filename)
		{
	// Separate the filename into a name and extension.
			$info     = pathinfo($filename);
			$ext      = !empty($info['extension']) ? '.' . $info['extension'] : '';

			$number   = '';
		// Change '.ext' to lower case.
			if ($ext && strtolower($ext) != $ext) {
				$ext2      = strtolower($ext);
				$filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);
		// Check for both lower and upper case extension or image sub-sizes may be overwritten.
				while (file_exists($dir . "/$filename") || file_exists($dir . "/$filename2")) {
					$filename = str_replace([
						"-$number$ext",
						"$number$ext",
					], "-$new_number$ext", $filename);
					$filename2 = str_replace([
						"-$number$ext2",
						"$number$ext2",
					], "-$new_number$ext2", $filename2);
					$number = $new_number;
				}

				return $filename2;
			}
			while (file_exists($dir . "/$filename")) {
				if ('' == "$number$ext") {
					$filename = "$filename-" . ++$number;
				} else {
					$filename = str_replace([
						"-$number$ext",
						"$number$ext",
					], '-' . ++$number . $ext, $filename);
				}
			}

			return $filename;
		}
	}

	/**
	 * to slug
	 * @param  [type] $string 
	 * @return [type]         
	 */
	if (!function_exists('to_slug')) {
		function to_slug($string){
			return preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($string)));
		}
	}

	/**
	 * has permission
	 * @param  [type]  $permission 
	 * @param  string  $staffid    
	 * @param  string  $can        
	 * @return boolean             
	 */
	if (!function_exists('has_permission')) {
		function has_permission($permission, $staffid = '', $can = '')
		{
			return true;
		}

	}

	/**
	 * hr has permission
	 * @param  [type] $staff_permission 
	 * @param  string $staffid          
	 * @return [type]                   
	 */
	if (!function_exists('hr_has_permission')) {
		function hr_has_permission($staff_permission, $staffid = '')
		{
			$db = db_connect('default');
			$dbprefix = get_db_prefix();
			if (!$db->fieldExists("plugins_permissions1" ,$dbprefix . "roles")) { 
				return false;
			}
			
			$ci = new Security_Controller(false);
			$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");
			$login_user = $Hr_profile_model->plugin_get_access_info($ci->login_user->id);
			$permissions = is_array(unserialize($login_user->permissions ?? '')) ? unserialize($login_user->permissions ?? '') : array();

			if($ci->login_user->is_admin){
				return true;
			}

			if(get_array_value($permissions, $staff_permission)){
				return true;
			}
			return false;
		}
	}

	
	if (!function_exists('_d')) {
	/**
	 * { format date }
	 *
	 * @param       $date   The date
	 *
	 * @return     date 
	 */
	function _d($date)
	{
		if($date == '0000-00-00' || $date == '0000-00-00 00:00:00')
		{
			return '';
		}
		
		return format_to_date($date);
	}
}