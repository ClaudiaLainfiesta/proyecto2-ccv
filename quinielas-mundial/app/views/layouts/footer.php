<?php
require_once __DIR__ . '/../../helpers/auth.php';
$esAdminFooter = esAdmin();
?>

<footer class="mt-20 border-t border-white/10 bg-black/40 backdrop-blur-md">

  <div class="max-w-7xl mx-auto px-6 py-10">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

      <!-- BRAND -->
      <div class="flex items-center gap-3">
                <a href="index.php"
                    class="p-1 rounded-2xl flex items-center justify-center h-11 w-11
                        shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95
                        transition-all duration-200">
                    <img src="assets/img/copa26.jpeg" alt="Logo Quiniela-Copa" class="h-full w-full object-contain">
                </a>

                <!-- Texto al lado del logo -->
                <div class="hidden sm:block">
                    <a href="index.php"
                        class="font-extrabold text-lg tracking-tight
                            bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500
                            bg-clip-text text-transparent">
                        Quiniela Mundial 2026
                    </a>

                    <p class="text-[11px] text-slate-500 tracking-widest font-bold uppercase">
                        FIFA World Cup
                    </p>
                </div>
            </div>

      <!-- NAVEGACIÓN -->
      <div>

        <h4 class="text-sm font-black uppercase tracking-widest text-amber-400">
          Navegación
        </h4>

        <div class="mt-5 flex flex-col gap-3 text-sm">

          <a href="index.php"
             class="text-slate-400 hover:text-amber-400 transition-colors">
            Inicio
          </a>

          <a href="calendario.php"
             class="text-slate-400 hover:text-amber-400 transition-colors">
            Calendario
          </a>

          <a href="predicciones.php"
              class="text-slate-400 hover:text-amber-400 transition-colors">
            Mis Predicciones
          </a>

          <a href="ranking.php"
             class="text-slate-400 hover:text-amber-400 transition-colors">
            Ranking
          </a>

          <a href="posiciones.php"
             class="text-slate-400 hover:text-amber-400 transition-colors">
            Posiciones
          </a>

          <a href="llaves.php"
             class="text-slate-400 hover:text-amber-400 transition-colors">
            Bracket
          </a>

        </div>

      </div>

      <!-- INFO -->
      <div>

        <h4 class="text-sm font-black uppercase tracking-widest text-amber-400">
          Información
        </h4>

        <div class="mt-5 space-y-4 text-sm text-slate-400">

          <div>
            <p class="font-bold text-white">
              Sistema de puntos
            </p>

            <p class="mt-1">
              +3 por acertar resultado y +3 extra por acertar marcador exacto.
            </p>
          </div>

        </div>

      </div>

    </div>

    <!-- BOTTOM -->
    <div class="mt-10 pt-6 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-4">

      <p class="text-xs text-slate-500 text-center md:text-left">
        © <?php echo date('Y'); ?> Quiniela Mundial 2026. Todos los derechos reservados.
      </p>

    </div>

  </div>

</footer>
