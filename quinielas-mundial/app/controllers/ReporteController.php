<?php

require_once __DIR__ . '/../models/Partido.php';
require_once __DIR__ . '/../models/Reporte.php';

class ReporteController {

    public function calendario() {

        $partidoModel = new Partido();

        $partidos = $partidoModel->obtenerCalendario();

        require_once __DIR__ . '/../views/reportes/calendario.php';
    }

    public function posiciones() {
        $reporteModel = new Reporte();
        $grupos = $reporteModel->obtenerPosiciones();

        require_once __DIR__ . '/../views/reportes/posiciones.php';
    }

    public function ranking() {
        $reporteModel = new Reporte();
        $ranking = $reporteModel->obtenerRanking();

        require_once __DIR__ . '/../views/reportes/ranking.php';
    }

    public function quinielas() {
        $reporteModel = new Reporte();
        $quinielas = $reporteModel->obtenerQuinielas();

        require_once __DIR__ . '/../views/reportes/quinielas.php';
    }
}
