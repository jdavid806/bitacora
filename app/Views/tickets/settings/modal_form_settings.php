<div class="card">
    <div class='card-header'>
        <i data-feather="layout" class='icon-16 mr10'></i>
        <?php echo $user_id ? app_lang('update_setting') : app_lang('create_setting'); ?>
    </div>
    <?php echo form_open(get_uri("tickets_settings/save"), array("class" => "general-form ticket-setting-form", "id" => "ticket-setting-form", "role" => "form")); ?>
    <input type="hidden" name="id" value="<?php echo $user_id ? $user_id : 0; ?>">
    <div class="modal-body clearfix">
        <div class='row'>
            <div class="form-group row">
                <div class="col-md-6">
                    <label for="user_id" class=" col-md-3"><?php echo app_lang('username'); ?></label>
                    <?php
                    echo form_dropdown('user_id', $users, '', array(
                        'class' => 'form-control',
                        'id' => 'user_id',
                        'name' => 'user_id',
                        'data-rule-required' => true,
                        'data-msg-required' => app_lang('field_required')
                    ));
                    ?>
                </div>
                <div class="col-md-6">
                    <label for="ticket_type_id"><?php echo app_lang('lvl_assignment'); ?></label>
                    <?php
                    echo form_input(array(
                        "id" => "ticket_types",
                        "name" => "ticket_types",
                        "class" => "form-control",
                        "placeholder" => app_lang('labels')
                    ));
                    ?>
                </div>
            </div>
        </div>
        <hr />
        <div id="ticket-cards-container"></div>
        <div class="form-group m0">
            <button type="submit" class="btn btn-primary mr15"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        $("#ticket_types").select2({
            multiple: true,
            data: <?php echo json_encode($ticket_types); ?>
        });

        $("#ticket_types").on('change', function() {
            var selectedTicketTypes = $(this).val().split(','); // Obtener los tipos seleccionados

            var cardsContainer = $("#ticket-cards-container");
            cardsContainer.empty();

            selectedTicketTypes.forEach(function(ticketTypeId) {

                var ticketType = <?php echo json_encode($ticket_types); ?>.find(function(item) {
                    return item.id == ticketTypeId;
                });


                if (ticketType) {
                    var cardHtml = `
                        <div class="card mb-3">
                            <div class="card-body">
                                <label for="ticket_type_${ticketType.id}">${ticketType.text}</label>
                                <input type="number" id="ticket_type_${ticketType.id}" name="ticket_type[${ticketType.id}]" class="form-control" placeholder="<?php echo app_lang("tickets") ?>" />
                                <input type="hidden" id="project_${ticketType.id}" name="project_title[${ticketType.id}]" class="form-control my-2" value="${ticketType.text}" />
                        <input type="number" id="project_count_${ticketType.id}" name="project_count[${ticketType.id}]" class="form-control my-2" placeholder="<?php echo app_lang("tasks") ?>" />
                            </div>
                        </div>
                    `;
                    cardsContainer.append(cardHtml);
                }
            });
        });
    })
</script>