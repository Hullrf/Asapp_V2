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

        $mesa->delete();
        return back()->with('message', '✅ Mesa eliminada correctamente.');
    }

    private function autorizarMesa(Mesa $mesa): void
    {
        abort_unless($mesa->id_negocio === auth()->user()->id_negocio, 403);
    }
}
