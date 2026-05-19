<?php

require_once __DIR__ . '/../config/database.php';

class Participante {

    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerResumen($username) {
        $sql = "
            SELECT
                u.username,
                u.nombre,
                COALESCE(SUM(p.puntos_prediccion), 0) AS puntos
            FROM Usuario u
            LEFT JOIN Prediccion p
                ON u.username = p.username
            WHERE u.username = :username
            GROUP BY u.username, u.nombre
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        return $stmt->fetch();
    }
}
