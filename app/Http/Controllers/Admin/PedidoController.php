<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_mesa'     => ['required', 'integer', 'exists:mesas,id_mesa'],
            'productos'   => ['required', 'array', 'min:1'],
            'productos.*' => ['integer'],
            'cantidades'  => ['required', 'array'],
        ]);

        $negocio = auth()->user()->negocioActivo();
        $mesa    = Mesa::findOrFail($request->id_mesa);

        abort_unless($mesa->id_negocio === $negocio->id_negocio, 403);

        if ($mesa->estaOcupada()) {
            $activo = $mesa->pedidoActivo()->first();
            return back()->with('message', "❌ Esta mesa ya tiene un pedido activo (Pedido #{$activo->id_pedido}).");
        }

        $pedido = Pedido::create([
            'id_negocio' => $negocio->id_negocio,
            'id_mesa'    => $mesa->id_mesa,
            'codigo_qr'  => 'PED-' . strtoupper(Str::random(6)),
            'estado'     => 'Pendiente',
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

        $url = route('factura.show', $pedido->id_pedido);
        return back()->with('message', "✅ Pedido #{$pedido->id_pedido} creado. <a href='{$url}'>Ver factura →</a>");
    }
}
