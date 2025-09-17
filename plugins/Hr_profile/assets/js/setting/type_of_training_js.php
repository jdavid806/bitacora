<script> 
/*load table*/
$(document).ready(function () {
        'use strict';
    
    $("#type_of_training-table").appTable({
        source: '<?php echo get_uri("hr_profile/list_type_of_training_data") ?>',
        order: [[0, 'desc']],
        filterDropdown: [
        ],
        columns: [
        {title: "<?php echo app_lang('type_of_training_name') ?> ", "class": "w80p"},
        {title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
        ],
        printColumns: [0, 1, 2, 3, 4],
        xlsColumns: [0, 1, 2, 3, 4]
    });
});

</script>
