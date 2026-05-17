<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../models/Participante.php';

$usuarioSesion = $_SESSION['usuario'] ?? null;

$username = '';
$nombre = 'Usuario';
$puntos = 0;
$esAdmin = esAdmin();

if (is_array($usuarioSesion)) {
    $username = $usuarioSesion['username'] ?? '';
    $nombre = $usuarioSesion['nombre'] ?? $username;
} else {
    $username = $usuarioSesion ?? '';
    $nombre = $username;
}

if (!empty($username)) {
    try {
        $participanteModel = new Participante();
        $datosUsuario = $participanteModel->obtenerResumen($username);

        if ($datosUsuario) {
            $nombre = $datosUsuario['nombre'];
            $puntos = $datosUsuario['puntos'];
        }
    } catch (PDOException $e) {
        $nombre = 'Usuario';
        $puntos = 0;
    }
}
?>

<!-- NAVBAR PRINCIPAL -->
<nav class="bg-black/95 backdrop-blur-md text-white shadow-lg sticky top-0 z-50 border-b border-amber-500/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- LOGO (Actúa como disparador del menú en móviles) -->
            <div class="flex items-center gap-3">
                <!-- Se agregó id="btn-menu" y cursor-pointer -->
                <button id="btn-menu"
                    class="p-1 rounded-2xl flex items-center justify-center h-11 w-11
                        shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95
                        transition-all duration-200 focus:outline-none cursor-pointer">
                    <img src="assets/img/copa26.jpeg" alt="Logo Quiniela-Copa" class="h-full w-full object-contain">
                </button>

                <!-- Texto al lado del logo -->
                <div class="hidden sm:block">
                    <a href="index.php"
                        class="font-extrabold text-lg tracking-tight
                            bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500
                            bg-clip-text text-transparent">
                        Quiniela Mundial 2026
                    </a>
                    <p class="text-[11px] text-slate-500 tracking-widest font-bold uppercase">
                        FIFA World Cup
                    </p>
                </div>
            </div>

            <!-- ENRUTADORES ESCRITORIO (Ocultos en móviles: hidden lg:flex) -->
            <div class="hidden lg:flex items-center space-x-1 font-medium text-sm text-white">
                <a href="index.php" class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">Inicio</a>

                <?php if ($esAdmin): ?>
                    <a href="equipos.php" class="px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10">Equipos</a>
                    <a href="partidos.php" class="px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10">Partidos</a>
                    <a href="resultados.php" class="px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10">Resultados</a>
                <?php else: ?>
                    <a href="calendario.php" class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">Calendario</a>
                    <a href="predicciones.php" class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">Mis Predicciones</a>
                    <a href="posiciones.php" class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">Posiciones</a>
                    <a href="ranking.php" class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">Ranking</a>
                <?php endif; ?>
            </div>

            <!-- SECCIÓN USUARIO -->
            <div class="flex items-center gap-3">
                <?php if (!$esAdmin): ?>
                    <div class="hidden sm:flex items-center gap-2 bg-white/[0.04] border border-white/10 px-4 py-2 rounded-2xl">
                        <div class="flex flex-col leading-none">
                            <span class="text-sm font-black text-amber-400">
                                <?php echo htmlspecialchars($puntos); ?> pts
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="relative flex items-center gap-3 pl-3 border-l border-white/10">
                    <div class="hidden sm:block text-right">
                        <p class="text-xs font-bold text-slate-200">@<?php echo htmlspecialchars($username); ?></p>
                        <p class="text-[11px] text-slate-500">
                            <?php echo htmlspecialchars($nombre); ?>
                            <?php if ($esAdmin): ?><span class="text-amber-400 font-bold"> · Admin</span><?php endif; ?>
                        </p>
                    </div>

                    <a href="logout.php" title="Cerrar sesión"
                        class="h-11 w-11 rounded-2xl bg-amber-500/10 border border-amber-500/30
                        flex items-center justify-center font-black text-amber-400
                        hover:bg-gradient-to-br hover:from-yellow-600 hover:via-amber-400 hover:to-yellow-500
                        hover:text-slate-950 hover:scale-105 active:scale-95
                        transition-all duration-200 shadow-lg shadow-amber-500/10">
                        <?php echo strtoupper(substr($nombre, 0, 1)); ?>
                    </a>
                </div>
            </div>

        </div>
    </div>
</nav>

<!-- ========================================== -->
<!-- MENÚ LATERAL MÓVIL (SIDEBAR) & OVERLAY     -->
<!-- ========================================== -->

