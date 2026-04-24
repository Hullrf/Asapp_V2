<?php

namespace App\Http\Controllers\Mesero;

use App\Http\Controllers\Controller;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeseroController extends Controller
{
    public function index()
    {
        $negocio   = auth()->user()->negocio;
        $pisos     = $negocio->pisos()->orderBy('orden')->get();
        $productos = $negocio->productos()->where('disponible', true)->with('categoria')->orderBy('nombre')->get();
        $mesas     = $negocio->mesas()
            ->with([
                'piso',
                'pedidos'      => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1),
                'mesasUnidas',
                'mesaPrincipal',
                'mesaPrincipal.pedidos' => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1),
            ])
            ->orderByRaw('LENGTH(nombre), nombre')
            ->get();

        return view('mesero.index', compact('negocio', 'mesas', 'pisos', 'productos'));
    }

    public function storePedido(Request $request)
    {
        $request->validate([
            'id_mesa'     => ['required', 'integer', 'exists:mesas,id_mesa'],
            'productos'   => ['required', 'array', 'min:1'],
            'productos.*' => ['integer'],
            'cantidades'  => ['required', 'array'],
        ]);

        $negocio = auth()->user()->negocio;
        $mesa    = Mesa::findOrFail($request->id_mesa);

        abort_unless($mesa->id_negocio === $negocio->id_negocio, 403);

        if ($mesa->estaOcupada()) {
            return response()->json(['success' => false, 'message' => 'Esta mesa ya tiene un pedido activo.']);
        }

        $pedido = Pedido::create([
            'id_negocio' => $negocio->id_negocio,
            'id_mesa'    => $mesa->id_mesa,
            'id_mesero'  => auth()->id(),
            'codigo_qr'  => 'PED-' . strtoupper(Str::random(6)),
            'estado'     => 'Pendiente',
            'fecha'      => now(),
        ]);

        foreach ($request->productos as $id_producto) {
            $cantidad = intval($request->cantidades[$id_producto] ?? 1);
            if ($cantidad < 1) continue;

            $producto = Producto::where('id_producto', $id_producto)
                ->where('id_negocio', $negocio->id_negocio)
                ->first();

            if (!$producto) continue;

            $pedido->items()->create([
                'id_producto'     => $producto->id_producto,
                'cantidad'        => $cantidad,
                'precio_unitario' => $producto->precio,
                'subtotal'        => $producto->precio * $cantidad,
                'estado'          => 'Pendiente',
            ]);
        }

        return response()->json([
            'success'     => true,
            'message'     => "Pedido #{$pedido->id_pedido} creado.",
            'factura_url' => route('factura.show', $pedido->id_pedido),
        ]);
    }

    public function unirGrupo(Request $request, Mesa $mesa)
    {
        $this->autorizarMesa($mesa);
        $negocio = auth()->user()->negocio;

        if ($mesa->estaUnida()) {
            return response()->json(['success' => false, 'message' => '❌ Esta mesa ya es secundaria. Sepárala primero.'], 422);
        }

        if ($mesa->estaOcupada()) {
            return response()->json(['success' => false, 'message' => '❌ La mesa base tiene un pedido activo.'], 422);
        }

        $request->validate([
            'id_mesas'   => ['required', 'array', 'min:1'],
            'id_mesas.*' => ['integer'],
        ]);
        $ids = $request->input('id_mesas');

        $unidas   = [];
        $omitidas = [];

        foreach ($ids as $id) {
            $secundaria = Mesa::where('id_mesa', $id)
                ->where('id_negocio', $negocio->id_negocio)
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

        if (empty($unidas)) {
            $msg = '❌ Ninguna mesa pudo unirse.' . (! empty($omitidas) ? ' Omitidas: ' . implode(', ', $omitidas) : '');
            return response()->json(['success' => false, 'message' => $msg], 422);
        }

        $msg = '✅ Unidas a ' . $mesa->nombre_display . ': ' . implode(', ', $unidas);
        if (! empty($omitidas)) {
            $msg .= '. Omitidas: ' . implode(', ', $omitidas);
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    private function autorizarMesa(Mesa $mesa): void
    {
        abort_unless($mesa->id_negocio === auth()->user()->negocio->id_negocio, 403);
    }
}
