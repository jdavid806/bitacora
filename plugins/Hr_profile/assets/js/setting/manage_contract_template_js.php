<script> 
	/*load table*/
	$(document).ready(function () {
		'use strict';
		
		$("#contract_template-table").appTable({
			source: '<?php echo get_uri("hr_profile/list_contract_template_data") ?>',
			order: [[0, 'desc']],
			filterDropdown: [
			],
			columns: [
			{title: "<?php echo app_lang('hr_contract_name') ?> ", "class": "w30p"},
			{title: "<?php echo app_lang('hr_hr_job_position') ?>", "class": "w30p"},
			{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
			],
			printColumns: [0, 1, 2, 3, 4],
			xlsColumns: [0, 1, 2, 3, 4]
		});
	});

</script>