<div class="clearfix">

    <ul id="whatsapp-template-tab" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white title scrollable-tabs" role="tablist">
        <li class="whatsapp-template-tabs"><a role="presentation" data-bs-toggle="tab" href="<?php echo echo_uri("templates/different_language_form_wpp/" . $model_info->id); ?>" data-bs-target="#whatsapp-template-form-default" data-reload="1" data-name="<?php echo $model_info->template_name; ?>" class="whatsapp-template-form-tab"><?php echo app_lang("default"); ?></a></li>
        <?php
        if ($different_language_templates) {
            foreach ($different_language_templates as $different_language_template) {
                echo view("whatsapp_templates/tab_view", array("tab_data" => $different_language_template));
            }
        }
        ?>
        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("templates/add_template_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> ", array("class" => "btn btn-default", "id" => "add-template-button", "title" => app_lang('add_template'), "data-post-template_name" => $model_info->template_name)); ?>
            </div>
        </div>
    </ul>

    <div class="tab-content mt20">
        <div role="tabpanel" class="tab-pane clearfix" id="whatsapp-template-form-default"></div>
    </div>
</div>