<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                  <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
                  <hr class="hr-color">
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('timezone', get_setting('timezone')); ?>
                      <?php echo form_hidden('campaign_id',$campaign->id); ?>
                      <table class="table table-striped  no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span style="color: <?php echo html_entity_decode($campaign->color); ?>"><?php echo html_entity_decode($campaign->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($campaign->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($campaign->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($campaign->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('start_date'); ?></td>
                              <td><?php echo _d($campaign->start_date) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('end_date'); ?></td>
                              <td><?php echo _d($campaign->end_date) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                      <table class="table table-striped  no-margin">
                        <tbody>
                          <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _d($campaign->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($campaign->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($campaign->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                </div>

                  <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a role="presentation"  data-bs-toggle="tab" href="javascript:;" data-bs-target="#workflow" class="active show"><?php echo app_lang('preview'); ?></a></li>

                    <li><a role="presentation"  data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>
                    <li><a role="presentation"  data-bs-toggle="tab" href="javascript:;" data-bs-target="#actions"><?php echo app_lang('actions'); ?></a></li>

                    <li><a role="presentation"  data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$campaign->id.'/campaign'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
                    <li><a role="presentation"  data-bs-toggle="tab" href="<?php echo_uri('ma/clients/'.$campaign->id.'/campaign'); ?>" data-bs-target="#clients"><?php echo app_lang('clients'); ?></a></li>
                  </ul>

                  <div class="tab-content mt-3">
                    <div role="tabpanel" class="tab-pane fade active show" id="workflow">
                      <div class="wrapper">
                        <div class="col-md-12">
                          <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
                            <div class="btn-export" onclick="builder(); return false;"><?php echo _l('builder'); ?></div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="actions">
                      <div class="row mtop15">
                        <div class="col-md-4 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <h4><?php echo _l('point_actions'); ?></h4>
                              <hr>
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($point_actions as $action){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span><?php echo html_entity_decode($action->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($action->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <h4><?php echo _l('emails'); ?></h4>
                              <hr>
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($emails as $email){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($email->color); ?>"><?php echo html_entity_decode($email->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($email->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <h4><?php echo _l('segments'); ?></h4>
                              <hr>
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($segments as $segment){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($segment->color); ?>"><?php echo html_entity_decode($segment->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($segment->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-4 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <h4><?php echo _l('stages'); ?></h4>
                              <hr>
                              <table class="table table-striped  no-margin">
                                <tbody>
                                  <?php foreach($stages as $stage){ ?>
                                    <tr class="project-overview">
                                      <td width="30%"><span style="color: <?php echo html_entity_decode($stage->color); ?>"><?php echo html_entity_decode($stage->name); ?></span></td>
                                      <td class="text-right"><?php echo html_entity_decode($stage->total); ?></td>
                                   </tr>
                                  <?php } ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="statistics">
                      <div class="row">
                        <div class="col-md-6 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_email"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_text_message"></div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6 mb-3">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_point_action"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="leads"></div>
                    <div role="tabpanel" class="tab-pane fade" id="clients"></div>
                  </div>
      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/campaigns/campaign_detail_js.php';?>
<?php require 'plugins/Ma/assets/js/campaigns/workflow_builder_js.php';?>
