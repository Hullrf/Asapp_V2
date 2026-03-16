<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error — ASAPP</title>
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
            max-width: 400px;
            width: 100%;
            text-align: center;
            border: 1px solid #FECACA;
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

        .icono { font-size: 56px; margin-bottom: 20px; }

        h2 { font-size: 22px; font-weight: 700; color: #C8102E; margin-bottom: 12px; }

        p { font-size: 14px; color: #9B8EC4; line-height: 1.6; }

        .simulado-nota {
            margin-top: 32px;
            font-size: 11px;
            color: #C4B5FD;
        }
    </style>
</head>
<body>
<div class="card">
    <span class="logo">ASAPP</span>

    <div class="icono">⚠️</div>

    <h2>{{ $titulo }}</h2>
    <p>{{ $mensaje }}</p>

    <p class="simulado-nota">
        ASAPP · Pagos compartidos en restaurantes<br>
        Universidad de Cundinamarca 2026
    </p>
</div>
</body>
</html>
