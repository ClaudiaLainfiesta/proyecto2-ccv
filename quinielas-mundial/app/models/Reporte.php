<?php

require_once __DIR__ . '/../config/database.php';

class Reporte {

    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerRanking($usuariosAdmin = []) {
        $params = [];
        $filtroAdmins = '';

        if (!empty($usuariosAdmin)) {
            $placeholders = [];

            foreach ($usuariosAdmin as $index => $usernameAdmin) {
                $placeholder = ':admin_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = $usernameAdmin;
            }

            $filtroAdmins = 'WHERE u.username NOT IN (' . implode(', ', $placeholders) . ')';
        }

        $sql = "
            SELECT
                u.username,
                u.nombre,
                COALESCE(SUM(pr.puntos_prediccion), 0) AS puntos,
                COUNT(pr.codigo_partido) AS predicciones
            FROM Usuario u
            LEFT JOIN Prediccion pr
                ON u.username = pr.username
            {$filtroAdmins}
            GROUP BY u.username, u.nombre
            ORDER BY puntos DESC, predicciones DESC, u.nombre ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function obtenerPosiciones() {
        $sql = "
            WITH partidos_grupo AS (
                SELECT
                    p.*,
                    el.codigo_grupo AS grupo_local,
                    ev.codigo_grupo AS grupo_visitante
                FROM Partido p
                INNER JOIN Equipo el
                    ON p.pais_local = el.pais
                INNER JOIN Equipo ev
                    ON p.pais_visitante = ev.pais
                WHERE p.goles_local_oficial IS NOT NULL
                  AND p.goles_visitante_oficial IS NOT NULL
                  AND el.codigo_grupo = ev.codigo_grupo
            ),
            estadisticas AS (
                SELECT
                    pais_local AS pais,
                    grupo_local AS codigo_grupo,
                    1 AS jugados,
                    CASE WHEN goles_local_oficial > goles_visitante_oficial THEN 1 ELSE 0 END AS ganados,
                    CASE WHEN goles_local_oficial = goles_visitante_oficial THEN 1 ELSE 0 END AS empatados,
                    CASE WHEN goles_local_oficial < goles_visitante_oficial THEN 1 ELSE 0 END AS perdidos,
                    goles_local_oficial AS goles_favor,
                    goles_visitante_oficial AS goles_contra,
                    CASE
                        WHEN goles_local_oficial > goles_visitante_oficial THEN 3
                        WHEN goles_local_oficial = goles_visitante_oficial THEN 1
                        ELSE 0
                    END AS puntos
                FROM partidos_grupo

                UNION ALL

                SELECT
                    pais_visitante AS pais,
                    grupo_visitante AS codigo_grupo,
                    1 AS jugados,
                    CASE WHEN goles_visitante_oficial > goles_local_oficial THEN 1 ELSE 0 END AS ganados,
                    CASE WHEN goles_visitante_oficial = goles_local_oficial THEN 1 ELSE 0 END AS empatados,
                    CASE WHEN goles_visitante_oficial < goles_local_oficial THEN 1 ELSE 0 END AS perdidos,
                    goles_visitante_oficial AS goles_favor,
                    goles_local_oficial AS goles_contra,
                    CASE
                        WHEN goles_visitante_oficial > goles_local_oficial THEN 3
                        WHEN goles_visitante_oficial = goles_local_oficial THEN 1
                        ELSE 0
                    END AS puntos
                FROM partidos_grupo
            )
            SELECT
                e.codigo_grupo,
                e.pais,
                e.bandera,
                COALESCE(SUM(es.jugados), 0) AS jugados,
                COALESCE(SUM(es.ganados), 0) AS ganados,
                COALESCE(SUM(es.empatados), 0) AS empatados,
                COALESCE(SUM(es.perdidos), 0) AS perdidos,
                COALESCE(SUM(es.goles_favor), 0) AS goles_favor,
                COALESCE(SUM(es.goles_contra), 0) AS goles_contra,
                COALESCE(SUM(es.goles_favor), 0) - COALESCE(SUM(es.goles_contra), 0) AS diferencia_goles,
                COALESCE(SUM(es.puntos), 0) AS puntos
            FROM Equipo e
            LEFT JOIN estadisticas es
                ON e.pais = es.pais
                AND e.codigo_grupo = es.codigo_grupo
            GROUP BY e.codigo_grupo, e.pais, e.bandera
            ORDER BY e.codigo_grupo ASC, puntos DESC, diferencia_goles DESC, goles_favor DESC, e.pais ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $filas = $stmt->fetchAll();
        $grupos = [];

        foreach ($filas as $fila) {
            $grupos[$fila['codigo_grupo']][] = $fila;
        }

        return $grupos;
    }

    public function obtenerQuinielas() {
        $sql = "
            SELECT
                pr.username,
                u.nombre,
                pr.codigo_partido,
                pr.goles_local_prediccion,
                pr.goles_visitante_prediccion,
                pr.puntos_prediccion,
                p.fecha,
                p.hora,
                p.nombre_fase,
                p.estadio,
                p.pais_local,
                p.pais_visitante,
                p.goles_local_oficial,
                p.goles_visitante_oficial,
                el.bandera AS bandera_local,
                ev.bandera AS bandera_visitante
            FROM Prediccion pr
            INNER JOIN Usuario u
                ON pr.username = u.username
            INNER JOIN Partido p
                ON pr.codigo_partido = p.codigo_partido
            LEFT JOIN Equipo el
                ON p.pais_local = el.pais
            LEFT JOIN Equipo ev
                ON p.pais_visitante = ev.pais
            ORDER BY p.fecha ASC, p.hora ASC, u.nombre ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
