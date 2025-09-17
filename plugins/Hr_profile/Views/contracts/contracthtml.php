<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="row">
					<div class="col-md-3">
						<div class="mbot30">
							<div class="contract-html-logo">
								<img src="<?php echo get_file_from_setting("invoice_logo", true) ?>" />
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>


				<div class="page-title clearfix">
					<h4 class="pull-left no-mtop contract-html-subject"><?php echo html_entity_decode($contract->contract_code); ?>
					<?php if($contract->signature != null && strlen($contract->signature) > 0) { ?>
						<span class="badge bg-success large mt-0"> <?php echo app_lang('hr_is_signed') ?></span>
					<?php } ?>
					<br />
					<small><?php echo hr_get_contract_type($contract->name_contract); ?></small>
				</h4>
				<div class="title-button-group">

					<?php if($contract->staff_signature == '' ) { ?>
						<?php if( get_staff_user_id() == $contract->staff){ ?>
							<button type="submit" id="staff_accept_action" class="btn btn-success pull-right action-button"><?php echo app_lang('staff_signature_sign'); ?></button>
						<?php } ?>

					<?php }?>

					<?php if($contract->signature == '' ) { ?>
						<?php if(is_admin() || get_staff_user_id() == $contract->staff_delegate){ ?>
							<button type="submit" id="accept_action" class="btn btn-success pull-right action-button"><?php echo app_lang('e_signature_sign'); ?></button>
						<?php } ?>

					<?php } ?>

					<a href="<?php echo get_uri('hr_profile/contract_pdf/'.$contract->id_contract); ?>" class="btn btn-default pull-right action-button mright5 contract-html-pdf d-none"><i class="fa fa-file-pdf-o"></i> <?php echo app_lang('download'); ?></a>

				</div>

			</div>

			<div class="card">
				<div class="row">
					<div class="col-md-8 contract-left">
						<div class="panel_s mtop20">
							<div class="panel-body tc-content padding-30 contract-html-content">
								<?php echo html_entity_decode($contract->content); ?>
							</div>
						</div>
					</div>
					<div class="col-md-4 contract-right">
						<div class="inner mtop20 contract-html-tabs">
							<ul class="nav nav-tabs nav-tabs-flat mbot15" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="true"><?php echo app_lang('summary'); ?></button>
								</li>

							</ul>
							<div >
								<address class="contract-html-company-info">
									<?php echo company_widget(get_default_company_id()); ?>
								</address>
								<div class="row mtop20">

									<div class="col-md-5 text-muted contract-number">
										# <?php echo app_lang('hr_contract_code'); ?>
									</div>
									<div class="col-md-7 contract-number">
										<?php echo html_entity_decode($contract->contract_code); ?>
									</div>
									<div class="col-md-5 text-muted contract-start-date">
										<?php echo app_lang('contract_start_date'); ?>
									</div>
									<div class="col-md-7 contract-start-date">
										<?php echo _d($contract->start_valid); ?>
									</div>
									<?php if(!empty($contract->end_valid)){ ?>
										<div class="col-md-5 text-muted contract-end-date">
											<?php echo app_lang('contract_end_date'); ?>
										</div>
										<div class="col-md-7 contract-end-date">
											<?php echo _d($contract->end_valid); ?>
										</div>
									<?php } ?>
									<?php if(!empty($contract->type_name)){ ?>
										<div class="col-md-5 text-muted contract-type">
											<?php echo app_lang('contract_type'); ?>
										</div>
										<div class="col-md-7 contract-type">
											<?php echo html_entity_decode($contract->name_contract); ?>
										</div>
									<?php } ?>
									<?php if($contract->signature != ''){ ?>
										<div class="col-md-5 text-muted contract-type">
											<?php echo app_lang('date_signed'); ?>
										</div>
										<div class="col-md-7 contract-type">
											<?php echo _d($contract->sign_day); ?>
										</div>
									<?php } ?>
								</div>


								<?php if($contract->staff_signature != ''){ ?>
									<div class="row mtop20">
										<div class="col-md-12 contract-value">
											<h4 class="bold mbot30">
												<?php echo app_lang('staff_signature'); ?>
											</h4>
										</div>
										<div class="col-md-5 text-muted contract-signed-by">
											<?php echo app_lang('contract_signed_by'); ?>
										</div>

										<?php 
										if(is_numeric($contract->staff)){
											$contracts_staff_signer = get_staff_full_name1($contract->staff);
										}else {
											$contracts_staff_signer = ' ';
										}

										?>

										<div class="col-md-7 contract-contract-signed-by">
											<?php echo html_entity_decode($contracts_staff_signer); ?>
										</div>

										<div class="col-md-5 text-muted contract-signed-by">
											<?php echo app_lang('contract_signed_date'); ?>
										</div>
										<div class="col-md-7 contract-contract-signed-by">
											<?php echo _d($contract->staff_sign_day); ?>
										</div>

									</div>
									<div class="row mtop20">


										<?php if ( strlen($contract->staff_signature) > 0) { ?>

											<?php if (file_exists(HR_PROFILE_CONTRACT_SIGN . $contract->id_contract . '/staff_signature.png') ){ ?>

												<img src="<?php echo base_url('plugins/Hr_profile/Uploads/contract_sign/'.$contract->id_contract.'/staff_signature.png'); ?>" class="img-responsive">

											<?php }else{ ?>
												<img src="<?php echo base_url('plugins/Hr_profile/Uploads/image_not_available.jpg'); ?>" class="img-responsive">
											<?php } ?>

											
										<?php } ?>

									</div>
								<?php } ?>

								<?php if($contract->signature != ''){ ?>
									<div class="row mtop20">
										<div class="col-md-12 contract-value">
											<h4 class="bold mbot30">
												<?php echo app_lang('company_signature'); ?>
											</h4>
										</div>
										<div class="col-md-5 text-muted contract-signed-by">
											<?php echo app_lang('contract_signed_by'); ?>
										</div>
										<?php 
										$staff_delegate = get_staff_full_name1($contract->signer);
										?>
										<?php 
										if(is_numeric($contract->signer)){
											$contracts_signer = get_staff_full_name1($contract->signer);
										}else {
											$contracts_signer = ' ';
										}

										?>

										<div class="col-md-7 contract-contract-signed-by">
											<?php echo html_entity_decode($contracts_signer); ?>
										</div>

										<div class="col-md-5 text-muted contract-signed-by">
											<?php echo app_lang('contract_signed_date'); ?>
										</div>
										<div class="col-md-7 contract-contract-signed-by">
											<?php echo _d($contract->sign_day); ?>
										</div>

									</div>
									<div class="row mtop20">
										<?php if ( strlen($contract->signature) > 0) { ?>
											<?php if (file_exists(HR_PROFILE_CONTRACT_SIGN . $contract->id_contract . '/signature.png') ){ ?>

												<img src="<?php echo base_url('plugins/Hr_profile/Uploads/contract_sign/'.$contract->id_contract.'/signature.png'); ?>" class="img-responsive">

											<?php }else{ ?>
												<img src="<?php echo base_url('plugins/Hr_profile/Uploads/image_not_available.jpg'); ?>" class="img-responsive">
											<?php } ?>

										<?php } ?>

									</div>
								<?php } ?>


							</div>
						</div>
					</div>
				</div>


			</div>


		</div>
	</div>
