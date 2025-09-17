<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      <div class="page-title clearfix">
          <h1><?php echo html_entity_decode($title); ?></h1>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
              <a href="<?php echo admin_url('ma/campaign_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('campaign_report'); ?></h4></a>
              <a href="<?php echo admin_url('ma/email_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('email_report'); ?></h4></a>
              <a href="<?php echo admin_url('ma/asset_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('asset_report'); ?></h4></a>
          </div>
          <div class="col-md-6">
              <a href="<?php echo admin_url('ma/lead_and_point_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('contact_and_point_report'); ?></h4></a>
              <a href="<?php echo admin_url('ma/form_report'); ?>"><h4 class=""><i class="fa fa-area-chart"></i> <?php echo _l('form_report'); ?></h4></a>
          </div>
        </div>
    </div>
  </div>
</div>
