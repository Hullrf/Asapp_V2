<?php

namespace App\Enums;

enum EstadoPedido: string
{
    case Pendiente = 'Pendiente';
    case Parcial   = 'Parcial';
    case Pagado    = 'Pagado';
}
