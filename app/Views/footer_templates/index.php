<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "footer_templates";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">
            <div class="row d-flex gap-2 align-items-center">
                <h3 class="col"> Plantillas de pie de pagina</h2>
                    <div class="title-button-group col d-flex justify-content-end">
                        <?php
                        echo modal_anchor(get_uri("footer_templates/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_template'), array("class" => "btn btn-default", "title" => app_lang('add_template')));
                        ?>
                    </div>
            </div>
            <div>
                <table id="templates-table" class="display" cellspacing="0" width="100%">
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#templates-table").appTable({
                source: '<?php echo_uri("footer_templates/list_data") ?>',
                smartFilterIdentity: "all_templates_list",
                columns: [{
                        title: '<?php echo app_lang("id") ?>',
                        "class": "w50"
                    },
                    {
                        title: '<?php echo app_lang("template_name") ?>',
                        "class": "w50"
                    },
                    {
                        title: '<?php echo app_lang("subject_") ?>',
                        "class": "w10p"
                    },
                    {
                        title: '<?php echo app_lang("template_type") ?>',
                        "class": "w10p"
                    },
                    {
                        title: '<?php echo app_lang("actions") ?>',
                        "class": "text-center w100",
                        render: function(data, type, row) {
                            let id = row[0];
                            let editButton = "<a href='#' data-act='ajax-modal' data-title='<?php echo app_lang("edit_template") ?>' data-action-url='" + '<?php echo_uri("footer_templates/modal_form/"); ?>' + id + "' title='<?php echo app_lang("edit") ?>'><i data-feather='edit'></i></a>";
                            let deleteButton = "<a href='" + '<?php echo_uri("footer_templates/delete/"); ?>' + id + "' title='<?php echo app_lang("delete") ?>' onclick='return confirm(\"<?php echo app_lang("are_you_sure") ?>\");'><i data-feather='x'></i></a>";
                            return "<div class='d-flex gap-2 justify-content-center'>" + editButton + deleteButton + "</div>";
                        }
                    }
                ]
            });
        });
    </script>