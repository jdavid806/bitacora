<div class="page-title clearfix">
   <div class="title-button-group">
      <a href="<?php echo admin_url('ma/email_template'); ?>" class="btn btn-default">
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
			render_datatable($table_data,'email-templates');
		?>
	</div>
</div>

<div class="modal fade email-template" id="clone_email_template_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('ma/clone_email_template'), array('id' => 'clone-email-template-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('clone_template'); ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    	<div class="col-md-12">
                        <?php echo form_hidden('id'); ?>
                        <?php echo render_input('name', 'name'); ?>
                    	</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('close'); ?></button>
                <button group="submit" class="btn btn-info text-white"><i data-feather="check-circle" class="icon-16"></i> <?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php require 'plugins/Ma/assets/js/channels/email_template_manage_js.php'; ?>
