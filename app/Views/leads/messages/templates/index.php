<div id="page-content" class="page-wrapper clearfix grid-button leads-view">
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php echo modal_anchor(get_uri("client_messages/template_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('new_template'), array("class" => "btn btn-default", "title" => app_lang('new_template'), "data-post-client_id" => $client_id)); ?>
            </div>
        </div>
    </ul>
    <div class="card border-top-0 rounded-top-0">
        <div class="table-responsive">
            <table id="lead-message-templates-table" class="display" cellspacing="0" width="100%">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#lead-message-templates-table").appTable({
            source: '<?php echo_uri("client_messages/list_templates/" . $template_type . "/" . $client_id) ?>',
            smartFilterIdentity: "all_client_message_templates_list",
            columns: [
                { title: 'Nombre de Plantilla', "class": "all" },
                { title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100" }
            ]
        });
    });
</script>