{{-- Crear mesero --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg></div>
            Agregar mesero
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('panel.meseros.store') }}" method="POST"
              data-ajax data-refresh="meseros">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:flex-end;">
                <div class="form-group">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" placeholder="Nombre completo" required maxlength="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>
                <button type="submit" class="btn btn-primary" style="align-self:flex-end;">
                    <svg viewBox="0 0 20 20" fill="currentColor" width="14" height="14"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    Agregar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Lista de meseros --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <div class="card-icon"><svg viewBox="0 0 20 20" fill="#6B21E8" width="14" height="14"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg></div>
            Equipo activo
        </div>
        @if ($meseros->isNotEmpty())
            <span class="card-count">{{ $meseros->count() }} mesero{{ $meseros->count() !== 1 ? 's' : '' }}</span>
        @endif
    </div>
    <div class="card-body">
        @if ($meseros->isEmpty())
            <div style="text-align:center;padding:32px;color:var(--text-faint);">
                <div style="width:48px;height:48px;border-radius:50%;background:var(--purple-dim);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg viewBox="0 0 20 20" fill="#6B21E8" width="22" height="22"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                </div>
                <div style="font-size:13px;">No hay meseros registrados.<br>Crea uno desde el formulario de arriba.</div>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;">
                @foreach ($meseros as $mesero)
                    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--r-lg);padding:16px;display:flex;flex-direction:column;align-items:center;gap:10px;text-align:center;">
                        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--purple),var(--purple-dk));display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;">
                            {{ strtoupper(substr($mesero->nombre, 0, 2)) }}
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:700;color:var(--text);">{{ $mesero->nombre }}</div>
                            <div style="font-size:11px;color:var(--text-faint);word-break:break-all;">{{ $mesero->email }}</div>
                        </div>
                        <form action="{{ route('panel.meseros.destroy', $mesero->id_usuario) }}"
                              method="POST"
                              data-ajax data-refresh="meseros" style="width:100%;"
                              onsubmit="return confirm('¿Eliminar al mesero {{ addslashes($mesero->nombre) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs" style="width:100%;justify-content:center;">
                                <svg viewBox="0 0 20 20" fill="currentColor" width="12" height="12"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"/></svg>
                                Eliminar
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
