<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\DTO\RecommendationResponseDTO;

class RecommendationService
{
    public function __construct(
        private LhmtWeatherService $weatherService,
        private ProductRepository $productRepository
    ) {
    }

    public function recommend(string $city): RecommendationResponseDTO
    {
        $weather = $this->weatherService->getCurrentWeather($city);

        $days = $weather['days'] ?? [];

        if (!is_array($days)) {
            $days = [];
        }

        $daily = [];

        foreach ($days as $day) {

            $time = $day['time'] ?? null;
            $tags = $day['weatherTags'] ?? [];

            if (!is_array($tags)) {
                $tags = [];
            }

            $products = $this->productRepository->findByWeatherTags($tags);

            $daily[] = [
                'time' => $time,
                'weatherTags' => $tags,
                'products' => array_map(fn($product) => [
                    'name' => $product->getName(),
                    'category' => $product->getCategory(),
                    'price' => $product->getPrice(),
                    'sku' => $product->getSku(),
                ], $products),
            ];
        }

        return new RecommendationResponseDTO(
            $weather['city'] ?? $city,
            $daily,
            $weather['source'] ?? 'LHMT'
        );
    }
}
