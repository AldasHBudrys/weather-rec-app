<?php

namespace App\Service;

use App\DTO\RecommendationResponseDTO;
use App\Repository\ProductRepository;

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

        $tags = $weather['weatherTags'];

        $products = $this->productRepository->findByWeatherTags($tags);

        return new RecommendationResponseDTO(
            $weather['city'],
            $tags,
            array_map(fn($product) => [
                'name' => $product->getName(),
                'category' => $product->getCategory(),
                'price' => $product->getPrice(),
                'sku' => $product->getSku(),
            ], $products),
            $weather['source']
        );
    }
}
