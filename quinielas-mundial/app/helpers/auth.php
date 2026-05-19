<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario'])) {
    header("Location: /proyecto2-ccv/quinielas-mundial/public/login.php");
    exit;
}

function obtenerUsernameSesion() {

    $usuarioSesion = $_SESSION['usuario'];

    if (is_array($usuarioSesion)) {
        return $usuarioSesion['username'] ?? '';
    }

    return $usuarioSesion;
}



function obtenerUsuariosAdmin() {
    return [
        'anleu29',
        'admin1'
    ];
}

function esAdmin() {

    $username = obtenerUsernameSesion();

    return in_array($username, obtenerUsuariosAdmin());
}



function soloAdmin() {

    if (!esAdmin()) {

        header("Location: /proyecto2-ccv/quinielas-mundial/public/index.php");
        exit;
    }
}
