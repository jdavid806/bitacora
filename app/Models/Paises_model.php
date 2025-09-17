<?php

namespace App\Models;

class Paises_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'paises'; // Define la tabla 'paises'
        parent::__construct($this->table);
    }

    // Obtener todos los países o un país en particular si se especifica una opción
    function get_details($options = array()) {
        $country_table = $this->db->prefixTable('paises');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $country_table.id=$id";
        }

        $sql = "SELECT $country_table.*
                FROM $country_table
                WHERE 1=1 $where
                ORDER BY $country_table.nombre ASC";

        return $this->db->query($sql);
    }

    // Obtener el país con la abreviatura más corta (por ejemplo, para mostrar como predeterminado)
    function get_country_with_shortest_abbreviation() {
        $country_table = $this->db->prefixTable('paises');

        $sql = "SELECT $country_table.*
                FROM $country_table
                ORDER BY LENGTH($country_table.abreviatura) ASC
                LIMIT 1";

        return $this->db->query($sql)->getRow();
    }

    // Obtener un país por su ID
    function get_country_by_id($id) {
        $country_table = $this->db->prefixTable('paises');
        $sql = "SELECT * FROM $country_table WHERE id=$id";
        return $this->db->query($sql)->getRow();
    }

    // Obtener el nombre del país por ID
    function get_country_name_by_id($id) {
        $country_table = $this->db->prefixTable('paises');
        $sql = "SELECT nombre FROM $country_table WHERE id=$id";
        return $this->db->query($sql)->getRow()->nombre;
    }
}
