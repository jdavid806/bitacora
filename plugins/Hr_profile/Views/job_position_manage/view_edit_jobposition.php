
<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card pr15 pl15">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('hr_job_position_detail'); ?></h4>
					<div class="title-button-group">

					</div>
				</div>
				
				<div class="row pr15 pl15">
					<div class="row col-md-12">

						<div class="col-md-12 panel-padding">
							<table class="table border table-striped table-margintop">
								<tbody>
									<tr class="project-overview">
										<td class="bold" width="30%"><?php echo app_lang('hr_position_code'); ?></td>
										<td><?php echo html_entity_decode($job_position_general->position_code) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('hr_position_name'); ?></td>
										<td><?php echo html_entity_decode($job_position_general->position_name) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('hr_job_p_id'); ?></td>
										<td><?php echo html_entity_decode(get_job_name($job_position_general->job_p_id)) ; ?></td>
									</tr>
									<tr class="project-overview">
										<td class="bold"><?php echo app_lang('hr_department'); ?></td>
										<td><?php echo (get_department_from_strings($job_position_general->department_id, 3)) ; ?></td>
									</tr>
								</tbody>
							</table>
						</div>

						<br>
					</div>
				</div>

				<div class=" row pr15 pl15">
					<div class="col-md-12">
						<h4 class="h4-color"><?php echo app_lang('hr_hr_description'); ?></h4>
						<hr class="hr-color">
						<h6><?php echo html_entity_decode($job_position_general->job_position_description) ; ?></h6>
					</div>
				</div>

				<!-- file attachment -->
				<div class="row pr15 pl15">                           
					<div id="contract_attachments" class="mtop30 col-md-8 ">
						<?php if(isset($job_position_attachment)){ ?>
							<?php
							$data = '<div class="row" id="attachment_file">';
							foreach($job_position_attachment as $attachment) {
								$href_url = site_url('modules/hr_profile/uploads/job_position/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
								if(!empty($attachment['external'])){
									$href_url = $attachment['external_link'];
								}
								$data .= '<div class="display-block contract-attachment-wrapper">';
								$data .= '<div class="col-md-10">';
								$data .= '<div class="col-md-1 mr-5">';
								$data .= '<a name="preview-btn" onclick="preview_file_job_position(this); return false;" rel_id = "'.$attachment['rel_id'].'" id = "'.$attachment['id'].'" href="Javascript:void(0);" class="mbot10 btn btn-success pull-left" data-toggle="tooltip" title data-original-title="'.app_lang("preview_file").'">';
								$data .= '<i class="fa fa-eye"></i>'; 
								$data .= '</a>';
								$data .= '</div>';
								$data .= '<div class=col-md-9>';
								$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
								$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
								$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
								$data .= '</div>';
								$data .= '</div>';
								$data .= '<div class="col-md-2 text-right">';

								$data .= '</div>';
								$data .= '<div class="clearfix"></div><hr/>';
								$data .= '</div>';
							}
							$data .= '</div>';
							echo html_entity_decode($data);
							?>
						<?php } ?>                              
					</div>

				</div>

				<div class="row pr15 pl15">
					<div class="col-md-12">
						<div class="btn-bottom-toolbar text-right mb20">
							<a href="<?php echo get_uri('hr_profile/job_positions'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
<div id="contract_file_data"></div>
</body>
</html>



