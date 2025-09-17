<script type="text/javascript">
	(function($) {
  "use strict";
  	$('input[type=radio][name^=settings]').change(function() {
  		var name = this.name;
	    if (this.value == '1') {
	  		$('input[type=radio][name^=settings][value=0]').each(function() {
	  			if(name != this.name){
	  				$(this).prop('checked', true);
	  			}
				});
	    }
	    
	});

  	// Send test sms
    $('.send-test-sms').on('click', function () {
        var id = $(this).data('id');
        var errorContainer = $('#sms_test_response[data-id="' + id + '"]');
        var message = $('textarea[data-id="' + id + '"]').val();
        var number = $('input.test-phone[data-id="' + id + '"]').val();
        var that = $(this);
        errorContainer.empty();
        message = message.trim();
        if (message != '' && number != '') {
            that.prop('disabled', true);
            $.post(window.location.href, {
                message: message,
                number: number,
                id: id,
                sms_gateway_test: true
            }).done(function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    errorContainer.html('<div class="alert alert-success no-mbot mtop15">SMS Sent Successfully!</div>');
                } else {
                    errorContainer.html('<div class="alert alert-warning no-mbot mtop15">' + response.error + '</div>');
                }
            }).always(function () {
                that.prop('disabled', false);
            });
        }
    });
	})(jQuery);

</script>