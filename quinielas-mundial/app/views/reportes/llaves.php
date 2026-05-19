<?php
require_once __DIR__ . '/../../helpers/banderas.php';

$partidosLlaves = $partidosLlaves ?? [];

$fasesLlave = [
  'Dieciseisavos de Final' => ['titulo' => 'Dieciseisavos', 'partidos' => 16],
  'Octavos de Final'       => ['titulo' => 'Octavos',       'partidos' => 8],
  'Cuartos de Final'       => ['titulo' => 'Cuartos',       'partidos' => 4],
  'Semifinales'            => ['titulo' => 'Semis',          'partidos' => 2],
];

$partidosPorFase = [];
foreach ($partidosLlaves as $partido) {
  $partidosPorFase[$partido['nombre_fase']][] = $partido;
}

function ordenarPartidosLlavePorCodigo($partidos, $ordenCodigos) {
  $partidos = $partidos ?? [];
  $ordenados = [];
  $porCodigo = [];
  $codigosOrdenados = [];

  foreach ($partidos as $partido) {
    $codigo = (int)$partido['codigo_partido'];
    $porCodigo[$codigo] = $partido;
  }

  foreach ($ordenCodigos as $codigo) {
    if (isset($porCodigo[$codigo])) {
      $codigosOrdenados[$codigo] = true;
      $ordenados[] = $porCodigo[$codigo];
    }
  }

  foreach ($partidos as $partido) {
    $codigo = (int)$partido['codigo_partido'];
    if (!isset($codigosOrdenados[$codigo])) {
      $ordenados[] = $partido;
    }
  }

  return $ordenados;
}

$ordenVisualLlaves = [
  'Dieciseisavos de Final' => [74, 77, 73, 75, 83, 84, 81, 82, 76, 78, 79, 80, 86, 88, 85, 87],
  'Octavos de Final'       => [89, 90, 93, 94, 91, 92, 95, 96],
  'Cuartos de Final'       => [97, 98, 99, 100],
  'Semifinales'            => [101, 102],
  'Final'                  => [104],
];

foreach ($ordenVisualLlaves as $fase => $ordenCodigos) {
  $partidosPorFase[$fase] = ordenarPartidosLlavePorCodigo($partidosPorFase[$fase] ?? [], $ordenCodigos);
}

function ganadorPartidoLlave($partido) {
  if (
    empty($partido) ||
    $partido['goles_local_oficial'] === null ||
    $partido['goles_visitante_oficial'] === null ||
    (int)$partido['goles_local_oficial'] === (int)$partido['goles_visitante_oficial']
  ) return null;
  return (int)$partido['goles_local_oficial'] > (int)$partido['goles_visitante_oficial']
    ? $partido['pais_local']
    : $partido['pais_visitante'];
}

function claseEquipoLlave($partido, $pais) {
  $ganador = ganadorPartidoLlave($partido);
  if ($ganador === null) return '';
  return $ganador === $pais ? ' bracket-team-winner' : ' bracket-team-loser';
}

function renderEquipoLlave($partido, $lado) {
  if (empty($partido)) {
    echo '<div class="bracket-team bracket-team-empty"><span class="bracket-team-name">Por definir</span><span class="bracket-score bracket-score-empty">-</span></div>';
    return;
  }
  $pais    = $lado === 'local' ? $partido['pais_local']          : $partido['pais_visitante'];
  $bandera = $lado === 'local' ? $partido['bandera_local']       : $partido['bandera_visitante'];
  $goles   = $lado === 'local' ? $partido['goles_local_oficial'] : $partido['goles_visitante_oficial'];
  $clase   = claseEquipoLlave($partido, $pais);
  $scoreClass = ($goles !== null && $clase === ' bracket-team-winner') ? ' bracket-score-win' : '';
?>
  <div class="bracket-team<?php echo htmlspecialchars($clase); ?>">
    <span class="bracket-team-name"><?php echo equipoConBandera($pais, $bandera, 'h-4 w-6'); ?></span>
    <span class="bracket-score<?php echo $scoreClass; ?>"><?php echo $goles !== null ? htmlspecialchars($goles) : '-'; ?></span>
  </div>
<?php
}

