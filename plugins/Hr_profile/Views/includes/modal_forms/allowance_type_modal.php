<?php echo form_open(get_uri("hr_profile/allowance_type/".$id), array("id" => "add_allowance_type-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 
						$type_name = '';
						$allowance_val = '';
						if(isset($allowance_type_data)){
							$type_name = $allowance_type_data->type_name;
							$allowance_val = $allowance_type_data->allowance_val;
						}
						?>

						<?php 
						echo render_input1('type_name','hr_allowance_type_name', $type_name, '', [], [], '', '', true); ?>
						<?php 
						$arrAtt = array();
						$arrAtt['data-type']='currency';
						echo render_input1('allowance_val','hr_amount_of_money', $allowance_val,'text',$arrAtt, [], '', '', true); ?> 

						<div class="form-group d-none">
							<label for="taxable" class="control-label"><?php echo app_lang('hr_taxable'); ?></label>
							<select name="taxable" class="selectpicker" id="taxable" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
								<option value="0" selected><?php echo app_lang('hr_hr_no'); ?></option>
								<option value="1"><?php echo app_lang('hr_yes'); ?></option>
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
<?php require 'plugins/Hr_profile/assets/js/setting/modal_forms/allowance_type_modal_js.php';?>
