<script type="text/javascript">
       var site_url = $('input[name="site_url"]').val();
  var admin_url  = $('input[name="site_url"]').val();
(function($) {
  "use strict";
  $(".select2").select2();
    initColorPicker();
  
  $('select[name=sms_template]').on('change', function(){
    var id = $(this).val();
    get_exam_template(id);
  });

})(jQuery);

function get_exam_template(id){
    "use strict";
    $.get(admin_url+'ma/get_sms_template_preview/'+id, function(reponses){
      $('textarea[name=content]').text(reponses);
    });
}
</script>