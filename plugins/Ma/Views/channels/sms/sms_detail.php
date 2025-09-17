<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                  <h4 class="customer-profile-group-heading"><?php echo _l('sms'); ?></h4>
                  <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
                  <hr class="hr-color">
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('sms_id', $sms->id); ?>
                      <table class="table table-striped  no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span><?php echo html_entity_decode($sms->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($sms->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('sms_template'); ?></td>
                              <td><?php echo ma_get_text_message_name($sms->sms_template); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($sms->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($sms->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('language'); ?></td>
                              <td><?php echo html_entity_decode($sms->language); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _d($sms->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($sms->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview ">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($sms->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6 general-form">
                     <?php echo render_textarea('content', 'content', html_entity_decode($sms->content), array('readonly' => true)); ?>
                     
                  </div>
                </div>
                  <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">

                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>

                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$sms->id.'/sms'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
                </ul>

                  <div class="tab-content mt-3">
                    <div role="tabpanel" class="tab-pane fade" id="statistics">
                     <div class="row">
                      <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo count($lead_by_sms); ?>
                              </h3>
                              <span class="text-warning"><?php echo _l('total_number_of_lead'); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo html_entity_decode($campaign_by_sms['campaigns']); ?>
                              </h3>
                              <span class="text-success"><?php echo _l('number_of_active_campaigns'); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo html_entity_decode($campaign_by_sms['old_campaigns']); ?>
                              </h3>
                              <span class="text-default"><?php echo _l('number_of_campaigns_participated'); ?></span>
                           </div>
                        </div>
                     </div>
                   </div>
                     <div class="row">
                        <div class="col-md-12 mt-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_chart"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12 mt-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_campaign_chart" class="container_campaign"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="leads"></div>
                    
                  </div>


      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/channels/sms_detail_js.php';?>
