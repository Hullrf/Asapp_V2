# Rediseño UI — Vistas Mesero y Factura — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Aplicar el sistema de tokens CSS del panel admin (near-black topbar, Plus Jakarta Sans, variables CSS completas) a las vistas de mesero y factura, sin tocar lógica PHP.

**Architecture:** CSS inline en blade files (patrón del proyecto). Dos tareas independientes — mesero primero, factura después. Cada tarea reemplaza el bloque `:root`, actualiza el topbar, y migra todos los valores hardcodeados a CSS vars.

**Tech Stack:** Laravel 12, Blade templates, CSS custom properties inline

---

## Archivos involucrados

| Archivo | Tipo |
|---|---|
| `resources/views/mesero/index.blade.php` | Modificar — `:root` + topbar + componentes |
| `resources/views/factura/show.blade.php` | Modificar — `:root` (migración tokens) + topbar + componentes |

---

## Task 1: Mesero — `:root` y topbar

**Files:**
- Modify: `resources/views/mesero/index.blade.php`

- [ ] **Step 1: Reemplazar el bloque `:root` y los estilos de `body`**

Localizar en `mesero/index.blade.php` el bloque que comienza con `* { margin: 0; padding: 0;` (línea ~10) y reemplazar hasta la llave de cierre del body style por:

```css
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --bg:#F4F1FA; --surface:#ffffff; --surface2:#FAF8FF;
  --border:#E0D9F5; --border-soft:#EDE9F8;
  --sb-bg:#0F0A1E;
  --purple:#6B21E8; --purple-dk:#3D0E8A; --purple-lt:#8B5CF6;
  --purple-dim:rgba(107,33,232,0.10); --purple-glow:rgba(107,33,232,0.20);
  --accent:#C4A0FF;
  --text:#1a1a2e; --text-muted:#6B7280; --text-faint:#9B8EC4;
  --danger:#B91C1C; --danger-bg:#FEF2F2; --danger-border:#FECACA;
  --r-sm:6px; --r-md:10px; --r-lg:14px; --r-xl:20px;
  --shadow-sm:0 1px 4px rgba(107,33,232,0.06);
  --shadow-md:0 4px 16px rgba(107,33,232,0.10);
  --shadow-lg:0 8px 32px rgba(0,0,0,0.18);
  --font:'Plus Jakarta Sans',system-ui,sans-serif;
  --mono:ui-monospace,'SF Mono',Menlo,monospace;
}

body {
    font-family: var(--font);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
}
```

- [ ] **Step 2: Actualizar el topbar**

Localizar el bloque `.topbar { ... }` y `.topbar-logo { ... }` y reemplazarlos por:

```css
.topbar {
    background: var(--sb-bg);
    color: #fff;
    padding: 0 24px;
    height: 60px;
    display: flex;
    align-items: center;
    gap: 16px;
    position: sticky;
    top: 0;
    z-index: 100;
    box-shadow: var(--shadow-lg);
}

.topbar-logo {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -1px;
    background: linear-gradient(135deg, #C4A0FF, #A78BFA);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    flex-shrink: 0;
}
```

- [ ] **Step 3: Actualizar `.btn-logout`**

```css
.btn-logout {
    background: rgba(107,33,232,0.15);
    color: var(--accent);
    border: 1px solid rgba(107,33,232,0.3);
    padding: 7px 16px;
    border-radius: var(--r-md);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
    white-space: nowrap;
}
.btn-logout:hover { background: rgba(107,33,232,0.3); }
```

- [ ] **Step 4: Verificar topbar visualmente**

Abrir `http://localhost/asapp-v2/public/mesero` (o la ruta configurada en XAMPP). Confirmar que el topbar es near-black (`#0F0A1E`) y el logo tiene el degradado lila.

- [ ] **Step 5: Commit**

```bash
git add resources/views/mesero/index.blade.php
git commit -m "style(mesero): tokens CSS y topbar near-black"
```

---

## Task 2: Mesero — cards, mesa-cards, botones y modal

**Files:**
- Modify: `resources/views/mesero/index.blade.php`

