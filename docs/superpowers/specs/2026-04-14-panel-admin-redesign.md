# Panel Admin — Rediseño UI/UX

**Fecha:** 2026-04-14
**Alcance:** Panel admin (`/panel`) — todas las secciones
**Enfoque:** Rediseño estructural + visual (Enfoque 2)

---

## Decisiones de diseño

| Dimensión | Decisión |
|---|---|
| Tipografía | Plus Jakarta Sans (Google Fonts) — todas las vistas del panel |
| Sidebar | Near-black `#0F0A1E` con acentos púrpura `#C4A0FF` |
| Fondo contenido | Lavanda suave `#F4F1FA` (actual, conservado) |
| Color dominante | Morado `#6B21E8` / `#3D0E8A` — identidad ASAPP |
| Colores destructivos | Rojo apagado `#B91C1C` **solo** en acciones irreversibles (eliminar) |
| Íconos | SVG inline (Heroicons) — cero emojis en UI estructural |
| Navegación desktop | Sidebar vertical permanente (≥ 900px) |
| Navegación móvil | Tabs horizontales + swipe (< 900px) — comportamiento conservado |

---

## Sistema de tokens CSS

Todos los tokens se definen como variables CSS en `:root` dentro de `panel.blade.php`. Los partials los heredan sin redefinirlos.

```css
:root {
  /* Superficies */
  --bg:           #F4F1FA;
  --surface:      #ffffff;
  --surface2:     #FAF8FF;
  --border:       #E0D9F5;
  --border-soft:  #EDE9F8;

  /* Sidebar */
  --sb-bg:        #0F0A1E;
  --sb-active:    rgba(107,33,232,0.30);
  --sb-text:      rgba(255,255,255,0.45);
  --sb-text-on:   #C4A0FF;
  --sb-divider:   rgba(255,255,255,0.07);

  /* Marca */
  --purple:       #6B21E8;
  --purple-dk:    #3D0E8A;
  --purple-lt:    #8B5CF6;
  --purple-dim:   rgba(107,33,232,0.10);
  --purple-glow:  rgba(107,33,232,0.20);
  --accent:       #C4A0FF;

  /* Texto */
  --text:         #1a1a2e;
  --text-muted:   #6B7280;
  --text-faint:   #9B8EC4;

  /* Semánticos */
  --danger:       #B91C1C;
  --danger-bg:    #FEF2F2;
  --danger-border:#FECACA;
  --warn-bg:      #FFFBEB;
  --warn-text:    #92400E;
  --warn-border:  #FDE68A;

  /* Escala de radio */
  --r-sm:  6px;
  --r-md:  10px;
  --r-lg:  14px;
  --r-xl:  20px;

  /* Sombras */
  --shadow-sm: 0 1px 4px rgba(107,33,232,0.06);
  --shadow-md: 0 4px 16px rgba(107,33,232,0.10);
  --shadow-lg: 0 8px 32px rgba(0,0,0,0.18);

  /* Tipografía */
  --font: 'Plus Jakarta Sans', system-ui, sans-serif;
}
```

---

## Estructura HTML del panel

### Layout general

```
body
└── .panel-shell                         ← flex row, 100vw/100vh
    ├── <aside>.sidebar                  ← 220px, near-black
    │   ├── .sb-header                   ← min-height: 96px (espacio para logo futuro)
    │   ├── .sb-section                  ← nav items agrupados por sección
    │   │   ├── .sb-section-label        ← "Gestión", "Equipo"
    │   │   └── .sb-item[.active]        ← con ::before barra izquierda púrpura
    │   └── .sb-footer                   ← avatar + nombre + rol
    └── <main>.main                      ← flex column, flex:1
        ├── .topbar                      ← 56px, título sección + sede-chip
        └── .content                     ← overflow-y auto, padding 24px 28px
```

### Breakpoint móvil (< 900px)

```
body
└── .panel-shell (flex column)
    ├── .m-topbar                        ← near-black, logo + sede compacto
    ├── .m-tabs                          ← tabs horizontales con overflow-x scroll
    ├── .swipe-hint                      ← indicador visual "← desliza →"
    └── .content                         ← secciones apiladas, swipe JS conservado
```

El sidebar se oculta con `display:none` en `@media (max-width: 900px)`. Las `.m-tabs` y `.m-topbar` se muestran solo en móvil.

---

## Jerarquía de botones

