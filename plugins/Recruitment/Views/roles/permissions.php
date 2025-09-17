<div class="tab-content">
	<?php echo form_open(get_uri("recruitment/role_save_permissions"), array("id" => "permissions-form", "class" => "general-form dashed-row", "role" => "form")); ?>
	<input type="hidden" name="id" value="<?php echo html_entity_decode($model_info->id); ?>" />
	<div class="card">
		<div class="card-header">
			<h4><?php echo app_lang('permissions') . ": " . html_entity_decode($model_info->title); ?></h4>
		</div>
		<div class="card-body">

			<ul class="permission-list">
				<?php echo  view('Recruitment\Views\includes\recruitment_permissions', $permissions); ?>

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
<?php require 'plugins/Recruitment/assets/js/roles/permissions_js.php';?>
