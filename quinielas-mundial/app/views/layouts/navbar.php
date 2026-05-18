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
$busquedaNavbar = trim($_GET['q'] ?? '');
$accionBusquedaNavbar = $esAdmin ? 'partidos.php' : 'calendario.php';
$publicBaseUrl = $publicBaseUrl ?? rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

if ($publicBaseUrl === '/' || $publicBaseUrl === '.') {
    $publicBaseUrl = '';
}

$assetBaseUrl = $assetBaseUrl ?? ($publicBaseUrl . '/assets');
$paginaActualNavbar = basename(parse_url($_SERVER['SCRIPT_NAME'] ?? 'index.php', PHP_URL_PATH) ?: 'index.php');
$navDesktopClass = 'px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5';
$navDesktopAdminClass = 'px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10';
$navSidebarClass = 'px-4 py-3 rounded-xl transition-all hover:bg-white/5 hover:text-amber-400 text-slate-200';
$navSidebarAdminClass = 'px-4 py-3 rounded-xl transition-all bg-amber-500/5 text-amber-300 hover:bg-amber-500/10 hover:text-amber-400';
$navClass = function (string $pagina, string $baseClass) use ($paginaActualNavbar): string {
    return $baseClass . ($paginaActualNavbar === $pagina ? ' nav-link-active' : '');
};

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

<style>
    html,
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .main-navbar {
        left: 0;
        right: 0;
        top: 0;
        height: 4rem;
        margin-top: 0;
    }

    main {
        width: 100%;
    }

    .scroll-x-soft {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(166, 124, 50, 0.65) rgba(224, 217, 200, 0.65);
    }

    @media (max-width: 1023px) {
        .main-navbar {
            height: 4.5rem;
        }

        .main-navbar > div > div {
            height: 4.5rem;
        }

        .main-navbar + .h-16 {
            height: 4.5rem;
        }
    }

    @media (max-width: 640px) {
        main {
            padding: 1.5rem 1rem !important;
        }

        main > section:first-child {
            margin-bottom: 1.5rem !important;
        }

        main h1 {
            font-size: clamp(2rem, 10vw, 2.75rem) !important;
            line-height: 1.05 !important;
            overflow-wrap: anywhere;
        }

        main h3 {
            line-height: 1.25 !important;
            overflow-wrap: anywhere;
        }

        main p[class*="tracking"] {
            letter-spacing: 0 !important;
        }

        main section[class*="rounded-3xl"],
        main article[class*="rounded-3xl"],
        main div[class*="rounded-3xl"],
        main form[class*="rounded-3xl"] {
            border-radius: 4px !important;
        }

        main input,
        main select,
        main button,
        main a[class*="rounded"] {
            min-height: 44px;
        }

        main button[type="submit"],
        main a[class*="bg-gradient"],
        main a[class*="bg-red"] {
            width: 100%;
            justify-content: center;
            text-align: center;
        }

        main table {
            min-width: 680px;
        }

        main .overflow-x-auto {
            margin-left: -1rem;
            margin-right: -1rem;
            padding-left: 1rem;
            padding-right: 1rem;
            -webkit-overflow-scrolling: touch;
        }

        main .items-center.justify-between {
            align-items: flex-start;
            gap: 0.75rem;
        }

        #sidebar-menu {
            width: min(22rem, calc(100vw - 1rem));
        }
    }

    @media (min-width: 641px) and (max-width: 1023px) {
        main {
            padding: 2rem 1.5rem !important;
        }

        main h1 {
            font-size: 3rem !important;
            line-height: 1.05 !important;
        }

        main table {
            min-width: 760px;
        }
    }
</style>

