<script>
	var purchase;
	var purchase_value;

	$(function() {
		'use strict';

		$("#staff-contract-form .select2").select2();
		setDatePicker("#start_valid, #end_valid, #sign_day");
	});
	

	function manage_contract_type(form) {
		'use strict';

		var data = $(form).serialize();
		var url = form.action;
		$.post(url, data).done(function(response){
			response = JSON.parse(response);
			if(response.success == true){
				appAlert.success(response.message);

				if($('body').hasClass('contract') && typeof(response.id) != 'undefined') {
					var ctype = $('#contract_forms');
					ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
					ctype.selectpicker('val',response.id);
					ctype.selectpicker('refresh');
				}
			}
			$('#form').modal('hide')
		});
		return false;
	}


	(function($) {
    "use strict";  

	/*+ button for adding more attachments*/
	var addMoreAttachmentsInputKey = 1;
	/*button for adding more attachment in project*/
	$("body").on('click', '.add_more_attachments_file', function() {
		'use strict';
		

	    if ($(this).hasClass('disabled')) {
	        return false;
	    }

	    var total_attachments = $('.attachments input[name*="file"]').length;
	    if ($(this).data('max') && total_attachments >= $(this).data('max')) {
	        return false;
	    }

	    var newattachment = $('.attachments').find('.attachment').eq(0).clone().appendTo('.attachments');
	    newattachment.find('input').removeAttr('aria-describedby aria-invalid');
	    newattachment.find('input').attr('name', 'file[' + addMoreAttachmentsInputKey + ']').val('');
	    
	    newattachment.find('.add_more_attachments_file svg').removeClass('feather-plus-circle').addClass('feather-x');
		newattachment.find('.add_more_attachments_file svg').html('');
		newattachment.find('.add_more_attachments_file svg').html('<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>');

	    newattachment.find('.input-group-btn button').removeClass('add_more_attachments_file').addClass('remove_attachment_file').removeClass('btn-success').addClass('btn-danger');
	    addMoreAttachmentsInputKey++;
	});


	/*Remove attachment*/
	$("body").on('click', '.remove_attachment_file', function() {
		'use strict';

		$(this).parents('.attachment').remove();
	}); 

	/*disabled input jobposition*/
	$( "#job_position" ).prop( "disabled", true );
	$("#staff_delegate").change(function(){
		'use strict';

		var formData = new FormData();
		formData.append("rise_csrf_token", $('input[name="rise_csrf_token"]').val());
		formData.append("id", $(this).children("option:selected").val());
		$.ajax({ 

			url: "<?php echo get_uri("hr_profile/get_staff_role") ?>", 
			method: 'post', 
			data: formData, 
			contentType: false, 
			processData: false
		}).done(function(response) {
			response = JSON.parse(response);
			if(response.name != null ){
				$('#job_position').val(response.name);
			}
		});
		return false;

	});

	})(jQuery); 



	/*function delete contract attachment file */
	function delete_contract_attachment(wrapper, id) {
		'use strict';


		$.get("<?php echo get_uri("hr_profile/delete_hrm_contract_attachment_file/") ?>" + id, function (response) {
			if (response.success == true) {
				$(wrapper).parents('.contract-attachment-wrapper').remove();

				var totalAttachmentsIndicator = $('.attachments-indicator');
				var totalAttachments = totalAttachmentsIndicator.text().trim();
				if(totalAttachments == 1) {
					totalAttachmentsIndicator.remove();
				} else {
					totalAttachmentsIndicator.text(totalAttachments-1);
				}
			} else {
				appAlert.warning(response.message);
			}
		}, 'json');
		return false;
	}

	/*contract preview file*/
	function preview_file_staff(invoker){
		'use strict';

		var id = $(invoker).attr('id');
		var rel_id = $(invoker).attr('rel_id');
		view_hrmstaff_file(id, rel_id);
	}

	/* function view hrm_file*/
	function view_hrmstaff_file(id, rel_id) {   
		'use strict';

		$('#contract_file_data').empty();

		$("#contract_file_data").load("<?php echo get_uri("hr_profile/hrm_file_contract/") ?>" + id + '/' + rel_id, function(response, status, xhr) {
			if (status == "error") {
				appAlert.warning(xhr.statusText);
			}
		});
	}



	$(document).ready(function () {

		"use strict";  


		<?php if(!isset($contracts)){ ?>
			var warehouses ={};
			/*hansometable for purchase*/
			var row_global;
			var dataObject_pu = [];
			var hotElement1 = document.getElementById('staff_contract_hs');

			purchase = new Handsontable(hotElement1, {
				licenseKey: 'non-commercial-and-evaluation',

				contextMenu: true,
				manualRowMove: true,
				manualColumnMove: true,
				stretchH: 'all',
				autoWrapRow: true,
				rowHeights: 30,
				defaultRowHeight: 100,
				minRows: 10,
				maxRows: 40,
				width: '100%',
				height: 400,


				rowHeaders: true,
				colHeaders: true,
				autoColumnSize: {
					samplingRatio: 23
				},

				filters: true,
				manualRowResize: true,
				manualColumnResize: true,
				allowInsertRow: true,
				allowRemoveRow: true,
				columnHeaderHeight: 40,

				colWidths: [40, 120,50,50, 100,100],
				rowHeights: 30,
				rowHeaderWidth: [44],
				minSpareRows: 1,
				hiddenColumns: {
					columns: [0],
					indicators: true
				},

				columns: [
				{
					type: 'text',
					data: 'type',
					renderer: customDropdownRenderer,
					editor: "chosen",
					chosenOptions: {
						data: <?php echo json_encode($types); ?>
					},
					readOnly: true
				},
				{
					type: 'text',
					data: 'rel_type',
					renderer: customDropdownRenderer,
					editor: "chosen",
					chosenOptions: {
						data: <?php echo json_encode($salary_allowance_type); ?>
					}

				},
				{
					type: 'numeric',
					data: 'rel_value',
					numericFormat: {
						pattern: '0,00',
					},
				},

				{
					type: 'date',
					data: 'since_date',
					dateFormat: 'YYYY-MM-DD',
					correctFormat: true,
					defaultDate: "<?php echo format_to_date(get_my_local_time('Y-m-d')) ?>"
				},

				{
					type: 'text',
					data: 'contract_note',
				},

				],

				colHeaders: [
				'<?php echo _l('hr_hr_contract_type'); ?>',
				'<?php echo _l('hr_hr_contract_rel_type'); ?>',
				'<?php echo _l('hr_hr_contract_rel_value'); ?>',
				'<?php echo _l('hr_start_month'); ?>',
				'<?php echo _l('note'); ?>',

				],

				data: dataObject_pu,
			});

		<?php }else{ ?>


			<?php if(isset($contract_details)){?>
				var dataObject_pu = <?php echo html_entity_decode($contract_details); ?>;
			<?php }else{ ?>
				var dataObject_pu = [];
			<?php } ?>

			var warehouses ={};
			/*hansometable for purchase*/
			var row_global;
			var hotElement1 = document.getElementById('staff_contract_hs');

			purchase = new Handsontable(hotElement1, {
				licenseKey: 'non-commercial-and-evaluation',

				contextMenu: true,
				manualRowMove: true,
				manualColumnMove: true,
				stretchH: 'all',
				autoWrapRow: true,
				rowHeights: 30,
				defaultRowHeight: 100,
				minRows: 10,
				maxRows: 40,
				width: '100%',
				height: 400,

				rowHeaders: true,
				colHeaders: true,
				autoColumnSize: {
					samplingRatio: 23
				},

				filters: true,
				manualRowResize: true,
				manualColumnResize: true,
				allowInsertRow: true,
				allowRemoveRow: true,
				columnHeaderHeight: 40,

				colWidths: [40, 120,50,50, 100,100],
				rowHeights: 30,
				rowHeaderWidth: [44],
				minSpareRows: 1,
				hiddenColumns: {
					columns: [0,5,6],
					indicators: true
				},

				columns: [
				{
					type: 'text',
					data: 'type',
					renderer: customDropdownRenderer,
					editor: "chosen",
					chosenOptions: {
						data: <?php echo json_encode($types); ?>
					},
					readOnly: true
				},
				{
					type: 'text',
					data: 'rel_type',
					renderer: customDropdownRenderer,
					editor: "chosen",
					chosenOptions: {
						data: <?php echo json_encode($salary_allowance_type); ?>
					}

				},
				{
					type: 'numeric',
					data: 'rel_value',
					numericFormat: {
						pattern: '0,00',
					},
				},

				{
					type: 'date',
					data: 'since_date',
					dateFormat: 'YYYY-MM-DD',
					correctFormat: true,
					defaultDate: "<?php echo format_to_date(get_my_local_time('Y-m-d')) ?>"
				},

				{
					type: 'text',
					data: 'contract_note',
				},
				{
					type: 'text',
					data: 'contract_detail_id',
				},
				{
					type: 'text',
					data: 'staff_contract_id',
				},


				],

				colHeaders: [
				'<?php echo _l('hr_hr_contract_type'); ?>',
				'<?php echo _l('hr_hr_contract_rel_type'); ?>',
				'<?php echo _l('hr_hr_contract_rel_value'); ?>',
				'<?php echo _l('hr_start_month'); ?>',
				'<?php echo _l('note'); ?>',

				],

				data: dataObject_pu,
			});

		<?php } ?>



		purchase_value = purchase;
		purchase.addHook('afterChange', function(changes, src) {
			"use strict";

			if(changes !== null){
				changes.forEach(([row, col, prop, oldValue, newValue]) => {

					if(col == 'rel_type' && oldValue != ''){

						$.post("<?php echo get_uri("hr_profile/get_salary_allowance_value/") ?>"+ oldValue).done(function(response) {
							response = JSON.parse(response);

							purchase.setDataAtCell(row,0, response.type);
							purchase.setDataAtCell(row,2, response.rel_value);
							purchase.setDataAtCell(row,3, response.effective_date);
						});
					}

					if(col == 'rel_type' && oldValue == null){
						console.log('row1', row);
						console.log('col1', col);
						console.log('prop1', prop);
						console.log('oldValue1', oldValue);
						console.log('newValue1', newValue);
						purchase.setDataAtCell(row,0,'');
						purchase.setDataAtCell(row,2,'');
						purchase.setDataAtCell(row,3,'');
					}

				});
			}
		});


	});

