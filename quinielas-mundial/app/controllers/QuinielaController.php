<?php

require_once __DIR__ . '/../models/Quiniela.php';
require_once __DIR__ . '/../models/Partido.php';

class QuinielaController {

    public function ingresar() {
        $usuarioSesion = $_SESSION['usuario'];

        if (is_array($usuarioSesion)) {
            $username = $usuarioSesion['username'] ?? '';
        } else {
            $username = $usuarioSesion;
        }

        $codigoPartido = $_GET['partido'] ?? null;
        $faseSeleccionada = $_GET['fase'] ?? '';
        $quinielaModel = new Quiniela();
        $partidoModel = new Partido();

        $partidos = $quinielaModel->obtenerPartidosParaPrediccion($username, $codigoPartido, $faseSeleccionada);
        $fases = $partidoModel->obtenerFases();
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
        $faseActual = trim($_POST['fase_actual'] ?? '');
        $modoPartido = ($_POST['modo_partido'] ?? '') === '1';
        $queryContexto = $this->queryContextoPrediccion($codigoPartido, $faseActual, $modoPartido);

        if ($codigoPartido === null || $golesLocal === null || $golesVisitante === null) {
            header("Location: predicciones.php?error=datos" . $queryContexto);
            exit;
        }

        if ($golesLocal < 0 || $golesVisitante < 0) {
            header("Location: predicciones.php?error=goles" . $queryContexto);
            exit;
        }

        $quinielaModel = new Quiniela();
        $partidoModel = new Partido();
        $fasePartido = $partidoModel->obtenerFasePartido($codigoPartido);

        if ($fasePartido === null) {
            header("Location: predicciones.php?error=datos" . $queryContexto);
            exit;
        }

        if ((int) $golesLocal === (int) $golesVisitante && !$this->fasePermiteEmpate($fasePartido)) {
            header("Location: predicciones.php?error=empate" . $queryContexto);
            exit;
        }

        $guardado = $quinielaModel->guardarPrediccion(
            $codigoPartido,
            $username,
            $golesLocal,
            $golesVisitante
        );

        if ($guardado) {
            header("Location: predicciones.php?success=1" . $queryContexto);
            exit;
        }

        header("Location: predicciones.php?error=tiempo" . $queryContexto);
        exit;
    }

    private function queryContextoPrediccion($codigoPartido, $faseActual, $modoPartido)
    {
        $query = '';

        if ($modoPartido && $codigoPartido !== null) {
            $query .= '&partido=' . urlencode($codigoPartido);
        }

        if ($faseActual !== '') {
            $query .= '&fase=' . urlencode($faseActual);
        }

        return $query;
    }

    private function fasePermiteEmpate($fase)
    {
        return trim((string) $fase) === 'Fase de Grupos';
    }
}
