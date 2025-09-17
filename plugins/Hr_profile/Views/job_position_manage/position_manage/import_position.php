<?php 
$file_header = array();
$file_header[] = app_lang('hr_position_code');
$file_header[] = app_lang('hr_position_name');
$file_header[] = app_lang('hr_job_p_id');
$file_header[] = app_lang('hr_job_descriptions');
$file_header[] = app_lang('department_id');

?>
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card pr15 pl15">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_import_job_positions'); ?></h4>
					<div class="title-button-group">
						<div id ="dowload_file_sample">
						</div>
					</div>
				</div>


				<?php if(!isset($simulate)) { ?>
					<ul class="mt15">
						<li>1. <?php echo app_lang('hr_import_excel_1'); ?></li>
						<li class="text-danger">2. <?php echo app_lang('hr_import_xlsx_required'); ?></li>
						<li>3. <?php echo app_lang('hr_import_job_position_code'); ?></li>
						<li><a href="<?php echo get_uri('hr_profile/job_position_manage'); ?>" target="_blank">4. <?php echo app_lang('hr_import_job_position_group'); ?></a></li>
						<li><a href="<?php echo get_uri('hr_profile/organizational_chart'); ?>" target="_blank">5. <?php echo app_lang('hr_import_job_position_department_ids'); ?></a></li>
					</ul>

					<div class="table-responsive no-dt">
						<table class="table table-hover table-bordered">
							<thead>
								<tr>
									<?php
									$total_fields = 0;

									for($i=0;$i<count($file_header);$i++){
										if($i == 1 ||$i == 22){
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

						<?php echo form_open_multipart(get_uri("hr_profile/import_job_p_excel"), array("id" => "import_form", "class" => "general-form", "role" => "form")); ?>

						<?php echo form_hidden('leads_import','true'); ?>
						<?php echo render_input1('file_csv','choose_excel_file','','file', [], [], '', '', true ); ?>

						<div class="form-group">
							<a href="<?php echo get_uri('hr_profile/job_positions'); ?>" class="btn btn-default pull-left display-block mr-5 button-margin-r-b" title="<?php echo app_lang('close') ?> "><?php echo app_lang('close'); ?>
						</a>
						<button id="uploadfile" type="button" class="btn btn-primary text-white import" onclick="return uploadfilecsv();" ><?php echo app_lang('hr_job_p_import_excel'); ?></button>

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
<?php require('plugins/Hr_profile/assets/js/job_position/position/import_excel_js.php'); ?>

</body>
</html>