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

    $mime = 'image/png';

    if (str_starts_with($binario, "\xFF\xD8\xFF")) {
        $mime = 'image/jpeg';
    } elseif (str_starts_with($binario, 'RIFF') && substr($binario, 8, 4) === 'WEBP') {
        $mime = 'image/webp';
    }

    return 'data:' . $mime . ';base64,' . base64_encode($binario);
}

function equipoConBandera($pais, $bandera, $clases = 'h-6 w-9') {
    $paisSeguro = htmlspecialchars($pais ?? '');
    $src = obtenerBanderaSrc($bandera);

    if ($src === '') {
        return '<span class="inline-flex max-w-full min-w-0 items-center gap-2 align-middle"><span class="min-w-0 break-words">' . $paisSeguro . '</span></span>';
    }

    return '<span class="inline-flex max-w-full min-w-0 items-center gap-2 align-middle">' .
        '<img src="' . htmlspecialchars($src) . '" alt="Bandera de ' . $paisSeguro . '" class="' . $clases . ' rounded-sm object-cover border border-white/10 shadow-sm">' .
        '<span class="min-w-0 break-words">' . $paisSeguro . '</span>' .
    '</span>';
}
