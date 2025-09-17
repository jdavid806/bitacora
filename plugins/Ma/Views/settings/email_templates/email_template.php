<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
        </div>
        <div class="card-body">
        <?php $id = (isset($email_template) ? $email_template->id : ''); ?>
         <?php echo form_open_multipart(get_uri('ma/email_template/'.$id),array('id'=>'email-template-form', 'class' => 'general-form')) ;?>
            <div class="row">
                <div class="col-md-6">
                   <?php $value = (isset($email_template) ? $email_template->name : ''); ?>
                   <?php echo render_input('name','name',$value, 'text', array('required' => true)); ?>
                   <?php $value = (isset($email_template) ? $email_template->category : ''); ?>
                   <?php echo render_select('category',$category, array('id', 'name'),'category',$value, array('required' => true)); ?>
                   <?php $value = (isset($email_template) ? $email_template->color : ''); ?>
                   <?php echo render_color_picker('color',_l('color'),$value); ?>
                   <div class="form-group">
                     <?php
                       $selected = (isset($email_template) ? $email_template->published : ''); 
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
                </div>
                <div class="col-md-6">
                   <?php
                    $description = (isset($email_template) ? $email_template->description : ''); 
                    ?>
                   <p class="bold"><?php echo _l('description'); ?></p>
                   <?php echo render_textarea('description','',$description,array(),array(),'','tinymce'); ?>
                   <?php $types = [
                      ['id' => 'template_email_template', 'name' => _l('template_email_template')],
                      ['id' => 'segment_email_template', 'name' => _l('segment_email_template')],
                   ]; ?>
                </div>
            </div>
            <div class="btn-bottom-toolbar text-right">
               <a href="<?php echo admin_url('ma/setting?group=ma_email_templates'); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <button type="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>

            </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/emails/email_template_js.php'; ?>
