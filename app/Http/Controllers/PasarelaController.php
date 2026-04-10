<?php

namespace App\Http\Controllers;

use App\Models\ItemPedido;
use App\Models\Pago;
use App\Models\Pedido;
use App\Models\Producto;
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
        $subtotal   = $items->sum('subtotal');
        $ipoconsumo = round($subtotal * 0.08, 2);
        $total      = $subtotal + $ipoconsumo;

        return view('pasarela.show', compact('pedido', 'items', 'subtotal', 'ipoconsumo', 'total'));
    }

    public function confirmar(Pedido $pedido, Request $request)
    {
        $ids    = array_map('intval', (array) $request->input('items_confirmados', []));
        $metodo = in_array($request->input('metodo_pago'), ['tarjeta', 'pse', 'nequi', 'efectivo'])
                    ? $request->input('metodo_pago')
                    : 'tarjeta';
        $monto  = 0;

        // Simular fallo aleatorio (15 % de probabilidad)
        if (rand(1, 100) <= 15) {
            $razones = [
                'Fondos insuficientes en la cuenta.',
                'Tarjeta declinada por el banco emisor.',
                'Error de conexión con la entidad financiera.',
                'Límite de transacciones diarias excedido.',
                'Datos de pago no verificados.',
            ];
            return redirect()
                ->route('pasarela.fallido', $pedido->id_pedido)
                ->with('razon', $razones[array_rand($razones)])
                ->with('metodo', $request->input('metodo_pago', 'tarjeta'));
        }

        DB::transaction(function () use ($ids, $pedido, $metodo, &$monto) {
            $subtotal = 0;

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
                $subtotal += $item->subtotal;

                // Descontar del stock solo si el producto lo rastrea
                Producto::where('id_producto', $item->id_producto)
                    ->whereNotNull('stock')
                    ->update(['stock' => DB::raw('GREATEST(0, stock - ' . (int) $item->cantidad . ')')]);
            }

            if ($subtotal > 0) {
                $monto = $subtotal + round($subtotal * 0.08, 2);
                Pago::create([
                    'id_pedido'   => $pedido->id_pedido,
                    'monto'       => $monto,
                    'metodo_pago' => $metodo,
                    'estado'      => 'simulado',
                    'fecha'       => now(),
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

    public function cobrarEfectivo(Pedido $pedido, Request $request)
    {
        $ids   = array_map('intval', (array) $request->input('items_confirmados', []));
        $monto = 0;

        DB::transaction(function () use ($ids, $pedido, &$monto) {
            $subtotal = 0;

            foreach ($ids as $id_item) {
                $item = ItemPedido::where('id_item', $id_item)
                            ->where('id_pedido', $pedido->id_pedido)
                            ->where('estado', 'Pendiente')
                            ->lockForUpdate()
                            ->first();

                if (!$item) continue;

                $item->update(['estado' => 'Pagado']);
                $subtotal += $item->subtotal;

                // Descontar del stock solo si el producto lo rastrea
                Producto::where('id_producto', $item->id_producto)
                    ->whereNotNull('stock')
                    ->update(['stock' => DB::raw('GREATEST(0, stock - ' . (int) $item->cantidad . ')')]);
            }

            if ($subtotal > 0) {
                $monto = $subtotal + round($subtotal * 0.08, 2);
                Pago::create([
                    'id_pedido'   => $pedido->id_pedido,
                    'monto'       => $monto,
                    'metodo_pago' => 'efectivo',
                    'estado'      => 'confirmado',
                    'fecha'       => now(),
                ]);
            }

            $pedido->refresh();
            $pedido->update(['estado' => $pedido->estaPagado() ? 'Pagado' : 'Parcial']);
        });

        return redirect()
            ->route('factura.show', $pedido->id_pedido)
            ->with('success_efectivo', $monto);
    }

    public function fallido(Pedido $pedido)
    {
        $pedido->load(['negocio', 'mesa']);
        $razon  = session('razon', 'El pago no pudo procesarse.');
        $metodo = session('metodo', 'tarjeta');

        return view('pasarela.fallido', compact('pedido', 'razon', 'metodo'));
    }
}
