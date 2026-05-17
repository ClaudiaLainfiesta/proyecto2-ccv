<?php

require_once __DIR__ . '/../config/database.php';

class Partido
{

    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerCalendario()
    {
        $sql = "
            SELECT *
            FROM Partido
            ORDER BY fecha ASC, hora ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function actualizarResultado($codigoPartido, $golesLocal, $golesVisitante)
    {
        $sql = "
            UPDATE Partido
            SET 
                goles_local_oficial = :goles_local,
                goles_visitante_oficial = :goles_visitante
            WHERE codigo_partido = :codigo_partido
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':goles_local' => $golesLocal,
            ':goles_visitante' => $golesVisitante,
            ':codigo_partido' => $codigoPartido
        ]);
    }

    public function quitarResultado($codigoPartido)
    {
        $sql = "
            UPDATE Partido
            SET 
                goles_local_oficial = NULL,
                goles_visitante_oficial = NULL
            WHERE codigo_partido = :codigo_partido
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);
    }

    public function recalcularPuntosPartido($codigoPartido)
    {
        $sql = "
            UPDATE Prediccion pr
            SET puntos_prediccion =
                CASE
                    WHEN pr.goles_local_prediccion = p.goles_local_oficial
                     AND pr.goles_visitante_prediccion = p.goles_visitante_oficial
                    THEN 6

                    WHEN pr.goles_local_prediccion > pr.goles_visitante_prediccion
                     AND p.goles_local_oficial > p.goles_visitante_oficial
                    THEN 3

                    WHEN pr.goles_local_prediccion < pr.goles_visitante_prediccion
                     AND p.goles_local_oficial < p.goles_visitante_oficial
                    THEN 3

                    WHEN pr.goles_local_prediccion = pr.goles_visitante_prediccion
                     AND p.goles_local_oficial = p.goles_visitante_oficial
                    THEN 3

                    ELSE 0
                END
            FROM Partido p
            WHERE pr.codigo_partido = p.codigo_partido
              AND p.codigo_partido = :codigo_partido
              AND p.goles_local_oficial IS NOT NULL
              AND p.goles_visitante_oficial IS NOT NULL
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);
    }

    public function reiniciarPuntosPartido($codigoPartido)
    {
        $sql = "
            UPDATE Prediccion
            SET puntos_prediccion = 0
            WHERE codigo_partido = :codigo_partido
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);
    }

    public function obtenerEquipos()
    {
        $sql = "
        SELECT pais
        FROM Equipo
        ORDER BY pais ASC
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerFases()
    {
        $sql = "
        SELECT nombre_fase
        FROM Fase
        ORDER BY nombre_fase ASC
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function crearPartido($codigoPartido, $estadio, $fecha, $hora, $nombreFase, $paisLocal, $paisVisitante)
    {
        $sql = "
        INSERT INTO Partido (
            codigo_partido,
            estadio,
            fecha,
            hora,
            goles_local_oficial,
            goles_visitante_oficial,
            nombre_fase,
            pais_local,
            pais_visitante
        ) VALUES (
            :codigo_partido,
            :estadio,
            :fecha,
            :hora,
            NULL,
            NULL,
            :nombre_fase,
            :pais_local,
            :pais_visitante
        )
    ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido,
            ':estadio' => $estadio,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':nombre_fase' => $nombreFase,
            ':pais_local' => $paisLocal,
            ':pais_visitante' => $paisVisitante
        ]);
    }

    public function actualizarPartido($codigoPartido, $estadio, $fecha, $hora, $nombreFase, $paisLocal, $paisVisitante)
    {
        $sql = "
        UPDATE Partido
        SET
            estadio = :estadio,
            fecha = :fecha,
            hora = :hora,
            nombre_fase = :nombre_fase,
            pais_local = :pais_local,
            pais_visitante = :pais_visitante
        WHERE codigo_partido = :codigo_partido
    ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido,
            ':estadio' => $estadio,
            ':fecha' => $fecha,
            ':hora' => $hora,
            ':nombre_fase' => $nombreFase,
            ':pais_local' => $paisLocal,
            ':pais_visitante' => $paisVisitante
        ]);
    }

    public function eliminarPartido($codigoPartido)
    {
        $sql = "
        DELETE FROM Partido
        WHERE codigo_partido = :codigo_partido
    ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);
    }
}
