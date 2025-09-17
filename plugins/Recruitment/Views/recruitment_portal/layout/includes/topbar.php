<?php load_js(array("assets/js/push_notification/pusher/pusher.min.js")); ?>

<?php
if(isset($login_user)){
    $user = $login_user->id;
}else{
    $user = '';
}
?>

<nav class="navbar navbar-expand fixed-top navbar-light navbar-custom shadow-sm" role="navigation" id="default-navbar">
    <div class="container-fluid">
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link sidebar-toggle-btn" aria-current="page" href="#">
                        <i data-feather="menu" class="icon"></i>
                    </a>
                </li>
            </ul>

            <?php if(isset($login_user)){ ?>
            <div class="d-flex w-auto">
                <ul class="navbar-nav">

                    <li class="nav-item dropdown">
                        <a id="user-dropdown" href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="avatar-xs avatar me-1" >
                                <img alt="..." src="<?php echo candidate_profile_image_url($login_user->id); ?>">
                            </span>
                            <span class="user-name ml10"><?php echo html_entity_decode($login_user->first_name . " " . $login_user->last_name); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end w200">

                            <li><a href="<?php echo_uri('recruitment_portal/profile'); ?>" class="dropdown-item"><i data-feather="user" class='icon-16 me-2'></i> <?php echo app_lang('my_profile'); ?></a></li>
                            <li><a href="<?php echo_uri('recruitment_portal/applied_jobs'); ?>" class="dropdown-item"><i data-feather="check-square" class='icon-16 me-2'></i> <?php echo app_lang('re_applied_jobs'); ?></a></li>
                            <li><a href="<?php echo_uri('recruitment_portal/interview_schedules'); ?>" class="dropdown-item"><i data-feather="calendar" class='icon-16 me-2'></i> <?php echo app_lang('interview_schedules'); ?></a></li>

                            <?php if(get_setting('enable_gdpr')){ ?>
                                <li><a href="<?php echo get_setting('gdpr_terms_and_conditions_link'); ?>" target="_blank" class="dropdown-item"><i data-feather="package" class='icon-16 me-2'></i> <?php echo app_lang('re_gdpr'); ?></a></li>
                            <?php } ?>

                            <?php if (get_setting("show_theme_color_changer") === "yes") { ?>

                                <li class="dropdown-divider"></li>    
                                <li class="pl10 ms-2 mt10 theme-changer">
                                    <?php echo get_custom_theme_color_list(); ?>
                                </li>

                            <?php } ?>

                            <li class="dropdown-divider"></li>
                            <li><a href="<?php echo_uri('candidate_signin/sign_out'); ?>" class="dropdown-item"><i data-feather="log-out" class='icon-16 me-2'></i> <?php echo app_lang('sign_out'); ?></a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php }else{ ?>
            <div class="d-flex w-auto">
                <a href="<?php echo_uri('candidate_signin'); ?>" class="btn btn-primary"><?php echo app_lang('re_sign_in') ?></a>
            </div>
        <?php } ?>

        </div><!--/.nav-collapse -->
    </div>
</nav>

<?php require 'plugins/Recruitment/assets/js/recruitment_portals/layout/includes/topbar_js.php';?>
