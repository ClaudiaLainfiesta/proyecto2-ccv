<?php
session_start();

$destino = isset($_SESSION['usuario'])
    ? 'quinielas-mundial/public/index.php'
    : 'quinielas-mundial/public/login.php';

header('Location: ' . $destino);
exit;
