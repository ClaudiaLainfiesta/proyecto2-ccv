<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Posiciones - Quiniela Mundial 2026</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

  <?php require_once __DIR__ . '/../../helpers/banderas.php'; ?>
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <main class="max-w-7xl mx-auto px-6 py-10">

    <section class="mb-10">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
        Fase de grupos
      </p>

      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        Tabla de
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
          Posiciones
        </span>
      </h1>
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-2 gap-6">
      <?php if (!empty($grupos)): ?>
        <?php foreach ($grupos as $codigoGrupo => $equipos): ?>
          <article class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">
            <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
              <h2 class="text-2xl font-black">
                Grupo <?php echo htmlspecialchars($codigoGrupo); ?>
              </h2>

              <span class="text-xs font-bold uppercase tracking-widest text-amber-400">
                <?php echo count($equipos); ?> equipos
              </span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left">
                <thead class="bg-black/40 border-b border-white/10">
                  <tr>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-widest text-slate-400">Equipo</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">PJ</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">G</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">E</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">P</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">GF</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">GC</th>
                    <th class="px-3 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-center">DG</th>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-widest text-slate-400 text-right">Pts</th>
                  </tr>
                </thead>

                <tbody class="divide-y divide-white/10">
                  <?php foreach ($equipos as $equipo): ?>
                    <tr class="hover:bg-white/[0.04] transition-colors">
                      <td class="px-5 py-4 font-black text-white">
                        <?php echo equipoConBandera($equipo['pais'], $equipo['bandera']); ?>
                      </td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['jugados']); ?></td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['ganados']); ?></td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['empatados']); ?></td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['perdidos']); ?></td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['goles_favor']); ?></td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['goles_contra']); ?></td>
                      <td class="px-3 py-4 text-center text-slate-300"><?php echo htmlspecialchars($equipo['diferencia_goles']); ?></td>
                      <td class="px-5 py-4 text-right">
                        <span class="inline-flex px-3 py-1 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 font-black">
                          <?php echo htmlspecialchars($equipo['puntos']); ?>
                        </span>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="xl:col-span-2 bg-white/[0.03] border border-white/10 rounded-3xl p-10 text-center text-slate-400">
          No hay equipos registrados todavía.
        </div>
      <?php endif; ?>
    </section>

  </main>

  <?php require_once __DIR__ . '/../layouts/footer.php'; ?>

</body>
</html>

