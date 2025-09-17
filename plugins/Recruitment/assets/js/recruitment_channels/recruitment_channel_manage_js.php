<script type="text/javascript">
	(function($) {
		"use strict";  
		initDataTable('.table-table_recruitment_channel', "<?php echo get_uri("recruitment/table_recruitment_channel") ?>");
	})(jQuery);


	function duplicate_recruitment_channel(invoker,id){
		"use strict"; 

		$.post("<?php echo get_uri("recruitment/duplicate_recruitment_channel/") ?>"+id).done(function(response) {
			response = JSON.parse(response);

			var table_recruitment_channel = $('table.table-table_recruitment_channel');
			table_recruitment_channel.DataTable().ajax.reload(null, false);

			if(response.status == 'true' || response.status == true){
				appAlert.success(response.message);
				
			}

		});

	}
</script>