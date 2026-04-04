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

        .topbar-info {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
        }

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
        .btn-outline  { background: transparent; color: #6B21E8; border: 1px solid #6B21E8; }
        .btn-sm       { padding: 6px 12px; font-size: 12px; }

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
            padding: 20px;
            text-align: center;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .mesa-card.ocupada { border-color: #8B5CF6; background: #EDE9FE; }
        .mesa-card.libre   { border-color: #D4C9F0; background: #F5F3FF; }
        .mesa-card:hover   { box-shadow: 0 4px 16px rgba(107,33,232,0.12); }

        .mesa-icono  { font-size: 32px; margin-bottom: 8px; }
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

        /* ── MODAL ── */
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
            padding: 32px;
            max-width: 600px;
            width: 94%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
        }

        .modal-np { text-align: left; max-height: 90vh; display: flex; flex-direction: column; }

        .np-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .np-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: #3D0E8A;
        }

        .np-close {
            background: none;
            border: none;
            font-size: 16px;
            color: #9B8EC4;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .np-close:hover { background: #F5F3FF; color: #3D0E8A; }

        .np-body {
            overflow-y: auto;
            flex: 1;
            margin-bottom: 16px;
        }

        .np-body::-webkit-scrollbar { width: 4px; }
        .np-body::-webkit-scrollbar-track { background: #F5F3FF; border-radius: 4px; }
        .np-body::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 4px; }

        .pedido-tabla { width: 100%; font-size: 13px; border-collapse: collapse; }
        .pedido-tabla th {
            text-align: left;
            padding: 8px 10px;
            color: #6B21E8;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #E0D9F5;
            position: sticky;
            top: 0;
            background: #fff;
        }
        .pedido-tabla td { padding: 9px 10px; border-bottom: 1px solid #EDE9F8; vertical-align: middle; }
        .pedido-tabla input[type="number"] { width: 65px; padding: 5px 8px; border: 1px solid #D4C9F0; border-radius: 6px; font-size: 13px; }
        .pedido-tabla input[type="checkbox"] { width: 18px; height: 18px; accent-color: #6B21E8; cursor: pointer; }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        select {
            padding: 9px 13px;
            border: 1px solid #D4C9F0;
            border-radius: 8px;
            font-size: 14px;
            background: #FAF8FF;
            color: #1a1a2e;
            outline: none;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        input:focus, select:focus { border-color: #6B21E8; }

        /* ── TOAST ── */
        #mesero-toast {
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

        #mesero-toast.show      { opacity: 1; transform: translateY(0); pointer-events: auto; }
        #mesero-toast.toast-ok  { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }
        #mesero-toast.toast-err { background: #FFF0F0; color: #C8102E; border: 1px solid #F5C6CB; }

        .toast-inner { display: flex; align-items: center; gap: 14px; }
        .toast-close { background: none; border: none; font-size: 16px; cursor: pointer; opacity: 0.5; color: inherit; padding: 0; line-height: 1; flex-shrink: 0; }
        .toast-close:hover { opacity: 1; }

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

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            color: #9B8EC4;
            font-size: 14px;
            padding: 48px 0;
        }

        @media (max-width: 640px) {
            .topbar { padding: 0 14px; }
            .topbar-center { display: none; }
            .content { padding: 0 12px; margin: 16px auto; }
            .mesas-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
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
                   oninput="filtrarNP(this.value)"
                   style="width:100%; margin-bottom:12px; font-size:13px;">

            <input type="hidden" id="np-id-mesa">

            <div class="np-body" style="max-height:340px;">
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
                                    <input type="checkbox"
                                           data-id="{{ $p->id_producto }}"
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

    // Resetear checkboxes y cantidades
    document.querySelectorAll('#np-tabla input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        const cant = document.getElementById('np-cant-' + cb.dataset.id);
        if (cant) { cant.disabled = true; cant.value = 1; }
    });

    // Resetear buscador
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

// ── Crear pedido (AJAX) ───────────────────────────────────────────────
async function crearPedido() {
    const idMesa = document.getElementById('np-id-mesa').value;
    if (!idMesa) return;

    // Recolectar productos seleccionados
    const checkboxes = document.querySelectorAll('#np-tabla input[type="checkbox"]:checked');
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
        const cant = document.getElementById('np-cant-' + id);
        formData.append('cantidades[' + id + ']', cant ? cant.value : 1);
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
