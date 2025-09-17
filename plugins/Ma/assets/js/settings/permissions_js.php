<script type="text/javascript">
var fnServerParams;
  var admin_url  = $('input[name="site_url"]').val();
(function($) {
		"use strict";

    $(".select2").select2();
    init_permission_table();
})(jQuery);

function init_permission_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-permissions')) {
    $('.table-permissions').DataTable().destroy();
  }

  var permissions_table = $('.table-permissions');

  initDataTable(permissions_table, admin_url + 'ma/permission_table', false, false, fnServerParams);
}


function add_permission(id) {
  "use strict";
  	$('#permission-modal').find('button[type="submit"]').prop('disabled', false);

  	$('#permission-modal').modal('show');
    $('#permission-modal input[name="id"]').val('');
  	$('#permission-modal input[type="checkbox"]').prop('checked', false);
}

function edit_permission(id) {
  "use strict";
  $('#permission-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'ma/get_data_permission/'+id).done(function(response) {

  	$('#permission-modal').find('button[type="submit"]').prop('disabled', false);

    $('#permission-modal select[name="user"]').val(response.user_id).change();
    $('#permission-modal input[name="id"]').val(response.id);
  	$('#permission-modal input[type="checkbox"]').prop('checked', false);
  		console.log(response.permissions);

  	$.each( response.permissions, function( key, value ) {
		console.log(key);
		console.log(value);
		$('#permission-modal input[type="checkbox"][name="'+key+'"]').prop('checked', true);
	});
	  	

  	$('#permission-modal').modal('show');

  });
}
</script>