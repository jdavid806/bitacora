<?php
use App\Controllers\App_Controller;
use App\Controllers\Security_Controller;
use Recruitment\Controllers\Recruitment;
use App\Libraries\Pdf;
use App\Libraries\Clean_data;


/**
 * Check whether column exists in a table
 * Custom function because Codeigniter is caching the tables and this is causing issues in migrations
 * @param  string $column column name to check
 * @param  string $table table name to check
 * @return boolean
 */
function handle_rec_proposal_file($id)
{

	if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/proposal/'. $id . '/';
		// Get the temp file path
		$tmpFilePath = $_FILES['file']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {
			_maybe_create_upload_path($path);
			$filename    = unique_filename($path, $_FILES['file']['name']);

			$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {
				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['file']['type'],
				];
				$Recruitment_model = model('Recruitment\Models\Recruitment_model');
				$Recruitment_model->add_attachment_to_database($id, 'rec_proposal', $attachment);

				return true;
			}
		}
	}

	return false;
}

/**
 * reformat currency rec
 * @param  string $value
 * @return string
 */
function reformat_currency_rec($value)
{
	return str_replace(',','', $value);
}

/**
 * get rec dpm name
 * @param  int $id
 * @return string
 */
function get_rec_dpm_name($id){
	if($id != 0){
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'team');

		$builder->where('id', $id);
		$dpm = $builder->get()->getRow();
		if($dpm->title){
			return $dpm->title;
		}else{
			return '';
		}
		
	}else{
		return '';
	}
}

/**
 * get rec position name
 * @param  int $id
 * @return string
 */
function get_rec_position_name($id){
	if($id != 0){
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'rec_job_position');
		$builder->where('position_id',$id);
		$dpm = $builder->get()->getRow();
		if($dpm->position_name){
			return $dpm->position_name;
		}else{
			return '';
		}
		
	}else{
		return '';
	}
}

/**
 * handle rec campaign file
 * @param  int $id 
 * @return bool
 */
function handle_rec_campaign_file($id){
	if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/campaign/'. $id . '/';
		// Get the temp file path
		$tmpFilePath = $_FILES['file']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {
			_maybe_create_upload_path($path);
			$filename    = unique_filename($path, $_FILES['file']['name']);
			$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {
				
				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['file']['type'],
				];

				$Recruitment_model = model('Recruitment\Models\Recruitment_model');
				$Recruitment_model->add_attachment_to_database($id, 'rec_campaign', $attachment);
				return true;
			}
		}
	}

	return false;
}

/**
 * handle rec candidate file
 * @param  int $id
 * @return bool
 */
function handle_rec_candidate_file($id){
	if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/files/'. $id . '/';
		// Get the temp file path
		$tmpFilePath = $_FILES['file']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {
			_maybe_create_upload_path($path);
			$filename    = unique_filename($path, $_FILES['file']['name']);

			$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {
				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['file']['type'],
				];

				$Recruitment_model = model('Recruitment\Models\Recruitment_model');
				$Recruitment_model->add_attachment_to_database($id, 'rec_cadidate_file', $attachment);

				return true;
			}
		}
	}
	return false;
}

/**
 * handle rec candidate avar file
 * @param  int $id
 * @return bool   
 */
function handle_rec_candidate_avar_file($id){

	if (isset($_FILES['cd_avar']['name']) && $_FILES['cd_avar']['name'] != '') {
		
		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/avartar/'. $id . '/';
		// Get the temp file path
		$tmpFilePath = $_FILES['cd_avar']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {
			_maybe_create_upload_path($path);
			$filename    = unique_filename($path, $_FILES['cd_avar']['name']);

			$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {
				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['cd_avar']['type'],
				];
				$Recruitment_model = model('Recruitment\Models\Recruitment_model');
				$Recruitment_model->add_attachment_to_database($id, 'rec_cadidate_avar', $attachment);

				return true;
			}
		}
	}

	return false;
}

if (!function_exists('create_slug')) {
	function create_slug($str, $delimiter = '-'){
		return strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'UTF-8//TRANSLIT', $str))))), $delimiter));
	} 
} 


/**
 * get rec campaign hp
 * @param  string $id
 * @return string
 */
function get_rec_campaign_hp($id = ''){
	if($id != ''){
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'rec_campaign');
		$builder->where('cp_id', $id);
		return $builder->get()->getRow();
	}elseif ($id == '') {
		$builder = db_connect('default');
		$builder = $builder->table(get_db_prefix().'rec_campaign');
		return $builder->get()->result_array();
	}
}

/**
 * get status candidate
 * @param  int $status
 * @return string
 */
function get_status_candidate($status){
	$result = '';
	if($status == 1){
		$result = '<span class="badge bg-info large mt-0">'.app_lang('application').'</span>';
	}elseif($status == 2){
		$result = '<span class="badge potential-style large mt-0">'.app_lang('potential').'</span>';
	}elseif($status == 3){
		$result = '<span class="badge interview-style large mt-0">'.app_lang('interview').'</span>';
	}elseif($status == 4){
		$result = '<span class="badge won_interview-style large mt-0">'.app_lang('won_interview').'</span>';
	}elseif($status == 5){
		$result = '<span class="badge send_offer-style large mt-0">'.app_lang('send_offer').'</span>';
	}elseif($status == 6){
		$result = '<span class="badge elect-style large mt-0">'.app_lang('elect').'</span>';
	}elseif($status == 7){
		$result = '<span class="badge non_elect-style large mt-0">'.app_lang('non_elect').'</span>';
	}elseif($status == 8){
		$result = '<span class="badge unanswer-style large mt-0">'.app_lang('unanswer').'</span>';
	}elseif($status == 9){
		$result = '<span class="badge transferred-style large mt-0">'.app_lang('transferred').'</span>';
	}elseif($status == 10){
		$result = '<span class="badge bg-coral large mt-0">'.app_lang('freedom').'</span>';
	}

	return $result;
}

