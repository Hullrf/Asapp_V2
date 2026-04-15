# Landing Page ASAPP — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Crear una landing page estática en `/` que presente ASAPP a dueños de restaurantes, reemplazando el actual redirect a `/login`.

**Architecture:** Un archivo Blade autocontenido (`resources/views/landing.blade.php`) con CSS inline. No usa layouts existentes ni JavaScript. El cambio de ruta es una sola línea en `routes/web.php`. La vista usa `asapp-base.css` para la fuente y los tokens de color, y carga `public/img/hero.png` que ya existe.

**Tech Stack:** Laravel 12 Blade · CSS inline · Plus Jakarta Sans vía `asapp-base.css` · SVG Heroicons inline · Sin JavaScript

---

## File Map

| Acción | Archivo |
|--------|---------|
| Modify | `routes/web.php` — cambiar ruta raíz |
| Create | `resources/views/landing.blade.php` — vista completa de la landing |
| Create | `tests/Feature/LandingPageTest.php` — test HTTP básico |
| Existing (no tocar) | `public/css/asapp-base.css` |
| Existing (no tocar) | `public/img/hero.png` |

---

## Task 1: Ruta raíz + test HTTP

**Files:**
- Modify: `routes/web.php:20`
- Create: `tests/Feature/LandingPageTest.php`

- [ ] **Step 1: Escribir el test**

Crear `tests/Feature/LandingPageTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_loads(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('landing');
        $response->assertSee('ASAPP');
        $response->assertSee('Registrar');
    }
}
```

- [ ] **Step 2: Ejecutar el test y verificar que falla**

```bash
php artisan test tests/Feature/LandingPageTest.php
```

Esperado: FAIL — `View [landing] not found` o redirección 302.

- [ ] **Step 3: Cambiar la ruta raíz**

En `routes/web.php`, reemplazar la línea:
```php
Route::get('/', fn() => redirect()->route('login'));
```
por:
```php
Route::get('/', fn() => view('landing'))->name('home');
```

- [ ] **Step 4: Crear vista mínima para que el test pase**

Crear `resources/views/landing.blade.php` con contenido mínimo:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>ASAPP</title>
</head>
<body>
<p>ASAPP</p>
<a href="{{ route('register') }}">Registrar</a>
</body>
</html>
```

- [ ] **Step 5: Ejecutar el test y verificar que pasa**

```bash
php artisan test tests/Feature/LandingPageTest.php
```

Esperado: PASS — 1 test, 4 assertions.

- [ ] **Step 6: Commit**

```bash
git add routes/web.php tests/Feature/LandingPageTest.php resources/views/landing.blade.php
git commit -m "feat: ruta raíz apunta a landing page (vista mínima)"
```

---

## Task 2: Estructura base + Navbar + Hero

**Files:**
- Modify: `resources/views/landing.blade.php` — reemplazar por vista completa hasta sección Hero

- [ ] **Step 1: Reemplazar `landing.blade.php` con estructura base + Navbar + Hero**

```blade
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ASAPP — Sistema POS para restaurantes</title>
<link rel="stylesheet" href="/css/asapp-base.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: var(--font-sans); background:#fff; color: var(--text); }
a { text-decoration:none; color:inherit; }

/* ── NAVBAR ─────────────────────────────── */
.nav {
    position:sticky; top:0; z-index:100;
    background:#fff;
    border-bottom:1px solid var(--border);
    padding:0 40px; height:64px;
    display:flex; align-items:center; justify-content:space-between;
}
.nav-logo {
    font-size:22px; font-weight:900; letter-spacing:-1px;
    background:linear-gradient(135deg, var(--purple), var(--purple-dk));
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    background-clip:text;
}
.nav-links { display:flex; gap:12px; align-items:center; }
.btn-login {
    color: var(--purple); font-size:13px; font-weight:600;
    padding:8px 16px; border-radius:8px;
    border:1.5px solid var(--purple-lt);
    transition: background 0.15s;
}
.btn-login:hover { background: var(--purple-bg); }
.btn-register {
    background: var(--purple); color:#fff;
    font-size:13px; font-weight:700;
    padding:8px 18px; border-radius:8px;
    transition: background 0.15s;
}
.btn-register:hover { background: var(--purple-dk); }