- [ ] **Step 1: Actualizar `.card` y `.card-title`**

```css
.card {
    background: var(--surface);
    border-radius: var(--r-lg);
    padding: 20px;
    border: 1px solid var(--border);
    margin-bottom: 20px;
    box-shadow: var(--shadow-sm);
}

.card-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}
```

- [ ] **Step 2: Actualizar `.mesa-card` y sus variantes**

```css
.mesa-card {
    background: var(--surface2);
    border: 2px solid var(--border);
    border-radius: var(--r-lg);
    padding: 20px 16px;
    text-align: center;
    transition: border-color 0.2s, box-shadow 0.2s;
    cursor: default;
}
.mesa-card.ocupada { border-color: var(--purple-lt); background: #EDE9FE; }
.mesa-card.libre   { border-color: var(--border); background: var(--surface2); }
.mesa-card:hover   { box-shadow: var(--shadow-md); }

.mesa-icono  { display:flex; align-items:center; justify-content:center; gap:6px; margin-bottom:10px; color:var(--purple); }
.mesa-status-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.dot-libre   { background:#22c55e; box-shadow:0 0 0 3px rgba(34,197,94,0.2); }
.dot-ocupada { background:#eab308; box-shadow:0 0 0 3px rgba(234,179,8,0.2); }
.mesa-nombre { font-size: 15px; font-weight: 700; color: var(--purple-dk); margin-bottom: 6px; }

.mesa-estado {
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-block;
    margin-bottom: 12px;
}
.estado-libre   { background: var(--surface2); color: var(--text-faint); }
.estado-ocupada { background: #C4B5FD; color: var(--purple-dk); }
.mesa-acciones  { display: flex; flex-direction: column; gap: 6px; }
```

- [ ] **Step 3: Actualizar `.piso-label`**

```css
.piso-label {
    font-size: 14px;
    font-weight: 700;
    color: var(--purple-dk);
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}
```

- [ ] **Step 4: Actualizar botones**

```css
.btn {
    padding: 10px 18px;
    border-radius: var(--r-md);
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: opacity 0.2s;
    white-space: nowrap;
    min-height: 44px;
    touch-action: manipulation;
}
.btn:hover { opacity: 0.85; }
.btn-primary { background: var(--purple); color: #fff; box-shadow: 0 2px 8px rgba(107,33,232,0.25); }
.btn-success { background: var(--purple-dk); color: #fff; }
.btn-outline { background: transparent; color: var(--text-muted); border: 1.5px solid var(--border); }
.btn-sm      { padding: 8px 14px; font-size: 13px; min-height: 44px; }
.btn-block   { width: 100%; }
```

- [ ] **Step 5: Actualizar el modal nuevo pedido**

Reemplazar los bloques `.modal`, `.np-handle`, `.np-header h3`, `.np-close`, `#np-buscador`, `.cant-btn`, `.cant-num`, `.np-footer`:

```css
.modal {
    background: var(--surface);
    border-radius: var(--r-xl) var(--r-xl) 0 0;
    padding: 24px 20px 20px;
    width: 100%;
    max-width: 640px;
    max-height: 92dvh;
    box-shadow: var(--shadow-lg);
    display: flex;
    flex-direction: column;
}

.np-handle {
    width: 40px; height: 4px;
    background: var(--border);
    border-radius: 4px;
    margin: 0 auto 16px;
    flex-shrink: 0;
}

.np-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
    flex-shrink: 0;
}
.np-header h3 { font-size: 17px; font-weight: 700; color: var(--purple-dk); }

.np-close {
    background: var(--purple-dim);
    border: none;
    color: var(--purple);
    cursor: pointer;
    padding: 0;
    border-radius: var(--r-md);
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    min-height: 44px;
    touch-action: manipulation;
}

#np-buscador {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    font-size: 15px;
    background: var(--surface2);
    color: var(--text);
    outline: none;
    font-family: inherit;
    margin-bottom: 10px;
    flex-shrink: 0;
}
#np-buscador:focus { border-color: var(--purple); box-shadow: 0 0 0 3px var(--purple-glow); }

.cant-btn {
    width: 44px; height: 44px;
    background: var(--purple-dim);
    border: none;
    border-radius: var(--r-md);
    font-size: 20px;
    color: var(--purple);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    font-weight: 700;
    touch-action: manipulation;
    flex-shrink: 0;
}
.cant-btn:active { background: #C4B5FD; }
.cant-num {
    width: 32px;
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    color: var(--purple-dk);
}

.np-footer {
    display: flex;
    gap: 10px;
    padding-top: 14px;
    flex-shrink: 0;
    border-top: 1px solid var(--border);
    margin-top: 10px;
}
```

