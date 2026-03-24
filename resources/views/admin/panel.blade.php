<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Panel de Control — ASAPP</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #F4F1FA;
            color: #1a1a2e;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #3D0E8A;
            color: #fff;
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        .topbar-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #C4A0FF, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .topbar-info { font-size: 13px; color: rgba(255,255,255,0.7); }
        .topbar-info strong { color: #fff; }

        .btn-logout {
            background: rgba(107,33,232,0.15);
            color: #C4A0FF;
            border: 1px solid rgba(107,33,232,0.3);
            padding: 7px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-logout:hover { background: rgba(107,33,232,0.3); }

        /* ── SEDE SWITCHER ── */
        .sede-switcher { position: relative; }

        .sede-btn {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 7px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: background 0.2s;
            white-space: nowrap;
            max-width: 220px;
        }

        .sede-btn:hover { background: rgba(255,255,255,0.22); }

        .sede-btn-nombre {
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }

        .sede-drop {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            background: #fff;
            border-radius: 12px;
            min-width: 210px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            z-index: 500;
            overflow: hidden;
            border: 1px solid #E0D9F5;
        }

        .sede-drop.open { display: block; }

        .sede-drop-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9B8EC4;
            padding: 12px 16px 6px;
        }

        .sede-item {
            display: block;
            width: 100%;
            padding: 9px 16px;
            text-align: left;
            font-size: 13px;
            color: #1a1a2e;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.1s;
        }

        .sede-item:hover { background: #F5F3FF; color: #6B21E8; }

        .sede-item.activa {
            color: #6B21E8;
            font-weight: 700;
            background: #EDE9FE;
        }

        .sede-divider { height: 1px; background: #E0D9F5; margin: 4px 0; }

        .sede-nueva { color: #6B21E8; font-weight: 600; }

        /* ── TABS ── */
        .tabs {
            background: #fff;
            border-bottom: 1px solid #E0D9F5;
            padding: 0 32px;
            display: flex;
            gap: 4px;
        }

        .tab {
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #9B8EC4;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: color 0.2s, border-color 0.2s;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }

        .tab:hover { color: #6B21E8; }
        .tab.active { color: #6B21E8; border-bottom-color: #6B21E8; }

        /* ── CONTENIDO ── */
        .content { max-width: 1400px; margin: 32px auto; padding: 0 32px; }
        .section { display: none; }
        .section.active { display: block; }

        /* ── FLASH ── */
        .flash {
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
        }

        .flash.ok  { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }
        .flash.err { background: #FFF0F0; color: #C8102E; border: 1px solid #F5C6CB; }

        /* ── CARDS ── */
        .card {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            border: 1px solid #E0D9F5;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(107,33,232,0.06);
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #3D0E8A;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── FORMULARIOS ── */
        .form-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end; }
        .form-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 140px; }
        .form-group label { font-size: 12px; font-weight: 600; color: #6B21E8; text-transform: uppercase; letter-spacing: 0.5px; }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            padding: 9px 13px;
            border: 1px solid #D4C9F0;
            border-radius: 8px;
            font-size: 14px;
            background: #FAF8FF;
            color: #1a1a2e;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus, select:focus, textarea:focus { border-color: #6B21E8; }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #555;
            padding: 9px 0;
        }

        /* ── BOTONES ── */
        .btn {
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: opacity 0.2s;
            white-space: nowrap;
        }

        .btn:hover { opacity: 0.85; }
        .btn-primary  { background: #6B21E8; color: #fff; }
        .btn-success  { background: #3D0E8A; color: #fff; }
        .btn-danger   { background: #5B21B6; color: #fff; }
        .btn-warning  { background: #7C3AED; color: #fff; }
        .btn-info     { background: #8B5CF6; color: #fff; }
        .btn-outline  { background: transparent; color: #6B21E8; border: 1px solid #6B21E8; }
        .btn-sm       { padding: 6px 12px; font-size: 12px; }

        /* ── TABLA PRODUCTOS ── */
        table { width: 100%; border-collapse: collapse; font-size: 13px; }

        thead th {
            background: #6B21E8;
            color: #fff;
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
        }

        tbody tr { border-bottom: 1px solid #EDE9F8; }
        tbody tr:hover { background: #FAF8FF; }
        tbody td { padding: 10px 14px; vertical-align: middle; }

        .badge-disponible { color: #5B21B6; font-weight: 600; }
        .badge-no         { color: #9B8EC4; }

        /* ── GRID MESAS ── */
        .mesas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }

        .mesa-card {
            background: #FAF8FF;
            border: 2px solid #E0D9F5;
            border-radius: 14px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .mesa-card.ocupada { border-color: #8B5CF6; background: #EDE9FE; }
        .mesa-card.libre   { border-color: #D4C9F0; background: #F5F3FF; }
        .mesa-card:hover   { box-shadow: 0 4px 16px rgba(107,33,232,0.12); }

        .mesa-icono  { font-size: 36px; margin-bottom: 8px; }
        .mesa-nombre { font-size: 15px; font-weight: 700; color: #3D0E8A; margin-bottom: 6px; }

        .mesa-estado {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 14px;
        }

        .estado-libre   { background: #F5F3FF; color: #9B8EC4; }
        .estado-ocupada { background: #C4B5FD; color: #3D0E8A; }
        .mesa-acciones  { display: flex; flex-direction: column; gap: 6px; }

        /* ── MODAL QR ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; }

        .modal {
            background: #fff;
            border-radius: 20px;
            padding: 36px;
            max-width: 360px;
            width: 90%;
            text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
        }

        .modal h3 { font-size: 18px; font-weight: 700; color: #3D0E8A; margin-bottom: 6px; }
        .modal p  { font-size: 13px; color: #9B8EC4; margin-bottom: 20px; }

        #qr-container { margin: 0 auto 20px; display: flex; justify-content: center; }

        .modal-url {
            font-size: 11px;
            color: #9B8EC4;
            word-break: break-all;
            background: #FAF8FF;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #E0D9F5;
        }

        .modal-acciones { display: flex; gap: 10px; justify-content: center; }

        /* ── ESTADÍSTICAS ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #E0D9F5;
            border-radius: 14px;
            padding: 24px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(107,33,232,0.06);
        }

        .stat-icon  { font-size: 28px; margin-bottom: 8px; }
        .stat-valor { font-size: 26px; font-weight: 800; color: #3D0E8A; margin-bottom: 4px; word-break: break-all; }
        .stat-label { font-size: 11px; color: #9B8EC4; text-transform: uppercase; letter-spacing: 1px; }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .chart-card { padding: 24px !important; }
        .chart-wrap { position: relative; height: 260px; }
        .chart-wrap-wide { position: relative; height: 200px; }
        .chart-empty { text-align: center; color: #9B8EC4; font-size: 13px; padding: 48px 0; }

        @media (max-width: 700px) { .charts-row { grid-template-columns: 1fr; } }

        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #C8102E;
            color: #fff;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            margin-left: 6px;
            vertical-align: middle;
        }

        /* ── TOAST ── */
        #panel-toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            padding: 14px 22px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            max-width: 420px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            z-index: 9999;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.25s, transform 0.25s;
            pointer-events: none;
        }
        #panel-toast.show      { opacity: 1; transform: translateY(0); }
        #panel-toast.toast-ok  { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }
        #panel-toast.toast-err { background: #FFF0F0; color: #C8102E; border: 1px solid #F5C6CB; }
        .toast-inner   { display: flex; align-items: center; gap: 14px; }
        .toast-close   { background: none; border: none; font-size: 16px; cursor: pointer; opacity: 0.5; color: inherit; padding: 0; line-height: 1; flex-shrink: 0; }
        .toast-close:hover { opacity: 1; }

        /* ── MODAL NUEVO PEDIDO ── */
        .modal-np { max-width: 640px; text-align: left; max-height: 90vh; display: flex; flex-direction: column; }
        .modal-np h3 { margin-bottom: 0; }
        .np-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
        .np-close { background: none; border: none; font-size: 16px; color: #9B8EC4; cursor: pointer; padding: 4px 8px; border-radius: 6px; transition: background 0.2s; }
        .np-close:hover { background: #F5F3FF; color: #3D0E8A; }
        .np-body { overflow-y: auto; flex: 1; margin-bottom: 16px; }
        .np-body::-webkit-scrollbar { width: 4px; }
        .np-body::-webkit-scrollbar-track { background: #F5F3FF; border-radius: 4px; }
        .np-body::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 4px; }
        .pedido-tabla { width: 100%; font-size: 13px; border-collapse: collapse; }
        .pedido-tabla th { text-align: left; padding: 8px 10px; color: #6B21E8; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #E0D9F5; position: sticky; top: 0; background: #fff; }
        .pedido-tabla td { padding: 9px 10px; border-bottom: 1px solid #EDE9F8; vertical-align: middle; }
        .pedido-tabla input[type="number"] { width: 65px; }
        .pedido-tabla input[type="checkbox"] { width: 18px; height: 18px; accent-color: #6B21E8; cursor: pointer; }
    </style>
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="topbar-logo">ASAPP</div>

    {{-- Sede switcher --}}
    <div class="sede-switcher">
        <button class="sede-btn" id="sedeBtn" type="button">
            🏪 <span class="sede-btn-nombre">{{ $negocio->nombre }}</span>
            <span style="opacity:0.5; font-size:10px; flex-shrink:0;">▾</span>
        </button>
        <div class="sede-drop" id="sedeDrop">
            <div class="sede-drop-title">Tus sedes</div>
            @foreach ($todasLasSedes as $sede)
                <form action="{{ route('panel.sedes.activar', $sede) }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit"
                            class="sede-item {{ $sede->id_negocio === $negocio->id_negocio ? 'activa' : '' }}">
                        {{ $sede->nombre }}
                        @if ($sede->id_negocio === $negocio->id_negocio)
                            &nbsp;✓
                        @endif
                    </button>
                </form>
            @endforeach
            <div class="sede-divider"></div>
            <button type="button" class="sede-item sede-nueva"
                    onclick="abrirNuevaSede(); document.getElementById('sedeDrop').classList.remove('open');">
                + Nueva sede
            </button>
        </div>
    </div>

    <div class="topbar-info">
        Bienvenido, <strong>{{ auth()->user()->nombre }}</strong>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="margin:0">
        @csrf
        <button type="submit" class="btn-logout">Cerrar sesión</button>
    </form>
</div>

{{-- TABS --}}
<div class="tabs">
    <button class="tab active" onclick="showTab('inventario', this)">
        📦 Inventario
        <span class="tab-badge" id="badge-stock"
              style="{{ $productosStockBajo->isEmpty() ? 'display:none' : '' }}">{{ $productosStockBajo->count() }}</span>
    </button>
    <button class="tab"        onclick="showTab('mesas', this)">🪑 Mesas</button>
    <button class="tab"        onclick="showTab('estadisticas', this)">📊 Estadísticas</button>
    <button class="tab"        onclick="showTab('historial', this)">✅ Historial</button>
</div>

{{-- CONTENIDO --}}
<div class="content">

    {{-- Flash message --}}
    @if (session('message'))
        @php $es_error = str_starts_with(session('message'), '❌'); @endphp
        <div class="flash {{ $es_error ? 'err' : 'ok' }}">
            {!! session('message') !!}
        </div>
    @endif

    {{-- TAB: INVENTARIO --}}
    <div id="tab-inventario" class="section active">
        @include('admin.partials.inventario')
    </div>

    {{-- TAB: MESAS --}}
    <div id="tab-mesas" class="section">
        @include('admin.partials.mesas')
    </div>

    {{-- TAB: ESTADÍSTICAS --}}
    <div id="tab-estadisticas" class="section">
        @include('admin.partials.estadisticas')
    </div>

    {{-- TAB: HISTORIAL --}}
    <div id="tab-historial" class="section">
        @include('admin.partials.historial')
    </div>

</div>

{{-- TOAST --}}
<div id="panel-toast"></div>

{{-- MODAL QR --}}
<div class="modal-overlay" id="modal-qr">
    <div class="modal">
        <h3 id="qr-titulo">Mesa</h3>
        <p>Escanea este código con el celular para acceder a la factura</p>
        <div id="qr-container"></div>
        <div class="modal-url" id="qr-url"></div>
        <div class="modal-acciones">
            <button class="btn btn-success" onclick="copiarLink()" id="btn-copiar">📋 Copiar link</button>
            <button class="btn btn-primary" onclick="imprimirQR()">🖨️ Imprimir</button>
            <button class="btn btn-outline" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>
</div>

{{-- MODAL NUEVO PEDIDO --}}
<div class="modal-overlay" id="modal-np">
    <div class="modal modal-np">
        <div class="np-header">
            <h3 id="np-titulo">🧾 Nuevo pedido</h3>
            <button class="np-close" onclick="cerrarNuevoPedido()">✕</button>
        </div>

        @php $productosDisp = $productos->filter(fn($p) => $p->disponible)->values(); @endphp

        @if ($productosDisp->isEmpty())
            <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
                No hay productos disponibles. Agrégalos en la pestaña Inventario.
            </p>
        @else
            <input type="text" id="np-buscador" placeholder="🔍 Buscar producto…"
                   oninput="filtrarNP(this.value)"
                   style="width:100%; margin-bottom:12px; font-size:13px;">

            <form action="{{ route('panel.pedidos.store') }}" method="POST" id="form-np">
                @csrf
                <input type="hidden" name="id_mesa" id="np-id-mesa">

                <div class="np-body" style="max-height:360px;">
                    <table class="pedido-tabla" id="np-tabla">
                        <thead>
                            <tr>
                                <th>Agregar</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productosDisp as $p)
                                <tr data-np="{{ strtolower($p->nombre . ' ' . ($p->categoria?->nombre ?? '')) }}">
                                    <td>
                                        <input type="checkbox" name="productos[]"
                                               value="{{ $p->id_producto }}"
                                               onchange="toggleNP(this, {{ $p->id_producto }})">
                                    </td>
                                    <td>
                                        <div style="font-weight:600; color:#1a1a2e;">{{ $p->nombre }}</div>
                                        @if ($p->categoria)
                                            <div style="font-size:11px; color:#9B8EC4;">{{ $p->categoria->nombre }}</div>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">${{ number_format($p->precio, 0, ',', '.') }}</td>
                                    <td>
                                        <input type="number"
                                               name="cantidades[{{ $p->id_producto }}]"
                                               id="np-cant-{{ $p->id_producto }}"
                                               value="1" min="1" disabled>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="np-sin-resultados" style="display:none;">
                                <td colspan="4" style="text-align:center; color:#9B8EC4; padding:16px; font-size:13px;">
                                    Sin productos que coincidan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; padding-top:4px;">
                    <button type="button" class="btn btn-outline" onclick="cerrarNuevoPedido()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">✅ Crear pedido</button>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- MODAL NUEVA SEDE --}}
<div class="modal-overlay" id="modal-sede">
    <div class="modal" style="max-width:420px; text-align:left;">
        <h3 style="margin-bottom:6px;">🏪 Nueva sede</h3>
        <p style="margin-bottom:20px;">Añade una nueva sucursal o punto de venta a tu cuenta.</p>
        <form action="{{ route('panel.sedes.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:#6B21E8; display:block; margin-bottom:6px;">
                    Nombre de la sede *
                </label>
                <input type="text" name="nombre" required maxlength="100"
                       placeholder="Ej: Sede Norte, Sucursal Centro"
                       style="width:100%; padding:10px 12px; border:1.5px solid #E0D9F5;
                              border-radius:10px; font-size:14px; font-family:inherit;">
            </div>
            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:#6B21E8; display:block; margin-bottom:6px;">
                    Dirección
                </label>
                <input type="text" name="direccion" maxlength="150"
                       placeholder="Calle 10 # 20-30"
                       style="width:100%; padding:10px 12px; border:1.5px solid #E0D9F5;
                              border-radius:10px; font-size:14px; font-family:inherit;">
            </div>
            <div style="margin-bottom:20px;">
                <label style="font-size:11px; font-weight:700; text-transform:uppercase;
                               letter-spacing:1px; color:#6B21E8; display:block; margin-bottom:6px;">
                    Teléfono
                </label>
                <input type="text" name="telefono" maxlength="20"
                       placeholder="300 000 0000"
                       style="width:100%; padding:10px 12px; border:1.5px solid #E0D9F5;
                              border-radius:10px; font-size:14px; font-family:inherit;">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="cerrarNuevaSede()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Crear sede →</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Tabs ──────────────────────────────────────────────────────────────
function showTab(tabName, btn) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tabName).classList.add('active');
    if (btn) btn.classList.add('active');
    if (tabName === 'estadisticas' && typeof initEstadisticasCharts === 'function') {
        initEstadisticasCharts();
    }
}

// ── Modal Nuevo Pedido ────────────────────────────────────────────────
function abrirNuevoPedido(idMesa, nombreMesa) {
    document.getElementById('np-id-mesa').value = idMesa;
    document.getElementById('np-titulo').textContent = '🧾 Nuevo pedido — ' + nombreMesa;

    // Resetear checkboxes y cantidades
    document.querySelectorAll('#np-tabla input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        const cant = document.getElementById('np-cant-' + cb.value);
        if (cant) { cant.disabled = true; cant.value = 1; }
    });

    // Resetear buscador y mostrar todas las filas
    const buscador = document.getElementById('np-buscador');
    if (buscador) { buscador.value = ''; filtrarNP(''); }

    document.getElementById('modal-np').classList.add('open');
}

function cerrarNuevoPedido() {
    document.getElementById('modal-np').classList.remove('open');
}

function toggleNP(checkbox, id) {
    const input = document.getElementById('np-cant-' + id);
    input.disabled = !checkbox.checked;
    if (checkbox.checked) input.focus();
}

function filtrarNP(q) {
    const term  = q.toLowerCase().trim();
    const filas = document.querySelectorAll('#np-tabla tbody tr[data-np]');
    let visibles = 0;
    filas.forEach(fila => {
        const match = !term || fila.dataset.np.includes(term);
        fila.style.display = match ? '' : 'none';
        if (match) visibles++;
    });
    const aviso = document.getElementById('np-sin-resultados');
    if (aviso) aviso.style.display = visibles === 0 ? '' : 'none';
}

// ── QR Modal ─────────────────────────────────────────────────────────
function mostrarQR(nombreMesa, url) {
    document.getElementById('qr-titulo').textContent = '📷 ' + nombreMesa;
    document.getElementById('qr-url').textContent = url;

    const container = document.getElementById('qr-container');
    container.innerHTML = '';

    new QRCode(container, {
        text: url,
        width: 200,
        height: 200,
        colorDark: '#3D0E8A',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });

    document.getElementById('modal-qr').classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal-qr').classList.remove('open');
    document.getElementById('qr-container').innerHTML = '';
}

// ── Sede switcher ─────────────────────────────────────────────────────
const sedeBtn  = document.getElementById('sedeBtn');
const sedeDrop = document.getElementById('sedeDrop');

sedeBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    sedeDrop.classList.toggle('open');
});

document.addEventListener('click', function () {
    sedeDrop.classList.remove('open');
});

sedeDrop.addEventListener('click', function (e) {
    e.stopPropagation();
});

// ── Modal nueva sede ──────────────────────────────────────────────────
function abrirNuevaSede() {
    document.getElementById('modal-sede').classList.add('open');
}

function cerrarNuevaSede() {
    document.getElementById('modal-sede').classList.remove('open');
}

document.getElementById('modal-sede').addEventListener('click', function (e) {
    if (e.target === this) cerrarNuevaSede();
});

function copiarLink() {
    const url = document.getElementById('qr-url').textContent;
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('btn-copiar');
        btn.textContent = '✅ ¡Copiado!';
        btn.style.background = '#5B21B6';
        setTimeout(() => { btn.textContent = '📋 Copiar link'; btn.style.background = ''; }, 2500);
    });
}

function imprimirQR() {
    const nombre = document.getElementById('qr-titulo').textContent;
    const url    = document.getElementById('qr-url').textContent;
    const canvas = document.querySelector('#qr-container canvas');
    const imgSrc = canvas ? canvas.toDataURL() : '';

    const win = window.open('', '_blank');
    win.document.write(`
        <!DOCTYPE html><html><head>
        <title>QR ${nombre}</title>
        <style>
            body { font-family: sans-serif; text-align: center; padding: 40px; }
            h2   { color: #3D0E8A; margin-bottom: 8px; }
            p    { color: #888; font-size: 12px; margin-bottom: 20px; }
            img  { border: 2px solid #6B21E8; border-radius: 8px; padding: 8px; }
            .url { font-size: 10px; color: #aaa; margin-top: 12px; word-break: break-all; }
        </style></head><body>
        <h2>${nombre}</h2>
        <p>Escanea el código QR para acceder a tu factura</p>
        <img src="${imgSrc}" width="220">
        <div class="url">${url}</div>
        <script>window.onload=()=>window.print()<\/script>
        </body></html>
    `);
    win.document.close();
}

// Cerrar modales al hacer clic fuera
document.getElementById('modal-qr').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});
document.getElementById('modal-np').addEventListener('click', function(e) {
    if (e.target === this) cerrarNuevoPedido();
});

// ── AJAX Form Interceptor ─────────────────────────────────────────────
document.addEventListener('submit', async function(e) {
    const form = e.target;
    if (!('ajax' in form.dataset)) return;
    e.preventDefault();

    let res, data;
    try {
        res = await fetch(form.action, {
            method: form.method.toUpperCase(),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        });
        data = await res.json();
    } catch {
        showToast('❌ Error de conexión', false);
        return;
    }

    if (res.status === 422) {
        const msg = data.errors
            ? Object.values(data.errors).flat().join(' · ')
            : (data.message || 'Error de validación');
        showToast('❌ ' + msg, false);
        return;
    }

    showToast(data.message, data.success !== false);

    if (data.success !== false) {
        const toRefresh = (form.dataset.refresh || '').split(',').filter(Boolean);
        if (toRefresh.length) await refreshPartials(toRefresh);
    }
});

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, ok = true) {
    const t = document.getElementById('panel-toast');
    t.innerHTML = `<div class="toast-inner"><span>${msg}</span><button class="toast-close" onclick="document.getElementById('panel-toast').classList.remove('show')">✕</button></div>`;
    t.className = (ok ? 'toast-ok' : 'toast-err');
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 3500);
}

// ── Refresh parciales ─────────────────────────────────────────────────
async function refreshPartials(names) {
    await Promise.all(names.map(async name => {
        try {
            const res       = await fetch('/panel/partials/' + name, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html      = await res.text();
            const container = document.getElementById('tab-' + name);
            if (!container) return;
            container.innerHTML = html;
            activarScripts(container);
            if (name === 'inventario') actualizarBadgeStock();
            if (name === 'estadisticas' && container.classList.contains('active')) {
                if (typeof initEstadisticasCharts === 'function') initEstadisticasCharts();
            }
        } catch { /* fallo silencioso por parcial */ }
    }));
}

function activarScripts(container) {
    container.querySelectorAll('script').forEach(old => {
        const s = document.createElement('script');
        s.textContent = old.textContent;
        document.head.appendChild(s);
        document.head.removeChild(s);
    });
}

function actualizarBadgeStock() {
    const el    = document.getElementById('stock-bajo-count');
    const badge = document.getElementById('badge-stock');
    if (!el || !badge) return;
    const n = parseInt(el.dataset.count || '0');
    badge.textContent    = n;
    badge.style.display  = n > 0 ? '' : 'none';
}
</script>

</body>
</html>
