<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['nombre' => ['required', 'string', 'max:80']]);
        auth()->user()->negocio->categorias()->create(['nombre' => $request->nombre]);
        return back()->with('message', '✅ Categoría creada.');
    }

    public function update(Request $request, Categoria $categoria)
    {
        abort_unless($categoria->id_negocio === auth()->user()->id_negocio, 403);
        $request->validate(['nombre' => ['required', 'string', 'max:80']]);
        $categoria->update(['nombre' => $request->nombre]);
        return back()->with('message', '✅ Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        abort_unless($categoria->id_negocio === auth()->user()->id_negocio, 403);
        $categoria->delete();
        return back()->with('message', '✅ Categoría eliminada. Los productos asociados quedaron sin categoría.');
    }
}
