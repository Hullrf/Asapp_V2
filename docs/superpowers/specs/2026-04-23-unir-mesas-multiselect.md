# Unir Mesas — Multi-select con Modal

**Objetivo:** Reemplazar el dropdown de un solo select para unir mesas por un modal con checkboxes multi-select, disponible en el panel admin y en la vista de mesero. La mesa en la que se pulsa el botón actúa como principal; las seleccionadas se convierten en secundarias.

**Patrón del proyecto:** CSS inline en blade files. No se crea ningún archivo CSS externo.

---

## Archivos involucrados

| Archivo | Tipo |
|---|---|
| `resources/views/admin/partials/_mesa-card.blade.php` | Modificar — reemplazar form dropdown por botón + modal |
| `resources/views/admin/panel.blade.php` | Modificar — agregar modal + JS |
| `resources/views/mesero/index.blade.php` | Modificar — agregar botón "Unir mesas" + modal + JS |
| `app/Http/Controllers/Admin/MesaController.php` | Modificar — agregar método `unirGrupo()`, eliminar `unir()` |
| `app/Http/Controllers/MeseroController.php` | Modificar — agregar método `unirGrupo()` |
| `routes/web.php` | Modificar — agregar ruta `unir-grupo` en admin y mesero, eliminar ruta `unir` |

---

## Sección 1: Backend

### Nuevo endpoint (admin)

```
POST /panel/mesas/{mesa}/unir-grupo
```

- Nombre de ruta: `panel.mesas.unir-grupo`
- Middleware: `admin` (existente)
- Reemplaza: `panel.mesas.unir`

### Nuevo endpoint (mesero)

```
POST /mesero/mesas/{mesa}/unir-grupo
```

- Nombre de ruta: `mesero.mesas.unir-grupo`
- Middleware: `mesero` (existente)

### Método `unirGrupo(Request $request, Mesa $mesa)` — idéntico en ambos controladores

`MeseroController` no tiene `autorizarMesa()`. Agregar antes de `unirGrupo`:

```php
private function autorizarMesa(Mesa $mesa): void
{
    if ($mesa->id_negocio !== auth()->user()->id_negocio) {
        abort(403);
    }
}
```

```php
public function unirGrupo(Request $request, Mesa $mesa)
{
    $this->autorizarMesa($mesa);

    if ($mesa->estaUnida()) {
        $msg = '❌ Esta mesa ya es secundaria de otra. Sepárala primero.';
        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $msg], 422)
            : back()->with('message', $msg);
    }

    if ($mesa->estaOcupada()) {
        $msg = '❌ La mesa base tiene un pedido activo.';
        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $msg], 422)
            : back()->with('message', $msg);
    }

    $ids = array_filter((array) $request->input('id_mesas', []), 'is_numeric');

    if (empty($ids)) {
        $msg = '❌ Selecciona al menos una mesa.';
        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $msg], 422)
            : back()->with('message', $msg);
    }

    $unidas   = [];
    $omitidas = [];

    foreach ($ids as $id) {
        $secundaria = Mesa::where('id_mesa', $id)
            ->where('id_negocio', $mesa->id_negocio)
            ->first();

        if (! $secundaria
            || $secundaria->id_mesa === $mesa->id_mesa
            || $secundaria->estaUnida()
            || $secundaria->estaOcupada()) {
            $omitidas[] = $secundaria?->nombre_display ?? "#$id";
            continue;
        }

        $secundaria->update(['mesa_principal_id' => $mesa->id_mesa]);
        $unidas[] = $secundaria->nombre_display;
    }

    if (empty($unidas)) {
        $msg = '❌ Ninguna mesa pudo unirse. ' . (empty($omitidas) ? '' : 'Omitidas: ' . implode(', ', $omitidas));
        return $request->ajax()
            ? response()->json(['success' => false, 'message' => $msg], 422)
            : back()->with('message', $msg);
    }

    $msg = '✅ Unidas a ' . $mesa->nombre_display . ': ' . implode(', ', $unidas);
    if (! empty($omitidas)) {
        $msg .= '. Omitidas: ' . implode(', ', $omitidas);
    }

    return $request->ajax()
        ? response()->json(['success' => true, 'message' => $msg])
        : back()->with('message', $msg);
}
```

### Cambios en rutas (`routes/web.php`)

Eliminar:
```php
Route::post('/mesas/{mesa}/unir', [MesaController::class, 'unir'])->name('mesas.unir');
```

Agregar (dentro del grupo `panel`):
```php
Route::post('/mesas/{mesa}/unir-grupo', [MesaController::class, 'unirGrupo'])->name('mesas.unir-grupo');
```

