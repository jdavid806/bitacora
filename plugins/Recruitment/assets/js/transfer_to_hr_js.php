<script>
	(function($) {
		"use strict"; 

		$(".select2").select2();
		setDatePicker("#birthday_filter");
		$('#generate_password').on('click', function(){

			$("#password").val(getRndomString(8));
		});

		$('#show_hide_password').on('click', function(){
			
			var $target = $("#password"),
			type = $target.attr("type");
			if (type === "password") {
				$(this).attr("title", "<?php echo app_lang("hide_text"); ?>");
				$(this).html("<span data-feather='eye-off' class='icon-16'></span>");
				feather.replace();
				$target.attr("type", "text");
			} else if (type === "text") {
				$(this).attr("title", "<?php echo app_lang("show_text"); ?>");
				$(this).html("<span data-feather='eye' class='icon-16'></span>");
				feather.replace();
				$target.attr("type", "password");
			}
		});
	})(jQuery); 
</script>