<?php echo form_open(get_uri("api_evolution/save_connect_EA"), array("id" => "instance-form-EA", "class" => "general-form", "role" => "form")); ?>
<div>
    <div class="d-flex flex-column justify-content-center m-2">
        <div class="d-flex flex-row align-items-center m-3">
            <label for="reject_call" class="form-check-label mx-3"><?php echo app_lang("client"); ?></label>
            <?php
            echo form_input(array(
                'name' => 'readonly_field',
                'id' => 'readonly_field',
                'value' => $client_info->company_name,
                'class' => 'form-control',
                'readonly' => 'readonly' // Esto lo hace readonly
            ));
            ?>
        </div>
        <div class="d-flex justify-content-center mb-3">
            <img src="<?php echo get_file_url(isset($info_instance_new_connection) ? $info_instance_new_connection->qrcode_path : $info_instance->qrcode_path) ?>" alt="Sincronización con la instancia de Evolution Api">
        </div>
        <div class="alert alert-primary" role="alert">
            <a data-feather="alert-circle" class="icon-16 mx-2"></a><?php echo app_lang("msg_info_scan_qr_EA") ?>
        </div>
    </div>
</div>
<?php echo form_close(); ?>