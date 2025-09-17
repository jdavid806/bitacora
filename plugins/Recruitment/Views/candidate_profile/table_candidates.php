<?php
$Recruitment_model = model("Recruitment\Models\Recruitment_model");

$aColumns = [
	get_db_prefix().'rec_candidate.id',  
	'candidate_code',  
	'candidate_name',
	'rate',
	'skill',
	'status',
	'email',
	'phonenumber', 
	'birthday',
	'gender',
	'marital_status',
	'rec_campaign',
	'1',
];

$sIndexColumn = 'id';
$sTable       = get_db_prefix().'rec_candidate';
$join         = [];
$where = [];
$string_query='';
if(isset($dataPost['campaign_filter'])){
	$campaign_filter = $dataPost['campaign_filter'];
}
if(isset($dataPost['status_filter'])){
	$status_filter = $dataPost['status_filter'];
}

if(isset($dataPost['company_filter']) && $dataPost['company_filter'] != ''){
	$company_filter = $dataPost['company_filter'];
}
if(isset($dataPost['skill_filter'])){
	$skill_filter = $dataPost['skill_filter'];
}
if(isset($dataPost['job_title_filter'])){
	$job_title_filter = $dataPost['job_title_filter'];
}
if(isset($dataPost['experience_filter'])){
	$experience_filter = $dataPost['experience_filter'];
}
if(isset($dataPost['age_group_filter']) && $dataPost['age_group_filter'] !=''){
	$age_group_filter = $dataPost['age_group_filter'];
}
if(isset($dataPost['gender_filter'])){
	$gender_filter = $dataPost['gender_filter'];
}
if(isset($dataPost['marital_status_filter'])){
	$marital_status_filter = $dataPost['marital_status_filter'];
}


if(isset($campaign_filter)&&($campaign_filter!='')){
	$campaign_filter=implode(',',$campaign_filter);
	array_push($where, "AND rec_campaign IN (". $campaign_filter.")");
}

if(isset($status_filter)&&($status_filter!='')){
	$status_filter=implode(',',$status_filter);
	array_push($where, "AND status IN (". $status_filter.")");

}

if(isset($company_filter)&&($company_filter!='')){
	$campaign_by_company = $Recruitment_model->get_recruitment_campaign_by_company($company_filter);
	if(count($campaign_by_company) > 0){
		$campaign_by_company_where = '';
		foreach ($campaign_by_company as $campaign_id) {
			if ($campaign_id != '') {
				if ($campaign_by_company_where == '') {
					$campaign_by_company_where .= 'AND (rec_campaign = '.$campaign_id;
				} else {
					$campaign_by_company_where .= ' OR rec_campaign = '.$campaign_id;
				}
			}
		}

		if ($campaign_by_company_where != '') {
			$campaign_by_company_where .= ')';
			$where[] = $campaign_by_company_where;
		}
	}else{
		$where[] = 'AND 1=2';
	}

}

if(isset($skill_filter)&&($skill_filter!='')){

	$skill_where = '';
	foreach ($skill_filter as $skill_id) {
		if ($skill_id != '') {
			if ($skill_where == '') {
				$skill_where .= 'AND (find_in_set(' . $skill_id . ', ' . get_db_prefix() . 'rec_candidate.skill) ';
			} else {
				$skill_where .= ' OR find_in_set(' . $skill_id . ', ' . get_db_prefix() . 'rec_candidate.skill) ';
			}
		}
	}

	if ($skill_where != '') {
		$skill_where .= ')';
		$where[] = $skill_where;
	}
}

