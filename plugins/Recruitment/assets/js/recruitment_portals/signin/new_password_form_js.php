<script type="text/javascript">
    $(document).ready(function () {
        "use strict";
        
        $("#reset-password-form").appForm({
            isModal: false,
            onSubmit: function () {
                appLoader.show();
            },
            onSuccess: function (result) {
                appLoader.hide();
                appAlert.success(result.message, {container: '.card-body', animate: false});
                $("#reset-password-form").remove();
            },
            onError: function (result) {
                appLoader.hide();
                appAlert.error(result.message, {container: '.card-body', animate: false});
                return false;
            }
        });
    });
</script>    