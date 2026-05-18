<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/ReporteController.php';

$controller = new ReporteController();
$controller->llaves();
