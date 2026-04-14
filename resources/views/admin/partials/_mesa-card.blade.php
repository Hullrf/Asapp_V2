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
    $candidatas    = $mesas->filter(fn($m) =>
        $m->id_mesa !== $mesa->id_mesa &&
        ! $m->estaUnida() &&
        $m->pedidos->isEmpty()
    );
@endphp

<div style="border-radius:var(--r-lg);border:1.5px solid {{ $esSecundaria ? '#C4B5FD' : ($ocupada ? 'var(--purple-lt)' : 'var(--border)') }};border-style:{{ $esSecundaria ? 'dashed' : 'solid' }};background:{{ $ocupada ? '#F5F3FF' : 'var(--surface2)' }};padding:16px;display:flex;flex-direction:column;gap:10px;">

    {{-- Header: nombre + estado --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
        <div>
            <div style="font-size:14px;font-weight:700;color:var(--text);">{{ $nombreDisplay }}</div>
            @if ($mesa->alias)
                <div style="font-size:10px;color:var(--text-faint);">{{ $mesa->nombre }}</div>
            @endif
        </div>
        <div style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;flex-shrink:0;background:{{ $ocupada ? '#EDE9FE' : '#F3F4F6' }};color:{{ $ocupada ? 'var(--purple)' : 'var(--text-faint)' }};">
            <span style="width:6px;height:6px;border-radius:50%;background:{{ $ocupada ? 'var(--purple)' : '#9CA3AF' }};"></span>
            {{ $ocupada ? 'Ocupada' : 'Libre' }}
        </div>
    </div>

    {{-- Badge unión --}}
    @if ($esSecundaria)
        <div style="font-size:11px;font-weight:600;background:var(--purple-dim);color:var(--purple);border:1px solid var(--border);border-radius:var(--r-sm);padding:4px 9px;display:flex;align-items:center;gap:5px;">
            <svg viewBox="0 0 20 20" fill="currentColor" width="11" height="11"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/></svg>
            Unida a: <strong>{{ $mesa->mesaPrincipal->nombre_display }}</strong>
        </div>
    @elseif ($mesa->mesasUnidas->isNotEmpty())
        <div style="font-size:11px;font-weight:600;background:var(--purple-dim);color:var(--purple);border:1px solid var(--border);border-radius:var(--r-sm);padding:4px 9px;display:flex;align-items:center;gap:5px;">
            <svg viewBox="0 0 20 20" fill="currentColor" width="11" height="11"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/></svg>
            Grupo: {{ $mesa->mesasUnidas->map(fn($m) => $m->nombre_display)->prepend($nombreDisplay)->join(' + ') }}
        </div>
    @endif

    {{-- Alias --}}
    <form action="{{ route('panel.mesas.update', $mesa) }}" method="POST"
          data-ajax data-refresh="mesas"
          style="display:flex;gap:5px;align-items:center;">
        @csrf @method('PUT')
        <input type="text" name="alias" value="{{ $mesa->alias }}"
               placeholder="Alias (opcional)…" maxlength="50"
               style="flex:1;font-size:11px;padding:5px 9px;border-radius:var(--r-sm);">
        <button type="submit" class="btn-icon" title="Guardar alias">
            <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
        </button>
    </form>

    {{-- Acciones --}}
    <div style="display:flex;flex-direction:column;gap:5px;">

        {{-- Acción principal --}}
        @if ($ocupada)
            <a href="{{ route('factura.show', $pedidoActivo->id_pedido) }}"
               class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">
                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                Ver factura
            </a>
        @elseif (! $esSecundaria)
            <button class="btn btn-primary btn-sm" style="width:100%;justify-content:center;"
                    onclick="abrirNuevoPedido({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                Nuevo pedido
            </button>
        @endif

        {{-- QR + acciones secundarias --}}
        <div style="display:flex;gap:5px;">
            <button class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;"
                    onclick="mostrarQR('{{ addslashes($nombreDisplay) }}', '{{ $urlQr }}')">
                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"/></svg>
                QR
            </button>

            @if (! $ocupada)
                @if ($esSecundaria)
                    <form action="{{ route('panel.mesas.separar', $mesa) }}" method="POST"
                          data-ajax data-refresh="mesas" style="flex:1;"
                          onsubmit="return confirm('¿Separar {{ addslashes($nombreDisplay) }}?')">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">Separar</button>
                    </form>
                @else
                    <form action="{{ route('panel.mesas.destroy', $mesa) }}" method="POST"
                          data-ajax data-refresh="mesas,estadisticas,historial" style="flex:1;"
                          onsubmit="return confirm('¿Eliminar {{ addslashes($nombreDisplay) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center;">
                            <svg viewBox="0 0 20 20" fill="currentColor" width="12" height="12"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9z"/></svg>
                            Eliminar
                        </button>
                    </form>
                @endif
            @endif
        </div>

        {{-- Unir mesa (libre, no secundaria, hay candidatas) --}}
        @if (! $ocupada && ! $esSecundaria && $mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty())
            <form action="{{ route('panel.mesas.unir', $mesa) }}" method="POST"
                  data-ajax data-refresh="mesas"
                  style="display:flex;gap:6px;">
                @csrf
                <select name="id_mesa_principal" required style="flex:1;font-size:12px;padding:5px 8px;">
                    <option value="">— Unir con —</option>
                    @foreach ($candidatas as $c)
                        <option value="{{ $c->id_mesa }}">{{ $c->nombre_display }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-ghost btn-sm" title="Unir mesa">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/></svg>
                    Unir
                </button>
            </form>
        @endif

    </div>
</div>
