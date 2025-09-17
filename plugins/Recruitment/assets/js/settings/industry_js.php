<script> 
/*load table*/
$(document).ready(function () {
	'use strict';
	
	$("#industry-table").appTable({
		source: '<?php echo get_uri("recruitment/list_industry_data") ?>',
		order: [[0, 'desc']],
		filterDropdown: [
		],
		columns: [
		{title: "<?php echo app_lang('industry_name') ?>"},
		{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
		],
		printColumns: [0, 1, 2, 3, 4],
		xlsColumns: [0, 1, 2, 3, 4]
	});
});

</script>
