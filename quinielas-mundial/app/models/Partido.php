<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/calcularPuntos.php';
require_once __DIR__ . '/../helpers/validarFechas.php';

class Partido
{

    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerCalendario($fase = null, $busqueda = null)
    {
        $puedeVaticinarSql = sqlPuedeVaticinar('p');
        $condiciones = [];
        $params = [];

        if ($fase !== null && $fase !== '') {
            $condiciones[] = 'p.nombre_fase = :fase';
            $params[':fase'] = $fase;
        }

        if ($busqueda !== null && trim($busqueda) !== '') {
            $condiciones[] = "(
                p.pais_local ILIKE :busqueda
                OR p.pais_visitante ILIKE :busqueda
                OR p.estadio ILIKE :busqueda
                OR p.fecha::text = :busqueda_exacta
                OR to_char(p.fecha, 'DD/MM/YYYY') = :busqueda_exacta
            )";
            $params[':busqueda'] = '%' . trim($busqueda) . '%';
            $params[':busqueda_exacta'] = trim($busqueda);
        }

        $where = !empty($condiciones) ? 'WHERE ' . implode(' AND ', $condiciones) : '';

        $sql = "
            SELECT
                p.*,
                el.bandera AS bandera_local,
                ev.bandera AS bandera_visitante,
                {$puedeVaticinarSql} AS puede_vaticinar
            FROM Partido p
            LEFT JOIN Equipo el
                ON p.pais_local = el.pais
            LEFT JOIN Equipo ev
                ON p.pais_visitante = ev.pais
            {$where}
            ORDER BY p.fecha ASC, p.hora ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

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
        $calcularPuntosSql = sqlCalcularPuntosPrediccion('pr', 'p');

        $sql = "
            UPDATE Prediccion pr
            SET puntos_prediccion = {$calcularPuntosSql}
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
            ORDER BY
                CASE nombre_fase
                    WHEN 'Fase de Grupos' THEN 1
                    WHEN 'Dieciseisavos de Final' THEN 2
                    WHEN 'Octavos de Final' THEN 3
                    WHEN 'Cuartos de Final' THEN 4
                    WHEN 'Semifinales' THEN 5
                    WHEN 'Final' THEN 6
                    ELSE 99
                END,
                nombre_fase ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerFasePartido($codigoPartido)
    {
        $sql = "
            SELECT nombre_fase
            FROM Partido
            WHERE codigo_partido = :codigo_partido
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);

        $fase = $stmt->fetchColumn();

