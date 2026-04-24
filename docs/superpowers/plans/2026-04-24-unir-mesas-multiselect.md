# Unir Mesas Multi-select Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Reemplazar el dropdown de un solo select para unir mesas por un modal con checkboxes multi-select, disponible en el panel admin y en la vista de mesero.

**Architecture:** Nuevo endpoint `POST /mesas/{mesa}/unir-grupo` en admin y mesero, que acepta `id_mesas[]` y asigna múltiples secundarias en un loop. El frontend usa un modal compartido poblado desde una variable JS renderizada por Blade. El admin usa el patrón `data-ajax`/`refreshPartials` existente; el mesero hace `location.reload()` tras éxito.

**Tech Stack:** Laravel 12 Blade (CSS inline), PHP 8.x, Vanilla JS, CSS custom properties existentes.

---

## Archivos involucrados

| Archivo | Acción |
|---|---|
| `app/Http/Controllers/Admin/MesaController.php` | Agregar `unirGrupo()`, eliminar `unir()` |
| `app/Http/Controllers/Mesero/MeseroController.php` | Agregar `autorizarMesa()` + `unirGrupo()` |
| `routes/web.php` | Reemplazar ruta `mesas.unir` → `mesas.unir-grupo`; agregar ruta mesero |
| `resources/views/admin/partials/_mesa-card.blade.php` | Reemplazar form dropdown por botón |
| `resources/views/admin/partials/mesas.blade.php` | Agregar `<script>` con `todasLasMesasData` |
| `resources/views/admin/panel.blade.php` | Agregar modal HTML + JS |
| `resources/views/mesero/index.blade.php` | Agregar botón + modal HTML + JS |

---

### Task 1: Backend admin — `unirGrupo` + rutas

**Files:**
- Modify: `app/Http/Controllers/Admin/MesaController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Agregar `unirGrupo()` en `MesaController` (antes de `separar()`)**

En `app/Http/Controllers/Admin/MesaController.php`, insertar el siguiente método entre la línea que cierra `unir()` (línea 129) y `separar()` (línea 131):

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
                || $secundaria->estaOcupada()
                || $secundaria->mesasUnidas()->exists()) {
                $omitidas[] = $secundaria?->nombre_display ?? "#$id";
                continue;
            }

            $secundaria->update(['mesa_principal_id' => $mesa->id_mesa]);
            $unidas[] = $secundaria->nombre_display;
        }

        if (empty($unidas)) {
            $msg = '❌ Ninguna mesa pudo unirse.' . (! empty($omitidas) ? ' Omitidas: ' . implode(', ', $omitidas) : '');
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

- [ ] **Step 2: Eliminar el método `unir()` de `MesaController`**

Eliminar las líneas 100–129 completas (el método `unir()`):

```php
    public function unir(Request $request, Mesa $mesa)
    {
        // ... todo el cuerpo
    }
```

El archivo queda con la secuencia: `destroy()` → `unirGrupo()` → `separar()` → `autorizarMesa()`.

- [ ] **Step 3: Actualizar `routes/web.php`**

Localizar la línea (aprox. 91):
```php
Route::post('/mesas/{mesa}/unir',        [MesaController::class, 'unir'])->name('mesas.unir');
```

Reemplazarla por:
```php
Route::post('/mesas/{mesa}/unir-grupo',  [MesaController::class, 'unirGrupo'])->name('mesas.unir-grupo');
```

La ruta `mesas.separar` en la línea siguiente no se toca.

- [ ] **Step 4: Verificar rutas**

```bash
php artisan route:list --name=mesas
```

Resultado esperado: aparece `panel.mesas.unir-grupo` y `panel.mesas.separar`. **No** debe aparecer `panel.mesas.unir`.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/MesaController.php routes/web.php
git commit -m "feat: reemplazar unir() por unirGrupo() multi-select en admin"
```

---

### Task 2: Backend mesero — `unirGrupo` + ruta

