@if ($pedidosPagados->isEmpty())
    <div class="card" style="text-align:center; padding: 56px 24px;">
        <div style="font-size:40px; margin-bottom:12px; opacity:0.4;">🧾</div>
        <p style="color:#9B8EC4; font-size:14px;">Aún no hay pedidos completamente pagados.</p>
    </div>
@else
    <div class="card">
        <div class="card-title" style="margin-bottom:0;">
            ✅ Pedidos pagados
            <span style="margin-left:auto; font-size:12px; font-weight:500; color:#9B8EC4;">
                {{ $pedidosPagados->count() }} registro{{ $pedidosPagados->count() !== 1 ? 's' : '' }}
            </span>
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
                        <tr>
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
                        <tr class="historial-detalle">
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
                </tbody>
            </table>
        </div>
    </div>
@endif

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
