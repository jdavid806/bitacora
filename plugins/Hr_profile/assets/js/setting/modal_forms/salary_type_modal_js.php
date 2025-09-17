<script>
	$(function() {
		'use strict';
		formatCurrency($("input[data-type='currency']"));
		formatCurrency($("input[data-type='currency']"), "blur");
		$("input[data-type='currency']").on({
			keyup: function() {        
				formatCurrency($(this));
			},
			blur: function() { 
				formatCurrency($(this), "blur");
			}
		});

	});
</script>