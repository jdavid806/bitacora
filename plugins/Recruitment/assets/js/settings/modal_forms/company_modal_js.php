<script>

	$(document).ready(function () {
		"use strict";
		$("#add_company-form .select2").select2();

		$("#add_company-form").appForm({
			ajaxSubmit: false,
			onSuccess: function (result) {
				if (window.refreshAfterUpdate) {
					window.refreshAfterUpdate = false;
					location.reload();
				} else {
					$("#company-table").appTable({newData: result.data, dataId: result.id});
				}
			}
		});

	});

	function delete_company_attachment(wrapper, id) {
		"use strict";

		$.get("<?php echo get_uri("recruitment/delete_company_file/") ?>" + id, function (response) {
			if (response.success == true) {
				$(wrapper).parents('.dz-preview').remove();

				var totalAttachmentsIndicator = $('.dz-preview'+id);
				var totalAttachments = totalAttachmentsIndicator.text().trim();

				if(totalAttachments == 1) {
					totalAttachmentsIndicator.remove();
				} else {
					totalAttachmentsIndicator.text(totalAttachments-1);
				}
				appAlert.success("<?php echo app_lang('delete_company_file_success') ?>");

				$('.profile-image-upload-group').removeClass('hide');
				$('.profile-image-group').addClass('hide');
			} else {
				appAlert.warning("<?php echo app_lang('delete_company_file_false') ?>");

			}
		}, 'json');
		return false;
	}

</script>