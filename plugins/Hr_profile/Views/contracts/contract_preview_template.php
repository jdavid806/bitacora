

<div id="page-content" class="page-wrapper clearfix">
	<div class="row">
		<div class="col-sm-12 col-lg-12">
			<div class="card">
				<div class="page-title clearfix">
					<h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo html_entity_decode($title); ?></h4>
					<div class="title-button-group">
						<?php if (hr_has_permission('hr_profile_can_edit_hr_contract') || is_admin()) { ?>
							<span class="dropdown inline-block mt10">
								<button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
									<i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
								</button>
								<ul class="dropdown-menu" role="menu">

									<?php if (hr_has_permission('hr_profile_can_edit_hr_contract') || is_admin()) { ?>
										<li role="presentation" class="dropdown-divider"></li>

										<li role="presentation"><a href="<?php echo site_url('hr_profile/contract/' . $contracts->id_contract ) ?>" class="dropdown-item"><span data-feather="edit" class="icon-16"></span> <?php echo app_lang('edit')  ?></a></li>

									<?php } ?>
								</ul>
							</span>
						<?php } ?>

					</div>
				</div>
				<div id="invoice-status-bar">
					<div class="bg-white  p15 no-border m0 rounded-bottom">

						<?php if($contracts->contract_status == 'draft' ){ ?>
							<span class="badge bg-warning large mt-0" > <?php echo app_lang('hr_hr_draft') ?> </span>
						<?php }elseif($contracts->contract_status == 'valid'){ ?>
							<span class="badge bg-success large mt-0"> <?php echo app_lang('hr_hr_valid') ?></span>
						<?php }elseif($contracts->contract_status == 'invalid'){ ?>
							<span class="badge bg-danger large mt-0"> <?php echo app_lang('hr_hr_expired') ?> </span>
						<?php }elseif($contracts->contract_status == 'finish'){ ?>
							<span class="badge bg-primary large mt-0"> <?php echo app_lang('hr_hr_finish') ?> </span>
						<?php }?>

						<span class="ml15"><?php echo app_lang("staff") ?>: <?php echo get_staff_full_name1($contracts->staff) ?></span> 
					</div>
				</div>

				<div class="card-header ">
					<ul class="nav nav-tabs pb15 justify-content-left border-bottom-0" id="myTab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="tab_items-tab" data-bs-toggle="tab" data-bs-target="#tab_items" type="button" role="tab" aria-controls="tab_items" aria-selected="true"><?php echo app_lang('hr_contract_information'); ?></button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="tab_receipt_delivery-tab" data-bs-toggle="tab" data-bs-target="#tab_receipt_delivery" type="button" role="tab" aria-controls="tab_receipt_delivery" aria-selected="false"><?php echo app_lang('hr_contract'); ?></button>
						</li>
					</ul>
				</div>

				<div class="card-body">
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="tab_items" role="tabpanel" aria-labelledby="tab_items-tab">
							


							<div class="modal-body card-body clearfix">
								<div class="col-md-12">
									<h6 class="h5-color"> <strong><?php echo app_lang('general_info') ?></strong></h6>
									<hr class="hr-color">
								</div>

								<div class="row" >
									<div class="col-md-6" >
										<table class="table border table-striped ">
											<tbody>
												<?php 
												$contract_code = (isset($contracts) ? $contracts->contract_code : ''); ?>

												<tr class="project-overview">
													<td class="bold" width="30%"><?php echo app_lang('hr_contract_code'); ?></td>
													<td class="text-right"><strong><?php echo html_entity_decode($contract_code); ?></strong></td>
												</tr>
												<tr class="project-overview">
													<td class="bold" width="30%"><?php echo app_lang('hr_name_contract'); ?></td>
													<?php foreach($contract_type as $c){
														if(isset($contracts) && $contracts->name_contract == $c['id_contracttype'] ){
															?>
															<td class="text-right"><strong><?php echo html_entity_decode($c['name_contracttype']); ?></strong></td>
														<?php }?>
													<?php }?>
												</tr>

											</tbody>
										</table>

									</div>

									<div class="col-md-6" >
										<table class="table table-striped">

											<tbody>
												<tr class="project-overview">
													<td class="bold" width="40%"><?php echo app_lang('staff'); ?></td>
													<td class="text-right">
														<a href="<?php echo get_uri('profile/'.$contracts->staff); ?>">
															<?php get_staff_image($contracts->staff, false); ?>
														</a><strong><?php echo get_staff_full_name1($contracts->staff); ?></strong></td>
													</tr>
													<tr class="project-overview">
														<?php $start_valid = (isset($contracts) ? $contracts->start_valid : '');
														?>
														<?php $end_valid = (isset($contracts) ? $contracts->end_valid : '');
														?>
														<td class="bold"><?php echo app_lang('hr_hr_time'); ?></td>
														<td class="text-right"><strong><?php echo format_to_date($start_valid, false) ." - ".format_to_date($end_valid, false); ?></strong></td>
													</tr>
													<tr class="project-overview">
														<td class="bold" width="30%"><?php echo app_lang('hr_hourly_rate_month'); ?></td>
														<td class="text-right"><strong><?php echo app_lang($contracts->hourly_or_month); ?></strong></td>
													</tr>
													<tr class="project-overview hide">
														<?php
														$contract_status = (isset($contracts) ? $contracts->contract_status : '');
														$_data='';
														?>
														<td class="bold"><?php echo app_lang('hr_status_label'); ?></td>
														<td class="text-right">
															<?php if($contract_status == 'draft' ){
																$_data .= ' <span class="label label-warning" > '.app_lang('hr_hr_draft').' </span>';
															}elseif($contract_status == 'valid'){
																$_data .= ' <span class="label label-success"> '.app_lang('hr_hr_valid').' </span>';
															}elseif($contract_status == 'invalid'){
																$_data .= ' <span class="label label-danger"> '.app_lang('hr_hr_expired').' </span>';
															}elseif($contract_status == 'finish'){
																$_data .= ' <span class="label label-primary"> '.app_lang('hr_hr_finish').' </span>';
															}

															echo html_entity_decode($_data);
															?>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>

									<div class="col-md-12">
										<h6 class="h5-color"><strong><?php echo app_lang('hr_wages_allowances') ?></strong></h6>
										<hr class="hr-color">
									</div>

									<div class="col-md-12">
										<table class="table border table-striped ">
											<thead>
												<th class="th-color"><?php echo app_lang('hr_hr_contract_rel_type'); ?></th>
												<th class="text-center th-color"><?php echo app_lang('hr_hr_contract_rel_value'); ?></th>
												<th class="th-color"><?php echo app_lang('hr_start_month'); ?></th>
												<th class="th-color"><?php echo app_lang('note'); ?></th>
											</thead>
											<tbody>
												<?php 
												$Hr_profile_model = model("Hr_profile\Models\Hr_profile_model");

												?>
												<?php foreach($contract_details as $contract_detail){ ?>
													<?php 
													$type_name ='';
													if(preg_match('/^st_/', $contract_detail['rel_type'])){
														$rel_value = str_replace('st_', '', $contract_detail['rel_type']);
														$salary_type = $Hr_profile_model->get_salary_form($rel_value);

														$type = 'salary';
														if($salary_type){
															$type_name = $salary_type->form_name;
														}

													}elseif(preg_match('/^al_/', $contract_detail['rel_type'])){
														$rel_value = str_replace('al_', '', $contract_detail['rel_type']);
														$allowance_type = $Hr_profile_model->get_allowance_type($rel_value);

														$type = 'allowance';
														if($allowance_type){
															$type_name = $allowance_type->type_name;
														}
													}
													?>
													<tr>
														<td><strong><?php echo html_entity_decode($type_name); ?></strong></td>
														<td class="text-right"><strong><?php echo to_decimal_format((float)$contract_detail['rel_value']); ?></strong></td>
														<td><strong><?php echo format_to_date($contract_detail['since_date'], false); ?></strong></td>
														<td><strong><?php echo html_entity_decode($contract_detail['contract_note']); ?></strong></td>

													</tr>
												<?php } ?>
											</tbody>
										</table>  
									</div>

									<div class="col-md-12">
										<h6 class="h5-color"><strong><?php echo app_lang('hr_sign_day') ?></strong></h6>
										<hr class="hr-color">
									</div>

									<div class="row" >
										<div class="col-md-6" >
											<table class="table border table-striped " >
												<tbody>
													<?php
													$sign_day = (isset($contracts) ? $contracts->sign_day : '');
													?>
													<tr class="project-overview">
														<td class="bold" width="30%"><?php echo app_lang('hr_sign_day'); ?></td>
														<td class="text-right"><strong><?php echo format_to_date($sign_day, false); ?></strong></td>
													</tr>
													<tr class="project-overview">
														<?php 
														if(isset($staff_delegate_role) && $staff_delegate_role != null){
															$staff_role = $staff_delegate_role->title ; }else{
																$staff_role = '';   
															} ?>

															<td class="bold" width="30%"><?php echo app_lang('hr_hr_job_position'); ?></td>
															<td class="text-right"><strong><?php echo html_entity_decode($staff_role); ?></strong></td>

														</tr>
													</tbody>
												</table>

											</div>

											<div class="col-md-6">
												<table class="table table-striped">

													<tbody>
														<tr class="project-overview">
															<td class="bold" width="40%"><?php echo app_lang('hr_staff_delegate'); ?></td>
															<?php foreach($staff as $s){ 
																if(isset($contracts) && $contracts->staff_delegate == $s['id'] ){
																	?>
																	<td class="text-right">
																		<a href="#">
																			<?php echo get_staff_image($s['id'], false); ?>
																		</a><?php echo html_entity_decode($s['first_name'].''.$s['last_name']); ?></td>
																	<?php }?>
																<?php }?>
															</tr>

														</tbody>
													</table>
												</div>
											</div>
											<div class="col-md-12">
												<div id="contract_attachments" class="mt-2">
													<?php
													$data = '<div class="row" id="attachment_file">';
													foreach($contract_attachment as $attachment) {
														$data .= '<div class="col-md-6">';

														$href_url = site_url('modules/hr_profile/uploads/contracts/'.$attachment['rel_id'].'/'.$attachment['file_name']).'" download';
														if(!empty($attachment['external'])){
															$href_url = $attachment['external_link'];
														}
														$data .= '<div class="col-md-12 mt-1 mb-1 row inline-block full-width att-background-color" >';
														$data .= '<div class="row" >';
														$data .= '<div class="col-md-1 mr-5">';
														$data .= modal_anchor(get_uri("hr_profile/hrm_file_contract/".$attachment['id']."/".$attachment['rel_id']), "<i data-feather='eye' class='icon-16'></i>", array("class" => "btn btn-success text-white mr5", "title" => $attachment['file_name'], "data-post-id" => $attachment['id']));

														
														$data .= '</a>';
														$data .= '</div>';
														$data .= '<div class=col-md-9>';
														$data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
														$data .= '<a href="'.$href_url.'>'.$attachment['file_name'].'</a>';
														$data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
														$data .= '</div>';
														$data .= '</div>';

														$data .= '<div class="clearfix"></div><hr/>';
														$data .= '</div>';
														$data .= '</div>';

													}
													$data .= '</div>';
													echo html_entity_decode($data);
													?>

												</div>
											</div>

										</div>
										<div id="contract_file_data"></div>

										<div class="card">
											<div class="container-fluid">
												<div class="">
													<div class="btn-bottom-toolbar text-right mb20 mt20">
														<a href="<?php echo get_uri('hr_profile/contracts'); ?>"  class="btn btn-default mr-2 "><span data-feather="x" class="icon-16" ></span> <?php echo app_lang('hr_close'); ?></a>
													</div>
												</div>
												<div class="btn-bottom-pusher"></div>
											</div>
										</div>


									</div>

									<div class="tab-pane fade" id="tab_receipt_delivery" role="tabpanel" aria-labelledby="tab_receipt_delivery-tab">
										<div class="row">

											<div class="col-md-12 text-right _buttons">

												<span class="dropdown inline-block mt10">
													<button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
														<i data-feather="align-justify" class="icon-16"></i> <?php echo app_lang('actions'); ?>
													</button>
													<ul class="dropdown-menu" role="menu">
														<li role="presentation"><a href="<?php echo get_uri('hr_profile/contract_sign/'.$contracts->id_contract); ?>" class="dropdown-item"><span data-feather="eye" class="icon-16"></span> <?php echo app_lang('view')  ?></a></li>

														<?php if(1 == 2){ ?>
															<li role="presentation"><?php echo anchor(get_uri("warehouse/download_goods_delivery_pdf/" . $contracts->id_contract), "<i data-feather='download' class='icon-16'></i> " . app_lang('download_pdf'), array("title" => app_lang('download_pdf'), "class" => "dropdown-item")); ?> </li>
															<li role="presentation"><?php echo anchor(get_uri("warehouse/download_goods_delivery_pdf/" . $contracts->id_contract . "/view"), "<i data-feather='file-text' class='icon-16'></i> " . app_lang('view_pdf'), array("title" => app_lang('view_pdf'), "target" => "_blank", "class" => "dropdown-item")); ?> </li>

															<li role="presentation"><?php echo js_anchor("<i data-feather='printer' class='icon-16'></i> " . app_lang('print_invoice'), array('title' => app_lang('print_invoice'), 'id' => 'print-invoice-btn', "class" => "dropdown-item")); ?> </li> 
														<?php } ?>
													</ul>
												</span>


											</div>
										</div>

										<?php echo form_open_multipart(get_uri("hr_profile/update_staff_contract_content"), array("id" => "contract-template-form", "class" => "general-form", "role" => "form")); ?>
										<?php echo form_hidden('id', $contracts->id_contract); ?>
										<?php echo html_entity_decode($sample_contract); ?>


										<div class="row mtop25">

											<div class="col-md-6  text-left">
												<?php if(!empty($contracts->staff_signature)) { ?>
													<p class="bold"><?php echo app_lang('staff_signature'); ?>

													<div class="bold">
														<?php 
														if(is_numeric($contracts->staff)){
															$contracts_staff_signer = get_staff_full_name1($contracts->staff);
														}else {
															$contracts_staff_signer = ' ';
														}

														?>
														<p class="no-mbot"><?php echo app_lang('contract_signed_by') . ": ".$contracts_staff_signer?></p>
														<p class="no-mbot"><?php echo app_lang('contract_signed_date') . ': ' . format_to_date($contracts->staff_sign_day, false) ?></p>
													</div>
													<p class="bold"><?php echo app_lang('hr_signature_text'); ?>

												</p>
												<div class="pull-left">
													<img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(HR_PROFILE_CONTRACT_SIGN.$contracts->id_contract.'/'.$contracts->staff_signature)); ?>" class="img-responsive" alt="">
												</div>
											<?php } ?> 
										</div>

										<div class="col-md-6  text-right">
											<?php if(!empty($contracts->signature)) { ?>
												<p class="bold"><?php echo app_lang('company_signature'); ?>

												<div class="bold">
													<?php 
													if(is_numeric($contracts->signer)){
														$contracts_signer = get_staff_full_name1($contracts->signer);
													}else {
														$contracts_signer = ' ';
													}

													?>
													<p class="no-mbot"><?php echo app_lang('contract_signed_by') . ": ".$contracts_signer?></p>
													<p class="no-mbot"><?php echo app_lang('contract_signed_date') . ': ' . format_to_date($contracts->sign_day, false) ?></p>
												</div>
												<p class="bold"><?php echo app_lang('hr_signature_text'); ?>
												<?php if($contracts->staff_delegate == get_staff_user_id() || $contracts->signer == get_staff_user_id() || hr_has_permission('hr_profile_can_delete_hr_contract')){ ?>
													<a href="<?php echo get_uri('hr_profile/hr_clear_signature/'.$contracts->id_contract); ?>" data-toggle="tooltip" title="<?php echo app_lang('clear_signature'); ?>" class="_delete text-danger">
														<i class="fa fa-remove"></i>
													</a>
												<?php } ?>
											</p>
											<div class="pull-right">
												<img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(HR_PROFILE_CONTRACT_SIGN.$contracts->id_contract.'/'.$contracts->signature)); ?>" class="img-responsive" alt="">
											</div>
										<?php } ?> 
									</div>

								</div>

								<div class="card">
									<div class="container-fluid">
										<div class="">
											<div class="btn-bottom-toolbar text-right mb20 mt20">
												<a href="<?php echo get_uri('hr_profile/contracts'); ?>"  class="btn btn-default mr-2 "><span data-feather="x" class="icon-16" ></span> <?php echo app_lang('hr_close'); ?></a>
												<?php if(hr_has_permission('hr_profile_can_create_hr_contract') || hr_has_permission('hr_profile_can_edit_hr_contract')){ ?>
													<button type="submit" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16" ></span> <?php echo app_lang('submit'); ?></button>
												<?php } ?>
											</div>
										</div>
										<div class="btn-bottom-pusher"></div>
									</div>
								</div>
								<?php echo form_close(); ?>


							</div>

						</div>
					</div>


				</div>
			</div>
		</div>
	</div>


	<?php require 'plugins/Hr_profile/assets/js/contracts/preview_contract_file_js.php';?>

</body>
</html>
<?php
load_css(array(
	"assets/js/summernote/summernote.css"
));
load_js(array(
	"assets/js/summernote/summernote.min.js"
));
?>

<script type="text/javascript">
	$(document).ready(function () {
		initWYSIWYGEditor("#content", {height: 480});
	});
</script>