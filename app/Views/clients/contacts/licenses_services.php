<div class="tab-content">
    <?php
    echo form_open(get_uri("clients/connect_instance"), array("id" => "connect-instance-form", "class" => "general-form dashed-row white", "role" => "form"));
    ?>
    <div class="card border-top-0 rounded-top-0">
        <div class="card-header">
            <h4><?php echo app_lang('items'); ?></h4>
        </div>
        <div class="card-body">
            <div class="form-group">
                <div class="row d-flex align-items-center">
                    <div class="col-md-2">
                        <?php echo app_lang('whatsapp_license'); ?>
                    </div>
                    <div class="col-md-10 d-flex justify-content-between align-items-center">
                        <div class="title-button-group">
                            <span class="dropdown inline-block mt15">
                                <button class="btn btn-info text-white dropdown-toggle caret mt0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true" <?php echo empty($instance_info) ? 'disabled' : ''; ?>>
                                    <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li role="presentation"><?php echo modal_anchor(get_uri("api_evolution/connect_instance/" . $user_info->client_id), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('instance_connect'), array("title" => app_lang('instance_connect'), "id" => "connect-instance-EA", "class" => "list-group-item", "data-modal-lg" => "1")); ?> </li>
                                    <li role="presentation"><?php echo anchor(get_uri("api_evolution/restart_instance/" . $user_info->client_id), "<i data-feather='rotate-cw' class='icon-16'></i> " . app_lang('restart_instance'), array("title" => app_lang('restart_instance'), "class" => "dropdown-item")); ?> </li>
                                </ul>
                            </span>
                        </div>
                        <?php if (isset($instance_info) && !empty($instance_info)) : ?>
                            <div style="width: fit-content;" class="p-1 text-primary-emphasis border border-primary-subtle rounded-3 d-flex align-items-center justify-content-center
                    <?php
                            switch ($instance_info->status_) {
                                case 'connect':
                                    echo 'bg-primary-subtle';
                                    break;
                                case 'connecting':
                                    echo 'bg-warning';
                                    break;
                                case 'logout':
                                case 'delete':
                                    echo 'bg-danger';
                                    break;
                                default:
                                    echo 'bg-secondary';
                                    break;
                            }
                    ?>">
                                <?php
                                switch ($instance_info->status_) {
                                    case 'connect':
                                        echo app_lang('connected');
                                        break;
                                    case 'logout':
                                        echo app_lang('disconnected');
                                        break;
                                    case 'connecting':
                                        echo app_lang('connecting');
                                        break;
                                    case 'delete':
                                        echo app_lang('deleted');
                                        break;
                                    default:
                                        echo app_lang('without_instance_created');
                                        break;
                                }
                                ?>
                            </div>
                        <?php else : ?>
                            <div class="p-1 text-secondary border border-secondary rounded-3 d-flex align-items-center justify-content-center">
                                <?php
                                echo app_lang('without_instance_created');
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#connect-instance-form").appForm({
            isModal: false,
            onSuccess: function(result) {
                appAlert.success(result.message, {
                    duration: 10000
                });
            }
        });
    });
</script>