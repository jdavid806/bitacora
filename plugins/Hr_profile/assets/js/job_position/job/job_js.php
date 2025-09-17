
<script type="text/javascript">
	$(document).ready(function () {
		$(".select2").select2();
	});

	(function(){
		'use strict';

		var ContractsServerParams = {
			"department_id": "[name='department_id[]']",
			"job_position_id"    : "[name='job_position_id[]']"
		};

		var table_job = $('.table-table_job');

		initDataTable(table_job, "<?php echo get_uri("hr_profile/table_job") ?>", [0], [0], ContractsServerParams, [0, 'desc']);

		//hide first column
		var hidden_columns = [];
		$('.table-table_job').DataTable().columns(hidden_columns).visible(false, false);

		$('#department_id').on('change', function() {
			table_job.DataTable().ajax.reload();
		});
		$('#job_position_id').on('change', function() {
			table_job.DataTable().ajax.reload();
		});

	})(jQuery);

	function new_job_p(){
		'use strict';

		$('#additional_job').empty();
		$('.edit-title').addClass('hide');
		$('.add-title').removeClass('hide');
		$('#job_p input[name="job_name"]').val('');
		$('#job_p').modal('show');
		$('#job_p textarea[name="description"]').val('');

		
		$('#_create_job_position_default').removeClass('hide');
		$('input[name="create_job_position"]').prop('checked',true); 
	}

	function edit_job_p(invoker,id){
		'use strict';

		$('input[name="create_job_position"]').prop('checked',false); 


		$('#_create_job_position_default').addClass('hide');
		$('#additional_job').append(hidden_input('id',id));
		$('#job_p input[name="job_name"]').val($(invoker).data('name'));

		$.post("<?php echo get_uri("hr_profile/get_job_p_edit/") ?>"+id).done(function(response) {
			response = JSON.parse(response);
			$('#job_p textarea[name="description"]').val(response.description);

		});
		$('.add-title').addClass('hide');
		$('.edit-title').removeClass('hide');
		$('#job_p').modal('show');
	}


	/*get jobposition in department by staff in department*/

	function department_change(invoker){
		'use strict';

		var data_select = {};
		data_select.department_id = $('select[name="department_id[]"]').val();
		data_select.status = 'true';
		if((data_select.department_id).length == 0){
			data_select.status = 'false';
		}

		$.post("<?php echo get_uri("hr_profile/get_position_by_department") ?>",data_select).done(function(response){
			response = JSON.parse(response);
			$("select[name='job_position_id[]']").html('');

			$("select[name='job_position_id[]']").append(response.job_position);
			$("select[name='job_position_id[]']").selectpicker('refresh');

		});

	}

	function staff_bulk_actions(){
		'use strict';

		$('#table_contract_bulk_actions').modal('show');
	}

	 // Leads bulk action
	 function staff_delete_bulk_action(event) {
		'use strict';

			var mass_delete = $('#mass_delete').prop('checked');

			if(mass_delete == true){
				var ids = [];
				var data = {};

				data.mass_delete = true;
				data.rel_type = 'hrm_job';

				var rows = $('#table-table_job').find('tbody tr');
				$.each(rows, function() {
					var checkbox = $($(this).find('td').eq(0)).find('input');
					if (checkbox.prop('checked') === true) {
						ids.push(checkbox.val());
					}
				});

				data.ids = ids;
				$(event).addClass('disabled');
				setTimeout(function() {

					$.post("<?php echo get_uri("hr_profile/hrm_delete_bulk_action_v2") ?>", data).done(function() {
						window.location.reload();
					}).fail(function(data) {
						$('#table_contract_bulk_actions').modal('hide');
						appAlert.warning( data.responseText);
					});
				}, 200);
			}else{
				window.location.reload();
			}

	 }

	 $("body").on('change', '#mass_select_all', function () {
		'use strict';
	 	
		var to, rows, checked;
		to = $(this).data('to-table');

		rows = $('.table-' + to).find('tbody tr');
		checked = $(this).prop('checked');
		$.each(rows, function () {
			$($(this).find('td').eq(0)).find('input').prop('checked', checked);
		});
	 });

	</script>