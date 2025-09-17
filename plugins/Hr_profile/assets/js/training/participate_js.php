<script>
	$(function(){
		'use strict';

		var survey_fields_required = $('#survey_form').find('[data-required="1"]');
		$.each(survey_fields_required, function() {
			
			var name = $(this).data('for');
			var label = $(this).parents('.form-group').find('[for="' + name + '"]');
			if (label.length > 0) {
				if (label.find('.req').length == 0) {
					label.prepend(' <small class="req text-danger">* </small>');
				}
			}
		});
	});
	
</script>