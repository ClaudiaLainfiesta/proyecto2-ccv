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
                    WHEN 'Tercer Lugar' THEN 6
                    WHEN 'Final' THEN 7
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

    public function obtenerFechaHoraPartido($codigoPartido)
    {
        $sql = "
            SELECT fecha, hora
            FROM Partido
            WHERE codigo_partido = :codigo_partido
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':codigo_partido' => $codigoPartido]);
        $fila = $stmt->fetch();

        return $fila ?: null;
    }

    public function crearFasesEliminatorias()
    {
        $fases = [
            'Dieciseisavos de Final',
            'Octavos de Final',
            'Cuartos de Final',
            'Semifinales',
            'Tercer Lugar',
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

    public function existePartido($codigoPartido)
    {
        $sql = "
            SELECT 1
            FROM Partido
            WHERE codigo_partido = :codigo_partido
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);

        return (bool) $stmt->fetchColumn();
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

        $clasificados = $this->obtenerClasificadosPorPosicion();

        if (count($clasificados['primeros']) < 12 || count($clasificados['segundos']) < 12 || count($clasificados['terceros']) < 8) {
            return [
                'ok' => false,
                'mensaje' => 'La fase de grupos aún no tiene suficientes resultados para generar 32 clasificados.'
            ];
        }

        $tercerosAsignados = $this->asignarMejoresTerceros($clasificados['terceros']);

        if ($tercerosAsignados === null) {
            return [
                'ok' => false,
                'mensaje' => 'No se pudo acomodar a los mejores terceros en las reglas de dieciseisavos.'
            ];
        }

        $this->crearFasesEliminatorias();

        $this->pdo->beginTransaction();

        try {
            foreach ($this->calendarioDieciseisavos() as $codigo => $partido) {
                $local = $this->resolverClasificado($partido['local'], $clasificados, $tercerosAsignados);
                $visitante = $this->resolverClasificado($partido['visitante'], $clasificados, $tercerosAsignados);

                if ($local === null || $visitante === null) {
                    throw new RuntimeException('Clasificado no encontrado para el partido ' . $codigo);
                }

                $this->insertarPartidoGenerado(
                    $codigo,
                    $partido['estadio'],
                    $partido['fecha'],
                    $partido['hora'],
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
        foreach ($this->calendarioFasesEliminatorias() as $faseActual => $partidos) {
            $partidosYaGenerados = true;

            foreach (array_keys($partidos) as $codigoPartido) {
                if (!$this->existePartido($codigoPartido)) {
                    $partidosYaGenerados = false;
                    break;
                }
            }

            if (!$this->existenPartidosFase($faseActual) || $partidosYaGenerados) {
                continue;
            }

            foreach ($partidos as $partido) {
                foreach (['local', 'visitante'] as $lado) {
                    $equipo = ($partido[$lado]['tipo'] === 'ganador')
                        ? $this->obtenerGanadorPartido($partido[$lado]['partido'])
                        : $this->obtenerPerdedorPartido($partido[$lado]['partido']);

                    if ($equipo === null) {
                        return [
                            'ok' => false,
                            'mensaje' => 'Aún faltan resultados o desempates en ' . $faseActual . '.'
                        ];
                    }
                }
            }

            $this->crearFasesEliminatorias();

            $this->pdo->beginTransaction();

            try {
                foreach ($partidos as $codigo => $partido) {
                    if ($this->existePartido($codigo)) {
                        continue;
                    }

                    $local = ($partido['local']['tipo'] === 'ganador')
                        ? $this->obtenerGanadorPartido($partido['local']['partido'])
                        : $this->obtenerPerdedorPartido($partido['local']['partido']);
                    $visitante = ($partido['visitante']['tipo'] === 'ganador')
                        ? $this->obtenerGanadorPartido($partido['visitante']['partido'])
                        : $this->obtenerPerdedorPartido($partido['visitante']['partido']);

                    $this->insertarPartidoGenerado(
                        $codigo,
                        $partido['estadio'],
                        $partido['fecha'],
                        $partido['hora'],
                        $partido['fase'],
                        $local,
                        $visitante
                    );
                }

                $this->pdo->commit();

                return [
                    'ok' => true,
                    'mensaje' => 'Fase generada correctamente.'
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

    private function obtenerClasificadosPorPosicion()
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
            FROM orden_grupo
            WHERE posicion_grupo <= 3
            ORDER BY codigo_grupo ASC, posicion_grupo ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $clasificados = [
            'primeros' => [],
            'segundos' => [],
            'terceros' => []
        ];

        foreach ($stmt->fetchAll() as $fila) {
            $grupo = $this->letraGrupo((int) $fila['codigo_grupo']);

            if ((int) $fila['posicion_grupo'] === 1) {
                $clasificados['primeros'][$grupo] = $fila;
            } elseif ((int) $fila['posicion_grupo'] === 2) {
                $clasificados['segundos'][$grupo] = $fila;
            } elseif ((int) $fila['posicion_grupo'] === 3) {
                $clasificados['terceros'][$grupo] = $fila;
            }
        }

        uasort($clasificados['terceros'], function ($a, $b) {
            return [$b['puntos'], $b['diferencia_goles'], $b['goles_favor'], $a['pais']]
                <=> [$a['puntos'], $a['diferencia_goles'], $a['goles_favor'], $b['pais']];
        });

        $clasificados['terceros'] = array_slice($clasificados['terceros'], 0, 8, true);

        return $clasificados;
    }

    private function calendarioDieciseisavos()
    {
        return [
            73 => ['fecha' => '2026-06-28', 'hora' => '15:00:00', 'estadio' => 'Estadio Los Ángeles', 'local' => '2A', 'visitante' => '2B'],
            74 => ['fecha' => '2026-06-29', 'hora' => '15:00:00', 'estadio' => 'Estadio Boston', 'local' => '1E', 'visitante' => '3A/B/C/D/F'],
            75 => ['fecha' => '2026-06-29', 'hora' => '18:00:00', 'estadio' => 'Estadio Monterrey', 'local' => '1F', 'visitante' => '2C'],
            76 => ['fecha' => '2026-06-29', 'hora' => '21:00:00', 'estadio' => 'Estadio Houston', 'local' => '1C', 'visitante' => '2F'],
            77 => ['fecha' => '2026-06-30', 'hora' => '15:00:00', 'estadio' => 'Estadio Nueva York Nueva Jersey', 'local' => '1I', 'visitante' => '3C/D/F/G/H'],
            78 => ['fecha' => '2026-06-30', 'hora' => '18:00:00', 'estadio' => 'Estadio Dallas', 'local' => '2E', 'visitante' => '2I'],
            79 => ['fecha' => '2026-06-30', 'hora' => '21:00:00', 'estadio' => 'Estadio Ciudad de México', 'local' => '1A', 'visitante' => '3C/E/F/H/I'],
            80 => ['fecha' => '2026-07-01', 'hora' => '15:00:00', 'estadio' => 'Estadio Atlanta', 'local' => '1L', 'visitante' => '3E/H/I/J/K'],
            81 => ['fecha' => '2026-07-01', 'hora' => '18:00:00', 'estadio' => 'Estadio Bahía de San Francisco', 'local' => '1D', 'visitante' => '3B/E/F/I/J'],
            82 => ['fecha' => '2026-07-01', 'hora' => '21:00:00', 'estadio' => 'Estadio Seattle', 'local' => '1G', 'visitante' => '3A/E/H/I/J'],
            83 => ['fecha' => '2026-07-02', 'hora' => '15:00:00', 'estadio' => 'Estadio Toronto', 'local' => '2K', 'visitante' => '2L'],
            84 => ['fecha' => '2026-07-02', 'hora' => '18:00:00', 'estadio' => 'Estadio Los Ángeles', 'local' => '1H', 'visitante' => '2J'],
            85 => ['fecha' => '2026-07-02', 'hora' => '21:00:00', 'estadio' => 'Estadio BC Place Vancouver', 'local' => '1B', 'visitante' => '3E/F/G/I/J'],
            86 => ['fecha' => '2026-07-03', 'hora' => '15:00:00', 'estadio' => 'Estadio Miami', 'local' => '1J', 'visitante' => '2H'],
            87 => ['fecha' => '2026-07-03', 'hora' => '18:00:00', 'estadio' => 'Estadio Kansas City', 'local' => '1K', 'visitante' => '3D/E/I/J/L'],
            88 => ['fecha' => '2026-07-03', 'hora' => '21:00:00', 'estadio' => 'Estadio Dallas', 'local' => '2D', 'visitante' => '2G']
        ];
    }

    private function calendarioFasesEliminatorias()
    {
        return [
            'Dieciseisavos de Final' => [
                89 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-04', 'hora' => '15:00:00', 'estadio' => 'Estadio Filadelfia', 'local' => $this->ganadorDe(74), 'visitante' => $this->ganadorDe(77)],
                90 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-04', 'hora' => '19:00:00', 'estadio' => 'Estadio Houston', 'local' => $this->ganadorDe(73), 'visitante' => $this->ganadorDe(75)],
                91 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-05', 'hora' => '15:00:00', 'estadio' => 'Estadio Nueva York Nueva Jersey', 'local' => $this->ganadorDe(76), 'visitante' => $this->ganadorDe(78)],
                92 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-05', 'hora' => '19:00:00', 'estadio' => 'Estadio Ciudad de México', 'local' => $this->ganadorDe(79), 'visitante' => $this->ganadorDe(80)],
                93 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-06', 'hora' => '15:00:00', 'estadio' => 'Estadio Dallas', 'local' => $this->ganadorDe(83), 'visitante' => $this->ganadorDe(84)],
                94 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-06', 'hora' => '19:00:00', 'estadio' => 'Estadio Seattle', 'local' => $this->ganadorDe(81), 'visitante' => $this->ganadorDe(82)],
                95 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-07', 'hora' => '15:00:00', 'estadio' => 'Estadio Atlanta', 'local' => $this->ganadorDe(86), 'visitante' => $this->ganadorDe(88)],
                96 => ['fase' => 'Octavos de Final', 'fecha' => '2026-07-07', 'hora' => '19:00:00', 'estadio' => 'Estadio BC Place Vancouver', 'local' => $this->ganadorDe(85), 'visitante' => $this->ganadorDe(87)]
            ],
            'Octavos de Final' => [
                97 => ['fase' => 'Cuartos de Final', 'fecha' => '2026-07-09', 'hora' => '19:00:00', 'estadio' => 'Estadio Boston', 'local' => $this->ganadorDe(89), 'visitante' => $this->ganadorDe(90)],
                98 => ['fase' => 'Cuartos de Final', 'fecha' => '2026-07-10', 'hora' => '19:00:00', 'estadio' => 'Estadio Los Ángeles', 'local' => $this->ganadorDe(93), 'visitante' => $this->ganadorDe(94)],
                99 => ['fase' => 'Cuartos de Final', 'fecha' => '2026-07-11', 'hora' => '15:00:00', 'estadio' => 'Estadio Miami', 'local' => $this->ganadorDe(91), 'visitante' => $this->ganadorDe(92)],
                100 => ['fase' => 'Cuartos de Final', 'fecha' => '2026-07-11', 'hora' => '19:00:00', 'estadio' => 'Estadio Kansas City', 'local' => $this->ganadorDe(95), 'visitante' => $this->ganadorDe(96)]
            ],
            'Cuartos de Final' => [
                101 => ['fase' => 'Semifinales', 'fecha' => '2026-07-14', 'hora' => '19:00:00', 'estadio' => 'Estadio Dallas', 'local' => $this->ganadorDe(97), 'visitante' => $this->ganadorDe(98)],
                102 => ['fase' => 'Semifinales', 'fecha' => '2026-07-15', 'hora' => '19:00:00', 'estadio' => 'Estadio Atlanta', 'local' => $this->ganadorDe(99), 'visitante' => $this->ganadorDe(100)]
            ],
            'Semifinales' => [
                103 => ['fase' => 'Tercer Lugar', 'fecha' => '2026-07-18', 'hora' => '15:00:00', 'estadio' => 'Estadio Miami', 'local' => $this->perdedorDe(101), 'visitante' => $this->perdedorDe(102)],
                104 => ['fase' => 'Final', 'fecha' => '2026-07-19', 'hora' => '15:00:00', 'estadio' => 'Estadio Nueva York Nueva Jersey', 'local' => $this->ganadorDe(101), 'visitante' => $this->ganadorDe(102)]
            ]
        ];
    }

    private function ganadorDe($codigoPartido)
    {
        return ['tipo' => 'ganador', 'partido' => $codigoPartido];
    }

    private function perdedorDe($codigoPartido)
    {
        return ['tipo' => 'perdedor', 'partido' => $codigoPartido];
    }

    private function resolverClasificado($regla, $clasificados, $tercerosAsignados)
    {
        $posicion = substr($regla, 0, 1);

        if ($posicion === '1') {
            return $clasificados['primeros'][substr($regla, 1, 1)]['pais'] ?? null;
        }

        if ($posicion === '2') {
            return $clasificados['segundos'][substr($regla, 1, 1)]['pais'] ?? null;
        }

        return $tercerosAsignados[$regla]['pais'] ?? null;
    }

    private function asignarMejoresTerceros($terceros)
    {
        $slots = [];

        foreach ($this->calendarioDieciseisavos() as $partido) {
            if (str_starts_with($partido['visitante'], '3')) {
                $slots[] = $partido['visitante'];
            }
        }

        return $this->buscarAsignacionTerceros($slots, $terceros, []);
    }

    private function buscarAsignacionTerceros($slots, $terceros, $asignados)
    {
        if (empty($slots)) {
            return $asignados;
        }

        $slot = array_shift($slots);
        $permitidos = explode('/', substr($slot, 1));

        foreach ($terceros as $grupo => $equipo) {
            if (!in_array($grupo, $permitidos, true)) {
                continue;
            }

            $restantes = $terceros;
            unset($restantes[$grupo]);
            $nuevoAsignados = $asignados;
            $nuevoAsignados[$slot] = $equipo;

            $resultado = $this->buscarAsignacionTerceros($slots, $restantes, $nuevoAsignados);

            if ($resultado !== null) {
                return $resultado;
            }
        }

        return null;
    }

    private function letraGrupo($codigoGrupo)
    {
        return chr(64 + $codigoGrupo);
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

    private function obtenerPerdedoresFase($nombreFase)
    {
        $sql = "
            SELECT
                codigo_partido,
                CASE
                    WHEN goles_local_oficial > goles_visitante_oficial THEN pais_visitante
                    WHEN goles_visitante_oficial > goles_local_oficial THEN pais_local
                    ELSE NULL
                END AS pais_perdedor
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

        $perdedores = $stmt->fetchAll();

        return array_values(array_filter($perdedores, function ($perdedor) {
            return !empty($perdedor['pais_perdedor']);
        }));
    }

    private function obtenerGanadorPartido($codigoPartido)
    {
        $sql = "
            SELECT
                CASE
                    WHEN goles_local_oficial > goles_visitante_oficial THEN pais_local
                    WHEN goles_visitante_oficial > goles_local_oficial THEN pais_visitante
                    ELSE NULL
                END AS pais_ganador
            FROM Partido
            WHERE codigo_partido = :codigo_partido
              AND goles_local_oficial IS NOT NULL
              AND goles_visitante_oficial IS NOT NULL
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);

        $ganador = $stmt->fetchColumn();

        return $ganador !== false ? $ganador : null;
    }

    private function obtenerPerdedorPartido($codigoPartido)
    {
        $sql = "
            SELECT
                CASE
                    WHEN goles_local_oficial > goles_visitante_oficial THEN pais_visitante
                    WHEN goles_visitante_oficial > goles_local_oficial THEN pais_local
                    ELSE NULL
                END AS pais_perdedor
            FROM Partido
            WHERE codigo_partido = :codigo_partido
              AND goles_local_oficial IS NOT NULL
              AND goles_visitante_oficial IS NOT NULL
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':codigo_partido' => $codigoPartido
        ]);

        $perdedor = $stmt->fetchColumn();

        return $perdedor !== false ? $perdedor : null;
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

    public function equiposSonDelMismoGrupo($paisLocal, $paisVisitante)
    {
        $sql = "
            SELECT 1
            FROM Equipo e1
            JOIN Equipo e2
                ON e1.codigo_grupo = e2.codigo_grupo
            WHERE e1.pais = :pais_local
            AND e2.pais = :pais_visitante
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pais_local'     => $paisLocal,
            ':pais_visitante' => $paisVisitante
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function yaJugaronEnFaseGrupos($paisLocal, $paisVisitante, $codigoIgnorar = null)
    {
        $sql = "
            SELECT 1
            FROM Partido
            WHERE nombre_fase = 'Fase de Grupos'
            AND (
                (pais_local = :local_a AND pais_visitante = :visitante_a)
                OR
                (pais_local = :visitante_b AND pais_visitante = :local_b)
            )
        ";

        $params = [
            ':local_a'     => $paisLocal,
            ':visitante_a' => $paisVisitante,
            ':visitante_b' => $paisVisitante,
            ':local_b'     => $paisLocal
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
