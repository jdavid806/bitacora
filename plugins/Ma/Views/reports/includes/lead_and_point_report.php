<div id="page-content" class="page-wrapper clearfix">
    <?php echo form_hidden('site_url', get_uri()); ?>
    <div class="card">
      <div class="card-body">
          <h4 class="no-margin font-bold"><?php echo html_entity_decode($title); ?></h4>
          <a href="<?php echo admin_url('ma/reports'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />
          <div id="container_chart"></div>
          <?php echo form_hidden('timezone', get_setting('timezone')); ?>

          <table class="table table-point-action mtop25">
            <thead>
              <th><?php echo _l('point_action'); ?></th>
              <th><?php echo _l('contact_type'); ?></th>
              <th><?php echo _l('contact_name'); ?></th>
              <th><?php echo _l('email'); ?></th>
              <th><?php echo _l('change_points'); ?></th>
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
<?php require 'plugins/Ma/assets/js/reports/lead_and_point_report_js.php'; ?>
