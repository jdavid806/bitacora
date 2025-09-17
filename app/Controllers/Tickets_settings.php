<?php

namespace App\Controllers;

class Tickets_settings extends Security_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    function settings_assign_tickets_automatic()
    {

        return $this->template->rander("tickets/settings/form_settings");
    }

    function modal_form_settings($id = 0)
    {
        $view_data["user_id"] = $id;

        $view_data['users'] = array("" => "-") + $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", ["user_type" => "staff"]);
        $view_data['ticket_types'] = [];
        $ticket_types = $this->Ticket_types_model->get_details()->getResult();

        foreach ($ticket_types as $ticket_type) {
            $view_data["ticket_types"][] = [
                'id' => $ticket_type->id,
                'text' => $ticket_type->title
            ];
        }

        return $this->template->view("tickets/settings/modal_form_settings", $view_data);
    }

    function list_data_user_tickets()
    {
        $data_tickets_settings = $this->Tickets_settings_model->count_tickets_by_setting()->getResult();
        $data = [];

        foreach ($data_tickets_settings as $ticket_setting) {
            if ($ticket_setting->ticket_types) {
                $data[] = [
                    $ticket_setting->user_id,
                    $ticket_setting->user_name,
                    $ticket_setting->ticket_types,
                    $ticket_setting->total_max_tickets,
                    $ticket_setting->total_tickets,
                    $ticket_setting->max_tasks,
                    $ticket_setting->total_tasks,
                ];;
            }
        }

        echo json_encode(["data" => $data]);
    }

    public function save()
    {
        $user_id = $this->request->getPost('user_id');
        $ticket_types = $this->request->getPost('ticket_type');
        $project_titles = $this->request->getPost('project_title'); // Títulos/textos de los proyectos
        $project_counts = $this->request->getPost('project_count');
        $save_ids = [];
        $task_settings_save_ids = [];

        $tickets_settings_info = $this->Tickets_settings_model->get_details(["user_id" => $user_id])->getResult();
        $tasks_settings_info = $this->Tasks_settings_model->get_details(["user_id" => $user_id])->getResult();
        if (count($tickets_settings_info) || count($tasks_settings_info)) {
            $this->Tickets_settings_model->delete_by_user($user_id);
            $this->Tasks_settings_model->delete_by_user($user_id);
        }

        foreach ($ticket_types as $type_id => $quantity) {

            $tickets_settings_data = [
                "user_id" => $user_id,
                "ticket_type_id" => $type_id,
                "max_tickets" => $quantity,
            ];

            $saved_id = $this->Tickets_settings_model->ci_save($tickets_settings_data);
            if ($saved_id) {
                $save_ids[] = $saved_id;
            }

            if (isset($project_titles[$type_id]) && isset($project_counts[$type_id])) {

                $info_project = $this->Projects_model->get_details(["title" => $project_titles[$type_id]])->getRow();

                $tasks_settings_data = [
                    "user_id" => $user_id,
                    "project_id" => $info_project->id,
                    "max_tasks" => $project_counts[$type_id],
                ];

                $project_saved_id = $this->Tasks_settings_model->ci_save($tasks_settings_data);
                if ($project_saved_id) {
                    $task_settings_save_ids[] = $project_saved_id;
                }
            }
        }

        if (count($save_ids) && count($task_settings_save_ids)) {
            echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
        } else {
            echo json_encode([
                "success" => false,
                "message" => app_lang('error_occurred'),
            ]);
        }
    }

    public function delete($user_id)
    {
        $this->Tickets_settings_model->delete_by_user($user_id);
        echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
    }
}
