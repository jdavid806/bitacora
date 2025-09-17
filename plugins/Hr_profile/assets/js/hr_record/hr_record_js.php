
<script>
	$(function(){
		'use strict';

		$(".select2").select2();

		var StaffServerParams = {
			"status_work": "[name='status_work[]']",
			"hr_profile_deparment": "[name='hr_profile_deparment']",
			"staff_role": "[name='staff_role[]']",
			"staff_teammanage": "input[name='staff_dep_tree']",
		};
		var table_staff = $('table.table-table_staff');

		initDataTable(table_staff,"<?php echo get_uri("hr_profile/table") ?>", [0],[0], StaffServerParams, [1, 'desc']);

		//hide first column
		var hidden_columns = [];
		$('.table-table_staff').DataTable().columns(hidden_columns).visible(false, false);

		$('#hr_profile_deparment').on('change', function() {
			table_staff.DataTable().ajax.reload();
		});
				//staff role
				$('#staff_role').on('change', function() {
					table_staff.DataTable().ajax.reload();
				});

				//combotree filter by team manage
				$('#staff_dep_tree').on('change', function() {
					$('#staff_tree').val(tree_dep.getSelectedItemsId());
					table_staff.DataTable().ajax.reload();
				});

				$('#status_work').on('change', function() {
					table_staff.DataTable().ajax.reload();
				});

			//combotree
			var tree_dep_derpartment = $('#hrm_derpartment_tree').comboTree({
				source : <?php echo html_entity_decode($dep_tree) ?>
			});

			//staff combotree
			var tree_dep = $('#staff_dep_tree').comboTree({
				source : <?php echo html_entity_decode($staff_dep_tree);?>
			});

		})
//staff role end  
function delete_staff_member(id){
	'use strict';
	$('#delete_staff').modal('show');
	$('#transfer_data_to').find('option').prop('disabled',false);
	$('#transfer_data_to').find('option[value="'+id+'"]').prop('disabled',true);
	$('#delete_staff .delete_id input').val(id);
	$('#transfer_data_to').selectpicker('refresh');
}

var nodeTemplate = function(data) { 
	'use strict';

	if(data.name){
		return `
		<div class="staff-chart-background-color">
		${data.image}${data.name}
		</div>
		<div class="content chart_company_name"><span data-feather="loader" class="icon-16"></span> ${data.job_position_name}</div>
		<div class="content"><span data-feather="codepen" class="icon-16"></span> ${data.departmentname}</div>
		`;
	}else{
		return `
		<div class="staff-chart-background-color">
		${data.image}${data.name}
		</div>
		`;
	}
};

//load staff chart
window.onload = function () {
	'use strict';

	var img_dir = '<?php echo get_file_from_setting("site_logo", true); ?>';
	var ds = {
		'image':'<img class="img_logo" src=" '+img_dir+' ">' ,
		'name': '',
		'title': '<p class="title_company"><?php echo get_setting('app_title'); ?></p>',
		'departmentname': '',
		'children': <?php echo html_entity_decode($staff_members_chart); ?>
	};
	var oc = $('#staff_chart').orgchart({
		'data' :ds ,
		'nodeTemplate': nodeTemplate,
		'pan': true,
		'zoom': true,
		nodeContent: "title",
		verticalLevel: 100,
		visibleLevel: 100,
		'toggleSiblingsResp': true,
		'createNode': function(node, data) {
			node.on('click', function(event) {
				if (!$(event.target).is('.edge, .toggleBtn')) {
					var this_obj = $(this);
					var chart_obj = this_obj.closest('.orgchart');
					var newX = window.parseInt((chart_obj.outerWidth(true)/2) - (this_obj.offset().left - chart_obj.offset().left) - (this_obj.outerWidth(true)/2));
					var newY = window.parseInt((chart_obj.outerHeight(true)/2) - (this_obj.offset().top - chart_obj.offset().top) - (this_obj.outerHeight(true)/2));
					chart_obj.css('transform', 'matrix(1, 0, 0, 1, ' + newX + ', ' + newY + ')');
				}
			});
		}
	});
};

function staff_bulk_actions(){
	'use strict';
	$('#table_staff_bulk_actions').modal('show');
}

function staff_delete_bulk_action(event) {
	'use strict';
	var mass_delete = $('#mass_delete').prop('checked');

	if(mass_delete == true){
		var ids = [];
		var data = {};
		data.mass_delete = true;
		data.rel_type = 'hrm_staff';

		var rows = $('#table-table_staff').find('tbody tr');
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
				$('#table_contract_bulk_actions').modal('hide');
				appAlert.warning(data.responseText);
			});
		}, 200);

	}else{
		window.location.reload();
	}
}


function view_staff_chart(){
	'use strict';
	$('#staff_chart_view').modal('show');
}


function staff_export_item(){
	"use strict";
	var ids = [];
	var data = {};

	data.mass_delete = true;
	data.rel_type = 'staff_list';

	var rows = $('#table-table_staff').find('tbody tr');
	$.each(rows, function() {
		var checkbox = $($(this).find('td').eq(0)).find('input');
		if (checkbox.prop('checked') === true) {
			ids.push(checkbox.val());
		}
	});
	data.ids = ids;

	$(event).addClass('disabled');

	if(data.ids.length > 0){
		setTimeout(function() {

			$.post("<?php echo get_uri("hr_profile/create_staff_sample_file") ?>", data).done(function(response) {
				response = JSON.parse(response);
				if(response.success == true){
					appAlert.success("<?php echo _l("create_export_file_success") ?>");


					$('#dowload_items').removeClass('hide');
					$('.hr_export_staff').addClass('hide');

					$('#dowload_items').attr({target: '_blank', 
						href  : response.site_url+'\\'+response.filename});

				}else{
					appAlert.warning("<?php echo _l("create_export_file_fails") ?>");
				}

			}).fail(function(data) {


			});
		}, 200);
	}else{
		appAlert.warning("<?php echo _l("please_select_the_employee_you_want_to_export_to_excel") ?>");
	}

}

$("body").on('change', '#mass_select_all', function () {
	"use strict";

	var to, rows, checked;
	to = $(this).data('to-table');

	rows = $('.table-' + to).find('tbody tr');
	checked = $(this).prop('checked');
	$.each(rows, function () {
		$($(this).find('td').eq(0)).find('input').prop('checked', checked);
	});
});

$("body").on('change', '.onoffswitch input', function (event, state) {
	"use strict";

	var $selector = $(this),
	switch_url = $selector.attr('data-switch-url');
	if (!switch_url) {
		return;
	}

	switch_field(this);
});

        // Switch field make request
        function switch_field(field) {
        	"use strict";

        	var status, url, id;
        	status = 'inactive';
        	if ($(field).prop('checked') === true) {
        		status = 'active';
        	}
        	url = $(field).data('switch-url');
        	id = $(field).data('id');
        	requestGet(url + '/' + id + '/' + status);
        }


</script>