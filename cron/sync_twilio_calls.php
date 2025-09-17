<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Verificar autoload
$autoloadPath = '../../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: No se encuentra autoload.php en $autoloadPath");
}
require_once $autoloadPath;
use Twilio\Rest\Client;
use PDO;
class TwilioSyncManager {
    private $twilioClient;
    private $pdo;
    private $pageSize = 100; // Aumentamos el tamaño de página para mejor rendimiento
    public function __construct($config) {
        try {
            // Inicializar Twilio
            $this->twilioClient = new Client(
                $config['twilio']['sid'],
                $config['twilio']['token']
            );
            // Inicializar BD
            $this->pdo = new PDO(
                "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset=utf8mb4",
                $config['db']['user'],
                $config['db']['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (Exception $e) {
            $this->log("Error de inicialización: " . $e->getMessage());
            throw $e;
        }
    }
    public function syncCalls() {
        try {
            // 1. Obtener llamadas existentes que necesitan actualización
            $this->updateExistingCalls();
            // 2. Sincronizar nuevas llamadas
            $this->syncNewCalls();
        } catch (Exception $e) {
            $this->log("Error en sincronización: " . $e->getMessage());
            throw $e;
        }
    }
    private function updateExistingCalls() {
        $this->log("Iniciando actualización de llamadas existentes...");
        // Obtener llamadas que necesitan actualización
        $stmt = $this->pdo->query("
            SELECT twilio_call_sid
            FROM crm_twilio_calls
            WHERE (price IS NULL OR duration IS NULL OR status != 'completed')
            AND twilio_call_sid IS NOT NULL
        ");
        $callsToUpdate = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($callsToUpdate as $callSid) {
            try {
                $this->log("Actualizando llamada: $callSid");
                $twilioCall = $this->twilioClient->calls($callSid)->fetch();
                // Solo actualizar si la llamada existe en Twilio
                if ($twilioCall) {
                    $this->updateCallRecord($twilioCall);
                }
            } catch (Exception $e) {
                $this->log("Error actualizando llamada $callSid: " . $e->getMessage());
                continue;
            }
        }
    }
    private function syncNewCalls() {
        $this->log("Iniciando sincronización de nuevas llamadas...");
        $existingSids = $this->getExistingCallSids();
        $page = $this->twilioClient->calls->read([], $this->pageSize);
        $processedCalls = 0;
        while (!empty($page)) {
            foreach ($page as $call) {
                try {
                    if (!in_array($call->sid, $existingSids)) {
                        $this->insertNewCall($call);
                        $processedCalls++;
                    } else {
                        // Si la llamada está completed pero faltan datos, actualizarla
                        if ($call->status === 'completed') {
                            $this->updateCallRecord($call);
                        }
                    }
                } catch (Exception $e) {
                    $this->log("Error procesando llamada {$call->sid}: " . $e->getMessage());
                    continue;
                }
            }
            // Obtener siguiente página
            if (count($page) === $this->pageSize) {
                $this->log("Procesando siguiente página...");
                $page = $this->twilioClient->calls->read(
                    [],
                    $this->pageSize,
                    ['pageToken' => end($page)->nextPageUri]
                );
            } else {
                break;
            }
        }
        $this->log("Total de nuevas llamadas procesadas: $processedCalls");
    }
    private function getExistingCallSids() {
        $stmt = $this->pdo->query("SELECT twilio_call_sid FROM crm_twilio_calls");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    private function insertNewCall($call) {
        $sql = "INSERT INTO crm_twilio_calls (
                    twilio_call_sid, status, from_number, to_number,
                    duration, created_at, updated_at, price, currency
                ) VALUES (
                    :sid, :status, :from_number, :to_number,
                    :duration, :created_at, NOW(), :price, :currency
                )";
        $stmt = $this->pdo->prepare($sql);
        $this->executeCallStatement($stmt, $call);
        $this->log("Nueva llamada insertada: " . $call->sid);
    }
    private function updateCallRecord($call) {
        $sql = "UPDATE crm_twilio_calls
                SET status = :status,
                    from_number = :from_number,
                    to_number = :to_number,
                    duration = :duration,
                    price = :price,
                    currency = :currency,
                    updated_at = NOW()
                WHERE twilio_call_sid = :sid
                AND (
                    status != :status OR
                    price IS NULL OR
                    duration != :duration OR
                    price != :price
                )";
        $stmt = $this->pdo->prepare($sql);
        $this->executeCallStatement($stmt, $call);
        $this->log("Llamada actualizada: " . $call->sid);
    }
    private function executeCallStatement($stmt, $call) {
        $params = [
            ':sid' => $call->sid,
            ':status' => $call->status,
            ':from_number' => $call->from,
            ':to_number' => $call->to,
            ':duration' => $call->duration ?: 0,
            ':created_at' => $call->dateCreated->format('Y-m-d H:i:s'),
            ':price' => $call->price ?: 0,
            ':currency' => $call->priceUnit ?: 'USD'
        ];
        $stmt->execute($params);
    }
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        echo "[$timestamp] $message\n";
    }
}
// Configuración
$config = [
    'twilio' => [
        'sid' => 'AC6b0e817ff1ce14e92db315b18ce3ea11',
        'token' => '6d52c1c30524df76d126d952979e3af2'
    ],
    'db' => [
        'host' => 'localhost',
        'dbname' => 'appnaros_sistema',
        'user' => 'appnaros_admin',
        'password' => 'xT1h[b{+sXvH'
    ]
];
try {
    $manager = new TwilioSyncManager($config);
    $manager->syncCalls();
    echo "Sincronización completada exitosamente\n";
} catch (Exception $e) {
    echo "Error crítico: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}