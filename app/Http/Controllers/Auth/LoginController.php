<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if (auth()->check()) {
            return auth()->user()->esAdmin()
                ? redirect()->route('panel.index')
                : redirect()->route('login');
        }

        $negocios = Negocio::orderBy('nombre')->get();
        return view('auth.login', compact('negocios'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => 'Credenciales incorrectas.'])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->esAdmin()) {
            return redirect()->route('panel.index');
        }

        // Cliente: requiere negocio seleccionado
        $id_negocio = $request->integer('id_negocio');
        if (!$id_negocio) {
            Auth::logout();
            return back()->withErrors(['id_negocio' => 'Debes seleccionar un negocio.'])->onlyInput('email');
        }

        session(['id_negocio_cliente' => $id_negocio]);
        return redirect()->route('login'); // placeholder hasta tener vista cliente
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function verificarRol(Request $request)
    {
        $user = User::where('email', $request->input('email'))->first();
        return response($user ? $user->rol->value : 'none');
    }
}
