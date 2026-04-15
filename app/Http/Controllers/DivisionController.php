<?php
namespace App\Http\Controllers;

use App\Models\DivisionParte;
use App\Models\ItemDivision;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DivisionController extends Controller
{
    public function iniciar(Request $request, Pedido $pedido, ItemPedido $item)
    {
        $request->validate([
            'token'    => ['required', 'string', 'max:64'],
            'partes'   => ['required', 'integer', 'min:2'],
            'montos'   => ['required', 'array', 'min:2'],
            'montos.*' => ['required', 'numeric', 'min:0.01'],
        ]);

        abort_if($item->id_pedido !== $pedido->id_pedido, 403);
        abort_if($item->estado->value !== 'Pendiente', 422, 'El ítem no está pendiente');
        abort_if(count($request->montos) !== (int) $request->partes, 422, 'Cantidad de montos incorrecta');
        abort_if(
            ItemDivision::where('id_item', $item->id_item)->where('estado', 'pendiente')->exists(),
            409, 'Ya existe una división activa para este ítem'
        );

        $suma = array_sum(array_map('floatval', $request->montos));
        abort_if(abs($suma - (float) $item->subtotal) > 0.5, 422, 'Los montos no suman el subtotal del ítem');

        DB::transaction(function () use ($request, $item) {
            $division = ItemDivision::create([
                'id_item'         => $item->id_item,
                'total_partes'    => $request->partes,
                'iniciador_token' => $request->token,
                'estado'          => 'pendiente',
            ]);
            foreach ($request->montos as $i => $monto) {
                DivisionParte::create([
                    'id_division'  => $division->id_division,
                    'numero_parte' => $i + 1,
                    'monto'        => $monto,
                    'estado'       => 'libre',
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function tomar(Request $request, Pedido $pedido, ItemDivision $division)
    {
        $request->validate([
            'token'    => ['required', 'string', 'max:64'],
            'id_parte' => ['required', 'integer'],
        ]);

        abort_if($division->estado !== 'pendiente', 422, 'División no activa');
        abort_if($division->item->id_pedido !== $pedido->id_pedido, 403);

        $confirmado = false;

        DB::transaction(function () use ($request, $division, &$confirmado) {
            // Release any part already held by this token in this division
            DivisionParte::where('id_division', $division->id_division)
                ->where('participante_token', $request->token)
                ->update(['estado' => 'libre', 'participante_token' => null]);

            // Claim the requested part (with lock)
            $parte = DivisionParte::where('id_parte', $request->id_parte)
                ->where('id_division', $division->id_division)
                ->where('estado', 'libre')
                ->lockForUpdate()
                ->first();

            abort_if(!$parte, 409, 'Esta parte ya fue tomada por alguien más');

            $parte->update(['estado' => 'tomada', 'participante_token' => $request->token]);

            // If no more free parts → confirm division
            if (!DivisionParte::where('id_division', $division->id_division)->where('estado', 'libre')->exists()) {
                $this->confirmar($division);
                $confirmado = true;
            }
        });

        return response()->json(['ok' => true, 'confirmado' => $confirmado]);
    }

    public function liberar(Request $request, Pedido $pedido, ItemDivision $division)
    {
        $request->validate(['token' => ['required', 'string', 'max:64']]);

        abort_if($division->estado !== 'pendiente', 422);
        abort_if($division->item->id_pedido !== $pedido->id_pedido, 403);

        DivisionParte::where('id_division', $division->id_division)
            ->where('participante_token', $request->token)
            ->update(['estado' => 'libre', 'participante_token' => null]);

        return response()->json(['ok' => true]);
    }

    public function cancelar(Request $request, Pedido $pedido, ItemDivision $division)
    {
        $division->load('item');
        abort_if($division->item->id_pedido !== $pedido->id_pedido, 403);

        DB::transaction(function () use ($division) {
            $restante = (float) $division->partes()->sum('monto');

            // Si ya se extrajeron partes, ajustar el ítem al monto restante
            if ($restante > 0 && abs($restante - (float) $division->item->subtotal) > 0.5) {
                $division->item->update([
                    'cantidad'        => 1,
                    'precio_unitario' => $restante,
                    'subtotal'        => $restante,
                ]);
            }

            $division->update(['estado' => 'cancelada']);
        });

        return response()->json(['ok' => true]);
    }

    public function actualizar(Request $request, Pedido $pedido, ItemDivision $division)
    {
        $request->validate([
            'token'    => ['required', 'string', 'max:64'],
            'partes'   => ['required', 'integer', 'min:2'],
            'montos'   => ['required', 'array', 'min:2'],
            'montos.*' => ['required', 'numeric', 'min:0.01'],
        ]);

        abort_if($division->estado !== 'pendiente', 422);
        abort_if($division->item->id_pedido !== $pedido->id_pedido, 403);
        abort_if(count($request->montos) !== (int) $request->partes, 422);

        // Validar contra el monto restante (puede diferir del subtotal original si ya se extrajeron partes)
        $restante = (float) $division->partes()->sum('monto');
        $suma     = array_sum(array_map('floatval', $request->montos));
        abort_if(abs($suma - $restante) > 0.5, 422, 'Los montos no suman el monto restante (' . number_format($restante, 0, ',', '.') . ')');

        DB::transaction(function () use ($request, $division) {
            $division->update(['estado' => 'cancelada']);

            $nueva = ItemDivision::create([
                'id_item'         => $division->id_item,
                'total_partes'    => $request->partes,
                'iniciador_token' => $request->token,
                'estado'          => 'pendiente',
            ]);

            foreach ($request->montos as $i => $monto) {
                DivisionParte::create([
                    'id_division'  => $nueva->id_division,
                    'numero_parte' => $i + 1,
                    'monto'        => $monto,
                    'estado'       => 'libre',
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function extraerParte(Request $request, Pedido $pedido, ItemDivision $division)
    {
        $request->validate(['token' => ['required', 'string', 'max:64']]);

        $division->load('item');
        abort_if($division->item->id_pedido !== $pedido->id_pedido, 403);
        abort_if($division->estado !== 'pendiente', 422, 'División no activa');

        $idItem = null;

        DB::transaction(function () use ($request, $division, $pedido, &$idItem) {
            $parte = DivisionParte::where('id_division', $division->id_division)
                ->where('participante_token', $request->token)
                ->where('estado', 'tomada')
                ->lockForUpdate()
                ->first();

            if (!$parte) return; // ya fue extraída o no hay parte tomada

            $nuevoItem = $pedido->items()->create([
                'id_producto'     => $division->item->id_producto,
                'cantidad'        => 1,
                'precio_unitario' => $parte->monto,
                'subtotal'        => $parte->monto,
                'estado'          => 'Pendiente',
            ]);
            $idItem = $nuevoItem->id_item;

            $parte->delete();

            // Si ya no quedan partes: limpiar división e ítem original
            if (!DivisionParte::where('id_division', $division->id_division)->exists()) {
                $itemOriginal = $division->item;
                $division->delete();
                $itemOriginal->delete();
            }
        });

        return response()->json(['ok' => true, 'id_item' => $idItem]);
    }

    private function confirmar(ItemDivision $division): void
    {
        $division->load(['item.pedido', 'partes']);
        $item   = $division->item;
        $pedido = $item->pedido;

        foreach ($division->partes as $parte) {
            $pedido->items()->create([
                'id_producto'     => $item->id_producto,
                'cantidad'        => 1,
                'precio_unitario' => $parte->monto,
                'subtotal'        => $parte->monto,
                'estado'          => 'Pendiente',
            ]);
        }

        $item->delete(); // cascades division + partes
    }
}
