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
                    <li>[[PRODUCTO_DE_INTERES]] <span class="text-danger">Nota esto enviara numeros</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="d-flex b-b comment-form-container">
    <div class="w-100">
        <div id="lead-dropzone" class="post-dropzone mb-3 form-group">
            <div class="mb-3">
                <?php
                echo form_checkbox(
                    [
                        "id" => "send_attachment_first",
                        "name" => "send_attachment_first"
                    ], true, true
                );
                ?>
                <label for="send_attachment_first">Enviar adjunto primero</label>
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
                <button class="btn btn-primary float-end" type="submit"><i data-feather="send" class='icon-16'></i> <?php echo app_lang("send_message"); ?></button>
            </footer>
        </div>
    </div>
</div>