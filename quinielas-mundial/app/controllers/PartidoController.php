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

        if (!$this->esEnteroNoNegativo($golesLocal) || !$this->esEnteroNoNegativo($golesVisitante)) {
            header("Location: resultados.php?error=goles");
            exit;
        }

        try {
            $partidoModel->actualizarResultado($codigoPartido, $golesLocal, $golesVisitante);
            $partidoModel->recalcularPuntosPartido($codigoPartido);
        } catch (PDOException $e) {
            header("Location: resultados.php?error=bd");
            exit;
        }

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
        $error = $this->validarDatosPartido($_POST);

        if ($error !== null) {
            header("Location: partidos.php?error=" . $error);
            exit;
        }

        $partidoModel = new Partido();

        try {
            $partidoModel->crearPartido(
                $_POST['codigo_partido'],
                $_POST['estadio'],
                $_POST['fecha'],
                $_POST['hora'],
                $_POST['nombre_fase'],
                $_POST['pais_local'],
                $_POST['pais_visitante']
            );
        } catch (PDOException $e) {
            header("Location: partidos.php?error=" . $this->traducirErrorBaseDatos($e));
            exit;
        }

        header("Location: partidos.php?success=creado");
        exit;
    }

    public function actualizarPartido()
    {
        $error = $this->validarDatosPartido($_POST, true);

        if ($error !== null) {
            header("Location: partidos.php?error=" . $error);
            exit;
        }

        $partidoModel = new Partido();

        try {
            $partidoModel->actualizarPartido(
                $_POST['codigo_partido'],
                $_POST['estadio'],
                $_POST['fecha'],
                $_POST['hora'],
                $_POST['nombre_fase'],
                $_POST['pais_local'],
                $_POST['pais_visitante']
            );
        } catch (PDOException $e) {
            header("Location: partidos.php?error=" . $this->traducirErrorBaseDatos($e));
            exit;
        }

        header("Location: partidos.php?success=editado");
        exit;
    }

    public function eliminarPartido()
    {
        $partidoModel = new Partido();

        try {
            $partidoModel->eliminarPartido($_POST['codigo_partido']);
        } catch (PDOException $e) {
            header("Location: partidos.php?error=relacionado");
            exit;
        }

        header("Location: partidos.php?success=eliminado");
        exit;
    }

    public function generarDieciseisavos()
    {
        $partidoModel = new Partido();
        $resultado = $partidoModel->generarDieciseisavos();
        $parametro = $resultado['ok'] ? 'success=generado_dieci' : 'error=' . urlencode($resultado['mensaje']);

        header("Location: partidos.php?" . $parametro);
        exit;
    }

    public function generarSiguienteFase()
    {
        $partidoModel = new Partido();
        $resultado = $partidoModel->generarSiguienteFase();
        $parametro = $resultado['ok'] ? 'success=generado_fase' : 'error=' . urlencode($resultado['mensaje']);

        header("Location: partidos.php?" . $parametro);
        exit;
    }

    private function validarDatosPartido($datos, $esEdicion = false)
    {
        $codigoPartido = $datos['codigo_partido'] ?? null;
        $estadio = trim($datos['estadio'] ?? '');
        $fecha = $datos['fecha'] ?? '';
        $hora = $datos['hora'] ?? '';
        $nombreFase = trim($datos['nombre_fase'] ?? '');
        $paisLocal = trim($datos['pais_local'] ?? '');
        $paisVisitante = trim($datos['pais_visitante'] ?? '');

        if (!$this->esEnteroPositivo($codigoPartido)) {
            return 'codigo';
        }

        if ($estadio === '' || $fecha === '' || $hora === '' || $nombreFase === '' || $paisLocal === '' || $paisVisitante === '') {
            return 'datos';
        }

        if ($paisLocal === $paisVisitante) {
            return 'paises';
        }

        if (strtotime($fecha . ' ' . $hora) === false) {
            return 'fecha';
        }

        return null;
    }

    private function esEnteroNoNegativo($valor)
    {
        return filter_var($valor, FILTER_VALIDATE_INT) !== false && (int) $valor >= 0;
    }

    private function esEnteroPositivo($valor)
    {
        return filter_var($valor, FILTER_VALIDATE_INT) !== false && (int) $valor > 0;
    }

    private function traducirErrorBaseDatos(PDOException $e)
    {
        $codigo = $e->getCode();

        if ($codigo === '23505') {
            return 'duplicado';
        }

        if ($codigo === '23514') {
            return 'paises';
        }

        if ($codigo === '23503') {
            return 'referencia';
        }

        return 'bd';
    }
}
