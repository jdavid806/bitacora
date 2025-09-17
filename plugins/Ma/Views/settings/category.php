
<div class="page-title clearfix">
   <div class="title-button-group">
      <a href="#" class="btn btn-default add-new-category">
        <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add'); ?>
      </a>
   </div>
 </div>
                   

<div class="row">
   <div class="col-md-12">
      <?php 
         $table_data = array(
            _l('name'),
            _l('type'),
            _l('description'),
            '<i data-feather="menu" class="icon-16"></i>'
            );

         render_datatable($table_data,'category');
      ?>
   </div>
</div>
<div class="clearfix"></div>

<?php 
	$types = [
      ['id' => 'segment', 'name' => _l('segment')],
      ['id' => 'stage', 'name' => _l('stage')],
      ['id' => 'point_action', 'name' => _l('point_action')],
      ['id' => 'asset', 'name' => _l('asset')],
      ['id' => 'form', 'name' => _l('form')],
      ['id' => 'email', 'name' => _l('email')],
      ['id' => 'sms', 'name' => _l('sms')],
      ['id' => 'email_template', 'name' => _l('email_template')],
      ['id' => 'text_message', 'name' => _l('text_message')],
      ['id' => 'campaign', 'name' => _l('campaign')],
	];
?>
<div class="modal fade" id="category-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('category')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <?php echo form_open_multipart(admin_url('ma/category'),array('id'=>'category-form', 'class' => 'general-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
            <?php echo render_input('name','name'); ?>
            <?php echo render_select('type',$types,array('id','name'),'type','',array(),array(),'','',false); ?>
            <?php echo render_color_picker('color',_l('color')); ?>
            <div class="row">
                <div class="col-md-12">
                  <p class="bold"><?php echo _l('description'); ?></p>
                  <?php echo render_textarea('description',''); ?>
                </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>
            <button group="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>

         </div>
         <?php echo form_close(); ?>  
      </div>
   </div>
</div>

<?php require 'plugins/Ma/assets/js/settings/category_js.php'; ?>
