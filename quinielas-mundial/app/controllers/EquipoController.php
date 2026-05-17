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

        $equipoModel->crearEquipo(
            $_POST['pais'],
            $_POST['codigo_grupo']
        );

        header("Location: equipos.php?success=creado");
        exit;
    }

    public function actualizarEquipo()
    {
        $equipoModel = new Equipo();

        $equipoModel->actualizarEquipo(
            $_POST['pais_original'],
            $_POST['pais'],
            $_POST['codigo_grupo']
        );

        header("Location: equipos.php?success=editado");
        exit;
    }

    public function eliminarEquipo()
    {
        $equipoModel = new Equipo();

        $equipoModel->eliminarEquipo($_POST['pais']);

        header("Location: equipos.php?success=eliminado");
        exit;
    }
}
