<script> 
/*load table*/
$(document).ready(function () {
	'use strict';
	
	$("#evaluation_criteria-table").appTable({
		source: '<?php echo get_uri("recruitment/list_evaluation_criteria_data") ?>',
		order: [[0, 'desc']],
		filterDropdown: [
		],
		columns: [
		{title: "<?php echo app_lang('add_from') ?> ", "class": "w20p"},
		{title: "<?php echo app_lang('criteria_title') ?>"},
		{title: "<?php echo app_lang('type') ?>"},
		{title: "<?php echo app_lang('date_add') ?>"},
		{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
		],
		printColumns: [0, 1, 2, 3, 4],
		xlsColumns: [0, 1, 2, 3, 4]
	});
});

</script>
