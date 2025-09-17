<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "on_boarding_process";
            echo view("Recruitment\Views\includes/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="card">
                <div class="page-title clearfix">
                    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('on_boarding_process'); ?></h4>
                    <div class="title-button-group">
                        <?php if (re_has_permission('recruitment_can_create') || re_has_permission('recruitment_can_edit')|| is_admin() ) { ?>
                            
                            <?php echo modal_anchor(get_uri("recruitment/on_boarding_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('new_setting_tranfer'), array("class" => "btn btn-info text-white", "title" => app_lang('new_setting_tranfer'))); ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="on_boarding-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'plugins/Recruitment/assets/js/settings/on_boarding_js.php';?>
</body>
</html>