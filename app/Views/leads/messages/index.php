<div id="page-content" class="page-wrapper clearfix grid-button leads-view">

    <ul data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs" role="tablist">
        <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("leads/send_messages/" . $client_id); ?>"
                data-bs-target="#lead-messages"> <?php echo app_lang('messages'); ?></a></li>
        <li><a role="presentation" data-bs-toggle="tab"
                href="<?php echo_uri("client_messages/templates/" . $client_id); ?>"
                data-bs-target="#lead-message-templates">
                <?php echo app_lang('templates'); ?></a></li>
    </ul>
    <div class="tab-content lead-tab-content">
        <div role="tabpanel" class="tab-pane fade" id="lead-messages"></div>
        <div role="tabpanel" class="tab-pane fade" id="lead-message-templates"></div>
    </div>
</div>