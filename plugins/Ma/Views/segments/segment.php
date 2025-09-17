<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                  <div>
                     <div class="tab-content">
  <?php $id = (isset($segment) ? $segment->id : ''); ?>
<?php echo form_open(get_uri('ma/segment/'.$id),array('class'=>'segment-form','autocomplete'=>'off', 'class' => 'general-form')); ?>
<div class="row">
<div class="additional"></div>
<div class="col-md-12">
    <ul data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
      <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#segment_details"><?php echo app_lang('details'); ?></a></li>

      <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#segment_filters"><?php echo app_lang('filters'); ?></a></li>
    </ul>
   <div class="tab-content mt-3 mb-3">
      <div role="tabpanel" class="tab-pane fade" id="segment_details">
         <div class="row">
            <div class="col-md-12">
              <?php $name = ( isset($segment) ? $segment->name : '');
                  echo render_input('name','name',$name,'text', array('required' => true)); ?>
            </div>
            <!-- <div class="col-md-12">
              <?php $value = ( isset($segment) ? $segment->category : ''); ?>
              <?php echo render_select('category',$categories,array('id','name'), 'category', $value, array('required' => true)); ?>
            </div> -->

            <div class="col-md-12">
            <?php $value = (isset($segment) ? $segment->color : ''); ?>
              <?php echo render_color_picker('color',_l('color'),$value); ?>
            </div>
            <div class="form-group col-md-12">
              <?php
                $selected = (isset($segment) ? $segment->published : ''); 
                ?>
              <label for="published"><?php echo _l('published'); ?></label><br />
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="published" id="published_yes" value="1" <?php if($selected == '1'|| $selected == ''){echo 'checked';} ?> class="form-check-input">
                <label for="published_yes"><?php echo _l("yes"); ?></label>
              </div>
              <div class="radio radio-inline radio-primary">
                <input type="radio" name="published" id="published_no" value="0" <?php if($selected == '0'){echo 'checked';} ?> class="form-check-input">
                <label for="published_no"><?php echo _l("no"); ?></label>
              </div>
            </div>
            <div class="col-md-12">
              <?php
                $description = (isset($segment) ? $segment->description : ''); 
                ?>
              <p class="bold"><?php echo _l('description'); ?></p>
              <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
            </div>
         </div>
      </div>
      <?php 
        $types = [];
      ?>
      <div role="tabpanel" class="tab-pane fade" id="segment_filters">
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
            <div class="list_approve">
              <?php if(isset($segment)){ ?>

              <?php foreach($segment->filters as $key => $filter){ ?>
                <div id="item_approve" class="border mtop10 p-3">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="select-placeholder form-group">
                          <label for="sub_type_1[<?php echo html_entity_decode($key); ?>]"></label>
                          <select name="sub_type_1[<?php echo html_entity_decode($key); ?>]" id="sub_type_1[<?php echo html_entity_decode($key); ?>]" data-index="<?php echo html_entity_decode($key); ?>" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
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
                          <select name="type[<?php echo html_entity_decode($key); ?>]" id="type[<?php echo html_entity_decode($key); ?>]" data-index="<?php echo html_entity_decode($key); ?>" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
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
                        <select name="sub_type_2[<?php echo html_entity_decode($key); ?>]" id="sub_type_2[<?php echo html_entity_decode($key); ?>]" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
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
                        <input type="value" id="value[<?php echo html_entity_decode($key); ?>]" name="value[<?php echo html_entity_decode($key); ?>]" class="form-control" value="<?php echo html_entity_decode($filter['value']); ?>">
                      </div>
                   </div>
                   <div class="col-md-1">
                    <button name="add_template" class="btn <?php if($key == 0){ echo 'new_vendor_requests btn-success'; }else{ echo 'remove_vendor_requests btn-danger';} ?> mt-4" data-ticket="true" type="button"><i data-feather="<?php if($key == 0){ echo 'plus'; }else{ echo 'minus';} ?>" class="icon-16"></i></button>
                  </div>
                </div>
                </div>
              <?php } ?>
              <?php }else{ ?>
                <div id="item_approve" class="border mtop10 p-3">
                  <div class="row">
                    <div class="col-md-3">
                      <div class="select-placeholder form-group">
                          <label for="sub_type_1[0]"></label>
                          <select name="sub_type_1[0]" id="sub_type_1[0]" data-index="0" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                            <?php foreach($follow_1 as $val){
                             $selected = '';
                             ?>
                             <option value="<?php echo html_entity_decode($val['id']); ?>">
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
                          <label for="type[0]"></label>
                          <select name="type[0]" id="type[0]" data-index="0" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                            <?php foreach($types as $val){
                             $selected = '';
                             ?>
                             <option value="<?php echo html_entity_decode($val['id']); ?>">
                               <?php echo html_entity_decode($val['name']); ?>
                             </option>
                           <?php } ?>
                         </select>
                       </div> 
                    </div>
                   <div class="col-md-3" id="div_subtype_0">                            
                      <div class="select-placeholder form-group">
                        <label for="sub_type_2[0]"></label>
                        <select name="sub_type_2[0]" id="sub_type_2[0]" class="select2" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-hide-disabled="true" data-live-search="true">
                          <?php foreach($follow_2 as $val){
                           $selected = '';
                           ?>
                           <option value="<?php echo html_entity_decode($val['id']); ?>">
                             <?php echo html_entity_decode($val['name']); ?>
                           </option>
                         <?php } ?>
                        </select>
                      </div> 
                   </div>
                   <div class="col-md-3">                            
                      <div class="form-group" app-field-wrapper="name">
                        <label for="value[0]" class="control-label"></label>
                        <input type="value" id="value[0]" name="value[0]" class="form-control" value="">
                      </div>
                   </div>
                   <div class="col-md-1">
                     <span class="input-group-btn">
                      <button name="add_template" class="btn new_vendor_requests btn-success mt-4" data-ticket="true" type="button"><i data-feather="plus" class="icon-16"></i></i></button>
                    </span>
                  </div>
                </div>
                </div>
              <?php } ?>
            </div>
                  </div>
               </div>
            </div>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/segments'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
            </div>
            </div>
            <?php echo form_close(); ?>
           </div>
        </div>
      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/segments/segment_js.php';?>
