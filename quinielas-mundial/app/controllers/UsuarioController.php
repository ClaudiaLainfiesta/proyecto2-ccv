<?php

session_start();

require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->conectar();

$accion = $_POST['accion'] ?? '';

if ($accion === 'registro') {

    $nombre = trim($_POST['nombre'] ?? '');
    $username = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($nombre === '' || $username === '' || $password === '' || $confirmPassword === '') {

        $_SESSION['error'] = "Todos los campos son obligatorios";

        header("Location: ../../public/login.php?panel=registro");
        exit;
    }

    if ($password !== $confirmPassword) {

        $_SESSION['error'] = "Las contraseñas no coinciden";

        header("Location: ../../public/login.php?panel=registro");
        exit;
    }

    if (strlen($password) < 6 || !preg_match('/\p{L}/u', $password) || !preg_match('/\d/', $password)) {

        $_SESSION['error'] = "La contraseña debe tener mínimo 6 caracteres, una letra y un número";

        header("Location: ../../public/login.php?panel=registro");
        exit;
    }

    $sql = "SELECT username
            FROM Usuario
            WHERE username = :username";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':username' => $username
    ]);

    if ($stmt->fetch()) {

        $_SESSION['error'] = "Ese usuario ya existe";

        header("Location: ../../public/login.php?panel=registro");
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Usuario (
                username,
                nombre,
                contrasena
            )
            VALUES (
                :username,
                :nombre,
                :contrasena
            )";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':nombre' => $nombre,
        ':contrasena' => $passwordHash
    ]);
    
    $_SESSION['usuario'] = [
        'username' => $username,
        'nombre' => $nombre
    ];

    $_SESSION['success'] = "¡Cuenta creada con éxito! Bienvenido(a) " . htmlspecialchars($nombre);

    header("Location: ../../public/index.php");
    exit;
}

if ($accion === 'login') {

    $username = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $_SESSION['error'] = "Todos los campos son obligatorios";

        header("Location: ../../public/login.php?panel=login");
        exit;
    }

    $sql = "SELECT *
            FROM Usuario
            WHERE username = :username
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':username' => $username
    ]);

    $usuario = $stmt->fetch();

    if (!$usuario) {

        $_SESSION['error'] = "El usuario no existe";

        header("Location: ../../public/login.php?panel=login");
        exit;
    }

    if (!password_verify($password, $usuario['contrasena'])) {

        $_SESSION['error'] = "Contraseña incorrecta";

        header("Location: ../../public/login.php?panel=login");
        exit;
    }

    $_SESSION['usuario'] = [
        'username' => $usuario['username'],
        'nombre' => $usuario['nombre']
    ];

    header("Location: ../../public/index.php");
    exit;
}

header("Location: ../../public/login.php");
exit;
