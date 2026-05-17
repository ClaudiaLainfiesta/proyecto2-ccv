<?php
session_start();

require_once __DIR__ . '/../app/helpers/auth.php';
soloAdmin();

require_once __DIR__ . '/../app/controllers/EquipoController.php';

$controller = new EquipoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'crear') {
        $controller->guardarEquipo();
    } elseif ($_POST['accion'] === 'editar') {
        $controller->actualizarEquipo();
    } elseif ($_POST['accion'] === 'eliminar') {
        $controller->eliminarEquipo();
    }
    exit;
}

$controller->equipos();