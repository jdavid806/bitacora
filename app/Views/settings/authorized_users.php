<div id="page-content" class="page-wrapper clearfix">
    <div class="row">

        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "authorized_users";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <?php echo form_open(get_uri("settings/save_authorized_user_settings"), array("id" => "authorized-users-settings-form", "class" => "general-form dashed-row", "role" => "form")); ?>
            <div class="card">
                <div class="card-header">
                    <h4><?php echo app_lang("authorized_users_settings"); ?></h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <label for="can_approve_budget_users" class=" col-md-2"><?php echo app_lang('can_approve_budgets'); ?></label>
                            <div class="col-md-10">
                                <?php
                                echo form_input(array(
                                    "id" => "can_approve_budget_users",
                                    "name" => "can_approve_budget_users",
                                    "value" => get_setting("can_approve_budget_users"),
                                    "class" => "form-control",
                                    "placeholder" => app_lang('users')
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php echo view("includes/cropbox"); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#authorized-users-settings-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                if (result.success) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    appAlert.error(result.message);
                }
            }
        });

        $("#can_approve_budget_users").select2({
            multiple: true,
            data: <?php echo (json_encode($users_dropdown)); ?>
        });

        $("#authorized-users-settings-form .select2").select2();
    });
</script>