**Files:**
- Modify: `app/Http/Controllers/Mesero/MeseroController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Agregar `autorizarMesa()` y `unirGrupo()` al final de `MeseroController`**

En `app/Http/Controllers/Mesero/MeseroController.php`, antes de la llave `}` de cierre de la clase (actualmente línea 85), agregar:

```php
    public function unirGrupo(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $negocio = auth()->user()->negocio;

        if ($mesa->estaUnida()) {
            return response()->json(['success' => false, 'message' => '❌ Esta mesa ya es secundaria. Sepárala primero.'], 422);
        }

        if ($mesa->estaOcupada()) {
            return response()->json(['success' => false, 'message' => '❌ La mesa base tiene un pedido activo.'], 422);
        }

        $ids = array_filter((array) $request->input('id_mesas', []), 'is_numeric');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => '❌ Selecciona al menos una mesa.'], 422);
        }

        $unidas   = [];
        $omitidas = [];

        foreach ($ids as $id) {
            $secundaria = Mesa::where('id_mesa', $id)
                ->where('id_negocio', $negocio->id_negocio)
                ->first();

            if (! $secundaria
                || $secundaria->id_mesa === $mesa->id_mesa
                || $secundaria->estaUnida()
                || $secundaria->estaOcupada()
                || $secundaria->mesasUnidas()->exists()) {
                $omitidas[] = $secundaria?->nombre_display ?? "#$id";
                continue;
            }

            $secundaria->update(['mesa_principal_id' => $mesa->id_mesa]);
            $unidas[] = $secundaria->nombre_display;
        }

        if (empty($unidas)) {
            $msg = '❌ Ninguna mesa pudo unirse.' . (! empty($omitidas) ? ' Omitidas: ' . implode(', ', $omitidas) : '');
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        $msg = '✅ Unidas a ' . $mesa->nombre_display . ': ' . implode(', ', $unidas);
        if (! empty($omitidas)) {
            $msg .= '. Omitidas: ' . implode(', ', $omitidas);
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    private function autorizarMesa(Mesa $mesa): void
    {
        abort_unless($mesa->id_negocio === auth()->user()->negocio->id_negocio, 403);
    }
```

- [ ] **Step 2: Agregar ruta mesero en `routes/web.php`**

Localizar el grupo mesero (aprox. líneas 47–51):
```php
Route::middleware('mesero')->prefix('mesero')->name('mesero.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Mesero\MeseroController::class, 'index'])->name('index');
    Route::post('/pedidos', [\App\Http\Controllers\Mesero\MeseroController::class, 'storePedido'])->name('pedidos.store');
});
```

Agregar la nueva ruta dentro del grupo:
```php
Route::middleware('mesero')->prefix('mesero')->name('mesero.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Mesero\MeseroController::class, 'index'])->name('index');
    Route::post('/pedidos', [\App\Http\Controllers\Mesero\MeseroController::class, 'storePedido'])->name('pedidos.store');
    Route::post('/mesas/{mesa}/unir-grupo', [\App\Http\Controllers\Mesero\MeseroController::class, 'unirGrupo'])->name('mesas.unir-grupo');
});
```

- [ ] **Step 3: Verificar rutas**

```bash
php artisan route:list --name=mesero
```

Resultado esperado: aparece `mesero.mesas.unir-grupo`.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/Mesero/MeseroController.php routes/web.php
git commit -m "feat: agregar unirGrupo multi-select en MeseroController"
```

---

### Task 3: Admin UI — botón + modal

**Files:**
- Modify: `resources/views/admin/partials/_mesa-card.blade.php`
- Modify: `resources/views/admin/partials/mesas.blade.php`
- Modify: `resources/views/admin/panel.blade.php`

- [ ] **Step 1: Actualizar `_mesa-card.blade.php` — bloque `@php` y botón "Unir mesas"**

**1a.** En el bloque `@php` inicial (líneas 1–16), reemplazar la variable `$candidatas` por `$candidatasMesas` con filtro corregido. Reemplazar:

```php
    $candidatas    = $mesas->filter(fn($m) =>
        $m->id_mesa !== $mesa->id_mesa &&
        ! $m->estaUnida() &&
        $m->pedidos->isEmpty()
    );
```

Por:

