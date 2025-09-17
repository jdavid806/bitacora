<?php echo form_open(get_uri("recruitment/evaluation_criteria/".$id), array("id" => "add_evaluation_criteria-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$criteria_type = '';
						$criteria_title = '';
						$group_criteria = '';
						$description = '';
						$select_group_criteria_hide = ' hide';
						$score_des1 = '';
						$score_des2 = '';
						$score_des3 = '';
						$score_des4 = '';
						$score_des5 = '';

						if(isset($evaluation_criteria_data)){
							$criteria_type = $evaluation_criteria_data->criteria_type;
							$criteria_title = $evaluation_criteria_data->criteria_title;
							$group_criteria = $evaluation_criteria_data->group_criteria;
							$description = $evaluation_criteria_data->description;
							if($evaluation_criteria_data->criteria_type == 'group_criteria'){
								$select_group_criteria_hide = ' hide';
							}else{
								$select_group_criteria_hide = '';
							}

							$score_des1 = $evaluation_criteria_data->score_des1;
							$score_des2 = $evaluation_criteria_data->score_des2;
							$score_des3 = $evaluation_criteria_data->score_des3;
							$score_des4 = $evaluation_criteria_data->score_des4;
							$score_des5 = $evaluation_criteria_data->score_des5;
						}
						?>

						<div class="col-md-12">
							<?php 
							$criteria_type_data = [];
							$criteria_type_data[] = [
								'name' => 'criteria',
								'label' => app_lang('criteria'),
							];
							$criteria_type_data[] = [
								'name' => 'group_criteria',
								'label' => app_lang('group_criteria'),
							];

							?>

							<?php echo render_select1('criteria_type', $criteria_type_data, array('name', 'label'), 'criteria_type', $criteria_type, [], [], '', '', true, true); ?>

							<?php echo render_input1('criteria_title', 'criteria_title', $criteria_title, '', [], [], '', '', true); ?>

							<div class="select_group_criteria <?php echo html_entity_decode($select_group_criteria_hide); ?>">
								<?php echo render_select1('group_criteria', $list_group, array('criteria_id', 'criteria_title'), 'group_criteria', $group_criteria); ?>
							</div>

							<?php echo render_textarea1('description', 'description', $description) ?>

							<div class="row form-group">
								
								<div class="col-md-3 mbot5"><label for="score1"><?php echo app_lang('scores'); ?></label><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo app_lang('score1'); ?>" placeholder="<?php echo app_lang('description_for_score'); ?>" /></div>
								<div class="col-md-9 mbot5 padding-left-0"><label for="score1"><?php echo app_lang('scores_des'); ?></label><input type="text" class="form-control" name="score_des1" value="<?php echo html_entity_decode($score_des1); ?>"  placeholder="<?php echo app_lang('description_for_score'); ?>" /></div>
							</div>

							<div class="row form-group">

								<div class="col-md-3 mbot5"><input type="text" class="form-control" disabled="true" name="score2" value="<?php echo app_lang('score2'); ?>" /></div>
								<div class="col-md-9 mbot5 padding-left-0"><input type="text" class="form-control" name="score_des2" value="<?php echo html_entity_decode($score_des2); ?>" placeholder="<?php echo app_lang('description_for_score'); ?>" /></div>
							</div>

							<div class="row form-group">

								<div class="col-md-3 mbot5"><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo app_lang('score3'); ?>" /></div>
								<div class="col-md-9 mbot5 padding-left-0"><input type="text" class="form-control" name="score_des3" value="<?php echo html_entity_decode($score_des3); ?>" placeholder="<?php echo app_lang('description_for_score'); ?>" /></div>
							</div>

							<div class="row form-group">

								<div class="col-md-3 mbot5"><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo app_lang('score4'); ?>" /></div>
								<div class="col-md-9 mbot5 padding-left-0"><input type="text" class="form-control" name="score_des4" value="<?php echo html_entity_decode($score_des4); ?>" placeholder="<?php echo app_lang('description_for_score'); ?>" /></div>
							</div>
							<div class="row form-group">

								<div class="col-md-3"><input type="text" class="form-control" disabled="true" name="score1" value="<?php echo app_lang('score5'); ?>" /></div>
								<div class="col-md-9 padding-left-0"><input type="text" class="form-control" name="score_des5" value="<?php echo html_entity_decode($score_des5); ?>" placeholder="<?php echo app_lang('description_for_score'); ?>" /></div>
							</div>

						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
		<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('save'); ?></button>

	</div>
</div>
<?php echo form_close(); ?>
<?php require 'plugins/Recruitment/assets/js/settings/modal_forms/evaluation_criteria_modal_js.php';?>
