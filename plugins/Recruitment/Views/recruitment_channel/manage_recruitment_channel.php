<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">

						<?php if (re_has_permission("recruitment_can_create") || is_admin()) {?>
							<a href="<?php echo site_url('recruitment/add_edit_recruitment_channel') ?>" class="btn btn-info pull-left display-block text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('new'); ?></a>
						<?php }?>
					</div>
				</div>

				<div class="table-responsive">
					<?php
					$table_data = array(
						app_lang('id'),
						app_lang('r_form_name'),
						app_lang('responsible_person'),
						app_lang('form_type'),
						app_lang('status'),
						"<i data-feather='menu' class='icon-16'></i>",

					);
					render_datatable1($table_data,'table_recruitment_channel',
				); ?>

			</div>
		</div>
	</div>
</div>
</div>
<?php require 'plugins/Recruitment/assets/js/recruitment_channels/recruitment_channel_manage_js.php';?>
</body>
</html>