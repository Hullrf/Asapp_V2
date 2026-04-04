<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $pedido->id_pedido }} — ASAPP</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:         #F4F1FA;
            --surface:    #ffffff;
            --surface2:   #FAF8FF;
            --border:     #E0D9F5;
            --border-hot: #D4C9F0;
            --purple:     #6B21E8;
            --purple-lt:  #8B45F5;
            --purple-dk:  #3D0E8A;
            --gold:       #6B21E8;
            --teal:       #6B21E8;
            --text:       #1a1a2e;
            --muted:      #9B8EC4;
            --mono:       'DM Mono', monospace;
            --sans:       'Syne', sans-serif;
        }

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 32px;
            height: 60px;
            background: #3D0E8A;
            border-bottom: 1px solid #2d0a6b;
            box-shadow: 0 2px 12px rgba(0,0,0,0.2);
        }

        .logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #C4A0FF, #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .topbar-meta { display: flex; align-items: center; gap: 16px; }

        .pedido-badge {
            font-family: var(--mono);
            font-size: 12px;
            color: rgba(255,255,255,0.7);
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 6px;
            padding: 5px 10px;
        }

        .pedido-badge span { color: #C4A0FF; }

        .btn-logout {
            font-family: var(--sans);
            font-size: 12px;
            font-weight: 600;
            color: #C4A0FF;
            background: rgba(107,33,232,0.15);
            border: 1px solid rgba(107,33,232,0.3);
            padding: 7px 14px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }

        .btn-logout:hover { background: rgba(107,33,232,0.3); }

        .page {
            position: relative;
            z-index: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 36px 24px 64px;
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 24px;
            align-items: start;
        }

        .panel-main { display: flex; flex-direction: column; gap: 20px; min-width: 0; }
        .panel-side  { display: flex; flex-direction: column; gap: 16px; min-width: 0; }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .card-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
        }

        .card-body { padding: 20px 24px; }

        .negocio-nombre {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 14px;
        }

        .info-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }

        .info-list li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: var(--muted);
        }

        .info-list li strong {
            color: var(--text);
            font-weight: 600;
            min-width: 64px;
            flex-shrink: 0;
        }

        .divider { border: none; border-top: 1px solid var(--border); margin: 14px 0; }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--muted);
            padding: 4px 0;
        }

        .meta-row span:last-child {
            font-family: var(--mono);
            color: var(--text);
            font-size: 11px;
        }

        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .estado-pendiente { background: #F5F3FF; color: #9B8EC4; border: 1px solid #E0D9F5; }
        .estado-parcial   { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }
        .estado-pagado    { background: #3D0E8A; color: #fff;    border: 1px solid #2d0a6b; }

        .alert-pagado {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #EDE9FE;
            border: 1px solid #C4B5FD;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #5B21B6;
        }

        .pago-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }

        .total-display {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--border);
        }

        .ipo-desglose {
            font-size: 12px;
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .ipo-desglose.ipo-line { color: #6B21E8; font-weight: 600; }

        .ipo-divider { border: none; border-top: 1px solid var(--border); margin: 8px 0; }

        .total-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--muted);
            margin-bottom: 6px;
            text-align: center;
        }

        .total-amount {
            font-size: 36px;
            font-weight: 800;
            font-family: var(--mono);
            color: #9B8EC4;
            text-align: center;
        }

        .pago-actions { padding: 16px; }

        .btn-pagar {
            width: 100%;
            background: #6B21E8;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-family: var(--sans);
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.3px;
        }

        .btn-pagar:hover:not(:disabled) { background: #5B18C8; transform: translateY(-1px); }
        .btn-pagar:disabled {
            background: rgba(107,33,232,0.15);
            color: rgba(107,33,232,0.4);
            cursor: not-allowed;
        }

        .btn-efectivo {
            width: 100%;
            background: #16a34a;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-family: var(--sans);
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s;
            letter-spacing: 0.3px;
            margin-top: 8px;
        }

        .btn-efectivo:hover:not(:disabled) { background: #15803d; transform: translateY(-1px); }
        .btn-efectivo:disabled {
            background: rgba(22,163,74,0.15);
            color: rgba(22,163,74,0.4);
            cursor: not-allowed;
        }

        .hint-text {
            text-align: center;
            font-size: 11px;
            color: var(--muted);
            margin-top: 10px;
            line-height: 1.5;
        }

        .table-wrap { overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; font-size: 13px; }

        thead tr { border-bottom: 1px solid var(--border-hot); }

        thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--muted);
            white-space: nowrap;
        }

        thead th:first-child { width: 44px; text-align: center; }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr { border-bottom: 1px solid rgba(107,33,232,0.08); transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(107,33,232,0.05); }

        tbody td { padding: 13px 16px; vertical-align: middle; color: var(--text); }
        tbody td:first-child { text-align: center; }
        tbody td.right  { text-align: right; font-family: var(--mono); font-size: 12px; }
        tbody td.center { text-align: center; }

        .producto-nombre { font-weight: 600; font-size: 14px; }
        .precio-unit { font-family: var(--mono); font-size: 12px; color: var(--muted); }

        input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: var(--purple);
            cursor: pointer;
        }

        input[type="checkbox"]:disabled { opacity: 0.4; cursor: not-allowed; }

        .actions-cell { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }

        .small-input {
            width: 58px;
            background: #FAF8FF;
            border: 1px solid #D4C9F0;
            border-radius: 6px;
            color: var(--text);
            padding: 5px 8px;
            font-size: 12px;
            font-family: var(--mono);
            text-align: center;
            outline: none;
        }

        .small-input:focus { border-color: var(--purple); }

        .btn {
            font-family: var(--sans);
            font-size: 11px;
            font-weight: 700;
            padding: 5px 11px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            white-space: nowrap;
        }

        .btn:hover { opacity: 0.82; transform: translateY(-1px); }
        .btn-editar   { background: rgba(120, 56, 176, 0.85); color: #fff; }
        .btn-eliminar { background: rgba(120, 56, 176, 0.85);   color: #fff; }

        .form-agregar {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 10px;
            align-items: center;
        }

        .form-agregar select,
        .form-agregar input[type="number"] {
            background: #FAF8FF;
            border: 1px solid #D4C9F0;
            border-radius: 8px;
            color: var(--text);
            padding: 10px 12px;
            font-size: 13px;
            outline: none;
            -webkit-appearance: none;
        }

        .form-agregar select option { background: #fff; color: var(--text); }
        .qty-input { width: 80px; font-family: var(--mono); text-align: center; }

        .prod-search-wrap { position: relative; }
        .prod-search-input {
            width: 100%;
            padding: 9px 14px;
            background: #FAF8FF;
            border: 1px solid #D4C9F0;
            border-radius: 8px;
            font-size: 13px;
            font-family: var(--sans);
            color: var(--text);
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.15s;
        }
        .prod-search-input:focus { border-color: #9B8EC4; }
        .prod-search-input.tiene-seleccion { border-color: #6B21E8; background: #f5f0ff; font-weight: 600; }
        .prod-dropdown {
            display: none;
            position: fixed;
            background: #fff;
            border: 1.5px solid #D4C9F0;
            border-radius: 10px;
            max-height: 220px;
            overflow-y: auto;
            z-index: 9999;
            box-shadow: 0 6px 20px rgba(107,33,232,0.12);
        }
        .prod-dropdown.visible { display: block; }
        .prod-option {
            padding: 9px 14px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #f3f0ff;
        }
        .prod-option:last-child { border-bottom: none; }
        .prod-option:hover, .prod-option.activo { background: #f5f0ff; }
        .prod-option-nombre { font-size: 13px; color: var(--text); }
        .prod-option-precio { font-size: 11px; color: #9B8EC4; font-family: var(--mono); white-space: nowrap; }
        .prod-sin-resultados { padding: 12px 14px; font-size: 12px; color: #9B8EC4; text-align: center; }

        .btn-crear {
            background: var(--purple);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-crear:hover { background: var(--purple-lt); }

        .empty-state { text-align: center; padding: 48px 24px; color: var(--muted); }

        .btn-reabrir {
            width: 100%;
            background: transparent;
            color: var(--purple);
            border: 1.5px solid var(--purple);
            border-radius: 10px;
            padding: 12px;
            font-family: var(--sans);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }

        .btn-reabrir:hover { background: var(--purple); color: #fff; }
        .empty-state .empty-icon { font-size: 40px; margin-bottom: 12px; display: block; opacity: 0.5; }
        .empty-state p { font-size: 14px; }

        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #16a34a;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 3px 9px;
        }
        .live-dot {
            width: 6px;
            height: 6px;
            background: #16a34a;
            border-radius: 50%;
            animation: pulse-dot 1.8s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.75); }
        }

        @media (max-width: 900px) {
            .page { grid-template-columns: 1fr; }
            .panel-side { order: -1; display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        }

        @media (max-width: 600px) {
            .topbar { padding: 14px 16px; }
            .page { padding: 20px 16px 48px; gap: 16px; }
            .panel-side { grid-template-columns: 1fr; }
            .card-body { padding: 16px; }
            .form-agregar { grid-template-columns: 1fr; }
            .qty-input { width: 100%; }
        }

        /* ── División ─────────────────────────────────────────────────────── */
        .btn-dividir {
            background: none;
            border: 1px dashed #9B8EC4;
            color: #9B8EC4;
            border-radius: 6px;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 4px;
            letter-spacing: 0.5px;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .btn-dividir:hover { background: rgba(107,33,232,0.08); border-color: #6B21E8; color: #6B21E8; }

        .division-row td { padding: 0 !important; }
        .division-panel {
            background: linear-gradient(135deg, #f5f0ff 0%, #fdf8ff 100%);
            border-top: 2px solid #e9d5ff;
            border-bottom: 1px solid #e9d5ff;
            padding: 14px 20px;
        }
        .division-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
        .division-titulo {
            font-size: 13px;
            font-weight: 700;
            color: #6B21E8;
        }
        .division-sub {
            font-size: 11px;
            color: #9B8EC4;
            margin-left: 6px;
        }
        .division-btns { display: flex; gap: 8px; flex-wrap: wrap; }
        .btn-div-accion {
            padding: 5px 12px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            transition: opacity 0.15s;
        }
        .btn-div-accion:hover { opacity: 0.82; }
        .btn-div-mod  { background: #e9d5ff; color: #6B21E8; }
        .btn-div-cancel { background: #fee2e2; color: #dc2626; }

        .division-partes {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .div-parte {
            background: #fff;
            border: 1.5px solid #e9d5ff;
            border-radius: 10px;
            padding: 10px 14px;
            min-width: 140px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .div-parte.tomada-mia  { border-color: #6B21E8; background: #f5f0ff; }
        .div-parte.tomada-otro { border-color: #d1d5db; opacity: 0.7; }
        .div-parte-num  { font-size: 10px; color: #9B8EC4; font-weight: 700; text-transform: uppercase; }
        .div-parte-monto { font-size: 16px; font-weight: 800; color: #1a1a2e; font-family: var(--mono); }
        .div-parte-estado { font-size: 11px; }
        .btn-tomar {
            margin-top: 4px;
            background: #6B21E8;
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.15s;
        }
        .btn-tomar:hover { background: #5B18C8; }
        .btn-liberar {
            margin-top: 4px;
            background: #f3f4f6;
            color: #6B21E8;
            border: 1px solid #e9d5ff;
            border-radius: 7px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }
        .div-esperando {
            font-size: 11px;
            color: #9B8EC4;
            font-style: italic;
            margin-top: 2px;
        }

        /* Modal división */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,0.45);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-overlay.visible { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            max-width: 480px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }
        .modal-titulo {
            font-size: 18px;
            font-weight: 800;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .modal-subtitulo {
            font-size: 13px;
            color: #9B8EC4;
            margin-bottom: 20px;
        }
        .modal-label {
            font-size: 12px;
            font-weight: 700;
            color: #4B5563;
            margin-bottom: 6px;
            display: block;
        }
        .modal-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e9d5ff;
            border-radius: 9px;
            font-size: 14px;
            font-family: var(--sans);
            box-sizing: border-box;
            margin-bottom: 16px;
        }
        .modal-input:focus { outline: none; border-color: #6B21E8; }
        .modal-partes-grid {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }
        .modal-parte-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-parte-label {
            font-size: 12px;
            color: #9B8EC4;
            font-weight: 700;
            min-width: 55px;
        }
        .modal-parte-input {
            flex: 1;
            padding: 8px 12px;
            border: 1.5px solid #e9d5ff;
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--mono);
        }
        .modal-parte-input:focus { outline: none; border-color: #6B21E8; }
        .modal-restante {
            text-align: right;
            font-size: 12px;
            color: #9B8EC4;
            margin-bottom: 16px;
        }
        .modal-restante.error { color: #dc2626; font-weight: 700; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; }
        .btn-modal-cancel {
            padding: 10px 20px;
            border-radius: 9px;
            border: 1.5px solid #e9d5ff;
            background: #fff;
            color: #6B21E8;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-modal-confirm {
            padding: 10px 20px;
            border-radius: 9px;
            border: none;
            background: #6B21E8;
            color: #fff;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-modal-confirm:disabled { background: rgba(107,33,232,0.3); cursor: not-allowed; }
    </style>
</head>
<body>

{{-- TOPBAR --}}
<header class="topbar">
    <span class="logo">ASAPP</span>
    <div class="topbar-meta">
        <span class="pedido-badge">Pedido <span>#{{ $pedido->id_pedido }}</span></span>
        @auth
            <form action="{{ route('logout') }}" method="POST" style="margin:0">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        @endauth
    </div>
</header>

{{-- PÁGINA --}}
<main class="page">

@if(session('success_efectivo'))
<div id="toast-efectivo" style="
    position:fixed; bottom:24px; right:24px; z-index:9999;
    background:#16a34a; color:#fff; border-radius:12px;
    padding:14px 20px; font-family:var(--sans); font-size:14px; font-weight:700;
    box-shadow:0 4px 20px rgba(0,0,0,0.18); display:flex; align-items:center; gap:10px;
    animation: slideIn 0.3s ease;">
    ✓ Pago en efectivo registrado — {{ '$' . number_format(session('success_efectivo'), 0, ',', '.') }}
    <button onclick="document.getElementById('toast-efectivo').remove()" style="
        background:none; border:none; color:#fff; font-size:18px; cursor:pointer; line-height:1; padding:0 0 0 4px;">✕</button>
</div>
<script>setTimeout(()=>{const t=document.getElementById('toast-efectivo');if(t)t.remove();},4000);</script>
@endif

    {{-- ════ PANEL PRINCIPAL ════ --}}
    <div class="panel-main">

        {{-- Tabla de ítems --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Ítems del pedido</span>
                <div style="display:flex; align-items:center; gap:10px;">
                    @if (!$pedidoPagado)
                        <span class="live-badge"><span class="live-dot"></span>En vivo</span>
                    @endif
                    @php $estadoClass = 'estado-' . strtolower($pedido->estado->value); @endphp
                    <span class="estado-badge {{ $estadoClass }}" id="pedido-estado-badge">{{ $pedido->estado->value }}</span>
                </div>
            </div>

            @if ($pedidoPagado)
                <div style="padding: 16px 24px 0;">
                    <div class="alert-pagado">✅ Este pedido está completamente pagado.</div>
                </div>
            @endif

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            @if ($es_admin ? !$pedidoPagado : !$es_mesero)<th></th>@endif
                            <th>Producto</th>
                            <th class="center">Cant.</th>
                            <th class="right">Precio unit.</th>
                            <th class="right">Subtotal</th>
                            <th class="center">Estado</th>
                            @if (($es_admin || $es_mesero) && !$pedidoBloqueado)
                                <th class="right">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pedido->items->isEmpty())
                            <tr>
                                <td colspan="{{ ($es_admin || $es_mesero) ? ($pedidoBloqueado ? 5 : 7) : 6 }}">
                                    <div class="empty-state">
                                        <span class="empty-icon">🧾</span>
                                        <p>No hay ítems en este pedido aún.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($pedido->items as $item)
                                <tr data-item-id="{{ $item->id_item }}">
                                    {{-- Checkbox (admin cuando no pagado; cliente; no mesero) --}}
                                    @if ($es_admin ? !$pedidoPagado : !$es_mesero)
                                    <td>
                                        @if ($item->estado->value === 'Pendiente')
                                            <div style="display:flex;flex-direction:column;align-items:center;gap:3px;">
                                                <input type="checkbox" class="pay-checkbox"
                                                       value="{{ $item->id_item }}"
                                                       data-precio="{{ $item->subtotal }}">
                                                @if (!$es_admin && !$es_mesero)
                                                <button class="btn-dividir"
                                                        data-item-id="{{ $item->id_item }}"
                                                        data-subtotal="{{ $item->subtotal }}"
                                                        data-nombre="{{ $item->producto->nombre }}"
                                                        type="button">÷ Dividir</button>
                                                @endif
                                            </div>
                                        @elseif ($item->estado->value === 'Pagado')
                                            <input type="checkbox" checked disabled>
                                        @else
                                            <input type="checkbox" disabled>
                                        @endif
                                    </td>
                                    @endif

                                    {{-- Producto --}}
                                    <td>
                                        <div class="producto-nombre">{{ $item->producto->nombre }}</div>
                                    </td>

                                    {{-- Cantidad --}}
                                    <td class="center" style="font-family: var(--mono); font-size: 13px;">
                                        {{ $item->cantidad }}
                                    </td>

                                    {{-- Precio unitario --}}
                                    <td class="right">
                                        <span class="precio-unit">${{ number_format($item->precio_unitario, 0, ',', '.') }}</span>
                                    </td>

                                    {{-- Subtotal --}}
                                    <td class="right" style="font-weight: 700; color: var(--gold);">
                                        ${{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>

                                    {{-- Estado --}}
                                    <td class="center">
                                        @php $itemEstadoClass = 'estado-' . strtolower($item->estado->value); @endphp
                                        <span class="estado-badge {{ $itemEstadoClass }}">{{ $item->estado->value }}</span>
                                    </td>

                                    {{-- Acciones (admin o mesero, pedido no bloqueado) --}}
                                    @if (($es_admin || $es_mesero) && !$pedidoBloqueado)
                                        <td>
                                            <div class="actions-cell">
                                                {{-- Editar cantidad --}}
                                                <form action="{{ route('factura.item.update', [$pedido->id_pedido, $item->id_item]) }}"
                                                      method="POST"
                                                      style="display:flex; gap:6px; align-items:center;">
                                                    @csrf
                                                    @method('PUT')
                                                    <input class="small-input" type="number"
                                                           name="nueva_cantidad"
                                                           value="{{ $item->cantidad }}"
                                                           min="1" required>
                                                    <button type="submit" class="btn btn-editar">Editar</button>
                                                </form>

                                                {{-- Eliminar --}}
                                                <form action="{{ route('factura.item.delete', [$pedido->id_pedido, $item->id_item]) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('¿Eliminar este ítem?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-eliminar">✕</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Agregar producto (admin o mesero, pedido no bloqueado) --}}
        @if (($es_admin || $es_mesero) && !$pedidoBloqueado)
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Agregar producto</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('factura.item.add', $pedido->id_pedido) }}"
                          method="POST"
                          class="form-agregar"
                          id="formAgregarProducto">
                        @csrf
                        <div class="prod-search-wrap">
                            <input type="text"
                                   id="prodSearchInput"
                                   class="prod-search-input"
                                   placeholder="Buscar producto..."
                                   autocomplete="off">
                            <input type="hidden" name="id_producto" id="prodSeleccionado">
                            <div class="prod-dropdown" id="prodDropdown">
                                @foreach ($productos as $prod)
                                <div class="prod-option"
                                     data-id="{{ $prod->id_producto }}"
                                     data-nombre="{{ $prod->nombre }}"
                                     data-precio="{{ $prod->precio }}">
                                    <span class="prod-option-nombre">{{ $prod->nombre }}</span>
                                    <span class="prod-option-precio">${{ number_format($prod->precio, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                <div class="prod-sin-resultados" style="display:none;">Sin resultados</div>
                            </div>
                        </div>
                        <input class="qty-input" type="number" name="cantidad" placeholder="Cant." min="1" value="1" required>
                        <button type="submit" class="btn-crear">+ Agregar</button>
                    </form>
                </div>
            </div>
        @endif

    </div>{{-- /panel-main --}}


    {{-- ════ PANEL LATERAL ════ --}}
    <div class="panel-side">

        {{-- Info del negocio --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Negocio</span>
            </div>
            <div class="card-body">
                <div class="negocio-nombre">{{ $pedido->negocio->nombre }}</div>
                <ul class="info-list">
                    <li><strong>Dirección</strong>{{ $pedido->negocio->direccion }}</li>
                    <li><strong>Teléfono</strong>{{ $pedido->negocio->telefono }}</li>
                    <li><strong>Email</strong>{{ $pedido->negocio->email }}</li>
                </ul>

                <hr class="divider">

                <div class="meta-row">
                    <span>Fecha</span>
                    <span>{{ $pedido->fecha }}</span>
                </div>
                <div class="meta-row">
                    <span>Código QR</span>
                    <span>{{ $pedido->codigo_qr }}</span>
                </div>
                @if ($pedido->mesa)
                    <div class="meta-row">
                        <span>Mesa</span>
                        <span>{{ $pedido->mesa->nombre_display }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reabrir pedido (solo admin) --}}
        @if ($es_admin && $pedidoBloqueado)
            <div class="card">
                <div class="card-body" style="text-align:center;">
                    <div class="alert-pagado" style="margin-bottom:16px;">🎉 Pedido completamente pagado</div>
                    <form action="{{ route('factura.reabrir', $pedido->id_pedido) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-reabrir"
                                onclick="return confirm('¿Reabrir este pedido? Pasará a estado Parcial y podrás agregar nuevos ítems.')">
                            🔓 Reabrir pedido
                        </button>
                    </form>
                    <p style="font-size:11px; color:var(--muted); margin-top:10px; line-height:1.5;">
                        Úsalo si el cliente pide algo adicional después de haber pagado.
                    </p>
                </div>
            </div>
        @endif

        {{-- Cobro en efectivo (solo admin, pedido no pagado) --}}
        @if ($es_admin && !$pedidoPagado)
            <div class="pago-card">
                <div class="total-display">
                    <div class="ipo-desglose">
                        <span>Subtotal</span>
                        <span id="subtotal-val">$0</span>
                    </div>
                    <div class="ipo-desglose ipo-line">
                        <span>Ipoconsumo (8%)</span>
                        <span id="ipo-val">$0</span>
                    </div>
                    <hr class="ipo-divider">
                    <div class="total-label">Total a cobrar</div>
                    <div class="total-amount" id="total">$0</div>
                </div>
                <div class="pago-actions">
                    <button id="btnEfectivo" class="btn-efectivo" type="button" disabled>
                        Cobrar en efectivo
                    </button>
                    <p class="hint-text">Selecciona los ítems que recibiste en efectivo</p>
                </div>
            </div>
        @endif

        {{-- Pago (solo clientes, no admin ni mesero) --}}
        @if (!$es_admin && !$es_mesero)
            @if (!$pedidoPagado)
                <div class="pago-card">
                    <div class="total-display">
                        <div class="ipo-desglose">
                            <span>Subtotal</span>
                            <span id="subtotal-val">$0</span>
                        </div>
                        <div class="ipo-desglose ipo-line">
                            <span>Ipoconsumo (8%)</span>
                            <span id="ipo-val">$0</span>
                        </div>
                        <hr class="ipo-divider">
                        <div class="total-label">Total a pagar</div>
                        <div class="total-amount" id="total">$0</div>
                    </div>
                    <div class="pago-actions">
                        <button id="btnPagar" class="btn-pagar" type="button" disabled>
                            Pagar seleccionados →
                        </button>
                        <p class="hint-text">Selecciona los ítems que deseas pagar en la tabla</p>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="alert-pagado">🎉 Pedido completamente pagado</div>
                    </div>
                </div>
            @endif
        @endif

    </div>{{-- /panel-side --}}

</main>

{{-- Modal de división --}}
@if (!$es_admin)
<div class="modal-overlay" id="modalDivision">
    <div class="modal-box">
        <div class="modal-titulo" id="modalDivTitulo">Dividir ítem</div>
        <div class="modal-subtitulo" id="modalDivSubtitulo"></div>

        <label class="modal-label">¿En cuántas partes?</label>
        <input type="number" class="modal-input" id="modalDivN" min="2" max="20" value="2">

        <label class="modal-label">Monto por parte</label>
        <div class="modal-partes-grid" id="modalDivPartes"></div>
        <div class="modal-restante" id="modalDivRestante"></div>

        <div class="modal-footer">
            <button class="btn-modal-cancel" id="modalDivCancelarBtn" type="button">Cancelar</button>
            <button class="btn-modal-confirm" id="modalDivConfirmarBtn" type="button" disabled>Confirmar división</button>
        </div>
    </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const PASARELA_URL  = '{{ route('pasarela.show', $pedido->id_pedido) }}';
    const EFECTIVO_URL  = '{{ route('pago.efectivo', $pedido->id_pedido) }}';
    const SYNC_URL      = '{{ route('factura.sync', $pedido->id_pedido) }}';
    const ES_ADMIN      = {{ $es_admin ? 'true' : 'false' }};
    const PEDIDO_PAGADO = {{ $pedidoPagado ? 'true' : 'false' }};
    const CSRF_TOKEN   = '{{ csrf_token() }}';

    const btnPagar   = document.getElementById('btnPagar');
    const totalSpan  = document.getElementById('total');
    const checkboxes = () => Array.from(document.querySelectorAll('.pay-checkbox'));

    function formatCOP(valor) {
        return '$' + Math.round(valor).toLocaleString('es-CO');
    }

    function actualizar() {
        let subtotal = 0, anyChecked = false;
        checkboxes().forEach(cb => {
            if (cb.checked && !cb.disabled) {
                subtotal  += parseFloat(cb.dataset.precio || 0);
                anyChecked = true;
            }
        });
        // Sumar partes de división tomadas por este usuario
        document.querySelectorAll('.div-mia-monto[data-monto]').forEach(el => {
            subtotal  += parseFloat(el.dataset.monto || 0);
            anyChecked = true;
        });
        const ipoconsumo = subtotal * 0.08;
        const total      = subtotal + ipoconsumo;

        const subtotalSpan = document.getElementById('subtotal-val');
        const ipoSpan      = document.getElementById('ipo-val');
        if (subtotalSpan) subtotalSpan.textContent = formatCOP(subtotal);
        if (ipoSpan)      ipoSpan.textContent      = formatCOP(ipoconsumo);
        if (totalSpan) {
            totalSpan.textContent = formatCOP(total);
            totalSpan.style.color = anyChecked ? '#6B21E8' : '#9B8EC4';
        }
        if (btnPagar)    btnPagar.disabled    = !anyChecked;
        const btnEfe = document.getElementById('btnEfectivo');
        if (btnEfe) btnEfe.disabled = !anyChecked;
    }

    checkboxes().forEach(cb => cb.addEventListener('change', actualizar));
    actualizar();

    // ── Polling en tiempo real ──────────────────────────────────────────
    let sincronizar = async function () {}; // stub; overwritten below if !PEDIDO_PAGADO

    if (!PEDIDO_PAGADO) {
        let prevSnapshot = null;

        sincronizar = async function sincronizar() {
            if (document.hidden) return;
            try {
                const res  = await fetch(SYNC_URL + '?token=' + encodeURIComponent(getToken()), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                if (prevSnapshot === null) { prevSnapshot = data; return; }

                // Detectar cambios estructurales (ítems añadidos o eliminados)
                const prevIds = prevSnapshot.items.map(i => i.id).sort().join(',');
                const currIds = data.items.map(i => i.id).sort().join(',');
                if (prevIds !== currIds || data.pedido_estado !== prevSnapshot.pedido_estado) {
                    location.reload();
                    return;
                }

                // Actualizar estados individuales
                data.items.forEach(item => {
                    const prev = prevSnapshot.items.find(i => i.id === item.id);
                    if (prev && prev.estado !== item.estado) {
                        actualizarItemEstado(item.id, item.estado);
                    }
                });

                // Actualizar divisiones
                renderDivisiones(data.divisiones || []);

                prevSnapshot = data;
            } catch { /* fallo silencioso */ }
        }

        function actualizarItemEstado(id, estado) {
            const row = document.querySelector(`tr[data-item-id="${id}"]`);
            if (!row) return;

            // Actualizar badge de estado del ítem
            const badge = row.querySelector('.estado-badge');
            if (badge) {
                badge.textContent = estado;
                badge.className   = 'estado-badge estado-' + estado.toLowerCase();
            }

            // Bloquear checkbox si el ítem ya fue pagado (vista cliente)
            if (!ES_ADMIN && estado === 'Pagado') {
                const cb = row.querySelector('input[type="checkbox"].pay-checkbox');
                if (cb) {
                    cb.checked  = true;
                    cb.disabled = true;
                    cb.classList.remove('pay-checkbox');
                    actualizar(); // recalcular total
                }
            }
        }

        setTimeout(sincronizar, 1000);
        setInterval(sincronizar, 3000);
    }
    // ────────────────────────────────────────────────────────────────────

    // Extrae partes de división tomadas y devuelve sus nuevos IDs de ítem
    async function extraerPartesDiv() {
        const misPartes = Array.from(document.querySelectorAll('.div-mia-monto'));
        const ids = [];
        for (const el of misPartes) {
            const divRow = el.closest('tr.division-row');
            if (!divRow) continue;
            const divId = divRow.dataset.divisionId;
            try {
                const res = await ajaxPost(`/factura/${PEDIDO_ID}/division/${divId}/extraer`, { token: getToken() });
                if (res.id_item) ids.push(String(res.id_item));
            } catch { /* ignorar si ya fue extraída */ }
        }
        return ids;
    }

    if (btnPagar) {
        btnPagar.addEventListener('click', async function () {
            const selected = checkboxes()
                .filter(cb => cb.checked && !cb.disabled)
                .map(cb => cb.value);
            const tienePartes = document.querySelectorAll('.div-mia-monto').length > 0;

            if (selected.length === 0 && !tienePartes) return;

            btnPagar.disabled    = true;
            btnPagar.textContent = 'Procesando...';

            const extraidos = await extraerPartesDiv();
            const allIds    = [...selected, ...extraidos];

            if (allIds.length === 0) {
                btnPagar.disabled    = false;
                btnPagar.textContent = 'Pagar seleccionados →';
                return;
            }

            const form = document.createElement('form');
            form.method = 'GET';
            form.action = PASARELA_URL;
            allIds.forEach(id => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'items[]';
                input.value = id;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        });
    }

    const btnEfectivo = document.getElementById('btnEfectivo');
    if (btnEfectivo) {
        btnEfectivo.addEventListener('click', async function () {
            const selected = checkboxes()
                .filter(cb => cb.checked && !cb.disabled)
                .map(cb => cb.value);
            const tienePartes = document.querySelectorAll('.div-mia-monto').length > 0;

            if (selected.length === 0 && !tienePartes) return;

            btnEfectivo.disabled    = true;
            btnEfectivo.textContent = 'Procesando...';

            const extraidos = await extraerPartesDiv();
            const allIds    = [...selected, ...extraidos];

            if (allIds.length === 0) {
                btnEfectivo.disabled    = false;
                btnEfectivo.textContent = 'Cobrar en efectivo';
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = EFECTIVO_URL;
            const csrf  = document.createElement('input');
            csrf.type   = 'hidden';
            csrf.name   = '_token';
            csrf.value  = CSRF_TOKEN;
            form.appendChild(csrf);
            allIds.forEach(id => {
                const input = document.createElement('input');
                input.type  = 'hidden';
                input.name  = 'items_confirmados[]';
                input.value = id;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        });
    }

    // ── División de ítems ────────────────────────────────────────────────
    const PEDIDO_ID = {{ $pedido->id_pedido }};

    function getToken() {
        let t = localStorage.getItem('asapp_token');
        if (!t) {
            t = (crypto.randomUUID ? crypto.randomUUID() : Math.random().toString(36).slice(2) + Date.now().toString(36));
            localStorage.setItem('asapp_token', t);
        }
        return t;
    }

    async function ajaxPost(url, body, method = 'POST') {
        const r = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body),
        });
        if (!r.ok) {
            const err = await r.json().catch(() => ({}));
            throw new Error(err.message || 'Error en la solicitud');
        }
        return r.json();
    }

    // ── Modal ─────────────────────────────────────────────────────────────
    let modalItemId    = null;
    let modalSubtotal  = 0;
    let modalDivId     = null; // null = nuevo, int = modificar

    const modalOverlay  = document.getElementById('modalDivision');
    const modalN        = document.getElementById('modalDivN');
    const modalPartes   = document.getElementById('modalDivPartes');
    const modalRestante = document.getElementById('modalDivRestante');
    const modalConfirm  = document.getElementById('modalDivConfirmarBtn');
    const modalCancelar = document.getElementById('modalDivCancelarBtn');

    function abrirModal(itemId, subtotal, nombre, divId = null, partesActuales = null) {
        if (!modalOverlay) return;
        modalItemId   = itemId;
        modalSubtotal = subtotal;
        modalDivId    = divId;
        document.getElementById('modalDivTitulo').textContent = divId ? 'Modificar división' : 'Dividir ítem';
        document.getElementById('modalDivSubtitulo').textContent = nombre + ' · Total: ' + formatCOP(subtotal);
        const n = partesActuales ? partesActuales.length : 2;
        modalN.value = n;
        renderModalPartes(n, partesActuales);
        modalOverlay.classList.add('visible');
        modalN.focus();
    }

    function cerrarModal() {
        if (modalOverlay) modalOverlay.classList.remove('visible');
        modalItemId = null;
        modalDivId  = null;
    }

    function renderModalPartes(n, existentes = null) {
        modalPartes.innerHTML = '';
        const equalAmount = Math.floor(modalSubtotal / n);
        let remainder = modalSubtotal - (equalAmount * n);
        for (let i = 0; i < n; i++) {
            const defaultVal = existentes ? existentes[i]?.monto : (equalAmount + (i === 0 ? remainder : 0));
            const row = document.createElement('div');
            row.className = 'modal-parte-row';
            row.innerHTML = `
                <span class="modal-parte-label">Parte ${i+1}</span>
                <input type="number" class="modal-parte-input" data-idx="${i}" value="${Math.round(defaultVal)}" min="1" step="1">
            `;
            modalPartes.appendChild(row);
        }
        validarModal();
        modalPartes.querySelectorAll('input').forEach(inp => inp.addEventListener('input', validarModal));
    }

    function validarModal() {
        const inputs = Array.from(modalPartes.querySelectorAll('input'));
        const suma   = inputs.reduce((acc, inp) => acc + (parseFloat(inp.value) || 0), 0);
        const diff   = modalSubtotal - suma;
        if (Math.abs(diff) < 0.5) {
            modalRestante.textContent = '✓ Los montos suman correctamente';
            modalRestante.className   = 'modal-restante';
            modalConfirm.disabled     = false;
        } else {
            modalRestante.textContent = diff > 0
                ? 'Falta asignar: ' + formatCOP(diff)
                : 'Excedido en: ' + formatCOP(-diff);
            modalRestante.className   = 'modal-restante error';
            modalConfirm.disabled     = true;
        }
    }

    if (modalN) {
        modalN.addEventListener('input', function () {
            const n = Math.max(2, Math.min(20, parseInt(this.value) || 2));
            renderModalPartes(n);
        });
    }

    if (modalCancelar) modalCancelar.addEventListener('click', cerrarModal);
    if (modalOverlay)  modalOverlay.addEventListener('click', e => { if (e.target === modalOverlay) cerrarModal(); });

    if (modalConfirm) {
        modalConfirm.addEventListener('click', async function () {
            const inputs = Array.from(modalPartes.querySelectorAll('input'));
            const montos = inputs.map(inp => parseFloat(inp.value));
            const n      = montos.length;
            modalConfirm.disabled    = true;
            modalConfirm.textContent = 'Guardando...';
            try {
                if (modalDivId) {
                    await ajaxPost(`/factura/${PEDIDO_ID}/division/${modalDivId}`, { token: getToken(), partes: n, montos }, 'PATCH');
                } else {
                    await ajaxPost(`/factura/${PEDIDO_ID}/item/${modalItemId}/dividir`, { token: getToken(), partes: n, montos });
                }
                cerrarModal();
                sincronizar();
            } catch (err) {
                alert(err.message || 'Error al procesar la división');
                modalConfirm.disabled    = false;
                modalConfirm.textContent = 'Confirmar división';
            }
        });
    }

    // ── Evento click en botón Dividir ───────────────────────────────────
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-dividir');
        if (!btn) return;
        const itemId   = parseInt(btn.dataset.itemId);
        const subtotal = parseFloat(btn.dataset.subtotal);
        const nombre   = btn.dataset.nombre;
        abrirModal(itemId, subtotal, nombre);
    });

    // ── Renderizar divisiones (llamado desde sincronizar) ────────────────
    function renderDivisiones(divisiones) {
        // Remove stale division rows
        document.querySelectorAll('tr.division-row').forEach(r => {
            const divId = parseInt(r.dataset.divisionId);
            if (!divisiones.find(d => d.id_division === divId)) r.remove();
        });

        divisiones.forEach(div => {
            const itemRow = document.querySelector(`tr[data-item-id="${div.id_item}"]`);
            if (!itemRow) return;

            // Disable checkbox for this item
            const cb = itemRow.querySelector('input.pay-checkbox');
            if (cb) { cb.checked = false; cb.disabled = true; }

            // Hide "Dividir" btn for this item
            const btnDiv = itemRow.querySelector('.btn-dividir');
            if (btnDiv) btnDiv.style.display = 'none';

            let divRow = document.querySelector(`tr.division-row[data-division-id="${div.id_division}"]`);
            if (!divRow) {
                divRow = document.createElement('tr');
                divRow.className = 'division-row';
                divRow.dataset.divisionId = div.id_division;
                itemRow.after(divRow);
            }

            const tomadas      = div.partes.filter(p => p.estado === 'tomada').length;
            const miParte      = div.partes.find(p => p.es_mia);
            const partesHTML   = div.partes.map(p => {
                let claseExtra = '', contenidoExtra = '';
                if (p.estado === 'tomada' && p.es_mia) {
                    claseExtra    = `tomada-mia div-mia-monto`;
                    contenidoExtra = `<span class="div-parte-estado" style="color:#6B21E8;font-weight:700;">✓ Tu parte</span>
                                     <button class="btn-liberar" data-div-id="${div.id_division}">Liberar</button>`;
                } else if (p.estado === 'tomada') {
                    claseExtra    = 'tomada-otro';
                    contenidoExtra = `<span class="div-parte-estado" style="color:#9B8EC4;">Tomada</span>`;
                } else {
                    const yaToméOtra = !!miParte;
                    contenidoExtra = `<button class="btn-tomar" data-div-id="${div.id_division}" data-parte-id="${p.id_parte}">${yaToméOtra ? 'Cambiar a esta' : 'Tomar'}</button>`;
                }
                return `<div class="div-parte ${claseExtra}" data-monto="${p.monto}">
                    <span class="div-parte-num">Parte ${p.numero_parte}</span>
                    <span class="div-parte-monto">${formatCOP(p.monto)}</span>
                    ${contenidoExtra}
                </div>`;
            }).join('');

            const accionesHTML = `
                <div class="division-btns">
                    <button class="btn-div-accion btn-div-mod"
                        data-div-id="${div.id_division}"
                        data-item-id="${div.id_item}"
                        data-total-partes="${div.total_partes}">Modificar</button>
                    <button class="btn-div-accion btn-div-cancel" data-div-id="${div.id_division}">Cancelar división</button>
                </div>`;

            divRow.innerHTML = `<td colspan="99">
                <div class="division-panel">
                    <div class="division-header">
                        <div>
                            <span class="division-titulo">División activa</span>
                            <span class="division-sub">${tomadas} de ${div.total_partes} partes tomadas</span>
                        </div>
                        ${accionesHTML}
                    </div>
                    <div class="division-partes">${partesHTML}</div>
                    ${tomadas < div.total_partes && !miParte ? '<p class="div-esperando">Selecciona la parte que quieres pagar</p>' : ''}
                </div>
            </td>`;
        });

        // Re-enable checkboxes for items NOT in a division
        document.querySelectorAll('tr[data-item-id]').forEach(row => {
            const itemId  = parseInt(row.dataset.itemId);
            const enDiv   = divisiones.some(d => d.id_item === itemId);
            const cb      = row.querySelector('input.pay-checkbox');
            const btnDiv  = row.querySelector('.btn-dividir');
            if (!enDiv) {
                if (cb && cb.disabled) {
                    // Only re-enable if it was disabled by division (not by being Pagado)
                    const badge = row.querySelector('.estado-badge');
                    if (badge && badge.textContent.trim() === 'Pendiente') {
                        cb.disabled = false;
                    }
                }
                if (btnDiv) btnDiv.style.display = '';
            }
        });

        actualizar();
    }

    // ── Delegación de eventos para botones de división ──────────────────
    document.addEventListener('click', async function (e) {
        const token = getToken();

        // Tomar parte
        const btnTomar = e.target.closest('.btn-tomar');
        if (btnTomar) {
            btnTomar.disabled    = true;
            btnTomar.textContent = '...';
            try {
                const res = await ajaxPost(`/factura/${PEDIDO_ID}/division/${btnTomar.dataset.divId}/tomar`, {
                    token,
                    id_parte: parseInt(btnTomar.dataset.parteId),
                });
                if (res.confirmado) { location.reload(); return; }
                sincronizar();
            } catch (err) {
                alert(err.message);
                btnTomar.disabled    = false;
                btnTomar.textContent = 'Tomar';
            }
            return;
        }

        // Liberar parte
        const btnLiberar = e.target.closest('.btn-liberar');
        if (btnLiberar) {
            btnLiberar.disabled = true;
            try {
                await ajaxPost(`/factura/${PEDIDO_ID}/division/${btnLiberar.dataset.divId}/liberar`, { token });
                sincronizar();
            } catch (err) {
                alert(err.message);
                btnLiberar.disabled = false;
            }
            return;
        }

        // Cancelar división
        const btnCancel = e.target.closest('.btn-div-cancel');
        if (btnCancel) {
            if (!confirm('¿Cancelar la división? El ítem se ajustará al monto pendiente (descontando partes ya pagadas).')) return;
            btnCancel.disabled = true;
            try {
                await ajaxPost(`/factura/${PEDIDO_ID}/division/${btnCancel.dataset.divId}/cancelar`, { token });
                sincronizar();
            } catch (err) {
                alert(err.message);
                btnCancel.disabled = false;
            }
            return;
        }

        // Modificar división
        const btnMod = e.target.closest('.btn-div-mod');
        if (btnMod) {
            const divId  = parseInt(btnMod.dataset.divId);
            const itemId = parseInt(btnMod.dataset.itemId);
            const itemRow = document.querySelector(`tr[data-item-id="${itemId}"]`);
            if (!itemRow) return;
            const divRow  = itemRow.nextElementSibling;
            // Read montos from the rendered parts
            const montos = Array.from(divRow ? divRow.querySelectorAll('.div-parte-monto') : [])
                .map(el => parseFloat(el.textContent.replace(/[^0-9]/g, '')) || 0);
            const subtotal = montos.reduce((a, b) => a + b, 0);
            const nombre   = itemRow.querySelector('.producto-nombre')?.textContent || 'Ítem';
            const partesActuales = montos.map(m => ({ monto: m }));
            abrirModal(itemId, subtotal, nombre, divId, partesActuales);
            return;
        }
    });

});

// ── Buscador de productos (agregar ítem) ─────────────────────────────
(function () {
    const searchInput = document.getElementById('prodSearchInput');
    const hiddenInput = document.getElementById('prodSeleccionado');
    const dropdown    = document.getElementById('prodDropdown');
    const form        = document.getElementById('formAgregarProducto');
    if (!searchInput) return;

    const allOptions  = () => Array.from(dropdown.querySelectorAll('.prod-option'));
    const noResults   = dropdown.querySelector('.prod-sin-resultados');

    function posicionarDropdown() {
        const rect = searchInput.getBoundingClientRect();
        dropdown.style.top   = (rect.bottom + 4) + 'px';
        dropdown.style.left  = rect.left + 'px';
        dropdown.style.width = rect.width + 'px';
    }

    function filtrar(q) {
        const texto = q.toLowerCase().trim();
        let visibles = 0;
        allOptions().forEach(opt => {
            const match = opt.dataset.nombre.toLowerCase().includes(texto);
            opt.style.display = match ? '' : 'none';
            if (match) visibles++;
        });
        noResults.style.display = visibles === 0 ? '' : 'none';
    }

    function seleccionar(opt) {
        hiddenInput.value = opt.dataset.id;
        searchInput.value = opt.dataset.nombre;
        searchInput.classList.add('tiene-seleccion');
        allOptions().forEach(o => o.classList.remove('activo'));
        opt.classList.add('activo');
        dropdown.classList.remove('visible');
    }

    function limpiarSeleccion() {
        hiddenInput.value = '';
        searchInput.classList.remove('tiene-seleccion');
    }

    searchInput.addEventListener('focus', () => {
        posicionarDropdown();
        filtrar(searchInput.value);
        dropdown.classList.add('visible');
    });

    searchInput.addEventListener('input', () => {
        limpiarSeleccion();
        posicionarDropdown();
        filtrar(searchInput.value);
        dropdown.classList.add('visible');
    });

    window.addEventListener('scroll', () => { if (dropdown.classList.contains('visible')) posicionarDropdown(); }, true);
    window.addEventListener('resize', () => { if (dropdown.classList.contains('visible')) posicionarDropdown(); });

    dropdown.addEventListener('mousedown', e => {
        const opt = e.target.closest('.prod-option');
        if (opt) { e.preventDefault(); seleccionar(opt); }
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.prod-search-wrap')) {
            dropdown.classList.remove('visible');
            // Si escribió algo pero no seleccionó, limpiar
            if (!hiddenInput.value) searchInput.value = '';
        }
    });

    // Navegación con teclado
    searchInput.addEventListener('keydown', e => {
        const visible = allOptions().filter(o => o.style.display !== 'none');
        const activo  = dropdown.querySelector('.prod-option.activo');
        const idx     = visible.indexOf(activo);
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = visible[idx + 1] || visible[0];
            if (next) { allOptions().forEach(o => o.classList.remove('activo')); next.classList.add('activo'); next.scrollIntoView({ block: 'nearest' }); }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = visible[idx - 1] || visible[visible.length - 1];
            if (prev) { allOptions().forEach(o => o.classList.remove('activo')); prev.classList.add('activo'); prev.scrollIntoView({ block: 'nearest' }); }
        } else if (e.key === 'Enter') {
            const act = dropdown.querySelector('.prod-option.activo');
            if (act && dropdown.classList.contains('visible')) { e.preventDefault(); seleccionar(act); }
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('visible');
        }
    });

    form.addEventListener('submit', e => {
        if (!hiddenInput.value) {
            e.preventDefault();
            searchInput.focus();
            searchInput.style.borderColor = '#dc2626';
            setTimeout(() => searchInput.style.borderColor = '', 1500);
        }
    });
})();
</script>

</body>
</html>
