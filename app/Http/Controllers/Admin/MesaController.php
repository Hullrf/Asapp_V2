<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MesaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['nombre_mesa' => ['required', 'string', 'max:50']]);

        $negocio = auth()->user()->negocio;
        $nombre  = $request->nombre_mesa;

        if ($negocio->mesas()->where('nombre', $nombre)->exists()) {
            $msg = '❌ Ya existe una mesa con ese nombre en tu negocio.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $negocio->mesas()->create([
            'nombre'    => $nombre,
            'codigo_qr' => 'MESA-' . strtoupper(Str::random(8)),
        ]);

        $msg = "✅ Mesa '{$nombre}' creada correctamente.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function update(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $request->validate(['nuevo_nombre' => ['required', 'string', 'max:50']]);
        $mesa->update(['nombre' => $request->nuevo_nombre]);

        $msg = '✅ Mesa renombrada correctamente.';
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

        $mesa->delete();
        $msg = '✅ Mesa eliminada correctamente.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function unir(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $request->validate(['id_mesa_principal' => ['required', 'integer']]);

        $principal = Mesa::findOrFail($request->id_mesa_principal);
        $this->autorizarMesa($principal);

        $checks = [
            $mesa->id_mesa === $principal->id_mesa     => '❌ Una mesa no puede unirse a sí misma.',
            $mesa->estaUnida()                          => '❌ Esta mesa ya está unida a otra. Sepárala primero.',
            $mesa->mesasUnidas()->exists()              => '❌ Esta mesa ya tiene mesas unidas. No puede unirse a otra.',
            $principal->estaUnida()                     => '❌ La mesa seleccionada ya está unida a otra. No se pueden encadenar uniones.',
            $mesa->estaOcupada() || $principal->estaOcupada() => '❌ Solo se pueden unir mesas que estén libres.',
        ];

        foreach ($checks as $condition => $msg) {
            if ($condition) {
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => $msg], 422)
                    : back()->with('message', $msg);
            }
        }

        $mesa->update(['mesa_principal_id' => $principal->id_mesa]);
        $msg = "✅ {$mesa->nombre} unida a {$principal->nombre}.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function separar(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);

        if (!$mesa->estaUnida()) {
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

        $nombrePrincipal = $mesa->mesaPrincipal->nombre;
        $mesa->update(['mesa_principal_id' => null]);

        $msg = "✅ {$mesa->nombre} separada de {$nombrePrincipal}.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    private function autorizarMesa(Mesa $mesa): void
    {
        abort_unless($mesa->id_negocio === auth()->user()->id_negocio, 403);
    }
}
