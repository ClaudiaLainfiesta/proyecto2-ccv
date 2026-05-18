<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Mis Predicciones - Quiniela Mundial 2026</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

  <?php require_once __DIR__ . '/../../helpers/banderas.php'; ?>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <main class="max-w-7xl mx-auto px-6 py-10">

    <section class="mb-10">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
        <?php echo !empty($modoPartido) ? 'Vaticinar partido' : 'Mis predicciones'; ?>
      </p>

      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        <?php echo !empty($modoPartido) ? 'Ingresar' : 'Mis'; ?>
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
          Predicciones
        </span>
      </h1>

    </section>

    <?php if (isset($_GET['success'])): ?>
      <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-400 font-bold">
        Predicción guardada correctamente.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <?php
        $mensajesError = [
          'datos' => 'No se pudo guardar la predicción. Revisá los datos del partido.',
          'goles' => 'Los goles deben ser números mayores o iguales a cero.',
          'empate' => 'Los empates solo están permitidos en Fase de Grupos.',
          'tiempo' => 'El tiempo para vaticinar este partido ya terminó.'
        ];
        $mensajeError = $mensajesError[$_GET['error']] ?? 'No se pudo guardar la predicción. Revisá los datos o el tiempo del partido.';
      ?>
      <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-500/10 px-5 py-4 text-red-400 font-bold">
        <?php echo htmlspecialchars($mensajeError); ?>
      </div>
    <?php endif; ?>

    <?php if (empty($modoPartido)): ?>
      <?php
        $accionFiltro = 'predicciones.php';
        $tituloFiltro = 'Ver fase';
        require __DIR__ . '/../components/filtro_fases.php';
      ?>
    <?php endif; ?>

    <section class="grid grid-cols-1 gap-5">

      <?php if (!empty($partidos)): ?>

        <?php foreach ($partidos as $partido): ?>

          <?php
            $puedeVaticinar = !empty($partido['puede_vaticinar']);
            $tienePrediccion = $partido['goles_local_prediccion'] !== null && $partido['goles_visitante_prediccion'] !== null;
            $hayResultado = $partido['goles_local_oficial'] !== null && $partido['goles_visitante_oficial'] !== null;
          ?>

          <article class="bg-white/[0.03] border border-white/10 rounded-3xl p-6 shadow-xl shadow-black/20 hover:bg-white/[0.05] transition-all">

            <form action="predicciones.php" method="POST" class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-6 items-center">

              <input type="hidden" name="codigo_partido" value="<?php echo htmlspecialchars($partido['codigo_partido']); ?>">
              <input type="hidden" name="fase_actual" value="<?php echo htmlspecialchars($faseSeleccionada ?? ''); ?>">
              <input type="hidden" name="modo_partido" value="<?php echo !empty($modoPartido) ? '1' : '0'; ?>">

              <div>
                <p class="text-xs text-amber-400 font-bold uppercase tracking-widest">
                  <?php echo htmlspecialchars($partido['nombre_fase']); ?>
                </p>

                <h2 class="mt-3 text-2xl font-black text-white">
                  <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                  <span class="mx-3 text-slate-500 text-lg">vs</span>
                  <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                </h2>

                <p class="mt-2 text-sm text-slate-400">
                  <?php echo htmlspecialchars($partido['estadio']); ?>
                </p>

                <div class="mt-4 flex flex-wrap gap-3 text-sm">
                  <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                    <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                  </span>

                  <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300">
                    <?php echo date('H:i', strtotime($partido['hora'])); ?>
                  </span>

                  <?php if ($hayResultado): ?>
                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-bold">
                      Resultado: <?php echo htmlspecialchars($partido['goles_local_oficial']); ?> - <?php echo htmlspecialchars($partido['goles_visitante_oficial']); ?>
                    </span>
                  <?php elseif (!$puedeVaticinar && $tienePrediccion): ?>
                    <span class="px-3 py-1 rounded-full bg-slate-800 border border-white/10 text-slate-400 font-bold">
                      Vaticinio cerrado
                    </span>
                  <?php endif; ?>
                </div>
              </div>

              <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">

                <div class="flex items-center gap-3 bg-black/30 border border-white/10 rounded-2xl px-4 py-3">
                  <span class="text-sm font-bold text-slate-300">
                    <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local'], 'h-5 w-7'); ?>
                  </span>

                  <input
                    type="number"
                    name="goles_local"
                    min="0"
                    required
                    <?php echo $puedeVaticinar ? '' : 'readonly'; ?>
                    value="<?php echo htmlspecialchars($partido['goles_local_prediccion'] ?? ''); ?>"
                    class="w-16 bg-slate-950 border border-white/10 rounded-xl px-3 py-2 text-center text-white font-black focus:outline-none focus:border-amber-400">
                </div>

                <span class="hidden sm:block text-slate-500 font-black">
                  -
                </span>

                <div class="flex items-center gap-3 bg-black/30 border border-white/10 rounded-2xl px-4 py-3">
                  <input
                    type="number"
                    name="goles_visitante"
                    min="0"
                    required
                    <?php echo $puedeVaticinar ? '' : 'readonly'; ?>
                    value="<?php echo htmlspecialchars($partido['goles_visitante_prediccion'] ?? ''); ?>"
                    class="w-16 bg-slate-950 border border-white/10 rounded-xl px-3 py-2 text-center text-white font-black focus:outline-none focus:border-amber-400">

                  <span class="text-sm font-bold text-slate-300">
                    <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante'], 'h-5 w-7'); ?>
                  </span>
                </div>

                <?php if ($puedeVaticinar): ?>
                  <button type="submit"
                    class="px-5 py-3 rounded-2xl
                                 bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                                 text-slate-950 font-black
                                 shadow-lg shadow-amber-500/20
                                 hover:scale-105 active:scale-95
                                 transition-all">
                    Guardar
                  </button>
                <?php else: ?>
                  <span class="px-5 py-3 rounded-2xl bg-slate-800 border border-white/10 text-slate-400 font-black text-center">
                    <?php echo $hayResultado ? htmlspecialchars($partido['puntos_prediccion'] ?? 0) . ' pts' : 'Cerrado'; ?>
                  </span>
                <?php endif; ?>

              </div>

            </form>

          </article>

        <?php endforeach; ?>

      <?php else: ?>

        <div class="bg-white/[0.03] border border-white/10 rounded-3xl p-10 text-center">
          <p class="text-slate-400">
            <?php if (!empty($modoPartido)): ?>
              No se encontró el partido solicitado.
            <?php else: ?>
              Todavía no has guardado predicciones.
            <?php endif; ?>
          </p>

          <?php if (empty($modoPartido)): ?>
            <a href="calendario.php"
               class="inline-flex mt-5 px-5 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500 text-slate-950 font-black shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all">
              Ir al calendario
            </a>
          <?php endif; ?>
        </div>

      <?php endif; ?>

    </section>

  </main>

  <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

</body>

</html>

