<?php
$fases = $fases ?? [];
$faseSeleccionada = $faseSeleccionada ?? '';
$accionFiltro = $accionFiltro ?? basename($_SERVER['PHP_SELF']);
$tituloFiltro = $tituloFiltro ?? 'Filtrar por fase';
$textoTodas = $textoTodas ?? 'Todas las fases';
$busquedaPartidos = $busquedaPartidos ?? trim($_GET['q'] ?? '');
$urlTodas = $accionFiltro;

if ($busquedaPartidos !== '') {
  $urlTodas .= '?q=' . urlencode($busquedaPartidos);
}
?>

<?php if (!empty($fases)): ?>
  <form action="<?php echo htmlspecialchars($accionFiltro); ?>" method="GET"
        class="mb-6 flex flex-col sm:flex-row sm:items-end gap-4 rounded-3xl border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/20">
    <div class="flex-1">
      <label class="block text-xs text-slate-400 font-bold mb-2 uppercase tracking-widest">
        <?php echo htmlspecialchars($tituloFiltro); ?>
      </label>

      <select name="fase" onchange="this.form.submit()"
              class="w-full bg-slate-950 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-amber-400">
        <option value=""><?php echo htmlspecialchars($textoTodas); ?></option>

        <?php foreach ($fases as $fase): ?>
          <option value="<?php echo htmlspecialchars($fase['nombre_fase']); ?>"
            <?php echo $faseSeleccionada === $fase['nombre_fase'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($fase['nombre_fase']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php if ($busquedaPartidos !== ''): ?>
        <input type="hidden" name="q" value="<?php echo htmlspecialchars($busquedaPartidos); ?>">
      <?php endif; ?>
    </div>

    <?php if ($faseSeleccionada !== ''): ?>
      <a href="<?php echo htmlspecialchars($urlTodas); ?>"
         class="inline-flex w-full justify-center rounded-xl border border-white/10 px-5 py-3 text-sm font-bold text-slate-200 transition hover:bg-white/10 active:scale-95 sm:w-auto">
        Ver todas
      </a>
    <?php endif; ?>
  </form>
<?php endif; ?>
