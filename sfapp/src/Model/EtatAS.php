<?php
namespace App\Model;

enum EtatAS: string
{
    case A_Reparer = 'À réparer';
    case Installer = 'Installer';
    case A_Desinstaller = 'À Désinstaller';
    case Disponible = 'Disponible';
    case En_Installation=' En cours d\'installation';

}
