<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      
      <div class="card-body">
          <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
          <a href="<?php echo admin_url('ma/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <?php echo form_hidden('timezone', get_setting('timezone')); ?>
          <hr />
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_email"></div>
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="panel_s">
                <div class="panel-body">
                  <div id="container_point_action"></div>
                </div>
              </div>
            </div>
          </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>

<?php require 'plugins/Ma/assets/js/reports/campaign_report_js.php'; ?>
