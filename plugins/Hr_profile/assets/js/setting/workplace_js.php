<script> 
	/*load table*/
	$(document).ready(function () {
		'use strict';
		
		$("#workplace-table").appTable({
			source: '<?php echo get_uri("hr_profile/list_workplace_data") ?>',
			order: [[0, 'desc']],
			filterDropdown: [
			],
			columns: [
			{title: "<?php echo app_lang('hr_hr_workplace') ?> ", "class": "w30p"},
			{title: "<?php echo app_lang('hr_workplace_address') ?>", "class": "w30p"},
			{title: "<?php echo app_lang('hr_latitude_lable') ?>"},
			{title: "<?php echo app_lang('hr_longitude_lable') ?>"},
			{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
			],
			printColumns: [0, 1, 2, 3, 4],
			xlsColumns: [0, 1, 2, 3, 4]
		});
	});

</script>