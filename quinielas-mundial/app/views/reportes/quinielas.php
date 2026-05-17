<?php

require_once __DIR__ . '/../config/database.php';

class Quiniela {

    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerPartidosParaPrediccion($username) {
        $sql = "
            SELECT 
                p.codigo_partido,
                p.estadio,
                p.fecha,
                p.hora,
                p.nombre_fase,
                p.pais_local,
                p.pais_visitante,
                pr.goles_local_prediccion,
                pr.goles_visitante_prediccion
            FROM Partido p
            LEFT JOIN Prediccion pr
                ON p.codigo_partido = pr.codigo_partido
                AND pr.username = :username
            WHERE 
                p.goles_local_oficial IS NULL
                AND p.goles_visitante_oficial IS NULL
                AND (p.fecha + p.hora) > NOW()
            ORDER BY p.fecha ASC, p.hora ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        return $stmt->fetchAll();
    }

    public function guardarPrediccion($codigoPartido, $username, $golesLocal, $golesVisitante) {
        $sqlValidar = "
            SELECT codigo_partido
            FROM Partido
            WHERE 
                codigo_partido = :codigo_partido
                AND goles_local_oficial IS NULL
                AND goles_visitante_oficial IS NULL
                AND (fecha + hora) > NOW()
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