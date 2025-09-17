<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "permission_roles";
            echo view("Recruitment\Views\includes/tabs", $tab_view);
            ?>
        </div>

        <div class="col-sm-9 col-lg-10">
            <div class="row">
                <div class="col-md-4">
                    <div id="role-list-box" class="card">
                        <div class="page-title clearfix">
                            <h4> <?php echo app_lang('roles'); ?></h4>
                        </div>
                        <div class="table-responsiv">
                            <table id="role-table" class="display clickable no-thead b-b-only" cellspacing="0" width="100%">            
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="role-details-section"> 
                        <div id="empty-role" class="text-center p15 box card min-height-300">
                            <div class="box-content ertical-align-middle height-100"> 
                                <div><?php echo app_lang("select_a_role"); ?></div>
                                <span data-feather="sliders" width="6rem" height="6rem" class="role-color"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<?php require 'plugins/Recruitment/assets/js/roles/index_js.php';?>

</html>

