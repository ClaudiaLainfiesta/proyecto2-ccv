<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Calendario - Quiniela Mundial 2026</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

  <?php require_once __DIR__ . '/../../helpers/banderas.php'; ?>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <main class="max-w-7xl mx-auto px-6 py-10">

    <section class="mb-10">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
        Calendario oficial
      </p>

      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        Partidos del
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
          Mundial 2026
        </span>
      </h1>

    
    </section>

    <?php
      $accionFiltro = 'calendario.php';
      $tituloFiltro = 'Ver fase';
      require __DIR__ . '/../components/filtro_fases.php';
    ?>

    <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

      <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black">
            Lista de partidos
          </h2>

          
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-white/[0.04] border border-white/10 px-4 py-2 rounded-xl">
          <span class="text-sm font-bold text-amber-400">
            <?php echo count($partidos ?? []); ?> partidos
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-black/40 border-b border-white/10">
            <tr>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Fecha</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Hora</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Partido</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Fase</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Estadio</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Resultado</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Acción</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-white/10">

            <?php if (!empty($partidos)): ?>

              <?php foreach ($partidos as $partido): ?>

                <?php
                  $resultadoLocal = $partido['goles_local_oficial'];
                  $resultadoVisitante = $partido['goles_visitante_oficial'];

                  $hayResultado = $resultadoLocal !== null && $resultadoVisitante !== null;
                  $puedeVaticinar = !empty($partido['puede_vaticinar']);
                ?>

                <tr class="hover:bg-white/[0.04] transition-colors">
                  <td class="px-6 py-5 whitespace-nowrap">
                    <span class="text-sm font-bold text-white">
                      <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                    </span>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <span class="text-sm font-bold text-white">
                      <?php echo date('H:i', strtotime($partido['hora'])); ?>
                    </span>
                  </td>

                  <td class="px-6 py-5">
                    <div class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-4 min-w-64">
                      <div class="space-y-2 text-base font-black text-white">
                        <div class="leading-tight">
                          <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                        </div>

                        <div class="leading-tight">
                          <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                        </div>
                      </div>

                      <span class="text-xs font-black tracking-widest text-slate-300">
                        VS
                      </span>
                    </div>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <span class="inline-flex px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-slate-300">
                      <?php echo htmlspecialchars($partido['nombre_fase']); ?>
                    </span>
                  </td>

                  <td class="px-6 py-5 text-sm text-white font-bold min-w-56">
                    <?php echo htmlspecialchars($partido['estadio']); ?>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <?php if ($hayResultado): ?>
                      <span class="inline-flex px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-sm font-black">
                        <?php echo htmlspecialchars($resultadoLocal); ?>
                        -
                        <?php echo htmlspecialchars($resultadoVisitante); ?>
                      </span>
                    <?php else: ?>
                      <span class="inline-flex px-3 py-1 rounded-full bg-slate-800 border border-white/10 text-slate-400 text-xs font-bold">
                        Pendiente
                      </span>
                    <?php endif; ?>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <?php if ($puedeVaticinar && !$esAdmin): ?>
                      <a href="predicciones.php?partido=<?php echo urlencode($partido['codigo_partido']); ?>"
                         class="inline-flex px-4 py-2 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500 text-slate-950 text-sm font-black shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all">
                        Vaticinar
                      </a>
                    <?php else: ?>
                      <span class="inline-flex px-3 py-1 rounded-full bg-slate-800 border border-white/10 text-slate-500 text-xs font-bold">
                        Cerrado
                      </span>
                    <?php endif; ?>
                  </td>
                </tr>

              <?php endforeach; ?>

            <?php else: ?>

              <tr>
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                  No hay partidos registrados todavía.
                </td>
              </tr>

            <?php endif; ?>

          </tbody>
        </table>
      </div>

    </section>

  </main>
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

</body>
</html>

