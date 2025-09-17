<?php echo form_open(get_uri("client_messages/save_template"), array("id" => "template-form", "class" => "general-form p-3", "role" => "form")); ?>
<?php echo view("leads/messages/templates/template_form_fields", ["client_id" => $client_id]); ?>
<?php echo form_close(); ?>

<script>
    $(document).ready(function () {
        $("#template-form").appForm({
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                    $("#lead-message-templates-table").appTable({newData: result.data, dataId: result.id});
                }
            }
        });
    });
</script>