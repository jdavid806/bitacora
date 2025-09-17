<div id="page-content" class="page-wrapper clearfix">
   <?php echo form_hidden('site_url', get_uri()); ?>
   <?php $id = (isset($text_message) ? $text_message->id : ''); ?>
   <?php echo form_open_multipart(get_uri('ma/text_message/'.$id),array('id'=>'text-messages-form', 'class' => 'general-form')) ;?>
   <div class="row">
   <div class="col-md-6">
      <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
         <div class="card-body">
            <div class="col-md-12">
               <?php $value = (isset($text_message) ? $text_message->name : ''); ?>
               <?php echo render_input('name', 'name', $value, 'text', array('required' => true)); ?>
               <?php $value = (isset($text_message) ? $text_message->category : ''); ?>
               <?php echo render_select('category',$category, array('id', 'name'),'category',$value, array('required' => true)); ?>
               <div class="form-group select-placeholder">
                <label for="language" class="control-label"><?php echo _l('form_lang_validation'); ?></label>
                <select name="language" id="language" class="select2 form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                   <option value=""></option>
                   <?php foreach ($languages as $language) {
                    ?>
                   <option value="<?php echo html_entity_decode($language); ?>"<?php if ((isset($text_message) && $text_message->language == $language) || (!isset($text_message) && get_setting('language') == $language)) {
                        echo ' selected';
                    } ?>><?php echo html_entity_decode($language); ?></option>
                   <?php } ?>
                </select>
             </div>
               <div class="form-group">
                 <?php
                   $selected = (isset($text_message) ? $text_message->published : ''); 
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
                <?php $value=( isset($text_message) ? $text_message->description : ''); ?>
               <?php echo render_textarea( 'description', 'content',$value); ?>
            </div>
            <div><strong><?php echo app_lang("avilable_variables"); ?></strong>: <?php
            foreach ($available_merge_fields as $variable) {
                echo "{" . $variable . "}, ";
            }
            ?></div>
            <hr class="hr-panel-heading" />
            <div class="col-md-12 text-right">
               <a href="<?php echo admin_url('ma/setting?group=text_messages'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
            </div>
         </div>
      </div>
   </div>
   </div>
   <?php echo form_close(); ?>
</div>

<?php require 'plugins/Ma/assets/js/channels/text_message_js.php'; ?>
