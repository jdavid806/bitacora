<div id="page-content" class="page-wrapper clearfix">
	<div class="row container-fluid">
		<div class="col-md-12" id="training-add-edit-wrapper">
			<?php 
			$training_program_point = $training_result['training_program_point'];
			$staff_training_result = $training_result['staff_training_result'];
			$result_data = $training_result['result_data'];

			$_point = $training_program_point.'/'.$staff_training_result[0]['total_question_point'];

			?>


			<div class="card">
				<div class="modal-body card-body">
					<h3 class="bold text-center"><?php echo html_entity_decode($training->subject); ?></h3>
					<h3 class="bold"><?php echo html_entity_decode($training_result['staff_name']) ; ?>:<?php echo html_entity_decode($_point) ?></h3>

					<hr />
					<p class="mb-4 mt-4"><strong><?php echo html_entity_decode($training->viewdescription); ?></strong></p>

					<?php if(count($training->questions) > 0){
						$question_area = '<ul class="list-unstyled mtop25">';
						foreach($training->questions as $question){

							$true_false_text='';
							if(isset($result_data[$question['questionid']])){

								$flag_check_correct = true;

								if(count($result_data[$question['questionid']]['array_id_correct']) == count($result_data[$question['questionid']]['form_results'])){

									foreach ($result_data[$question['questionid']]['array_id_correct'] as $correct_key => $correct_value) {
										if(!in_array($correct_value, $result_data[$question['questionid']]['form_results'])){
											$flag_check_correct = false;
										}
									}
								}else{
									$flag_check_correct = false;
								}
								if($flag_check_correct == true){
									$true_false_text .='<a href="#" class="pull-left checkbox_true_false text-success"><span data-feather="check" class="icon-16" ></span></a>';

								}else{
									$true_false_text .='<a href="#" class="pull-left checkbox_true_false text-danger"><span data-feather="x" class="icon-16" ></span></a>';

								}

							}


							$question_area .= '<li>';
							$question_area .= '<div class="form-group">';
							$question_area .= $true_false_text.'<label class="control-label" for="'.$question['questionid'].'">'.$question['question'].  ' ( Point:'.$question['point'].')</label>';
							if($question['boxtype'] == 'textarea'){
								$question_area .= '<textarea class="form-control" rows="6" name="question['.$question['questionid'].'][]" data-for="'.$question['questionid'].'" id="'.$question['questionid'].'" data-required="'.$question['required'].'"></textarea>';
							} else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
								$question_area .= '<div class="row box chk" data-boxid="'.$question['boxid'].'">';


								foreach($question['box_descriptions'] as $box_description){

									$checked = '';
									if(isset($result_data[$question['questionid']])){
										if(in_array($box_description['questionboxdescriptionid'], $result_data[$question['questionid']]['form_results'])){
											$checked = 'checked="checked"';
										}
									}

									$quetion_correct_class='';
									if($box_description['correct'] == 0){
										$quetion_correct_class .= 'quetion-correct';
									}

									$question_area .= '<div class="col-md-12">';
									$question_area .= ' <div class="'.$question['boxtype'].' '.$question['boxtype'].'-default">';
									$question_area .=
									'<input class="form-check-input" type="'.$question['boxtype'].'" data-for="'.$question['questionid'].'"
									name="selectable['.$question['boxid'].']['.$question['questionid'].'][]" value="'.$box_description['questionboxdescriptionid'].'" data-required="'.$question['required'].'" id="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'" '.$checked.'/>';
									$question_area .= '
									<label for="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'" class="'.$quetion_correct_class.'">
									'.$box_description['description'].'
									</label>';
									$question_area .= '</div>';
									$question_area .= '</div>';
								}

								$question_area .= '</div>';
							} else {
								$question_area .= '<input type="text" data-for="'.$question['questionid'].'" class="form-control" name="question['.$question['questionid'].'][]" id="'.$question['questionid'].'" data-required="'.$question['required'].'">';
							}
							$question_area .= '</div>';
							$question_area .= '<hr /></li>';
						}
						$question_area .= '</ul>';
						echo html_entity_decode($question_area); ?>
						
					<?php } else { ?>
						<p class="no-margin text-center bold mtop20"><?php echo app_lang('hr_survey_no_questions'); ?></p>
					<?php } ?>
				</div>
			</div>

		</div>

	</div>

</div>
</div>

</body>
</html>


<?php require 'plugins/Hr_profile/assets/js/training/participate_js.php'; ?>