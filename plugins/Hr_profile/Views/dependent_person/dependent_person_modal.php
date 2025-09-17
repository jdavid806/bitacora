<?php echo form_open(get_uri("hr_profile/dependent_person"), array("id" => "dependent_person", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">

			<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>" />
			<input type="hidden" name="manage" value="<?php echo html_entity_decode($manage); ?>" />

			<div class="row">

				<div class="col-md-12 <?php if(isset($dependent_person)){ echo ' hide' ;}; ?>">
					<?php 
					$staff_selected = '';
					if(isset($dependent_person)){
						$staff_selected = $dependent_person->staffid;
					}

					?>
					<?php echo render_select1('staffid',$staff_members,array('id',array('first_name', 'last_name')),'hr_hr_staff_name',$staff_selected, [], [], '', '', false, true); ?>
				</div>
				<div class="col-md-6">
					<?php 
					$dependent_name =  isset($dependent_person) ? $dependent_person->dependent_name : '';
					echo render_input1('dependent_name','hr_dependent_name', $dependent_name, '', [], [], '', '', true); ?>
				</div>
				<div class="col-md-6">
					<?php 
					$relationship =  isset($dependent_person) ? $dependent_person->relationship : '';
					echo render_input1('relationship','hr_hr_relationship', $relationship, '', [], [], '', '', true); ?>
				</div>
			</div> 
			<div class="row">
				<div class="col-md-6">

					<?php 
					$birthday =  isset($dependent_person) ? format_to_date($dependent_person->dependent_bir, false) : '';
					echo render_date_input1('dependent_bir','hr_dependent_bir', $birthday, [], [], '', '', true); ?>
				</div>
				<div class="col-md-6">
					<?php 
					$dependent_iden =  isset($dependent_person) ? $dependent_person->dependent_iden : '';

					echo render_input1('dependent_iden','hr_citizen_identification', $dependent_iden,'number'); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<?php 
					$reason =  isset($dependent_person) ? $dependent_person->reason : '';
					echo render_input1('reason','hr_reason_label', $reason); ?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<?php 
					$start_month =  isset($dependent_person) ? format_to_date($dependent_person->start_month, false) : '';
					echo render_date_input1('start_month','hr_start_month', $start_month); ?>
				</div>
				<div class="col-md-6">
					<?php 
					$end_month =  isset($dependent_person) ? format_to_date($dependent_person->end_month, false) : '';
					echo render_date_input1('end_month','hr_end_month', $end_month); ?>
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
<?php require 'plugins/Hr_profile/assets/js/dependent_person/modal_js.php';?>

