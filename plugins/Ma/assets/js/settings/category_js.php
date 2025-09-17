<script type="text/javascript">
var fnServerParams;
  var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();
(function($) {
		"use strict";

    $(".select2").select2();
    initColorPicker();


		fnServerParams = {
    };

    init_category_table();

    $('.add-new-category').on('click', function(){

      $('#category-modal').find('button[type="submit"]').prop('disabled', false);
      $('#category-modal').modal('show');
      $('input[name="id"]').val('');
      $('select[name="type"]').val('segment').change();
      $('input[name="name"]').val('');
      $('input[name="color"]').val('');
      $('textarea[name="description"]').val('');
    });

})(jQuery);

function init_category_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-category')) {
    $('.table-category').DataTable().destroy();
  }

  var category_table = $('.table-category');

  initDataTable(category_table, admin_url + 'ma/category_table', false, false, fnServerParams);
}

function edit_category(id) {
  "use strict";
  $('#category-modal').find('button[type="submit"]').prop('disabled', false);

  requestGetJSON(admin_url + 'ma/get_data_category/'+id).done(function(response) {
      $('select[name="type"]').val(response.type).change();
      $('input[name="name"]').val(response.name);
      $('input[name="color"]').val(response.color);
      $('input[name="id"]').val(id);
      $('textarea[name="description"]').val(response.description.replace(/(<|&lt;)br\s*\/*(>|&gt;)/g, " "));
      $('#category-modal').modal('show');

  });
}

</script>