Agregar (dentro del grupo `mesero`):
```php
Route::post('/mesas/{mesa}/unir-grupo', [MeseroController::class, 'unirGrupo'])->name('mesero.mesas.unir-grupo');
```

### Eliminar método `unir()` de `MesaController`

El método `unir()` (líneas ~100-129) se elimina. El nuevo `unirGrupo()` lo reemplaza completamente.

---

## Sección 2: Admin Panel (`_mesa-card.blade.php` + `panel.blade.php`)

### `_mesa-card.blade.php` — Reemplazar form de dropdown

**Eliminar** el bloque completo del form (actualmente visible cuando `!$ocupada && !$esSecundaria && $mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty()`):

```php
{{-- ELIMINAR este bloque --}}
@if (!$ocupada && !$esSecundaria && $mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty())
<form action="{{ route('panel.mesas.unir', $mesa) }}" method="POST" data-ajax data-refresh="mesas" ...>
    ...
</form>
@endif
```

**Agregar** botón que abre el modal (misma condición de visibilidad, pero ahora el botón se muestra en la mesa que será PRINCIPAL — libre, no secundaria, con candidatas disponibles):

```php
@php
    $candidatas = $mesas
        ->where('id_negocio', $mesa->id_negocio)
        ->where('id_mesa', '!=', $mesa->id_mesa)
        ->filter(fn($m) => !$m->estaUnida() && !$m->estaOcupada() && $m->mesasUnidas->isEmpty());
@endphp

@if (!$ocupada && !$esSecundaria && $mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty())
<button type="button"
        class="btn btn-ghost btn-sm"
        onclick="abrirModalUnir({{ $mesa->id_mesa }}, '{{ addslashes($mesa->nombre_display) }}')"
        title="Unir mesas">
    <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14">
        <path d="M13 10a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM7 16a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
        <path stroke-dasharray="3 2" d="M10 7l-3 6"/>
    </svg>
    Unir
</button>
@endif
```

La variable `$mesas` (colección de todas las mesas del negocio) ya está disponible en el contexto del panel. Se usa en lugar de `$mesas`.

### Modal en `panel.blade.php`

Agregar antes del cierre `</body>`:

```html
{{-- MODAL UNIR GRUPO --}}
<div id="modal-unir-grupo" style="
    display:none; position:fixed; inset:0; z-index:800;
    background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
  <div style="
      background:var(--surface); border-radius:var(--r-xl); padding:24px;
      width:min(420px,92vw); max-height:80vh; display:flex; flex-direction:column;
      box-shadow:var(--shadow-lg); border:1px solid var(--border);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <h3 id="modal-unir-titulo" style="font-size:15px;font-weight:700;color:var(--text);margin:0;"></h3>
      <button onclick="cerrarModalUnir()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:20px;line-height:1;">×</button>
    </div>
    <div id="modal-unir-lista" style="overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px;"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;">
      <button onclick="cerrarModalUnir()" class="btn btn-outline btn-sm">Cancelar</button>
      <button id="modal-unir-confirmar" onclick="confirmarUnirGrupo()" class="btn btn-primary btn-sm" disabled>Confirmar unión (0)</button>
    </div>
  </div>
</div>
```

### JS en `panel.blade.php`

