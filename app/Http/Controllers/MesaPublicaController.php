<?php

namespace App\Http\Controllers;

use App\Models\Mesa;

class MesaPublicaController extends Controller
{
    public function show(string $qr)
    {
        $mesa = Mesa::with('negocio')->where('codigo_qr', $qr)->first();

        if (!$mesa) {
            return view('mesa.error', [
                'titulo'  => 'QR no válido',
                'mensaje' => 'El código QR no está registrado en el sistema.',
            ]);
        }

        $pedido = $mesa->pedidoActivo()->latest('id_pedido')->first();

        if (!$pedido) {
            return view('mesa.sin-pedido', compact('mesa'));
        }

        return redirect()->route('factura.show', $pedido->id_pedido);
    }
}
