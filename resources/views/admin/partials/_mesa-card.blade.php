@php
    $esSecundaria = $mesa->estaUnida();

    if ($esSecundaria) {
        $pedidoActivo = $mesa->mesaPrincipal->pedidos->first() ?? null;
    } else {
        $pedidoActivo = $mesa->pedidos->first();
    }

    $ocupada       = (bool) $pedidoActivo;
    $urlQr         = route('mesa.publica', $mesa->codigo_qr);
    $nombreDisplay = $mesa->nombre_display;

    // Candidatas: libres, no secundarias, no la misma mesa, sin mesas unidas propias
    $candidatas = $mesas->filter(fn($m) =>
        $m->id_mesa !== $mesa->id_mesa &&
        ! $m->estaUnida() &&
        $m->pedidos->isEmpty()
    );
@endphp

<div class="mesa-card {{ $ocupada ? 'ocupada' : 'libre' }}"
     style="{{ $esSecundaria ? 'border-style: dashed; opacity: 0.85;' : '' }}">

    <div class="mesa-icono">{{ $ocupada ? '🟡' : '🟢' }} {{ $esSecundaria ? '🔗' : '🪑' }}</div>

    {{-- Nombre principal (alias si existe, si no "Mesa N") --}}
    <div class="mesa-nombre">{{ $nombreDisplay }}</div>

    {{-- Si hay alias, mostrar el nombre canónico como subtítulo --}}
    @if ($mesa->alias)
        <div style="font-size:10px; color:#9B8EC4; margin-top:-4px; margin-bottom:4px;">
            {{ $mesa->nombre }}
        </div>
    @endif

    <span class="mesa-estado {{ $ocupada ? 'estado-ocupada' : 'estado-libre' }}">
        {{ $ocupada ? 'Ocupada' : 'Libre' }}
    </span>

    {{-- Indicador de unión --}}
    @if ($esSecundaria)
        <div style="font-size:11px; color:#7C3AED; background:#EDE9FE; border-radius:6px;
                    padding:3px 8px; margin-bottom:4px; text-align:center;">
            🔗 Unida a: <strong>{{ $mesa->mesaPrincipal->nombre_display }}</strong>
        </div>
    @elseif ($mesa->mesasUnidas->isNotEmpty())
        <div style="font-size:11px; color:#6B21A8; background:#F3E8FF; border-radius:6px;
                    padding:3px 8px; margin-bottom:4px; text-align:center;">
            🪑 Grupo: {{ $mesa->mesasUnidas->map(fn($m) => $m->nombre_display)->prepend($nombreDisplay)->join(' + ') }}
        </div>
    @endif

    {{-- Campo de alias --}}
    <form action="{{ route('panel.mesas.update', $mesa) }}" method="POST"
          data-ajax data-refresh="mesas"
          style="display:flex; gap:5px; align-items:center; margin-bottom:6px; width:100%;">
        @csrf
        @method('PUT')
        <input type="text" name="alias"
               value="{{ $mesa->alias }}"
               placeholder="Alias (opcional)…"
               maxlength="50"
               style="flex:1; min-width:0; font-size:11px; padding:4px 7px;
                      border:1px solid #E0D9F5; border-radius:6px;
                      color:#1a1a2e; background:#FAFAFA; font-family:inherit;">
        <button type="submit" class="btn btn-warning btn-sm"
                style="flex-shrink:0; padding:3px 8px; font-size:12px;" title="Guardar alias">✏️</button>
    </form>

    <div class="mesa-acciones">

        {{-- Ver QR --}}
        <button class="btn btn-info btn-sm"
                onclick="mostrarQR('{{ addslashes($nombreDisplay) }}', '{{ $urlQr }}')">
            📷 Ver QR
        </button>

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

        @if (! $ocupada)
            @if ($esSecundaria)
                <form action="{{ route('panel.mesas.separar', $mesa) }}" method="POST"
                      data-ajax data-refresh="mesas"
                      onsubmit="return confirm('¿Separar {{ addslashes($nombreDisplay) }} de {{ addslashes($mesa->mesaPrincipal->nombre_display) }}?')">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm" style="width:100%;">
                        🔓 Separar mesa
                    </button>
                </form>
            @else
                @if ($mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty())
                    <form action="{{ route('panel.mesas.unir', $mesa) }}" method="POST"
                          data-ajax data-refresh="mesas"
                          style="display:flex; gap:6px; margin-top:4px; width:100%;">
                        @csrf
                        <select name="id_mesa_principal" required
                                style="flex:1; min-width:0; font-size:12px; padding:5px 8px;">
                            <option value="">— Unir con —</option>
                            @foreach ($candidatas as $c)
                                <option value="{{ $c->id_mesa }}">{{ $c->nombre_display }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-info btn-sm"
                                style="flex-shrink:0;" title="Unir mesa">
                            🔗
                        </button>
                    </form>
                @endif

                <form action="{{ route('panel.mesas.destroy', $mesa) }}" method="POST"
                      data-ajax data-refresh="mesas,estadisticas,historial"
                      onsubmit="return confirm('¿Eliminar {{ addslashes($nombreDisplay) }}? Las demás mesas del piso se renumerarán.')">
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
