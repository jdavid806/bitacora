<div id="page-content" class="clearfix page-wrapper">
    <div class="clearfix leads-details-view">
        <div class="row">
            <div class="col-md-12">
                <div class="page-title clearfix no-border no-border-top-radius no-bg leads-page-title">
                    <h1 class="pl0">
                        <?php echo app_lang('lead_details') . " - " . $lead_info->company_name ?>
                    </h1>
                    <div class="title-button-group mr0">
                        <?php
                        if (can_access_reminders_module()) {
                            echo modal_anchor(get_uri("events/reminders/") . $lead_info->id, "<i data-feather='clock' class='icon-16'></i> " . app_lang('reminders'), array("class" => "btn btn-default mr10", "id" => "reminder-icon", "data-post-lead_id" => $lead_info->id, "data-post-reminder_view_type" => "client", "lead" => app_lang('reminders') . " (" . app_lang('private') . ")"));
                        }
                        ?>
                        <?php
                        if ($is_discard != 1) {
                            echo modal_anchor(get_uri("notes/modal_form_discard/") . $lead_info->id, "<i data-feather='x' class='icon-16'></i> " . app_lang('discard'), array("class" => "btn btn-danger float-end mr15", "title" => app_lang('discard')));
                        }
                        ?>
                        <?php echo modal_anchor(get_uri("leads/make_client_modal_form/") . $lead_info->id, "<i data-feather='briefcase' class='icon-16'></i> " . app_lang('make_client'), array("class" => "btn btn-primary float-end mr15", "title" => app_lang('make_client'))); ?>
                    </div>
                </div>

                <ul data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs" role="tablist">
                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("leads/contacts/" . $lead_info->id); ?>"
                            data-bs-target="#lead-contacts"> <?php echo app_lang('contacts'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("leads/company_info_tab/" . $lead_info->id); ?>"
                            data-bs-target="#lead-info"> <?php echo app_lang('lead_info'); ?></a></li>
                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("leads/tasks/" . $lead_info->id); ?>"
                            data-bs-target="#lead-tasks"><?php echo app_lang('tasks'); ?></a></li>


                    <?php if ($show_estimate_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/estimates/" . $lead_info->id); ?>"
                                data-bs-target="#lead-estimates"> <?php echo app_lang('estimates'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_estimate_request_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/estimate_requests/" . $lead_info->id); ?>"
                                data-bs-target="#lead-estimate-requests"> <?php echo app_lang('estimate_requests'); ?></a>
                        </li>
                    <?php } ?>
                    <?php if ($show_proposal_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/proposals/" . $lead_info->id); ?>"
                                data-bs-target="#lead-proposals"> <?php echo app_lang('proposals'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_contract_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/contracts/" . $lead_info->id); ?>"
                                data-bs-target="#lead-contracts"> <?php echo app_lang('contracts'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_ticket_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/tickets/" . $lead_info->id); ?>" data-bs-target="#lead-tickets">
                                <?php echo app_lang('tickets'); ?></a></li>
                    <?php } ?>
                    <?php if ($show_note_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/notes/" . $lead_info->id); ?>" data-bs-target="#lead-notes">
                                <?php echo app_lang('notes'); ?></a></li>
                    <?php } ?>
                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("leads/files/" . $lead_info->id); ?>"
                            data-bs-target="#lead-files"><?php echo app_lang('files'); ?></a></li>

                    <?php if ($show_event_info) { ?>
                        <li><a role="presentation" data-bs-toggle="tab"
                                href="<?php echo_uri("leads/events/" . $lead_info->id); ?>" data-bs-target="#lead-events">
                                <?php echo app_lang('events'); ?></a></li>
                    <?php } ?>


                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("mailbox/clientEmails/" . $lead_info->id . "/lead"); ?>"
                            data-bs-target="#mailbox_client_emails"> <?php echo app_lang('email'); ?></a></li>

                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("leads/messages/" . $lead_info->id); ?>"
                            data-bs-target="#lead-send-messages"><?php echo app_lang('send_messages'); ?></a></li>

                    <li><a role="presentation" data-bs-toggle="tab"
                            href="<?php echo_uri("leads/make_calls/" . $lead_info->id); ?>"
                            data-bs-target="#lead-make-calls"><?php echo app_lang('make_calls'); ?></a></li>
                </ul>
                <div class="tab-content lead-tab-content">
                    <div role="tabpanel" class="tab-pane fade" id="lead-projects"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-files"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-info"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-contacts"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-contracts"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-estimates"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-proposals"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-estimate-requests"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-tickets"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-notes"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-tasks"></div>
                    <div role="tabpanel" class="tab-pane" id="lead-events" style="min-height: 300px"></div>
                    <div role="tabpanel" class="tab-pane fade" id="mailbox_client_emails"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-send-messages"></div>
                    <div role="tabpanel" class="tab-pane fade" id="lead-make-calls"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        var tab = "<?php echo $tab; ?>";
        if (tab === "info") {
            $("[data-bs-target='#lead-info']").trigger("click");
        }

    });
</script>