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
      init_asset_table();
    });

    init_asset_table();

})(jQuery);

function init_asset_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-asset')) {
    $('.table-asset').DataTable().destroy();
  }
  initDataTable('.table-asset', admin_url + 'ma/asset_table', false, false, fnServerParams);
}
</script>