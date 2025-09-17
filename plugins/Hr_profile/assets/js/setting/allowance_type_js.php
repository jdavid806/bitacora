<script> 
/*load table*/
$(document).ready(function () {
  'use strict';
  
  $("#commodity_type-table").appTable({
    source: '<?php echo get_uri("hr_profile/list_allowance_type_data") ?>',
    order: [[0, 'desc']],
    filterDropdown: [
    ],
    columns: [
    {title: "<?php echo app_lang('hr_allowance_type_name') ?> ", "class": "w20p"},
    {title: "<?php echo app_lang('amount') ?>"},
    {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
    ],
    printColumns: [0, 1, 2, 3, 4],
    xlsColumns: [0, 1, 2, 3, 4]
  });
});

</script>
