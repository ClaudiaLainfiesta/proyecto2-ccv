<?php
session_start();

$destino = isset($_SESSION['usuario'])
    ? 'public/index.php'
    : 'public/login.php';

header('Location: ' . $destino);
exit;
