<div class="page-title clearfix">
   <div class="title-button-group">
      <a href="#" class="btn btn-default" onclick="add_permission(); return false;">
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
				_l('role'),
                '<i data-feather="menu" class="icon-16"></i>'
				);
			render_datatable($table_data,'permissions');
		?>
	</div>
</div>

<div class="modal fade email-template" id="permission-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('ma/permission'), array('id' => 'clone-email-template-form', 'class' => 'general-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('permission'); ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo form_hidden('id'); ?>
                <?php echo render_select('user', $members, array('id', array('first_name', 'last_name')), 'user', '', array('required' => true)); ?>
                <hr>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="dashboard_view" id="dashboard_view" class="form-check-input">
                    <label for="dashboard_view"><?php echo sprintf(_l('ma_can_access'), 'dashboard'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="segments_view" id="segments_view" class="form-check-input">
                    <label for="segments_view"><?php echo sprintf(_l('ma_can_access'), 'segments'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="components_view" id="components_view" class="form-check-input">
                    <label for="components_view"><?php echo sprintf(_l('ma_can_access'), 'components'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="campaigns_view" id="campaigns_view" class="form-check-input">
                    <label for="campaigns_view"><?php echo sprintf(_l('ma_can_access'), 'campaigns'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="channels_view" id="channels_view" class="form-check-input">
                    <label for="channels_view"><?php echo sprintf(_l('ma_can_access'), 'channels'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="points_view" id="points_view" class="form-check-input">
                    <label for="points_view"><?php echo sprintf(_l('ma_can_access'), 'points'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="stages_view" id="stages_view" class="form-check-input">
                    <label for="stages_view"><?php echo sprintf(_l('ma_can_access'), 'stages'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="reports_view" id="reports_view" class="form-check-input">
                    <label for="reports_view"><?php echo sprintf(_l('ma_can_access'), 'reports'); ?></label>
                </div>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="settings_view" id="settings_view" class="form-check-input">
                    <label for="settings_view"><?php echo sprintf(_l('ma_can_access'), 'settings'); ?></label>
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

<?php require 'plugins/Ma/assets/js/settings/permissions_js.php'; ?>
