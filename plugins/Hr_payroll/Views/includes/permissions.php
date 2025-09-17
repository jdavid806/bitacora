<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-md-12">
		<h4 class="h4-color no-margin"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <?php echo app_lang('hrp_permissions'); ?></h4>
	</div>
</div>
<hr class="hr-color">

<?php if(is_admin()){ ?>
<a href="#" onclick="hr_payroll_permissions_update(0,0,' hide'); return false;" class="btn btn-info mbot10"><?php echo app_lang('_new'); ?></a>
<?php } ?>
<table class="table table-hr-profile-permission">
  <thead>
    <th><?php echo app_lang('hrp_staff_name'); ?></th>
    <th><?php echo app_lang('role'); ?></th>
    <th><?php echo app_lang('staff_dt_email'); ?></th>
    <th><?php echo app_lang('hrp_phone'); ?></th>
    <th><?php echo app_lang('options'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div id="modal_wrapper"></div>

