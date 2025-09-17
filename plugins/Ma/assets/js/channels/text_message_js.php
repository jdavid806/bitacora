<script type="text/javascript">
(function($) {
  "use strict";
  $(".select2").select2();


  $('.textarea-merge-field').on('click', function(e) {
    e.preventDefault();
    var textArea = $('textarea[name="' + $(this).data('to') + '"]');
    textArea.val(textArea.val() + $(this).text());
  });
})(jQuery);
</script>