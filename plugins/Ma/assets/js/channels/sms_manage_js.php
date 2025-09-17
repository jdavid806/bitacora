<script type="text/javascript">
var fnServerParams;
 var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();
(function($) {
		"use strict";
  $(".select2").select2();

		fnServerParams = {
      "category": '[name="category"]',
    };

    $('select[name="category"]').on('change', function() {
      init_sms_table();
    });
    
    init_sms_table();

})(jQuery);

function init_sms_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-sms')) {
    $('.table-sms').DataTable().destroy();
  }
  initDataTable('.table-sms', admin_url + 'ma/sms_table', false, false, fnServerParams);
}
</script>