<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<?php 
		$template_id = '';
		?>
		<?php if(isset($contract_template)){
			$template_id = $contract_template->id;
			?>
			<div class="member">
				<?php echo form_hidden('isedit'); ?>
				<?php echo form_hidden('contractid',$contract_template->id); ?>
			</div>
		<?php } ?>

		<div class="col-sm-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>

				<?php echo form_open_multipart(get_uri("hr_profile/contract_template"), array("id" => "contract-template-form", "class" => "general-form", "role" => "form")); ?>
				<div class="card-body">
					<div class="row">
						<?php echo form_hidden('id', $template_id); ?>


						<?php 

						$name = (isset($contract_template) ? $contract_template->name : ''); 
						$value = (isset($contract_template) ? $contract_template->job_position : ''); 
						$arr_job_position = isset($contract_template) ? explode(",", $contract_template->job_position): [];

						?>

						<?php $attrs = (isset($contract_template) ? array() : array('autofocus'=>true)); ?>

						<div class="row">
							<div class="col-md-6">
								<?php echo render_input1('name','contract_template',$name,'text',$attrs, [], '', '', true); ?>   
							</div>

							<div  class="col-md-6">
								<div class="form-group">
									<label><small class="req text-danger">* </small><?php echo app_lang('hr_hr_job_position'); ?></label>
									<select name="job_position[]" id="job_position" data-live-search="true" class="select2 validate-hidden" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-rule-required=1 data-msg-required=<?php echo app_lang('field_required'); ?>>
										<?php foreach($job_positions as $job_position) { 
											$selected = '';
											if(in_array($job_position['position_id'], $arr_job_position)){
												$selected = 'selected="selected"';
											}

											?>
											<option value="<?php echo html_entity_decode($job_position['position_id']); ?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($job_position['position_name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

					</div>
					<label><?php echo app_lang("hr_staff_contract_template"); ?></label>
					<?php echo html_entity_decode($sample_contract); ?>
				</div>
			</div>

			<div class="card">
				<div class="container-fluid">
					<div class="">
						<div class="btn-bottom-toolbar text-right mb20 mt20">
							<a href="<?php echo get_uri('hr_profile/contract_templates'); ?>"  class="btn btn-default mr-2 "><span data-feather="x" class="icon-16" ></span> <?php echo app_lang('hr_close'); ?></a>
							<?php if(hr_has_permission('hr_profile_can_create_setting') || hr_has_permission('hr_profile_can_edit_setting')){ ?>
								<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
							<?php } ?>
						</div>
					</div>
					<div class="btn-bottom-pusher"></div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<?php require 'plugins/Hr_profile/assets/js/setting/contract_template_js.php';?>
</body>
</html>

<?php
load_css(array(
	"assets/js/summernote/summernote.css"
));
load_js(array(
	"assets/js/summernote/summernote.min.js"
));
?>