- [ ] **Step 6: Actualizar lista de productos y toast**

```css
.prod-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 10px;
    border-radius: var(--r-md);
    border-bottom: 1px solid var(--border-soft);
    transition: background 0.15s;
    cursor: pointer;
    user-select: none;
}
.prod-item:active  { background: var(--surface2); }
.prod-item.selected { background: #EDE9FE; }
.prod-item.hidden  { display: none; }

.prod-nombre { font-size: 14px; font-weight: 600; color: var(--text); }
.prod-cat    { font-size: 12px; color: var(--text-faint); }
.prod-precio { font-size: 13px; font-weight: 700; color: var(--purple); white-space: nowrap; flex-shrink: 0; }

#mesero-toast.toast-ok  { background: var(--purple-dim); color: var(--purple-dk); border: 1px solid #C4B5FD; }
```

- [ ] **Step 7: Actualizar breakpoint tablet (modal centrado)**

Localizar `@media (min-width: 640px)` y actualizar el border-radius del modal:

```css
@media (min-width: 640px) {
    .modal {
        border-radius: var(--r-xl);
        max-width: 580px;
        margin-bottom: 40px;
        max-height: 88vh;
    }
    .modal-overlay { align-items: center; }
    .np-handle { display: none; }
    .mesas-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); }
}
```

- [ ] **Step 8: Verificar visualmente**

- Grid de mesas: cards con borde `#E0D9F5`, sombra sutil
- Mesa ocupada: borde lila `#8B5CF6`, fondo `#EDE9FE`
- Modal nuevo pedido: abrir una mesa libre y confirmar bottom-sheet near-black → lavanda
- Botones: púrpura con sombra sutil

- [ ] **Step 9: Commit**

```bash
git add resources/views/mesero/index.blade.php
git commit -m "style(mesero): componentes alineados al sistema de diseño del panel"
```

---

## Task 3: Factura — `:root`, topbar y migración de tokens

**Files:**
- Modify: `resources/views/factura/show.blade.php`

- [ ] **Step 1: Reemplazar el bloque `:root` completo**

Localizar el bloque `:root { ... }` (~línea 11) en `factura/show.blade.php` y reemplazarlo con:

```css
:root {
  --bg:#F4F1FA; --surface:#ffffff; --surface2:#FAF8FF;
  --border:#E0D9F5; --border-soft:#EDE9F8;
  --sb-bg:#0F0A1E;
  --purple:#6B21E8; --purple-dk:#3D0E8A; --purple-lt:#8B5CF6;
  --purple-dim:rgba(107,33,232,0.10); --purple-glow:rgba(107,33,232,0.20);
  --accent:#C4A0FF;
  --text:#1a1a2e; --text-muted:#6B7280; --text-faint:#9B8EC4;
  --danger:#B91C1C; --danger-bg:#FEF2F2; --danger-border:#FECACA;
  --r-sm:6px; --r-md:10px; --r-lg:14px; --r-xl:20px;
  --shadow-sm:0 1px 4px rgba(107,33,232,0.06);
  --shadow-md:0 4px 16px rgba(107,33,232,0.10);
  --shadow-lg:0 8px 32px rgba(0,0,0,0.18);
  --font:'Plus Jakarta Sans',system-ui,sans-serif;
  --mono:ui-monospace,'SF Mono',Menlo,monospace;
}
```

- [ ] **Step 2: Migrar tokens viejos en todo el archivo**

