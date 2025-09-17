<?php echo form_open(get_uri("ma/save_email_settings"), array("id" => "email-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <div class="card">
        <div class="card-header">
            <h4><?php echo app_lang("email_settings"); ?></h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="mail_engine"><?php echo _l('ma_smtp_type'); ?></label><br />
                <div class="radio radio-inline radio-primary">
                    <input type="radio" name="ma_smtp_type" id="system_default_smtp" value="system_default_smtp" <?php if(get_setting('ma_smtp_type') == 'system_default_smtp'){echo 'checked';} ?> class="form-check-input">
                    <label for="system_default_smtp"><?php echo _l('system_default_smtp'); ?></label>
                </div>

                <div class="radio radio-inline radio-primary">
                    <input type="radio" name="ma_smtp_type" id="other_smtp" value="other_smtp" <?php if(get_setting('ma_smtp_type') == 'other_smtp'){echo 'checked';} ?> class="form-check-input">
                    <label for="other_smtp"><?php echo _l('other_smtp'); ?></label>
                </div>
            </div>
            <div class="div_other_smtp <?php if(get_setting('ma_smtp_type') == 'system_default_smtp'){echo 'hide';} ?>">
            <div class="form-group">
                <div class="row">
                    <label for="ma_email_sent_from_address" class=" col-md-2"><?php echo app_lang('email_sent_from_address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "ma_email_sent_from_address",
                            "name" => "ma_email_sent_from_address",
                            "value" => get_setting('ma_email_sent_from_address'),
                            "class" => "form-control",
                            "placeholder" => "somemail@somedomain.com",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="ma_email_sent_from_name" class=" col-md-2"><?php echo app_lang('email_sent_from_name'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "ma_email_sent_from_name",
                            "name" => "ma_email_sent_from_name",
                            "value" => get_setting('ma_email_sent_from_name'),
                            "class" => "form-control",
                            "placeholder" => "Company Name",
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="use_smtp" class=" col-md-2 col-xs-8 col-sm-4"><?php echo app_lang('email_use_smtp'); ?></label>
                    <div class="col-md-10 col-xs-4 col-sm-8">
                        <?php
                        echo form_checkbox(
                                "ma_email_protocol", "smtp", get_setting('ma_email_protocol') === "smtp" ? true : false, "id='use_smtp' class='form-check-input'"
                        );
                        ?>
                    </div>
                </div>
            </div>

            <div id="smtp_settings" class="<?php echo get_setting('ma_email_protocol') === "smtp" ? "" : "hide"; ?>">
                <div class="form-group">
                    <div class="row">
                        <label for="ma_email_smtp_host" class=" col-md-2"><?php echo app_lang('email_smtp_host'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "ma_email_smtp_host",
                                "name" => "ma_email_smtp_host",
                                "value" => get_setting('ma_email_smtp_host'),
                                "class" => "form-control",
                                "placeholder" => app_lang('email_smtp_host'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="ma_email_smtp_user" class=" col-md-2"><?php echo app_lang('email_smtp_user'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "ma_email_smtp_user",
                                "name" => "ma_email_smtp_user",
                                "value" => get_setting('ma_email_smtp_user'),
                                "class" => "form-control",
                                "placeholder" => app_lang('email_smtp_user'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="ma_email_smtp_pass" class=" col-md-2"><?php echo app_lang('email_smtp_password'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_password(array(
                                "id" => "ma_email_smtp_pass",
                                "name" => "ma_email_smtp_pass",
                                "value" => get_setting('ma_email_smtp_pass') ? "******" : "",
                                "class" => "form-control",
                                "placeholder" => app_lang('email_smtp_password'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="ma_email_smtp_port" class=" col-md-2"><?php echo app_lang('email_smtp_port'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_input(array(
                                "id" => "ma_email_smtp_port",
                                "name" => "ma_email_smtp_port",
                                "value" => get_setting('ma_email_smtp_port'),
                                "class" => "form-control",
                                "placeholder" => app_lang('email_smtp_port'),
                                "data-rule-required" => true,
                                "data-msg-required" => app_lang("field_required"),
                            ));
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <label for="ma_email_smtp_security_type" class=" col-md-2"><?php echo app_lang('security_type'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown(
                                    "ma_email_smtp_security_type", array(
                                "none" => "-",
                                "tls" => "TLS",
                                "ssl" => "SSL"
                                    ), get_setting('ma_email_smtp_security_type'), "class='select2 mini'"
                            );
                            ?>
                        </div>
                    </div>
                </div>

            </div>
            <div class="form-group">
                <div class="row">
                    <label for="ma_send_test_mail_to" class=" col-md-2"><?php echo app_lang('send_test_mail_to'); ?></label>
                    <div class="col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "ma_send_test_mail_to",
                            "name" => "ma_send_test_mail_to",
                            "value" => get_setting('ma_send_test_mail_to'),
                            "class" => "form-control",
                            "placeholder" => "Keep it blank if you are not interested to send test mail",
                        ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>

<?php require 'plugins/Ma/assets/js/settings/email_configuration_js.php'; ?>
