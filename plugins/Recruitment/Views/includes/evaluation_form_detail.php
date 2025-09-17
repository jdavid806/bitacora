<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <?php echo form_hidden('id', $id); ?>
                <div class="page-title clearfix">
                    <h4 class="no-margin font-bold"><i class="fa fa-clone menu-icon menu-icon" aria-hidden="true"></i> <?php echo app_lang('evaluation_form_detail'); ?></h4>
                    <div class="title-button-group">
                            <a href="<?php echo get_uri('recruitment/evaluation_forms'); ?>"class="btn btn-default text-right mright5"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></a>
                        <?php if (re_has_permission('recruitment_can_create') || re_has_permission('recruitment_can_edit') || is_admin() ) { ?>
                            
                            <?php echo modal_anchor(get_uri("recruitment/evaluation_detail_form_modal_form/0/".$id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('re_add_criteria'), array("class" => "btn btn-info text-white", "title" => app_lang('re_add_criteria'))); ?>
                        <?php } ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="evaluation_criteria_form-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require 'plugins/Recruitment/assets/js/settings/evaluation_form_detail_js.php';?>
</body>
</html>
