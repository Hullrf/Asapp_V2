@extends('layouts.auth')

@section('title', 'Crear Cuenta — ASAPP')

@section('styles')
<style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Segoe UI', system-ui, sans-serif;
        background: #F4F1FA;
        color: #1a1a2e;
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 40px 24px 60px;
    }

    .container { width: 100%; max-width: 440px; }

    .logo-block { text-align: center; margin-bottom: 28px; }

    .logo {
        font-size: 44px;
        font-weight: 800;
        letter-spacing: -2px;
        background: linear-gradient(135deg, #6B21E8, #3D0E8A);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: block;
        line-height: 1;
        margin-bottom: 6px;
    }

    .logo-sub {
        font-size: 11px;
        color: #9B8EC4;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    .card {
        background: #fff;
        border: 1px solid #E0D9F5;
        border-radius: 20px;
        padding: 36px 32px 32px;
        box-shadow: 0 8px 32px rgba(107, 33, 232, 0.1);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 24px;
        text-align: center;
    }

    .field { margin-bottom: 16px; }

    label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #6B21E8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        background: #FAF8FF;
        border: 1px solid #D4C9F0;
        border-radius: 10px;
        color: #1a1a2e;
        padding: 11px 14px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
        -webkit-appearance: none;
    }

    input:focus {
        border-color: #6B21E8;
        background: #F0EBF8;
    }

    input::placeholder { color: #9B8EC4; }

    .input-error { border-color: #C8102E !important; }

    .error-text {
        display: block;
        color: #C8102E;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
    }

    .btn-submit {
        width: 100%;
        background: #6B21E8;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 10px;
        transition: background 0.2s;
    }

    .btn-submit:hover { background: #5B18C8; }

    .divider {
        border: none;
        border-top: 1px solid #E0D9F5;
        margin: 20px 0;
    }

    .card-footer {
        text-align: center;
        font-size: 13px;
        color: #9B8EC4;
    }

    .card-footer a {
        color: #6B21E8;
        text-decoration: none;
        font-weight: 600;
    }

    .card-footer a:hover { text-decoration: underline; }

    @media (max-width: 480px) {
        .card { padding: 28px 20px; }
        .logo { font-size: 36px; }
    }
</style>
@endsection

@section('content')
<div class="container">

    <div class="logo-block">
        <span class="logo">ASAPP</span>
        <span class="logo-sub">Pagos compartidos en restaurantes</span>
    </div>

    <div class="card">
        <div class="card-title">Crear cuenta</div>

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf

            <div class="field">
                <label>Nombre del negocio</label>
                <input type="text" name="nombre_negocio"
                       value="{{ old('nombre_negocio') }}"
                       placeholder="Ej: Restaurante El Sabor"
                       class="{{ $errors->has('nombre_negocio') ? 'input-error' : '' }}"
                       required>
                @error('nombre_negocio')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label>Correo electrónico</label>
                <input type="email" name="email_negocio"
                       value="{{ old('email_negocio') }}"
                       placeholder="negocio@asapp.com"
                       class="{{ $errors->has('email_negocio') ? 'input-error' : '' }}"
                       required>
                @error('email_negocio')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label>Contraseña</label>
                <input type="password" name="password"
                       placeholder="Mínimo 6 caracteres"
                       class="{{ $errors->has('password') ? 'input-error' : '' }}"
                       required>
                @error('password')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label>Dirección</label>
                <input type="text" name="direccion"
                       value="{{ old('direccion') }}"
                       placeholder="Calle 10 # 5-20"
                       class="{{ $errors->has('direccion') ? 'input-error' : '' }}"
                       required>
                @error('direccion')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label>Teléfono</label>
                <input type="text" name="telefono"
                       value="{{ old('telefono') }}"
                       placeholder="3001234567"
                       class="{{ $errors->has('telefono') ? 'input-error' : '' }}"
                       required>
                @error('telefono')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-submit">Crear cuenta →</button>
        </form>

        <hr class="divider">

        <div class="card-footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
        </div>
    </div>

</div>
@endsection
