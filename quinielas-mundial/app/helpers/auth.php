<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| VALIDAR SESIÓN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['usuario'])) {
    header("Location: /proyecto2-ccv/quinielas-mundial/public/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| OBTENER USERNAME
|--------------------------------------------------------------------------
*/

function obtenerUsernameSesion() {

    $usuarioSesion = $_SESSION['usuario'];

    if (is_array($usuarioSesion)) {
        return $usuarioSesion['username'] ?? '';
    }

    return $usuarioSesion;
}

/*
|--------------------------------------------------------------------------
| VALIDAR ADMIN
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| BLOQUEAR SI NO ES ADMIN
|--------------------------------------------------------------------------
*/

function soloAdmin() {

    if (!esAdmin()) {

        header("Location: /proyecto2-ccv/quinielas-mundial/public/index.php");
        exit;
    }
}
