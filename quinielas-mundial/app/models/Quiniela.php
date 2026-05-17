<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/validarFechas.php';

class Quiniela {

    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerPartidosParaPrediccion($username, $codigoPartido = null) {
        $puedeVaticinarSql = sqlPuedeVaticinar('p');

        if ($codigoPartido !== null) {
            $sql = "
                SELECT 
                    p.codigo_partido,
                    p.estadio,
                    p.fecha,
                    p.hora,
                    p.nombre_fase,
                    p.pais_local,
                    p.pais_visitante,
                    el.bandera AS bandera_local,
                    ev.bandera AS bandera_visitante,
                    p.goles_local_oficial,
                    p.goles_visitante_oficial,
                    pr.goles_local_prediccion,
                    pr.goles_visitante_prediccion,
                    pr.puntos_prediccion,
                    {$puedeVaticinarSql} AS puede_vaticinar
                FROM Partido p
                LEFT JOIN Equipo el
                    ON p.pais_local = el.pais
                LEFT JOIN Equipo ev
                    ON p.pais_visitante = ev.pais
                LEFT JOIN Prediccion pr
                    ON p.codigo_partido = pr.codigo_partido
                    AND pr.username = :username
                WHERE p.codigo_partido = :codigo_partido
                ORDER BY p.fecha ASC, p.hora ASC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':codigo_partido' => $codigoPartido
            ]);

            return $stmt->fetchAll();
        }

        $sql = "
            SELECT 
                p.codigo_partido,
                p.estadio,
                p.fecha,
                p.hora,
                p.nombre_fase,
                p.pais_local,
                p.pais_visitante,
                el.bandera AS bandera_local,
                ev.bandera AS bandera_visitante,
                p.goles_local_oficial,
                p.goles_visitante_oficial,
                pr.goles_local_prediccion,
                pr.goles_visitante_prediccion,
                pr.puntos_prediccion,
                {$puedeVaticinarSql} AS puede_vaticinar
            FROM Partido p
            LEFT JOIN Equipo el
                ON p.pais_local = el.pais
            LEFT JOIN Equipo ev
                ON p.pais_visitante = ev.pais
            INNER JOIN Prediccion pr
                ON p.codigo_partido = pr.codigo_partido
                AND pr.username = :username
            ORDER BY p.fecha ASC, p.hora ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        return $stmt->fetchAll();
    }

    public function guardarPrediccion($codigoPartido, $username, $golesLocal, $golesVisitante) {
        $partidoAbiertoSql = sqlPartidoAbiertoParaVaticinio();

        $sqlValidar = "
            SELECT codigo_partido
            FROM Partido p
            WHERE p.codigo_partido = :codigo_partido
              AND {$partidoAbiertoSql}
        ";

        $stmtValidar = $this->pdo->prepare($sqlValidar);
        $stmtValidar->execute([
            ':codigo_partido' => $codigoPartido
        ]);

        $partidoDisponible = $stmtValidar->fetch();

        if (!$partidoDisponible) {
            return false;
        }

        $sql = "
            INSERT INTO Prediccion (
                codigo_partido,
                username,
                goles_local_prediccion,
                goles_visitante_prediccion,
                puntos_prediccion
            ) VALUES (
                :codigo_partido,
                :username,
                :goles_local,
                :goles_visitante,
                0
            )
            ON CONFLICT (codigo_partido, username)
            DO UPDATE SET
                goles_local_prediccion = EXCLUDED.goles_local_prediccion,
                goles_visitante_prediccion = EXCLUDED.goles_visitante_prediccion
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':codigo_partido' => $codigoPartido,
            ':username' => $username,
            ':goles_local' => $golesLocal,
            ':goles_visitante' => $golesVisitante
        ]);
    }
}
