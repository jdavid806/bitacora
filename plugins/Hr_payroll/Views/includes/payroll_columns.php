<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "payroll_columns";
			echo view("Hr_payroll\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('payroll_columns'); ?></h4>

					<div class="title-button-group">

						<?php if(is_admin() || hrp_has_permission('hr_payroll_can_create_hrp_setting')) {?>
							<a href="#" onclick="new_column_type(); return false;" class="btn btn-info text-white" ><span data-feather="plus-circle" class="icon-16" ></span> 
								<?php echo app_lang('add'); ?>
							</a>
						<?php } ?>
					</div>

				</div>



				<div class="table-responsive pt15 pl15 pr15">
					<table id="dtBasicExample" class="table  ">
						<thead>
							<tr>
								<th><?php echo app_lang('order'); ?></th>
								<th><?php echo app_lang('column_name_lable'); ?></th>
								<th><?php echo app_lang('taking_method_lable'); ?></th>
								<th><?php echo app_lang('staff_id_created'); ?></th>
								<th><?php echo app_lang('date_created'); ?></th>
								<th><?php echo app_lang('actions'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($payroll_column_value as $value){ ?>
								<?php 

								$_data = get_staff_image($value['staff_id_created'], false);
								$_data .= get_staff_full_name1($value['staff_id_created']);
								?>
								<tr>
									<td><?php echo html_entity_decode($value['order_display']); ?></td>
									<td><?php echo html_entity_decode($value['column_key']); ?></td>
									<td><?php echo html_entity_decode($value['taking_method'] == 'caculator' ? 'formular' : $value['taking_method']); ?></td>
									<td><?php echo html_entity_decode($_data); ?></td>
									<td><?php echo html_entity_decode(format_to_datetime($value['date_created'], false)); ?></td>
									<td class=" text-center option w100 ">

										<?php if(is_admin() || hrp_has_permission('hr_payroll_can_edit_hrp_setting')) {?>
											<a href="#" onclick="edit_column_type(this,<?php echo html_entity_decode($value['id']); ?>); return false"  class="btn btn-default btn-icon" data-toggle="sidebar-right" data-target=".insurance_type_modal-edit-modal"><span data-feather="edit" class="icon-16" ></span> </a>
										<?php } ?>

										<?php if(is_admin() || hrp_has_permission('hr_payroll_can_delete_hrp_setting')) {?>
											<?php if($value['is_edit'] != 'no'){ ?>
												<?php 
												echo modal_anchor(get_uri("hr_payroll/confirm_delete_modal_form"), "<span data-feather='x' class='icon-16' ></span>", array("title" => app_lang('delete'). "?", "data-post-id" => $value['id'], "data-post-function" => 'delete_payroll_column_setting', "class" => "delete" ));
												 ?>

											<?php } ?>

										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table> 
				</div>

			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="insurance_type_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<?php echo form_open_multipart(get_uri("hr_payroll/payroll_column"), array("id" => "add_payroll_column", "class" => "general-form", "role" => "form")); ?>

		<div class="modal-content">
			<div class="modal-header">

				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('edit_payroll_column'); ?></span>
					<span class="add-title"><?php echo app_lang('new_payroll_column'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body">
				<div id="additional_payroll_column"></div>

				<div class="row">
					<div class="col-md-6"> 

						<div class="form-group">
							<label for="taking_method" class="control-label taking_method_class"><?php echo app_lang('taking_method_lable'); ?></label>
							<select name="taking_method" id="taking_method" class="select2 validate-hidden"  data-live-search="true" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-rule-required="1" requied="1">

							</select>
						</div>
					</div>
					<div class="col-md-6">
						<?php echo render_select1('function_name',[],[],'function_name_lable','',[],[],'function_name_hide hide function_name_class'); ?>
					</div>

				</div>
				<div class="row">
					<div class="col-md-6">

						<?php echo render_input1('column_key','column_name_lable','','text', [], [], '', '', true); ?>
					</div>            
					<div class="col-md-6">
						<?php echo render_input1('function_name','column_key_lable','','text'); ?>
					</div>            
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php echo render_textarea1('description','description_lable',''); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php echo render_input1('order_display', 'order_display_label', $order_display_in_paylip, 'number'); ?>
					</div>
				</div>
				<div class="row hide">
					<div class="col-md-12">
						<div class="form-group">
							<div class="checkbox checkbox-primary">
								<input  type="checkbox" id="display_with_staff" name="display_with_staff" value="display_with_staff" checked="true">

								<label for="display_with_staff"><?php echo app_lang('display_with_staff'); ?><small > </small>
								</label>
							</div>
						</div>
					</div>  
				</div> 
				<?php echo form_hidden('value_related_to'); ?>

			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>

				<?php if(hrp_has_permission('hr_payroll_can_create_hrp_setting') || hrp_has_permission('hr_payroll_can_edit_hrp_setting')){ ?>
					<button type="button" class="btn btn-info payroll_column_submit text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
				<?php } ?>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div> 


<?php require 'plugins/Hr_payroll/assets/js/payroll_column/payroll_column_js.php';?>

</body>
</html>
