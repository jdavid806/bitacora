<script>
	$(document).ready(function () {
		$("#staff_quitting_work_form .select2").select2();
		setDatePicker("#dateoff");

	});

	$(function(){
		'use strict';

		<?php if(isset($detail)){ ?>
			var id = <?php echo html_entity_decode($detail); ?>;
			detail_checklist_staff_notify(id);
		<?php } ?>	
		$('#btn_new_staff').on('click',function(){
			$('#new_staff').modal('show');
		})


		var StaffServerParams={};
		var table_resignation_procedures = $('table.table-table_resignation_procedures');

		initDataTable(table_resignation_procedures,"<?php echo get_uri("hr_profile/table_resignation_procedures") ?>",[0], [0], StaffServerParams, [0, 'desc']);

		/*hide first column*/
		var hidden_columns = [1];
		$('.table-table_resignation_procedures').DataTable().columns(hidden_columns).visible(false, false);

		$('.table-table_resignation_procedures').DataTable().on('draw', function() {
			var rows = $('.table-table_resignation_procedures').find('tr');
			$.each(rows, function() {
				var td = $(this).find('td').eq(6);//order column, only colum display
				var percent = $(td).find('input[name="percent"]').val();
				$(td).find('.goal-progress').circleProgress({
					value: percent,
					size: 45,
					animation: false,
					fill: {
						gradient: ["#28b8da", "#059DC1"]
					}
				})
			})
		})

		$('#finish_btn').on('click',function(){
			$('input[name="finish"]').val(1);
		});


	});

	function detail_checklist_staff(el){
		'use strict';

		$('#detail_checklist_staff').modal('toggle');
		var staffid = $(el).data('id');
		
		$.post("<?php echo get_uri("hr_profile/set_data_detail_staff_checklist_quit_work/") ?>"+staffid).done(function(response) {
			response = JSON.parse(response);
			$('.content-modal-details').html(response.result);
		})
	}

	function detail_checklist_staff_notify(staffid){
		'use strict';

		$('#detail_checklist_staff').modal('toggle');

		$.post("<?php echo get_uri("hr_profile/set_data_detail_staff_checklist_quit_work/") ?>"+staffid).done(function(response) {
			response = JSON.parse(response);
			$('.content-modal-details').html(response.result);
		})
	}

	function update_status_quit_work(el){
		'use strict';

		var data ={};
		data.staffid = $(el).attr('id');
		data.id = $(el).attr('resignation_id');

		$.post("<?php echo get_uri("hr_profile/update_status_quit_work") ?>", data).done(function(response) {
			response = JSON.parse(response);
			if(response.status == 0){
				$(el).attr('checked',true);
				$(el).attr('disabled',true);
				appAlert.success(response.message);
			}else{
				appAlert.warning(response.message);
			}

			$('table.table-table_resignation_procedures').DataTable().ajax.reload(null, false);
		})
	}

	$("#staffid").on('change', function() {
		'use strict';

		var staff_id = $('select[name="staffid"]').val();
		if(staff_id != ''){
			$.get("<?php echo get_uri("hr_profile/get_staff_info_of_resignation_procedures/") ?>"+ staff_id).done(function(response){
				response = JSON.parse(response);
				
				if(response.status == true || response.status == 'true'){
					$('form').find('input[name="email"]').val(response.staff_email);
					$('form').find('input[name="department_name"]').val(response.staff_department_name);
					$('form').find('input[name="role_name"]').val(response.staff_job_position);
				}else{
					appAlert.warning(response.message);
				}

			}).fail(function(data) {
				appAlert.warning(data.responseText);
			});
		}else{
			$('form').find('input[name="email"]').val('');
			$('form').find('input[name="department_name"]').val('');
			$('form').find('input[name="role_name"]').val('');
			$('form').find('input[name="dateoff"]').val('');
		}
	});

	function staff_bulk_actions(){
		'use strict';

		$('#table_resignation_procedures_bulk_actions').modal('show');
	}

	/*Leads bulk action*/
	function staff_delete_bulk_action(event) {
		'use strict';

		var mass_delete = $('#mass_delete').prop('checked');

		if(mass_delete == true){
			var ids = [];
			var data = {};

			data.mass_delete = true;
			data.rel_type = 'hrm_resignation_procedures';

			var rows = $('#table-table_resignation_procedures').find('tbody tr');
			$.each(rows, function() {
				var checkbox = $($(this).find('td').eq(0)).find('input');
				if (checkbox.prop('checked') === true) {
					ids.push(checkbox.val());
				}
			});

			data.ids = ids;
			$(event).addClass('disabled');
			setTimeout(function() {

				$.post("<?php echo get_uri("hr_profile/hrm_delete_bulk_action") ?>", data).done(function() {
					window.location.reload();
				}).fail(function(data) {
					$('#table_resignation_procedures_bulk_actions').modal('hide');
					appAlert.warning(data.responseText);
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