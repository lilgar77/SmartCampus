<?php
namespace App\Model;

enum AlertType: string
{
    case temp = 'Température';
    case hum = 'Humidité';

    case co2 = 'CO2';
}
