{{-- ── CREAR MESA ── --}}
<div class="card">
    <div class="card-title">➕ Crear nueva mesa</div>
    <form action="{{ route('panel.mesas.store') }}" method="POST">
        @csrf
        <div class="form-row">
            <div class="form-group" style="max-width:280px">
                <label>Nombre de la mesa</label>
                <input type="text" name="nombre_mesa" placeholder="Ej: Mesa 1, VIP, Terraza" required>
            </div>
            <div class="form-group" style="max-width:140px; justify-content:flex-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Crear mesa</button>
            </div>
        </div>
    </form>
</div>

{{-- ── GRID DE MESAS ── --}}
<div class="card">
    <div class="card-title">🪑 Mesas del negocio</div>

    @if ($mesas->isEmpty())
        <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
            Aún no has creado ninguna mesa. Crea la primera arriba.
        </p>
    @else
        <div class="mesas-grid">
            @foreach ($mesas as $mesa)
                @php
                    $esSecundaria = $mesa->estaUnida();

                    // Pedido activo: para secundarias, usar el de la principal
                    if ($esSecundaria) {
                        $pedidoActivo = $mesa->mesaPrincipal->pedidos->first() ?? null;
                    } else {
                        $pedidoActivo = $mesa->pedidos->first();
                    }

                    $ocupada = (bool) $pedidoActivo;
                    $urlQr   = route('mesa.publica', $mesa->codigo_qr);

                    // Mesas candidatas a ser la principal de esta mesa:
                    // libres, no secundarias, no la mesa misma, sin mesas unidas propias
                    $candidatas = $mesas->filter(fn($m) =>
                        $m->id_mesa !== $mesa->id_mesa &&
                        ! $m->estaUnida() &&
                        $m->pedidos->isEmpty()
                    );
                @endphp

                <div class="mesa-card {{ $ocupada ? 'ocupada' : 'libre' }}"
                     style="{{ $esSecundaria ? 'border-style: dashed; opacity: 0.85;' : '' }}">

                    <div class="mesa-icono">{{ $ocupada ? '🟡' : '🟢' }} {{ $esSecundaria ? '🔗' : '🪑' }}</div>
                    <div class="mesa-nombre">{{ $mesa->nombre }}</div>

                    <span class="mesa-estado {{ $ocupada ? 'estado-ocupada' : 'estado-libre' }}">
                        {{ $ocupada ? 'Ocupada' : 'Libre' }}
                    </span>

                    {{-- Indicador de unión --}}
                    @if ($esSecundaria)
                        <div style="font-size:11px; color:#7C3AED; background:#EDE9FE; border-radius:6px;
                                    padding:3px 8px; margin-bottom:4px; text-align:center;">
                            🔗 Unida a: <strong>{{ $mesa->mesaPrincipal->nombre }}</strong>
                        </div>
                    @elseif ($mesa->mesasUnidas->isNotEmpty())
                        <div style="font-size:11px; color:#6B21A8; background:#F3E8FF; border-radius:6px;
                                    padding:3px 8px; margin-bottom:4px; text-align:center;">
                            🪑 Grupo: {{ $mesa->mesasUnidas->pluck('nombre')->prepend($mesa->nombre)->join(' + ') }}
                        </div>
                    @endif

                    <div class="mesa-acciones">

                        {{-- Ver QR --}}
                        <button class="btn btn-info btn-sm"
                                onclick="mostrarQR('{{ addslashes($mesa->nombre) }}', '{{ $urlQr }}')">
                            📷 Ver QR
                        </button>

                        @if ($ocupada)
                            {{-- Ver factura del pedido activo --}}
                            <a href="{{ route('factura.show', $pedidoActivo->id_pedido) }}"
                               class="btn btn-success btn-sm">
                                📄 Ver factura
                            </a>
                        @else
                            @if (! $esSecundaria)
                                {{-- Ir a crear pedido (solo mesas principales/independientes) --}}
                                <button class="btn btn-primary btn-sm"
                                        onclick="irACrearPedido({{ $mesa->id_mesa }})">
                                    🧾 Nuevo pedido
                                </button>
                            @endif
                        @endif

                        {{-- Renombrar --}}
                        <form action="{{ route('panel.mesas.update', $mesa) }}" method="POST"
                              style="display:flex; gap:6px; margin-top:4px; width:100%;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="nuevo_nombre"
                                   value="{{ $mesa->nombre }}"
                                   style="flex:1; min-width:0; font-size:12px; padding:5px 8px;" required>
                            <button type="submit" class="btn btn-warning btn-sm" style="flex-shrink:0;">✏️</button>
                        </form>

                        @if (! $ocupada)
                            @if ($esSecundaria)
                                {{-- Separar de la principal --}}
                                <form action="{{ route('panel.mesas.separar', $mesa) }}" method="POST"
                                      onsubmit="return confirm('¿Separar {{ addslashes($mesa->nombre) }} de {{ addslashes($mesa->mesaPrincipal->nombre) }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm" style="width:100%;">
                                        🔓 Separar mesa
                                    </button>
                                </form>
                            @else
                                {{-- Unir con otra mesa (solo si no tiene mesas unidas propias) --}}
                                @if ($mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty())
                                    <form action="{{ route('panel.mesas.unir', $mesa) }}" method="POST"
                                          style="display:flex; gap:6px; margin-top:4px; width:100%;">
                                        @csrf
                                        <select name="id_mesa_principal" required
                                                style="flex:1; min-width:0; font-size:12px; padding:5px 8px;">
                                            <option value="">— Unir con —</option>
                                            @foreach ($candidatas as $c)
                                                <option value="{{ $c->id_mesa }}">{{ $c->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-info btn-sm"
                                                style="flex-shrink:0;" title="Unir mesa">
                                            🔗
                                        </button>
                                    </form>
                                @endif

                                {{-- Eliminar --}}
                                <form action="{{ route('panel.mesas.destroy', $mesa) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar esta mesa?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" style="width:100%;">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            @endif
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
