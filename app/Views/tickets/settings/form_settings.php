<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "assignment_panel";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">
            <div class="row d-flex gap-2 align-items-center">
                <h3 class="col"><?php echo app_lang("assignment_panel") ?></h2>
                    <div class="title-button-group col d-flex justify-content-end">
                        <?php
                        echo modal_anchor(get_uri("tickets_settings/modal_form_settings"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_setting'), array("class" => "btn btn-default", "title" => app_lang('add_setting')));
                        ?>
                    </div>
            </div>
            <div>
                <table id="tickets-table" class="display" cellspacing="0" width="100%">
                </table>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {

            $("#tickets-table").appTable({
                source: '<?php echo_uri("tickets_settings/list_data_user_tickets") ?>',
                smartFilterIdentity: "all_tickets_list",
                columns: [{
                        title: '<?php echo app_lang("id") ?>',
                        "class": "w50"
                    },
                    {
                        title: '<?php echo app_lang("username") ?>',
                        "class": "w50"
                    },
                    {
                        title: '<?php echo app_lang("ticket_types") ?>',
                        "class": "w50"
                    },
                    {
                        title: '<?php echo app_lang("total_max_tickets") ?>',
                        "class": "w10p"
                    },
                    {
                        title: '<?php echo app_lang("tickets_open_currently") ?>',
                        "class": "w10p"
                    },
                    {
                        title: '<?php echo app_lang("total_max_tasks") ?>',
                        "class": "w10p"
                    },
                    {
                        title: '<?php echo app_lang("tasks_open_currently") ?>',
                        "class": "w10p"
                    },
                    {
                        title: '<?php echo app_lang("actions") ?>',
                        "class": "text-center w100",
                        render: function(data, type, row) {
                            let id = row[0];
                            let deleteButton = "<a href='" + '<?php echo_uri("tickets_settings/delete/"); ?>' + id + "' title='<?php echo app_lang("delete") ?>' onclick='return confirm(\"<?php echo app_lang("are_you_sure") ?>\");'><i data-feather='x'></i></a>";
                            return "<div class='d-flex gap-2 justify-content-center'>" + deleteButton + "</div>";
                        }
                    }
                ]
            });
        });
    </script>