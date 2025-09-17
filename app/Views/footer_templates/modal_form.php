<?php
$template_type_options = array(
    '' => app_lang('select_template_type'), // Placeholder de selección vacía
    'invoice_footer' => app_lang('invoice_footer'),
    'estimate_footer' => app_lang('estimate_footer'),
    'contracts_footer' => app_lang('contracts_footer'),
    'proposals_footer' => app_lang('proposals_footer'),
    'whatsapp' => app_lang('whatsapp'),
    'email' => app_lang('email'),
    'tickets' => app_lang('tickets'),
);
?>

<div class="card">
    <div class='card-header'>
        <i data-feather="layout" class='icon-16 mr10'></i>
        <?php echo isset($template) ? app_lang('update_template') : app_lang('create_template'); ?>
    </div>
    <?php echo form_open(get_uri("footer_templates/save"), array("class" => "general-form template-form", "role" => "form")); ?>
    <input type="hidden" name="id" value="<?php echo isset($template) ? $template->id : ''; ?>">
    <div class="modal-body clearfix">
        <div class='row'>
            <div class="form-group row">
                <div class="col-md-6">
                    <?php
                    echo form_input(array(
                        "id" => "template_name",
                        "name" => "template_name",
                        "value" => isset($template) ? $template->template_name : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('template_name'),
                        "autofocus" => true,
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                </div>
                <div class="col-md-6">
                    <?php
                    echo form_dropdown('template_type', $template_type_options, isset($template) ? $template->template_type : '', array(
                        'class' => 'form-control',
                        'id' => 'template_type',
                        'data-rule-required' => true,
                        'data-msg-required' => app_lang('field_required')
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <?php
                    echo form_textarea(array(
                        "id" => "default_content",
                        "name" => "default_content",
                        "value" => isset($template) ? $template->default_content : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('template_content'),
                        "data-height" => 200,
                        "data-toolbar" => "pdf_friendly_toolbar",
                        "data-encode_ajax_post_data" => "1"
                    ));
                    ?>
                </div>
            </div>
            <div class="form-group" id="content-preview">
                <label for="invoice_footer" class=" col-md-8"><?php echo app_lang('add_msg_to_example'); ?></label>
                <div class="col-md-12">
                    <?php
                    echo form_textarea(array(
                        "id" => "custom_content",
                        "name" => "custom_content",
                        "value" => isset($template) ? $template->custom_content : '',
                        "class" => "form-control",
                        "data-height" => 200,
                        "data-toolbar" => "pdf_friendly_toolbar",
                        "data-encode_ajax_post_data" => "1"
                    ));
                    ?>
                </div>
            </div>
        </div>
        <hr />
        <div class="form-group m0">
            <button type="submit" class="btn btn-primary mr15"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        initWYSIWYGEditor("#default_content");
        $('#content-preview').hide();

        $('#template_type').change(function() {
            if ($(this).val() == 'tickets') {
                $('#content-preview').show();
                initWYSIWYGEditor("#custom_content");
            } else {
                $('#content-preview').hide();
                $('#custom_content').val('')
            }
        })

    })
</script>