/**
 * candidate profile image
 * @param  int $id     
 * @param  array  $classes
 * @param  string $type   
 * @param  array  $img_attrs
 * @return string
 */
function candidate_profile_image($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
	$url = get_file_uri('plugins/Recruitment/Uploads/none_avatar.jpg');

	$_attributes = '';
	foreach ($img_attrs as $key => $val) {
		$_attributes .= $key . '=' . '"' . $val . '" ';
	}

	$blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';

	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'files');
	$builder->where('rel_id',$id);
	$builder->where('rel_type','rec_cadidate_avar');
	$result = $builder->get()->getRow();  


	if ($result && $result->file_name !== null) {
		$profileImagePath = get_file_uri('plugins/Recruitment/Uploads/candidate/avartar/'.$id . '/' . $result->file_name);
		if ($profileImagePath) {
			
			$profile_image = '<img ' . $_attributes . ' src="' . $profileImagePath . '" class="' . implode(' ', $classes) . '" />';
		} else {
			
			return $blankImageFormatted;
		}
	} else {
		$profile_image = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';
	}
	return $profile_image;
}

/**
 * candidate profile image url
 * @param  [type] $id        
 * @param  array  $classes   
 * @param  string $type      
 * @param  array  $img_attrs 
 * @return [type]            
 */
function candidate_profile_image_url($id, $classes = ['staff-profile-image'], $type = 'small', $img_attrs = [])
{
	$url = get_file_uri('plugins/Recruitment/Uploads/none_avatar.jpg');

	$_attributes = '';
	foreach ($img_attrs as $key => $val) {
		$_attributes .= $key . '=' . '"' . html_escape($val) . '" ';
	}

	$blankImageFormatted = '<img src="' . $url . '" ' . $_attributes . ' class="' . implode(' ', $classes) . '" />';

	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'files');
	$builder->where('rel_id',$id);
	$builder->where('rel_type','rec_cadidate_avar');
	$result = $builder->get()->getRow();  


	if ($result && $result->file_name !== null) {
		$profileImagePath = get_file_uri('plugins/Recruitment/Uploads/candidate/avartar/'.$id . '/' . $result->file_name);
		if ($profileImagePath) {
			$profile_image = $profileImagePath;
		} else {
			return $url;
		}
	} else {
		$profile_image = $url;
	}
	return $profile_image;
}

/**
 * get candidate name
 * @param  int $id
 * @return string
 */
function get_candidate_name($id){
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'rec_candidate');
	$builder->where('id',$id);
	$candidate = $builder->get()->getRow();
	if($candidate && $candidate->candidate_name != ''){
		return $candidate->candidate_name.' '.$candidate->last_name;
	}else{
		return '';
	}
}

/**
 * get candidate interview
 * @param  int $id
 * @return 
 */
function get_candidate_interview($id){
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'cd_interview');
	$builder->where('interview',$id);
	$data_rs = array();
	$cdinterview = $builder->get()->result_array();
	
	foreach($cdinterview as $cd){
		$data_rs[] = $cd['candidate'];
	}

	return $data_rs;
}

/**
 * count criteria
 * @param  int $id
 * @return int
 */
function count_criteria($id){
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'rec_list_criteria');
	$builder->where('evaluation_form',$id);
	$list = $builder->get()->result_array();

	return count($list);
}

/**
 * get criteria name
 * @param  int $id
 * @return string
 */
function get_criteria_name($id){
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'rec_criteria');
	$builder->where('criteria_id',$id);
	$builder->select('criteria_title');
	$list = $builder->get()->getRow();
	if($list->criteria_title){
		return $list->criteria_title;
	}else{
		return '';
	}
	
}

/**
 * handle rec set transfer record
 * @param  int $id
 * @return bool
 */
function handle_rec_set_transfer_record($id){

	if (isset($_FILES['attachment']['name']) && $_FILES['attachment']['name'] != '') {
		hooks()->do_action('before_upload_contract_attachment', $id);
		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/set_transfer/'. $id . '/';
		// Get the temp file path
		$tmpFilePath = $_FILES['attachment']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {
			_maybe_create_upload_path($path);
			$filename    = unique_filename($path, $_FILES['attachment']['name']);

			$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {
				$CI           = & get_instance();
				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['attachment']['type'],
				];
				$CI->misc_model->add_attachment_to_database($id, 'rec_set_transfer', $attachment);

				return true;
			}
		}
	}

	return false;
}

/**
 * Gets the staff email by identifier.
 *
 * @param      int   $id     The identifier
 *
 * @return     String  The staff email by identifier.
 */
function get_staff_email_by_id_rec($id)
{
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'users');

	$builder->where('id', $id);
	$staff = $builder->get()->getRow();

	return ($staff ? $staff->email : '');
}


/**
 * [handle rec candidate file form description]
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function handle_rec_candidate_file_form($id){
	if (isset($_FILES['file-input']['name']) && $_FILES['file-input']['name'] != '') {

		hooks()->do_action('before_upload_contract_attachment', $id);
		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/files/'. $id . '/';
		// Get the temp file path
		$tmpFilePath = $_FILES['file-input']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {
			_maybe_create_upload_path($path);
			$filename    = unique_filename($path, $_FILES['file-input']['name']);

			$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {
				$CI           = & get_instance();
				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['file-input']['type'],
				];
				$CI->misc_model->add_attachment_to_database($id, 'rec_cadidate_file', $attachment);

				return true;
			}
		}
	}
	return false;
}


/**
 * get_kan ban status candidate color
 * @param  integer  $status 
 * @param  boolean $name   
 * @return string         
 */
