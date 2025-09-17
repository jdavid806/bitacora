<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "reset_datas";
            echo view("Hr_profile\Views\includes/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">
                <div class="page-title clearfix">
                    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('reset_data'); ?></h4>
                    <div class="title-button-group">
                        <?php if ($login_user->is_admin ) { ?>
                            <?php

                            echo modal_anchor(get_uri("hr_profile/confirm_delete_modal_form"), app_lang('reset_data'), array("title" => app_lang('delete'). "?", "data-post-id" => 2, "data-post-id2" => 1,"data-post-function" => 'reset_data', "class" => 'btn btn-danger text-white' ));
                            ; ?>
                        <?php } ?>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
</body>

</html>

