@if ($pedidosPagados->isEmpty())
    <div class="card" style="text-align:center; padding: 56px 24px;">
        <div style="font-size:40px; margin-bottom:12px; opacity:0.4;">🧾</div>
        <p style="color:#9B8EC4; font-size:14px;">Aún no hay pedidos completamente pagados.</p>
    </div>
@else
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/></svg></div>
                Pedidos pagados
            </div>
            <span id="hist-contador" class="card-count">{{ $pedidosPagados->count() }} registro{{ $pedidosPagados->count() !== 1 ? 's' : '' }}</span>
        </div>

        {{-- Filtros --}}
        <div style="display:flex;gap:10px;align-items:center;padding:12px 20px;border-bottom:1px solid var(--border-soft);flex-wrap:wrap;">
            <div style="position:relative;flex:1;max-width:280px;">
                <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-faint);pointer-events:none;">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"/></svg>
                </span>
                <input type="text" id="hist-buscar" placeholder="# pedido o mesa…"
                       oninput="filtrarHistorial()"
                       style="padding-left:32px;">
            </div>
            <input type="date" id="hist-fecha" onchange="filtrarHistorial()" style="max-width:160px;">
            <button type="button" id="hist-limpiar" class="btn btn-outline btn-sm"
                    onclick="limpiarFiltrosHistorial()" style="display:none;">
                <svg viewBox="0 0 20 20" fill="currentColor" width="12" height="12"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                Limpiar
            </button>
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
                            data-fecha="{{ \Carbon\Carbon::parse($p->fecha)->format('Y-m-d') }}"
                            onclick="toggleDetalle(this)"
                            style="cursor:pointer;">
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
                                   class="btn btn-ghost btn-xs"
                                   target="_blank">Ver</a>
                            </td>
                        </tr>

                        {{-- Detalle de ítems expandible --}}
                        <tr class="historial-detalle hist-detalle-fila" style="display:none;">
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
function toggleDetalle(fila) {
    const detalle = fila.nextElementSibling;
    if (detalle?.classList.contains('hist-detalle-fila')) {
        detalle.style.display = detalle.style.display === 'none' ? '' : 'none';
    }
}

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
        padding: 8px 14px 12px;
        background: var(--surface2);
    }
    .detalle-chip {
        background: var(--purple-dim);
        color: var(--purple-dk);
        border: 1px solid var(--border);
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        padding: 3px 10px;
    }
    .detalle-chip em {
        font-style: normal;
        color: var(--text-faint);
        margin-left: 2px;
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
