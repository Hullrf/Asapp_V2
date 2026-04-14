{{-- Badge oculto para badge del sidebar --}}
<span id="stock-bajo-count" data-count="{{ $productosStockBajo->count() }}" style="display:none;"></span>

{{-- KPIs --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px;">
    <div class="card" style="margin-bottom:0;padding:16px 18px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Total productos</div>
        <div style="font-size:24px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $productos->count() }}</div>
        <div style="font-size:10px;color:var(--text-faint);margin-top:3px;">En {{ $categorias->count() }} categorías</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:16px 18px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Disponibles</div>
        <div style="font-size:24px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $productos->where('disponible', true)->count() }}</div>
        <div style="font-size:10px;color:var(--text-faint);margin-top:3px;">Del catálogo activo</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:16px 18px;{{ $productosStockBajo->isNotEmpty() ? 'border-color:var(--warn-border);background:var(--warn-bg);' : '' }}">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:{{ $productosStockBajo->isNotEmpty() ? '#B45309' : 'var(--text-faint)' }};margin-bottom:4px;">Stock bajo</div>
        <div style="font-size:24px;font-weight:800;color:{{ $productosStockBajo->isNotEmpty() ? 'var(--warn-text)' : 'var(--purple-dk)' }};letter-spacing:-0.5px;line-height:1;">{{ $productosStockBajo->count() }}</div>
        <div style="font-size:10px;color:var(--text-faint);margin-top:3px;">Requieren atención</div>
    </div>
    <div class="card" style="margin-bottom:0;padding:16px 18px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--text-faint);margin-bottom:4px;">Categorías</div>
        <div style="font-size:24px;font-weight:800;color:var(--purple-dk);letter-spacing:-0.5px;line-height:1;">{{ $categorias->count() }}</div>
        <div style="font-size:10px;color:var(--text-faint);margin-top:3px;">Activas</div>
    </div>
</div>

{{-- Alerta stock bajo --}}
@if ($productosStockBajo->isNotEmpty())
<div style="background:var(--warn-bg);border:1px solid var(--warn-border);border-radius:var(--r-md);padding:12px 16px;font-size:13px;color:var(--warn-text);display:flex;align-items:flex-start;gap:10px;margin-bottom:16px;">
    <svg viewBox="0 0 20 20" fill="currentColor" width="16" height="16" style="flex-shrink:0;margin-top:1px;"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/></svg>
    <div>
        <strong>Stock bajo en {{ $productosStockBajo->count() }} producto{{ $productosStockBajo->count() !== 1 ? 's' : '' }}:</strong>
        <span style="margin-left:8px;font-size:12px;">
            @foreach ($productosStockBajo as $p)
                {{ $p->nombre }} — {{ $p->stock === 0 ? 'Agotado' : $p->stock . ' ud.' }}{{ !$loop->last ? ' · ' : '' }}
            @endforeach
        </span>
    </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

    {{-- Categorías --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <div class="card-title">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg></div>
                Categorías
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('panel.categorias.store') }}" method="POST"
                  data-ajax data-refresh="inventario"
                  style="display:flex;gap:8px;margin-bottom:14px;">
                @csrf
                <input type="text" name="nombre" placeholder="Nueva categoría…" style="flex:1;" required>
                <button type="submit" class="btn btn-primary btn-sm">+ Agregar</button>
            </form>
            @if ($categorias->isNotEmpty())
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach ($categorias as $cat)
                        <span style="display:inline-flex;align-items:center;gap:6px;background:var(--purple-dim);color:var(--purple);border:1px solid var(--border);border-radius:20px;padding:5px 12px;font-size:12px;font-weight:600;">
                            {{ $cat->nombre }}
                            <form action="{{ route('panel.categorias.destroy', $cat) }}" method="POST"
                                  data-ajax data-refresh="inventario" style="margin:0;"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($cat->nombre) }}»?')">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-faint);font-size:14px;line-height:1;padding:0;font-family:inherit;" onmouseover="this.style.color='var(--danger)'" onmouseout="this.style.color='var(--text-faint)'">×</button>
                            </form>
                        </span>
                    @endforeach
                </div>
            @else
                <p style="color:var(--text-faint);font-size:13px;text-align:center;padding:16px 0;">Sin categorías aún.</p>
            @endif
        </div>
    </div>

    {{-- Agregar producto --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-header">
            <div class="card-title">
                <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg></div>
                Agregar producto
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('panel.productos.store') }}" method="POST"
                  data-ajax data-refresh="inventario,estadisticas">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" placeholder="Pizza Margarita" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Precio ($)</label>
                        <input type="number" name="precio" step="100" min="0" placeholder="35000" required>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:10px;">
                    <label class="form-label">Descripción</label>
                    <input type="text" name="descripcion" placeholder="Descripción breve" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:14px;">
                    <div class="form-group">
                        <label class="form-label">Categoría</label>
                        <select name="id_categoria">
                            <option value="">— Sin categoría —</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stock actual</label>
                        <input type="number" name="stock" min="0" placeholder="Vacío = libre">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alerta en</label>
                        <input type="number" name="stock_minimo" min="1" placeholder="5" value="5">
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <input type="checkbox" name="disponible" value="1" checked id="chk-disp" style="width:18px;height:18px;accent-color:var(--purple);">
                    <label for="chk-disp" style="font-size:13px;color:var(--text-muted);cursor:pointer;">Disponible al crear</label>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    Guardar producto
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Tabla de productos --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/></svg></div>
            Todos los productos
        </div>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $producto)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $producto->nombre }}</div>
                        @if($producto->descripcion)
                            <div style="font-size:11px;color:var(--text-faint);">{{ $producto->descripcion }}</div>
                        @endif
                    </td>
                    <td style="color:var(--text-muted);font-size:12px;">{{ $producto->categoria?->nombre ?? '—' }}</td>
                    <td style="font-weight:700;color:var(--purple-dk);">${{ number_format($producto->precio, 0, ',', '.') }}</td>
                    <td>
                        @if($producto->stock !== null)
                            @if($producto->stock === 0)
                                <span style="color:var(--danger);font-weight:600;font-size:12px;">Agotado</span>
                            @elseif($producto->stock <= $producto->stock_minimo)
                                <span style="color:var(--warn-text);font-weight:600;font-size:12px;">{{ $producto->stock }} ud.</span>
                            @else
                                <span style="color:var(--text-muted);font-size:12px;">{{ $producto->stock }} ud.</span>
                            @endif
                        @else
                            <span style="color:var(--text-faint);font-size:12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($producto->disponible)
                            <span class="badge badge-ok">Disponible</span>
                        @elseif($producto->stock === 0)
                            <span class="badge badge-warn">Sin stock</span>
                        @else
                            <span class="badge badge-off">No disponible</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;justify-content:flex-end;">
                            <button class="btn btn-ghost btn-xs"
                                    onclick="abrirModalProducto(
                                        {{ $producto->id_producto }},
                                        '{{ addslashes($producto->nombre) }}',
                                        '{{ addslashes($producto->descripcion) }}',
                                        {{ $producto->precio }},
                                        {{ $producto->id_categoria ?? 'null' }},
                                        {{ $producto->stock ?? 'null' }},
                                        {{ $producto->stock_minimo }},
                                        {{ $producto->disponible ? 'true' : 'false' }}
                                    )">Editar</button>
                            <form action="{{ route('panel.productos.destroy', $producto) }}" method="POST"
                                  data-ajax data-refresh="inventario,estadisticas" style="margin:0;"
                                  onsubmit="return confirm('¿Eliminar {{ addslashes($producto->nombre) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">
                                    <svg viewBox="0 0 20 20" fill="currentColor" width="12" height="12"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/></svg>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-faint);padding:32px;">Sin productos aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal editar producto --}}
