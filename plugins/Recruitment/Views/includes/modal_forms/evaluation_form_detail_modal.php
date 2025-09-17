<?php echo form_open(get_uri("recruitment/evaluation_form_add_criteria/".$id), array("id" => "add_evaluation_form-form", "class" => "general-form", "role" => "form")); 
$time = time();
?>
<div id="items-dropzone" class="post-dropzone <?php echo html_entity_decode($time); ?>">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$group_criteria = '';
						$group_criteria_data = [];
						$total_eval = 1;
						

						if(isset($evaluation_form_detail_data) && count($evaluation_form_detail_data) > 0){
							$group_criteria = $evaluation_form_detail_data[0]['group_criteria'];
							if(isset($e_group_criteria_data)){
								$group_criteria_data = $e_group_criteria_data;
							}
						}
						?>

						<div class="col-md-12">
							<?php echo form_hidden('evaluation_form', $evaluation_id) ?>

							<p class="bold margin-top-15 general-infor-color"><?php echo app_lang('list_of_evaluation_criteria'); ?></p>
							<!-- list criteria -->
							<div id="list_criteria">
								<div class="new-kpi-group-al">
									<div id="new_kpi_group" class="col-md-12">

										<div class="row margin-top-10">
											<div class="col-md-12">
												<div class="form-group group_criteria_class">
													<label for="group_criteria" class="control-label"><span class="text-danger">* </span><?php echo app_lang('group_criteria'); ?></label>
													<select onchange="group_criteria_change(this)" name="group_criteria" class="select2 validate-hidden" id="group_criteria" data-width="100%" placeholder="<?php echo app_lang('group_criteria'); ?>" required>
														<option value=""></option>
														<?php foreach ($list_group as $kpi_coll) {?>
															<option value="<?php echo html_entity_decode($kpi_coll['criteria_id']); ?>" <?php if($kpi_coll['criteria_id'] == $group_criteria){ echo "selected";} ?>> <?php echo html_entity_decode($kpi_coll['criteria_title']); ?></option>
														<?php }?>
													</select>
												</div>
											</div>

										</div>
										<div class="row " >
											<div class="col-md-11 new-kpi-al pull-right padding-left-right-20-0">
												<?php if(isset($evaluation_form_detail_data) && count($evaluation_form_detail_data) > 0){ ?>

													<?php foreach ($evaluation_form_detail_data as $key => $value) { 
														$total_eval++;
														?>
														<div id ="new_kpi" class="row paddig-top-height-0-75">

															<div class="col-md-7 padding-right-0">
																<div class="form-group evaluation_criteria_class">
																	<label for="evaluation_criteria[<?php echo html_entity_decode($key); ?>]" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo app_lang('evaluation_criteria'); ?></label>
																	<select name="evaluation_criteria[<?php echo html_entity_decode($key); ?>]" class="select2 validate-hidden" id="evaluation_criteria[<?php echo html_entity_decode($key); ?>]" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-sl-id="e_criteria[<?php echo html_entity_decode($key); ?>]" required>
																		<?php foreach ($group_criteria_data as $kpi_coll) {?>
																			<option value="<?php echo html_entity_decode($kpi_coll['criteria_id']); ?>" <?php if($kpi_coll['criteria_id'] == $value['evaluation_criteria']){ echo "selected";} ?>> <?php echo html_entity_decode($kpi_coll['criteria_title']); ?></option>
																		<?php }?>
																	</select>
																</div>
															</div>

															<div class="col-md-3 padding-right-0">
																<?php echo render_input1('percent['.$key.']', 'proportion', $value['percent'], '', ['min' => 1, 'max' => 100, 'step' => 1], [], '', '', true); ?>
															</div>
															<?php if($key == 0){ ?>
																<div class="col-md-1 mt25 " name="button_add_kpi ">
																	<button name="add" class="btn new_kpi btn-success " data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16" ></span> </button>
																</div>
															<?php }else{ ?>
																<div class="col-md-1 mt25 " name="button_add_kpi ">
																	<button name="add" class="btn remove_kpi btn-danger " data-ticket="true" type="button"><span data-feather="x" class="icon-16" ></span> </button>
																</div>
															<?php } ?>

														</div>
													<?php } ?>

												<?php }else{ ?>
													<div id ="new_kpi" class="row paddig-top-height-0-75">

														<div class="col-md-7 padding-right-0">
															<div class="form-group evaluation_criteria_class">
																<label for="evaluation_criteria[0]" class="control-label get_id_row" value ="0" ><span class="text-danger">* </span><?php echo app_lang('evaluation_criteria'); ?></label>
																<select name="evaluation_criteria[0]" class="select2 validate-hidden" id="evaluation_criteria[0]" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-sl-id="e_criteria[0]" required>
																</select>
															</div>
														</div>

														<div class="col-md-3 padding-right-0">
															<?php echo render_input1('percent[0]', 'proportion', '', '', ['min' => 1, 'max' => 100, 'step' => 1], [], '', '', true); ?>
														</div>
														<div class="col-md-1 mt25 " name="button_add_kpi ">
															<button name="add" class="btn new_kpi btn-success " data-ticket="true" type="button"><span data-feather="plus-circle" class="icon-16" ></span> </button>
														</div>

													</div>
												<?php } ?>

											</div>

										</div>

									</div>
								</div>
							</div>
							<!-- list criteria -->


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
<?php require 'plugins/Recruitment/assets/js/settings/modal_forms/evaluation_form_detail_modal_js.php';?>
