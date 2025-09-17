<?php

namespace App\Models;

class Especialidades_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'especialidades'; // Define la tabla 'paises'
        parent::__construct($this->table);
    }

    // Obtener todos los países o un país en particular si se especifica una opción
    function get_details($options = array()) {
        $especialidad_table = $this->db->prefixTable('especialidades');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $especialidad_table.id=$id";
        }

        $sql = "SELECT $especialidad_table.*
                FROM $especialidad_table
                WHERE 1=1 $where
                ORDER BY $especialidad_table.nombre ASC";

        return $this->db->query($sql);
    }

    // Obtener el nombre del país por ID
    function get_especialidad_name_by_id($id) {
        $especialidad_table = $this->db->prefixTable('especialidades');
        $sql = "SELECT nombre FROM $especialidad_table WHERE id=$id";
        return $this->db->query($sql)->getRow()->nombre;
    }
}
