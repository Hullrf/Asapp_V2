<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago Fallido — ASAPP</title>
    <link rel="stylesheet" href="/css/asapp-base.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-sans);
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
            border: 1px solid #FEE2E2;
            box-shadow: 0 8px 32px rgba(220, 38, 38, 0.08);
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

        .error-circle {
            width: 88px;
            height: 88px;
            background: #FEE2E2;
            border: 3px solid #FECACA;
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

        h2 { font-size: 24px; font-weight: 800; color: #DC2626; margin-bottom: 8px; }

        .negocio { font-size: 13px; color: #9B8EC4; margin-bottom: 28px; }

        .razon-box {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            text-align: left;
        }

        .razon-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #EF4444;
            margin-bottom: 6px;
        }

        .razon-texto { font-size: 14px; color: #7F1D1D; font-weight: 500; }

        .metodo-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #F5F3FF;
            border: 1px solid #E0D9F5;
            color: #5B21B6;
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 28px;
        }

        .info-box {
            background: #FAF8FF;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 24px;
            text-align: left;
            border: 1px solid #E0D9F5;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 7px 0;
            border-bottom: 1px solid #E0D9F5;
            color: #9B8EC4;
        }

        .info-row:last-child { border-bottom: none; }
        .info-row span:last-child { color: #1a1a2e; font-weight: 500; }

        .btn-reintentar {
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

        .btn-reintentar:hover { background: #5B18C8; }

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

        .btn-reintentar, .btn-outline { min-height: 48px; display: flex; align-items: center; justify-content: center; }

        @media (max-width: 480px) {
            body { padding: 16px; align-items: flex-start; padding-top: 24px; }
            .card { padding: 32px 20px; }
        }
    </style>
</head>
<body>
<div class="card">
    <span class="logo">ASAPP</span>

    <div class="error-circle">✗</div>

    <h2>Pago rechazado</h2>
    <p class="negocio">
        {{ $pedido->negocio->nombre }}
        @if ($pedido->mesa) · {{ $pedido->mesa->nombre_display }} @endif
    </p>

    <div class="razon-box">
        <div class="razon-label">Motivo del rechazo</div>
        <div class="razon-texto">{{ $razon }}</div>
    </div>

    @php
        $metodosLabel = [
            'tarjeta' => '💳 Tarjeta',
            'pse'     => '🏦 PSE',
            'nequi'   => '📱 Nequi',
        ];
    @endphp
    <div class="metodo-badge">
        {{ $metodosLabel[$metodo] ?? '💳 ' . ucfirst($metodo) }}
        &nbsp;·&nbsp; Transacción no procesada
    </div>

    <div class="info-box">
        <div class="info-row">
            <span>Pedido #</span>
            <span>{{ $pedido->id_pedido }}</span>
        </div>
        @if ($pedido->mesa)
            <div class="info-row">
                <span>Mesa</span>
                <span>{{ $pedido->mesa->nombre_display }}</span>
            </div>
        @endif
        <div class="info-row">
            <span>Estado del pedido</span>
            <span>{{ $pedido->estado->value }}</span>
        </div>
    </div>

    <a href="{{ route('factura.show', $pedido->id_pedido) }}" class="btn-reintentar">
        🔄 Volver e intentar de nuevo
    </a>
    <a href="javascript:window.close()" class="btn-outline">Cerrar</a>

    <p class="simulado-nota">
        Transacción simulada · No se procesaron pagos reales<br>
        Software de Pagos Compartidos · Universidad de Cundinamarca 2026
    </p>
</div>
</body>
</html>
