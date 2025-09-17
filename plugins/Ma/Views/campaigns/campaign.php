<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
               <?php $id = isset($campaign) ? $campaign->id : ''; ?>
            <?php echo form_open(get_uri('ma/campaign/'.$id),array('class'=>'campaign-form','autocomplete'=>'off', 'class' => 'general-form')); ?>
                     <div class="row">
                        <div class="col-md-6">
                          <?php $name = ( isset($campaign) ? $campaign->name : '');
                              echo render_input('name','name',$name,'text', array('required' => true)); ?>
                          <?php $value = ( isset($campaign) ? $campaign->category : ''); ?>
                          <?php echo render_select('category',$categories,array('id','name'), 'category', $value, array('required' => true)); ?>
                           <?php $value = (isset($campaign) ? $campaign->color : ''); ?>
                           <?php echo render_color_picker('color',_l('color'),$value); ?>
                           <div class="form-group">
                             <?php $selected = (isset($campaign) ? $campaign->published : ''); ?>
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
                           <div class="row">
                              <div class="col-md-6">
                                 <?php $value = (isset($campaign) ? _d($campaign->start_date) : _d(date('Y-m-d'))); ?>
                                 <?php echo render_date_input('start_date','start_date',$value);?>
                              </div>
                              <div class="col-md-6">
                                 <?php $due_date = (isset($campaign) ? _d($campaign->end_date) : _d(date('Y-m-d')));
                                 echo render_date_input('end_date','end_date',$due_date); ?>
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                          <?php
                            $description = (isset($campaign) ? $campaign->description : ''); 
                            ?>
                          <p class="bold"><?php echo _l('description'); ?></p>
                          <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
                        </div>
                     </div>
                    <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/campaigns'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
            </div>
             
                        <?php echo form_close(); ?>


      </div>
     
   </div>
</div>

<?php require 'plugins/Ma/assets/js/campaigns/campaign_js.php';?>
