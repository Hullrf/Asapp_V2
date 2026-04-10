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

{{-- ── TARJETAS RESUMEN ── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🧾</div>
        <div class="stat-valor">{{ $resumen['total_pedidos'] }}</div>
        <div class="stat-label">Pedidos totales</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-valor">${{ number_format($resumen['total_cobrado'], 0, ',', '.') }}</div>
        <div class="stat-label">Total cobrado</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-valor">{{ $resumen['productos_activos'] }}</div>
        <div class="stat-label">Productos activos</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🪑</div>
        <div class="stat-valor">{{ $resumen['mesas_total'] }}</div>
        <div class="stat-label">Mesas registradas</div>
    </div>
</div>

{{-- ── FILA DE GRÁFICOS ── --}}
<div class="charts-row">

    {{-- Donut: pedidos por estado --}}
    <div class="card chart-card">
        <div class="card-title">📊 Pedidos por estado</div>
        @if ($pedidosPorEstado->isEmpty())
            <p class="chart-empty">Sin pedidos registrados aún.</p>
        @else
            <div class="chart-wrap"><canvas id="chart-estados"></canvas></div>
        @endif
    </div>

    {{-- Barras: top productos --}}
    <div class="card chart-card">
        <div class="card-title">🏆 Top 5 productos más pedidos</div>
        @if ($topProductos->isEmpty())
            <p class="chart-empty">Sin datos de productos aún.</p>
        @else
            <div class="chart-wrap"><canvas id="chart-productos"></canvas></div>
        @endif
    </div>

</div>

{{-- ── TRAZABILIDAD DE PAGOS ── --}}
<div class="card" style="margin-bottom:0;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
        <div class="card-title" style="margin:0;">💳 Fuentes de pago</div>
        <div id="pagos-periodos" style="display:flex; gap:6px;">
            <button class="periodo-btn active" data-periodo="dia">Hoy</button>
            <button class="periodo-btn" data-periodo="semana">7 días</button>
            <button class="periodo-btn" data-periodo="mes">30 días</button>
            <button class="periodo-btn" data-periodo="anio">1 año</button>
        </div>
    </div>

    <div id="pagos-empty" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:14px;">
        Sin pagos registrados en este período.
    </div>

    <div id="pagos-contenido" style="display:flex; gap:24px; align-items:center; flex-wrap:wrap;">
        <div style="flex:0 0 200px; max-width:200px;">
            <canvas id="chart-pagos-fuente"></canvas>
        </div>
        <div id="pagos-tabla" style="flex:1; min-width:180px;"></div>
    </div>
</div>

<style>
.periodo-btn {
    background: #F5F3FF;
    border: 1.5px solid #E0D9F5;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #9B8EC4;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}
.periodo-btn.active {
    background: #EDE9FE;
    border-color: #6B21E8;
    color: #6B21E8;
}
.periodo-btn:hover:not(.active) {
    border-color: #C4B5FD;
    color: #5B21B6;
}
</style>

{{-- Barras horizontales: ingresos por mesa --}}
@if ($ingresosPorMesa->isNotEmpty())
<div class="card">
    <div class="card-title">💵 Ingresos cobrados por mesa</div>
    <div class="chart-wrap chart-wrap-wide"><canvas id="chart-mesas"></canvas></div>
</div>
@endif

@php
    $productosConStock = $productos->filter(fn($p) => $p->stock !== null)->sortBy('stock')->values();
@endphp
@if ($productosConStock->isNotEmpty())
<div class="card" style="margin-top:0;">
    <div class="card-title">📦 Stock actual por producto</div>
    <div class="chart-wrap chart-wrap-wide" style="height: {{ max(200, $productosConStock->count() * 36) }}px;">
        <canvas id="chart-stock"></canvas>
    </div>
</div>
@endif

<script>
function initEstadisticasCharts() {
    // Guard: solo inicializar una vez
    initEstadisticasCharts = function() {};

    // ── Trazabilidad de pagos ────────────────────────────────────────────
    (function() {
        const url = '{{ route('panel.partials.estadisticas-pagos') }}';

        const labels   = { tarjeta: 'Tarjeta', pse: 'PSE', nequi: 'Nequi', efectivo: 'Efectivo', digital: 'Digital (legado)' };
        const colores  = { tarjeta: '#3D0E8A', pse: '#6B21E8', nequi: '#8B5CF6', efectivo: '#A78BFA', digital: '#C4B5FD' };
        const iconos   = { tarjeta: '💳', pse: '🏦', nequi: '📱', efectivo: '💵', digital: '🔷' };

        let chartPagos = null;

        function cargarPagos(periodo) {
            fetch(url + '?periodo=' + periodo)
                .then(r => r.json())
                .then(data => {
                    const activos = Object.entries(data).filter(([, v]) => v.total > 0);

                    const empty   = document.getElementById('pagos-empty');
                    const contenido = document.getElementById('pagos-contenido');

                    if (activos.length === 0) {
                        empty.style.display = '';
                        contenido.style.display = 'none';
                        return;
                    }

                    empty.style.display = 'none';
                    contenido.style.display = 'flex';

                    const keys   = activos.map(([k]) => k);
                    const totals = activos.map(([, v]) => v.total);
                    const counts = activos.map(([, v]) => v.cantidad);
                    const bgColors = keys.map(k => colores[k] || '#9B8EC4');

                    // Donut
                    if (chartPagos) {
                        chartPagos.data.labels   = keys.map(k => labels[k] || k);
                        chartPagos.data.datasets[0].data            = totals;
                        chartPagos.data.datasets[0].backgroundColor = bgColors;
                        chartPagos.update();
                    } else {
                        chartPagos = new Chart(document.getElementById('chart-pagos-fuente'), {
                            type: 'doughnut',
                            data: {
                                labels: keys.map(k => labels[k] || k),
                                datasets: [{
                                    data: totals,
                                    backgroundColor: bgColors,
                                    borderColor: '#fff',
                                    borderWidth: 3,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: '#1a1a2e',
                                        titleColor: '#C4B5FD',
                                        bodyColor: '#fff',
                                        cornerRadius: 8,
                                        padding: 10,
                                        callbacks: {
                                            label: ctx => ' $' + Number(ctx.raw).toLocaleString('es-CO', {minimumFractionDigits:0})
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Tabla lateral
                    const totalGeneral = totals.reduce((a, b) => a + b, 0);
                    let html = '<table style="width:100%; border-collapse:collapse; font-size:13px;">';
                    keys.forEach((k, i) => {
                        const pct = totalGeneral > 0 ? Math.round((totals[i] / totalGeneral) * 100) : 0;
                        html += `<tr style="border-bottom:1px solid #F0EBF8;">
                            <td style="padding:8px 6px; color:#1a1a2e;">
                                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:${bgColors[i]}; margin-right:7px;"></span>
                                ${iconos[k] || ''} ${labels[k] || k}
                            </td>
                            <td style="padding:8px 6px; color:#6B21E8; font-weight:700; text-align:right;">
                                $${Number(totals[i]).toLocaleString('es-CO', {minimumFractionDigits:0})}
                            </td>
                            <td style="padding:8px 6px; color:#9B8EC4; text-align:right; font-size:11px;">
                                ${counts[i]} pago${counts[i] !== 1 ? 's' : ''} · ${pct}%
                            </td>
                        </tr>`;
                    });
                    html += `<tr>
                        <td style="padding:10px 6px; font-weight:700; color:#1a1a2e;">Total</td>
                        <td style="padding:10px 6px; font-weight:800; color:#3D0E8A; text-align:right;">
                            $${Number(totalGeneral).toLocaleString('es-CO', {minimumFractionDigits:0})}
                        </td>
                        <td></td>
                    </tr>`;
                    html += '</table>';
                    document.getElementById('pagos-tabla').innerHTML = html;
                });
        }

        // Inicializar con período por defecto
        cargarPagos('dia');

        // Botones de período
        document.querySelectorAll('#pagos-periodos .periodo-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('#pagos-periodos .periodo-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                cargarPagos(this.dataset.periodo);
            });
        });
    })();


    const purple = {
        dk:  '#3D0E8A',
        md:  '#6B21E8',
        lt:  '#8B5CF6',
        llt: '#A78BFA',
        pale:'#C4B5FD',
        bg:  '#EDE9FE',
    };

    const baseOpts = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: { color: '#1a1a2e', font: { family: 'Segoe UI', size: 12 } }
            },
            tooltip: {
                backgroundColor: '#1a1a2e',
                titleColor: '#C4B5FD',
                bodyColor: '#fff',
                cornerRadius: 8,
                padding: 10,
            }
        },
        scales: {
            x: { ticks: { color: '#9B8EC4' }, grid: { color: '#E0D9F5' } },
            y: { ticks: { color: '#9B8EC4' }, grid: { color: '#E0D9F5' } },
        }
    };

    // ── Donut: pedidos por estado ────────────────────────────────────────
    @if ($pedidosPorEstado->isNotEmpty())
    (function() {
        const data = @json($pedidosPorEstado);
        const labels = Object.keys(data);
        const values = Object.values(data);

        const colorMap = { Pendiente: purple.pale, Parcial: purple.lt, Pagado: purple.dk };
        const colors   = labels.map(l => colorMap[l] || purple.md);

        new Chart(document.getElementById('chart-estados'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data: values, backgroundColor: colors, borderColor: '#fff', borderWidth: 3 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#1a1a2e', font: { family: 'Segoe UI', size: 12 }, padding: 16 }
                    },
                    tooltip: baseOpts.plugins.tooltip,
                }
            }
        });
    })();
    @endif

    // ── Barras: top productos ────────────────────────────────────────────
    @if ($topProductos->isNotEmpty())
    (function() {
        const data     = @json($topProductos);
        const labels   = data.map(d => d.nombre);
        const values   = data.map(d => d.cantidad);
        const colors   = [purple.dk, purple.md, purple.lt, purple.llt, purple.pale];

        new Chart(document.getElementById('chart-productos'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Unidades pedidas',
                    data: values,
                    backgroundColor: colors.slice(0, labels.length),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                ...baseOpts,
                plugins: { ...baseOpts.plugins, legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#9B8EC4', maxRotation: 30 }, grid: { display: false } },
                    y: { ticks: { color: '#9B8EC4', precision: 0 }, grid: { color: '#E0D9F5' }, beginAtZero: true },
                }
            }
        });
    })();
    @endif

    // ── Barras horizontales: ingresos por mesa ───────────────────────────
    @if ($ingresosPorMesa->isNotEmpty())
    (function() {
        const raw    = @json($ingresosPorMesa);
        const labels = Object.keys(raw);
        const values = Object.values(raw);

        new Chart(document.getElementById('chart-mesas'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Ingresos ($)',
                    data: values,
                    backgroundColor: purple.md,
                    hoverBackgroundColor: purple.dk,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: { ...baseOpts.plugins, legend: { display: false } },
                scales: {
                    x: {
                        ticks: {
                            color: '#9B8EC4',
                            callback: v => '$' + Number(v).toLocaleString('es-CO'),
                        },
                        grid: { color: '#E0D9F5' },
                        beginAtZero: true,
                    },
                    y: { ticks: { color: '#1a1a2e' }, grid: { display: false } },
                }
            }
        });
    })();
    @endif

    // ── Barras: stock por producto ────────────────────────────────────────
    @php $productosConStock = $productos->filter(fn($p) => $p->stock !== null)->sortBy('stock')->values(); @endphp
    @if ($productosConStock->isNotEmpty())
    (function() {
        const prods = @json($productosConStock->map(fn($p) => ['nombre' => $p->nombre, 'stock' => (int)$p->stock, 'minimo' => (int)$p->stock_minimo])->values());

        const labels  = prods.map(p => p.nombre);
        const stocks  = prods.map(p => p.stock);
        const minimos = prods.map(p => p.minimo);
        const colors  = stocks.map((s, i) =>
            s === 0         ? '#FEE2E2' :
            s <= minimos[i] ? '#FEF3C7' : purple.bg
        );
        const borders = stocks.map((s, i) =>
            s === 0         ? '#FECACA' :
            s <= minimos[i] ? '#FCD34D' : purple.pale
        );

        new Chart(document.getElementById('chart-stock'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Stock actual',
                        data: stocks,
                        backgroundColor: colors,
                        borderColor: borders,
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Mínimo',
                        data: minimos,
                        type: 'line',
                        borderColor: '#C8102E',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        pointRadius: 0,
                        fill: false,
                        tension: 0,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#1a1a2e', font: { size: 11 } } },
                    tooltip: baseOpts.plugins.tooltip,
                },
                scales: {
                    x: { ticks: { color: '#9B8EC4', precision: 0 }, grid: { color: '#E0D9F5' }, beginAtZero: true },
                    y: { ticks: { color: '#1a1a2e', font: { size: 11 } }, grid: { display: false } },
                }
            }
        });
    })();
    @endif
}
</script>
