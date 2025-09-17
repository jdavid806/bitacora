<?php

namespace App\Models;

class Wb_Templates_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'wb_templates'; // Define la tabla 'paises'
        parent::__construct($this->table);
    }

    // Obtener todos los países o un país en particular si se especifica una opción
    function get_details($options = array())
    {
        $templates_table = $this->db->prefixTable('wb_templates');

        $sql = "SELECT CONCAT(template_name,' | ',language) as template, id
                FROM $templates_table";

        return $this->db->query($sql);
    }


    // Obtener el nombre del país por ID
    function get_especialidad_name_by_id($id)
    {
        $templates_table = $this->db->prefixTable('wb_templates');
        $sql = "SELECT template_name FROM $templates_table WHERE id=$id";
        return $this->db->query($sql)->getRow()->nombre;
    }
}
