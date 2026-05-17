<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Calendario - Quiniela Mundial 2026</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

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

    <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

      <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black">
            Lista de partidos
          </h2>

          
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-xl">
          <span class="h-2 w-2 rounded-full bg-amber-400"></span>
          <span class="text-sm font-bold text-amber-400">
            <?php echo count($partidos ?? []); ?> partidos
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-black/40 border-b border-white/10">
            <tr>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">#</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Fecha</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Hora</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Partido</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Fase</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Estadio</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Resultado</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-white/10">

            <?php if (!empty($partidos)): ?>

              <?php foreach ($partidos as $partido): ?>

                <?php
                  $resultadoLocal = $partido['goles_local_oficial'];
                  $resultadoVisitante = $partido['goles_visitante_oficial'];

                  $hayResultado = $resultadoLocal !== null && $resultadoVisitante !== null;
                ?>

                <tr class="hover:bg-white/[0.04] transition-colors">
                  <td class="px-6 py-5 text-sm font-bold text-slate-400">
                    <?php echo htmlspecialchars($partido['codigo_partido']); ?>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <span class="text-sm font-bold text-white">
                      <?php echo date('d/m/Y', strtotime($partido['fecha'])); ?>
                    </span>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <span class="text-sm text-slate-300">
                      <?php echo date('H:i', strtotime($partido['hora'])); ?>
                    </span>
                  </td>

                  <td class="px-6 py-5">
                    <div class="flex flex-col">
                      <span class="text-base font-black text-white">
                        <?php echo htmlspecialchars($partido['pais_local']); ?>
                        <span class="mx-2 text-slate-500">vs</span>
                        <?php echo htmlspecialchars($partido['pais_visitante']); ?>
                      </span>
                    </div>
                  </td>

                  <td class="px-6 py-5 whitespace-nowrap">
                    <span class="inline-flex px-3 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-slate-300">
                      <?php echo htmlspecialchars($partido['nombre_fase']); ?>
                    </span>
                  </td>

                  <td class="px-6 py-5 text-sm text-slate-400 min-w-56">
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