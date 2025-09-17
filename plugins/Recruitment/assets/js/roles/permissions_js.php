
<script type="text/javascript">
	$(document).ready(function () {
        "use strict"
		
		$("#permissions-form").appForm({
			isModal: false,
			onSuccess: function (result) {
				appAlert.success(result.message, {duration: 10000});
			}
		});

	});
</script>