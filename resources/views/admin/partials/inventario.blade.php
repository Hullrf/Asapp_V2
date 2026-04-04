{{-- Badge oculto para actualizar el contador del tab sin recargar --}}
<span id="stock-bajo-count" data-count="{{ $productosStockBajo->count() }}" style="display:none;"></span>

{{-- ── ALERTAS DE STOCK BAJO ── --}}
@if ($productosStockBajo->isNotEmpty())
<div class="stock-alert-box">
    <strong>⚠️ Stock bajo en {{ $productosStockBajo->count() }} producto{{ $productosStockBajo->count() !== 1 ? 's' : '' }}:</strong>
    <div class="stock-alert-chips">
        @foreach ($productosStockBajo as $p)
            <span class="stock-chip-alerta">
                {{ $p->nombre }} — {{ $p->stock === 0 ? 'Agotado' : $p->stock . ' ud.' }}
            </span>
        @endforeach
    </div>
</div>
@endif

{{-- ── CATEGORÍAS ── --}}
<div class="inv-top-grid">

    {{-- ── CATEGORÍAS ── --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-title">🏷️ Categorías</div>
        <form action="{{ route('panel.categorias.store') }}" method="POST"
              data-ajax data-refresh="inventario"
              style="display:flex; gap:8px; margin-bottom: {{ $categorias->isNotEmpty() ? '16px' : '0' }}">
            @csrf
            <input type="text" name="nombre" placeholder="Nueva categoría…" style="flex:1;" required>
            <button type="submit" class="btn btn-primary btn-sm">+ Agregar</button>
        </form>

        @if ($categorias->isNotEmpty())
            <div class="categorias-lista">
                @foreach ($categorias as $cat)
                    <div class="categoria-item">
                        {{-- Fila superior: nombre + eliminar --}}
                        <div class="cat-item-header">
                            <span class="cat-chip">{{ $cat->nombre }}</span>
                            <form action="{{ route('panel.categorias.destroy', $cat) }}" method="POST"
                                  data-ajax data-refresh="inventario"
                                  onsubmit="return confirm('¿Eliminar «{{ addslashes($cat->nombre) }}»? Los productos quedarán sin categoría.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑</button>
                            </form>
                        </div>
                        {{-- Fila inferior: renombrar --}}
                        <form action="{{ route('panel.categorias.update', $cat) }}" method="POST"
                              data-ajax data-refresh="inventario"
                              class="cat-item-rename">
                            @csrf
                            @method('PUT')
                            <input type="text" name="nombre" value="{{ $cat->nombre }}" required>
                            <button type="submit" class="btn btn-warning btn-sm">✏️ Guardar</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color:#9B8EC4; font-size:13px; text-align:center; padding:20px 0;">
                Sin categorías aún.
            </p>
        @endif
    </div>

    {{-- ── AGREGAR PRODUCTO ── --}}
    <div class="card" style="margin-bottom:0;">
        <div class="card-title">➕ Agregar nuevo producto</div>
        <form action="{{ route('panel.productos.store') }}" method="POST"
              data-ajax data-refresh="inventario,estadisticas">
            @csrf
            <div class="inv-form-grid">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" placeholder="Ej: Pizza Margarita" required>
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" placeholder="Descripción breve" required>
                </div>
                <div class="form-group">
                    <label>Precio ($)</label>
                    <input type="number" name="precio" step="100" min="0" placeholder="35000" required>
                </div>
                <div class="form-group">
                    <label>Categoría</label>
                    <select name="id_categoria">
                        <option value="">— Sin categoría —</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id_categoria }}">{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Stock actual</label>
                    <input type="number" name="stock" min="0" placeholder="Vacío = no rastrear">
                </div>
                <div class="form-group">
                    <label>Alerta cuando queden</label>
                    <input type="number" name="stock_minimo" min="1" placeholder="5" value="5">
                </div>
                <div class="form-group inv-footer">
                    <div class="checkbox-row">
                        <input type="checkbox" name="disponible" id="chk-disponible" checked>
                        <label for="chk-disponible">Disponible</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar producto</button>
                </div>
            </div>
        </form>
    </div>

</div>{{-- /inv-top-grid --}}