```php
    $candidatasMesas = $mesas->filter(fn($m) =>
        $m->id_mesa !== $mesa->id_mesa &&
        ! $m->estaUnida() &&
        $m->pedidos->isEmpty() &&
        $m->mesasUnidas->isEmpty()
    );
```

**1b.** Reemplazar el bloque entero del form de unir (líneas 108–125):

```php
        {{-- Unir mesa (libre, no secundaria, hay candidatas) --}}
        @if (! $ocupada && ! $esSecundaria && $mesa->mesasUnidas->isEmpty() && $candidatas->isNotEmpty())
            <form action="{{ route('panel.mesas.unir', $mesa) }}" method="POST"
                  data-ajax data-refresh="mesas"
                  style="display:flex;gap:6px;">
                @csrf
                <select name="id_mesa_principal" required style="flex:1;font-size:12px;padding:5px 8px;">
                    <option value="">— Unir con —</option>
                    @foreach ($candidatas as $c)
                        <option value="{{ $c->id_mesa }}">{{ $c->nombre_display }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-ghost btn-sm" title="Unir mesa">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/></svg>
                    Unir
                </button>
            </form>
        @endif
```

Por:

```php
        {{-- Unir mesas (libre, no secundaria, hay candidatas disponibles) --}}
        @if (! $ocupada && ! $esSecundaria && $candidatasMesas->isNotEmpty())
            <button type="button"
                    class="btn btn-ghost btn-sm"
                    style="width:100%;justify-content:center;"
                    onclick="abrirModalUnir({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/></svg>
                Unir mesas
            </button>
        @endif
```

- [ ] **Step 2: Agregar script de datos al final de `mesas.blade.php`**

Al final del archivo `resources/views/admin/partials/mesas.blade.php` (después de todo el HTML existente), agregar:

```html
<script>
var todasLasMesasData = @json(
    $mesas
        ->filter(fn($m) => ! $m->estaUnida() && $m->pedidos->isEmpty() && $m->mesasUnidas->isEmpty())
        ->values()
        ->map(fn($m) => [
            'id'     => $m->id_mesa,
            'nombre' => $m->nombre_display,
            'piso'   => optional($m->piso)->nombre ?? 'Sin piso',
        ])
);
</script>
```

Este script se re-ejecuta automáticamente cada vez que `refreshPartials(['mesas'])` actualiza el partial (vía `activarScripts()`), manteniendo los datos sincronizados.

- [ ] **Step 3: Agregar HTML del modal en `panel.blade.php`**

Buscar el div `id="panel-toast"` al final de la sección HTML (antes de los `<script>`). Agregar el modal justo antes de ese div:

```html
{{-- MODAL UNIR MESAS (multi-select) --}}
<div id="modal-unir-grupo" style="display:none;position:fixed;inset:0;z-index:800;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
  <div style="background:var(--surface);border-radius:var(--r-xl);padding:24px;width:min(420px,92vw);max-height:80vh;display:flex;flex-direction:column;box-shadow:var(--shadow-lg);border:1px solid var(--border);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <h3 id="modal-unir-titulo" style="font-size:15px;font-weight:700;color:var(--text);margin:0;"></h3>
      <button onclick="cerrarModalUnir()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:22px;line-height:1;padding:0 4px;">×</button>
    </div>
    <div id="modal-unir-lista" style="overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px;min-height:40px;"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;">
      <button onclick="cerrarModalUnir()" class="btn btn-outline btn-sm">Cancelar</button>
      <button id="modal-unir-confirmar" onclick="confirmarUnirGrupo()" class="btn btn-primary btn-sm" disabled>Confirmar unión (0)</button>
    </div>
  </div>
</div>
```

- [ ] **Step 4: Agregar JS del modal en `panel.blade.php`**

Al final del bloque `<script>` existente (después del bloque de sede switcher, antes de `</script>`), agregar:

