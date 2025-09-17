<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
       
        <div class="card-body">
            <div id="EmailEditor" class="EmailEditor"></div>
            <hr>
            <div><strong><?php echo app_lang("avilable_variables"); ?></strong>: <?php
            foreach ($available_merge_fields as $variable) {
                echo "{" . $variable . "}, ";
            }
            ?></div>
            <?php echo form_open(admin_url('ma/email_design_save'),array('id'=>'email-template-form','autocomplete'=>'off')); ?>
            <?php echo form_hidden('id',(isset($email_design) ? $email_design->id : '') ); ?>
            <?php echo form_hidden('email_id',(isset($email_design) ? $email_design->email_id : '') ); ?>
            <?php echo form_hidden('data_design',(isset($email_design) && $email_design->data_design != null ? $email_design->data_design : '')); ?>
            <?php echo form_hidden('data_html',(isset($email_design) && $email_design->data_html != null ? $email_design->data_html : '')); ?>
            <?php echo form_close(); ?>

            <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
               <a href="<?php echo admin_url('ma/email_detail/'.$email_design->email_id); ?>" class="btn btn-default"><i data-feather="x" class="icon-16"></i> <?php echo _l('back'); ?></a>
               <a href="#" onclick="save_template(); return false;" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></a>
           </div>
      </div>
   </div>
</div>
<?php require 'plugins/Ma/assets/js/channels/email_design_js.php';?>
