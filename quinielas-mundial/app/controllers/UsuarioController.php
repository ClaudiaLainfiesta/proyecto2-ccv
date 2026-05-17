<?php

session_start();

require_once __DIR__ . '/../config/database.php';

$db = new Database();
$conn = $db->conectar();

$accion = $_POST['accion'] ?? '';

/* =====================================================
   REGISTRO
===================================================== */

if ($accion === 'registro') {

    $nombre = trim($_POST['nombre'] ?? '');
    $username = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    /* VALIDAR CAMPOS */

    if ($nombre === '' || $username === '' || $password === '') {

        $_SESSION['error'] = "Todos los campos son obligatorios";

        header("Location: ../../public/login.php?panel=registro");
        exit;
    }

    /* VALIDAR SI EXISTE */

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

    /* HASH CONTRASEÑA */

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    /* INSERTAR USUARIO */

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

    $_SESSION['success'] = "Cuenta creada correctamente";

    header("Location: ../../public/login.php?panel=login");
    exit;
}

/* =====================================================
   LOGIN
===================================================== */

if ($accion === 'login') {

    $username = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    /* VALIDAR CAMPOS */

    if ($username === '' || $password === '') {

        $_SESSION['error'] = "Todos los campos son obligatorios";

        header("Location: ../../public/login.php?panel=login");
        exit;
    }

    /* BUSCAR USUARIO */

    $sql = "SELECT *
            FROM Usuario
            WHERE username = :username
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':username' => $username
    ]);

    $usuario = $stmt->fetch();

    /* USUARIO NO EXISTE */

    if (!$usuario) {

        $_SESSION['error'] = "El usuario no existe";

        header("Location: ../../public/login.php?panel=login");
        exit;
    }

    /* VERIFICAR PASSWORD */

    if (!password_verify($password, $usuario['contrasena'])) {

        $_SESSION['error'] = "Contraseña incorrecta";

        header("Location: ../../public/login.php?panel=login");
        exit;
    }

    /* CREAR SESIÓN */

    $_SESSION['usuario'] = [
        'username' => $usuario['username'],
        'nombre' => $usuario['nombre']
    ];

    header("Location: ../../public/index.php");
    exit;
}

/* =====================================================
   SI NO EXISTE ACCIÓN
===================================================== */

header("Location: ../../public/login.php");
exit;