<script type="text/javascript">
  (function() {
    "use strict";
    $(document).ready(function() {

      setInterval(() => {
        var $itemWrapper = $("#lead-table");
        var $items;

        if ($itemWrapper.hasClass('dataTable')) {
          $items = $itemWrapper.find("tbody tr");
        } else {
          $items = $itemWrapper.find(".kanban-item");
        }

        var hasSelectedItem = $items.is(".batch-operation-selected");

        if (hasSelectedItem) {
          $("#send-massively").show();
        } else {
          $("#send-massively").hide();
        }
      }, 150)

      const dataTable = $("#lead-table").appTable({
        source: '<?php echo_uri("ma/leads_list_data") ?>',
        smartFilterIdentity: "all_leads_list_for_massive_messaging",
        selectionHandler: {},
        columns: [{
            title: "<?php echo app_lang("company_name") ?>"
          },
          {
            title: "<?php echo app_lang("primary_contact") ?>"
          },
          {
            title: "<?php echo app_lang("owner") ?>"
          },
          {
            visible: false,
            searchable: false
          },
          {
            title: "<?php echo app_lang("created_date") ?>",
            "iDataSort": 3
          },
          {
            title: "<?php echo app_lang("status") ?>"
          },
          {
            title: "<?php echo app_lang("point") ?>"
          },
        ],
        filterDropdown: [{
            name: "status",
            class: "w200",
            options: <?php echo view("leads/lead_statuses"); ?>
          },
          {
            name: "source",
            class: "w200",
            options: <?php echo view("leads/lead_sources"); ?>
          }
        ],
        filterParams: {
          rel_id: "<?php echo html_entity_decode($rel_id); ?>",
          rel_type: "<?php echo html_entity_decode($rel_type); ?>"
        },
        rangeDatepicker: [{
          startDate: {
            name: "start_date",
            value: ""
          },
          endDate: {
            name: "end_date",
            value: ""
          },
          showClearButton: true
        }],
        printColumns: combineCustomFieldsColumns([0, 1, 2]),
        xlsColumns: combineCustomFieldsColumns([0, 1, 2])
      });
    });
  })(jQuery);
</script>