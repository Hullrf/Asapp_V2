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

        .panel-main { display: flex; flex-direction: column; gap: 20px; }
        .panel-side  { display: flex; flex-direction: column; gap: 16px; }

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
            padding: 24px;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .total-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .total-amount {
            font-size: 38px;
            font-weight: 800;
            font-family: var(--mono);
            color: #9B8EC4;
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

    {{-- ════ PANEL PRINCIPAL ════ --}}
    <div class="panel-main">

        {{-- Tabla de ítems --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Ítems del pedido</span>
                @php $estadoClass = 'estado-' . strtolower($pedido->estado->value); @endphp
                <span class="estado-badge {{ $estadoClass }}">{{ $pedido->estado->value }}</span>
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
                            @if (!$es_admin)<th></th>@endif
                            <th>Producto</th>
                            <th class="center">Cant.</th>
                            <th class="right">Precio unit.</th>
                            <th class="right">Subtotal</th>
                            <th class="center">Estado</th>
                            @if ($es_admin && !$pedidoBloqueado)
                                <th class="right">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if ($pedido->items->isEmpty())
                            <tr>
                                <td colspan="{{ $es_admin ? ($pedidoBloqueado ? 5 : 6) : 6 }}">
                                    <div class="empty-state">
                                        <span class="empty-icon">🧾</span>
                                        <p>No hay ítems en este pedido aún.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($pedido->items as $item)
                                <tr>
                                    {{-- Checkbox (solo clientes) --}}
                                    @if (!$es_admin)
                                    <td>
                                        @if ($item->estado->value === 'Pendiente')
                                            <input type="checkbox" class="pay-checkbox"
                                                   value="{{ $item->id_item }}"
                                                   data-precio="{{ $item->subtotal }}">
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

                                    {{-- Acciones (solo admin, pedido no bloqueado) --}}
                                    @if ($es_admin && !$pedidoBloqueado)
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

        {{-- Agregar producto (solo admin, pedido no bloqueado) --}}
        @if ($es_admin && !$pedidoBloqueado)
            <div class="card">
                <div class="card-header">
                    <span class="card-title">Agregar producto</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('factura.item.add', $pedido->id_pedido) }}"
                          method="POST"
                          class="form-agregar">
                        @csrf
                        <select name="id_producto" required>
                            <option value="">— Selecciona un producto —</option>
                            @foreach ($productos as $prod)
                                <option value="{{ $prod->id_producto }}">{{ $prod->nombre }}</option>
                            @endforeach
                        </select>
                        <input class="qty-input" type="number" name="cantidad" placeholder="Cant." min="1" required>
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
                        <span>{{ $pedido->mesa->nombre }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Reabrir pedido (solo admin, pedido pagado) --}}
        @if ($pedidoBloqueado)
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

        {{-- Pago (solo clientes) --}}
        @if (!$es_admin)
            @if (!$pedidoPagado)
                <div class="pago-card">
                    <div class="total-display">
                        <div class="total-label">Total seleccionado</div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const PASARELA_URL = '{{ route('pasarela.show', $pedido->id_pedido) }}';
    const CSRF_TOKEN   = '{{ csrf_token() }}';

    const btnPagar   = document.getElementById('btnPagar');
    const totalSpan  = document.getElementById('total');
    const checkboxes = () => Array.from(document.querySelectorAll('.pay-checkbox'));

    function formatCOP(valor) {
        return '$' + Math.round(valor).toLocaleString('es-CO');
    }

    function actualizar() {
        let total = 0, anyChecked = false;
        checkboxes().forEach(cb => {
            if (cb.checked && !cb.disabled) {
                total     += parseFloat(cb.dataset.precio || 0);
                anyChecked = true;
            }
        });
        if (totalSpan) {
            totalSpan.textContent = formatCOP(total);
            totalSpan.style.color = anyChecked ? '#6B21E8' : '#9B8EC4';
        }
        if (btnPagar) btnPagar.disabled = !anyChecked;
    }

    checkboxes().forEach(cb => cb.addEventListener('change', actualizar));
    actualizar();

    if (btnPagar) {
        btnPagar.addEventListener('click', function () {
            const selected = checkboxes()
                .filter(cb => cb.checked && !cb.disabled)
                .map(cb => cb.value);

            if (selected.length === 0) return;

            btnPagar.disabled    = true;
            btnPagar.textContent = 'Procesando...';

            const form = document.createElement('form');
            form.method = 'GET';
            form.action = PASARELA_URL;

            selected.forEach(id => {
                const input  = document.createElement('input');
                input.type   = 'hidden';
                input.name   = 'items[]';
                input.value  = id;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        });
    }
});
</script>

</body>
</html>
