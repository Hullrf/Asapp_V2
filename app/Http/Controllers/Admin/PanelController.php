<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PanelController extends Controller
{
    public function index()
    {
        $negocio  = auth()->user()->negocio;
        $productos = $negocio->productos()->with('categoria')->orderBy('nombre')->get();
        $categorias = $negocio->categorias()->orderBy('nombre')->get();
        $mesas     = $negocio->mesas()
            ->with(['pedidos' => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1)])
            ->orderBy('nombre')
            ->get();

        $base_url = request()->getSchemeAndHttpHost();

        $productosStockBajo = $productos->filter(
            fn($p) => $p->stock !== null && $p->stock <= $p->stock_minimo
        );

        // ── Estadísticas ──────────────────────────────────────────────────
        $todosLosPedidos = $negocio->pedidos()
            ->with('items.producto', 'mesa')
            ->get();

        // Pedidos agrupados por estado
        $pedidosPorEstado = $todosLosPedidos
            ->groupBy(fn($p) => $p->estado->value)
            ->map->count();

        // Top 5 productos más pedidos (por cantidad total)
        $topProductos = $todosLosPedidos
            ->flatMap(fn($p) => $p->items)
            ->groupBy('id_producto')
            ->map(fn($items) => [
                'nombre'   => $items->first()->producto->nombre,
                'cantidad' => (int) $items->sum('cantidad'),
            ])
            ->sortByDesc('cantidad')
            ->take(5)
            ->values();

        // Ingresos cobrados por mesa (solo ítems Pagado)
        $ingresosPorMesa = $todosLosPedidos
            ->filter(fn($p) => $p->mesa !== null)
            ->groupBy(fn($p) => $p->mesa->nombre)
            ->map(fn($pedidos) => (float) $pedidos
                ->flatMap->items
                ->filter(fn($item) => $item->estado->value === 'Pagado')
                ->sum('subtotal')
            )
            ->filter(fn($total) => $total > 0)
            ->sortByDesc(fn($v) => $v)
            ->take(6);

        // Historial de pedidos completamente pagados
        $pedidosPagados = $todosLosPedidos
            ->filter(fn($p) => $p->estado->value === 'Pagado')
            ->sortByDesc('id_pedido')
            ->values();

        // Tarjetas de resumen
        $resumen = [
            'total_pedidos'     => $todosLosPedidos->count(),
            'total_cobrado'     => (float) $todosLosPedidos
                ->flatMap->items
                ->filter(fn($i) => $i->estado->value === 'Pagado')
                ->sum('subtotal'),
            'productos_activos' => $productos->where('disponible', true)->count(),
            'mesas_total'       => $mesas->count(),
        ];

        return view('admin.panel', compact(
            'negocio', 'productos', 'mesas', 'base_url',
            'pedidosPorEstado', 'topProductos', 'ingresosPorMesa', 'resumen',
            'pedidosPagados', 'categorias', 'productosStockBajo'
        ));
    }
}