if(isset($job_title_filter)&&($job_title_filter!='')){
	$campaign_by_job = $Recruitment_model->get_recruitment_campaign_by_job($job_title_filter);
	if(count($campaign_by_job) > 0){
		$campaign_by_job_where = '';
		foreach ($campaign_by_job as $campaign_id) {
			if ($campaign_id != '') {
				if ($campaign_by_job_where == '') {
					$campaign_by_job_where .= 'AND (rec_campaign = '.$campaign_id;
				} else {
					$campaign_by_job_where .= ' OR rec_campaign = '.$campaign_id;
				}
			}
		}

		if ($campaign_by_job_where != '') {
			$campaign_by_job_where .= ')';
			$where[] = $campaign_by_job_where;
		}
	}else{
		$where[] = 'AND 1=2';
	}
}
if(isset($experience_filter)&&($experience_filter!='')){
	$experience_where = '';
	foreach ($experience_filter as $experience_value) {
		if ($experience_value != '') {
			if ($experience_where == '') {
				$experience_where .= 'AND (find_in_set("' . $experience_value . '", ' . get_db_prefix() . 'rec_candidate.year_experience) ';
			} else {
				$experience_where .= ' OR find_in_set("' . $experience_value . '", ' . get_db_prefix() . 'rec_candidate.year_experience) ';
			}
		}
	}

	if ($experience_where != '') {
		$experience_where .= ')';
		$where[] = $experience_where;
	}
}
if(isset($age_group_filter)&&($age_group_filter!='')){
	$current_year = date('Y');
	if(strlen($age_group_filter) == 2){
		$start_year = (int)$current_year - (int)$age_group_filter;
		$start_year = $start_year.'-01-01';

		$where[] = 'AND birthday <= "'.$start_year.'"';
	}else{
		$arr_age = explode("/", $age_group_filter);
		$start_year = (int)$current_year - (int)$arr_age[0];
		$end_year = (int)$current_year - (int)$arr_age[1];
		$start_year = $start_year.'-12-31';
		$end_year = $end_year.'-01-01';

		$where[] = 'AND (birthday >= "'.$end_year .'" AND  birthday <= "'.$start_year.'")';
	}
}

if (isset($dataPost['birthday_filter']) && $dataPost['birthday_filter'] != '') {
	array_push($where, "AND date_format(birthday, '%Y-%m-%d') <= '" . date('Y-m-d', strtotime(to_sql_date1($dataPost['birthday_filter']))) . "'");
}

if (isset($dataPost['gender_filter'])) {
	$gender_filter = $dataPost['gender_filter'];
	$where_gender_filter = '';
	foreach ($gender_filter as $y) {
		if ($y != '') {
			if ($where_gender_filter == '') {
				$where_gender_filter .= 'AND ('.get_db_prefix().'rec_candidate.gender = "' . $y . '"';
			} else {
				$where_gender_filter .= ' or '.get_db_prefix().'rec_candidate.gender = "' . $y . '"';
			}
		}
	}
	if ($where_gender_filter != '') {
		$where_gender_filter .= ')';
		array_push($where, $where_gender_filter);
	}
}

if (isset($dataPost['marital_status_filter'])) {
	$marital_status_filter = $dataPost['marital_status_filter'];
	$where_marital_status_filter = '';
	foreach ($marital_status_filter as $y) {
		if ($y != '') {
			if ($where_marital_status_filter == '') {
				$where_marital_status_filter .= 'AND ('.get_db_prefix().'rec_candidate.marital_status = "' . $y . '"';
			} else {
				$where_marital_status_filter .= ' or '.get_db_prefix().'rec_candidate.marital_status = "' . $y . '"';
			}
		}
	}
	if ($where_marital_status_filter != '') {
		$where_marital_status_filter .= ')';
		array_push($where, $where_marital_status_filter);
	}
}


if($string_query!=''){
	$string_query=rtrim($string_query," AND");
	$where=["where".' '.$string_query];
}


