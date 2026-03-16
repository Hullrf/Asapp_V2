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
                    $pedidoActivo = $mesa->pedidos->first();
                    $ocupada      = (bool) $pedidoActivo;
                    $urlQr        = route('mesa.publica', $mesa->codigo_qr);
                @endphp

                <div class="mesa-card {{ $ocupada ? 'ocupada' : 'libre' }}">
                    <div class="mesa-icono">{{ $ocupada ? '🟡' : '🟢' }} 🪑</div>
                    <div class="mesa-nombre">{{ $mesa->nombre }}</div>

                    <span class="mesa-estado {{ $ocupada ? 'estado-ocupada' : 'estado-libre' }}">
                        {{ $ocupada ? 'Ocupada' : 'Libre' }}
                    </span>

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
                            {{-- Ir a crear pedido para esta mesa --}}
                            <button class="btn btn-primary btn-sm"
                                    onclick="irACrearPedido({{ $mesa->id_mesa }})">
                                🧾 Nuevo pedido
                            </button>
                        @endif

                        {{-- Renombrar --}}
                        <form action="{{ route('panel.mesas.update', $mesa) }}" method="POST"
                              style="display:flex; gap:6px; margin-top:4px;">
                            @csrf
                            @method('PUT')
                            <input type="text" name="nuevo_nombre"
                                   value="{{ $mesa->nombre }}"
                                   style="flex:1; font-size:12px; padding:5px 8px;" required>
                            <button type="submit" class="btn btn-warning btn-sm">✏️</button>
                        </form>

                        @if (!$ocupada)
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

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
