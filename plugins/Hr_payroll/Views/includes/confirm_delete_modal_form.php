<!-- Modal -->
<?php echo form_open(get_uri("hr_payroll/".$function), array("id" => "item-form", "class" => "general-form", "role" => "form")); ?>
<input type="hidden" name="id" value="<?php echo html_entity_decode($id); ?>" />
<?php if(isset($id2)){ ?>
	<input type="hidden" name="id2" value="<?php echo html_entity_decode($id2); ?>" />
<?php } ?>

<div id="deleteModalContent" class="modal-body">
	<div class="container-fluid">
		<?php echo app_lang('delete_confirmation_message'); ?>
	</div>
</div>
<div class="modal-footer clearfix">
	<button type="submit" class="btn btn-danger"><span data-feather="trash-2" class="icon-16"></span> <?php echo app_lang('delete'); ?></button>
	<button type="button" class="btn btn-default" data-bs-dismiss="modal"><i data-feather="x" class="icon-16"></i> <?php echo app_lang('cancel'); ?></button>
</div>
<?php echo form_close(); ?>