```javascript
// ── Unir mesas multi-select ────────────────────────────────────────────
var todasLasMesasData = [];
var _unirBaseMesaId   = null;

function abrirModalUnir(mesaId, mesaNombre) {
    _unirBaseMesaId = mesaId;
    document.getElementById('modal-unir-titulo').textContent = 'Unir mesas a ' + mesaNombre;

    const candidatas = todasLasMesasData.filter(function(m) { return m.id !== mesaId; });
    const lista      = document.getElementById('modal-unir-lista');
    lista.innerHTML  = '';

    if (candidatas.length === 0) {
        lista.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No hay mesas libres disponibles.</p>';
    } else {
        const porPiso = {};
        candidatas.forEach(function(m) {
            if (!porPiso[m.piso]) porPiso[m.piso] = [];
            porPiso[m.piso].push(m);
        });
        Object.keys(porPiso).forEach(function(piso) {
            const lbl = document.createElement('p');
            lbl.style.cssText = 'font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-faint);margin:8px 0 4px;';
            lbl.textContent   = piso;
            lista.appendChild(lbl);

            const grid = document.createElement('div');
            grid.style.cssText = 'display:flex;flex-wrap:wrap;gap:6px;';
            porPiso[piso].forEach(function(m) {
                const chip = document.createElement('label');
                chip.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1.5px solid var(--border);border-radius:var(--r-md);cursor:pointer;font-size:12px;font-weight:500;color:var(--text);background:var(--surface2);transition:border-color 0.15s,background 0.15s;';
                const cb = document.createElement('input');
                cb.type  = 'checkbox';
                cb.value = m.id;
                cb.style.accentColor = 'var(--purple)';
                cb.addEventListener('change', function() {
                    chip.style.borderColor = cb.checked ? 'var(--purple)' : 'var(--border)';
                    chip.style.background  = cb.checked ? 'var(--purple-dim)' : 'var(--surface2)';
                    chip.style.color       = cb.checked ? 'var(--purple)' : 'var(--text)';
                    actualizarContadorUnir();
                });
                chip.appendChild(cb);
                chip.appendChild(document.createTextNode(' ' + m.nombre));
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
    const n   = document.querySelectorAll('#modal-unir-lista input[type=checkbox]:checked').length;
    const btn = document.getElementById('modal-unir-confirmar');
    btn.textContent = 'Confirmar unión (' + n + ')';
    btn.disabled    = n === 0;
}

async function confirmarUnirGrupo() {
    const ids = Array.from(document.querySelectorAll('#modal-unir-lista input[type=checkbox]:checked'))
        .map(function(cb) { return cb.value; });
    if (!ids.length || !_unirBaseMesaId) return;

    const fd = new FormData();
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    ids.forEach(function(id) { fd.append('id_mesas[]', id); });

    try {
        const res  = await fetch('/panel/mesas/' + _unirBaseMesaId + '/unir-grupo', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        });
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

- [ ] **Step 5: Verificación manual**

1. Abrir el panel admin → pestaña Mesas
2. Con al menos dos mesas libres: verificar que aparece botón "Unir mesas" en cada mesa libre no-secundaria
3. Pulsar "Unir mesas" en Mesa 1 → modal se abre con título "Unir mesas a Mesa 1"
4. Verificar que Mesa 1 no aparece como opción en la lista
5. Seleccionar Mesa 2 → botón dice "Confirmar unión (1)"
6. Pulsar Confirmar → toast "✅ Unidas a Mesa 1: Mesa 2" → modal cierra → grid se actualiza
7. Verificar que Mesa 2 ahora muestra badge "Unida a: Mesa 1"
8. Verificar que Mesa 1 muestra badge "Grupo: Mesa 1 + Mesa 2"
9. Verificar que NO aparece el botón "Unir mesas" en la barra de Mesa 2 (es secundaria)
10. Con solo 1 mesa libre: verificar que el botón "Unir mesas" no aparece (sin candidatas)

- [ ] **Step 6: Commit**

```bash
git add resources/views/admin/partials/_mesa-card.blade.php \
        resources/views/admin/partials/mesas.blade.php \
        resources/views/admin/panel.blade.php