```javascript
// ── Unir mesas (multi-select) ──────────────────────────────────────────
let _unirBaseMesaId   = null;
let _unirBaseRuta     = null;

// todasLasMesasData: array plano de mesas libres, no secundarias, sin hijas
// Se renderiza desde Blade como JSON
const todasLasMesasData = @json(
    $mesas
        ->filter(fn($m) => !$m->estaUnida() && !$m->estaOcupada() && $m->mesasUnidas->isEmpty())
        ->values()
        ->map(fn($m) => [
            'id'     => $m->id_mesa,
            'nombre' => $m->nombre_display,
            'piso'   => $m->piso?->nombre ?? 'Sin piso',
        ])
);

function abrirModalUnir(mesaId, mesaNombre) {
    _unirBaseMesaId = mesaId;
    _unirBaseRuta   = `/panel/mesas/${mesaId}/unir-grupo`;

    document.getElementById('modal-unir-titulo').textContent = `Unir mesas a ${mesaNombre}`;

    const candidatas = todasLasMesasData.filter(m => m.id !== mesaId);
    const lista      = document.getElementById('modal-unir-lista');
    lista.innerHTML  = '';

    if (candidatas.length === 0) {
        lista.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No hay mesas libres disponibles.</p>';
        document.getElementById('modal-unir-confirmar').disabled = true;
    } else {
        // Agrupar por piso
        const porPiso = {};
        candidatas.forEach(m => {
            if (!porPiso[m.piso]) porPiso[m.piso] = [];
            porPiso[m.piso].push(m);
        });
        Object.entries(porPiso).forEach(([piso, mesas]) => {
            const label = document.createElement('p');
            label.style = 'font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-faint);margin:8px 0 4px;';
            label.textContent = piso;
            lista.appendChild(label);

            const grid = document.createElement('div');
            grid.style = 'display:flex;flex-wrap:wrap;gap:6px;';
            mesas.forEach(m => {
                const chip = document.createElement('label');
                chip.style = 'display:flex;align-items:center;gap:6px;padding:6px 10px;border:1.5px solid var(--border);border-radius:var(--r-md);cursor:pointer;font-size:12px;font-weight:500;color:var(--text);background:var(--surface2);transition:border-color 0.15s,background 0.15s;';
                chip.innerHTML = `<input type="checkbox" value="${m.id}" style="accent-color:var(--purple);" onchange="actualizarContadorUnir()"> ${m.nombre}`;
                chip.querySelector('input').addEventListener('change', function() {
                    chip.style.borderColor = this.checked ? 'var(--purple)' : 'var(--border)';
                    chip.style.background  = this.checked ? 'var(--purple-dim)' : 'var(--surface2)';
                    chip.style.color       = this.checked ? 'var(--purple)' : 'var(--text)';
                });
                grid.appendChild(chip);
            });
            lista.appendChild(grid);
        });
    }

    actualizarContadorUnir();
    const modal = document.getElementById('modal-unir-grupo');
    modal.style.display = 'flex';
}

function cerrarModalUnir() {
    document.getElementById('modal-unir-grupo').style.display = 'none';
    _unirBaseMesaId = null;
}

function actualizarContadorUnir() {
    const checked = document.querySelectorAll('#modal-unir-lista input[type=checkbox]:checked').length;
    const btn     = document.getElementById('modal-unir-confirmar');
    btn.textContent = `Confirmar unión (${checked})`;
    btn.disabled    = checked === 0;
}

async function confirmarUnirGrupo() {
    const ids = [...document.querySelectorAll('#modal-unir-lista input[type=checkbox]:checked')]
        .map(cb => cb.value);

    if (!ids.length) return;

    const fd = new FormData();
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    ids.forEach(id => fd.append('id_mesas[]', id));

    try {
        const res  = await fetch(_unirBaseRuta, { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body:fd });
        const data = await res.json();
        showToast(data.message, data.success !== false);
        if (data.success !== false) {
            cerrarModalUnir();
            await refreshPartials(['mesas']);
        }
    } catch {
        showToast('❌ Error de conexión', false);
    }
}

document.getElementById('modal-unir-grupo').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalUnir();
});
```

---

## Sección 3: Vista mesero (`mesero/index.blade.php`)

### Botón "Unir mesas" en cada mesa card

Agregar en la sección de acciones de la card (mesas libres y no secundarias, con candidatas disponibles):

```php
@php
    $candidatasMesero = $mesas
        ->where('id_negocio', $negocio->id_negocio)
        ->where('id_mesa', '!=', $mesa->id_mesa)
        ->filter(fn($m) => !$m->estaUnida() && !$m->estaOcupada() && $m->mesasUnidas->isEmpty());
@endphp

@if (!$mesa->estaUnida() && !$mesa->estaOcupada() && $mesa->mesasUnidas->isEmpty() && $candidatasMesero->isNotEmpty())
<button type="button"
        class="btn-outline"
        style="font-size:11px;padding:5px 10px;border-radius:var(--r-md);..."
        onclick="abrirModalUnirMesero({{ $mesa->id_mesa }}, '{{ addslashes($mesa->nombre_display) }}')">
    Unir mesas
</button>
@endif
```

### Modal mesero

Estructura idéntica al modal del panel pero con ID `modal-unir-mesero` y ruta `/mesero/mesas/{id}/unir-grupo`. Misma lógica JS con prefijo `mesero` para evitar colisión de nombres con el panel.

### JS mesero

Idéntico al del panel pero:
- `todasLasMesasDataMesero` en lugar de `todasLasMesasData`
- `_unirBaseRuta` apunta a `/mesero/mesas/${mesaId}/unir-grupo`
- Al confirmar exitosamente recarga con `location.reload()` (en lugar de `refreshPartials`)

---

## Excepciones / No tocar

- Botón "Separar" en mesas secundarias — sin cambios
- Lógica de `estaOcupada()` / `estaUnida()` en el modelo — sin cambios
- Vista de factura — sin cambios
- Vistas de pasarela, auth, superadmin — fuera de alcance
