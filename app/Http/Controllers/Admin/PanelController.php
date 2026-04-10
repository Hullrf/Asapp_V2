<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PanelController extends Controller
{
    public function index()
    {
        return view('admin.panel', $this->cargarDatos());
    }

    public function parcialInventario()
    {
        return view('admin.partials.inventario', $this->cargarDatos());
    }

    public function parcialMesas()
    {
        return view('admin.partials.mesas', $this->cargarDatos());
    }

    public function parcialNuevoPedido()
    {
        return view('admin.partials.nuevo-pedido', $this->cargarDatos());
    }

    public function parcialEstadisticas()
    {
        return view('admin.partials.estadisticas', $this->cargarDatos());
    }

    public function parcialHistorial()
    {
        return view('admin.partials.historial', $this->cargarDatos());
    }

    public function parcialMeseros()
    {
        return view('admin.partials.meseros', $this->cargarDatos());
    }

    public function estadisticasPagos(Request $request)
    {
        $negocio = auth()->user()->negocioActivo();
        $periodo = $request->input('periodo', 'semana');

        $desde = match($periodo) {
            'dia'  => Carbon::today(),
            'mes'  => Carbon::now()->subDays(30),
            'anio' => Carbon::now()->subYear(),
            default => Carbon::now()->subDays(7), // semana
        };

        $pagos = Pago::whereHas('pedido', fn($q) => $q->where('id_negocio', $negocio->id_negocio))
            ->where('estado', '!=', 'fallido')
            ->where('fecha', '>=', $desde)
            ->selectRaw('metodo_pago, SUM(monto) as total, COUNT(*) as cantidad')
            ->groupBy('metodo_pago')
            ->get()
            ->keyBy('metodo_pago');

        $metodos = ['tarjeta', 'pse', 'nequi', 'efectivo', 'digital'];
        $resultado = [];
        foreach ($metodos as $m) {
            $resultado[$m] = [
                'total'    => (float) ($pagos[$m]->total ?? 0),
                'cantidad' => (int)   ($pagos[$m]->cantidad ?? 0),
            ];
        }

        return response()->json($resultado);
    }

    private function cargarDatos(): array
    {
        $negocio        = auth()->user()->negocioActivo();
        $todasLasSedes  = auth()->user()->negocios()->orderBy('nombre')->get();
        $productos  = $negocio->productos()->with('categoria')->orderBy('nombre')->get();
        $categorias = $negocio->categorias()->orderBy('nombre')->get();
        $pisos = $negocio->pisos()->orderBy('orden')->get();

        $mesas = $negocio->mesas()
            ->with([
                'piso',
                'pedidos'      => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1),
                'mesasUnidas',
                'mesaPrincipal',
                'mesaPrincipal.pedidos' => fn($q) => $q->whereIn('estado', ['Pendiente', 'Parcial'])->latest('id_pedido')->limit(1),
            ])
            ->orderByRaw('LENGTH(nombre), nombre')
            ->get();

        $base_url = request()->getSchemeAndHttpHost();

        $productosStockBajo = $productos->filter(
            fn($p) => $p->stock !== null && $p->stock <= $p->stock_minimo
        );

        $todosLosPedidos = $negocio->pedidos()
            ->with('items.producto', 'mesa')
            ->get();

        $pedidosPorEstado = $todosLosPedidos
            ->groupBy(fn($p) => $p->estado->value)
            ->map->count();

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

        $pedidosPagados = $todosLosPedidos
            ->filter(fn($p) => $p->estado->value === 'Pagado')
            ->sortByDesc('id_pedido')
            ->values();

        $resumen = [
            'total_pedidos'     => $todosLosPedidos->count(),
            'total_cobrado'     => (float) $todosLosPedidos
                ->flatMap->items
                ->filter(fn($i) => $i->estado->value === 'Pagado')
                ->sum('subtotal'),
            'productos_activos' => $productos->where('disponible', true)->count(),
            'mesas_total'       => $mesas->count(),
        ];

        $meseros = $negocio->usuarios()->where('rol', 'mesero')->orderBy('nombre')->get();

        return compact(
            'negocio', 'todasLasSedes', 'productos', 'mesas', 'pisos', 'base_url',
            'pedidosPorEstado', 'topProductos', 'ingresosPorMesa', 'resumen',
            'pedidosPagados', 'categorias', 'productosStockBajo', 'meseros'
        );
    }
}
