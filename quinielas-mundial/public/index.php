<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/InicioController.php';

$controller = new InicioController();
$controller->index();
