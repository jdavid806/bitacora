<div class="modal-body clearfix">

    <ul data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs" role="tablist">
        <li class="active"><a class="active" role="presentation" data-bs-toggle="tab"
                href="<?php echo_uri("leads/send_messages/"); ?>"
                data-bs-target="#lead-messages-templates"><?php echo app_lang('templates'); ?></a></li>
        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leads/make_calls/"); ?>"
                data-bs-target="#lead-messages-new-template"><?php echo app_lang('new_template'); ?></a></li>
    </ul>
    <div class="tab-content lead-tab-content">
        <div role="tabpanel" class="tab-pane fade show active" id="lead-messages-templates">
            <div class="table-responsive">
                <table id="template-table" class="display" width="100%"></table>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="lead-messages-new-template">
            <?php echo view("leads/messages/templates/template_form"); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        try {
            var templateTable = $("#template-table").appTable({
                source: '<?php echo_uri("client_messages/list_templates/" . $template_type) ?>',
                columns: [
                    { title: 'Nombre de Plantilla', "class": "all" },
                    { title: 'Tipo' },
                    {
                        title: '<i class="fa fa-bars"></i>',
                        "class": "text-center option w100",
                        "data": function (row) {
                            var buttons = '';
                            buttons += '<li role="presentation">' +
                                '<a class="edit-template" data-id="' + row.id + '" href="#"><i class="fa fa-pencil"></i> Editar</a>' +
                                '</li>';
                            buttons += '<li role="presentation">' +
                                '<a class="delete-template" data-id="' + row.id + '" href="#"><i class="fa fa-times fa-fw"></i> Eliminar</a>' +
                                '</li>';
                            return '<div class="dropdown">'
                                + '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-cog"></i></button>'
                                + '<ul class="dropdown-menu dropdown-menu-right">' + buttons + '</ul>';
                        }
                    }
                ]
            });
        } catch (e) {
            console.log(e);
        }
    });
</script>