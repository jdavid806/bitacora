<div class="card rounded-0">
	<div class="page-title clearfix">

		<div class="title-button-group">
			<?php if($user_id == get_staff_user_id1() || hr_has_permission('hr_profile_can_create_hr_records') || hr_has_permission('hr_profile_can_edit_hr_records')){ ?>
				<div class="_buttons">
					<a href="#" onclick="new_dependent_person(); return false;"  class="btn btn-info pull-left text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('hr_add_dependents'); ?></a>

				</div>
			<?php } ?>
		</div>
	</div>

	<div class="table-responsive">
		<?php render_datatable1(array(
			'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_dependent_person"><label></label></div>',
			app_lang('id'),
			app_lang('hr_dependent_name'),
			app_lang('hr_hr_staff_name'),
			app_lang('hr_dependent_bir'),
			app_lang('hr_dependent_iden'),
			app_lang('hr_start_month'),
			app_lang('hr_reason_label'),
			app_lang('hr_status_label'),
			app_lang('options'),
			app_lang('hr_status_comment'),
			"<i data-feather='menu' class='icon-16'></i>",

		),'table_dependent_person'); ?>

	</div>
</div>
<div class="modal fade" id="dependent" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open_multipart(get_uri("hr_profile/dependent_person"), array("id" => "dependent_person", "class" => "general-form", "role" => "form", "autocomplete" => "false")); ?>


		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<span class="edit-title"><?php echo app_lang('hr_edit_dependents'); ?></span>
					<span class="add-title"><?php echo app_lang('hr_add_dependents'); ?></span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body card-body">
				<div class="row">
					<div class="col-md-12">
						<div id="dependent_person_id"></div>   
						<div class="form"> 
							<div class="row">
								<div class="col-md-6">
									<?php 
									echo render_input1('dependent_name','hr_dependent_name', '', '', [], [], '', '', true); ?>
								</div>
								<div class="col-md-6">
									<?php 
									echo render_input1('relationship','hr_hr_relationship', '', '', [], [], '', '', true); ?>
								</div>
							</div>    
							<div class="row">
								<div class="col-md-6">
									<?php 
									echo render_date_input1('dependent_bir','hr_dependent_bir', '', [], [], '', '', true); ?>
								</div>
								<div class="col-md-6">
									<?php 
									echo render_input1('dependent_iden','hr_citizen_identification','','number'); ?>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<?php 
									echo render_input1('reason','hr_reason_label'); ?>
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
	</div>
</div>
<?php require 'plugins/Hr_profile/assets/js/hr_record/includes/dependent_person_js.php';?>
