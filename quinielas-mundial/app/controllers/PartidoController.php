<?php

require_once __DIR__ . '/../models/Partido.php';

class PartidoController
{

    public function resultados()
    {
        $partidoModel = new Partido();

        $partidos = $partidoModel->obtenerCalendario();

        require_once __DIR__ . '/../views/partidos/resultados.php';
    }

    public function guardarResultado()
    {
        $codigoPartido = $_POST['codigo_partido'] ?? null;
        $golesLocal = $_POST['goles_local'] ?? null;
        $golesVisitante = $_POST['goles_visitante'] ?? null;
        $accion = $_POST['accion'] ?? 'guardar';

        if (!$codigoPartido) {
            header("Location: resultados.php?error=partido");
            exit;
        }

        $partidoModel = new Partido();

        if ($accion === 'quitar') {
            $partidoModel->quitarResultado($codigoPartido);
            $partidoModel->reiniciarPuntosPartido($codigoPartido);

            header("Location: resultados.php?success=quitado");
            exit;
        }

        if ($golesLocal === null || $golesVisitante === null || $golesLocal < 0 || $golesVisitante < 0) {
            header("Location: resultados.php?error=goles");
            exit;
        }

        $partidoModel->actualizarResultado($codigoPartido, $golesLocal, $golesVisitante);
        $partidoModel->recalcularPuntosPartido($codigoPartido);

        header("Location: resultados.php?success=guardado");
        exit;
    }

    public function partidos()
    {
        $partidoModel = new Partido();

        $partidos = $partidoModel->obtenerCalendario();
        $equipos = $partidoModel->obtenerEquipos();
        $fases = $partidoModel->obtenerFases();

        require_once __DIR__ . '/../views/partidos/index.php';
    }

    public function guardarPartido()
    {
        $partidoModel = new Partido();

        $partidoModel->crearPartido(
            $_POST['codigo_partido'],
            $_POST['estadio'],
            $_POST['fecha'],
            $_POST['hora'],
            $_POST['nombre_fase'],
            $_POST['pais_local'],
            $_POST['pais_visitante']
        );

        header("Location: partidos.php?success=creado");
        exit;
    }

    public function actualizarPartido()
    {
        $partidoModel = new Partido();

        $partidoModel->actualizarPartido(
            $_POST['codigo_partido'],
            $_POST['estadio'],
            $_POST['fecha'],
            $_POST['hora'],
            $_POST['nombre_fase'],
            $_POST['pais_local'],
            $_POST['pais_visitante']
        );

        header("Location: partidos.php?success=editado");
        exit;
    }

    public function eliminarPartido()
    {
        $partidoModel = new Partido();

        $partidoModel->eliminarPartido($_POST['codigo_partido']);

        header("Location: partidos.php?success=eliminado");
        exit;
    }
}
