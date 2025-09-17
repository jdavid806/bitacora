<script> 
/*load table*/
$(document).ready(function () {
	'use strict';
	
	$("#job_position-table").appTable({
		source: '<?php echo get_uri("recruitment/list_job_position_data") ?>',
		order: [[0, 'desc']],
		filterDropdown: [
		],
		columns: [
		{title: "<?php echo app_lang('id') ?> ", "class": "w20p"},
		{title: "<?php echo app_lang('job_position') ?>"},
		{title: "<?php echo app_lang('industry') ?>"},
		{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
		],
		printColumns: [0, 1, 2, 3, 4],
		xlsColumns: [0, 1, 2, 3, 4]
	});
});

</script>
