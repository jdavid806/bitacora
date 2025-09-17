<script type="text/javascript">
	(function($) {
		"use strict"; 
		$(".select2").select2();
		setDatePicker("#cp_from_date_filter, #cp_to_date_filter");
		
		var CampaignServerParams = {
			"dpm": "[name='department_filter[]']",
			"posiotion_ft": "[name='position_filter[]']",
			"status": "[name='status_filter[]']",
			"company_filter": "[name='company_filter[]']",
			"cp_from_date_filter": "[name='cp_from_date_filter']",
			"cp_to_date_filter": "[name='cp_to_date_filter']",
			"rec_channel_form_id_filter": "[name='rec_channel_form_id_filter[]']",
			"cp_manager_filter": "[name='cp_manager_filter[]']",
		};
		var table_rec_campaign = $('.table-table_rec_campaign');

		var _table_api = initDataTable(table_rec_campaign, "<?php echo get_uri("recruitment/table_campaign") ?>", '', '', CampaignServerParams);
		$.each(CampaignServerParams, function(i, obj) {
			$('select' + obj).on('change', function() {
				table_rec_campaign.DataTable().ajax.reload();
			});
		});

		$('input[name="cp_from_date_filter"]').on('change', function() {  
			table_rec_campaign.DataTable().ajax.reload();
		});
		$('input[name="cp_to_date_filter"]').on('change', function() {  
			table_rec_campaign.DataTable().ajax.reload();
		});


		$("input[data-type='currency']").on({
			keyup: function() {
				formatCurrency($(this));
			},
			blur: function() {
				formatCurrency($(this), "blur");
			}
		});  
	})(jQuery);


	function formatNumber(n) {
		"use strict"; 

		return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
	}
	function formatCurrency(input, blur) {
		"use strict"; 
		var input_val = input.val();
		if (input_val === "") { return; }
		var original_len = input_val.length;
		var caret_pos = input.prop("selectionStart");
		if (input_val.indexOf(".") >= 0) {
			var decimal_pos = input_val.indexOf(".");
			var left_side = input_val.substring(0, decimal_pos);
			var right_side = input_val.substring(decimal_pos);
			left_side = formatNumber(left_side);
			right_side = formatNumber(right_side);
			right_side = right_side.substring(0, 2);
			input_val = left_side + "." + right_side;
		} else {

			input_val = formatNumber(input_val);
			input_val = input_val;
		}
		input.val(input_val);
		var updated_len = input_val.length;
		caret_pos = updated_len - original_len + caret_pos;
		input[0].setSelectionRange(caret_pos, caret_pos);
	}


	function close_modal_preview(){
		"use strict"; 

		$('._project_file').modal('hide');
	}
</script>