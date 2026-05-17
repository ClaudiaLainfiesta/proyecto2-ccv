<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../helpers/auth.php';

$db = new Database();
$pdo = $db->conectar();

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
        $sql = "
            SELECT 
                u.username,
                u.nombre,
                COALESCE(SUM(p.puntos_prediccion), 0) AS puntos
            FROM Usuario u
            LEFT JOIN Prediccion p 
                ON u.username = p.username
            WHERE u.username = :username
            GROUP BY u.username, u.nombre
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        $datosUsuario = $stmt->fetch();

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

<nav class="bg-black/95 backdrop-blur-md text-white shadow-lg sticky top-0 z-50 border-b border-amber-500/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <!-- LOGO -->
            <div class="flex items-center gap-3">
                <a href="index.php"
                    class="bg-gradient-to-br from-yellow-600 via-amber-400 to-yellow-500
                  p-2 rounded-2xl text-slate-950 font-black tracking-wider text-xl
                  flex items-center justify-center h-11 w-11
                  shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95
                  transition-all duration-200">
                    Q
                </a>

                <div class="hidden sm:block">
                    <a href="index.php"
                        class="font-extrabold text-lg tracking-tight
                    bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500
                    bg-clip-text text-transparent">
                        Quiniela Mundial 2026
                    </a>

                    <p class="text-[11px] text-slate-500 tracking-widest uppercase">
                        FIFA World Cup
                    </p>
                </div>
            </div>

            <div class="hidden lg:flex items-center space-x-1 font-medium text-sm text-white">

                <a href="index.php"
                    class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">
                    Inicio
                </a>

                <?php if ($esAdmin): ?>

                    <a href="equipos.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10">
                        Equipos
                    </a>

                    <a href="partidos.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10">
                        Partidos
                    </a>

                    <a href="resultados.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 text-amber-300 hover:text-amber-400 hover:bg-amber-500/10">
                        Resultados
                    </a>

                <?php else: ?>

                    <a href="calendario.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">
                        Calendario
                    </a>

                    <a href="predicciones.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">
                        Mis Predicciones
                    </a>

                    <a href="posiciones.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">
                        Posiciones
                    </a>

                    <a href="ranking.php"
                        class="px-4 py-2 rounded-xl transition-all duration-200 hover:text-amber-400 hover:bg-white/5">
                        Ranking
                    </a>

                <?php endif; ?>

            </div>

            <div class="flex items-center gap-3">

                <?php if (!$esAdmin): ?>

                    <div class="hidden sm:flex items-center gap-2 bg-white/[0.04] border border-white/10 px-4 py-2 rounded-2xl">

                        <div class="h-2 w-2 rounded-full bg-amber-400 shadow shadow-amber-400"></div>

                        <div class="flex flex-col leading-none">

                            <span class="text-[10px] uppercase tracking-widest text-slate-500 font-bold">
                                Mis puntos
                            </span>

                            <span class="text-sm font-black text-amber-400 mt-1">
                                <?php echo htmlspecialchars($puntos); ?> pts
                            </span>

                        </div>

                    </div>

                <?php endif; ?>
                <div class="relative flex items-center gap-3 pl-3 border-l border-white/10">

                    <div class="hidden sm:block text-right">
                        <p class="text-xs font-bold text-slate-200">
                            @<?php echo htmlspecialchars($username); ?>
                        </p>

                        <p class="text-[11px] text-slate-500">
                            <?php echo htmlspecialchars($nombre); ?>
                            <?php if ($esAdmin): ?>
                                <span class="text-amber-400 font-bold"> · Admin</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <a href="logout.php"
                        title="Cerrar sesión"
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