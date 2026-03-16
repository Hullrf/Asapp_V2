<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'password'       => ['required', Password::min(6)],
            'nombre_negocio' => ['required', 'string', 'max:100'],
            'direccion'      => ['required', 'string', 'max:150'],
            'telefono'       => ['required', 'string', 'max:20'],
            'email_negocio'  => ['required', 'email', 'max:100', 'unique:users,email'],
        ]);

        $negocio = Negocio::create([
            'nombre'    => $request->nombre_negocio,
            'direccion' => $request->direccion,
            'telefono'  => $request->telefono,
            'email'     => $request->email_negocio,
        ]);

        User::create([
            'rol'        => 'admin',
            'nombre'     => $request->nombre_negocio,
            'email'      => $request->email_negocio,
            'password'   => $request->password,
            'id_negocio' => $negocio->id_negocio,
        ]);

        return redirect()->route('login')->with('success', 'Cuenta creada correctamente. Inicia sesión.');
    }
}