<!-- NAVBAR PRINCIPAL -->
<nav class="main-navbar fixed w-full bg-black/95 backdrop-blur-md text-white shadow-lg z-50 border-b border-amber-500/10">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- LOGO (Actúa como disparador del menú en móviles) -->
            <div class="flex items-center gap-3">
                <!-- Se agregó id="btn-menu" y cursor-pointer -->
                <button id="btn-menu"
                    class="p-1 rounded-2xl flex items-center justify-center h-11 w-11
                        shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95
                        transition-all duration-200 focus:outline-none cursor-pointer">
                    <img src="<?php echo htmlspecialchars($assetBaseUrl . '/img/copa26.jpeg?v=2'); ?>" alt="Logo Quiniela-Copa" class="h-full w-full object-contain">
                </button>

                <!-- Texto al lado del logo -->
                <div class="hidden sm:block">
                    <a href="index.php"
                        class="font-extrabold text-lg tracking-tight whitespace-nowrap
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
            <div class="hidden lg:flex items-center justify-center flex-1 space-x-1 font-medium text-sm text-white whitespace-nowrap">
                <a href="index.php" class="<?php echo htmlspecialchars($navClass('index.php', $navDesktopClass)); ?>">Inicio</a>

                <?php if ($esAdmin): ?>
                    <a href="equipos.php" class="<?php echo htmlspecialchars($navClass('equipos.php', $navDesktopAdminClass)); ?>">Equipos</a>
                    <a href="partidos.php" class="<?php echo htmlspecialchars($navClass('partidos.php', $navDesktopAdminClass)); ?>">Partidos</a>
                    <a href="resultados.php" class="<?php echo htmlspecialchars($navClass('resultados.php', $navDesktopAdminClass)); ?>">Resultados</a>
                    <a href="llaves.php" class="<?php echo htmlspecialchars($navClass('llaves.php', $navDesktopAdminClass)); ?>">Llaves</a>
                <?php else: ?>
                    <a href="calendario.php" class="<?php echo htmlspecialchars($navClass('calendario.php', $navDesktopClass)); ?>">Calendario</a>
                    <a href="predicciones.php" class="<?php echo htmlspecialchars($navClass('predicciones.php', $navDesktopClass)); ?>">Mis Predicciones</a>
                    <a href="llaves.php" class="<?php echo htmlspecialchars($navClass('llaves.php', $navDesktopClass)); ?>">Llaves</a>
                    <a href="posiciones.php" class="<?php echo htmlspecialchars($navClass('posiciones.php', $navDesktopClass)); ?>">Posiciones</a>
                    <a href="ranking.php" class="<?php echo htmlspecialchars($navClass('ranking.php', $navDesktopClass)); ?>">Ranking</a>
                <?php endif; ?>
            </div>

            <!-- SECCIÓN USUARIO -->
            <div class="flex items-center gap-3">
                <form action="<?php echo htmlspecialchars($accionBusquedaNavbar); ?>" method="GET"
                    class="hidden xl:flex items-center gap-2 w-64 2xl:w-80 h-11 rounded-2xl border border-white/10 bg-white/[0.04] px-3 focus-within:border-amber-400/70">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                        <circle cx="11" cy="11" r="7" />
                    </svg>

                    <input type="search" name="q"
                        value="<?php echo htmlspecialchars($busquedaNavbar); ?>"
                        placeholder="Buscar equipo o fecha"
                        class="min-w-0 flex-1 bg-transparent text-sm font-semibold text-white placeholder:text-slate-500 outline-none">
                </form>

                <?php if (!$esAdmin): ?>
                    <div class="hidden sm:flex h-11 items-center gap-2 bg-white/[0.04] border border-white/10 px-4 rounded-2xl whitespace-nowrap">
                        <div class="flex items-center leading-none">
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
                        class="js-logout-confirm h-11 w-11 rounded-2xl bg-amber-500/10 border border-amber-500/30
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
<div class="h-16"></div>

<div class="user-bounce-bg" aria-hidden="true">
    <div class="user-bounce-ball">
        <img
            src="https://upload.wikimedia.org/wikipedia/commons/d/d3/Soccerball.svg"
            alt=""
            class="user-bounce-ball-svg">
    </div>
