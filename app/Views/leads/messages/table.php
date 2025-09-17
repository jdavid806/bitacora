<div id="page-content" class="page-wrapper clearfix grid-button leads-view">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("client_messages/modal_form/" . $client_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('send_new_message'), array("class" => "btn btn-default", "title" => app_lang('send_new_message'))); ?>
            </div>
        </div>
    </ul>

    <div class="card border-top-0 rounded-top-0">
        <div class="table-responsive">
            <table id="lead-messages-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var ignoreSavedFilter = false;
        var hasString = window.location.hash.substring(1);
        if (hasString) {
            var ignoreSavedFilter = true;
        }
        $("#lead-messages-table").appTable({
            source: '<?php echo_uri("client_messages/list_data_of_client/" . $client_id) ?>',
            columns: [
                {
                    title: "<?php echo app_lang("sender") ?>",
                    "class": "all",
                    order_by: "user_id"
                },
                {
                    title: "<?php echo app_lang("sent_at") ?>",
                    "class": "all",
                    order_by: "created_at"
                },
                {
                    title: "<?php echo app_lang("message") ?>",
                    "class": "all",
                    order_by: "content"
                },
                {
                    title: "<?php echo app_lang("attached") ?>",
                    "class": "w20p",
                }
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 4]),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 4])
        });
    });
</script>