<?php
namespace App\Model;

enum EtatAS: string
{
    case REPAIRED = 'À réparer';
    case INSTALL = 'installé';
    case UNINSTALL = 'désinstallé';
    case AVAILABLE = 'disponible';
}
