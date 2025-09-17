<div class="tab-content">
	<?php echo form_open(get_uri("hr_profile/role_save_permissions"), array("id" => "permissions-form", "class" => "general-form dashed-row", "role" => "form")); ?>
	<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
	<div class="card">
		<div class="card-header">
			<h4><?php echo app_lang('permissions') . ": " . $model_info->title; ?></h4>
		</div>
		<div class="card-body">

			<ul class="permission-list">
				<?php echo  view('Hr_profile\Views\includes\hr_permissions', $permissions); ?>

				<div class="d-none">
					<?php app_hooks()->do_action('app_hook_role_permissions_extension_plugins', $permissions); ?>
				</div>
			</ul>

		</div>
		<div class="card-footer">
			<button type="submit" class="btn btn-primary mr10"><span data-feather="check-circle" class="icon-14"></span> <?php echo app_lang('save'); ?></button>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>

<script type="text/javascript">
	$(document).ready(function () {
        "use strict"
		
		$("#permissions-form").appForm({
			isModal: false,
			onSuccess: function (result) {
				appAlert.success(result.message, {duration: 10000});
			}
		});

	});
</script>