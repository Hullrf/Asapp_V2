<?php

namespace App\Http\Controllers;

use App\Enums\EstadoPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function show(Pedido $pedido)
    {
        $pedido->load(['negocio', 'mesa', 'items.producto']);
        $es_admin        = auth()->check() && auth()->user()->esAdmin();
        $pedidoPagado    = $pedido->estaPagado();
        $pedidoBloqueado = $es_admin && $pedido->estado === EstadoPedido::Pagado;
        $productos       = $es_admin
            ? $pedido->negocio->productos()->orderBy('nombre')->get()
            : collect();

        return view('factura.show', compact('pedido', 'es_admin', 'pedidoPagado', 'pedidoBloqueado', 'productos'));
    }

    public function addItem(Pedido $pedido, Request $request)
    {
        $request->validate([
            'id_producto' => ['required', 'integer'],
            'cantidad'    => ['required', 'integer', 'min:1'],
        ]);

        $producto = $pedido->negocio->productos()->findOrFail($request->id_producto);

        $pedido->items()->create([
            'id_producto'     => $producto->id_producto,
            'cantidad'        => $request->cantidad,
            'precio_unitario' => $producto->precio,
            'subtotal'        => $producto->precio * $request->cantidad,
            'estado'          => 'Pendiente',
        ]);

        return redirect()->route('factura.show', $pedido->id_pedido);
    }

    public function updateItem(Pedido $pedido, ItemPedido $item, Request $request)
    {
        $request->validate(['nueva_cantidad' => ['required', 'integer', 'min:1']]);

        $item->update([
            'cantidad' => $request->nueva_cantidad,
            'subtotal' => $item->precio_unitario * $request->nueva_cantidad,
        ]);

        return redirect()->route('factura.show', $pedido->id_pedido);
    }

    public function deleteItem(Pedido $pedido, ItemPedido $item)
    {
        $item->delete();
        return redirect()->route('factura.show', $pedido->id_pedido);
    }

    public function reabrir(Pedido $pedido)
    {
        if ($pedido->estado === EstadoPedido::Pagado) {
            $pedido->update(['estado' => EstadoPedido::Parcial]);
        }
        return redirect()->route('factura.show', $pedido->id_pedido);
    }
}
