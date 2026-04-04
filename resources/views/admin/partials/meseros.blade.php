{{-- Crear mesero --}}
<div class="card">
    <div class="card-title">👤 Crear mesero</div>
    <form action="{{ route('panel.meseros.store') }}" method="POST"
          data-ajax data-refresh="meseros">
        @csrf
        <div class="form-row">
            <div class="form-group" style="max-width:260px;">
                <label>Nombre</label>
                <input type="text" name="nombre" placeholder="Nombre del mesero" required maxlength="100">
            </div>
            <div class="form-group" style="max-width:280px;">
                <label>Email</label>
                <input type="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>
            <div class="form-group" style="max-width:200px;">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="form-group" style="max-width:140px; justify-content:flex-end;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">+ Agregar</button>
            </div>
        </div>
    </form>
</div>

{{-- Lista de meseros --}}
<div class="card">
    <div class="card-title">🗂️ Meseros del negocio</div>
    @if ($meseros->isEmpty())
        <p style="color:#9B8EC4; font-size:14px; text-align:center; padding:24px 0;">
            No hay meseros registrados. Crea uno desde el formulario de arriba.
        </p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th style="width:100px; text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($meseros as $mesero)
                    <tr>
                        <td style="font-weight:600;">{{ $mesero->nombre }}</td>
                        <td style="color:#6B21E8;">{{ $mesero->email }}</td>
                        <td style="text-align:center;">
                            <form action="{{ route('panel.meseros.destroy', $mesero->id_usuario) }}"
                                  method="POST"
                                  data-ajax data-refresh="meseros"
                                  onsubmit="return confirm('¿Eliminar al mesero {{ addslashes($mesero->nombre) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
