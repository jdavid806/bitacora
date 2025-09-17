<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-8 col-lg-8 container-fluid">
			<div class="card">
				<?php echo form_hidden('cd_id',$candidate->id); ?>
				<?php echo form_open(site_url('recruitment/transfer_hr/'.$candidate->id),array('class'=>'transfer-form general-form','autocomplete'=>'off')); ?>
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
				</div>

				<div class="modal-body clearfix">

					<div class="row">
						<div class="col-md-12">
							<?php $attrs = (isset($candidate) ? array() : array('autofocus'=>true)); ?>
							<?php $staff_identifi = $staff_code ?>
							<?php echo render_input1('staff_identifi','re_staff_code', $staff_identifi,'text',$attrs); ?>
						</div>

					</div>
					<div class="row">
						<div class="col-md-6">
							<?php $value = (isset($candidate) ? $candidate->candidate_name : ''); ?>
							<?php $attrs = (isset($candidate) ? array() : array('autofocus'=>true)); ?>
							<?php echo render_input1('first_name','first_name',$value,'text',$attrs, [], '', '', true); ?>  
						</div>

						<div class="col-md-6">
							<?php $last_name = (isset($candidate) ? $candidate->last_name : ''); ?>
							<?php $attrs = (isset($candidate) ? array() : array('autofocus'=>true)); ?>
							<?php echo render_input1('last_name','last_name',$last_name,'text',$attrs, [], '', '', true); ?>  
						</div>

					</div>
					<div class="row">
						<div class="col-md-6">
							<?php $email = isset($candidate) ? $candidate->email : ''; ?>
							<?php echo render_input1('email','email', $email,'email',array('autocomplete'=>'off'), [], '', '', true); ?>
						</div>

						<div class="col-md-6">
							<?php $value = (isset($candidate) ? $candidate->phonenumber : ''); ?>
							<?php echo render_input1('phone','phonenumber',$value); ?>
						</div>
					</div>

					<div class="row">

						<?php if(isset($positions)){ ?>
							<div class="col-md-6">
								<div class="form-group">
									<label for="job_position" class="control-label"><?php echo app_lang('hr_hr_job_position'); ?></label>
									<select name="job_position" class="select2 validate-hidden" id="job_position" data-width="100%"  data-action-box="true" data-hide-disabled="true" data-live-search="true" placeholder="<?php echo app_lang('hr_hr_job_position'); ?>"> 
										<option value=""></option> 
										<?php foreach($positions as $p){ ?> 
											<option value="<?php echo html_entity_decode($p['position_id']); ?>" <?php if(isset($position_id) && $position_id == $p['position_id']){echo 'selected';} ?>><?php echo html_entity_decode($p['position_name']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>

						<div class="col-md-6">
							<?php 
							$birthday = (isset($candidate) ? $candidate->birthday : ''); 
							echo render_date_input1('dob','birthday', format_to_date($birthday, false)); ?>
						</div>

						<div class="form-group col-md-6">
							<label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo app_lang('facebook'); ?></label>
							<input type="text" class="form-control" name="facebook" value="<?php echo html_entity_decode($candidate->facebook); ?>">
						</div>

						<div class="form-group col-md-6">
							<label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo app_lang('skype'); ?></label>
							<input type="text" class="form-control" name="skype" value="<?php echo html_entity_decode($candidate->skype); ?>">
						</div>


						<div class="col-md-6">
							<?php
							$birthplace = (isset($candidate) ? $candidate->birthplace : '');
							echo render_input1('birthplace','birthplace',$birthplace,'text'); ?> 
						</div>
						<div class="col-md-6">
							<?php 
							$home_town = (isset($candidate) ? $candidate->home_town : '');
							echo render_input1('home_town','home_town',$home_town,'text'); ?> 
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="marital_status" class="control-label"><?php echo app_lang('marital_status'); ?></label>
								<select name="marital_status" class="select2 validate-hidden" id="marital_status" data-width="100%" placeholder="<?php echo app_lang('marital_status'); ?>"> 
									<option value=""></option>                  
									<option value="<?php echo 'single'; ?>" <?php if(isset($candidate) && $candidate->marital_status == 'single'){echo 'selected';} ?>><?php echo app_lang('single'); ?></option>
									<option value="<?php echo 'married'; ?>" <?php if(isset($candidate) && $candidate->marital_status == 'married'){echo 'selected';} ?>><?php echo app_lang('married'); ?></option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<?php
							$nation = (isset($candidate) ? $candidate->nation : '');
							echo render_input1('nation','nation',$nation,'text'); ?>
						</div>
						<div class="col-md-6">
							<?php 
							$religion = (isset($candidate) ? $candidate->religion : '');
							echo render_input1('religion','religion',$religion,'text'); ?>
						</div>

						<div class="col-md-6">
							<?php 
							$identification = (isset($candidate) ? $candidate->identification : '');
							echo render_input1('identification','identification',$identification,'text'); ?>
						</div>
						<div class="col-md-6">
							<?php
							$days_for_identity = (isset($candidate) ? $candidate->days_for_identity : '');
							echo render_date_input1('days_for_identity','days_for_identity', format_to_date($days_for_identity, false)); ?>
						</div>
						<div class="col-md-6">
							<?php
							$place_of_issue = (isset($candidate) ? $candidate->place_of_issue : '');
							echo render_input1('place_of_issue','place_of_issue',$place_of_issue, 'text'); ?>
						</div>

						<div class="col-md-6">
							<?php 
							$resident = (isset($candidate) ? $candidate->resident : '');
							echo render_input1('resident','resident',$resident,'text'); ?>
						</div>
						<div class="col-md-6">
							<?php 
							$current_accommodation = (isset($candidate) ? $candidate->current_accommodation : '');
							echo render_input1('address','current_address',$current_accommodation,'text'); ?>
						</div>
						<div class="col-md-12">
							<?php
							echo render_input1('literacy','literacy','','text'); ?>
						</div>
						<div class="col-md-12">
							<?php echo render_select1('role_id',$roles,array('id','title'),'role','', [], [], '', '', false); ?>
						</div>
					</div>

					<div class="form-group">
						<label for="password" class="col-md-3"><small class="req text-danger">* </small><?php echo app_lang('password'); ?></label>
						<div class="row">
							<div class=" col-md-11">
								<div class="input-group">
									<?php
									echo form_password(array(
										"id" => "password",
										"name" => "password",
										"class" => "form-control",
										"placeholder" => app_lang('password'),
										"autocomplete" => "off",
										"data-rule-required" => true,
										"data-msg-required" => app_lang("field_required"),
										"data-rule-minlength" => 6,
										"data-msg-minlength" => app_lang("enter_minimum_6_characters"),
										"autocomplete" => "off",
										"required" => 1,
										"style" => "z-index:auto;"
									));
									?>
									<button type="button" class="input-group-text clickable no-border" id="generate_password"><span data-feather="key" class="icon-16"></span> <?php echo app_lang('generate'); ?></button>
								</div>
							</div>
							<div class="col-md-1 p0">
								<a href="#" id="show_hide_password" class="btn btn-default" title="<?php echo app_lang('show_text'); ?>"><span data-feather="eye" class="icon-16"></span></a>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<hr class="hr-10" />
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="email_login_details" id="email_login_details" class='form-check-input'>
								<label for="email_login_details"><?php echo app_lang('email_login_details'); ?></label>
							</div>
						</div>
					</div>

					<div class=" text-right btn-toolbar-container-out">
						<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('transfer'); ?></button>
					</div>
					<?php echo form_close(); ?>
				</div>

			</div>
		</div>

	</div>
</div>
<?php require 'plugins/Recruitment/assets/js/transfer_to_hr_js.php';?>
