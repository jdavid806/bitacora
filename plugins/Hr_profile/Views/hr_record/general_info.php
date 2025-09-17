<div class="tab-content">
    <?php echo form_open(get_uri("hr_profile/save_general_info/" . $user_info->id), array("id" => "general-info-form", "class" => "general-form dashed-row white", "role" => "form")); ?>
    <div class="card">
        <div class=" card-header">
            <h4> <?php echo app_lang('general_info'); ?></h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <label for="first_name" class=" col-md-2"><?php echo app_lang('first_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "first_name",
                            "name" => "first_name",
                            "value" => $user_info->first_name,
                            "class" => "form-control",
                            "placeholder" => app_lang('first_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="last_name" class=" col-md-2"><?php echo app_lang('last_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "last_name",
                            "name" => "last_name",
                            "value" => $user_info->last_name,
                            "class" => "form-control",
                            "placeholder" => app_lang('last_name'),
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="address" class=" col-md-2"><?php echo app_lang('mailing_address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_textarea(array(
                            "id" => "address",
                            "name" => "address",
                            "value" => $user_info->address,
                            "class" => "form-control",
                            "placeholder" => app_lang('mailing_address')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="alternative_address" class=" col-md-2"><?php echo app_lang('alternative_address'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_textarea(array(
                            "id" => "alternative_address",
                            "name" => "alternative_address",
                            "value" => $user_info->alternative_address,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_address')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="phone" class=" col-md-2"><?php echo app_lang('phone'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "phone",
                            "name" => "phone",
                            "value" => $user_info->phone,
                            "class" => "form-control",
                            "placeholder" => app_lang('phone')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="alternative_phone" class=" col-md-2"><?php echo app_lang('alternative_phone'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "alternative_phone",
                            "name" => "alternative_phone",
                            "value" => $user_info->alternative_phone,
                            "class" => "form-control",
                            "placeholder" => app_lang('alternative_phone')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="skype" class=" col-md-2">Skype</label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "skype",
                            "name" => "skype",
                            "value" => $user_info->skype ? $user_info->skype : "",
                            "class" => "form-control",
                            "placeholder" => "Skype"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="dob" class=" col-md-2"><?php echo app_lang('date_of_birth'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "dob",
                            "name" => "dob",
                            "value" => $user_info->dob,
                            "class" => "form-control",
                            "placeholder" => app_lang('date_of_birth'),
                            "autocomplete" => "off"
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="ssn" class=" col-md-2"><?php echo app_lang('ssn'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_input(array(
                            "id" => "ssn",
                            "name" => "ssn",
                            "value" => $user_info->ssn,
                            "class" => "form-control",
                            "placeholder" => app_lang('ssn')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="gender" class=" col-md-2"><?php echo app_lang('gender'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_radio(array(
                            "id" => "gender_male",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "male", ($user_info->gender === "male") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_male" class="mr15 p0"><?php echo app_lang('male'); ?></label> 
                        <?php
                        echo form_radio(array(
                            "id" => "gender_female",
                            "name" => "gender",
                            "class" => "form-check-input",
                                ), "female", ($user_info->gender === "female") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_female" class="mr15 p0"><?php echo app_lang('female'); ?></label>
                        <?php
                        echo form_radio(array(
                            "id" => "gender_other",
                            "name" => "gender",
                            "class" => "form-check-input",
                        ), "other", ($user_info->gender === "other") ? true : false, "class='form-check-input'");
                        ?>
                        <label for="gender_other" class="p0"><?php echo app_lang('other'); ?></label>

                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="account_number" class=" col-md-2"><?php echo app_lang('hr_bank_account_number'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        $account_number = (isset($user_info) ? $user_info->account_number : '');
                        echo render_input1('account_number','',$account_number, 'text', ['placeholder' => _l('hr_bank_account_number')], [], ' mb0'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="name_account" class=" col-md-2"><?php echo app_lang('hr_bank_account_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                    $name_account = (isset($user_info) ? $user_info->name_account : '');
                    echo render_input1('name_account','',$name_account, 'text', ['placeholder' => _l('hr_bank_account_name')], [], ' mb0'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <label for="issue_bank" class=" col-md-2"><?php echo app_lang('hr_bank_name'); ?></label>
                    <div class=" col-md-10">
                        <?php
                    $issue_bank = (isset($user_info) ? $user_info->issue_bank : '');
                    echo render_input1('issue_bank','',$issue_bank, 'text', ['placeholder' => _l('hr_bank_name')], [], ' mb0'); ?>
                    </div>
                </div>
            </div>
            

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-2", "field_column" => " col-md-10")); ?> 

        </div>
        <div class="card-footer rounded-0">
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#general-info-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
                setTimeout(function () {
                    window.location.href = "<?php echo get_uri("hr_profile/staff_profile/" . $user_info->id); ?>" + "/general";
                }, 500);
            }
        });
        $("#general-info-form .select2").select2();

        setDatePicker("#dob");

    });
</script>    