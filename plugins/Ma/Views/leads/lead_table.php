<div class="d-flex w-full justify-content-end">
    <?php echo modal_anchor(
        get_uri("client_messages/modal_form_massive"),
        "<i data-feather='send' class='icon-16'></i> " . app_lang('send_new_massive_message'),
        array(
            "class" => "btn btn-default",
            "title" => app_lang('send_new_massive_message'),
            "id" => "send-massively"
        )
    ); ?>
</div>

<div class="table-responsive">
    <table id="lead-table" class="display" cellspacing="0" width="100%">            
    </table>
</div>

<?php require 'plugins/Ma/assets/js/lead_table_js.php';?>
