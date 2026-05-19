<?php require_once __DIR__ . '/../../helpers/banderas.php'; ?>

<?php
$partidosPendientesResultado = $partidosPendientesResultado ?? [];
$proximosPartidos = $proximosPartidos ?? [];
$esAdmin = $esAdmin ?? false;
$nombre = $nombre ?? 'Usuario';
$puntos = $puntos ?? 0;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>
    <title>Inicio - Quiniela Mundial 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

    <?php include __DIR__ . '/../layouts/navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-6 py-10">

        <?php if ($esAdmin): ?>

            <section class="mb-10">
                <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
                    Panel administrativo
                </p>

                <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
                    Hola,
                    <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
                        <?php echo htmlspecialchars($nombre); ?>
                    </span>
                </h1>


            </section>

            <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

                <div class="admin-pending-card bg-red-500/10 border border-red-500/30 rounded-3xl p-6 shadow-xl shadow-black/20">
                    <p class="text-sm text-red-300 font-bold uppercase tracking-widest">
                        Pendientes de resultado
                    </p>

                    <h2 class="mt-4 text-6xl font-black text-red-400">
                        <?php echo count($partidosPendientesResultado); ?>
                    </h2>



                    <a href="resultados.php"
                        class="admin-pending-link inline-flex mt-5 px-5 py-3 rounded-xl bg-red-500 text-white font-black hover:scale-105 active:scale-95 transition-all">
                        Ir a resultados
                    </a>
                </div>

                <div class="bg-white/[0.03] border border-white/10 rounded-3xl p-6 shadow-xl shadow-black/20">
                    <p class="text-sm text-amber-400 font-bold uppercase tracking-widest">
                        Gestión del calendario
                    </p>

                    <h2 class="mt-4 text-6xl font-black text-white">
                        Admin
                    </h2>



                    <a href="partidos.php"
                        class="inline-flex mt-5 px-5 py-3 rounded-xl
            bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
            text-slate-950 font-black
            hover:scale-105 active:scale-95 transition-all">
                        Administrar partidos
                    </a>
                </div>

            </section>

            <section class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <div class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

                    <div class="px-6 py-5 border-b border-white/10">
                        <h2 class="text-2xl font-black">
                            Partidos sin resultado
                        </h2>


                    </div>

                    <div class="divide-y divide-white/10">

                        <?php if (!empty($partidosPendientesResultado)): ?>

                            <?php foreach ($partidosPendientesResultado as $partido): ?>

                                <div class="px-6 py-5 hover:bg-white/[0.04] transition-colors">

                                    <p class="text-xs text-red-400 font-bold uppercase tracking-widest">
                                        Requiere resultado
                                    </p>

                                    <h3 class="mt-2 text-lg font-black">
                                        <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                                        <span class="mx-2 text-slate-500">vs</span>
                                        <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        <?php echo htmlspecialchars($partido['estadio']); ?>
                                    </p>

                                    <div class="mt-3 flex gap-3 text-sm">
                                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                                            <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                                        </span>

                                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                                            <?php echo date('H:i', strtotime($partido['hora'])); ?>
                                        </span>
                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="px-6 py-10 text-center text-slate-400">
                                No hay partidos pendientes de resultado.
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

                <div class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

                    <div class="px-6 py-5 border-b border-white/10">
                        <h2 class="text-2xl font-black">
                            Próximos partidos
                        </h2>


                    </div>

                    <div class="divide-y divide-white/10">

                        <?php if (!empty($proximosPartidos)): ?>

                            <?php foreach ($proximosPartidos as $partido): ?>

                                <div class="px-6 py-5 hover:bg-white/[0.04] transition-colors">

                                    <p class="text-xs text-amber-400 font-bold uppercase tracking-widest">
                                        <?php echo htmlspecialchars($partido['nombre_fase']); ?>
                                    </p>

                                    <h3 class="mt-2 text-lg font-black">
                                        <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                                        <span class="mx-2 text-slate-500">vs</span>
                                        <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-400">
                                        <?php echo htmlspecialchars($partido['estadio']); ?>
                                    </p>

                                    <div class="mt-3 flex gap-3 text-sm">
                                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                                            <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                                        </span>

                                        <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                                            <?php echo date('H:i', strtotime($partido['hora'])); ?>
                                        </span>
                                    </div>

                                </div>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <div class="px-6 py-10 text-center text-slate-400">
                                No hay próximos partidos registrados.
                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </section>

        <?php else: ?>

            <div class="relative z-10">
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10 items-stretch">

                <div class="lg:col-span-2 bg-white/[0.03] border border-white/10 rounded-3xl p-8 shadow-xl shadow-black/20">
                    <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
                        Panel principal
                    </p>

                    <h1 class="mt-4 text-4xl md:text-5xl font-black tracking-tight leading-tight">
                        Hola,
                        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
                            <?php echo htmlspecialchars($nombre); ?>
                        </span>
                    </h1>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="calendario.php"
                            class="px-5 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                      text-slate-950 font-bold shadow-lg shadow-amber-500/20
                      hover:scale-[1.02] active:scale-95 transition-all">
                            Ver Calendario y Vaticinar
                        </a>
                    </div>
                </div>

                <div class="relative overflow-hidden bg-gradient-to-br from-yellow-600 via-amber-500 to-yellow-500
                    rounded-3xl p-8 shadow-2xl shadow-amber-500/20 text-slate-950">

                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <p class="text-sm font-black opacity-80 !text-black uppercase tracking-widest">
                                Mis puntos
                            </p>

                            <h2 class="mt-5 text-7xl font-black leading-none">
                                <?php echo htmlspecialchars($puntos); ?>
                            </h2>


                        </div>


                    </div>
                </div>

            </section>
            <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

                <div class="px-6 py-5 border-b border-white/10">
                    <h2 class="text-2xl font-black">
                        Próximos partidos
                    </h2>


                </div>

                <div class="divide-y divide-white/10">

                    <?php if (!empty($proximosPartidos)): ?>

                        <?php foreach ($proximosPartidos as $partido): ?>

                            <a href="predicciones.php?partido=<?php echo htmlspecialchars($partido['codigo_partido']); ?>"
                                class="block px-6 py-5 hover:bg-white/[0.04] transition-colors">

                                <p class="text-xs text-amber-400 font-bold uppercase tracking-widest">
                                    <?php echo htmlspecialchars($partido['nombre_fase']); ?>
                                </p>

                                <h3 class="mt-2 text-xl font-black">
                                    <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                                    <span class="mx-2 text-slate-500">vs</span>
                                    <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                                </h3>

                                <p class="mt-1 text-sm text-slate-400">
                                    <?php echo htmlspecialchars($partido['estadio']); ?>
                                </p>

                                <div class="mt-3 flex gap-3 text-sm">
                                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                                        <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                                    </span>

                                    <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                                        <?php echo date('H:i', strtotime($partido['hora'])); ?>
                                    </span>
                                </div>

                            </a>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="px-6 py-10 text-center text-slate-400">
                            No hay próximos partidos disponibles.
                        </div>

                    <?php endif; ?>

                </div>

            </section>


            </div>
        <?php endif; ?>

    </main>
    <?php if (!$esAdmin):
        require_once __DIR__ . '/../layouts/footer.php';
    endif; ?>
</body>

</html>

