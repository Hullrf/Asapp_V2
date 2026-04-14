@php $cp = $configPanel; @endphp

{{-- ── ALERTAS DE STOCK ── --}}
@if ($productosStockBajo->isNotEmpty())
<div class="card" style="border-color: #FCD34D; margin-bottom: 24px;">
    <div class="card-title" style="color: #92400E;">⚠️ Productos con stock bajo</div>
    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:4px;">
        @foreach ($productosStockBajo as $p)
            <div style="background:#FEF3C7; border:1px solid #FCD34D; border-radius:10px; padding:10px 14px; font-size:13px;">
                <div style="font-weight:700; color:#1a1a2e;">{{ $p->nombre }}</div>
                <div style="color:#92400E; margin-top:3px;">
                    {{ $p->stock === 0 ? '🔴 Agotado' : '🟡 ' . $p->stock . ' unidades (mín. ' . $p->stock_minimo . ')' }}
                </div>
                @if ($p->categoria)
                    <div style="color:#9B8EC4; font-size:11px; margin-top:2px;">{{ $p->categoria->nombre }}</div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- KPIs --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:16px;">
    <div class="card" style="margin-bottom:0;padding:14px 16px;border-color:#C4B5FD;background:#F5F3FF;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--purple);margin-bottom:4px;">Total cobrado</div>
        <div style="font-size:20px;font-weight:800;color:var(--purple);letter-spacing:-0.5px;line-height:1;">${{ number_format($resumen['total_cobrado'], 0, ',', '.') }}</div>
        <div style="font-size:10px;color:var(--text-faint);margin-top:3px;">Acumulado</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:14px 16px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Pedidos totales</div>
        <div style="font-size:20px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $resumen['total_pedidos'] }}</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:14px 16px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Pendientes</div>
        <div style="font-size:20px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $resumen['pedidos_pendientes'] }}</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:14px 16px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Parciales</div>
        <div style="font-size:20px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $resumen['pedidos_parciales'] }}</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:14px 16px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Productos</div>
        <div style="font-size:20px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $resumen['productos_activos'] }}</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:14px 16px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Mesas</div>
        <div style="font-size:20px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $resumen['mesas_total'] }}</div>
    </div>
</div>

