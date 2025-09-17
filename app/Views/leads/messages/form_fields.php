<input type="hidden" name="client_id" value="<?php $client_id ?>">
<div class="variableDetailsFirst col-md-12">
    <div class="card rounded-bottom">
        <div class="card-header">
            <h5 class=""><?php echo app_lang('variables'); ?></h5>
            <div>
                <h6>Las variables a son:</h6>
                <ul>
                    <li>[[NOMBRE_CLIENTE]]</li>
                    <li>[[ESPECIALIDAD]]</li>
                    <li>[[PAIS]]</li>
                    <li>[[VENDEDOR]]</li>
                    <li>[[PRODUCTO_DE_INTERES]]</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="d-flex b-b comment-form-container">
    <div class="w-100">
        <div id="lead-dropzone" class="post-dropzone mb-3 form-group">
            <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : 0; ?>">
            <input type="hidden" name="view" value="leads/view" />
            <div class="mb-3">
                <?php
                echo form_checkbox(
                    [
                        "id" => "send_attachment_first",
                        "name" => "send_attachment_first"
                    ],
                    true,
                    true
                );
                ?>
                <label for="send_attachment_first">Enviar adjunto primero</label>
            </div>
            <div class="form-group row">
                <label for="message_template">Plantillas</label>
                <?php
                echo form_dropdown(
                    [
                        "id" => "message_template",
                        "name" => "message_template",
                        "class" => "select2 w-100"
                    ],
                    $message_templates_dropdown,
                    [],
                    "class='form-control'"
                );
                ?>
            </div>
            <?php
            echo form_textarea(array(
                "id" => "message_description",
                "name" => "content",
                "class" => "form-control comment_description",
                "placeholder" => app_lang('write_a_message'),
                "data-rule-required" => true,
                "data-rich-text-editor" => true,
                "data-msg-required" => app_lang("field_required")
            ));
            ?>
            <?php echo view("includes/dropzone_preview"); ?>
            <footer class="card-footer b-a clearfix">
                <div class="float-start">
                    <?php echo view("includes/upload_button"); ?>
                </div>
                <button class="btn btn-primary float-end" type="submit"><i data-feather="send" class='icon-16'></i>
                    <?php echo app_lang("send_message"); ?></button>
            </footer>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var $messageDescription = $("#message_description"),
            $messageTemplate = $("#message_template");

        $messageTemplate.select2();

        $messageTemplate.on("change", function () {
            console.log("xd");

            var templateId = $(this).val();
            console.log(templateId);
            console.log(<?php echo $client_id; ?>);


            if (templateId) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo get_uri("client_messages/get_message_template") ?>",
                    dataType: "json",
                    data: {
                        id: templateId,
                        client_id: <?php echo $client_id; ?>
                    },
                    success: function (result) {
                        console.log(result);
                        if (result.success) {
                            $messageDescription.val(result.data);
                        }
                    }
                });
            }
        });
    });
</script>