<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Ranking - Quiniela Mundial 2026</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <main class="max-w-7xl mx-auto px-6 py-10">

    <section class="mb-10">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
        Tabla general
      </p>

      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        Ranking de
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
          Participantes
        </span>
      </h1>
    </section>

    <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

      <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black">
            Puntos acumulados
          </h2>
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-white/[0.04] border border-white/10 px-4 py-2 rounded-xl">
          <span class="text-sm font-bold text-amber-400">
            <?php echo count($ranking ?? []); ?> participantes
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left">
          <thead class="bg-black/40 border-b border-white/10">
            <tr>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">#</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Participante</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Usuario</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Predicciones</th>
              <th class="px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Puntos</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-white/10">
            <?php if (!empty($ranking)): ?>
              <?php foreach ($ranking as $index => $participante): ?>
                <tr class="hover:bg-white/[0.04] transition-colors">
                  <td class="px-6 py-5">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/5 border border-white/10 text-sm font-black text-amber-400">
                      <?php echo $index + 1; ?>
                    </span>
                  </td>

                  <td class="px-6 py-5 font-black">
                    <?php echo htmlspecialchars($participante['nombre']); ?>
                  </td>

                  <td class="px-6 py-5 font-black">
                    @<?php echo htmlspecialchars($participante['username']); ?>
                  </td>

                  <td class="px-6 py-5 font-black">
                    <?php echo htmlspecialchars($participante['predicciones']); ?>
                  </td>

                  <td class="px-6 py-5">
                    <span class="inline-flex px-4 py-2 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 font-black">
                      <?php echo htmlspecialchars($participante['puntos']); ?> pts
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                  No hay participantes registrados todavía.
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