{{-- ── TABLA DE PRODUCTOS ── --}}
<div class="card">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px; flex-wrap:wrap;">
        <div class="card-title" style="margin-bottom:0;">📋 Productos del negocio</div>
        @if ($productos->isNotEmpty())
            <input type="text" id="buscador-productos" placeholder="🔍 Buscar por nombre, categoría…"
                   oninput="filtrarProductos(this.value)"
                   style="max-width:260px; font-size:13px; padding:7px 12px;">
        @endif
    </div>

    @if ($productos->isEmpty())
        <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
            Aún no has agregado productos. Usa el formulario de arriba.
        </p>
    @else
        <div style="overflow-x:auto; max-height:420px; overflow-y:auto;">
            <table id="tabla-productos">
                <thead>
                    <tr>
                        <th data-col="0" data-tipo="num" class="th-sort"># <span class="sort-icon">↕</span></th>
                        <th data-col="1" data-tipo="txt" class="th-sort">Nombre <span class="sort-icon">↕</span></th>
                        <th>Descripción</th>
                        <th data-col="3" data-tipo="txt" class="th-sort">Categoría <span class="sort-icon">↕</span></th>
                        <th data-col="4" data-tipo="num" class="th-sort right">Precio <span class="sort-icon">↕</span></th>
                        <th data-col="5" data-tipo="num" class="th-sort center">Stock <span class="sort-icon">↕</span></th>
                        <th data-col="6" data-tipo="txt" class="th-sort center">Disp. <span class="sort-icon">↕</span></th>
                        <th class="center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $i => $producto)
                    @php $busqueda = strtolower($producto->nombre . ' ' . $producto->descripcion . ' ' . ($producto->categoria?->nombre ?? '')); @endphp
                        @php
                            $sinStock  = $producto->stock === null;
                            $agotado   = !$sinStock && $producto->stock === 0;
                            $stockBajo = !$sinStock && !$agotado && $producto->stock <= $producto->stock_minimo;
                        @endphp
                        <tr data-busqueda="{{ $busqueda }}">
                            <td style="color:#9B8EC4;" data-val="{{ $i + 1 }}">{{ $i + 1 }}</td>
                            <td style="font-weight:600;" data-val="{{ strtolower($producto->nombre) }}">{{ $producto->nombre }}</td>
                            <td style="color:#9B8EC4; font-size:12px;">{{ $producto->descripcion }}</td>
                            <td data-val="{{ strtolower($producto->categoria?->nombre ?? '') }}">
                                @if ($producto->categoria)
                                    <span class="cat-chip">{{ $producto->categoria->nombre }}</span>
                                @else
                                    <span style="color:#D4C9F0;">—</span>
                                @endif
                            </td>
                            <td class="right" data-val="{{ $producto->precio }}">${{ number_format($producto->precio, 0, ',', '.') }}</td>
                            <td class="center" data-val="{{ $producto->stock ?? -1 }}">
                                @if ($sinStock)
                                    <span style="color:#D4C9F0;">—</span>
                                @elseif ($agotado)
                                    <span class="stock-badge stock-agotado">Agotado</span>
                                @elseif ($stockBajo)
                                    <span class="stock-badge stock-bajo">⚠ {{ $producto->stock }}</span>
                                @else
                                    <span class="stock-badge stock-ok">{{ $producto->stock }}</span>
                                @endif
                            </td>
                            <td class="center" data-val="{{ $producto->disponible ? '1' : '0' }}">
                                <span class="{{ $producto->disponible ? 'badge-disponible' : 'badge-no' }}">
                                    {{ $producto->disponible ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="center">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button class="btn btn-warning btn-sm"
                                            onclick="abrirModalProducto(
                                                {{ $producto->id_producto }},
                                                '{{ addslashes($producto->nombre) }}',
                                                '{{ addslashes($producto->descripcion) }}',
                                                {{ $producto->precio }},
                                                {{ $producto->id_categoria ?? 'null' }},
                                                {{ $producto->stock ?? 'null' }},
                                                {{ $producto->stock_minimo }},
                                                {{ $producto->disponible ? 'true' : 'false' }}
                                            )">
                                        ✏️ Editar
                                    </button>

                                    <form action="{{ route('panel.productos.destroy', $producto) }}" method="POST"
                                          data-ajax data-refresh="inventario,estadisticas"
                                          onsubmit="return confirm('¿Eliminar «{{ addslashes($producto->nombre) }}»?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑 Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="sin-resultados" style="display:none;">
                        <td colspan="8" style="text-align:center; color:#9B8EC4; padding:20px; font-size:13px;">
                            Sin productos que coincidan con la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ── MODAL EDITAR PRODUCTO ── --}}
