<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      <div class="page-title clearfix">
          <h1><?php echo html_entity_decode($title); ?></h1>
      </div>
      <div class="card-body">
            
        <div class="wrapper">
          <div class="col-md-2 action-tab">
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="flow_start">
              <span class="text-success glyphicon glyphicon-log-in"> </span><span class="text-success"> <?php echo _l('flow_start'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="condition">
              <span class="text-danger glyphicon glyphicon-fullscreen"> </span><span class="text-danger"> <?php echo _l('condition'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="action">
              <span class="text-info glyphicon glyphicon-retweet"> </span><span class="text-info"> <?php echo _l('action'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="filter">
              <span class="text-warning glyphicon glyphicon-random"> </span><span class="text-warning"> <?php echo _l('filter'); ?></span>
            </div>
            <div class="drag-drawflow" draggable="true" ondragstart="drag(event)" data-node="email">
              <span class="text-primary glyphicon glyphicon-envelope"> </span><span class="text-primary"> <?php echo _l('email'); ?></span>
            </div>
          </div>
          <div class="col-md-10 pright20">
            <div id="drawflow" ondrop="drop(event)" ondragover="allowDrop(event)">
              <?php echo form_open(admin_url('ma/workflow_builder_save'),array('id'=>'workflow-form','autocomplete'=>'off')); ?>
            <?php echo form_hidden('campaign_id',(isset($campaign) ? $campaign->id : '') ); ?>
            <?php echo form_hidden('workflow',((isset($campaign) && $campaign->workflow != null) ? $campaign->workflow : '')); ?>
              <button type="submit" class="btn-export"><?php echo _l('save'); ?></button>
            <?php echo form_close(); ?>
              <!-- <div class="btn-export" onclick="save_workflow(); return false;"><?php echo _l('save'); ?></div> -->
              <div class="btn-clear" onclick="editor.clearModuleSelected()">Clear</div>
            </div>
          </div>
        </div>

      </div>
  </div>
</div>

<?php require 'plugins/Ma/assets/js/campaigns/workflow_builder_js.php';?>