function get_kan_ban_status_candidate_color($status, $name = false){
	$result = '';
	if($name == false){
		if($status == 1){
			$result = 'application-style';
		}elseif($status == 2){
			$result = 'potential-style';
		}elseif($status == 3){
			$result = 'interview-style';
		}elseif($status == 4){
			$result = 'won_interview-style';
		}elseif($status == 5){
			$result = 'send_offer-style';
		}elseif($status == 6){
			$result = 'elect-style';
		}elseif($status == 7){
			$result = 'non_elect-style';
		}elseif($status == 8){
			$result = 'unanswer-style';
		}elseif($status == 9){
			$result = 'transferred-style';
		}elseif($status == 10){
			$result = 'freedom-style';
		}
	}else{
		if($status == 1){
			$result = app_lang('application');
		}elseif($status == 2){
			$result = app_lang('potential');
		}elseif($status == 3){
			$result = app_lang('interview');
		}elseif($status == 4){
			$result = app_lang('won_interview');
		}elseif($status == 5){
			$result = app_lang('send_offer');
		}elseif($status == 6){
			$result = app_lang('elect');
		}elseif($status == 7){
			$result = app_lang('non_elect');
		}elseif($status == 8){
			$result = app_lang('unanswer');
		}elseif($status == 9){
			$result = app_lang('transferred');
		}elseif($status == 10){
			$result = app_lang('freedom');
		}
	}

	return $result;
}

/**
 * Used for customer forms eq. leads form, builded from the form builder plugin
 * @param  object $field field from database
 * @return mixed
 */
function render_form_builder_field_recruitment($field)
{

	$type         = $field->type;
	$classNameCol = 'col-md-12';
	if (isset($field->className)) {
		if (strpos($field->className, 'form-col') !== false) {
			$classNames = explode(' ', $field->className);
			if (is_array($classNames)) {
				$classNameColArray = array_filter($classNames, function ($class) {
					return startsWith($class, 'form-col');
				});

				$classNameCol = implode(' ', $classNameColArray);
				$classNameCol = trim($classNameCol);

				$classNameCol = str_replace('form-col-xs', 'col-xs', $classNameCol);
				$classNameCol = str_replace('form-col-sm', 'col-sm', $classNameCol);
				$classNameCol = str_replace('form-col-md', 'col-md', $classNameCol);
				$classNameCol = str_replace('form-col-lg', 'col-lg', $classNameCol);

				// Default col-md-X
				$classNameCol = str_replace('form-col', 'col-md', $classNameCol);
			}
		}
	}

	echo '<div class="' . $classNameCol . '">';
	if ($type == 'header' || $type == 'paragraph') {
		echo '<' . $field->subtype . ' class="' . (isset($field->className) ? $field->className : '') . '">' . check_for_links(nl2br($field->label)) . '</' . $field->subtype . '>';
	} else {
		echo '<div class="form-group" data-type="' . $type . '" data-name="' . $field->name . '" data-required="' . (isset($field->required) ? true : 'false') . '">';
		echo '<label class="control-label" for="' . $field->name . '">' . (isset($field->required) ? ' <span class="text-danger">* </span> ': '') . $field->label . '' . (isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $field->description . '" data-placement="' . (is_rtl(true) ? 'left' : 'right') . '"></i>' : '') . '</label>';
		if (isset($field->subtype) && $field->subtype == 'color') {
			echo '<div class="input-group colorpicker-input">
			<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text"' . (isset($field->value) ? ' value="' . $field->value . '"' : '') . ' name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" />
			<span class="input-group-addon"><i></i></span>
			</div>';
		} elseif (($type == 'file' || $type == 'text' || $type == 'number') && ($field->name != 'skill')) {
			$ftype = isset($field->subtype) ? $field->subtype : $type;
			echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize="' . file_upload_max_size() . '"' : '') . '>';
		} elseif ($type == 'textarea') {
			echo '<textarea' . (isset($field->required) ? ' required="true"': '') . ' id="' . $field->name . '" name="' . $field->name . '" rows="' . (isset($field->rows) ? $field->rows : '4') . '" class="' . (isset($field->className) ? $field->className : '') . '" placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '">' . (isset($field->value) ? $field->value : '') . '</textarea>';
		} elseif ($type == 'date') {
			echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? to_decimal_format($field->value) : '') . '">';
		} elseif ($type == 'datetime-local') {
			echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datetimepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _dt($field->value) : '') . '">';
		} elseif ($type == 'select') {
			echo '<select' . (isset($field->required) ? ' required="true"': '') . '' . (isset($field->multiple) ? ' multiple="true"' : '') . ' class="' . (isset($field->className) ? $field->className : '') . '" name="' . $field->name . (isset($field->multiple) ? '[]' : '') . '" id="' . $field->name . '"' . (isset($field->values) && count($field->values) > 10 ? 'data-live-search="true"': '') . 'placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '">';
			$values = [];
			if (isset($field->values) && count($field->values) > 0) {
				foreach ($field->values as $option) {
					echo '<option value="' . $option->value . '" ' . (isset($option->selected) ? ' selected' : '') . '>' . $option->label . '</option>';
				}
			}
			echo '</select>';
		} elseif ($type == 'checkbox-group') {
			$values = [];
			if (isset($field->values) && count($field->values) > 0) {
				$i = 0;
				echo '<div class="chk">';
				foreach ($field->values as $checkbox) {
					echo '<div class="checkbox' . ((isset($field->inline) && $field->inline == 'true') || (isset($field->className) && strpos($field->className, 'form-inline-checkbox') !== false) ? ' checkbox-inline' : '') . '">';
					echo '<input' . (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="checkbox" id="chk_' . $field->name . '_' . $i . '" value="' . $checkbox->value . '" name="' . $field->name . '[]"' . (isset($checkbox->selected) ? ' checked' : '') . '>';
					echo '<label for="chk_' . $field->name . '_' . $i . '">';
					echo html_entity_decode($checkbox->label);
					echo '</label>';
					echo '</div>';
					$i++;
				}
				echo '</div>';
			}
		}
		echo '</div>';
	}
	echo '</div>';
}