Hacer reemplazos globales en el bloque `<style>`:

| Buscar | Reemplazar con |
|---|---|
| `var(--muted)` | `var(--text-faint)` |
| `var(--sans)` | `var(--font)` |
| `var(--border-hot)` | `var(--border)` |

Los tokens `--gold` y `--teal` se eliminaron del `:root` (no se referencian en el CSS).

- [ ] **Step 3: Actualizar el topbar y logo**

Reemplazar `.topbar { ... }` y `.logo { ... }`:

```css
.topbar {
    position: sticky;
    top: 0;
    z-index: 100;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 32px;
    height: 60px;
    background: var(--sb-bg);
    border-bottom: 1px solid rgba(255,255,255,0.07);
    box-shadow: var(--shadow-lg);
}

.logo {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -1px;
    background: linear-gradient(135deg, #C4A0FF, #A78BFA);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
```

- [ ] **Step 4: Actualizar `.btn-logout`**

```css
.btn-logout {
    font-family: var(--font);
    font-size: 12px;
    font-weight: 600;
    color: var(--accent);
    background: rgba(107,33,232,0.15);
    border: 1px solid rgba(107,33,232,0.3);
    padding: 7px 14px;
    border-radius: var(--r-md);
    text-decoration: none;
    transition: background 0.2s;
    cursor: pointer;
}
.btn-logout:hover { background: rgba(107,33,232,0.3); }
```

- [ ] **Step 5: Verificar topbar**

Abrir una factura en el navegador (`/factura/{id}`). Confirmar topbar near-black con degradado lila en el logo.

- [ ] **Step 6: Commit**

```bash
git add resources/views/factura/show.blade.php
git commit -m "style(factura): tokens CSS migrados y topbar near-black"
```

---

## Task 4: Factura — cards, tabla, botones, división y modal

**Files:**
- Modify: `resources/views/factura/show.blade.php`

- [ ] **Step 1: Actualizar `.card`, `.pago-card`, `.card-header`, `.card-title`, `.card-body`**

```css
.card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.pago-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.card-header {
    padding: 18px 24px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.card-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-faint);
}

.card-body { padding: 20px 24px; }
```

- [ ] **Step 2: Actualizar tabla**

```css
thead tr { border-bottom: 1px solid var(--border); }

thead th {
    padding: 12px 16px;
    text-align: left;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--text-faint);
    white-space: nowrap;
}

tbody tr { border-bottom: 1px solid var(--border-soft); transition: background 0.15s; }
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: var(--purple-dim); }

.precio-unit { font-family: var(--mono); font-size: 12px; color: var(--text-faint); }
.ipo-desglose.ipo-line { color: var(--purple); font-weight: 600; }
```

- [ ] **Step 3: Actualizar inputs y dropdowns**

```css
.small-input {
    width: 58px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    color: var(--text);
    padding: 5px 8px;
    font-size: 12px;
    font-family: var(--mono);
    text-align: center;
    outline: none;
}
.small-input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px var(--purple-glow); }

.form-agregar select,
.form-agregar input[type="number"] {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    color: var(--text);
    padding: 10px 12px;
    font-size: 13px;
    outline: none;
    -webkit-appearance: none;
}

.prod-search-input {
    width: 100%;
    padding: 9px 14px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    font-size: 13px;
    font-family: var(--font);
    color: var(--text);
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.15s;
}
.prod-search-input:focus { border-color: var(--text-faint); }
.prod-search-input.tiene-seleccion { border-color: var(--purple); background: var(--surface2); font-weight: 600; }

.prod-dropdown {
    display: none;
    position: fixed;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    max-height: 220px;
    overflow-y: auto;
    z-index: 9999;
    box-shadow: var(--shadow-md);
}
.prod-dropdown.visible { display: block; }
.prod-option {
    padding: 9px 14px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid var(--surface2);
}
.prod-option:last-child { border-bottom: none; }
.prod-option:hover, .prod-option.activo { background: var(--surface2); }
.prod-option-nombre { font-size: 13px; color: var(--text); }
.prod-option-precio { font-size: 11px; color: var(--text-faint); font-family: var(--mono); white-space: nowrap; }
.prod-sin-resultados { padding: 12px 14px; font-size: 12px; color: var(--text-faint); text-align: center; }
```