/* ── HERO ────────────────────────────────── */
.hero {
    background:linear-gradient(160deg, #F4F1FA 0%, #EDE9FE 60%, #F4F1FA 100%);
    padding:72px 40px 80px;
}
.hero-inner {
    max-width:1100px; margin:0 auto;
    display:grid; grid-template-columns:1fr 1fr;
    gap:56px; align-items:center;
}
.hero-tag {
    display:inline-block;
    background:#EDE9FE; color: var(--purple);
    font-size:11px; font-weight:700; letter-spacing:2px; text-transform:uppercase;
    padding:6px 14px; border-radius:20px; margin-bottom:24px;
}
.hero-text h1 {
    font-size:50px; font-weight:900; line-height:1.1;
    letter-spacing:-2px; margin-bottom:20px;
}
.hero-text h1 span { color: var(--purple); }
.hero-text p {
    font-size:17px; color:#6B7280; line-height:1.6; margin-bottom:32px;
}
.hero-btns { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
.btn-primary {
    display:inline-block;
    background: var(--purple); color:#fff;
    font-size:14px; font-weight:700;
    padding:14px 28px; border-radius:12px;
    transition: background 0.15s;
}
.btn-primary:hover { background: var(--purple-dk); }
.btn-secondary {
    display:inline-block;
    background:#fff; color: var(--purple);
    border:1.5px solid var(--purple-lt);
    font-size:14px; font-weight:600;
    padding:14px 22px; border-radius:12px;
    transition: background 0.15s;
}
.btn-secondary:hover { background: var(--purple-bg); }
.hero-sub { font-size:12px; color: var(--text-muted); }

.hero-img { position:relative; }
.hero-img img {
    width:100%; border-radius:20px;
    box-shadow:0 24px 64px rgba(107,33,232,0.18);
    display:block;
}
.hero-badge {
    position:absolute; bottom:20px; left:-20px;
    background:#fff; border:1px solid var(--border);
    border-radius:12px; padding:12px 16px;
    box-shadow:0 8px 24px rgba(107,33,232,0.12);
    display:flex; align-items:center; gap:10px;
}
.badge-dot {
    width:8px; height:8px;
    background:#4ade80; border-radius:50%;
    box-shadow:0 0 0 3px rgba(74,222,128,0.25);
    flex-shrink:0;
}
.badge-text { font-size:12px; font-weight:700; color: var(--text); }
.badge-sub { font-size:11px; color: var(--text-muted); }
</style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="nav">
    <div class="nav-logo">ASAPP</div>
    <div class="nav-links">
        <a href="{{ route('login') }}" class="btn-login" id="nav-login">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn-register">Registrar negocio</a>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-tag">Sistema POS para restaurantes</div>
            <h1>Tu restaurante,<br><span>sin fricciones.</span></h1>
            <p>Mesas con QR, pedidos en tiempo real y pagos directos desde el celular del cliente. Sin hardware extra.</p>
            <div class="hero-btns">
                <a href="{{ route('register') }}" class="btn-primary">Registrar mi negocio</a>
                <a href="#como-funciona" class="btn-secondary">Ver cómo funciona</a>
            </div>
            <div class="hero-sub">Sin tarjeta de crédito &middot; Configuración en minutos</div>
        </div>
        <div class="hero-img">
            <img src="/img/hero.png" alt="ASAPP en acción — panel de pedidos en tiempo real">
            <div class="hero-badge">
                <div class="badge-dot"></div>
                <div>
                    <div class="badge-text">Pedido actualizado</div>
                    <div class="badge-sub">Mesa 4 &middot; EN VIVO</div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>
```

- [ ] **Step 2: Verificar en browser**

Abrir `http://localhost/asapp-v2/public/` (o `http://localhost:8000` si usás `artisan serve`).

Verificar:
- Navbar sticky con logo degradado morado y dos botones
- Hero con grid de dos columnas: texto a la izquierda, imagen a la derecha
- Badge verde flotante en la imagen
- Fondo degradado lavanda

- [ ] **Step 3: Ejecutar el test**

```bash
php artisan test tests/Feature/LandingPageTest.php
```

Esperado: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/views/landing.blade.php
git commit -m "feat: landing — navbar + hero con imagen y badge"
```

---

## Task 3: Sección Features (grid 3×2)

**Files:**
- Modify: `resources/views/landing.blade.php` — agregar sección Features antes de `</body>`

- [ ] **Step 1: Agregar CSS de Features dentro del `<style>` existente (antes del cierre `</style>`)**

```css
/* ── FEATURES ────────────────────────────── */
.section-header {
    text-align:center; padding:72px 40px 0;
    background: var(--purple-bg);
}
.section-label {
    font-size:11px; font-weight:700; letter-spacing:3px;
    text-transform:uppercase; color: var(--text-muted); margin-bottom:12px;
}
.section-title {
    font-size:34px; font-weight:800; letter-spacing:-1px; margin-bottom:10px;
}
.section-sub { font-size:15px; color:#6B7280; }

.features-wrap {
    background: var(--purple-bg); padding:40px 40px 72px;
}
.features-grid {
    max-width:1100px; margin:0 auto;
    display:grid; grid-template-columns:repeat(3, 1fr);
    gap:20px; margin-top:40px;
}
.feat-card {
    background:#fff; border:1px solid var(--border);
    border-radius:16px; padding:28px 24px;
    transition: box-shadow 0.2s;
}
.feat-card:hover { box-shadow: var(--shadow-card); }
.feat-icon {
    width:44px; height:44px;
    background:#EDE9FE; border-radius:10px;
    margin-bottom:16px;
    display:flex; align-items:center; justify-content:center;
}
.feat-icon svg {
    width:22px; height:22px;
    stroke: var(--purple); fill:none;
    stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round;
}
.feat-card h3 { font-size:16px; font-weight:700; margin-bottom:8px; }
.feat-card p { font-size:13px; color:#6B7280; line-height:1.6; }
```

- [ ] **Step 2: Agregar HTML de Features antes de `</body>`**

```blade
{{-- FEATURES --}}
<div class="section-header">
    <div class="section-label">Funcionalidades</div>
    <div class="section-title">Todo lo que necesita tu negocio</div>
    <div class="section-sub">Desde el pedido hasta el pago, en un solo lugar.</div>
</div>
<section class="features-wrap">
    <div class="features-grid">

        <div class="feat-card">
            <div class="feat-icon">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><circle cx="17.5" cy="17.5" r="3"/></svg>
            </div>
            <h3>QR por mesa</h3>
            <p>El cliente escanea y accede a su factura. Sin apps, sin descargas.</p>
        </div>

        <div class="feat-card">
            <div class="feat-icon">
                <svg viewBox="0 0 24 24"><path d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
            </div>
            <h3>Pagos desde el celular</h3>
            <p>El cliente paga sin esperar al mesero. Sin efectivo obligatorio.</p>
        </div>

        <div class="feat-card">
            <div class="feat-icon">
                <svg viewBox="0 0 24 24"><path d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3"/></svg>
            </div>
            <h3>Panel en tiempo real</h3>
            <p>Pedidos, ventas e inventario desde cualquier dispositivo.</p>
        </div>

        <div class="feat-card">
            <div class="feat-icon">
                <svg viewBox="0 0 24 24"><path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <h3>Roles de mesero</h3>
            <p>Asigná cuentas con acceso limitado. Cada mesero gestiona sus mesas.</p>
        </div>

        <div class="feat-card">
            <div class="feat-icon">
                <svg viewBox="0 0 24 24"><path d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
            </div>
            <h3>División de cuenta</h3>
            <p>Los clientes dividen ítems entre varios pagadores sin cálculos manuales.</p>
        </div>

        <div class="feat-card">
            <div class="feat-icon">
                <svg viewBox="0 0 24 24"><path d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/></svg>
            </div>
            <h3>Inventario y menú</h3>
            <p>Creá categorías y productos. Activá o desactivá al instante.</p>
        </div>

    </div>
</section>
```

- [ ] **Step 3: Verificar en browser**

Verificar:
- Grid de 6 tarjetas en 3 columnas
- Cada tarjeta tiene icono morado en fondo lavanda, título y descripción
- Fondo lavanda continuo desde el header de sección

- [ ] **Step 4: Commit**

```bash
git add resources/views/landing.blade.php
git commit -m "feat: landing — sección features grid 3×2"
```

---

## Task 4: Sección Panel Preview (mockup browser)

**Files:**
- Modify: `resources/views/landing.blade.php` — agregar sección Panel Preview

- [ ] **Step 1: Agregar CSS de Panel Preview dentro del `<style>` existente**

```css
/* ── PANEL PREVIEW ───────────────────────── */
.panel-preview-wrap {
    background:#fff; padding:72px 40px;
    text-align:center;
}
.panel-preview-wrap .section-header-inline {
    margin-bottom:48px;
}
.panel-preview-wrap .section-label { display:block; margin-bottom:10px; }
.panel-preview-wrap .section-title { margin-bottom:10px; }
.panel-preview-wrap .section-sub { margin-bottom:0; }

.browser-mockup {
    max-width:860px; margin:0 auto;
    border-radius:16px; overflow:hidden;
    box-shadow:0 24px 64px rgba(107,33,232,0.12);
    border:1px solid var(--border);
}
.browser-bar {
    background:#f0f0f5; padding:10px 16px;
    display:flex; align-items:center; gap:10px;
    border-bottom:1px solid var(--border);
}
.browser-dots { display:flex; gap:6px; }
.browser-dot {
    width:10px; height:10px; border-radius:50%;
}
.browser-url {
    flex:1; background:#fff;
    border:1px solid var(--border); border-radius:6px;
    padding:4px 12px; font-size:11px; color:#9B8EC4;
    text-align:left;
}
.browser-body {
    background: var(--purple-bg); padding:24px;
}
.kpi-row {
    display:grid; grid-template-columns:repeat(3,1fr);
    gap:16px; margin-bottom:20px;
}
.kpi-card {
    background:#fff; border:1px solid var(--border);
    border-radius:12px; padding:18px 20px; text-align:left;
}
.kpi-label { font-size:11px; color:#6B7280; font-weight:600; margin-bottom:6px; }
.kpi-value { font-size:22px; font-weight:800; letter-spacing:-0.5px; }
.kpi-value.green { color:#16a34a; }
.kpi-value.purple { color: var(--purple); }
.kpi-value.blue { color:#2563eb; }

.chart-mockup {
    background:#fff; border:1px solid var(--border);
    border-radius:12px; padding:18px 20px;
}
.chart-title { font-size:12px; font-weight:700; color: var(--text); margin-bottom:14px; }
.chart-bars {
    display:flex; align-items:flex-end; gap:8px; height:72px;
}
.chart-bar {
    flex:1; border-radius:4px 4px 0 0;
    background: var(--purple);
}
```

- [ ] **Step 2: Agregar HTML de Panel Preview antes de `</body>` (después de la sección Features)**

```blade
{{-- PANEL PREVIEW --}}
<section class="panel-preview-wrap">
    <div class="section-header-inline">
        <span class="section-label">Panel de control</span>
        <div class="section-title">Estadísticas en tiempo real</div>
        <div class="section-sub">Todo lo que pasa en tu restaurante, visible desde cualquier dispositivo.</div>
    </div>
    <div class="browser-mockup">
        <div class="browser-bar">
            <div class="browser-dots">
                <div class="browser-dot" style="background:#ff5f57;"></div>
                <div class="browser-dot" style="background:#febc2e;"></div>
                <div class="browser-dot" style="background:#28c840;"></div>
            </div>
            <div class="browser-url">app.asapp.co/panel</div>
        </div>
        <div class="browser-body">
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-label">Ventas hoy</div>
                    <div class="kpi-value green">$1.240.000</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Pedidos</div>
                    <div class="kpi-value purple">38</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">Ticket promedio</div>
                    <div class="kpi-value blue">$32.600</div>
                </div>
            </div>
            <div class="chart-mockup">
                <div class="chart-title">Ventas últimos 7 días</div>
                <div class="chart-bars">
                    <div class="chart-bar" style="height:40%; opacity:0.4;"></div>
                    <div class="chart-bar" style="height:60%; opacity:0.5;"></div>
                    <div class="chart-bar" style="height:50%; opacity:0.45;"></div>
                    <div class="chart-bar" style="height:75%; opacity:0.6;"></div>
                    <div class="chart-bar" style="height:55%; opacity:0.5;"></div>
                    <div class="chart-bar" style="height:90%; opacity:0.75;"></div>
                    <div class="chart-bar" style="height:100%; opacity:1;"></div>
                </div>
            </div>
        </div>
    </div>
</section>
```

- [ ] **Step 3: Verificar en browser**

Verificar:
- Barra de navegador simulada con 3 puntos de colores (rojo/amarillo/verde) y URL ficticia
- 3 tarjetas KPI en fila: verde ($1.240.000), morado (38), azul ($32.600)
- Barras de gráfico de distintas alturas, la última al 100%, en degradado de opacidad
- Sección sobre fondo blanco (contraste con el lavanda de Features)

- [ ] **Step 4: Commit**

```bash
git add resources/views/landing.blade.php
git commit -m "feat: landing — sección panel preview con mockup browser"
```

---

## Task 5: Sección Flujo "¿Cómo funciona?" (3 pasos)

**Files:**
- Modify: `resources/views/landing.blade.php` — agregar sección Flujo

- [ ] **Step 1: Agregar CSS de Flujo dentro del `<style>` existente**

```css
/* ── FLUJO ───────────────────────────────── */
.flujo-wrap {
    background: var(--purple-bg); padding:72px 40px;
    text-align:center;
}
.flujo-steps {
    max-width:800px; margin:48px auto 0;
    display:flex; align-items:flex-start;
    position:relative;
}
.flujo-steps::before {
    content:'';
    position:absolute; top:28px; left:calc(16.66% + 28px);
    right:calc(16.66% + 28px); height:2px;
    background:linear-gradient(90deg, var(--purple-lt), var(--purple));
}
.flujo-step { flex:1; text-align:center; padding:0 16px; }
.step-circle {
    width:56px; height:56px; border-radius:50%;
    background:linear-gradient(135deg, var(--purple), var(--purple-dk));
    color:#fff; font-size:20px; font-weight:900;
    display:flex; align-items:center; justify-content:center;
    margin:0 auto 16px; position:relative; z-index:1;
}
.step-title { font-size:15px; font-weight:700; margin-bottom:6px; }
.step-desc { font-size:13px; color:#6B7280; line-height:1.5; }
```

- [ ] **Step 2: Agregar HTML de Flujo antes de `</body>` (después de Panel Preview)**

```blade
{{-- FLUJO --}}
<section class="flujo-wrap" id="como-funciona">
    <div class="section-label">¿Cómo funciona?</div>
    <div class="section-title">Tres pasos, sin complicaciones</div>
    <div class="section-sub">Tu cliente paga solo. Vos cobrás al instante.</div>
    <div class="flujo-steps">
        <div class="flujo-step">
            <div class="step-circle">1</div>
            <div class="step-title">Cliente escanea el QR</div>
            <div class="step-desc">Cada mesa tiene su propio código QR. Sin apps ni descargas.</div>
        </div>
        <div class="flujo-step">
            <div class="step-circle">2</div>
            <div class="step-title">Selecciona y paga</div>
            <div class="step-desc">Ve su factura, elige sus ítems y paga desde el celular.</div>
        </div>
        <div class="flujo-step">
            <div class="step-circle">3</div>
            <div class="step-title">Vos cobrás al instante</div>
            <div class="step-desc">El pago llega directo a tu cuenta. Sin intermediarios.</div>
        </div>
    </div>
</section>
```

- [ ] **Step 3: Verificar en browser**

Verificar:
- 3 círculos numerados con degradado morado conectados por una línea horizontal
- Títulos y descripciones debajo de cada círculo
- El botón "Ver cómo funciona" del Hero scrollea hasta esta sección (`id="como-funciona"`)

- [ ] **Step 4: Commit**

```bash
git add resources/views/landing.blade.php
git commit -m "feat: landing — sección flujo 3 pasos"
```

---

## Task 6: CTA Final + Footer

**Files:**
- Modify: `resources/views/landing.blade.php` — agregar CTA Final y Footer

- [ ] **Step 1: Agregar CSS de CTA + Footer dentro del `<style>` existente**

```css
/* ── CTA FINAL ───────────────────────────── */
.cta-section {
    background:linear-gradient(135deg, var(--purple) 0%, var(--purple-dk) 100%);
    padding:72px 40px; text-align:center;
}
.cta-section h2 {
    font-size:38px; font-weight:900; color:#fff;
    letter-spacing:-1px; margin-bottom:14px;
}
.cta-section p {
    font-size:16px; color:rgba(255,255,255,0.75);
    margin-bottom:36px; max-width:500px; margin-left:auto; margin-right:auto;
}
.btn-white {
    display:inline-block;
    background:#fff; color: var(--purple);
    font-size:15px; font-weight:800;
    padding:16px 36px; border-radius:12px;
    transition: box-shadow 0.15s;
}
.btn-white:hover { box-shadow:0 8px 24px rgba(0,0,0,0.15); }
.cta-note { margin-top:16px; font-size:12px; color:rgba(255,255,255,0.45); }

/* ── FOOTER ──────────────────────────────── */
.footer {
    background:#1a1a2e; padding:28px 40px;
    display:flex; align-items:center; justify-content:space-between;
}
.footer-logo { font-size:18px; font-weight:900; color: var(--purple-lt); }
.footer-copy { font-size:12px; color:#4B5563; }
```

- [ ] **Step 2: Agregar HTML de CTA Final + Footer antes de `</body>`**

```blade
{{-- CTA FINAL --}}
<section class="cta-section">
    <h2>Empezá hoy mismo.</h2>
    <p>Registrá tu negocio en minutos y empezá a cobrar desde el celular de tus clientes.</p>
    <a href="{{ route('register') }}" class="btn-white">Crear cuenta gratis</a>
    <p class="cta-note">Sin tarjeta de crédito &middot; Cancelá cuando quieras</p>
</section>

{{-- FOOTER --}}
<footer class="footer">
    <div class="footer-logo">ASAPP</div>
    <div class="footer-copy">© 2025 ASAPP. Sistema POS para restaurantes.</div>
</footer>
```

- [ ] **Step 3: Verificar en browser**

Verificar:
- CTA con fondo degradado morado intenso, título, párrafo y botón blanco
- Footer oscuro (`#1a1a2e`) con logo en morado claro y copyright

- [ ] **Step 4: Ejecutar test**

```bash
php artisan test tests/Feature/LandingPageTest.php
```

Esperado: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/landing.blade.php
git commit -m "feat: landing — CTA final y footer"
```

---

## Task 7: CSS Responsive (breakpoint ≤ 600px)

**Files:**
- Modify: `resources/views/landing.blade.php` — agregar media queries al final del `<style>`

- [ ] **Step 1: Agregar media queries al final del `<style>`, antes de `</style>`**

```css
/* ── RESPONSIVE ≤ 600px ──────────────────── */
@media (max-width:600px) {

    /* Navbar: ocultar "Iniciar sesión" */
    #nav-login { display:none; }
    .nav { padding:0 20px; }

    /* Hero: columna única, imagen debajo del texto */
    .hero { padding:48px 20px 56px; }
    .hero-inner { grid-template-columns:1fr; gap:32px; }
    .hero-text h1 { font-size:36px; letter-spacing:-1px; }
    .hero-badge { left:12px; bottom:12px; }

    /* Features: 1 columna */
    .section-header { padding:48px 20px 0; }
    .features-wrap { padding:24px 20px 48px; }
    .features-grid { grid-template-columns:1fr; }

    /* Panel Preview */
    .panel-preview-wrap { padding:48px 20px; }
    .kpi-row { grid-template-columns:1fr; }

    /* Flujo: vertical con línea izquierda */
    .flujo-wrap { padding:48px 20px; }
    .flujo-steps { flex-direction:column; align-items:flex-start; gap:0; }
    .flujo-steps::before {
        top:28px; bottom:28px; left:27px;
        right:auto; width:2px; height:auto;
    }
    .flujo-step { display:flex; align-items:flex-start; gap:16px; text-align:left; padding:0 0 32px 0; }
    .flujo-step:last-child { padding-bottom:0; }
    .step-circle { flex-shrink:0; margin:0; }

    /* CTA */
    .cta-section { padding:56px 20px; }
    .cta-section h2 { font-size:28px; }

    /* Footer */
    .footer { padding:20px; flex-direction:column; gap:8px; text-align:center; }
}
```

- [ ] **Step 2: Verificar en browser en modo móvil**

En Chrome DevTools, activar modo responsivo a 390px de ancho (iPhone 14).

Verificar:
- Navbar solo muestra "Registrar negocio" (el botón de login desaparece)
- Hero en columna única: texto arriba, imagen debajo
- Features en 1 columna
- KPI cards en 1 columna
- Flujo en vertical con línea izquierda y círculos alineados a la izquierda

- [ ] **Step 3: Ejecutar test final**

```bash
php artisan test tests/Feature/LandingPageTest.php
```

Esperado: PASS.

- [ ] **Step 4: Commit final**

```bash
git add resources/views/landing.blade.php
git commit -m "feat: landing — responsive breakpoint 600px"
```

---

## Checklist de spec coverage

| Sección spec | Task que la implementa |
|---|---|
| Navbar sticky con logo + 2 botones | Task 2 |
| Hero split layout con imagen y badge | Task 2 |
| Features grid 3×2 con 6 tarjetas | Task 3 |
| Panel Preview mockup browser + KPIs + chart | Task 4 |
| Flujo 3 pasos con línea horizontal | Task 5 |
| CTA Final con fondo degradado | Task 6 |
| Footer oscuro | Task 6 |
| Ruta `GET /` → `view('landing')` | Task 1 |
| Responsive ≤ 600px (hero col, features 1col, flujo vertical, navbar) | Task 7 |
| Test HTTP 200 | Task 1 |
