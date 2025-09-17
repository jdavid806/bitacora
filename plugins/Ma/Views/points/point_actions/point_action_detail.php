<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                <h4 class="customer-profile-group-heading"><?php echo _l('point_action'); ?></h4>
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo form_hidden('point_action_id', $point_action->id); ?>
                      <table class="table table-striped table-margintop">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><?php echo html_entity_decode($point_action->name) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($point_action->category) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($point_action->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($point_action->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = $point_action->change_points; ?>
                              <?php $text_class = (($point_action->change_points >= 0) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('change_points'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('when_a_contact'); ?></td>
                              <td><?php echo _l($point_action->action) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                    <table class="table table-striped table-margintop">
                        <tbody>
                            
                           <tr class="project-overview">
                              <?php $value = $point_action->change_points; ?>
                              <?php $text_class = (($point_action->change_points >= 0) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold" width="30%"><?php echo _l('change_points'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                          </tr>
                          <tr class="project-overview">
                              <?php $value = (($point_action->add_points_by_country == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($point_action->add_points_by_country == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('add_points_by_country'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                    </table>
                    <?php if ($point_action->add_points_by_country == 1) { ?>
                        <table class="table items">
                            <thead>
                              <tr class="project-overview">
                                  <th class="text-center bold"><?php echo _l('country'); ?></th>
                                  <th class="text-center bold"><?php echo _l('change_points'); ?></th>
                               </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($point_action->change_point_details as $key => $value) { 
                                ?>
                                <tr class="project-overview">
                                  <td class="" width="30%"><?php echo html_entity_decode($value['country']); ?></td>
                                  <?php $point = $value['change_points']; ?>
                                  <?php $text_class = (($value['change_points'] >= 0) ? 'text-success' : 'text-danger'); ?>
                                  <td class="text-center <?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($point) ; ?></td>
                               </tr>
                            <?php } ?>
                        </table>
                    <?php } ?>
                  </div>
                </div>
                <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$point_action->id.'/point_action'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
                </ul>
                <div class="tab-content mt-3">
                    <div role="tabpanel" class="tab-pane fade" id="statistics">
                      <div id="container_chart"></div>
                      <div id="container_campaign_chart" class="mt-3"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="leads"></div>
                </div>
      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/points/point_action_detail_js.php';?>
