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
            return back()->with('message', '❌ Ya existe una mesa con ese nombre en tu negocio.');
        }

        $negocio->mesas()->create([
            'nombre'    => $nombre,
            'codigo_qr' => 'MESA-' . strtoupper(Str::random(8)),
        ]);

        return back()->with('message', "✅ Mesa '{$nombre}' creada correctamente.");
    }

    public function update(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $request->validate(['nuevo_nombre' => ['required', 'string', 'max:50']]);
        $mesa->update(['nombre' => $request->nuevo_nombre]);
        return back()->with('message', '✅ Mesa renombrada correctamente.');
    }

    public function destroy(Mesa $mesa)
    {
        $this->autorizarMesa($mesa);

        if ($mesa->estaOcupada()) {
            return back()->with('message', '❌ No se puede eliminar: la mesa tiene un pedido activo.');
        }

        if ($mesa->mesasUnidas()->exists()) {
            return back()->with('message', '❌ No se puede eliminar: esta mesa tiene mesas unidas. Sepáralas primero.');
        }

        $mesa->delete();
        return back()->with('message', '✅ Mesa eliminada correctamente.');
    }

    public function unir(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $request->validate(['id_mesa_principal' => ['required', 'integer']]);

        $principal = Mesa::findOrFail($request->id_mesa_principal);
        $this->autorizarMesa($principal);

        if ($mesa->id_mesa === $principal->id_mesa) {
            return back()->with('message', '❌ Una mesa no puede unirse a sí misma.');
        }

        if ($mesa->estaUnida()) {
            return back()->with('message', '❌ Esta mesa ya está unida a otra. Sepárala primero.');
        }

        if ($mesa->mesasUnidas()->exists()) {
            return back()->with('message', '❌ Esta mesa ya tiene mesas unidas. No puede unirse a otra.');
        }

        if ($principal->estaUnida()) {
            return back()->with('message', '❌ La mesa seleccionada ya está unida a otra. No se pueden encadenar uniones.');
        }

        if ($mesa->estaOcupada() || $principal->estaOcupada()) {
            return back()->with('message', '❌ Solo se pueden unir mesas que estén libres.');
        }

        $mesa->update(['mesa_principal_id' => $principal->id_mesa]);

        return back()->with('message', "✅ {$mesa->nombre} unida a {$principal->nombre}.");
    }

    public function separar(Mesa $mesa)
    {
        $this->autorizarMesa($mesa);

        if (!$mesa->estaUnida()) {
            return back()->with('message', '❌ Esta mesa no está unida a ninguna otra.');
        }

        if ($mesa->estaOcupada()) {
            return back()->with('message', '❌ No se puede separar una mesa con pedido activo.');
        }

        $nombrePrincipal = $mesa->mesaPrincipal->nombre;
        $mesa->update(['mesa_principal_id' => null]);

        return back()->with('message', "✅ {$mesa->nombre} separada de {$nombrePrincipal}.");
    }

    private function autorizarMesa(Mesa $mesa): void
    {
        abort_unless($mesa->id_negocio === auth()->user()->id_negocio, 403);
    }
}
