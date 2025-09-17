<?php echo form_open_multipart(get_uri("recruitment/company_add_edit/".$id), array("id" => "add_company-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>" />

			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$company_name = '';
						$company_address = '';
						$company_industry = '';

						if(isset($company_data)){
							$company_name = $company_data->company_name;
							$company_address = $company_data->company_address;
							$company_industry = $company_data->company_industry;
						}
						?>

						<div class="row">
							
							<div class="col-md-12">
								<?php echo render_input1('company_name', 'company_name', $company_name, '', [], [], '', '', true); ?>
							</div>
							<div class="col-md-12">
								<?php echo render_textarea1('company_address', 'company_address', $company_address) ?>
							</div>
							<div class="col-md-12">
								<?php echo render_textarea1('company_industry', 'company_industry', $company_industry) ?>
							</div>
						</div>

						<div class="form-group">
						
								<div class="form-group profile-image-upload-group <?php if(isset($logo)){ echo 'hide';} ?>">
									<label for="profile_image" class="profile-image"><?php echo app_lang('company_image'); ?></label>
									<input type="file" name="file" class="form-control" id="file">
								</div>

							<?php if(isset($logo)){ ?>
								<div class="form-group profile-image-group">
									<div class="row">
										<div class="col-md-9">
											<img src="<?php echo get_file_uri('plugins/Recruitment/Uploads/company_images/'.$logo->rel_id . '/' . $logo->file_name) ?>" />
										</div>

										<div class="col-md-3 text-right">
											<a class="text-danger" href="#" onclick="delete_company_attachment(this,<?php echo html_entity_decode($logo->id); ?>);return false;"><span data-feather="x" class="icon-16"></span></a>
										</div>
									</div>
								</div>
							<?php } ?>
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
<?php require 'plugins/Recruitment/assets/js/settings/modal_forms/company_modal_js.php';?>