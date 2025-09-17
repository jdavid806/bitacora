<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
      <div class="row">
         <div class="col-md-6">
      <div class="panel_s">
   <?php $id = (isset($email) ? $email->id : ''); ?>
         <?php echo form_open_multipart(get_uri('ma/email/'.$id),array('id'=>'email-form', 'class' => 'general-form')) ;?>
         <div class="panel-body">
            <h4 class="customer-profile-group-heading"><?php echo html_entity_decode($title); ?></h4>
            
            <ul id="client-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title" role="tablist">
                 <li><a role="presentation"  data-bs-toggle="tab" href="javascript:;" data-bs-target="#information"><?php echo app_lang('information'); ?></a></li>

                 <li><a role="presentation"  data-bs-toggle="tab" href="javascript:;" data-bs-target="#advanced"><?php echo app_lang('advanced'); ?></a></li>
             </ul>

            <div class="tab-content mt-3">
               <div role="tabpanel" class="tab-pane fade" id="information">
                     <?php $value = (isset($email) ? $email->subject : ''); ?>
                     <?php echo render_input('subject','subject',$value, 'text', array('required' => true)); ?>
                     <?php $value = (isset($email) ? $email->name : ''); ?>
                     <?php echo render_input('name','internal_name',$value,  'text', array('required' => true)); ?>
                     <?php $value = (isset($email) ? $email->category : ''); ?>
                     <?php echo render_select('category',$category, array('id', 'name'),'category',$value, array('required' => true)); ?>
                     <?php $email_template = (isset($email) ? $email->email_template : ''); ?>
                        <?php echo render_select('email_template',$email_templates, array('id', 'name'),'email_template',$email_template, array('required' => true)); ?>
                     <div class="form-group">
                       <?php
                         $selected = (isset($email) ? $email->published : ''); 
                         ?>
                       <label for="published"><?php echo _l('published'); ?></label><br />
                       <div class="radio radio-inline radio-primary">
                         <input type="radio" name="published" id="published_yes" value="1" <?php if($selected == '1'|| $selected == ''){echo 'checked';} ?>>
                         <label for="published_yes"><?php echo _l("yes"); ?></label>
                       </div>
                       <div class="radio radio-inline radio-primary">
                         <input type="radio" name="published" id="published_no" value="0" <?php if($selected == '0'){echo 'checked';} ?>>
                         <label for="published_no"><?php echo _l("no"); ?></label>
                       </div>
                     </div>
                     
                     <?php $value = (isset($email) ? $email->color : ''); ?>
                     <?php echo render_color_picker('color',_l('color'),$value); ?>
                     <div class="form-group select-placeholder">
                         <label for="language" class="control-label"><?php echo _l('form_lang_validation'); ?></label>
                         <select name="language" id="language" class="select2 form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php foreach ($languages as $language) {
                             ?>
                            <option value="<?php echo html_entity_decode($language); ?>"<?php if ((isset($email) && $email->language == $language) || (!isset($email) && get_setting('language') == $language)) {
                                 echo ' selected';
                             } ?>><?php echo html_entity_decode($language); ?></option>
                            <?php } ?>
                         </select>
                      </div>
                        
                        <?php
                      $description = (isset($email) ? $email->description : ''); 
                      ?>
                     <p class="bold"><?php echo _l('description'); ?></p>
                     <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
               </div>
               <div role="tabpanel" class="tab-pane fade" id="advanced">
                  <?php $value = (isset($email) ? $email->from_name : ''); ?>
                  <?php echo render_input('from_name','from_name',$value); ?>
                  <?php $value = (isset($email) ? $email->from_address : ''); ?>
                  <?php echo render_input('from_address','from_address',$value, 'email'); ?>
                  <?php $value = (isset($email) ? $email->reply_to_address : ''); ?>
                  <?php echo render_input('reply_to_address','reply_to_address',$value, 'email'); ?>
               
                  <?php $value = (isset($email) ? $email->bcc_address : ''); ?>
                  <?php echo render_input('bcc_address','bcc_address',$value, 'email'); ?>
                  <?php $value = (isset($email) ? $email->attachment : ''); ?>
                  <?php echo render_select('attachment',$assets, array('id', 'name'),'attachment',$value); ?>
               </div>
            </div>
            <hr class="hr-panel-heading" />
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/channels?group=emails'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
            </div>
         </div>
         <?php echo form_close(); ?>
      </div>
      </div>
      <div id="preview_area" class="col-md-6 no-padding build-section">
      </div>
      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/channels/email_js.php';?>
