<?php
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Quinielas Mundial 2026</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0b1018] px-4">

<div class="relative w-[760px] h-[460px] bg-white rounded-[28px] overflow-hidden shadow-2xl">

    <input type="checkbox" id="toggle" class="peer hidden">

    <div class="absolute top-0 left-0 w-1/2 h-full bg-gradient-to-br from-violet-800 to-blue-700 z-20 transition-all duration-700 ease-in-out peer-checked:translate-x-full"></div>

    <!-- Panel Copa lado izquierdo -->
    <div class="absolute top-0 left-0 w-1/2 h-full z-30 flex flex-col items-center justify-center gap-6 px-8 transition-all duration-700 ease-in-out peer-checked:opacity-0 peer-checked:-translate-x-16">
        <img src="assets/img/copa2026.png" alt="Copa Mundial 2026" class="w-64 drop-shadow-2xl">

        <label for="toggle" class="px-8 py-3 border border-white/70 rounded-full text-white text-sm cursor-pointer hover:bg-white/20 transition">
            CREAR CUENTA
        </label>
    </div>

    <!-- Panel Copa lado derecho -->
    <div class="absolute top-0 left-1/2 w-1/2 h-full z-30 flex flex-col items-center justify-center gap-6 px-8 opacity-0 translate-x-16 transition-all duration-700 ease-in-out peer-checked:opacity-100 peer-checked:translate-x-0">
        <img src="assets/img/copa2026.png" alt="Copa Mundial 2026" class="w-64 drop-shadow-2xl">

        <label for="toggle" class="px-8 py-3 border border-white/70 rounded-full text-white text-sm cursor-pointer hover:bg-white/20 transition">
            INICIAR SESIÓN
        </label>
    </div>

    <!-- Login -->
    <form action="../app/controllers/UsuarioController.php" method="POST"
          class="absolute top-0 left-1/2 w-1/2 h-full flex flex-col justify-center px-12 z-10 transition-all duration-700 ease-in-out peer-checked:-translate-x-full">

        <input type="hidden" name="accion" value="login">

        <h2 class="text-3xl font-semibold text-gray-800 mb-8 text-center">
            Iniciar sesión
        </h2>

        <input type="text" name="usuario" placeholder="Usuario"
               class="mb-4 px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-violet-500">

        <input type="password" name="password" placeholder="Contraseña"
               class="mb-6 px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-violet-500">

        <button class="py-3 rounded-full bg-violet-700 text-white font-medium hover:bg-violet-800 transition">
            Entrar
        </button>
    </form>

    <!-- Registro -->
    <form action="../app/controllers/UsuarioController.php" method="POST"
          class="absolute top-0 left-0 w-1/2 h-full flex flex-col justify-center px-12 z-10 transition-all duration-700 ease-in-out translate-x-full peer-checked:translate-x-0">

        <input type="hidden" name="accion" value="registro">

        <h2 class="text-3xl font-semibold text-gray-800 mb-6 text-center">
            Crear cuenta
        </h2>

        <input type="text" name="nombre" placeholder="Nombre completo"
               class="mb-3 px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-violet-500">

        <input type="text" name="usuario" placeholder="Usuario"
               class="mb-3 px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-violet-500">

        <input type="password" name="password" placeholder="Contraseña"
               class="mb-5 px-4 py-3 rounded-xl bg-gray-100 outline-none focus:ring-2 focus:ring-violet-500">

        <button class="py-3 rounded-full bg-violet-700 text-white font-medium hover:bg-violet-800 transition">
            Registrarme
        </button>
    </form>

</div>

</body>
</html>