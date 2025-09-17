<?php

namespace App\Models;

class Estimates_audit_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'estimates_audit';
        parent::__construct($this->table);
    }
}
