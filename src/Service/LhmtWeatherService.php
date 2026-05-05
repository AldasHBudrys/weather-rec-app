<?php

namespace App\Service;

use App\Enum\WeatherTag;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LhmtWeatherService
{
    private const BASE_URL = 'https://api.meteo.lt/v1/places';

    public function __construct(
        private HttpClientInterface $client,
        private CacheInterface $cache,
        private WeatherTagMapperService $mapper
    ) {
    }

    public function getCurrentWeather(string $city): array
    {
        $city = strtolower($city);

        $cacheKey = 'weather_' . $city;

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter(300);

            $response = $this->client->request(
                'GET',
                self::BASE_URL . '/' . $city . '/forecasts/long-term'
            );

            if ($response->getStatusCode() === 404) {
                throw new \InvalidArgumentException('Invalid city provided');
            }

            if ($response->getStatusCode() >= 400) {
                throw new \RuntimeException('Weather service unavailable');
            }

            $data = $response->toArray();

            return $this->normalize($data);
        });
    }

    private function normalize(array $data): array
    {
        if (!isset($data['forecastTimestamps']) || empty($data['forecastTimestamps'])) {
            throw new \RuntimeException('No weather data available');
        }

        $current = $data['forecastTimestamps'][0];

        $temperature = $current['airTemperature'] ?? null;
        $windSpeed = $current['windSpeed'] ?? null;

        $tags = [];

        $tags[] = $this->mapper->map($current['conditionCode']);

        if ($temperature !== null && $temperature >= 20) {
            $tags[] = WeatherTag::HOT;
        } elseif ($temperature !== null && $temperature <= 5) {
            $tags[] = WeatherTag::COLD;
        }

        if ($windSpeed !== null && $windSpeed >= 10) {
            $tags[] = WeatherTag::WIND;
        }

        $tags = array_values(array_unique(
            array_map(fn($tag) => $tag->value, $tags)
        ));

        return [
            'city' => $data['place']['name'] ?? null,
            'temperature' => $temperature,
            'windSpeed' => $windSpeed,
            'condition' => $current['conditionCode'] ?? null,
            'weatherTags' => $tags,
            'time' => $current['forecastTimeUtc'] ?? null,
            'source' => 'LHMT',
        ];
    }
}
