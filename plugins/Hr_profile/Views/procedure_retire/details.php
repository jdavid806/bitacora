<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_configure_procedure_retire'); ?></h4>
				</div>

				<?php echo form_open(get_uri("hr_profile/procedure_form/".$id), array("id" => "add_procedure_form-form", "class" => "general-form", "role" => "form")); ?>
				<div class="card-body">
					<div class="_buttons text-right mb20">
						<?php if(is_admin() || hr_has_permission('hr_profile_can_create_setting')) {?>

							<a href="#" id="add_save" onclick="add_procedure_retire(); return false;" class="btn btn-info pull-left text-white"><span data-feather="plus-circle" class="icon-16"></span> 
								<?php echo app_lang('hr_hr_add'); ?>
							</a>

						<?php } ?>

						<a href="<?php echo admin_url('hr_profile/procedure_retires'); ?>"  class="btn btn-default pull-left  mleft10"><span data-feather="x" class="icon-16"></span>
							<?php echo app_lang('hr_go_back_setting_menu'); ?>
						</a>

					</div>
					<div class="load_add_box"></div>
					<?php echo form_close(); ?>

					<div class="total_box">
						<?php foreach ($procedure_retire as $key => $value) {?>

							<?php if($value['people_handle_id'] == get_staff_user_id1() || is_admin() || hr_has_permission('hr_profile_can_create_setting') || hr_has_permission('hr_profile_can_edit_setting')){?>
								<div class="row">
									<div class="col-md-11">
										<h5 class="no-margin font-bold"> <?php echo app_lang('hr_step'); ?> <?php echo html_entity_decode($key+1); ?>:  <?php echo html_entity_decode($value['rel_name']); ?>
										<span >( <?php echo get_staff_full_name1($value['people_handle_id']); ?> )</span>
									</h5>

								</div>
								<div class="col-md-1">
									<?php if(is_admin() || hr_has_permission('hr_profile_can_delete_setting')) {?>
										<?php 
											echo modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), "<i data-feather='x' class='icon-16'></i> ", array("title" => app_lang('delete'). "?", "data-post-id" => $value['id'], "data-post-id2" => $id,"data-post-function" => 'delete_procedure_retire', "class" => 'btn btn-danger text-white' ));
										 ?>
									<?php } ?>

									<?php if(is_admin() || hr_has_permission('hr_profile_can_edit_setting')) {?>
										<a href="#" onclick="edit_procedure_retire(this); " data-id="<?php echo html_entity_decode($value['id']); ?>" class=" pull-right btn btn-warning btn-icon text-white"><span data-feather="edit" class="icon-16"></span></a>
									<?php } ?>

								</div>
								<br>
								<br>

								<div class="col-md-12">
									<div class="">
										<?php $option_select = json_decode($value['option_name']); ?>
										<?php foreach ($option_select as $key => $option) {?>
											<div class="box_area">
												<div class="row">
													<div class="col-md-1">
													</div>
													<?php if($option) { ?>
														<div class="col-md-11">
															<input type="text" placeholder="<?php echo app_lang('hr_add_options'); ?>..." data-box-descriptionid="" value="<?php echo html_entity_decode($option); ?>" class="form-control" disabled>
														</div>
													<?php } ?>  
												</div>
												<br/><div class="clearfix"></div>
											</div>
										<?php }?>
									</div>
								</div> 

							</div>
						<?php } ?>
					<?php } ?>
				</div>

				<div class="form-group hide">
					<div class="checkbox checkbox-primary">
						<input type="checkbox" id="mange_asset" value="total_salary" checked >
						<label for="mange_asset"><?php echo app_lang('hr_hand_over_assets_received_when_working'); ?></label>
					</div>
				</div>




			</div>


		</div>
	</div>
</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header">
				<h4 class="modal-title"></h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<?php echo form_open(get_uri('hr_profile/edit_procedure_form'), array( "class" => "general-form", "role" => "form")); ?>
			<?php echo form_hidden('id'); ?>
			<?php echo form_hidden('procedure_retire_id'); ?>
			<!-- Modal body -->
			<div class="modal-body card-body">
				<span class="no-margin font-bold"><?php echo app_lang('hr_group_name'); ?></span>
				<input type="text" placeholder="<?php echo app_lang('hr_item_name_to_add'); ?>..." name="rel_name[1]" id="rel_name[1]" data-box-descriptionid="" class="form-control check_edit_cus" value="">
				<br>
				<?php echo render_select1('people_handle_id',$staffs, array('id', array('first_name','last_name')), 'hr_people_handle_id','',array('data-live-search' => 'true') ); ?>

				<div class="content_edit">

				</div>
			</div>
			<!-- Modal footer -->
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>
				<button type="submit" class="btn btn-primary text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>


<?php require 'plugins/Hr_profile/assets/js/setting/procedure_procedure_retire_details_js.php';?>
</body>
</html>
