<?php

function obtenerBanderaSrc($bandera) {
    if (empty($bandera)) {
        return '';
    }

    if (is_resource($bandera)) {
        $bandera = stream_get_contents($bandera);
    }

    if (!is_string($bandera) || $bandera === '') {
        return '';
    }

    if ($bandera === "\\x00" || $bandera === "\x00") {
        return '';
    }

    if (str_starts_with($bandera, '\\x')) {
        $binario = hex2bin(substr($bandera, 2));
    } elseif (ctype_xdigit($bandera)) {
        $binario = hex2bin($bandera);
    } else {
        $binario = $bandera;
    }

    if ($binario === false || $binario === '') {
        return '';
    }

    return 'data:image/png;base64,' . base64_encode($binario);
}

function equipoConBandera($pais, $bandera, $clases = 'h-6 w-9') {
    $paisSeguro = htmlspecialchars($pais ?? '');
    $src = obtenerBanderaSrc($bandera);

    if ($src === '') {
        return '<span class="inline-flex items-center gap-2">' . $paisSeguro . '</span>';
    }

    return '<span class="inline-flex items-center gap-2">' .
        '<img src="' . htmlspecialchars($src) . '" alt="Bandera de ' . $paisSeguro . '" class="' . $clases . ' rounded-sm object-cover border border-white/10 shadow-sm">' .
        '<span>' . $paisSeguro . '</span>' .
    '</span>';
}
