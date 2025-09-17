<script>
	$(function() {
		'use strict';
		
		$(".select2").select2();

		$('select[name="criteria_type"]').on('change', function () {

			"use strict";
			var criteria = $('select[name="criteria_type"]').val();
			if(criteria == 'criteria'){
				$('select[name="group_criteria"]').attr('required','');
				$('.select_group_criteria').removeClass('hide');
			}else{
				$('select[name="group_criteria"]').removeAttr('required');
				$('.select_group_criteria').addClass('hide');
			}
		});

	});
</script>