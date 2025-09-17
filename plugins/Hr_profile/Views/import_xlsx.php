<?php 

$file_header[] = app_lang('id');
$file_header[] = app_lang('hr_staff_code');
$file_header[] = app_lang('hr_firstname');
$file_header[] = app_lang('hr_lastname');
$file_header[] = app_lang('hr_sex');
$file_header[] = app_lang('hr_hr_birthday');
$file_header[] = app_lang('email');
$file_header[] = app_lang('phone');
$file_header[] = app_lang('hr_hr_workplace');
$file_header[] = app_lang('hr_status_work');
$file_header[] = app_lang('hr_hr_job_position');
$file_header[] = app_lang('hr_team_manage'); 
$file_header[] = app_lang('role'); 
$file_header[] = app_lang('hr_hr_literacy'); 
$file_header[] = app_lang('staff_hourly_rate'); 
$file_header[] = app_lang('team'); 
$file_header[] = app_lang('password'); 
$file_header[] = app_lang('hr_hr_home_town');
$file_header[] = app_lang('hr_hr_marital_status'); 
$file_header[] = app_lang('hr_current_address'); 
$file_header[] = app_lang('hr_hr_nation'); 
$file_header[] = app_lang('hr_hr_birthplace'); 
$file_header[] = app_lang('hr_hr_religion'); 
$file_header[] = app_lang('hr_citizen_identification'); 
$file_header[] = app_lang('hr_license_date'); 
$file_header[] = app_lang('hr_hr_place_of_issue'); 
$file_header[] = app_lang('hr_hr_resident'); 
$file_header[] = app_lang('hr_bank_account_number'); 
$file_header[] = app_lang('hr_bank_account_name'); 
$file_header[] = app_lang('hr_bank_name'); 
$file_header[] = app_lang('hr_Personal_tax_code'); 

?>
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card pr15 pl15">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_import_staff'); ?></h4>
					<div class="title-button-group">
						<div id ="dowload_file_sample">

							<a href="#" onclick="staff_export_item(); return false;"  class="mright5 btn btn-warning text-white hr_export_staff"><span data-feather="check-circle" class="icon-16" ></span> 
								<?php echo app_lang('hr_create_sample_file'); ?>
							</a>

							<a href="#" id="dowload_items"  class="btn btn-success pull-left  mr-4 button-margin-r-b hide"><span data-feather="download" class="icon-16"></span> <?php echo app_lang('dowload_sample_file'); ?></a>
						</div>
					</div>
				</div>


				<?php if(!isset($simulate)) { ?>
					<ul class="mt15">
						<li class="text-danger">1. <?php echo app_lang('file_xlsx_staff1'); ?></li>
						<li>2. <?php echo app_lang('file_xlsx_staff2'); ?></li>
						<li>3. <?php echo app_lang('file_xlsx_staff21'); ?></li>

						<li><a href="<?php echo get_uri('hr_profile/workplaces'); ?>" target="_blank" >4. <?php echo app_lang('file_xlsx_staff3'); ?></a></li>
						<li><a href="<?php echo get_uri('hr_profile/job_positions'); ?>" target="_blank"> 5. <?php echo app_lang('file_xlsx_staff4'); ?></a></li>
						<li><a href="<?php echo get_uri('hr_profile/staff_infor'); ?>" target="_blank"> 6. <?php echo app_lang('file_xlsx_staff5'); ?></a></li>
						<li><a href="<?php echo get_uri('roles'); ?>" target="_blank"> 7. <?php echo app_lang('file_xlsx_staff6'); ?></a></li>
						<li>8. <?php echo app_lang('file_xlsx_staff7'); ?></li>
						<li><a href="<?php echo get_uri('hr_profile/organizational_chart'); ?>" target="_blank"> 9. <?php echo app_lang('file_xlsx_staff8'); ?></a></li>
						<li>10. <?php echo app_lang('file_xlsx_staff9'); ?></li>
						<li>11. <?php echo app_lang('file_xlsx_staff10'); ?></li>
						<li>12. <?php echo app_lang('file_xlsx_staff11'); ?></li>
					</ul>
					<div class="table-responsive no-dt">
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<?php
									$total_fields = 0;

									for($i=0;$i<count($file_header);$i++){
										if( $i == 2 ||$i == 3 ||$i == 6 ||$i == 9 || $i == 10){
											?>
											<th class="bold"><span class="text-danger">*</span> <?php echo html_entity_decode($file_header[$i]); ?> </th>
											<?php 
										} else {
											?>
											<th class="bold"><?php echo html_entity_decode($file_header[$i]); ?> </th>

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
						<?php echo form_open_multipart(get_uri("hr_profile/importxlsx2"), array("id" => "import_form", "class" => "general-form", "role" => "form")); ?>
						<?php echo form_hidden('leads_import','true'); ?>
						<?php echo render_input1('file_csv','choose_excel_file','','file', [], [], '', '', true ); ?> 

						<div class="form-group">
							<a href="<?php echo get_uri('hr_profile/staff_infor'); ?>" class="btn btn-default pull-left display-block mr-5 button-margin-r-b" title="<?php echo app_lang('close') ?> "><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?>
						</a>
						<button id="uploadfile" type="button" class="btn btn-primary text-white import" onclick="return uploadfilecsv(this);" ><?php echo app_lang('hr_import_xlsx_hr_profile'); ?></button>
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

<!-- box loading -->
<div id="box-loading">

</div>
<?php require('plugins/Hr_profile/assets/js/hr_record/importxlsx_js.php'); ?>

</body>
</html>