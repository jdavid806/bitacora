<?php echo form_open("client_messages/save", array("id" => "lead-comment-form", "class" => "general-form")); ?>
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <?php echo view("leads/messages/form_fields", ["client_id" => $client_id]); ?>
        </div>
    </div>
<?php echo form_close(); ?>