| Clase | Uso | Visual |
|---|---|---|
| `.btn-primary` | Acción principal de la página (guardar, crear) | Morado sólido + sombra |
| `.btn-ghost` | Acción secundaria positiva (editar, ver) | Fondo morado tenue |
| `.btn-outline` | Acción neutral (exportar, limpiar) | Borde gris, texto muted |
| `.btn-danger` | **Solo** acciones irreversibles (eliminar) | Fondo rojo suave `#FEF2F2`, texto `#B91C1C` |

Nunca usar `.btn-danger` para acciones que se puedan deshacer.

---

## Archivos a modificar

| Archivo | Cambio |
|---|---|
| `panel.blade.php` | Reemplazar estructura tabs → sidebar; nuevo sistema CSS tokens; breakpoint móvil; Google Fonts |
| `partials/inventario.blade.php` | SVG icons, btn-danger solo en eliminar, cat-chips, alert-warn |
| `partials/mesas.blade.php` | SVG icons, nueva mesa-card (estado visual), piso-header, form agregar unificado |
| `partials/_mesa-card.blade.php` | Reescritura completa: SVG icons, union-badge, jerarquía de botones |
| `partials/estadisticas.blade.php` | SVG icons en chart titles, btn Personalizar al topbar, periodo-pills |
| `partials/historial.blade.php` | SVG icon en búsqueda, btn-ghost en "Ver", detalle-chips |
| `partials/meseros.blade.php` | Tabla → mesero-cards con avatar de iniciales, SVG icon en eliminar |

---

## Sección por sección

### Inventario
- KPIs en grid 4 columnas: Total productos, Disponibles, Stock bajo (card ámbar), Categorías
- Alerta de stock con SVG ícono de advertencia (reemplaza ⚠️ emoji)
- Categorías como chips interactivos con botón × para eliminar
- Formulario "Agregar producto" en grid 2 columnas
- Tabla de productos: columnas #, Categoría, Precio, Stock, Estado, Acciones
- Badges de estado: verde (Disponible), ámbar (Stock bajo), gris (No disponible)

### Mesas
- Card unificada "Agregar": crear piso y crear mesa en grid 2 columnas
- Cards de mesa con estado visual: fondo tintado + borde morado para ocupadas, borde punteado para secundarias
- `union-badge` con SVG ícono de enlace reemplaza emojis 🔗/🪑
- Acciones priorizadas: botón principal arriba (Nuevo pedido / Ver factura), secundarias (QR, Eliminar) abajo
- Alias con campo inline + botón ícono

### Estadísticas
- KPIs en grid 6 columnas; total cobrado con `.highlight` (fondo púrpura suave)
- Botón "Personalizar" en topbar (no en el contenido)
- Charts grid 2 columnas, stock fuera del grid en ancho completo (sin cambio estructural)
- Todos los chart headers con SVG icon + título, sin emojis
- Periodo-pills uniformes en todas las gráficas
- Paleta de gráficas: escala morada `['#6B21E8','#8B5CF6','#A78BFA','#C4A0FF','#DDD6FE']`
- Modal Personalizar: toggles estilizados con tokens del sistema

### Historial
- Filtros: input con ícono SVG interno, datepicker, botón limpiar
- Botón "Ver" como `.btn-ghost` en lugar de `.btn-primary`
- Detalle expandible conservado, chips estilizados

### Meseros
- Lista como grid de cards con avatar de iniciales (gradiente morado)
- Formulario "Agregar mesero" en grid 4 columnas: Nombre, Email, Contraseña, Botón
- Botón "Eliminar" como `.btn-danger` con SVG ícono de basura

---

## Logo sidebar

El área `.sb-header` tiene `min-height: 96px` y `justify-content: center` para acomodar un logo imagen en el futuro sin reajustar estructura. Cuando se reemplace el texto "ASAPP" por una imagen, solo cambiar el contenido interno del `.sb-header`.

---

## Qué NO cambia

- Lógica PHP/Laravel: controllers, routes, middlewares
- Sistema AJAX (`data-ajax`, `data-refresh`, parcials)
- Rutas de parcials (`/panel/partials/*`)
- Funcionalidad de swipe entre secciones en móvil
- Vista mesero (`/mesero`) — es independiente
- Vista factura (`/factura`) — es independiente
- Sistema de configuración de gráficas (`$configPanel`)
- Lógica de unión/separación de mesas
