<script type="text/javascript">
(function($) {
    "use strict";
    $(document).ready(function () {
      $("#email-settings-form").appForm({
            isModal: false,
            onSubmit: function () {
                appLoader.show();
            },
            onSuccess: function (result) {
                appLoader.hide();
                appAlert.success(result.message, {duration: 10000});
            },
            onError: function (result) {
                appLoader.hide();
                appAlert.error(result.message);
            }
        });

        $('#use_smtp').on('click', function() {
            if ($(this).is(":checked")) {
                $("#smtp_settings").removeClass("hide");
            } else {
                $("#smtp_settings").addClass("hide");
            }
        });

        $("#email-settings-form .select2").select2();
    });

  $(document).on("change", "input[type=radio][name*=ma_smtp_type]", function() { 

      if(this.value == 'other_smtp'){
          $('.div_other_smtp').removeClass('hide');
          $('.div_test_email').removeClass('hide');
      }else{
          $('.div_other_smtp').addClass('hide');
          $('.div_test_email').addClass('hide');
      }

  });

  $('.ma_test_email').on('click', function() {
      var email = $('input[name="test_email"]').val();
      if (email != '') {
      $(this).attr('disabled', true);
       $.post(admin_url + 'ma/sent_smtp_test_email', {
        test_email: email
      }).done(function(data) {
        window.location.reload();
      });
    }
  });
})(jQuery);
</script>
