<?php echo form_open(get_uri("hr_profile/department"), array("id" => "general-form-form", "class" => "general-form", "role" => "form")); ?>
<div id="items-dropzone" class="post-dropzone">
	<div class="modal-body card-body clearfix">
		<div class="container-fluid">

		</ul>
		<?php 

		$department_id = '';
		$title = '';
		$manager_id = '';
		$parent_id = '';

		if(isset($department)){
			$department_id = $department->id;
			$title = $department->title;
			$manager_id = $department->manager_id;
			$parent_id = $department->parent_id;
		}

		?>

		<input type="hidden" name="id" value="<?php echo html_entity_decode($department_id); ?>" />

		<div class="row">
			<div class="col-md-12">
				<?php echo render_input1('title','unit_name', $title, '', [], [], '', '', true); ?>
			</div>
			<div class="col-md-12">
				<?php echo render_select1('manager_id', $list_staff,array('id', array('first_name', 'last_name')),'hr_manager_unit', $manager_id); ?>
			</div>

			<div class="col-md-12">
				<?php echo render_select1('parent_id',$list_department ,array('id','title'),'hr_parent_unit', $parent_id); ?>
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
		$("#general-form-form .select2").select2();
	});
</script>

