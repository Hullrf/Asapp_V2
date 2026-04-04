<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Negocio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminController extends Controller
{
    public function showLogin()
    {
        if (session('superadmin_auth')) {
            return redirect()->route('superadmin.panel');
        }

        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $emailOk    = $request->email === config('app.superadmin_email');
        $passwordOk = Hash::check($request->password, bcrypt(config('app.superadmin_password')))
                      || $request->password === config('app.superadmin_password');

        if (!$emailOk || !$passwordOk) {
            return back()->withErrors(['credenciales' => 'Credenciales incorrectas.']);
        }

        session(['superadmin_auth' => true]);

        return redirect()->route('superadmin.panel');
    }

    public function logout()
    {
        session()->forget('superadmin_auth');
        return redirect()->route('superadmin.login');
    }

    public function panel()
    {
        $negocios = Negocio::withCount(['productos', 'mesas', 'pedidos'])
            ->with(['usuarios' => fn($q) => $q->where('rol', 'admin')->limit(1)])
            ->get();

        return view('superadmin.panel', compact('negocios'));
    }

    public function update(Request $request, Negocio $negocio)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'direccion' => 'nullable|string|max:150',
            'telefono'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
        ]);

        $negocio->update($request->only('nombre', 'direccion', 'telefono', 'email'));

        return response()->json(['success' => true, 'message' => 'Negocio actualizado.']);
    }

    public function destroy(Negocio $negocio)
    {
        $negocio->delete();

        return response()->json(['success' => true, 'message' => 'Negocio eliminado.']);
    }

    public function toggleSuspendido(Negocio $negocio)
    {
        $negocio->update(['suspendido' => !$negocio->suspendido]);

        $mensaje = $negocio->suspendido ? 'Negocio suspendido.' : 'Negocio reactivado.';

        return response()->json(['success' => true, 'message' => $mensaje, 'suspendido' => $negocio->suspendido]);
    }
}
