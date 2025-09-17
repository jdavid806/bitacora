<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      <div class="card-body">
          <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
          <?php echo form_hidden('timezone', get_setting('timezone')); ?>
          <a href="<?php echo admin_url('ma/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />
          <div id="container_chart"></div>

          <table class="table table-form-logs mtop25">
            <thead>
              <th><?php echo _l('form'); ?></th>
              <th><?php echo _l('lead_name'); ?></th>
              <th><?php echo _l('email'); ?></th>
              <th><?php echo _l('time'); ?></th>
            </thead>
            <tbody>
            </tbody>
          </table>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php require 'plugins/Ma/assets/js/reports/form_report_js.php'; ?>
