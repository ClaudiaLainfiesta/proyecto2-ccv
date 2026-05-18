<?php
$partidos = $partidos ?? [];
$equipos = $equipos ?? [];
$fases = $fases ?? [];
require_once __DIR__ . '/../../helpers/banderas.php';

$mensajesError = [
  'codigo' => 'El código del partido debe ser un número entero positivo.',
  'datos' => 'Todos los campos del partido son obligatorios.',
  'paises' => 'Un equipo no puede jugar contra sí mismo.',
  'fecha' => 'La fecha u hora del partido no es válida.',
  'duplicado' => 'Ya existe un partido con ese código.',
  'estadio_fecha' => 'Ya hay un partido programado ese día en ese estadio.',
  'equipo_horario' => 'Uno de los equipos ya tiene un partido programado en esa fecha y hora.',
  'referencia' => 'La fase o alguno de los equipos seleccionados no existe.',
  'relacionado' => 'No se pudo eliminar el partido porque tiene datos relacionados.',
  'bd' => 'No se pudo guardar el partido. Revisa los datos e intenta de nuevo.'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Partidos - Admin</title>
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
          Partidos
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

    <section class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-4">
      <form action="partidos.php" method="POST" class="bg-white/[0.03] border border-white/10 rounded-3xl p-5 shadow-xl shadow-black/20">
        <input type="hidden" name="accion" value="generar_dieci">

        <p class="text-sm text-amber-400 font-bold uppercase tracking-widest">
          Eliminatorias
        </p>

        <h2 class="mt-2 text-2xl font-black text-white">
          Generar dieciseisavos
        </h2>

        <p class="mt-2 text-sm text-slate-400">
          Usa las posiciones de grupo y el calendario oficial de partidos 73 al 88.
        </p>

        <button type="submit"
                class="mt-5 px-5 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500 text-slate-950 font-black shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all">
          Generar
        </button>
      </form>

      <form action="partidos.php" method="POST" class="bg-white/[0.03] border border-white/10 rounded-3xl p-5 shadow-xl shadow-black/20">
        <input type="hidden" name="accion" value="generar_siguiente">

        <p class="text-sm text-amber-400 font-bold uppercase tracking-widest">
          Avance automático
        </p>

        <h2 class="mt-2 text-2xl font-black text-white">
          Generar siguiente fase
        </h2>

        <p class="mt-2 text-sm text-slate-400">
          Toma los ganadores según las llaves oficiales hasta la final.
        </p>

        <button type="submit"
                class="mt-5 px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-white font-black hover:bg-white/10 transition-all">
          Avanzar fase
        </button>
      </form>
    </section>

    <!-- CREAR PARTIDO -->
    <section class="mb-10 bg-white/[0.03] border border-white/10 rounded-3xl p-6 shadow-xl shadow-black/20">

      <h2 class="text-2xl font-black mb-5">
        Crear nuevo partido
      </h2>

      <form action="partidos.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

        <input type="hidden" name="accion" value="crear">
        <input type="hidden" name="fase_actual" value="<?php echo htmlspecialchars($faseSeleccionada ?? ''); ?>">

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Código
          </label>
          <input type="number" name="codigo_partido" required
                 class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
        </div>

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Fecha
          </label>
          <input type="date" name="fecha" required
                 class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
        </div>

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Hora
          </label>
          <input type="time" name="hora" required
                 class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
        </div>

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Fase
          </label>
          <select name="nombre_fase" required
                  class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
            <?php foreach ($fases as $fase): ?>
              <option value="<?php echo htmlspecialchars($fase['nombre_fase']); ?>">
                <?php echo htmlspecialchars($fase['nombre_fase']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Local
          </label>
          <select name="pais_local" required
                  class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
            <?php foreach ($equipos as $equipo): ?>
              <option value="<?php echo htmlspecialchars($equipo['pais']); ?>">
                <?php echo htmlspecialchars($equipo['pais']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Visitante
          </label>
          <select name="pais_visitante" required
                  class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
            <?php foreach ($equipos as $equipo): ?>
              <option value="<?php echo htmlspecialchars($equipo['pais']); ?>">
                <?php echo htmlspecialchars($equipo['pais']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
            Estadio
          </label>
          <input type="text" name="estadio" required
                 class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
        </div>

        <div class="lg:col-span-4 flex justify-end">
          <button type="submit"
                  class="px-6 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                         text-slate-950 font-black shadow-lg shadow-amber-500/20
                         hover:scale-105 active:scale-95 transition-all">
            Crear partido
          </button>
        </div>

      </form>

    </section>

    <?php
      $accionFiltro = 'partidos.php';
      $tituloFiltro = 'Ver fase';
      require __DIR__ . '/../components/filtro_fases.php';
    ?>

    <!-- LISTA PARTIDOS -->
    <section class="bg-white/[0.03] border border-white/10 rounded-3xl shadow-xl shadow-black/20 overflow-hidden">

      <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-black">
            Partidos registrados
          </h2>
          
        </div>

        <div class="hidden sm:flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-xl">
          <span class="h-2 w-2 rounded-full bg-amber-400"></span>
          <span class="text-sm font-bold text-amber-400">
            <?php echo count($partidos ?? []); ?> partidos
          </span>
        </div>
      </div>

      <div class="divide-y divide-white/10">

        <?php if (!empty($partidos)): ?>

          <?php foreach ($partidos as $partido): ?>

            <form action="partidos.php" method="POST"
                  class="p-6 grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-6 hover:bg-white/[0.04] transition-colors">

              <input type="hidden" name="accion" value="editar">
              <input type="hidden" name="codigo_partido" value="<?php echo htmlspecialchars($partido['codigo_partido']); ?>">
              <input type="hidden" name="fase_actual" value="<?php echo htmlspecialchars($faseSeleccionada ?? ''); ?>">

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <div class="md:col-span-2 lg:col-span-4 text-lg font-black text-white">
                  <?php echo equipoConBandera($partido['pais_local'], $partido['bandera_local']); ?>
                  <span class="mx-2 text-slate-500">vs</span>
                  <?php echo equipoConBandera($partido['pais_visitante'], $partido['bandera_visitante']); ?>
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Partido
                  </label>
                  <p class="text-lg font-black text-white">
                    #<?php echo htmlspecialchars($partido['codigo_partido']); ?>
                  </p>
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Fecha
                  </label>
                  <input type="date" name="fecha" required
                         value="<?php echo htmlspecialchars($partido['fecha']); ?>"
                         class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Hora
                  </label>
                  <input type="time" name="hora" required
                         value="<?php echo htmlspecialchars(substr($partido['hora'], 0, 5)); ?>"
                         class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Fase
                  </label>
                  <select name="nombre_fase" required
                          class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                    <?php foreach ($fases as $fase): ?>
                      <option value="<?php echo htmlspecialchars($fase['nombre_fase']); ?>"
                        <?php echo $fase['nombre_fase'] === $partido['nombre_fase'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($fase['nombre_fase']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Local
                  </label>
                  <select name="pais_local" required
                          class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                    <?php foreach ($equipos as $equipo): ?>
                      <option value="<?php echo htmlspecialchars($equipo['pais']); ?>"
                        <?php echo $equipo['pais'] === $partido['pais_local'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($equipo['pais']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div>
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Visitante
                  </label>
                  <select name="pais_visitante" required
                          class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                    <?php foreach ($equipos as $equipo): ?>
                      <option value="<?php echo htmlspecialchars($equipo['pais']); ?>"
                        <?php echo $equipo['pais'] === $partido['pais_visitante'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($equipo['pais']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs text-slate-500 font-bold mb-2 uppercase tracking-widest">
                    Estadio
                  </label>
                  <input type="text" name="estadio" required
                         value="<?php echo htmlspecialchars($partido['estadio']); ?>"
                         class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
                </div>

              </div>

              <div class="flex flex-col gap-3 justify-end sm:flex-row lg:flex-col">

                <button type="submit"
                        class="px-5 py-3 rounded-xl bg-gradient-to-r from-yellow-600 via-amber-500 to-yellow-500
                               text-slate-950 font-black shadow-lg shadow-amber-500/20
                               hover:scale-105 active:scale-95 transition-all">
                  Guardar
                </button>

            </form>

            <form action="partidos.php" method="POST" class="px-6 pb-6 lg:px-0 lg:pb-0">
              <input type="hidden" name="accion" value="eliminar">
              <input type="hidden" name="codigo_partido" value="<?php echo htmlspecialchars($partido['codigo_partido']); ?>">
              <input type="hidden" name="fase_actual" value="<?php echo htmlspecialchars($faseSeleccionada ?? ''); ?>">

              <button type="submit"
                      onclick="return confirm('¿Seguro que querés eliminar este partido? También puede afectar predicciones relacionadas.')"
                      class="px-5 py-3 rounded-xl bg-red-500/10 border border-red-500/30
                             text-red-400 font-bold hover:bg-red-500 hover:text-white transition-all">
                Eliminar
              </button>
            </form>

              </div>

          <?php endforeach; ?>

        <?php else: ?>

          <div class="p-10 text-center text-slate-400">
            No hay partidos registrados.
          </div>

        <?php endif; ?>

      </div>

    </section>

  </main>

</body>
</html>
