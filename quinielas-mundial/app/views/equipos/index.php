<?php
$equipos = $equipos ?? [];
$grupos = $grupos ?? [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Equipos - Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-950 text-white min-h-screen">

  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>

  <main class="max-w-7xl mx-auto px-6 py-10">

    <section class="mb-10">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">
        Panel administrativo
      </p>

      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        Gestión de
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">
          Equipos
        </span>
      </h1>

     
    </section>

    <?php if (isset($_GET['success'])): ?>
      <div class="mb-6 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-400 font-bold">
        Operación realizada correctamente.
      </div>
    <?php endif; ?>

    <!-- CREAR EQUIPO -->
    <section class="mb-10 bg-white/[0.03] border border-white/10 rounded-3xl p-6 shadow-xl shadow-black/20">

      <h2 class="text-2xl font-black mb-5">
        Crear nuevo equipo
      </h2>

      <form action="equipos.php" method="POST" class="grid grid-cols-1 md:grid-cols-[1fr_220px_auto] gap-4 items-end">

        <input type="hidden" name="accion" value="crear">

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            País
          </label>

          <input type="text" name="pais" required
                 class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
        </div>

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Grupo
          </label>

          <select name="codigo_grupo" required
                  class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">

            <?php foreach ($grupos as $grupo): ?>
              <option value="<?php echo htmlspecialchars($grupo['codigo_grupo']); ?>">
                Grupo <?php echo htmlspecialchars($grupo['codigo_grupo']); ?>
              </option>
            <?php endforeach; ?>

          </select>
        </div>

        <button type="submit"
                class="px-6 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                       text-slate-950 font-black shadow-lg shadow-amber-500/20
                       hover:scale-105 active:scale-95 transition-all">
          Crear equipo
        </button>

      </form>

    </section>

    <!-- LISTA EQUIPOS -->
    <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

      <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black">
            Equipos registrados
          </h2>

          <p class="text-sm text-slate-400 mt-1">
            Editá país o grupo de cada equipo.
          </p>
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-xl">
          <span class="h-2 w-2 rounded-full bg-amber-400"></span>

          <span class="text-sm font-bold text-amber-400">
            <?php echo count($equipos ?? []); ?> equipos
          </span>
        </div>
      </div>

      <div class="divide-y divide-white/10">

        <?php if (!empty($equipos)): ?>

          <?php foreach ($equipos as $equipo): ?>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-5 hover:bg-white/[0.04] transition-colors">

              <form action="equipos.php" method="POST" class="grid grid-cols-1 md:grid-cols-[1fr_220px_auto] gap-4 items-end">

                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="pais_original" value="<?php echo htmlspecialchars($equipo['pais']); ?>">

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    País
                  </label>

                  <input type="text" name="pais" required
                         value="<?php echo htmlspecialchars($equipo['pais']); ?>"
                         class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Grupo
                  </label>

                  <select name="codigo_grupo" required
                          class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">

                    <?php foreach ($grupos as $grupo): ?>
                      <option value="<?php echo htmlspecialchars($grupo['codigo_grupo']); ?>"
                        <?php echo $grupo['codigo_grupo'] == $equipo['codigo_grupo'] ? 'selected' : ''; ?>>
                        Grupo <?php echo htmlspecialchars($grupo['codigo_grupo']); ?>
                      </option>
                    <?php endforeach; ?>

                  </select>
                </div>

                <button type="submit"
                        class="px-5 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                               text-slate-950 font-black shadow-lg shadow-amber-500/20
                               hover:scale-105 active:scale-95 transition-all">
                  Guardar
                </button>

              </form>

              <form action="equipos.php" method="POST" class="flex items-end">
                <input type="hidden" name="accion" value="eliminar">
                <input type="hidden" name="pais" value="<?php echo htmlspecialchars($equipo['pais']); ?>">

                <button type="submit"
                        onclick="return confirm('¿Seguro que querés eliminar este equipo? Puede afectar partidos relacionados.')"
                        class="px-5 py-3 rounded-xl bg-red-500/10 border border-red-500/30
                               text-red-400 font-bold hover:bg-red-500 hover:text-white transition-all">
                  Eliminar
                </button>
              </form>

            </div>

          <?php endforeach; ?>

        <?php else: ?>

          <div class="p-10 text-center text-slate-400">
            No hay equipos registrados.
          </div>

        <?php endif; ?>

      </div>

    </section>

  </main>

</body>
</html>