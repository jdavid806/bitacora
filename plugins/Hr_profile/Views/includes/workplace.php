<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "workplaces";
			echo view("Hr_profile\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('workplaces'); ?></h4>
					<div class="title-button-group">
						<?php if (hr_has_permission('hr_profile_can_create_setting') || is_admin() ) { ?>
							
							<?php echo modal_anchor(get_uri("hr_profile/workplace_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('hr_new_workplace'), array("class" => "btn btn-info text-white", "title" => app_lang('hr_new_workplace'))); ?>
						<?php } ?>
					</div>
				</div>

				<div class="table-responsive">
					<table id="workplace-table" class="display" cellspacing="0" width="100%">            
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require 'plugins/Hr_profile/assets/js/setting/workplace_js.php';?>
</body>
</html>
