<?php

namespace App\Enum;

enum WeatherTag: string
{
    case COLD = 'cold';
    case HOT = 'hot';
    case RAIN = 'rain';
    case SNOW = 'snow';
    case WIND = 'wind';
    case CLEAR = 'clear';
}
