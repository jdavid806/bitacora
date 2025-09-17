<?php echo form_open(get_uri("hr_profile/salary_form/".$id), array("id" => "add_salary_form-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 
						$form_name = '';
						$salary_val = '';
						if(isset($salary_type_data)){
							$form_name = $salary_type_data->form_name;
							$salary_val = $salary_type_data->salary_val;
						}
						?>
							
						<?php 
						echo render_input1('form_name','hr_salary_form_name', $form_name, '', [], [], '', '', true); ?>
						<?php 
						$arrAtt = array();
						$arrAtt['data-type']='currency';
						echo render_input1('salary_val','amount', $salary_val,'text',$arrAtt, [], '', '', true); ?> 

						<div class="form-group hide">
							<label for="tax" class="control-label"><?php echo app_lang('taxable'); ?></label>
							<select name="tax" class="selectpicker" id="taxable" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
								<option value=""></option>                  
								<option value="0"><?php echo app_lang('no'); ?></option>
								<option value="1"><?php echo app_lang('yes'); ?></option>
							</select>
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
<?php require 'plugins/Hr_profile/assets/js/setting/modal_forms/salary_type_modal_js.php';?>