function renderPartidoLlave($partido, $numero, $lado = 'left', $bannerCampeon = null) {
  $hayResultado = !empty($partido)
    && $partido['goles_local_oficial'] !== null
    && $partido['goles_visitante_oficial'] !== null;
?>
  <div class="bracket-match-wrapper">
    <article class="bracket-match bracket-match-<?php echo htmlspecialchars($lado); ?><?php echo $hayResultado ? ' bracket-match-done' : ''; ?>">
      <div class="bracket-match-meta">
        <span class="bracket-match-code">#<?php echo !empty($partido) ? htmlspecialchars($partido['codigo_partido']) : htmlspecialchars($numero); ?></span>
        <?php if ($hayResultado): ?>
          <span class="bracket-badge bracket-badge-done">Finalizado</span>
        <?php else: ?>
          <span class="bracket-badge bracket-badge-pending">Pendiente</span>
        <?php endif; ?>
      </div>
      <div class="bracket-teams">
        <?php renderEquipoLlave($partido, 'local'); ?>
        <div class="bracket-divider"></div>
        <?php renderEquipoLlave($partido, 'visitante'); ?>
      </div>
      <?php if ($bannerCampeon !== null && isset($bannerCampeon['nombre'])): ?>
        <div class="bracket-champion-banner">
          
          <?php if (!empty($bannerCampeon['bandera'])): ?>
            <img src="<?php echo htmlspecialchars($bannerCampeon['bandera']); ?>" alt="Bandera">
          <?php endif; ?>
          <span><?php echo htmlspecialchars($bannerCampeon['nombre']); ?> &mdash; Campeon</span>
        </div>
      <?php endif; ?>
      <div class="bracket-match-footer">
        <?php if (!empty($partido)): ?>
          <span class="bracket-footer-date"><?php echo date('d/m/Y', strtotime($partido['fecha'])); ?></span>
          <span class="bracket-footer-dot">·</span>
          <span class="bracket-footer-time"><?php echo date('H:i', strtotime($partido['hora'])); ?></span>
        <?php else: ?>
          <span class="bracket-footer-tbd">Se definira al avanzar</span>
        <?php endif; ?>
      </div>
    </article>
  </div>
<?php
}

function partidosMitadLlave($partidos, $total, $lado) {
  $partidos = $partidos ?? [];
  $mitad    = (int)ceil($total / 2);
  if ($lado === 'left') return array_pad(array_slice($partidos, 0, $mitad), $mitad, null);
  return array_pad(array_slice($partidos, $mitad, $mitad), $mitad, null);
}

