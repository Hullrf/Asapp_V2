<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    /** Crea una nueva sede y la activa inmediatamente. */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => ['required', 'string', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'telefono'  => ['nullable', 'string', 'max:20'],
        ]);

        $negocio = Negocio::create([
            'nombre'    => $request->nombre,
            'direccion' => $request->direccion,
            'telefono'  => $request->telefono,
            'email'     => auth()->user()->email,
        ]);

        // Asociar con el usuario en el pivot
        auth()->user()->negocios()->attach($negocio->id_negocio);

        // Activar la nueva sede
        session(['sede_activa_id' => $negocio->id_negocio]);

        return redirect()
            ->route('panel.index')
            ->with('message', "✅ Sede '{$negocio->nombre}' creada y activada.");
    }

    /** Cambia la sede activa en sesión. */
    public function activar(Request $request, Negocio $negocio)
    {
        // Verificar que el usuario tiene acceso a esta sede
        abort_unless(
            auth()->user()->negocios()->where('id_negocio', $negocio->id_negocio)->exists(),
            403
        );

        session(['sede_activa_id' => $negocio->id_negocio]);

        return redirect()->route('panel.index');
    }
}
