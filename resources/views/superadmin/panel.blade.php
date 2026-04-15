<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin — ASAPP</title>
    <link rel="stylesheet" href="/css/asapp-base.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: var(--font-sans); background: #0f0720; color: #e2d9f3; min-height: 100vh; }

        /* ── TOPBAR ── */
        .topbar {
            background: #1a0f35;
            border-bottom: 1px solid #3D0E8A;
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo { font-size: 22px; font-weight: 900; letter-spacing: -1px; background: linear-gradient(135deg, #C4A0FF, #fff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .badge-sa { background: #3D0E8A; color: #C4A0FF; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; letter-spacing: 1px; text-transform: uppercase; margin-left: 10px; }
        .btn-logout { background: rgba(107,33,232,0.2); color: #C4A0FF; border: 1px solid rgba(107,33,232,0.4); padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-logout:hover { background: rgba(107,33,232,0.35); }
        .btn-backup { background: rgba(34,197,94,0.12); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); padding: 7px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-backup:hover { background: rgba(34,197,94,0.25); }

        /* ── CONTENIDO ── */
        .container { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }

        .page-title { font-size: 22px; font-weight: 800; color: #fff; margin-bottom: 6px; }
        .page-sub   { font-size: 13px; color: #7C3AED; margin-bottom: 28px; }

        /* ── STATS ── */
        .stats { display: flex; gap: 16px; margin-bottom: 32px; flex-wrap: wrap; }
        .stat-card { background: #1a0f35; border: 1px solid #3D0E8A; border-radius: 14px; padding: 20px 24px; flex: 1; min-width: 150px; }
        .stat-val  { font-size: 28px; font-weight: 800; color: #C4A0FF; }
        .stat-lbl  { font-size: 11px; color: #7C3AED; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        /* ── TABLA ── */
        .card { background: #1a0f35; border: 1px solid #3D0E8A; border-radius: 16px; overflow: hidden; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #3D0E8A; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
        .card-title { font-size: 15px; font-weight: 700; color: #fff; }

        input[type="text"].buscador {
            background: #0f0720;
            border: 1.5px solid #3D0E8A;
            border-radius: 8px;
            color: #e2d9f3;
            padding: 7px 12px;
            font-size: 13px;
            font-family: inherit;
            width: 220px;
            transition: border-color 0.2s;
        }
        input[type="text"].buscador:focus { outline: none; border-color: #7C3AED; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { background: #3D0E8A; color: #C4A0FF; padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; }
        tbody tr { border-bottom: 1px solid rgba(61,14,138,0.4); transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(107,33,232,0.08); }
        tbody td { padding: 14px 16px; vertical-align: middle; }

        .negocio-nombre { font-weight: 700; color: #fff; }
        .negocio-email  { font-size: 12px; color: #7C3AED; margin-top: 2px; }
        .chip { display: inline-block; background: rgba(107,33,232,0.2); color: #C4A0FF; border: 1px solid rgba(107,33,232,0.4); border-radius: 20px; padding: 2px 10px; font-size: 12px; font-weight: 600; }
        .chip-muted { background: rgba(255,255,255,0.05); color: #7C3AED; border-color: rgba(255,255,255,0.1); }

        .acciones { display: flex; gap: 8px; }
        .btn-edit { background: rgba(107,33,232,0.2); color: #C4A0FF; border: 1px solid rgba(107,33,232,0.4); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-edit:hover { background: rgba(107,33,232,0.4); }
        .btn-del  { background: rgba(200,16,46,0.15); color: #f87171; border: 1px solid rgba(200,16,46,0.3); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-del:hover { background: rgba(200,16,46,0.3); }
        .btn-stats { background: rgba(6,182,212,0.15); color: #67e8f9; border: 1px solid rgba(6,182,212,0.3); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-stats:hover { background: rgba(6,182,212,0.3); }
        .btn-suspend   { background: rgba(234,179,8,0.15); color: #fbbf24; border: 1px solid rgba(234,179,8,0.3); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-suspend:hover { background: rgba(234,179,8,0.3); }
        .btn-reactivar { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-reactivar:hover { background: rgba(34,197,94,0.3); }
        .badge-suspendido { background: rgba(200,16,46,0.2); color: #f87171; border: 1px solid rgba(200,16,46,0.3); border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 700; }
        .badge-activo     { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.3); border-radius: 20px; padding: 2px 10px; font-size: 11px; font-weight: 700; }

        /* ── MODAL ── */
        .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; }
        .overlay.open { display: flex; }
        .modal { background: #1a0f35; border: 1px solid #3D0E8A; border-radius: 20px; padding: 36px; width: 100%; max-width: 420px; box-shadow: 0 24px 64px rgba(107,33,232,0.3); }
        .modal h3 { font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 20px; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9B8EC4; margin-bottom: 6px; }
        .field input { width: 100%; padding: 10px 12px; background: #0f0720; border: 1.5px solid #3D0E8A; border-radius: 10px; color: #fff; font-size: 14px; font-family: inherit; transition: border-color 0.2s; }
        .field input:focus { outline: none; border-color: #7C3AED; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
        .btn-cancel { background: rgba(255,255,255,0.06); color: #9B8EC4; border: 1px solid rgba(255,255,255,0.1); padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-save   { background: #6B21E8; color: #fff; border: none; padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-save:hover { background: #7C3AED; }

        /* ── TOAST ── */
        #toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; opacity: 0; transform: translateY(8px); transition: all 0.25s; pointer-events: none; z-index: 999; max-width: 360px; }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.ok  { background: #1a2e1a; color: #4ade80; border: 1px solid #166534; }
        #toast.err { background: #2e1a1a; color: #f87171; border: 1px solid #991b1b; pointer-events: all; cursor: pointer; }

        /* ── MODAL MÉTRICAS ── */
        .overlay-stats { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 300; overflow-y: auto; padding: 24px 16px; }
        .overlay-stats.open { display: block; }
        .modal-stats {
            background: #1a0f35;
            border: 1px solid #3D0E8A;
            border-radius: 20px;
            max-width: 860px;
            margin: 0 auto;
            padding: 32px;
            box-shadow: 0 24px 80px rgba(107,33,232,0.4);
        }
        .stats-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
        .stats-title { font-size: 20px; font-weight: 800; color: #fff; }
        .stats-subtitle { font-size: 12px; color: #7C3AED; margin-top: 3px; }
        .stats-header-btns { display: flex; gap: 8px; align-items: center; }
        .btn-pdf { background: #6B21E8; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.2s; font-family: inherit; }
        .btn-pdf:hover { background: #7C3AED; }
        .btn-close-stats { background: rgba(255,255,255,0.07); color: #9B8EC4; border: 1px solid rgba(255,255,255,0.1); padding: 8px 14px; border-radius: 8px; font-size: 13px; cursor: pointer; font-family: inherit; }
        .btn-close-stats:hover { background: rgba(255,255,255,0.12); }

        .resumen-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .resumen-card { background: #0f0720; border: 1px solid #3D0E8A; border-radius: 12px; padding: 16px; text-align: center; }
        .resumen-val { font-size: 24px; font-weight: 800; color: #C4A0FF; }
        .resumen-lbl { font-size: 11px; color: #7C3AED; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        .charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .chart-box { background: #0f0720; border: 1px solid #3D0E8A; border-radius: 14px; padding: 20px; }
        .chart-box-title { font-size: 12px; font-weight: 700; color: #C4A0FF; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px; }
        .chart-box canvas { max-height: 200px; }
        .chart-empty-sa { text-align: center; color: #3D0E8A; font-size: 13px; padding: 32px 0; }

        .fuentes-tabla { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 8px; }
        .fuentes-tabla td { padding: 7px 4px; border-bottom: 1px solid rgba(61,14,138,0.3); color: #e2d9f3; }
        .fuentes-tabla td:last-child { text-align: right; color: #C4A0FF; font-weight: 700; border-bottom: none; }
        .fuentes-tabla tr:last-child td { border-bottom: none; }

        .stats-loading { text-align: center; padding: 60px 0; color: #7C3AED; font-size: 14px; }

        @media (max-width: 640px) {
            .modal-stats { padding: 20px 16px; }
            .resumen-grid { grid-template-columns: 1fr 1fr; }
            .charts-grid  { grid-template-columns: 1fr; }
        }

        /* ── ESTILOS DE IMPRESIÓN ── */
        @media print {
            body { background: #fff !important; color: #000 !important; }
            .topbar, .stats, .card, #toast, .overlay-stats .stats-header-btns { display: none !important; }
            .overlay-stats { display: block !important; position: static !important; padding: 0 !important; background: transparent !important; }
            .modal-stats { border: none !important; box-shadow: none !important; background: #fff !important; color: #000 !important; max-width: 100% !important; padding: 0 !important; }
            .resumen-card { border: 1px solid #ccc !important; background: #f9f9f9 !important; }
            .resumen-val { color: #3D0E8A !important; }
            .resumen-lbl, .stats-subtitle { color: #555 !important; }
            .stats-title { color: #000 !important; }
            .chart-box { border: 1px solid #ccc !important; background: #fff !important; page-break-inside: avoid; }
            .chart-box-title { color: #3D0E8A !important; }
            .btn-pdf, .btn-close-stats { display: none !important; }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 640px) {
            .topbar { padding: 0 14px; }
            .badge-sa { display: none; }
            .container { padding: 20px 14px; }
            .card-header { flex-wrap: wrap; gap: 10px; }
            input[type="text"].buscador { width: 100%; }
            .stats { gap: 10px; }
            .stat-card { padding: 16px; min-width: 120px; }
            .stat-val { font-size: 24px; }
            .acciones { flex-wrap: wrap; }
            #toast { right: 12px; left: 12px; max-width: none; }
        }

        @media (max-width: 400px) {
            .topbar { height: 54px; }
            .logo { font-size: 18px; }
            .btn-logout { padding: 6px 10px; font-size: 12px; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <div style="display:flex; align-items:center;">
        <div class="logo">ASAPP</div>
        <span class="badge-sa">Superadmin</span>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <a href="{{ route('superadmin.backup') }}" class="btn-backup">⬇ Backup DB</a>
        <a href="{{ route('superadmin.logout') }}" class="btn-logout">Cerrar sesión</a>
    </div>
</div>

<div class="container">
    <div class="page-title">Panel de control</div>
    <div class="page-sub">Gestión global de negocios registrados en ASAPP</div>

    {{-- Stats --}}
    <div class="stats">
        <div class="stat-card">
            <div class="stat-val">{{ $negocios->count() }}</div>
            <div class="stat-lbl">Negocios registrados</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $negocios->sum('productos_count') }}</div>
            <div class="stat-lbl">Productos en total</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $negocios->sum('mesas_count') }}</div>
            <div class="stat-lbl">Mesas en total</div>
        </div>
        <div class="stat-card">
            <div class="stat-val">{{ $negocios->sum('pedidos_count') }}</div>
            <div class="stat-lbl">Pedidos en total</div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Negocios</div>
            <input type="text" class="buscador" placeholder="🔍 Buscar negocio..." oninput="filtrar(this.value)">
        </div>
        <div style="overflow-x:auto;">
        <table id="tabla-negocios">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Negocio</th>
                    <th>Admin</th>
                    <th>Teléfono</th>
                    <th style="text-align:center">Estado</th>
                    <th class="center" style="text-align:center">Productos</th>
                    <th class="center" style="text-align:center">Mesas</th>
                    <th class="center" style="text-align:center">Pedidos</th>
                    <th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($negocios as $i => $negocio)
                @php $admin = $negocio->administradores->first(); @endphp
                <tr data-busqueda="{{ strtolower($negocio->nombre . ' ' . ($negocio->email ?? '') . ' ' . ($admin?->email ?? '')) }}">
                    <td style="color:#7C3AED;">{{ $i + 1 }}</td>
                    <td>
                        <div class="negocio-nombre">{{ $negocio->nombre }}</div>
                        @if ($negocio->direccion)
                            <div class="negocio-email">{{ $negocio->direccion }}</div>
                        @endif
                    </td>
                    <td>
                        @if ($admin)
                            <div style="color:#e2d9f3; font-weight:600;">{{ $admin->nombre }}</div>
                            <div class="negocio-email">{{ $admin->email }}</div>
                        @else
                            <span style="color:#3D0E8A;">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($negocio->telefono)
                            <span class="chip">{{ $negocio->telefono }}</span>
                        @else
                            <span style="color:#3D0E8A;">—</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <span class="{{ $negocio->suspendido ? 'badge-suspendido' : 'badge-activo' }}">
                            {{ $negocio->suspendido ? 'Suspendido' : 'Activo' }}
                        </span>
                    </td>
                    <td style="text-align:center;"><span class="chip">{{ $negocio->productos_count }}</span></td>
                    <td style="text-align:center;"><span class="chip">{{ $negocio->mesas_count }}</span></td>
                    <td style="text-align:center;"><span class="chip">{{ $negocio->pedidos_count }}</span></td>
                    <td>
                        <div class="acciones" style="justify-content:center;">
                            <button class="btn-stats" onclick="verMetricas({{ $negocio->id_negocio }}, '{{ addslashes($negocio->nombre) }}')">📊 Métricas</button>
                            <button class="btn-edit" onclick="abrirEditar(
                                {{ $negocio->id_negocio }},
                                '{{ addslashes($negocio->nombre) }}',
                                '{{ addslashes($negocio->direccion ?? '') }}',
                                '{{ addslashes($negocio->telefono ?? '') }}',
                                '{{ addslashes($negocio->email ?? '') }}'
                            )">✏️ Editar</button>
                            <button class="{{ $negocio->suspendido ? 'btn-reactivar' : 'btn-suspend' }}"
                                    onclick="toggleSuspendido({{ $negocio->id_negocio }}, '{{ addslashes($negocio->nombre) }}', {{ $negocio->suspendido ? 'true' : 'false' }})">
                                {{ $negocio->suspendido ? '✅ Reactivar' : '⏸ Suspender' }}
                            </button>
                            <button class="btn-del" onclick="eliminar({{ $negocio->id_negocio }}, '{{ addslashes($negocio->nombre) }}')">🗑 Eliminar</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>{{-- /overflow-x:auto --}}
    </div>
</div>

{{-- Modal métricas --}}
<div class="overlay-stats" id="overlay-stats" onclick="if(event.target===this)cerrarStats()">
    <div class="modal-stats" id="modal-stats-content">
        <div class="stats-loading" id="stats-loading">Cargando métricas...</div>
        <div id="stats-body" style="display:none;">
            <div class="stats-header">
                <div>
                    <div class="stats-title" id="stats-nombre"></div>
                    <div class="stats-subtitle" id="stats-sub"></div>
                </div>
                <div class="stats-header-btns">
                    <button class="btn-pdf" onclick="imprimirPDF()">⬇ Descargar PDF</button>
                    <button class="btn-close-stats" onclick="cerrarStats()">✕ Cerrar</button>
                </div>
            </div>

            <div class="resumen-grid">
                <div class="resumen-card"><div class="resumen-val" id="r-pedidos">—</div><div class="resumen-lbl">Pedidos totales</div></div>
                <div class="resumen-card"><div class="resumen-val" id="r-cobrado">—</div><div class="resumen-lbl">Total cobrado</div></div>
                <div class="resumen-card"><div class="resumen-val" id="r-productos">—</div><div class="resumen-lbl">Productos activos</div></div>
                <div class="resumen-card"><div class="resumen-val" id="r-mesas">—</div><div class="resumen-lbl">Mesas registradas</div></div>
                <div class="resumen-card"><div class="resumen-val" id="r-ticket">—</div><div class="resumen-lbl">Ticket promedio</div></div>
            </div>

            <div class="charts-grid">
                <div class="chart-box">
                    <div class="chart-box-title">Pedidos por estado</div>
                    <div id="chart-estados-wrap"><p class="chart-empty-sa">Sin datos</p></div>
                </div>
                <div class="chart-box">
                    <div class="chart-box-title">Top 5 productos</div>
                    <div id="chart-productos-wrap"><p class="chart-empty-sa">Sin datos</p></div>
                </div>
                <div class="chart-box">
                    <div class="chart-box-title">Fuentes de pago</div>
                    <div id="chart-fuentes-wrap"><p class="chart-empty-sa">Sin pagos registrados</p></div>
                </div>
                <div class="chart-box">
                    <div class="chart-box-title">Ingresos últimos 6 meses</div>
                    <div id="chart-meses-wrap"><p class="chart-empty-sa">Sin datos</p></div>
                </div>
                <div class="chart-box">
                    <div class="chart-box-title">👤 Rendimiento por mesero</div>
                    <div id="chart-meseros-wrap"><p class="chart-empty-sa">Sin datos de meseros</p></div>
                </div>
                <div class="chart-box">
                    <div class="chart-box-title">🕐 Horas pico</div>
                    <div id="chart-horas-wrap"><p class="chart-empty-sa">Sin ventas registradas</p></div>
                </div>
                <div class="chart-box">
                    <div class="chart-box-title">🏷️ Ingresos por categoría</div>
                    <div id="chart-categorias-wrap"><p class="chart-empty-sa">Sin ventas registradas</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal editar --}}
<div class="overlay" id="modal-editar" onclick="if(event.target===this)cerrarEditar()">
    <div class="modal">
        <h3>✏️ Editar negocio</h3>
        <form id="form-editar" method="POST">
            @csrf
            @method('PUT')
            <div class="field"><label>Nombre *</label><input type="text" name="nombre" id="e-nombre" required></div>
            <div class="field"><label>Dirección</label><input type="text" name="direccion" id="e-direccion"></div>
            <div class="field"><label>Teléfono</label><input type="text" name="telefono" id="e-telefono"></div>
            <div class="field"><label>Email del negocio</label><input type="email" name="email" id="e-email"></div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="cerrarEditar()">Cancelar</button>
                <button type="submit" class="btn-save">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="toast"></div>

<script>
const RUTAS_NEGOCIOS = @json($negocios->mapWithKeys(fn($n) => [
    $n->id_negocio => route('superadmin.negocios.update', $n)
]));

const RUTAS_DELETE = @json($negocios->mapWithKeys(fn($n) => [
    $n->id_negocio => route('superadmin.negocios.destroy', $n)
]));

const RUTAS_TOGGLE = @json($negocios->mapWithKeys(fn($n) => [
    $n->id_negocio => route('superadmin.negocios.toggle', $n)
]));

function filtrar(q) {
    const term = q.toLowerCase().trim();
    document.querySelectorAll('#tabla-negocios tbody tr').forEach(tr => {
        tr.style.display = !term || tr.dataset.busqueda.includes(term) ? '' : 'none';
    });
}

function abrirEditar(id, nombre, direccion, telefono, email) {
    document.getElementById('form-editar').action = RUTAS_NEGOCIOS[id];
    document.getElementById('e-nombre').value    = nombre;
    document.getElementById('e-direccion').value = direccion;
    document.getElementById('e-telefono').value  = telefono;
    document.getElementById('e-email').value     = email;
    document.getElementById('modal-editar').classList.add('open');
}

function cerrarEditar() {
    document.getElementById('modal-editar').classList.remove('open');
}

document.getElementById('form-editar').addEventListener('submit', async function(e) {
    e.preventDefault();
    const res  = await fetch(this.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: new FormData(this),
    });
    const data = await res.json();
    cerrarEditar();
    toast(data.message, data.success !== false);
    if (data.success !== false) setTimeout(() => location.reload(), 800);
});

async function toggleSuspendido(id, nombre, suspendido) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    if (suspendido) {
        if (!confirm(`¿Reactivar el negocio «${nombre}»?`)) return;
        const res  = await fetch(RUTAS_TOGGLE[id], {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
        const data = await res.json();
        toast(data.message, data.success !== false);
        if (data.success !== false) setTimeout(() => location.reload(), 800);
        return;
    }

    // Primera llamada: verifica pedidos activos
    const res1  = await fetch(RUTAS_TOGGLE[id], {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    });
    const data1 = await res1.json();

    if (data1.advertencia) {
        const ok = confirm(
            `⚠️ «${nombre}» tiene ${data1.pedidos_activos} pedido(s) activo(s).\n\n` +
            `Si suspendes el negocio ahora, el administrador perderá acceso inmediatamente.\n\n` +
            `¿Deseas suspenderlo de todas formas?`
        );
        if (!ok) return;

        // Segunda llamada: confirma la suspensión
        const res2  = await fetch(RUTAS_TOGGLE[id] + '?confirmar=1', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        });
        const data2 = await res2.json();
        toast(data2.message, data2.success !== false);
        if (data2.success !== false) setTimeout(() => location.reload(), 800);
        return;
    }

    // Sin pedidos activos: suspende directo
    if (!confirm(`¿Suspender el negocio «${nombre}»?`)) return;
    toast(data1.message, data1.success !== false);
    if (data1.success !== false) setTimeout(() => location.reload(), 800);
}

async function eliminar(id, nombre) {
    if (!confirm(`¿Eliminar el negocio «${nombre}»? Esta acción eliminará todos sus datos.`)) return;
    const res  = await fetch(RUTAS_DELETE[id], {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: (() => { const f = new FormData(); f.append('_method', 'DELETE'); return f; })(),
    });
    let data;
    try { data = await res.json(); } catch { data = { success: false, message: 'Error del servidor (' + res.status + ').' }; }
    if (!res.ok && data.success === undefined) data.success = false;
    toast(data.message, data.success !== false);
    if (data.success !== false) setTimeout(() => location.reload(), 800);
}

function toast(msg, ok = true) {
    const t = document.getElementById('toast');
    t.textContent = ok ? msg : '⚠️ ' + msg + '  ✕';
    t.className = ok ? 'ok show' : 'err show';
    clearTimeout(t._t);
    if (ok) {
        t._t = setTimeout(() => t.classList.remove('show'), 3000);
    }
    // Los errores persisten hasta que el usuario haga clic
}

document.getElementById('toast').addEventListener('click', function() {
    if (this.classList.contains('err')) this.classList.remove('show');
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { cerrarEditar(); cerrarStats(); }
});

// ── MÉTRICAS ─────────────────────────────────────────────────────────────────
const RUTAS_STATS = @json($negocios->mapWithKeys(fn($n) => [
    $n->id_negocio => route('superadmin.negocios.stats', $n)
]));

let chartInstances = {};

function cerrarStats() {
    document.getElementById('overlay-stats').classList.remove('open');
    Object.values(chartInstances).forEach(c => c.destroy());
    chartInstances = {};
}

function imprimirPDF() {
    window.print();
}

async function verMetricas(id, nombre) {
    document.getElementById('stats-loading').style.display = '';
    document.getElementById('stats-body').style.display    = 'none';
    document.getElementById('overlay-stats').classList.add('open');

    // Cargar Chart.js si no está disponible
    if (typeof Chart === 'undefined') {
        await new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js';
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });
    }

    const res  = await fetch(RUTAS_STATS[id]);
    const data = await res.json();

    // Resumen
    document.getElementById('stats-nombre').textContent = '📊 ' + data.negocio.nombre;
    document.getElementById('stats-sub').textContent    = data.negocio.direccion || 'Sin dirección registrada';
    document.getElementById('r-pedidos').textContent   = data.resumen.total_pedidos;
    document.getElementById('r-cobrado').textContent   = '$' + Number(data.resumen.total_cobrado).toLocaleString('es-CO', {minimumFractionDigits:0});
    document.getElementById('r-productos').textContent = data.resumen.productos_activos;
    document.getElementById('r-mesas').textContent     = data.resumen.mesas_total;
    document.getElementById('r-ticket').textContent    = '$' + Number(data.resumen.ticket_promedio).toLocaleString('es-CO', {minimumFractionDigits:0});

    const purple = ['#3D0E8A','#6B21E8','#8B5CF6','#A78BFA','#C4B5FD'];
    const tooltipOpts = { backgroundColor:'#1a1a2e', titleColor:'#C4A0FF', bodyColor:'#fff', cornerRadius:8, padding:10 };

    // Pedidos por estado
    const estadosData = data.pedidos_por_estado;
    const estadosKeys = Object.keys(estadosData);
    if (estadosKeys.length) {
        document.getElementById('chart-estados-wrap').innerHTML = '<canvas id="sa-chart-estados"></canvas>';
        const colorMap = { Pendiente:'#C4B5FD', Parcial:'#8B5CF6', Pagado:'#3D0E8A' };
        chartInstances.estados = new Chart(document.getElementById('sa-chart-estados'), {
            type: 'doughnut',
            data: {
                labels: estadosKeys,
                datasets: [{ data: Object.values(estadosData), backgroundColor: estadosKeys.map(k => colorMap[k] || '#6B21E8'), borderColor:'#1a0f35', borderWidth:3 }]
            },
            options: { responsive:true, maintainAspectRatio:true, plugins:{ legend:{ position:'bottom', labels:{ color:'#e2d9f3', font:{ size:11 } } }, tooltip: tooltipOpts } }
        });
    }

    // Top productos
    if (data.top_productos.length) {
        document.getElementById('chart-productos-wrap').innerHTML = '<canvas id="sa-chart-productos"></canvas>';
        chartInstances.productos = new Chart(document.getElementById('sa-chart-productos'), {
            type: 'bar',
            data: {
                labels: data.top_productos.map(p => p.nombre),
                datasets: [{ label:'Unidades', data: data.top_productos.map(p => p.cantidad), backgroundColor: purple, borderRadius:6, borderSkipped:false }]
            },
            options: {
                responsive:true, maintainAspectRatio:true,
                plugins:{ legend:{ display:false }, tooltip: tooltipOpts },
                scales:{
                    x:{ ticks:{ color:'#9B8EC4', maxRotation:30 }, grid:{ display:false } },
                    y:{ ticks:{ color:'#9B8EC4', precision:0 }, grid:{ color:'rgba(61,14,138,0.4)' }, beginAtZero:true }
                }
            }
        });
    }

    // Fuentes de pago
    const fuentesLabels = { tarjeta:'💳 Tarjeta', pse:'🏦 PSE', nequi:'📱 Nequi', efectivo:'💵 Efectivo', digital:'🔷 Digital' };
    const fuentesActivas = Object.entries(data.fuentes_pago).filter(([,v]) => v.total > 0);
    if (fuentesActivas.length) {
        document.getElementById('chart-fuentes-wrap').innerHTML = `
            <canvas id="sa-chart-fuentes" style="max-height:140px;"></canvas>
            <table class="fuentes-tabla" style="margin-top:12px;" id="sa-fuentes-tabla"></table>`;
        chartInstances.fuentes = new Chart(document.getElementById('sa-chart-fuentes'), {
            type: 'doughnut',
            data: {
                labels: fuentesActivas.map(([k]) => fuentesLabels[k] || k),
                datasets: [{ data: fuentesActivas.map(([,v]) => v.total), backgroundColor: purple.slice(0, fuentesActivas.length), borderColor:'#1a0f35', borderWidth:3 }]
            },
            options: {
                responsive:true, maintainAspectRatio:true,
                plugins:{ legend:{ display:false }, tooltip:{ ...tooltipOpts, callbacks:{ label: ctx => ' $' + Number(ctx.raw).toLocaleString('es-CO',{minimumFractionDigits:0}) } } }
            }
        });
        const total = fuentesActivas.reduce((s,[,v]) => s + v.total, 0);
        document.getElementById('sa-fuentes-tabla').innerHTML =
            fuentesActivas.map(([k,v]) =>
                `<tr><td>${fuentesLabels[k]||k}</td><td>$${Number(v.total).toLocaleString('es-CO',{minimumFractionDigits:0})} · ${v.cantidad} pago${v.cantidad!==1?'s':''}</td></tr>`
            ).join('') +
            `<tr><td style="font-weight:700;color:#fff;">Total</td><td style="font-weight:800;">$${Number(total).toLocaleString('es-CO',{minimumFractionDigits:0})}</td></tr>`;
    }

    // Ingresos por mes
    const mesesData = data.ingresos_por_mes;
    const mesesKeys = Object.keys(mesesData);
    const mesesLabels = mesesKeys.map(m => {
        const [y, mo] = m.split('-');
        return new Date(y, mo-1).toLocaleDateString('es-CO', { month:'short', year:'2-digit' });
    });
    document.getElementById('chart-meses-wrap').innerHTML = '<canvas id="sa-chart-meses"></canvas>';
    chartInstances.meses = new Chart(document.getElementById('sa-chart-meses'), {
        type: 'bar',
        data: {
            labels: mesesLabels,
            datasets: [{
                label: 'Ingresos ($)',
                data: Object.values(mesesData),
                backgroundColor: '#6B21E8',
                hoverBackgroundColor: '#3D0E8A',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive:true, maintainAspectRatio:true,
            plugins:{ legend:{ display:false }, tooltip:{ ...tooltipOpts, callbacks:{ label: ctx => ' $' + Number(ctx.raw).toLocaleString('es-CO',{minimumFractionDigits:0}) } } },
            scales:{
                x:{ ticks:{ color:'#9B8EC4' }, grid:{ display:false } },
                y:{ ticks:{ color:'#9B8EC4', callback: v => '$'+Number(v).toLocaleString('es-CO',{minimumFractionDigits:0}) }, grid:{ color:'rgba(61,14,138,0.4)' }, beginAtZero:true }
            }
        }
    });

    // Rendimiento por mesero
    if (data.rendimiento_meseros.length) {
        const labels = data.rendimiento_meseros.map(r => r.mesero);
        const values = data.rendimiento_meseros.map(r => r.total);
        const h = Math.max(120, labels.length * 48);
        document.getElementById('chart-meseros-wrap').innerHTML = `<div style="position:relative;height:${h}px;"><canvas id="sa-chart-meseros"></canvas></div>`;
        chartInstances.meseros = new Chart(document.getElementById('sa-chart-meseros'), {
            type: 'bar',
            data: { labels, datasets: [{ label:'Ingresos', data: values, backgroundColor: purple.slice(0, labels.length), borderRadius:6, borderSkipped:false }] },
            options: {
                indexAxis: 'y', responsive:true, maintainAspectRatio:false,
                plugins: { legend:{ display:false }, tooltip:{ ...tooltipOpts, callbacks:{ label: ctx => ' $'+Number(ctx.raw).toLocaleString('es-CO',{minimumFractionDigits:0}) } } },
                scales: {
                    x: { beginAtZero:true, ticks:{ color:'#9B8EC4', callback: v => '$'+Number(v).toLocaleString('es-CO',{minimumFractionDigits:0}) }, grid:{ color:'rgba(61,14,138,0.4)' } },
                    y: { ticks:{ color:'#e2d9f3', font:{ size:12 } }, grid:{ display:false } },
                }
            }
        });
    }

    // Horas pico
    const horasData = data.horas_pico;
    if (horasData.reduce((s, v) => s + v, 0) > 0) {
        const horasLabels = Array.from({length:24}, (_, i) => String(i).padStart(2,'0')+':00');
        const maxVal = Math.max(...horasData);
        const bgColors = horasData.map(v => `rgba(107,33,232,${(0.2+(maxVal>0?v/maxVal:0)*0.75).toFixed(2)})`);
        document.getElementById('chart-horas-wrap').innerHTML = '<canvas id="sa-chart-horas" style="max-height:180px;"></canvas>';
        chartInstances.horas = new Chart(document.getElementById('sa-chart-horas'), {
            type: 'bar',
            data: { labels: horasLabels, datasets: [{ label:'Ingresos', data: horasData, backgroundColor: bgColors, borderRadius:3, borderSkipped:false }] },
            options: {
                responsive:true, maintainAspectRatio:true,
                plugins: { legend:{ display:false }, tooltip:{ ...tooltipOpts, callbacks:{ label: ctx => ' $'+Number(ctx.raw).toLocaleString('es-CO',{minimumFractionDigits:0}) } } },
                scales: {
                    x: { ticks:{ color:'#9B8EC4', font:{ size:8 }, maxRotation:0 }, grid:{ display:false } },
                    y: { beginAtZero:true, ticks:{ color:'#9B8EC4', callback: v => '$'+Number(v).toLocaleString('es-CO',{minimumFractionDigits:0}) }, grid:{ color:'rgba(61,14,138,0.4)' } },
                }
            }
        });
    }

    // Ingresos por categoría
    if (data.ingresos_categorias.length) {
        const catLabels = data.ingresos_categorias.map(c => c.categoria);
        const catValues = data.ingresos_categorias.map(c => c.total);
        document.getElementById('chart-categorias-wrap').innerHTML = '<canvas id="sa-chart-categorias" style="max-height:200px;"></canvas>';
        chartInstances.categorias = new Chart(document.getElementById('sa-chart-categorias'), {
            type: 'doughnut',
            data: { labels: catLabels, datasets: [{ data: catValues, backgroundColor: purple.slice(0, catLabels.length), borderColor:'#1a0f35', borderWidth:3 }] },
            options: {
                responsive:true, maintainAspectRatio:true,
                plugins: {
                    legend: { position:'bottom', labels:{ color:'#e2d9f3', font:{ size:11 }, padding:10 } },
                    tooltip: { ...tooltipOpts, callbacks:{ label: ctx => ' '+ctx.label+': $'+Number(ctx.raw).toLocaleString('es-CO',{minimumFractionDigits:0}) } },
                }
            }
        });
    }

    document.getElementById('stats-loading').style.display = 'none';
    document.getElementById('stats-body').style.display    = '';
}
</script>
</body>
</html>
