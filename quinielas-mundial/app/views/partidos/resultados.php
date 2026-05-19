<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Resultados - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

  <?php require_once __DIR__ . '/../../helpers/banderas.php'; ?>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <main class="max-w-7xl mx-auto px-6 py-10">

    <section class="mb-10">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
        Panel administrativo
      </p>

      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        Gestión de
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
          Resultados
        </span>
      </h1>
    </section>

    <?php if (isset($_GET['success'])): ?>
      <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-400 font-bold">
        Resultado actualizado correctamente.
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <?php
        $mensajesError = [
          'partido' => 'No se encontró el partido seleccionado.',
          'goles' => 'Los goles oficiales deben ser números enteros mayores o iguales a cero.',
          'empate' => 'Los empates oficiales solo están permitidos en Fase de Grupos.',
          'bd' => 'No se pudo actualizar el resultado. Revisa los datos e intenta de nuevo.',
          'tiempo'   => 'No se puede ingresar el resultado antes de la fecha y hora programada del partido.'
        ];
        $mensajeError = $mensajesError[$_GET['error']] ?? 'No se pudo actualizar el resultado. Revisa los datos.';
      ?>
      <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-500/10 px-5 py-4 text-red-400 font-bold">
        <?php echo htmlspecialchars($mensajeError); ?>
      </div>
    <?php endif; ?>

    <?php
      $accionFiltro = 'resultados.php';
      $tituloFiltro = 'Ver fase';
      require __DIR__ . '/../components/filtro_fases.php';
    ?>

    <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

      <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black">Partidos registrados</h2>
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-xl">
          <span class="text-sm font-bold text-amber-400">
            <?php echo count($partidos ?? []); ?> partidos
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 p-6">

        <?php if (!empty($partidos)): ?>

          <?php foreach ($partidos as $partido): ?>

            <?php
              $hayResultado =
                $partido['goles_local_oficial'] !== null &&
                $partido['goles_visitante_oficial'] !== null;
            ?>

            <article class="bg-black/30 border border-white/10 rounded-3xl p-5 hover:bg-white/[0.04] transition-all">

              <div class="flex flex-col items-start justify-between gap-4 sm:flex-row">
                <div class="min-w-0">
                  <p class="text-xs text-amber-400 font-bold uppercase tracking-widest">
                    <?php echo htmlspecialchars($partido['nombre_fase']); ?>
                  </p>

                  <h3 class="mt-2 text-xl font-black text-white break-words">
                    <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                    <span class="mx-2 text-slate-500">vs</span>
                    <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                  </h3>

                  <p class="mt-1 text-xs text-slate-500">
                    Código: <?php echo htmlspecialchars($partido['codigo_partido']); ?>
                  </p>
                </div>

                <?php if ($hayResultado): ?>
                  <span class="shrink-0 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold">
                    Con resultado
                  </span>
                <?php else: ?>
                  <span class="shrink-0 px-3 py-1 rounded-full bg-yellow-500/10 border border-yellow-500/30 text-yellow-400 text-xs font-bold">
                    Pendiente
                  </span>
                <?php endif; ?>
              </div>

              <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="bg-white/[0.03] border border-white/10 rounded-2xl px-4 py-3">
                  <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Fecha / Hora</p>
                  <p class="mt-1 font-bold text-white">
                    <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                    <span class="text-slate-500 mx-1">·</span>
                    <?php echo date('H:i', strtotime($partido['hora'])); ?>
                  </p>
                </div>

                <div class="bg-white/[0.03] border border-white/10 rounded-2xl px-4 py-3">
                  <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Estadio</p>
                  <p class="mt-1 font-bold text-white truncate">
                    <?php echo htmlspecialchars($partido['estadio']); ?>
                  </p>
                </div>
              </div>

              <form action="resultados.php" method="POST" class="mt-5">
                <input type="hidden" name="codigo_partido" value="<?php echo htmlspecialchars($partido['codigo_partido']); ?>">
                <input type="hidden" name="accion" value="guardar">
                <input type="hidden" name="fase_actual" value="<?php echo htmlspecialchars($faseSeleccionada ?? ''); ?>">

                <div class="grid grid-cols-1 md:grid-cols-[1fr_auto_1fr] gap-3 items-center">

                  <div class="bg-slate-950 border border-white/10 rounded-2xl p-3">
                    <label class="block text-xs font-bold text-slate-400 mb-2 truncate">
                      <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local'], 'h-5 w-7'); ?>
                    </label>

                    <input
                      type="number"
                      name="goles_local"
                      min="0"
                      required
                      value="<?php echo htmlspecialchars($partido['goles_local_oficial'] ?? ''); ?>"
                      class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-3 text-center text-white text-xl font-black focus:outline-none focus:border-amber-400">
                  </div>

                  <span class="hidden md:block text-slate-500 font-black text-xl">
                    -
                  </span>

                  <div class="bg-slate-950 border border-white/10 rounded-2xl p-3">
                    <label class="block text-xs font-bold text-slate-400 mb-2 truncate">
                      <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante'], 'h-5 w-7'); ?>
                    </label>

                    <input
                      type="number"
                      name="goles_visitante"
                      min="0"
                      required
                      value="<?php echo htmlspecialchars($partido['goles_visitante_oficial'] ?? ''); ?>"
                      class="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-3 text-center text-white text-xl font-black focus:outline-none focus:border-amber-400">
                  </div>

                </div>

                <div class="mt-5 flex flex-col sm:flex-row gap-3">
                  <button type="submit"
                          class="flex-1 px-5 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                                 text-slate-950 font-black shadow-lg shadow-amber-500/20
                                 hover:scale-[1.01] active:scale-95 transition-all">
                    Guardar resultado
                  </button>
                </div>
              </form>

              <?php if ($hayResultado): ?>
                <form action="resultados.php" method="POST" class="mt-3">
                  <input type="hidden" name="codigo_partido" value="<?php echo htmlspecialchars($partido['codigo_partido']); ?>">
                  <input type="hidden" name="accion" value="quitar">
                  <input type="hidden" name="fase_actual" value="<?php echo htmlspecialchars($faseSeleccionada ?? ''); ?>">

                  <button type="submit"
                          onclick="return confirm('¿Seguro que querés quitar este resultado y reiniciar los puntos?')"
                          class="w-full px-5 py-3 rounded-xl bg-red-500/10 border border-red-500/30
                                 text-red-400 font-bold hover:bg-red-500 hover:text-white transition-all">
                    Quitar resultado
                  </button>
                </form>
              <?php endif; ?>

            </article>

          <?php endforeach; ?>

        <?php else: ?>

          <div class="xl:col-span-2 p-10 text-center text-slate-400">
            No hay partidos registrados.
          </div>

        <?php endif; ?>

      </div>

    </section>

  </main>

</body>
</html>
