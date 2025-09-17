<div class="page-title clearfix">
   <div class="title-button-group">
      <a href="<?php echo admin_url('ma/text_message'); ?>" class="btn btn-default">
        <i data-feather="plus-circle" class="icon-16"></i> <?php echo _l('add'); ?>
      </a>
   </div>
 </div>
 
<div class="row">
	<div class="col-md-12">
		<?php 
			$table_data = array(
            _l('id'),
				_l('name'),
				_l('category'),
				_l('dateadded'),
            '<i data-feather="menu" class="icon-16"></i>'
				
				);
			render_datatable($table_data,'text-messages');
		?>
	</div>
</div>

<?php require 'plugins/Ma/assets/js/channels/text_messages_manage_js.php'; ?>
