<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "contract_types";
			echo view("Hr_profile\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('contract_types'); ?></h4>
					<div class="title-button-group">
						<?php if (hr_has_permission('hr_profile_can_create_setting') || is_admin() ) { ?>
							
							<?php echo modal_anchor(get_uri("hr_profile/contract_type_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('hr_new_contract_type'), array("class" => "btn btn-info text-white", "title" => app_lang('hr_new_contract_type'))); ?>
						<?php } ?>
					</div>
				</div>

				<div class="table-responsive">
					<table id="commodity_type-table" class="display" cellspacing="0" width="100%">            
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php require 'plugins/Hr_profile/assets/js/setting/contract_type_js.php';?>
</body>
</html>