<div class="inv-modal-overlay" id="modal-producto" onclick="if(event.target===this) cerrarModalProducto()">
    <div class="inv-modal">
        <div class="inv-modal-header">
            <span class="inv-modal-titulo">Editar producto</span>
            <button class="inv-modal-close" onclick="cerrarModalProducto()">✕</button>
        </div>

        <form id="form-editar" method="POST"
              data-ajax data-refresh="inventario,estadisticas">
            @csrf
            @method('PUT')
            <div class="inv-form-grid">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="edit-nombre" name="nombre" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Descripción</label>
                    <input type="text" id="edit-descripcion" name="descripcion" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" id="edit-precio" name="precio" step="100" min="0" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select id="edit-categoria" name="id_categoria">
                        <option value="">— Sin categoría —</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock actual</label>
                    <input type="number" id="edit-stock" name="stock" min="0" placeholder="Vacío = no rastrear">
                </div>
                <div class="form-group">
                    <label>Alerta cuando queden</label>
                    <input type="number" id="edit-stock-min" name="stock_minimo" min="1">
                </div>
                <div class="form-group inv-footer">
                    <div class="checkbox-row">
                        <input type="checkbox" id="edit-disponible" name="disponible">
                        <label for="edit-disponible">Disponible</label>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn btn-outline" onclick="cerrarModalProducto()">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Modal editar producto */
.inv-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15, 10, 30, 0.5);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 24px;
}

.inv-modal-overlay.open { display: flex; }

.inv-modal {
    background: var(--card, #fff);
    border-radius: var(--r-lg, 16px);
    padding: 28px;
    width: 100%;
    max-width: 600px;
    box-shadow: var(--shadow-lg, 0 24px 64px rgba(107,33,232,0.2));
    border: 1px solid var(--border, #E0D9F5);
}

.inv-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.inv-modal-titulo {
    font-size: 16px;
    font-weight: 700;
    color: var(--purple-dk);
}

.inv-modal-close {
    background: none;
    border: none;
    font-size: 16px;
    color: var(--text-faint);
    cursor: pointer;
    padding: 4px 8px;
    border-radius: var(--r-sm, 6px);
    transition: background 0.2s;
}

.inv-modal-close:hover { background: var(--surface2); color: var(--purple-dk); }

/* Grid form dentro del modal */
.inv-form-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.inv-footer {
    grid-column: 1 / -1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 4px;
}
</style>

<script>
window.RUTAS_PRODUCTOS = @json($productos->mapWithKeys(fn($p) => [
    $p->id_producto => route('panel.productos.update', $p)
]));

function abrirModalProducto(id, nombre, descripcion, precio, idCategoria, stock, stockMinimo, disponible) {
    document.getElementById('form-editar').action = RUTAS_PRODUCTOS[id];
    document.getElementById('edit-nombre').value       = nombre;
    document.getElementById('edit-descripcion').value  = descripcion;
    document.getElementById('edit-precio').value       = precio;
    document.getElementById('edit-categoria').value    = idCategoria ?? '';
    document.getElementById('edit-stock').value        = stock !== null ? stock : '';
    document.getElementById('edit-stock-min').value    = stockMinimo;
    document.getElementById('edit-disponible').checked = disponible;
    document.getElementById('modal-producto').classList.add('open');
}

function cerrarModalProducto() {
    document.getElementById('modal-producto').classList.remove('open');
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalProducto(); });
</script>
