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

        [$desde, $formatoDb, $buckets, $displayLabels] = match($periodo) {
            'dia' => [
                Carbon::today(),
                '%H:00',
                collect(range(0, 23))->mapWithKeys(fn($h) => [str_pad($h, 2, '0', STR_PAD_LEFT).':00' => 0.0]),
                collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00')->toArray(),
            ],
            'mes' => [
                Carbon::now()->subDays(29)->startOfDay(),
                '%Y-%m-%d',
                collect(range(29, 0))->mapWithKeys(fn($i) => [Carbon::now()->subDays($i)->format('Y-m-d') => 0.0]),
                collect(range(29, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('d/m'))->toArray(),
            ],
            'anio' => [
                Carbon::now()->subMonths(11)->startOfMonth(),
                '%Y-%m',
                collect(range(11, 0))->mapWithKeys(fn($i) => [Carbon::now()->subMonths($i)->format('Y-m') => 0.0]),
                collect(range(11, 0))->map(fn($i) => Carbon::now()->subMonths($i)->translatedFormat('M y'))->toArray(),
            ],
            default => [ // semana
                Carbon::now()->subDays(6)->startOfDay(),
                '%Y-%m-%d',
                collect(range(6, 0))->mapWithKeys(fn($i) => [Carbon::now()->subDays($i)->format('Y-m-d') => 0.0]),
                collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->translatedFormat('D d/m'))->toArray(),
            ],
        };

        $pagos = Pago::whereHas('pedido', fn($q) => $q->where('id_negocio', $negocio->id_negocio))
            ->where('estado', '!=', 'fallido')
            ->where('fecha', '>=', $desde)
            ->selectRaw("metodo_pago, DATE_FORMAT(fecha, '{$formatoDb}') as bucket, SUM(monto) as total")
            ->groupBy('metodo_pago', 'bucket')
            ->get();

        $metodos = ['tarjeta', 'pse', 'nequi', 'efectivo', 'digital'];
        $series  = [];
        foreach ($metodos as $m) {
            $data = $buckets->toArray();
            foreach ($pagos->where('metodo_pago', $m) as $pago) {
                if (array_key_exists($pago->bucket, $data)) {
                    $data[$pago->bucket] = (float) $pago->total;
                }
            }
            if (array_sum($data) > 0) {
                $series[$m] = array_values($data);
            }
        }

        return response()->json([
            'labels' => $displayLabels,
            'series' => $series,
        ]);
    }

    public function estadisticasMesas(Request $request)
    {
        $negocio = auth()->user()->negocioActivo();
        $periodo = $request->input('periodo', 'semana');

        [$desde, $formatoDb, $buckets, $displayLabels] = match($periodo) {
            'dia' => [
                Carbon::today(),
                '%H:00',
                collect(range(0, 23))->mapWithKeys(fn($h) => [str_pad($h, 2, '0', STR_PAD_LEFT).':00' => 0.0]),
                collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00')->toArray(),
            ],
            'mes' => [
                Carbon::now()->subDays(29)->startOfDay(),
                '%Y-%m-%d',
                collect(range(29, 0))->mapWithKeys(fn($i) => [Carbon::now()->subDays($i)->format('Y-m-d') => 0.0]),
                collect(range(29, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('d/m'))->toArray(),
            ],
            'anio' => [
                Carbon::now()->subMonths(11)->startOfMonth(),
                '%Y-%m',
                collect(range(11, 0))->mapWithKeys(fn($i) => [Carbon::now()->subMonths($i)->format('Y-m') => 0.0]),
                collect(range(11, 0))->map(fn($i) => Carbon::now()->subMonths($i)->translatedFormat('M y'))->toArray(),
            ],
            default => [
                Carbon::now()->subDays(6)->startOfDay(),
                '%Y-%m-%d',
                collect(range(6, 0))->mapWithKeys(fn($i) => [Carbon::now()->subDays($i)->format('Y-m-d') => 0.0]),
                collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->translatedFormat('D d/m'))->toArray(),
            ],
        };

        // Ingresos por mesa agrupados por bucket de tiempo
        $rows = DB::table('items_pedido')
            ->join('pedidos', 'items_pedido.id_pedido', '=', 'pedidos.id_pedido')
            ->join('mesas', 'pedidos.id_mesa', '=', 'mesas.id_mesa')
            ->where('pedidos.id_negocio', $negocio->id_negocio)
            ->where('items_pedido.estado', 'Pagado')
            ->where('pedidos.fecha', '>=', $desde)
            ->whereNotNull('pedidos.id_mesa')
            ->selectRaw("mesas.nombre as mesa, DATE_FORMAT(pedidos.fecha, '{$formatoDb}') as bucket, SUM(items_pedido.subtotal) as total")
            ->groupBy('mesas.nombre', 'bucket')
            ->get();

        $mesas  = $rows->pluck('mesa')->unique()->sort()->values();
        $series = [];

        foreach ($mesas as $mesa) {
            $data = $buckets->toArray();
            foreach ($rows->where('mesa', $mesa) as $row) {
                if (array_key_exists($row->bucket, $data)) {
                    $data[$row->bucket] = (float) $row->total;
                }
            }
            if (array_sum($data) > 0) {
                $series[$mesa] = array_values($data);
            }
        }

        return response()->json([
            'labels' => $displayLabels,
            'series' => $series,
        ]);
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
