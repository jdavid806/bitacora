<?php

namespace App\Models;

class Templates_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'templates';
        parent::__construct($this->table);
    }

    function get_details($options = array())
    {
        $templates_table = $this->db->prefixTable('templates');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $templates_table.id=$id";
        }

        $template_name = $this->_get_clean_value($options, "template_name");
        if ($template_name) {
            $where .= " AND $templates_table.template_name='$template_name'";
        }

        $template_type = $this->_get_clean_value($options, "template_type");
        $include_general_items = $this->_get_clean_value($options, "include_general_items") ?? false;
        if ($template_type) {
            $where .= " AND $templates_table.template_type in ('$template_type'" . ($include_general_items ? ", 'general-client-wpp-msg')" : ")");
        }

        $sql = "SELECT $templates_table.*
        FROM $templates_table
        WHERE $templates_table.deleted=0 $where";
        return $this->db->query($sql);
    }

    function get_final_template($template_name = "", $return_all = false)
    {
        $templates_table = $this->db->prefixTable('templates');
        $template_name = $this->_get_clean_value($template_name);

        $where = "";
        if (!$return_all) {
            //get default template only
            $where = " AND $templates_table.template_type='default' ";
        }

        $sql = "SELECT $templates_table.default_content, $templates_table.custom_content, $templates_table.subject_, $templates_table.language_,
            signature_template.custom_content AS signature_custom_message, signature_template.default_content AS signature_default_message
        FROM $templates_table
        LEFT JOIN $templates_table AS signature_template ON signature_template.template_name='signature' AND signature_template.language_=$templates_table.language_
        WHERE $templates_table.deleted=0 AND $templates_table.template_name='$template_name' $where ";
        $templates = $this->db->query($sql)->getResult();

        if ($return_all) {
            $info = array();

            foreach ($templates as $template) {

                $language = "default";
                if ($template->language_) {
                    $language = $template->language_;
                }

                $info["subject_" . $language] = $template->subject_;
                $info["message_" . $language] = $template->custom_content ? $template->custom_content : $template->default_content;
                $info["signature_" . $language] = $template->signature_custom_message ? $template->signature_custom_message : $template->signature_default_message;
            }

            return $info;
        } else {
            $result = $this->db->query($sql)->getRow();

            $info = new \stdClass();
            $info->subject = $result->subject_;
            $info->message = $result->custom_content ? $result->custom_content : $result->default_content;
            $info->signature = $result->signature_custom_message ? $result->signature_custom_message : $result->signature_default_message;

            return $info;
        }
    }
}
