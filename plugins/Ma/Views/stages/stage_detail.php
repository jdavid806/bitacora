<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('timezone', get_setting('timezone')); ?>
                      <?php echo form_hidden('stage_id',$stage->id); ?>
                      <table class="table table-striped no-margin">
                        <tbody>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span style="color: <?php echo html_entity_decode($stage->color); ?>"><?php echo html_entity_decode($stage->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($stage->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('ma_weight'); ?></td>
                              <td><?php echo html_entity_decode($stage->weight) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($stage->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($stage->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                      <table class="table table-striped no-margin">
                        <tbody>
                          <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _d($stage->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($stage->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($stage->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                </div>
                  <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">

                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>

                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$stage->id.'/stage'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
                  </ul>

                  <div class="tab-content mt-3">
                    <div role="tabpanel" class="tab-pane fade" id="statistics">
                      <div class="row">
                        <div class="col-lg-4 col-xs-12 col-md-12 total-column mb-3">
                          <div class="panel_s">
                             <div class="panel-body">
                                <h3 class="text-muted _total">
                                   <?php echo count($lead_by_stage); ?>
                                </h3>
                                <span class="text-warning"><?php echo _l('total_number_of_lead'); ?></span>
                             </div>
                          </div>
                       </div>
                       <div class="col-lg-4 col-xs-12 col-md-12 total-column mb-3">
                          <div class="panel_s">
                             <div class="panel-body">
                                <h3 class="text-muted _total">
                                   <?php echo html_entity_decode($campaign_by_stage['campaigns']); ?>
                                </h3>
                                <span class="text-success"><?php echo _l('number_of_active_campaigns'); ?></span>
                             </div>
                          </div>
                       </div>
                       <div class="col-lg-4 col-xs-12 col-md-12 total-column mb-3">
                          <div class="panel_s">
                             <div class="panel-body">
                                <h3 class="text-muted _total">
                                   <?php echo html_entity_decode($campaign_by_stage['old_campaigns']); ?>
                                </h3>
                                <span class="text-default"><?php echo _l('number_of_campaigns_participated'); ?></span>
                             </div>
                          </div>
                       </div>
                        <div class="col-md-12 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_stage"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_stage_campaign"></div>
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
<?php require 'plugins/Ma/assets/js/stages/stage_detail_js.php';?>