- [ ] **Step 4: Actualizar botones (excepto `.btn-efectivo`)**

```css
.btn {
    font-family: var(--font);
    font-size: 11px;
    font-weight: 700;
    padding: 5px 11px;
    border-radius: var(--r-sm);
    border: none;
    cursor: pointer;
    transition: opacity 0.15s, transform 0.1s;
    white-space: nowrap;
}
.btn:hover { opacity: 0.82; transform: translateY(-1px); }
.btn-editar   { background: var(--purple-dim); color: var(--purple); }
.btn-eliminar { background: var(--purple-dim); color: var(--purple); }

.btn-crear {
    background: var(--purple);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    padding: 10px 18px;
    border-radius: var(--r-md);
    border: none;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-crear:hover { background: var(--purple-lt); }

.btn-reabrir {
    width: 100%;
    background: transparent;
    color: var(--purple);
    border: 1.5px solid var(--purple);
    border-radius: var(--r-md);
    padding: 12px;
    font-family: var(--font);
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.btn-reabrir:hover { background: var(--purple); color: #fff; }

.btn-pagar {
    width: 100%;
    background: var(--purple);
    color: #fff;
    border: none;
    border-radius: var(--r-md);
    padding: 14px;
    font-family: var(--font);
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    touch-action: manipulation;
    transition: background 0.2s, transform 0.1s;
    letter-spacing: 0.3px;
}
.btn-pagar:hover:not(:disabled) { background: var(--purple-dk); transform: translateY(-1px); }
.btn-pagar:disabled {
    background: var(--purple-dim);
    color: rgba(107,33,232,0.4);
    cursor: not-allowed;
}
```

> **NO tocar** `.btn-efectivo` — mantiene verde `#16a34a` (semántico: pago en efectivo).

- [ ] **Step 5: Actualizar badges de estado y alert**

```css
.estado-pendiente { background: var(--surface2); color: var(--text-faint); border: 1px solid var(--border); }
.estado-parcial   { background: var(--purple-dim); color: var(--purple-dk); border: 1px solid #C4B5FD; }
.estado-pagado    { background: var(--purple-dk); color: #fff; border: 1px solid #2d0a6b; }

.alert-pagado {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--purple-dim);
    border: 1px solid #C4B5FD;
    border-radius: var(--r-md);
    padding: 12px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--purple-dk);
}

.empty-state { text-align: center; padding: 48px 24px; color: var(--text-faint); }
```

- [ ] **Step 6: Actualizar panel de división (vista cliente)**

```css
.btn-dividir {
    background: none;
    border: 1px dashed var(--text-faint);
    color: var(--text-faint);
    border-radius: var(--r-sm);
    padding: 2px 7px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 4px;
    letter-spacing: 0.5px;
    transition: all 0.15s;
    white-space: nowrap;
}
.btn-dividir:hover { background: var(--purple-dim); border-color: var(--purple); color: var(--purple); }

.division-panel {
    background: linear-gradient(135deg, var(--surface2) 0%, #fdf8ff 100%);
    border-top: 2px solid var(--border-soft);
    border-bottom: 1px solid var(--border-soft);
    padding: 14px 20px;
}
.division-titulo { font-size: 13px; font-weight: 700; color: var(--purple); }
.division-sub { font-size: 11px; color: var(--text-faint); margin-left: 6px; }
.btn-div-mod  { background: var(--purple-dim); color: var(--purple); }

.div-parte {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    padding: 10px 14px;
    min-width: 140px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.div-parte.tomada-mia  { border-color: var(--purple); background: var(--surface2); }
.div-parte.tomada-otro { border-color: var(--border); opacity: 0.7; }
.div-parte-num  { font-size: 10px; color: var(--text-faint); font-weight: 700; text-transform: uppercase; }
.div-parte-monto { font-size: 16px; font-weight: 800; color: var(--text); font-family: var(--mono); }
.div-esperando { font-size: 11px; color: var(--text-faint); font-style: italic; margin-top: 2px; }

.btn-tomar {
    margin-top: 4px;
    background: var(--purple);
    color: #fff;
    border: none;
    border-radius: var(--r-sm);
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s;
}
.btn-tomar:hover { background: var(--purple-dk); }
.btn-liberar {
    margin-top: 4px;
    background: var(--surface2);
    color: var(--purple);
    border: 1px solid var(--border);
    border-radius: var(--r-sm);
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
```

