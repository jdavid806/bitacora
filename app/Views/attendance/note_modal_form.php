<?php
$type = $info_attendance["type"];
$id_attendance = $info_attendance["id_attendance"];

$url = "attendance/save_note";
if ($clock_out == "1" || !$info_attendance["is_break_finish"]) {
    $url = "attendance/log_time/$user_id/$type/$id_attendance";
}

echo form_open(get_uri($url), array("id" => "attendance-note-form", "class" => "general-form", "role" => "form"));
?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

    <?php if ($info_attendance["is_break_finish"]): ?>
        <div class="form-group">
            <label for="note" class=" col-md-12"><?php echo app_lang('note'); ?></label>
            <div class=" col-md-12">
                <?php
                echo form_textarea(array(
                    "id" => "note",
                    "name" => "note",
                    "class" => "form-control",
                    "placeholder" => app_lang('note'),
                    "value" => process_images_from_content($model_info->note, false),
                    "data-rich-text-editor" => true
                ));
                ?>
            </div>
            <input name="clock_out" type="hidden" value="<?php echo $clock_out; ?>" />
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="camera" class="col-md-12">Tomar una Foto</label>
        <div class="col-md-12">
            <div class="w-100 h-100 d-flex gap-2">
                <video id="camera" width="50%" autoplay></video>
                <img id="photo-preview" src="#" alt="Vista previa de la foto" style="display: none; width: 50%; " />
            </div>
            <button type="button" id="take-photo" class="btn btn-primary mt-2">Capturar Foto</button>
            <canvas id="photo-canvas" style="display: none;"></canvas>
            <input type="hidden" id="photo_data" name="photo_data" />
        </div>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary" id="btn_save"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function() {

        // Configuración de la cámara
        const video = document.getElementById('camera');
        const canvas = document.getElementById('photo-canvas');
        const photoPreview = document.getElementById('photo-preview');
        const photoDataInput = document.getElementById('photo_data');
        let stream;
        const saveButton = $("#attendance-note-form button[type='submit']");
        saveButton.prop("disabled", true);

        // Acceder a la cámara
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then((mediaStream) => {
                stream = mediaStream; // Guardamos el stream para detenerlo después
                video.srcObject = stream;
            })
            .catch((err) => {
                console.error("Error al acceder a la cámara: ", err);
            });

        // Tomar foto
        document.getElementById('take-photo').addEventListener('click', () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

            const photoData = canvas.toDataURL('image/png');
            photoPreview.src = photoData;
            photoPreview.style.display = 'inline';
            photoDataInput.value = photoData;
            if (photoDataInput.value) {
                saveButton.prop("disabled", false);
            }
        });
        document.getElementById('btn_save').addEventListener('click', () => {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        })

        $("#attendance-note-form").appForm({
            onSuccess: function(result) {
                if (result.clock_widget) {
                    $("#timecard-clock-out").closest("#js-clock-in-out").html(result.clock_widget);
                } else {
                    if (result.isUpdate) {
                        $(".dataTable:visible").appTable({
                            newData: result.data,
                            dataId: result.id
                        });
                    } else {
                        $(".dataTable:visible").appTable({
                            reload: true
                        });
                        window.location.reload();
                    }
                }
            }
        });

        setTimeout(function() {
            $("#note").focus();
        }, 200);
    });
</script>