<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$accion = $_POST['accion'] ?? '';

if ($accion === 'login') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    // Aquí validas contra la BD
    // Si es correcto:
    $_SESSION['usuario'] = $usuario;

    header('Location: ../../public/index.php');
    exit;
}

if ($accion === 'registro') {
    $nombre = $_POST['nombre'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    // Aquí insertas en la BD

    header('Location: ../../public/login.php');
    exit;
}