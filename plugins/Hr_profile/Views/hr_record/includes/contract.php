<div class="card rounded-0">
    <div class="table-responsive">
        <?php
        $table_data = array(
            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="table_contract"><label></label></div>',

            app_lang('id'),
            app_lang('hr_contract_code'),
            app_lang('hr_name_contract'),
            app_lang('staff'),
            app_lang('departments'),
            app_lang('hr_start_month'),
            app_lang('hr_end_month'),
            app_lang('hr_status_label'),
            app_lang('hr_sign_day'), 
        );
        render_datatable1($table_data,'table_contract');
        ?>
    </div>
</div>
<?php require 'plugins/Hr_profile/assets/js/hr_record/includes/contract_js.php';?>
