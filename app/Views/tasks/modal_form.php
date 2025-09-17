<?php echo form_open(get_uri("tasks/save"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>
<div id="tasks-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
            <input type="hidden" name="add_type" value="<?php echo $add_type; ?>" />
            <input type="hidden" name="status" value="to_do" />

            <?php
            $contexts_dropdown = array();

            foreach ($contexts as $context) {
                if ($context !== "general") {
                    $context_id_key = $context . "_id";
                    $contexts_dropdown[$context] = app_lang($context);
            ?>

                    <input type="hidden" name="<?php echo $context_id_key; ?>" value="<?php echo ${$context_id_key}; ?>" />

            <?php } else {
                    $contexts_dropdown[$context] = "-";
                }
            } ?>

            <?php if ($is_clone) { ?>
                <input type="hidden" name="is_clone" value="1" />
            <?php } ?>

            <div class="form-group">
                <div class="row">
                    <label for="title" class=" col-md-3"><?php echo app_lang('title'); ?></label>
                    <div class=" col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "title",
                            "name" => "title",
                            "value" => $add_type == "multiple" ? "" : $model_info->title,
                            "class" => "form-control",
                            "placeholder" => app_lang('title'),
                            "autofocus" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="row">
                    <div class="col">
                        <label for="description" class=" col-md-3"><?php echo app_lang('description'); ?></label>
                        <div class="mb-3">
                            <?php
                            echo form_dropdown('template_name', $template_options, set_value('template_name'), array(
                                'class' => 'form-control',
                                'id' => 'template_name',
                                'name' => 'template_name',
                                'data-rule-required' => true,
                                'data-msg-required' => app_lang('field_required')
                            ));
                            ?>
                        </div>
                        <button type="button" class="btn btn-primary"
                            id="preview-description"
                            data-bs-toggle="popover" data-bs-placement="right"
                            data-bs-custom-class="custom-popover"
                            data-bs-title="<?php echo app_lang('preview_description'); ?>"
                            data-bs-content="">
                            <?php echo app_lang('click_to_example'); ?>
                        </button>
                    </div>
                    <div class=" col-md-9">
                        <?php
                        echo form_textarea(array(
                            "id" => "description",
                            "name" => "description",
                            "value" => $add_type == "multiple" ? "" : process_images_from_content($model_info->description, false),
                            "class" => "form-control decription-editor",
                            "placeholder" => app_lang('description'),
                            // "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="user_id" class=" col-md-3"><?php echo app_lang('assign_to'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_dropdown("user_id", $users_assign_to, array($model_info->client_id), "class='select2' id='user_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <label for="priority_id" class=" col-md-3"><?php echo app_lang('priority'); ?></label>
                    <div class="col-md-9">
                        <?php
                        echo form_input(array(
                            "id" => "priority_id",
                            "name" => "priority_id",
                            "value" => $model_info->priority_id,
                            "class" => "form-control",
                            "placeholder" => app_lang('priority'),
                            "data-rule-required" => "true", // Atributo para validación
                            "data-msg-required" => app_lang('field_required')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <?php if ($login_user->user_type === 'staff'): ?>
                <div class="form-group">
                    <div class="row">
                        <label for="project_id" class=" col-md-3"><?php echo app_lang('project'); ?></label>
                        <div class="col-md-9">
                            <?php
                            echo form_dropdown('project_id', $projects, set_value('project_id'), array(
                                'class' => 'form-control validate-hidden',
                                'id' => 'project_id',
                                'name' => 'project_id',
                                'data-rule-required' => true,
                                'data-msg-required' => app_lang('field_required')
                            ));
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <div class="row">
                    <label for="project_labels" class=" col-md-3"><?php echo app_lang('labels'); ?></label>
                    <div class=" col-md-9" id="dropdown-apploader-section">
                        <?php
                        echo form_input(array(
                            "id" => "project_labels",
                            "name" => "labels",
                            "value" => $model_info->labels,
                            "class" => "form-control",
                            "placeholder" => app_lang('labels')
                        ));
                        ?>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <div class="row">
                    <label for="start_date" class="<?php echo $show_time_with_task ? "col-md-3 col-sm-3" : "col-md-3" ?>"><?php echo app_lang('start_date'); ?></label>
                    <div class="<?php echo $show_time_with_task ? "col-md-4 col-sm-4" : "col-md-9" ?> form-group">
                        <?php
                        echo form_input(array(
                            "id" => "start_date",
                            "name" => "start_date",
                            "autocomplete" => "off",
                            "value" => is_date_exists($model_info->start_date) ? date("Y-m-d", strtotime($model_info->start_date)) : date("Y-m-d"),
                            "class" => "form-control",
                            "placeholder" => "YYYY-MM-DD",
                            "disabled" => true
                        ));
                        ?>
                    </div>

                    <?php if ($show_time_with_task) { ?>
                        <label for="start_time" class=" col-md-2 col-sm-2"><?php echo app_lang('start_time'); ?></label>
                        <div class=" col-md-3 col-sm-3 form-group">
                            <?php
                            date_default_timezone_set('America/Bogota');
                            // Verifica si $model_info->start_date existe y es una fecha válida
                            if (isset($model_info->start_date) && is_date_exists($model_info->start_date)) {
                                $start_date = $model_info->start_date;
                            } else {
                                $start_date = ""; // O un valor predeterminado
                            }

                            // Si no hay fecha válida, usa la hora actual
                            if ($time_format_24_hours) {
                                // Si el formato es de 24 horas
                                $start_time = $start_date ? date("H:i", strtotime($start_date)) : date("H:i");
                            } else {
                                // Si el formato es de 12 horas
                                if ($start_date && date("H:i:s", strtotime($start_date)) == "00:00:00") {
                                    $start_time = "";
                                } else {
                                    $start_time = $start_date ? convert_time_to_12hours_format(date("H:i:s", strtotime($start_date))) : convert_time_to_12hours_format(date("H:i:s"));
                                }
                            }

                            // Depuración de start_time

                            echo form_input(array(
                                "id" => "start_time",
                                "name" => "start_time",
                                "value" => $start_time,
                                "class" => "form-control",
                                "placeholder" => app_lang('start_time'),
                                "disabled" => true
                            ));
                            ?>


                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="clearfix">
                <div class="row">
                    <label for="deadline" class="<?php echo $show_time_with_task ? "col-md-3 col-sm-3" : "col-md-3" ?>"><?php echo app_lang('deadline'); ?></label>
                    <div class="<?php echo $show_time_with_task ? "col-md-4 col-sm-4" : "col-md-9" ?> form-group">
                        <?php
                        echo form_input(array(
                            "id" => "deadline",
                            "name" => "deadline",
                            "autocomplete" => "off",
                            "value" => is_date_exists($model_info->deadline) ? date("Y-m-d", strtotime($model_info->deadline)) : "",
                            "class" => "form-control",
                            "placeholder" => "YYYY-MM-DD",
                            "data-rule-greaterThanOrEqual" => "#start_date",
                            "data-msg-greaterThanOrEqual" => app_lang("deadline_must_be_equal_or_greater_than_start_date"),
                            "disabled" => true
                        ));
                        ?>
                    </div>

                    <?php if ($show_time_with_task) { ?>
                        <label for="end_time" class=" col-md-2 col-sm-2"><?php echo app_lang('end_time'); ?></label>
                        <div class=" col-md-3 col-sm-3 form-group">
                            <?php
                            $deadline = (is_date_exists($model_info->deadline)) ? $model_info->deadline : "";
                            if ($time_format_24_hours) {
                                $end_time = $deadline ? date("H:i", strtotime($deadline)) : "";
                            } else {
                                if (date("H:i:s", strtotime($deadline)) == "00:00:00") {
                                    $end_time = "";
                                } else {
                                    $end_time = $deadline ? convert_time_to_12hours_format(date("H:i:s", strtotime($deadline))) : "";
                                }
                            }
                            echo form_input(array(
                                "id" => "end_time",
                                "name" => "end_time",
                                "value" => $end_time,
                                "class" => "form-control",
                                "placeholder" => app_lang('end_time'),
                                "disabled" => true
                            ));
                            ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="alert alert-primary" role="alert">
                <a data-feather="alert-circle" class="icon-16 mx-2"></a><?php echo app_lang("msg_info_time_to_tasks") ?>
            </div>

            <?php if (get_setting("enable_recurring_option_for_tasks")) { ?>

                <div class="form-group">
                    <div class="row">
                        <label for="recurring" class=" col-md-3"><?php echo app_lang('recurring'); ?> <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('cron_job_required'); ?>"><i data-feather="help-circle" class="icon-16"></i></span></label>
                        <div class=" col-md-9">
                            <?php
                            echo form_checkbox("recurring", "1", $model_info->recurring ? true : false, "id='recurring' class='form-check-input'");
                            ?>
                        </div>
                    </div>
                </div>

                <div id="recurring_fields" class="<?php if (!$model_info->recurring) echo "hide"; ?>">
                    <div class="form-group">
                        <div class="row">
                            <label for="repeat_every" class=" col-md-3"><?php echo app_lang('repeat_every'); ?></label>
                            <div class="col-md-4">
                                <?php
                                echo form_input(array(
                                    "id" => "repeat_every",
                                    "name" => "repeat_every",
                                    "type" => "number",
                                    "value" => $model_info->repeat_every ? $model_info->repeat_every : 1,
                                    "min" => 1,
                                    "class" => "form-control recurring_element",
                                    "placeholder" => app_lang('repeat_every'),
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required")
                                ));
                                ?>
                            </div>
                            <div class="col-md-5">
                                <?php
                                echo form_dropdown(
                                    "repeat_type",
                                    array(
                                        "days" => app_lang("interval_days"),
                                        "weeks" => app_lang("interval_weeks"),
                                        "months" => app_lang("interval_months"),
                                        "years" => app_lang("interval_years"),
                                    ),
                                    $model_info->repeat_type ? $model_info->repeat_type : "months",
                                    "class='select2 recurring_element' id='repeat_type'"
                                );
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <label for="no_of_cycles" class=" col-md-3"><?php echo app_lang('cycles'); ?></label>
                            <div class="col-md-4">
                                <?php
                                echo form_input(array(
                                    "id" => "no_of_cycles",
                                    "name" => "no_of_cycles",
                                    "type" => "number",
                                    "min" => 1,
                                    "value" => $model_info->no_of_cycles ? $model_info->no_of_cycles : "",
                                    "class" => "form-control",
                                    "placeholder" => app_lang('cycles')
                                ));
                                ?>
                            </div>
                            <div class="col-md-5 mt5">
                                <span class="help" data-bs-toggle="tooltip" title="<?php echo app_lang('recurring_cycle_instructions'); ?>"><i data-feather="help-circle" class="icon-16"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group hide" id="next_recurring_date_container">
                        <div class="row">
                            <label for="next_recurring_date" class=" col-md-3"><?php echo app_lang('next_recurring_date'); ?> </label>
                            <div class=" col-md-9">
                                <?php
                                echo form_input(array(
                                    "id" => "next_recurring_date",
                                    "name" => "next_recurring_date",
                                    "class" => "form-control",
                                    "placeholder" => app_lang('next_recurring_date'),
                                    "autocomplete" => "off",
                                    "data-rule-required" => true,
                                    "data-msg-required" => app_lang("field_required"),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?>

            <?php echo view("includes/dropzone_preview"); ?>

            <?php if ($is_clone) { ?>
                <?php if ($has_checklist) { ?>
                    <div class="form-group">
                        <label for="copy_checklist" class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_checklist", "1", true, "id='copy_checklist' class='float-start mr15 form-check-input'");
                            ?>
                            <?php echo app_lang('copy_checklist'); ?>
                        </label>
                    </div>
                <?php } ?>

                <?php if ($has_sub_task) { ?>
                    <div class="form-group">
                        <label for="copy_sub_tasks" class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_sub_tasks", "1", false, "id='copy_sub_tasks' class='float-start mr15 form-check-input'");
                            ?>
                            <?php echo app_lang('copy_sub_tasks'); ?>
                        </label>
                    </div>
                <?php } ?>

                <?php if ($model_info->parent_task_id) { ?>
                    <input type="hidden" name="parent_task_id" value="<?php echo $model_info->parent_task_id; ?>" />
                    <div class="form-group">
                        <label for="create_as_a_non_subtask" class=" col-md-12">
                            <?php
                            echo form_checkbox("create_as_a_non_subtask", "1", false, "id='create_as_a_non_subtask' class='float-start mr15 form-check-input'");
                            ?>
                            <?php echo app_lang('create_as_a_non_subtask'); ?>
                        </label>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="modal-footer">
        <div id="link-of-new-view" class="hide">
            <?php
            echo modal_anchor(get_uri("tasks/view"), "", array("data-modal-lg" => "1"));
            ?>
        </div>

        <?php
        if (!$model_info->id || $add_type == "multiple") {
            echo view("includes/upload_button");
        }
        ?>

        <button type="button" class="btn btn-default hidden-xs" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

        <?php if ($add_type == "multiple") { ?>
            <button id="save-and-add-button" type="button" class="btn btn-primary save-button"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_add_more'); ?></button>
        <?php } else { ?>
            <?php if ($view_type !== "details") { ?>
                <button id="save-and-show-button" type="button" class="btn btn-info text-white save-button"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_show'); ?></button>
            <?php } ?>
            <button id="save-button" type="submit" class="btn btn-primary save-button"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        let $popoverButton = $('button[data-bs-toggle="popover"]');
        $popoverButton.attr('disabled', 'disabled');

        var textarea = document.querySelectorAll('.decription-editor');
        textarea.forEach(item => {
            sceditor.create(item, {
                format: 'bbcode',
                style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css',
                toolbar: 'bold,italic,underline,strike|left,center,right,justify|cut,copy,paste,pastetext|bulletlist,orderedlist|code,quote,link,unlink',
                height: "20em",
                width: "100%"
            });

            $('#template_name').change(function() {
                let templateId = $(this).val();

                if (templateId) {
                    $.ajax({
                        url: "<?php echo get_uri('footer_templates/list_data'); ?>" + "/" + templateId,
                        dataType: "json",
                        success: function(response) {
                            if (response.content) {
                                var editor = sceditor.instance(item);

                                if (editor) {
                                    editor.val(response.content);
                                }

                                $popoverButton.removeAttr('disabled');
                                $popoverButton.popover('dispose');

                                $popoverButton.attr('data-bs-content', formatPreviewContent(response.preview));
                                $popoverButton.popover({
                                    trigger: 'focus',
                                    placement: 'right'
                                });

                            }
                        },
                        error: function(error) {
                            var editor = sceditor.instance(item);
                            editor.val('');
                            alert("Error al obtener el contenido de la plantilla.");
                            $popoverButton.attr('disabled', 'disabled');
                            $('button[data-bs-toggle="popover"]').attr('data-bs-content', '').popover('dispose');
                        }
                    });
                } else {
                    var editor = sceditor.instance(item);
                    if (editor) {
                        editor.val('');
                    }
                }
            });

        })


        let valueProyectType = $('#project_id').val();

        $('#project_id').change(function() {
            const projectType = $(this).find('option:selected').text().toLowerCase();
            const projectTypeValue = $(this).val();
            const startDate = $('#start_date').val();
            const startTime = $('#start_time').val();

            const [time, period] = startTime.split(' '); // Divide "1:53 PM" en ["1:53", "PM"]
            const [hours, minutes] = time.split(':').map(Number); // Divide "1:53" en ["1", "53"]

            let hours24 = hours; // Por defecto, las horas son las mismas
            if (period === 'PM' && hours !== 12) {
                hours24 = hours + 12; // Convierte PM a formato 24 horas
            } else if (period === 'AM' && hours === 12) {
                hours24 = 0; // Convierte 12 AM a 00:00
            }

            // Convierte la fecha startDate a un objeto Date
            const startDateParts = startDate.split('-'); // [DD, MM, YYYY]
            const startDateTime = new Date(startDateParts[2], startDateParts[1] - 1, startDateParts[0]); // Ahora se ajusta correctamente

            // Establece la hora y los minutos en el objeto startDateTime
            startDateTime.setHours(hours24);
            startDateTime.setMinutes(minutes);

            if (!startDate) {
                alert('Por favor selecciona una fecha de inicio.');
                return;
            }

            // Define las horas según el tipo de proyecto
            let additionalHours = 0;
            switch (projectType) {
                case 'baja complejidad':
                    additionalHours = 24;
                    break;
                case 'media complejidad':
                    additionalHours = 48;
                    break;
                case 'alta complejidad':
                case 'desarrollos':
                    additionalHours = 72;
                    break;
                default:
                    alert('Tipo de proyecto no válido.');
                    return;
            }

            startDateTime.setHours(startDateTime.getHours() + additionalHours);

            // Formatea la nueva fecha en formato DD-MM-YYYY para 'deadline'
            const day = String(startDateTime.getDate()).padStart(2, '0');
            const month = String(startDateTime.getMonth() + 1).padStart(2, '0'); // Los meses en JS van de 0 a 11
            const year = startDateTime.getFullYear();

            let validateDate = new Date(`${year}-${month}-${day}`);

            validateDate.setDate(validateDate.getDate() + 1);

            if (validateDate.getDay() == 0) {
                validateDate.setDate(validateDate.getDate() + 1);
            }

            const deadline = `${validateDate.getDate()}-${String(validateDate.getMonth() + 1).padStart(2, '0')}-${validateDate.getFullYear()}`; // Formato DD-MM-YYYY


            // Convierte la hora de 24 horas a 12 horas para 'end_time'
            let endHours = startDateTime.getHours();
            let endPeriod = 'AM';

            if (endHours >= 12) {
                if (endHours > 12) endHours -= 12; // Ajusta la hora de 24h a 12h
                endPeriod = 'PM';
            } else {
                if (endHours === 0) endHours = 12; // Convierte 00 a 12 (AM)
            }

            const endTime = `${endHours}:${startDateTime.getMinutes().toString().padStart(2, '0')} ${endPeriod}`;

            // Actualiza los inputs de fecha y hora
            $('#deadline').val(deadline); // El input ahora recibe la fecha en formato DD-MM-YYYY
            $('#end_time').val(endTime);
        });



        function formatPreviewContent(previewContent) {
            previewContent = previewContent.replace(/<b>/g, '**').replace(/<\/b>/g, '**');

            previewContent = previewContent.replace(/<p>/g, '\n').replace(/<\/p>/g, '');

            return previewContent;
        }

        const saveButtons = document.querySelectorAll(".save-button");

        saveButtons.forEach(button => {
            button.addEventListener("click", function() {
                const startDateInput = document.getElementById("start_date");
                const endDateInput = document.getElementById("deadline");
                const startTimeInput = document.getElementById("start_time");
                const endTimeInput = document.getElementById("end_time");

                startDateInput.removeAttribute("disabled");
                endDateInput.removeAttribute("disabled");
                startTimeInput.removeAttribute("disabled");
                endTimeInput.removeAttribute("disabled");

            });
        });



        //send data to show the task after save
        window.showAddNewModal = false;

        $("#save-and-show-button, #save-and-add-button").click(function() {
            window.showAddNewModal = true;
            $(this).trigger("submit");
        });

        var taskShowText = "<?php echo app_lang('task_info') ?>",
            multipleTaskAddText = "<?php echo app_lang('add_multiple_tasks') ?>",
            addType = "<?php echo $add_type; ?>";

        window.taskForm = $("#task-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function(result) {
                $("#task-table").appTable({
                    newData: result.data,
                    dataId: result.id
                });
                $("#reload-kanban-button:visible").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

                if (window.showAddNewModal) {
                    var $taskViewLink = $("#link-of-new-view").find("a");

                    if (addType === "multiple") {
                        //add multiple tasks
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("tasks/modal_form"); ?>");
                        $taskViewLink.attr("data-title", multipleTaskAddText);
                        $taskViewLink.attr("data-post-last_id", result.id);
                        $taskViewLink.attr("data-post-project_id", "<?php echo isset($project_id) ? $project_id : ''; ?>");
                        $taskViewLink.attr("data-post-add_type", "multiple");
                    } else {
                        //save and show
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("tasks/view"); ?>");
                        $taskViewLink.attr("data-title", taskShowText + " #" + result.id);
                        $taskViewLink.attr("data-post-id", result.id);
                    }

                    $taskViewLink.trigger("click");
                } else {
                    window.taskForm.closeModal();

                    if (window.refreshAfterAddTask) {
                        window.refreshAfterAddTask = false;
                        location.reload();
                    }
                }

                window.reloadKanban = true;

                if (typeof window.reloadGantt === "function") {
                    window.reloadGantt(true);
                }
            },
            onAjaxSuccess: function(result) {
                if (!result.success && result.next_recurring_date_error) {
                    $("#next_recurring_date").val(result.next_recurring_date_value);
                    $("#next_recurring_date_container").removeClass("hide");

                    $("#task-form").data("validator").showErrors({
                        "next_recurring_date": result.next_recurring_date_error
                    });
                }
            }
        });
        $("#task-form .select2").select2();
        setTimeout(function() {
            $("#title").focus();
        }, 200);

        setDatePicker("#start_date");

        setDatePicker("#deadline", {
            endDate: "<?php echo $project_deadline; ?>"
        });

        setTimePicker("#start_time, #end_time");

        $('[data-bs-toggle="tooltip"]').tooltip();

        //show/hide recurring fields
        $("#recurring").click(function() {
            if ($(this).is(":checked")) {
                $("#recurring_fields").removeClass("hide");
            } else {
                $("#recurring_fields").addClass("hide");
            }
        });

        setDatePicker("#next_recurring_date", {
            startDate: moment().add(1, 'days').format("YYYY-MM-DD") //set min date = tomorrow
        });


    });
</script>

<?php
echo view("tasks/get_dropdowns_script", array(
    "related_to_dropdowns" => [],
    "milestones_dropdown" => $milestones_dropdown,
    "assign_to_dropdown" => $assign_to_dropdown,
    "collaborators_dropdown" => $collaborators_dropdown,
    "statuses_dropdown" => $statuses_dropdown,
    "label_suggestions" => $label_suggestions,
    "priorities_dropdown" => $priorities_dropdown
));
