<?php echo form_open(get_uri("hr_profile/add_procedure_form_manage/".$id), array("id" => "add_procedure_form_manage-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="form"> 
					<?php 
					$name_procedure_retire = '';
					$departmentid = '';

					if(isset($procedure_retire_data)){
						$name_procedure_retire = $procedure_retire_data->name_procedure_retire;
						if($procedure_retire_data->department != null && strlen($procedure_retire_data->department) > 0){
							$departmentid = explode(",", $procedure_retire_data->department);
						}
					}
					?>

					<div class="col-md-12">
						<div class="form-group">
							<?php echo render_input1('name_procedure_retire','hr_name_procedure_retire', $name_procedure_retire, '', [], [], '', '', true); ?>
						</div>            
					</div>
					<div class="col-md-12">
						<div class="form-group select-placeholder department_add_edit">
							<label for="departmentid" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('hr_department'); ?></label>
							<select name="departmentid[]" id="departmentid" multiple="true" class="select2 validate-hidden" data-actions-box="true" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-rule-required="1" data-msg-required="<?php echo app_lang('field_required'); ?>">
								<?php foreach ($departments as $d) { ?>
									<?php 
										$selected = "";
										if(is_array($departmentid) && in_array($d['id'], $departmentid)){
											$selected = " selected='selected'";
										}
									 ?>
									<option value="<?php echo html_entity_decode($d['id'])?>" <?php echo html_entity_decode($selected); ?>><?php echo html_entity_decode($d['title']); ?></option>
								<?php } ?>
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
<?php require 'plugins/Hr_profile/assets/js/setting/modal_forms/procedure_retire_modal_js.php';?>

