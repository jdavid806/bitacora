
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if(is_admin() || hr_has_permission('hr_profile_can_create_onboarding')){ ?>
							<a href="<?php echo get_uri('hr_profile/add_reception_staff'); ?>" class="btn btn-info pull-left text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_add_reception'); ?></a>
						<?php } ?>
					</div>
				</div>
				
				<div class="modal fade bulk_actions" id="table_staff_bulk_actions" tabindex="-1" role="dialog">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title"><?php echo app_lang('hr_bulk_actions'); ?></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body card-body">
								<?php if (hr_has_permission('hr_profile_can_delete_onboarding') || is_admin()) {?>
									<div class="checkbox checkbox-danger">
										<input type="checkbox" name="mass_delete" id="mass_delete"  class="form-check-input">
										<label for="mass_delete"><?php echo app_lang('hr_mass_delete'); ?></label>
									</div>
								<?php }?>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>

								<?php if (hr_has_permission('hr_profile_can_delete_onboarding') || is_admin()) {?>
									<a href="#" class="btn btn-info text-white" onclick="staff_delete_bulk_action(this); return false;"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('hr_confirm'); ?></a>
								<?php }?>
							</div>
						</div>
					</div>
				</div>

				<?php if (hr_has_permission('hr_profile_can_delete_onboarding')) { ?>
					<a href="#"  onclick="staff_bulk_actions(); return false;" data-toggle="modal" data-table=".table-table_staff" data-target="#leads_bulk_actions" class=" hide bulk-actions-btn table-btn"><?php echo app_lang('hr_bulk_actions'); ?></a>
				<?php } ?>

				<div class="table-responsive">
					<?php
					$table_data = array(
						'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_staff" class="form-check-input"><label></label></div>',
						app_lang('staff_id'),
						app_lang('staff_name'),
						app_lang('hr_hr_code'),
						app_lang('hr_hr_birthday'),
						app_lang('hr_hr_finish'),
						"<i data-feather='menu' class='icon-16'></i>",
					);
					render_datatable1($table_data,'table_staff',
						array('customizable-table'),
						array(
							'id'=>'table-table_staff',
							'data-last-order-identifier'=>'table_staff',
						));
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="add_reception_staff" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">
						<span class="add-title"><?php echo app_lang('hr_add_reception'); ?></span>
						<span class="edit-title hide"><?php echo app_lang('hr_edit_reception'); ?></span>
					</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body card-body">

					<?php echo form_open_multipart(get_uri('hr_profile/add_new_reception'), array('id' => 'add_new_reception', 'autocomplete' => 'off', 'class' => 'general-form')); ?>


					<div class="container-fluid">
						<div class="col-md-12">
							<label for="staff_id" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('hr_select_employee'); ?></label>
							<select name="staff_id" data-live-search="true" class="select2 validate-hidden" id="staff_id" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
								<option value="">-</option> 
								<?php foreach ($list_staff_not_record as $e){ ?>
									<option value="<?php echo html_entity_decode($e['id']) ?>"><?php echo html_entity_decode($e['first_name'].' '.$e['last_name']); ?></option>
								<?php } ?>
							</select>
						</div>



						<?php if(count($group_checklist)>0){ ?>
							<div class="col-md-12">
								<br>
								<h4 class="text-primary"><i class="fa fa-info-circle"></i> <?php echo app_lang('hr_reception_information'); ?></h4>
								<hr>
								<div class="col-md-12">
									<div class="col-md-12" id="manage_reception">

										<?php 
										$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");


										if (isset($group_checklist)) {
											foreach ($group_checklist as $key => $value) {?>                 
												<div class="row title">                           
													<div class="row">
														<div class="col-md-10">
															<div class="form-group">
																<input type="text" name="title_name[<?php echo html_entity_decode($key); ?>]" class="form-control" placeholder="<?php echo app_lang('hr_title'); ?>" value="<?php echo html_entity_decode($value['group_name']); ?>" required>
															</div>
														</div>
														<div class="col-md-2" name="button_add">
															<?php 
															if($key == 0){ ?>
																<button onclick="add_title(this); return false;" class="btn btn-primary mt-1 btn-title" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
															<?php }else{?>
																<button onclick="remove_title(this); return false;" class="btn btn-danger mt-1 btn-title" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
															<?php } ?>
														</div>
													</div>
													


													<?php 
													$checklist = $Hr_profile_model->checklist_by_group($value['id']);

													foreach ($checklist as $ind => $sub_item) {?>
														<div class="sub"> 
															<div class="row">                          
																<div class="col-md-9">
																	<div class="form-group">
																		<input type="text" name="sub_title_name[<?php echo html_entity_decode($key); ?>][<?php echo html_entity_decode($ind); ?>]" data-count="<?php echo html_entity_decode($key); ?>" class="form-control" value="<?php echo html_entity_decode($sub_item['name']); ?>" placeholder="<?php echo app_lang('hr_sub_title'); ?>" required>
																	</div>
																</div>
																<div class="col-md-3" name="button_add">
																	<?php 
																	if($ind == 0){ ?>
																		<button onclick="add_subtitle(this); return false;" class="btn btn-primary btn-sub-title" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
																	<?php }else{?>
																		<button onclick="remove_subtitle(this); return false;" class="btn btn-danger btn-sub-title" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
																	<?php } ?>
																</div>
															</div>
														</div>
													<?php  } ?>                   
													<div class="col-md-12 pl-0 sub_title"></div>
												</div>
												<?php 
											}}else{?>            
												<div class="row title">                           
													<div class="col-md-11 pt-2">
														<div class="form-group">
															<input type="text" name="title_name[0]" class="form-control" placeholder="Tiêu đề mục" value="">
														</div>
													</div>
													<div class="col-md-1 pl-0 pt-0" name="button_add">
														<button onclick="add_title(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
													</div>

													<div class="col-md-12 pl-0">
														<div class="sub">                           
															<div class="col-md-10 pt-2">
																<div class="form-group">
																	<input type="text" name="sub_title_name[0][0]" data-count="0" class="form-control" value="" placeholder="Mục con">
																</div>
															</div>
															<div class="col-md-2 pl-0 pt-0" name="button_add">
																<button onclick="add_subtitle(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
															</div>
														</div>
													</div>
													<div class="col-md-12 pl-0 sub_title"></div>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
							<?php } ?>
							<?php if(count($list_reception_staff_asset)>0){ ?>
								<div class="col-md-12 mt-1">
									<br>
									<h4 class="text-primary"><i class="fa fa-star"></i> <?php echo app_lang('hr_property_allocation'); ?></h4>
									<hr>
									<div class="col-md-12">
										<!--  Add assets    -->
										<div class="col-md-12 assets_wrap">
											<?php if($list_reception_staff_asset){
												foreach ($list_reception_staff_asset as $p_key => $p_value) {              
													?>
													<div id ="assets_emp" class="row">                            
														<div class="col-md-11 pt-2">
															<div class="form-group">
																<?php
																$name=$p_value['name'];

																?>
																<input type="text" name="asset_name[]" class="form-control" value="<?php echo html_entity_decode($name); ?>" placeholder="<?php echo app_lang('hr_enter_property_name'); ?>" required>
															</div>
														</div>                            
														<div class="col-md-1 pl-0 pt-0" name="button_add">
															<?php if($p_key == 0){ ?>
																<button name="add_asset" class="btn mt-1 new_assets_emp btn-primary" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>

															<?php }else{ ?>
																<button name="add_asset" class="btn mt-1 remove_assets_emp btn-danger" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>

															<?php } ?>

														</div>
													</div>
												<?php } ?>
											<?php }else{ ?>
												<div id ="assets_emp" class="row">                           
													<div class="col-md-11 pt-2">
														<div class="form-group">                
															<input type="text" name="asset_name[]" class="form-control" value="" placeholder="<?php echo app_lang('hr_enter_property_name'); ?>" required>
														</div>
													</div>
													<div class="col-md-1 pl-0 pt-0" name="button_add">
														<button name="add_asset" class="btn new_assets_emp btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
													</div>
												</div>
											<?php } ?>
										</div>
										<!--  End add asset    -->
									</div>
								</div>
							<?php } ?>
							<?php if(isset($setting_training)>0){ ?>
								<div class="col-md-12">
									<br>
									<h4 class="text-primary "><i class="fa fa-graduation-cap"></i> <?php echo app_lang('hr_training'); ?></h4>
									<hr >
									<div class="col-md-12">      
										<div class="row">

											<div class="col-md-12">
												<div class="form-group">
													<label><?php echo app_lang('type_of_training'); ?></label>

													<select name="training_type" class="select2 validate-hidden" id="training_type" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
														<option value=""></option> 
														<?php foreach ($type_of_trainings as $key => $value) { ?>
															<option value="<?php echo html_entity_decode($value['id']) ?>" <?php if(isset($setting_training) && $setting_training->training_type == $value['id']  ){echo 'selected';}; ?> ><?php echo html_entity_decode($value['name'])  ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>

							<div class="col-md-12">
								<div class="col-md-12">      
									<div class="row">
										<div class="col-md-12 ">
											<div class="form-group">
												<label><small class="req text-danger">* </small><?php echo app_lang('hr_training_program'); ?></label>
												<select name="training_program" class="select2 validate-hidden" id="training_program" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" >
											

												</select>
											</div>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>   
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hr_close'); ?></button>
						<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('submit'); ?></button>
					</div>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>

		<div class="modal fade" id="reception_sidebar" tabindex="-1" role="dialog">
			<div class="modal-dialog new_job_positions_dialog">
				<div class="modal-content">

				</div>
			</div>
		</div>


		<?php require 'plugins/Hr_profile/assets/js/reception_staff/reception_staff_manage_js.php';?>

	</body>
	</html>
