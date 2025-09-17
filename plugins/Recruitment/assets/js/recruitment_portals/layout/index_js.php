<script type='text/javascript'>
	(function($) {
		"use strict";  

		feather.replace();

		<?php
		$session = \Config\Services::session();
		$error_message = $session->getFlashdata("error_message");
		$success_message = $session->getFlashdata("success_message");
		if (isset($error)) {
			echo 'appAlert.error("' . $error . '");';
		}
		if (isset($error_message)) {
			echo 'appAlert.error("' . $error_message . '");';
		}
		if (isset($success_message)) {
			echo 'appAlert.success("' . $success_message . '", {duration: 10000});';
		}
		?>

	})(jQuery);
</script>