</div>

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

        <!-- Buscador móvil/lateral -->
        <form action="<?php echo htmlspecialchars($accionBusquedaNavbar); ?>" method="GET"
            class="mx-4 mt-4 flex h-11 items-center gap-2 rounded-2xl border border-white/10 bg-white/[0.04] px-3 focus-within:border-amber-400/70">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                <circle cx="11" cy="11" r="7" />
            </svg>

            <input type="search" name="q"
                value="<?php echo htmlspecialchars($busquedaNavbar); ?>"
                placeholder="Buscar partido"
                class="min-w-0 flex-1 bg-transparent text-sm font-semibold text-white placeholder:text-slate-500 outline-none">

            <button type="submit"
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-500/10 text-amber-400 transition hover:bg-amber-500/20 active:scale-95"
                title="Buscar">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35" />
                    <circle cx="11" cy="11" r="7" />
                </svg>
            </button>
        </form>

        <!-- Links de navegación móvil -->
        <div class="p-4 flex flex-col space-y-1 font-medium text-sm">
            <a href="index.php" class="<?php echo htmlspecialchars($navClass('index.php', $navSidebarClass . ' flex items-center gap-3')); ?>">
                <span>Inicio</span>
            </a>

            <?php if ($esAdmin): ?>
                <div class="pt-2 pb-1 px-4 text-[11px] font-bold text-amber-500 uppercase tracking-wider">Administración</div>
                <a href="equipos.php" class="<?php echo htmlspecialchars($navClass('equipos.php', $navSidebarAdminClass)); ?>">Equipos</a>
                <a href="partidos.php" class="<?php echo htmlspecialchars($navClass('partidos.php', $navSidebarAdminClass)); ?>">Partidos</a>
                <a href="resultados.php" class="<?php echo htmlspecialchars($navClass('resultados.php', $navSidebarAdminClass)); ?>">Resultados</a>
                <a href="llaves.php" class="<?php echo htmlspecialchars($navClass('llaves.php', $navSidebarAdminClass)); ?>">Llaves</a>
            <?php else: ?>
                <a href="calendario.php" class="<?php echo htmlspecialchars($navClass('calendario.php', $navSidebarClass)); ?>">Calendario</a>
                <a href="predicciones.php" class="<?php echo htmlspecialchars($navClass('predicciones.php', $navSidebarClass)); ?>">Mis Predicciones</a>
                <a href="llaves.php" class="<?php echo htmlspecialchars($navClass('llaves.php', $navSidebarClass)); ?>">Llaves</a>
                <a href="posiciones.php" class="<?php echo htmlspecialchars($navClass('posiciones.php', $navSidebarClass)); ?>">Posiciones</a>
                <a href="ranking.php" class="<?php echo htmlspecialchars($navClass('ranking.php', $navSidebarClass)); ?>">Ranking</a>
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
            <a href="logout.php" class="js-logout-confirm text-xs font-bold text-red-400 hover:text-red-300 underline">Salir</a>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAR CIERRE DE SESIÓN -->
<div id="logout-modal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4">
    <div class="w-full max-w-sm rounded-2xl border border-amber-500/25 bg-slate-950 p-6 text-white shadow-2xl shadow-black/40">
        <div class="mb-5 flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-500/10 text-amber-400 border border-amber-500/30">
                <?php echo strtoupper(substr($nombre, 0, 1)); ?>
            </div>
            <div>
                <h2 class="text-lg font-black text-slate-100">¿Cerrar sesión?</h2>
                <p class="text-sm text-slate-400">@<?php echo htmlspecialchars($username); ?></p>
            </div>
        </div>

        <p class="text-sm leading-6 text-slate-300">
            ¿Seguro que quieres salir de tu cuenta?
        </p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" id="logout-cancel"
                class="rounded-xl border border-white/10 px-4 py-2 text-sm font-bold text-slate-200 transition hover:bg-white/10 active:scale-95">
                No, quedarme
            </button>
            <button type="button" id="logout-confirm"
                class="rounded-xl bg-red-500 px-4 py-2 text-sm font-bold text-white shadow-lg shadow-red-500/20 transition hover:bg-red-400 active:scale-95">
                Sí, salir
            </button>
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
        const logoutLinks = document.querySelectorAll('.js-logout-confirm');
        const logoutModal = document.getElementById('logout-modal');
        const logoutConfirm = document.getElementById('logout-confirm');
        const logoutCancel = document.getElementById('logout-cancel');
        let logoutUrl = 'logout.php';

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

        function openLogoutModal(url) {
            logoutUrl = url;
            logoutModal.classList.remove('hidden');
            logoutModal.classList.add('flex');
        }

        function closeLogoutModal() {
            logoutModal.classList.add('hidden');
            logoutModal.classList.remove('flex');
        }

        logoutLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                openLogoutModal(link.getAttribute('href'));
            });
        });

        logoutConfirm.addEventListener('click', () => {
            window.location.href = logoutUrl;
        });

        logoutCancel.addEventListener('click', closeLogoutModal);

        logoutModal.addEventListener('click', (event) => {
            if (event.target === logoutModal) {
                closeLogoutModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !logoutModal.classList.contains('hidden')) {
                closeLogoutModal();
            }
        });
    });
</script>
