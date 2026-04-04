<?php

namespace App\Enums;

enum RolUsuario: string
{
    case Admin   = 'admin';
    case Cliente = 'cliente';
    case Mesero  = 'mesero';
}
