<?php echo form_open(get_uri("recruitment/setting_tranfer/".$id), array("id" => "add_on_boarding-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body clearfix">
		<div class="container-fluid">
			<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>" />

			<div class="row">
				<div class="col-md-12">
					<div class="form"> 
						<?php 

						$order = 1;
						$send_to = '';
						$email_to = '';
						$content = '';
						$subject = '';
						$email_to_div = ' hide';
						$email_to_required = '';
						$email_to_data = [];

						if(isset($on_boarding_data)){
							$order = $on_boarding_data->order;
							$send_to = $on_boarding_data->send_to;
							$email_to = explode(",", $on_boarding_data->email_to);
							$content = $on_boarding_data->content;
							$subject = $on_boarding_data->subject;

							if($send_to == 'staff' || $send_to == 'department'){
								$email_to_div = '';
								$email_to_required = true;

							}

							if($send_to == 'staff'){
								$email_to_data = $arr_staff;
							}elseif($send_to == 'department'){
								$email_to_data = $arr_dpm;
							}
						}
						?>

						<div class="row">
							<div class="col-md-12">
								<?php echo render_input1('order', 'order', $order, '', [], [], '', '', true); ?>
							</div>


							<div class="col-md-12">
								<?php 
								$send_to_data = [];
								$send_to_data[] = [
									'name' => 'candidate',
									'label' => app_lang('successful_candidates'),
								];
								$send_to_data[] = [
									'name' => 'staff',
									'label' => app_lang('staff'),
								];
								$send_to_data[] = [
									'name' => 'department',
									'label' => app_lang('department'),
								];


								?>
								<?php echo render_select1('send_to', $send_to_data, array('name', 'label'), 'send_to', $send_to, [], [], '', '', false, true); ?>

							</div>

							<div class="col-md-12 <?php echo html_entity_decode($email_to_div); ?>" id="email_to_div">		
								<?php echo render_select1('email_to[]', $email_to_data, array('name', 'label'), 'email_to', $email_to, ['multiple' => true], [], '', '', false, $email_to_required); ?>
							</div>

							<div class="col-md-12">
								<?php echo render_input1('subject', 'subject', $subject, '', [], [], '', '', true); ?>
							</div>

							<div class="col-md-12">		
								<?php echo render_textarea1('content', 'content', $content) ?>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-12 row pr0">
									<?php
									if(isset($model_info)){
										$files = $model_info->files;
									}else{
										$files = '';
									}
									$get_timeline_file_path = SET_TRANSFER_UPLOAD;
									echo view("Recruitment\Views\includes\modal_forms/file_list", array("files" => $files, "image_only" => true, 'get_timeline_file_path' => $get_timeline_file_path));
									?>
								</div>
							</div>
						</div>

						<?php echo view("includes/dropzone_preview"); ?>

						<button class="btn btn-default upload-file-button float-start btn-sm round me-auto color-7988a2" type="button" ><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_image"); ?></button>
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
<?php require 'plugins/Recruitment/assets/js/settings/modal_forms/on_boarding_modal_js.php';?>
