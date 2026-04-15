@extends('layouts.auth')

@section('title', 'Iniciar Sesión — ASAPP')

@section('styles')
<style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

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

    .container { width: 100%; max-width: 420px; }

    .logo-block { text-align: center; margin-bottom: 32px; }

    .logo {
        font-size: 48px;
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
        font-size: 12px;
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

    .field { margin-bottom: 18px; }

    label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: #6B21E8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
    }

    input[type="email"],
    input[type="password"],
    select {
        width: 100%;
        background: #FAF8FF;
        border: 1px solid #D4C9F0;
        border-radius: 10px;
        color: #1a1a2e;
        padding: 12px 14px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s, background 0.2s;
        -webkit-appearance: none;
        appearance: none;
    }

    input:focus, select:focus {
        border-color: #6B21E8;
        background: #F0EBF8;
    }

    input::placeholder { color: #9B8EC4; }
    select option { background: #fff; color: #1a1a2e; }

    .input-error { border-color: #C8102E !important; }

    .error-text {
        display: block;
        color: #C8102E;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 500;
    }

    .field-negocio { display: none; }

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
        margin-top: 8px;
        transition: background 0.2s;
    }

    .btn-submit:hover { background: #5B18C8; }

    .card-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 13px;
        color: #9B8EC4;
    }

    .card-footer a {
        color: #6B21E8;
        text-decoration: none;
        font-weight: 600;
    }

    .card-footer a:hover { text-decoration: underline; }

    .divider {
        border: none;
        border-top: 1px solid #E0D9F5;
        margin: 20px 0;
    }

    .btn-submit { min-height: 48px; }

    .remember-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }

    .remember-row label {
        display: inline;
        font-size: 13px;
        font-weight: 500;
        color: #4a4a6a;
        text-transform: none;
        letter-spacing: 0;
        cursor: pointer;
        margin: 0;
    }

    @media (max-width: 480px) {
        body { padding: 16px; align-items: flex-start; padding-top: 32px; }
        .card { padding: 28px 18px 24px; }
        .logo { font-size: 40px; }
    }
</style>

<script>
    async function verificarRol() {
        const email         = document.getElementById('email').value.trim();
        const selectNegocio = document.getElementById('selectNegocio');
        const fieldNegocio  = document.getElementById('fieldNegocio');

        if (email.length === 0) return;

        const formData = new FormData();
        formData.append('email', email);
        formData.append('_token', '{{ csrf_token() }}');

        const res = await fetch('{{ route("verificar.rol") }}', { method: 'POST', body: formData });
        const rol = await res.text();

        if (rol === 'admin') {
            fieldNegocio.style.display = 'none';
            selectNegocio.removeAttribute('required');
        } else if (rol === 'cliente') {
            fieldNegocio.style.display = 'block';
            selectNegocio.setAttribute('required', 'true');
        } else {
            fieldNegocio.style.display = 'none';
            selectNegocio.removeAttribute('required');
        }
    }
</script>
@endsection

@section('content')
<div class="container">

    <div class="logo-block">
        <span class="logo">ASAPP</span>
        <span class="logo-sub">Pagos compartidos en restaurantes</span>
    </div>

    <div class="card">
        <div class="card-title">Bienvenido de nuevo</div>

        @if (session('success'))
            <p style="color:#217346; font-size:13px; text-align:center; margin-bottom:16px; background:#EDFFF5; border:1px solid #A8E6C3; padding:10px; border-radius:8px;">
                ✅ {{ session('success') }}
            </p>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="field">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       placeholder="negocio@asapp.com"
                       value="{{ old('email') }}"
                       required
                       onblur="verificarRol()"
                       class="{{ $errors->has('email') ? 'input-error' : '' }}">
                @error('email')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••"
                       required
                       class="{{ $errors->has('password') ? 'input-error' : '' }}">
                @error('password')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="field field-negocio" id="fieldNegocio">
                <label for="selectNegocio">Selecciona el negocio</label>
                <select id="selectNegocio" name="id_negocio"
                        class="{{ $errors->has('id_negocio') ? 'input-error' : '' }}">
                    <option value="">— Selecciona un negocio —</option>
                    @foreach ($negocios as $negocio)
                        <option value="{{ $negocio->id_negocio }}">
                            {{ $negocio->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('id_negocio')
                    <span class="error-text">⚠ {{ $message }}</span>
                @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Recordarme en este dispositivo</label>
            </div>

            <button type="submit" class="btn-submit">Ingresar →</button>
        </form>

        <hr class="divider">

        <div class="card-footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </div>
    </div>

</div>
@endsection
