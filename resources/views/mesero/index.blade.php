<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesero — ASAPP</title>
    <link rel="stylesheet" href="/css/asapp-base.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

        :root {
          --bg:#F4F1FA; --surface:#ffffff; --surface2:#FAF8FF;
          --border:#E0D9F5; --border-soft:#EDE9F8;
          --sb-bg:#0F0A1E;
          --purple:#6B21E8; --purple-dk:#3D0E8A; --purple-lt:#8B5CF6;
          --purple-dim:rgba(107,33,232,0.10); --purple-glow:rgba(107,33,232,0.20);
          --accent:#C4A0FF;
          --text:#1a1a2e; --text-muted:#6B7280; --text-faint:#9B8EC4;
          --danger:#B91C1C; --danger-bg:#FEF2F2; --danger-border:#FECACA;
          --r-sm:6px; --r-md:10px; --r-lg:14px; --r-xl:20px;
          --shadow-sm:0 1px 4px rgba(107,33,232,0.06);
          --shadow-md:0 4px 16px rgba(107,33,232,0.10);
          --shadow-lg:0 8px 32px rgba(0,0,0,0.18);
          --font:'Plus Jakarta Sans',system-ui,sans-serif;
          --mono:ui-monospace,'SF Mono',Menlo,monospace;
        }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--sb-bg);
            color: #fff;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-lg);
        }

        .topbar-logo {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #C4A0FF, #A78BFA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            flex-shrink: 0;
        }

        .topbar-center {
            flex: 1;
            text-align: center;
            font-size: 13px;
            color: rgba(255,255,255,0.7);
        }
        .topbar-center strong { color: #fff; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .topbar-info { font-size: 13px; color: rgba(255,255,255,0.7); }
        .topbar-info strong { color: #fff; }

        .btn-logout {
            background: rgba(107,33,232,0.15);
            color: var(--accent);
            border: 1px solid rgba(107,33,232,0.3);
            padding: 7px 16px;
            border-radius: var(--r-md);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-logout:hover { background: rgba(107,33,232,0.3); }

        /* ── CONTENIDO ── */
        .content {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 20px;
        }

        /* ── CARDS ── */
        .card {
            background: var(--surface);
            border-radius: var(--r-lg);
            padding: 20px;
            border: 1px solid var(--border);
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── BOTONES ── */
        .btn {
            padding: 10px 18px;
            border-radius: var(--r-md);
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: opacity 0.2s;
            white-space: nowrap;
            min-height: 44px;
            touch-action: manipulation;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: var(--purple); color: #fff; box-shadow: 0 2px 8px rgba(107,33,232,0.25); }
        .btn-success { background: var(--purple-dk); color: #fff; }
        .btn-outline { background: transparent; color: var(--text-muted); border: 1.5px solid var(--border); }
        .btn-sm      { padding: 8px 14px; font-size: 13px; min-height: 44px; }
        .btn-block   { width: 100%; }

        /* ── GRID MESAS ── */
        .mesas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .mesa-card {
            background: var(--surface2);
            border: 2px solid var(--border);
            border-radius: var(--r-lg);
            padding: 20px 16px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: default;
        }
        .mesa-card.ocupada { border-color: var(--purple-lt); background: #EDE9FE; }
        .mesa-card.libre   { border-color: var(--border); background: var(--surface2); }
        .mesa-card:hover   { box-shadow: var(--shadow-md); }

        .mesa-icono  { display:flex; align-items:center; justify-content:center; gap:6px; margin-bottom:10px; color:var(--purple); }
        .mesa-status-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
        .dot-libre   { background:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,0.2); }
        .dot-ocupada { background:#eab308; box-shadow:0 0 0 3px rgba(234,179,8,0.2); }
        .mesa-nombre { font-size: 15px; font-weight: 700; color: var(--purple-dk); margin-bottom: 6px; }

        .mesa-estado {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .estado-libre   { background: var(--surface2); color: var(--text-faint); }
        .estado-ocupada { background: #C4B5FD; color: var(--purple-dk); }
        .mesa-acciones  { display: flex; flex-direction: column; gap: 6px; }

        /* ── PISO LABEL ── */
        .piso-label {
            font-size: 14px;
            font-weight: 700;
            color: var(--purple-dk);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── MODAL ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: 999;
            align-items: flex-end;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }

        .modal {
            background: var(--surface);
            border-radius: var(--r-xl) var(--r-xl) 0 0;
            padding: 24px 20px 20px;
            width: 100%;
            max-width: 640px;
            max-height: 92dvh;
            box-shadow: var(--shadow-lg);
            display: flex;
            flex-direction: column;
        }

        .np-handle {
            width: 40px; height: 4px;
            background: var(--border);
            border-radius: 4px;
            margin: 0 auto 16px;
            flex-shrink: 0;
        }

        .np-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            flex-shrink: 0;
        }
        .np-header h3 { font-size: 17px; font-weight: 700; color: var(--purple-dk); }

        .np-close {
            background: var(--purple-dim);
            border: none;
            color: var(--purple);
            cursor: pointer;
            padding: 0;
            border-radius: var(--r-md);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            min-height: 44px;
            touch-action: manipulation;
        }

        /* buscador */
        #np-buscador {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: var(--r-md);
            font-size: 15px;
            background: var(--surface2);
            color: var(--text);
            outline: none;
            font-family: inherit;
            margin-bottom: 10px;
            flex-shrink: 0;
        }
        #np-buscador:focus { border-color: var(--purple); box-shadow: 0 0 0 3px var(--purple-glow); }

        .np-body {
            overflow-y: auto;
            flex: 1;
            -webkit-overflow-scrolling: touch;
        }
        .np-body::-webkit-scrollbar { width: 4px; }
        .np-body::-webkit-scrollbar-thumb { background: var(--accent); border-radius: 4px; }

        /* Lista de productos — tarjetas táctiles */
        .prod-lista { display: flex; flex-direction: column; gap: 2px; }

        .prod-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 10px;
            border-radius: var(--r-md);
            border-bottom: 1px solid var(--border-soft);
            transition: background 0.15s;
            cursor: pointer;
            user-select: none;
        }
        .prod-item:active  { background: var(--surface2); }
        .prod-item.selected { background: #EDE9FE; }
        .prod-item.hidden  { display: none; }

        .prod-info { flex: 1; min-width: 0; }
        .prod-nombre { font-size: 14px; font-weight: 600; color: var(--text); }
        .prod-cat    { font-size: 12px; color: var(--text-faint); }

        .prod-precio { font-size: 13px; font-weight: 700; color: var(--purple); white-space: nowrap; flex-shrink: 0; }

        .prod-cant {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .cant-btn {
            width: 44px; height: 44px;
            background: var(--purple-dim);
            border: none;
            border-radius: var(--r-md);
            font-size: 20px;
            color: var(--purple);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            font-weight: 700;
            touch-action: manipulation;
            flex-shrink: 0;
        }
        .cant-btn:active { background: #C4B5FD; }
        .cant-num {
            width: 32px;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: var(--purple-dk);
        }
        .prod-cant-wrap { display: none; }
        .prod-item.selected .prod-cant-wrap { display: flex; }

        .np-footer {
            display: flex;
            gap: 10px;
            padding-top: 14px;
            flex-shrink: 0;
            border-top: 1px solid var(--border);
            margin-top: 10px;
        }

        /* ── TOAST ── */
        #mesero-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(12px);
            padding: 13px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            width: calc(100% - 32px);
            max-width: 420px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.25s, transform 0.25s;
            pointer-events: none;
        }
        #mesero-toast.show      { opacity: 1; transform: translateX(-50%) translateY(0); pointer-events: auto; }
        #mesero-toast.toast-ok  { background: var(--purple-dim); color: var(--purple-dk); border: 1px solid #C4B5FD; }
        #mesero-toast.toast-err { background: #FFF0F0; color: #C8102E; border: 1px solid #F5C6CB; }

        .toast-inner { display: flex; align-items: center; gap: 12px; }
        .toast-close { background: none; border: none; font-size: 16px; cursor: pointer; opacity: 0.5; color: inherit; padding: 0; flex-shrink: 0; }
        .toast-close:hover { opacity: 1; }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; color: var(--text-faint); font-size: 14px; padding: 48px 0; }

        /* ── TABLET (≥ 640px) ── */
        @media (min-width: 640px) {
            .modal {
                border-radius: var(--r-xl);
                max-width: 580px;
                margin-bottom: 40px;
                max-height: 88vh;
            }
            .modal-overlay { align-items: center; }
            .np-handle { display: none; }
            .mesas-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
        }

        /* ── DESKTOP (≥ 1024px) ── */
        @media (min-width: 1024px) {
            .mesas-grid { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
            .content { margin: 32px auto; }
        }

        /* ── MÓVIL PEQUEÑO (< 400px) ── */
        @media (max-width: 400px) {
            .topbar { padding: 0 12px; height: 54px; }
            .topbar-logo { font-size: 18px; }
            .topbar-center { display: none; }
            .topbar-info  { display: none; }
            .content { padding: 0 10px; margin: 12px auto; }
            .mesas-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .mesa-card { padding: 14px 10px; }
            .mesa-icono { font-size: 24px; }
            .mesa-nombre { font-size: 13px; }
            .card { padding: 14px; }
        }
    </style>
</head>
<body>

{{-- TOPBAR --}}
<div class="topbar">
    <div class="topbar-logo">ASAPP</div>

    <div class="topbar-center topbar-info">
        <strong>{{ $negocio->nombre }}</strong>
    </div>

    <div class="topbar-right">
        <span class="topbar-info" style="display:flex;align-items:center;gap:6px;">
            <svg viewBox="0 0 20 20" fill="currentColor" width="15" height="15" style="opacity:0.7;flex-shrink:0;"><path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z"/></svg>
            <strong>{{ auth()->user()->nombre }}</strong>
        </span>
        <form action="{{ route('logout') }}" method="POST" style="margin:0">
            @csrf
            <button type="submit" class="btn-logout">Cerrar sesión</button>
        </form>
    </div>
</div>

{{-- CONTENIDO --}}
<div class="content">

    @if ($pisos->isEmpty() && $mesas->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div style="font-size:48px; margin-bottom:16px;">🪑</div>
                <p>No hay mesas configuradas en este negocio.</p>
                <p style="margin-top:8px; font-size:13px;">Contacta al administrador para crear pisos y mesas.</p>
            </div>
        </div>
    @else

        @php $mesasPorPiso = $mesas->groupBy('id_piso'); @endphp

        @foreach ($pisos as $piso)
            @php $mesasDePiso = $mesasPorPiso->get($piso->id_piso, collect()); @endphp
            @if ($mesasDePiso->isNotEmpty())
                <div class="card">
                    <div class="piso-label">
                        <svg viewBox="0 0 20 20" fill="currentColor" width="15" height="15" style="flex-shrink:0;"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/></svg>
                        {{ $piso->nombre }}
                    </div>
                    <div class="mesas-grid">
                        @foreach ($mesasDePiso as $mesa)
                            @php
                                $esSecundaria = $mesa->estaUnida();
                                if ($esSecundaria) {
                                    $pedidoActivo = $mesa->mesaPrincipal->pedidos->first() ?? null;
                                } else {
                                    $pedidoActivo = $mesa->pedidos->first();
                                }
                                $ocupada       = (bool) $pedidoActivo;
                                $nombreDisplay = $mesa->nombre_display;
                            @endphp
                            <div class="mesa-card {{ $ocupada ? 'ocupada' : 'libre' }}"
                                 style="{{ $esSecundaria ? 'border-style: dashed; opacity: 0.85;' : '' }}">

                                <div class="mesa-icono">
                                    <span class="mesa-status-dot {{ $ocupada ? 'dot-ocupada' : 'dot-libre' }}"></span>
                                    @if ($esSecundaria)
                                        <svg viewBox="0 0 20 20" fill="currentColor" width="22" height="22"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg viewBox="0 0 20 20" fill="currentColor" width="22" height="22"><path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z" clip-rule="evenodd"/></svg>
                                    @endif
                                </div>
                                <div class="mesa-nombre">{{ $nombreDisplay }}</div>

                                @if ($mesa->alias)
                                    <div style="font-size:10px; color:#9B8EC4; margin-top:-4px; margin-bottom:4px;">
                                        {{ $mesa->nombre }}
                                    </div>
                                @endif

                                <span class="mesa-estado {{ $ocupada ? 'estado-ocupada' : 'estado-libre' }}">
                                    {{ $ocupada ? 'Ocupada' : 'Libre' }}
                                </span>

                                @if ($esSecundaria)
                                    <div style="font-size:11px; color:#7C3AED; background:#EDE9FE; border-radius:6px; padding:3px 8px; margin-bottom:8px; text-align:center; display:flex; align-items:center; justify-content:center; gap:4px;">
                                        <svg viewBox="0 0 20 20" fill="currentColor" width="11" height="11"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/></svg>
                                        Unida a: <strong>{{ $mesa->mesaPrincipal->nombre_display }}</strong>
                                    </div>
                                @elseif ($mesa->mesasUnidas->isNotEmpty())
                                    <div style="font-size:11px; color:#6B21A8; background:#F3E8FF; border-radius:6px; padding:3px 8px; margin-bottom:8px; text-align:center;">
                                        Grupo: {{ $mesa->mesasUnidas->map(fn($m) => $m->nombre_display)->prepend($nombreDisplay)->join(' + ') }}
                                    </div>
                                @endif

                                <div class="mesa-acciones">
                                    @if ($ocupada)
                                        <a href="{{ route('factura.show', $pedidoActivo->id_pedido) }}"
                                           class="btn btn-success btn-sm">
                                            <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                            Ver factura
                                        </a>
                                    @else
                                        @if (! $esSecundaria)
                                            <button class="btn btn-primary btn-sm"
                                                    onclick="abrirNuevoPedido({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                                                <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                                                Nuevo pedido
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Mesas sin piso --}}
        @php $mesasSinPiso = $mesasPorPiso->get(null, collect()); @endphp
        @if ($mesasSinPiso->isNotEmpty())
            <div class="card" style="border-color:#FECACA;">
                <div class="card-title" style="color:#DC2626;">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16" style="flex-shrink:0;"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                    Sin piso asignado
                </div>
                <div class="mesas-grid">
                    @foreach ($mesasSinPiso as $mesa)
                        @php
                            $esSecundaria = $mesa->estaUnida();
                            $pedidoActivo = $esSecundaria
                                ? ($mesa->mesaPrincipal->pedidos->first() ?? null)
                                : $mesa->pedidos->first();
                            $ocupada       = (bool) $pedidoActivo;
                            $nombreDisplay = $mesa->nombre_display;
                        @endphp
                        <div class="mesa-card {{ $ocupada ? 'ocupada' : 'libre' }}">
                            <div class="mesa-icono">
                                <span class="mesa-status-dot {{ $ocupada ? 'dot-ocupada' : 'dot-libre' }}"></span>
                                <svg viewBox="0 0 20 20" fill="currentColor" width="22" height="22"><path fill-rule="evenodd" d="M5 4a3 3 0 00-3 3v6a3 3 0 003 3h10a3 3 0 003-3V7a3 3 0 00-3-3H5zm-1 9v-1h5v2H5a1 1 0 01-1-1zm7 1h4a1 1 0 001-1v-1h-5v2zm0-4h5V8h-5v2zM9 8H4v2h5V8z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="mesa-nombre">{{ $nombreDisplay }}</div>
                            <span class="mesa-estado {{ $ocupada ? 'estado-ocupada' : 'estado-libre' }}">
                                {{ $ocupada ? 'Ocupada' : 'Libre' }}
                            </span>
                            <div class="mesa-acciones">
                                @if ($ocupada)
                                    <a href="{{ route('factura.show', $pedidoActivo->id_pedido) }}"
                                       class="btn btn-success btn-sm">
                                        📄 Ver factura
                                    </a>
                                @else
                                    <button class="btn btn-primary btn-sm"
                                            onclick="abrirNuevoPedido({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                                        🧾 Nuevo pedido
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif

</div>

{{-- TOAST --}}
<div id="mesero-toast"></div>

{{-- MODAL NUEVO PEDIDO --}}
<div class="modal-overlay" id="modal-np">
    <div class="modal modal-np">
        <div class="np-handle"></div>
        <div class="np-header">
            <h3 id="np-titulo">Nuevo pedido</h3>
            <button class="np-close" onclick="cerrarNuevoPedido()" aria-label="Cerrar">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>

        @php $productosDisp = $productos->filter(fn($p) => $p->disponible)->values(); @endphp

        @if ($productosDisp->isEmpty())
            <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
                No hay productos disponibles. Pide al administrador que los active.
            </p>
        @else
            <input type="text" id="np-buscador" placeholder="Buscar producto…"
                   oninput="filtrarNP(this.value)">

            <input type="hidden" id="np-id-mesa">

            <div class="np-body">
                <div class="prod-lista" id="np-lista">
                    @foreach ($productosDisp as $p)
                        <div class="prod-item"
                             data-np="{{ strtolower($p->nombre . ' ' . ($p->categoria?->nombre ?? '')) }}"
                             onclick="toggleNP(this, {{ $p->id_producto }})">
                            <input type="checkbox"
                                   class="prod-check"
                                   data-id="{{ $p->id_producto }}"
                                   onclick="event.stopPropagation()">
                            <div class="prod-info">
                                <div class="prod-nombre">{{ $p->nombre }}</div>
                                @if ($p->categoria)
                                    <div class="prod-cat">{{ $p->categoria->nombre }}</div>
                                @endif
                            </div>
                            <span class="prod-precio">${{ number_format($p->precio, 0, ',', '.') }}</span>
                            <div class="prod-cant-wrap prod-cant">
                                <button type="button" class="cant-btn"
                                        onclick="event.stopPropagation(); cambiarCant({{ $p->id_producto }}, -1)">−</button>
                                <span class="cant-num" id="np-cant-{{ $p->id_producto }}">1</span>
                                <button type="button" class="cant-btn"
                                        onclick="event.stopPropagation(); cambiarCant({{ $p->id_producto }}, 1)">+</button>
                            </div>
                        </div>
                    @endforeach
                    <div id="np-sin-resultados" style="display:none; text-align:center; color:#9B8EC4; padding:24px 0; font-size:13px;">
                        Sin productos que coincidan.
                    </div>
                </div>
            </div>

            <div class="np-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarNuevoPedido()">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-crear-pedido" onclick="crearPedido()">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Crear pedido
                </button>
            </div>
        @endif
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Modal Nuevo Pedido ────────────────────────────────────────────────
function abrirNuevoPedido(idMesa, nombreMesa) {
    document.getElementById('np-id-mesa').value = idMesa;
    document.getElementById('np-titulo').textContent = 'Nuevo pedido — ' + nombreMesa;

    // Resetear selecciones y cantidades
    document.querySelectorAll('#np-lista .prod-item').forEach(item => {
        item.classList.remove('selected');
        item.querySelector('.prod-check').checked = false;
        const id  = item.querySelector('.prod-check').dataset.id;
        const num = document.getElementById('np-cant-' + id);
        if (num) num.textContent = '1';
    });

    // Resetear buscador
    const buscador = document.getElementById('np-buscador');
    if (buscador) { buscador.value = ''; filtrarNP(''); }

    document.getElementById('modal-np').classList.add('open');
}

function cerrarNuevoPedido() {
    document.getElementById('modal-np').classList.remove('open');
}

function toggleNP(item, id) {
    const checkbox = item.querySelector('.prod-check');
    checkbox.checked = !checkbox.checked;
    item.classList.toggle('selected', checkbox.checked);
}

function cambiarCant(id, delta) {
    const span = document.getElementById('np-cant-' + id);
    if (!span) return;
    let val = parseInt(span.textContent) + delta;
    if (val < 1) val = 1;
    span.textContent = val;
}

function filtrarNP(q) {
    const term  = q.toLowerCase().trim();
    const items = document.querySelectorAll('#np-lista .prod-item');
    let visibles = 0;
    items.forEach(item => {
        const match = !term || item.dataset.np.includes(term);
        item.classList.toggle('hidden', !match);
        if (match) visibles++;
    });
    const aviso = document.getElementById('np-sin-resultados');
    if (aviso) aviso.style.display = visibles === 0 ? '' : 'none';
}

// ── Crear pedido (AJAX) ───────────────────────────────────────────────
async function crearPedido() {
    const idMesa = document.getElementById('np-id-mesa').value;
    if (!idMesa) return;

    // Recolectar productos seleccionados
    const checkboxes = document.querySelectorAll('#np-lista .prod-check:checked');
    if (checkboxes.length === 0) {
        showToast('Selecciona al menos un producto.', false);
        return;
    }

    const formData = new FormData();
    formData.append('_token', CSRF);
    formData.append('id_mesa', idMesa);

    checkboxes.forEach(cb => {
        const id = cb.dataset.id;
        formData.append('productos[]', id);
        const num = document.getElementById('np-cant-' + id);
        formData.append('cantidades[' + id + ']', num ? num.textContent : 1);
    });

    const btn = document.getElementById('btn-crear-pedido');
    btn.disabled = true;
    btn.textContent = 'Creando…';

    let res, data;
    try {
        res  = await fetch('{{ route('mesero.pedidos.store') }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     CSRF,
            },
            body: formData,
        });
        data = await res.json();
    } catch {
        showToast('Error de conexión', false);
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Crear pedido';
        return;
    }

    if (res.status === 422) {
        const msg = data.errors
            ? Object.values(data.errors).flat().join(' · ')
            : (data.message || 'Error de validación');
        showToast(msg, false);
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Crear pedido';
        return;
    }

    if (data.success) {
        cerrarNuevoPedido();
        showToast(data.message, true);
        // Redirigir a la factura tras un momento
        setTimeout(() => {
            window.location.href = data.factura_url;
        }, 900);
    } else {
        showToast(data.message || 'Error al crear el pedido', false);
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Crear pedido';
    }
}

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, ok = true) {
    const t = document.getElementById('mesero-toast');
    t.innerHTML = `<div class="toast-inner"><span>${msg}</span><button class="toast-close" aria-label="Cerrar" onclick="document.getElementById('mesero-toast').classList.remove('show')"><svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></button></div>`;
    t.className = (ok ? 'toast-ok' : 'toast-err');
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 3500);
}

// ── Cerrar modal al clic fuera ────────────────────────────────────────
document.getElementById('modal-np').addEventListener('click', function(e) {
    if (e.target === this) cerrarNuevoPedido();
});
</script>

</body>
</html>
