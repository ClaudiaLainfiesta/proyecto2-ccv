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

    public function existeEquipo($pais) {
        $sql = "
            SELECT 1
            FROM Equipo
            WHERE LOWER(pais) = LOWER(:pais)
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':pais' => $pais
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function crearEquipo($pais, $codigoGrupo, $banderaHex = null) {
        $banderaHex = $banderaHex ?? '00';

        $sql = "
            INSERT INTO Equipo (
                pais,
                codigo_grupo,
                bandera
            ) VALUES (
                :pais,
                :codigo_grupo,
                decode(:bandera_hex, 'hex')
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':pais' => $pais,
            ':codigo_grupo' => $codigoGrupo,
            ':bandera_hex' => $banderaHex
        ]);
    }

    public function actualizarEquipo($paisOriginal, $pais, $codigoGrupo, $banderaHex = null) {
        $actualizarBandera = $banderaHex !== null;

        $sql = "
            UPDATE Equipo
            SET 
                pais = :pais,
                codigo_grupo = :codigo_grupo
                " . ($actualizarBandera ? ", bandera = decode(:bandera_hex, 'hex')" : "") . "
            WHERE pais = :pais_original
        ";

        $stmt = $this->pdo->prepare($sql);

        $params = [
            ':pais_original' => $paisOriginal,
            ':pais' => $pais,
            ':codigo_grupo' => $codigoGrupo
        ];

        if ($actualizarBandera) {
            $params[':bandera_hex'] = $banderaHex;
        }

        return $stmt->execute($params);
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
