<?php
$equipos = $equipos ?? [];
$grupos = $grupos ?? [];
require_once __DIR__ . '/../../helpers/banderas.php';

$mensajesError = [
  'datos' => 'Todos los campos del equipo son obligatorios.',
  'duplicado' => 'Ya existe un equipo con ese país.',
  'imagen' => 'No se pudo subir la imagen. Intenta de nuevo.',
  'imagen_tamano' => 'La imagen debe pesar 2 MB o menos.',
  'imagen_tipo' => 'La bandera debe ser una imagen PNG, JPG o WebP.',
  'referencia' => 'El grupo seleccionado no existe.',
  'relacionado' => 'No se pudo eliminar el equipo porque tiene partidos o datos relacionados.',
  'bd' => 'No se pudo guardar el equipo. Revisa los datos e intenta de nuevo.',
  'grupo_lleno' => 'El grupo seleccionado ya tiene 4 equipos. Cada grupo admite exactamente 4 países.'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
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

    <?php if (isset($_GET['error'])): ?>
      <?php
        $error = $_GET['error'];
        $mensajeError = $mensajesError[$error] ?? $error;
      ?>
      <div class="mb-6 rounded-2xl border border-red-500/30 bg-red-500/10 px-5 py-4 text-red-400 font-bold">
        <?php echo htmlspecialchars($mensajeError); ?>
      </div>
    <?php endif; ?>

    <!-- CREAR EQUIPO -->
    <section class="mb-10 bg-white/[0.03] border border-white/10 rounded-3xl p-6 shadow-xl shadow-black/20">

      <h2 class="text-2xl font-black mb-5">
        Crear nuevo equipo
      </h2>

      <form action="equipos.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1fr_180px_240px_auto] gap-4 items-end">

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

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Bandera
          </label>

          <div class="rounded-xl border border-white/10 bg-slate-950 p-2 focus-within:border-amber-400">
            <label for="bandera-crear"
                   class="flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500 px-4 py-2 text-sm font-black text-slate-950 transition hover:scale-[1.02] active:scale-[0.98]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16.5V19a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-2.5" />
              </svg>
              Seleccionar bandera
            </label>

            <input id="bandera-crear" type="file" name="bandera" accept="image/png,image/jpeg,image/webp"
                   class="sr-only js-bandera-input">

            <p class="js-bandera-name mt-2 rounded-lg bg-white/[0.03] px-3 py-2 text-xs font-bold text-slate-500">
              Ninguna imagen seleccionada
            </p>
          </div>
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
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-xl">
          <span class="text-sm font-bold text-amber-400">
            <?php echo count($equipos ?? []); ?> equipos
          </span>
        </div>
      </div>

      <div class="divide-y divide-white/10">

        <?php if (!empty($equipos)): ?>

          <?php foreach ($equipos as $indiceEquipo => $equipo): ?>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-5 hover:bg-white/[0.04] transition-colors">

              <form action="equipos.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[1fr_180px_240px_auto] gap-4 items-end">

                <input type="hidden" name="accion" value="editar">
                <input type="hidden" name="pais_original" value="<?php echo htmlspecialchars($equipo['pais']); ?>">

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    País
                  </label>

                  <div class="mb-3 text-sm font-bold text-white">
                    <?php echo equipoConBandera($equipo['pais'], $equipo['bandera']); ?>
                  </div>

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

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Bandera
                  </label>

                  <?php $banderaId = 'bandera-editar-' . $indiceEquipo; ?>

                  <div class="rounded-xl border border-white/10 bg-slate-950 p-2 focus-within:border-amber-400">
                    <label for="<?php echo htmlspecialchars($banderaId); ?>"
                           class="flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-amber-500/10 px-4 py-2 text-sm font-black text-amber-300 transition hover:bg-amber-500/20 active:scale-[0.98]">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0 4 4m-4-4-4 4M4 16.5V19a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-2.5" />
                      </svg>
                      Cambiar bandera
                    </label>

                    <input id="<?php echo htmlspecialchars($banderaId); ?>" type="file" name="bandera" accept="image/png,image/jpeg,image/webp"
                           class="sr-only js-bandera-input">

                    <p class="js-bandera-name mt-2 rounded-lg bg-white/[0.03] px-3 py-2 text-xs font-bold text-slate-500">
                      Mantener bandera actual
                    </p>
                  </div>
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

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.js-bandera-input').forEach((input) => {
        const contenedor = input.closest('div');
        const nombreArchivo = contenedor?.querySelector('.js-bandera-name');

        input.addEventListener('change', () => {
          const archivo = input.files?.[0];

          if (!nombreArchivo) {
            return;
          }

          if (archivo) {
            nombreArchivo.textContent = archivo.name;
            nombreArchivo.classList.remove('text-slate-500', 'bg-white/[0.03]');
            nombreArchivo.classList.add('text-emerald-300', 'bg-emerald-500/10', 'border', 'border-emerald-500/20');
          }
        });
      });
    });
  </script>

</body>
</html>
