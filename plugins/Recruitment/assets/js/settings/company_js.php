<script> 
/*load table*/
$(document).ready(function () {
	'use strict';
	
	$("#company-table").appTable({
		source: '<?php echo get_uri("recruitment/list_company_data") ?>',
		order: [[0, 'desc']],
		filterDropdown: [
		],
		columns: [
		{title: "<?php echo app_lang('company_name') ?> ", "class": "w20p"},
		{title: "<?php echo app_lang('company_address') ?>"},
		{title: "<?php echo app_lang('company_industry') ?>"},
		{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
		],
		printColumns: [0, 1, 2, 3, 4],
		xlsColumns: [0, 1, 2, 3, 4]
	});
});

</script>