git commit -m "feat: modal multi-select unir mesas en panel admin"
```

---

### Task 4: Mesero UI — botón + modal

**Files:**
- Modify: `resources/views/mesero/index.blade.php`

- [ ] **Step 1: Agregar botón "Unir mesas" en la mesa card**

En `resources/views/mesero/index.blade.php`, localizar el bloque de acciones de mesa libre (aprox. líneas 503–511):

```php
                                    @else
                                        @if (! $esSecundaria)
                                            <button class="btn btn-primary btn-sm"
                                                    onclick="abrirNuevoPedido({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                                                ...
                                                Nuevo pedido
                                            </button>
                                        @endif
                                    @endif
```

Reemplazar por:

```php
                                    @else
                                        @if (! $esSecundaria)
                                            @php
                                                $candidatasMesero = $mesas->filter(fn($m) =>
                                                    $m->id_mesa !== $mesa->id_mesa &&
                                                    ! $m->estaUnida() &&
                                                    $m->pedidos->isEmpty() &&
                                                    $m->mesasUnidas->isEmpty()
                                                );
                                            @endphp
                                            <button class="btn btn-primary btn-sm"
                                                    onclick="abrirNuevoPedido({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                                                <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                                                Nuevo pedido
                                            </button>
                                            @if ($candidatasMesero->isNotEmpty())
                                                <button class="btn btn-outline btn-sm"
                                                        style="margin-top:5px;"
                                                        onclick="abrirModalUnirMesero({{ $mesa->id_mesa }}, '{{ addslashes($nombreDisplay) }}')">
                                                    <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/></svg>
                                                    Unir mesas
                                                </button>
                                            @endif
                                        @endif
                                    @endif
```

- [ ] **Step 2: Agregar HTML del modal mesero**

Localizar `{{-- MODAL NUEVO PEDIDO --}}` (aprox. línea 573). Agregar justo ANTES de esa línea:

```html
{{-- MODAL UNIR MESAS (mesero) --}}
<div id="modal-unir-mesero" style="display:none;position:fixed;inset:0;z-index:800;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
  <div style="background:var(--surface);border-radius:var(--r-xl);padding:24px;width:min(420px,92vw);max-height:80vh;display:flex;flex-direction:column;box-shadow:var(--shadow-lg);border:1px solid var(--border);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
      <h3 id="mu-titulo" style="font-size:15px;font-weight:700;color:var(--text);margin:0;"></h3>
      <button onclick="cerrarModalUnirMesero()" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:22px;line-height:1;padding:0 4px;">×</button>
    </div>
    <div id="mu-lista" style="overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px;min-height:40px;"></div>
    <div style="display:flex;gap:8px;justify-content:flex-end;">
      <button onclick="cerrarModalUnirMesero()" class="btn-outline" style="padding:8px 14px;border-radius:var(--r-md);font-size:13px;font-weight:600;border:1.5px solid var(--border);background:none;color:var(--text-muted);cursor:pointer;">Cancelar</button>
      <button id="mu-confirmar" onclick="confirmarUnirMesero()" style="padding:8px 14px;border-radius:var(--r-md);font-size:13px;font-weight:600;background:var(--purple);color:#fff;border:none;cursor:pointer;" disabled>Confirmar unión (0)</button>
    </div>
  </div>
</div>
```

- [ ] **Step 3: Agregar JS del modal mesero**

Al final del bloque `<script>` existente (después de la función `showToast`, antes del cierre `</script>`), agregar:

```javascript
// ── Unir mesas multi-select (mesero) ──────────────────────────────────
var _muBaseMesaId = null;

const todasLasMesasDataMesero = @json(
    $mesas
        ->filter(fn($m) => ! $m->estaUnida() && $m->pedidos->isEmpty() && $m->mesasUnidas->isEmpty())
        ->values()
        ->map(fn($m) => [
            'id'     => $m->id_mesa,
            'nombre' => $m->nombre_display,
            'piso'   => optional($m->piso)->nombre ?? 'Sin piso',
        ])
);

