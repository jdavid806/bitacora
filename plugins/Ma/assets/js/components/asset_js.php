<script type="text/javascript">
  var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();
Dropzone.options.expenseForm = false;
var expenseDropzone;
var is_edit = $('input[name="is_edit"]').val();
var uploadUrl = "<?php echo get_uri("ma/asset_upload_file"); ?>";
var validationUrl = "<?php echo get_uri("ma/validate_asset_file"); ?>";
var dropzone = attachDropzoneWithForm("#new-asset-dropzone", uploadUrl, validationUrl, {maxFiles: 1});

(function($) {
  "use strict";

  $(".select2").select2();
    initColorPicker();

})(jQuery);
    
</script>