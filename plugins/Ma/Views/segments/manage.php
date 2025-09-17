<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
   <div class="card">
        <div class="page-title clearfix">
            <h1><?php echo html_entity_decode($title); ?></h1>
            <div class="title-button-group">
               <a href="<?php echo admin_url('ma/segment'); ?>" class="btn btn-default">
                 <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add'); ?>
               </a>
               |
               <a href="<?php echo admin_url('ma/segments?group=list'); ?>" class="btn pull-right <?php echo ($group == 'list' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-list" aria-hidden="true"></i> <?php echo _l('list'); ?></a>
               <a href="<?php echo admin_url('ma/segments?group=chart'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'chart' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('chart'); ?></a>
               <a href="<?php echo admin_url('ma/segments?group=kanban'); ?>" class="btn pull-right mleft5 <?php echo ($group == 'kanban' ? 'btn-success disabled' : 'btn-default'); ?>"><i class="fa fa-th-large" aria-hidden="true"></i> <?php echo _l('kanban'); ?></a>
            </div>
        </div>
   <div class="card-body">
      <div class="row mbot15">
         <div class="col-md-12">
            <h4 class="no-margin"><?php echo _l('segments_summary'); ?></h4>
         </div>
         <div class="col-md-2 col-xs-6 border-right">
            <h3 class="bold"><?php echo total_rows(db_prefix().'ma_segments'); ?></h3>
            <span class="text-dark"><?php echo _l('segments_summary_total'); ?></span>
         </div>
         <?php foreach($categories as $category){ ?>
         <div class="col-md-2 col-xs-6 border-right">
            <h3 class="bold"><?php echo total_rows(db_prefix().'ma_segments','category='.$category['id']); ?></h3>
            <span style="color: <?php echo html_entity_decode($category['color']); ?>;"><?php echo html_entity_decode($category['name']); ?></span>
         </div>
         <?php } ?>
       </div>
      <hr class="hr-panel-heading" />
      <?php echo view($view); ?>
    </div>
  </div>
</div>
<?php require 'plugins/Ma/assets/js/segments/manage_js.php';?>

