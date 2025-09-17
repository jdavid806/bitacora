<?php echo form_open(get_uri("hr_profile/workplace/".$id), array("id" => "add_workplace-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">
			<div class="row">
				<div class="form"> 
					<?php 
					$name = '';
					$workplace_address = '';
					$latitude = '';
					$longitude = '';

					if(isset($workplace_data)){
						$name = $workplace_data->name;
						$workplace_address = $workplace_data->workplace_address;
						$latitude = $workplace_data->latitude;
						$longitude = $workplace_data->longitude;
					}
					?>

					<div class="col-md-12">
						<div id="additional_workplace"></div>   
						<div class="form">     
							<?php 
							echo render_input1('name','hr_hr_workplace', $name, '', [], [], '', '', true); ?>
						</div>
					</div>
					<div class="col-md-12">
						<?php echo render_textarea1('workplace_address', 'hr_workplace_address', $workplace_address) ?>
					</div>
					<div class="row">
						<div class="col-md-6">

							<?php echo render_input1('latitude', 'hr_latitude_lable', $latitude, 'number', ["step" => "any"]) ?>
						</div>
						<div class="col-md-6">
							<?php echo render_input1('longitude', 'hr_longitude_lable', $longitude, 'number', ["step" => "any"]) ?>
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
