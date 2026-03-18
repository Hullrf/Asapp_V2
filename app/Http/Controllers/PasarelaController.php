<?php

namespace App\Http\Controllers;

use App\Models\ItemPedido;
use App\Models\Pago;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PasarelaController extends Controller
{
    public function show(Pedido $pedido, Request $request)
    {
        $ids   = array_map('intval', (array) $request->input('items', []));
        $items = ItemPedido::whereIn('id_item', $ids)
                    ->where('id_pedido', $pedido->id_pedido)
                    ->with('producto')
                    ->get();
        $total = $items->sum('subtotal');

        return view('pasarela.show', compact('pedido', 'items', 'total'));
    }

    public function confirmar(Pedido $pedido, Request $request)
    {
        $ids   = array_map('intval', (array) $request->input('items_confirmados', []));
        $monto = 0;

        DB::transaction(function () use ($ids, $pedido, &$monto) {
            foreach ($ids as $id_item) {
                // lockForUpdate: si otro request ya está procesando este ítem,
                // espera hasta que termine. Solo procesa ítems aún Pendientes.
                $item = ItemPedido::where('id_item', $id_item)
                            ->where('id_pedido', $pedido->id_pedido)
                            ->where('estado', 'Pendiente')
                            ->lockForUpdate()
                            ->first();

                if (!$item) continue; // ya fue pagado por otro usuario, se omite

                $item->update(['estado' => 'Pagado']);
                $monto += $item->subtotal;
            }

            if ($monto > 0) {
                Pago::create([
                    'id_pedido'   => $pedido->id_pedido,
                    'monto'       => $monto,
                    'metodo_pago' => 'digital',
                    'estado'      => 'simulado',
                ]);
            }

            $pedido->refresh();
            $pedido->update(['estado' => $pedido->estaPagado() ? 'Pagado' : 'Parcial']);
        });

        return redirect()->route('pago.exitoso', ['pedido' => $pedido->id_pedido, 'monto' => $monto]);
    }

    public function exitoso(Pedido $pedido, Request $request)
    {
        $pedido->load(['negocio', 'mesa']);
        $monto          = $request->input('monto', 0);
        $pedidoCompleto = $pedido->estaPagado();

        return view('pasarela.exitoso', compact('pedido', 'monto', 'pedidoCompleto'));
    }
}
