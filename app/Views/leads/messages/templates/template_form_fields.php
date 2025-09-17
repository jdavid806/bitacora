<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
<div class="form-group row">
    <label for="template_name" class="col-sm-3 col-form-label">Nombre de la Plantilla</label>
    <div class="col-sm-9">
        <?php echo form_input(array(
            "name" => "template_name",
            "class" => "form-control",
            "required" => "required",
            "placeholder" => "Ingrese el nombre de la plantilla",
            "value" => $template_name
        )); ?>
    </div>
</div>
<div class="form-group row">
    <label for="subject" class="col-sm-3 col-form-label">Asunto</label>
    <div class="col-sm-9">
        <?php echo form_input(array(
            "name" => "subject",
            "class" => "form-control",
            "placeholder" => "Ingrese el asunto",
            "value" => $subject
        )); ?>
    </div>
</div>
<div class="form-group row">
    <label for="default_content" class="col-sm-3 col-form-label">Contenido</label>
    <div class="col-sm-9">
        <?php echo form_textarea(array(
            "id" => "default_content",
            "name" => "default_content",
            "class" => "form-control rich-text-editor",
            "placeholder" => "Ingrese el contenido",
            "value" => $default_content
        )); ?>
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
        <div>
            <div id="message-preview" class="mt-3 p-3 border rounded">
                <h5>Vista previa del mensaje:</h5>
                <div id="preview-content"><?php echo $default_content; ?></div>
            </div>

            <script>
                $(document).ready(function() {
                    var previewContent = $('#preview-content');
                    var timeout = null;

                    $('#default_content').on('input', function() {
                        clearTimeout(timeout);
                        console.log('input');
                        console.log('default_content', $('#default_content').val());
                        
                        
                        timeout = setTimeout(function() {
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo get_uri("client_messages/render_template_preview") ?>", // Update with your endpoint
                                method: 'POST',
                                data: {
                                    template: $('#default_content').val(),
                                    client_id: <?php echo $client_id; ?>
                                },
                                success: function(response) {
                                    console.log(response);
                                    
                                    previewContent.html(response);
                                },
                                error: function() {
                                    previewContent.html('Error loading preview');
                                }
                            });
                        }, 300);
                    });
                });
            </script>
        </div>
    </div>
</div>
<div class="form-group row">
    <label for="template_type" class="col-sm-3 col-form-label">Tipo de Plantilla</label>
    <div class="col-sm-9">
        <?php echo form_dropdown("template_type", array(
            "general-client-wpp-msg" => "General",
            "lead-wpp-msg" => "Soporte",
            "client-wpp-msg" => "Ventas",
        ), [$template_type], "class='form-control'"); ?>
    </div>
</div>
<div class="form-group row">
    <label for="language" class="col-sm-3 col-form-label">Idioma</label>
    <div class="col-sm-9">
        <?php echo form_dropdown("language", array(
            "es" => "Español",
            "en" => "Inglés"
        ), [$language], "class='form-control'"); ?>
    </div>
</div>
<div class="form-group text-right">
    <?php echo form_button(array(
        "type" => "submit",
        "class" => "btn btn-primary",
        "content" => '<span class="fa fa-check-circle"></span> Guardar'
    )); ?>
</div>