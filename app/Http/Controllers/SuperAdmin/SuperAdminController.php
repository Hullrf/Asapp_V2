<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ItemPedido;
use App\Models\Negocio;
use App\Models\Pago;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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
            ->with(['administradores' => fn($q) => $q->where('rol', 'admin')->limit(1)])
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
        DB::transaction(function () use ($negocio) {
            $pedidoIds = $negocio->pedidos()->pluck('id_pedido');

            // 1. Pagos (FK a pedidos sin cascade)
            Pago::whereIn('id_pedido', $pedidoIds)->delete();

            // 2. Items (cascade elimina item_divisiones y division_partes)
            ItemPedido::whereIn('id_pedido', $pedidoIds)->delete();

            // 3. Pedidos (FK a negocio sin cascade)
            $negocio->pedidos()->delete();

            // 4. Productos (FK a negocio sin cascade; items ya eliminados)
            $negocio->productos()->delete();

            // 5. Limpiar auto-referencia de mesas antes del cascade
            $negocio->mesas()->update(['mesa_principal_id' => null]);

            // 6. Eliminar negocio — cascade: mesas, categorias, pisos, user_negocio
            $negocio->delete();
        });

        return response()->json(['success' => true, 'message' => 'Negocio y todos sus datos eliminados.']);
    }

    public function negocioStats(Negocio $negocio)
    {
        $pedidos = $negocio->pedidos()->with('items.producto', 'mesa')->get();

        $resumen = [
            'total_pedidos'     => $pedidos->count(),
            'total_cobrado'     => (float) $pedidos->flatMap->items->filter(fn($i) => $i->estado->value === 'Pagado')->sum('subtotal'),
            'productos_activos' => $negocio->productos()->where('disponible', true)->count(),
            'mesas_total'       => $negocio->mesas()->count(),
        ];

        $pedidosPorEstado = $pedidos
            ->groupBy(fn($p) => $p->estado->value)
            ->map->count();

        $topProductos = $pedidos
            ->flatMap(fn($p) => $p->items)
            ->groupBy('id_producto')
            ->map(fn($items) => [
                'nombre'   => $items->first()->producto?->nombre ?? 'Eliminado',
                'cantidad' => (int) $items->sum('cantidad'),
            ])
            ->sortByDesc('cantidad')
            ->take(5)
            ->values();

        $pedidoIds = $pedidos->pluck('id_pedido');
        $fuentesPago = Pago::whereIn('id_pedido', $pedidoIds)
            ->where('estado', '!=', 'fallido')
            ->selectRaw('metodo_pago, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('metodo_pago')
            ->get()
            ->mapWithKeys(fn($p) => [$p->metodo_pago => ['total' => (float)$p->total, 'cantidad' => (int)$p->cantidad]]);

        // Ingresos por mes — últimos 6 meses
        $ingresosPorMes = Pago::whereIn('id_pedido', $pedidoIds)
            ->where('estado', '!=', 'fallido')
            ->where('fecha', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(fecha, '%Y-%m') as mes, SUM(monto) as total")
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->mapWithKeys(fn($p) => [$p->mes => (float)$p->total]);

        // Rellenar meses sin datos con 0
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $meses[$key] = $ingresosPorMes[$key] ?? 0;
        }

        return response()->json([
            'negocio'          => ['nombre' => $negocio->nombre, 'direccion' => $negocio->direccion, 'fecha_registro' => $negocio->fecha_registro],
            'resumen'          => $resumen,
            'pedidos_por_estado' => $pedidosPorEstado,
            'top_productos'    => $topProductos,
            'fuentes_pago'     => $fuentesPago,
            'ingresos_por_mes' => $meses,
        ]);
    }

    public function toggleSuspendido(Request $request, Negocio $negocio)
    {
        // Si va a suspender y no confirmó la advertencia, devuelve conteo de pedidos activos
        if (!$negocio->suspendido) {
            $pedidosActivos = $negocio->pedidos()
                ->whereIn('estado', ['Pendiente', 'Parcial'])
                ->count();

            if ($pedidosActivos > 0 && !$request->boolean('confirmar')) {
                return response()->json([
                    'success'        => false,
                    'advertencia'    => true,
                    'pedidos_activos' => $pedidosActivos,
                ]);
            }
        }

        $negocio->update(['suspendido' => !$negocio->suspendido]);

        $mensaje = $negocio->suspendido ? 'Negocio suspendido.' : 'Negocio reactivado.';

        return response()->json(['success' => true, 'message' => $mensaje, 'suspendido' => $negocio->suspendido]);
    }
}
