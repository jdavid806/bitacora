<?php

namespace App\Controllers;

class Footer_templates extends Security_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->access_only_admin_or_settings_admin();
    }

    function index()
    {
        return $this->template->rander("footer_templates/index");
    }

    function list_data($id = null)
    {
        if ($id) {
            $template = $this->Templates_model->get_details(["id" => $id])->getRow();
            echo json_encode(["content" => $template->default_content, "preview" => $template->custom_content]);
        } else {
            $templates = $this->Templates_model->get_details()->getResult();
            $data = [];
            foreach ($templates as $template) {
                $data[] = [
                    $template->id,
                    $template->template_name,
                    $template->subject_,
                    $template->template_type,
                ];
            }
            echo json_encode(["data" => $data]);
        }
    }

    function modal_form($id = null)
    {
        $view_data = [];
        if ($id) {
            $view_data['template'] = $this->Templates_model->get_details(['id' => $id])->getRow();
            $arr_template_name = explode("_", $view_data['template']->template_name);
            $view_data['template']->template_name = implode(" ", $arr_template_name);
        }
        return $this->template->view('footer_templates/modal_form', $view_data);
    }


    function save()
    {
        $id = $this->request->getPost('id');
        $array_template_name = explode(" ", $this->request->getPost('template_name'));
        $str_template_name = implode("_", $array_template_name);
        $data = array(
            "template_name" => $str_template_name,
            "subject_" => $this->request->getPost('template_name'),
            "default_content" => $this->request->getPost('default_content'),
            "default_content" => $this->request->getPost('default_content'),
            "custom_content" => $this->request->getPost('custom_content'),
            "template_type" => $this->request->getPost('template_type')
        );

        if ($id) {
            $success = $this->Templates_model->ci_save($data, $id);
        } else {
            $success = $this->Templates_model->ci_save($data);
        }
        if ($success) {
            echo '<script type="text/javascript"> window.onload = function() { window.history.back(); }; </script>';
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete($id)
    {
        $data = [
            "deleted" => 1
        ];
        $success = $this->Templates_model->ci_save($data, $id);
        if ($success) {
            return $this->index();
        }
    }
}

/* End of file Footer_templates.php */
/* Location: ./app/controllers/Footer_templates.php */