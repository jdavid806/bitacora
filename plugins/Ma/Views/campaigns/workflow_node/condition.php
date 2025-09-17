<div class="page-title clearfix">
  <h4>
    <span class="text-danger glyphicon glyphicon-fullscreen"> </span><span class="text-danger"> <?php echo _l('condition'); ?></span>
  </h4>
</div>
<div class="box" node-id="<?php echo html_entity_decode($nodeId); ?>">
  <?php $tracks = [
      ['id' => 'delivery','name' => _l('delivery')],
      ['id' => 'opens','name' => _l('opens')],
      ['id' => 'clicks','name' => _l('clicks')],
    ]; ?>
    <?php echo render_select('track['.$nodeId.']',$tracks, array('id', 'name'),'track', '', ['df-track' => ''], [], '', '', false); ?>
    <?php $units = [
      ['id' => 'min','name' => _l('min')],
      ['id' => 'hours','name' => _l('hours')],
      ['id' => 'days','name' => _l('days')],
    ]; ?>
    
    <label>Wait for trigger:</label>
    <div class="row">
      <div class="col-md-6 no-padding-right">
        <?php echo render_input('waiting_number['.$nodeId.']', '', '1', 'number', ['df-waiting_number' => '']); ?>
      </div>
      <div class="col-md-6">
        <?php echo render_select('waiting_type['.$nodeId.']',$units, array('id', 'name'),'', '', ['df-waiting_type' => ''], [], '', '', false); ?>
      </div>
    </div>
</div>
   