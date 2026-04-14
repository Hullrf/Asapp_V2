@php
    $mesasPorPiso = $mesas->groupBy('id_piso');
    $mesasSinPiso = $mesasPorPiso->get(null, collect());
@endphp

{{-- Card unificada: crear piso + crear mesa --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg></div>
            Agregar
        </div>
    </div>
    <div class="card-body">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
            <div>
                <div style="font-size:11px;font-weight:700;color:var(--purple);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Nuevo piso</div>
                <form action="{{ route('panel.pisos.store') }}" method="POST"
                      data-ajax data-refresh="mesas" style="display:flex;gap:8px;">
                    @csrf
                    <input type="text" name="nombre_piso" placeholder="Ej: Terraza, Salón VIP…" style="flex:1;" required>
                    <button type="submit" class="btn btn-outline btn-sm">Crear piso</button>
                </form>
            </div>
            @if ($pisos->isNotEmpty())
            <div>
                <div style="font-size:11px;font-weight:700;color:var(--purple);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Nueva mesa</div>
                <form action="{{ route('panel.mesas.store') }}" method="POST"
                      data-ajax data-refresh="mesas,estadisticas" style="display:flex;gap:8px;">
                    @csrf
                    <select name="id_piso" required style="flex:1;">
                        <option value="">— Selecciona piso —</option>
                        @foreach ($pisos as $piso)
                            <option value="{{ $piso->id_piso }}">{{ $piso->nombre }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">+ Mesa</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@if ($pisos->isEmpty())
    <div class="card">
        <div style="text-align:center;padding:32px;color:var(--text-faint);font-size:13px;">
            Crea tu primer piso para comenzar a agregar mesas.
        </div>
    </div>
@else
    @foreach ($pisos as $piso)
        @php $mesasDePiso = $mesasPorPiso->get($piso->id_piso, collect()); @endphp
        <div class="card">

            {{-- Header del piso --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:var(--surface2);border-bottom:1px solid var(--border-soft);flex-wrap:wrap;gap:10px;">
                <div style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:700;color:var(--purple-dk);">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="15" height="15" style="color:var(--purple-dk);"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"/></svg>
                    <form action="{{ route('panel.pisos.update', $piso) }}" method="POST"
                          data-ajax data-refresh="mesas"
                          style="display:flex;gap:6px;align-items:center;">
                        @csrf @method('PUT')
                        <input type="text" name="nuevo_nombre" value="{{ $piso->nombre }}" required
                               style="font-size:13px;font-weight:700;padding:4px 8px;width:160px;color:var(--purple-dk);">
                        <button type="submit" class="btn-icon" title="Guardar nombre">
                            <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                        </button>
                    </form>
                    <span style="font-size:11px;font-weight:600;color:var(--text-faint);background:var(--border-soft);padding:2px 9px;border-radius:20px;">{{ $mesasDePiso->count() }} mesa{{ $mesasDePiso->count() !== 1 ? 's' : '' }}</span>
                </div>
                <div style="display:flex;gap:6px;">
                    <form action="{{ route('panel.pisos.destroy', $piso) }}" method="POST"
                          data-ajax data-refresh="mesas"
                          onsubmit="return confirm('¿Eliminar el piso {{ addslashes($piso->nombre) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs">Eliminar piso</button>
                    </form>
                </div>
            </div>

            @if ($mesasDePiso->isEmpty())
                <p style="color:var(--text-faint);font-size:13px;text-align:center;padding:16px 20px;">
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
        <div class="card" style="border-color:var(--danger-border);">
            <div class="card-header">
                <div class="card-title" style="color:var(--danger);">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/></svg>
                    Sin piso asignado
                    <span style="font-size:12px;font-weight:400;color:var(--text-muted);margin-left:8px;">
                        Estas mesas no pertenecen a ningún piso.
                    </span>
                </div>
            </div>
            <div class="mesas-grid">
                @foreach ($mesasSinPiso as $mesa)
                    @include('admin.partials._mesa-card', ['mesa' => $mesa, 'mesas' => $mesas])
                @endforeach
            </div>
        </div>
    @endif
@endif
