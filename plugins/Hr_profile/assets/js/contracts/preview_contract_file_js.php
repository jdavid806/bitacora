<script>
	/*contract preview file*/
	function preview_file_staff(invoker){
		'use strict';
		
		var id = $(invoker).attr('id');
		var rel_id = $(invoker).attr('rel_id');
		view_hrmstaff_file(id, rel_id);
	}

	var contract_id = '<?php echo html_entity_decode($contracts->id_contract); ?>';

	function save_contract_content(manual) {
		'use strict';
		
		var editor = tinyMCE.activeEditor;
		var data = {};
		data.contract_id = contract_id;
		data.content = editor.getContent();


		$.post("<?php echo get_uri("hr_profile/save_hr_contract_data") ?>", data).done(function (response) {
			response = JSON.parse(response);
			if (typeof (manual) != 'undefined') {

				/*Show some message to the user if saved via CTRL + S*/
				appAlert.success(response.message);


			}
			/*Invokes to set dirty to false*/
			editor.save();
		}).fail(function (error) {
			var response = JSON.parse(error.responseText);
			appAlert.warning(response.message);
		});
	}

</script>