<?php
$mysqli = new mysqli("127.0.0.1", "medicaso_rootBase", "5qA?o]t6d-h25qA?o]t6d-h2", "appnaros_sistema");

if ($mysqli->connect_errno) {
    die("Error al conectar: " . $mysqli->connect_error);
}

echo "Conexión exitosa a la base de datos";
