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

        $msg = '✅ Producto agregado correctamente.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
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

        $msg = '✅ Producto actualizado correctamente.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    public function destroy(Request $request, Producto $producto)
    {
        $this->autorizarProducto($producto);

        if ($producto->itemsPedido()->exists()) {
            $msg = '❌ No se puede eliminar: el producto está incluido en una o más facturas.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('message', $msg);
        }

        $producto->delete();
        $msg = '✅ Producto eliminado.';
        return $request->ajax()
            ? response()->json(['success' => true, 'message' => $msg])
            : back()->with('message', $msg);
    }

    private function autorizarProducto(Producto $producto): void
    {
        abort_unless($producto->id_negocio === auth()->user()->id_negocio, 403);
    }
}
