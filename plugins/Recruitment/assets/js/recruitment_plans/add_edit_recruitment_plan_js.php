<script type="text/javascript">
	$(document).ready(function () {
		"use strict";

		$(".select2").select2();
		setDatePicker("#from_date, #to_date");
		
	});

	/*function delete contract attachment file */
	function delete_proposal_attachment(wrapper, id) {
		'use strict';

		$.get("<?php echo get_uri("recruitment/delete_proposal_attachment/") ?>" + id, function (response) {
			if (response.success == true) {
				$(wrapper).parents('.contract-attachment-wrapper').remove();

				var totalAttachmentsIndicator = $('.attachments-indicator');
				var totalAttachments = totalAttachmentsIndicator.text().trim();
				if(totalAttachments == 1) {
					totalAttachmentsIndicator.remove();
				} else {
					totalAttachmentsIndicator.text(totalAttachments-1);
				}
			} else {
				appAlert.warning(response.message);
			}
		}, 'json');
		return false;
	}
</script>