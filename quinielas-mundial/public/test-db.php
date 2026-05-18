<?php
require_once __DIR__ . '/../app/config/database.php';

$db = new Database();
$conexion = $db->conectar();

echo "Conexión exitosa a PostgreSQL";
