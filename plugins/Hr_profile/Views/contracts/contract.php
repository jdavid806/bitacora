<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<?php echo form_open_multipart(get_uri("hr_profile/contract"), array("id" => "staff-contract-form", "class" => "general-form", "role" => "form")); ?>
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>
				<div class="modal-body card-body clearfix">

					<?php 
					$id = '';
					if(isset($contracts)){
						$id = $contracts->id_contract;
						echo form_hidden('isedit');
					}
					?>


					<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>">

					<div class="row">
						<div class="col-md-6">

							<?php 
							$attrs = (isset($contracts) ? array('readonly' => true) : array('autofocus'=>true, 'readonly' => true));
							$contract_code = (isset($contracts) ? $contracts->contract_code : $staff_contract_code);
							echo render_input1('contract_code','hr_contract_code',$contract_code,'text',$attrs); ?>   
						</div>
						<div class="col-md-6">
							<label for="staff" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('hr_hr_staff_name'); ?></label>
							<select name="staff" class="select2 validate-hidden" id="staff" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" data-live-search="true" required="1" data-rule-required="1"> 
								<option value="">-</option>                  
								<?php foreach($staff as $s){ ?>
									<option value="<?php echo html_entity_decode($s['id']); ?>"  <?php if(isset($contracts) && $contracts->staff == $s['id'] ){echo 'selected';} ?>> <?php echo html_entity_decode($s['first_name'].''.$s['last_name']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="name_contract" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('hr_name_contract'); ?></label>
								<select name="name_contract" class="select2 validate-hidden" id="name_contract" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required="1" data-rule-required="1"> 
									<option value="">-</option>                  
									<?php foreach($contract_type as $c){ ?>
										<option value="<?php echo html_entity_decode($c['id_contracttype']); ?>" <?php if(isset($contracts) && $contracts->name_contract == $c['id_contracttype'] ){echo 'selected';} ?>><?php echo html_entity_decode($c['name_contracttype']); ?> </option>
									<?php }?>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="contract_status" class="control-label"><small class="req text-danger">* </small><?php echo app_lang('hr_status_label'); ?></label>
								<select name="contract_status" class="select2 validate-hidden" id="contract_status" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" required> 
									<option value="draft" <?php if(isset($contracts) && $contracts->contract_status == 'draft' ){echo 'selected';} ?> ><?php echo app_lang('hr_hr_draft') ?></option>
									<option value="valid" <?php if(isset($contracts) && $contracts->contract_status == 'valid' ){echo 'selected';} ?>><?php echo app_lang('hr_hr_valid') ?></option>
									<option value="invalid" <?php if(isset($contracts) && $contracts->contract_status == 'invalid' ){echo 'selected';} ?>><?php echo app_lang('hr_hr_expired') ?></option>
									<option value="finish" <?php if(isset($contracts) && $contracts->contract_status == 'finish' ){echo 'selected';} ?>><?php echo app_lang('hr_hr_finish') ?></option>
								</select>
							</div>
						</div>

					</div>
					<div class="row">
						<div class="col-md-6">
							<?php

							$start_valid = (isset($contracts) ? $contracts->start_valid : date('Y-m-d'));
							echo render_date_input1('start_valid','hr_start_month', ($start_valid), [], [], '', '', true); ?>
						</div>
						<div class="col-md-6">
							<?php
							$end_valid = (isset($contracts) ? $contracts->end_valid : '');
							echo render_date_input1('end_valid','hr_end_month', ($end_valid)); ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="hourly_or_month" class="control-label"><?php echo app_lang('hr_hourly_rate_month'); ?></label>
								<select name="hourly_or_month" class="select2 validate-hidden" id="hourly_or_month" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>"> 
									<option value="month" <?php if(isset($contracts) && $contracts->hourly_or_month == 'month' ){echo 'selected';} ?>><?php echo app_lang('hr_month') ?></option>
									<option value="hourly_rate" <?php if(isset($contracts) && $contracts->hourly_or_month == 'hourly_rate' ){echo 'selected';} ?> ><?php echo app_lang('hourly_rate') ?></option>

								</select>
							</div>
						</div>
					</div>


				</div>

			</div>

			<div class="card">
				<div class="table-responsive pt15 pl15 pr15">
					<div class="form"> 
						<div id="staff_contract_hs" class="col-md-12 add_handsontable handsontable htColumnHeaders">

						</div>
						<?php echo form_hidden('staff_contract_hs'); ?>
					</div>
				</div>

			</div>

			<div class="card">
				<div class="modal-body card-body clearfix">
					<div class="row">
						<div class="col-md-6">
							<?php
							$sign_day = (isset($contracts) ? $contracts->sign_day : date('Y-m-d'));
							echo render_date_input1('sign_day','hr_sign_day', ($sign_day)); ?>

						</div>
						<div class="col-md-6">
							<label for="staff_delegate" class="control-label"><?php echo app_lang('hr_staff_delegate'); ?></label>
							<select name="staff_delegate" class="select2 validate-hidden" data-live-search="true" id="staff_delegate" data-width="100%" placeholder="<?php echo app_lang('dropdown_non_selected_tex'); ?>" > 
								<option value=""></option>                  
								<?php foreach($staff as $s){ ?>
									<option value="<?php echo html_entity_decode($s['id']); ?>"  <?php if(isset($contracts) && $contracts->staff_delegate == $s['id'] ){echo 'selected';} ?>> <?php echo html_entity_decode($s['first_name'].''.$s['last_name']); ?></option>                  
								<?php }?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class=" attachments">
								<div class="attachment">
									<div class="form-group">
										<label for="attachment" class="control-label"><?php echo app_lang('hr_attachment'); ?></label>
										<div class="input-group">
											<input type="file"  class="form-control" name="file[0]">
											<span class="input-group-btn">
												<button class="btn btn-success add_more_attachments_file p8" type="button"><span data-feather="plus-circle" class="icon-16" ></span> </button>
											</span>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<div class="row">

						<div class="col-md-6">
							<div class="row d-none">
								<div class="col-md-12">
									<?php 
									$staff_role = (isset($staff_delegate_role) ? $staff_delegate_role->title : '');
									echo render_input1('job_position','hr_hr_job_position',$staff_role,'text',$attrs); ?> 
								</div>
							</div>
							<div class="row">
								<div id="contract_attachments" class="mtop30 ">
									<?php if(isset($contract_attachment)){ ?>

										<?php
										$data = '<div class="row" id="attachment_file">';
										foreach($contract_attachment as $attachment) {
											$href_url = site_url('modules/hr_profile/uploads/contracts/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
											if(!empty($attachment['external'])){
												$href_url = $attachment['external_link'];
											}
											$data .= '<div class="display-block contract-attachment-wrapper">';
											$data .= '<div class="row">';
											$data .= '<div class="col-md-1 mr-5">';
											$data .= modal_anchor(get_uri("hr_profile/hrm_file_contract/".$attachment['id']."/".$attachment['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $attachment['file_name'], "data-post-id" => $attachment['id']));

											$data .= '</a>';
											$data .= '</div>';
											$data .= '<div class=col-md-9>';
											$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
											$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
											$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
											$data .= '</div>';
											$data .= '<div class="col-md-2 text-right">';
											if(is_admin() || hr_has_permission('hr_profile_can_delete_hr_contract')){
												$data .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,'.$attachment['id'].'); return false;"><span data-feather="x-circle" class="icon-16" ></span></a>';
											}
											$data .= '</div>';
											$data .= '</div>';

											$data .= '<div class="clearfix"></div><hr/>';
											$data .= '</div>';
										}
										$data .= '</div>';
										echo html_entity_decode($data);
										?>
									<?php } ?>
									<!-- check if edit contract => display attachment file end-->

								</div>

								<div id="contract_file_data"></div>
							</div>

						</div>


					</div>
				</div>
			</div>

			<div class="card">
				<div class="container-fluid">
					<div class="">
						<div class="btn-bottom-toolbar text-right mb20 mt20">
							<a href="<?php echo get_uri('hr_profile/contracts'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>

							<?php if(hr_has_permission('hr_profile_can_create_hr_contract') || hr_has_permission('hr_profile_can_edit_hr_contract')){ ?>
								<a href="#"class="btn btn-info text-white add_goods_receipt" ><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></a>
							<?php } ?>
						</div>
					</div>
					<div class="btn-bottom-pusher"></div>
				</div>
			</div>

			<?php echo form_close(); ?>
		</div>
	</div>
</div>
<div id="modal_wrapper"></div>

<?php require 'plugins/Hr_profile/assets/js/contracts/contract_js.php';?>


</body>
</html>
