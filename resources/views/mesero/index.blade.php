<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesero — ASAPP</title>
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
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            gap: 16px;
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
            color: #C4A0FF;
            border: 1px solid rgba(107,33,232,0.3);
            padding: 7px 16px;
            border-radius: 8px;
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
            background: #fff;
            border-radius: 14px;
            padding: 20px;
            border: 1px solid #E0D9F5;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(107,33,232,0.06);
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #3D0E8A;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── BOTONES ── */
        .btn {
            padding: 10px 18px;
            border-radius: 8px;
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
            min-height: 40px;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: #6B21E8; color: #fff; }
        .btn-success { background: #3D0E8A; color: #fff; }
        .btn-outline { background: transparent; color: #6B21E8; border: 1px solid #6B21E8; }
        .btn-sm      { padding: 8px 14px; font-size: 13px; min-height: 36px; }
        .btn-block   { width: 100%; }

        /* ── GRID MESAS ── */
        .mesas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .mesa-card {
            background: #FAF8FF;
            border: 2px solid #E0D9F5;
            border-radius: 14px;
            padding: 20px 16px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
            cursor: default;
        }
        .mesa-card.ocupada { border-color: #8B5CF6; background: #EDE9FE; }
        .mesa-card.libre   { border-color: #D4C9F0; background: #F5F3FF; }
        .mesa-card:hover   { box-shadow: 0 4px 16px rgba(107,33,232,0.12); }

        .mesa-icono  { font-size: 30px; margin-bottom: 8px; }
        .mesa-nombre { font-size: 15px; font-weight: 700; color: #3D0E8A; margin-bottom: 6px; }

        .mesa-estado {
            font-size: 12px;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .estado-libre   { background: #F5F3FF; color: #9B8EC4; }
        .estado-ocupada { background: #C4B5FD; color: #3D0E8A; }
        .mesa-acciones  { display: flex; flex-direction: column; gap: 6px; }

        /* ── PISO LABEL ── */
        .piso-label {
            font-size: 14px;
            font-weight: 700;
            color: #3D0E8A;
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
            background: #fff;
            border-radius: 20px 20px 0 0;
            padding: 24px 20px 20px;
            width: 100%;
            max-width: 640px;
            max-height: 92dvh;
            box-shadow: 0 -8px 40px rgba(0,0,0,0.25);
            display: flex;
            flex-direction: column;
        }

        .np-handle {
            width: 40px; height: 4px;
            background: #D4C9F0;
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
        .np-header h3 { font-size: 17px; font-weight: 700; color: #3D0E8A; }

        .np-close {
            background: #F5F3FF;
            border: none;
            font-size: 15px;
            color: #6B21E8;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 8px;
            line-height: 1;
            min-width: 36px;
            min-height: 36px;
        }

        /* buscador */
        #np-buscador {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #D4C9F0;
            border-radius: 10px;
            font-size: 15px;
            background: #FAF8FF;
            color: #1a1a2e;
            outline: none;
            font-family: inherit;
            margin-bottom: 10px;
            flex-shrink: 0;
        }
        #np-buscador:focus { border-color: #6B21E8; }

        .np-body {
            overflow-y: auto;
            flex: 1;
            -webkit-overflow-scrolling: touch;
        }
        .np-body::-webkit-scrollbar { width: 4px; }
        .np-body::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 4px; }

        /* Lista de productos — tarjetas táctiles */
        .prod-lista { display: flex; flex-direction: column; gap: 2px; }

        .prod-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 10px;
            border-radius: 10px;
            border-bottom: 1px solid #F0EBF8;
            transition: background 0.15s;
            cursor: pointer;
            user-select: none;
        }
        .prod-item:active  { background: #F5F3FF; }
        .prod-item.selected { background: #EDE9FE; }
        .prod-item.hidden  { display: none; }

        .prod-check {
            width: 22px; height: 22px;
            accent-color: #6B21E8;
            cursor: pointer;
            flex-shrink: 0;
        }

        .prod-info { flex: 1; min-width: 0; }
        .prod-nombre { font-size: 14px; font-weight: 600; color: #1a1a2e; }
        .prod-cat    { font-size: 12px; color: #9B8EC4; }

        .prod-precio { font-size: 13px; font-weight: 700; color: #6B21E8; white-space: nowrap; flex-shrink: 0; }

        .prod-cant {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }
        .cant-btn {
            width: 30px; height: 30px;
            background: #EDE9FE;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            color: #6B21E8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            font-weight: 700;
        }
        .cant-btn:active { background: #C4B5FD; }
        .cant-num {
            width: 32px;
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            color: #3D0E8A;
        }
        .prod-cant-wrap { display: none; }
        .prod-item.selected .prod-cant-wrap { display: flex; }

        .np-footer {
            display: flex;
            gap: 10px;
            padding-top: 14px;
            flex-shrink: 0;
            border-top: 1px solid #E0D9F5;
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
        #mesero-toast.toast-ok  { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }
        #mesero-toast.toast-err { background: #FFF0F0; color: #C8102E; border: 1px solid #F5C6CB; }

        .toast-inner { display: flex; align-items: center; gap: 12px; }
        .toast-close { background: none; border: none; font-size: 16px; cursor: pointer; opacity: 0.5; color: inherit; padding: 0; flex-shrink: 0; }
        .toast-close:hover { opacity: 1; }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; color: #9B8EC4; font-size: 14px; padding: 48px 0; }

        /* ── TABLET (≥ 640px) ── */
        @media (min-width: 640px) {
            .modal {
                border-radius: 20px;
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
        <span class="topbar-info">
            👤 <strong>{{ auth()->user()->nombre }}</strong>
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
                    <div class="piso-label">🏢 {{ $piso->nombre }}</div>
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

                                <div class="mesa-icono">{{ $ocupada ? '🟡' : '🟢' }} {{ $esSecundaria ? '🔗' : '🪑' }}</div>
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
                                    <div style="font-size:11px; color:#7C3AED; background:#EDE9FE; border-radius:6px;
                                                padding:3px 8px; margin-bottom:8px; text-align:center;">
                                        🔗 Unida a: <strong>{{ $mesa->mesaPrincipal->nombre_display }}</strong>
                                    </div>
                                @elseif ($mesa->mesasUnidas->isNotEmpty())
                                    <div style="font-size:11px; color:#6B21A8; background:#F3E8FF; border-radius:6px;
                                                padding:3px 8px; margin-bottom:8px; text-align:center;">
                                        🪑 Grupo: {{ $mesa->mesasUnidas->map(fn($m) => $m->nombre_display)->prepend($nombreDisplay)->join(' + ') }}
                                    </div>
                                @endif

                                <div class="mesa-acciones">
                                    @if ($ocupada)
                                        <a href="{{ route('factura.show', $pedidoActivo->id_pedido) }}"
                                           class="btn btn-success btn-sm">
                                            📄 Ver factura
                                        </a>
                                    @else
                                        @if (! $esSecundaria)
                                            <button class="btn btn-primary btn-sm"
                                                    onclick="abrirNuevoPedido({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                                                🧾 Nuevo pedido
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
                <div class="card-title" style="color:#DC2626;">⚠️ Sin piso asignado</div>
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
                            <div class="mesa-icono">{{ $ocupada ? '🟡' : '🟢' }} 🪑</div>
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
            <h3 id="np-titulo">🧾 Nuevo pedido</h3>
            <button class="np-close" onclick="cerrarNuevoPedido()">✕</button>
        </div>

        @php $productosDisp = $productos->filter(fn($p) => $p->disponible)->values(); @endphp

        @if ($productosDisp->isEmpty())
            <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
                No hay productos disponibles. Pide al administrador que los active.
            </p>
        @else
            <input type="text" id="np-buscador" placeholder="🔍 Buscar producto…"
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
                <button type="button" class="btn btn-primary" id="btn-crear-pedido" onclick="crearPedido()">✅ Crear pedido</button>
            </div>
        @endif
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── Modal Nuevo Pedido ────────────────────────────────────────────────
function abrirNuevoPedido(idMesa, nombreMesa) {
    document.getElementById('np-id-mesa').value = idMesa;
    document.getElementById('np-titulo').textContent = '🧾 Nuevo pedido — ' + nombreMesa;

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
        showToast('⚠️ Selecciona al menos un producto.', false);
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
        showToast('❌ Error de conexión', false);
        btn.disabled    = false;
        btn.textContent = '✅ Crear pedido';
        return;
    }

    if (res.status === 422) {
        const msg = data.errors
            ? Object.values(data.errors).flat().join(' · ')
            : (data.message || 'Error de validación');
        showToast('❌ ' + msg, false);
        btn.disabled    = false;
        btn.textContent = '✅ Crear pedido';
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
        showToast('❌ ' + (data.message || 'Error al crear el pedido'), false);
        btn.disabled    = false;
        btn.textContent = '✅ Crear pedido';
    }
}

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, ok = true) {
    const t = document.getElementById('mesero-toast');
    t.innerHTML = `<div class="toast-inner"><span>${msg}</span><button class="toast-close" onclick="document.getElementById('mesero-toast').classList.remove('show')">✕</button></div>`;
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
