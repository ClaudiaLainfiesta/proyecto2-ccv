<?php

require_once __DIR__ . '/../models/Equipo.php';


class EquipoController
{

    public function equipos()
    {
        $equipoModel = new Equipo();

        $equipos = $equipoModel->obtenerEquipos();
        $grupos = $equipoModel->obtenerGrupos();

        require_once __DIR__ . '/../views/equipos/index.php';
    }

    public function guardarEquipo()
    {
        $equipoModel = new Equipo();
        $pais = trim($_POST['pais'] ?? '');
        $codigoGrupo = $_POST['codigo_grupo'] ?? '';

        if ($pais === '' || $codigoGrupo === '') {
            header("Location: equipos.php?error=datos");
            exit;
        }

        if ($equipoModel->existeEquipo($pais)) {
            header("Location: equipos.php?error=duplicado");
            exit;
        }

        if ($equipoModel->contarEquiposEnGrupo($codigoGrupo) >= 4) {
            header("Location: equipos.php?error=grupo_lleno");
            exit;
        }

        $bandera = $this->leerBanderaSubida();

        if (!$bandera['ok']) {
            header("Location: equipos.php?error=" . $bandera['error']);
            exit;
        }

        try {
            $equipoModel->crearEquipo($pais, $codigoGrupo, $bandera['hex']);
        } catch (PDOException $e) {
            header("Location: equipos.php?error=" . $this->traducirErrorBaseDatos($e));
            exit;
        }

        header("Location: equipos.php?success=creado");
        exit;
    }

    public function actualizarEquipo()
    {
        $equipoModel = new Equipo();
        $paisOriginal = trim($_POST['pais_original'] ?? '');
        $pais = trim($_POST['pais'] ?? '');
        $codigoGrupo = $_POST['codigo_grupo'] ?? '';

        if ($paisOriginal === '' || $pais === '' || $codigoGrupo === '') {
            header("Location: equipos.php?error=datos");
            exit;
        }

        if (strcasecmp($paisOriginal, $pais) !== 0 && $equipoModel->existeEquipo($pais)) {
            header("Location: equipos.php?error=duplicado");
            exit;
        }

        if ($equipoModel->contarEquiposEnGrupo($codigoGrupo, $paisOriginal) >= 4) {
            header("Location: equipos.php?error=grupo_lleno");
            exit;
        }

        $bandera = $this->leerBanderaSubida();

        if (!$bandera['ok']) {
            header("Location: equipos.php?error=" . $bandera['error']);
            exit;
        }

        try {
            $equipoModel->actualizarEquipo($paisOriginal, $pais, $codigoGrupo, $bandera['hex']);
        } catch (PDOException $e) {
            header("Location: equipos.php?error=" . $this->traducirErrorBaseDatos($e));
            exit;
        }

        header("Location: equipos.php?success=editado");
        exit;
    }

    public function eliminarEquipo()
    {
        $equipoModel = new Equipo();

        try {
            $equipoModel->eliminarEquipo($_POST['pais']);
        } catch (PDOException $e) {
            header("Location: equipos.php?error=relacionado");
            exit;
        }

        header("Location: equipos.php?success=eliminado");
        exit;
    }

    private function leerBanderaSubida()
    {
        if (!isset($_FILES['bandera']) || $_FILES['bandera']['error'] === UPLOAD_ERR_NO_FILE) {
            return [
                'ok' => true,
                'hex' => null
            ];
        }

        if ($_FILES['bandera']['error'] !== UPLOAD_ERR_OK) {
            return [
                'ok' => false,
                'error' => 'imagen'
            ];
        }

        if ($_FILES['bandera']['size'] > 2 * 1024 * 1024) {
            return [
                'ok' => false,
                'error' => 'imagen_tamano'
            ];
        }

        $rutaTemporal = $_FILES['bandera']['tmp_name'];
        $infoImagen = getimagesize($rutaTemporal);
        $tiposPermitidos = [
            IMAGETYPE_PNG,
            IMAGETYPE_JPEG,
            IMAGETYPE_WEBP
        ];

        if ($infoImagen === false || !in_array($infoImagen[2], $tiposPermitidos, true)) {
            return [
                'ok' => false,
                'error' => 'imagen_tipo'
            ];
        }

        $contenido = file_get_contents($rutaTemporal);

        if ($contenido === false || $contenido === '') {
            return [
                'ok' => false,
                'error' => 'imagen'
            ];
        }

        return [
            'ok' => true,
            'hex' => bin2hex($contenido)
        ];
    }

    private function traducirErrorBaseDatos(PDOException $e)
    {
        $codigo = $e->getCode();

        if ($codigo === '23505') {
            return 'duplicado';
        }

        if ($codigo === '23503') {
            return 'referencia';
        }

        return 'bd';
    }
}
