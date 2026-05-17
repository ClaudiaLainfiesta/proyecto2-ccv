<?php

require_once __DIR__ . '/../models/Partido.php';

class ReporteController {

    public function calendario() {

        $partidoModel = new Partido();

        $partidos = $partidoModel->obtenerCalendario();

        require_once __DIR__ . '/../views/reportes/calendario.php';
    }
}