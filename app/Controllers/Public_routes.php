<?php

namespace App\Controllers;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeZone;
use stdClass;

class Public_routes extends App_Controller
{

    protected $Checklist_items_model;

    function __construct()
    {
        $this->Checklist_items_model = model('App\Models\Checklist_items_model');
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "OPTIONS") {
            die();
        }
        parent::__construct(false);
    }

    function no_auth_preview($estimate_id = 0, $public_key = "")
    {

        $this->get_send_estimate_client_int_template_wpp($estimate_id);
        if (!($estimate_id && $public_key)) {
            show_404();
        }

        validate_numeric_value($estimate_id);

        //check public key
        $estimate_info = $this->Estimates_model->get_one($estimate_id);
        if ($estimate_info->public_key !== $public_key) {
            show_404();
        }

        $view_data = array();

        $estimate_data = get_estimate_making_data($estimate_id);
        if (!$estimate_data) {
            show_404();
        }

        $view_data['estimate_preview'] = prepare_estimate_pdf($estimate_data, "html");
        $view_data['show_close_preview'] = false; //don't show back button
        $view_data['estimate_id'] = $estimate_id;
        $view_data['estimate_type'] = "public";
        $view_data['public_key'] = clean_data($public_key);

        return view("estimates/estimate_public_preview", $view_data);
    }

    function get_send_estimate_client_int_template_wpp($estimate_id)
    {

        $estimate_info = $this->Estimates_model->get_details(array("id" => $estimate_id))->getRow();
        $estimate_info->total = $this->Estimates_model->get_estimate_total_summary($estimate_id);
        $setting_info = $this->Settings_model->get_setting('can_approve_budget_users');
        $user_approve_ids = explode(',', $setting_info);
        $template_name = $estimate_info->is_lead ? "estimate_view_lead" : "estimate_view_client";
        $client = new stdClass();
        $client->id = 0;
        $client->company_name = "";
        $client->country = "";
        $client->especialidad = "";
        $client->productos_interes = "";
        $data_estimate_audit = new stdClass();

        $whatsapp_template = $this->Templates_model->get_final_template($template_name, true);

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $url = "http://ipinfo.io/{$ip}/json";

        $response = file_get_contents($url);
        $data = json_decode($response, true);
        $urltest = 'https://app.monaros.co';

        $content = '';
        if ($data) {

            $ip = $data['ip'] ?? 'No disponible';
            $city = $data['city'] ?? 'No disponible';
            $country = $data['country'] ?? 'No disponible';

            $content = str_replace('{IP}', $ip, $whatsapp_template['message_default']);
            $content = str_replace('{ESTIMATE_ID}', $estimate_info->id, $content);
            $content = str_replace('{COUNTRY}', $country, $content);
            $content = str_replace('{CITY}', $city, $content);
            $content = str_replace('{CLIENT_NAME}', $estimate_info->company_name, $content);
            $content = str_replace('{ESTIMATE_CREATED_BY}', $estimate_info->user_creator_name, $content);
            $content = str_replace('{ESTIMATE_VALUE}', to_currency($estimate_info->total->estimate_total, $estimate_info->total->currency_symbol), $content);
            $content = str_replace('{ESTIMATE_URL}', "<p>$urltest/sistema/index.php/estimate/preview/{$estimate_info->id}/{$estimate_info->public_key}</p>", $content);
        }


        $content = convertHtmlToPlainText($content);

        $wpp_linkkey_type = "SUPPORT";

        if ($ip !== "5.78.99.47") {
            $data_estimate_audit->estimate_id = $estimate_id;
            $data_estimate_audit->ip = $ip;
            $data_estimate_audit->city = $city;
            $data_estimate_audit->country = $country;
            $data_estimate_audit->country = $country;
            $data_estimate_audit->type_ = "view";
            $this->save_audit_estimate($data_estimate_audit);
            foreach ($user_approve_ids as $user_id) {
                $user_info = $this->Users_model->get_details(array("id" => $user_id))->getRow();
                save_message($client, $user_id, $content, "");
                $this->whatsapp_sent_EA($user_info->phone, $content, 'sendText', $wpp_linkkey_type);
            }
        }
    }

    function save_audit_estimate($data)
    {
        $data = array(
            "estimate_id" => $data->estimate_id,
            "ip" => $data->ip,
            "city" => $data->city,
            "country" => $data->country,
            "created_at" => date('Y-m-d H:i:s'),
            "type_" => $data->type_
        );

        return $this->Estimates_audit_model->ci_save($data);
    }

    function client_register()
    {
        $creation_type = $this->request->getPost('creation_type');
        $available_user_id = null;

        if ($creation_type == "with_demo") {
            $demo_date = date('Y-m-d', strtotime($this->request->getPost('demo_date')));
            $demo_time = date('H:i:s', strtotime($this->request->getPost('demo_time')));
            $timezone = $this->request->getPost('timezone');

            $client_timezone = new DateTimeZone($timezone);
            $demo_datetime = new DateTime("$demo_date $demo_time", $client_timezone);

            $demo_datetime->setTimezone(new DateTimeZone('America/Bogota'));

            $demo_date_colombia = $demo_datetime->format('Y-m-d');
            $demo_time_colombia = $demo_datetime->format('H:i:s');

            $available_user_id = $this->check_demo_availability($demo_date, $demo_time, $timezone);

            if (!$available_user_id) {
                echo json_encode(array("status" => "error", "message" => "No hay usuarios disponibles para la demo en la fecha y hora especificada"));
                return;
            }
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $specialty = $this->request->getPost('specialty');
        $message = $this->request->getPost('message');
        $city = $this->request->getPost('city');
        $country = $this->request->getPost('country');
        $postal = $this->request->getPost('postal');
        $region = $this->request->getPost('region');

        $client_id = $this->external_client_save(
            $name,
            $email,
            $phone,
            $specialty,
            $city,
            $country,
            $postal,
            $region,
            $message
        );

        if ($client_id) {
            if ($creation_type == "with_demo") {
                $event_data = array(
                    "title" => "Demostración Software Medico",
                    "description" => "Reunión de demostración con $name",
                    "start_date" => $demo_date_colombia,
                    "end_date" => $demo_date_colombia,
                    "start_time" => $demo_time_colombia,
                    "start_time_client" => $demo_time,
                    "end_time" => date('H:i:s', strtotime($demo_time) + 3600),
                    "created_by" => $available_user_id,
                    "client_id" => $client_id,
                    "color" => "#f1c40f"
                );

                $this->Events_model->ci_save($event_data);
            }
            echo json_encode(array("status" => "success", "message" => $this->get_client_register_message($creation_type)));
        } else {
            echo json_encode(array("status" => "error", "message" => "Error al registrar la información"));
        }
    }

    public function schedule_demo_by_phone()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['phone']) || empty($input['demo_date']) || empty($input['demo_time']) || empty($input['timezone'])) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren los parámetros "phone", "demo_date", "demo_time" y "timezone".']);
            http_response_code(400);
            return;
        }

        $phone = $input['phone'];
        $demo_date = $input['demo_date'];
        $demo_time = $input['demo_time'];
        $timezone = $input['timezone'];

        $client = $this->Clients_model->get_details(['phone' => $phone])->getRow();

        if (!$client) {
            echo json_encode(['status' => 'error', 'message' => 'Cliente no encontrado.']);
            http_response_code(404);
            return;
        }

        $client_timezone = new DateTimeZone($timezone);
        $demo_datetime = new DateTime("$demo_date $demo_time", $client_timezone);
        $demo_datetime->setTimezone(new DateTimeZone('America/Bogota'));

        $demo_date_colombia = $demo_datetime->format('Y-m-d');
        $demo_time_colombia = $demo_datetime->format('H:i:s');

        $available_user_id = $this->check_demo_availability($demo_date, $demo_time, $timezone);

        if (!$available_user_id) {
            echo json_encode(['status' => 'error', 'message' => 'No hay usuarios disponibles para la demo en la fecha y hora especificada.']);
            return;
        }

        $event_data = [
            'title' => 'Demostración Software Medico',
            'description' => "Reunión de demostración con {$client->company_name}",
            'start_date' => $demo_date_colombia,
            'end_date' => $demo_date_colombia,
            'start_time' => $demo_time_colombia,
            'start_time_client' => $demo_time,
            'end_time' => date('H:i:s', strtotime($demo_time) + 3600),
            'created_by' => $available_user_id,
            'client_id' => $client->id,
            'color' => '#f1c40f'
        ];

        $this->Events_model->ci_save($event_data);

        echo json_encode(['status' => 'success', 'message' => 'Demostración agendada correctamente.']);
    }

    private function get_client_register_message($creation_type)
    {
        $message = "Se ha registrado tu información correctamente. Pronto nos pondremos en contacto contigo.";

        if ($creation_type == "with_demo") {
            $message = "Se ha registrado tu información correctamente y hemos agendado una reunión de demostración. Pronto nos pondremos en contacto contigo.";
        }

        return $message;
    }

    function check_demo_availability($demo_date, $demo_time, $timezone)
    {
        $client_timezone = new DateTimeZone($timezone);
        $demo_datetime = new DateTime("$demo_date $demo_time", $client_timezone);

        $demo_datetime->setTimezone(new DateTimeZone('America/Bogota'));

        $demo_date_colombia = $demo_datetime->format('Y-m-d');
        $demo_time_colombia = $demo_datetime->format('H:i:s');

        $demo_end_datetime = clone $demo_datetime;
        $demo_end_datetime->modify('+1 hour');

        $day_of_week = $demo_datetime->format('N');
        $start_working_time = new DateTime("$demo_date_colombia 08:30:00", new DateTimeZone('America/Bogota'));

        $end_working_time = ($day_of_week == 6)
            ? new DateTime("$demo_date_colombia 12:00:00", new DateTimeZone('America/Bogota'))
            : new DateTime("$demo_date_colombia 17:30:00", new DateTimeZone('America/Bogota'));

        $current_datetime = new DateTime('now', new DateTimeZone('America/Bogota'));

        if ($day_of_week == 7 || $demo_datetime < $start_working_time || $demo_end_datetime > $end_working_time || $demo_datetime < $current_datetime) {
            return false;
        }

        $app_controller = new App_Controller();
        $staff_users = $app_controller->Users_model->get_details(array(
            "user_type" => "staff",
            "user_role" => 2 // Rol de ventas
        ))->getResult();

        foreach ($staff_users as $user) {

            $events = $app_controller->Events_model->get_events_for_user(
                $user,
                $demo_date_colombia,
                $demo_time_colombia,
                $demo_end_datetime
            );

            if (count($events) == 0) {
                return $user->id;
            }
        }

        return false;
    }


    function external_client_save($name, $email, $phone, $specialty, $city, $country, $postal, $region, $message)
    {
        $data = array(
            "company_name" => $name,
            "email" => $email,
            "phone" => $phone,
            "especialidad" => $specialty,
            "city" => $city,
            "country" => $country,
            "zip" => $postal,
            "state" => $region,
            "type" => "person",
            "is_lead" => 1,
            "lead_status_id" => 1,
            "comentarios" => $message,
            "created_date" => date('Y-m-d H:i:s'),
        );

        if ($data["created_date"] !== null && $data["created_date"] !== "0000-00-00") {
            $app_controller = new App_Controller();
            return $app_controller->Clients_model->ci_save($data);
        }
    }

    public function get_demo_available_hours()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        // Obtener datos del post
        $demo_date = $this->request->getPost('demo_date') ?? $input['demo_date'];
        $timezone = $this->request->getPost('timezone') ?? $input['timezone'];

        // Zona horaria del cliente
        $client_timezone = new DateTimeZone($timezone);
        $demo_datetime = new DateTime($demo_date, $client_timezone);
        $demo_datetime->setTimezone(new DateTimeZone('America/Bogota'));
        $demo_date_colombia = $demo_datetime->format('Y-m-d');

        // Obtener la fecha y hora actual en Bogotá
        $current_datetime = new DateTime('now', new DateTimeZone('America/Bogota'));
        $current_date_colombia = $current_datetime->format('Y-m-d');
        $current_time_colombia = $current_datetime->format('H:i'); // Hora actual en Bogotá (formato 'H:i')

        // Validar si la fecha seleccionada es anterior a la fecha actual
        if ($demo_date_colombia < $current_date_colombia) {
            echo json_encode(array("status" => "error", "message" => "La fecha seleccionada no puede ser anterior a la fecha actual."));
            return;
        }

        // Obtener festivos de Colombia
        $public_holidays = $this->getColombiaPublicHolidays($demo_datetime->format('Y')); // Obtener festivos del año actual

        // Revisar si la fecha seleccionada es un festivo
        if (in_array($demo_date_colombia, $public_holidays)) {
            echo json_encode(array("status" => "error", "message" => "El día seleccionado es festivo en Colombia."));
            return;
        }

        // Definir horarios laborales según el día
        $day_of_week = $demo_datetime->format('N'); // Día de la semana (1=Lunes, 7=Domingo)
        $start_working_time = null;
        $end_working_time = null;

        // Lunes a Viernes
        if ($day_of_week >= 1 && $day_of_week <= 5) {
            $start_working_time = new DateTime("$demo_date_colombia 08:30:00", new DateTimeZone('America/Bogota'));
            $end_working_time = new DateTime("$demo_date_colombia 17:30:00", new DateTimeZone('America/Bogota'));
        }
        // Sábado
        elseif ($day_of_week == 6) {
            $start_working_time = new DateTime("$demo_date_colombia 08:00:00", new DateTimeZone('America/Bogota'));
            $end_working_time = new DateTime("$demo_date_colombia 12:00:00", new DateTimeZone('America/Bogota'));
        }
        // Domingo
        else {
            echo json_encode(array("status" => "error", "message" => "No se puede agendar una demostración en domingo."));
            return;
        }

        // Obtener usuarios del staff
        $app_controller = new App_Controller();
        $staff_users = $app_controller->Users_model->get_details(array("user_type" => "staff"))->getResult();

        $available_hours = [];
        $interval = new DateInterval('PT60M'); // Intervalo de 60 minutos (una hora)
        $time_slots = new DatePeriod($start_working_time, $interval, $end_working_time);

        // Iterar sobre los posibles horarios
        foreach ($time_slots as $slot) {
            // Si la fecha es la actual, validamos si la hora ya ha pasado en Colombia
            if ($demo_date_colombia == $current_date_colombia) {
                if ($slot->format('H:i') < $current_time_colombia) {
                    continue; // Si ya ha pasado la hora, la saltamos
                }
            }

            $slot_end = clone $slot;
            $slot_end->modify('+1 hour');

            // Revisar disponibilidad
            $available = true;
            foreach ($staff_users as $user) {
                $events = $app_controller->Events_model->get_events_for_user(
                    $user,
                    $demo_date_colombia,
                    $slot->format('H:i:s'),
                    $slot_end
                );

                if (count($events) > 0) {
                    $available = false;
                    break;
                } else {
                    break;
                }
            }

            if ($available) {
                // Convertir las horas disponibles a la zona horaria del cliente
                $slot_client_time = clone $slot;
                $slot_client_time->setTimezone($client_timezone);
                $available_hours[] = [$slot_client_time->format('H:i')];
            }
        }

        // Responder con los horarios disponibles convertidos a la zona horaria del cliente
        echo json_encode(array("status" => "success", "available_hours" => $available_hours));
    }

    public function get_demo_available_hours_range()
    {
        // Obtener datos del post
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        $start_date = $input['start_date'] ?? null;
        $end_date = $input['end_date'] ?? null;
        $timezone = $input['timezone'] ?? null;

        // Validar fechas
        if (!$start_date || !$end_date || !$timezone) {
            echo json_encode(array("status" => "error", "message" => "Se requieren los parámetros 'start_date', 'end_date' y 'timezone'."));
            return;
        }

        $start_datetime = new DateTime($start_date, new DateTimeZone($timezone));
        $end_datetime = new DateTime($end_date, new DateTimeZone($timezone));

        if ($start_datetime > $end_datetime) {
            echo json_encode(array("status" => "error", "message" => "La fecha de inicio no puede ser mayor que la fecha de fin."));
            return;
        }

        $available_hours_range = [];
        $interval = new DateInterval('P1D'); // Intervalo de 1 día
        $date_range = new DatePeriod($start_datetime, $interval, $end_datetime->modify('+1 day'));

        foreach ($date_range as $date) {
            $demo_date = $date->format('Y-m-d');
            $this->request->setGlobal('post', ['demo_date' => $demo_date, 'timezone' => $timezone]);

            ob_start();
            $this->get_demo_available_hours();
            $response = ob_get_clean();
            $response_data = json_decode($response, true);

            if ($response_data['status'] === 'success') {
                $available_hours_range[$demo_date] = $response_data['available_hours'];
            }
        }

        echo json_encode(array("status" => "success", "available_hours_range" => $available_hours_range), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    private function getColombiaPublicHolidays($year)
    {
        // Aquí puedes hacer una consulta a una API externa de festivos, o tener una lista fija
        // Ejemplo de lista de festivos para 2024 y 2025
        $holidays = [
            '2024-01-01', // Año Nuevo
            '2024-03-19', // Día de San José
            '2024-04-06', // Jueves Santo
            '2024-04-07', // Viernes Santo
            '2024-05-01', // Día del Trabajo
            '2024-06-15', // Corpus Christi
            '2024-07-20', // Independencia de Colombia
            '2024-08-07', // Batalla de Boyacá
            '2024-12-08', // Inmaculada Concepción
            '2024-12-25', // Navidad
            '2024-12-31', // Año nuevo
            '2025-01-01', // Año Nuevo
            '2025-03-24', // Día de San José
            '2025-04-17', // Jueves Santo
            '2025-04-18', // Viernes Santo
            '2025-05-01', // Día del Trabajo
            '2025-06-07', // Corpus Christi
            '2025-07-20', // Independencia de Colombia
            '2025-08-07', // Batalla de Boyacá
            '2025-12-08', // Inmaculada Concepción
            '2025-12-25', // Navidad
            '2025-12-31', // Año nuevo
        ];

        return $holidays;
    }

    public function search_ticket_by_phone($phone)
    {

        $info_client = $this->Clients_model->get_details(["phone" => $phone])->getRow();
        $info_ticket = $this->Tickets_model->get_details_refactor(["client_id" => $info_client->id])->getResult();
        foreach ($info_ticket as $ticket) {
            $labelsListParts = explode("--::--", $ticket->labels_list);
            $ticket->labels_list = $labelsListParts[1] ?? '';
            $ticket->tasks = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
            foreach ($ticket->tasks as $task) {
                $task->comments = $this->Project_comments_model->get_details_refactor(["task_id" => $task->id])->getRow();
            }
        }
        header('Content-Type: application/json');
        echo json_encode($info_ticket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    public function search_ticket_by_cod($cod_cliente)
    {

        $info_client = $this->Clients_model->get_details_refactor(["cod_cliente" => $cod_cliente])->getRow();
        $info_ticket = $this->Tickets_model->get_details_refactor(["client_id" => $info_client->id])->getResult();
        foreach ($info_ticket as $ticket) {
            $labelsListParts = explode("--::--", $ticket->labels_list);
            $ticket->labels_list = $labelsListParts[1] ?? '';
            $ticket->tasks = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
            foreach ($ticket->tasks as $task) {
                $task->comments = $this->Project_comments_model->get_details_refactor(["task_id" => $task->id])->getRow();
            }
        }
        header('Content-Type: application/json');
        echo json_encode($info_ticket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    public function search_client_by_cod($cod_cliente)
    {

        $info_client = $this->Clients_model->get_details_refactor(["cod_cliente" => $cod_cliente])->getRow();
        // $info_ticket = $this->Tickets_model->get_details_refactor(["client_id" => $info_client->id])->getResult();
        // foreach ($info_ticket as $ticket) {
        //     $labelsListParts = explode("--::--", $ticket->labels_list);
        //     $ticket->labels_list = $labelsListParts[1] ?? '';
        //     $ticket->tasks = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
        //     foreach ($ticket->tasks as $task) {
        //         $task->comments = $this->Project_comments_model->get_details_refactor(["task_id" => $task->id])->getRow();
        //     }
        // }
        header('Content-Type: application/json');
        echo json_encode($info_client, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function search_task_by_phone($phone)
    {

        $info_client = $this->Clients_model->get_details(["phone" => $phone])->getRow();
        $info_tickets = $this->Tickets_model->get_details_refactor(["client_id" => $info_client->id])->getResult();
        $tasks_by_ticket = [];

        foreach ($info_tickets as $ticket) {
            $tasks_by_ticket = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
        }

        header('Content-Type: application/json');
        echo json_encode($tasks_by_ticket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    public function search_task_by_ticket($ticket)
    {

        // $info_client = $this->Clients_model->get_details(["phone" => $phone])->getRow();
        $info_tickets = $this->Tickets_model->get_details_refactor(["id" => $ticket])->getResult();
        $tasks_by_ticket = [];

        foreach ($info_tickets as $ticket) {
            $tasks_by_ticket = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
        }

        header('Content-Type: application/json');
        echo json_encode($tasks_by_ticket, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
    public function search_task_by_id($task)
    {

        // $info_client = $this->Clients_model->get_details(["phone" => $phone])->getRow();
        // $info_tickets = $this->Tickets_model->get_details_refactor(["id" => $ticket])->getResult();
        // $tasks_by_ticket = [];

        // foreach ($info_tickets as $ticket) {
        $tasks = $this->Tasks_model->get_details_refactor(["id" => $task])->getResult();
        // }
        foreach ($tasks as $task) {
            $task->comments = $this->Project_comments_model->get_details_refactor(["task_id" => $task->id])->getResult();
        }

        header('Content-Type: application/json');
        echo json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function search_ticket_by_phone_detailed($phone)
    {

        $clients = $this->Clients_model->get_all_clients(["phone" => $phone])->getRow();

        // echo var_dump($clients);
        $info_client = new stdClass;
        $info_client->client_id = $clients->id;
        $info_client->full_name = $clients->company_name;
        $info_client->cod_client = $clients->cod_cliente;
        $info_client->email = $clients->email;
        $info_client->join_date = $clients->created_date;
        $info_client->country = $clients->country;
        $info_client->specialty = $clients->especialidad;
        $info_client->info_tickets = $this->Tickets_model->get_details_refactor(["client_id" => $clients->id])->getResult();

        if (count($info_client->info_tickets) > 0) {
            foreach ($info_client->info_tickets as $ticket) {
                $labelsListParts = explode("--::--", $ticket->labels_list);
                $ticket->labels_list = $labelsListParts[1] ?? '';
                $ticket->ticket_comments = $this->Ticket_comments_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
                $ticket->tasks = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
                foreach ($ticket->tasks as $task) {
                    $task->comments = $this->Project_comments_model->get_details_refactor(["task_id" => $task->id])->getResult();
                }
            }
        } else {
            $info_client->info_tickets = app_lang("clients_without_tickets");
        }

        header('Content-Type: application/json');
        echo json_encode($info_client, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function search_ticket_by_phone_classified($phone)
    {
        $clients = $this->Clients_model->get_all_clients(["phone" => $phone])->getRow();

        $info_client = new stdClass;
        $info_client->client_id = $clients->id;
        $info_client->full_name = $clients->company_name;
        $info_client->cod_client = $clients->cod_cliente;
        $info_client->email = $clients->email;
        $info_client->join_date = $clients->created_date;
        $info_client->country = $clients->country;
        $info_client->specialty = $clients->especialidad;

        $info_client->info_tickets = new stdClass;

        // Tickets abiertos
        $tickets_open = $this->Tickets_model->get_details_reduced([
            "client_id" => $clients->id,
            "status" => "open"
        ])->getResult();

        if (count($tickets_open) > 0) {
            foreach ($tickets_open as $ticket) {
                $labelsListParts = explode("--::--", $ticket->labels_list);
                $ticket->labels_list = $labelsListParts[1] ?? '';

                $ticket->ticket_comments = $this->Ticket_comments_model->get_details_refactor([
                    "ticket_id" => $ticket->id
                ])->getResult();

                $ticket->tasks = $this->Tasks_model->get_details_reduced([
                    "ticket_id" => $ticket->id
                ])->getResult();

                foreach ($ticket->tasks as $task) {
                    $task->comments = $this->Project_comments_model->get_details_refactor([
                        "task_id" => $task->id
                    ])->getResult();
                }
            }
            $info_client->info_tickets->tickets_open = $tickets_open;
        } else {
            $info_client->info_tickets->tickets_open = app_lang("clients_without_tickets");
        }

        // Tickets cerrados
        $tickets_closed = $this->Tickets_model->get_details_reduced([
            "client_id" => $clients->id,
            "status" => "closed"
        ])->getResult();

        if (count($tickets_closed) > 0) {
            foreach ($tickets_closed as $ticket) {
                $labelsListParts = explode("--::--", $ticket->labels_list);
                $ticket->labels_list = $labelsListParts[1] ?? '';

                $ticket->ticket_comments = $this->Ticket_comments_model->get_details_refactor([
                    "ticket_id" => $ticket->id
                ])->getResult();

                $ticket->tasks = $this->Tasks_model->get_details_reduced([
                    "ticket_id" => $ticket->id
                ])->getResult();

                foreach ($ticket->tasks as $task) {
                    $task->comments = $this->Project_comments_model->get_details_refactor([
                        "task_id" => $task->id
                    ])->getResult();
                }
            }
            $info_client->info_tickets->tickets_closed = $tickets_closed;
        } else {
            $info_client->info_tickets->tickets_closed = app_lang("clients_without_closed_tickets");
        }

        header('Content-Type: application/json');
        echo json_encode($info_client, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function search_tickets_open_by_phone($phone)
    {
        $clients = $this->Clients_model->get_all_clients(["phone" => $phone])->getRow();

        $info_client = new stdClass;
        $info_client->client_id = $clients->id;
        $info_client->full_name = $clients->company_name;
        $info_client->cod_client = $clients->cod_cliente;
        $info_client->email = $clients->email;
        $info_client->join_date = $clients->created_date;
        $info_client->country = $clients->country;
        $info_client->specialty = $clients->especialidad;

        $info_client->info_tickets = new stdClass;

        // Tickets abiertos
        $tickets_open = $this->Tickets_model->get_details_reduced([
            "client_id" => $clients->id,
            "status" => "open"
        ])->getResult();

        if (count($tickets_open) > 0) {
            foreach ($tickets_open as $ticket) {
                $labelsListParts = explode("--::--", $ticket->labels_list);
                $ticket->labels_list = $labelsListParts[1] ?? '';

                $ticket->ticket_comments = $this->Ticket_comments_model->get_details_refactor([
                    "ticket_id" => $ticket->id
                ])->getResult();

                $ticket->tasks = $this->Tasks_model->get_details_reduced([
                    "ticket_id" => $ticket->id
                ])->getResult();

                foreach ($ticket->tasks as $task) {
                    $task->comments = $this->Project_comments_model->get_details_refactor([
                        "task_id" => $task->id
                    ])->getResult();
                }
            }
            $info_client->info_tickets->tickets_open = $tickets_open;
        } else {
            $info_client->info_tickets->tickets_open = app_lang("clients_without_tickets");
        }

        header('Content-Type: application/json');
        echo json_encode($info_client, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }


    public function create_ticket_and_task()
    {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['ticket']) || empty($input['task'])) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren los parametros "ticket" y "task".']);
            http_response_code(400);
            exit();
        }

        date_default_timezone_set('America/Bogota');

        // Validar y obtener información del cliente
        $info_client = $this->Clients_model->get_details(["phone" => $input["ticket"]["phone_client"]])->getRow();
        if (!$info_client) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el cliente con el telefono: ' . $input["ticket"]["phone_client"]]);
            http_response_code(404);
            exit();
        }

        // Validar y obtener información del proyecto
        $info_project = $this->Projects_model->get_details_copy(["id" => (int) $input["ticket"]["project_id"]])->getRow();
        if (!$info_project) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el proyecto con el titulo: ' . $input["ticket"]["project_title"]]);
            http_response_code(404);
            exit();
        }

        // Validar y obtener información del tipo de ticket
        // $info_ticket_type = $this->Ticket_types_model->get_details_copy(["title" => $input["ticket"]["ticket_type_title"]])->getRow();
        // if (!$info_ticket_type) {
        //     echo json_encode(['status' => 'error', 'message' => 'No se encontro el tipo de ticket con el titulo: ' . $input["ticket"]["ticket_type_title"]]);
        //     http_response_code(404);
        //     exit();
        // }

        // Validar y obtener información del usuario solicitante
        $info_user_requested = $this->Users_model->get_details_copy(["phone" => $input["ticket"]["requested_phone"]])->getRow();
        if (!$info_user_requested) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el usuario solicitante con el telefono: ' . $input["ticket"]["requested_phone"]]);
            http_response_code(404);
            exit();
        }

        // Validar y obtener información del usuario asignado
        // $info_user_assigned = $this->Users_model->get_details_copy(["phone" => $input["ticket"]["assigned_phone"]])->getRow();
        // if (!$info_user_assigned) {
        //     echo json_encode(['status' => 'error', 'message' => 'No se encontro el usuario asignado con el telefono: ' . $input["ticket"]["assigned_phone"]]);
        //     http_response_code(404);
        //     exit();
        // }

        // Si todas las validaciones pasan, crear el ticket y la tarea
        $data_ticket = [
            "client_id" => $info_client->id,
            "project_id" => (int) $info_project->id,
            // "ticket_type_id" => (int) $info_ticket_type->id,
            "title" => $input["ticket"]["title"],
            "created_by" => (int) $info_user_requested->id,
            "requested_by" => (int) $info_user_requested->id,
            "created_at" => date('Y-m-d H:i:s'),
            "status" => "new",
            "last_activity_at" => date('Y-m-d H:i:s'),
            // "assigned_to" => (int) $info_user_assigned->id
        ];

        $id_ticket = $this->Tickets_model->ci_save($data_ticket);

        $data_task = [
            "title" => $input["task"]["title"],
            "description" => $input["task"]["description"],
            "project_id" => (int) $info_project->id,
            // "assigned_to" => (int) $info_user_assigned->id,
            "status_id" => 1,
            "status" => "to_do",
            "created_date" => date('Y-m-d'),
            "context" => "project",
            "ticket_id" => (int) $id_ticket,
            "created_by" => (int) $info_user_requested->id,
        ];

        $id_task = $this->Tasks_model->ci_save($data_task);

        echo json_encode([
            'status' => 'success',
            'message' => 'Ticket y tarea creados exitosamente',
            'ticket_id' => $id_ticket,
            'task_id' => $id_task
        ]);
        exit();
    }

    public function create_ticket_and_task_test()
    {
        header('Content-Type: application/json');

        // Obtener datos desde FormData en lugar de JSON
        $ticket_phone_client = $this->request->getPost('ticket_phone_client');
        $ticket_project_id = $this->request->getPost('ticket_project_id');
        $ticket_title = $this->request->getPost('ticket_title');
        $ticket_requested_phone = $this->request->getPost('ticket_requested_phone');
        $task_title = $this->request->getPost('task_title');
        $task_description = $this->request->getPost('task_description');

        if (
            empty($ticket_phone_client) || empty($ticket_project_id) || empty($ticket_title) ||
            empty($ticket_requested_phone) || empty($task_title) || empty($task_description)
        ) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren todos los parámetros: ticket_phone_client, ticket_project_id, ticket_title, ticket_requested_phone, task_title, task_description.']);
            http_response_code(400);
            return;
        }

        date_default_timezone_set('America/Bogota');

        // Validar y obtener información del cliente
        $info_client = $this->Clients_model->get_details(["phone" => $ticket_phone_client])->getRow();
        if (!$info_client) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el cliente con el telefono: ' . $ticket_phone_client]);
            http_response_code(404);
            exit();
        }

        // Validar y obtener información del proyecto
        $info_project = $this->Projects_model->get_details_copy(["id" => (int) $ticket_project_id])->getRow();
        if (!$info_project) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el proyecto con el ID: ' . $ticket_project_id]);
            http_response_code(404);
            exit();
        }

        // Validar y obtener información del tipo de ticket
        // $info_ticket_type = $this->Ticket_types_model->get_details_copy(["title" => $input["ticket"]["ticket_type_title"]])->getRow();
        // if (!$info_ticket_type) {
        //     echo json_encode(['status' => 'error', 'message' => 'No se encontro el tipo de ticket con el titulo: ' . $input["ticket"]["ticket_type_title"]]);
        //     http_response_code(404);
        //     exit();
        // }

        // Validar y obtener información del usuario solicitante
        $info_user_requested = $this->Users_model->get_details_copy(["phone" => $ticket_requested_phone])->getRow();
        if (!$info_user_requested) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el usuario solicitante con el telefono: ' . $ticket_requested_phone]);
            http_response_code(404);
            exit();
        }

        // Validar y obtener información del usuario asignado
        // $info_user_assigned = $this->Users_model->get_details_copy(["phone" => $input["ticket"]["assigned_phone"]])->getRow();
        // if (!$info_user_assigned) {
        //     echo json_encode(['status' => 'error', 'message' => 'No se encontro el usuario asignado con el telefono: ' . $input["ticket"]["assigned_phone"]]);
        //     http_response_code(404);
        //     exit();
        // }

        $files_data = $this->save_files();
        $files = unserialize($files_data);
        $archivos = []; // Inicializar array vacío

        foreach ($files as $file) {
            $file_name = $file['file_name'];
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");
            $actual_file_name = remove_file_prefix($file_name);
            $thumbnail = get_source_url_of_file($file, get_setting("timeline_file_path"), "thumbnail");
            $url = get_source_url_of_file($file, get_setting("timeline_file_path"));

            // Agregar al array
            $archivos[] = [
                'url' => $url,
                'thumbnail' => $thumbnail,
                'actual_file_name' => $actual_file_name
            ];
        }

        // Si todas las validaciones pasan, crear el ticket y la tarea
        $data_ticket = [
            "client_id" => $info_client->id,
            "project_id" => (int) $info_project->id,
            // "ticket_type_id" => (int) $info_ticket_type->id,
            "title" => $ticket_title,
            "created_by" => (int) $info_user_requested->id,
            "requested_by" => (int) $info_user_requested->id,
            "created_at" => date('Y-m-d H:i:s'),
            "status" => "new",
            "last_activity_at" => date('Y-m-d H:i:s'),
            // "assigned_to" => (int) $info_user_assigned->id
        ];

        $id_ticket = $this->Tickets_model->ci_save($data_ticket);

        $data_task = [
            "title" => $task_title,
            "description" => $task_description,
            "project_id" => (int) $info_project->id,
            // "assigned_to" => (int) $info_user_assigned->id,
            "status_id" => 1,
            "status" => "to_do",
            "created_date" => date('Y-m-d'),
            "context" => "project",
            "ticket_id" => (int) $id_ticket,
            "created_by" => (int) $info_user_requested->id,
        ];



        $id_task = $this->Tasks_model->ci_save($data_task);



        if (!empty($files_data)) {
            $data_comment = [
                "created_by" => $info_user_requested->id,
                "created_at" => date('Y-m-d H:i:s'),
                "description" => "Archivos adjuntos de la tarea",
                "project_id" => (int) $info_project->id,
                "task_id" => (int) $id_task,
                "files" => $files_data
            ];
        }

        $id_comment = $this->Project_comments_model->ci_save($data_comment);

        echo json_encode([
            'status' => 'success',
            'message' => 'Ticket y tarea creados exitosamente',
            'ticket_id' => $id_ticket,
            'task_id' => $id_task,
            'comment_id' => $id_comment
        ]);
        exit();
    }

    public function create_task()
    {

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren los parámetros.']);
            http_response_code(400);
            return;
        }

        $info_project = $this->Projects_model->get_details_copy(["id" => (int) $input["project_id"]])->getRow();

        if (!$info_project) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el proyecto con este ID: ' . $input["project_id"]]);
            http_response_code(404);
            exit();
        }

        $info_user_requested = $this->Users_model->get_details_copy(["phone" => $input["requested_phone"]])->getRow();

        if (!$info_user_requested) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el usuario con este numero de telefono: ' . $input["requested_phone"]]);
            http_response_code(404);
            exit();
        }
        // $info_user_assigned = $this->Users_model->get_details_copy(["phone" => $input["assigned_phone"]])->getRow();

        $data_task = [
            "title" => $input["title"],
            "description" => $input["description"],
            "project_id" => (int) $info_project->id,
            // "assigned_to" => $input["assigned_phone"] !== "" ? $info_user_assigned->id : 0,
            "status_id" => 1,
            "status" => "to_do",
            "created_date" => date('Y-m-d'),
            "context" => "project",
            "ticket_id" => (int) $input["ticket_id"],
            "created_by" => (int) $info_user_requested->id,
        ];

        $id_task = $this->Tasks_model->ci_save($data_task);

        echo json_encode(array("success" => true, 'message' => "Tarea guardada exitosamente", "id_task" => $id_task));
        exit();
    }

    public function save_files()
    {
        $target_path = get_setting("timeline_file_path");

        // PRIMERO subir a temporal
        upload_file_to_temp_bulk();

        // LUEGO usar una función diferente que SÍ procese $_FILES['file']
        $files_data = $this->process_uploaded_files($target_path, "project_comment");

        return $files_data;
    }

    private function process_uploaded_files($target_path, $related_to)
    {
        $files_data = array();

        if (isset($_FILES['file']) && is_array($_FILES['file']['name'])) {
            $file_count = count($_FILES['file']['name']);

            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                    $temp_file = $_FILES['file']['tmp_name'][$i];
                    $file_name = $_FILES['file']['name'][$i];
                    $file_size = $_FILES['file']['size'][$i];

                    $file_data = move_temp_file($file_name, $target_path, $related_to, $temp_file, "", "", false, $file_size);
                    $files_data[] = array(
                        "file_name" => get_array_value($file_data, "file_name"),
                        "file_size" => $file_size,
                        "file_id" => get_array_value($file_data, "file_id"),
                        "service_type" => get_array_value($file_data, "service_type")
                    );
                }
            }
        }

        return serialize($files_data);
    }

    public function create_comment_to_task()
    {
        header('Content-Type: application/json');

        // Obtener datos desde FormData en lugar de JSON
        $project_id = $this->request->getPost('project_id');
        $task_id = $this->request->getPost('task_id');
        $commented_phone = $this->request->getPost('commented_phone');
        $coment = $this->request->getPost('coment');

        if (empty($project_id) || empty($task_id) || empty($commented_phone) || empty($coment)) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren todos los parámetros: project_id, task_id, commented_phone y coment.']);
            http_response_code(400);
            return;
        }

        $info_project = $this->Projects_model->get_details_copy(["id" => (int) $project_id])->getRow();

        if (!$info_project) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el proyecto con este ID: ' . $project_id]);
            http_response_code(404);
            exit();
        }

        $info_user_requested = $this->Users_model->get_details_copy(["phone" => $commented_phone])->getRow();

        if (!$info_user_requested) {
            echo json_encode(['status' => 'error', 'message' => 'No se encontro el usuario con este numero de telefono: ' . $commented_phone]);
            http_response_code(404);
            exit();
        }

        $files_data = $this->save_files();
        $files = unserialize($files_data);
        $archivos = []; // Inicializar array vacío

        foreach ($files as $file) {
            $file_name = $file['file_name'];
            $file_id = get_array_value($file, "file_id");
            $service_type = get_array_value($file, "service_type");
            $actual_file_name = remove_file_prefix($file_name);
            $thumbnail = get_source_url_of_file($file, get_setting("timeline_file_path"), "thumbnail");
            $url = get_source_url_of_file($file, get_setting("timeline_file_path"));

            // Agregar al array
            $archivos[] = [
                'url' => $url,
                'thumbnail' => $thumbnail,
                'actual_file_name' => $actual_file_name
            ];
        }

        // $files_data = [];

        // foreach ($this->request->getPost('files') as $file) {
        //     $files_data[] = $file;
        // }

        $data_comment = [
            "created_by" => $info_user_requested->id,
            "created_at" => date('Y-m-d H:i:s'),
            "description" => $coment,
            "project_id" => (int) $info_project->id,
            "task_id" => (int) $task_id
        ];

        if (!empty($files_data)) {
            $data_comment["files"] = $files_data;
        }

        $id_comment = $this->Project_comments_model->ci_save($data_comment);

        echo json_encode(array(
            "success" => true,
            'message' => "Comentario publicado a la tarea " . $task_id,
            "ID del comentario" => $id_comment,
            "archivos" => $archivos
            // "archivos_guardados" => count($files_data)
        ));
        exit();
    }

    public function create_task_and_checklist()
    {

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren los parámetros.']);
            http_response_code(400);
            return;
        }

        $info_project = $this->Projects_model->get_details(["title" => $input["subtask"]["project_title"]])->getRow();
        $info_user_requested = $this->Users_model->get_details(["phone" => $input["subtask"]["requested_phone"]])->getRow();
        $info_user_assigned = $this->Users_model->get_details(["phone" => $input["subtask"]["assigned_phone"]])->getRow();

        $data_task = [
            "title" => $input["subtask"]["title"],
            "description" => $input["subtask"]["description"],
            "project_id" => (int) $input["subtask"]["project_id"] ? $input["subtask"]["project_id"] : $info_project->id,
            "assigned_to" => $input["subtask"]["assigned_phone"] !== "" ? $info_user_assigned->id : 0,
            "status_id" => 1,
            "status" => "to_do",
            "created_date" => date('Y-m-d'),
            "context" => "project",
            "created_by" => (int) $info_user_requested->id,
            "parent_task_id" => $input["subtask"]["task_id"]
        ];

        $data_checklist = [
            "title" => $input["checklist"]["title"],
            "is_checked" => 0,
            "task_id" => (int) $input["checklist"]["task_id"],
        ];

        $id_subtask = 0;
        $id_checklist = 0;

        if (isset($input["subtask"])) {
            $id_subtask = $this->Tasks_model->ci_save($data_task);
        }
        if (isset($input["checklist"])) {
            $id_checklist = $this->Checklist_items_model->ci_save($data_checklist);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('saved'), "id_subtask" => $id_subtask, "id_checklist" => $id_checklist));
    }

    public function get_clients_by_scheduling_and_demo()
    {
        $country = $this->request->getGet('country');
        $isLead = $this->request->getGet('is_lead');
        $exists = $this->request->getGet('exists') ?? 1;
        $created_since = $this->request->getGet('created_since');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $nextStepExecuted = $this->request->getGet('next_step_executed') ?? 0;

        $clients = $this->Clients_model->get_clients_by_scheduling_and_demo(
            $country,
            $isLead,
            $created_since,
            $start_date,
            $end_date,
            $exists,
            $nextStepExecuted
        );

        header('Content-Type: application/json');
        echo json_encode(["data" => $clients], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function get_clients_by_demo_and_estimate()
    {
        $country = $this->request->getGet('country');
        $isLead = $this->request->getGet('is_lead');
        $exists = $this->request->getGet('exists') ?? 1;
        $created_since = $this->request->getGet('created_since');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $nextStepExecuted = $this->request->getGet('next_step_executed') ?? 0;

        $clients = $this->Clients_model->get_clients_by_demo_and_estimate(
            $country,
            $isLead,
            $created_since,
            $start_date,
            $end_date,
            $exists,
            $nextStepExecuted
        );

        if (is_array($clients["exit_clients"]) && !empty($clients["exit_clients"])) {
            foreach ($clients["exit_clients"] as $client) {
                $client->estimates_items = $this->Estimate_items_model->get_details(["estimate_id" => $client->last_estimate_id])->getResult();
                $client->total_price_estimate = 0;
                foreach ($client->estimates_items as $item) {
                    $client->total_price_estimate += $item->total;
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode(["data" => $clients], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function get_clients_by_estimate_and_purchase()
    {
        $country = $this->request->getGet('country');
        $isLead = $this->request->getGet('is_lead');
        $exists = $this->request->getGet('exists') ?? 1;
        $created_since = $this->request->getGet('created_since');
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');
        $nextStepExecuted = $this->request->getGet('next_step_executed') ?? 0;

        $clients = $this->Clients_model->get_clients_by_estimate_and_purchase(
            $country,
            $isLead,
            $created_since,
            $start_date,
            $end_date,
            $exists,
            $nextStepExecuted
        );

        header('Content-Type: application/json');
        echo json_encode(["data" => $clients], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function get_tickets_open()
    {
        $tickets = $this->Tickets_model->get_details_refactor(["statuses" => "open,client_replied,new"])->getResult();

        foreach ($tickets as $ticket) {
            $ticket->tasks = $this->Tasks_model->get_details_refactor(["ticket_id" => $ticket->id])->getResult();
            $ticket->comments_tickets = $this->Ticket_comments_model->get_details_by_columns(["ticket_id" => $ticket->id])->getResult();

            foreach ($ticket->tasks as $task) {
                $task->comments_tasks = $this->Project_comments_model->get_details_by_columns(["task_id" => $task->id])->getResult();
            }
        }

        header('Content-Type: application/json');
        echo json_encode(["data" => $tickets], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function get_tickets_by_range_dates($start_date, $end_date)
    {

        $info_tickets = $this->Tickets_model->get_tickets_by_range_dates($start_date, $end_date)->getResult();

        foreach ($info_tickets as $ticket) {
            $ticket->tasks = $this->Tasks_model->get_details(["ticket_id" => $ticket->id])->getResult();
            $ticket->comments_tickets = $this->Ticket_comments_model->get_details_by_columns(["ticket_id" => $ticket->id])->getResult();

            foreach ($ticket->tasks as $task) {
                $task->comments_tasks = $this->Project_comments_model->get_details_by_columns(["task_id" => $task->id])->getResult();
            }
        }

        header('Content-Type: application/json');
        echo json_encode(["data" => $info_tickets], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function get_clients_by_demo()
    {
        $info_events = $this->Clients_model->get_clients_by_demo();
        header('Content-Type: application/json');
        echo json_encode(["data" => $info_events], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function pay_invoice_by_status_onepay()
    {

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input)) {
            echo json_encode(['status' => 'error', 'message' => 'Se requieren los parámetros.']);
            http_response_code(400);
            return;
        }

        $info_user = $this->Users_model->get_details(["phone" => $input["client_phone"]])->getRow();
        $info_invoice = $this->Invoices_model->get_details([
            "client_id" => $info_user->client_id,
            "status" => "not_paid",
            "recurring_invoices" => 1,
            "subscription_id" => $input["subscription_id"],
            "due_date_interval" => 7
        ])->getRow();
        $info_subsrciption = $this->Subscriptions_model->get_details(["id" => $input["subscription_id"]])->getRow();

        if ($info_invoice) {

            if ($input["status_"] == 'approved') {

                $data_invoice_payment = [
                    "amount" => $input["amount"],
                    "payment_date" => date('Y-m-d'),
                    "payment_method_id" => "8",
                    "note" => $input["note"],
                    "invoice_id" => $info_invoice->id,
                    "subscription_id" => $input["subscription_id"],
                    "created_by" => "2",
                    "created_at" => date('Y-m-d H:i:s'),
                ];
                $data_subscription = [
                    "status" => "active",
                ];
                $save_payment_id = $this->Invoice_payments_model->ci_save($data_invoice_payment);
                $save_subscription_id = $this->Subscriptions_model->ci_save($data_subscription, $info_subsrciption->id);

                echo json_encode(array("success" => true, 'message' => app_lang('saved'), "id_invoice_payment" => $save_payment_id, "subscription_id" => $save_subscription_id));
            } else {

                $data = [
                    "status" => "rejected",
                ];

                $save_id = $this->Invoices_model->ci_save($data, $info_invoice->id);

                echo json_encode(array("success" => true, 'message' => app_lang('update'), "id_invoice" => $save_id));
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontró información de la factura.']);
        }
    }

    function get_instenace_EA_by_phone($numberPhone)
    {

        $info_instance = $this->Api_evolution_instances_model->get_details(["phone" => $numberPhone])->getRow();


        header('Content-Type: application/json');
        echo json_encode($info_instance, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }
}
