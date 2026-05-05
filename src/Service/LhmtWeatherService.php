<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
        $cacheKey = 'weather_' . strtolower($city);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter(300);

            $response = $this->client->request(
                'GET',
                self::BASE_URL . '/' . strtolower($city) . '/forecasts/long-term'
            );

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

        return [
            'city' => $data['place']['name'] ?? null,
            'temperature' => $current['airTemperature'] ?? null,
            'windSpeed' => $current['windSpeed'] ?? null,
            'condition' => $current['conditionCode'] ?? null,
            'weatherTag' => $this->mapper->map($current['conditionCode']),
            'time' => $current['forecastTimeUtc'] ?? null,
            'source' => 'LHMT',
        ];
    }
}
