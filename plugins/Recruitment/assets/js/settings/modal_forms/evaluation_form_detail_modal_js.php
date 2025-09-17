<script type="text/javascript">

	(function($) {
		"use strict";  

		var addnewkpi;  
		$(".evaluation_criteria_class .select2").select2();
		$(".group_criteria_class .select2").select2();
		
		addnewkpi = $('.new-kpi-al').children().length;
		<?php if(isset($total_eval)){ ?>
			addnewkpi = <?php echo html_entity_decode($total_eval); ?>;
		<?php } ?>

		$("body .<?php echo html_entity_decode($time); ?>").on('click', '.new_kpi', function() {
			var data_select = {};
			data_select.count = addnewkpi;
			data_select.class = 'danger';
			data_select.class_btn = 'remove_kpi';
			data_select.i = 'x';
			data_select.group_criteria = $('select[name="group_criteria"]').val();

			$.get("<?php echo get_uri("recruitment/get_evaluation_from_criteria_sample") ?>",data_select).done(function(response){
				response = JSON.parse(response);
				$('.new-kpi-al').append(response.html);

				$('.select_candidate_class2 .select2').select2('destroy');
				$('.select_candidate_class2 .select2').select2();

			});
			addnewkpi++;
		});

		$("body").on('click', '.remove_kpi', function() {
			$(this).parents('#new_kpi').remove();
		});

	})(jQuery);

	function group_criteria_change(invoker){
		"use strict"; 
		var result = invoker.name.match(/\d/g);
		

		$.post("<?php echo get_uri("recruitment/get_criteria_by_group/") ?>"+invoker.value).done(function(response) {
			response = JSON.parse(response);
			$('select[name="evaluation_criteria[0]"').html('');
			$('select[name="evaluation_criteria[0]"').append(response.html);

		});
	}
</script>