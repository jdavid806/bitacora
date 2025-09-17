<?php echo form_open("client_messages/save_massive", array("id" => "client-massive-message-form", "class" => "general-form")); ?>
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input id="leads_ids" type="hidden" name="leads_ids" value="">
            <?php echo view("leads/messages/massive/form_fields"); ?>
        </div>
    </div>
<?php echo form_close(); ?>

<script>
    $("#client-massive-message-form").on("submit", function(event) {
        event.preventDefault();

        const form = this;

        var batchIds = [];

        var $items;
        var $itemWrapper = $("#lead-table")

        if ($itemWrapper.hasClass('dataTable')) {
            $items = $itemWrapper.find("tbody tr");
        } else {
            $items = $itemWrapper.find(".kanban-item");
        }

        $items.each(function () {
            if ($(this).hasClass("batch-operation-selected")) {
                if ($itemWrapper.hasClass('dataTable')) {
                    var id = $(this).find(".js-selection-id").data("id");
                } else {
                    var id = $(this).data("id");
                }

                if (!batchIds.includes(id)) {
                    batchIds.push(id);
                }
            }
        });

        let serializedIds = batchIds.join("-")
        $("#leads_ids").val(serializedIds);

        setTimeout(function() {
            form.submit();
        }, 2000);
    });
</script>