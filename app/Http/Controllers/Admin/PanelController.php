<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

    private function desdeParaPeriodo(string $periodo): \Illuminate\Support\Carbon
    {
        return match($periodo) {
            'dia'   => Carbon::today(),
            'mes'   => Carbon::now()->subDays(29)->startOfDay(),
            'anio'  => Carbon::now()->subMonths(11)->startOfMonth(),
            default => Carbon::now()->subDays(6)->startOfDay(),
        };
    }

    public function estadisticasMeseros(Request $request)
    {
        $negocio = auth()->user()->negocioActivo();
        $desde   = $this->desdeParaPeriodo($request->input('periodo', 'semana'));

        $rows = DB::table('items_pedido')
            ->join('pedidos', 'items_pedido.id_pedido', '=', 'pedidos.id_pedido')
            ->join('users', 'pedidos.id_mesero', '=', 'users.id_usuario')
            ->where('pedidos.id_negocio', $negocio->id_negocio)
            ->where('items_pedido.estado', 'Pagado')
            ->where('pedidos.fecha', '>=', $desde)
            ->whereNotNull('pedidos.id_mesero')
            ->selectRaw('users.nombre as mesero, SUM(items_pedido.subtotal) as total')
            ->groupBy('users.nombre')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('mesero')->toArray(),
            'data'   => $rows->map(fn($r) => (float) $r->total)->toArray(),
        ]);
    }

    public function estadisticasHoras(Request $request)
    {
        $negocio = auth()->user()->negocioActivo();
        $desde   = $this->desdeParaPeriodo($request->input('periodo', 'semana'));

        $rows = DB::table('items_pedido')
            ->join('pedidos', 'items_pedido.id_pedido', '=', 'pedidos.id_pedido')
            ->where('pedidos.id_negocio', $negocio->id_negocio)
            ->where('items_pedido.estado', 'Pagado')
            ->where('pedidos.fecha', '>=', $desde)
            ->selectRaw('HOUR(pedidos.fecha) as hora, SUM(items_pedido.subtotal) as total')
            ->groupBy('hora')
            ->get();

        $buckets = collect(range(0, 23))->mapWithKeys(fn($h) => [$h => 0.0]);
        foreach ($rows as $row) {
            $buckets[(int) $row->hora] = (float) $row->total;
        }

        return response()->json([
            'labels' => collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00')->toArray(),
            'data'   => array_values($buckets->toArray()),
        ]);
    }

    public function estadisticasCategorias(Request $request)
    {
        $negocio = auth()->user()->negocioActivo();
        $desde   = $this->desdeParaPeriodo($request->input('periodo', 'semana'));

        $rows = DB::table('items_pedido')
            ->join('pedidos', 'items_pedido.id_pedido', '=', 'pedidos.id_pedido')
            ->join('productos', 'items_pedido.id_producto', '=', 'productos.id_producto')
            ->leftJoin('categorias', 'productos.id_categoria', '=', 'categorias.id_categoria')
            ->where('pedidos.id_negocio', $negocio->id_negocio)
            ->where('items_pedido.estado', 'Pagado')
            ->where('pedidos.fecha', '>=', $desde)
            ->selectRaw("COALESCE(categorias.nombre, 'Sin categoría') as categoria, SUM(items_pedido.subtotal) as total")
            ->groupBy('categorias.nombre')
            ->orderByDesc('total')
            ->get();

        return response()->json([
            'labels' => $rows->pluck('categoria')->toArray(),
            'data'   => $rows->map(fn($r) => (float) $r->total)->toArray(),
        ]);
    }

    public function estadisticasTicket(Request $request)
    {
        $negocio = auth()->user()->negocioActivo();
        $periodo = $request->input('periodo', 'semana');

        [$desde, $formatoDb, $buckets, $displayLabels] = match($periodo) {
            'dia' => [
                Carbon::today(),
                '%H:00',
                collect(range(0, 23))->mapWithKeys(fn($h) => [str_pad($h, 2, '0', STR_PAD_LEFT).':00' => null]),
                collect(range(0, 23))->map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00')->toArray(),
            ],
            'mes' => [
                Carbon::now()->subDays(29)->startOfDay(),
                '%Y-%m-%d',
                collect(range(29, 0))->mapWithKeys(fn($i) => [Carbon::now()->subDays($i)->format('Y-m-d') => null]),
                collect(range(29, 0))->map(fn($i) => Carbon::now()->subDays($i)->format('d/m'))->toArray(),
            ],
            'anio' => [
                Carbon::now()->subMonths(11)->startOfMonth(),
                '%Y-%m',
                collect(range(11, 0))->mapWithKeys(fn($i) => [Carbon::now()->subMonths($i)->format('Y-m') => null]),
                collect(range(11, 0))->map(fn($i) => Carbon::now()->subMonths($i)->translatedFormat('M y'))->toArray(),
            ],
            default => [
                Carbon::now()->subDays(6)->startOfDay(),
                '%Y-%m-%d',
                collect(range(6, 0))->mapWithKeys(fn($i) => [Carbon::now()->subDays($i)->format('Y-m-d') => null]),
                collect(range(6, 0))->map(fn($i) => Carbon::now()->subDays($i)->translatedFormat('D d/m'))->toArray(),
            ],
        };

        $rows = DB::table('items_pedido')
            ->join('pedidos', 'items_pedido.id_pedido', '=', 'pedidos.id_pedido')
            ->where('pedidos.id_negocio', $negocio->id_negocio)
            ->where('items_pedido.estado', 'Pagado')
            ->where('pedidos.fecha', '>=', $desde)
            ->selectRaw("pedidos.id_pedido, DATE_FORMAT(pedidos.fecha, '{$formatoDb}') as bucket, SUM(items_pedido.subtotal) as total_pedido")
            ->groupBy('pedidos.id_pedido', 'bucket')
            ->get();

        $data = $buckets->toArray();
        foreach ($rows->groupBy('bucket') as $bucket => $items) {
            if (array_key_exists($bucket, $data)) {
                $data[$bucket] = round($items->avg('total_pedido'), 0);
            }
        }

        return response()->json([
            'labels' => $displayLabels,
            'data'   => array_values($data),
        ]);
    }

    public function guardarConfigPanel(Request $request)
    {
        $valid  = ['top_productos','fuentes_pago','ingresos_mesa','stock_productos',
                   'rendimiento_mesero','horas_pico','categorias','ticket_promedio'];
        $charts = array_values(array_intersect($request->input('charts', []), $valid));
        auth()->user()->negocioActivo()->update(['config_panel' => $charts]);
        return response()->json(['ok' => true]);
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
            'pedidos_pendientes' => $todosLosPedidos->filter(fn($p) => $p->estado->value === 'Pendiente')->count(),
            'pedidos_parciales'  => $todosLosPedidos->filter(fn($p) => $p->estado->value === 'Parcial')->count(),
            'total_cobrado'     => (float) $todosLosPedidos
                ->flatMap->items
                ->filter(fn($i) => $i->estado->value === 'Pagado')
                ->sum('subtotal'),
            'productos_activos' => $productos->where('disponible', true)->count(),
            'mesas_total'       => $mesas->count(),
        ];

        $meseros = $negocio->usuarios()->where('rol', 'mesero')->orderBy('nombre')->get();

        $allCharts = ['top_productos','fuentes_pago','ingresos_mesa','stock_productos',
                      'rendimiento_mesero','horas_pico','categorias','ticket_promedio'];
        $configPanel = $negocio->config_panel ?? $allCharts;

        return compact(
            'negocio', 'todasLasSedes', 'productos', 'mesas', 'pisos', 'base_url',
            'pedidosPorEstado', 'topProductos', 'ingresosPorMesa', 'resumen',
            'pedidosPagados', 'categorias', 'productosStockBajo', 'meseros', 'configPanel'
        );
    }
}