- [ ] **Step 7: Actualizar modal de división**

```css
.modal-box {
    background: var(--surface);
    border-radius: var(--r-lg);
    padding: 28px;
    max-width: 480px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
}
.modal-titulo { font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
.modal-subtitulo { font-size: 13px; color: var(--text-faint); margin-bottom: 20px; }

.modal-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 6px;
    display: block;
}
.modal-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    font-size: 14px;
    font-family: var(--font);
    box-sizing: border-box;
    margin-bottom: 16px;
}
.modal-input:focus { outline: none; border-color: var(--purple); box-shadow: 0 0 0 3px var(--purple-glow); }

.partes-stepper {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 20px;
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    overflow: hidden;
    width: fit-content;
}
.stepper-btn {
    background: var(--surface2);
    border: none;
    color: var(--purple);
    font-size: 22px;
    font-weight: 700;
    width: 52px;
    height: 52px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s;
    line-height: 1;
    flex-shrink: 0;
}
.stepper-btn:hover:not(:disabled) { background: #EDE9FE; }
.stepper-btn:disabled { opacity: 0.35; cursor: not-allowed; }
.stepper-val {
    font-size: 22px;
    font-weight: 800;
    color: var(--text);
    min-width: 56px;
    text-align: center;
    font-family: var(--mono);
    border-left: 1.5px solid var(--border);
    border-right: 1.5px solid var(--border);
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-parte-label { font-size: 12px; color: var(--text-faint); font-weight: 700; min-width: 55px; }
.modal-parte-input {
    flex: 1;
    padding: 8px 12px;
    border: 1.5px solid var(--border);
    border-radius: var(--r-md);
    font-size: 14px;
    font-family: var(--mono);
}
.modal-parte-input:focus { outline: none; border-color: var(--purple); box-shadow: 0 0 0 3px var(--purple-glow); }
.modal-restante { text-align: right; font-size: 12px; color: var(--text-faint); margin-bottom: 16px; }
.modal-restante.error { color: var(--danger); font-weight: 700; }

.btn-modal-cancel {
    padding: 10px 20px;
    border-radius: var(--r-md);
    border: 1.5px solid var(--border);
    background: var(--surface);
    color: var(--purple);
    font-weight: 700;
    cursor: pointer;
    font-size: 14px;
}
.btn-modal-confirm {
    padding: 10px 20px;
    border-radius: var(--r-md);
    border: none;
    background: var(--purple);
    color: #fff;
    font-weight: 700;
    cursor: pointer;
    font-size: 14px;
}
.btn-modal-confirm:disabled { background: var(--purple-dim); cursor: not-allowed; }
```

- [ ] **Step 8: Verificar las tres vistas de factura**

1. **Vista admin** (`/factura/{id}` logueado como admin): confirmar tabla con botones editar/eliminar en púrpura suave, botón "Cobrar efectivo" verde (sin cambio), topbar near-black.
2. **Vista mesero** (`/factura/{id}` logueado como mesero): confirmar misma tabla sin opciones de pago.
3. **Vista cliente** (`/factura/{id}` sin sesión / desde QR): confirmar checkboxes de selección, panel de división con tokens, modal de partes.

> **NO tocar** `.live-badge` / `.live-dot` — se mantienen verdes (estado en vivo).

- [ ] **Step 9: Commit**

```bash
git add resources/views/factura/show.blade.php
git commit -m "style(factura): componentes alineados al sistema de diseño del panel"
```
