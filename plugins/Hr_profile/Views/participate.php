<div id="page-content" class="page-wrapper clearfix">
	<div class="row container-fluid">
		<div class="col-md-12" id="training-add-edit-wrapper">
			<?php echo form_open_multipart(get_uri("hr_profile/training_detail/".$id."/".$hash), array("id" => "survey_form", "class" => "general-form", "role" => "form")); ?>
			<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">
			<input type="hidden" name="hash" value="<?php echo html_entity_decode($hash); ?>">


			<div class="card">
				<div class="modal-body card-body">

					<h3 class="bold text-center"><?php echo html_entity_decode($training->subject); ?></h3>
					<hr />
					<p class="mb-4 mt-4"><strong><?php echo html_entity_decode($training->viewdescription); ?></strong></p>
					<?php if(count($training->questions) > 0){
						$question_area = '<ul class="list-unstyled mtop25">';
						foreach($training->questions as $question){
							$question_area .= '<li>';
							$question_area .= '<div class="form-group">';
							$question_area .= '<label class="control-label mb-4" for="'.$question['questionid'].'">'.$question['question'].'</label>';
							if($question['boxtype'] == 'textarea'){
								$question_area .= '<textarea class="form-control" rows="6" name="question['.$question['questionid'].'][]" data-for="'.$question['questionid'].'" id="'.$question['questionid'].'" data-required="'.$question['required'].'"></textarea>';
							} else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
								$question_area .= '<div class="row box chk" data-boxid="'.$question['boxid'].'">';
								foreach($question['box_descriptions'] as $box_description){
									$question_area .= '<div class="col-md-12">';
									$question_area .= '<div class="form-group '.$question['boxtype'].' '.$question['boxtype'].'-default">';
									$question_area .=
									'<input class="form-check-input" type="'.$question['boxtype'].'" data-for="'.$question['questionid'].'"
									name="selectable['.$question['boxid'].']['.$question['questionid'].'][]" value="'.$box_description['questionboxdescriptionid'].'" data-required="'.$question['required'].'" id="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'"/>';
									$question_area .= '
									<label for="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'">
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
						<div class="row">
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-info text-white" id="submit"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
							</div>
						</div>
					<?php } else { ?>
						<p class="no-margin text-center bold mtop20"><?php echo app_lang('hr_survey_no_questions'); ?></p>
					<?php } ?>




				</div>
			</div>

			<?php echo form_close(); ?>
		</div>

	</div>

</div>
</div>

</body>
</html>


<?php require 'plugins/Hr_profile/assets/js/training/participate_js.php'; ?>
