<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar — ASAPP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #F4F1FA;
            color: #1a1a2e;
            min-height: 100vh;
            padding: 24px 16px 40px;
        }

        .container { max-width: 460px; margin: 0 auto; }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid #E0D9F5;
            box-shadow: 0 8px 32px rgba(107, 33, 232, 0.08);
            margin-bottom: 14px;
        }

        /* ── Header ── */
        .logo {
            font-size: 18px;
            font-weight: 800;
            background: linear-gradient(135deg, #6B21E8, #3D0E8A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .negocio-info { font-size: 12px; color: #9B8EC4; margin-top: 2px; margin-bottom: 22px; }

        /* ── Section title ── */
        .section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #9B8EC4;
            margin-bottom: 12px;
        }

        /* ── Items list ── */
        .items-lista { list-style: none; margin-bottom: 12px; }
        .items-lista li {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #F0EBF8;
            font-size: 13px;
        }
        .items-lista li:last-child { border-bottom: none; }
        .item-cant { color: #9B8EC4; font-size: 11px; margin-left: 4px; }
        .item-precio { color: #6B21E8; font-weight: 600; }

        /* ── Desglose ── */
        .desglose { border-top: 1px solid #E0D9F5; padding-top: 10px; }
        .desglose-row { display: flex; justify-content: space-between; font-size: 12px; color: #9B8EC4; padding: 3px 0; }
        .desglose-row.ipo { color: #6B21E8; font-weight: 600; }
        .total-final {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0 0;
            border-top: 2px solid #E0D9F5;
            margin-top: 8px;
        }
        .total-final-label { font-size: 14px; font-weight: 700; }
        .total-final-monto { font-size: 22px; font-weight: 800; color: #3D0E8A; }

        /* ── Method tabs ── */
        .metodo-tabs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 20px; }
        .metodo-tab {
            background: #F5F3FF;
            border: 2px solid #E0D9F5;
            border-radius: 10px;
            padding: 10px 6px;
            font-size: 12px;
            font-weight: 600;
            color: #9B8EC4;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        .metodo-tab .tab-icon { font-size: 18px; }
        .metodo-tab.active { background: #EDE9FE; border-color: #6B21E8; color: #6B21E8; }
        .metodo-tab:hover:not(.active) { border-color: #C4B5FD; color: #5B21B6; }

        /* ── Form ── */
        .form-metodo { display: none; }
        .form-metodo.visible { display: block; }

        .form-group { margin-bottom: 14px; }
        .form-label {
            font-size: 10px;
            font-weight: 700;
            color: #6B21E8;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            display: block;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #E0D9F5;
            border-radius: 10px;
            font-size: 14px;
            color: #1a1a2e;
            background: #FAFAFA;
            font-family: inherit;
            transition: border-color 0.15s, background 0.15s;
            appearance: none;
        }
        .form-input:focus { outline: none; border-color: #6B21E8; background: #fff; }
        .form-input::placeholder { color: #C4B5FD; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        /* Card number badge */
        .card-number-wrap { position: relative; }
        .card-type-badge {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
            font-weight: 800;
            padding: 3px 7px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .card-type-visa { background: #1a1f71; color: #fff; }
        .card-type-mc   { background: #EB001B; color: #fff; }
        .card-type-amex { background: #007BC1; color: #fff; }

        /* Info box */
        .info-box {
            background: #F5F3FF;
            border: 1px solid #E0D9F5;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 12px;
            color: #5B21B6;
            margin-bottom: 14px;
            line-height: 1.5;
        }
        .info-box strong { display: block; margin-bottom: 3px; font-size: 13px; }

        /* Buttons */
        .btn-pagar {
            width: 100%;
            background: #6B21E8;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 4px;
        }
        .btn-pagar:hover { background: #5B18C8; }

        .link-cancelar {
            display: block;
            text-align: center;
            color: #9B8EC4;
            font-size: 13px;
            text-decoration: none;
            padding: 12px;
        }
        .link-cancelar:hover { color: #C8102E; }

        .simulado-badge {
            background: #EDE9FE;
            border: 1px solid #C4B5FD;
            color: #5B21B6;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 11px;
            text-align: center;
            margin-bottom: 18px;
        }

        .security-note { text-align: center; font-size: 11px; color: #C4B5FD; }

        .sin-items { text-align: center; color: #9B8EC4; padding: 20px 0; font-size: 14px; }

        /* ── Processing overlay ── */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(26, 26, 46, 0.82);
            backdrop-filter: blur(4px);
            z-index: 100;
            align-items: center;
            justify-content: center;
        }
        .overlay.visible { display: flex; }
        .spinner-card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 44px;
            text-align: center;
            min-width: 220px;
        }
        .spinner {
            width: 52px;
            height: 52px;
            border: 4px solid #E0D9F5;
            border-top-color: #6B21E8;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner-titulo { font-size: 16px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .spinner-sub { font-size: 12px; color: #9B8EC4; }

        /* ── Dots animation on processing text ── */
        .dots::after {
            content: '';
            animation: dots 1.4s steps(4, end) infinite;
        }
        @keyframes dots {
            0%   { content: ''; }
            25%  { content: '.'; }
            50%  { content: '..'; }
            75%  { content: '...'; }
        }
    </style>
</head>
<body>
<div class="container">

@if ($items->isEmpty())
    <div class="card">
        <div class="logo">ASAPP</div>
        <div class="negocio-info">{{ $pedido->negocio->nombre }} · Pedido #{{ $pedido->id_pedido }}</div>
        <p class="sin-items">
            No se recibieron ítems para pagar.<br>
            <a href="{{ route('factura.show', $pedido->id_pedido) }}" style="color:#6B21E8;">← Volver a la factura</a>
        </p>
    </div>
@else

    {{-- ── Resumen del pedido ── --}}
    <div class="card">
        <div class="logo">ASAPP</div>
        <div class="negocio-info">{{ $pedido->negocio->nombre }} · Pedido #{{ $pedido->id_pedido }}</div>

        <div class="section-title">Resumen del pago</div>

        <ul class="items-lista">
            @foreach ($items as $item)
                <li>
                    <span>
                        {{ $item->producto->nombre }}
                        <span class="item-cant">x{{ $item->cantidad }}</span>
                    </span>
                    <span class="item-precio">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </li>
            @endforeach
        </ul>

        <div class="desglose">
            <div class="desglose-row">
                <span>Subtotal</span>
                <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="desglose-row ipo">
                <span>Ipoconsumo (8%)</span>
                <span>${{ number_format($ipoconsumo, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="total-final">
            <span class="total-final-label">Total a pagar</span>
            <span class="total-final-monto">${{ number_format($total, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- ── Formulario de pago ── --}}
    <div class="card">
        <div class="simulado-badge">⚠️ Entorno simulado — No se realizarán cobros reales</div>

        <div class="section-title">Método de pago</div>

        <div class="metodo-tabs">
            <button type="button" class="metodo-tab active" data-metodo="tarjeta">
                <span class="tab-icon">💳</span>Tarjeta
            </button>
            <button type="button" class="metodo-tab" data-metodo="pse">
                <span class="tab-icon">🏦</span>PSE
            </button>
            <button type="button" class="metodo-tab" data-metodo="nequi">
                <span class="tab-icon">📱</span>Nequi
            </button>
        </div>

        <form id="form-pago" action="{{ route('pasarela.confirmar', $pedido->id_pedido) }}" method="POST">
            @csrf
            @foreach ($items as $item)
                <input type="hidden" name="items_confirmados[]" value="{{ $item->id_item }}">
            @endforeach
            <input type="hidden" name="metodo_pago" id="metodo_pago_input" value="tarjeta">

            {{-- Tarjeta --}}
            <div id="form-tarjeta" class="form-metodo visible">
                <div class="form-group">
                    <label class="form-label">Nombre en la tarjeta</label>
                    <input class="form-input" type="text" name="titular"
                           placeholder="Como aparece en la tarjeta" autocomplete="cc-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Número de tarjeta</label>
                    <div class="card-number-wrap">
                        <input class="form-input" id="card-number" type="text" inputmode="numeric"
                               placeholder="0000 0000 0000 0000" maxlength="19" autocomplete="cc-number">
                        <span id="card-type-badge" class="card-type-badge" style="display:none;"></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Vencimiento</label>
                        <input class="form-input" id="card-expiry" type="text" inputmode="numeric"
                               placeholder="MM/AA" maxlength="5" autocomplete="cc-exp">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input class="form-input" id="card-cvv" type="text" inputmode="numeric"
                               placeholder="•••" maxlength="4" autocomplete="cc-csc">
                    </div>
                </div>
            </div>

            {{-- PSE --}}
            <div id="form-pse" class="form-metodo">
                <div class="info-box">
                    <strong>Pago mediante débito bancario (PSE)</strong>
                    Serás redirigido al portal de tu banco para autorizar el pago.
                </div>
                <div class="form-group">
                    <label class="form-label">Banco</label>
                    <select class="form-input" name="banco">
                        <option value="">— Selecciona tu banco —</option>
                        <option>Bancolombia</option>
                        <option>Banco de Bogotá</option>
                        <option>Davivienda</option>
                        <option>BBVA Colombia</option>
                        <option>Banco Popular</option>
                        <option>AV Villas</option>
                        <option>Banco de Occidente</option>
                        <option>Banco Caja Social</option>
                        <option>Bancoomeva</option>
                        <option>Banco Falabella</option>
                        <option>Banco Agrario</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tipo de doc.</label>
                        <select class="form-input" name="tipo_doc">
                            <option value="CC">CC</option>
                            <option value="CE">CE</option>
                            <option value="NIT">NIT</option>
                            <option value="PP">Pasaporte</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Número de documento</label>
                        <input class="form-input" type="text" inputmode="numeric" placeholder="1234567890">
                    </div>
                </div>
            </div>

            {{-- Nequi --}}
            <div id="form-nequi" class="form-metodo">
                <div class="info-box">
                    <strong>Pago con Nequi</strong>
                    Recibirás una notificación push en tu app Nequi para aprobar el pago.
                </div>
                <div class="form-group">
                    <label class="form-label">Número de celular Nequi</label>
                    <input class="form-input" type="text" inputmode="tel"
                           placeholder="300 000 0000" maxlength="13">
                </div>
            </div>

            <button type="submit" class="btn-pagar" id="btn-pagar">
                Pagar ${{ number_format($total, 0, ',', '.') }}
            </button>
        </form>

        <a href="{{ route('factura.show', $pedido->id_pedido) }}" class="link-cancelar">← Cancelar y volver</a>
    </div>

    <p class="security-note">🔒 Pago cifrado · Simulación académica · Universidad de Cundinamarca 2026</p>

@endif
</div>

{{-- ── Overlay de procesando ── --}}
<div id="overlay-procesando" class="overlay">
    <div class="spinner-card">
        <div class="spinner"></div>
        <div class="spinner-titulo">Procesando pago<span class="dots"></span></div>
        <div class="spinner-sub">No cierres esta ventana</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Tab switching ──────────────────────────────────────────────────────
    const tabs        = document.querySelectorAll('.metodo-tab');
    const metodoInput = document.getElementById('metodo_pago_input');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const metodo = this.dataset.metodo;
            metodoInput.value = metodo;

            document.querySelectorAll('.form-metodo').forEach(f => f.classList.remove('visible'));
            document.getElementById('form-' + metodo).classList.add('visible');
        });
    });

    // ── Card number formatting ─────────────────────────────────────────────
    const cardInput = document.getElementById('card-number');
    const cardBadge = document.getElementById('card-type-badge');

    if (cardInput) {
        cardInput.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = val.replace(/(.{4})/g, '$1 ').trim();

            if (val.startsWith('4')) {
                cardBadge.textContent = 'VISA';
                cardBadge.className   = 'card-type-badge card-type-visa';
                cardBadge.style.display = '';
            } else if (/^5[1-5]/.test(val) || /^2[2-7]/.test(val)) {
                cardBadge.textContent = 'MC';
                cardBadge.className   = 'card-type-badge card-type-mc';
                cardBadge.style.display = '';
            } else if (val.startsWith('34') || val.startsWith('37')) {
                cardBadge.textContent = 'AMEX';
                cardBadge.className   = 'card-type-badge card-type-amex';
                cardBadge.style.display = '';
            } else {
                cardBadge.style.display = 'none';
            }
        });
    }

    // ── Expiry formatting ──────────────────────────────────────────────────
    const expiryInput = document.getElementById('card-expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '').substring(0, 4);
            if (val.length >= 3) val = val.substring(0, 2) + '/' + val.substring(2);
            this.value = val;
        });
    }

    // ── CVV — solo números ─────────────────────────────────────────────────
    const cvvInput = document.getElementById('card-cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    // ── Submit: mostrar overlay 2s y luego enviar ──────────────────────────
    const form    = document.getElementById('form-pago');
    const overlay = document.getElementById('overlay-procesando');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        overlay.classList.add('visible');
        setTimeout(() => form.submit(), 2000);
    });
});
</script>
</body>
</html>
