@php
    $mesasPorPiso = $mesas->groupBy('id_piso');
    $mesasSinPiso = $mesasPorPiso->get(null, collect());
@endphp

{{-- ── CREAR PISO ── --}}
<div class="card">
    <div class="card-title">🏢 Crear nuevo piso</div>
    <form action="{{ route('panel.pisos.store') }}" method="POST"
          data-ajax data-refresh="mesas">
        @csrf
        <div class="form-row">
            <div class="form-group" style="max-width:280px">
                <label>Nombre del piso</label>
                <input type="text" name="nombre_piso"
                       placeholder="Ej: Piso 1, Terraza, Salón VIP" required>
            </div>
            <div class="form-group" style="max-width:140px; justify-content:flex-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Crear piso</button>
            </div>
        </div>
    </form>
</div>

{{-- ── CREAR MESA (solo si hay pisos) ── --}}
@if ($pisos->isNotEmpty())
<div class="card">
    <div class="card-title">➕ Crear nueva mesa</div>
    <form action="{{ route('panel.mesas.store') }}" method="POST"
          data-ajax data-refresh="mesas,estadisticas">
        @csrf
        <div class="form-row">
            <div class="form-group" style="max-width:240px">
                <label>Piso</label>
                <select name="id_piso" required>
                    <option value="">— Selecciona un piso —</option>
                    @foreach ($pisos as $piso)
                        <option value="{{ $piso->id_piso }}">{{ $piso->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="max-width:140px; justify-content:flex-end">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">+ Agregar mesa</button>
            </div>
        </div>
        <p style="font-size:11px; color:#9B8EC4; margin-top:-4px;">
            La mesa se creará automáticamente como "Mesa N" en el piso seleccionado.
        </p>
    </form>
</div>
@endif

{{-- ── SIN PISOS ── --}}
@if ($pisos->isEmpty())
    <div class="card">
        <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
            Crea tu primer piso para comenzar a agregar mesas.
        </p>
    </div>

{{-- ── MESAS AGRUPADAS POR PISO ── --}}
@else
    @foreach ($pisos as $piso)
        @php $mesasDePiso = $mesasPorPiso->get($piso->id_piso, collect()); @endphp
        <div class="card">

            {{-- Header del piso --}}
            <div style="display:flex; align-items:center; justify-content:space-between;
                        margin-bottom:16px; flex-wrap:wrap; gap:10px;">

                {{-- Renombrar piso --}}
                <form action="{{ route('panel.pisos.update', $piso) }}" method="POST"
                      data-ajax data-refresh="mesas"
                      style="display:flex; gap:8px; align-items:center;
                             flex:1; min-width:180px; max-width:360px;">
                    @csrf
                    @method('PUT')
                    <span style="font-size:18px; flex-shrink:0;">🏢</span>
                    <input type="text" name="nuevo_nombre" value="{{ $piso->nombre }}"
                           style="font-weight:700; font-size:15px; flex:1; min-width:0;
                                  padding:5px 9px; border-radius:8px;" required>
                    <button type="submit" class="btn btn-warning btn-sm"
                            style="flex-shrink:0;" title="Renombrar piso">✏️</button>
                </form>

                {{-- Eliminar piso --}}
                <form action="{{ route('panel.pisos.destroy', $piso) }}" method="POST"
                      data-ajax data-refresh="mesas,estadisticas"
                      onsubmit="return confirm('¿Eliminar {{ addslashes($piso->nombre) }}? Solo puedes eliminar un piso sin mesas.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Eliminar piso</button>
                </form>
            </div>

            @if ($mesasDePiso->isEmpty())
                <p style="color:#9B8EC4; font-size:13px; text-align:center;
                          padding:16px 0; border-top:1px solid var(--border);">
                    Sin mesas en este piso. Crea una desde el formulario de arriba.
                </p>
            @else
                <div class="mesas-grid">
                    @foreach ($mesasDePiso as $mesa)
                        @include('admin.partials._mesa-card', ['mesa' => $mesa, 'mesas' => $mesas])
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach

    {{-- Mesas sin piso asignado (datos previos a la migración) --}}
    @if ($mesasSinPiso->isNotEmpty())
        <div class="card" style="border-color:#FECACA;">
            <div class="card-title" style="color:#DC2626;">
                ⚠️ Sin piso asignado
                <span style="font-size:12px; font-weight:400; color:#9B8EC4; margin-left:8px;">
                    Estas mesas no pertenecen a ningún piso.
                </span>
            </div>
            <div class="mesas-grid">
                @foreach ($mesasSinPiso as $mesa)
                    @include('admin.partials._mesa-card', ['mesa' => $mesa, 'mesas' => $mesas])
                @endforeach
            </div>
        </div>
    @endif
@endif
