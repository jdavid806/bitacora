<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(hr_has_permission('hr_profile_can_create_setting')){ ?>
<a href="#" onclick="hr_profile_permissions_update(0,0,' hide'); return false;" class="btn btn-info mbot10"><?php echo app_lang('hr_hr_add'); ?></a>
<?php } ?>
<table class="table table-hr-profile-permission">
  <thead>
    <th><?php echo app_lang('hr_hr_staff_name'); ?></th>
    <th><?php echo app_lang('role'); ?></th>
    <th><?php echo app_lang('staff_dt_email'); ?></th>
    <th><?php echo app_lang('phone'); ?></th>
    <th><?php echo app_lang('options'); ?></th>
  </thead>
  <tbody>
  </tbody>
</table>
<div id="modal_wrapper"></div>

