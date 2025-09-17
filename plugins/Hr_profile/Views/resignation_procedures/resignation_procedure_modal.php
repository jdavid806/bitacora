<?php echo form_open(get_uri("hr_profile/add_resignation_procedure"), array("id" => "staff_quitting_work_form-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">

		</ul>
		<?php 

		$id = '';
		$staffid = '';
		$email = '';
		$department_name = '';
		$role_name = '';
		$dateoff = '';

		if(isset($resignation)){
			$id = $resignation->id;
			$staffid = $resignation->staffid;
			$email = $resignation->email;
			$department_name = $resignation->department_name;
			$role_name = $resignation->role_name;
			$dateoff = $resignation->dateoff;
		}

		?>

		<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>" />

		<div class="row">
			<div class="col-md-12">
				<?php echo render_select1('staffid',$staffs, array('id', array('first_name', 'last_name')), 'staff',$staffid, [], [], '', '', true, true); ?>
			</div>
			<?php 
			$input_attr=[];
			$input_attr['readonly'] = true;
			?>

			<div class="col-md-12">
				<?php echo render_input1('email', 'email', $email , 'text', $input_attr, [], '', '', true); ?>
			</div>

			<div class="col-md-12">
				<?php echo render_input1('department_name', 'departments', $department_name , 'text', $input_attr) ?>
			</div>
			<div class="col-md-12">
				<?php echo render_input1('role_name', 'hr_hr_job_position', $role_name , 'text', $input_attr) ?>
			</div>
			<div class="col-md-12">
				<?php echo render_date_input1('dateoff', 'hr_day_off', $dateoff, [], [], '', '', true); ?>
			</div>
			
		</div>

	</div>
</div>

<div class="modal-footer">
	<button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
	<button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
<script>
	
	$(document).ready(function () {
		$("#staff_quitting_work_form-form .select2").select2();
		setDatePicker("#staff_quitting_work_form-form #dateoff");

	});


	$("#staff_quitting_work_form-form #staffid").on('change', function() {
		'use strict';
		var staff_id = $('select[name="staffid"]').val();
		if(staff_id != ''){
			$.get("<?php echo get_uri("hr_profile/get_staff_info_of_resignation_procedures/") ?>"+ staff_id).done(function(response){
				response = JSON.parse(response);
				
				if(response.status == true || response.status == 'true'){
					$('form').find('input[name="email"]').val(response.staff_email);
					$('form').find('input[name="department_name"]').val(response.staff_department_name);
					$('form').find('input[name="role_name"]').val(response.staff_job_position);
				}else{
					appAlert.warning(response.message);
				}

			}).fail(function(data) {
				appAlert.warning(data.responseText);
			});
		}else{
			$('form').find('input[name="email"]').val('');
			$('form').find('input[name="department_name"]').val('');
			$('form').find('input[name="role_name"]').val('');
			$('form').find('input[name="dateoff"]').val('');
		}
	});

</script>