$final          = null;
foreach ($partidosPorFase['Final'] ?? [] as $partidoFinal) {
  if ((int)$partidoFinal['codigo_partido'] === 104) {
    $final = $partidoFinal;
    break;
  }
}
$final          = $final ?? ($partidosPorFase['Final'][0] ?? null);
$tercerLugar    = $partidosPorFase['Tercer Lugar'][0] ?? null;
$campeon        = ganadorPartidoLlave($final);
$banderaCampeon = '';
if ($campeon !== null && !empty($final)) {
  $banderaCampeon = $campeon === $final['pais_local']
    ? obtenerBanderaSrc($final['bandera_local'])
    : obtenerBanderaSrc($final['bandera_visitante']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <?php require_once __DIR__ . '/../layouts/header.php'; ?>
  <title>Llaves - Quiniela Mundial 2026</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* CAMBIO SOLICITADO: Removidos los bordes de la caja exterior para un look 100% integrado */
    .bracket-shell { 
      background: transparent !important; 
      border: none !important; 
      box-shadow: none !important; 
    }
    .bracket-summary { background: var(--color-negro); border-bottom: 1px solid var(--color-oro-oscuro); }
    .bracket-summary h2, .bracket-summary p { color: #fff !important; }
    .bracket-count { border: 1px solid rgba(201,168,76,.45); background: rgba(201,168,76,.12); color: var(--color-oro) !important; }
    
    .bracket-scroll { 
      background: transparent !important; 
      position: relative;
    }

    .bracket-board {
      --bracket-gap: 1.5rem; --line: var(--color-oro-oscuro);
      min-width: 1480px; position: relative; display: grid;
      grid-template-columns: 1.12fr 1fr 0.9fr 0.82fr 0.9fr 0.82fr 0.9fr 1fr 1.12fr;
      column-gap: var(--bracket-gap); align-items: stretch;
    }
    .bracket-column { position: relative; display: flex; flex-direction: column; min-height: 930px; }
    .bracket-column-title { height: 2rem; display: flex; align-items: center; justify-content: center; color: var(--color-oro-oscuro); font-size: .62rem; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; }
    .bracket-column-matches { position: relative; flex: 1; display: flex; flex-direction: column; justify-content: space-around; }
    .bracket-match-wrapper { position: relative; width: 100%; }

    /* === TARJETA PARTIDO === */
    .bracket-match {
      position: relative; width: 100%;
      display: flex; flex-direction: column;
      border-radius: 7px;
      border: 1px solid #ddd4be;
      background: #fdfaf5;
      overflow: hidden; z-index: 2;
      transition: box-shadow .15s ease, transform .15s ease;
    }
    .bracket-match:hover { box-shadow: 0 6px 20px rgba(0,0,0,.12); transform: translateY(-1px); }
    .bracket-match-done  { border-top: 3px solid var(--color-oro-oscuro, #a07830); }

    /* Cabecera */
    .bracket-match-meta {
      display: flex; align-items: center; justify-content: space-between;
      padding: .28rem .55rem;
      background: linear-gradient(135deg,#f7f0e0,#f0e8d0);
      border-bottom: 1px solid #e5d9c0;
    }
    .bracket-match-code { font-size: .58rem; font-weight: 800; color: #9c7a3a; letter-spacing: .07em; text-transform: uppercase; }
    .bracket-badge { font-size: .5rem; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; padding: .12rem .45rem; border-radius: 20px; }
    .bracket-badge-done    { background: #fef0c7; color: #7a5500; }
    .bracket-badge-pending { background: #fef3c7; color: #92400e; }

    /* Equipos */
    .bracket-teams { display: flex; flex-direction: column; }
    .bracket-divider { height: 1px; background: #e8dfc8; margin: 0 .55rem; }
    .bracket-team { display: flex; align-items: center; justify-content: space-between; gap: .4rem; padding: .42rem .55rem; }
    .bracket-team-winner { background: #fffdf0; }
    .bracket-team-loser  { opacity: .48; }
    .bracket-team-empty  { color: #bbb; font-style: italic; }
    .bracket-team-name { display: flex; align-items: center; gap: .45rem; font-size: .71rem; font-weight: 700; color: #231d0f; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; flex: 1; }

    /* Marcador */
    .bracket-score { min-width: 1.55rem; height: 1.55rem; display: flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 900; border-radius: 5px; background: #eae0cc; color: #4a3a18; flex-shrink: 0; }
    .bracket-score-win   { background: #7a5500; color: #ffe99a; }
    .bracket-score-empty { background: #f0e8d8; color: #c0a870; }

    /* Pie */
    .bracket-match-footer { display: flex; align-items: center; gap: .3rem; padding: .26rem .55rem; background: linear-gradient(135deg,#f7f0e0,#f0e8d0); border-top: 1px solid #e5d9c0; }
    .bracket-footer-date,.bracket-footer-time { font-size: .57rem; font-weight: 700; color: #9c7a3a; letter-spacing: .03em; }
    .bracket-footer-dot  { font-size: .57rem; color: #c9a84c; }
    .bracket-footer-tbd  { font-size: .57rem; color: #b8a07a; font-style: italic; }

    /* Banner campeón */
    .bracket-champion-banner {
      display: flex; align-items: center; justify-content: center; gap: .5rem;
      padding: .4rem .55rem;
      background: linear-gradient(135deg,#3d2600,#7a5000);
      border-top: 1px solid #c9a84c;
    }
    .bracket-champion-banner img { width: 2rem; height: 1.3rem; object-fit: cover; border-radius: 2px; box-shadow: 0 1px 4px rgba(0,0,0,.4); }
    .bracket-champion-banner span { font-size: .62rem; font-weight: 900; color: #ffe99a; text-transform: uppercase; letter-spacing: .08em; }

    /* Centro */
    .bracket-center { min-height: 930px; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: .75rem; z-index: 2; }
    .bracket-center-title,.bracket-center > div,.bracket-special { width: 100%; }
    .bracket-center-title { min-height: 2rem; display: grid; place-items: center; background: var(--color-negro); color: var(--color-oro) !important; font-size: .68rem; font-weight: 900; text-transform: uppercase; letter-spacing: .15em; border-radius: 5px 5px 0 0; }
    .bracket-trophy { display: flex; flex-direction: column; align-items: center; gap: .45rem; text-align: center; }
    .bracket-trophy-mark { width: 7rem; height: 4.6rem; display: grid; place-items: center; overflow: hidden; border: 2px solid #e0d4b8; background: var(--color-negro); color: var(--color-oro); font-size: 2rem; font-weight: 900; border-radius: 4px; }
    .bracket-trophy-flag { width: 100%; height: 100%; object-fit: cover; }
    .bracket-trophy-country { color: #fff !important; } 
    .bracket-special { background: rgba(201,168,76,.15); border: 1px solid #e0d4b8; padding: .55rem; border-radius: 6px; }
    .bracket-special-title { margin-bottom: .45rem; color: var(--color-oro) !important; font-size: .6rem; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; }
    #bracket-lines-svg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; overflow: visible; z-index: 1; }
    @media(max-width: 768px){ .bracket-board{ min-width: 1180px; grid-template-columns: 1.08fr .96fr .86fr .78fr .86fr .78fr .86fr .96fr 1.08fr; } .bracket-center{ min-height: 820px; } }
  </style>
</head>
<body class="bg-slate-950 text-white min-h-screen">
  <?php require_once __DIR__ . '/../layouts/navbar.php'; ?>
  <main class="max-w-[1800px] mx-auto px-6 py-10">
    <section class="mb-8">
      <p class="text-amber-400 font-semibold text-sm uppercase tracking-[0.25em]">Eliminatorias</p>
      <h1 class="mt-3 text-4xl md:text-5xl font-black tracking-tight">
        Mundial 2026
        <span class="bg-gradient-to-r from-yellow-600 via-amber-400 to-yellow-500 bg-clip-text text-transparent">Bracket</span>
      </h1>
    </section>
    <section class="bracket-shell overflow-hidden">
      <div class="bracket-summary px-5 py-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-2xl font-black">Camino a la final</h2>
          
        </div>
      </div>
      <div class="bracket-scroll overflow-x-auto scroll-x-soft p-5">
        <div class="bracket-board" id="bracketBoard">
          <svg id="bracket-lines-svg" aria-hidden="true"></svg>

          <?php foreach ($fasesLlave as $nombreFase => $datosFase): ?>
            <?php $pf = partidosMitadLlave($partidosPorFase[$nombreFase] ?? [], $datosFase['partidos'], 'left'); ?>
            <section class="bracket-column" data-side="left" data-fase="<?php echo htmlspecialchars($nombreFase); ?>">
              <div class="bracket-column-title"><?php echo htmlspecialchars($datosFase['titulo']); ?></div>
              <div class="bracket-column-matches">
                <?php foreach ($pf as $i => $p): renderPartidoLlave($p, $i+1, 'left'); endforeach; ?>
              </div>
            </section>
          <?php endforeach; ?>

          <section class="bracket-center" data-side="center">
            
            <div class="bracket-trophy mb-4">
              <div class="bracket-trophy-mark">
                <?php if ($banderaCampeon !== ''): ?>
                  <img src="<?php echo htmlspecialchars($banderaCampeon); ?>" alt="Campeon" class="bracket-trophy-flag">
                <?php else: ?>
                  
                <?php endif; ?>
              </div>
              <p class="bracket-trophy-country text-xs font-black uppercase mt-1">
                <?php echo $campeon !== null ? htmlspecialchars($campeon) : 'Mundial 2026'; ?>
              </p>
            </div>

            <div class="bracket-special-final">
              <div class="bracket-center-title">Final</div>
              <div><?php
                $bannerData = $campeon !== null ? ['nombre' => $campeon, 'bandera' => $banderaCampeon] : null;
                renderPartidoLlave($final, 1, 'center', $bannerData);
              ?></div>
            </div>

            <div class="bracket-special mt-4">
              <p class="bracket-special-title">Tercer lugar</p>
              <?php renderPartidoLlave($tercerLugar, 3, 'center'); ?>
            </div>
          </section>

          <?php foreach (array_reverse($fasesLlave, true) as $nombreFase => $datosFase): ?>
            <?php $pf = partidosMitadLlave($partidosPorFase[$nombreFase] ?? [], $datosFase['partidos'], 'right'); ?>
            <section class="bracket-column" data-side="right" data-fase="<?php echo htmlspecialchars($nombreFase); ?>">
              <div class="bracket-column-title"><?php echo htmlspecialchars($datosFase['titulo']); ?></div>
              <div class="bracket-column-matches">
                <?php foreach ($pf as $i => $p): renderPartidoLlave($p, $i+1, 'right'); endforeach; ?>
              </div>
            </section>
          <?php endforeach; ?>

        </div>
      </div>
    </section>
  </main>

  <?php if (!$esAdmin): ?>
    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
  <?php endif; ?>

  <script>
  (function(){
    const STROKE='var(--color-oro-oscuro,#a07830)',SW=2;
    const board=document.getElementById('bracketBoard');
    const svg=document.getElementById('bracket-lines-svg');
    const scroll=board?.closest('.bracket-scroll');
    if(!board||!svg)return;
    const br=()=>board.getBoundingClientRect();
    const rR=el=>el.getBoundingClientRect().right -br().left;
    const rL=el=>el.getBoundingClientRect().left  -br().left;
    const mY=el=>{const r=el.getBoundingClientRect();return r.top-br().top+r.height/2;};
    function ln(x1,y1,x2,y2){
      const l=document.createElementNS('http://www.w3.org/2000/svg','line');
      l.setAttribute('x1',x1);l.setAttribute('y1',y1);
      l.setAttribute('x2',x2);l.setAttribute('y2',y2);
      l.setAttribute('stroke',STROKE);l.setAttribute('stroke-width',SW);l.setAttribute('stroke-linecap','round');
      svg.appendChild(l);
    }
    function connector(tEl,bEl,dEl,side){
      const yT=mY(tEl),yB=mY(bEl),yM=(yT+yB)/2;
      if(side==='left'){
        const xO=rR(tEl),xI=rL(dEl),xE=xO+(xI-xO)*.5;
        ln(xO,yT,xE,yT);ln(xO,yB,xE,yB);ln(xE,yT,xE,yB);ln(xE,yM,xI,yM);
      }else{
        const xO=rL(tEl),xI=rR(dEl),xE=xO-(xO-xI)*.5;
        ln(xO,yT,xE,yT);ln(xO,yB,xE,yB);ln(xE,yT,xE,yB);ln(xE,yM,xI,yM);
      }
    }
    function cols(src,dst,side){
      const wA=Array.from(src.querySelectorAll('.bracket-column-matches .bracket-match-wrapper'));
      const wB=Array.from(dst.querySelectorAll('.bracket-column-matches .bracket-match-wrapper'));
      for(let i=0;i<wB.length;i++) if(wA[i*2]&&wA[i*2+1]&&wB[i]) connector(wA[i*2],wA[i*2+1],wB[i],side);
    }
    function semi(sCol,fW,side){
      const w=sCol.querySelector('.bracket-column-matches .bracket-match-wrapper');
      if(!w||!fW)return;
      const y=mY(w);
      side==='left'?ln(rR(w),y,rL(fW),y):ln(rL(w),y,rR(fW),y);
    }
    function draw(){
      svg.innerHTML='';
      const b=br();
      svg.setAttribute('viewBox',`0 0 ${b.width} ${b.height}`);
      svg.style.width=b.width+'px';svg.style.height=b.height+'px';
      const lC=Array.from(board.querySelectorAll('.bracket-column[data-side="left"]'));
      const rC=Array.from(board.querySelectorAll('.bracket-column[data-side="right"]'));
      const cS=board.querySelector('[data-side="center"]');
      const fW=cS?.querySelector('.bracket-special-final .bracket-match-wrapper'); 
      for(let i=0;i<lC.length-1;i++) cols(lC[i],lC[i+1],'left');
      for(let i=0;i<rC.length-1;i++) cols(rC[i+1],rC[i],'right');
      if(lC[lC.length-1]&&fW) semi(lC[lC.length-1],fW,'left');
      if(rC[0]&&fW) semi(rC[0],fW,'right');
    }
    window.addEventListener('load',draw);
    window.addEventListener('resize',draw);
    if(scroll)scroll.addEventListener('scroll',draw);
    if(document.readyState==='complete')requestAnimationFrame(draw);
  })();
  </script>
</body>
</html>
