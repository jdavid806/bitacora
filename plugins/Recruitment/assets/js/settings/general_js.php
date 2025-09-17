<script> 

	function recruitment_campaign_setting(invoker){
		"use strict";
		var input_name = invoker.value;
		var input_name_status = $('input[id="'+invoker.value+'"]').is(":checked");
		console.log(input_name_status);
		
		var data = {};
		data.input_name = input_name;
		data.input_name_status = input_name_status;
		
		$.post('<?php echo get_uri("recruitment/recruitment_campaign_setting") ?>', data).done(function(response){
			response = JSON.parse(response); 
			if (response.success == true) {
				appAlert.success(response.message);

			}else{
				appAlert.warning(response.message);
			}
		});

	}
</script>