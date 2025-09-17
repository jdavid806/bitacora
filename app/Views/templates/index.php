<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        <div class="col-sm-3 col-lg-2">
            <?php
            $tab_view['active_tab'] = "whatsapp_templates";
            echo view("settings/tabs", $tab_view);
            ?>
        </div>
        <div class="col-sm-9 col-lg-10">
            <div class="row">
                <div class="col-md-3">
                    <div id="template-list-box" class="card bg-white">
                        <div class="page-title clearfix">
                            <h4> <?php echo app_lang('whatsapp_templates'); ?></h4>
                        </div>

                        <ul class="nav nav-tabs vertical settings p15 d-block" role="tablist">
                            <?php
                            foreach ($templates as $template => $value) {

                                //collapse the selected template tab panel
                                $collapse_in = "";
                                $collapsed_class = "collapsed";
                            ?>
                                <div class="clearfix settings-anchor <?php echo $collapsed_class; ?>" data-bs-toggle="collapse" data-bs-target="#settings-tab-<?php echo $template; ?>">
                                    <?php echo app_lang($template); ?>
                                </div>
                            <?php
                                echo "<div id='settings-tab-$template' class='collapse $collapse_in'>";
                                echo "<ul class='mx-2 list-group help-catagory'>";

                                foreach ($value as $sub_template_name => $sub_template) {
                                    $sub_collapse_in = "";
                                    $sub_collapsed_class = "collapsed";
                                    $access_type_arr = ['internal', 'external'];

                                    echo "<div class='clearfix settings-anchor " . $sub_collapsed_class . "' data-bs-toggle='collapse' data-bs-target='#settings-tab-" . $sub_template_name . "'>
                                    " . app_lang($sub_template_name) . " 
                                    </div>
                                    <div id='settings-tab-$sub_template_name' class='collapse $sub_collapse_in'>
                                        <ul class='list-group help-catagory'>" .
                                        (($sub_template_name == 'estimate_accepted') || ($sub_template_name == 'add_meet') || ($sub_template_name == 'modify_meet') || ($sub_template_name == 'reminder') ?
                                            "<div class='d-flex align-items-center justify-content-between m-2' >
                                            <div>
                                                <span>Cliente</span>
                                            </div>
                                            <div>
                                                <div class='dropdown-content'>
                                                <button class='btn btn-secondary dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                                    <i data-feather='settings'></i>
                                                </button>
                                                <ul class='dropdown-menu'>
                                                    <li class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_client_internal'><a class='dropdown-item' href='#'>Interno</a></li>
                                                    <li class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_client_external'><a class='dropdown-item' href='#'>Externo</a></li>
                                                    <li class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_client'><a class='dropdown-item' href='#'>No aplica</a></li>
                                                </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='d-flex align-items-center justify-content-between m-2' >
                                            <div>
                                                <span>Lead</span>
                                            </div>
                                            <div>
                                                <div class='dropdown-content'>
                                                <button class='btn btn-secondary dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                                    <i data-feather='settings'></i>
                                                </button>
                                                <ul class='dropdown-menu'>
                                                    <li class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_lead_internal'><a class='dropdown-item' href='#'>Interno</a></li>
                                                    <li class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_lead_external'><a class='dropdown-item' href='#'>Externo</a></li>
                                                    <li class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_lead'><a class='dropdown-item' href='#'>No aplica</a></li>
                                                </ul>
                                                </div>
                                            </div>
                                        </div>" :
                                            "<span class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_client'>Cliente</span>
                                            <span class='generic-template-row list-group-item clickable' data-name='" . $sub_template_name . "_lead'>Lead</span>") .
                                        "<ul>
                                    </div>";
                                }

                                echo "</ul>";
                                echo "</div>";
                            }
                            ?>
                        </ul>

                    </div>
                </div>
                <div class="col-md-9">
                    <div id="template-details-section">
                        <div id="empty-template" class="text-center p15 box card ">
                            <div class="box-content" style="vertical-align: middle; height: 100%">
                                <div><?php echo app_lang("select_a_template"); ?></div>
                                <span data-feather="code" width="15rem" height="15rem" style="color:rgba(128, 128, 128, 0.1)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function() {

        /*load a template details*/
        $(".generic-template-row").click(function() {
            //don't load this message if already has selected.
            $(".generic-template-row").removeClass("active");
            if (!$(this).hasClass("active")) {
                var template_name = $(this).attr("data-name");
                var template_language = "";
                if (template_name) {
                    $(".icon-container").removeClass("active");
                    $(this).addClass("active");
                    $.ajax({
                        url: "<?php echo get_uri("templates/form"); ?>/" + template_name + "/" + template_language,
                        success: function(result) {
                            $("#template-details-section").html(result);
                            $(".template-form-tab").trigger("click");
                        }
                    });
                }
            }
        });
    });
</script>