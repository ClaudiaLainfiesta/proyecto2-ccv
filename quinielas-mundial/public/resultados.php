<?php
session_start();

require_once __DIR__ . '/../app/helpers/auth.php';
soloAdmin();
require_once __DIR__ . '/../app/controllers/PartidoController.php';

$controller = new PartidoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->guardarResultado();
} else {
    $controller->resultados();
}
