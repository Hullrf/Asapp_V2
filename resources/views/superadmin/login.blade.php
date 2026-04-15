<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin — ASAPP</title>
    <link rel="stylesheet" href="/css/asapp-base.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: var(--font-sans);
            background: #0f0720;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background: #1a0f35;
            border: 1px solid #3D0E8A;
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 24px 64px rgba(107,33,232,0.25);
        }
        .logo {
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #C4A0FF, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 6px;
        }
        .subtitle {
            text-align: center;
            font-size: 12px;
            color: #7C3AED;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            margin-bottom: 36px;
        }
        label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #9B8EC4;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 11px 14px;
            background: #0f0720;
            border: 1.5px solid #3D0E8A;
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            font-family: inherit;
            margin-bottom: 18px;
            transition: border-color 0.2s;
        }
        input:focus { outline: none; border-color: #7C3AED; }
        .error {
            background: rgba(200,16,46,0.15);
            border: 1px solid rgba(200,16,46,0.4);
            color: #f87171;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }
        button {
            width: 100%;
            padding: 13px;
            background: #6B21E8;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #7C3AED; }
        button { min-height: 48px; }

        @media (max-width: 480px) {
            body { padding: 20px; align-items: flex-start; padding-top: 40px; }
            .card { padding: 36px 22px; }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">ASAPP</div>
        <div class="subtitle">Panel Superadmin</div>

        @if ($errors->has('credenciales'))
            <div class="error">{{ $errors->first('credenciales') }}</div>
        @endif

        <form method="POST" action="{{ route('superadmin.login.submit') }}">
            @csrf
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>

            <label>Contraseña maestra</label>
            <input type="password" name="password" required>

            <button type="submit">Ingresar →</button>
        </form>
    </div>
</body>
</html>
