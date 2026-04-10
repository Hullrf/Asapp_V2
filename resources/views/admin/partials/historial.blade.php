@if ($pedidosPagados->isEmpty())
    <div class="card" style="text-align:center; padding: 56px 24px;">
        <div style="font-size:40px; margin-bottom:12px; opacity:0.4;">🧾</div>
        <p style="color:#9B8EC4; font-size:14px;">Aún no hay pedidos completamente pagados.</p>
    </div>
@else
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">
            ✅ Pedidos pagados
            <span id="hist-contador" style="margin-left:auto; font-size:12px; font-weight:500; color:#9B8EC4;">
                {{ $pedidosPagados->count() }} registro{{ $pedidosPagados->count() !== 1 ? 's' : '' }}
            </span>
        </div>

        {{-- Filtros --}}
        <div class="hist-filtros">
            <input type="text" id="hist-buscar" placeholder="🔍 # pedido o mesa…"
                   oninput="filtrarHistorial()" class="hist-input">
            <input type="date" id="hist-fecha" onchange="filtrarHistorial()" class="hist-input">
            <button type="button" id="hist-limpiar" class="btn btn-outline btn-sm"
                    onclick="limpiarFiltrosHistorial()" style="display:none;">✕ Limpiar</button>
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="hist-col-mesa">Mesa</th>
                        <th>Fecha</th>
                        <th class="hist-col-items">Ítems</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:center;">Factura</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pedidosPagados as $p)
                        @php
                            $total = $p->items->sum('subtotal');
                            $fecha = \Carbon\Carbon::parse($p->fecha)->format('d/m/Y H:i');
                        @endphp
                        <tr class="hist-fila"
                            data-id="{{ $p->id_pedido }}"
                            data-mesa="{{ strtolower($p->mesa?->nombre ?? '') }}"
                            data-fecha="{{ \Carbon\Carbon::parse($p->fecha)->format('Y-m-d') }}">
                            <td style="font-weight:700; color:#3D0E8A;">
                                #{{ $p->id_pedido }}
                                <div class="hist-mesa-sub">{{ $p->mesa?->nombre ?? '—' }}</div>
                            </td>
                            <td class="hist-col-mesa">{{ $p->mesa?->nombre ?? '—' }}</td>
                            <td style="font-size:12px; color:#9B8EC4; white-space:nowrap;">{{ $fecha }}</td>
                            <td class="hist-col-items" style="text-align:center;">{{ $p->items->count() }}</td>
                            <td style="text-align:right; font-weight:700; color:#6B21E8;">
                                ${{ number_format($total, 0, ',', '.') }}
                            </td>
                            <td style="text-align:center;">
                                <a href="{{ route('factura.show', $p->id_pedido) }}"
                                   class="btn btn-primary btn-sm"
                                   target="_blank">Ver</a>
                            </td>
                        </tr>

                        {{-- Detalle de ítems expandible --}}
                        <tr class="historial-detalle hist-detalle-fila">
                            <td colspan="6" style="padding:0;">
                                <div class="detalle-items">
                                    @foreach ($p->items as $item)
                                        <span class="detalle-chip">
                                            {{ $item->producto->nombre }}
                                            <em>×{{ $item->cantidad }}</em>
                                            — ${{ number_format($item->subtotal, 0, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="hist-sin-resultados">
                        <td colspan="6" style="text-align:center; color:#9B8EC4; padding:20px; font-size:13px;">
                            Sin pedidos que coincidan con los filtros.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endif

<script>
function filtrarHistorial() {
    const texto = document.getElementById('hist-buscar')?.value.toLowerCase().trim() ?? '';
    const fecha  = document.getElementById('hist-fecha')?.value ?? '';
    const filas  = document.querySelectorAll('.hist-fila');
    let visibles = 0;

    filas.forEach(fila => {
        const matchTexto = !texto ||
            String(fila.dataset.id).includes(texto) ||
            fila.dataset.mesa.includes(texto);
        const matchFecha = !fecha || fila.dataset.fecha === fecha;
        const visible = matchTexto && matchFecha;

        fila.style.display = visible ? '' : 'none';

        // Ocultar/mostrar el detalle de ítems que sigue a esta fila
        const detalle = fila.nextElementSibling;
        if (detalle?.classList.contains('hist-detalle-fila')) {
            detalle.style.display = visible ? '' : 'none';
        }

        if (visible) visibles++;
    });

    // Contador
    const total = filas.length;
    const contador = document.getElementById('hist-contador');
    if (contador) {
        contador.textContent = visibles === total
            ? `${total} registro${total !== 1 ? 's' : ''}`
            : `${visibles} de ${total}`;
    }

    // Fila sin resultados
    const sinRes = document.getElementById('hist-sin-resultados');
    if (sinRes) sinRes.style.display = visibles === 0 ? '' : 'none';

    // Botón limpiar
    const limpiar = document.getElementById('hist-limpiar');
    if (limpiar) limpiar.style.display = (texto || fecha) ? '' : 'none';
}

function limpiarFiltrosHistorial() {
    const buscar = document.getElementById('hist-buscar');
    const fecha  = document.getElementById('hist-fecha');
    if (buscar) buscar.value = '';
    if (fecha)  fecha.value  = '';
    filtrarHistorial();
}
</script>

<style>
    .historial-detalle td { background: #FAF8FF; }

    .detalle-items {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 10px 16px;
    }

    .detalle-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #EDE9FE;
        border: 1px solid #D4C9F0;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        color: #3D0E8A;
    }

    .detalle-chip em {
        font-style: normal;
        font-weight: 700;
        color: #6B21E8;
    }

    /* Filtros */
    .hist-filtros {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
        align-items: center;
    }

    .hist-input {
        padding: 7px 12px;
        border: 1.5px solid #E0D9F5;
        border-radius: 10px;
        font-size: 13px;
        color: #1a1a2e;
        background: #FAFAFA;
        font-family: inherit;
        transition: border-color 0.15s;
    }

    .hist-input:focus { outline: none; border-color: #6B21E8; }

    #hist-buscar { flex: 1; min-width: 160px; }
    #hist-fecha  { width: auto; }

    /* Sin resultados */
    #hist-sin-resultados { display: none; text-align: center; color: #9B8EC4; padding: 20px; font-size: 13px; }

    /* Mesa inline bajo el # en móvil (oculto en desktop) */
    .hist-mesa-sub { display: none; }

    @media (max-width: 640px) {
        /* Ocultar columnas que caben dentro de otras */
        .hist-col-mesa,
        .hist-col-items { display: none; }

        /* Mostrar mesa bajo el número de pedido */
        .hist-mesa-sub {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: #9B8EC4;
            margin-top: 2px;
        }

        /* Compactar celdas */
        table td, table th { padding: 10px 8px; font-size: 12px; }
        .detalle-items { padding: 8px 10px; }
    }
</style>
