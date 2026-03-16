<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* ── CREAR PEDIDO ── */
        .pedido-tabla { width: 100%; font-size: 13px; border-collapse: collapse; }
        .pedido-tabla th { text-align: left; padding: 8px 10px; color: #6B21E8; font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #E0D9F5; }
        .pedido-tabla td { padding: 10px; border-bottom: 1px solid #EDE9F8; vertical-align: middle; }
        .pedido-tabla input[type="number"] { width: 70px; }
        .pedido-tabla input[type="checkbox"] { width: 18px; height: 18px; accent-color: #6B21E8; cursor: pointer; }
    </style>
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="topbar-logo">ASAPP</div>
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
        @if ($productosStockBajo->isNotEmpty())
            <span class="tab-badge">{{ $productosStockBajo->count() }}</span>
        @endif
    </button>
    <button class="tab"        onclick="showTab('mesas', this)">🪑 Mesas</button>
    <button class="tab"        onclick="showTab('nuevo-pedido', this)">🧾 Nuevo Pedido</button>
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

    {{-- TAB: NUEVO PEDIDO --}}
    <div id="tab-nuevo-pedido" class="section">
        @include('admin.partials.nuevo-pedido')
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

// ── Checkbox cantidad en nuevo pedido ────────────────────────────────
function toggleCantidad(checkbox, id) {
    const input = document.getElementById('cant-' + id);
    input.disabled = !checkbox.checked;
    if (checkbox.checked) input.focus();
}

// ── Ir a "Nuevo Pedido" preseleccionando mesa ────────────────────────
function irACrearPedido(idMesa) {
    showTab('nuevo-pedido', document.querySelectorAll('.tab')[2]);
    setTimeout(() => {
        const sel = document.getElementById('select-mesa');
        if (sel) sel.value = idMesa;
    }, 50);
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

// Cerrar modal al hacer clic fuera
document.getElementById('modal-qr').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

// Activar tab correcto si el flash es de pedido recién creado
@if (session('message') && str_contains(session('message'), 'Pedido'))
    showTab('mesas', document.querySelectorAll('.tab')[1]);
@endif
</script>

</body>
</html>