/**
 * handle company attchment
 * @param  integer $id
 * @return array or row
 */
function handle_company_attachments($id)
{

	$path = RECRUITMENT_COMPANY_UPLOAD . $id . '/';

	if (isset($_FILES['file']['name'])) {

		// 
		// Get the temp file path
		$tmpFilePath = $_FILES['file']['tmp_name'];
		// Make sure we have a filepath
		if (!empty($tmpFilePath) && $tmpFilePath != '') {

			_maybe_create_upload_path($path);
			$filename    = $_FILES['file']['name'];

			$newFilePath = $path . $filename;

			// Upload the file into the temp dir
			if (move_uploaded_file($tmpFilePath, $newFilePath)) {

				$attachment   = [];
				$attachment[] = [
					'file_name' => $filename,
					'filetype'  => $_FILES['file']['type'],
				];

				$Recruitment_model = model('Recruitment\Models\Recruitment_model');
				$Recruitment_model->add_attachment_to_database($id, 'rec_company', $attachment);
			}
		}
	}

}

/**
 * get industry name
 * @param  integer $id 
 * @return string     
 */
function get_rec_industry_name($id){

	
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'job_industry');
	$builder->where('id',$id);
	$job_industry = $builder->get()->getRow();

	if($job_industry){
		return $job_industry->industry_name;
	}else{
		return '';
	}

}

/**
 * get company name
 * @param  integer $id 
 * @return string    
 */
function get_rec_company_name($id)
{
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'rec_company');
	$builder->where('id',$id);
	$rec_company = $builder->get()->getRow();

	if($rec_company){
		return $rec_company->company_name;
	}else{
		return '';
	}

}

/*separation portal v1.1.2*/

/**
 * app rec portal head
 * @param  [type] $language 
 * @return [type]           
 */
function app_rec_portal_head($language = null)
{
	// $language param is deprecated
	if (is_null($language)) {
		$language = $GLOBALS['language'];
	}

	if (file_exists(FCPATH . 'assets/css/custom.css')) {
		echo '<link href="' . base_url('assets/css/custom.css') . '" rel="stylesheet" type="text/css" id="custom-css">' . PHP_EOL;
	}

	hooks()->do_action('app_rec_portal_head');
}

/**
 * get template part rec portal
* @param      string   $name    The name
 * @param      array    $data    The data
 * @param      boolean  $return  The return
 *
 * @return     string   The template part.
 */
function get_template_part_rec_portal($name, $data = [], $return = false)
{
	if ($name === '') {
		return '';
	}

	$CI   = & get_instance();
	$path = 'recruitment_portal/template_parts/';

	if ($return == true) {
		return $CI->load->view($path . $name, $data, true);
	}

	$CI->load->view($path . $name, $data);
}

/**
 * init rec_portal area assets.
 */
function init_rec_portal_area_assets()
{
	// Used by themes to add assets
	hooks()->do_action('app_rec_portal_assets');

	hooks()->do_action('app_client_assets_added');
}

/**
 * { register theme rec_portal assets hook }
 *
 * @param      <type>   $function  The function
 *
 * @return     boolean  
 */
function register_theme_rec_portal_assets_hook($function)
{
	if (hooks()->has_action('app_rec_portal_assets', $function)) {
		return false;
	}

	return hooks()->add_action('app_rec_portal_assets', $function, 1);
}


/**
 * get company name
 * @param  integer $id 
 * @return string    
 */
function get_rec_channel_form_key($id)
{
	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'rec_campaign_form_web');
	$builder->where('id',$id);
	$rec_campaign_form_web = $builder->get()->getRow();

	if($rec_campaign_form_web){
		return $rec_campaign_form_web->form_key;
	}else{
		return '';
	}

}

/**
 * is_rtl_rec
 * @param  boolean $client_area 
 * @return boolean              
 */
function is_rtl_rec($client_area = false)
{
	$CI = & get_instance();
	
	if ($client_area == true) {
		// Client not logged in and checked from clients area
		if (get_option('rtl_support_client') == 1) {
			return true;
		}
	} elseif (is_staff_logged_in()) {
		if (isset($GLOBALS['current_user'])) {
			$direction = $GLOBALS['current_user']->direction;
		} else {
			$CI->db->select('direction')->from(db_prefix() . 'staff')->where('staffid', get_staff_user_id1());
			$direction = $CI->db->get()->row()->direction;
		}

		if ($direction == 'rtl') {
			return true;
		} elseif ($direction == 'ltr') {
			return false;
		} elseif (empty($direction)) {
			if (get_option('rtl_support_admin') == 1) {
				return true;
			}
		}

		return false;
	} elseif ($client_area == false) {
		if (get_option('rtl_support_admin') == 1) {
			return true;
		}
	}

	return false;
}

