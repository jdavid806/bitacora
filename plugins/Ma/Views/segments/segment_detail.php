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
                      <?php echo form_hidden('segment_id',$segment->id); ?>
                      <table class="table table-striped no-margin">
                        <tbody>
                            <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('name'); ?></td>
                              <td><span style="color: <?php echo html_entity_decode($segment->color); ?>"><?php echo html_entity_decode($segment->name); ?></span></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('category'); ?></td>
                              <td><?php echo ma_get_category_name($segment->category); ?></td>
                           </tr>
                           <tr class="project-overview">
                              <?php $value = (($segment->published == 1) ? _l('yes') : _l('no')); ?>
                              <?php $text_class = (($segment->published == 1) ? 'text-success' : 'text-danger'); ?>
                              <td class="bold"><?php echo _l('published'); ?></td>
                              <td class="<?php echo html_entity_decode($text_class) ; ?>"><?php echo html_entity_decode($value) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                  <div class="col-md-6">
                      <table class="table table-striped  no-margin">
                        <tbody>
                          <tr class="project-overview">
                              <td class="bold" width="30%"><?php echo _l('datecreator'); ?></td>
                              <td><?php echo _d($segment->dateadded) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('addedfrom'); ?></td>
                              <td><?php echo get_staff_full_name($segment->addedfrom) ; ?></td>
                           </tr>
                           <tr class="project-overview">
                              <td class="bold"><?php echo _l('description'); ?></td>
                              <td><?php echo html_entity_decode($segment->description) ; ?></td>
                           </tr>
                          </tbody>
                    </table>
                  </div>
                </div>
                <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">

                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#statistics"><?php echo app_lang('statistics'); ?></a></li>

                    <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri('ma/leads/'.$segment->id.'/segment'); ?>" data-bs-target="#leads"><?php echo app_lang('leads'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#filters"><?php echo app_lang('filters'); ?></a></li>
                </ul>
                  <div class="tab-content mt-3">
                    
                    <div role="tabpanel" class="tab-pane fade" id="leads"></div>

                    <div role="tabpanel" class="tab-pane fade" id="statistics">
                      <div class="row mb-3">
                      <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo count($lead_by_segment); ?>
                              </h3>
                              <span class="text-warning"><?php echo _l('total_number_of_lead'); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo html_entity_decode($campaign_by_segment['campaigns']); ?>
                              </h3>
                              <span class="text-success"><?php echo _l('number_of_active_campaigns'); ?></span>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-4 col-xs-12 col-md-12 total-column">
                        <div class="panel_s">
                           <div class="panel-body">
                              <h3 class="text-muted _total">
                                 <?php echo html_entity_decode($campaign_by_segment['old_campaigns']); ?>
                              </h3>
                              <span class="text-default"><?php echo _l('number_of_campaigns_participated'); ?></span>
                           </div>
                        </div>
                     </div>
                     </div>
                      <div class="row mb-3">
                        <div class="col-md-12">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_segment"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="panel_s">
                            <div class="panel-body">
                              <div id="container_segment_campaign"></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade general-form" id="filters">
                    <?php $types = [
                      2 => ['id' => 'specialty', 'name' => _l('specialty')],
                      3 => ['id' => 'country', 'name' => _l('country')],
                      4 => ['id' => 'users_number', 'name' => _l('users_number')],
                      7 => ['id' => 'city', 'name' => _l('city')],
                      8 => ['id' => 'state', 'name' => _l('state')],
                    ]; ?>
                      <?php $follow_1 = [ 
                        1 => ['id' => 'and', 'name' => _l('ma_and')],
                        2 => ['id' => 'or', 'name' => _l('ma_or')],
                      ]; ?>

                      <?php $follow_2 = [
                        1 => ['id' => 'equals', 'name' => _l('equals')],
                        2 => ['id' => 'not_equal', 'name' => _l('not_equal')],
                        3 => ['id' => 'greater_than', 'name' => _l('greater_than')],
                        4 => ['id' => 'greater_than_or_equal', 'name' => _l('greater_than_or_equal')],
                        5 => ['id' => 'less_than', 'name' => _l('less_than')],
                        6 => ['id' => 'less_than_or_equal', 'name' => _l('less_than_or_equal')],
                        11 => ['id' => 'between', 'name' => _l('between')],
                        12 => ['id' => 'empty', 'name' => _l('empty')],
                        13 => ['id' => 'not_empty', 'name' => _l('not_empty')],
                        14 => ['id' => 'like', 'name' => _l('like')],
                        15 => ['id' => 'not_like', 'name' => _l('not_like')],
                      ]; ?>
                      <?php foreach($segment->filters as $key => $filter){ ?>
                <div id="item_approve" class="border mtop10 p-3">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="select-placeholder form-group">
                          <label for="sub_type_1[<?php echo html_entity_decode($key); ?>]"></label>
                          <select name="sub_type_1[<?php echo html_entity_decode($key); ?>]" id="sub_type_1[<?php echo html_entity_decode($key); ?>]" data-index="<?php echo html_entity_decode($key); ?>" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true" disabled>
                            <?php foreach($follow_1 as $val){
                                $selected = '';
                             if($val['id'] == $filter['sub_type_1']){
                                $selected = 'selected';
                              }
                              ?>
                              <option value="<?php echo html_entity_decode($val['id']); ?>" <?php echo html_entity_decode($selected); ?>>
                               <?php echo html_entity_decode($val['name']); ?>
                             </option>
                           <?php } ?>
                         </select>
                       </div> 
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="select-placeholder form-group">
                          <label for="type[<?php echo html_entity_decode($key); ?>]"></label>
                          <select name="type[<?php echo html_entity_decode($key); ?>]" id="type[<?php echo html_entity_decode($key); ?>]" data-index="<?php echo html_entity_decode($key); ?>" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true" disabled>
                            <?php foreach($types as $val){
                                $selected = '';
                             if($val['id'] == $filter['type']){
                                $selected = 'selected';
                              }
                              ?>
                              <option value="<?php echo html_entity_decode($val['id']); ?>" <?php echo html_entity_decode($selected); ?>>
                               <?php echo html_entity_decode($val['name']); ?>
                             </option>
                           <?php } ?>
                         </select>
                       </div> 
                    </div>
                   <div class="col-md-3">                            
                      <div class="select-placeholder form-group">
                        <label for="sub_type_2[<?php echo html_entity_decode($key); ?>]"></label>
                        <select name="sub_type_2[<?php echo html_entity_decode($key); ?>]" id="sub_type_2[<?php echo html_entity_decode($key); ?>]" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true" disabled>
                          <?php foreach($follow_2 as $val){
                                $selected = '';
                           if($val['id'] == $filter['sub_type_2']){
                            $selected = 'selected';
                          }
                          ?>
                          <option value="<?php echo html_entity_decode($val['id']); ?>" <?php echo html_entity_decode($selected); ?>>
                             <?php echo html_entity_decode($val['name']); ?>
                           </option>
                         <?php } ?>
                        </select>
                      </div> 
                   </div>
                   <div class="col-md-3">                            
                      <div class="form-group" app-field-wrapper="name">
                        <label for="value[<?php echo html_entity_decode($key); ?>]" class="control-label"></label>
                        <input type="value" id="value[<?php echo html_entity_decode($key); ?>]" name="value[<?php echo html_entity_decode($key); ?>]" class="form-control" value="<?php echo html_entity_decode($filter['value']); ?>" disabled>
                      </div>
                   </div>
                  
                </div>
                </div>
              <?php } ?>
                    </div>
                  </div>


      </div>
     
   </div>
</div>

<?php require 'plugins/Ma/assets/js/segments/segment_detail_js.php';?>