function customDropdownRenderer(instance, td, row, col, prop, value, cellProperties) {
	"use strict";
	var selectedId;
	var optionsList = cellProperties.chosenOptions.data;
	
	if(typeof optionsList === "undefined" || typeof optionsList.length === "undefined" || !optionsList.length) {
		Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
		return td;
	}

	var values = (value + "").split("|");
	value = [];
	for (var index = 0; index < optionsList.length; index++) {

		if (values.indexOf(optionsList[index].id + "") > -1) {
			selectedId = optionsList[index].id;
			value.push(optionsList[index].label);
		}
	}
	value = value.join(", ");

	Handsontable.cellTypes.text.renderer(instance, td, row, col, prop, value, cellProperties);
	return td;
}


$('.add_goods_receipt').on('click', function() {
	'use strict';
	
	var valid_contract = $('#staff_contract_hs').find('.htInvalid').html();
	var name_contract = $('select[name="name_contract"]').val();
	var staff = $('select[name="staff"]').val();

	if(valid_contract){
		appAlert.warning("<?php echo _l('data_must_number') ; ?>");

	}else if(name_contract.length == 0){
		appAlert.warning("<?php echo _l('hr_please_select_Contract_type') ; ?>");

	}else if(staff.length == 0){
		appAlert.warning("<?php echo _l('hr_please_select_Staff_name') ; ?>");

	}else{

		$('input[name="staff_contract_hs"]').val(JSON.stringify(purchase_value.getData()));   
		$('#staff-contract-form').submit(); 

	}
});


</script>