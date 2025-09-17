<div class="page-title clearfix">
  <h4>
    <span class="text-success glyphicon glyphicon-log-in"> </span><span class="text-success"> <?php echo _l('flow_start'); ?></span>
  </h4>
</div>
<div class="box" node-id="<?php echo html_entity_decode($nodeId); ?>">
  <div class="form-group">
    <label for="data_type"><?php echo _l('data_type'); ?></label><br />
    <div class="radio radio-inline radio-primary">
      <input type="radio" name="data_type[<?php echo html_entity_decode($nodeId); ?>]" id="data_type_lead[<?php echo html_entity_decode($nodeId); ?>]" value="lead" checked df-data_type class="form-check-input">
      <label for="data_type_lead[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("lead"); ?></label>
    </div>
    <div class="radio radio-inline radio-primary">
      <input type="radio" name="data_type[<?php echo html_entity_decode($nodeId); ?>]" id="data_type_customer[<?php echo html_entity_decode($nodeId); ?>]" value="customer" df-data_type class="form-check-input">
      <label for="data_type_customer[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("customer"); ?></label>
    </div>
  </div>
  <div class="div_data_type_customer hide">
    <?php echo render_select('customer_group['. $nodeId.']',$customer_groups, array('id', 'title'),'customer_group', '', ['df-customer_group' => '']); ?>
  </div>
  <div class="div_data_type_lead">
    <div class="form-group">
      <label for="lead_data_from"><?php echo _l('lead_data_from'); ?></label><br />
      <div class="radio radio-inline radio-primary">
        <input type="radio" name="lead_data_from[<?php echo html_entity_decode($nodeId); ?>]" id="lead_data_from_segment[<?php echo html_entity_decode($nodeId); ?>]" value="segment" checked df-lead_data_from class="form-check-input">
        <label for="lead_data_from_segment[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("segment"); ?></label>
      </div>
      <div class="radio radio-inline radio-primary">
        <input type="radio" name="lead_data_from[<?php echo html_entity_decode($nodeId); ?>]" id="lead_data_from_form[<?php echo html_entity_decode($nodeId); ?>]" value="form" df-lead_data_from class="form-check-input">
        <label for="lead_data_from_form[<?php echo html_entity_decode($nodeId); ?>]"><?php echo _l("form"); ?></label>
      </div>
    </div>
    <div class="div_lead_data_from_segment">
      <?php echo render_select('segment['. $nodeId.']',$segments, array('id', 'name'),'segment', '', ['df-segment' => '']); ?>
    </div>
    <div class="div_lead_data_from_form hide">
      <?php echo render_select('form['. $nodeId.']',$forms, array('id', 'name'),'form', '', ['df-form' => '']); ?>
    </div>
  </div>
</div>
