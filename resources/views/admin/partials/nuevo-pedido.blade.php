@php
    $mesasLibres      = $mesas->filter(fn($m) => $m->pedidos->isEmpty())->values();
    $productosDisp    = $productos->filter(fn($p) => $p->disponible)->values();
@endphp

<div class="card">
    <div class="card-title">🧾 Crear nuevo pedido</div>

    @if ($mesasLibres->isEmpty())
        <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:16px 0;">
            Todas las mesas están ocupadas o no has creado mesas aún.
            <a href="#" onclick="showTab('mesas', document.querySelectorAll('.tab')[1]); return false;"
               style="color:#6B21E8;">Ir a Mesas →</a>
        </p>
    @else
        <form action="{{ route('panel.pedidos.store') }}" method="POST" id="form-pedido">
            @csrf

            <div class="form-row" style="margin-bottom:20px;">
                <div class="form-group" style="max-width:260px">
                    <label>Mesa</label>
                    <select name="id_mesa" id="select-mesa" required>
                        <option value="">— Selecciona una mesa —</option>
                        @foreach ($mesasLibres as $m)
                            <option value="{{ $m->id_mesa }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($productosDisp->isEmpty())
                <p style="color:#9B8EC4; font-size:14px; padding:12px 0;">
                    No hay productos disponibles. Agrégalos en la pestaña de Inventario.
                </p>
            @else
                <table class="pedido-tabla">
                    <thead>
                        <tr>
                            <th>Agregar</th>
                            <th>Producto</th>
                            <th>Precio unitario</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productosDisp as $p)
                            <tr>
                                <td>
                                    <input type="checkbox" name="productos[]"
                                           value="{{ $p->id_producto }}"
                                           onchange="toggleCantidad(this, {{ $p->id_producto }})">
                                </td>
                                <td>{{ $p->nombre }}</td>
                                <td>${{ number_format($p->precio, 0, ',', '.') }}</td>
                                <td>
                                    <input type="number"
                                           name="cantidades[{{ $p->id_producto }}]"
                                           id="cant-{{ $p->id_producto }}"
                                           value="1" min="1" max="99"
                                           disabled>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-primary">✅ Crear Pedido</button>
                </div>
            @endif

        </form>
    @endif
</div>
