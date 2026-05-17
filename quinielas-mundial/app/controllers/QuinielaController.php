<?php

require_once __DIR__ . '/../models/Quiniela.php';

class QuinielaController {

    public function ingresar() {
        $usuarioSesion = $_SESSION['usuario'];

        if (is_array($usuarioSesion)) {
            $username = $usuarioSesion['username'] ?? '';
        } else {
            $username = $usuarioSesion;
        }

        $codigoPartido = $_GET['partido'] ?? null;
        $quinielaModel = new Quiniela();

        $partidos = $quinielaModel->obtenerPartidosParaPrediccion($username, $codigoPartido);
        $modoPartido = $codigoPartido !== null;

        require_once __DIR__ . '/../views/quinielas/ingresar.php';
    }

    public function guardarPrediccion() {
        $usuarioSesion = $_SESSION['usuario'];

        if (is_array($usuarioSesion)) {
            $username = $usuarioSesion['username'] ?? '';
        } else {
            $username = $usuarioSesion;
        }

        $codigoPartido = $_POST['codigo_partido'] ?? null;
        $golesLocal = $_POST['goles_local'] ?? null;
        $golesVisitante = $_POST['goles_visitante'] ?? null;

        if ($codigoPartido === null || $golesLocal === null || $golesVisitante === null) {
            header("Location: predicciones.php?error=datos");
            exit;
        }

        if ($golesLocal < 0 || $golesVisitante < 0) {
            header("Location: predicciones.php?error=goles");
            exit;
        }

        $quinielaModel = new Quiniela();

        $guardado = $quinielaModel->guardarPrediccion(
            $codigoPartido,
            $username,
            $golesLocal,
            $golesVisitante
        );

        if ($guardado) {
            header("Location: predicciones.php?success=1");
            exit;
        }

        header("Location: predicciones.php?error=tiempo");
        exit;
    }
}