/**
 * re pdf logo url
 * @return [type] 
 */
function re_pdf_logo_url()
{
	$custom_pdf_logo_image_url = get_option('custom_pdf_logo_image_url');
	$width                     = get_option('pdf_logo_width');
	$logoUrl                   = '';

	if ($width == '') {
		$width = 120;
	}
	if ($custom_pdf_logo_image_url != '') {
		$logoUrl = $custom_pdf_logo_image_url;
	} else {
		if (get_option('company_logo_dark') != '' && file_exists(get_upload_path_by_type('company') . get_option('company_logo_dark'))) {
			$logoUrl = get_upload_path_by_type('company') . get_option('company_logo_dark');
		} elseif (get_option('company_logo') != '' && file_exists(get_upload_path_by_type('company') . get_option('company_logo'))) {
			$logoUrl = get_upload_path_by_type('company') . get_option('company_logo');
		}
	}

	$logoImage = '';
	if ($logoUrl != '') {
		$logoImage = '<img class="logo_width" src="' . $logoUrl . '">';
	}

	return hooks()->apply_filters('pdf_logo_url', $logoImage);
}

/**
 * rec get status modules
 * @param  [type] $module_name 
 * @return [type]              
 */
function rec_get_status_modules($module_name){
	$plugins = get_setting("plugins");
	$plugins = @unserialize($plugins);
	if (!($plugins && is_array($plugins))) {
		$plugins = array();
	}
	
	if(isset($plugins[$module_name]) && $plugins[$module_name] == 'activated'){
		return true;
	}else{
		return false;
	}
}

function portal_handle_rec_candidate_file_form($id){
	if (isset($_FILES['file-input']['name']) && $_FILES['file-input']['name'] != '') {

		$path = RECRUITMENT_MODULE_UPLOAD_FOLDER .'/candidate/files/'. $id . '/';
		

		if(is_array($_FILES['file-input']['name'])){
			for ($i = 0; $i < count($_FILES['file-input']['name']) ; $i++) {

				// Get the temp file path
				$tmpFilePath = $_FILES['file-input']['tmp_name'][$i];

				// Make sure we have a filepath
				if (!empty($tmpFilePath) && $tmpFilePath != '') {
					_maybe_create_upload_path($path);
					$filename    = unique_filename($path, $_FILES['file-input']['name'][$i]);

					$newFilePath = $path . $filename;
				 // Upload the file into the company uploads dir
					if (move_uploaded_file($tmpFilePath, $newFilePath)) {
						$CI           = & get_instance();
						$attachment   = [];
						$attachment[] = [
							'file_name' => $filename,
							'filetype'  => $_FILES['file-input']['type'][$i],
						];
						$CI->misc_model->add_attachment_to_database($id, 'rec_cadidate_file', $attachment);

					}
				}

			}

			return true;

		}else{
			 // Get the temp file path
			$tmpFilePath = $_FILES['file-input']['tmp_name'];

			// Make sure we have a filepath
			if (!empty($tmpFilePath) && $tmpFilePath != '') {
				_maybe_create_upload_path($path);
				$filename    = unique_filename($path, $_FILES['file-input']['name']);

				$newFilePath = $path . $filename;
			// Upload the file into the company uploads dir
				if (move_uploaded_file($tmpFilePath, $newFilePath)) {
					$CI           = & get_instance();
					$attachment   = [];
					$attachment[] = [
						'file_name' => $filename,
						'filetype'  => $_FILES['file-input']['type'],
					];
					$CI->misc_model->add_attachment_to_database($id, 'rec_cadidate_file', $attachment);

					return true;
				}
			}

		}

	}
	return false;
}

/**
 * rec year experience
 * @return [type] 
 */
function rec_year_experience()
{
	$field_array = [];
	$field_array[] = [
		'label' => app_lang('no_experience_yet'),
		'value' => 'no_experience_yet',
	];
	$field_array[] = [
		'label' => app_lang('less_than_1_year'),
		'value' => 'less_than_1_year',
	];
	$field_array[] = [
		'label' => app_lang('1_year'),
		'value' => '1_year',
	];
	$field_array[] = [
		'label' => app_lang('2_years'),
		'value' => '2_years',
	];
	$field_array[] = [
		'label' => app_lang('3_years'),
		'value' => '3_years',
	];
	$field_array[] = [
		'label' => app_lang('4_years'),
		'value' => '4_years',
	];
	$field_array[] = [
		'label' => app_lang('5_years'),
		'value' => '5_years',
	];
	$field_array[] = [
		'label' => app_lang('over_5_years'),
		'value' => 'over_5_years',
	];

	return $field_array;
}

/**
 * get_rec_skill_name
 * @param  string $id 
 * @return [type]     
 */
function get_rec_skill_name($id = ''){
	$skill_name = '';

	$builder = db_connect('default');
	$builder = $builder->table(get_db_prefix().'rec_skill');

	$builder->where('id', $id);
	$rec_skill = $builder->get()->getRow();
	if($rec_skill){
		$skill_name = $rec_skill->skill_name;
	}
	return $skill_name;
}

