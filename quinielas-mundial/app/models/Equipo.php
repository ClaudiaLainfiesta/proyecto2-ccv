<?php

require_once __DIR__ . '/../config/database.php';

class Equipo {

    private $pdo;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->conectar();
    }

    public function obtenerEquipos() {
        $sql = "
            SELECT 
                e.pais,
                e.codigo_grupo,
                e.bandera
            FROM Equipo e
            ORDER BY e.codigo_grupo ASC, e.pais ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerGrupos() {
        $sql = "
            SELECT codigo_grupo
            FROM Grupo
            ORDER BY codigo_grupo ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function crearEquipo($pais, $codigoGrupo) {
        $sql = "
            INSERT INTO Equipo (
                pais,
                codigo_grupo,
                bandera
            ) VALUES (
                :pais,
                :codigo_grupo,
                '\\x00'
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':pais' => $pais,
            ':codigo_grupo' => $codigoGrupo
        ]);
    }

    public function actualizarEquipo($paisOriginal, $pais, $codigoGrupo) {
        $sql = "
            UPDATE Equipo
            SET 
                pais = :pais,
                codigo_grupo = :codigo_grupo
            WHERE pais = :pais_original
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':pais_original' => $paisOriginal,
            ':pais' => $pais,
            ':codigo_grupo' => $codigoGrupo
        ]);
    }

    public function eliminarEquipo($pais) {
        $sql = "
            DELETE FROM Equipo
            WHERE pais = :pais
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':pais' => $pais
        ]);
    }
}
