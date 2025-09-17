<?php
// Habilitar la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos
$servername = "127.0.0.1";
$username = "medicaso_rootBase";
$password = "5qA?o]t6d-h25qA?o]t6d-h2";
$dbname = "appnaros_sistema";
$table_prefix = "crm_";

// Definir la API Key esperada
$expected_api_key = "Monaros2024";

// Recibir datos del webhook
$data = json_decode(file_get_contents('php://input'), true);

// Verificar si la API Key se recibió y es válida
if (!isset($data['api_key']) || $data['api_key'] !== $expected_api_key) {
    echo json_encode(["status" => "error", "message" => "Acceso no autorizado. API Key inválida."]);
    exit; // Finalizar el script si la API Key no es válida
}

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el conjunto de caracteres
$conn->set_charset('utf8mb4');

// Validar que se han recibido los datos necesarios
if (isset($data['company_name']) && isset($data['type'])) {

    // Añadir las columnas necesarias a la consulta SQL
    $sql = "INSERT INTO {$table_prefix}clients 
        (company_name, type, created_date, phone, email, owner_id, especialidad, productos_interes, comentarios, lead_status_id, country, is_lead) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación de la declaración: " . $conn->error);
    }

    // Asignar valores a las variables, usando valores por defecto si no están presentes
    $company_name = $data['company_name'];
    $type = $data['type'];
    $created_date = $data['created_date'] ?? '';
    $phone = $data['phone'] ?? '';
    $email = $data['email'] ?? '';
    $owner_id = $data['owner_id'] ?? 0;
    $especialidad = $data['especialidad'] ?? '';
    $productos_interes = $data['productos_interes'] ?? '';
    $comentarios = $data['comentarios'] ?? '';
    $lead_status_id = $data['lead_status_id'] ?? 1; // Valor por defecto es 2
    $country = $data['country'] ?? ''; // Agregar country
    $is_lead = 1; // Valor por defecto para is_lead

    // Vincular parámetros (12 valores)
    $stmt->bind_param(
        "sssssiissisi",
        $company_name,
        $type,
        $created_date,
        $phone,
        $email,
        $owner_id,
        $especialidad,
        $productos_interes,
        $comentarios,
        $lead_status_id,
        $country,
        $is_lead
    );

    // Ejecutar la declaración
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Datos insertados correctamente"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error al insertar datos: " . $stmt->error]);
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Datos insuficientes recibidos."]);
}

// Cerrar la conexión
$conn->close();