<div class="inv-modal-overlay" id="modal-producto" onclick="if(event.target===this) cerrarModalProducto()">
    <div class="inv-modal">
        <div class="inv-modal-header">
            <span class="inv-modal-titulo">✏️ Editar producto</span>
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
.th-sort { cursor: pointer; user-select: none; white-space: nowrap; }
.th-sort:hover { background: rgba(255,255,255,0.12); }
.sort-icon { font-size: 11px; color: #C4B5FD; margin-left: 3px; }
.th-sort.asc  .sort-icon { color: #6B21E8; }
.th-sort.desc .sort-icon { color: #6B21E8; }
.th-sort.asc  .sort-icon::before { content: '▲'; }
.th-sort.desc .sort-icon::before { content: '▼'; }
.th-sort:not(.asc):not(.desc) .sort-icon::before { content: '↕'; }
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

function filtrarProductos(q) {
    const term = q.toLowerCase().trim();
    const filas = document.querySelectorAll('#tabla-productos tbody tr');
    let visibles = 0;
    filas.forEach(fila => {
        const texto = fila.dataset.busqueda || '';
        const match = !term || texto.includes(term);
        fila.style.display = match ? '' : 'none';
        if (match) visibles++;
    });
    const aviso = document.getElementById('sin-resultados');
    if (aviso) aviso.style.display = visibles === 0 ? '' : 'none';
}

// ── Sorting ────────────────────────────────────────────────────────────
(function () {
    let sortCol = null, sortDir = 1;

    function sortTabla(th) {
        const col  = parseInt(th.dataset.col);
        const tipo = th.dataset.tipo;

        if (sortCol === col) {
            sortDir = -sortDir;
        } else {
            sortCol = col;
            sortDir = 1;
        }

        document.querySelectorAll('#tabla-productos thead .th-sort').forEach(h => {
            h.classList.remove('asc', 'desc');
        });
        th.classList.add(sortDir === 1 ? 'asc' : 'desc');

        const tbody = document.querySelector('#tabla-productos tbody');
        const filas = Array.from(tbody.querySelectorAll('tr'));

        filas.sort((a, b) => {
            const celdaA = a.querySelectorAll('td')[col];
            const celdaB = b.querySelectorAll('td')[col];
            const valA   = celdaA ? (celdaA.dataset.val ?? celdaA.textContent.trim()) : '';
            const valB   = celdaB ? (celdaB.dataset.val ?? celdaB.textContent.trim()) : '';

            if (tipo === 'num') {
                return (parseFloat(valA) - parseFloat(valB)) * sortDir;
            }
            return valA.localeCompare(valB, 'es') * sortDir;
        });

        filas.forEach(f => tbody.appendChild(f));
    }

    document.querySelectorAll('#tabla-productos thead .th-sort').forEach(th => {
        th.addEventListener('click', () => sortTabla(th));
    });
})();
</script>

<style>
    /* Layout superior: categorías + agregar producto en fila */
    .inv-top-grid {
        display: grid;
        grid-template-columns: 3fr 7fr;
        gap: 24px;
        margin-bottom: 24px;
        align-items: stretch;
    }

    .inv-top-grid > .card {
        display: flex;
        flex-direction: column;
    }

    .inv-top-grid > .card form:last-child,
    .inv-top-grid > .card > form { flex: 1; }

    .inv-top-grid > .card { min-width: 0; }

    /* Grid del formulario de agregar/editar */
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

    /* Columnas de la tabla */
    thead th.right, tbody td.right   { text-align: right; }
    thead th.center, tbody td.center { text-align: center; }

    /* Badges */
    .cat-chip {
        display: inline-block;
        background: #EDE9FE;
        color: #5B21B6;
        border: 1px solid #C4B5FD;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 12px;
        font-weight: 600;
    }

    .stock-badge {
        display: inline-block;
        border-radius: 20px;
        padding: 2px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .stock-ok     { background: #EDE9FE; color: #5B21B6; border: 1px solid #C4B5FD; }
    .stock-bajo   { background: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
    .stock-agotado{ background: #FEE2E2; color: #C8102E; border: 1px solid #FECACA; }

    /* Alertas */
    .stock-alert-box {
        background: #FEF3C7;
        border: 1px solid #FCD34D;
        border-radius: 12px;
        padding: 14px 18px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #92400E;
    }

    .stock-alert-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; }

    .stock-chip-alerta {
        background: #FDE68A;
        border: 1px solid #F59E0B;
        border-radius: 20px;
        padding: 3px 10px;
        font-size: 12px;
        color: #78350F;
        font-weight: 600;
    }

    /* Categorías lista */
    .categorias-lista {
        display: flex;
        flex-direction: column;
        max-height: 260px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .categorias-lista::-webkit-scrollbar { width: 4px; }
    .categorias-lista::-webkit-scrollbar-track { background: #F5F3FF; border-radius: 4px; }
    .categorias-lista::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 4px; }

    .categoria-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 10px 0;
        border-bottom: 1px solid #EDE9F8;
    }

    .categoria-item:last-child { border-bottom: none; }

    .cat-item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .cat-item-rename {
        display: flex;
        gap: 6px;
    }

    .cat-item-rename input {
        flex: 1;
        min-width: 0;
        font-size: 12px;
        padding: 5px 8px;
    }

    /* Modal */
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
        background: #fff;
        border-radius: 16px;
        padding: 28px;
        width: 100%;
        max-width: 600px;
        box-shadow: 0 24px 64px rgba(107,33,232,0.2);
        border: 1px solid #E0D9F5;
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
        color: #3D0E8A;
    }

    .inv-modal-close {
        background: none;
        border: none;
        font-size: 16px;
        color: #9B8EC4;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background 0.2s;
    }

    .inv-modal-close:hover { background: #F5F3FF; color: #3D0E8A; }

    /* Scrollbar tabla productos */
    #tabla-productos { border-collapse: collapse; width: 100%; }
    div:has(> #tabla-productos)::-webkit-scrollbar { width: 4px; height: 4px; }
    div:has(> #tabla-productos)::-webkit-scrollbar-track { background: #F5F3FF; border-radius: 4px; }
    div:has(> #tabla-productos)::-webkit-scrollbar-thumb { background: #C4B5FD; border-radius: 4px; }

    @media (max-width: 900px) {
        .inv-top-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
        .inv-form-grid { grid-template-columns: 1fr; }
        .form-group[style*="span 2"] { grid-column: span 1 !important; }
    }
</style>
