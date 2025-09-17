<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      <div class="card-body">
          <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
          <a href="<?php echo admin_url('ma/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <?php echo form_hidden('timezone', get_setting('timezone')); ?>
          <hr />
          <div id="container_chart"></div>

          <table class="table table-email-logs mtop25">
            <thead>
              <th><?php echo _l('time'); ?></th>
              <th><?php echo _l('email_template'); ?></th>
              <th><?php echo _l('contact_type'); ?></th>
              <th><?php echo _l('contact_name'); ?></th>
              <th><?php echo _l('email'); ?></th>
              <th><?php echo _l('delivery'); ?></th>
              <th><?php echo _l('open'); ?></th>
              <th><?php echo _l('click'); ?></th>
            </thead>
            <tbody>
            </tbody>
          </table>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php require 'plugins/Ma/assets/js/reports/email_report_js.php'; ?>
