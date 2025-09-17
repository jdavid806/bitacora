<!DOCTYPE html>
<html>
<head>
	<title><?php echo app_lang('recruitment_form'); ?></title>
</head>
<?php echo view('includes/head'); ?>
<body class=" web-to-lead <?php echo html_entity_decode($form->form_key) ; ?>">
	<div class="scrollable-page container-fluid ">
			<div class="col-md-6 col-md-offset-3 form-col">
				
				<div id="response"></div>
				<?php echo form_open_multipart(current_url(),array('id'=>$form->form_key,'class'=>'disable-on-submit general-form')); ?>
				<?php echo form_hidden('key',$form->form_key); ?>
				<?php echo form_hidden('rec_campaignid', $rec_campaignid); ?>
				<div class="row">
					<?php foreach($form_fields as $field){
						re_render_form_builder_field($field);
					} ?>

					<?php if(1==2){ ?>
						<?php if (is_gdpr() && get_option('gdpr_enable_terms_and_conditions_lead_form') == 1) { ?>
							<div class="col-md-12">
								<div class="checkbox chk">
									<input type="checkbox" name="accept_terms_and_conditions" required="true" id="accept_terms_and_conditions" <?php echo set_checkbox('accept_terms_and_conditions', 'on'); ?>>
									<label for="accept_terms_and_conditions">
										<?php echo app_lang('gdpr_terms_agree', terms_url()); ?>
									</label>
								</div>
							</div>
						<?php } ?>
					<?php } ?>

					<div class="clearfix"></div>
					<div class="text-left col-md-12 submit-btn-wrapper">
						<button class="btn btn-success" id="form_submit" type="submit"><?php echo html_entity_decode($form->submit_btn_name); ?></button>
					</div>
				</div>

				<?php echo form_close(); ?>
			</div>

	</div>
<?php require 'plugins/Recruitment/assets/js/recruitment_channels/channel_form_js.php';?>


