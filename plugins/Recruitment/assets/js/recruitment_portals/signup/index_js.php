<script type="text/javascript">
    $(document).ready(function () {
        "use strict";
        
        $("#signup-form").appForm({
            isModal: false,
            onSubmit: function () {
                appLoader.show();
            },
            onSuccess: function (result) {
                appLoader.hide();
                appAlert.success(result.message, {container: '.card-body', animate: false});
                $("#signup-form").remove();

                <?php if ($signup_type !== "send_verify_email") { ?>
                    $("#signin_link").remove();
                <?php } ?>
            },
            onError: function (result) {
                appLoader.hide();
                appAlert.error(result.message, {container: '.card-body', animate: false});
                return false;
            }
        });
    });
</script>  