<!-- Fondo oscuro (Overlay) -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300"></div>

<!-- Contenedor del Menú Lateral Izquierdo -->
<div id="sidebar-menu" class="fixed top-0 left-0 bottom-0 w-72 bg-slate-950 border-r border-amber-500/20 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col justify-between">
    
    <div>
        <!-- Encabezado del Menú Lateral -->
        <div class="p-5 flex items-center justify-between border-b border-white/5">
            <div class="flex flex-col">
                <span class="font-extrabold text-md bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">Quiniela Mundial 2026</span>
                <span class="text-[10px] text-slate-500 tracking-widest font-bold uppercase">Navegación</span>
            </div>
            <!-- Botón para cerrar menú -->
            <button id="btn-close-menu" class="text-slate-400 hover:text-white p-1 rounded-xl hover:bg-white/5 transition-all">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Links de navegación móvil -->
        <div class="p-4 flex flex-col space-y-1 font-medium text-sm">
            <a href="index.php" class="px-4 py-3 rounded-xl transition-all hover:bg-amber-500/10 hover:text-amber-400 flex items-center gap-3 text-slate-200">
                <span>Inicio</span>
            </a>

            <?php if ($esAdmin): ?>
                <div class="pt-2 pb-1 px-4 text-[11px] font-bold text-amber-500 uppercase tracking-wider">Administración</div>
                <a href="equipos.php" class="px-4 py-3 rounded-xl transition-all bg-amber-500/5 text-amber-300 hover:bg-amber-500/10 hover:text-amber-400">Equipos</a>
                <a href="partidos.php" class="px-4 py-3 rounded-xl transition-all bg-amber-500/5 text-amber-300 hover:bg-amber-500/10 hover:text-amber-400">Partidos</a>
                <a href="resultados.php" class="px-4 py-3 rounded-xl transition-all bg-amber-500/5 text-amber-300 hover:bg-amber-500/10 hover:text-amber-400">Resultados</a>
            <?php else: ?>
                <a href="calendario.php" class="px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-amber-400 text-slate-200">Calendario</a>
                <a href="predicciones.php" class="px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-amber-400 text-slate-200">Mis Predicciones</a>
                <a href="posiciones.php" class="px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-amber-400 text-slate-200">Posiciones</a>
                <a href="ranking.php" class="px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-amber-400 text-slate-200">Ranking</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Info del usuario en la parte inferior del menú lateral (Vista Móvil) -->
    <div class="p-4 border-t border-white/5 bg-black/40 sm:hidden">
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-xs font-bold text-slate-200">@<?php echo htmlspecialchars($username); ?></span>
                <span class="text-[11px] text-slate-500"><?php echo htmlspecialchars($nombre); ?></span>
                <?php if (!$esAdmin): ?>
                    <span class="text-xs font-black text-amber-400 mt-1"><?php echo htmlspecialchars($puntos); ?> pts</span>
                <?php endif; ?>
            </div>
            <a href="logout.php" class="text-xs font-bold text-red-400 hover:text-red-300 underline">Salir</a>
        </div>
    </div>
</div>

<!-- LÓGICA DE JAVASCRIPT PARA PASAR EL MENÚ -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnMenu = document.getElementById('btn-menu');
        const btnCloseMenu = document.getElementById('btn-close-menu');
        const sidebarMenu = document.getElementById('sidebar-menu');
        const sidebarOverlay = document.getElementById('sidebar-overlay');

        function openMenu() {
            // Solo actuar si estamos en pantallas móviles (opcional, pero buena práctica)
            sidebarOverlay.classList.remove('hidden');
            // Timeout pequeño para permitir la animación CSS de opacidad
            setTimeout(() => {
                sidebarOverlay.classList.remove('opacity-0');
                sidebarMenu.classList.remove('-translate-x-full');
                sidebarMenu.classList.add('translate-x-0');
            }, 10);
        }

        function closeMenu() {
            sidebarMenu.classList.remove('translate-x-0');
            sidebarMenu.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('opacity-0');
            
            // Esperar a que termine la animación antes de ocultar por completo
            setTimeout(() => {
                sidebarOverlay.classList.add('hidden');
            }, 300);
        }

        // Eventos
        btnMenu.addEventListener('click', openMenu);
        btnCloseMenu.addEventListener('click', closeMenu);
        sidebarOverlay.addEventListener('click', closeMenu);
    });
</script>