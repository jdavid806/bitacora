<?php
function conseguirFechaVencimeinto($tipoLicencia, $fechaActivacion)
{
    $intervalos = [
        "1" => 12,
        "2" => 12,   // Whatssap anual
        "10" => 12,  // Medica anual
        "12" => 6,  // Whatssap semestral
        "13" => 1,  // Whatssap Mensual
        "19" => 3,  // Medica Trimestral
        "20" => 1,  // Medica Mensual
        "27" => 1,  // Medica Mensual
        "28" => 3,  // Whatssap Trimestrañ
        "29" => 3  // Medica Trimestrañ
    ];

    if ($tipoLicencia == "0" || $tipoLicencia == "") {
        return "Sin Licencia";
    }

    // Verificar si el tipo de licencia es válido
    if (!isset($intervalos[$tipoLicencia])) {
        throw new Exception("Tipo de licencia no válido");
    }

    // Si es tipo licencia "1", retornamos "Licencia Vitalicia"
    if ($tipoLicencia == "1") {
        return "Sin fecha de vencimiento, Licencia Vitalicia";
    }

    // Obtener el intervalo en meses para el tipo de licencia proporcionado
    $intervaloMeses = $intervalos[$tipoLicencia];

    // Crear un objeto DateTime a partir de la fecha de activación
    $fechaActivacionObj = new DateTime($fechaActivacion);

    // Añadir el intervalo de meses para calcular la fecha de vencimiento
    $fechaActivacionObj->modify("+{$intervaloMeses} months");

    // Retornar la fecha de vencimiento en formato 'Y-m-d'
    return $fechaActivacionObj->format('Y-m-d');
}
?>

<div class="container">
    <!-- Licencia Médica -->
    <div class="card mb-3 mt-3">
        <div class="card-header">Licencia Médica</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Tipo:</strong></div>
                <div class="col-md-8"><?php echo $Medica_item->title; ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Activación:</strong></div>
                <div class="col-md-8"><?php echo $client_info->fecha_compra_medica; ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Vencimiento:</strong></div>
                <div class="col-md-8"><?php echo conseguirFechaVencimeinto($client_info->tipo_licencia_medica, $client_info->fecha_compra_medica); ?></div>
            </div>
        </div>
    </div>

    <!-- Licencia WhatsApp -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>Licencia WhatsApp</div>
            <div class="d-flex justify-content-end gap-3 w-100">
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
                <div class="title-button-group d-flex align-items-center justify-content-center">
                    <span class="dropdown inline-block">
                        <button class="btn btn-info text-white dropdown-toggle caret" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                            <i data-feather="tool" class="icon-16"></i> <?php echo app_lang('actions'); ?>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <div><?php echo modal_anchor(get_uri("clients/modal_instance_create_EA/" . $client_info->id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('create_instance'), array("title" => app_lang('create_instance'), "id" => "create-instance-EA", "class" => "list-group-item", "data-modal-lg" => "1")); ?></div>
                            <li role="presentation"><?php echo modal_anchor(get_uri("api_evolution/connect_instance/" . $client_info->id), "<i data-feather='check-circle' class='icon-16'></i> " . app_lang('instance_connect'), array("title" => app_lang('instance_connect'), "id" => "connect-instance-EA", "class" => "list-group-item", "data-modal-lg" => "1")); ?> </li>
                            <li role="presentation"><?php echo anchor(get_uri("api_evolution/restart_instance/" . $client_info->id), "<i data-feather='rotate-cw' class='icon-16'></i> " . app_lang('restart_instance'), array("title" => app_lang('restart_instance'), "class" => "dropdown-item")); ?> </li>
                            <li role="presentation"><?php echo anchor(get_uri("api_evolution/logout_instance/" . $client_info->id), "<i data-feather='log-out' class='icon-16'></i> " . app_lang('logout_instance'), array("title" => app_lang("logout_instance"), "class" => "dropdown-item")); ?> </li>
                            <li role="presentation"><?php echo anchor(get_uri("api_evolution/delete_instance/" . $client_info->id), "<i data-feather='log-out' class='icon-16'></i> " . app_lang('delete_instance'), array("title" => app_lang("delete_instance"), "class" => "dropdown-item")); ?> </li>
                        </ul>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Tipo:</strong></div>
                <div class="col-md-8"><?php echo $Whatsapp_item->title ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Activación:</strong></div>
                <div class="col-md-8"><?php echo $client_info->fecha_compra_wpp; ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Vencimiento:</strong></div>
                <div class="col-md-8"><?php echo conseguirFechaVencimeinto($client_info->tipo_licencia_wpp, $client_info->fecha_compra_wpp) ?></div>
            </div>
        </div>
    </div>

    <!-- Licencia Facturación Electrónica -->
    <div class="card mb-3">
        <div class="card-header">Licencia Facturación Electrónica</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Tipo:</strong></div>
                <div class="col-md-8"><?php echo $fe_item->title ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Activación:</strong></div>
                <div class="col-md-8"><?php echo $client_info->fecha_compra_fe; ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Vencimiento:</strong></div>
                <div class="col-md-8"><?php echo conseguirFechaVencimeinto($client_info->tipo_licencia_fe, $client_info->fecha_compra_fe) ?></div>
            </div>
        </div>
    </div>

    <!-- Licencia Contable -->
    <div class="card mb-3">
        <div class="card-header">Licencia Contable</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Tipo:</strong></div>
                <div class="col-md-8"><?php echo $Contable_item->title ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Activación:</strong></div>
                <div class="col-md-8"><?php echo $client_info->fecha_compra_contabilidad; ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Vencimiento:</strong></div>
                <div class="col-md-8"><?php echo conseguirFechaVencimeinto($client_info->tipo_licencia_contabilidad, $client_info->fecha_compra_contabilidad) ?></div>
            </div>
        </div>
    </div>

    <!-- Licencia Nómina -->
    <div class="card mb-3">
        <div class="card-header">Licencia Nómina</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4"><strong>Tipo:</strong></div>
                <div class="col-md-8"><?php echo $Nomina_item->title ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Activación:</strong></div>
                <div class="col-md-8"><?php echo $client_info->fecha_compra_nomina; ?></div>
            </div>
            <div class="row">
                <div class="col-md-4"><strong>Fecha Vencimiento:</strong></div>
                <div class="col-md-8"><?php echo conseguirFechaVencimeinto($client_info->tipo_licencia_nomina, $client_info->fecha_compra_nomina) ?></div>
            </div>
        </div>
    </div>
</div>