<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
            <div class="title-button-group">
               <a href="#" class="btn btn-default add-new-stage">
                 <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add'); ?>
               </a>
               |
               <a href="<?php echo admin_url('ma/stages?group=list'); ?>" class="btn pull-right <?php echo ($group == 'list' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-list" aria-hidden="true"></i> <?php echo _l('list'); ?></a>
               <a href="<?php echo admin_url('ma/stages?group=chart'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'chart' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('chart'); ?></a>
               <a href="<?php echo admin_url('ma/stages?group=kanban'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'kanban' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-large" aria-hidden="true"></i> <?php echo _l('kanban'); ?></a>
            </div>
        </div>
        <div class="card-body">
          <div class="row mbot15">
             <div class="col-md-12">
                <h4 class="no-margin"><?php echo _l('stages_summary'); ?></h4>
             </div>
             <div class="col-md-2 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows(db_prefix().'ma_stages'); ?></h3>
                <span class="text-dark"><?php echo _l('stages_summary_total'); ?></span>
             </div>
             <?php foreach($categories as $category){ ?>
             <div class="col-md-2 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows(db_prefix().'ma_stages','category='.$category['id']); ?></h3>
                <span style="color: <?php echo html_entity_decode($category['color']); ?>;"><?php echo html_entity_decode($category['name']); ?></span>
             </div>
             <?php } ?>
           </div>
          <hr class="hr-panel-heading" />
            <?php echo view($view); ?>
          
    </div>
  </div>
</div>

<div class="modal fade" id="stage-modal">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h4 class="modal-title"><?php echo _l('stages')?></h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <?php echo form_open_multipart(admin_url('ma/stage'),array('id'=>'stage-form', 'class' => 'general-form'));?>
         <?php echo form_hidden('id'); ?>
         
         <div class="modal-body">
              <?php echo render_input('name', 'name', '','text', array('required' => true)) ?>
              <?php echo render_input('weight', 'weight', '', 'number') ?>
              <?php echo render_color_picker('color',_l('color')); ?>
              <?php echo render_select('category',$categories,array('id','name'), 'category', '', array('required' => true)); ?>
              <div class="row">
                <div class="col-md-12">
                  <p class="bold"><?php echo _l('description'); ?></p>
                  <?php echo render_textarea('description','',''); ?>
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


<?php require 'plugins/Ma/assets/js/stages/manage_js.php';?>
