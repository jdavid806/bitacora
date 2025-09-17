<script type="text/javascript">
	(function($) {
		"use strict";
		$(".select2").select2();
		var ProposalServerParams = {
			"dpm": "[name='department_filter[]']",
			"posiotion_ft": "[name='position_filter[]']",
			"status": "[name='status_filter[]']",
		};
		var table_rec_proposal = $('.table-table_rec_proposal');

		var _table_api = initDataTable(table_rec_proposal, "<?php echo get_uri("recruitment/table_proposal") ?>", '', '', ProposalServerParams);

		$.each(ProposalServerParams, function(i, obj) {
			$('select' + obj).on('change', function() {  
				table_rec_proposal.DataTable().ajax.reload();
			});
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

	function preview_proposal_btn(invoker){
		"use strict";
		var id = $(invoker).attr('id');
		var rel_id = $(invoker).attr('rel_id');
		view_proposal_file(id, rel_id);
	}

	function view_proposal_file(id, rel_id) {
		"use strict";
		$('#proposal_file_data').empty();
		
		$("#proposal_file_data").load("<?php echo get_uri("recruitment/file/") ?>" + id + '/' + rel_id, function(response, status, xhr) {
			if (status == "error") {
				alert_float('danger', xhr.statusText);
			}
		});
	}
	function close_modal_preview(){
		"use strict";
		
		$('._project_file').modal('hide');
	}
</script>