</div>
<!-- signature_pad -->
<div class="modal fade" tabindex="-1" role="dialog" id="identityConfirmationModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<?php echo form_open_multipart(get_uri("hr_profile/contract_sign/".$contract->id_contract), array("id" => "identityConfirmationForm", "class" => "form-horizontal general-form", "role" => "form")); ?>

			<div class="modal-body card-body">

				<div id="identity_fields">
					<div class="form-group hide">
						<label for="acceptance_firstname" class="control-label col-sm-2">
							<span class="text-left inline-block full-width">
								<?php echo app_lang('client_firstname'); ?>
							</span>
						</label>
						<div class="col-sm-10">
							<input type="text" name="acceptance_firstname" id="acceptance_firstname" class="form-control"  value="<?php echo (isset($contract) ? get_staff_full_name1($contract->staff_delegate) : '') ?>">
						</div>
					</div>
					
					<div class="sign_by">

					</div>

					<p class="bold" id="signatureLabel"><?php echo app_lang('signature'); ?></p>
					<div class="signature-pad--body">
						<canvas id="signature" height="130" width="470"></canvas>
					</div>
					<input type="text" class="sig-input-style d-none" tabindex="-1" name="signature" id="signatureInput">
					<div class="dispay-block">
						<button type="button" class="btn btn-default btn-xs clear" tabindex="-1" onclick="signature_clear();"><span data-feather="refresh-cw" class="icon-16"></span> <?php echo app_lang('clear'); ?></button>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('cancel'); ?></button>
				<button type="button" onclick="sign_request(<?php echo html_entity_decode($contract->id_contract); ?>);"  autocomplete="off" class="btn btn-primary sign_request_class"><span data-feather="edit-3" class="icon-16"></span> <?php echo app_lang('e_signature_sign'); ?></button>

			</div>
			<?php echo form_close(); ?>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<?php require 'plugins/Hr_profile/assets/js/contracts/contracthtml_js.php';?>

</body>
</html>
