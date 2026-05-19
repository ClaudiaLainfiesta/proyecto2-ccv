<?php

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../models/Inicio.php';
require_once __DIR__ . '/../models/Participante.php';

class InicioController {

    public function index() {
        $usuarioSesion = $_SESSION['usuario'];
        $username = is_array($usuarioSesion) ? ($usuarioSesion['username'] ?? '') : $usuarioSesion;
        $nombre = is_array($usuarioSesion) ? ($usuarioSesion['nombre'] ?? $username) : $usuarioSesion;
        $puntos = 0;
        $esAdmin = esAdmin();

        $partidosPendientesResultado = [];
        $proximosPartidos = [];

        try {
            if (!$esAdmin && $username !== '') {
                $participanteModel = new Participante();
                $datosUsuario = $participanteModel->obtenerResumen($username);

                if ($datosUsuario) {
                    $nombre = $datosUsuario['nombre'];
                    $puntos = $datosUsuario['puntos'];
                }
            }

            $inicioModel = new Inicio();
            $partidosPendientesResultado = $inicioModel->obtenerPartidosPendientesResultado();
            $proximosPartidos = $inicioModel->obtenerProximosPartidos();
        } catch (PDOException $e) {
            $partidosPendientesResultado = [];
            $proximosPartidos = [];
        }

        require_once __DIR__ . '/../views/inicio/index.php';
    }
}
