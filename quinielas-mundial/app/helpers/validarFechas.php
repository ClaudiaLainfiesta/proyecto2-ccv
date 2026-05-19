<?php

function partidoEstaAbiertoParaVaticinio($fecha, $hora, $golesLocalOficial = null, $golesVisitanteOficial = null) {
    if ($golesLocalOficial !== null || $golesVisitanteOficial !== null) {
        return false;
    }

    $fechaHoraPartido = strtotime(trim($fecha . ' ' . $hora));

    if ($fechaHoraPartido === false) {
        return false;
    }

    return $fechaHoraPartido > time();
}

function sqlPartidoAbiertoParaVaticinio($alias = 'p') {
    return "({$alias}.fecha + {$alias}.hora) > NOW()
        AND {$alias}.goles_local_oficial IS NULL
        AND {$alias}.goles_visitante_oficial IS NULL";
}

function sqlPuedeVaticinar($alias = 'p') {
    return "
        CASE
            WHEN " . sqlPartidoAbiertoParaVaticinio($alias) . "
            THEN 1
            ELSE 0
        END
    ";
}

function partidoPuedeRecibirResultado($fecha, $hora) {
    $fechaHoraPartido = strtotime(trim($fecha . ' ' . $hora));

    if ($fechaHoraPartido === false) {
        return false;
    }

    return time() >= $fechaHoraPartido;
}