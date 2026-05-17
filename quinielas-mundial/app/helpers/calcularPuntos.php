<?php

function calcularPuntosPrediccion($prediccionLocal, $prediccionVisitante, $oficialLocal, $oficialVisitante) {
    if ($oficialLocal === null || $oficialVisitante === null) {
        return 0;
    }

    if ((int) $prediccionLocal === (int) $oficialLocal && (int) $prediccionVisitante === (int) $oficialVisitante) {
        return 6;
    }

    $resultadoPredicho = $prediccionLocal <=> $prediccionVisitante;
    $resultadoOficial = $oficialLocal <=> $oficialVisitante;

    return $resultadoPredicho === $resultadoOficial ? 3 : 0;
}

function sqlCalcularPuntosPrediccion($aliasPrediccion = 'pr', $aliasPartido = 'p') {
    return "
        CASE
            WHEN {$aliasPrediccion}.goles_local_prediccion = {$aliasPartido}.goles_local_oficial
             AND {$aliasPrediccion}.goles_visitante_prediccion = {$aliasPartido}.goles_visitante_oficial
            THEN 6

            WHEN {$aliasPrediccion}.goles_local_prediccion > {$aliasPrediccion}.goles_visitante_prediccion
             AND {$aliasPartido}.goles_local_oficial > {$aliasPartido}.goles_visitante_oficial
            THEN 3

            WHEN {$aliasPrediccion}.goles_local_prediccion < {$aliasPrediccion}.goles_visitante_prediccion
             AND {$aliasPartido}.goles_local_oficial < {$aliasPartido}.goles_visitante_oficial
            THEN 3

            WHEN {$aliasPrediccion}.goles_local_prediccion = {$aliasPrediccion}.goles_visitante_prediccion
             AND {$aliasPartido}.goles_local_oficial = {$aliasPartido}.goles_visitante_oficial
            THEN 3

            ELSE 0
        END
    ";
}
