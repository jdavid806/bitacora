<script type="text/javascript">
	$(document).ready(function () {
		'use strict';
		
		$(".upload").change(function () {
			if (typeof FileReader == 'function' && !$(this).hasClass("hidden-input-file")) {
				showCropBox(this);
			} else {
				$("#profile-image-form").submit();
			}
		});
		$("#profile_image").change(function () {
			$("#profile-image-form").submit();
		});


		$("#profile-image-form").appForm({
			isModal: false,
			beforeAjaxSubmit: function (data) {
				$.each(data, function (index, obj) {
					if (obj.name === "profile_image") {
						var profile_image = replaceAll(":", "~", data[index]["value"]);
						data[index]["value"] = profile_image;
					}
				});
			},
			onSuccess: function (result) {
				if (typeof FileReader == 'function' && !result.reload_page) {
					appAlert.success(result.message, {duration: 10000});
				} else {
					location.reload();
				}
			}
		});

		setTimeout(function () {
			var tab = "<?php echo html_entity_decode($tab); ?>";
			if (tab === "general") {
				$("[data-bs-target='#tab-general-info']").trigger("click");
			} else if (tab === "account") {
				$("[data-bs-target='#tab-account-settings']").trigger("click");
			} else if (tab === "social") {
				$("[data-bs-target='#tab-social-links']").trigger("click");
			} else if (tab === "job_info") {
				$("[data-bs-target='#tab-job-info']").trigger("click");
			} else if (tab === "my_preferences") {
				$("[data-bs-target='#tab-my-preferences']").trigger("click");
			} else if (tab === "left_menu") {
				$("[data-bs-target='#tab-user-left-menu']").trigger("click");
			}else if (tab === "staff_contract") {
				$("[data-bs-target='#tab-staff-contracts-info']").trigger("click");
			}else if (tab === "staff_dependent") {
				$("[data-bs-target='#tab-staff-dependen-person-info']").trigger("click");
			}else if (tab === "staff_training") {
				$("[data-bs-target='#tab-staff-training-info']").trigger("click");
			}
		}, 210);

	});
</script>