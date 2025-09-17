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

				<?php echo form_open_multipart(get_uri("hr_payroll/payslip_pdf_template/".$id), array("id" => "contract-template-form", "class" => "general-form", "role" => "form")); ?>
				<div class="card-body">
					<div class="row">
						<?php echo form_hidden('id', $template_id); ?>


						<?php 

						$name = (isset($pdf_payslip_template) ? $pdf_payslip_template->name : ''); 
						$payslip_template_id = isset($pdf_payslip_template) ? $pdf_payslip_template->payslip_template_id: '';

						?>

						<?php $attrs = (isset($pdf_payslip_template) ? array() : array('autofocus'=>true)); ?>

						<div class="row">
							<div class="col-md-6">
								<?php echo render_input1('name','pdf_payslip_template',$name,'text',$attrs, [], '', '', true); ?>   
							</div>

							<div  class="col-md-6">
								<div class="form-group">
									<label><small class="req text-danger">* </small><?php echo _l('payslip_template'); ?></label>
									<select name="payslip_template_id" id="payslip_template_id" data-live-search="true" class="select2" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
										<?php foreach($payslip_templates as $payslip_template) { 
											$selected = '';
											if(($payslip_template['id'] == $payslip_template_id)){
												$selected = 'selected';
											}

											?>
											<option value="<?php echo html_entity_decode($payslip_template['id']); ?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($payslip_template['templates_name']); ?></option>
										<?php } ?>
									</select>

								</div>
							</div>
						</div>

					</div>
					<label><?php echo app_lang("pdf_payslip_template"); ?></label>
					<?php echo html_entity_decode($sample_pdf_payslip_template); ?>
				</div>
			</div>

			<div class="card">
				<div class="container-fluid">
					<div class="">
						<div class="btn-bottom-toolbar text-right mb20 mt20">
							<a href="<?php echo get_uri('hr_payroll/pdf_payslip_templates'); ?>"  class="btn btn-default mr-2 "><span data-feather="x" class="icon-16" ></span> <?php echo app_lang('hrp_close'); ?></a>
							<?php if(hrp_has_permission('hr_payroll_can_create_setting') || hrp_has_permission('hr_payroll_can_create_hrp_setting')){ ?>
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

<?php require 'plugins/Hr_payroll/assets/js/pdf_payslip_templates/pdf_payslip_template_js.php';?>
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