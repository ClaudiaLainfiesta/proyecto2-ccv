<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/controllers/QuinielaController.php';

$controller = new QuinielaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->guardarPrediccion();
} else {
    $controller->ingresar();
}