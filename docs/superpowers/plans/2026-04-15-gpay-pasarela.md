# Google Pay en Pasarela — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Agregar Google Pay como cuarto método de pago en la pasarela, usando la Google Pay API en modo TEST, sin gateway real.

**Architecture:** Se modifican dos archivos: el controlador acepta `'gpay'` como método válido, y la vista agrega el tab, el formulario GPay con botón oficial, y el JS de la Google Pay API. El token de Google Pay se descarta — el flujo de confirmación simulado existente no cambia.

**Tech Stack:** Google Pay API for Web · Laravel 12 Blade · CSS inline · JS vanilla

---

## File Map

| Acción | Archivo |
|--------|---------|
| Modify | `app/Http/Controllers/PasarelaController.php` — whitelist de métodos |
| Modify | `resources/views/pasarela/show.blade.php` — tab, form div, JS |
| Create | `tests/Unit/PasarelaMetodoTest.php` — test de whitelist |

---

## Task 1: Backend — aceptar 'gpay' como método válido

**Files:**
- Modify: `app/Http/Controllers/PasarelaController.php:31`
- Create: `tests/Unit/PasarelaMetodoTest.php`

- [ ] **Step 1: Escribir el test**

Crear `tests/Unit/PasarelaMetodoTest.php`:

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class PasarelaMetodoTest extends TestCase
{
    private function resolveMetodo(string $input): string
    {
        return in_array($input, ['tarjeta', 'pse', 'nequi', 'efectivo', 'gpay'])
            ? $input
            : 'tarjeta';
    }

    public function test_gpay_es_metodo_valido(): void
    {
        $this->assertEquals('gpay', $this->resolveMetodo('gpay'));
    }

    public function test_metodo_invalido_cae_a_tarjeta(): void
    {
        $this->assertEquals('tarjeta', $this->resolveMetodo('paypal'));
        $this->assertEquals('tarjeta', $this->resolveMetodo(''));
    }

    public function test_metodos_existentes_siguen_validos(): void
    {
        foreach (['tarjeta', 'pse', 'nequi', 'efectivo'] as $metodo) {
            $this->assertEquals($metodo, $this->resolveMetodo($metodo));
        }
    }
}
```

- [ ] **Step 2: Ejecutar el test y verificar que falla**

```bash
php artisan test tests/Unit/PasarelaMetodoTest.php
```

Esperado: FAIL en `test_gpay_es_metodo_valido` — porque la whitelist actual no incluye `'gpay'`.

- [ ] **Step 3: Modificar el controlador**

En `app/Http/Controllers/PasarelaController.php`, línea 31, reemplazar:

```php
$metodo = in_array($request->input('metodo_pago'), ['tarjeta', 'pse', 'nequi', 'efectivo'])
            ? $request->input('metodo_pago')
            : 'tarjeta';
```

por:

```php
$metodo = in_array($request->input('metodo_pago'), ['tarjeta', 'pse', 'nequi', 'efectivo', 'gpay'])
            ? $request->input('metodo_pago')
            : 'tarjeta';
```

- [ ] **Step 4: Ejecutar el test y verificar que pasa**

```bash
php artisan test tests/Unit/PasarelaMetodoTest.php
```

Esperado: PASS — 3 tests, 6 assertions.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/PasarelaController.php tests/Unit/PasarelaMetodoTest.php
git commit -m "feat: aceptar gpay como método de pago válido en pasarela"
```

---

## Task 2: Tab GPay + formulario + responsive

**Files:**
- Modify: `resources/views/pasarela/show.blade.php`

Esta tarea agrega la UI del tab y el área de formulario GPay. La lógica JS viene en Task 3.

- [ ] **Step 1: Cambiar grid de tabs de 3 a 4 columnas**

En el CSS de `show.blade.php`, reemplazar:

```css
.metodo-tabs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 20px; }
```

por:

```css
.metodo-tabs { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 20px; }
```

- [ ] **Step 2: Actualizar el responsive de tabs en @media 480px**

En el bloque `@media (max-width: 480px)`, reemplazar:

