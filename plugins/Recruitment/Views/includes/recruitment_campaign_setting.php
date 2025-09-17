<div id="page-content" class="page-wrapper clearfix">
	<div class="row">

		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "generals";
			echo view("Recruitment\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">

			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('general'); ?></h4>
				</div>

				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="checkbox checkbox-primary">
									<input <?php if(re_has_permission('recruitment_can_edit')){ echo 'onchange="recruitment_campaign_setting(this); return false"' ;} ?> class="form-check-input"  type="checkbox" id="recruitment_create_campaign_with_plan" name="recruitment_create_campaign_with_plan" <?php if(get_setting('recruitment_create_campaign_with_plan') == 1 ){ echo 'checked';} ?> value="recruitment_create_campaign_with_plan" >
									<label for="recruitment_create_campaign_with_plan"><?php echo app_lang('create_recruitment_campaign_not_create_plan'); ?>

									<a href="#" class="pull-right display-block input_method"><i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('recruitment_campaign_setting_tooltip'); ?>"></i></a>
								</label>
							</div>
						</div>
					</div>

					<div class="col-md-12">
						<div class="form-group">
							<div class="checkbox checkbox-primary">
								<input <?php if(re_has_permission('recruitment_can_edit')){ echo 'onchange="recruitment_campaign_setting(this); return false"' ;} ?> class="form-check-input"  type="checkbox" id="display_quantity_to_be_recruited" name="display_quantity_to_be_recruited" <?php if(get_setting('recruitment_create_campaign_with_plan') == 1 ){ echo 'checked';} ?> value="display_quantity_to_be_recruited" >
								<label for="display_quantity_to_be_recruited"><?php echo app_lang('Display_the_Quantity_to_be_recruited_on_the_Recruitment_Portal'); ?>

								<a href="#" class="pull-right display-block input_method">
									<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('recruitment_campaign_setting_tooltip'); ?>"></i>
								</a>
							</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<h5 class="no-margin font-bold h5-color"><?php echo app_lang('new_candidate_lable') ?></h5>
						<hr class="hr-color">
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox checkbox-primary no-mtop">
						<input <?php if(re_has_permission('recruitment_can_edit')){ echo 'onchange="recruitment_campaign_setting(this); return false"' ;} ?> class="form-check-input" type="checkbox" id="send_email_welcome_for_new_candidate" name="purchase_setting[send_email_welcome_for_new_candidate]" <?php if(get_setting('send_email_welcome_for_new_candidate') == 1 ){ echo 'checked';} ?> value="send_email_welcome_for_new_candidate">
						<label for="send_email_welcome_for_new_candidate"><?php echo app_lang('send_email_welcome_for_new_candidate_label'); ?>
					</label>
				</div>
			</div>

				<?php echo form_open_multipart(site_url('recruitment/prefix_number'),array('class'=>'prefix_number','autocomplete'=>'off')); ?>
				<div class="row">
					<div class="col-md-12">
						<h5 class="no-margin font-bold h5-color"><?php echo app_lang('candidate_code') ?></h5>
						<hr class="hr-color">
					</div>
				</div>

				<div class="form-group">
					<label><?php echo app_lang('re_candidate_code_prefix'); ?></label>
					<div  class="form-group" app-field-wrapper="candidate_code_prefix">
						<input type="text" id="candidate_code_prefix" name="candidate_code_prefix" class="form-control" value="<?php echo get_setting('candidate_code_prefix'); ?>"></div>
					</div>

					<div class="form-group">
						<label><?php echo app_lang('re_candidate_code_number'); ?></label>
						<i class="fa fa-question-circle i_tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('re_next_number_tooltip'); ?>"></i>
						<div  class="form-group" app-field-wrapper="candidate_code_number">
							<input type="number" min="0" id="candidate_code_number" name="candidate_code_number" class="form-control" value="<?php echo get_setting('candidate_code_number'); ?>">
						</div>

					</div>

					<div class="modal-footer">
						<?php if(re_has_permission("recruitment_can_edit") ){ ?>
							<button type="submit" class="btn btn-info text-white"><?php echo app_lang('submit'); ?></button>
						<?php } ?>
					</div>
					<?php echo form_close(); ?>


			</div>
		</div>
	</div>
</div>
</div>
</div>

<div class="clearfix"></div>

<?php require 'plugins/Recruitment/assets/js/settings/general_js.php';?>
</body>
</html>