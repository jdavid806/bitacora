<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-3 col-lg-2">
			<?php
			$tab_view['active_tab'] = "pdf_payslip_templates";
			echo view("Hr_payroll\Views\includes/tabs", $tab_view);
			?>
		</div>

		<div class="col-sm-9 col-lg-10">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('pdf_payslip_templates'); ?></h4>

					<div class="title-button-group">

						<?php if(is_admin() || hrp_has_permission('hr_payroll_can_create_hrp_setting')) {?>
							<a href="<?php echo get_uri('hr_payroll/payslip_pdf_template'); ?>" class="btn btn-info text-white" ><span data-feather="plus-circle" class="icon-16" ></span> 
								<?php echo app_lang('new_pdf_payslip_template'); ?>
							</a>
						<?php } ?>
					</div>
				</div>

				<div class="table-responsive pt15 pl15 pr15">
					<table id="dtBasicExample" class="table  ">
						<thead>
							<tr>
								<th><?php echo app_lang('name'); ?></th>
								<th><?php echo app_lang('payslip_template'); ?></th>
								<th><?php echo app_lang('actions'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($pdf_payslip_templates as $pdf_payslip_template){ ?>
								<?php 
								$names = get_payslip_template_name($pdf_payslip_template['payslip_template_id']);
								?>
								<tr>
									<td><?php echo html_entity_decode($pdf_payslip_template['name']); ?></td>
									<td><?php echo html_entity_decode($names); ?></td>

									<td class=" text-center option w100 ">

										<?php if(is_admin() || hrp_has_permission('hr_payroll_can_edit_hrp_setting')) {?>
											<a href="<?php echo get_uri('hr_payroll/payslip_pdf_template/'.$pdf_payslip_template['id']); ?>" class="btn btn-default btn-icon" data-toggle="sidebar-right" data-target=".insurance_type_modal-edit-modal"><span data-feather="edit" class="icon-16" ></span> </a>
										<?php } ?>

										<?php if(is_admin() || hrp_has_permission('hr_payroll_can_delete_hrp_setting')) {?>
												<?php 
												echo modal_anchor(get_uri("hr_payroll/confirm_delete_modal_form"), "<span data-feather='x' class='icon-16' ></span>", array("title" => app_lang('delete'). "?", "data-post-id" => $pdf_payslip_template['id'], "data-post-function" => 'delete_payslip_pdf_template', "class" => "delete" ));
												 ?>

										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table> 
				</div>

			</div>
		</div>
	</div>
</div>
<?php require 'plugins/Hr_payroll/assets/js/pdf_payslip_templates/pdf_payslip_template_manage_js.php';?>

</body>
</html>