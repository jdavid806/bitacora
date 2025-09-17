
<script>
	$(document).ready(function () {
		$(".select2").select2();
	});

	"use strict";
	var InvoiceServerParams={};

	var payslip_template_table = $('.table-payslip_template_table');

	initDataTable(payslip_template_table, "<?php echo get_uri("hr_payroll/payslip_template_table") ?>",[0],[0], InvoiceServerParams, [0 ,'desc']);

	$('#date_add').on('change', function() {
		payslip_template_table.DataTable().ajax.reload().columns.adjust().responsive.recalc();
	});

	var hidden_columns = [0];
	$('.table-payslip_template_table').DataTable().columns(hidden_columns).visible(false, false);


	function new_payslip_template(){
		"use strict";

		$('#payslip_template_modal').modal('show');
		$('.edit-title').addClass('hide');
		$('.add-title').removeClass('hide');
		$('#additional_payslip_template').html('');
		$('#additional_payslip_column').html('');

		$('#add_payslip_template input[name="templates_name"]').val('');

		$('#add_payslip_template select[name="department_id[]"]').val('').change();
		$('#add_payslip_template select[name="role_employees[]"]').val('').change();
		$('#add_payslip_template select[name="staff_employees[]"]').val('').change();
		$('#add_payslip_template select[name="except_staff[]"]').val('').change();


		var id = '';
		requestGetJSON('get_payslip_template/' + id).done(function (response) {
			$("select[id='payslip_id_copy']").html('');
			$("select[id='payslip_id_copy']").append(response.payslip_template_selected);

			$("#add_payslip_template select[id='payslip_id_copy']").select2('destroy');
			$("#add_payslip_template select[id='payslip_id_copy']").select2();

			$("select[id='payslip_columns']").html('');
			$("select[id='payslip_columns']").append(response.payslip_column_selected);
			$("#add_payslip_template select[id='payslip_columns']").select2('destroy');
			$("#add_payslip_template select[id='payslip_columns']").select2();

		});

	}

	function edit_payslip_template(invoker,id){
		"use strict";

		$('#additional_payslip_template').html('');
		$('#additional_payslip_column').html('');

		requestGetJSON('get_payslip_template/' + id).done(function (response) {

			$('#additional_payslip_template').append(hidden_input('id',id));

			$('#add_payslip_template input[name="templates_name"]').val(response.payslip_template_data.templates_name);
			$('#add_payslip_template select[name="manager_id"]').val(response.payslip_template_data.manager_id).change();
			$('#add_payslip_template select[name="follower_id"]').val(response.payslip_template_data.follower_id).change();

			$("select[id='payslip_id_copy']").html('');
			$("select[id='payslip_id_copy']").append(response.payslip_template_selected);
			$("#add_payslip_template select[id='payslip_id_copy']").select2('destroy');
			$("#add_payslip_template select[id='payslip_id_copy']").select2();

			$("select[id='payslip_columns']").html('');
			$("select[id='payslip_columns']").append(response.payslip_column_selected);
			$("#add_payslip_template select[id='payslip_columns']").select2('destroy');
			$("#add_payslip_template select[id='payslip_columns']").select2();

			var department_id_str = response.payslip_template_data.department_id;
			if(typeof(department_id_str) == "string"){
				$('#add_payslip_template select[name="department_id[]"]').val( (department_id_str).split(',')).change();
			}else{
				$('#add_payslip_template select[name="department_id[]"]').val(department_id_str).change();
			}

			var role_id_str = response.payslip_template_data.role_employees;
			if(typeof(role_id_str) == "string"){
				$('#add_payslip_template select[name="role_employees[]"]').val( (role_id_str).split(',')).change();
			}else{
				$('#add_payslip_template select[name="role_employees[]"]').val(role_id_str).change();
			}

			var staff_id_str = response.payslip_template_data.staff_employees;
			if(typeof(staff_id_str) == "string"){
				$('#add_payslip_template select[name="staff_employees[]"]').val( (staff_id_str).split(',')).change();
			}else{
				$('#add_payslip_template select[name="staff_employees[]"]').val(staff_id_str).change();
			}

			var expect_staff_id_str = response.payslip_template_data.except_staff;
			if(typeof(expect_staff_id_str) == "string"){
				$('#add_payslip_template select[name="except_staff[]"]').val( (expect_staff_id_str).split(',')).change();
			}else{
				$('#add_payslip_template select[name="except_staff[]"]').val(expect_staff_id_str).change();
			}

		});

		$('#payslip_template_modal').modal('show');
		$('.add-title').addClass('hide');
		$('.edit-title').removeClass('hide');
	}


//check edit column display when edit
$('.payslip_template_checked').on('click', function(event) {

	"use strict";
	
	var templates_name = $("body").find(' input[name="templates_name"]').val();
	var payslip_columns = $("body").find('select[id="payslip_columns"]').val();

	if ((templates_name !== '' && payslip_columns.length > 0) ) {
		var data={};
		data.department_ids = $("select[id='department_id']").val();
		data.role_ids = $("select[id='role_employees']").val();
		data.staff_ids = $("select[id='staff_employees']").val();
		data.expect_staff_ids = $("select[id='except_staff']").val();
		data.id = $('input[name="id"]').val();

		$(event).attr( "disabled", "disabled" );

		$.post("<?php echo get_uri("hr_payroll/payslip_template_checked") ?>", data).done(function(response) {
			response = JSON.parse(response);

			if (response.status === true || response.status == 'true') {
				$('#add_payslip_template').submit()
			} else {
				$(event).removeAttr('disabled')
				appAlert.warning(response.staff_name);

			}
		});
	}

});


</script>