<script>
	$(document).ready(function () {

		initWYSIWYGEditor("#content", {
            height: 700,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'hr', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview']]
            ],
            lang: "<?php echo app_lang('language_locale_long'); ?>"
        });



		// initWYSIWYGEditor("#content", {height: 700});
	});
	
	$(function() {
		'use strict';
		$(".select2").select2();
		
	});

	function save_contract_content(manual) {
		'use strict';
		
		var editor = tinyMCE.activeEditor;
		var data = {};
		data.contract_id = contract_id;
		data.content = editor.getContent();
		$.post(admin_url + 'hr_payroll/save_hr_contract_data', data).done(function (response) {
			response = JSON.parse(response);
			if (typeof (manual) != 'undefined') {

				/*Show some message to the user if saved via CTRL + S*/
				alert_float('success', response.message);

			}
			/*Invokes to set dirty to false*/
			editor.save();
		}).fail(function (error) {
			var response = JSON.parse(error.responseText);
			alert_float('danger', response.message);
		});
	}


</script>