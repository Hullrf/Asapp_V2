<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MesaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['id_piso' => ['required', 'integer']]);

        $negocio = auth()->user()->negocioActivo();

        $piso = $negocio->pisos()->find($request->id_piso);
        if (! $piso) {
            $msg = '❌ Piso no válido.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $numero = (Mesa::where('id_piso', $piso->id_piso)->max('numero') ?? 0) + 1;
        $nombre = 'Mesa ' . $numero;

        $negocio->mesas()->create([
            'id_piso'   => $piso->id_piso,
            'numero'    => $numero,
            'nombre'    => $nombre,
            'codigo_qr' => 'MESA-' . strtoupper(Str::random(8)),
        ]);

        $msg = "✅ {$nombre} creada en {$piso->nombre}.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    /** Guarda o borra el alias de la mesa. */
    public function update(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $request->validate(['alias' => ['nullable', 'string', 'max:50']]);

        $alias = filled($request->alias) ? trim($request->alias) : null;
        $mesa->update(['alias' => $alias]);

        $msg = $alias ? "✅ Alias '{$alias}' guardado." : '✅ Alias eliminado.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function destroy(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);

        if ($mesa->estaOcupada()) {
            $msg = '❌ No se puede eliminar: la mesa tiene un pedido activo.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        if ($mesa->mesasUnidas()->exists()) {
            $msg = '❌ No se puede eliminar: esta mesa tiene mesas unidas. Sepáralas primero.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $idPiso       = $mesa->id_piso;
        $numEliminada = $mesa->numero;
        $mesa->delete();

        // Renumerar las mesas con número mayor dentro del mismo piso
        if ($idPiso && $numEliminada) {
            Mesa::where('id_piso', $idPiso)
                ->where('numero', '>', $numEliminada)
                ->orderBy('numero')
                ->get()
                ->each(function (Mesa $m) {
                    $nuevoNum = $m->numero - 1;
                    $m->update([
                        'numero' => $nuevoNum,
                        'nombre' => 'Mesa ' . $nuevoNum,
                    ]);
                });
        }

        $msg = '✅ Mesa eliminada y mesas renumeradas.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function unirGrupo(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);

        if ($mesa->estaUnida()) {
            $msg = '❌ Esta mesa ya es secundaria de otra. Sepárala primero.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        if ($mesa->estaOcupada()) {
            $msg = '❌ La mesa base tiene un pedido activo.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $request->validate([
            'id_mesas'   => ['required', 'array', 'min:1'],
            'id_mesas.*' => ['integer'],
        ]);
        $ids = $request->input('id_mesas');

        $unidas   = [];
        $omitidas = [];

        DB::transaction(function () use ($ids, $mesa, &$unidas, &$omitidas) {
            foreach ($ids as $id) {
                $secundaria = Mesa::where('id_mesa', $id)
                    ->where('id_negocio', $mesa->id_negocio)
                    ->first();

                if (! $secundaria
                    || $secundaria->id_mesa === $mesa->id_mesa
                    || $secundaria->estaUnida()
                    || $secundaria->estaOcupada()
                    || $secundaria->mesasUnidas()->exists()) {
                    if ($secundaria) {
                        $omitidas[] = $secundaria->nombre_display;
                    }
                    continue;
                }

                $secundaria->update(['mesa_principal_id' => $mesa->id_mesa]);
                $unidas[] = $secundaria->nombre_display;
            }
        });

        if (empty($unidas)) {
            $msg = '❌ Ninguna mesa pudo unirse.' . (! empty($omitidas) ? ' Omitidas: ' . implode(', ', $omitidas) : '');
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $msg = '✅ Unidas a ' . $mesa->nombre_display . ': ' . implode(', ', $unidas);
        if (! empty($omitidas)) {
            $msg .= '. Omitidas: ' . implode(', ', $omitidas);
        }

        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function separar(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);

        if (! $mesa->estaUnida()) {
            $msg = '❌ Esta mesa no está unida a ninguna otra.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        if ($mesa->estaOcupada()) {
            $msg = '❌ No se puede separar una mesa con pedido activo.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $nombrePrincipal = $mesa->mesaPrincipal->nombre_display;
        $mesa->update(['mesa_principal_id' => null]);

        $msg = "✅ {$mesa->nombre_display} separada de {$nombrePrincipal}.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    private function autorizarMesa(Mesa $mesa): void
    {
        abort_unless($mesa->id_negocio === auth()->user()->idNegocioActivo(), 403);
    }
}
