<div class="d-flex b-b comment-form-container">
    <div class="w-100">
        <div id="lead-dropzone" class="post-dropzone mb-3 form-group">
            <input type="hidden" name="client_id" value="<?php echo isset($client_id) ? $client_id : 0; ?>">
            <footer class="card-footer b-a clearfix">
                <div id="callCenterContainer">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <?php
                        echo form_input(array(
                            "id" => "phonenumber",
                            "name" => "phonenumber",
                            "class" => "form-control form-control-lg comment_description flex-grow-1",
                            "value" => "+".$client_info->phone,
                            "placeholder" => app_lang("write_a_phonenumber"),
                            "data-rule-required" => true,
                            "data-rich-text-editor" => true,
                            "data-msg-required" => app_lang("field_required")
                        ));
                        ?>
                        <input type="hidden" id="client_id" name="client_id" value="<?php echo $client_id; ?>">
                        <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id; ?>">

                        <button id="btnDial" class="btn btn-success">Iniciar Llamada</button>
                        <button id="btnHangUp" class="btn btn-danger d-none">Finalizar Llamada</button>
                    </div>

                    <div id="callStatus" style="height: 200px; overflow-y: scroll;" class="fs-4 d-none ps-4">
                    </div>
                </div>
                <div id="callCenterLoading" class="d-none fs-4">
                    Cargando...
                </div>
            </footer>
        </div>
    </div>
</div>

<script>
    let device;

    function getMySQLFormattedDate() {
        const date = new Date();
        
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    function getFormattedDateTime() {
        const date = new Date();
        
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();

        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        const seconds = String(date.getSeconds()).padStart(2, '0');

        return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
    }

    async function getCapabilityToken() {
        $('#callCenterContainer').addClass('d-none')
        $('#callCenterLoading').removeClass('d-none')
        try {
            const response = await $.ajax({
                url: 'https://mint-prawn-2481.twil.io/capability-token',
                type: 'GET',
                dataType: 'json'
            });
            $('#callCenterContainer').removeClass('d-none')
            $('#callCenterLoading').addClass('d-none')
            return response;
        } catch (error) {
            console.error("Error al obtener el token de capacidad:", error);
            throw error;
        }
    }

    $(document).ready(async function() {
        const data = await getCapabilityToken()

        device = new Twilio.Device(data.token);

        device.on('tokenWillExpire', async () => {
            const data = await getCapabilityToken();
            device.updateToken(data.token);
        });

        device.on('error', function(error) {
            $('#btnDial').removeClass('d-none')
            log("Twilio.Device Error: " + error.message);
        });

        device.on('destroyed', function() {
            $('#btnDial').removeClass('d-none')
            log("Call ended.");
        });
    });

    $('#btnDial').bind('click', async function () {
        var params = {
            To: document.getElementById("phonenumber").value,
            client_id: document.getElementById("client_id").value,
            user_id: document.getElementById("user_id").value
        };
        $('#callStatus').html('')
        $('#callStatus').removeClass('d-none')

        if (device) {

            var outgoingConnection = await device.connect({params});

            outgoingConnection.on("ringing", function () {
                log("Timbrando...");
                $('#btnDial').addClass('d-none');
                $('#btnHangUp').removeClass('d-none');
            });

            outgoingConnection.on("accept", function (conn) {
                log('La llamada ha sido iniciada!');
            })

            outgoingConnection.on("disconnect", function (conn) {
                log('Llamada finalizada!');
                $('#btnDial').removeClass('d-none')
                $('#btnHangUp').addClass('d-none')
            });
        }
    })

    $('#btnHangUp').bind('click', function () {
        log("Colgando...");
        if (device) {
            device.disconnectAll();
        }
        $('#btnHangUp').addClass('d-none')
        $('#btnDial').removeClass('d-none')
    })

    function log(message) {
        var logDiv = document.getElementById("callStatus");
        logDiv.innerHTML += "<p>" + getFormattedDateTime() + " &gt;&nbsp;" + message + "</p>";
        logDiv.scrollTop = logDiv.scrollHeight;
    }
</script>