if (!function_exists('re_delete_company_files')) {

	function re_delete_company_files($file_path, $delete_files) {
		if ($delete_files && $file_path) {
			$request = \Config\Services::request();

            //is deleted any file?
			if (!is_array($delete_files)) {
				$delete_files = array();
			}

            //delete files from directory and update the database array
			foreach ($delete_files as $file) {
				delete_app_files($file_path, array($file));
			}
		}
		return true;
	}

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
		/*Separate the filename into a name and extension.*/
		$info     = pathinfo($filename);
		$ext      = !empty($info['extension']) ? '.' . $info['extension'] : '';

		$number   = '';
		/*Change '.ext' to lower case.*/
		if ($ext && strtolower($ext) != $ext) {
			$ext2      = strtolower($ext);
			$filename2 = preg_replace('|' . preg_quote($ext) . '$|', $ext2, $filename);
			/*Check for both lower and upper case extension or image sub-sizes may be overwritten.*/
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

if (!function_exists('re_has_permission')) {
	function re_has_permission($staff_permission, $staffid = '')
	{
		$db = db_connect('default');
		$dbprefix = get_db_prefix();
		if (!$db->fieldExists("plugins_permissions1" ,$dbprefix . "roles")) { 
			return false;
		}

		$ci = new Security_Controller(false);
		$Recruitment_model = model("Recruitment\Models\Recruitment_model");
		$login_user = $Recruitment_model->plugin_get_access_info($ci->login_user->id);
		$permissions = array();
		if($login_user->permissions != null){
			$permissions = is_array(unserialize($login_user->permissions)) ? unserialize($login_user->permissions) : array();
		}

		if($ci->login_user->is_admin){
			return true;
		}

		if(get_array_value($permissions, $staff_permission)){
			return true;
		}
		return false;
	}
}

if(!function_exists('get_base_currency')){
    function get_base_currency(){
        return get_setting('default_currency');
    }
}

/**
 * applied jobs status
 * @param  string $status 
 * @return [type]         
 */
function applied_jobs_status($status='')
{

    $statuses = [

        [
            'id'             => '1',
            'color'          => '#28B8DA',
            'name'           => app_lang('application'),
            'order'          => 1,
            'filter_default' => true,
        ],
        [
            'id'             => '2',
            'color'          => '#CD853F',
            'name'           => app_lang('potential'),
            'order'          => 2,
            'filter_default' => true,
        ],
        [
            'id'             => '3',
            'color'          => '#00FF33',
            'name'           => app_lang('interview'),
            'order'          => 3,
            'filter_default' => true,
        ],
        [
            'id'             => '4',
            'color'          => '#CCCC00',
            'name'           => app_lang('won_interview'),
            'order'          => 4,
            'filter_default' => false,
        ],
        [
            'id'             => '5',
            'color'          => '#FF9999',
            'name'           => app_lang('send_offer'),
            'order'          => 5,
            'filter_default' => false,
        ],
        [
            'id'             => '6',
            'color'          => '#999966',
            'name'           => app_lang('elect'),
            'order'          => 6,
            'filter_default' => false,
        ],
        [
            'id'             => '7',
            'color'          => '#FF6666',
            'name'           => app_lang('non_elect'),
            'order'          => 7,
            'filter_default' => false,
        ],
        [
            'id'             => '8',
            'color'          => '#FF3333',
            'name'           => app_lang('unanswer'),
            'order'          => 8,
            'filter_default' => false,
        ],
        [
            'id'             => '9',
            'color'          => '#8B4726',
            'name'           => app_lang('transferred'),
            'order'          => 9,
            'filter_default' => false,
        ],
        [
            'id'             => '10',
            'color'          => '#FF34B3',
            'name'           => app_lang('freedom'),
            'order'          => 10,
            'filter_default' => false,
        ],
        
    ];
        
    usort($statuses, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $statuses;
}

/**
 * re get status by id
 * @param  [type] $id   
 * @param  [type] $type 
 * @return [type]       
 */
function re_get_status_by_id($id, $type)
{

    if($type == 'applied_job'){
        $statuses = applied_jobs_status();
        $status = [
            'id'         => 0,
            'color'   => '#28B8DA',
            'color' => '#28B8DA',
            'name'       => app_lang('application'),
            'order'      => 1,
        ];
    }else{
        $statuses = applied_jobs_status();
        $status = [
            'id'         => 0,
            'color'   => '#28B8DA',
            'color' => '#28B8DA',
            'name'       => app_lang('application'),
            'order'      => 1,
        ];
    }

    foreach ($statuses as $s) {
        if ($s['id'] == $id) {
            $status = $s;

            break;
        }
    }

    return $status;
}

/**
 * re render status html
 * @param  [type]  $id           
 * @param  [type]  $type         
 * @param  string  $status_value 
 * @param  boolean $ChangeStatus 
 * @return [type]                
 */
function re_render_status_html($id, $type, $status_value = '', $ChangeStatus = true)
{
	$status          = re_get_status_by_id($status_value, $type);

	if($type == 'applied_job'){
		$task_statuses = applied_jobs_status();
	}else{
		$task_statuses = applied_jobs_status();
	}
	$outputStatus    = '';
	if(is_candidate_logged_in()){
		$canChangeStatus = false;
	}else{
		$canChangeStatus = (re_has_permission('recruitment_can_edit') || is_admin());
	}

	if ($canChangeStatus && $ChangeStatus) {
		$outputStatus .= '<span class="dropdown inline-block text-white" style="background-color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $status_value . '">';
	}else{

		$outputStatus .= '<span class="dropdown inline-block label badge  large" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $status_value . '">';
	}

	if ($canChangeStatus && $ChangeStatus) {

		$outputStatus .= '<button id="tableTaskStatus-' . $id . '" class="btn text-white dropdown-toggle caret mt0 mb0" style="background-color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" type="button" data-bs-toggle="dropdown" aria-expanded="true">
								'.$status['name'].'
							';

		$outputStatus .= '<span data-toggle="tooltip" title="' . app_lang('ticket_single_change_status') . '"><i class="fa fa-caret-down" aria-hidden="true"></i></span>';
		$outputStatus .= '</button>';

		$outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="tableTaskStatus-' . $id . '">';
		foreach ($task_statuses as $taskChangeStatus) {
			if ($status_value != $taskChangeStatus['id']) {
				$outputStatus .= '<li role="presentation">
				<a class="dropdown-item" href="#" onclick="re_status_mark_as(\'' . $taskChangeStatus['id'] . '\',' . $id . ',\'' . $type . '\'); return false;">
				' . $taskChangeStatus['name'] . '
				</a>
				</li>';
			}
		}
		$outputStatus .= '</ul>';
	}else{
		$outputStatus    .= $status['name'];
	}

	$outputStatus .= '</span>';

	return $outputStatus;
}


if (!function_exists('re_render_form_builder_field')) {
    /**
     * Used for customer forms eq. leads form, builded from the form builder plugin
     * @param  object $field field from database
     * @return mixed
     */
    function re_render_form_builder_field($field)
    {
        $type         = $field->type;
        $classNameCol = 'col-md-12';
        if (isset($field->className)) {
            if (strpos($field->className, 'form-col') !== false) {
                $classNames = explode(' ', $field->className);
                if (is_array($classNames)) {
                    $classNameColArray = array_filter($classNames, function ($class) {
                        return startsWith($class, 'form-col');
                    });

                    $classNameCol = implode(' ', $classNameColArray);
                    $classNameCol = trim($classNameCol);

                    $classNameCol = str_replace('form-col-xs', 'col-xs', $classNameCol);
                    $classNameCol = str_replace('form-col-sm', 'col-sm', $classNameCol);
                    $classNameCol = str_replace('form-col-md', 'col-md', $classNameCol);
                    $classNameCol = str_replace('form-col-lg', 'col-lg', $classNameCol);

                    // Default col-md-X
                    $classNameCol = str_replace('form-col', 'col-md', $classNameCol);
                }
            }
        }

        echo '<div class="' . $classNameCol . '">';
        if ($type == 'header' || $type == 'paragraph') {
            echo '<' . $field->subtype . ' class="' . (isset($field->className) ? $field->className : '') . '">' . (nl2br($field->label)) . '</' . $field->subtype . '>';
        } else {
            echo '<div class="form-group" data-type="' . $type . '" data-name="' . $field->name . '" data-required="' . (isset($field->required) ? true : 'false') . '">';
            echo '<label class="control-label" for="' . $field->name . '">' . (isset($field->required) ? ' <span class="text-danger">* </span> ': '') . $field->label . '' . (isset($field->description) ? ' <i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . $field->description . '" data-placement="' . (is_rtl(true) ? 'left' : 'right') . '"></i>' : '') . '</label>';
            if (isset($field->subtype) && $field->subtype == 'color') {
                echo '<div class="input-group colorpicker-input">
         <input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text"' . (isset($field->value) ? ' value="' . $field->value . '"' : '') . ' name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" />
             <span class="input-group-addon"><i></i></span>
         </div>';
            } elseif ($type == 'text' || $type == 'number') {
                $ftype = isset($field->subtype) ? $field->subtype : $type;


                echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . $field->name . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize=""' : '') . (isset($field->step) ? 'step="'. $field->step.'"' : '')  . (isset($field->min) ? 'min="'. $field->min.'"' : '') . (isset($field->max) ? 'max="'. $field->max.'"' : '')  . (isset($field->maxlength) ? 'maxlength="'. $field->maxlength.'"' : '') . '>';
            } elseif ($type == 'file') {
                $ftype = isset($field->subtype) ? $field->subtype : $type;
                echo '<input' . (isset($field->required) ? ' required="true"': '') . (isset($field->placeholder) ? ' placeholder="' . $field->placeholder . '"' : '') . ' type="' . $ftype . '" name="' . (isset($field->multiple) ? $field->name . "[]" : $field->name ) . '" id="' . $field->name . '" class="' . (isset($field->className) ? $field->className : '') . '" value="' . (isset($field->value) ? $field->value : '') . '"' . ($field->type == 'file' ? ' accept="' . get_form_accepted_mimes() . '" filesize=""' : '') . (isset($field->step) ? 'step="'. $field->step.'"' : ''). (isset($field->multiple) ? 'multiple="'. $field->multiple.'"' : '').'>';
            } elseif ($type == 'textarea') {
                echo '<textarea' . (isset($field->required) ? ' required="true"': '') . ' id="' . $field->name . '" name="' . $field->name . '" rows="' . (isset($field->rows) ? $field->rows : '4') . '" class="' . (isset($field->className) ? $field->className : '') . '" placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '"'. (isset($field->maxlength) ? 'maxlength="'. $field->maxlength.'"' : '') . '>'
                 . (isset($field->value) ? $field->value : '') . '</textarea>';
            } elseif ($type == 'date') {
                echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _d($field->value) : '') . '">';
            } elseif ($type == 'datetime-local') {
                echo '<input' . (isset($field->required) ? ' required="true"': '') . ' placeholder="' . (isset($field->placeholder) ? $field->placeholder : '') . '" type="text" class="' . (isset($field->className) ? $field->className : '') . ' datetimepicker" name="' . $field->name . '" id="' . $field->name . '" value="' . (isset($field->value) ? _dt($field->value) : '') . '">';
            } elseif ($type == 'select') {
                echo '<select' . (isset($field->required) ? ' required="true"': '') . '' . (isset($field->multiple) ? ' multiple="true"' : '') . ' class="select2 validate-hidden' . (isset($field->className) ? $field->className : '') . '" name="' . $field->name . (isset($field->multiple) ? '[]' : '') . '" id="' . $field->name . '"' . (isset($field->values) && count($field->values) > 10 ? 'data-live-search="true"': '') . 'data-none-selected-text="' . (isset($field->placeholder) ? $field->placeholder : '') . '">';
                $values = [];
                if (isset($field->values) && count($field->values) > 0) {
                    foreach ($field->values as $option) {
                        echo '<option value="' . $option->value . '" ' . (isset($option->selected) ? ' selected' : '') . '>' . $option->label . '</option>';
                    }
                }
                echo '</select>';
            } elseif ($type == 'checkbox-group') {
                $values = [];
                if (isset($field->values) && count($field->values) > 0) {
                    $i = 0;
                    echo '<div class="chk">';
                    foreach ($field->values as $checkbox) {
                        echo '<div class="checkbox' . ((isset($field->inline) && $field->inline == 'true') || (isset($field->className) && strpos($field->className, 'form-inline-checkbox') !== false) ? ' checkbox-inline' : '') . '">';
                        echo '<input' . (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="checkbox" id="chk_' . $field->name . '_' . $i . '" value="' . $checkbox->value . '" name="' . $field->name . '[]"' . (isset($checkbox->selected) ? ' checked' : '') . '>';
                        echo '<label for="chk_' . $field->name . '_' . $i . '">';
                        echo html_entity_decode($checkbox->label);
                        echo '</label>';
                        echo '</div>';
                        $i++;
                    }
                    echo '</div>';
                }
            } elseif ($type == 'radio-group') {
                if (isset($field->values) && count($field->values) > 0) {
                    $i = 0;
                    foreach ($field->values as $radio) {
                        echo '<div class="radio ' . ((isset($field->inline) && $field->inline == true) || (isset($field->className) && strpos($field->className, 'form-inline-radio') !== false) ? ' radio-inline' : '') . '">';
                        echo '  <input '. (isset($field->required) ? ' required="true"': '') . ' class="' . (isset($field->className) ? $field->className : '') . '" type="radio"';
                        echo 'name="' . $field->name . '" id="radio_' . $field->name . '_' . $i . '"';
                        echo 'value="' . $radio->value . '"' . (isset($radio->selected) ? ' checked' : '') . '>';
                       echo '<label for="radio_' . $field->name . '_' . $i . '">';
                        echo html_entity_decode($radio->label);
                        echo '</label>';
                        echo '</div>';
                        $i++;
                    }
                }
            }

            echo '</div>';
        }
        echo '</div>';
    }
}

if (!function_exists('get_form_accepted_mimes')) {
	function get_form_accepted_mimes()
	{
		$allowed_extensions  = get_setting('accepted_file_formats');
		$_allowed_extensions = array_map(function ($ext) {
			return trim($ext);
		}, explode(',', $allowed_extensions));

		$all_form_ext = '';

		$all_form_ext = rtrim($allowed_extensions, ', ');

		return $all_form_ext;
	}
}

/**
 * is candidate logged in
 * @return boolean 
 */
function is_candidate_logged_in()
{
	$Candidates_model = model('Recruitment\Models\Candidates_model');
	return $Candidates_model->login_user_id();
}

/**
 * get candidate id
 * @return [type] 
 */
function get_candidate_id()
{
    $Candidates_model = model('Recruitment\Models\Candidates_model');
	return $Candidates_model->login_user_id();
}

if (!function_exists('get_candidate_image')) {
	
	function get_candidate_image($staff_id, $include_name = true)
	{
		$staff_image = '';
		if(is_numeric($staff_id) && $staff_id != 0){

			$get_staff_infor = get_staff_infor($staff_id);
			if($get_staff_infor){
				$staff_image .= '<span class="avatar-xs avatar me-1" data-bs-toggle="tooltip" data-bs-original-title="'.$get_staff_infor->first_name.' '.$get_staff_infor->last_name.'">
				<img alt="..." src="'.get_avatar($get_staff_infor->image).'" >
				</span>';

			}

			if( $include_name && $get_staff_infor){
				$staff_image .= '<span class="user-name ml10">'.$get_staff_infor->first_name.' '.$get_staff_infor->last_name.'</span>';
			}
		}

		return $staff_image;
	}
}

if (!function_exists('prepare_dowload_candidate_pdf')) {

	function prepare_dowload_candidate_pdf($candidate_data, $mode = "download") {
		$pdf = new Pdf();
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetCellPadding(1.5);
		$pdf->setImageScale(1.42);
		$pdf->AddPage();
		$pdf->SetFontSize(9);

		if ($candidate_data) {

			$candidate_data["mode"] = clean_data($mode);

			$html = view("Recruitment\Views\candidate_profile\\export_candidate_pdf", $candidate_data);

			if ($mode != "html") {
				$pdf->writeHTML($html, true, false, true, false, '');
			}
			$candidate = $candidate_data['candidate'];
			$pdf_file_name = $candidate['candidate_code'].'_'.$candidate['candidate_name'].'_'.$candidate['last_name'].date("YmdHis"). ".pdf";

			if ($mode === "download") {
				$pdf->Output($pdf_file_name, "D");
			} else if ($mode === "send_email") {
				$temp_download_path = getcwd() . "/" . get_setting("temp_file_path") . $pdf_file_name;
				$pdf->Output($temp_download_path, "F");
				return $temp_download_path;
			} else if ($mode === "view") {
				$pdf->SetTitle($pdf_file_name);
				$pdf->Output($pdf_file_name, "I");
				exit;
			} else if ($mode === "html") {
				return $html;
			}
		}
	}

}
