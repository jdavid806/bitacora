<?php echo view('leads/calls/manager', ["user_id" => $user_id, "client_id" => $client_id ]) ?>

<div class="card border-top-0 rounded-top-0">
    <div class="table-responsive">
        <table id="lead-table" class="display" cellspacing="0" width="100%">
        </table>
    </div>
</div>

<script>
    var ignoreSavedFilter = false;
    var hasString = window.location.hash.substring(1);
    if (hasString) {
        var ignoreSavedFilter = true;
    }

    $("#lead-table").appTable({
        source: '<?php echo_uri("client_calls/list_data_of_client/".$client_id) ?>',
        serverSide: true,
        smartFilterIdentity: "calls_of_client",
        ignoreSavedFilter: ignoreSavedFilter,
        order: [[0, "asc"]],
        columns: [
            {
                title: "<?php echo app_lang("id") ?>",
                "class": "all",
                order_by: "id"
            },
            {
                title: "<?php echo app_lang("caller_name") ?>",
                "class": "all",
                order_by: "user_id"
            },
            {
                title: "<?php echo app_lang("caller_number") ?>",
                order_by: "created_at"
            },
            {
                title: "<?php echo app_lang("call_started_at") ?>",
                order_by: "content"
            },
            {
                title: "<?php echo app_lang("duration") ?>"
            },
            {
                title: "<?php echo app_lang("cost") ?>"
            },
            {
                title: "<?php echo app_lang("status") ?>"
            }
        ],
        printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5]),
        xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5])
    });
</script>