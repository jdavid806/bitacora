<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
                  <div>
                     <div class="tab-content">
        <?php $id = (isset($point_action) ? $point_action->id : ''); ?>
         <?php echo form_open_multipart(get_uri('ma/point_action/'.$id),array('id'=>'point-action-form', 'class' => 'general-form')) ;?>
            <div class="row">
            <div class="col-md-6">
               <?php $value = (isset($point_action) ? $point_action->name : ''); ?>
               <?php echo render_input('name','name',$value, 'text', array('required' => true)); ?>
               <?php $value = (isset($point_action) ? $point_action->category : ''); ?>
               <?php echo render_select('category',$category, array('id', 'name'),'category',$value, array('required' => true)); ?>
               <div class="form-group">
                 <?php
                   $selected = (isset($point_action) ? $point_action->published : ''); 
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
               <?php
                $description = (isset($point_action) ? $point_action->description : ''); 
                ?>
               <p class="bold"><?php echo _l('description'); ?></p>
               <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
            </div>
            <div class="col-md-6">
               <?php 
                  $actions = [
                     ['id' => 'downloads_an_asset', 'name' => _l('downloads_an_asset')],
                     ['id' => 'is_sent_an_email', 'name' => _l('is_sent_an_email')],
                     ['id' => 'opens_an_email', 'name' => _l('opens_an_email')],
                     ['id' => 'submit_a_form', 'name' => _l('submit_a_form')],
                  ];
               ?>

               <?php $value = (isset($point_action) ? $point_action->action : ''); ?>
               <?php echo render_select('action',$actions, array('id', 'name'),'when_a_contact',$value); ?>
               <?php $value = (isset($point_action) ? $point_action->change_points : ''); ?>
               <?php echo render_input('change_points','change_points',$value, 'number'); ?>
               <div class="checkbox checkbox-primary">
                  <input type="checkbox" name="add_points_by_country" id="add_points_by_country" <?php if (isset($point_action) && $point_action->add_points_by_country == 1) {
                      echo 'checked';
                  } ?> value="1" class="form-check-input">
                  <label for="add_points_by_country"><?php echo _l('add_points_by_country'); ?></label>
               </div>
               <div id="div_add_points_by_country" class="<?php if (isset($point_action) && $point_action->add_points_by_country != 1 || !isset($point_action)) { echo ' hide'; } ?>">
                  <div class="col-md-12">
                    <div class="row list_ladder_setting">
                      <?php if(isset($point_action) && count($point_action->change_point_details) > 0) { 
                        $setting = $point_action->change_point_details;
                        foreach ($setting as $key => $value) { ?>
                        <div id="item_ladder_setting">
                          <div class="row">
                            <div class="col-md-10">
                              <div class="row">
                                 <div class="col-md-6">
                                 <?php echo render_input('country['.$key.']','country',$value['country']); ?>
                               </div>
                               <div class="col-md-6">
                                 <?php echo render_input('list_change_points['.$key.']','change_points',$value['change_points'],'number'); ?>
                               </div>
                            </div>
                            </div>
                            <div class="col-md-2">
                            <span class="pull-bot">
                                <?php if($key != 0){ ?>
                                  <button name="add" class="btn remove_item_ladder btn-danger mtop25" data-ticket="true" type="button"><i data-feather="minus" class="icon-16"></i></button>
                                <?php }else{ ?>
                                  <button name="add" class="btn new_item_ladder btn-success mtop25" data-ticket="true" type="button"><i data-feather="plus" class="icon-16"></i></button>
                                <?php } ?>
                                  </span>
                            </div>
                          </div>
                        </div>
                      <?php 
                        }
                        }else{ 
                        ?>
                      <div id="item_ladder_setting">
                        <div class="row">
                          <div class="col-md-10">
                           <div class="row">
                               <div class="col-md-6">
                                 <?php echo render_input('country[0]','country'); ?>
                               </div>
                               <div class="col-md-6">
                                 <?php echo render_input('list_change_points[0]','change_points','','number'); ?>
                               </div>
                           </div>
                          </div>
                          <div class="col-md-2 no-padding">
                          <span class="pull-bot">
                              <button name="add" class="btn new_item_ladder btn-success mtop25" data-ticket="true" type="button"><i data-feather="plus" class="icon-16"></i></button>
                              </span>
                          </div>
                        </div>
                      </div>
                        
                        <?php 
                        } ?>
                    </div>
                  </div>
               </div>
            </div>
         </div>
         <hr>
         <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/points?group=point_actions'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
            </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>

<?php require 'plugins/Ma/assets/js/points/point_action_js.php';?>
