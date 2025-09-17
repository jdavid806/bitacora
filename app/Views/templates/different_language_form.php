<?php
$template_name_formatted = preg_replace('/_(lead|client|lead_internal|client_external|lead_external|client_internal)$/', '', $model_info->template_name);
$template_name_array = explode("_", $model_info->template_name);
?>
<div class="card">
    <div class='card-header'>
        <i data-feather="mail" class='icon-16 mr10'></i><?php echo app_lang($template_name_formatted) . ' - ' . app_lang($template_name_array[count($template_name_array) - 1]); ?>
    </div>
    <?php echo form_open(get_uri("templates/save"), array("id" => "template-form-$model_info->id", "class" => "general-form template-form", "role" => "form")); ?>
    <div class="modal-body clearfix">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
        <div class='row'>
            <div class="form-group">
                <div class=" col-md-12">
                    <?php
                    echo form_input(array(
                        "id" => "subject_",
                        "name" => "subject_",
                        "value" => $model_info->subject_,
                        "class" => "form-control",
                        "placeholder" => app_lang('subject'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <span id="unsupported-title-variable-error" class="text-danger inline-block mt5 hide"></span>
                </div>
            </div>
            <div class="form-group">
                <div class=" col-md-12">
                    <?php
                    echo form_textarea(array(
                        "id" => "custom_content",
                        "name" => "custom_content",
                        "value" => process_images_from_content(($model_info->custom_content ? $model_info->custom_content : $model_info->default_content), false),
                        "class" => "form-control different_language_custom_message",
                        "data-toolbar" => "pdf_friendly_toolbar",
                        "data-height" => 480,
                        "data-encode_ajax_post_data" => "1"
                    ));
                    ?>
                </div>
            </div>
        </div>
        <div><strong><?php echo app_lang("avilable_variables"); ?></strong>: <?php
                                                                                foreach ($variables as $variable) {
                                                                                    echo "{" . $variable . "}, ";
                                                                                }
                                                                                ?></div>
        <hr />
        <div class="form-group m0">
            <button type="submit" class="btn btn-primary mr15"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
            <button id="restore_to_default" data-bs-toggle="popover" data-id="<?php echo $model_info->id; ?>" data-placement="top" type="button" class="btn btn-danger"><span data-feather="refresh-cw" class="icon-16"></span> <?php echo app_lang('restore_to_default'); ?></button>
        </div>

    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var formId = "#template-form-<?php echo $model_info->id; ?>";
        $(formId).appForm({
            isModal: false,
            onSuccess: function(result) {
                if (result.success) {
                    appAlert.success(result.message, {
                        duration: 10000
                    });
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        initWYSIWYGEditor("#custom_content");


        $('#restore_to_default').click(function() {
            var $instance = $(this);
            $(this).appConfirmation({
                title: "<?php echo app_lang('are_you_sure'); ?>",
                btnConfirmLabel: "<?php echo app_lang('yes'); ?>",
                btnCancelLabel: "<?php echo app_lang('no'); ?>",
                onConfirm: function() {
                    $.ajax({
                        url: "<?php echo get_uri('templates/restore_to_default') ?>",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: $instance.attr("data-id")
                        },
                        success: function(result) {
                            if (result.success) {
                                setWYSIWYGEditorHTML("#custom_content", result.data);
                                appAlert.success(result.message, {
                                    duration: 10000
                                });
                            } else {
                                appAlert.error(result.message);
                            }
                        }
                    });

                }
            });

            return false;
        });

    });
</script>