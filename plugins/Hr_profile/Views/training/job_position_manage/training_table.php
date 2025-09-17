<?php

$aColumns = [
	'1',
	'training_id',
	'subject',
	'training_type',
	'(SELECT count(questionid) FROM ' . get_db_prefix() . 'hr_position_training_question_form WHERE ' . get_db_prefix() . 'hr_position_training_question_form.rel_id = ' . get_db_prefix() . 'hr_position_training.training_id AND rel_type="position_training")',
	'(SELECT count(resultsetid) FROM ' . get_db_prefix() . 'hr_p_t_surveyresultsets WHERE ' . get_db_prefix() . 'hr_p_t_surveyresultsets.trainingid = ' . get_db_prefix() . 'hr_position_training.training_id)',
	'datecreated',
	'2',
];
$sIndexColumn = 'training_id';
$sTable       = get_db_prefix() . 'hr_position_training';
$result       = data_tables_init1($aColumns, $sIndexColumn, $sTable, [], [], ['hash',get_db_prefix() . 'hr_position_training.training_id'], '', [], $dataPost);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

	$row = [];
	for ($i = 0; $i < count($aColumns); $i++) {
		$_data = $aRow[$aColumns[$i]];
		if($aColumns[$i] == '1') {
			$_data = '<div class="checkbox"><input type="checkbox" class="form-check-input" value="' . $aRow['training_id'] . '"><label></label></div>';

		}elseif ($aColumns[$i] == 'subject') {
			$_data = '<a href="' . get_uri('hr_profile/training_detail/'.$aRow['training_id'] . '/' . $aRow['hash']) . '" target="_blank">' . $_data . '</a>';
			$_data .= '</div>';
		}elseif($aColumns[$i] == 'training_type'){
			$_data = get_type_of_training_by_id($_data);
		} elseif ($aColumns[$i] == 'datecreated') {
			$_data = format_to_datetime($_data, false);
		} elseif($aColumns[$i] == '2'){

			$view = '';
			$edit = '';
			$delete = '';
			/*options*/

			$view = '<li role="presentation"><a href="'.get_uri('hr_profile/training_detail/'.$aRow['training_id'] . '/' . $aRow['hash']).'" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> ' . app_lang('view') . '</a></li>';

			if (is_admin() || hr_has_permission('hr_profile_can_edit_hr_training')) {
				$edit = '<li role="presentation"><a href="'.get_uri('hr_profile/position_training/' . $aRow['training_id']).'"  class="dropdown-item"><span data-feather="edit" class="icon-16"></span> ' . app_lang('hr_edit') . '</a></li>';
			}

			if (is_admin() || hr_has_permission('hr_profile_can_delete_hr_training')) {
				$delete .= '<li role="presentation">' .modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> " . app_lang('delete'), array("title" => app_lang('delete'). "?", "data-post-id" => $aRow['training_id'], "data-post-function" => 'delete_position_training', "class" => 'dropdown-item' )). '</li>';
			}


		$_data = '
		<span class="dropdown inline-block">
		<button class="btn btn-default dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" data-bs-display="static">
		<i data-feather="tool" class="icon-16"></i>
		</button>
		<ul class="dropdown-menu dropdown-menu-end" role="menu">'.$view . $edit. $delete. '</ul>
		</span>';

	}


		$row[] = $_data;
	}
	$row['DT_RowClass'] = 'has-row-options';
	$output['aaData'][] = $row;
}
