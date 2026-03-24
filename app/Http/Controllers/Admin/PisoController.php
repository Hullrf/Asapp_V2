<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Piso;
use Illuminate\Http\Request;

class PisoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['nombre_piso' => ['required', 'string', 'max:50']]);

        $negocio = auth()->user()->negocioActivo();
        $nombre  = $request->nombre_piso;

        if ($negocio->pisos()->where('nombre', $nombre)->exists()) {
            $msg = '❌ Ya existe un piso con ese nombre.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $orden = ($negocio->pisos()->max('orden') ?? 0) + 1;
        $negocio->pisos()->create(['nombre' => $nombre, 'orden' => $orden]);

        $msg = "✅ Piso '{$nombre}' creado.";
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function update(Request $request, Piso $piso)
    {
        $this->autorizarPiso($piso);
        $request->validate(['nuevo_nombre' => ['required', 'string', 'max:50']]);
        $piso->update(['nombre' => $request->nuevo_nombre]);

        $msg = '✅ Piso renombrado correctamente.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function destroy(Request $request, Piso $piso)
    {
        $this->autorizarPiso($piso);

        if ($piso->mesas()->exists()) {
            $msg = '❌ No se puede eliminar: el piso tiene mesas asignadas. Elimínalas primero.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $piso->delete();

        $msg = '✅ Piso eliminado.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    private function autorizarPiso(Piso $piso): void
    {
        abort_unless($piso->id_negocio === auth()->user()->idNegocioActivo(), 403);
    }
}
