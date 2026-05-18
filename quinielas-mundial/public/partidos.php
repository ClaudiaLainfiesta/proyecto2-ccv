<?php
session_start();
require_once __DIR__ . '/../app/helpers/auth.php';
soloAdmin();

require_once __DIR__ . '/../app/controllers/PartidoController.php';

$controller = new PartidoController();

$accion = $_GET['accion'] ?? 'index';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['accion'] === 'crear') {
        $controller->guardarPartido();
    } elseif ($_POST['accion'] === 'editar') {
        $controller->actualizarPartido();
    } elseif ($_POST['accion'] === 'eliminar') {
        $controller->eliminarPartido();
    } elseif ($_POST['accion'] === 'generar_dieci') {
        $controller->generarDieciseisavos();
    } elseif ($_POST['accion'] === 'generar_siguiente') {
        $controller->generarSiguienteFase();
    }
    exit;
}

$controller->partidos();
