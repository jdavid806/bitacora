<script type="text/javascript">
	(function($) {
		"use strict";  

		$(".select2").select2();
		
	})(jQuery);

	function change_status_campaign(invoker,id_cp){
		"use strict";	

		$.post("<?php echo get_uri("recruitment/change_status_campaign/") ?>"+invoker.value+'/'+id_cp).done(function(reponse){
			reponse = JSON.parse(reponse);
			location.reload();
			appAlert.success(response.result);
		});
	}
</script>