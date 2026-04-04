<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Exitoso — ASAPP</title>
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
            padding: 48px 36px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            border: 1px solid #D1FAE5;
            box-shadow: 0 8px 32px rgba(107, 33, 232, 0.1);
        }

        .logo {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, #6B21E8, #3D0E8A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 32px;
            display: block;
        }

        .check-circle {
            width: 88px;
            height: 88px;
            background: #EDE9FE;
            border: 3px solid #C4B5FD;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 40px;
            animation: pop 0.4s ease-out;
        }

        @keyframes pop {
            0%   { transform: scale(0.5); opacity: 0; }
            80%  { transform: scale(1.1); }
            100% { transform: scale(1);   opacity: 1; }
        }

        h2 { font-size: 24px; font-weight: 800; color: #3D0E8A; margin-bottom: 8px; }

        .negocio { font-size: 13px; color: #9B8EC4; margin-bottom: 28px; }

        .monto-box {
            background: #F5F3FF;
            border: 1px solid #E0D9F5;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .monto-label {
            font-size: 12px;
            color: #9B8EC4;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .monto-valor { font-size: 36px; font-weight: 800; color: #3D0E8A; }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px solid #E0D9F5;
            color: #9B8EC4;
        }

        .info-row:last-child { border-bottom: none; }
        .info-row span:last-child { color: #1a1a2e; font-weight: 500; }

        .info-box {
            background: #FAF8FF;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            text-align: left;
            border: 1px solid #E0D9F5;
        }

        .badge-completo {
            background: #3D0E8A;
            border: 1px solid #2d0a6b;
            color: #fff;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .badge-parcial {
            background: #EDE9FE;
            border: 1px solid #C4B5FD;
            color: #5B21B6;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .btn {
            display: block;
            background: #6B21E8;
            color: #fff;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: background 0.2s;
            margin-bottom: 10px;
        }

        .btn:hover { background: #5B18C8; }

        .btn-outline {
            display: block;
            background: transparent;
            color: #9B8EC4;
            padding: 12px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 13px;
            border: 1px solid #E0D9F5;
            transition: border-color 0.2s, color 0.2s;
        }

        .btn-outline:hover { border-color: #C4B5FD; color: #6B21E8; }

        .simulado-nota { margin-top: 20px; font-size: 11px; color: #C4A0FF; }

        .btn, .btn-outline { min-height: 48px; display: flex; align-items: center; justify-content: center; }

        @media (max-width: 480px) {
            body { padding: 16px; align-items: flex-start; padding-top: 24px; }
            .card { padding: 32px 20px; }
            .monto-valor { font-size: 30px; }
        }
    </style>
</head>
<body>
<div class="card">
    <span class="logo">ASAPP</span>

    <div class="check-circle">✓</div>

    <h2>¡Pago registrado!</h2>
    <p class="negocio">
        {{ $pedido->negocio->nombre }}
        @if ($pedido->mesa) · {{ $pedido->mesa->nombre_display }} @endif
    </p>

    <div class="monto-box">
        <div class="monto-label">Total pagado</div>
        <div class="monto-valor">${{ number_format($monto, 0, ',', '.') }}</div>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span>Pedido #</span>
            <span>{{ $pedido->id_pedido }}</span>
        </div>
        <div class="info-row">
            <span>Estado del pedido</span>
            <span>{{ $pedido->estado->value }}</span>
        </div>
        @if ($pedido->mesa)
            <div class="info-row">
                <span>Mesa</span>
                <span>{{ $pedido->mesa->nombre_display }}</span>
            </div>
        @endif
        <div class="info-row">
            <span>Tipo de pago</span>
            <span>Digital (simulado)</span>
        </div>
    </div>

    @if ($pedidoCompleto)
        <div class="badge-completo">🎉 ¡La cuenta de esta mesa quedó completamente pagada!</div>
    @else
        <div class="badge-parcial">⏳ Aún quedan ítems pendientes por pagar en esta mesa.</div>
    @endif

    <a href="{{ route('factura.show', $pedido->id_pedido) }}" class="btn">📄 Ver factura completa</a>
    <a href="javascript:window.close()" class="btn-outline">Cerrar</a>

    <p class="simulado-nota">
        Transacción simulada · No se procesaron pagos reales<br>
        Software de Pagos Compartidos · Universidad de Cundinamarca 2026
    </p>
</div>
</body>
</html>