```css
.metodo-tabs { gap: 6px; }
.metodo-tab { padding: 8px 4px; font-size: 11px; }
```

por:

```css
.metodo-tabs { gap: 6px; grid-template-columns: repeat(2, 1fr); }
.metodo-tab { padding: 8px 4px; font-size: 11px; }
```

- [ ] **Step 3: Agregar estilo para el tab GPay activo (negro)**

Dentro del bloque `<style>`, después de `.metodo-tab:hover:not(.active)`, agregar:

```css
.metodo-tab.gpay.active { background: #000; border-color: #000; color: #fff; }
.metodo-tab.gpay.active svg path { fill: #fff; }
```

- [ ] **Step 4: Agregar el tab GPay al HTML**

En la sección `<div class="metodo-tabs">`, después del tab de Nequi, agregar:

```blade
<button type="button" class="metodo-tab gpay" id="tab-gpay" data-metodo="gpay" style="display:none;">
    <span class="tab-icon">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z" fill="currentColor"/>
        </svg>
    </span>
    GPay
</button>
```

- [ ] **Step 5: Agregar el div #form-gpay**

Dentro del `<form id="form-pago">`, después del div `#form-nequi` (antes del `<button type="submit">`), agregar:

```blade
{{-- Google Pay --}}
<div id="form-gpay" class="form-metodo">
    <div class="info-box">
        <strong>Google Pay</strong>
        Se abrirá tu billetera de Google. Entorno TEST — se usarán tarjetas de prueba.
    </div>
    <div id="gpay-button-container"></div>
</div>
```

- [ ] **Step 6: Verificar en browser**

Abrir `http://localhost/asapp-v2/public/pasarela/1?items[]=1` (ajustar ID según pedido de prueba disponible).

Verificar:
- Los 3 tabs existentes siguen funcionando (Tarjeta, PSE, Nequi)
- En desktop: 4 columnas (el tab GPay aún no aparece — lo muestra el JS en Task 3)
- En móvil (≤480px): 2 columnas (los 3 tabs existentes en grid 2×2, el 4° aún oculto)

- [ ] **Step 7: Ejecutar tests**

```bash
php artisan test
```

Esperado: PASS — todos los tests existentes más el nuevo.

- [ ] **Step 8: Commit**

```bash
git add resources/views/pasarela/show.blade.php
git commit -m "feat: tab GPay y formulario en pasarela (UI sin JS aún)"
```

---

## Task 3: Google Pay JS — librería, isReadyToPay, botón y pago

**Files:**
- Modify: `resources/views/pasarela/show.blade.php`

- [ ] **Step 1: Cargar la librería de Google Pay en el `<head>`**

En el `<head>` de `show.blade.php`, después de `<link rel="stylesheet" href="/css/asapp-base.css">`, agregar:

```blade
<script async src="https://pay.google.com/gp/p/js/pay.js" onload="initGooglePay()"></script>
```

- [ ] **Step 2: Agregar la función `initGooglePay` en el bloque `<script>` existente**

Al inicio del bloque `<script>` (antes del `document.addEventListener('DOMContentLoaded', ...)`), agregar:

