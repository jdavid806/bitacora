<?php
$wrapper_start = '<div id="js-clock-in-out" class="card dashboard-icon-widget clock-in-out-card" >';
$wrapper_end = '</div>';

if (isset($remove_wrapper) && $remove_wrapper == 1) {
    $wrapper_start = "";
    $wrapper_end = "";
} ?>
<?php echo  $wrapper_start; ?>

<div class="card-body">
    <div class="widget-icon  <?php echo (isset($clock_status->id)) ? 'bg-info' : 'bg-coral'; ?> ">
        <i data-feather="clock" class="icon"></i>
    </div>
    <div class="widget-details">
        <?php
        if (isset($clock_status->id)) {
            if ($clock_status->out_break_time == null) {
                $file = @unserialize($clock_status->photo);
                $img_url = "";
                if ($file) {
                    $file = get_source_url_of_file($file, get_setting("timeline_file_path"), "");
                    $img_url = '<img src="' . $file . '" width="40" height="40" style="object-fit: cover;">';
                }
                echo modal_anchor(
                    get_uri("attendance/note_modal_form/0/$clock_status->id/" . "out_break_time/"),
                    "<i data-feather='coffee' class='icon-16'></i> " . app_lang('clock_out_for_break') . $img_url,
                    array(
                        "class" => "btn btn-default",
                        "title" => app_lang('clock_out_for_break'),
                        "id" => "timecard-clock-out-for-break",
                        "data-post-id" => $clock_status->id,
                        "data-post-clock_out" => 1,
                        "data-post-id" => $clock_status->user_id
                    )
                );

                $in_time = format_to_time($clock_status->in_time);
                $in_datetime = format_to_datetime($clock_status->in_time);
                echo "<div class='mt5 bg-transparent-white' title='$in_datetime'>" . app_lang('clock_started_at') . " : $in_time</div>";
            } elseif ($clock_status->in_break_time == null) {
                $file = @unserialize($clock_status->photo);
                $img_url = "";
                if ($file) {
                    $file = get_source_url_of_file($file, get_setting("timeline_file_path"), "");
                    $img_url = '<img src="' . $file . '" width="40" height="40" style="object-fit: cover;">';
                }
                // El usuario ha iniciado el descanso pero no ha regresado
                echo modal_anchor(
                    get_uri("attendance/note_modal_form/0/$clock_status->id/" . "in_break_time"),
                    "<i data-feather='coffee' class='icon-16'></i> " . app_lang('clock_in_from_break') . $img_url,
                    array(
                        "class" => "btn btn-default",
                        "title" => app_lang('clock_in_from_break'),
                        "id" => "timecard-in-break-time",
                        "data-post-id" => $clock_status->id,
                        "data-post-clock_out" => 1,
                        "data-post-id" => $login_user->id
                    )
                );

                $out_break_time = format_to_time($clock_status->out_break_time);
                $out_break_datetime = format_to_datetime($clock_status->out_break_time);
                echo "<div class='mt5 bg-transparent-white' title='$out_break_datetime'>" . app_lang('break_started_at') . " : $out_break_time</div>";
            } else {
                $file = @unserialize($clock_status->photo);
                $img_url = "";
                if ($file) {
                    $file = get_source_url_of_file($file, get_setting("timeline_file_path"), "");
                    $img_url = '<img src="' . $file . '" width="40" height="40" style="object-fit: cover;">';
                }
                echo modal_anchor(
                    get_uri("attendance/note_modal_form/0/$clock_status->id/" . "clock_out/1"),
                    "<i data-feather='log-out' class='icon-16'></i> " . app_lang('clock_out') . $img_url,
                    array(
                        "class" => "btn btn-default",
                        "title" => app_lang('clock_out'),
                        "id" => "timecard-in-break-time",
                        "data-post-id" => $clock_status->id,
                        "data-post-clock_out" => 1,
                        "data-post-id" => $login_user->id
                    )
                );

                $in_time = format_to_time($clock_status->in_time);
                $in_datetime = format_to_datetime($clock_status->in_time);
                echo "<div class='mt5 bg-transparent-white' title='$in_datetime'>" . app_lang('clock_started_at') . " : $in_time</div>";
            }
        } else {
            echo modal_anchor(
                get_uri("attendance/note_modal_form/0/0/" . "clock_in/"),
                "<i data-feather='log-out' class='icon-16'></i> " . app_lang('clock_in'),
                array(
                    "class" => "btn btn-default",
                    "title" => app_lang('clock_in'),
                    "id" => "timecard-clock-out",
                    "data-post-id" => $login_user->id,
                    "data-post-clock_in" => 1,
                    "data-post-id" => $login_user->id
                )
            );
            echo "<div class='mt5 bg-transparent-white'>" . app_lang('you_are_currently_clocked_out') . "</div>";
        }

        ?>
    </div>
</div>
<?php echo  $wrapper_end; ?>