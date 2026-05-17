<?php

require_once __DIR__ . '/../config/database.php';

class Inicio {

    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerPartidosPendientesResultado($limite = 5) {
        $sql = "
            SELECT
                p.*,
                el.bandera AS bandera_local,
                ev.bandera AS bandera_visitante
            FROM Partido p
            LEFT JOIN Equipo el
                ON p.pais_local = el.pais
            LEFT JOIN Equipo ev
                ON p.pais_visitante = ev.pais
            WHERE
                (p.fecha + p.hora) < NOW()
                AND p.goles_local_oficial IS NULL
                AND p.goles_visitante_oficial IS NULL
            ORDER BY p.fecha ASC, p.hora ASC
            LIMIT :limite
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerProximosPartidos($limite = 5) {
        $sql = "
            SELECT
                p.*,
                el.bandera AS bandera_local,
                ev.bandera AS bandera_visitante
            FROM Partido p
            LEFT JOIN Equipo el
                ON p.pais_local = el.pais
            LEFT JOIN Equipo ev
                ON p.pais_visitante = ev.pais
            WHERE (p.fecha + p.hora) >= NOW()
            ORDER BY p.fecha ASC, p.hora ASC
            LIMIT :limite
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