{{-- ── GRID DE GRÁFICAS (2 por fila) ── --}}
<div class="charts-grid">

    {{-- Top 5 productos --}}
    <div class="card chart-card" data-chart="top_productos" @if(!in_array('top_productos', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></div>
                Top 5 productos más pedidos
            </div>
        </div>
        @if ($topProductos->isEmpty())
            <p class="chart-empty">Sin datos de productos aún.</p>
        @else
            <div class="chart-wrap"><canvas id="chart-productos"></canvas></div>
            <div id="chart-productos-leyenda" class="chart-leyenda"></div>
        @endif
    </div>

    {{-- Rendimiento por mesero --}}
    <div class="card chart-card" data-chart="rendimiento_mesero" @if(!in_array('rendimiento_mesero', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg></div>
                Rendimiento por mesero
            </div>
            <div id="meseros-periodos" style="display:flex; gap:6px; flex-wrap:wrap;">
                <button class="periodo-btn" data-periodo="dia">Hoy</button>
                <button class="periodo-btn active" data-periodo="semana">7 días</button>
                <button class="periodo-btn" data-periodo="mes">30 días</button>
                <button class="periodo-btn" data-periodo="anio">1 año</button>
            </div>
        </div>
        <div id="meseros-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
            Sin datos de meseros en este período.
        </div>
        <div id="meseros-contenido" style="display:none;">
            <div id="meseros-wrap" style="position:relative; height:160px;"><canvas id="chart-meseros"></canvas></div>
        </div>
    </div>

    {{-- Fuentes de pago --}}
    <div class="card chart-card" data-chart="fuentes_pago" @if(!in_array('fuentes_pago', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9z"/></svg></div>
                Fuentes de pago
            </div>
            <div id="pagos-periodos" style="display:flex; gap:6px; flex-wrap:wrap;">
                <button class="periodo-btn active" data-periodo="dia">Hoy</button>
                <button class="periodo-btn" data-periodo="semana">7 días</button>
                <button class="periodo-btn" data-periodo="mes">30 días</button>
                <button class="periodo-btn" data-periodo="anio">1 año</button>
            </div>
        </div>
        <div id="pagos-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
            Sin pagos registrados en este período.
        </div>
        <div id="pagos-contenido" style="display:none;">
            <div style="position:relative; height:220px;"><canvas id="chart-pagos-fuente"></canvas></div>
        </div>
    </div>

    {{-- Ingresos por mesa --}}
    <div class="card chart-card" data-chart="ingresos_mesa" @if(!in_array('ingresos_mesa', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z"/></svg></div>
                Ingresos por mesa
            </div>
            <div id="mesas-periodos" style="display:flex; gap:6px; flex-wrap:wrap;">
                <button class="periodo-btn active" data-periodo="dia">Hoy</button>
                <button class="periodo-btn" data-periodo="semana">7 días</button>
                <button class="periodo-btn" data-periodo="mes">30 días</button>
                <button class="periodo-btn" data-periodo="anio">1 año</button>
            </div>
        </div>
        <div id="mesas-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
            Sin ingresos registrados en este período.
        </div>
        <div id="mesas-contenido" style="display:none;">
            <div style="position:relative; height:220px;"><canvas id="chart-mesas"></canvas></div>
        </div>
    </div>

    {{-- Horas pico --}}
    <div class="card chart-card" data-chart="horas_pico" @if(!in_array('horas_pico', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg></div>
                Horas pico
            </div>
            <div id="horas-periodos" style="display:flex; gap:6px; flex-wrap:wrap;">
                <button class="periodo-btn" data-periodo="dia">Hoy</button>
                <button class="periodo-btn active" data-periodo="semana">7 días</button>
                <button class="periodo-btn" data-periodo="mes">30 días</button>
                <button class="periodo-btn" data-periodo="anio">1 año</button>
            </div>
        </div>
        <div id="horas-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
            Sin ventas registradas en este período.
        </div>
        <div id="horas-contenido" style="display:none;">
            <div style="position:relative; height:200px;"><canvas id="chart-horas"></canvas></div>
        </div>
    </div>

    {{-- Ingresos por categoría --}}
    <div class="card chart-card" data-chart="categorias" @if(!in_array('categorias', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg></div>
                Ingresos por categoría
            </div>
            <div id="categorias-periodos" style="display:flex; gap:6px; flex-wrap:wrap;">
                <button class="periodo-btn" data-periodo="dia">Hoy</button>
                <button class="periodo-btn active" data-periodo="semana">7 días</button>
                <button class="periodo-btn" data-periodo="mes">30 días</button>
                <button class="periodo-btn" data-periodo="anio">1 año</button>
            </div>
        </div>
        <div id="categorias-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
            Sin ventas registradas en este período.
        </div>
        <div id="categorias-contenido" style="display:none;">
            <div style="position:relative; height:220px;"><canvas id="chart-categorias"></canvas></div>
        </div>
    </div>

    {{-- Ticket promedio --}}
    <div class="card chart-card" data-chart="ticket_promedio" @if(!in_array('ticket_promedio', $cp)) style="display:none" @endif>
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
            <div class="card-title" style="margin:0;">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z"/></svg></div>
                Ticket promedio
            </div>
            <div id="ticket-periodos" style="display:flex; gap:6px; flex-wrap:wrap;">
                <button class="periodo-btn active" data-periodo="dia">Hoy</button>
                <button class="periodo-btn" data-periodo="semana">7 días</button>
                <button class="periodo-btn" data-periodo="mes">30 días</button>
                <button class="periodo-btn" data-periodo="anio">1 año</button>
            </div>
        </div>
        <div id="ticket-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
            Sin pedidos en este período.
        </div>
        <div id="ticket-contenido" style="display:none;">
            <div style="position:relative; height:220px;"><canvas id="chart-ticket"></canvas></div>
        </div>
    </div>

</div>{{-- /charts-grid --}}

{{-- ── STOCK (ancho completo, fuera del grid) ── --}}
@php $productosConStock = $productos->filter(fn($p) => $p->stock !== null)->sortBy('stock')->values(); @endphp
@if ($productosConStock->isNotEmpty())
<div class="card chart-card" data-chart="stock_productos" @if(!in_array('stock_productos', $cp)) style="display:none" @endif>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
        <div class="card-title" style="margin:0;">
            <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="13" height="13"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4zM3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/></svg></div>
            Stock actual por producto
        </div>
    </div>
    <div class="chart-wrap chart-wrap-wide" style="height: {{ max(200, $productosConStock->count() * 36) }}px;">
        <canvas id="chart-stock"></canvas>
    </div>
</div>
@endif

{{-- ── MODAL PERSONALIZAR ── --}}
<div class="modal-overlay" id="personalizar-overlay">
    <div class="modal" style="max-width:480px; text-align:left;">
        <h3 style="margin-bottom:4px;">Personalizar estadísticas</h3>
        <p style="font-size:13px;color:var(--text-faint);margin-bottom:20px;">Activa o desactiva las gráficas que quieres ver.</p>
        <div id="personalizar-toggles"></div>
        <div class="modal-acciones" style="margin-top:20px;">
            <button class="btn-modal-sec" onclick="cerrarPersonalizar()">Cancelar</button>
            <button class="btn-modal-pri" onclick="guardarPersonalizar()">Guardar cambios</button>
        </div>
    </div>
</div>

<style>
.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}
.charts-grid .card {
    margin-bottom: 0;
}
@media (max-width: 700px) {
    .charts-grid { grid-template-columns: 1fr; }
}

.periodo-btn {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 4px 10px;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-faint);
    cursor: pointer;
    transition: all 0.15s;
    font-family: var(--font);
}
.periodo-btn.active  { background: var(--purple); border-color: var(--purple); color: #fff; }
.periodo-btn:hover:not(.active) { border-color: var(--purple); color: var(--purple); }

.toggle-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 4px;
    border-bottom: 1px solid #F0EBF8;
    cursor: pointer;
    user-select: none;
}
.toggle-item:last-child { border-bottom: none; }
.toggle-label-text { font-size: 14px; color: #1a1a2e; }
.toggle-wrapper    { display: flex; align-items: center; flex-shrink: 0; }
.chart-toggle      { display: none; }
.toggle-switch {
    width: 40px; height: 22px;
    background: #D4C9F0; border-radius: 11px;
    position: relative; transition: background 0.2s;
}
.toggle-switch::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 16px; height: 16px; background: #fff; border-radius: 50%;
    transition: transform 0.2s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.chart-toggle:checked + .toggle-switch             { background: #6B21E8; }
.chart-toggle:checked + .toggle-switch::after      { transform: translateX(18px); }

.btn-modal-sec {
    background: #F5F3FF; border: 1.5px solid #D4C9F0; border-radius: 10px;
    padding: 10px 20px; font-size: 14px; font-weight: 600; color: #6B21E8;
    cursor: pointer; font-family: inherit;
}
.btn-modal-pri {
    background: #6B21E8; border: none; border-radius: 10px;
    padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff;
    cursor: pointer; font-family: inherit; transition: background 0.15s;
}
.btn-modal-pri:hover { background: #5B18C8; }
</style>

<script>
function initEstadisticasCharts() {
    initEstadisticasCharts = function() {};

    const habilitados = new Set(@json($configPanel));
    const iniciados   = {};
    const chartInst   = {};

    const purple  = { dk:'#3D0E8A', md:'#6B21E8', lt:'#8B5CF6', llt:'#A78BFA', pale:'#C4B5FD', bg:'#EDE9FE' };
    const palette = [purple.dk, purple.md, purple.lt, purple.llt, purple.pale, '#1D4ED8','#0891B2','#059669','#D97706','#DC2626'];
    const tooltipBase = { backgroundColor:'#1a1a2e', titleColor:'#C4B5FD', bodyColor:'#fff', cornerRadius:8, padding:10 };
    const fmtCOP = v => '$' + Number(v).toLocaleString('es-CO', { minimumFractionDigits:0 });

    // ── FUENTES DE PAGO ──────────────────────────────────────────────────
    const urlPagos = '{{ route('panel.partials.estadisticas-pagos') }}';
    const nombresMetodo = { tarjeta:'Tarjeta', pse:'PSE', nequi:'Nequi', efectivo:'Efectivo', digital:'Digital' };
    const coloresLinea  = {
        tarjeta: { line:'#3D0E8A', fill:'rgba(61,14,138,0.08)'   },
        pse:     { line:'#6B21E8', fill:'rgba(107,33,232,0.08)'  },
        nequi:   { line:'#8B5CF6', fill:'rgba(139,92,246,0.08)'  },
        efectivo:{ line:'#A78BFA', fill:'rgba(167,139,250,0.08)' },
        digital: { line:'#C4B5FD', fill:'rgba(196,181,253,0.08)' },
    };

    function cargarPagos(periodo) {
        fetch(urlPagos + '?periodo=' + periodo).then(r => r.json()).then(({ labels, series }) => {
            const empty = document.getElementById('pagos-empty'), con = document.getElementById('pagos-contenido');
            if (!series || !Object.keys(series).length) {
                empty.style.display = ''; con.style.display = 'none';
                if (chartInst.fuentes_pago) { chartInst.fuentes_pago.destroy(); chartInst.fuentes_pago = null; }
                return;
            }
            empty.style.display = 'none'; con.style.display = '';
            const datasets = Object.entries(series).map(([m, vals]) => ({
                label: nombresMetodo[m] || m, data: vals,
                borderColor: coloresLinea[m]?.line || purple.md,
                backgroundColor: coloresLinea[m]?.fill || 'rgba(107,33,232,0.08)',
                borderWidth:2, pointRadius: labels.length<=12?4:2, pointHoverRadius:6, tension:0.35, fill:false,
            }));
            if (chartInst.fuentes_pago) {
                chartInst.fuentes_pago.data.labels = labels; chartInst.fuentes_pago.data.datasets = datasets; chartInst.fuentes_pago.update();
            } else {
                chartInst.fuentes_pago = new Chart(document.getElementById('chart-pagos-fuente'), {
                    type:'line', data:{ labels, datasets },
                    options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
                        plugins:{ legend:{ position:'top', labels:{ color:'#1a1a2e', font:{ family:'Segoe UI', size:12 }, padding:16 } },
                            tooltip:{ ...tooltipBase, callbacks:{ label: ctx=>' '+ctx.dataset.label+': '+fmtCOP(ctx.raw) } } },
                        scales:{ x:{ ticks:{ color:'#9B8EC4', maxTicksLimit:10, maxRotation:30 }, grid:{ color:'#E0D9F5' } },
                            y:{ beginAtZero:true, ticks:{ color:'#9B8EC4', callback:fmtCOP }, grid:{ color:'#E0D9F5' } } } }
                });
            }
        });
    }

    // ── INGRESOS POR MESA ────────────────────────────────────────────────
    const urlMesas = '{{ route('panel.partials.estadisticas-mesas') }}';

    function cargarMesas(periodo) {
        fetch(urlMesas + '?periodo=' + periodo).then(r => r.json()).then(({ labels, series }) => {
            const empty = document.getElementById('mesas-empty'), con = document.getElementById('mesas-contenido');
            if (!series || !Object.keys(series).length) {
                empty.style.display = ''; con.style.display = 'none';
                if (chartInst.ingresos_mesa) { chartInst.ingresos_mesa.destroy(); chartInst.ingresos_mesa = null; }
                return;
            }
            empty.style.display = 'none'; con.style.display = '';
            const datasets = Object.entries(series).map(([mesa, vals], i) => ({
                label:mesa, data:vals, borderColor:palette[i%palette.length],
                backgroundColor:palette[i%palette.length]+'14',
                borderWidth:2, pointRadius:labels.length<=12?4:2, pointHoverRadius:6, tension:0.35, fill:false,
            }));
            if (chartInst.ingresos_mesa) {
                chartInst.ingresos_mesa.data.labels = labels; chartInst.ingresos_mesa.data.datasets = datasets; chartInst.ingresos_mesa.update();
            } else {
                chartInst.ingresos_mesa = new Chart(document.getElementById('chart-mesas'), {
                    type:'line', data:{ labels, datasets },
                    options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
                        plugins:{ legend:{ position:'top', labels:{ color:'#1a1a2e', font:{ family:'Segoe UI', size:12 }, padding:14 } },
                            tooltip:{ ...tooltipBase, callbacks:{ label: ctx=>' '+ctx.dataset.label+': '+fmtCOP(ctx.raw) } } },
                        scales:{ x:{ ticks:{ color:'#9B8EC4', maxTicksLimit:10, maxRotation:30 }, grid:{ color:'#E0D9F5' } },
                            y:{ beginAtZero:true, ticks:{ color:'#9B8EC4', callback:fmtCOP }, grid:{ color:'#E0D9F5' } } } }
                });
            }
        });
    }

    // ── RENDIMIENTO MESERO ───────────────────────────────────────────────
    const urlMeseros = '{{ route('panel.partials.estadisticas-meseros') }}';

    function cargarMeseros(periodo) {
        fetch(urlMeseros + '?periodo=' + periodo).then(r => r.json()).then(({ labels, data }) => {
            const empty = document.getElementById('meseros-empty'), con = document.getElementById('meseros-contenido');
            if (!labels.length) {
                empty.style.display = ''; con.style.display = 'none';
                if (chartInst.rendimiento_mesero) { chartInst.rendimiento_mesero.destroy(); chartInst.rendimiento_mesero = null; }
                return;
            }
            empty.style.display = 'none'; con.style.display = '';
            document.getElementById('meseros-wrap').style.height = Math.max(120, labels.length*52) + 'px';
            const bgColors = palette.slice(0, labels.length);
            if (chartInst.rendimiento_mesero) {
                chartInst.rendimiento_mesero.data.labels = labels;
                chartInst.rendimiento_mesero.data.datasets[0].data = data;
                chartInst.rendimiento_mesero.data.datasets[0].backgroundColor = bgColors;
                chartInst.rendimiento_mesero.update();
            } else {
                chartInst.rendimiento_mesero = new Chart(document.getElementById('chart-meseros'), {
                    type:'bar', data:{ labels, datasets:[{ label:'Ingresos generados', data, backgroundColor:bgColors, borderRadius:8, borderSkipped:false }] },
                    options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false,
                        plugins:{ legend:{ display:false }, tooltip:{ ...tooltipBase, callbacks:{ label: ctx=>' '+fmtCOP(ctx.raw) } } },
                        scales:{ x:{ beginAtZero:true, ticks:{ color:'#9B8EC4', callback:fmtCOP }, grid:{ color:'#E0D9F5' } },
                            y:{ ticks:{ color:'#1a1a2e', font:{ size:13 } }, grid:{ display:false } } } }
                });
            }
        });
    }

    // ── HORAS PICO ───────────────────────────────────────────────────────
    const urlHoras = '{{ route('panel.partials.estadisticas-horas') }}';

    function cargarHoras(periodo) {
        fetch(urlHoras + '?periodo=' + periodo).then(r => r.json()).then(({ labels, data }) => {
            const total = data.reduce((s,v)=>s+v,0);
            const empty = document.getElementById('horas-empty'), con = document.getElementById('horas-contenido');
            if (total === 0) {
                empty.style.display = ''; con.style.display = 'none';
                if (chartInst.horas_pico) { chartInst.horas_pico.destroy(); chartInst.horas_pico = null; }
                return;
            }
            empty.style.display = 'none'; con.style.display = '';
            const maxVal = Math.max(...data);
            const bgColors = data.map(v => `rgba(107,33,232,${(0.15+(maxVal>0?v/maxVal:0)*0.75).toFixed(2)})`);
            if (chartInst.horas_pico) {
                chartInst.horas_pico.data.datasets[0].data = data;
                chartInst.horas_pico.data.datasets[0].backgroundColor = bgColors;
                chartInst.horas_pico.update();
            } else {
                chartInst.horas_pico = new Chart(document.getElementById('chart-horas'), {
                    type:'bar', data:{ labels, datasets:[{ label:'Ingresos', data, backgroundColor:bgColors, borderRadius:4, borderSkipped:false }] },
                    options:{ responsive:true, maintainAspectRatio:false,
                        plugins:{ legend:{ display:false }, tooltip:{ ...tooltipBase, callbacks:{ label: ctx=>' '+fmtCOP(ctx.raw) } } },
                        scales:{ x:{ ticks:{ color:'#9B8EC4', font:{ size:9 }, maxRotation:0 }, grid:{ display:false } },
                            y:{ beginAtZero:true, ticks:{ color:'#9B8EC4', callback:fmtCOP }, grid:{ color:'#E0D9F5' } } } }
                });
            }
        });
    }

    // ── INGRESOS POR CATEGORÍA ───────────────────────────────────────────
    const urlCategorias = '{{ route('panel.partials.estadisticas-categorias') }}';

    function cargarCategorias(periodo) {
        fetch(urlCategorias + '?periodo=' + periodo).then(r => r.json()).then(({ labels, data }) => {
            const total = data.reduce((s,v)=>s+v,0);
            const empty = document.getElementById('categorias-empty'), con = document.getElementById('categorias-contenido');
            if (!labels.length || total === 0) {
                empty.style.display = ''; con.style.display = 'none';
                if (chartInst.categorias) { chartInst.categorias.destroy(); chartInst.categorias = null; }
                return;
            }
            empty.style.display = 'none'; con.style.display = '';
            const bgColors = palette.slice(0, labels.length);
            if (chartInst.categorias) {
                chartInst.categorias.data.labels = labels;
                chartInst.categorias.data.datasets[0].data = data;
                chartInst.categorias.data.datasets[0].backgroundColor = bgColors;
                chartInst.categorias.update();
            } else {
                chartInst.categorias = new Chart(document.getElementById('chart-categorias'), {
                    type:'doughnut', data:{ labels, datasets:[{ data, backgroundColor:bgColors, borderWidth:2, borderColor:'#fff' }] },
                    options:{ responsive:true, maintainAspectRatio:false,
                        plugins:{ legend:{ position:'right', labels:{ color:'#1a1a2e', font:{ family:'Segoe UI', size:11 }, padding:12, boxWidth:12 } },
                            tooltip:{ ...tooltipBase, callbacks:{ label: ctx=>' '+ctx.label+': '+fmtCOP(ctx.raw) } } } }
                });
            }
        });
    }

    // ── TICKET PROMEDIO ──────────────────────────────────────────────────
    const urlTicket = '{{ route('panel.partials.estadisticas-ticket') }}';

    function cargarTicket(periodo) {
        fetch(urlTicket + '?periodo=' + periodo).then(r => r.json()).then(({ labels, data }) => {
            const total = data.filter(v=>v!==null).reduce((s,v)=>s+v,0);
            const empty = document.getElementById('ticket-empty'), con = document.getElementById('ticket-contenido');
            if (total === 0) {
                empty.style.display = ''; con.style.display = 'none';
                if (chartInst.ticket_promedio) { chartInst.ticket_promedio.destroy(); chartInst.ticket_promedio = null; }
                return;
            }
            empty.style.display = 'none'; con.style.display = '';
            const datasets = [{ label:'Ticket promedio', data,
                borderColor:purple.md, backgroundColor:'rgba(107,33,232,0.08)',
                borderWidth:2, pointRadius:labels.length<=12?4:2, pointHoverRadius:6, tension:0.35, fill:true, spanGaps:true }];
            if (chartInst.ticket_promedio) {
                chartInst.ticket_promedio.data.labels = labels; chartInst.ticket_promedio.data.datasets = datasets; chartInst.ticket_promedio.update();
            } else {
                chartInst.ticket_promedio = new Chart(document.getElementById('chart-ticket'), {
                    type:'line', data:{ labels, datasets },
                    options:{ responsive:true, maintainAspectRatio:false, interaction:{ mode:'index', intersect:false },
                        plugins:{ legend:{ display:false }, tooltip:{ ...tooltipBase, callbacks:{ label: ctx=>' Ticket: '+fmtCOP(ctx.raw) } } },
                        scales:{ x:{ ticks:{ color:'#9B8EC4', maxTicksLimit:10, maxRotation:30 }, grid:{ color:'#E0D9F5' } },
                            y:{ beginAtZero:true, ticks:{ color:'#9B8EC4', callback:fmtCOP }, grid:{ color:'#E0D9F5' } } } }
                });
            }
        });
    }

    // ── TOP PRODUCTOS ────────────────────────────────────────────────────
    @if ($topProductos->isNotEmpty())
    (function() {
        const data   = @json($topProductos);
        const values = data.map(d => d.cantidad);
        const colors = [purple.dk, purple.md, purple.lt, purple.llt, purple.pale];
        chartInst['top_productos'] = new Chart(document.getElementById('chart-productos'), {
            type:'bar',
            data:{ labels: data.map((_,i)=>i+1), datasets:[{ label:'Unidades pedidas', data:values, backgroundColor:colors.slice(0,values.length), borderRadius:8, borderSkipped:false }] },
            options:{ responsive:true, maintainAspectRatio:true,
                plugins:{ legend:{ display:false }, tooltip:{ ...tooltipBase } },
                scales:{ x:{ ticks:{ color:'#9B8EC4' }, grid:{ display:false } },
                    y:{ ticks:{ color:'#9B8EC4', precision:0 }, grid:{ color:'#E0D9F5' }, beginAtZero:true } } }
        });
        const leyenda = document.getElementById('chart-productos-leyenda');
        if (leyenda) {
            leyenda.innerHTML = data.map((d,i) => `
                <div class="leyenda-item">
                    <span class="leyenda-dot" style="background:${colors[i]};"></span>
                    <span class="leyenda-num">${i+1}</span>
                    <span class="leyenda-nombre">${d.nombre}</span>
                    <span class="leyenda-cant">${d.cantidad} ud.</span>
                </div>`).join('');
        }
        iniciados['top_productos'] = true;
    })();
    @endif

    // ── STOCK ────────────────────────────────────────────────────────────
    @php $productosConStock = $productos->filter(fn($p) => $p->stock !== null)->sortBy('stock')->values(); @endphp
    @if ($productosConStock->isNotEmpty())
    (function() {
        const prods   = @json($productosConStock->map(fn($p)=>['nombre'=>$p->nombre,'stock'=>(int)$p->stock,'minimo'=>(int)$p->stock_minimo])->values());
        const labels  = prods.map(p=>p.nombre), stocks = prods.map(p=>p.stock), minimos = prods.map(p=>p.minimo);
        const colors  = stocks.map((s,i)=>s===0?'#FEE2E2':s<=minimos[i]?'#FEF3C7':purple.bg);
        const borders = stocks.map((s,i)=>s===0?'#FECACA':s<=minimos[i]?'#FCD34D':purple.pale);
        chartInst['stock_productos'] = new Chart(document.getElementById('chart-stock'), {
            type:'bar',
            data:{ labels, datasets:[
                { label:'Stock actual', data:stocks, backgroundColor:colors, borderColor:borders, borderWidth:1, borderRadius:6, borderSkipped:false },
                { label:'Mínimo', data:minimos, type:'line', borderColor:'#C8102E', borderWidth:1.5, borderDash:[4,4], pointRadius:0, fill:false, tension:0 },
            ]},
            options:{ indexAxis:'y', responsive:true, maintainAspectRatio:false,
                plugins:{ legend:{ labels:{ color:'#1a1a2e', font:{ size:11 } } }, tooltip:{ ...tooltipBase } },
                scales:{ x:{ ticks:{ color:'#9B8EC4', precision:0 }, grid:{ color:'#E0D9F5' }, beginAtZero:true },
                    y:{ ticks:{ color:'#1a1a2e', font:{ size:11 } }, grid:{ display:false } } } }
        });
        iniciados['stock_productos'] = true;
    })();
    @endif

    // ── INICIALIZAR AJAX HABILITADOS ─────────────────────────────────────
    function iniciarChart(id) {
        if (iniciados[id]) return;
        iniciados[id] = true;
        if      (id === 'fuentes_pago')       cargarPagos('dia');
        else if (id === 'ingresos_mesa')      cargarMesas('dia');
        else if (id === 'rendimiento_mesero') cargarMeseros('semana');
        else if (id === 'horas_pico')         cargarHoras('semana');
        else if (id === 'categorias')         cargarCategorias('semana');
        else if (id === 'ticket_promedio')    cargarTicket('dia');
        else if (chartInst[id])               chartInst[id].resize();
    }

    habilitados.forEach(id => iniciarChart(id));

    // ── BOTONES DE PERÍODO ───────────────────────────────────────────────
    function setupPeriodos(groupId, fn) {
        document.querySelectorAll('#' + groupId + ' .periodo-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#' + groupId + ' .periodo-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                fn(this.dataset.periodo);
            });
        });
    }
    setupPeriodos('pagos-periodos',      cargarPagos);
    setupPeriodos('mesas-periodos',      cargarMesas);
    setupPeriodos('meseros-periodos',    cargarMeseros);
    setupPeriodos('horas-periodos',      cargarHoras);
    setupPeriodos('categorias-periodos', cargarCategorias);
    setupPeriodos('ticket-periodos',     cargarTicket);

    // ── PERSONALIZAR ─────────────────────────────────────────────────────
    const CHARTS_META = [
        { id:'top_productos',      label:'Top 5 productos más pedidos' },
        { id:'rendimiento_mesero', label:'Rendimiento por mesero' },
        { id:'fuentes_pago',       label:'Fuentes de pago' },
        { id:'ingresos_mesa',      label:'Ingresos por mesa' },
        { id:'horas_pico',         label:'Horas pico' },
        { id:'categorias',         label:'Ingresos por categoría' },
        { id:'ticket_promedio',    label:'Ticket promedio' },
        { id:'stock_productos',    label:'Stock actual' },
    ];

    window.abrirPersonalizar = function() {
        document.getElementById('personalizar-toggles').innerHTML = CHARTS_META.map(c => `
            <label class="toggle-item">
                <span class="toggle-label-text">${c.label}</span>
                <span class="toggle-wrapper">
                    <input type="checkbox" class="chart-toggle" value="${c.id}" ${habilitados.has(c.id)?'checked':''}>
                    <span class="toggle-switch"></span>
                </span>
            </label>`).join('');
        document.getElementById('personalizar-overlay').classList.add('open');
    };

    window.cerrarPersonalizar = function() {
        document.getElementById('personalizar-overlay').classList.remove('open');
    };

    window.guardarPersonalizar = function() {
        const nuevos = new Set([...document.querySelectorAll('.chart-toggle:checked')].map(c => c.value));

        CHARTS_META.forEach(({ id }) => {
            const block = document.querySelector('[data-chart="' + id + '"]');
            if (!block) return;
            if (nuevos.has(id) && !habilitados.has(id))  block.style.display = '';
            else if (!nuevos.has(id) && habilitados.has(id)) block.style.display = 'none';
        });

        nuevos.forEach(id => { if (!habilitados.has(id)) iniciarChart(id); });

        habilitados.clear();
        nuevos.forEach(id => habilitados.add(id));

        fetch('{{ route('panel.config-panel.save') }}', {
            method:'POST',
            headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' },
            body: JSON.stringify({ charts:[...nuevos] }),
        });

        cerrarPersonalizar();
    };

    document.getElementById('personalizar-overlay').addEventListener('click', function(e) {
        if (e.target === this) cerrarPersonalizar();
    });
}
</script>