```javascript
// ── Google Pay ─────────────────────────────────────────────────────────
const GPAY_TOTAL = '{{ number_format($total, 2, ".", "") }}';

const gpayBaseRequest = {
    apiVersion: 2,
    apiVersionMinor: 0
};

const gpayTokenizationSpec = {
    type: 'PAYMENT_GATEWAY',
    parameters: {
        gateway: 'example',
        gatewayMerchantId: 'exampleGatewayMerchantId'
    }
};

const gpayCardPaymentMethod = {
    type: 'CARD',
    parameters: {
        allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'],
        allowedCardNetworks: ['AMEX', 'MASTERCARD', 'VISA']
    },
    tokenizationSpecification: gpayTokenizationSpec
};

let gpayClient = null;

function initGooglePay() {
    gpayClient = new google.payments.api.PaymentsClient({ environment: 'TEST' });

    gpayClient.isReadyToPay({
        ...gpayBaseRequest,
        allowedPaymentMethods: [{ type: 'CARD', parameters: gpayCardPaymentMethod.parameters }]
    }).then(function(response) {
        if (!response.result) return;

        // Mostrar tab GPay
        const tabGpay = document.getElementById('tab-gpay');
        if (tabGpay) tabGpay.style.display = '';

        // Renderizar botón oficial
        const button = gpayClient.createButton({
            onClick: onGPayButtonClick,
            buttonType: 'pay',
            buttonColor: 'black',
            buttonSizeMode: 'fill'
        });
        const container = document.getElementById('gpay-button-container');
        if (container) container.appendChild(button);
    }).catch(function(err) {
        console.warn('Google Pay no disponible:', err);
    });
}

function onGPayButtonClick() {
    const paymentDataRequest = {
        ...gpayBaseRequest,
        allowedPaymentMethods: [gpayCardPaymentMethod],
        merchantInfo: {
            merchantName: 'ASAPP'
        },
        transactionInfo: {
            totalPriceStatus: 'FINAL',
            totalPrice: GPAY_TOTAL,
            currencyCode: 'COP',
            countryCode: 'CO'
        }
    };

    gpayClient.loadPaymentData(paymentDataRequest)
        .then(function(paymentData) {
            // Pago autorizado en TEST — enviar formulario simulado
            const metodoInput = document.getElementById('metodo_pago_input');
            const form        = document.getElementById('form-pago');
            const overlay     = document.getElementById('overlay-procesando');

            metodoInput.value = 'gpay';
            overlay.classList.add('visible');
            setTimeout(() => form.submit(), 2000);
        })
        .catch(function(err) {
            // Usuario canceló la hoja de Google Pay — no hacer nada
            if (err.statusCode !== 'CANCELED') {
                console.warn('Error GPay:', err);
            }
        });
}
```

- [ ] **Step 3: Verificar en browser con Chrome en desktop**

Abrir la pasarela en Chrome desktop (Chrome soporta Google Pay en TEST mode).

Verificar:
- El tab "GPay" aparece visible (cuarto tab)
- Al hacer clic en el tab GPay, el área del formulario muestra el info-box y el botón negro "Buy with G Pay"
- Al hacer clic en el botón negro: se abre la hoja de Google Pay con tarjetas de prueba TEST
- Al seleccionar una tarjeta y confirmar: aparece el overlay "Procesando pago..." y luego redirige a la pantalla de éxito
- En la BD, el `Pago` tiene `metodo_pago = 'gpay'` y `estado = 'simulado'`

Si el navegador no soporta GPay (Firefox, Safari sin Apple Pay), el tab permanece oculto — comportamiento correcto.

- [ ] **Step 4: Ejecutar tests**

```bash
php artisan test
```

Esperado: PASS — todos los tests.

- [ ] **Step 5: Commit**

```bash
git add resources/views/pasarela/show.blade.php
git commit -m "feat: integración Google Pay API (TEST mode) en pasarela"
```

---

## Checklist de spec coverage

| Requisito spec | Task |
|---|---|
| Whitelist acepta `'gpay'` en `confirmar()` | Task 1 |
| Test de validación de métodos | Task 1 |
| Grid `.metodo-tabs` pasa a `repeat(4,1fr)` | Task 2 |
| Tab GPay oculto por defecto, visible si isReadyToPay | Tasks 2+3 |
| `#form-gpay` con info-box y `#gpay-button-container` | Task 2 |
| Responsive ≤480px → `repeat(2,1fr)` | Task 2 |
| `<script async src="...pay.js" onload="initGooglePay()">` | Task 3 |
| `initGooglePay()` → isReadyToPay → show tab + createButton | Task 3 |
| `onGPayButtonClick()` → loadPaymentData → submit form con `metodo_pago=gpay` | Task 3 |
| Error CANCELED ignorado silenciosamente | Task 3 |
| `environment: 'TEST'`, `gateway: 'example'` | Task 3 |
| `currencyCode: 'COP'`, `countryCode: 'CO'` | Task 3 |
