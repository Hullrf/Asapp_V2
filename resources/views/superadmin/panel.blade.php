<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Superadmin — ASAPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; background: #0f0720; color: #e2d9f3; min-height: 100vh; }

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
    <form action="{{ route('superadmin.logout') }}" method="POST" style="margin:0">
        @csrf
        <button type="submit" class="btn-logout">Cerrar sesión</button>
    </form>
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

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarEditar(); });
</script>
</body>
</html>
