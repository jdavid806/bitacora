<script type="text/javascript">
var fnServerParams;
  var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();
(function($) {
		"use strict";

		fnServerParams = {
    };
    email_templates_table();

   
})(jQuery);

function email_templates_table() {
  "use strict";

  if ($.fn.DataTable.isDataTable('.table-email-templates')) {
    $('.table-email-templates').DataTable().destroy();
  }
  initDataTable('.table-email-templates', admin_url + 'ma/email_templates_table', false, false, fnServerParams);
}

function clone_template(id){
  "use strict";

  $('#clone_email_template_modal input[name=id]').val(id);
  $('#clone_email_template_modal input[name=name]').val('');
  $('#clone_email_template_modal').modal('show');
}

</script>
