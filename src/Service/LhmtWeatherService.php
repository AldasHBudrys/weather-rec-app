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
        $cacheKey = 'weather_' . strtolower($city);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($city) {
            $item->expiresAfter(300);

            try {
                $response = $this->client->request(
                    'GET',
                    self::BASE_URL . '/' . strtolower($city) . '/forecasts/long-term'
                );

                if ($response->getStatusCode() !== 200) {
                    throw new \RuntimeException('Invalid city or API error: ' . $city);
                }

                $data = $response->toArray(false);

            } catch (\Throwable $e) {
                throw new \RuntimeException(
                    'Weather service failed: ' . $e->getMessage()
                );
            }

            return $this->normalize($data);
        });
    }

    private function normalize(array $data): array
    {
        if (empty($data['forecastTimestamps'])) {
            throw new \RuntimeException('No weather data available');
        }

        $seenDates = [];
        $forecasts = [];

        foreach ($data['forecastTimestamps'] as $forecast) {

            if (!isset($forecast['forecastTimeUtc'])) {
                continue;
            }

            $date = substr($forecast['forecastTimeUtc'], 0, 10);

            if (isset($seenDates[$date])) {
                continue;
            }

            $seenDates[$date] = true;
            $forecasts[] = $forecast;

            if (count($forecasts) === 3) {
                break;
            }
        }

        $days = [];

        foreach ($forecasts as $forecast) {
            $temperature = $forecast['airTemperature'] ?? null;
            $windSpeed = $forecast['windSpeed'] ?? null;

            $tags = [];

            $tags[] = $this->mapper->map($forecast['conditionCode']);

            if ($temperature !== null && $temperature >= 20) {
                $tags[] = WeatherTag::HOT;
            } elseif ($temperature !== null && $temperature <= 5) {
                $tags[] = WeatherTag::COLD;
            } else {
                $tags[] = WeatherTag::MILD;
            }


            $tags = array_values(array_unique(
                array_map(fn($tag) => $tag->value, $tags)
            ));

            $days[] = [
                'time' => $forecast['forecastTimeUtc'] ?? null,
                'temperature' => $temperature,
                'windSpeed' => $windSpeed,
                'condition' => $forecast['conditionCode'] ?? null,
                'weatherTags' => $tags,
            ];
        }

        return [
            'city' => $data['place']['name'] ?? null,
            'days' => $days,
            'source' => 'LHMT',
        ];
    }
}