        return $fase !== false ? $fase : null;
    }

    public function crearFasesEliminatorias()
    {
        $fases = [
            'Dieciseisavos de Final',
            'Octavos de Final',
            'Cuartos de Final',
            'Semifinales',
            'Final'
        ];

        $sql = "
            INSERT INTO Fase (nombre_fase)
            VALUES (:nombre_fase)
            ON CONFLICT (nombre_fase) DO NOTHING
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($fases as $fase) {
            $stmt->execute([
                ':nombre_fase' => $fase
            ]);
        }
    }

    public function existenPartidosFase($nombreFase)
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM Partido
            WHERE nombre_fase = :nombre_fase
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre_fase' => $nombreFase
        ]);

        $fila = $stmt->fetch();

        return (int) ($fila['total'] ?? 0) > 0;
    }

    public function obtenerSiguienteCodigoPartido()
    {
        $sql = "
            SELECT COALESCE(MAX(codigo_partido), 0) + 1 AS siguiente
            FROM Partido
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $fila = $stmt->fetch();

        return (int) $fila['siguiente'];
    }

    public function generarDieciseisavos()
    {
        if ($this->existenPartidosFase('Dieciseisavos de Final')) {
            return [
                'ok' => false,
                'mensaje' => 'Los dieciseisavos ya fueron generados.'
            ];
        }

        if (!$this->faseGruposCompleta()) {
            return [
                'ok' => false,
                'mensaje' => 'La fase de grupos debe tener todos sus resultados oficiales antes de generar dieciseisavos.'
            ];
        }

        $clasificados = $this->obtenerClasificadosDieciseisavos();

        if (count($clasificados) < 32) {
            return [
                'ok' => false,
                'mensaje' => 'La fase de grupos aún no tiene suficientes resultados para generar 32 clasificados.'
            ];
        }

        $this->crearFasesEliminatorias();

        $this->pdo->beginTransaction();

        try {
            $codigo = $this->obtenerSiguienteCodigoPartido();
            $fechaBase = $this->obtenerFechaDespuesDeFase('Fase de Grupos');

            for ($i = 0; $i < 16; $i++) {
                $local = $clasificados[$i]['pais'];
                $visitante = $clasificados[31 - $i]['pais'];
                $fecha = date('Y-m-d', strtotime($fechaBase . ' +' . intdiv($i, 4) . ' days'));
                $hora = sprintf('%02d:00:00', 12 + (($i % 4) * 3));

                $this->insertarPartidoGenerado(
                    $codigo++,
                    'Por definir',
                    $fecha,
                    $hora,
                    'Dieciseisavos de Final',
                    $local,
                    $visitante
                );
            }

            $this->pdo->commit();

            return [
                'ok' => true,
                'mensaje' => 'Dieciseisavos generados correctamente.'
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();

            return [
                'ok' => false,
                'mensaje' => 'No se pudieron generar los dieciseisavos.'
            ];
        }
    }

    public function generarSiguienteFase()
    {
        $flujo = [
            'Dieciseisavos de Final' => ['siguiente' => 'Octavos de Final', 'partidos' => 8],
            'Octavos de Final' => ['siguiente' => 'Cuartos de Final', 'partidos' => 4],
            'Cuartos de Final' => ['siguiente' => 'Semifinales', 'partidos' => 2],
            'Semifinales' => ['siguiente' => 'Final', 'partidos' => 1]
        ];

        foreach ($flujo as $faseActual => $datos) {
            if (!$this->existenPartidosFase($faseActual)) {
                continue;
            }

            if ($this->existenPartidosFase($datos['siguiente'])) {
                continue;
            }

            $ganadores = $this->obtenerGanadoresFase($faseActual);

            if (count($ganadores) < ($datos['partidos'] * 2)) {
                return [
                    'ok' => false,
                    'mensaje' => 'Aún faltan resultados o desempates en ' . $faseActual . '.'
                ];
            }

            $this->crearFasesEliminatorias();

            $this->pdo->beginTransaction();

            try {
                $codigo = $this->obtenerSiguienteCodigoPartido();
                $fechaBase = $this->obtenerFechaDespuesDeFase($faseActual);

                for ($i = 0; $i < $datos['partidos']; $i++) {
                    $local = $ganadores[$i * 2]['pais_ganador'];
                    $visitante = $ganadores[($i * 2) + 1]['pais_ganador'];
                    $fecha = date('Y-m-d', strtotime($fechaBase . ' +' . intdiv($i, 2) . ' days'));
                    $hora = sprintf('%02d:00:00', 15 + (($i % 2) * 4));

                    $this->insertarPartidoGenerado(
                        $codigo++,
                        'Por definir',
                        $fecha,
                        $hora,
                        $datos['siguiente'],
                        $local,
                        $visitante
                    );
                }

                $this->pdo->commit();

                return [
                    'ok' => true,
                    'mensaje' => $datos['siguiente'] . ' generada correctamente.'
                ];
            } catch (PDOException $e) {
                $this->pdo->rollBack();

                return [
                    'ok' => false,
                    'mensaje' => 'No se pudo generar la siguiente fase.'
                ];
            }
        }

        return [
            'ok' => false,
            'mensaje' => 'No hay una fase eliminatoria lista para avanzar.'
        ];
    }

    private function obtenerClasificadosDieciseisavos()
    {
        $sql = "
            WITH partidos_grupo AS (
                SELECT
                    p.*,
                    el.codigo_grupo AS grupo_local,
                    ev.codigo_grupo AS grupo_visitante
                FROM Partido p
                INNER JOIN Equipo el ON p.pais_local = el.pais
                INNER JOIN Equipo ev ON p.pais_visitante = ev.pais
                WHERE p.nombre_fase = 'Fase de Grupos'
                  AND el.codigo_grupo = ev.codigo_grupo
                  AND p.goles_local_oficial IS NOT NULL
                  AND p.goles_visitante_oficial IS NOT NULL
            ),
            estadisticas AS (
                SELECT
                    pais_local AS pais,
                    grupo_local AS codigo_grupo,
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
                    goles_visitante_oficial AS goles_favor,
                    goles_local_oficial AS goles_contra,
                    CASE
                        WHEN goles_visitante_oficial > goles_local_oficial THEN 3
                        WHEN goles_visitante_oficial = goles_local_oficial THEN 1
                        ELSE 0
                    END AS puntos
                FROM partidos_grupo
            ),
            tabla AS (
                SELECT
                    e.pais,
                    e.codigo_grupo,
                    COALESCE(SUM(es.puntos), 0) AS puntos,
                    COALESCE(SUM(es.goles_favor), 0) AS goles_favor,
                    COALESCE(SUM(es.goles_contra), 0) AS goles_contra,
                    COALESCE(SUM(es.goles_favor), 0) - COALESCE(SUM(es.goles_contra), 0) AS diferencia_goles
                FROM Equipo e
                LEFT JOIN estadisticas es
                    ON e.pais = es.pais
                    AND e.codigo_grupo = es.codigo_grupo
                GROUP BY e.pais, e.codigo_grupo
            ),
            orden_grupo AS (
                SELECT
                    *,
                    ROW_NUMBER() OVER (
                        PARTITION BY codigo_grupo
                        ORDER BY puntos DESC, diferencia_goles DESC, goles_favor DESC, pais ASC
                    ) AS posicion_grupo
                FROM tabla
            ),
            clasificados AS (
                SELECT *
                FROM orden_grupo
                WHERE posicion_grupo <= 2

                UNION ALL

                SELECT *
                FROM (
                    SELECT *
                    FROM orden_grupo
                    WHERE posicion_grupo = 3
                    ORDER BY puntos DESC, diferencia_goles DESC, goles_favor DESC, pais ASC
                    LIMIT 8
                ) mejores_terceros
            )
            SELECT *
            FROM clasificados
            ORDER BY puntos DESC, diferencia_goles DESC, goles_favor DESC, pais ASC
            LIMIT 32
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function faseGruposCompleta()
    {
        $sql = "
            SELECT
                COUNT(*) AS total,
                COUNT(*) FILTER (
                    WHERE p.goles_local_oficial IS NOT NULL
                      AND p.goles_visitante_oficial IS NOT NULL
                ) AS completos
            FROM Partido p
            INNER JOIN Equipo el ON p.pais_local = el.pais
            INNER JOIN Equipo ev ON p.pais_visitante = ev.pais
            WHERE p.nombre_fase = 'Fase de Grupos'
              AND el.codigo_grupo = ev.codigo_grupo
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $fila = $stmt->fetch();

        return (int) ($fila['total'] ?? 0) > 0 && (int) $fila['total'] === (int) $fila['completos'];
    }

    private function obtenerGanadoresFase($nombreFase)
    {
        $sql = "
            SELECT
                codigo_partido,
                CASE
                    WHEN goles_local_oficial > goles_visitante_oficial THEN pais_local
                    WHEN goles_visitante_oficial > goles_local_oficial THEN pais_visitante
                    ELSE NULL
                END AS pais_ganador
            FROM Partido
            WHERE nombre_fase = :nombre_fase
              AND goles_local_oficial IS NOT NULL
              AND goles_visitante_oficial IS NOT NULL
            ORDER BY codigo_partido ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre_fase' => $nombreFase
        ]);

        $ganadores = $stmt->fetchAll();

        return array_values(array_filter($ganadores, function ($ganador) {
            return !empty($ganador['pais_ganador']);
        }));
    }

    private function obtenerFechaDespuesDeFase($nombreFase)
    {
        $sql = "
            SELECT COALESCE(MAX(fecha), CURRENT_DATE) + INTERVAL '1 day' AS fecha
            FROM Partido
            WHERE nombre_fase = :nombre_fase
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nombre_fase' => $nombreFase
        ]);

        $fila = $stmt->fetch();

        return date('Y-m-d', strtotime($fila['fecha']));
    }

    private function insertarPartidoGenerado($codigoPartido, $estadio, $fecha, $hora, $nombreFase, $paisLocal, $paisVisitante)
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

    public function existePartidoEnEstadioFecha($estadio, $fecha, $codigoIgnorar = null)
    {
        $sql = "
            SELECT 1
            FROM Partido
            WHERE LOWER(estadio) = LOWER(:estadio)
              AND fecha = :fecha
        ";

        $params = [
            ':estadio' => $estadio,
            ':fecha' => $fecha
        ];

        if ($codigoIgnorar !== null) {
            $sql .= " AND codigo_partido <> :codigo_ignorar";
            $params[':codigo_ignorar'] = $codigoIgnorar;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
    }

    public function existePartidoDeEquiposEnHorario($paisLocal, $paisVisitante, $fecha, $hora, $codigoIgnorar = null)
    {
        $sql = "
            SELECT 1
            FROM Partido
            WHERE fecha = :fecha
              AND hora = :hora
              AND (
                  pais_local IN (:pais_local_a, :pais_visitante_a)
                  OR pais_visitante IN (:pais_local_b, :pais_visitante_b)
              )
        ";

        $params = [
            ':pais_local_a' => $paisLocal,
            ':pais_visitante_a' => $paisVisitante,
            ':pais_local_b' => $paisLocal,
            ':pais_visitante_b' => $paisVisitante,
            ':fecha' => $fecha,
            ':hora' => $hora
        ];

        if ($codigoIgnorar !== null) {
            $sql .= " AND codigo_partido <> :codigo_ignorar";
            $params[':codigo_ignorar'] = $codigoIgnorar;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (bool) $stmt->fetchColumn();
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
