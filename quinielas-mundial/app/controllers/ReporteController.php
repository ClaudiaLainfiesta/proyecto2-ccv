<?php

require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../models/Reporte.php';
require_once __DIR__ . '/../helpers/auth.php';

class ReporteController {

    public function calendario() {

        $partidoModel = new Partido();
        $faseSeleccionada = $_GET['fase'] ?? '';
        $busquedaPartidos = trim($_GET['q'] ?? '');

        $partidos = $partidoModel->obtenerCalendario($faseSeleccionada, $busquedaPartidos);
        $fases = $partidoModel->obtenerFases();

        require_once __DIR__ . '/../views/reportes/calendario.php';
    }

    public function posiciones() {
        $reporteModel = new Reporte();
        $grupos = $reporteModel->obtenerPosiciones();

        require_once __DIR__ . '/../views/reportes/posiciones.php';
    }

    public function ranking() {
        $reporteModel = new Reporte();
        $ranking = $reporteModel->obtenerRanking(obtenerUsuariosAdmin());

        require_once __DIR__ . '/../views/reportes/ranking.php';
    }

    public function quinielas() {
        $reporteModel = new Reporte();
        $quinielas = $reporteModel->obtenerQuinielas();

        require_once __DIR__ . '/../views/reportes/quinielas.php';
    }

    public function llaves() {
        $reporteModel = new Reporte();
        $partidosLlaves = $reporteModel->obtenerLlavesEliminatorias();

        require_once __DIR__ . '/../views/reportes/llaves.php';
    }
}
