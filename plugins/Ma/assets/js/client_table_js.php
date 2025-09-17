<script type="text/javascript">
    loadClientsTable = function (selector) {
    var showInvoiceInfo = true;
    if (!"<?php echo $show_invoice_info; ?>") {
    showInvoiceInfo = false;
    }

    var showOptions = false;

    var quick_filters_dropdown = <?php echo view("clients/quick_filters_dropdown"); ?>;
    if (window.selectedClientQuickFilter){
    var filterIndex = quick_filters_dropdown.findIndex(x => x.id === window.selectedClientQuickFilter);
    if ([filterIndex] > - 1){
    //match found
    quick_filters_dropdown[filterIndex].isSelected = true;
    }
    }

    $(selector).appTable({
    source: '<?php echo_uri("ma/clients_list_data") ?>',
            filterDropdown: [
            {name: "group_id", class: "w200", options: <?php echo $groups_dropdown; ?>}
<?php if ($login_user->is_admin || get_array_value($login_user->permissions, "client") === "all") { ?>
                , {name: "created_by", class: "w200", options: <?php echo $team_members_dropdown; ?>}
<?php } ?>
            ],
            filterParams: {rel_id: "<?php echo html_entity_decode($rel_id); ?>", rel_type: "<?php echo html_entity_decode($rel_type); ?>"},
            columns: [
            {title: "<?php echo app_lang("id") ?>", "class": "text-center w50"},
            {title: "<?php echo app_lang("company_name") ?>"},
            {title: "<?php echo app_lang("primary_contact") ?>"},
            {title: "<?php echo app_lang("client_groups") ?>"},
            {title: "<?php echo app_lang("projects") ?>"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang("total_invoiced") ?>"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang("payment_received") ?>"},
            {visible: showInvoiceInfo, searchable: showInvoiceInfo, title: "<?php echo app_lang("due") ?>"},
            {title: "<?php echo app_lang("point") ?>"},
            ],
            printColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6]),
            xlsColumns: combineCustomFieldsColumns([0, 1, 2, 3, 4, 5, 6])
    });
    };

(function(){
  "use strict";
    $(document).ready(function () {
    loadClientsTable("#client-table");
    });

  })(jQuery);

</script>