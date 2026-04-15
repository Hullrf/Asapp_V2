# Landing Page ASAPP — Design Spec

## Goal
Reemplazar la ruta raíz `/` (actualmente redirige a `/login`) con una landing page de presentación para ASAPP, dirigida a dueños de restaurantes. La página incluye un CTA de registro pero no menciona precios (se agrega en el futuro).

## Architecture

Archivo único: `resources/views/landing.blade.php`  
Ruta: `GET /` → controlador o closure que retorna la vista `landing`  
Assets: `public/img/hero.png` (imagen generada, ya copiada)  
CSS: inline en la vista, usando `asapp-base.css` para fuente y tokens  

No requiere autenticación, no usa layouts existentes, es autocontenida.

## Tech Stack
- Laravel 12 Blade (sin Tailwind — CSS inline igual que el resto de vistas)
- Plus Jakarta Sans via `asapp-base.css`
- SVG Heroicons inline
- Sin JavaScript (la landing es estática)

---

## Secciones

### 1. Navbar
- Logo **ASAPP** (degradado morado, `gradient-text`)
- Botón **Iniciar sesión** → `route('login')` (outline morado)
- Botón **Registrar negocio** → `route('register')` (fondo morado sólido)
- Sticky, fondo blanco, borde inferior `#E0D9F5`

### 2. Hero (split layout)
- **Izquierda**: tag "Sistema POS para restaurantes" + H1 "Tu restaurante, sin fricciones." + párrafo + 2 botones CTA + nota "Sin tarjeta de crédito · Configuración en minutos"
- **Derecha**: `public/img/hero.png` con border-radius, sombra morada, y una badge flotante en la esquina inferior izquierda ("Pedido actualizado · Mesa 4 · EN VIVO")
- Fondo: degradado suave `#F4F1FA → #EDE9FE → #F4F1FA`
- En móvil: columna única, imagen debajo del texto

### 3. Features (grid 3×2)
6 tarjetas con icono SVG + título + descripción:
1. **QR por mesa** — el cliente escanea y accede a su factura
2. **Pagos desde el celular** — sin esperar al mesero
3. **Panel en tiempo real** — pedidos, ventas e inventario
4. **Roles de mesero** — cuentas con acceso limitado
5. **División de cuenta** — divide ítems entre varios pagadores
6. **Inventario y menú** — activá productos al instante

En móvil: grid 1 columna

### 4. Panel Preview
- Fondo blanco, centrado, max-width 900px
- Label superior: "Panel de control" (texto morado, mayúsculas, letra spacing)
- H2: "Estadísticas en tiempo real"
- Subtítulo: "Todo lo que pasa en tu restaurante, visible desde cualquier dispositivo."
- Mockup de browser window:
  - Barra del navegador simulada (3 puntos de colores + URL ficticia `app.asapp.co/panel`)
  - Fondo `#F4F1FA`, padding interno
  - Fila de 3 tarjetas KPI:
    1. **Ventas hoy** — `$1.240.000` — color verde `#4ade80`
    2. **Pedidos** — `38` — color morado `#6B21E8`
    3. **Ticket promedio** — `$32.600` — color azul `#3b82f6`
  - Barra de gráfico simplificada (7 barras de altura variable, color `#6B21E8` con opacidad decreciente)
- Sombra: `0 24px 64px rgba(107,33,232,0.12)`, border-radius 16px

### 5. Flujo "¿Cómo funciona?" (3 pasos)
Línea horizontal conectando 3 círculos numerados:
1. Cliente escanea el QR
2. Selecciona y paga
3. Vos cobrás al instante

En móvil: columna vertical con línea izquierda

### 6. CTA Final
- Fondo degradado `#6B21E8 → #3D0E8A`
- H2 "Empezá hoy mismo."
- Párrafo de apoyo
- Botón blanco "Crear cuenta gratis" → `route('register')`
- Nota: "Sin tarjeta de crédito · Cancelá cuando quieras"

### 7. Footer
- Fondo `#1a1a2e`
- Logo ASAPP en `#C4B5FD`
- Copyright "© 2025 ASAPP. Sistema POS para restaurantes."

---

## Routing

```php
// routes/web.php — reemplazar la línea actual:
// Route::get('/', fn() => redirect()->route('login'));
// por:
Route::get('/', fn() => view('landing'))->name('home');
```

---

## Responsive

| Breakpoint | Cambio |
|---|---|
| ≤ 600px | Hero: columna única (imagen debajo del texto) |
| ≤ 600px | Features: 1 columna |
| ≤ 600px | Flujo: vertical con línea izquierda en vez de horizontal |
| ≤ 600px | Navbar: ocultar "Iniciar sesión", solo mostrar "Registrar" |

---

## Fuera de scope
- Sección de precios (pendiente definición de costos)
- Animaciones de scroll / AOS
- Video de demo
- Testimonios / social proof
- Múltiples idiomas
