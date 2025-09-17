<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        
        <div class="card-body">
                  <h4 class="customer-profile-group-heading"><?php echo _l('text_message'); ?></h4>
                  <h4 class="h4-color"><?php echo _l('general_infor'); ?></h4>
                  <hr class="hr-color">
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('timezone', get_setting('timezone')); ?>
                      <?php echo form_hidden('text_message_id', $text_message->id); ?>
                      <table class="table table-striped  no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span><?php echo html_entity_decode($text_message->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($text_message->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($text_message->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($text_message->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('language'); ?></td>
                              <td><?php echo html_entity_decode($text_message->language); ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                      <table class="table table-striped  no-margin">
                        <tbody>
                          <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _d($text_message->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($text_message->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('content'); ?></td>
                              <td><?php echo html_entity_decode($text_message->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                </div>
                <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>

                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$text_message->id.'/text_message'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
                </ul>
                  <div class="tab-content mt-3">
                    <div role="tabpanel" class="tab-pane fade" id="statistics">
                     <div class="row">
                        <div class="col-md-12">
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

<?php require 'plugins/Ma/assets/js/channels/text_message_detail_js.php'; ?>
