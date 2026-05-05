<?php

namespace App\Service;

use App\Enum\WeatherTag;

class WeatherTagMapperService
{
    public function map(string $conditionCode): WeatherTag
    {
        return match ($conditionCode) {
            'rain', 'light-rain', 'heavy-rain', 'showers' => WeatherTag::RAIN,

            'snow', 'light-snow', 'heavy-snow' => WeatherTag::SNOW,

            'clear' => WeatherTag::CLEAR,

            'partly-cloudy', 'cloudy' => WeatherTag::CLOUDY,

            'thunder', 'thunderstorm', 'wind', 'windy' => WeatherTag::WIND,

            'fog' => WeatherTag::COLD,

            default => WeatherTag::CLEAR,
        };
    }
}
