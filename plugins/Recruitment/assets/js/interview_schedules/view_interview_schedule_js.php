<script>
	$("#wh_enter_activity").on('click', function() {
		"use strict"; 

		var message = $('#wh_activity_textarea').val();
		var interview_schedule_id = $('input[name="_attachment_sale_id"]').val();

		if (message === '') { return; }

		$.post("<?php echo get_uri("recruitment/re_add_activity") ?>", {
			interview_schedule_id: interview_schedule_id,
			activity: message,
			rel_type: 'rec_interview',
		}).done(function(response) {
			response = JSON.parse(response);
			if(response.status == true){
				appAlert.success(response.message);
			}else{
				appAlert.warning(response.message);

			}
		}).fail(function(data) {
				appAlert.warning(response.message);
		});
	});

	function delete_wh_activitylog(wrapper, id) {
		"use strict"; 

		if (confirm_delete()) {
			requestGetJSON('delete_activitylog/' + id).done(function(response) {
				if (response.success === true || response.success == 'true') { $(wrapper).parents('.feed-item').remove(); }
			}).fail(function(data) {
				appAlert.warning(data.responseText);
			});
		}
	}
</script>