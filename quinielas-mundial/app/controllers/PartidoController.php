<?php

require_once __DIR__ . '/../models/Partido.php';

class PartidoController
{

    public function resultados()
    {
        $partidoModel = new Partido();
        $faseSeleccionada = $_GET['fase'] ?? '';
        $busquedaPartidos = trim($_GET['q'] ?? '');

        $partidos = $partidoModel->obtenerCalendario($faseSeleccionada, $busquedaPartidos);
        $fases = $partidoModel->obtenerFases();

        require_once __DIR__ . '/../views/partidos/resultados.php';
    }

    public function guardarResultado()
    {
        $codigoPartido = $_POST['codigo_partido'] ?? null;
        $golesLocal = $_POST['goles_local'] ?? null;
        $golesVisitante = $_POST['goles_visitante'] ?? null;
        $accion = $_POST['accion'] ?? 'guardar';
        $queryFase = $this->queryFaseActual();

        if (!$codigoPartido) {
            header("Location: resultados.php?error=partido" . $queryFase);
            exit;
        }

        $partidoModel = new Partido();

        if ($accion === 'quitar') {
            $partidoModel->quitarResultado($codigoPartido);
            $partidoModel->reiniciarPuntosPartido($codigoPartido);

            header("Location: resultados.php?success=quitado" . $queryFase);
            exit;
        }

        if (!$this->esEnteroNoNegativo($golesLocal) || !$this->esEnteroNoNegativo($golesVisitante)) {
            header("Location: resultados.php?error=goles" . $queryFase);
            exit;
        }

        $fasePartido = $partidoModel->obtenerFasePartido($codigoPartido);

        if ($fasePartido === null) {
            header("Location: resultados.php?error=partido" . $queryFase);
            exit;
        }

        if ((int) $golesLocal === (int) $golesVisitante && !$this->fasePermiteEmpate($fasePartido)) {
            header("Location: resultados.php?error=empate" . $queryFase);
            exit;
        }

        try {
            $partidoModel->actualizarResultado($codigoPartido, $golesLocal, $golesVisitante);
            $partidoModel->recalcularPuntosPartido($codigoPartido);
        } catch (PDOException $e) {
            header("Location: resultados.php?error=bd" . $queryFase);
            exit;
        }

        header("Location: resultados.php?success=guardado" . $queryFase);
        exit;
    }

    public function partidos()
    {
        $partidoModel = new Partido();
        $faseSeleccionada = $_GET['fase'] ?? '';
        $busquedaPartidos = trim($_GET['q'] ?? '');

        $partidos = $partidoModel->obtenerCalendario($faseSeleccionada, $busquedaPartidos);
        $equipos = $partidoModel->obtenerEquipos();
        $fases = $partidoModel->obtenerFases();

        require_once __DIR__ . '/../views/partidos/index.php';
    }

    public function guardarPartido()
    {
        $queryFase = $this->queryFaseActual();
        $error = $this->validarDatosPartido($_POST);

        if ($error !== null) {
            header("Location: partidos.php?error=" . $error . $queryFase);
            exit;
        }

        $partidoModel = new Partido();
        $error = $this->validarConflictosPartido($partidoModel, $_POST);

        if ($error !== null) {
            header("Location: partidos.php?error=" . $error . $queryFase);
            exit;
        }

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
            header("Location: partidos.php?error=" . $this->traducirErrorBaseDatos($e) . $queryFase);
            exit;
        }

        header("Location: partidos.php?success=creado" . $queryFase);
        exit;
    }

    public function actualizarPartido()
    {
        $queryFase = $this->queryFaseActual();
        $error = $this->validarDatosPartido($_POST, true);

        if ($error !== null) {
            header("Location: partidos.php?error=" . $error . $queryFase);
            exit;
        }

        $partidoModel = new Partido();
        $error = $this->validarConflictosPartido($partidoModel, $_POST, true);

        if ($error !== null) {
            header("Location: partidos.php?error=" . $error . $queryFase);
            exit;
        }

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
            header("Location: partidos.php?error=" . $this->traducirErrorBaseDatos($e) . $queryFase);
            exit;
        }

        header("Location: partidos.php?success=editado" . $queryFase);
        exit;
    }

    public function eliminarPartido()
    {
        $partidoModel = new Partido();
        $queryFase = $this->queryFaseActual();

        try {
            $partidoModel->eliminarPartido($_POST['codigo_partido']);
        } catch (PDOException $e) {
            header("Location: partidos.php?error=relacionado" . $queryFase);
            exit;
        }

        header("Location: partidos.php?success=eliminado" . $queryFase);
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

    private function validarConflictosPartido(Partido $partidoModel, $datos, $esEdicion = false)
    {
        $codigoPartido = $datos['codigo_partido'] ?? null;
        $estadio = trim($datos['estadio'] ?? '');
        $fecha = $datos['fecha'] ?? '';
        $hora = $datos['hora'] ?? '';
        $paisLocal = trim($datos['pais_local'] ?? '');
        $paisVisitante = trim($datos['pais_visitante'] ?? '');
        $codigoIgnorar = $esEdicion ? $codigoPartido : null;

        if ($partidoModel->existePartidoEnEstadioFecha($estadio, $fecha, $codigoIgnorar)) {
            return 'estadio_fecha';
        }

        if ($partidoModel->existePartidoDeEquiposEnHorario($paisLocal, $paisVisitante, $fecha, $hora, $codigoIgnorar)) {
            return 'equipo_horario';
        }

        return null;
    }

    private function queryFaseActual()
    {
        $faseActual = trim($_POST['fase_actual'] ?? '');

        return $faseActual !== '' ? '&fase=' . urlencode($faseActual) : '';
    }

    private function fasePermiteEmpate($fase)
    {
        return trim((string) $fase) === 'Fase de Grupos';
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
