<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesa sin pedido activo — ASAPP</title>
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
            max-width: 400px;
            width: 100%;
            text-align: center;
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
            margin-bottom: 32px;
            display: block;
        }

        .icono { font-size: 56px; margin-bottom: 20px; opacity: 0.7; }

        h2 { font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 10px; }

        p {
            font-size: 14px;
            color: #9B8EC4;
            line-height: 1.6;
            margin-bottom: 6px;
        }

        .mesa-nombre { font-size: 16px; font-weight: 600; color: #6B21E8; margin-bottom: 4px; }
        .negocio-nombre { font-size: 13px; color: #9B8EC4; margin-bottom: 32px; }

        .hint {
            background: #FEF3C7;
            border: 1px solid #FCD34D;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 13px;
            color: #92400E;
            line-height: 1.5;
        }

        .simulado-nota {
            margin-top: 28px;
            font-size: 11px;
            color: #C4B5FD;
        }

        @media (max-width: 480px) {
            body { padding: 16px; }
            .card { padding: 32px 20px; }
        }
    </style>
</head>
<body>
<div class="card">
    <span class="logo">ASAPP</span>

    <div class="icono">🪑</div>

    <div class="mesa-nombre">{{ $mesa->nombre_display }}</div>
    <div class="negocio-nombre">{{ $mesa->negocio->nombre }}</div>

    <h2>Sin pedido activo</h2>
    <p>Esta mesa no tiene ningún pedido abierto en este momento.</p>

    <br>

    <div class="hint">
        ⏳ Espera a que el personal del restaurante cree un pedido para esta mesa y vuelve a escanear el código QR.
    </div>

    <p class="simulado-nota">
        ASAPP · Pagos compartidos en restaurantes<br>
        Universidad de Cundinamarca 2026
    </p>
</div>
</body>
</html>
