<script> 
/*load table*/
$(document).ready(function () {
	'use strict';
	
	$("#procedure_retire-table").appTable({
		source: '<?php echo get_uri("hr_profile/list_procedure_retire_data") ?>',
		order: [[0, 'desc']],
		filterDropdown: [
		],
		columns: [
		{title: "<?php echo app_lang('hr_name_procedure_retire') ?> ", "class": "w20p"},
		{title: "<?php echo app_lang('hr_department') ?>", "class": "w50p"},
		{title: "<?php echo app_lang('hr_datecreator') ?>"},
		{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option"}
		],
		printColumns: [0, 1, 2, 3, 4],
		xlsColumns: [0, 1, 2, 3, 4]
	});
});

</script>