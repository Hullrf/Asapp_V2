<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class MeseroAdminController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(6)],
        ]);

        $negocio = auth()->user()->negocioActivo();

        User::create([
            'nombre'     => $request->nombre,
            'email'      => $request->email,
            'password'   => $request->password,
            'rol'        => 'mesero',
            'id_negocio' => $negocio->id_negocio,
        ]);

        return response()->json(['success' => true, 'message' => 'Mesero creado correctamente.']);
    }

    public function destroy(User $user)
    {
        $negocio = auth()->user()->negocioActivo();
        abort_unless($user->id_negocio === $negocio->id_negocio && $user->esMesero(), 403);

        $user->delete();
        return response()->json(['success' => true, 'message' => 'Mesero eliminado.']);
    }
}
