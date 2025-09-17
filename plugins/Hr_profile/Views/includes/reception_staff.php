<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "reception_staffs";
			echo view("Hr_profile\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_reception_information'); ?></h4>
					<div class="title-button-group">
					</div>
				</div>

				<?php echo form_open(get_uri("hr_profile/save_setting_reception_staff"), array("id" => "reception_staff_form-form", "class" => "general-form", "role" => "form")); ?>

				<div class="card-body">
					<div id="manage_reception">
						<?php 
						if (count($group_checklist)>0) {
							foreach ($group_checklist as $key => $value) {?>                 
								<div class="row title">                           
									<div class="col-md-11 pt-2">
										<div class="form-group">
											<input type="text" name="title_name[<?php echo html_entity_decode($key); ?>]" class="form-control" placeholder="<?php echo app_lang('hr_title'); ?>" value="<?php echo html_entity_decode($value['group_name']); ?>">
										</div>
									</div>
									<div class="col-md-1 pl-0 pt-0" name="button_add">
										<?php 
										if($key == 0){ ?>
											<button onclick="add_title(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
										<?php }else{?>
											<button onclick="remove_title(this); return false;" class="btn btn-danger mt-1" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
										<?php } ?>
									</div>
									<?php 
									$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

									$checklist = $Hr_profile_model->checklist_by_group($value['id']);

									foreach ($checklist as $ind => $sub_item) {?>
										<div class=" row col-md-12 pl-0">
											<div class="sub row">                           
												<div class="col-md-10 pt-2">
													<div class="form-group">
														<input type="text" name="sub_title_name[<?php echo html_entity_decode($key); ?>][<?php echo html_entity_decode($ind); ?>]" data-count="<?php echo html_entity_decode($key); ?>" class="form-control" value="<?php echo html_entity_decode($sub_item['name']); ?>" placeholder="<?php echo app_lang('hr_sub_title'); ?>">
													</div>
												</div>
												<div class="col-md-2 pl-0 pt-0" name="button_add">
													<?php 
													if($ind == 0){ ?>
														<button onclick="add_subtitle(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
													<?php }else{?>
														<button onclick="remove_subtitle(this); return false;" class="btn btn-danger mt-1" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
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
											<input type="text" name="title_name[0]" class="form-control" placeholder="<?php echo app_lang('hr_title'); ?>" value="">
										</div>
									</div>
									<div class="col-md-1 pl-0 pt-0" name="button_add">
										<button onclick="add_title(this); return false;" class="btn btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
									</div>

									<div class="col-md-12 pl-0">
										<div class="sub row">                           
											<div class="col-md-10 pt-2">
												<div class="form-group">
													<input type="text" name="sub_title_name[0][0]" data-count="0" class="form-control" value="" placeholder="<?php echo app_lang('hr_sub_title'); ?>">
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

					<div class="page-title clearfix">
						<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_property_allocation'); ?></h4>
						<div class="title-button-group">
						</div>
					</div>
					<div class="card-body">
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
												<input type="text" name="asset_name[]" class="form-control" value="<?php echo html_entity_decode($name); ?>" placeholder="<?php echo app_lang('hr_enter_property_name'); ?>" >
											</div>
										</div>                            
										<div class="col-md-1 pl-0 pt-0" name="button_add">
											<?php if($p_key == 0){ ?>
												<button name="add_asset" class="btn new_assets_emp btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
											<?php }else{ ?>
												<button name="add_asset" class="btn remove_assets_emp btn-danger mt-1" data-ticket="true" type="button"><span data-feather="x" class="icon-16"></span></button>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							<?php }else{ ?>
								<div id ="assets_emp" class="row">                           
									<div class="col-md-11 pt-2">
										<div class="form-group">                
											<input type="text" name="asset_name[]" class="form-control" value="" placeholder="<?php echo app_lang('hr_enter_property_name'); ?>" >
										</div>
									</div>
									<div class="col-md-1 pl-0 pt-0" name="button_add">
										<button name="add_asset" class="btn new_assets_emp btn-primary mt-1" data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16"></span></button>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>

					<div class="page-title clearfix">
						<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_training'); ?></h4>
						<div class="title-button-group">
						</div>
					</div>

					<div class="card-body">
						<div class="row">
							<div class="check col-md-12">
								<div class="col-md-12">
									<div class="form-group mt-2">
										<select name="training_type" class="select2 validate-hidden" id="training_type" data-width="100%" data-none-selected-text="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
											<option value=""></option> 
											<?php foreach ($type_of_trainings as $key => $value) { ?>

												<option value="<?php echo html_entity_decode($value['id']); ?>" <?php if(isset($setting_training) && $setting_training->training_type == $value['id'] ){echo 'selected';}; ?> ><?php echo html_entity_decode($value['name']) ?></option>

											<?php } ?>

										</select>
									</div>
								</div>
								<div id="list_check"></div>
							</div>        
						</div>
					</div>
					<?php if (hr_has_permission('hr_profile_can_create_setting') || hr_has_permission('hr_profile_can_edit_setting') || hr_has_permission('hr_profile_can_delete_setting') || is_admin() ) { ?>
						<div class="modal-footer">
							<button type="submit" class="btn btn-primary inventory_min_modal"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('save'); ?></button>
						</div>
					<?php } ?>

					<?php echo form_close(); ?>

				</div>
			</div>
		</div>
	</div>
	<?php require 'plugins/Hr_profile/assets/js/setting/reception_staff_js.php';?>
</body>
</html>
