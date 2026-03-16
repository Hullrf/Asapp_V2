<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Pago — ASAPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #F4F1FA;
            color: #1a1a2e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 32px;
            max-width: 460px;
            width: 100%;
            border: 1px solid #E0D9F5;
            box-shadow: 0 8px 32px rgba(107, 33, 232, 0.1);
        }

        .logo {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, #6B21E8, #3D0E8A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .negocio-nombre { font-size: 12px; color: #9B8EC4; margin-bottom: 28px; }

        h2 { font-size: 18px; font-weight: 700; margin-bottom: 20px; color: #1a1a2e; }

        .items-lista { list-style: none; margin-bottom: 20px; }

        .items-lista li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #E0D9F5;
            font-size: 14px;
        }

        .items-lista li:last-child { border-bottom: none; }

        .item-nombre { color: #1a1a2e; }
        .item-cant   { color: #9B8EC4; font-size: 12px; margin-left: 6px; }
        .item-precio { color: #6B21E8; font-weight: 700; }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0 24px;
            border-top: 2px solid #E0D9F5;
            margin-top: 4px;
        }

        .total-label { font-size: 16px; font-weight: 700; color: #1a1a2e; }
        .total-monto { font-size: 24px; font-weight: 800; color: #3D0E8A; }

        .simulado-badge {
            background: #EDE9FE;
            border: 1px solid #C4B5FD;
            color: #5B21B6;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 20px;
            text-align: center;
        }

        .btn-pagar {
            width: 100%;
            background: #6B21E8;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-bottom: 12px;
        }

        .btn-pagar:hover { background: #5B18C8; }

        .link-cancelar {
            display: block;
            text-align: center;
            color: #9B8EC4;
            font-size: 13px;
            text-decoration: none;
            padding: 8px;
        }

        .link-cancelar:hover { color: #C8102E; }

        .sin-items { text-align: center; color: #9B8EC4; padding: 20px 0; font-size: 14px; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">ASAPP</div>
    <div class="negocio-nombre">{{ $pedido->negocio->nombre }} · Pedido #{{ $pedido->id_pedido }}</div>

    <h2>🧾 Resumen de tu pago</h2>

    @if ($items->isEmpty())
        <p class="sin-items">
            No se recibieron ítems para pagar.<br>
            <a href="{{ route('factura.show', $pedido->id_pedido) }}" style="color:#6B21E8;">← Volver a la factura</a>
        </p>
    @else
        <ul class="items-lista">
            @foreach ($items as $item)
                <li>
                    <span>
                        <span class="item-nombre">{{ $item->producto->nombre }}</span>
                        <span class="item-cant">x{{ $item->cantidad }}</span>
                    </span>
                    <span class="item-precio">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>

        <div class="total-row">
            <span class="total-label">Total a pagar</span>
            <span class="total-monto">${{ number_format($total, 0, ',', '.') }}</span>
        </div>

        <div class="simulado-badge">
            ⚠️ Simulación de pago — No se procesará ninguna transacción real
        </div>

        <form action="{{ route('pasarela.confirmar', $pedido->id_pedido) }}" method="POST">
            @csrf
            @foreach ($items as $item)
                <input type="hidden" name="items_confirmados[]" value="{{ $item->id_item }}">
            @endforeach
            <button type="submit" class="btn-pagar">✅ Confirmar Pago</button>
        </form>

        <a href="{{ route('factura.show', $pedido->id_pedido) }}" class="link-cancelar">
            ← Cancelar y volver a la factura
        </a>
    @endif
</div>
</body>
</html>
