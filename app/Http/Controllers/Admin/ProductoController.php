<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'descripcion'  => ['required', 'string'],
            'precio'       => ['required', 'numeric', 'min:0'],
            'id_categoria' => ['nullable', 'integer'],
            'stock'        => ['nullable', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:1'],
        ]);

        auth()->user()->negocio->productos()->create([
            ...$data,
            'disponible'   => $request->boolean('disponible'),
            'stock'        => $request->filled('stock') ? (int) $request->stock : null,
            'stock_minimo' => $request->filled('stock_minimo') ? (int) $request->stock_minimo : 5,
        ]);

        return back()->with('message', '✅ Producto agregado correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $this->autorizarProducto($producto);

        $data = $request->validate([
            'nombre'       => ['required', 'string', 'max:100'],
            'descripcion'  => ['required', 'string'],
            'precio'       => ['required', 'numeric', 'min:0'],
            'id_categoria' => ['nullable', 'integer'],
            'stock'        => ['nullable', 'integer', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:1'],
        ]);

        $producto->update([
            ...$data,
            'disponible'   => $request->boolean('disponible'),
            'stock'        => $request->filled('stock') ? (int) $request->stock : null,
            'stock_minimo' => $request->filled('stock_minimo') ? (int) $request->stock_minimo : 5,
        ]);

        return back()->with('message', '✅ Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $this->autorizarProducto($producto);

        if ($producto->itemsPedido()->exists()) {
            return back()->with('message', '❌ No se puede eliminar: el producto está incluido en una o más facturas.');
        }

        $producto->delete();
        return back()->with('message', '✅ Producto eliminado.');
    }

    private function autorizarProducto(Producto $producto): void
    {
        abort_unless($producto->id_negocio === auth()->user()->id_negocio, 403);
    }
}