$result = data_tables_init1($aColumns, $sIndexColumn, $sTable, $join, $where, [get_db_prefix().'rec_candidate.id as id', 'last_name', 'gender', 'birthplace', 'home_town', 'place_of_issue', 'nationality', 'nation', 'religion', 'current_accommodation', 'alternate_contact_number', 'desired_salary', 'birthplace', 'home_town', 'identification', 'days_for_identity','place_of_issue', 'nationality', 'nation', 'height', 'weight', 'introduce_yourself', 'interests', 'phonenumber', 'skype',  'facebook',  'linkedin', 'resident', 'current_accommodation' ], '', [], $dataPost);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {

		if($aColumns[$i] == 'id'){
			$_data = $aRow['id'];
		}elseif($aColumns[$i] == 'candidate_name'){
			$name = '<a href="#">'.candidate_profile_image($aRow['id'],[
				'staff-profile-image-small mright5',
			], 'small').'</a>';

			if (re_has_permission("recruitment_can_view_global") || is_admin()) {
				$name .= '<a href="' . site_url('recruitment/candidate/' . $aRow['id'] ).'" > ' . $aRow['candidate_name'].' '.$aRow['last_name']. '</a>';
			}else{
				$name .= '<a href="#" > ' . $aRow['candidate_name'].' '.$aRow['last_name']. '</a>';

			}


			$_data = $name;
		}elseif ($aColumns[$i] == 'birthday') {
			$_data = format_to_date($aRow['birthday'], false);
		}elseif ($aColumns[$i] == 'rec_campaign') {
			if($aRow['rec_campaign'] != null){

				$cp = get_rec_campaign_hp($aRow['rec_campaign']);
				if(isset($cp)){
					$_data = $cp->campaign_code.' - '.$cp->campaign_name;
				}else{
					$_data = '';
				}
			}else{
				$_data = '';

			}
			
		}elseif($aColumns[$i] == 'rate'){
			if (re_has_permission("recruitment_can_edit") || is_admin()) {
				if($aRow['status'] == 6){
					$_data = '<a href="' . site_url('recruitment/transfer_to_hr/' . $aRow['id'] ).'" class="btn btn-sm btn-success" >' .app_lang('tranfer_personnels') .'</a>';
				}else{
					$_data = '';
				}
			}else{
				$_data = '';
			}
		}elseif($aColumns[$i] == 'status'){
			$_data = get_status_candidate($aRow['status']);
		}elseif($aColumns[$i] == 'skill'){
			$skill_name_data = '';

			if(strlen($aRow['skill']) > 0){
				$skill_id = explode(',', $aRow['skill']);
				foreach($skill_id as $dpkey =>  $skill){ 
					if(strlen(get_rec_skill_name($skill)) > 0){
						$skill_name_data .= '<span class="label label-tag tag-id-1"><span class="tag">' .get_rec_skill_name($skill).'</span><span class="hide">, </span></span>&nbsp';
					}

					if($dpkey%3 ==0){
						$skill_name_data .='<br/>';
					}

				}
			}

			$_data = $skill_name_data;

		}elseif($aColumns[$i] == 'gender'){
			if($aRow['gender'] != null && strlen($aRow['gender']) > 0){
				$_data = app_lang($aRow['gender']);
			}else{
				$_data = $aRow['gender'];
			}

		}elseif($aColumns[$i] == 'marital_status'){
			if(strlen($aRow['marital_status']) > 0){
				$_data = app_lang($aRow['marital_status']);
			}else{
				$_data = '';
			}

		}elseif($aColumns[$i] == 'phonenumber'){
			$phonenumber_data = '';
			$phonenumber_data .= $aRow['phonenumber'];

			if(strlen($aRow['alternate_contact_number']) > 0){
				$phonenumber_data .= '<br/>'.app_lang('alternate_number').': '. $aRow['alternate_contact_number'];
			}
			$_data = $phonenumber_data;

		}elseif($aColumns[$i] == '1'){

			$view = '<li role="presentation"><a href="' . site_url('recruitment/candidate/' . $aRow['id'] ).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';

			$edit = '';
			if(re_has_permission("recruitment_can_edit")){
				$edit = '<li role="presentation"><a href="' . site_url('recruitment/candidates/' . $aRow['id'] ) .'" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('edit') . '</a></li>';
			}
			$delete = '';
			if (re_has_permission("recruitment_can_delete")) {

				$delete .= '<li role="presentation">' .modal_anchor(get_uri("recruitment/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['id'], "data-post-function" => 'delete_candidate', "class" => 'dropdown-item' )). '</li>';
			}

			$_data = '';
			if(strlen($view) > 0 || strlen($edit) > 0 || strlen($delete) > 0 ){

				$_data = '
				<span class="dropdown inline-block">
				<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
				<i data-feather="tool" class="icon-16"></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
				</span>';
			}

		}else{
			$_data = $aRow[$aColumns[$i]];
		}


		$row[] = $_data;
	}
	$output['aaData'][] = $row;

}
