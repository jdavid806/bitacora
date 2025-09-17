<script> 
/*load table*/
$(document).ready(function () {
	'use strict';
	
	var id = $('input[name="id"]').val();
	$("#evaluation_criteria_form-table").appTable({
		source: '<?php echo get_uri("recruitment/list_evaluation_form_detail_data/") ?>'+id,
		order: [[0, 'desc']],
		filterDropdown: [
		],
		columns: [
		{title: "<?php echo app_lang('group_criteria') ?>"},
		{title: "<i data-feather='menu' class='icon-16'></i>", "class": "text-center option w100"}
		],
		printColumns: [0, 1, 2, 3, 4],
		xlsColumns: [0, 1, 2, 3, 4]
	});
});

</script>
