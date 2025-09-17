<?php echo form_open('ma/ma_sms_setting',array('id'=>'sms-setting-form')); ?>
<?php 
	$total_gateways = count($gateways);

	if($total_gateways > 1) { ?>
	    <div class="alert alert-info">
	        <?php echo _l('notice_only_one_active_sms_gateway'); ?>
	    </div>
	<?php } ?>

	<div id="sms_gateways_options" role="tablist" aria-multiselectable="false">
	    <?php foreach($gateways as $gateway) { ?>
	    <div class="border p-4 mb-3">
	        <div class="panel-heading" role="tab" id="<?php echo 'heading'.$gateway['id']; ?>">
	          <h4 class="panel-title">
	            <a role="button" data-toggle="collapse" data-parent="#sms_gateways_options" href="#sms_<?php echo html_entity_decode($gateway['id']); ?>" aria-expanded="true" aria-controls="sms_<?php echo html_entity_decode($gateway['id']); ?>">
	                <?php echo html_entity_decode($gateway['name']); ?> <span class="pull-right"><i class="fa fa-sort-down"></i></span>
	            </a>
	        </h4>
	    </div>
	    <div id="sms_<?php echo html_entity_decode($gateway['id']); ?>" class="panel-collapse" role="tabpanel" aria-labelledby="<?php echo 'heading'.$gateway['id']; ?>">
	      <div class="no-br-tlr no-border-color">

	        <?php
	        if(isset($gateway['info']) && $gateway['info'] != '') {
	            echo html_entity_decode($gateway['info']);
	        }

	        foreach($gateway['options'] as $g_option){
	            echo render_input('settings['.$app_sms->option_name($gateway['id'],$g_option['name']).']',$g_option['label'],$app_sms->get_option($gateway['id'],$g_option['name']));
	            if(isset($g_option['info'])) {
	                echo html_entity_decode($g_option['info']);
	            }
	        }
	        echo '<div class="sms_gateway_active">';

	        echo render_yes_no_option($app_sms->option_name($gateway['id'],'active'),'active');
	        echo '</div>';
	            if(get_setting($app_sms->option_name($gateway['id'],'active')) == '1') {
	                echo '<hr />';
	                echo '<h4 class="mbot15">'._l('test_sms_config').'</h4>';
	                echo '<div class="form-group"><input type="text" placeholder="'._l('phone').'" class="form-control test-phone" data-id="'.$gateway['id'].'"></div>';
	                echo '<div class="form-group"><textarea class="form-control sms-gateway-test-message" placeholder="'._l('test_sms_message').'" data-id="'.$gateway['id'].'" rows="4"></textarea></div>';
	                echo '<button type="button" class="btn btn-info send-test-sms text-white" data-id="'.$gateway['id'].'">'._l('send_test_sms').'</button>';
	                echo '<div id="sms_test_response" data-id="'.$gateway['id'].'"></div>';
	            }
	        ?>
	    </div>
	</div>
	</div>
	<?php } ?>
	
	</div>

<div class="row">
<div class="col-md-12">
<hr>
</div>
</div>
<div class="col-md-12">
  <button type="submit" class="btn btn-info text-white pull-right"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
</div>
<?php echo form_close(); ?>