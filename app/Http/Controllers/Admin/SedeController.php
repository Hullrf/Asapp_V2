<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Negocio;
use App\Models\Producto;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    /** Crea una nueva sede y la activa inmediatamente. */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => ['required', 'string', 'max:100'],
            'direccion'      => ['nullable', 'string', 'max:150'],
            'telefono'       => ['nullable', 'string', 'max:20'],
            'importar_desde' => ['nullable', 'integer', 'exists:negocios,id_negocio'],
        ]);

        $negocio = Negocio::create([
            'nombre'    => $request->nombre,
            'direccion' => $request->direccion,
            'telefono'  => $request->telefono,
            'email'     => auth()->user()->email,
        ]);

        // Asociar con el usuario en el pivot
        auth()->user()->negocios()->attach($negocio->id_negocio);

        // Importar categorías y productos desde otra sede
        if ($request->filled('importar_desde')) {
            $origen = auth()->user()->negocios()->find($request->importar_desde);

            if ($origen) {
                // Mapa id_categoria_origen => id_categoria_nueva
                $mapaCateg = [];
                foreach ($origen->categorias as $cat) {
                    $nueva = Categoria::create([
                        'id_negocio' => $negocio->id_negocio,
                        'nombre'     => $cat->nombre,
                    ]);
                    $mapaCateg[$cat->id_categoria] = $nueva->id_categoria;
                }

                foreach ($origen->productos as $prod) {
                    Producto::create([
                        'id_negocio'   => $negocio->id_negocio,
                        'id_categoria' => $mapaCateg[$prod->id_categoria] ?? null,
                        'nombre'       => $prod->nombre,
                        'descripcion'  => $prod->descripcion,
                        'precio'       => $prod->precio,
                        'disponible'   => $prod->disponible,
                        'stock'        => $prod->stock,
                        'stock_minimo' => $prod->stock_minimo,
                    ]);
                }

                $mensaje = "✅ Sede '{$negocio->nombre}' creada con " . count($mapaCateg) . " categorías y {$origen->productos->count()} productos importados.";
            } else {
                $mensaje = "✅ Sede '{$negocio->nombre}' creada y activada.";
            }
        } else {
            $mensaje = "✅ Sede '{$negocio->nombre}' creada y activada.";
        }

        // Activar la nueva sede
        session(['sede_activa_id' => $negocio->id_negocio]);

        return redirect()
            ->route('panel.index')
            ->with('message', $mensaje);
    }

    /** Cambia la sede activa en sesión. */
    public function activar(Negocio $negocio)
    {
        $user = auth()->user();

        // Verificar acceso: vía pivot O vía FK directa (cuentas creadas manualmente)
        $tieneAcceso = $user->negocios()->where('negocios.id_negocio', $negocio->id_negocio)->exists()
                    || $user->id_negocio == $negocio->id_negocio;

        abort_unless($tieneAcceso, 403);

        // Auto-registrar en pivot si faltaba
        if ($user->id_negocio == $negocio->id_negocio && !$user->negocios()->where('negocios.id_negocio', $negocio->id_negocio)->exists()) {
            $user->negocios()->attach($negocio->id_negocio);
        }

        session(['sede_activa_id' => $negocio->id_negocio]);

        return redirect()->route('panel.index');
    }
}
