<?php 

$file_header = array();
$file_header[] = app_lang('employee_number');
$file_header[] = app_lang('employee_name');
$file_header[] = app_lang('job_title');
$file_header[] = app_lang('department_name');
$file_header[] = app_lang('income_tax_number');
$file_header[] = app_lang('residential_address');
$file_header[] = app_lang('income_rebate_code');
$file_header[] = app_lang('income_tax_rate');

?>
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card pr15 pl15">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hrp_import_employee'); ?></h4>
					<div class="title-button-group">
						<div class="row">
							<div class="col-md-6 mt-3">
								<?php echo render_input1('month_employees','',date('Y-m'), 'month'); ?>
							</div>
							<div class="col-md-6">
								<?php if(hrp_has_permission('hr_payroll_can_create_hrp_employee') || hrp_has_permission('hr_payroll_can_edit_hrp_employee')){ ?>
									<button id="export-file" onclick="dowload_contract_excel(); return false;" class="btn btn-warning btn-xs mleft5 staff_contract_create text-white" data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('create_attendance_file_download'); ?>"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('create_attendance_file_download') ?></button>
								<?php } ?>

								<a href="#" id="dowload-file" class="btn btn-success btn-xs mleft5 staff_contract_download hide " data-toggle="tooltip" title="" data-original-title="<?php echo app_lang('download_sample'); ?>"><span data-feather="download" class="icon-16"></span> <?php echo app_lang('download_sample'); ?></a>
							</div>
						</div>
					</div>
				</div>


				<?php if(!isset($simulate)) { ?>
					<ul>
						<li class="text-danger">1. <?php echo app_lang('file_xlsx_employees'); ?></li>
						<li class="text-danger">2. <?php echo app_lang('file_xlsx_employees2'); ?></li>
						<li class="text-danger">3. <?php echo app_lang('file_xlsx_employees3'); ?></li>
					</ul>
					<div class="table-responsive no-dt">
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<?php
									$total_fields = 0;

									for($i=0;$i<count($file_header);$i++){
										if($i == -1){
											?>
											<th class="bold"><span class="text-danger">*</span> <?php echo html_entity_decode($file_header[$i]) ?> </th>
											<?php 
										} else {
											?>
											<th class="bold"><?php echo html_entity_decode($file_header[$i]) ?> </th>

											<?php

										} 
										$total_fields++;
									}

									?>
								</tr>
							</thead>
							<tbody>
								<?php for($i = 0; $i<1;$i++){
									echo '<tr>';
									for($x = 0; $x<count($file_header);$x++){
										echo '<td>- </td>';
									}
									echo '</tr>';
								}
								?>
							</tbody>
						</table>
					</div>

				<?php } ?>


				<div class="row">
					<div class="col-md-4">
						<?php echo form_open_multipart(get_uri("hr_payroll/import_job_p_excel"), array("id" => "import_form", "class" => "general-form", "role" => "form")); ?>
						<?php echo form_hidden('leads_import','true'); ?>
						<?php echo render_input1('file_csv','choose_excel_file','','file', [], [], '', '', true ); ?> 

						<div class="form-group">
							<a href="<?php echo get_uri('hr_payroll/manage_employees'); ?>" class="btn btn-default pull-left display-block mr-5 button-margin-r-b" title="<?php echo app_lang('close') ?> "><span data-feather="x" class="icon-16"></span> <?php echo app_lang('hrp_back'); ?>
						</a>
						<?php if(hrp_has_permission('hr_payroll_can_create_hrp_employee') || hrp_has_permission('hr_payroll_can_edit_hrp_employee')){ ?>
							<button id="uploadfile" type="button" class="btn btn-primary text-white import" onclick="return uploadfilecsv(this);" ><?php echo app_lang('hrp_import_excel'); ?></button>
						<?php } ?>

					</div>
					<?php echo form_close(); ?>

				</div>
				<div class="col-md-8">
					<div class="form-group" id="file_upload_response">
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
</div>

<div id="box-loading">

</div>
<?php require('plugins/Hr_payroll/assets/js/manage_employees/import_employees_js.php'); ?>

</body>
</html>