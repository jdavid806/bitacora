<?php echo form_open(get_uri("api_evolution/save_instance_EA"), array("id" => "instance-form-EA", "class" => "general-form", "role" => "form")); ?>
<div>
    <input id="client_id" name="client_id" type="hidden" value="<?php echo $client_info->client_id ?>">
    <!-- Tabs -->
    <ul id="instance-tabs-EA" class="nav nav-tabs border-bottom-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="credentials-tab" data-bs-toggle="tab" href="#credentials-EA" role="tab" aria-controls="credentials-EA" aria-selected="true">
                <?php echo app_lang('credentials'); ?>
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="credentials-EA" role="tabpanel" aria-labelledby="credentials-tab">

            <!-- Credentials -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <label for="name_" class="form-label"><?php echo app_lang("instance_name"); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "name_",
                                "name" => "name_",
                                "value" => "",
                                "class" => "form-control"
                            ));
                            ?>
                        </div>
                        <div class="col">
                            <label for="phone" class="form-label"><?php echo app_lang("phone"); ?></label>
                            <?php
                            echo form_input(array(
                                "id" => "phone",
                                "name" => "phone",
                                "type" => "number",
                                "value" => "",
                                "class" => "form-control"
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="card">
                <div class="card-header">
                    <?php echo app_lang("settings"); ?>
                </div>
                <div class="card-body">
                    <div class="row row-cols-2 row-cols-md-3 gx-5 gy-3">
                        <div class="col">
                            <?php
                            echo form_checkbox(array(
                                "id" => "reject_call",
                                "name" => "reject_call",
                                "class" => "form-check-input"
                            ));
                            ?>
                            <label for="reject_call" class="form-check-label"><?php echo app_lang("reject_call"); ?></label>
                        </div>
                        <div class="col">
                            <?php
                            echo form_input(array(
                                "id" => "msg_call",
                                "name" => "msg_call",
                                "class" => "form-control",
                                "value" => "",
                                "placeholder" => app_lang("msg_call")
                            ));
                            ?>
                        </div>
                        <div class="col">
                            <?php
                            echo form_checkbox(array(
                                "id" => "groups_ignore",
                                "name" => "groups_ignore",
                                "class" => "form-check-input"
                            ));
                            ?>
                            <label for="groups_ignore" class="form-check-label"><?php echo app_lang("groups_ignore"); ?></label>
                        </div>
                        <div class="col">
                            <?php
                            echo form_checkbox(array(
                                "id" => "always_online",
                                "name" => "always_online",
                                "value" => "",
                                "class" => "form-check-input"
                            ));
                            ?>
                            <label for="always_online" class="form-check-label"><?php echo app_lang("always_online"); ?></label>
                        </div>
                        <div class="col">
                            <?php
                            echo form_checkbox(array(
                                "id" => "read_messages",
                                "name" => "read_messages",
                                "value" => "",
                                "class" => "form-check-input"
                            ));
                            ?>
                            <label for="read_messages" class="form-check-label"><?php echo app_lang("read_messages"); ?></label>
                        </div>
                        <div class="col">
                            <?php
                            echo form_checkbox(array(
                                "id" => "read_status",
                                "name" => "read_status",
                                "value" => "",
                                "class" => "form-check-input"
                            ));
                            ?>
                            <label for="read_status" class="form-check-label"><?php echo app_lang("read_status"); ?></label>
                        </div>
                        <div class="col">
                            <?php
                            echo form_checkbox(array(
                                "id" => "sync_full_history",
                                "name" => "sync_full_history",
                                "value" => "",
                                "class" => "form-check-input"
                            ));
                            ?>
                            <label for="sync_full_history" class="form-check-label"><?php echo app_lang("sync_full_history"); ?></label>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <?php
                    echo form_submit(array(
                        "name" => "save",
                        "value" => app_lang('save'),
                        "class" => "btn btn-primary mr15"
                    ));
                    ?>
                </div>
            </div>

        </div>
    </div>


</div>
<?php echo form_close(); ?>

<script>
    $(document).ready(function() {

        $("#name_").change(function() {
            const regex = /^[a-zA-Z0-9_ ]+$/;
            let value = $(this).val();

            if (!regex.test(value)) {
                alert("El campo solo puede contener letras, números y guiones bajos (_).");
                $(this).val("");
                $(this).focus();
            } else {
                if (value.includes(" ")) {
                    value = value.replace(/\s+/g, "_");
                    $(this).val(value);
                }
            }
        });


    })
</script>