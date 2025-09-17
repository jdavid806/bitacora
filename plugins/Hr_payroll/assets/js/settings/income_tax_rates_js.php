<script>
	var purchase, purchase_value;

	$(document).ready(function () {
		"use strict";  


		<?php if(isset($income_tax_rates)){?>
			var dataObject_pu = <?php echo json_encode($income_tax_rates); ?>;
		<?php }else{ ?>
			var dataObject_pu = [];
		<?php } ?>

		/*hansometable for purchase*/
		var row_global;

		setTimeout(function(){

			var hotElement1 = document.getElementById('incometax_rate_hs');

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
				height: 500,

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

				colWidths: [60, 60,50,50,50],
				rowHeights: 30,
				rowHeaderWidth: [44],
				minSpareRows: 1,
				hiddenColumns: {
					columns: [5],
					indicators: true
				},

				columns: [


				{
					type: 'numeric',
					data: 'tax_bracket_value_from',
					numericFormat: {
						pattern: '0,00',
					},
				},
				{
					type: 'numeric',
					data: 'tax_bracket_value_to',
					numericFormat: {
						pattern: '0,00',
					},
				},

				{
					type: 'numeric',
					data: 'tax_rate',
					numericFormat: {
						pattern: '0,00',
					},
				},

				{
					type: 'numeric',
					data: 'equivalent_value',
					numericFormat: {
						pattern: '0,00',
					},
				},
				{
					type: 'numeric',
					data: 'effective_rate',
					numericFormat: {
						pattern: '0,00',
					},
				},
				{
					type: 'text',
					data: 'id',
				},


				],

				colHeaders: [
				'<?php echo app_lang('tax_bracket_value_from'); ?>',
				'<?php echo app_lang('tax_bracket_value_to'); ?>',
				'<?php echo app_lang('hrp_tax_rate').' %'; ?>',
				'<?php echo app_lang('equivalent_value'); ?>',
				'<?php echo app_lang('effective_rate').' %'; ?>',

				],

				data: dataObject_pu,
			});

			purchase_value = purchase;
			purchase.addHook('afterChange', function(changes, src) {
				"use strict";

				if(changes !== null){
					changes.forEach(([row, col, prop, oldValue, newValue]) => {

						if(col == 'tax_rate' && oldValue != '' && oldValue != 0){
							if(purchase.getDataAtCell(row, 1) != 0){

								if(row == 0){
									purchase.setDataAtCell(row,3, (purchase.getDataAtCell(row, 1)*purchase.getDataAtCell(row, 2)/100).toFixed(2));
									purchase.setDataAtCell(row,4, (purchase.getDataAtCell(row, 1)*purchase.getDataAtCell(row, 2)/purchase.getDataAtCell(row, 1)).toFixed(2));
								}else{
									purchase.setDataAtCell(row,3, (parseFloat((purchase.getDataAtCell(row, 1)-purchase.getDataAtCell(row-1, 1)) * purchase.getDataAtCell(row, 2)/100) + parseFloat(purchase.getDataAtCell(row-1, 3))).toFixed(2));


									purchase.setDataAtCell(row,4, (((parseFloat((purchase.getDataAtCell(row, 1)-purchase.getDataAtCell(row-1, 1)) * purchase.getDataAtCell(row, 2)/100) + parseFloat(purchase.getDataAtCell(row-1, 3)))/purchase.getDataAtCell(row, 1))*100).toFixed(2));
								}
							}

						}

						if(col == 'tax_bracket_value_from' && oldValue != '' && oldValue != 0){
							if(purchase.getDataAtCell(row, 1) != 0 && purchase.getDataAtCell(row, 2) != 0){

								if(row == 0){
									purchase.setDataAtCell(row,3, (purchase.getDataAtCell(row, 1)*purchase.getDataAtCell(row, 2)/100).toFixed(2));
									purchase.setDataAtCell(row,4, (purchase.getDataAtCell(row, 1)*purchase.getDataAtCell(row, 2)/purchase.getDataAtCell(row, 1)).toFixed(2));
								}else{
									purchase.setDataAtCell(row,3, (parseFloat((purchase.getDataAtCell(row, 1)-purchase.getDataAtCell(row-1, 1)) * purchase.getDataAtCell(row, 2)/100) + parseFloat(purchase.getDataAtCell(row-1, 3))).toFixed(2));


									purchase.setDataAtCell(row,4, (((parseFloat((purchase.getDataAtCell(row, 1)-purchase.getDataAtCell(row-1, 1)) * purchase.getDataAtCell(row, 2)/100) + parseFloat(purchase.getDataAtCell(row-1, 3)))/purchase.getDataAtCell(row, 1))*100).toFixed(2));
								}
							}

						}

						if(col == 'tax_bracket_value_to' && oldValue != '' ){

							if(purchase.getDataAtCell(row, 1) != 0 && purchase.getDataAtCell(row, 2) != 0){

								if(row == 0){
									purchase.setDataAtCell(row,3, (purchase.getDataAtCell(row, 1)*purchase.getDataAtCell(row, 2)/100).toFixed(2));
									purchase.setDataAtCell(row,4, (purchase.getDataAtCell(row, 1)*purchase.getDataAtCell(row, 2)/purchase.getDataAtCell(row, 1)).toFixed(2));
								}else{
									purchase.setDataAtCell(row,3, (parseFloat((purchase.getDataAtCell(row, 1)-purchase.getDataAtCell(row-1, 1)) * purchase.getDataAtCell(row, 2)/100) + parseFloat(purchase.getDataAtCell(row-1, 3))).toFixed(2));


									purchase.setDataAtCell(row,4, (((parseFloat((purchase.getDataAtCell(row, 1)-purchase.getDataAtCell(row-1, 1)) * purchase.getDataAtCell(row, 2)/100) + parseFloat(purchase.getDataAtCell(row-1, 3)))/purchase.getDataAtCell(row, 1))*100).toFixed(2));
								}
							}

						}else if(col == 'tax_bracket_value_to' && oldValue == 0){
							purchase.setDataAtCell(row,3, (parseFloat(0)).toFixed(2));
							purchase.setDataAtCell(row,4, (parseFloat(0)).toFixed(2));
						}

					});
				}
			});

		},300);



	});


	$('.add_incometax_rates').on('click', function() {
		'use strict';

		var valid_contract = $('#incometax_rate_hs').find('.htInvalid').html();

		if(valid_contract){
			appAlert.warning("<?php echo app_lang('data_must_number') ; ?>");
		}else{

			$('input[name="incometax_rate_hs"]').val(JSON.stringify(purchase_value.getData()));   
			$('#add_incometax_rates').submit(); 
		}
	});


</script>