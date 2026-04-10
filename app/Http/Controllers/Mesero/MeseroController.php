<?php

namespace App\Http\Controllers\Mesero;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeseroController extends Controller
{
    public function index()
    {
        $negocio   = auth()->user()->negocio;
        $pisos     = $negocio->pisos()->orderBy('orden')->get();
        $productos = $negocio->productos()->where('disponible', true)->with('categoria')->orderBy('nombre')->get();
        $mesas     = $negocio->mesas()
            ->with([
                'piso',
                'pedidos'      => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1),
                'mesasUnidas',
                'mesaPrincipal',
                'mesaPrincipal.pedidos' => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1),
            ])
            ->orderByRaw('LENGTH(nombre), nombre')
            ->get();

        return view('mesero.index', compact('negocio', 'mesas', 'pisos', 'productos'));
    }

    public function storePedido(Request $request)
    {
        $request->validate([
            'id_mesa'     => ['required', 'integer', 'exists:mesas,id_mesa'],
            'productos'   => ['required', 'array', 'min:1'],
            'productos.*' => ['integer'],
            'cantidades'  => ['required', 'array'],
        ]);

        $negocio = auth()->user()->negocio;
        $mesa    = Mesa::findOrFail($request->id_mesa);

        abort_unless($mesa->id_negocio === $negocio->id_negocio, 403);

        if ($mesa->estaOcupada()) {
            return response()->json(['success' => false, 'message' => 'Esta mesa ya tiene un pedido activo.']);
        }

        $pedido = Pedido::create([
            'id_negocio' => $negocio->id_negocio,
            'id_mesa'    => $mesa->id_mesa,
            'id_mesero'  => auth()->id(),
            'codigo_qr'  => 'PED-' . strtoupper(Str::random(6)),
            'estado'     => 'Pendiente',
            'fecha'      => now(),
        ]);

        foreach ($request->productos as $id_producto) {
            $cantidad = intval($request->cantidades[$id_producto] ?? 1);
            if ($cantidad < 1) continue;

            $producto = Producto::where('id_producto', $id_producto)
                ->where('id_negocio', $negocio->id_negocio)
                ->first();

            if (!$producto) continue;

            $pedido->items()->create([
                'id_producto'     => $producto->id_producto,
                'cantidad'        => $cantidad,
                'precio_unitario' => $producto->precio,
                'subtotal'        => $producto->precio * $cantidad,
                'estado'          => 'Pendiente',
            ]);
        }

        return response()->json([
            'success'     => true,
            'message'     => "Pedido #{$pedido->id_pedido} creado.",
            'factura_url' => route('factura.show', $pedido->id_pedido),
        ]);
    }
}
