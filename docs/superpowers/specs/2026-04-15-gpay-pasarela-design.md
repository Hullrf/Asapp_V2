# Google Pay en Pasarela — Design Spec

## Goal
Agregar Google Pay como cuarto método de pago en la pasarela (`/pasarela/{pedido}`), usando la Google Pay API en modo TEST. El flujo de confirmación sigue siendo simulado — no se requiere procesador de pagos real.

## Architecture

Dos archivos modificados:
- `resources/views/pasarela/show.blade.php` — tab GPay, formulario GPay con botón oficial, JS de la Google Pay API, ajuste de grid
- `app/Http/Controllers/PasarelaController.php` — agregar `'gpay'` como método válido en la whitelist de `confirmar()`

No se crean rutas nuevas ni modelos nuevos. El `Pago` resultante se guarda con `metodo_pago: 'gpay'` y `estado: 'simulado'`, idéntico a los otros métodos.

## Tech Stack
- Google Pay API for Web (`https://pay.google.com/gp/p/js/pay.js`)
- Entorno: `TEST` (no requiere merchant account ni dominio registrado)
- Gateway de prueba: `example` (gateway oficial de Google para testing)
- Laravel 12 Blade · CSS inline · JS vanilla (sin librerías adicionales)

---

## Secciones

### 1. Tab GPay (UI)

- El grid `.metodo-tabs` cambia de `repeat(3,1fr)` a `repeat(4,1fr)`
- Nuevo tab con icono SVG del logo "G" de Google (colores Google: azul #4285F4, rojo #EA4335, amarillo #FBBC05, verde #34A853)
- Estado inactivo: fondo lavanda `#F5F3FF`, borde `#E0D9F5`
- Estado activo: fondo negro `#000`, borde negro, texto blanco
- El tab se oculta con `display:none` si `isReadyToPay()` retorna `false` (navegador sin soporte)
- En móvil (≤480px): grid cambia a `repeat(2,1fr)` para que los 4 tabs no queden comprimidos

### 2. Área de formulario GPay (`#form-gpay`)

- Misma estructura que los otros `div.form-metodo`
- Contiene:
  - Un `div#gpay-button-container` donde la librería inyecta el botón oficial negro
  - Un `div.info-box` con texto: "Entorno TEST · Se usarán tarjetas de prueba de Google"
- El botón oficial se renderiza con `paymentsClient.createButton({ onClick: onGPayClick })` — cumple con los brand guidelines de Google

### 3. Flujo JavaScript

```
Página carga
  └─ new google.payments.api.PaymentsClient({ environment: 'TEST' })
  └─ isReadyToPay(isReadyToPayRequest)
      ├─ false → ocultar tab GPay
      └─ true  → renderizar botón en #gpay-button-container

Usuario selecciona tab GPay
  └─ muestra #form-gpay (igual que los otros tabs)

Usuario hace clic en botón GPay
  └─ paymentsClient.loadPaymentData(paymentDataRequest)
      └─ Google muestra hoja de billetera TEST con tarjetas de prueba
      └─ Usuario confirma
          └─ callback recibe paymentData (token)
          └─ metodo_pago_input.value = 'gpay'
          └─ form.submit() → overlay de procesando → POST /pasarela/{pedido}/confirmar
```

### 4. Configuración Google Pay API

```js
const baseRequest = {
    apiVersion: 2,
    apiVersionMinor: 0
};

const tokenizationSpecification = {
    type: 'PAYMENT_GATEWAY',
    parameters: {
        gateway: 'example',
        gatewayMerchantId: 'exampleGatewayMerchantId'
    }
};

const allowedCardNetworks = ['AMEX', 'MASTERCARD', 'VISA'];
const allowedCardAuthMethods = ['PAN_ONLY', 'CRYPTOGRAM_3DS'];

const baseCardPaymentMethod = {
    type: 'CARD',
    parameters: {
        allowedAuthMethods: allowedCardAuthMethods,
        allowedCardNetworks: allowedCardNetworks
    }
};

const cardPaymentMethod = {
    ...baseCardPaymentMethod,
    tokenizationSpecification: tokenizationSpecification
};

// En loadPaymentData se incluye:
merchantInfo: {
    merchantName: 'ASAPP'
    // merchantId omitido en TEST — no requerido
},
transactionInfo: {
    totalPriceStatus: 'FINAL',
    totalPrice: '<total en string con 2 decimales>',  // e.g. "32600.00"
    currencyCode: 'COP',
    countryCode: 'CO'
}
```

### 5. Backend — PasarelaController

En `confirmar()`, la whitelist de métodos válidos cambia de:
```php
in_array($request->input('metodo_pago'), ['tarjeta', 'pse', 'nequi', 'efectivo'])
```
a:
```php
in_array($request->input('metodo_pago'), ['tarjeta', 'pse', 'nequi', 'efectivo', 'gpay'])
```

El resto del método `confirmar()` no cambia — guarda `Pago` con `estado: 'simulado'` igual que siempre.

---

## Responsive

| Breakpoint | Cambio |
|---|---|
| > 480px | `.metodo-tabs` → `repeat(4,1fr)` |
| ≤ 480px | `.metodo-tabs` → `repeat(2,1fr)` (los 4 tabs en 2 filas) |

---

## Fuera de scope
- Integración con gateway real (Stripe, Braintree)
- Apple Pay
- Guardar tarjetas del usuario
- Registro de merchant en Google Pay Business Console