function abrirModalUnirMesero(mesaId, mesaNombre) {
    _muBaseMesaId = mesaId;
    document.getElementById('mu-titulo').textContent = 'Unir mesas a ' + mesaNombre;

    const candidatas = todasLasMesasDataMesero.filter(function(m) { return m.id !== mesaId; });
    const lista      = document.getElementById('mu-lista');
    lista.innerHTML  = '';

    if (candidatas.length === 0) {
        lista.innerHTML = '<p style="color:var(--text-muted);font-size:13px;">No hay mesas libres disponibles.</p>';
    } else {
        const porPiso = {};
        candidatas.forEach(function(m) {
            if (!porPiso[m.piso]) porPiso[m.piso] = [];
            porPiso[m.piso].push(m);
        });
        Object.keys(porPiso).forEach(function(piso) {
            const lbl = document.createElement('p');
            lbl.style.cssText = 'font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-faint);margin:8px 0 4px;';
            lbl.textContent   = piso;
            lista.appendChild(lbl);

            const grid = document.createElement('div');
            grid.style.cssText = 'display:flex;flex-wrap:wrap;gap:6px;';
            porPiso[piso].forEach(function(m) {
                const chip = document.createElement('label');
                chip.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1.5px solid var(--border);border-radius:var(--r-md);cursor:pointer;font-size:12px;font-weight:500;color:var(--text);background:var(--surface2);transition:border-color 0.15s,background 0.15s;';
                const cb = document.createElement('input');
                cb.type  = 'checkbox';
                cb.value = m.id;
                cb.style.accentColor = 'var(--purple)';
                cb.addEventListener('change', function() {
                    chip.style.borderColor = cb.checked ? 'var(--purple)' : 'var(--border)';
                    chip.style.background  = cb.checked ? 'var(--purple-dim)' : 'var(--surface2)';
                    chip.style.color       = cb.checked ? 'var(--purple)' : 'var(--text)';
                    actualizarContadorMesero();
                });
                chip.appendChild(cb);
                chip.appendChild(document.createTextNode(' ' + m.nombre));
                grid.appendChild(chip);
            });
            lista.appendChild(grid);
        });
    }

    actualizarContadorMesero();
    document.getElementById('modal-unir-mesero').style.display = 'flex';
}

function cerrarModalUnirMesero() {
    document.getElementById('modal-unir-mesero').style.display = 'none';
    _muBaseMesaId = null;
}

function actualizarContadorMesero() {
    const n   = document.querySelectorAll('#mu-lista input[type=checkbox]:checked').length;
    const btn = document.getElementById('mu-confirmar');
    btn.textContent = 'Confirmar unión (' + n + ')';
    btn.disabled    = n === 0;
}

async function confirmarUnirMesero() {
    const ids = Array.from(document.querySelectorAll('#mu-lista input[type=checkbox]:checked'))
        .map(function(cb) { return cb.value; });
    if (!ids.length || !_muBaseMesaId) return;

    const fd = new FormData();
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    ids.forEach(function(id) { fd.append('id_mesas[]', id); });

    const btn = document.getElementById('mu-confirmar');
    btn.disabled = true;

    try {
        const res  = await fetch('/mesero/mesas/' + _muBaseMesaId + '/unir-grupo', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        });
        const data = await res.json();
        showToast(data.message, data.success !== false);
        if (data.success !== false) {
            cerrarModalUnirMesero();
            setTimeout(function() { location.reload(); }, 800);
        } else {
            btn.disabled = false;
        }
    } catch {
        showToast('❌ Error de conexión', false);
        btn.disabled = false;
    }
}

document.getElementById('modal-unir-mesero').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalUnirMesero();
});
```

- [ ] **Step 4: Verificación manual**

1. Hacer login como mesero → ver vista de mesas
2. Con al menos dos mesas libres: verificar que aparece botón "Unir mesas" bajo "Nuevo pedido"
3. Pulsar "Unir mesas" en Mesa 1 → modal abre con título correcto
4. Seleccionar Mesa 2 y Mesa 3 → botón dice "Confirmar unión (2)"
5. Confirmar → toast de éxito → página recarga → mesas muestran badges de grupo
6. Verificar que la mesa secundaria no muestra el botón "Unir mesas"
7. Verificar que el botón "Nuevo pedido" sigue funcionando correctamente

- [ ] **Step 5: Commit**

```bash
git add resources/views/mesero/index.blade.php
git commit -m "feat: modal multi-select unir mesas en vista mesero"
```
