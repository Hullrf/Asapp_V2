# Rediseño UI — Vistas Mesero y Factura

**Objetivo:** Aplicar el sistema de diseño del panel admin rediseñado (tokens CSS, topbar near-black, Plus Jakarta Sans) a las vistas de mesero y factura, sin tocar lógica PHP ni estructura de rutas.

**Patrón del proyecto:** CSS inline en blade files. No se crea ningún archivo CSS externo.

---

## Archivos involucrados

| Archivo | Tipo |
|---|---|
| `resources/views/mesero/index.blade.php` | Modificar — CSS tokens + componentes |
| `resources/views/factura/show.blade.php` | Modificar — CSS tokens + componentes |

---

## Sección 1: Tokens CSS y topbar (ambas vistas)

### `:root` — idéntico al panel

Reemplazar el bloque `:root` existente (o el bloque de variables hardcodeadas) con el set completo de tokens del panel:

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

### Topbar

- `background: var(--sb-bg)` (reemplaza `#3D0E8A` en ambas vistas)
- Logo ASAPP con degradado `linear-gradient(135deg, #C4A0FF, #A78BFA)` igual que `.sb-logo` del panel
- El resto del topbar (padding, height, flex, shadow) sin cambios estructurales

---

## Sección 2: Vista mesero (`mesero/index.blade.php`)

### Cards de piso
- `background: var(--surface)` → sin cambio (ya era `#fff`)
- `border: 1px solid var(--border)` → reemplaza `#E0D9F5`
- `border-radius: var(--r-lg)` → reemplaza `14px`
- `box-shadow: var(--shadow-sm)` → reemplaza `0 2px 8px rgba(107,33,232,0.06)`
- `.card-title` → `color: var(--text)` (antes `#3D0E8A`)

### Mesa cards
- `background: var(--surface2)` libre / `#EDE9FE` ocupada (sin cambio funcional)
- `border: 2px solid var(--border)` libre / `var(--purple-lt)` ocupada
- `border-radius: var(--r-lg)`
- `.mesa-nombre` → `color: var(--purple-dk)`
- `.piso-label` → `color: var(--purple-dk)`

### Botones
- `.btn-primary` → `background: var(--purple); box-shadow: 0 2px 8px rgba(107,33,232,0.25)`
- `.btn-success` → `background: var(--purple-dk)`
- `.btn-outline` → `border: 1.5px solid var(--border); color: var(--text-muted)`
- `border-radius: var(--r-md)` en todos

### Modal nuevo pedido
- `border-radius: var(--r-xl) var(--r-xl) 0 0` (tablet+: `var(--r-xl)`)
- `.np-handle` → `background: var(--border)`
- `#np-buscador` → `border: 1.5px solid var(--border); background: var(--surface2)`
- `#np-buscador:focus` → `border-color: var(--purple)`
- `.cant-btn` → `background: var(--purple-dim); color: var(--purple); border-radius: var(--r-md)`
- `.np-footer` → `border-top: 1px solid var(--border)`

### Colores hardcodeados a reemplazar
| Valor hardcodeado | Reemplazar con |
|---|---|
| `#6B21E8` | `var(--purple)` |
| `#3D0E8A` | `var(--purple-dk)` |
| `#8B5CF6` | `var(--purple-lt)` |
| `#E0D9F5` | `var(--border)` |
| `#D4C9F0` | `var(--border)` |
| `#FAF8FF` | `var(--surface2)` |
| `#9B8EC4` | `var(--text-faint)` |
| `#1a1a2e` | `var(--text)` |
| `14px` border-radius | `var(--r-lg)` |
| `10px` border-radius | `var(--r-md)` |
| `6px` border-radius | `var(--r-sm)` |
| `20px` border-radius | `var(--r-xl)` |

---

## Sección 3: Vista factura (`factura/show.blade.php`)

Un archivo, tres roles (admin / mesero / cliente) separados por condicionales Blade. El CSS es compartido.

### Migración de tokens viejos
| Token viejo | Token nuevo |
|---|---|
| `--muted` | `--text-faint` (`#9B8EC4`) |
| `--sans` | `--font` |
| `--border-hot` | `--border` |
| `--gold`, `--teal` | Eliminar (ambos eran `#6B21E8`) |
| `--mono` | Mantener igual |

### Topbar
- `background: var(--sb-bg)` (reemplaza `#3D0E8A`)
- Logo con degradado `#C4A0FF → #A78BFA`

### Cards (info pedido, ítems, totales, pago)
- `border-radius: var(--r-lg)` (pasa de 16px a 14px — diferencia mínima)
- `box-shadow: var(--shadow-sm)`
- `.card-header` → `border-bottom: 1px solid var(--border)`
- `.card-title` → `color: var(--text-faint)` (texto secundario uppercase)

### Inputs y selects
- `.small-input`, `.prod-search-input`, `.form-agregar select/input` → `background: var(--surface2); border-color: var(--border)`
- Focus → `border-color: var(--purple); box-shadow: 0 0 0 3px var(--purple-glow)`

### Botones
- `.btn-pagar` → `background: var(--purple)` (ya es `#6B21E8`)
- `.btn-crear` → `background: var(--purple)`
- `.btn-reabrir` → `border-color: var(--purple); color: var(--purple)`
- `.btn-editar`, `.btn-eliminar` → `background: var(--purple-dim); color: var(--purple)`
- `.btn-div-mod` → `background: var(--purple-dim); color: var(--purple)`
- `.btn-tomar` → `background: var(--purple)`

### División (cliente)
- `.division-panel` → `background: linear-gradient(135deg, var(--surface2), #fdf8ff); border-color: var(--border-soft)`
- `.div-parte` → `border: 1.5px solid var(--border)`
- `.div-parte.tomada-mia` → `border-color: var(--purple); background: var(--surface2)`
- `.division-titulo` → `color: var(--purple)`

### Modal división
- `.modal-box` → `border-radius: var(--r-lg); box-shadow: var(--shadow-lg)`

### Excepciones semánticas (NO tocar)
- `.btn-efectivo` — verde `#16a34a` (pago en efectivo)
- `.live-badge`, `.live-dot` — verde `#16a34a` (estado en vivo)
- `.estado-pagado` — `background: #3D0E8A` (hardcodeado intencional)

---

## Fuera de alcance

- Lógica PHP / controladores
- Estructura de rutas
- Vistas de pasarela (`pasarela/show.blade.php`, etc.)
- Vistas de auth, superadmin, landing
