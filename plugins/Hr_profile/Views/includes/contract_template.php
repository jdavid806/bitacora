<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "contract_templates";
			echo view("Hr_profile\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('contract_templates'); ?></h4>
					<div class="title-button-group">
						<?php if (hr_has_permission('hr_profile_can_create_setting') || is_admin() ) { ?>
							
							<a href="<?php echo get_uri('hr_profile/contract_template'); ?>" class="btn btn-info pull-left text-white"><span data-feather="plus-circle" class="icon-16"></span> <?php echo app_lang('new_contract_template'); ?></a>

						<?php } ?>
					</div>
				</div>

				<div class="table-responsive">
					<table id="contract_template-table" class="display" cellspacing="0" width="100%">            
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require 'plugins/Hr_profile/assets/js/setting/manage_contract_template_js.php';?>
</body>
</html>
