<script>

	$(function(){
		'use strict';
		setDatePicker("#training_time_from, #training_time_to");

		var ContractsServerParams = {
				"staff_id": "[name='memberid']",
			};
		
		var table_education = $('table.table-table_education');
		initDataTable(table_education, "<?php echo get_uri("hr_profile/table_education") ?>", [0], [0], ContractsServerParams, [0, 'desc']);

		$('form.save_update_education').on('submit', function (e) {
		'use strict';

			e.preventDefault();
			var data=$('form.save_update_education').serialize();
			var training_programs_name = $('input[name="training_programs_name"]').val();
			var training_places = $('input[name="training_places"]').val();
			var training_time_from = $('input[name="training_time_from"]').val();
			var training_time_to = $('input[name="training_time_to"]').val();

			if(training_programs_name != '' && training_places != '' && training_time_from != '' && training_time_to != ''){
				$('#education_sidebar').modal('hide');

				$.post("<?php echo get_uri("hr_profile/save_update_education") ?>",data).done(function(response){
					response = JSON.parse(response);
					if(response.success == true) {
						appAlert.success(response.message);
						table_education.DataTable().ajax.reload();
					}
					else{
						appAlert.warning(response.message);
						table_education.DataTable().ajax.reload();
					}
				});
			}
		});

	});


	function create_trainings(){
		'use strict';
		$('#education_sidebar').modal('show');
		$('input[name="id"]').val('');
		$('input[name="training_programs_name"]').val('');
		$('input[name="training_places"]').val('');
		$('input[name="training_time_from"]').val('');
		$('input[name="training_time_to"]').val('');
		$('textarea[name="training_result"]').val('');
		$('input[name="degree"]').val('');
		$('textarea[name="notes"]').val('');
		$('.education_sidebar').addClass('sidebar-open');
		$('.edit-title-training').hide();
		$('.add-title-training').show();
	}


	function delete_education(el){
		'use strict';
		var id = $(el).data('id');
		var table_education = $('table.table-table_education');

		$.post("<?php echo get_uri("hr_profile/delete_education") ?>",{'id':id}).done(function(response){
			response = JSON.parse(response);
			if(response.success == true) {
				appAlert.success(response.message);
				table_education.DataTable().ajax.reload();
			}
			else{
				appAlert.warning(response.message);
				table_education.DataTable().ajax.reload();
			}
		});
	}

	function update_education(el){
		'use strict';
		$('#education_sidebar').modal('show');
		var id = $(el).data('id');
		$('input[name="id"]').val(id);
		$('input[name="training_programs_name"]').val($(el).data('name_programe'));
		$('input[name="training_places"]').val($(el).data('training_pl'));
		$('input[name="training_time_from"]').val($(el).data('time_from'));
		$('input[name="training_time_to"]').val($(el).data('time_to'));
		$('input[name="degree"]').val($(el).data('degree'));
		$('textarea[name="notes"]').val($(el).data('notes'));
		$('textarea[name="training_result"]').val($(el).data('result'));
		$('.education_sidebar').addClass('sidebar-open');
		$('.edit-title-training').show();
		$('.add-title-training').hide();
	}

$('.trainingtable').dataTable( {
	 'destroy': true,
		"ordering": false
	} );
</script>