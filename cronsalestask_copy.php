<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(600); // 10 minutos
ini_set('memory_limit', '256M');

// Función para procesar un usuario
function procesarUsuario($mysqli, $userId, $sellerName, $date) {
    try {
        // Contar mensajes únicos
        $mensajesQuery = "SELECT COUNT(DISTINCT client_id) as total 
                         FROM crm_client_messages 
                         WHERE user_id = ? 
                         AND DATE(created_at) = ?";
        $stmt = $mysqli->prepare($mensajesQuery);
        $stmt->bind_param("is", $userId, $date);
        $stmt->execute();
        $mensajes = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();

        // Contar seguimientos únicos
        $seguimientosQuery = "SELECT COUNT(DISTINCT client_id) as total 
                            FROM crm_notes 
                            WHERE created_by = ? 
                            AND DATE(created_at) = ?";
        $stmt = $mysqli->prepare($seguimientosQuery);
        $stmt->bind_param("is", $userId, $date);
        $stmt->execute();
        $seguimientos = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();

        // Contar llamadas únicas
        $llamadasQuery = "SELECT COUNT(DISTINCT ccc.client_id) as total 
                         FROM crm_client_calls ccc 
                         JOIN crm_twilio_calls tc ON ccc.twilio_call_id = tc.id 
                         WHERE ccc.user_id = ? 
                         AND DATE(tc.created_at) = ?";
        $stmt = $mysqli->prepare($llamadasQuery);
        $stmt->bind_param("is", $userId, $date);
        $stmt->execute();
        $llamadas = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        $stmt->close();

        // Insertar resultados
        $insertQuery = "INSERT INTO crm_sales_task 
                       (date, seller_name, clientes_unicos_mensajes, 
                        clientes_unicos_seguimiento, clientes_unicos_llamadas, 
                        clientes_tres_impactos) 
                       VALUES (?, ?, ?, ?, ?, 0)
                       ON DUPLICATE KEY UPDATE 
                           clientes_unicos_mensajes = VALUES(clientes_unicos_mensajes),
                           clientes_unicos_seguimiento = VALUES(clientes_unicos_seguimiento),
                           clientes_unicos_llamadas = VALUES(clientes_unicos_llamadas),
                           clientes_tres_impactos = 0";
        
        $stmt = $mysqli->prepare($insertQuery);
        $stmt->bind_param("ssiiii", $date, $sellerName, $mensajes, $seguimientos, $llamadas);
        $stmt->execute();
        $stmt->close();

        // Registrar éxito
        file_put_contents('cron_log.txt', 
            "[$date] Procesado exitosamente: $sellerName\n", 
            FILE_APPEND);
        return true;

    } catch (Exception $e) {
        file_put_contents('cron_log.txt', 
            "[$date] Error procesando usuario $sellerName: " . $e->getMessage() . "\n", 
            FILE_APPEND);
        return false;
    }
}

// Conexión a la base de datos
$mysqli = new mysqli("localhost", "appnaros_admin", "xT1h[b{+sXvH", "appnaros_sistema");

// Verificar la conexión
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$date = date('Y-m-d');

// Procesar usuarios en lotes
$offset = 0;
$limit = 3; // Reducido a 3 usuarios por lote

while (true) {
    // Obtener un lote de usuarios
    $usersQuery = "SELECT id, CONCAT(first_name, ' ', last_name) AS seller_name 
                   FROM crm_users 
                   LIMIT ? OFFSET ?";
    $stmt = $mysqli->prepare($usersQuery);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $usersResult = $stmt->get_result();

    // Si no hay más usuarios, terminar
    if ($usersResult->num_rows == 0) {
        break;
    }

    // Procesar cada usuario en el lote
    while ($user = $usersResult->fetch_assoc()) {
        procesarUsuario($mysqli, $user['id'], $user['seller_name'], $date);
        
        // Pausa más larga entre usuarios
        sleep(1); // 1 segundo de pausa
    }

    $offset += $limit;
    
    // Pausa más larga entre lotes
    sleep(2); // 2 segundos de pausa
}

$mysqli->close();

file_put_contents('cron_log.txt', 
    "[$date] Proceso completado\n", 
    FILE_APPEND);
?>